<?php

use App\Models\MailLog;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\LogsToMailLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

uses(RefreshDatabase::class);

test('mail log can have tenants relationship', function () {
    $mailLog = MailLog::factory()->create();
    $tenant = Tenant::factory()->create(['id' => 'test-tenant']);

    $mailLog->tenants()->attach($tenant);

    expect($mailLog->tenants)->toHaveCount(1);
    expect($mailLog->tenants->first()->id)->toBe('test-tenant');
});

test('notification links to current tenant', function () {
    $tenant = Tenant::factory()->create(['id' => 'notif-tenant']);

    // Mock current tenancy
    config(['tenancy.tenant' => $tenant]);
    app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $tenant);

    $notification = new class extends Notification {
        use LogsToMailLog;
        public function toMail($notifiable) {
            return (new MailMessage)
                ->subject('Test Subject')
                ->line('Test body');
        }
    };

    $notification->log('target-123');

    $mailLog = MailLog::where('uid', 'like', 'notification-%')->first();
    expect($mailLog)->not->toBeNull();
    expect($mailLog->tenants)->toHaveCount(1);
    expect($mailLog->tenants->first()->id)->toBe('notif-tenant');
});

test('create from message identifies tenant by subdomain', function () {
    $tenant = Tenant::factory()->create(['id' => 'subdomain-tenant']);
    $tenant->domains()->create(['domain' => 'tenant-sub', 'is_primary' => true]);

    $centralDomain = central_domain();

    $loggable = new class($centralDomain) implements \App\Mail\Loggable {
        public $from = [['address' => 'sender@other.com']];
        public $to;
        public $cc = [];
        public $bcc = [];
        public $subject = 'Test Subdomain Message';
        public function __construct(string $centralDomain) {
            $this->to = [['address' => 'list@tenant-sub.' . $centralDomain]];
        }
        public function getUid(): string { return 'msg-456'; }
        public function getHasAttachments(): bool { return false; }
        public function getReceivedAt(): Carbon { return now(); }
        public function getContent(): string { return 'Subdomain body content'; }
        public function getSize(): int { return 1024; }
    };

    $mailLog = MailLog::createFromMessage($loggable);

    expect($mailLog->tenants)->toHaveCount(1);
    expect($mailLog->tenants->first()->id)->toBe('subdomain-tenant');
});

test('notification links to notifiable tenant if no current tenant', function () {
    $tenant = Tenant::factory()->create(['id' => 'notif-tenant-2']);
    $user = User::factory()->create(['default_tenant_id' => $tenant->id]);

    // Ensure NO current tenancy
    app()->forgetInstance(\Stancl\Tenancy\Contracts\Tenant::class);
    config(['tenancy.tenant' => null]);

    $notification = new class extends Notification {
        use LogsToMailLog;
        public function toMail($notifiable) {
            return (new MailMessage)
                ->subject('Test Subject 2')
                ->line('Test body 2');
        }
    };

    $notification->log('target-456', $user);

    $mailLog = MailLog::where('uid', 'like', 'notification-%')->latest()->first();
    expect($mailLog)->not->toBeNull();
    expect($mailLog->tenants)->toHaveCount(1);
    expect($mailLog->tenants->first()->id)->toBe('notif-tenant-2');
});

test('command populates existing logs with subdomains', function () {
    // 1. Setup tenant
    $tenant = Tenant::factory()->create(['id' => 'sub-migrate-tenant']);
    $tenant->domains()->create(['domain' => 'tenant-sub-mig', 'is_primary' => true]);

    // 2. Create existing logs WITHOUT relationships
    $log1 = MailLog::factory()->create([
        'to' => 'list@tenant-sub-mig.choirconcierge.com',
    ]);
    $log2 = MailLog::factory()->create([
        'cc' => 'member@tenant-sub-mig.choirconcierge.com',
    ]);
    $log3 = MailLog::factory()->create([
        'to' => 'someone@otherdomain.com',
    ]);

    expect($log1->tenants)->toHaveCount(0);
    expect($log2->tenants)->toHaveCount(0);
    expect($log3->tenants)->toHaveCount(0);

    // 3. Run command logic
    $this->artisan('mail-logs:populate-tenants')->assertSuccessful();

    // 4. Verify
    expect($log1->refresh()->tenants)->toHaveCount(1);
    expect($log1->tenants->first()->id)->toBe('sub-migrate-tenant');

    expect($log2->refresh()->tenants)->toHaveCount(1);
    expect($log2->tenants->first()->id)->toBe('sub-migrate-tenant');

    expect($log3->refresh()->tenants)->toHaveCount(0);
});

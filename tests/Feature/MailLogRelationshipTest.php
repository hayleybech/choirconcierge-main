<?php

namespace Tests\Feature;

use App\Models\MailLog;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\LogsToMailLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notification;
use Tests\TestCase;

class MailLogRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_mail_log_can_have_tenants_relationship()
    {
        $mailLog = MailLog::factory()->create();
        $tenant = Tenant::factory()->create(['id' => 'test-tenant']);

        $mailLog->tenants()->attach($tenant);

        $this->assertCount(1, $mailLog->tenants);
        $this->assertEquals('test-tenant', $mailLog->tenants->first()->id);
    }

    public function test_notification_links_to_current_tenant()
    {
        $tenant = Tenant::factory()->create(['id' => 'notif-tenant']);
        
        // Mock current tenancy
        config(['tenancy.tenant' => $tenant]);
        app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $tenant);
        
        $notification = new class extends Notification {
            use LogsToMailLog;
            public function toMail($notifiable) {
                return (new \Illuminate\Notifications\Messages\MailMessage)
                    ->subject('Test Subject')
                    ->line('Test body');
            }
        };

        $notification->log('target-123');

        $mailLog = MailLog::where('uid', 'like', 'notification-%')->first();
        $this->assertNotNull($mailLog);
        $this->assertCount(1, $mailLog->tenants);
        $this->assertEquals('notif-tenant', $mailLog->tenants->first()->id);
    }

    public function test_create_from_message_identifies_tenant_by_subdomain()
    {
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
            public function getReceivedAt(): \Carbon\Carbon { return now(); }
            public function getContent(): string { return 'Subdomain body content'; }
        };

        $mailLog = MailLog::createFromMessage($loggable);

        $this->assertCount(1, $mailLog->tenants);
        $this->assertEquals('subdomain-tenant', $mailLog->tenants->first()->id);
    }

    public function test_migration_populates_existing_logs_with_subdomains()
    {
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

        $this->assertCount(0, $log1->tenants);
        $this->assertCount(0, $log2->tenants);
        $this->assertCount(0, $log3->tenants);

        // 3. Run migration logic
        $migration = require base_path('database/migrations/2026_03_17_094620_populate_mail_log_tenant_for_existing_logs.php');
        $migration->up();

        // 4. Verify
        $this->assertCount(1, $log1->refresh()->tenants);
        $this->assertEquals('sub-migrate-tenant', $log1->tenants->first()->id);

        $this->assertCount(1, $log2->refresh()->tenants);
        $this->assertEquals('sub-migrate-tenant', $log2->tenants->first()->id);

        $this->assertCount(0, $log3->refresh()->tenants);
    }
}

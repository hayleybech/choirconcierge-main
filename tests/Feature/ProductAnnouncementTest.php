<?php

use App\Enums\EmailCategory;
use App\Enums\ProductAnnouncementAudience;
use App\Enums\SingerStatus;
use App\Jobs\DeliverProductAnnouncement;
use App\Jobs\QueueProductAnnouncement;
use App\Mail\ProductAnnouncement;
use App\Models\Membership;
use App\Models\MailLog;
use App\Models\Role;
use App\Models\User;
use App\Navigation\Navigation;
use App\Services\ProductAnnouncementRecipients;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->tenancy = false;
    Queue::fake();
    Storage::fake('temp');
});

test('super-admins can open the central announcement composer', function () {
    $user = User::firstWhere('email', 'hayleybech@gmail.com')
        ?? User::factory()->create(['email' => 'hayleybech@gmail.com']);
    $this->actingAs($user);

    $this->get(route('central.announcements.create'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Central/Announcements/Create')
            ->has('audiences', 6)
            ->where('categories', [
                ['key' => 'system', 'label' => 'System & Security'],
                ['key' => 'product', 'label' => 'Product Updates'],
                ['key' => 'promotions', 'label' => 'Promotions & Offers'],
                ['key' => 'community', 'label' => 'Community & Content'],
            ]));
});

test('central communications navigation contains mail logs and product announcements', function (): void {
    $communications = collect(app(Navigation::class)->get())
        ->firstWhere('name', 'Communications');

    expect($communications)->not->toBeNull()
        ->and(collect($communications['items'])->pluck('name')->all())
        ->toBe(['Mail Logs', 'Announcements']);
});

test('non-super-admins cannot open the announcement composer', function () {
    $this->actingAs(User::factory()->create(['email' => 'not-admin@example.com']));

    $this->get(route('central.announcements.create'))->assertRedirect();
});

test('sending an announcement queues delivery and writes to the central mail log', function () {
    $superAdmin = User::firstWhere('email', 'hayleybech@gmail.com')
        ?? User::factory()->create(['email' => 'hayleybech@gmail.com']);
    $superAdmin->updateEmailPreferences(EmailCategory::defaults());
    $recipient = User::factory()->create();
    $attachment = UploadedFile::fake()->create('release-notes.pdf', 10, 'application/pdf');
    $this->actingAs($superAdmin);

    $this->post(route('central.announcements.store'), [
        'audience' => ProductAnnouncementAudience::ALL_USERS->value,
        'category' => EmailCategory::PRODUCT_UPDATES->value,
        'subject' => 'A product update',
        'body' => '<p>New features are available.</p>',
        'attachments' => [$attachment],
    ])->assertRedirect(route('central.mail-logs.index'))
        ->assertSessionHas('status', 'Email queued for 2 recipients.');

    Queue::assertPushed(QueueProductAnnouncement::class, fn (QueueProductAnnouncement $job) =>
        $job->audience === ProductAnnouncementAudience::ALL_USERS->value
            && $job->category === EmailCategory::PRODUCT_UPDATES->value
            && $job->subject === 'A product update'
            && $job->mailLogId > 0
    );
    $this->assertDatabaseHas('mail_logs', [
        'from' => 'Choir Concierge <hayley@choirconcierge.com>',
        'to' => 'All users',
        'subject' => 'A product update',
        'has_attachments' => true,
    ]);
    $mailLog = MailLog::where('subject', 'A product update')->latest('id')->firstOrFail();
    $this->assertDatabaseHas('mail_log_events', [
        'mail_log_id' => $mailLog->id,
        'status' => 'pending',
        'context' => '2 recipients · Product Updates',
    ]);
    Storage::disk('temp')->assertExists('broadcasts/' . $attachment->hashName());
    expect($recipient->exists)->toBeTrue();
});

test('choir-focused categories cannot be used for product announcements', function (): void {
    $superAdmin = User::firstWhere('email', 'hayleybech@gmail.com')
        ?? User::factory()->create(['email' => 'hayleybech@gmail.com']);
    $this->actingAs($superAdmin);

    $this->post(route('central.announcements.store'), [
        'audience' => ProductAnnouncementAudience::ALL_USERS->value,
        'category' => EmailCategory::SONGS->value,
        'subject' => 'A product update',
        'body' => '<p>New features are available.</p>',
    ])->assertSessionHasErrors('category');

    Queue::assertNotPushed(QueueProductAnnouncement::class);
});

test('queued announcement fan-out filters optional preferences and deduplicates accounts', function () {
    $eligibleRecipient = User::factory()->create();
    $unsubscribedRecipient = User::factory()->create();
    $unsubscribedRecipient->updateEmailPreferences(['product' => false]);
    $mailLog = MailLog::create([
        'uid' => 'announcement-' . uniqid(),
        'from' => 'Choir Concierge <hayley@choirconcierge.com>',
        'to' => 'All users',
        'subject' => 'Feature update',
        'body' => '<p>Details</p>',
    ]);
    $job = new QueueProductAnnouncement(
        ProductAnnouncementAudience::ALL_USERS->value,
        EmailCategory::PRODUCT_UPDATES->value,
        'Feature update',
        '<p>Details</p>',
        $mailLog->id,
        [],
    );

    $job->handle(app(ProductAnnouncementRecipients::class));

    Queue::assertPushed(DeliverProductAnnouncement::class, fn (DeliverProductAnnouncement $delivery) =>
        $delivery->userId === $eligibleRecipient->id
    );
    Queue::assertNotPushed(DeliverProductAnnouncement::class, fn (DeliverProductAnnouncement $delivery) =>
        $delivery->userId === $unsubscribedRecipient->id
    );
});

test('mandatory announcement categories bypass saved preference filters', function () {
    $recipient = User::factory()->create();
    $preferences = $recipient->emailPreferences();
    $preferences['system'] = false;
    $recipient->forceFill(['email_preferences' => $preferences])->save();

    $recipients = app(ProductAnnouncementRecipients::class)
        ->query(ProductAnnouncementAudience::ALL_USERS, EmailCategory::SYSTEM_SECURITY)
        ->pluck('id');

    expect($recipients)->toContain($recipient->id);
});

test('role audiences include only active memberships with that role', function () {
    $role = Role::updateOrCreate(['name' => 'Music Team'], ['abilities' => []]);
    $activeMembership = Membership::factory()->create();
    $activeMembership->roles()->attach($role);
    $inactiveMembership = Membership::factory()->create();
    $inactiveMembership->roles()->attach($role);
    $inactiveMembership->status()->update(['status' => SingerStatus::INACTIVE_MEMBERS->value]);

    $recipients = app(ProductAnnouncementRecipients::class)
        ->query(ProductAnnouncementAudience::MUSIC_TEAM, EmailCategory::PRODUCT_UPDATES)
        ->pluck('users.id');

    expect($recipients)->toContain($activeMembership->user_id)
        ->not->toContain($inactiveMembership->user_id);
});

test('optional announcements use the product sender and include preference links', function () {
    $recipient = User::factory()->create();
    $mailLog = MailLog::create([
        'uid' => 'announcement-' . uniqid(),
        'from' => 'Choir Concierge <hayley@choirconcierge.com>',
        'to' => $recipient->email,
        'subject' => 'Feature update',
        'body' => '<p>Details</p>',
    ]);
    Mail::fake();

    (new DeliverProductAnnouncement(
        $recipient->id,
        'Feature update',
        '<p>Details</p>',
        EmailCategory::PRODUCT_UPDATES->value,
        $mailLog->id,
        [],
    ))->handle();

    Mail::assertSent(ProductAnnouncement::class, function (ProductAnnouncement $mail): bool {
        $rendered = $mail->render();

        return $mail->from[0]['address'] === 'hayley@choirconcierge.com'
            && $mail->from[0]['name'] === 'Choir Concierge'
            && $mail->replyTo[0]['address'] === 'hayley@choirconcierge.com'
            && str_contains($rendered, 'emails for Choir Concierge.')
            && str_contains($rendered, 'you are subscribed to')
            && str_contains($rendered, 'Manage your email preferences')
            && str_contains($rendered, 'Unsubscribe from Product Updates');
    });

    $this->assertDatabaseHas('mail_log_events', [
        'mail_log_id' => $mailLog->id,
        'status' => 'announcement-sent',
    ]);
});

test('mandatory product announcement emails include category context without an unsubscribe link', function (): void {
    $recipient = User::factory()->create();
    $rendered = (new ProductAnnouncement(
        $recipient,
        'Security notice',
        '<p>Important security information.</p>',
        EmailCategory::SYSTEM_SECURITY,
        [],
    ))->render();

    expect($rendered)
        ->toContain('emails for Choir Concierge are mandatory.')
        ->not->toContain('subscribed to System & Security')
        ->not->toContain('Unsubscribe from System & Security');
});

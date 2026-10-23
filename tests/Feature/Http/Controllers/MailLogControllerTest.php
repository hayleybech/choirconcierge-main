<?php

use App\Models\MailLog;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

it('uses inbox paths for tenant communications', function () {
    $this->actingAsRole('Admin');
    $log = MailLog::factory()->create();
    $log->tenants()->attach(tenant('id'));

    $indexPath = parse_url(the_tenant_route('communications.index'), PHP_URL_PATH);
    $showPath = parse_url(the_tenant_route('communications.show', ['mail_log' => $log]), PHP_URL_PATH);

    $this->assertStringEndsWith('/inbox', $indexPath);
    $this->assertStringEndsWith('/inbox/' . $log->id, $showPath);
    $this->get(the_tenant_route('communications.index'))->assertOk();
    $this->get(the_tenant_route('communications.show', ['mail_log' => $log]))->assertOk();
});

it('filters and sorts tenant communications logs', function () {
    $this->actingAsRole('Admin');

    $smallerLog = MailLog::factory()->create([
        'uid' => 'broadcast-winter-update',
        'subject' => 'Winter concert update',
        'size' => 100,
    ]);
    $smallerLog->tenants()->attach(tenant('id'));

    $largerLog = MailLog::factory()->create([
        'uid' => 'inbound-winter-reminder',
        'subject' => 'Winter concert reminder',
        'size' => 1000,
    ]);
    $largerLog->tenants()->attach(tenant('id'));

    MailLog::factory()->create([
        'uid' => 'notification-summer-update',
        'subject' => 'Summer concert update',
    ])->tenants()->attach(tenant('id'));

    $this->get(the_tenant_route('communications.index', [
        'filter' => ['subject' => 'Winter'],
    ]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('MailingLists/MailLogs/Index')
            ->has('logs.data', 2)
            ->where('logs.data', fn ($logs) => collect($logs)->pluck('id')->sort()->values()->all() === collect([$smallerLog->id, $largerLog->id])->sort()->values()->all())
        );

    $this->get(the_tenant_route('communications.index', [
        'sort' => 'size',
        'filter' => ['subject' => 'Winter'],
    ]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('logs.data.0.id', $smallerLog->id)
            ->where('logs.data.1.id', $largerLog->id)
        );
});

it('filters tenant communications by mail type and the current user read state', function () {
    $user = $this->actingAsRole('Admin');

    $readBroadcast = MailLog::factory()->create(['uid' => 'broadcast-read']);
    $readBroadcast->tenants()->attach(tenant('id'));
    $readBroadcast->events()->create(['status' => 'opened', 'context' => $user->email]);

    $unreadInbound = MailLog::factory()->create(['uid' => 'inbound-unread']);
    $unreadInbound->tenants()->attach(tenant('id'));

    $productAnnouncement = MailLog::factory()->create(['uid' => 'product-announcement-unread']);

    $notification = MailLog::factory()->create(['uid' => 'notification-hidden']);
    $notification->tenants()->attach(tenant('id'));

    $this->get(the_tenant_route('communications.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('logs.data', fn ($logs) => collect($logs)->pluck('id')->sort()->values()->all() === collect([
                $readBroadcast->id,
                $unreadInbound->id,
                $productAnnouncement->id,
            ])->sort()->values()->all())
            ->where('unreadCommunicationsCount', 2)
        );

    $this->get(the_tenant_route('communications.index', ['filter' => ['type' => ['notification']]]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('logs.data', 1)
            ->where('logs.data.0.id', $notification->id)
        );

    $this->get(the_tenant_route('communications.index', ['filter' => ['read' => 'read']]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('logs.data', 1)
            ->where('logs.data.0.id', $readBroadcast->id)
            ->where('logs.data.0.is_read', true)
        );

    $this->get(the_tenant_route('communications.index', ['filter' => ['read' => 'unread']]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('logs.data', fn ($logs) => collect($logs)->pluck('id')->sort()->values()->all() === collect([
                $unreadInbound->id,
                $productAnnouncement->id,
            ])->sort()->values()->all() && collect($logs)->every(fn ($log) => ! $log['is_read']))
        );
});

it('makes communications available to members who can view mail logs', function () {
    $user = $this->actingAsRole('Music Team');
    $group = UserGroup::factory()->create();
    $group->sender_users()->attach($user);
    $log = MailLog::factory()->create(['uid' => 'broadcast-music-team']);
    $log->events()->create([
        'user_group_id' => $group->id,
        'status' => 'group-found',
    ]);
    $log->tenants()->attach(tenant('id'));

    $this->get(the_tenant_route('communications.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('can.view_mail_logs', true)
            ->has('logs.data', 1)
            ->where('logs.data.0.id', $log->id)
        );
});

it('resolves the sender user for tenant communications logs', function () {
    $this->actingAsRole('Admin');
    $sender = User::factory()->create(['email' => 'sender@example.com']);
    $log = MailLog::factory()->create([
        'uid' => 'broadcast-sender',
        'from' => 'Sender Name <sender@example.com>',
    ]);
    $log->tenants()->attach(tenant('id'));

    $this->get(the_tenant_route('communications.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('logs.data.0.id', $log->id)
            ->where('logs.data.0.from_user.id', $sender->id)
        );
});

it('shows product announcements to tenant members without exposing delivery activity', function () {
    $user = $this->actingAsRole('User');
    $productAnnouncement = MailLog::factory()->create(['uid' => 'product-announcement-tenant-member']);
    $productAnnouncement->events()->create(['status' => 'announcement-sent', 'context' => $user->email]);

    $this->get(the_tenant_route('communications.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('logs.data', 1)
            ->where('logs.data.0.id', $productAnnouncement->id)
        );

    $showRoute = the_tenant_route('communications.show', [$productAnnouncement]);
    $this->get($showRoute)
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->missing('log.opens_count')
            ->where('log.events', [])
        );

    $this->get($showRoute)->assertOk();

    $this->assertDatabaseHas('mail_log_events', [
        'mail_log_id' => $productAnnouncement->id,
        'status' => 'opened',
        'context' => $user->email,
    ]);
    $this->assertDatabaseCount('mail_log_events', 2);
});

it('hides product announcement opens and activity from tenant administrators', function () {
    $this->actingAsRole('Admin');
    $productAnnouncement = MailLog::factory()->create(['uid' => 'product-announcement-admin']);
    $productAnnouncement->events()->createMany([
        ['status' => 'announcement-sent', 'context' => 'recipient@example.com'],
        ['status' => 'opened', 'context' => 'recipient@example.com'],
        ['status' => 'opened', 'context' => 'second-recipient@example.com'],
        ['status' => 'opened', 'context' => 'third-recipient@example.com'],
    ]);
    $broadcast = MailLog::factory()->create(['uid' => 'broadcast-tenant-admin']);
    $broadcast->tenants()->attach(tenant('id'));
    $broadcast->events()->create(['status' => 'opened', 'context' => 'recipient@example.com']);

    $this->get(the_tenant_route('communications.index', ['sort' => '-opens']))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('logs.data', 2)
            ->where('logs.data.0.id', $broadcast->id)
            ->where('logs.data.1.id', $productAnnouncement->id)
            ->missing('logs.data.1.opens_count')
            ->missing('logs.data.1.latest_event')
        );

    $this->get(the_tenant_route('communications.show', [$productAnnouncement]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->missing('log.opens_count')
            ->where('log.events', [])
        );
});

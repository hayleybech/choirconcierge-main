<?php

use App\Models\MailLog;
use App\Models\MailLogEvent;
use App\Models\Role;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

it('allows a recipient to view communications without admin details', function () {
    $user = $this->actingAsRole('User');
    $group = UserGroup::factory()->create();
    $group->recipient_users()->attach($user);
    $log = MailLog::factory()->create();
    MailLogEvent::create([
        'mail_log_id' => $log->id,
        'user_group_id' => $group->id,
        'status' => 'group-found',
    ]);

    $this->get(the_tenant_route('communications.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('MailingLists/MailLogs/Index')
            ->has('logs.data', 1)
            ->missing('can.view_admin_details')
        );

    $this->get(the_tenant_route('communications.show', ['mail_log' => $log]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('MailingLists/MailLogs/Show')
            ->missing('can.view_admin_details')
            ->missing('log.opens_count')
        );
});

it('does not allow a non-recipient to view a communication', function () {
    $user = $this->actingAsRole('User');
    $group = UserGroup::factory()->create();
    $recipient = User::factory()->create();
    $group->recipient_users()->attach($recipient);
    $log = MailLog::factory()->create();
    MailLogEvent::create([
        'mail_log_id' => $log->id,
        'user_group_id' => $group->id,
    ]);

    $this->get(the_tenant_route('communications.show', ['mail_log' => $log]))
        ->assertForbidden();
});

it('loads role recipients without lazy loading relationships', function () {
    $user = $this->actingAsRole('User');
    $role = Role::factory()->create();
    $user->membership->roles()->attach($role);

    $group = UserGroup::factory()->create();
    $group->recipient_roles()->attach($role);

    $log = MailLog::factory()->create();
    MailLogEvent::create([
        'mail_log_id' => $log->id,
        'user_group_id' => $group->id,
        'status' => 'group-found',
    ]);

    $this->get(the_tenant_route('communications.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('MailingLists/MailLogs/Index')
            ->has('logs.data', 1)
        );
});

it('filters and sorts communications', function () {
    $user = $this->actingAsRole('User');
    $group = UserGroup::factory()->create();
    $group->recipient_users()->attach($user);

    $older = MailLog::factory()->create([
        'subject' => 'Older newsletter',
        'from' => 'news@example.com',
        'to' => 'choir@example.com',
        'created_at' => now()->subDay(),
    ]);
    $newer = MailLog::factory()->create([
        'subject' => 'Newer announcement',
        'from' => 'admin@example.com',
        'to' => 'choir@example.com',
        'created_at' => now(),
    ]);

    foreach ([$older, $newer] as $log) {
        MailLogEvent::create([
            'mail_log_id' => $log->id,
            'user_group_id' => $group->id,
            'status' => 'group-found',
        ]);
    }

    $this->get(the_tenant_route('communications.index', [
        'filter' => ['from' => 'admin@example.com'],
        'sort' => 'subject',
    ]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('MailingLists/MailLogs/Index')
            ->has('logs.data', 1)
            ->where('logs.data.0.id', $newer->id)
        );

    $this->get(the_tenant_route('communications.index'))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('logs.data.0.id', $newer->id)
            ->where('logs.data.1.id', $older->id)
        );
});

it('sorts communications by size', function () {
    $user = $this->actingAsRole('User');
    $group = UserGroup::factory()->create();
    $group->recipient_users()->attach($user);

    $small = MailLog::factory()->create(['subject' => 'Small log', 'size' => 100]);
    $large = MailLog::factory()->create(['subject' => 'Large log', 'size' => 10000]);

    foreach ([$small, $large] as $log) {
        MailLogEvent::create([
            'mail_log_id' => $log->id,
            'user_group_id' => $group->id,
            'status' => 'group-found',
        ]);
    }

    $this->get(the_tenant_route('communications.index', [
        'sort' => 'size',
    ]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('logs.data.0.id', $small->id)
            ->where('logs.data.1.id', $large->id)
        );
});

it('sorts communications by opens for admins', function () {
    $user = $this->actingAsRole('Admin');
    $group = UserGroup::factory()->create();
    $group->recipient_users()->attach($user);

    $fewOpens = MailLog::factory()->create(['subject' => 'Few opens']);
    $fewOpens->tenants()->attach(tenant('id'));
    $manyOpens = MailLog::factory()->create(['subject' => 'Many opens']);
    $manyOpens->tenants()->attach(tenant('id'));

    foreach ([$fewOpens, $manyOpens] as $log) {
        MailLogEvent::create([
            'mail_log_id' => $log->id,
            'user_group_id' => $group->id,
            'status' => 'group-found',
        ]);
    }

    $fewOpens->events()->create(['status' => 'opened']);
    $manyOpens->events()->createMany([
        ['status' => 'opened'],
        ['status' => 'opened'],
        ['status' => 'opened'],
    ]);

    $this->get(the_tenant_route('communications.index', [
        'sort' => '-opens',
    ]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('logs.data.0.id', $manyOpens->id)
            ->where('logs.data.1.id', $fewOpens->id)
        );
});

it('filters communications by mailing list address', function () {
    $user = $this->actingAsRole('User');
    $group = UserGroup::factory()->create();
    $group->recipient_users()->attach($user);
    $mailingListAddress = $group->email;

    $matching = MailLog::factory()->create(['to' => $mailingListAddress.', other@example.com']);
    $nonMatching = MailLog::factory()->create(['to' => 'other@example.com']);

    foreach ([$matching, $nonMatching] as $log) {
        MailLogEvent::create([
            'mail_log_id' => $log->id,
            'user_group_id' => $group->id,
            'status' => 'group-found',
        ]);
    }

    $this->get(the_tenant_route('communications.index', [
        'filter' => ['to' => $mailingListAddress],
    ]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('logs.data', 1)
            ->where('logs.data.0.id', $matching->id)
        );
});

it('filters communications by an address inside a formatted recipient', function () {
    $user = $this->actingAsRole('User');
    $group = UserGroup::factory()->create();
    $group->recipient_users()->attach($user);

    $matching = MailLog::factory()->create([
        'to' => 'Active Members <'.$group->email.'>',
    ]);
    MailLogEvent::create([
        'mail_log_id' => $matching->id,
        'user_group_id' => $group->id,
        'status' => 'group-found',
    ]);

    $this->get(the_tenant_route('communications.index', [
        'filter' => ['to' => $group->email],
    ]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('logs.data', 1)
            ->where('logs.data.0.id', $matching->id)
        );
});

it('filters communications by multiple mailing list addresses', function () {
    $user = $this->actingAsRole('User');
    $group = UserGroup::factory()->create();
    $group->recipient_users()->attach($user);
    $otherGroup = UserGroup::factory()->create();

    $matching = MailLog::factory()->create(['to' => $otherGroup->email]);
    $alsoMatching = MailLog::factory()->create(['to' => $group->email]);
    $nonMatching = MailLog::factory()->create(['to' => 'other@example.com']);

    foreach ([$matching, $alsoMatching, $nonMatching] as $log) {
        MailLogEvent::create([
            'mail_log_id' => $log->id,
            'user_group_id' => $group->id,
            'status' => 'group-found',
        ]);
    }

    $this->get(the_tenant_route('communications.index', [
        'filter' => ['to' => [$otherGroup->email, $group->email]],
    ]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('logs.data', 2)
            ->where('logs.data', fn ($logs) => collect($logs)->pluck('id')->sort()->values()->all() === collect([$matching->id, $alsoMatching->id])->sort()->values()->all())
        );
});

it('loads the tenant before resolving a communication group email', function () {
    $group = UserGroup::factory()->create();
    $group->unsetRelation('tenant');

    expect($group->email)->toBe($group->slug.'@'.$group->tenant->host);
});

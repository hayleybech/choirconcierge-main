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

it('loads the tenant before resolving a communication group email', function () {
    $group = UserGroup::factory()->create();
    $group->unsetRelation('tenant');

    expect($group->email)->toBe($group->slug.'@'.$group->tenant->host);
});

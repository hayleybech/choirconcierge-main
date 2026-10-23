<?php

use App\Models\MailLog;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

it('filters and sorts tenant communications logs', function () {
    $this->actingAsRole('Admin');

    $smallerLog = MailLog::factory()->create([
        'subject' => 'Winter concert update',
        'size' => 100,
    ]);
    $smallerLog->tenants()->attach(tenant('id'));

    $largerLog = MailLog::factory()->create([
        'subject' => 'Winter concert reminder',
        'size' => 1000,
    ]);
    $largerLog->tenants()->attach(tenant('id'));

    MailLog::factory()->create([
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

it('makes communications available to members who can view mail logs', function () {
    $user = $this->actingAsRole('Music Team');
    $group = UserGroup::factory()->create();
    $group->sender_users()->attach($user);
    $log = MailLog::factory()->create();
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
    $log = MailLog::factory()->create(['from' => 'Sender Name <sender@example.com>']);
    $log->tenants()->attach(tenant('id'));

    $this->get(the_tenant_route('communications.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('logs.data.0.id', $log->id)
            ->where('logs.data.0.from_user.id', $sender->id)
        );
});

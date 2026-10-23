<?php

use App\Models\MailLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

function getMailLogSuperAdmin(): User
{
    return User::firstWhere('email', 'hayleybech@gmail.com')
        ?? User::factory()->create(['email' => 'hayleybech@gmail.com']);
}

it('does not allow a non super admin to view central mail logs', function () {
    $this->actingAs(User::factory()->create(['email' => 'not-admin@example.com']));

    $this->get(route('central.mail-logs.index'))
        ->assertRedirect();
});

it('filters and sorts central mail logs by tenant', function () {
    $this->actingAs(getMailLogSuperAdmin());

    $tenantA = Tenant::factory()->create(['id' => 'tenant-a', 'name' => 'Tenant A', 'timezone' => 'Australia/Perth']);
    $tenantB = Tenant::factory()->create(['id' => 'tenant-b', 'name' => 'Tenant B', 'timezone' => 'Australia/Perth']);

    $logA = MailLog::factory()->create(['subject' => 'For tenant A']);
    $logA->tenants()->attach($tenantA);

    $logB = MailLog::factory()->create(['subject' => 'For tenant B']);
    $logB->tenants()->attach($tenantB);

    $this->get(route('central.mail-logs.index', [
        'filter' => ['tenant' => $tenantA->id],
    ]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Central/MailLogs/Index')
            ->has('logs.data', 1)
            ->where('logs.data.0.id', $logA->id)
        );

    $this->get(route('central.mail-logs.index', ['sort' => 'tenant']))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('logs.data.0.id', $logA->id)
            ->where('logs.data.1.id', $logB->id)
        );
});

it('sorts central mail logs by size and opens', function () {
    $this->actingAs(getMailLogSuperAdmin());

    $small = MailLog::factory()->create(['subject' => 'Small log', 'size' => 100]);
    $large = MailLog::factory()->create(['subject' => 'Large log', 'size' => 10000]);

    $this->get(route('central.mail-logs.index', ['sort' => 'size']))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('logs.data.0.id', $small->id)
            ->where('logs.data.1.id', $large->id)
        );

    $fewOpens = MailLog::factory()->create(['subject' => 'Few opens']);
    $fewOpens->events()->create(['status' => 'opened']);
    $manyOpens = MailLog::factory()->create(['subject' => 'Many opens']);
    $manyOpens->events()->createMany([
        ['status' => 'opened'],
        ['status' => 'opened'],
        ['status' => 'opened'],
    ]);

    $this->get(route('central.mail-logs.index', ['sort' => '-opens']))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('logs.data.0.id', $manyOpens->id)
            ->where('logs.data.1.id', $fewOpens->id)
        );
});

it('passes tenants to the central mail log filters and resolves the sender', function () {
    $this->actingAs(getMailLogSuperAdmin());

    $tenant = Tenant::factory()->create(['id' => 'tenant-a', 'name' => 'Tenant A', 'timezone' => 'Australia/Perth']);
    $sender = User::factory()->create(['email' => 'sender@example.com']);
    $log = MailLog::factory()->create(['from' => 'Sender Name <sender@example.com>']);

    $this->get(route('central.mail-logs.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('tenants', fn ($tenants) => collect($tenants)->pluck('id')->contains($tenant->id))
            ->where('logs.data.0.from_user.id', $sender->id)
        );
});

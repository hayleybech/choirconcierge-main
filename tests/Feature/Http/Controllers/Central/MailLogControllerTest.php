<?php

use App\Models\MailLog;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

function getMailLogSuperAdmin(): User
{
    return User::firstWhere('email', 'hayleybech@gmail.com') ?? User::factory()->create(['email' => 'hayleybech@gmail.com']);
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

    $this->get(route('central.mail-logs.index', [
        'sort' => 'tenant',
    ]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('logs.data.0.id', $logA->id)
            ->where('logs.data.1.id', $logB->id)
        );
});

it('passes the list of tenants for the filter options', function () {
    $this->actingAs(getMailLogSuperAdmin());

    Tenant::factory()->create(['id' => 'tenant-a', 'name' => 'Tenant A', 'timezone' => 'Australia/Perth']);

    $this->get(route('central.mail-logs.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('tenants', fn ($tenants) => collect($tenants)->pluck('id')->contains('tenant-a'))
        );
});

it('resolves the sending user for central mail logs', function () {
    $this->actingAs(getMailLogSuperAdmin());

    $sender = User::factory()->create(['email' => 'sender@example.com']);
    $log = MailLog::factory()->create(['from' => 'Sender Name <sender@example.com>']);

    $this->get(route('central.mail-logs.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('logs.data.0.from_user.id', $sender->id)
        );
});

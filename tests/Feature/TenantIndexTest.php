<?php

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->tenancy = false;
});

test('central tenants index includes tenant stats for super admins', function () {
    $superAdmin = User::firstWhere('email', 'hayleybech@gmail.com')
        ?? User::factory()->create(['email' => 'hayleybech@gmail.com']);

    $this->actingAs($superAdmin);

    $this->get(route('central.tenants.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Central/Tenants/Index')
            ->has('tenantStats.activeTenants')
            ->has('tenantStats.tenantsOnTrial')
            ->has('tenantStats.tenantsTrialExpired')
            ->has('tenantStats.activeMembers')
            ->has('tenantStats.trialConversionRate')
            ->has('tenantStats.medianRetentionTime'));
});

test('inactive billing status includes ended tenants without a gratis value', function () {
    $superAdmin = User::firstWhere('email', 'hayleybech@gmail.com')
        ?? User::factory()->create(['email' => 'hayleybech@gmail.com']);
    $this->actingAs($superAdmin);

    $tenant = Tenant::factory()->create([
        'id' => 'inactive-example',
        'timezone' => 'Australia/Perth',
    ]);
    $tenant->customer()->update(['trial_ends_at' => null]);
    $tenant->subscriptions()->create([
        'name' => 'default',
        'paddle_id' => 12345,
        'paddle_status' => 'deleted',
        'paddle_plan' => 845528,
        'quantity' => 1,
        'trial_ends_at' => null,
        'ends_at' => '2025-07-07 08:45:07',
    ]);
    $gratisTenant = Tenant::factory()->create([
        'id' => 'gratis-example',
        'timezone' => 'Australia/Perth',
        'has_gratis' => true,
    ]);
    $gratisTenant->customer()->update(['trial_ends_at' => null]);
    expect($gratisTenant->fresh()->has_gratis)->toBeTrue();
    $this->get(route('central.tenants.index', ['filter' => ['billing_status' => 'inactive']]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Central/Tenants/Index')
            ->has('tenants', 1)
            ->where('tenants.0.id', $tenant->id));
});

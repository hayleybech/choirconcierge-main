<?php

use App\Models\User;
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

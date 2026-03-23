<?php

use App\Models\Membership;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the billing page', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    $membership = Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);
    $role = Role::firstOrCreate(['name' => 'Admin']);
    $membership->roles()->attach($role);

    $tenant->run(function () use ($user, $tenant) {
        $this->actingAs($user)
            ->get(route('organisation.billing', ['tenant' => $tenant->id]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Tenants/Billing')
                ->has('plans')
                ->has('tenant')
            );
    });
});

it('blocks non-admins from billing page', function () {
    $tenant = Tenant::factory()->create([
        'timezone' => 'UTC',
        'has_gratis' => true
    ]);
    $user = User::factory()->create();
    $membership = Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);
    // No Admin role

    $tenant->run(function () use ($user, $tenant) {
        $this->actingAs($user)
            ->get(route('organisation.billing', ['tenant' => $tenant->id]))
            ->assertForbidden();
    });
});

<?php

use App\Models\Membership;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use Laravel\Paddle\Subscription;
use Laravel\Paddle\SubscriptionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use function Pest\Laravel\mock;

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
        Gate::before(fn () => true);

        $this->actingAs($user);

        config(['spark.billables.tenant.plans' => []]);

        $this->get(route('organisation.billing', ['tenant' => $tenant->id]))
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

it('swaps the plan if already subscribed', function () {
    Gate::before(fn () => true);

    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);

    $planId = 54321;

    $tenant->run(function () use ($user, $tenant, $planId) {
        $subscription = mock(Subscription::class);
        $subscription->shouldReceive('swap')->with($planId)->once()->andReturnSelf();
        $subscription->shouldReceive('onTrial')->andReturn(false);

        $mockTenant = mock(Tenant::class . '[subscribed,subscription,onTrial]');
        $mockTenant->setRawAttributes($tenant->getAttributes());
        $mockTenant->exists = true;
        $mockTenant->shouldReceive('subscribed')->with('default')->andReturn(true);
        $mockTenant->shouldReceive('subscription')->with('default')->andReturn($subscription);
        $mockTenant->shouldReceive('onTrial')->andReturn(false);

        app()->instance(Tenant::class, $mockTenant);
        app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $mockTenant);

        $this->actingAs($user)
            ->get(route('organisation.billing.swap', ['tenant' => $tenant->id, 'plan' => $planId]))
            ->assertRedirect()
            ->assertSessionHas('status', 'Subscription swapped successfully!');
    });
});

it('pauses the subscription', function () {
    Gate::before(fn () => true);

    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);

    $tenant->run(function () use ($user, $tenant) {
        $subscription = mock(Subscription::class);
        $subscription->shouldReceive('pause')->once()->andReturnSelf();

        $mockTenant = mock(Tenant::class . '[subscription]');
        $mockTenant->setRawAttributes($tenant->getAttributes());
        $mockTenant->exists = true;
        $mockTenant->shouldReceive('subscription')->with('default')->andReturn($subscription);

        app()->instance(Tenant::class, $mockTenant);
        app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $mockTenant);

        $this->actingAs($user)
            ->get(route('organisation.billing.pause', ['tenant' => $tenant->id]))
            ->assertRedirect()
            ->assertSessionHas('status', 'Subscription paused successfully.');
    });
});

it('unpauses the subscription', function () {
    Gate::before(fn () => true);

    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);

    $tenant->run(function () use ($user, $tenant) {
        $subscription = mock(Subscription::class);
        $subscription->shouldReceive('unpause')->once()->andReturnSelf();

        $mockTenant = mock(Tenant::class . '[subscription]');
        $mockTenant->setRawAttributes($tenant->getAttributes());
        $mockTenant->exists = true;
        $mockTenant->shouldReceive('subscription')->with('default')->andReturn($subscription);

        app()->instance(Tenant::class, $mockTenant);
        app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $mockTenant);

        $this->actingAs($user)
            ->get(route('organisation.billing.unpause', ['tenant' => $tenant->id]))
            ->assertRedirect()
            ->assertSessionHas('status', 'Subscription unpaused successfully.');
    });
});

it('cancels the subscription', function () {
    Gate::before(fn () => true);

    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);

    $tenant->run(function () use ($user, $tenant) {
        $subscription = mock(Subscription::class);
        $subscription->shouldReceive('cancel')->once()->andReturnSelf();

        $mockTenant = mock(Tenant::class . '[subscription]');
        $mockTenant->setRawAttributes($tenant->getAttributes());
        $mockTenant->exists = true;
        $mockTenant->shouldReceive('subscription')->with('default')->andReturn($subscription);

        app()->instance(Tenant::class, $mockTenant);
        app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $mockTenant);

        $this->actingAs($user)
            ->get(route('organisation.billing.cancel', ['tenant' => $tenant->id]))
            ->assertRedirect()
            ->assertSessionHas('status', 'Subscription cancelled successfully.');
    });
});

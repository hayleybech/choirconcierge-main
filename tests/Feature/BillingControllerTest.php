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

        $mockTenant = mock(Tenant::class)->makePartial();
        $mockTenant->setRawAttributes($tenant->getAttributes());
        $mockTenant->exists = true;

        $mockTenant->shouldReceive('subscribed')->andReturn(false);
        $mockTenant->shouldReceive('newSubscription')->andReturn(new class {
            public function returnTo() { return $this; }
            public function create() { return 'https://pay.paddle.com/checkout/123'; }
        });
        
        config(['cashier.vendor_id' => 'real-vendor-id']);
        $mockTenant->shouldReceive('load')->andReturn($mockTenant);
        $mockTenant->shouldReceive('append')->andReturn($mockTenant);
        $mockTenant->shouldReceive('getAttribute')->passthru();
        $mockTenant->shouldReceive('hasRelation')->passthru();
        $mockTenant->shouldReceive('getRelation')->passthru();

        app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $mockTenant);
        app()->instance(Tenant::class, $mockTenant);

        $this->get(route('organisation.billing'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Tenants/Billing')
                ->has('plans')
                ->has('tenant')
                ->where('plans.0.payLink', 'https://pay.paddle.com/checkout/123')
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

it('does not return pay link in session for a new subscription', function () {
    Gate::before(fn () => true);
    
    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    $membership = Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);

    $planId = 12345;

    $tenant->run(function () use ($user, $tenant, $planId) {
        $builder = mock(SubscriptionBuilder::class);
        $builder->shouldReceive('returnTo')->andReturnSelf();
        $builder->shouldReceive('create')->andReturn('https://pay.paddle.com/checkout/123');

        $mockTenant = mock(Tenant::class)->makePartial();
        $mockTenant->setRawAttributes($tenant->getAttributes());
        $mockTenant->exists = true;
        
        $mockTenant->shouldReceive('subscribed')->with('default')->andReturn(false);
        $mockTenant->shouldReceive('newSubscription')->with('default', $planId)->andReturn($builder);

        config(['cashier.vendor_id' => 'real-vendor-id']);
        $this->instance(Tenant::class, $mockTenant);
        app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $mockTenant);

        $this->actingAs($user)
            ->post(route('organisation.billing.subscribe', ['tenant' => $tenant->id]), [
                'plan' => $planId
            ])
            ->assertRedirect()
            ->assertSessionMissing('payLink');
    });
});

it('swaps the plan if already subscribed', function () {
    Gate::before(fn () => true);

    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    $membership = Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);

    $planId = 54321;

    $tenant->run(function () use ($user, $tenant, $planId) {
        $subscription = mock(Subscription::class);
        $subscription->shouldReceive('swap')->with($planId)->once()->andReturnSelf();

        $mockTenant = mock(Tenant::class)->makePartial();
        $mockTenant->setRawAttributes($tenant->getAttributes());
        $mockTenant->exists = true;
        
        $mockTenant->shouldReceive('subscribed')->with('default')->andReturn(true);
        $mockTenant->shouldReceive('subscription')->with('default')->andReturn($subscription);

        $this->instance(Tenant::class, $mockTenant);
        app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $mockTenant);

        $this->actingAs($user)
            ->post(route('organisation.billing.subscribe', ['tenant' => $tenant->id]), [
                'plan' => $planId
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'Subscription swapped successfully!');
    });
});

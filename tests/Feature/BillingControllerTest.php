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
        $this->actingAs($user);

        config(['cashier.billables.tenant.plans' => []]);

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

it('blocks demo tenant from billing page', function () {
    $tenant = Tenant::withoutEvents(fn () => Tenant::factory()->create([
        'id' => 'demo',
        'timezone' => 'UTC',
    ]));
    $user = User::factory()->create();
    $membership = Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);
    $role = Role::firstOrCreate(['name' => 'Admin']);
    $membership->roles()->attach($role);

    $tenant->run(function () use ($user, $tenant) {
        config(['features.billing' => false]);

        $this->actingAs($user)
            ->get(route('organisation.billing', ['tenant' => $tenant->id]))
            ->assertForbidden();
    });
});

it('acknowledges a pending checkout for an authorized billing user', function () {
    $user = User::factory()->create();
    $tenant = Tenant::factory()->create([
        'timezone' => 'UTC',
        'billing_user_id' => $user->id,
    ]);
    Membership::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);

    $tenant->run(function () use ($user, $tenant) {
        $this->actingAs($user)
            ->postJson(route('organisation.billing.pending-checkout', ['tenant' => $tenant->id]), [
                'checkout_id' => 'checkout_123',
            ])
            ->assertOk()
            ->assertJson(['acknowledged' => true, 'pending' => true]);

        expect(session('billing.pending_checkout'))->toMatchArray([
            'tenant_id' => $tenant->id,
            'checkout_id' => 'checkout_123',
        ]);
    });
});

it('rejects malformed pending checkout requests', function () {
    $user = User::factory()->create();
    $tenant = Tenant::factory()->create([
        'timezone' => 'UTC',
        'billing_user_id' => $user->id,
    ]);
    Membership::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);

    $tenant->run(function () use ($user, $tenant) {
        $this->actingAs($user)
            ->postJson(route('organisation.billing.pending-checkout', ['tenant' => $tenant->id]), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['checkout_id']);
    });
});

it('blocks unauthorized pending checkout requests', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    Membership::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);

    $tenant->run(function () use ($user, $tenant) {
        $this->actingAs($user)
            ->postJson(route('organisation.billing.pending-checkout', ['tenant' => $tenant->id]), [
                'checkout_id' => 'checkout_123',
            ])
            ->assertForbidden();
    });
});

it('allows accounts team to access billing page', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    $membership = Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);
    $role = Role::firstOrCreate(['name' => 'Accounts Team']);
    $membership->roles()->attach($role);

    $tenant->run(function () use ($user, $tenant) {
        $this->actingAs($user);

        config(['cashier.billables.tenant.plans' => []]);
        config(['features.billing' => false]);

        $this->get(route('organisation.billing', ['tenant' => $tenant->id]))
            ->assertOk();
    });
});

it('allows billing user to access billing page', function () {
    $user = User::factory()->create();
    $tenant = Tenant::factory()->create([
        'timezone' => 'UTC',
        'billing_user_id' => $user->id,
    ]);
    $membership = Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);
    // No specific role, but is billing user

    $tenant->run(function () use ($user, $tenant) {
        $this->actingAs($user);

        config(['cashier.billables.tenant.plans' => []]);
        config(['features.billing' => false]);

        $this->get(route('organisation.billing', ['tenant' => $tenant->id]))
            ->assertOk();
    });
});

it('creates a checkout link for an eligible plan', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    $membership = Membership::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    $membership->roles()->attach(Role::firstOrCreate(['name' => 'Admin']));
    $planId = 54321;

    $tenant->run(function () use ($user, $tenant, $planId) {
        config(['cashier.billables.tenant.plans' => [['yearly_id' => $planId, 'options' => []]]]);

        $builder = mock(SubscriptionBuilder::class);
        $builder->shouldReceive('returnTo')->with(route('organisation.billing', ['tenant' => $tenant]))->andReturnSelf();
        $builder->shouldReceive('create')->once()->andReturn('https://checkout.example.test/link');
        $mockTenant = mock(Tenant::class . '[subscribed,newSubscription]');
        $mockTenant->setRawAttributes($tenant->getAttributes());
        $mockTenant->exists = true;
        $mockTenant->shouldReceive('subscribed')->with('default')->andReturn(false);
        $mockTenant->shouldReceive('newSubscription')->with('default', $planId)->andReturn($builder);
        app()->instance(Tenant::class, $mockTenant);
        app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $mockTenant);

        $this->actingAs($user)
            ->postJson(route('organisation.billing.subscribe', ['tenant' => $tenant->id]), ['plan' => $planId])
            ->assertOk()
            ->assertJson(['link' => 'https://checkout.example.test/link']);
    });
});

it('rejects an unknown subscription plan', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    $membership = Membership::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    $membership->roles()->attach(Role::firstOrCreate(['name' => 'Admin']));

    $tenant->run(function () use ($user, $tenant) {
        config(['cashier.billables.tenant.plans' => []]);

        $this->actingAs($user)
            ->postJson(route('organisation.billing.subscribe', ['tenant' => $tenant->id]), ['plan' => 54321])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['plan']);
    });
});

it('blocks unauthorized users from subscribing', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    Membership::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);

    $tenant->run(function () use ($user, $tenant) {
        $this->actingAs($user)
            ->postJson(route('organisation.billing.subscribe', ['tenant' => $tenant->id]), ['plan' => 54321])
            ->assertStatus(402);
    });
});

it('rejects an ineligible subscription plan', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    $membership = Membership::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    $membership->roles()->attach(Role::firstOrCreate(['name' => 'Admin']));
    $planId = 54321;

    $tenant->run(function () use ($user, $tenant, $planId) {
        config(['cashier.billables.tenant.plans' => [['yearly_id' => $planId, 'options' => ['activeUserQuota' => 5]]]]);
        $mockTenant = mock(Tenant::class . '[getAttribute]');
        $mockTenant->setRawAttributes($tenant->getAttributes());
        $mockTenant->exists = true;
        $mockTenant->shouldReceive('getAttribute')->with('billing_status')->andReturn(['activeUserQuota' => ['activeUserCount' => 10]]);
        $mockTenant->shouldReceive('getAttribute')->with('id')->andReturn($tenant->id);
        $mockTenant->shouldReceive('getAttribute')->with('billingUser')->andReturn($tenant->billingUser);
        app()->instance(Tenant::class, $mockTenant);
        app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $mockTenant);

        $this->actingAs($user)
            ->postJson(route('organisation.billing.subscribe', ['tenant' => $tenant->id]), ['plan' => $planId])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['plan']);
    });
});

it('rejects a duplicate initial subscription', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    $membership = Membership::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    $membership->roles()->attach(Role::firstOrCreate(['name' => 'Admin']));
    $planId = 54321;

    $tenant->run(function () use ($user, $tenant, $planId) {
        config(['cashier.billables.tenant.plans' => [['yearly_id' => $planId, 'options' => []]]]);
        $mockTenant = mock(Tenant::class . '[subscribed]');
        $mockTenant->setRawAttributes($tenant->getAttributes());
        $mockTenant->exists = true;
        $mockTenant->shouldReceive('subscribed')->with('default')->andReturn(true);
        app()->instance(Tenant::class, $mockTenant);
        app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $mockTenant);

        $this->actingAs($user)
            ->postJson(route('organisation.billing.subscribe', ['tenant' => $tenant->id]), ['plan' => $planId])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['plan']);
    });
});

it('fails plan eligibility check if too many active users', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    $membership = Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);
    $role = Role::firstOrCreate(['name' => 'Admin']);
    $membership->roles()->attach($role);

    $planId = 54321;

    // Mock active user count to be higher than plan quota
    $tenant->run(function () use ($user, $tenant, $planId) {
        config(['cashier.billables.tenant.plans' => [
            [
                'yearly_id' => $planId,
                'options' => ['activeUserQuota' => 5]
            ]
        ]]);

        $mockTenant = mock(Tenant::class . '[getAttribute]');
        $mockTenant->setRawAttributes($tenant->getAttributes());
        $mockTenant->exists = true;
        $mockTenant->shouldReceive('getAttribute')->with('billing_status')->andReturn([
            'activeUserQuota' => ['activeUserCount' => 10]
        ]);
        // Also need to mock other attributes that might be accessed
        $mockTenant->shouldReceive('getAttribute')->with('id')->andReturn($tenant->id);
        $mockTenant->shouldReceive('getAttribute')->with('billingUser')->andReturn($tenant->billingUser);

        app()->instance(Tenant::class, $mockTenant);
        app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $mockTenant);

        $this->actingAs($user)
            ->post(route('organisation.billing.swap', ['tenant' => $tenant->id, 'plan' => $planId]))
            ->assertSessionHasErrors(['plan' => 'This plan supports up to 5 active users, but your organisation has 10.']);
    });
});

it('swaps the plan if already subscribed', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    $membership = Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);
    $role = Role::firstOrCreate(['name' => 'Admin']);
    $membership->roles()->attach($role);

    $planId = 54321;

    $tenant->run(function () use ($user, $tenant, $planId) {
        config(['cashier.billables.tenant.plans' => [['yearly_id' => $planId, 'options' => []]]]);

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
            ->post(route('organisation.billing.swap', ['tenant' => $tenant->id, 'plan' => $planId]))
            ->assertRedirect()
            ->assertSessionHas('status', 'Subscription swapped successfully!');
    });
});

it('swaps the plan during a trial', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    $membership = Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);
    $membership->roles()->attach(Role::firstOrCreate(['name' => 'Admin']));
    $planId = 54321;

    $tenant->run(function () use ($user, $tenant, $planId) {
        config(['cashier.billables.tenant.plans' => [['yearly_id' => $planId, 'options' => []]]]);

        $subscription = mock(Subscription::class);
        $subscription->shouldReceive('swap')->with($planId)->once()->andReturnSelf();

        $mockTenant = mock(Tenant::class . '[subscribed,subscription,onTrial]');
        $mockTenant->setRawAttributes($tenant->getAttributes());
        $mockTenant->exists = true;
        $mockTenant->shouldReceive('subscribed')->with('default')->andReturn(true);
        $mockTenant->shouldReceive('subscription')->with('default')->andReturn($subscription);
        $mockTenant->shouldReceive('onTrial')->andReturn(true);

        app()->instance(Tenant::class, $mockTenant);
        app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $mockTenant);

        $this->actingAs($user)
            ->post(route('organisation.billing.swap', ['tenant' => $tenant->id, 'plan' => $planId]))
            ->assertRedirect()
            ->assertSessionHas('status', 'Subscription swapped successfully!');
    });
});

it('pauses the subscription', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    $membership = Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);
    $role = Role::firstOrCreate(['name' => 'Admin']);
    $membership->roles()->attach($role);

    $tenant->run(function () use ($user, $tenant) {
        $subscription = mock(Subscription::class);
        $subscription->shouldReceive('pause')->once()->andReturnSelf();

        $mockTenant = mock(Tenant::class . '[subscribed,subscription]');
        $mockTenant->setRawAttributes($tenant->getAttributes());
        $mockTenant->exists = true;
        $mockTenant->shouldReceive('subscribed')->with('default')->andReturn(true);
        $mockTenant->shouldReceive('subscription')->with('default')->andReturn($subscription);

        app()->instance(Tenant::class, $mockTenant);
        app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $mockTenant);

        $this->actingAs($user)
            ->post(route('organisation.billing.pause', ['tenant' => $tenant->id]))
            ->assertRedirect()
            ->assertSessionHas('status', 'Subscription paused successfully.');
    });
});

it('unpauses the subscription', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    $membership = Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);
    $role = Role::firstOrCreate(['name' => 'Admin']);
    $membership->roles()->attach($role);

    $tenant->run(function () use ($user, $tenant) {
        $subscription = mock(Subscription::class);
        $subscription->shouldReceive('unpause')->once()->andReturnSelf();

        $mockTenant = mock(Tenant::class . '[subscribed,subscription]');
        $mockTenant->setRawAttributes($tenant->getAttributes());
        $mockTenant->exists = true;
        $mockTenant->shouldReceive('subscribed')->with('default')->andReturn(true);
        $mockTenant->shouldReceive('subscription')->with('default')->andReturn($subscription);

        app()->instance(Tenant::class, $mockTenant);
        app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $mockTenant);

        $this->actingAs($user)
            ->post(route('organisation.billing.unpause', ['tenant' => $tenant->id]))
            ->assertRedirect()
            ->assertSessionHas('status', 'Subscription unpaused successfully.');
    });
});

it('cancels the subscription', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'UTC']);
    $user = User::factory()->create();
    $membership = Membership::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);
    $role = Role::firstOrCreate(['name' => 'Admin']);
    $membership->roles()->attach($role);

    $tenant->run(function () use ($user, $tenant) {
        $subscription = mock(Subscription::class);
        $subscription->shouldReceive('cancel')->once()->andReturnSelf();

        $mockTenant = mock(Tenant::class . '[subscribed,subscription]');
        $mockTenant->setRawAttributes($tenant->getAttributes());
        $mockTenant->exists = true;
        $mockTenant->shouldReceive('subscribed')->with('default')->andReturn(true);
        $mockTenant->shouldReceive('subscription')->with('default')->andReturn($subscription);

        app()->instance(Tenant::class, $mockTenant);
        app()->instance(\Stancl\Tenancy\Contracts\Tenant::class, $mockTenant);

        $this->actingAs($user)
            ->post(route('organisation.billing.cancel', ['tenant' => $tenant->id]))
            ->assertRedirect()
            ->assertSessionHas('status', 'Subscription cancelled successfully.');
    });
});

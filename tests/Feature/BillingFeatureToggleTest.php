<?php

use App\Features\BillingFeature;
use App\Http\Middleware\BlockMemberAccessWhenNoActiveSubscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Testing\AssertableInertia;
use Laravel\Pennant\Feature;
use Symfony\Component\HttpKernel\Exception\HttpException;

beforeEach(function (): void {
    $this->tenancy = false;
});

it('shows and updates the tenant billing feature for super admins', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'Australia/Perth']);
    $otherTenant = Tenant::factory()->create(['timezone' => 'Australia/Perth']);
    $superAdmin = User::firstWhere('email', 'hayleybech@gmail.com')
        ?? User::factory()->create(['email' => 'hayleybech@gmail.com']);
    $this->actingAs($superAdmin);

    $this->get(route('central.tenants.show', $tenant))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('billing_enabled', false));

    $this->put(route('central.tenants.billing-feature.update', $tenant), ['enabled' => 'invalid'])
        ->assertSessionHasErrors('enabled');

    expect(Feature::for($tenant)->active('billing-v2'))->toBeFalse();

    $this->put(route('central.tenants.billing-feature.update', $tenant), ['enabled' => true])
        ->assertRedirect();

    expect(Feature::for($tenant)->active('billing-v2'))->toBeTrue()
        ->and(Feature::for($otherTenant)->active('billing-v2'))->toBeFalse();

    $this->get(route('central.tenants.show', $tenant))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('billing_enabled', true));
});

it('does not allow non-super admins to change the tenant billing feature', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'Australia/Perth']);

    $this->actingAs(User::factory()->create())
        ->put(route('central.tenants.billing-feature.update', $tenant), ['enabled' => true])
        ->assertRedirect('/');

    expect(Feature::for($tenant)->active('billing-v2'))->toBeFalse();
});

it('defaults the tenant billing v2 feature to disabled', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'Australia/Perth']);

    expect(Feature::for($tenant)->active('billing-v2'))->toBeFalse();
});

it('allows a tenant to enable billing v2 independently', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'Australia/Perth']);
    $otherTenant = Tenant::factory()->create(['timezone' => 'Australia/Perth']);
    $superAdmin = User::firstWhere('email', 'hayleybech@gmail.com')
        ?? User::factory()->create(['email' => 'hayleybech@gmail.com']);

    $this->actingAs($superAdmin)
        ->put(route('central.tenants.billing-feature.update', $tenant), ['enabled' => true])
        ->assertRedirect();

    expect(Feature::for($tenant)->active('billing-v2'))->toBeTrue()
        ->and(Feature::for($otherTenant)->active('billing-v2'))->toBeFalse();
});

it('enforces legacy subscription access when billing v2 is disabled', function () {
    tenancy()->end();
    $tenant = Tenant::factory()->create(['timezone' => 'Australia/Perth']);
    tenancy()->initialize($tenant);
    $tenant->planSubscriptions()->delete();
    $this->actingAs(User::factory()->create());
    $middleware = app(BlockMemberAccessWhenNoActiveSubscription::class);
    $request = Request::create('/');

    Feature::for($tenant)->deactivate('billing-v2');

    expect(Feature::for($tenant)->active('billing-v2'))->toBeFalse();
    expect(BillingFeature::tenantEnabled(tenant()))->toBeFalse()
        ->and(tenant()->billing_status['valid'])->toBeFalse()
        ->and(auth()->user()->can('update', Tenant::class))->toBeFalse();

    expect(fn () => $middleware->handle($request, fn (Request $request) => response('allowed')))
        ->toThrow(HttpException::class);

    Feature::for($tenant)->activate('billing-v2');

    $response = $middleware->handle($request, fn (Request $request) => response('allowed'));

    expect($response->getContent())->toBe('allowed');
});

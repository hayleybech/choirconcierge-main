<?php

use App\Features\InboxFeature;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Laravel\Pennant\Feature;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->tenancy = false;
});

it('starts tenant inbox rollout disabled and restricts changes to super admins', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'Australia/Perth']);
    $otherTenant = Tenant::factory()->create(['timezone' => 'Australia/Perth']);
    $superAdmin = User::firstWhere('email', 'hayleybech@gmail.com')
        ?? User::factory()->create(['email' => 'hayleybech@gmail.com']);
    $this->actingAs($superAdmin);

    $this->get(route('central.tenants.show', $tenant))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('inbox_enabled', false));

    $this->put(route('central.tenants.inbox-feature.update', $tenant), ['enabled' => 'invalid'])
        ->assertSessionHasErrors('enabled');

    expect(Feature::for($tenant)->active('inbox'))->toBeFalse();

    $this->put(route('central.tenants.inbox-feature.update', $tenant), ['enabled' => true])
        ->assertRedirect();

    expect(Feature::for($tenant)->active('inbox'))->toBeTrue();
    expect(Feature::for($otherTenant)->active('inbox'))->toBeFalse();

    $this->actingAs(User::factory()->create())
        ->put(route('central.tenants.inbox-feature.update', $tenant), ['enabled' => false])
        ->assertRedirect('/');

    expect(Feature::for($tenant)->active('inbox'))->toBeTrue();
});

it('allows super admins to set a personal inbox override without changing tenant rollout', function (string $preference, bool $expected) {
    $tenant = Tenant::factory()->create(['timezone' => 'Australia/Perth']);
    $superAdmin = User::firstWhere('email', 'hayleybech@gmail.com')
        ?? User::factory()->create(['email' => 'hayleybech@gmail.com']);
    Feature::for($tenant)->activate('inbox');

    $this->actingAs($superAdmin)
        ->put(route('central.account.inbox-preference.update'), ['preference' => $preference])
        ->assertRedirect(route('central.account.edit', ['tab' => 'inbox']));

    expect(Feature::for($superAdmin)->value('inbox-super-admin-override'))->toBe($expected)
        ->and(Feature::for($tenant)->active('inbox'))->toBeTrue()
        ->and(InboxFeature::isEnabledFor($tenant, $superAdmin))->toBe($expected);

    $this->get(route('central.account.edit'))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('inbox_preference', $expected)
            ->where('can.manage_inbox_preview', true));
})->with([
    'opt in' => ['enabled', true],
    'opt out' => ['disabled', false],
]);

it('clears the super admin override when set to inherit', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'Australia/Perth']);
    $superAdmin = User::firstWhere('email', 'hayleybech@gmail.com')
        ?? User::factory()->create(['email' => 'hayleybech@gmail.com']);
    Feature::for($tenant)->activate('inbox');
    Feature::for($superAdmin)->deactivate('inbox-super-admin-override');

    $this->actingAs($superAdmin)
        ->put(route('central.account.inbox-preference.update'), ['preference' => 'inherit'])
        ->assertRedirect(route('central.account.edit', ['tab' => 'inbox']));

    expect(Feature::for($superAdmin)->value('inbox-super-admin-override'))->toBeNull()
        ->and(Feature::for($tenant)->active('inbox'))->toBeTrue()
        ->and(InboxFeature::isEnabledFor($tenant, $superAdmin))->toBeTrue();
});

it('does not allow non-super admins to change the personal override', function () {
    $this->actingAs(User::factory()->create())
        ->put(route('central.account.inbox-preference.update'), ['preference' => 'enabled'])
        ->assertRedirect('/');
});

it('requires authentication to change the personal override', function () {
    $this->put(route('central.account.inbox-preference.update'), ['preference' => 'enabled'])
        ->assertRedirect(route('login'));
});

it('rejects invalid personal preferences without saving an override', function () {
    $superAdmin = User::firstWhere('email', 'hayleybech@gmail.com')
        ?? User::factory()->create(['email' => 'hayleybech@gmail.com']);

    $this->actingAs($superAdmin)
        ->put(route('central.account.inbox-preference.update'), ['preference' => 'sometimes'])
        ->assertSessionHasErrors('preference');

    expect(InboxFeature::personalOverride($superAdmin))->toBeNull();
});

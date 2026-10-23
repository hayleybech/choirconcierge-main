<?php

use App\Models\Membership;
use function Pest\Laravel\post;

test('organisation settings can be saved without overwriting billing settings', function () {
    $this->actingAsRole('Admin');

    $tenant = tenant();
    $billingUser = Membership::factory()->create();
    $tenant->update(['billing_user_id' => $billingUser->id]);

    post(the_tenant_route('organisation.update'), [
        'tab' => 'organisation',
        'name' => 'Updated Organisation',
        'primary_domain' => 'updated-organisation',
        'timezone' => 'Australia/Sydney',
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    expect($tenant->fresh()->name)->toBe('Updated Organisation')
        ->and($tenant->fresh()->billing_user_id)->toBe($billingUser->id);
});

test('billing settings can be saved without requiring or changing organisation settings', function () {
    $this->actingAsRole('Admin');

    $tenant = tenant();
    $organisationName = $tenant->name;
    $billingUser = Membership::factory()->create();

    post(the_tenant_route('organisation.update'), [
        'tab' => 'billing',
        'billing_user' => $billingUser->id,
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    expect($tenant->fresh()->billing_user_id)->toBe($billingUser->id)
        ->and($tenant->fresh()->name)->toBe($organisationName);
});

test('organisation settings reject an unsupported submitted tab', function () {
    $this->actingAsRole('Admin');

    post(the_tenant_route('organisation.update'), [
        'tab' => 'ensembles',
    ])->assertSessionHasErrors('tab');
});

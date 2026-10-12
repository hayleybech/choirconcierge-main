<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->tenancy = false;
    $this->actingAs(User::firstOrCreate(['email' => 'hayleybech@gmail.com']));
});

test('a super admin can add gratis to a tenant', function () {
    $tenant = Tenant::factory()->create(['has_gratis' => false]);

    $response = $this->post(route('central.tenants.gratis.toggle', $tenant));

    $response->assertRedirect();
    expect($tenant->fresh()->has_gratis)->toBeTrue();
});

test('a super admin can remove gratis from a tenant', function () {
    $tenant = Tenant::factory()->create(['has_gratis' => true]);

    $response = $this->post(route('central.tenants.gratis.toggle', $tenant));

    $response->assertRedirect();
    expect($tenant->fresh()->has_gratis)->toBeFalse();
});

test('a regular user cannot toggle gratis', function () {
    $this->actingAs(User::factory()->create());
    $tenant = Tenant::factory()->create(['has_gratis' => false]);

    $this->post(route('central.tenants.gratis.toggle', $tenant))
        ->assertForbidden();

    expect($tenant->fresh()->has_gratis)->toBeFalse();
});

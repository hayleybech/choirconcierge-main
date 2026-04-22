<?php

use App\Navigation\Navigation;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

test('navigation returns route names and string permissions by default', function () {
    $navigation = new Navigation();
    $data = $navigation->get();

    expect($data)->toBeArray();
    expect($data[0])->toHaveKey('route');
    expect($data[0])->not->toHaveKey('url');
    expect($data[0]['can'])->toBeString();
});

test('navigation returns relative URLs and boolean permissions when app mode is true', function () {
    $tenant = \App\Models\Tenant::create('test', 'Test', 'Australia/Perth');
    $tenant->domains()->create(['domain' => 'test']);
    tenancy()->initialize($tenant);

    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Mock Gate to return true for 'view_dash'
    Gate::define('view_dash', fn () => true);

    $navigation = new Navigation();
    $data = $navigation->get(app: true);

    expect($data)->toBeArray();
    
    $dashboard = $data[0];
    
    // Check Dashboard item
    expect($dashboard['name'])->toBe('Dashboard');
    expect($dashboard)->not->toHaveKey('route');
    expect($dashboard)->toHaveKey('url');
    // We expect the relative URL. the_tenant_route uses route() under the hood.
    // In tests, it might include the domain if not careful, but we passed false for absolute.
    expect($dashboard['url'])->toContain('/test');
    
    expect($dashboard['can'])->toBeBool();
    expect($dashboard['can'])->toBeTrue();
    
    expect($dashboard['showAsActiveForRoutes'])->toBeArray();
    expect($dashboard['showAsActiveForRoutes'][0])->toContain('/test');
});

test('navigation converts nested items in app mode', function () {
    $tenant = \App\Models\Tenant::create('test-2', 'Test 2', 'Australia/Perth');
    $tenant->domains()->create(['domain' => 'test-2']);
    tenancy()->initialize($tenant);

    $user = User::factory()->create();
    $this->actingAs($user);

    $navigation = new Navigation();
    $data = $navigation->get(app: true);
    
    $singers = collect($data)->firstWhere('name', 'Singers');
    expect($singers)->not->toBeNull();
    expect($singers['items'])->not->toBeEmpty();
    
    $addNew = $singers['items'][0];
    expect($addNew['name'])->toBe('Add New');
    expect($addNew)->toHaveKey('url');
    expect($addNew)->not->toHaveKey('route');
    expect($addNew['url'])->toContain('/test-2');
});

test('navigation converts showAsActiveForRoutes wildcards to relative URLs in app mode', function () {
    $tenant = \App\Models\Tenant::create('test-3', 'Test 3', 'Australia/Perth');
    $tenant->domains()->create(['domain' => 'test-3']);
    tenancy()->initialize($tenant);

    $user = User::factory()->create();
    $this->actingAs($user);

    $navigation = new Navigation();
    $data = $navigation->get(app: true);
    
    $singers = collect($data)->firstWhere('name', 'Singers');
    expect($singers['showAsActiveForRoutes'])->toContain(the_tenant_route('singers.index', [], false));
});
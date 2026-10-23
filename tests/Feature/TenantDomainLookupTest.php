<?php

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('tenant lookup eagerly loads domains for reuse during the request', function () {
    $tenant = Tenant::factory()->create(['id' => 'domain-lookup-tenant']);
    $tenant->domains()->create(['domain' => 'domain-lookup', 'is_primary' => true]);

    DB::enableQueryLog();

    $resolvedTenant = Tenant::findByDomain('domain-lookup');

    expect($resolvedTenant)->not->toBeNull();
    expect($resolvedTenant->relationLoaded('domains'))->toBeTrue();

    $domainQueriesAfterResolution = collect(DB::getQueryLog())
        ->filter(fn (array $query): bool => str_contains(strtolower($query['query']), 'from `domains`'))
        ->count();

    expect($resolvedTenant->primary_domain)->toBe('domain-lookup');
    expect($resolvedTenant->host)->toBe('domain-lookup.'.central_domain());
    expect($resolvedTenant->domains)->toHaveCount(1);

    $domainQueriesAfterAccess = collect(DB::getQueryLog())
        ->filter(fn (array $query): bool => str_contains(strtolower($query['query']), 'from `domains`'))
        ->count();

    expect($domainQueriesAfterAccess)->toBe($domainQueriesAfterResolution);

    DB::disableQueryLog();
});

test('primary domain accessor loads domains only once when the relation is missing', function () {
    $tenant = Tenant::factory()->create(['id' => 'primary-domain-accessor-tenant']);
    $tenant->domains()->create(['domain' => 'primary-domain-accessor', 'is_primary' => true]);
    $tenant = Tenant::findOrFail($tenant->id);

    DB::enableQueryLog();

    expect($tenant->primary_domain)->toBe('primary-domain-accessor');
    expect($tenant->relationLoaded('domains'))->toBeTrue();

    $domainQueriesAfterFirstAccess = collect(DB::getQueryLog())
        ->filter(fn (array $query): bool => str_contains(strtolower($query['query']), 'from `domains`'))
        ->count();

    expect($tenant->primary_domain)->toBe('primary-domain-accessor');
    expect($tenant->host)->toBe('primary-domain-accessor.'.central_domain());

    $domainQueriesAfterRepeatedAccess = collect(DB::getQueryLog())
        ->filter(fn (array $query): bool => str_contains(strtolower($query['query']), 'from `domains`'))
        ->count();

    expect($domainQueriesAfterRepeatedAccess)->toBe($domainQueriesAfterFirstAccess);

    DB::disableQueryLog();
});

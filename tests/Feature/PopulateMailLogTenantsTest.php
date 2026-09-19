<?php

use App\Models\MailLog;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it populates mail log tenants based on recipient domains', function () {
    $centralDomain = central_domain();
    
    // 1. Setup tenants
    $tenant1 = Tenant::factory()->create(['id' => 'tenant-1']);
    $tenant1->domains()->create(['domain' => 't1', 'is_primary' => true]);

    $tenant2 = Tenant::factory()->create(['id' => 'tenant-2']);
    $tenant2->domains()->create(['domain' => 't2', 'is_primary' => true]);

    // 2. Create existing logs WITHOUT relationships
    $log1 = MailLog::factory()->create([
        'to' => 'list@t1.' . $centralDomain,
    ]);
    $log2 = MailLog::factory()->create([
        'cc' => 'member@t2.' . $centralDomain,
    ]);
    $log3 = MailLog::factory()->create([
        'to' => 'someone@otherdomain.com',
    ]);
    $log4 = MailLog::factory()->create([
        'to' => 'admin@t1.' . $centralDomain . ', user@t2.' . $centralDomain,
    ]);

    expect($log1->tenants)->toHaveCount(0)
        ->and($log2->tenants)->toHaveCount(0)
        ->and($log3->tenants)->toHaveCount(0)
        ->and($log4->tenants)->toHaveCount(0);

    // 3. Run command
    $this->artisan('mail-logs:populate-tenants')
        ->assertSuccessful();

    // 4. Verify
    expect($log1->refresh()->tenants)->toHaveCount(1)
        ->and($log1->tenants->first()->id)->toBe('tenant-1')
        ->and($log2->refresh()->tenants)->toHaveCount(1)
        ->and($log2->tenants->first()->id)->toBe('tenant-2')
        ->and($log3->refresh()->tenants)->toHaveCount(0)
        ->and($log4->refresh()->tenants)->toHaveCount(2)
        ->and($log4->tenants->pluck('id'))->toContain('tenant-1', 'tenant-2');
});

test('it handles legacy domain formats', function () {
    $tenant = Tenant::factory()->create(['id' => 'legacy-tenant']);
    $tenant->domains()->create(['domain' => 'legacy', 'is_primary' => true]);

    $log1 = MailLog::factory()->create([
        'to' => 'user@legacy', // Exact primary domain match
    ]);
    
    $log2 = MailLog::factory()->create([
        'to' => 'user@legacy.something.else', // First part match
    ]);

    $this->artisan('mail-logs:populate-tenants')
        ->assertSuccessful();

    expect($log1->refresh()->tenants)->toHaveCount(1)
        ->and($log1->tenants->first()->id)->toBe('legacy-tenant')
        ->and($log2->refresh()->tenants)->toHaveCount(1)
        ->and($log2->tenants->first()->id)->toBe('legacy-tenant');
});

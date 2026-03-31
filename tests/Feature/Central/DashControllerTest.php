<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->tenancy = false;
});

function getSuperAdmin(): User
{
    return User::firstWhere('email', 'hayleybech@gmail.com') ?? User::factory()->create(['email' => 'hayleybech@gmail.com']);
}

test('index does not show stats for non super admin', function () {
    $this->actingAs(User::factory()->create(['email' => 'not-admin@example.com']));

    $this->get(route('central.dash'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('tenantStats', null)
        );
});

test('trial conversion rate calculation', function () {
    $this->actingAs(getSuperAdmin());

    // Clean up any existing tenants to ensure a clean state for this test if RefreshDatabase failed
    Tenant::query()->delete();

    // 1. Tenant on trial (not converted)
    Tenant::factory()->create(['id' => 'trial1', 'timezone' => 'Australia/Perth']);
    DB::table('customers')->insert([
        'billable_id' => 'trial1',
        'billable_type' => Tenant::class,
        'trial_ends_at' => Carbon::now()->addDays(14),
    ]);

    // 2. Tenant converted (has active subscription, trial ended in past)
    $converted = Tenant::factory()->create(['id' => 'converted1', 'timezone' => 'Australia/Perth']);
    $converted->subscriptions()->create([
        'name' => 'default',
        'paddle_id' => 123,
        'paddle_status' => 'active',
        'paddle_plan' => 1,
        'quantity' => 1,
        'trial_ends_at' => Carbon::now()->subDays(1),
    ]);

    // 3. Another converted tenant
    $converted2 = Tenant::factory()->create(['id' => 'converted2', 'timezone' => 'Australia/Perth']);
    $converted2->subscriptions()->create([
        'name' => 'default',
        'paddle_id' => 124,
        'paddle_status' => 'active',
        'paddle_plan' => 1,
        'quantity' => 1,
        'trial_ends_at' => null, // No trial ends at also counts as converted if status is active
    ]);

    // 4. Tenant NOT active (should NOT count as converted)
    $notConverted = Tenant::factory()->create(['id' => 'notconverted', 'timezone' => 'Australia/Perth']);
    $notConverted->subscriptions()->create([
        'name' => 'default',
        'paddle_id' => 125,
        'paddle_status' => 'deleted', // Not active
        'paddle_plan' => 1,
        'quantity' => 1,
        'trial_ends_at' => Carbon::now()->subDays(10),
    ]);

    // Total trialed = 1 (trial1) + 2 (converted1, converted2) + 1 (notConverted) = 4
    // Total converted = 2
    // Rate = 2/4 * 100 = 50.0

    $this->get(route('central.dash'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('tenantStats.trialConversionRate', 50)
        );
});

test('median retention time calculation', function () {
    Carbon::setTestNow(Carbon::create(2026, 7, 1, 12, 0, 0));
    $this->actingAs(getSuperAdmin());

    // Clean up any existing tenants
    Tenant::query()->delete();

    // 1. Tenant 1: 3 months retention (April 1 to July 1)
    $t1 = Tenant::factory()->create(['id' => 't1', 'timezone' => 'Australia/Perth']);
    $t1->subscriptions()->create([
        'name' => 'default',
        'paddle_id' => 201,
        'paddle_status' => 'active',
        'paddle_plan' => 1,
        'quantity' => 1,
        'created_at' => Carbon::now()->subMonths(3),
        'ends_at' => null, // Active, so uses Carbon::now()
    ]);

    // 2. Tenant 2: 6 months retention (Jan 1 to July 1)
    $t2 = Tenant::factory()->create(['id' => 't2', 'timezone' => 'Australia/Perth']);
    $t2->subscriptions()->create([
        'name' => 'default',
        'paddle_id' => 202,
        'paddle_status' => 'active',
        'paddle_plan' => 1,
        'quantity' => 1,
        'created_at' => Carbon::now()->subMonths(6),
        'ends_at' => null, // Jan 1 to July 1 = 6 months
    ]);

    // 3. Tenant 3: 12 months retention (July 1 last year to July 1)
    $t3 = Tenant::factory()->create(['id' => 't3', 'timezone' => 'Australia/Perth']);
    $t3->subscriptions()->create([
        'name' => 'default',
        'paddle_id' => 203,
        'paddle_status' => 'active',
        'paddle_plan' => 1,
        'quantity' => 1,
        'created_at' => Carbon::now()->subMonths(12),
        'ends_at' => null,
    ]);

    // Durations: 3, 6, 12 -> Median 6
    
    $this->get(route('central.dash'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('tenantStats.medianRetentionTime', 6)
        );

    Carbon::setTestNow();
});

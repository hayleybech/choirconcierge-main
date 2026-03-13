<?php

namespace Tests\Feature\Central;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class DashControllerTest extends TestCase
{
    use RefreshDatabase;

    protected bool $tenancy = false;

    protected function getSuperAdmin(): User
    {
        return User::factory()->create(['email' => 'hayleybech@gmail.com']);
    }

    public function test_index_returns_ok_for_super_admin(): void
    {
        $this->actingAs($this->getSuperAdmin());

        $this->get('/app')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Central/Dash/Show')
                ->has('tenantStats')
                ->has('tenantStats.trialConversionRate')
                ->has('tenantStats.medianPurchaseValue')
                ->has('tenantStats.medianRetentionTime')
            );
    }

    public function test_index_does_not_show_stats_for_non_super_admin(): void
    {
        $this->actingAs(User::factory()->create(['email' => 'not-admin@example.com']));

        $this->get('/app')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('tenantStats', null)
            );
    }

    public function test_trial_conversion_rate_calculation(): void
    {
        $this->actingAs($this->getSuperAdmin());

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

        $this->get('/app')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('tenantStats.trialConversionRate', 50)
            );
    }

    public function test_median_purchase_value_calculation(): void
    {
        $this->actingAs($this->getSuperAdmin());

        $baseReceipt = [
            'billable_id' => 1,
            'billable_type' => 'user',
            'receipt_url' => 'http',
            'paid_at' => now(),
            'tax' => '0',
            'currency' => 'USD',
            'quantity' => 1,
        ];

        // Even number of receipts: 10, 20, 30, 40 -> median (20+30)/2 = 25
        DB::table('receipts')->insert(array_merge($baseReceipt, ['amount' => 10, 'checkout_id' => '1', 'order_id' => 'o1', 'receipt_url' => 'http1']));
        DB::table('receipts')->insert(array_merge($baseReceipt, ['amount' => 30, 'checkout_id' => '2', 'order_id' => 'o2', 'receipt_url' => 'http2']));
        DB::table('receipts')->insert(array_merge($baseReceipt, ['amount' => 20, 'checkout_id' => '3', 'order_id' => 'o3', 'receipt_url' => 'http3']));
        DB::table('receipts')->insert(array_merge($baseReceipt, ['amount' => 40, 'checkout_id' => '4', 'order_id' => 'o4', 'receipt_url' => 'http4']));

        $this->get('/app')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('tenantStats.medianPurchaseValue', 25)
            );

        // Odd number: 10, 20, 30, 40, 100 -> median 30
        DB::table('receipts')->insert(array_merge($baseReceipt, ['amount' => 100, 'checkout_id' => '5', 'order_id' => 'o5', 'receipt_url' => 'http5']));

        $this->get('/app')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('tenantStats.medianPurchaseValue', 30)
            );
    }

    public function test_median_retention_time_calculation(): void
    {
        $this->actingAs($this->getSuperAdmin());

        // 1. Tenant 1: 10 days retention
        $t1 = Tenant::factory()->create(['id' => 't1', 'timezone' => 'Australia/Perth']);
        $t1->subscriptions()->create([
            'name' => 'default',
            'paddle_id' => 201,
            'paddle_status' => 'active',
            'paddle_plan' => 1,
            'quantity' => 1,
            'created_at' => Carbon::now()->subDays(10),
            'ends_at' => null, // Active, so uses Carbon::now()
        ]);

        // 2. Tenant 2: 20 days retention
        $t2 = Tenant::factory()->create(['id' => 't2', 'timezone' => 'Australia/Perth']);
        $t2->subscriptions()->create([
            'name' => 'default',
            'paddle_id' => 202,
            'paddle_status' => 'deleted',
            'paddle_plan' => 1,
            'quantity' => 1,
            'created_at' => Carbon::now()->subDays(30),
            'ends_at' => Carbon::now()->subDays(10), // subDays(30) to subDays(10) = 20 days
        ]);

        // 3. Tenant 3: 50 days retention
        $t3 = Tenant::factory()->create(['id' => 't3', 'timezone' => 'Australia/Perth']);
        $t3->subscriptions()->create([
            'name' => 'default',
            'paddle_id' => 203,
            'paddle_status' => 'active',
            'paddle_plan' => 1,
            'quantity' => 1,
            'created_at' => Carbon::now()->subDays(50),
            'ends_at' => null,
        ]);

        // Durations: 10, 20, 50 -> Median 20
        
        $this->get('/app')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('tenantStats.medianRetentionTime', 20)
            );
    }
}

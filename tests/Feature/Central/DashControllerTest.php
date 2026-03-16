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

//    public function test_index_returns_ok_for_super_admin(): void
//    {
//        $this->actingAs($this->getSuperAdmin());
//
//        $this->get('/app')
//            ->assertOk()
//            ->assertInertia(fn (AssertableInertia $page) => $page
//                ->component('Central/Dash/Show')
//                ->has('tenantStats')
//                ->has('tenantStats.trialConversionRate')
//                ->has('tenantStats.medianRetentionTime')
//            );
//    }

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

    public function test_median_retention_time_calculation(): void
    {
        $this->actingAs($this->getSuperAdmin());

        // 1. Tenant 1: 3 months retention
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

        // 2. Tenant 2: 6 months retention
        $t2 = Tenant::factory()->create(['id' => 't2', 'timezone' => 'Australia/Perth']);
        $t2->subscriptions()->create([
            'name' => 'default',
            'paddle_id' => 202,
            'paddle_status' => 'deleted',
            'paddle_plan' => 1,
            'quantity' => 1,
            'created_at' => Carbon::now()->subMonths(18),
            'ends_at' => Carbon::now()->subMonths(12), // subMonths(18) to subMonths(12) = 6 months
        ]);

        // 3. Tenant 3: 12 months retention
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
        
        $this->get('/app')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('tenantStats.medianRetentionTime', 6)
            );
    }
}

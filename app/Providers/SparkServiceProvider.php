<?php

namespace App\Providers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Spark\Plan;
use Spark\Spark;

class SparkServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Spark::billable(Tenant::class)->resolve(function (Request $request) {
            return $request->tenant;
        });

        Spark::billable(Tenant::class)->authorize(function (Tenant $billable, Request $request) {
            return $billable->id !== 'demo'
                && $request->user() && (
                    $request->user()->is($billable->billingUser)
                    || $request->user()
                        ->memberships()
                        ->firstWhere('tenant_id', $billable->id)
                        ?->hasRole('Admin')
                    || $request->user()
                        ->memberships()
                        ->firstWhere('tenant_id', $billable->id)
                        ?->hasRole('Accounts Team')
                );
        });

        Spark::billable(Tenant::class)->checkPlanEligibility(function (Tenant $billable, Plan $plan) {
            // @todo add tiers for users (unless we decide to re-jig our pricing plan)
            // if ($billable->projects > 5 && $plan->name == 'Basic') {
            //     throw ValidationException::withMessages([
            //         'plan' => 'You have too many projects for the selected plan.'
            //     ]);
            // }
        });
    }
}

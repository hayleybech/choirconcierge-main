<?php

namespace App\Providers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
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
            // @todo feature flag here
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
            if($billable->billingStatus['activeUserQuota']['activeUserCount'] > $plan->options['activeUserQuota']) {
                throw ValidationException::withMessages([
                     'plan' => 'You have too many active users for the selected plan.',
                 ]);
            }
        });
    }
}

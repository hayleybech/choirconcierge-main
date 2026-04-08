<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function index(): Response
    {
        $this->authorize('update', tenant());

        $plans = collect(config('spark.billables.tenant.plans'))->map(fn($plan) => [
            'name' => $plan['name'],
            'description' => $plan['short_description'],
            'features' => $plan['features'],
            'quota' => $plan['options']['activeUserQuota'],
            'id' => $plan['yearly_id'],
            'payLink' => tenant()->subscribed('default')
                ? null
                : tenant()->newSubscription('default', $plan['yearly_id'])
                    ->returnTo(route('organisation.billing', ['tenant' => tenant()]))
                    ->create(),
        ]);

        return Inertia::render('Tenants/Billing', [
            'plans' => $plans,
            'tenant' => tenant()->load('subscriptions')->append(['plan', 'billing_status']),
            'termsUrl' => config('spark.terms_url'),
        ]);
    }

    public function swap(Request $request): RedirectResponse
    {
        $this->authorize('update', tenant());

        $planId = (int) $request->input('plan');
        $tenant = tenant();

        if (!$tenant->subscribed('default')) {
            return redirect()->back()->withErrors(['plan' => 'You must be subscribed to a plan to swap plans.']);
        }

        if($tenant->onTrial()) {
            return redirect()->back()->withErrors(['plan' => 'You must cannot switch plans during your free trial period.']);
        }

        try {
            $tenant->subscription('default')->swap($planId);
            return redirect()->back()->with('status', 'Subscription swapped successfully!');
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->withErrors(['plan' => 'Failed to swap plans. Please try again later.']);
        }
    }
}

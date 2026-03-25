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
                    ->returnTo(route('organisation.billing'))
                    ->create(),
        ]);

        return Inertia::render('Tenants/Billing', [
            'plans' => $plans,
            'tenant' => tenant()->load('subscriptions')->append(['plan', 'billing_status']),
            'termsUrl' => config('spark.terms_url'),
        ]);
    }

    public function subscribe(Request $request): RedirectResponse
    {
        $this->authorize('update', tenant());

        $planId = (int) $request->input('plan');
        $tenant = tenant();

        if ($tenant->subscribed('default')) {
            try {
                $tenant->subscription('default')->swap($planId);
                return redirect()->back()->with('status', 'Subscription swapped successfully!');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['plan' => $e->getMessage()]);
            }
        }

        if (config('cashier.vendor_id') && config('cashier.vendor_id') !== 'your-paddle-vendor-id') {
            $payLink = $tenant->newSubscription('default', $planId)
                ->returnTo(route('organisation.billing'))
                ->create();
        }

        return redirect()->back();
    }
}

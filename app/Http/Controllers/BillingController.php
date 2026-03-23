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

        $planId = $request->input('plan');

        // This would typically redirect to a Paddle checkout
        // For now, we'll return a redirect with a message as a placeholder
        // In a real scenario, you'd use $request->tenant()->newSubscription('default', $planId)->checkout();
        
        return redirect()->back()->with('status', 'Redirection to payment provider (Paddle) would happen here for plan: ' . $planId);
    }
}

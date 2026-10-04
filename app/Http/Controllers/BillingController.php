<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    private function planEligibility(Tenant $tenant, array $plan): ?string
    {
        $quota = $plan['options']['activeUserQuota'] ?? null;

        if ($quota === null) {
            return null;
        }

        $activeUserCount = $tenant->billing_status['activeUserQuota']['activeUserCount'];

        return $activeUserCount > $quota
            ? "This plan supports up to {$quota} active users, but your organisation has {$activeUserCount}."
            : null;
    }

    private function authorizeBilling(Request $request, Tenant $tenant): void
    {
        $user = $request->user();

        $isAuthorized = $tenant->id !== 'demo'
            && $user && (
                $user->is($tenant->billingUser)
                || $user->memberships()
                    ->where('tenant_id', $tenant->id)
                    ->get()
                    ->contains(fn (Membership $membership) => $membership->hasRole('Admin') || $membership->hasRole('Accounts Team'))
            );

        if (! $isAuthorized) {
            abort(403);
        }
    }

    public function index(Request $request): Response
    {
        $this->authorizeBilling($request, tenant());
        $tenant = tenant();

        $plans = collect(config('spark.billables.tenant.plans'))->map(fn($plan) => [
            'name' => $plan['name'],
            'description' => $plan['short_description'],
            'features' => $plan['features'],
            'quota' => $plan['options']['activeUserQuota'],
            'id' => $plan['yearly_id'],
            'eligible' => ($eligibilityReason = $this->planEligibility($tenant, $plan)) === null,
            'eligibilityReason' => $eligibilityReason,
        ]);

        return Inertia::render('Tenants/Billing', [
            'plans' => $plans,
            'tenant' => $tenant->load('subscriptions')->append(['plan', 'billing_status']),
            'termsUrl' => config('spark.terms_url'),
        ]);
    }

    public function subscribe(Request $request): JsonResponse
    {
        $tenant = tenant();
        $this->authorizeBilling($request, $tenant);

        $validated = $request->validate([
            'plan' => ['required', 'integer'],
        ]);
        $planId = (int) $validated['plan'];
        $plan = collect(config('spark.billables.tenant.plans'))->firstWhere('yearly_id', $planId);

        if (! $plan) {
            throw ValidationException::withMessages([
                'plan' => 'The selected plan is invalid.',
            ]);
        }

        if (($eligibilityReason = $this->planEligibility($tenant, $plan)) !== null) {
            throw ValidationException::withMessages([
                'plan' => $eligibilityReason,
            ]);
        }

        if ($tenant->subscribed('default')) {
            throw ValidationException::withMessages([
                'plan' => 'You are already subscribed to a plan.',
            ]);
        }

        $link = $tenant->newSubscription('default', $planId)
            ->returnTo(route('organisation.billing', ['tenant' => $tenant]))
            ->create();

        return response()->json(['link' => $link]);
    }

    public function pendingCheckout(Request $request): JsonResponse
    {
        $tenant = tenant();
        $this->authorizeBilling($request, $tenant);

        $validated = $request->validate([
            'checkout_id' => ['required', 'string', 'max:255'],
        ]);

        session()->put('billing.pending_checkout', [
            'tenant_id' => $tenant->id,
            'checkout_id' => $validated['checkout_id'],
        ]);

        return response()->json(['acknowledged' => true, 'pending' => true]);
    }

    public function swap(Request $request): RedirectResponse
    {
        $this->authorizeBilling($request, tenant());

        $planId = (int) $request->input('plan');
        $tenant = tenant();

        $plan = collect(config('spark.billables.tenant.plans'))->firstWhere('yearly_id', $planId);

        if ($plan && ($eligibilityReason = $this->planEligibility($tenant, $plan)) !== null) {
            throw ValidationException::withMessages([
                'plan' => $eligibilityReason,
            ]);
        }

        if (!$tenant->subscribed('default')) {
            return redirect()->back()->withErrors(['plan' => 'You must be subscribed to a plan to swap plans.']);
        }

        if($tenant->onTrial()) {
            return redirect()->back()->withErrors(['plan' => 'You must cannot switch plans during your free trial period.']);
        }

        try {
            $tenant->subscription('default')->swap($planId);
            return redirect()->back()->with('status', 'Subscription swapped successfully!');
        } catch (Throwable $e) {
            return redirect()->back()->withErrors(['plan' => 'Failed to swap plans. Please try again later.']);
        }
    }
    public function cancel(Request $request): RedirectResponse
    {
        $this->authorizeBilling($request, tenant());

        try {
            tenant()->subscription('default')->cancel();
            return redirect()->back()->with('status', 'Subscription cancelled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['subscription' => 'Failed to cancel subscription. Please try again later.']);
        }
    }

    public function pause(Request $request): RedirectResponse
    {
        $this->authorizeBilling($request, tenant());

        try {
            tenant()->subscription('default')->pause();
            return redirect()->back()->with('status', 'Subscription paused successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['subscription' => 'Failed to pause subscription. Please try again later.']);
        }
    }

    public function unpause(Request $request): RedirectResponse
    {
        $this->authorizeBilling($request, tenant());

        try {
            tenant()->subscription('default')->unpause();
            return redirect()->back()->with('status', 'Subscription unpaused successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['subscription' => 'Failed to unpause subscription. Please try again later.']);
        }
    }
}

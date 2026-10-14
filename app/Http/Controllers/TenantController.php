<?php

namespace App\Http\Controllers;

use App\Http\Requests\TenantSettingsRequest;
use App\Models\Tenant;
use DateTimeZone;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Stancl\Tenancy\Database\Models\Domain;

class TenantController extends Controller
{
    public function edit(): Response
    {
        $this->authorize('update', tenant());

        return Inertia::render('Tenants/Edit', [
            'organisation' => tenant()->loadMissing(['domains', 'billingUser'])
                ->load(['ensembles' => fn ($query) => $query->withCount('enrolments')])
                ->append(['primary_domain', 'plan']),
            'centralDomain' => central_domain(),
            'timezones' => DateTimeZone::listIdentifiers(),
        ]);
    }

    public function update(TenantSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($validated['tab'] === 'billing') {
            tenant()->update(['billing_user_id' => $validated['billing_user']]);

            return redirect()->back()->with(['status' => 'Organisation settings saved.']);
        }

        tenant()->loadMissing('domains');

        tenant()->update([
            'name' => $validated['name'],
            'timezone' => $validated['timezone'],
        ]);

        if ($request->hasFile('logo')) {
            tenant()->updateLogo($request->file('logo'), $request->file('logo')->hashName());
        }

        $this->updatePrimaryDomain(tenant(), $validated['primary_domain']);

        return redirect()->back()->with(['status' => 'Organisation settings saved.']);
    }

    private function updatePrimaryDomain(Tenant $tenant, string $domain)
    {
        $tenant->domains()->firstWhere('is_primary')?->update(['is_primary' => false]);

        $tenant->domains()->updateOrCreate(['domain' => $domain], ['is_primary' => true]);
    }
}

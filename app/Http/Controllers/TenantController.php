<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TenantController extends Controller
{
    public function edit(): Response
    {
        $this->authorize('update', tenant());

        $tenant = tenant()->load(['domains', 'ensembles'])->append(['primary_domain']);

        return Inertia::render('Tenants/Edit', [
            'organisation' => $tenant,
            'centralDomain' => central_domain(),
            'timezones' => DateTimeZone::listIdentifiers(),
        ]);
    }

    public function update(Request $request)
    {
        $this->authorize('update', tenant());

        $request->validate([
            'name' => ['required', 'max:127'],
            'logo' => ['sometimes', 'nullable', 'file', 'mimetypes:image/png', 'max:10240'],
            'primary_domain' => ['required', 'max:127'],
            'timezone' => ['required', Rule::in(DateTimeZone::listIdentifiers())],
        ]);

        tenant()->update($request->only([
            'name',
            'timezone',
        ]));

        if($request->hasFile('logo')) {
            tenant()->updateLogo($request->file('logo'), $request->file('logo')->hashName());
        }

        $this->updatePrimaryDomain(tenant(), $request->input('primary_domain'));

        return redirect()->back()->with(['status' => 'Organisation settings saved.']);
    }

    private function updatePrimaryDomain(Tenant $tenant, string $domain)
    {
        $tenant->domains()->firstWhere('is_primary')?->update(['is_primary' => false]);

        $tenant->domains()->updateOrCreate(['domain' => $domain], ['is_primary' => true]);
    }
}

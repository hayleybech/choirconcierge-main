<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TenantController extends Controller
{
    public function edit(): Response
    {
        $this->authorize('update', tenant());

        $tenant = tenant()->load('domains')->append('primary_domain');

        return Inertia::render('Tenants/Edit', [
            'tenant' => $tenant,
            'centralDomain' => central_domain(),
            'timezones' => DateTimeZone::listIdentifiers(),
            'choirLogo' => $tenant->logo_url,
        ]);
    }

    public function update(Request $request)
    {
        $this->authorize('update', tenant());

        $request->validate([
            'choir_name' => ['required', 'max:127'],
            'choir_logo' => ['sometimes', 'nullable', 'file', 'mimetypes:image/png', 'max:10240'],
            'primary_domain' => ['required', 'max:127'],
            'timezone' => ['required', Rule::in(DateTimeZone::listIdentifiers())],
        ]);

        tenant()->update($request->only([
            'choir_name',
            'timezone',
        ]));

        if($request->hasFile('choir_logo')) {
            tenant()->updateLogo($request->file('choir_logo'), $request->file('choir_logo')->hashName());
        }

        $this->updatePrimaryDomain(tenant(), $request->input('primary_domain'));

        return redirect()->back()->with(['status' => 'Choir settings saved.']);
    }

    private function updatePrimaryDomain(Tenant $tenant, string $domain)
    {
        $tenant->domains()->firstWhere('is_primary')?->update(['is_primary' => false]);

        $tenant->domains()->updateOrCreate(['domain' => $domain], ['is_primary' => true]);
    }
}

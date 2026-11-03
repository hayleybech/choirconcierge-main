<?php

namespace App\Http\Controllers\Central;

use App\Features\BillingFeature;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TenantBillingFeatureController extends Controller
{
    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        BillingFeature::setTenantEnabled($tenant, (bool) $validated['enabled']);

        return back()->with('status', 'Tenant Billing setting updated.');
    }
}

<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\Tenant;
use Inertia\Inertia;

class TenantOnboardingController extends Controller
{
    public function __invoke(Tenant $tenant) {
	    return Inertia::render('Central/Tenants/Onboarding', [
		    'tenant' => $tenant,
			'setupDone' => Membership::query()
				->where('tenant_id', $tenant->id)
				->where('user_id', auth()->user()->id)
				->exists(),
	    ]);
    }
}

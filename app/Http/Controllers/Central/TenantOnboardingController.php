<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Inertia\Inertia;

class TenantOnboardingController extends Controller
{
    public function __invoke(Tenant $tenant) {
	    return Inertia::render('Central/Tenants/Onboarding', [
		    'tenant' => $tenant->append('setup_done'),
	    ]);
    }
}

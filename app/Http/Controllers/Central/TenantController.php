<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Inertia\Inertia;
use Inertia\Response;

class TenantController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Central/Tenants/Index', [
            'tenants' => Tenant::with('domains')->get()->append(['timezone', 'billing_status'])->values(),
        ]);
    }

    public function show(Tenant $tenant): Response
    {
        return Inertia::render('Central/Tenants/Show', [
            'tenant' => $tenant,
        ]);
    }
}

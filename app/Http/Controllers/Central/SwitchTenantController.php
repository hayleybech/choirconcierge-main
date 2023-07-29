<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class SwitchTenantController extends Controller
{
    private const REDIRECT_ROUTE = 'dash';

    public function start(Request $request, Tenant $newTenant): Response|RedirectResponse
    {
        return Inertia::location(route(self::REDIRECT_ROUTE, ['tenant' => $newTenant->id]));
    }
}

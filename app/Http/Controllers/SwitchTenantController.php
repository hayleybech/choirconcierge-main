<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Stancl\Tenancy\Features\UserImpersonation;

class SwitchTenantController extends Controller
{
    private const REDIRECT_ROUTE = 'dash';

    public function start(Request $request, Tenant $newTenant): Response
    {
        if(session()->has('impersonation:active')) {
            abort(401);
        }

        $newTenant->load('domains');

        $token = tenancy()->impersonate(
            $newTenant,
            auth()->id(),
            tenant_route($newTenant->host, self::REDIRECT_ROUTE)
        );

        return Inertia::location(tenant_route($newTenant->host, 'tenants.switch.login', $token));
    }

    public function loginWithToken(string $token): RedirectResponse
    {
        return UserImpersonation::makeResponse($token);
    }
}

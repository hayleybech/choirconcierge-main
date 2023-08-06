<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class BlockMemberAccessWhenNoActiveSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if(! config('features.billing')) {
            return $next($request);
        }

        if(auth()->user()?->can('update', Tenant::class)
            || Gate::allows('update-fees')
        ) {
            return $next($request);
        }

        if(tenant()->billing_status['valid']) {
            return $next($request);
        }

        abort(402);
    }
}

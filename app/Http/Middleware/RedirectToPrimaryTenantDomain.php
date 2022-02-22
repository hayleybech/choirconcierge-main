<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RedirectToPrimaryTenantDomain
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        if(tenancy()->initialized && $request->getHost() !== tenant('host')) {
            return redirect('//'.tenant('host'));
        }

        return $next($request);
    }
}

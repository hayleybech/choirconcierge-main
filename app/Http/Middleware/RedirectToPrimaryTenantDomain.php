<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectToPrimaryTenantDomain
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (tenancy()->initialized && $request->getHost() !== tenant('host')) {
            return redirect('//'.tenant('host'));
        }

        return $next($request);
    }
}

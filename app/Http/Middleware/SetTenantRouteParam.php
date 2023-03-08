<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class SetTenantRouteParam
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
	    if (tenancy()->initialized){
		    URL::defaults(['tenant' => tenant('id')]);
	    }

        return $next($request);
    }
}

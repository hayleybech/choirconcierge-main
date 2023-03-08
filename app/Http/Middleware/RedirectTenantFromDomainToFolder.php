<?php

namespace App\Http\Middleware;

use App\ManuallyInitializeTenancyByDomainOrSubdomain;
use Closure;
use Illuminate\Http\Request;

class RedirectTenantFromDomainToFolder
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
	    $isACentralDomain = in_array($request->getHost(), config('tenancy.central_domains'), true);
	    if($isACentralDomain) {
		    return $next($request);
	    }

		app(ManuallyInitializeTenancyByDomainOrSubdomain::class)->handle($request->getHost());

		// if we found a tenant by subdomain
	    if (tenancy()->initialized && $request->getHost() !== central_domain().'/'.tenant('id')) {
	        // redirect to corresponding path
		    return redirect('//'.central_domain().'/'.tenant('id').'/'.$request->path());
	    }

        return $next($request);
    }
}


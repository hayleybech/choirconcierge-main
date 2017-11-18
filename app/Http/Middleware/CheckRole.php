<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;
use Session;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
		if ( ! $request->user()->hasRole($role)) {
            Session::flash('message', "You don't have permission to do that. ");
			return Redirect::to("/dash");
        }
		
        return $next($request);
    }
}

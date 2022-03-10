<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Session;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (! $request->user()->singer->hasRole($role)) {
            Session::flash('message', "You don't have permission to do that. ");

            return Redirect::to('/');
        }

        return $next($request);
    }
}

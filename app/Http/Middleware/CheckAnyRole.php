<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Session;

class CheckAnyRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user() && ! $request->user()->singer->isEmployee()) {
            Session::flash('message', "You don't have permission to do anything. ");

            return Redirect::to('/');
        }

        return $next($request);
    }
}

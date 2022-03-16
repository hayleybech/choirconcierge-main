<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SetFeatureFlags
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Session::get('rebuild') === 'on') {
            config(['features.rebuild' => true]);
        }

        if (Session::get('rebuild') === 'off') {
            config(['features.rebuild' => false]);
        }

        return $next($request);
    }
}

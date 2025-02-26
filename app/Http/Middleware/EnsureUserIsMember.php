<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class EnsureUserIsMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(auth()?->user()?->isSuperAdmin) {
            return $next($request);
        }

        if(! auth()?->user()?->membership) {
            abort(403);
        }

        if(in_array(
            auth()?->user()?->membership?->category->name, [
            'Archived Members',
            'Archived Prospects',
        ])) {
            abort(403);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Session;

class CheckRoleOrSameUser
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
        $singer = $request->route('singer');

        if ($request->user()->membership->id === $singer->id) {
            return $next($request);
        }

        if ($request->user()->membership->hasRole($role)) {
            return $next($request);
        }

        Session::flash('message', "You don't have permission to do that. ");

        return Redirect::to('/');
    }
}

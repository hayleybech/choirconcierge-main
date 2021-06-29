<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;
use Session;

class CheckRoleOrSameUser
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
		$singer = $request->route('singer');

		if ($request->user()->singer->id === $singer->id) {
			return $next($request);
		}

		if ($request->user()->hasRole($role)) {
			return $next($request);
		}

		Session::flash('message', "You don't have permission to do that. ");
		return Redirect::to('/');
	}
}

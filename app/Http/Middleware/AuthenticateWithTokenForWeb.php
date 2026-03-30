<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithTokenForWeb
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);

            if ($accessToken && $accessToken->tokenable) {
                // Manually log the user in using the 'web' guard
                Auth::guard('web')->login($accessToken->tokenable);
            }
        }

        return $next($request);
    }
}

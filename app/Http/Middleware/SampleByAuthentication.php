<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Nightwatch\Facades\Nightwatch;
use Throwable;

class SampleByAuthentication
{
    public static function rate(float $loggedInRate, float $loggedOutRate): string
    {
        return self::class.':'.$loggedInRate.','.$loggedOutRate;
    }

    public static function sampleRate(bool $isLoggedIn, float $loggedInRate, float $loggedOutRate): float
    {
        return $isLoggedIn ? $loggedInRate : $loggedOutRate;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): mixed  $next
     */
    public function handle(Request $request, Closure $next, float $loggedInRate, float $loggedOutRate): mixed
    {
        try {
            Nightwatch::sample(self::sampleRate($request->user() !== null, $loggedInRate, $loggedOutRate));
        } catch (Throwable $e) {
            Nightwatch::unrecoverableExceptionOccurred($e);
        }

        return $next($request);
    }
}

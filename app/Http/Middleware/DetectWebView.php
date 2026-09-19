<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectWebView
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('X-WebView-Source') === 'react-native-app') {
            // The request is coming from your React Native WebView
            $request->attributes->set('isWebView', true);
        } else {
            $request->attributes->set('isWebView', false);
        }

        return $next($request);
    }
}

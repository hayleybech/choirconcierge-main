<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\RobotsMiddleware\RobotsMiddleware;

class NoRobots extends RobotsMiddleware
{
    protected function shouldIndex(Request $request): bool
    {
        return false;
    }
}

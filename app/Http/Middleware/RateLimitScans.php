<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitScans
{
    public function handle($request, Closure $next)
    {
        $key = 'scans:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json(['error'=>'Rate limited'], 429);
        }
        RateLimiter::hit($key, 3600);
        return $next($request);
    }
}

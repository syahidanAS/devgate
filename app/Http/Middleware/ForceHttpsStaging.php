<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttpsStaging
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.env') === 'staging' && !$request->secure()) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}
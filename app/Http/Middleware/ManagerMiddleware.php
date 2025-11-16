<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ManagerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'manager') {
            return response()->json(['error' => 'Access denied. Managers only.'], 403);
        }

        return $next($request);
    }
}

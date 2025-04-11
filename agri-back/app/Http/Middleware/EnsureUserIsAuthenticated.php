<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class EnsureUserIsAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('user')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
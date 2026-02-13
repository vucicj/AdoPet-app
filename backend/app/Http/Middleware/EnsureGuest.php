<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGuest
{
    /**
     * Handle an incoming request - redirect authenticated users away from public routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            return response()->json([
                'message' => 'You are already authenticated.',
            ], 400);
        }

        return $next($request);
    }
}

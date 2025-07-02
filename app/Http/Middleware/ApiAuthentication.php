<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthentication
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (!$request->bearerToken()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication token is required.',
                    'status_code' => 401,
                ], 401);
            }

            return $next($request);
        } catch (AuthenticationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authenticated to access this resource.',
                'status_code' => 401,
            ], 401);
        }
    }
} 
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
        public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Use $request->user() which is populated by the auth:sanctum middleware for API requests
        if (! $request->user()) {
            // For API requests, return a JSON 401 response instead of redirecting
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            // For web requests, redirect to the login page
            return redirect()->route('login');
        }

        $user = $request->user();

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // For API requests, return a JSON 403 response for authorization failure
        if ($request->expectsJson()) {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }

        abort(403, 'Unauthorized');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        if (! $user) {
            \Log::info('RoleMiddleware: User not authenticated, redirecting to login.');
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                \Log::info('RoleMiddleware: User has role: ' . $role);
                return $next($request);
            }
        }

        abort(403, 'Unauthorized');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            \Log::info('AdminMiddleware: User not authenticated, redirecting to login.');
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin')) {
            \Log::info('AdminMiddleware: User does not have required role, aborting with 403.');
            abort(403, 'Unauthorized. This action requires admin privileges.');
        }

        return $next($request);
    }
}

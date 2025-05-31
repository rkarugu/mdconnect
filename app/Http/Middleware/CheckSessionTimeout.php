<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class CheckSessionTimeout
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in
        if (Auth::check()) {
            // Get the last activity timestamp from the session
            $lastActivity = Session::get('last_activity');
            
            // If there's no last activity timestamp, set it to now
            if (!$lastActivity) {
                Session::put('last_activity', time());
                return $next($request);
            }

            // Check if the session has expired (2 minutes = 120 seconds)
            if (time() - $lastActivity > 120) {
                // Clear all session data
                Session::flush();
                
                // Clear the remember me cookie if it exists
                Cookie::queue(Cookie::forget(Auth::getRecallerName()));
                
                // Logout the user
                Auth::logout();
                
                // Clear the session cookie
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // If it's an AJAX request, return JSON response
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Session expired'], 401);
                }
                
                // Redirect to login with a message
                return redirect()->route('login')
                    ->with('error', 'Your session has expired due to inactivity. Please login again.');
            }

            // Update the last activity timestamp
            Session::put('last_activity', time());
        }

        return $next($request);
    }
} 
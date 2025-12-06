<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has admin role
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            // Redirect to login if not authenticated
            if (!auth()->check()) {
                return redirect()->route('admin.login')->with('error', 'Please login as admin.');
            }

            // Redirect to home if not admin
            return redirect()->route('home')->with('error', 'Access denied. Admin only.');
        }

        return $next($request);
    }
}

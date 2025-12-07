<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Add security headers to response - P3 Enhancement
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Content Security Policy
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net unpkg.com; " .
            "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net fonts.googleapis.com; " .
            "img-src 'self' data: https: http:; " .
            "font-src 'self' fonts.gstatic.com; " .
            "connect-src 'self';"
        );

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // XSS Protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // MIME type sniffing protection
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=()'
        );

        return $response;
    }
}

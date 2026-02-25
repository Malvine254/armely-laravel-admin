<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Content Security Policy (CSP)
        // 'unsafe-inline' is required for Google Tag Manager, reCAPTCHA, and existing inline scripts.
        // Tighten further by replacing 'unsafe-inline' with nonces once templates are refactored.
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' "
                . "code.jquery.com "
                . "cdn.datatables.net "
                . "cdn.jsdelivr.net "
                . "cdnjs.cloudflare.com "
                . "stackpath.bootstrapcdn.com "
                . "www.google.com "
                . "www.googletagmanager.com "
                . "copilotstudio.microsoft.com "
                . "copilotstudio.preview.microsoft.com "
                . "fonts.bunny.net",
            "style-src 'self' 'unsafe-inline' "
                . "cdn.datatables.net "
                . "cdn.jsdelivr.net "
                . "cdnjs.cloudflare.com "
                . "stackpath.bootstrapcdn.com "
                . "fonts.googleapis.com "
                . "fonts.bunny.net",
            "img-src 'self' data: blob: https:",
            "font-src 'self' "
                . "fonts.googleapis.com "
                . "fonts.gstatic.com "
                . "fonts.bunny.net "
                . "cdnjs.cloudflare.com "
                . "cdn.jsdelivr.net",
            "frame-src 'self' "
                . "www.youtube.com "
                . "www.google.com "
                . "copilotstudio.microsoft.com "
                . "copilotstudio.preview.microsoft.com",
            "frame-ancestors 'self'",
            "connect-src 'self' "
                . "www.google.com "
                . "www.googletagmanager.com",
            "media-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Strict Transport Security (HSTS)
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        // X-Content-Type-Options
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-Frame-Options
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Cross Origin Resource Policy (CORP)
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        // Permissions Policy — disable browser features not needed
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        return $response;
    }
}

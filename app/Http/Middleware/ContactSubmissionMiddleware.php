<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ContactSubmissionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->query('token');
        $sessionToken = session('contact_thank_you_token');
        $tokenTime = session('contact_thank_you_time');

        // Debug logging
        Log::debug('ContactSubmissionMiddleware', [
            'url_token' => $token,
            'session_token' => $sessionToken,
            'token_time' => $tokenTime,
            'current_time' => time(),
            'session_id' => session()->getId()
        ]);

        // Check if token is valid and not expired (valid for 5 minutes)
        if (!$token || !$sessionToken || $token !== $sessionToken || !$tokenTime || (time() - $tokenTime > 300)) {
            Log::warning('Token validation failed', [
                'token_match' => ($token === $sessionToken),
                'has_token' => !empty($token),
                'has_session_token' => !empty($sessionToken),
                'time_valid' => $tokenTime && (time() - $tokenTime <= 300)
            ]);
            // Return 404 if trying to access without valid token
            abort(404);
        }

        // Clear the token after validating (one-time use)
        session()->forget(['contact_thank_you_token', 'contact_thank_you_time']);

        return $next($request);
    }
}

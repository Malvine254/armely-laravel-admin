<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectCanonicalHost
{
    public function handle(Request $request, Closure $next)
    {
        $canonicalHost = strtolower(trim((string) config('app.canonical_host', '')));

        if ($canonicalHost === '') {
            return $next($request);
        }

        $currentHost = strtolower(trim((string) $request->getHost()));
        if ($currentHost === '' || $currentHost === $canonicalHost) {
            return $next($request);
        }

        if (in_array($currentHost, ['localhost', '127.0.0.1', '::1'], true)) {
            return $next($request);
        }

        $scheme = $request->getScheme();
        $targetUrl = $scheme . '://' . $canonicalHost . $request->getRequestUri();

        return redirect()->away($targetUrl, 301);
    }
}

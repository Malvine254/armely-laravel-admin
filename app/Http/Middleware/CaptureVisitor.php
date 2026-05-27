<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CaptureVisitor
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        $country = null;
        $country_code = null;

        // Keep request middleware non-blocking: avoid expensive lookups for local/private IPs.
        $isLocal = in_array($ip, ['127.0.0.1', '::1'], true);
        $isPrivate = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;

        if (!$isLocal && !$isPrivate && function_exists('geoip')) {
            try {
                $location = geoip($ip);
                $country = $location->country ?? null;
                $country_code = $location->iso_code ?? ($location->country_code ?? null);
            } catch (\Throwable $e) {
                Log::debug('CaptureVisitor geoip lookup skipped: ' . $e->getMessage());
            }
        }

        Log::info('CaptureVisitor', [
            'ip' => $ip,
            'country' => $country,
            'country_code' => $country_code,
            'user_agent' => $request->userAgent(),
        ]);

        return $next($request);
    }
}

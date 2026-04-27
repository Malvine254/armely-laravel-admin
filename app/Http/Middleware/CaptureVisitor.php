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

        $location = null;
        if (function_exists('geoip')) {
            $location = geoip($ip);
        }

        $country = $location->country ?? null;
        $country_code = $location->iso_code ?? ($location->country_code ?? null);

        Log::info('CaptureVisitor', [
            'ip' => $ip,
            'country' => $country,
            'country_code' => $country_code,
            'user_agent' => $request->userAgent(),
        ]);

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VisitController extends Controller
{
    // Option 1: GeoIP local helper (MaxMind/Torann)
    public function geoip(Request $request)
    {
        $ip = $request->ip();
        $location = function_exists('geoip') ? geoip($ip) : null;

        return response()->json([
            'ip_address' => $ip,
            'country' => $location->country ?? null,
            'country_code' => $location->iso_code ?? ($location->country_code ?? null),
        ]);
    }

    // Option 2: API-based (ipapi.co)
    public function ipapi(Request $request)
    {
        $ip = $request->ip();

        try {
            $resp = Http::timeout(3)->get("https://ipapi.co/{$ip}/json/");
            $json = $resp->ok() ? $resp->json() : [];
        } catch (\Throwable $e) {
            $json = [];
        }

        return response()->json([
            'ip_address' => $ip,
            'country' => $json['country_name'] ?? 'Unknown',
            'country_code' => $json['country_code'] ?? 'XX',
        ]);
    }

    // Option 3: Cloudflare headers
    public function cloudflare(Request $request)
    {
        return response()->json([
            'ip_address' => $request->header('CF-Connecting-IP') ?? $request->ip(),
            'country' => $request->header('CF-IPCountry') ?? null,
        ]);
    }
}

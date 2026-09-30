<?php

namespace App\Services\Mela\Memory;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class VisitorCountry
{
    public function resolve(?string $ip): ?string
    {
        if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return null;
        }

        $key = 'mela:country:v2:'.hash('sha256', $ip.'|'.config('app.key'));
        $cached = Cache::get($key);
        if (is_array($cached)) return $cached['code'] ?? null;

        $code = $this->localCountry($ip);
        if (!$code && config('mela.geoip.api_enabled', true)) {
            try {
                $response = Http::acceptJson()->connectTimeout(1)->timeout(2)
                    ->get('https://ipwho.is/'.rawurlencode($ip), ['fields' => 'success,country_code']);
                if ($response->successful() && $response->json('success') === true) {
                    $code = $this->validCode($response->json('country_code'));
                }
            } catch (\Throwable) {
                // Country lookup must never prevent a visitor from chatting.
            }
        }

        // Cache failures too, to avoid repeatedly calling an unavailable provider.
        Cache::put($key, ['code' => $code], $code ? now()->addDays(7) : now()->addMinutes(15));
        return $code;
    }

    protected function localCountry(string $ip): ?string
    {
        if (!function_exists('geoip')) return null;
        try {
            $location = geoip($ip);
            if (!$location || ($location->default ?? false)) return null;
            return $this->validCode($location->iso_code ?? $location->country_code ?? null);
        } catch (\Throwable) {
            return null;
        }
    }

    private function validCode(mixed $value): ?string
    {
        if (!is_string($value)) return null;
        $code = strtoupper(trim($value));
        return preg_match('/^[A-Z]{2}$/', $code) && !in_array($code, ['XX', 'ZZ']) ? $code : null;
    }
}

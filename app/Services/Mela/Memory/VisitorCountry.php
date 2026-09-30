<?php

namespace App\Services\Mela\Memory;

class VisitorCountry
{
    public function resolve(?string $ip): ?string
    {
        if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) || !function_exists('geoip')) {
            return null;
        }

        try {
            $location = geoip($ip);
            // GeoIP's default location is not evidence of a visitor's country.
            if (!$location || ($location->default ?? false)) {
                return null;
            }
            $code = strtoupper((string) ($location->iso_code ?? $location->country_code ?? ''));

            return preg_match('/^[A-Z]{2}$/', $code) && !in_array($code, ['XX', 'ZZ']) ? $code : null;
        } catch (\Throwable) {
            return null;
        }
    }
}

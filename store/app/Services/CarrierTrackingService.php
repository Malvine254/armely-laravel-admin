<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CarrierTrackingService
{
    public function resolveLiveStatus(?string $carrier, ?string $trackingNumber, ?string $trackingUrl = null): ?array
    {
        $carrier = strtolower(trim((string) $carrier));
        $trackingNumber = $this->extractTrackingNumber($trackingNumber ?: $trackingUrl);
        $trackingUrl = $this->normalizeTrackingUrl($carrier, $trackingNumber, $trackingUrl);

        if ($carrier === '' || $trackingNumber === null || $trackingUrl === null) {
            return null;
        }

        $cacheKey = 'carrier-track:' . md5($carrier . '|' . $trackingNumber . '|' . $trackingUrl);

        return Cache::remember($cacheKey, now()->addSeconds(90), function () use ($carrier, $trackingNumber, $trackingUrl) {
            $html = $this->fetchTrackingPageHtml($trackingUrl);
            if ($html === null) {
                return null;
            }

            $parsed = $this->parseStatusFromTrackingPage($html);
            if ($parsed === null) {
                return null;
            }

            return [
                'carrier' => $carrier,
                'tracking_number' => $trackingNumber,
                'tracking_url' => $trackingUrl,
                'status' => $parsed['status'],
                'raw_status' => $parsed['raw_status'],
                'checked_at' => now()->toIso8601String(),
                'source' => 'carrier_page',
            ];
        });
    }

    public function extractTrackingNumber(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        if ($this->looksLikeUrl($text)) {
            $parsed = $this->extractTrackingNumberFromUrl($text);
            if ($parsed !== null) {
                return $parsed;
            }
        }

        if (preg_match('/[A-Za-z0-9\-]{8,}/', $text, $matches) !== 1) {
            return null;
        }

        return trim((string) $matches[0]);
    }

    private function normalizeTrackingUrl(string $carrier, ?string $trackingNumber, ?string $trackingUrl): ?string
    {
        $trackingUrl = trim((string) ($trackingUrl ?? ''));
        if ($trackingUrl !== '') {
            // Keep the original carrier URL when available. Some carriers include
            // additional query context (for example FedEx trkqual) that can improve
            // status page resolution and should not be stripped.
            return $trackingUrl;
        }

        if ($trackingNumber === null) {
            return null;
        }

        return match ($carrier) {
            'fedex' => 'https://www.fedex.com/wtrk/track/?trknbr=' . rawurlencode($trackingNumber),
            'ups' => 'https://www.ups.com/track?tracknum=' . rawurlencode($trackingNumber),
            'usps' => 'https://tools.usps.com/go/TrackConfirmAction?tLabels=' . rawurlencode($trackingNumber),
            'dhl' => 'https://www.dhl.com/en/express/tracking.html?AWB=' . rawurlencode($trackingNumber),
            default => null,
        };
    }

    private function fetchTrackingPageHtml(string $trackingUrl): ?string
    {
        try {
            $response = Http::timeout(12)
                ->retry(1, 250)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                ->get($trackingUrl);

            if (!$response->successful()) {
                return null;
            }

            return (string) $response->body();
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseStatusFromTrackingPage(string $html): ?array
    {
        $plain = strtolower(strip_tags($html));
        $plain = preg_replace('/\s+/', ' ', (string) $plain);

        if ($plain === null || trim($plain) === '') {
            return null;
        }

        if (str_contains($plain, 'system down') || str_contains($plain, 'temporarily unavailable') || str_contains($plain, 'technical difficulties')) {
            return [
                'status' => 'pending',
                'raw_status' => 'carrier tracking temporarily unavailable',
            ];
        }

        $deliveredHints = [
            ' delivered ',
            ' delivered\n',
            ' delivered\r',
            'delivery complete',
            'package delivered',
            'was delivered',
            'proof of delivery',
            'delivered at',
            'shipment facts delivered',
        ];

        foreach ($deliveredHints as $hint) {
            if (!str_contains($plain, trim($hint))) {
                continue;
            }

            return [
                'status' => 'delivered',
                'raw_status' => 'delivered',
            ];
        }

        $rules = [
            'delivered' => [
                'delivered',
                'delivery complete',
                'package delivered',
                'was delivered',
                'proof of delivery',
            ],
            'in_transit' => [
                'in transit',
                'on the way',
                'out for delivery',
                'on fedex vehicle for delivery',
                'at local facility',
                'arrived at fedex location',
                'departed fedex location',
            ],
            'shipped' => [
                'picked up',
                'shipment picked up',
                'left facility',
                'label created',
                'shipment information sent',
            ],
            'pending' => [
                'pending',
                'tracking details unavailable',
                'not yet available',
            ],
            'exception' => [
                'exception',
                'delivery exception',
                'unable to deliver',
                'returned to shipper',
            ],
        ];

        foreach ($rules as $status => $keywords) {
            foreach ($keywords as $keyword) {
                if (!str_contains($plain, $keyword)) {
                    continue;
                }

                return [
                    'status' => $status,
                    'raw_status' => $keyword,
                ];
            }
        }

        if (str_contains($plain, 'fedex') && str_contains($plain, 'track')) {
            return [
                'status' => 'in_transit',
                'raw_status' => 'tracking page available',
            ];
        }

        return null;
    }

    private function extractTrackingNumberFromUrl(string $url): ?string
    {
        if (!$this->looksLikeUrl($url)) {
            return null;
        }

        $query = [];
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        foreach (['trknbr', 'tracknumbers', 'trackingnumber', 'tracknum', 'tracking_number', 'tLabels', 'AWB'] as $key) {
            if (!isset($query[$key])) {
                continue;
            }

            $value = trim((string) $query[$key]);
            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function looksLikeUrl(string $value): bool
    {
        return Str::startsWith(strtolower($value), ['http://', 'https://']);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'tracking_number',
        'carrier',
        'tracking_url',
        'status',
        'shipped_at',
        'expected_delivery_at',
        'delivered_at',
        'raw_data',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'shipped_at' => 'datetime',
        'expected_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered' && $this->delivered_at;
    }

    public function getTrackingUrl(): ?string
    {
        if ($this->tracking_url) {
            return $this->normalizeTrackingUrl($this->tracking_url);
        }

        $carriers = [
            'fedex' => 'https://www.fedex.com/fedextrack/?trknbr={number}',
            'ups' => 'https://www.ups.com/track?tracknum={number}',
            'usps' => 'https://tools.usps.com/go/TrackConfirmAction?tLabels={number}',
            'dhl' => 'https://www.dhl.com/en/en/shipped.html?tracking_number={number}',
        ];

        $carrier = strtolower($this->carrier ?? '');
        $template = $carriers[$carrier] ?? null;

        if ($template && $this->tracking_number) {
            return str_replace('{number}', $this->tracking_number, $template);
        }

        return null;
    }

    private function normalizeTrackingUrl(string $url): string
    {
        if (str_contains($url, 'tracking.fedex.com/track')) {
            $query = [];
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
            $trackingNumber = trim((string) ($query['tracknumbers'] ?? $query['trknbr'] ?? $this->tracking_number ?? ''));

            if ($trackingNumber !== '') {
                return 'https://www.fedex.com/fedextrack/?trknbr=' . rawurlencode($trackingNumber);
            }
        }

        return $url;
    }
}

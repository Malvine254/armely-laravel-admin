<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\TDSynnexService;
use App\Services\NotificationService;
use App\Services\CarrierTrackingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateOrderStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 120, 300]; // Retry after 1m, 2m, 5m

    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle(
        TDSynnexService $tdsynnexService,
        NotificationService $notificationService,
        CarrierTrackingService $carrierTrackingService
    ): void
    {
        try {
            $this->order->loadMissing(['quote', 'invoice', 'shipments']);

            if (!$this->canCheckShippingStatus($this->order)) {
                return;
            }

            $poNumber = trim((string) ($this->order->quote_id ?: $this->order->order_number));
            if ($poNumber === '') {
                return;
            }

            // Fetch current status from TD SYNNEX XML POStatus when a PO number is available.
            $tdStatus = $tdsynnexService->checkPoStatus($poNumber);

            // checkPoStatus returns a structured failure payload instead of throwing.
            // Never replace a valid cached TD snapshot with that error response.
            if (($tdStatus['success'] ?? true) === false || isset($tdStatus['error'])) {
                Log::warning("TD SYNNEX returned no usable status for order {$this->order->order_number}", [
                    'po_number' => $poNumber,
                    'error' => $tdStatus['error'] ?? null,
                ]);
                return;
            }

            // Normalize the raw TD SYNNEX status code to our canonical set
            $rawStatus = $this->extractTdOrderStatus($tdStatus);
            $trackingNumber = $this->deepFindFirstByKeys($tdStatus, ['tracking_number', 'trackingNumber', 'TrackingNumber', 'carrierTrackingNumber', 'shipmentTrackingNumber', 'proNumber', 'ProNumber']);
            $shippingStatus = $this->deepFindFirstByKeys($tdStatus, ['shippingStatus', 'shipping_status', 'shipmentStatus', 'ShipmentStatus', 'deliveryStatus', 'DeliveryStatus', 'status', 'Status']);
            $freightAmount = $this->deepFindFirstByKeys($tdStatus, ['freight', 'Freight', 'freightAmount', 'poFreight', 'shippingAmount', 'shipping_amount', 'totalFreight', 'TotalFreight']);
            $estimatedDelivery = $this->deepFindFirstByKeys($tdStatus, ['estimatedDeliveryDate', 'EstimatedDeliveryDate', 'estimatedShipDate', 'EstimatedShipDate', 'estimatedArrivalDate', 'EstimatedArrivalDate', 'ETADate', 'etaDate']);
            $actualDelivery = $this->deepFindFirstByKeys($tdStatus, ['DeliveredDate', 'deliveredDate', 'DeliveryDate', 'deliveryDate', 'ActualDeliveryDate', 'actualDeliveryDate']);
            $carrier = $this->deepFindFirstByKeys($tdStatus, ['ShipMethodDescription', 'shipMethodDescription', 'Carrier', 'carrier', 'shipMethod', 'ShipMethod']);
            $shipDate = $this->deepFindFirstByKeys($tdStatus, ['DateShipped', 'dateShipped', 'ShipDatetime', 'shipDatetime', 'ShipDate', 'shipDate']);
            $tdOrderNumber = $this->deepFindFirstByKeys($tdStatus, ['OrderNumber', 'orderNumber', 'order_number', 'SynnexOrderNumber', 'synnexOrderNumber']);
            $deliverySignal = strtolower((string) ($shippingStatus ?? ''));
            $statusToNormalize = str_contains($deliverySignal, 'deliver')
                ? 'delivered'
                : $rawStatus;
            $normalized = self::normalizeTdStatus((string) $statusToNormalize) ?: $this->order->status;
            $oldTracking = is_array($this->order->tracking_info) ? $this->order->tracking_info : [];
            $resolvedTrackingNumber = trim((string) ($trackingNumber ?: ($oldTracking['tracking_number'] ?? '')));
            $resolvedCarrier = trim((string) ($carrier ?: ($oldTracking['carrier'] ?? '')));
            $trackingUrl = $oldTracking['carrier_tracking_url'] ?? $oldTracking['tracking_url'] ?? null;
            $carrierLive = $carrierTrackingService->resolveLiveStatus(
                $resolvedCarrier,
                $resolvedTrackingNumber,
                is_string($trackingUrl) ? $trackingUrl : null
            );

            $trackingInfo = array_merge($oldTracking, array_filter([
                'tracking_number' => $trackingNumber ? (string) $trackingNumber : null,
                'shipping_status' => $shippingStatus ? (string) $shippingStatus : null,
                'estimated_delivery_date' => $estimatedDelivery ? (string) $estimatedDelivery : null,
                'actual_delivery_date' => $actualDelivery ? (string) $actualDelivery : null,
                'carrier' => $carrier ? (string) $carrier : null,
                'ship_date' => $shipDate ? (string) $shipDate : null,
                'td_order_status_code' => $rawStatus !== '' ? $rawStatus : null,
                'td_order_number' => $tdOrderNumber ? (string) $tdOrderNumber : null,
                'td_synced_at' => now()->toIso8601String(),
                'freight_amount' => is_numeric((string) $freightAmount) ? (float) $freightAmount : null,
                'freight_source' => is_numeric((string) $freightAmount) ? 'td_synnex_po_status' : null,
                'carrier_live_status' => $carrierLive['raw_status'] ?? null,
                'carrier_live_status_normalized' => $carrierLive['status'] ?? null,
                'carrier_live_checked_at' => $carrierLive['checked_at'] ?? null,
                'carrier_tracking_url' => $carrierLive['tracking_url'] ?? null,
            ], fn ($value) => $value !== null && $value !== ''));

            $oldStatus = (string) ($this->order->status ?? '');
            // TD remains authoritative through fulfillment, but PO status often
            // stays "Invoiced" after last-mile delivery. A verified delivery is
            // terminal and must never regress during a later PO status refresh.
            $normalized = $this->resolveEffectiveStatus(
                $normalized,
                $tdStatus,
                $trackingInfo,
                $oldStatus
            );

            if ($oldStatus !== $normalized && trim((string) $normalized) !== '') {
                $trackingInfo['td_status_changed_at'] = now()->toIso8601String();
            }

            // Update local order
            $updates = [
                'status'   => $normalized,
                'raw_data' => $tdStatus,
                'tracking_info' => $trackingInfo,
            ];

            $confirmedDeliveryAt = $this->confirmedDeliveryTimestamp($trackingInfo);
            if ($normalized === 'delivered'
                && $this->order->delivered_at === null
                && $confirmedDeliveryAt !== null) {
                $updates['delivered_at'] = $confirmedDeliveryAt;
            }

            if ($normalized !== 'delivered' && $this->order->delivered_at !== null) {
                $updates['delivered_at'] = null;
            }

            if (in_array($normalized, ['shipped', 'invoiced', 'delivered'], true)
                && $this->order->shipped_at === null
                && ($shipDate || $trackingNumber)) {
                $updates['shipped_at'] = $shipDate ?: now();
            }

            if ($freightAmount !== null && is_numeric((string) $freightAmount)) {
                $updates['shipping_amount'] = (float) $freightAmount;
            }

            if ($tdOrderNumber !== null && trim((string) $tdOrderNumber) !== '') {
                $updates['tdsynnex_order_id'] = trim((string) $tdOrderNumber);
            }

            $trackingChanged = json_encode($oldTracking) !== json_encode($trackingInfo);
            $shippingChanged = array_key_exists('shipping_amount', $updates)
                && (float) $updates['shipping_amount'] !== (float) ($this->order->shipping_amount ?? 0);
            $statusChanged = $oldStatus !== (string) $updates['status'];

            $this->order->update($updates);
            $this->order->refresh();

            if ($resolvedTrackingNumber !== '') {
                $shipmentStatus = $this->normalizeFulfillmentStatus(
                    (string) ($carrierLive['status'] ?? $shippingStatus ?? $normalized)
                ) ?: $normalized;

                $this->order->shipments()->updateOrCreate(
                    ['tracking_number' => $resolvedTrackingNumber],
                    array_filter([
                        'carrier' => $resolvedCarrier ?: null,
                        'tracking_url' => $carrierLive['tracking_url'] ?? $trackingUrl,
                        'status' => $shipmentStatus,
                        'shipped_at' => $shipDate ?: $this->order->shipped_at,
                        'expected_delivery_at' => $estimatedDelivery ?: null,
                        'delivered_at' => $shipmentStatus === 'delivered'
                            ? ($this->order->delivered_at ?: $confirmedDeliveryAt)
                            : null,
                        'raw_data' => $carrierLive ?: $tdStatus,
                    ], fn ($value) => $value !== null && $value !== '')
                );
            }

            // If status changed, send notification
            if ($statusChanged || $trackingChanged || $shippingChanged) {
                if ($this->shouldSendStatusChangeNotification($oldStatus, (string) $this->order->status)) {
                    $notificationService->sendOrderShippedNotification($this->order);
                }

                // TD SYNNEX "invoiced" means the supplier invoiced Armely. It is
                // not evidence that the customer paid Armely, so supplier order
                // status must never mutate invoice payment state.

                Log::info("Order {$this->order->order_number} status updated from {$oldStatus} to {$this->order->status}", [
                    'raw_td_status' => $rawStatus,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to update order status for {$this->order->order_number}: " . $e->getMessage());
            throw $e;
        }
    }

    private function canCheckShippingStatus(Order $order): bool
    {
        $quote = $order->relationLoaded('quote') ? $order->quote : $order->quote()->first();
        $invoice = $order->relationLoaded('invoice') ? $order->invoice : $order->invoice()->first();

        return strtolower((string) ($quote?->status ?? '')) === 'approved'
            && strtolower((string) ($invoice?->status ?? '')) === 'paid';
    }

    private function deepFindFirstByKeys(mixed $data, array $keys): mixed
    {
        if (!is_array($data)) {
            return null;
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $data) && $data[$key] !== null && $data[$key] !== '') {
                return $data[$key];
            }
        }

        foreach ($data as $value) {
            $found = $this->deepFindFirstByKeys($value, $keys);
            if ($found !== null && $found !== '') {
                return $found;
            }
        }

        return null;
    }

    /**
     * Select the first recognizable TD order status, rather than an unrelated
     * generic XML Code/Status field (for example a request success code).
     */
    private function extractTdOrderStatus(array $payload): string
    {
        $header = $payload['OrderStatusResponse']['OrderStatus']
            ?? $payload['POStatusResponse']['POHeader']
            ?? $payload['POHeader']
            ?? null;

        $candidates = [];
        if (is_array($header)) {
            foreach (['POStatus', 'OrderStatus', 'Status', 'Code'] as $key) {
                if (isset($header[$key]) && !is_array($header[$key])) {
                    $candidates[] = (string) $header[$key];
                }
            }
        }

        $this->collectValuesByKeys(
            $payload,
            ['poStatus', 'POStatus', 'orderStatus', 'OrderStatus', 'status', 'Status', 'code', 'Code'],
            $candidates
        );

        foreach (array_unique($candidates) as $candidate) {
            if (self::normalizeTdStatus($candidate) !== '') {
                return trim($candidate);
            }
        }

        return '';
    }

    private function collectValuesByKeys(mixed $data, array $keys, array &$values): void
    {
        if (!is_array($data)) {
            return;
        }

        foreach ($data as $key => $value) {
            if (in_array((string) $key, $keys, true) && !is_array($value) && $value !== null && $value !== '') {
                $values[] = (string) $value;
            }
            if (is_array($value)) {
                $this->collectValuesByKeys($value, $keys, $values);
            }
        }
    }

    /**
     * Map TD SYNNEX raw status codes to our canonical order status set.
     * TD SYNNEX statuses: RECEIVED, OPEN, ACCEPTED, BACKORDERED, PARTIALLY_SHIPPED,
     *                      SHIPPED, INVOICED, COMPLETE, CANCELLED.
     */
    private static function normalizeTdStatus(string $raw): string
    {
        return match (strtolower(trim($raw))) {
            'received', 'open', 'accepted', 'confirmed',
            'pending', 'processing', 'draft'                             => 'accepted',
            'backordered', 'back_ordered', 'back ordered', 'backorder'  => 'backordered',
            'partiallyshipped', 'partially_shipped', 'partial'           => 'shipped',
            'shipped'                                                     => 'shipped',
            'invoiced', 'invoiced/complete', 'complete',
            'completed'                                                   => 'invoiced',
            'delivered'                                                   => 'delivered',
            'cancelled', 'canceled', 'voided', 'void'                   => 'cancelled',
            default                                                       => '',
        };
    }

    private static function trackingPayloadIndicatesDelivered(array $tracking): bool
    {
        $candidates = [
            $tracking['carrier_live_status_normalized'] ?? null,
            $tracking['carrier_live_status'] ?? null,
            $tracking['shipping_status'] ?? null,
            $tracking['delivery_status'] ?? null,
            $tracking['latest_status'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (!is_string($candidate)) {
                continue;
            }

            if (str_contains(strtolower($candidate), 'deliver')) {
                return true;
            }
        }

        return false;
    }

    private function deliveryWasConfirmed(string $oldStatus, array $tracking): bool
    {
        if (isset($tracking['customer_confirmed_delivered_at'])) {
            return true;
        }

        $deliveredBy = strtolower(trim((string) ($tracking['delivered_by'] ?? '')));
        if (in_array($deliveredBy, ['customer', 'carrier', 'admin'], true)) {
            return true;
        }

        foreach (['carrier_live_status_normalized', 'carrier_live_status', 'delivery_status'] as $key) {
            if (str_contains(strtolower((string) ($tracking[$key] ?? '')), 'deliver')) {
                return true;
            }
        }

        return $oldStatus === 'delivered' && $this->order->delivered_at !== null;
    }

    private function confirmedDeliveryTimestamp(array $tracking): mixed
    {
        foreach ([
            'actual_delivery_date',
            'carrier_delivered_at',
            'customer_confirmed_delivered_at',
        ] as $key) {
            $value = $tracking[$key] ?? null;
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        foreach ($this->order->shipments as $shipment) {
            if ($shipment->delivered_at !== null) {
                return $shipment->delivered_at;
            }
        }

        return null;
    }

    /**
     * Reconcile the complete fulfillment chain. TD PO status is the baseline;
     * TD package events, persisted shipments, and carrier events can advance it.
     * Confirmed delivery is terminal.
     */
    private function resolveEffectiveStatus(
        string $tdOrderStatus,
        array $tdPayload,
        array $tracking,
        string $oldStatus
    ): string {
        $candidates = [$tdOrderStatus];

        $tdFulfillmentValues = [];
        $this->collectValuesByKeys($tdPayload, [
            'shippingStatus', 'shipping_status', 'shipmentStatus', 'ShipmentStatus',
            'deliveryStatus', 'DeliveryStatus', 'packageStatus', 'PackageStatus',
            'lineStatus', 'LineStatus', 'itemStatus', 'ItemStatus',
        ], $tdFulfillmentValues);
        array_push($candidates, ...$tdFulfillmentValues);

        foreach ([
            'carrier_live_status_normalized', 'carrier_live_status', 'shipping_status',
            'delivery_status', 'latest_status',
        ] as $key) {
            if (!empty($tracking[$key])) {
                $candidates[] = (string) $tracking[$key];
            }
        }

        foreach ($this->order->shipments as $shipment) {
            if ($shipment->status) {
                $candidates[] = (string) $shipment->status;
            }
            if ($shipment->delivered_at !== null) {
                $candidates[] = 'delivered';
            }
        }

        if ($this->deliveryWasConfirmed($oldStatus, $tracking)) {
            $candidates[] = 'delivered';
        }

        $effective = $this->normalizeFulfillmentStatus($tdOrderStatus) ?: $oldStatus ?: 'pending';
        $effectiveRank = $this->fulfillmentStatusRank($effective);

        foreach ($candidates as $candidate) {
            $status = $this->normalizeFulfillmentStatus((string) $candidate);
            if ($status === '') {
                continue;
            }

            $rank = $this->fulfillmentStatusRank($status);
            if ($rank > $effectiveRank) {
                $effective = $status;
                $effectiveRank = $rank;
            }
        }

        return $effective;
    }

    private function normalizeFulfillmentStatus(string $raw): string
    {
        $value = strtolower(trim($raw));
        if ($value === '') return '';
        if (str_contains($value, 'deliver')) return 'delivered';
        if (str_contains($value, 'out for delivery') || str_contains($value, 'transit') || str_contains($value, 'on the way')) return 'in_transit';
        if (str_contains($value, 'partial') && str_contains($value, 'ship')) return 'shipped';
        if (str_contains($value, 'ship') || str_contains($value, 'picked up') || str_contains($value, 'label created')) return 'shipped';
        if (str_contains($value, 'invoice') || str_contains($value, 'complete')) return 'invoiced';
        if (str_contains($value, 'backorder')) return 'backordered';
        if (str_contains($value, 'accept') || str_contains($value, 'confirm') || str_contains($value, 'process')) return 'accepted';
        if (str_contains($value, 'cancel') || str_contains($value, 'void')) return 'cancelled';
        if (str_contains($value, 'exception') || str_contains($value, 'fail') || str_contains($value, 'return')) return 'failed';
        if (str_contains($value, 'pending') || str_contains($value, 'open') || str_contains($value, 'received')) return 'pending';

        return '';
    }

    private function fulfillmentStatusRank(string $status): int
    {
        return match ($status) {
            'pending' => 0,
            'accepted' => 10,
            'backordered' => 15,
            'invoiced' => 20,
            'cancelled' => 25,
            'shipped' => 30,
            'in_transit' => 40,
            'failed' => 45,
            'delivered' => 50,
            default => -1,
        };
    }

    private function shouldSendStatusChangeNotification(string $oldStatus, string $newStatus): bool
    {
        return trim($newStatus) !== '' && $oldStatus !== $newStatus;
    }
}

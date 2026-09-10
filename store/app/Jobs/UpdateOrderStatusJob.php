<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\TDSynnexService;
use App\Services\NotificationService;
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

    public function handle(TDSynnexService $tdsynnexService, NotificationService $notificationService): void
    {
        try {
            $this->order->loadMissing(['quote', 'invoice']);

            if (!$this->canCheckShippingStatus($this->order)) {
                return;
            }

            $poNumber = trim((string) ($this->order->quote_id ?: $this->order->order_number));
            if ($poNumber === '' || in_array((string) $this->order->status, ['cancelled'], true)) {
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
            $estimatedDelivery = $this->deepFindFirstByKeys($tdStatus, ['estimatedDeliveryDate', 'EstimatedDeliveryDate', 'estimatedShipDate', 'EstimatedShipDate', 'estimatedArrivalDate', 'EstimatedArrivalDate']);
            $carrier = $this->deepFindFirstByKeys($tdStatus, ['ShipMethodDescription', 'shipMethodDescription', 'Carrier', 'carrier', 'shipMethod', 'ShipMethod']);
            $shipDate = $this->deepFindFirstByKeys($tdStatus, ['DateShipped', 'dateShipped', 'ShipDatetime', 'shipDatetime', 'ShipDate', 'shipDate']);
            $tdOrderNumber = $this->deepFindFirstByKeys($tdStatus, ['OrderNumber', 'orderNumber', 'order_number', 'SynnexOrderNumber', 'synnexOrderNumber']);
            $deliverySignal = strtolower((string) ($shippingStatus ?? ''));
            $statusToNormalize = str_contains($deliverySignal, 'deliver')
                ? 'delivered'
                : $rawStatus;
            $normalized = self::normalizeTdStatus((string) $statusToNormalize) ?: $this->order->status;
            $oldTracking = is_array($this->order->tracking_info) ? $this->order->tracking_info : [];
            $trackingInfo = array_merge($oldTracking, array_filter([
                'tracking_number' => $trackingNumber ? (string) $trackingNumber : null,
                'shipping_status' => $shippingStatus ? (string) $shippingStatus : null,
                'estimated_delivery_date' => $estimatedDelivery ? (string) $estimatedDelivery : null,
                'carrier' => $carrier ? (string) $carrier : null,
                'ship_date' => $shipDate ? (string) $shipDate : null,
                'td_order_status_code' => $rawStatus !== '' ? $rawStatus : null,
                'td_order_number' => $tdOrderNumber ? (string) $tdOrderNumber : null,
                'td_synced_at' => now()->toIso8601String(),
            ], fn ($value) => $value !== null && $value !== ''));

            $oldStatus = (string) ($this->order->status ?? '');
            // TD's current PO/shipping response is authoritative. Do not let a
            // stale locally cached delivery flag override a newer TD status.
            if (str_contains($deliverySignal, 'deliver')) {
                $normalized = 'delivered';
            }

            if ($oldStatus !== $normalized && trim((string) $normalized) !== '') {
                $trackingInfo['td_status_changed_at'] = now()->toIso8601String();
            }

            // Update local order
            $updates = [
                'status'   => $normalized,
                'raw_data' => $tdStatus,
                'tracking_info' => $trackingInfo,
            ];

            if ($normalized === 'delivered' && $this->order->delivered_at === null) {
                $updates['delivered_at'] = now();
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

            // If status changed, send notification
            if ($statusChanged || $trackingChanged || $shippingChanged) {
                if ($this->shouldSendShippingNotification($oldStatus, (string) $this->order->status, $oldTracking, $trackingInfo)) {
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

    private function shouldSendShippingNotification(string $oldStatus, string $newStatus, array $oldTracking, array $newTracking): bool
    {
        // Every supplier status transition is customer-visible and must notify,
        // including accepted, backordered, invoiced, cancelled, and delivered.
        if ($oldStatus !== $newStatus && trim($newStatus) !== '') {
            return true;
        }

        $shippingMilestones = ['shipped', 'in_transit', 'delivered'];
        $oldTrackingNumber = strtolower(trim((string) ($oldTracking['tracking_number'] ?? '')));
        $newTrackingNumber = strtolower(trim((string) ($newTracking['tracking_number'] ?? '')));
        if ($oldTrackingNumber !== $newTrackingNumber && $newTrackingNumber !== '') {
            return true;
        }

        $oldShippingStatus = strtolower(trim((string) ($oldTracking['shipping_status'] ?? '')));
        $newShippingStatus = strtolower(trim((string) ($newTracking['shipping_status'] ?? '')));
        if ($oldShippingStatus !== $newShippingStatus && $newShippingStatus !== '') {
            return true;
        }

        $oldCarrierStatus = strtolower(trim((string) ($oldTracking['carrier_live_status_normalized'] ?? '')));
        $newCarrierStatus = strtolower(trim((string) ($newTracking['carrier_live_status_normalized'] ?? '')));
        if ($oldCarrierStatus !== $newCarrierStatus && in_array($newCarrierStatus, $shippingMilestones, true)) {
            return true;
        }

        return false;
    }
}

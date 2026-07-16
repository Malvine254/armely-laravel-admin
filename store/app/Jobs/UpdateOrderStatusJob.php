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

            // Normalize the raw TD SYNNEX status code to our canonical set
            $rawStatus = (string) ($this->deepFindFirstByKeys($tdStatus, ['status', 'Status', 'code', 'Code', 'orderStatus', 'OrderStatus', 'poStatus', 'POStatus']) ?? '');
            $trackingNumber = $this->deepFindFirstByKeys($tdStatus, ['tracking_number', 'trackingNumber', 'TrackingNumber', 'carrierTrackingNumber', 'shipmentTrackingNumber', 'proNumber', 'ProNumber']);
            $shippingStatus = $this->deepFindFirstByKeys($tdStatus, ['shippingStatus', 'shipping_status', 'shipmentStatus', 'ShipmentStatus', 'deliveryStatus', 'DeliveryStatus', 'status', 'Status']);
            $freightAmount = $this->deepFindFirstByKeys($tdStatus, ['freight', 'Freight', 'freightAmount', 'poFreight', 'shippingAmount', 'shipping_amount', 'totalFreight', 'TotalFreight']);
            $estimatedDelivery = $this->deepFindFirstByKeys($tdStatus, ['estimatedDeliveryDate', 'EstimatedDeliveryDate', 'estimatedShipDate', 'EstimatedShipDate', 'estimatedArrivalDate', 'EstimatedArrivalDate']);
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
            ], fn ($value) => $value !== null && $value !== ''));

            $oldStatus = (string) ($this->order->status ?? '');
            if (self::trackingPayloadIndicatesDelivered($trackingInfo)) {
                $normalized = 'delivered';
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

            if ($freightAmount !== null && is_numeric((string) $freightAmount)) {
                $updates['shipping_amount'] = (float) $freightAmount;
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
        $shippingMilestones = ['shipped', 'in_transit', 'delivered'];
        if ($oldStatus !== $newStatus && in_array($newStatus, $shippingMilestones, true)) {
            return true;
        }

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

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
            if ($poNumber === '' || in_array((string) $this->order->status, ['cancelled', 'delivered'], true)) {
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
            $normalized = self::normalizeTdStatus($rawStatus) ?: $this->order->status;
            $oldTracking = is_array($this->order->tracking_info) ? $this->order->tracking_info : [];
            $trackingInfo = array_merge($oldTracking, array_filter([
                'tracking_number' => $trackingNumber ? (string) $trackingNumber : null,
                'shipping_status' => $shippingStatus ? (string) $shippingStatus : null,
                'estimated_delivery_date' => $estimatedDelivery ? (string) $estimatedDelivery : null,
            ], fn ($value) => $value !== null && $value !== ''));

            // Update local order
            $oldStatus = $this->order->status;
            $updates = [
                'status'   => $normalized,
                'raw_data' => $tdStatus,
                'tracking_info' => $trackingInfo,
            ];

            if ($freightAmount !== null && is_numeric((string) $freightAmount)) {
                $updates['shipping_amount'] = (float) $freightAmount;
            }

            $trackingChanged = json_encode($oldTracking) !== json_encode($trackingInfo);
            $shippingChanged = array_key_exists('shipping_amount', $updates)
                && (float) $updates['shipping_amount'] !== (float) ($this->order->shipping_amount ?? 0);

            $this->order->update($updates);
            $this->order->refresh();

            // If status changed, send notification
            if ($oldStatus !== $this->order->status || $trackingChanged || $shippingChanged) {
                if ($this->order->status === 'shipped' || $trackingChanged || $shippingChanged) {
                    $notificationService->sendOrderShippedNotification($this->order);
                } elseif ($this->order->status === 'invoiced') {
                    // Mark invoice as paid when TD marks the order as invoiced/complete
                    $invoice = $this->order->invoice;
                    if ($invoice && $invoice->status !== 'paid') {
                        $invoice->update(['status' => 'paid', 'paid_at' => now()]);
                    }
                }

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
}

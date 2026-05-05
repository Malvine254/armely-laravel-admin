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
            // Fetch current status from TD SYNNEX XML POStatus when a PO number is available.
            $tdStatus = $tdsynnexService->checkPoStatus($this->order->quote_id ?: $this->order->order_number);

            // Normalize the raw TD SYNNEX status code to our canonical set
            $rawStatus = (string) ($tdStatus['status'] ?? $tdStatus['Code'] ?? $tdStatus['orderStatus'] ?? '');
            $normalized = self::normalizeTdStatus($rawStatus) ?: $this->order->status;

            // Update local order
            $oldStatus = $this->order->status;
            $this->order->update([
                'status'   => $normalized,
                'raw_data' => $tdStatus,
            ]);

            // If status changed, send notification
            if ($oldStatus !== $this->order->status) {
                if ($this->order->status === 'shipped') {
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
            'completed', 'delivered'                                     => 'invoiced',
            'cancelled', 'canceled', 'voided', 'void'                   => 'cancelled',
            default                                                       => '',
        };
    }
}

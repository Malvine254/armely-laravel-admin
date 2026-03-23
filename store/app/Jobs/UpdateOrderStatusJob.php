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
            // Fetch current status from TD SYNNEX
            $tdStatus = $tdsynnexService->getOrderStatus($this->order->order_number);

            // Update local order
            $oldStatus = $this->order->status;
            $this->order->update([
                'status' => $tdStatus['status'] ?? $oldStatus,
                'raw_data' => $tdStatus,
            ]);

            // If status changed, send notification
            if ($oldStatus !== $this->order->status) {
                if ($this->order->status === 'shipped') {
                    $notificationService->sendOrderShippedNotification($this->order);
                } elseif ($this->order->status === 'delivered') {
                    // Mark invoice as paid if needed
                    $invoice = $this->order->invoice;
                    if ($invoice && $invoice->status !== 'paid') {
                        $invoice->update(['status' => 'paid', 'paid_at' => now()]);
                    }
                }

                Log::info("Order {$this->order->order_number} status updated from {$oldStatus} to {$this->order->status}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to update order status for {$this->order->order_number}: " . $e->getMessage());
            throw $e;
        }
    }
}

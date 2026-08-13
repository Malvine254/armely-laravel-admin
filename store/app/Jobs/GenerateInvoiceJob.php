<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Message;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle(InvoiceService $invoiceService, NotificationService $notificationService): void
    {
        try {
            // Generate invoice for order
            $invoice = $invoiceService->generateInvoiceForOrder($this->order);

            // Create in-app message for the customer inbox
            Message::createMessage(
                $this->order->user_id,
                'invoice',
                'Invoice ready for payment',
                "Invoice {$invoice->invoice_number} is ready. Total: $" . number_format($invoice->total_amount ?? 0, 2),
                $invoice->invoice_number,
                'normal',
                [
                    'order_number' => $this->order->order_number,
                    'invoice_id' => $invoice->id,
                ]
            );

            // Send the initial invoice email when the invoice is generated.
            $notificationService->sendInvoiceNotification($invoice);

            Log::info("Invoice {$invoice->invoice_number} generated for order {$this->order->order_number}");
        } catch (\Exception $e) {
            Log::error("Failed to generate invoice for order {$this->order->order_number}: " . $e->getMessage());
            throw $e;
        }
    }
}

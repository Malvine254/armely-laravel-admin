<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    private TDSynnexService $tdsynnexService;

    public function __construct(TDSynnexService $tdsynnexService)
    {
        $this->tdsynnexService = $tdsynnexService;
    }

    /**
     * Generate invoice for an order
     */
    public function generateInvoiceForOrder(Order $order): Invoice
    {
        try {
            // Fetch from TD SYNNEX if available
            $tdData = null;
            if ($order->raw_data && isset($order->raw_data['invoiceNumber'])) {
                try {
                    $tdData = $this->tdsynnexService->getInvoice($order->raw_data['invoiceNumber']);
                } catch (\Exception $e) {
                    Log::warning("Could not fetch invoice data from TD SYNNEX for order {$order->order_number}: " . $e->getMessage());
                }
            }

            $existing = Invoice::where('order_number', $order->order_number)->first();

            // Keep stable invoice number per order to avoid accidental remapping.
            $invoiceNumber = $tdData['invoiceNumber']
                ?? $existing?->invoice_number
                ?? $this->generateInvoiceNumber($order);

            if ($existing) {
                $existing->update([
                    'user_id' => $order->user_id,
                    'invoice_number' => $invoiceNumber,
                    'status' => $tdData['status'] ?? $existing->status ?? 'issued',
                    'total_amount' => $tdData['totalAmount'] ?? $order->total_amount,
                    'tax_amount' => $tdData['taxAmount'] ?? $order->tax_amount,
                    'paid_amount' => $tdData['paidAmount'] ?? $existing->paid_amount ?? 0,
                    'items' => $tdData['items'] ?? $order->items,
                    'raw_data' => $tdData ?? $order->raw_data,
                    'issued_at' => $existing->issued_at ?? now(),
                    'due_at' => $existing->due_at ?? now()->addDays(30),
                    'paid_at' => $existing->paid_at,
                    'notes' => "Invoice for order #{$order->order_number}",
                ]);

                $invoice = $existing->fresh();
            } else {
                $invoice = Invoice::create([
                    'user_id' => $order->user_id,
                    'invoice_number' => $invoiceNumber,
                    'order_number' => $order->order_number,
                    'status' => $tdData['status'] ?? 'issued',
                    'total_amount' => $tdData['totalAmount'] ?? $order->total_amount,
                    'tax_amount' => $tdData['taxAmount'] ?? $order->tax_amount,
                    'paid_amount' => $tdData['paidAmount'] ?? 0,
                    'items' => $tdData['items'] ?? $order->items,
                    'raw_data' => $tdData ?? $order->raw_data,
                    'issued_at' => now(),
                    'due_at' => now()->addDays(30),
                    'paid_at' => null,
                    'notes' => "Invoice for order #{$order->order_number}",
                ]);
            }

            Log::info("Invoice {$invoiceNumber} generated for order {$order->order_number}");
            return $invoice;
        } catch (\Exception $e) {
            Log::error("Failed to generate invoice for order {$order->order_number}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate invoice PDF
     */
    public function generateInvoicePdf(Invoice $invoice): string
    {
        try {
            $pdf = Pdf::loadView('invoices.pdf', [
                'invoice' => $invoice,
                'order' => $invoice->order,
                'user' => $invoice->user,
            ]);

            $fileName = "invoice_{$invoice->invoice_number}.pdf";
            $path = storage_path("app/invoices/{$fileName}");

            // Ensure directory exists
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            $pdf->save($path);

            return $path;
        } catch (\Exception $e) {
            Log::error("Failed to generate PDF for invoice {$invoice->invoice_number}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber(Order $order): string
    {
        $year = now()->year;
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        $sequence = Invoice::whereYear('created_at', $year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;

        return sprintf('INV-%s%s-%05d', $year, $month, $sequence);
    }
}

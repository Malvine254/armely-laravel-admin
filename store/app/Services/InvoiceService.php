<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\AppSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    private TDSynnexService $tdsynnexService;

    public function __construct(TDSynnexService $tdsynnexService)
    {
        $this->tdsynnexService = $tdsynnexService;
    }

    private function normalizeInvoiceLineItems(array $rows, float $targetSubtotal = 0): array
    {
        $mapped = array_map(function ($item, $idx) {
            if (!is_array($item)) {
                $item = [];
            }

            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $unitPrice = (float) ($item['unit_price'] ?? $item['unitPrice'] ?? $item['price'] ?? $item['customer_price'] ?? 0);
            $lineTotal = (float) ($item['line_total'] ?? $item['lineTotal'] ?? $item['extendedPrice'] ?? ($unitPrice * $quantity));

            return [
                'product_id' => (string) ($item['product_id'] ?? $item['productId'] ?? $item['id'] ?? ''),
                'product_name' => $item['product_name']
                    ?? $item['productName']
                    ?? $item['partDescription']
                    ?? $item['name']
                    ?? $item['description']
                    ?? ('Item ' . ($idx + 1)),
                'mfg_part_number' => $item['mfg_part_number']
                    ?? $item['mfgPartNo']
                    ?? $item['partNumber']
                    ?? $item['sku']
                    ?? '',
                'quantity' => $quantity,
                'unit_price' => round($unitPrice, 2),
                'line_total' => round($lineTotal, 2),
            ];
        }, $rows, array_keys($rows));

        $hasPricing = collect($mapped)->contains(function ($item) {
            return ((float) ($item['unit_price'] ?? 0)) > 0 || ((float) ($item['line_total'] ?? 0)) > 0;
        });

        if (!$hasPricing && $targetSubtotal > 0 && count($mapped) > 0) {
            $totalQty = array_reduce($mapped, function ($sum, $item) {
                return $sum + max(1, (int) ($item['quantity'] ?? 1));
            }, 0);

            if ($totalQty > 0) {
                $running = 0.0;
                foreach ($mapped as $idx => &$item) {
                    $qty = max(1, (int) ($item['quantity'] ?? 1));
                    $isLast = $idx === count($mapped) - 1;
                    $lineTotal = $isLast
                        ? round($targetSubtotal - $running, 2)
                        : round(($targetSubtotal * $qty) / $totalQty, 2);

                    $item['line_total'] = $lineTotal;
                    $item['unit_price'] = round($lineTotal / $qty, 2);
                    $running = round($running + $lineTotal, 2);
                }
                unset($item);
            }
        }

        return $mapped;
    }

    /**
     * Generate invoice for an order
     */
    public function generateInvoiceForOrder(Order $order): Invoice
    {
        try {
            $tdData = null;

            $existing = Invoice::where('order_number', $order->order_number)->first();

            $baseSubtotal = (float) ($order->total_amount ?? 0);
            $profitRate = max(0, AppSetting::getNumber('pricing.profit_rate_percent', 0));
            $taxRate = max(0, AppSetting::getNumber('pricing.tax_rate_percent', 0));
            $profitAmount = round(($baseSubtotal * $profitRate) / 100, 2);
            $subtotal = round($baseSubtotal + $profitAmount, 2);

            $tax = $taxRate > 0
                ? round(($subtotal * $taxRate) / 100, 2)
                : (float) ($order->tax_amount ?? 0);

            $shipping = (float) ($order->shipping_amount ?? 0);
            $discount = (float) ($order->discount_amount ?? 0);
            $computedPayableTotal = max(0, $subtotal + $tax + $shipping - $discount);
            $normalizedItems = $this->normalizeInvoiceLineItems(is_array($order->items) ? $order->items : [], $subtotal);

            // Keep stable invoice number per order to avoid accidental remapping.
            $invoiceNumber = $tdData['invoiceNumber']
                ?? $existing?->invoice_number
                ?? $this->generateInvoiceNumber($order);

            if ($existing) {
                $existing->update([
                    'user_id' => $order->user_id,
                    'invoice_number' => $invoiceNumber,
                    'status' => $tdData['status'] ?? $existing->status ?? 'issued',
                    'total_amount' => $tdData['totalAmount'] ?? $computedPayableTotal,
                    'tax_amount' => $tdData['taxAmount'] ?? $tax,
                    'paid_amount' => $tdData['paidAmount'] ?? $existing->paid_amount ?? 0,
                    'items' => $tdData['items'] ?? $normalizedItems,
                    'raw_data' => array_merge(
                        is_array($tdData) ? $tdData : (is_array($order->raw_data) ? $order->raw_data : []),
                        [
                            'invoice_charge_breakdown' => [
                                'base_subtotal' => $baseSubtotal,
                                'profit_rate_percent' => $profitRate,
                                'profit_amount' => $profitAmount,
                                'tax_rate_percent' => $taxRate,
                                'subtotal' => $subtotal,
                                'tax_amount' => $tax,
                                'shipping_amount' => $shipping,
                                'discount_amount' => $discount,
                                'payable_total' => $computedPayableTotal,
                            ],
                        ]
                    ),
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
                    'total_amount' => $tdData['totalAmount'] ?? $computedPayableTotal,
                    'tax_amount' => $tdData['taxAmount'] ?? $tax,
                    'paid_amount' => $tdData['paidAmount'] ?? 0,
                    'items' => $tdData['items'] ?? $normalizedItems,
                    'raw_data' => array_merge(
                        is_array($tdData) ? $tdData : (is_array($order->raw_data) ? $order->raw_data : []),
                        [
                            'invoice_charge_breakdown' => [
                                'base_subtotal' => $baseSubtotal,
                                'profit_rate_percent' => $profitRate,
                                'profit_amount' => $profitAmount,
                                'tax_rate_percent' => $taxRate,
                                'subtotal' => $subtotal,
                                'tax_amount' => $tax,
                                'shipping_amount' => $shipping,
                                'discount_amount' => $discount,
                                'payable_total' => $computedPayableTotal,
                            ],
                        ]
                    ),
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

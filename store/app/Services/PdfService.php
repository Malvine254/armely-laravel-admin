<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\Invoice;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class PdfService
{
    private function toNumber($value, float $fallback = 0): float
    {
        return is_numeric($value) ? (float) $value : $fallback;
    }

    private function resolveInvoiceOrder(Invoice $invoice): ?Order
    {
        if (!$invoice->order_number) {
            return null;
        }

        return Order::where('user_id', $invoice->user_id)
            ->where('order_number', $invoice->order_number)
            ->first();
    }

    private function normalizeInvoiceItems(array $rows, float $invoiceTotal = 0): array
    {
        $mapped = array_map(function ($item, $idx) {
            $quantity = max(1, (int) $this->toNumber($item['quantity'] ?? 1, 1));
            $unitPrice = $this->toNumber($item['unit_price'] ?? $item['unitPrice'] ?? $item['price'] ?? 0, 0);
            $lineTotal = $this->toNumber(
                $item['line_total'] ?? $item['lineTotal'] ?? $item['extendedPrice'] ?? null,
                $unitPrice * $quantity
            );

            return [
                'product_id' => (string) (
                    $item['product_id']
                    ?? $item['productId']
                    ?? $item['id']
                    ?? ''
                ),
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
                    ?? $item['product_id']
                    ?? $item['id']
                    ?? '',
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
            ];
        }, $rows, array_keys($rows));

        $hasPricing = collect($mapped)->contains(function ($item) {
            return $this->toNumber($item['unit_price']) > 0 || $this->toNumber($item['line_total']) > 0;
        });

        if (!$hasPricing && $invoiceTotal > 0 && count($mapped) > 0) {
            $totalQty = array_reduce($mapped, function ($sum, $item) {
                return $sum + max(1, (int) $this->toNumber($item['quantity'], 1));
            }, 0);

            if ($totalQty > 0) {
                $running = 0.0;
                foreach ($mapped as $idx => &$item) {
                    $qty = max(1, (int) $this->toNumber($item['quantity'], 1));
                    $isLast = $idx === count($mapped) - 1;
                    $lineTotal = $isLast
                        ? round($invoiceTotal - $running, 2)
                        : round(($invoiceTotal * $qty) / $totalQty, 2);

                    $item['line_total'] = $lineTotal;
                    $item['unit_price'] = round($lineTotal / $qty, 2);
                    $running = round($running + $lineTotal, 2);
                }
                unset($item);
            }
        }

        // Merge duplicate lines by product id (fallback to part number/name key when id is missing).
        $grouped = [];
        foreach ($mapped as $item) {
            $productId = trim((string) ($item['product_id'] ?? ''));
            $partNumber = trim((string) ($item['mfg_part_number'] ?? ''));
            $productName = trim((string) ($item['product_name'] ?? ''));

            $groupKey = $productId !== ''
                ? 'id:' . $productId
                : 'fallback:' . mb_strtolower($partNumber . '|' . $productName);

            if (!isset($grouped[$groupKey])) {
                $grouped[$groupKey] = $item;
                continue;
            }

            $grouped[$groupKey]['quantity'] += (int) max(1, (int) $this->toNumber($item['quantity'], 1));
            $grouped[$groupKey]['line_total'] = round(
                $this->toNumber($grouped[$groupKey]['line_total'], 0) + $this->toNumber($item['line_total'], 0),
                2
            );

            if (trim((string) ($grouped[$groupKey]['mfg_part_number'] ?? '')) === '' && $partNumber !== '') {
                $grouped[$groupKey]['mfg_part_number'] = $partNumber;
            }
        }

        return array_values(array_map(function ($item) {
            $qty = max(1, (int) $this->toNumber($item['quantity'], 1));
            $lineTotal = round($this->toNumber($item['line_total'], 0), 2);
            $item['unit_price'] = round($lineTotal / $qty, 2);
            return $item;
        }, $grouped));
    }

    private function resolveInvoiceItems(Invoice $invoice, ?Order $order = null): array
    {
        $items = is_array($invoice->items) ? $invoice->items : [];

        if (empty($items) && $order && is_array($order->items)) {
            $items = $order->items;
        }

        // Combined invoices may have no order_number; rebuild items from source invoices.
        if (empty($items) && is_array($invoice->raw_data)) {
            $sourceInvoiceNumbers = $invoice->raw_data['source_invoice_numbers'] ?? [];

            if (is_array($sourceInvoiceNumbers) && !empty($sourceInvoiceNumbers)) {
                $sourceInvoices = Invoice::where('user_id', $invoice->user_id)
                    ->whereIn('invoice_number', $sourceInvoiceNumbers)
                    ->get();

                foreach ($sourceInvoices as $sourceInvoice) {
                    $sourceItems = is_array($sourceInvoice->items) ? $sourceInvoice->items : [];

                    if (empty($sourceItems) && $sourceInvoice->order_number) {
                        $sourceOrder = Order::where('user_id', $invoice->user_id)
                            ->where('order_number', $sourceInvoice->order_number)
                            ->first();
                        $sourceItems = is_array($sourceOrder?->items) ? $sourceOrder->items : [];
                    }

                    foreach ($sourceItems as $sourceItem) {
                        $items[] = $sourceItem;
                    }
                }
            }
        }

        return $this->normalizeInvoiceItems($items, $this->toNumber($invoice->total_amount, 0));
    }

    /**
     * Generate PDF for a quote
     */
    public function generateQuotePdf(Quote $quote): \Barryvdh\DomPDF\PDF
    {
        try {
            $data = [
                'quote' => $quote,
                'user' => $quote->user,
                'company' => $quote->user->company,
                'items' => $quote->items,
                'generatedDate' => now()->format('F d, Y'),
            ];

            $pdf = Pdf::loadView('pdfs.quote', $data);
            $pdf->setPaper('a4', 'portrait');
            
            Log::info("Quote PDF generated for quote {$quote->quote_id}");
            
            return $pdf;
        } catch (\Exception $e) {
            Log::error("Failed to generate quote PDF: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate PDF for an invoice
     */
    public function generateInvoicePdf(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        try {
            $order = $this->resolveInvoiceOrder($invoice);
            $items = $this->resolveInvoiceItems($invoice, $order);
            $subtotal = array_reduce($items, function ($sum, $item) {
                return $sum + $this->toNumber($item['line_total'] ?? 0, 0);
            }, 0.0);

            $data = [
                'invoice' => $invoice,
                'user' => $invoice->user,
                'company' => $invoice->user->company,
                'order' => $order,
                'items' => $items,
                'subtotal' => $subtotal,
                'generatedDate' => now()->format('F d, Y'),
            ];

            $pdf = Pdf::loadView('pdfs.invoice', $data);
            $pdf->setPaper('a4', 'portrait');
            
            Log::info("Invoice PDF generated for invoice {$invoice->invoice_number}");
            
            return $pdf;
        } catch (\Exception $e) {
            Log::error("Failed to generate invoice PDF: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Download quote PDF
     */
    public function downloadQuotePdf(Quote $quote): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = $this->generateQuotePdf($quote);
        $filename = "quote-{$quote->quote_id}.pdf";
        
        return $pdf->download($filename);
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoicePdf(Invoice $invoice): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = $this->generateInvoicePdf($invoice);
        $filename = "invoice-{$invoice->invoice_number}.pdf";
        
        return $pdf->download($filename);
    }

    /**
     * Stream quote PDF (for preview)
     */
    public function streamQuotePdf(Quote $quote): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = $this->generateQuotePdf($quote);
        
        return $pdf->stream("quote-{$quote->quote_id}.pdf");
    }

    /**
     * Stream invoice PDF (for preview)
     */
    public function streamInvoicePdf(Invoice $invoice): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = $this->generateInvoicePdf($invoice);
        
        return $pdf->stream("invoice-{$invoice->invoice_number}.pdf");
    }
}

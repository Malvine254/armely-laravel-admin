<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\AppSetting;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    private TDSynnexService $tdsynnexService;

    public function __construct(TDSynnexService $tdsynnexService)
    {
        $this->tdsynnexService = $tdsynnexService;
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
            if (is_array($value)) {
                $found = $this->deepFindFirstByKeys($value, $keys);
                if ($found !== null && $found !== '') {
                    return $found;
                }
            }
        }

        return null;
    }

    private function resolveOrderShippingAmount(Order $order): float
    {
        $raw = is_array($order->raw_data) ? $order->raw_data : [];
        $freight = $this->deepFindFirstByKeys($raw, [
            'freight',
            'Freight',
            'freightAmount',
            'poFreight',
            'shippingAmount',
            'shipping_amount',
            'totalFreight',
            'TotalFreight',
        ]);

        if ($freight !== null && $freight !== '' && is_numeric((string) $freight)) {
            return max(0, round((float) $freight, 2));
        }

        return max(0, round((float) ($order->shipping_amount ?? 0), 2));
    }

    private function findProductForInvoiceItem(array $item): ?Product
    {
        $lookupValues = [
            (string) ($item['product_id'] ?? ''),
            (string) ($item['productId'] ?? ''),
            (string) ($item['id'] ?? ''),
            (string) ($item['sku'] ?? ''),
            (string) ($item['partNumber'] ?? ''),
            (string) ($item['mfg_part_number'] ?? ''),
            (string) ($item['mfgPartNo'] ?? ''),
        ];

        $lookupValues = array_values(array_unique(array_filter(array_map('trim', $lookupValues), fn ($v) => $v !== '')));

        foreach ($lookupValues as $lookup) {
            $query = Product::query()
                ->select(['id', 'tdsynnex_product_id', 'tdsynnex_sku_no', 'mfg_part_no', 'retail_price'])
                ->where('vendor_id', 'TD SYNNEX')
                ->where(function ($q) use ($lookup) {
                    $q->where('mfg_part_no', $lookup)
                        ->orWhere('specifications->sku', $lookup);

                    if (ctype_digit($lookup)) {
                        $numeric = (int) $lookup;
                        $q->orWhere('id', $numeric)
                            ->orWhere('tdsynnex_product_id', $numeric)
                            ->orWhere('tdsynnex_sku_no', $numeric);
                    }
                })
                ->orderByDesc('updated_at')
                ->orderByDesc('id');

            $product = $query->first();
            if ($product) {
                return $product;
            }
        }

        return null;
    }

    private function buildRetailInvoiceLineItems(array $rows): array
    {
        return array_map(function ($item, $idx) {
            if (!is_array($item)) {
                $item = [];
            }

            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $product = $this->findProductForInvoiceItem($item);

            $retailUnitPrice = (float) ($product?->retail_price ?? 0);
            if ($retailUnitPrice <= 0) {
                $retailUnitPrice = (float) ($item['unit_price'] ?? $item['unitPrice'] ?? $item['price'] ?? 0);
            }

            $lineTotal = round($retailUnitPrice * $quantity, 2);

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
                    ?? (string) ($product?->mfg_part_no ?? ''),
                'quantity' => $quantity,
                'unit_price' => round($retailUnitPrice, 2),
                'line_total' => $lineTotal,
            ];
        }, $rows, array_keys($rows));
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

            $sourceItems = is_array($order->items) ? $order->items : [];
            $retailItems = $this->buildRetailInvoiceLineItems($sourceItems);
            $retailSubtotal = round((float) array_reduce($retailItems, function ($sum, $item) {
                return $sum + (float) ($item['line_total'] ?? 0);
            }, 0), 2);

            $tax = round((float) ($order->tax_amount ?? 0), 2);
            $shipping = $this->resolveOrderShippingAmount($order);
            $discount = (float) ($order->discount_amount ?? 0);
            $computedPayableTotal = max(0, round($retailSubtotal + $tax + $shipping - $discount, 2));
            $normalizedItems = $this->normalizeInvoiceLineItems($retailItems, $retailSubtotal);

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
                                'pricing_model' => 'retail',
                                'retail_subtotal' => $retailSubtotal,
                                'subtotal' => $retailSubtotal,
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
                                'pricing_model' => 'retail',
                                'retail_subtotal' => $retailSubtotal,
                                'subtotal' => $retailSubtotal,
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

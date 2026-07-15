<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('invoices')
            ->where('status', '!=', 'paid')
            ->where('notes', 'like', '%QUOTE:%')
            ->orderBy('id')
            ->chunkById(100, function ($invoices): void {
                foreach ($invoices as $invoice) {
                    $invoiceRaw = json_decode((string) ($invoice->raw_data ?? '{}'), true) ?: [];
                    $quoteId = trim((string) ($invoiceRaw['quote_id'] ?? ''));
                    if ($quoteId === '') {
                        continue;
                    }

                    $quote = DB::table('quotes')->where('quote_id', $quoteId)->first();
                    if (!$quote) {
                        continue;
                    }

                    $quoteRaw = json_decode((string) ($quote->raw_data ?? '{}'), true) ?: [];
                    $pricing = (array) ($quoteRaw['pricing'] ?? []);
                    $quotedTotal = round((float) ($quote->total_amount ?? 0), 2);
                    $tax = round((float) ($quote->tax_amount ?? $pricing['tax_amount'] ?? 0), 2);
                    $subtotal = round((float) ($pricing['subtotal'] ?? max(0, $quotedTotal - $tax)), 2);
                    $shipping = round((float) ($pricing['shipping_amount'] ?? $quoteRaw['shipping_amount'] ?? 0), 2);
                    $discount = round((float) ($quote->discount_amount ?? 0), 2);
                    $payableTotal = max(0, $quotedTotal + $shipping - $discount);

                    $invoiceRaw = array_merge($invoiceRaw, [
                        'base_subtotal' => (float) ($pricing['base_subtotal'] ?? $subtotal),
                        'profit_rate_percent' => (float) ($pricing['profit_rate_percent'] ?? 0),
                        'profit_amount' => (float) ($pricing['profit_amount'] ?? 0),
                        'tax_rate_percent' => (float) ($pricing['tax_rate_percent'] ?? 0),
                        'subtotal' => $subtotal,
                        'tax_amount' => $tax,
                        'shipping_amount' => $shipping,
                        'discount_amount' => $discount,
                        'payable_total' => $payableTotal,
                        'pricing_reapplied' => false,
                    ]);

                    DB::table('invoices')->where('id', $invoice->id)->update([
                        'total_amount' => $payableTotal,
                        'tax_amount' => $tax,
                        'items' => $quote->items,
                        'raw_data' => json_encode($invoiceRaw, JSON_UNESCAPED_SLASHES),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        // Financial corrections are intentionally not reversed.
    }
};

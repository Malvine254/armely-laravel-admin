<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!DB::getSchemaBuilder()->hasTable('quotes') || !DB::getSchemaBuilder()->hasTable('orders')) {
            return;
        }

        // Ensure orders.id has AUTO_INCREMENT — some MySQL hosts (e.g. GoDaddy shared) lose it on import.
        // AUTO_INCREMENT requires the column to be a key; include PRIMARY KEY when the key was also lost.
        $dbName = DB::getDatabaseName();
        $idMeta = DB::selectOne(
            "SELECT COLUMN_KEY, EXTRA FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'id'",
            [$dbName]
        );
        if ($idMeta && !str_contains(strtolower((string) ($idMeta->EXTRA ?? '')), 'auto_increment')) {
            $isPrimaryKey = strtoupper((string) ($idMeta->COLUMN_KEY ?? '')) === 'PRI';
            if ($isPrimaryKey) {
                DB::statement('ALTER TABLE `orders` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
            } else {
                DB::statement('ALTER TABLE `orders` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
            }
        }

        $approvedQuotes = DB::table('quotes')
            ->where('status', 'approved')
            ->orderBy('id')
            ->get();

        foreach ($approvedQuotes as $quote) {
            $quoteId = (string) ($quote->quote_id ?? '');
            if ($quoteId === '' || DB::table('orders')->where('quote_id', $quoteId)->exists()) {
                continue;
            }

            $invoice = DB::table('invoices')
                ->where('user_id', $quote->user_id)
                ->where('notes', 'like', '%QUOTE:' . $quoteId . '%')
                ->orderByDesc('id')
                ->first();

            $orderNumber = $this->generateOrderNumber($quoteId);
            $now = now();

            DB::table('orders')->insert([
                'user_id' => $quote->user_id,
                'order_number' => $orderNumber,
                'quote_id' => $quoteId,
                'status' => 'pending',
                'payment_status' => $invoice && (string) $invoice->status === 'paid' ? 'paid' : 'pending',
                'total_amount' => $invoice->total_amount ?? $quote->total_amount,
                'tax_amount' => $invoice->tax_amount ?? $quote->tax_amount ?? 0,
                'discount_amount' => $quote->discount_amount ?? 0,
                'shipping_amount' => 0,
                'items' => $quote->items,
                'raw_data' => json_encode([
                    'source' => 'approved_quote_backfill',
                    'quote_id' => $quoteId,
                    'invoice_number' => $invoice->invoice_number ?? null,
                    'td_submission_pending' => true,
                ]),
                'tracking_info' => json_encode([
                    'shipping_status' => 'pending',
                ]),
                'ordered_at' => $quote->approved_at ?? $now,
                'created_at' => $quote->approved_at ?? $now,
                'updated_at' => $now,
            ]);

            if ($invoice && empty($invoice->order_number)) {
                DB::table('invoices')
                    ->where('id', $invoice->id)
                    ->update([
                        'order_number' => $orderNumber,
                        'updated_at' => $now,
                    ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('orders')
            ->where('raw_data->source', 'approved_quote_backfill')
            ->delete();
    }

    private function generateOrderNumber(string $quoteId): string
    {
        $base = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $quoteId), -6));
        $candidate = $base;
        $suffix = 1;

        while (DB::table('orders')->where('order_number', $candidate)->exists()) {
            $candidate = $base . '-' . $suffix;
            $suffix++;
        }

        return $candidate;
    }
};

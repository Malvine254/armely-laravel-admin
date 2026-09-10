<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $orders = DB::table('orders')
            ->whereIn('order_number', ['161663243', '165041304'])
            ->get();

        foreach ($orders as $order) {
            $tracking = json_decode((string) ($order->tracking_info ?? ''), true);
            if (!is_array($tracking)) {
                $tracking = [];
            }

            $tracking['shipping_status'] = 'delivered';
            $tracking['delivered_by'] = $tracking['delivered_by'] ?? 'admin';
            $tracking['delivery_verified_reference'] = 'user-confirmed-correction-2026-09-10';

            DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'status' => 'delivered',
                    'tracking_info' => json_encode($tracking, JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ]);

            if (Schema::hasTable('shipments')) {
                DB::table('shipments')
                    ->where('order_id', $order->id)
                    ->update([
                        'status' => 'delivered',
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    public function down(): void
    {
        // Intentionally irreversible: reverting schema must not regress a
        // verified customer deliveries to an earlier supplier PO status.
    }
};

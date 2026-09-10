<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $order = DB::table('orders')
            ->where('order_number', '161663243')
            ->first();

        if (!$order) {
            return;
        }

        // 2026-09-10 was the verification date, not a carrier delivery event.
        // Retain Delivered status while removing the invented timestamp.
        DB::table('orders')
            ->where('id', $order->id)
            ->whereDate('delivered_at', '2026-09-10')
            ->update([
                'delivered_at' => null,
                'updated_at' => now(),
            ]);

        if (Schema::hasTable('shipments')) {
            DB::table('shipments')
                ->where('order_id', $order->id)
                ->whereDate('delivered_at', '2026-09-10')
                ->update([
                    'delivered_at' => null,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // The inaccurate timestamp must not be restored.
    }
};

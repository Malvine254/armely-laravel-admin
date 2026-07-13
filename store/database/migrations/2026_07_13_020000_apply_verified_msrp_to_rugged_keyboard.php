<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')
            ->where(function ($query) {
                $query->where('tdsynnex_sku_no', 9328579)
                    ->orWhere('specifications->sku', '9328579');
            })
            ->update([
                'base_price' => 400.92,
                'retail_price' => 624.99,
                'sale_price' => null,
                'is_on_sale' => false,
                'offer_source' => null,
            ]);
    }

    public function down(): void
    {
        // Verified pricing data is intentionally not reverted to reseller cost.
    }
};

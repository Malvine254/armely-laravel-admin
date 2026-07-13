<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'supplier_regular_price')) {
                $table->decimal('supplier_regular_price', 15, 2)->nullable()->after('base_price');
            }
            if (!Schema::hasColumn('products', 'offer_source')) {
                $table->string('offer_source', 50)->nullable()->after('is_on_sale');
            }
        });

        DB::table('products')->where('is_on_sale', true)->update([
            'sale_price' => null,
            'is_on_sale' => false,
            'offer_source' => null,
            'sale_started_at' => null,
            'sale_ended_at' => now(),
        ]);

        // Verified PartnerFirst pricing supplied on 2026-07-13.
        DB::table('products')->where('tdsynnex_sku_no', 8938995)->update([
            'base_price' => 369.91,
            'supplier_regular_price' => 404.72,
            'retail_price' => 549.99,
            'sale_price' => 369.91,
            'is_on_sale' => true,
            'offer_source' => 'verified_tdsynnex_special',
            'sale_started_at' => now(),
            'sale_ended_at' => null,
        ]);

        // Verified MSRP; $400.92 remains the private TD reseller cost.
        DB::table('products')->where('tdsynnex_sku_no', 9328579)->update([
            'base_price' => 400.92,
            'retail_price' => 624.99,
            'sale_price' => null,
            'is_on_sale' => false,
            'offer_source' => null,
        ]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'supplier_regular_price')) {
                $table->dropColumn('supplier_regular_price');
            }
            if (Schema::hasColumn('products', 'offer_source')) {
                $table->dropColumn('offer_source');
            }
        });
    }
};

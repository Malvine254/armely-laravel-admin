<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->char('category_segment', 2)->default('')->after('is_hardware')
                ->comment('First 2 chars of UNSPSC categoryCode for fast GROUP BY');
            $table->string('manufacturer', 255)->default('')->after('category_segment')
                ->comment('Denormalized from specifications.manufacturer for fast GROUP BY');

            $table->index(['vendor_id', 'is_hardware', 'base_price', 'category_segment'], 'idx_vendor_hw_price_seg');
            $table->index(['vendor_id', 'is_hardware', 'base_price', 'manufacturer'], 'idx_vendor_hw_price_mfr');
        });

        // Backfill category_segment from specifications JSON
        \Illuminate\Support\Facades\DB::statement("
            UPDATE products
            SET category_segment = LEFT(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.categoryCode')), ''), 2),
                manufacturer = TRIM(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(specifications, '$.manufacturer')), ''))
            WHERE vendor_id = 'TD SYNNEX'
        ");
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_vendor_hw_price_seg');
            $table->dropIndex('idx_vendor_hw_price_mfr');
            $table->dropColumn(['category_segment', 'manufacturer']);
        });
    }
};

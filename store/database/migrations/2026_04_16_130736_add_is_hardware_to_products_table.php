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
            $table->boolean('is_hardware')->default(false)->after('description')
                ->comment('Precomputed: true if product is physical hardware (not license/subscription/software/warranty)');
            $table->index(['vendor_id', 'is_hardware', 'base_price'], 'idx_vendor_hardware_price');
        });

        // Backfill: mark rows as hardware when product_name+description do NOT match exclusion keywords
        $nonHardwareRegex = '(license|lic/sa|subscription|software|office|windows svr|exchange svr|core cal|\\\\bcal\\\\b|addtl prod|step up|coverage|warranty|support|maintenance|consulting|implementation|training|care pack|onsite repair|extended service|service agreement|sa olv|olv nl|renewal|saas)';

        \Illuminate\Support\Facades\DB::statement(
            "UPDATE products SET is_hardware = CASE
                WHEN LOWER(CONCAT(' ', COALESCE(product_name, ''), ' ', COALESCE(description, ''), ' ')) REGEXP ? THEN 0
                ELSE 1
            END
            WHERE vendor_id = 'TD SYNNEX'",
            [$nonHardwareRegex]
        );
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_vendor_hardware_price');
            $table->dropColumn('is_hardware');
        });
    }
};

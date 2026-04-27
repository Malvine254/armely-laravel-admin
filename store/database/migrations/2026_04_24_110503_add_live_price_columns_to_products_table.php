<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Main stock quantity — updated nightly from live data, shown to customers
            $table->unsignedInteger('quantity')->nullable()->default(null)->after('retail_price');

            // Shadow columns — polled every 2 hours from TD Synnex, never displayed directly.
            // At midnight these are compared against the main columns and applied if different.
            $table->decimal('live_price', 15, 2)->nullable()->default(null)->after('quantity');
            $table->decimal('live_retail_price', 15, 2)->nullable()->default(null)->after('live_price');
            $table->unsignedInteger('live_quantity')->nullable()->default(null)->after('live_retail_price');
            $table->boolean('live_is_available')->nullable()->default(null)->after('live_quantity');
            $table->boolean('live_is_discontinued')->nullable()->default(null)->after('live_is_available');
            $table->timestamp('live_checked_at')->nullable()->default(null)->after('live_is_discontinued');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'quantity',
                'live_price',
                'live_retail_price',
                'live_quantity',
                'live_is_available',
                'live_is_discontinued',
                'live_checked_at',
            ]);
        });
    }
};

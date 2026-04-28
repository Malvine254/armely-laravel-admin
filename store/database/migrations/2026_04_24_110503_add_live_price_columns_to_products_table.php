<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Main stock quantity: updated nightly from live data, shown to customers.
            if (!Schema::hasColumn('products', 'quantity')) {
                $table->unsignedInteger('quantity')->nullable()->default(null)->after('retail_price');
            }

            // Shadow columns: polled from TD SYNNEX and not displayed directly.
            if (!Schema::hasColumn('products', 'live_price')) {
                $table->decimal('live_price', 15, 2)->nullable()->default(null)->after('quantity');
            }
            if (!Schema::hasColumn('products', 'live_retail_price')) {
                $table->decimal('live_retail_price', 15, 2)->nullable()->default(null)->after('live_price');
            }
            if (!Schema::hasColumn('products', 'live_quantity')) {
                $table->unsignedInteger('live_quantity')->nullable()->default(null)->after('live_retail_price');
            }
            if (!Schema::hasColumn('products', 'live_is_available')) {
                $table->boolean('live_is_available')->nullable()->default(null)->after('live_quantity');
            }
            if (!Schema::hasColumn('products', 'live_is_discontinued')) {
                $table->boolean('live_is_discontinued')->nullable()->default(null)->after('live_is_available');
            }
            if (!Schema::hasColumn('products', 'live_checked_at')) {
                $table->timestamp('live_checked_at')->nullable()->default(null)->after('live_is_discontinued');
            }
        });
    }

    public function down(): void
    {
        $columns = array_filter([
            'quantity',
            'live_price',
            'live_retail_price',
            'live_quantity',
            'live_is_available',
            'live_is_discontinued',
            'live_checked_at',
        ], fn ($column) => Schema::hasColumn('products', $column));

        if (!empty($columns)) {
            Schema::table('products', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('sale_price', 15, 2)->nullable()->after('retail_price');
            $table->boolean('is_on_sale')->default(false)->after('sale_price');
            $table->timestamp('sale_started_at')->nullable()->after('is_on_sale');
            $table->timestamp('sale_ended_at')->nullable()->after('sale_started_at');
            $table->index('is_on_sale');
        });

        Schema::create('product_price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('supplier_price', 15, 2);
            $table->decimal('regular_price', 15, 2)->nullable();
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->string('source', 50)->default('tdsynnex_priceavailability');
            $table->timestamp('checked_at');
            $table->timestamps();
            $table->index(['product_id', 'checked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_price_histories');
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_on_sale']);
            $table->dropColumn(['sale_price', 'is_on_sale', 'sale_started_at', 'sale_ended_at']);
        });
    }
};

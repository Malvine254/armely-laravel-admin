<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique(); // TD SYNNEX order number
            $table->string('quote_id')->nullable(); // Related quote ID
            $table->string('status')->default('pending'); // pending, processing, shipped, delivered, cancelled
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->json('items')->nullable(); // Store order items
            $table->json('raw_data')->nullable(); // Store full TD SYNNEX response
            $table->timestamp('ordered_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('tracking_info')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('order_number');
            $table->index('quote_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

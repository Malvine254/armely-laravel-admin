<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('quotes')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('line_total', 15, 2);
            
            $table->json('configuration')->nullable()->comment('Custom configs, warranties, etc.');
            
            $table->timestamps();
            
            $table->index('quote_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_line_items');
    }
};

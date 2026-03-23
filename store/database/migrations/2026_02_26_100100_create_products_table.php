<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            
            // TD SYNNEX IDs
            $table->integer('tdsynnex_product_id')->unique();
            $table->integer('tdsynnex_sku_no')->nullable();
            $table->string('vendor_id');
            
            // Product Details
            $table->string('product_name', 500);
            $table->string('mfg_part_no', 100)->nullable();
            $table->text('description')->nullable();
            
            // Pricing
            $table->decimal('base_price', 15, 2)->nullable();
            $table->decimal('retail_price', 15, 2)->nullable();
            
            // Billing
            $table->string('billing_model', 100)->nullable();
            $table->string('billing_frequency', 50)->nullable();
            $table->string('billing_term', 50)->nullable();
            
            // Status
            $table->boolean('is_available')->default(true);
            $table->boolean('is_discontinued')->default(false);
            
            // Metadata
            $table->json('specifications')->nullable();
            $table->json('images')->nullable();
            
            // Sync tracking
            $table->timestamp('last_synced_at')->nullable();
            
            $table->timestamps();
            
            $table->index('vendor_id');
            $table->index('mfg_part_no');
            $table->index(['is_available', 'is_discontinued']);
            // SQLite doesn't support fulltext indexes
            // $table->fullText(['product_name', 'mfg_part_no', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

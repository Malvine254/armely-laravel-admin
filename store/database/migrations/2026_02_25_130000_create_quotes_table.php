<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('quote_id')->unique(); // TD SYNNEX quote ID
            $table->string('status')->default('draft'); // draft, submitted, approved, expired, converted
            $table->text('description')->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->json('items')->nullable(); // Store quote items
            $table->json('raw_data')->nullable(); // Store full TD SYNNEX response
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('quote_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};

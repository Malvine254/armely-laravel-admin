<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique(); // TD SYNNEX invoice number
            $table->string('order_number')->nullable(); // Related order number
            $table->string('status')->default('pending'); // pending, issued, paid, overdue, cancelled
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->json('items')->nullable(); // Store invoice items
            $table->json('raw_data')->nullable(); // Store full TD SYNNEX response
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('pdf_url')->nullable(); // URL to invoice PDF
            $table->timestamps();
            $table->index('user_id');
            $table->index('invoice_number');
            $table->index('order_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->string('type'); // 'quote_approved', 'order_shipped', 'invoice_due', etc.
            $table->string('title');
            $table->text('message');
            $table->string('reference_id')->nullable(); // quote_id, order_number, invoice_number
            $table->string('reference_type')->nullable(); // 'quote', 'order', 'invoice'
            
            $table->enum('status', ['unread', 'read'])->default('unread');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            $table->json('metadata')->nullable();
            $table->timestamp('read_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

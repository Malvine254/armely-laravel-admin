<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            
            $table->enum('type', ['billing', 'shipping', 'both'])->default('shipping');
            $table->string('label')->nullable()->comment('e.g. Main Office, Warehouse A');
            
            $table->string('contact_name')->nullable();
            $table->string('contact_phone', 20)->nullable();
            
            $table->string('street_1');
            $table->string('street_2')->nullable();
            $table->string('city', 100);
            $table->string('state', 50);
            $table->string('postal_code', 20);
            $table->string('country', 2)->default('US');
            
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            $table->index(['company_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};

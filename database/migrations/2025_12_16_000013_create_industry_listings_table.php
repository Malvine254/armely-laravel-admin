<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('industry_listings')) {
            Schema::create('industry_listings', function (Blueprint $table) {
                $table->id();
                $table->string('category')->nullable();
                $table->string('listing_image')->nullable();
                $table->text('body')->nullable();
                $table->string('pdf_url')->nullable();
                $table->timestamps();

                $table->index('category');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('industry_listings');
    }
};

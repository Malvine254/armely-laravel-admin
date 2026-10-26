<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->timestamp('description_enrichment_attempted_at')->nullable()->index();
            $table->timestamp('images_enrichment_attempted_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn(['description_enrichment_attempted_at', 'images_enrichment_attempted_at']);
        });
    }
};

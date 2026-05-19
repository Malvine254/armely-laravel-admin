<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('website_ad_banners')) {
            Schema::create('website_ad_banners', function (Blueprint $table) {
                $table->id();
                $table->string('page')->default('company');
                $table->string('headline');
                $table->text('message')->nullable();
                $table->string('button_label')->nullable();
                $table->string('button_url')->nullable();
                $table->string('image_path')->nullable();
                $table->string('background_style')->nullable();
                $table->unsignedInteger('display_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamps();

                $table->index(['page', 'is_active']);
                $table->index(['starts_at', 'ends_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('website_ad_banners');
    }
};

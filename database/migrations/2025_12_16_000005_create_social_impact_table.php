<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('social_impact')) {
            Schema::create('social_impact', function (Blueprint $table) {
                $table->id();
                $table->string('secure_id')->unique()->nullable();
                $table->string('title')->nullable();
                $table->longText('body')->nullable();
                $table->text('snippet')->nullable();
                $table->string('image_url')->nullable();
                $table->string('posted_date')->nullable();
                $table->string('category')->nullable();
                $table->string('author_name')->nullable();
                $table->timestamps();

                $table->index('secure_id');
                $table->index('category');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('social_impact');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('resources')) {
            Schema::create('resources', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('category')->nullable();
                $table->string('resource_type', 40)->default('guide');
                $table->string('file_url')->nullable();
                $table->string('file_name')->nullable();
                $table->string('file_path')->nullable();
                $table->string('thumbnail_url')->nullable();
                $table->string('thumbnail_path')->nullable();
                $table->boolean('is_published')->default(false);
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_noindex')->default(false);
                $table->timestamps();

                $table->index(['is_published', 'is_featured']);
                $table->index('category');
                $table->index('resource_type');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};

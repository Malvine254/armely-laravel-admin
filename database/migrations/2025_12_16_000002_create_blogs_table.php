<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('blogs')) {
            Schema::create('blogs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('blog_id')->unique()->nullable();
                $table->string('title')->nullable();
                $table->string('author')->nullable();
                $table->string('date')->nullable();
                $table->longText('body')->nullable();
                $table->string('image_path')->nullable();
                $table->unsignedInteger('clicks')->default(0);
                $table->unsignedInteger('views')->default(0);
                $table->string('status')->default('published');
                $table->timestamps();

                $table->index('author');
                $table->index('date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};

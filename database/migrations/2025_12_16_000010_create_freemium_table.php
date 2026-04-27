<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('freemium')) {
            Schema::create('freemium', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('body')->nullable();
                $table->string('image_url')->nullable();
                $table->string('url_get_name')->nullable();
                $table->text('snippet')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('freemium');
    }
};

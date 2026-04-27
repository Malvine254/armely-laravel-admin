<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('offers')) {
            Schema::create('offers', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('body')->nullable();
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};

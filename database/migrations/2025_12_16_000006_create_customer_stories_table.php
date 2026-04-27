<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('customer_stories')) {
            Schema::create('customer_stories', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('position')->nullable();
                $table->text('body_content')->nullable();
                $table->string('profile')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_stories');
    }
};

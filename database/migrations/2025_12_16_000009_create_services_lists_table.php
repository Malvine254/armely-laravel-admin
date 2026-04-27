<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('services_lists')) {
            Schema::create('services_lists', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('image')->nullable();
                $table->text('body')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('services_lists');
    }
};

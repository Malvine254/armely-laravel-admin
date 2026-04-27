<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // NOTE: HomeController queries this column as 'icon-font as icon' which is a bug —
    // MySQL treats the hyphen as subtraction. The correct column name is icon_font.
    // Update HomeController::company() to use 'icon_font as icon' instead.
    public function up(): void
    {
        if (!Schema::hasTable('core_values')) {
            Schema::create('core_values', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('body')->nullable();
                $table->string('icon_font')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('core_values');
    }
};

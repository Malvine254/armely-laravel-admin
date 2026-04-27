<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('consultation')) {
            Schema::create('consultation', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('organization')->nullable();
                $table->string('phone')->nullable();
                $table->text('message')->nullable();
                $table->string('service_type')->nullable();
                $table->timestamps();

                $table->index('email');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation');
    }
};

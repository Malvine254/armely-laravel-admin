<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('team')) {
            Schema::create('team', function (Blueprint $table) {
                $table->id();
                $table->string('team_name')->nullable();
                $table->string('team_title')->nullable();
                $table->text('team_body')->nullable();
                $table->string('team_image')->nullable();
                $table->string('linkedin')->nullable();
                $table->string('facebook')->nullable();
                $table->string('instagram')->nullable();
                $table->string('x')->nullable();
                $table->string('created_date')->nullable();
                $table->timestamps();

                $table->index('team_name');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('team');
    }
};

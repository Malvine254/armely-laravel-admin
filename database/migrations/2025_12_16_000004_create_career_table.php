<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('career')) {
            Schema::create('career', function (Blueprint $table) {
                $table->id();
                $table->string('job_id')->unique()->nullable();
                $table->string('job_title')->nullable();
                $table->text('job_description')->nullable();
                $table->string('job_location')->nullable();
                $table->string('job_type')->nullable();
                $table->string('job_deadline')->nullable();
                $table->timestamps();

                $table->index('job_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('career');
    }
};

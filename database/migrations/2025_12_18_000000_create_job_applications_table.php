<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('job_applications')) {
            Schema::create('job_applications', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('position');
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('zip')->nullable();
                $table->string('role')->nullable();
                $table->string('cv')->nullable();
                $table->integer('status')->default(1); // 1 = pending, 2 = shortlisted, 3 = hired
                $table->timestamp('application_date')->useCurrent();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};

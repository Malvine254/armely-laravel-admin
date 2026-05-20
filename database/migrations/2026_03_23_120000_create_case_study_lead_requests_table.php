<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('case_study_lead_requests')) {
            return;
        }

        Schema::create('case_study_lead_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 50);
            $table->string('organization');
            $table->string('job_title')->nullable();
            $table->string('country', 120)->nullable();
            $table->enum('interest', ['case-studies', 'white-papers', 'both'])->default('case-studies');
            $table->string('requested_resource')->nullable();
            $table->unsignedBigInteger('case_study_id')->nullable();
            $table->text('message')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 1000)->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('case_study_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_study_lead_requests');
    }
};

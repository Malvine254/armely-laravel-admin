<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('event_name');
            $table->string('full_name', 150);
            $table->string('work_email');
            $table->string('organization', 200);
            $table->string('job_title');
            $table->string('compliance_focus')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->timestamp('event_link_sent_at')->nullable();
            $table->timestamps();

            $table->index(['event_name', 'work_email']);
            $table->index(['status', 'event_link_sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};

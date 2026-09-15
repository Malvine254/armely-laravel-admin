<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mela_documentation_requests')) {
            return;
        }

        Schema::create('mela_documentation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('organization')->nullable();
            $table->string('job_title')->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('message')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 1000)->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamps();

            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mela_documentation_requests');
    }
};

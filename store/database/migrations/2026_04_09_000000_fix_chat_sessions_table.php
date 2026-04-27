<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration should be safe in production rebuild flows.
        // If the table already exists, avoid destructive drops that can fail on FK constraints.
        if (Schema::hasTable('chat_sessions')) {
            return;
        }

        // Create table when missing.
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title')->default('New chat');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->integer('escalation_level')->default(0);
            $table->string('escalation_reason')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'updated_at']);
            $table->index(['user_id', 'last_message_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};

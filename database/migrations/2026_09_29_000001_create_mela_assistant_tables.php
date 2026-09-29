<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mela_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('token_hash', 64);
            $table->json('memory')->nullable();
            $table->text('summary')->nullable();
            $table->unsignedInteger('summarized_through_id')->default(0);
            $table->string('escalation_status', 32)->default('not_requested');
            $table->unsignedInteger('user_message_count')->default(0);
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('landing_page', 500)->nullable();
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('mela_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('conversation_id')->index();
            $table->string('role', 16);
            $table->text('content');
            $table->json('meta')->nullable();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });

        Schema::create('mela_knowledge_pages', function (Blueprint $table) {
            $table->id();
            $table->string('url', 500)->unique();
            $table->string('title', 300)->nullable();
            $table->string('page_type', 32)->default('other')->index();
            $table->string('content_hash', 64)->nullable();
            $table->timestamp('last_updated')->nullable();
            $table->timestamp('last_indexed_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('mela_knowledge_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('mela_knowledge_pages')->cascadeOnDelete();
            $table->unsignedInteger('chunk_index');
            $table->string('heading', 300)->nullable();
            $table->text('content');
            $table->json('metadata')->nullable();
            $table->string('content_hash', 64)->index();
            // Unit-normalised float32 vector packed with pack('g*').
            $table->binary('embedding')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mela_knowledge_chunks');
        Schema::dropIfExists('mela_knowledge_pages');
        Schema::dropIfExists('mela_messages');
        Schema::dropIfExists('mela_conversations');
    }
};

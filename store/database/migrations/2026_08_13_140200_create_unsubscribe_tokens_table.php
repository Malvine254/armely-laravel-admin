<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unsubscribe_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('email', 320)->nullable();
            $table->char('token_hash', 64)->unique();
            $table->string('scope', 32)->default('marketing')->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->string('last_used_ip', 45)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'scope'], 'ut_user_scope_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unsubscribe_tokens');
    }
};

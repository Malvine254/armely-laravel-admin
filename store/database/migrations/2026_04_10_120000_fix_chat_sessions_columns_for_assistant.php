<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('chat_sessions')) {
            return;
        }

        Schema::table('chat_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_sessions', 'escalated_to_human')) {
                $table->boolean('escalated_to_human')->default(false)->after('last_message_at');
            }

            if (!Schema::hasColumn('chat_sessions', 'escalated_at')) {
                $table->timestamp('escalated_at')->nullable()->after('escalated_to_human');
            }

            if (!Schema::hasColumn('chat_sessions', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('escalated_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('chat_sessions')) {
            return;
        }

        Schema::table('chat_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('chat_sessions', 'escalated_at')) {
                $table->dropColumn('escalated_at');
            }

            if (Schema::hasColumn('chat_sessions', 'escalated_to_human')) {
                $table->dropColumn('escalated_to_human');
            }

            // Keep resolved_at because other flows rely on it.
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chat_messages')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->index(['chat_session_id', 'id'], 'chat_messages_session_id_id_index');
            });
        }

        if (Schema::hasTable('chat_sessions')) {
            Schema::table('chat_sessions', function (Blueprint $table) {
                $table->index(['user_id', 'last_message_at', 'updated_at'], 'chat_sessions_user_last_updated_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chat_messages')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->dropIndex('chat_messages_session_id_id_index');
            });
        }

        if (Schema::hasTable('chat_sessions')) {
            Schema::table('chat_sessions', function (Blueprint $table) {
                $table->dropIndex('chat_sessions_user_last_updated_index');
            });
        }
    }
};
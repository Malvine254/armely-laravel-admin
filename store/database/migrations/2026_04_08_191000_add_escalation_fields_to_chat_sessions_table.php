<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->boolean('escalated_to_human')->default(false)->after('last_message_at');
            $table->timestamp('escalated_at')->nullable()->after('escalated_to_human');
        });
    }

    public function down(): void
    {
        Schema::table('chat_sessions', function (Blueprint $table) {
            $table->dropColumn(['escalated_to_human', 'escalated_at']);
        });
    }
};

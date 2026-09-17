<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('chat_attachments')) {
            Schema::create('chat_attachments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('chat_message_id')->nullable();
                $table->string('disk', 32)->default('local');
                $table->string('path');
                $table->string('original_name');
                $table->string('mime_type', 128);
                $table->unsignedInteger('size_bytes');
                $table->text('extracted_text')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'chat_message_id']);
            });
        }

        if (Schema::hasTable('chat_messages') && !Schema::hasColumn('chat_messages', 'attachments')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->json('attachments')->nullable()->after('actions');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_attachments');

        if (Schema::hasTable('chat_messages') && Schema::hasColumn('chat_messages', 'attachments')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->dropColumn('attachments');
            });
        }
    }
};

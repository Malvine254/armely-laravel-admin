<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('chat_messages')) {
            return;
        }

        $dbName = DB::getDatabaseName();
        $idMeta = DB::table('information_schema.columns')
            ->select(['COLUMN_KEY', 'EXTRA'])
            ->where('TABLE_SCHEMA', $dbName)
            ->where('TABLE_NAME', 'chat_messages')
            ->where('COLUMN_NAME', 'id')
            ->first();

        $isHealthy = $idMeta
            && strtoupper((string) ($idMeta->COLUMN_KEY ?? '')) === 'PRI'
            && str_contains(strtolower((string) ($idMeta->EXTRA ?? '')), 'auto_increment');

        if ($isHealthy) {
            return;
        }

        $tmp = 'chat_messages_repair_tmp';

        DB::statement('DROP TABLE IF EXISTS `'.$tmp.'`');
        Schema::create($tmp, function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('chat_session_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role');
            $table->longText('content');
            $table->json('actions')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('chat_session_id')->references('id')->on('chat_sessions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['chat_session_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        // Preserve chat history while regenerating a valid auto-increment PK.
        DB::statement(
            'INSERT INTO `'.$tmp.'` (`chat_session_id`, `user_id`, `role`, `content`, `actions`, `metadata`, `created_at`, `updated_at`) '
            .'SELECT `chat_session_id`, `user_id`, `role`, `content`, `actions`, `metadata`, `created_at`, `updated_at` '
            .'FROM `chat_messages` ORDER BY `created_at` ASC, `id` ASC'
        );

        DB::statement('DROP TABLE `chat_messages`');
        DB::statement('RENAME TABLE `'.$tmp.'` TO `chat_messages`');
    }

    public function down(): void
    {
        // Irreversible schema repair migration.
    }
};

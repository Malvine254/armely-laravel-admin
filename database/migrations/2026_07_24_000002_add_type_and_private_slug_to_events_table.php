<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('event_type', 20)->default('normal')->after('recorded_url');
            $table->string('private_slug', 80)->nullable()->unique()->after('event_type');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropUnique(['private_slug']);
            $table->dropColumn(['event_type', 'private_slug']);
        });
    }
};

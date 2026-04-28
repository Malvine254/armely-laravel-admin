<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'temp_password_expires_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('temp_password_expires_at')->nullable()->after('force_password_change');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('temp_password_expires_at');
        });
    }
};

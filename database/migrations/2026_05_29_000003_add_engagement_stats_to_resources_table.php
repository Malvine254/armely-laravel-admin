<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('resources')) {
            return;
        }

        Schema::table('resources', function (Blueprint $table) {
            if (!Schema::hasColumn('resources', 'click_count')) {
                $table->unsignedBigInteger('click_count')->default(0)->after('is_noindex');
            }

            if (!Schema::hasColumn('resources', 'download_count')) {
                $table->unsignedBigInteger('download_count')->default(0)->after('click_count');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('resources')) {
            return;
        }

        Schema::table('resources', function (Blueprint $table) {
            if (Schema::hasColumn('resources', 'download_count')) {
                $table->dropColumn('download_count');
            }

            if (Schema::hasColumn('resources', 'click_count')) {
                $table->dropColumn('click_count');
            }
        });
    }
};

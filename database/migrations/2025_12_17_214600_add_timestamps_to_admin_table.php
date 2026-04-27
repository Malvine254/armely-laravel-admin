<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin', function (Blueprint $table) {
            if (!Schema::hasColumn('admin', 'created_at')) {
                $table->timestamp('created_at')->nullable()->after('joined_date');
            }
            if (!Schema::hasColumn('admin', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admin', function (Blueprint $table) {
            if (Schema::hasColumn('admin', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
            if (Schema::hasColumn('admin', 'created_at')) {
                $table->dropColumn('created_at');
            }
        });
    }
};

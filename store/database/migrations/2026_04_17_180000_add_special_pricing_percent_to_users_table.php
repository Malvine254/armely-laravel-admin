<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'special_pricing_percent')) {
                $table->decimal('special_pricing_percent', 5, 2)
                    ->default(0)
                    ->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'special_pricing_percent')) {
                $table->dropColumn('special_pricing_percent');
            }
        });
    }
};

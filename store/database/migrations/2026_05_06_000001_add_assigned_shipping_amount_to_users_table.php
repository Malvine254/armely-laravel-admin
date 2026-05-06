<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'assigned_shipping_amount')) {
                $table->decimal('assigned_shipping_amount', 10, 2)
                    ->default(0)
                    ->after('special_pricing_percent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'assigned_shipping_amount')) {
                $table->dropColumn('assigned_shipping_amount');
            }
        });
    }
};

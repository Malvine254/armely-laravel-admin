<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('customer_stories') && !Schema::hasColumn('customer_stories', 'company')) {
            Schema::table('customer_stories', function (Blueprint $table) {
                $table->string('company')->nullable()->after('position');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('customer_stories') && Schema::hasColumn('customer_stories', 'company')) {
            Schema::table('customer_stories', function (Blueprint $table) {
                $table->dropColumn('company');
            });
        }
    }
};

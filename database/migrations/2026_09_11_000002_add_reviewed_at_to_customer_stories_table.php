<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = Schema::hasTable('customer_stories')
            ? 'customer_stories'
            : (Schema::hasTable('customer_story') ? 'customer_story' : null);

        if ($table !== null && !Schema::hasColumn($table, 'reviewed_at')) {
            Schema::table($table, function (Blueprint $table) {
                $table->date('reviewed_at')->nullable()->after('position');
            });
        }
    }

    public function down(): void
    {
        $table = Schema::hasTable('customer_stories')
            ? 'customer_stories'
            : (Schema::hasTable('customer_story') ? 'customer_story' : null);

        if ($table !== null && Schema::hasColumn($table, 'reviewed_at')) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('reviewed_at');
            });
        }
    }
};

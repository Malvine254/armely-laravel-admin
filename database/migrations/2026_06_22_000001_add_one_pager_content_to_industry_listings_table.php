<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('industry_listings') || Schema::hasColumn('industry_listings', 'one_pager_content')) {
            return;
        }

        Schema::table('industry_listings', function (Blueprint $table) {
            $table->longText('one_pager_content')->nullable()->after('body');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('industry_listings') || !Schema::hasColumn('industry_listings', 'one_pager_content')) {
            return;
        }

        Schema::table('industry_listings', function (Blueprint $table) {
            $table->dropColumn('one_pager_content');
        });
    }
};

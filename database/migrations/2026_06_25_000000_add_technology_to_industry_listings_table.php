<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('industry_listings')) {
            return;
        }

        Schema::table('industry_listings', function (Blueprint $table) {
            if (!Schema::hasColumn('industry_listings', 'technology')) {
                $table->string('technology')->nullable()->after('category');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('industry_listings')) {
            return;
        }

        Schema::table('industry_listings', function (Blueprint $table) {
            if (Schema::hasColumn('industry_listings', 'technology')) {
                $table->dropColumn('technology');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('customer_stories') && !Schema::hasColumn('customer_stories', 'pdf_url')) {
            Schema::table('customer_stories', function (Blueprint $table) {
                // Full review document: a case_docs filename or an absolute URL.
                $table->string('pdf_url')->nullable()->after('company');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('customer_stories') && Schema::hasColumn('customer_stories', 'pdf_url')) {
            Schema::table('customer_stories', function (Blueprint $table) {
                $table->dropColumn('pdf_url');
            });
        }
    }
};

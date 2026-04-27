<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('job_applications') && !Schema::hasColumn('job_applications', 'job_id')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->string('job_id')->nullable()->after('id');
                $table->index('job_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('job_applications') && Schema::hasColumn('job_applications', 'job_id')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->dropIndex(['job_id']);
                $table->dropColumn('job_id');
            });
        }
    }
};

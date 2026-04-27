<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_study_lead_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('case_study_lead_requests', 'white_paper_id')) {
                $table->unsignedBigInteger('white_paper_id')->nullable()->after('case_study_id');
                $table->index('white_paper_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('case_study_lead_requests', function (Blueprint $table) {
            if (Schema::hasColumn('case_study_lead_requests', 'white_paper_id')) {
                $table->dropIndex(['white_paper_id']);
                $table->dropColumn('white_paper_id');
            }
        });
    }
};

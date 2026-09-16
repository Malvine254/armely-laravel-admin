<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('career')) {
            return;
        }

        if (!Schema::hasColumn('career', 'public_token')) {
            Schema::table('career', function (Blueprint $table) {
                $table->string('public_token', 64)->nullable()->unique()->after('job_id');
            });
        }

        DB::table('career')
            ->whereNull('public_token')
            ->orderBy('id')
            ->get(['id'])
            ->each(function ($career) {
                do {
                    $token = Str::random(48);
                } while (DB::table('career')->where('public_token', $token)->exists());

                DB::table('career')->where('id', $career->id)->update(['public_token' => $token]);
            });
    }

    public function down(): void
    {
        if (Schema::hasTable('career') && Schema::hasColumn('career', 'public_token')) {
            Schema::table('career', function (Blueprint $table) {
                $table->dropUnique(['public_token']);
                $table->dropColumn('public_token');
            });
        }
    }
};
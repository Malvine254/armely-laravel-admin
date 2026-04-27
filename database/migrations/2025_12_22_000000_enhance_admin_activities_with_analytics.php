<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Enhance admin_activities table with analytics fields
        if (Schema::hasTable('admin_activities')) {
            if (!Schema::hasColumn('admin_activities', 'ip_address')) {
                Schema::table('admin_activities', function (Blueprint $table) {
                    $table->string('ip_address', 45)->nullable()->after('entity_id'); // IPv6 can be up to 45 chars
                });
            }

            if (!Schema::hasColumn('admin_activities', 'user_agent')) {
                Schema::table('admin_activities', function (Blueprint $table) {
                    $table->text('user_agent')->nullable()->after('ip_address');
                });
            }

            if (!Schema::hasColumn('admin_activities', 'page_url')) {
                Schema::table('admin_activities', function (Blueprint $table) {
                    $table->string('page_url', 500)->nullable()->after('user_agent');
                });
            }

            if (!Schema::hasColumn('admin_activities', 'referrer')) {
                Schema::table('admin_activities', function (Blueprint $table) {
                    $table->string('referrer', 500)->nullable()->after('page_url');
                });
            }

            if (!Schema::hasColumn('admin_activities', 'country')) {
                Schema::table('admin_activities', function (Blueprint $table) {
                    $table->string('country', 100)->nullable()->after('referrer');
                });
            }

            if (!Schema::hasColumn('admin_activities', 'visit_count')) {
                Schema::table('admin_activities', function (Blueprint $table) {
                    $table->integer('visit_count')->default(1)->after('country');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('admin_activities')) {
            Schema::table('admin_activities', function (Blueprint $table) {
                $table->dropColumn([
                    'ip_address',
                    'user_agent',
                    'page_url',
                    'referrer',
                    'country',
                    'visit_count'
                ]);
            });
        }
    }
};

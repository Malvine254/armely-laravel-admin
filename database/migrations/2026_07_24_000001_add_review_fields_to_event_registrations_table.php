<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('event_registrations', 'status')) {
                $table->string('status', 20)->default('pending')->after('ip_address');
                $table->timestamp('verified_at')->nullable()->after('status');
                $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
                $table->unsignedBigInteger('event_id')->nullable()->after('verified_by');
                $table->timestamp('event_link_sent_at')->nullable()->after('event_id');
                $table->index(['status', 'event_link_sent_at']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn(['status', 'verified_at', 'verified_by', 'event_id', 'event_link_sent_at']);
        });
    }
};

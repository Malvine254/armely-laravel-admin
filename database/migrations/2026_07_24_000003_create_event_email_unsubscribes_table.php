<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->string('unsubscribe_token', 64)->nullable()->unique()->after('event_link_sent_at');
        });

        Schema::create('event_email_unsubscribes', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('unsubscribed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_email_unsubscribes');
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropUnique(['unsubscribe_token']);
            $table->dropColumn('unsubscribe_token');
        });
    }
};

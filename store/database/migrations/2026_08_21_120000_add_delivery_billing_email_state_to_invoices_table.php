<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->timestamp('issued_email_attempted_at')->nullable()->after('issued_at');
            $table->timestamp('issued_email_sent_at')->nullable()->after('issued_email_attempted_at');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['issued_email_attempted_at', 'issued_email_sent_at']);
        });
    }
};

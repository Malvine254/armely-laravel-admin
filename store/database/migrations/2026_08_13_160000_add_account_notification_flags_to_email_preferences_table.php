<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_preferences', function (Blueprint $table) {
            $table->boolean('notification_email_enabled')->default(true)->after('transactional_enabled');
            $table->boolean('quotes_notifications_enabled')->default(true)->after('notification_email_enabled');
            $table->boolean('orders_notifications_enabled')->default(true)->after('quotes_notifications_enabled');
            $table->boolean('invoices_notifications_enabled')->default(true)->after('orders_notifications_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('email_preferences', function (Blueprint $table) {
            $table->dropColumn([
                'notification_email_enabled',
                'quotes_notifications_enabled',
                'orders_notifications_enabled',
                'invoices_notifications_enabled',
            ]);
        });
    }
};

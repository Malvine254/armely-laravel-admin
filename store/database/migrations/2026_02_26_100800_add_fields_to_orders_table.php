<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->nullOnDelete()->after('quote_id');
            $table->foreignId('billing_address_id')->nullable()->constrained('addresses')->nullOnDelete()->after('shipping_address_id');
            $table->string('shipping_amount', 15)->default(0)->after('discount_amount');
            $table->string('payment_status')->default('pending')->after('status')->comment('pending, paid, failed');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->text('cancellation_reason')->nullable()->after('delivered_at');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
            $table->string('tdsynnex_order_id')->nullable()->unique()->after('order_number');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['shipping_address_id', 'billing_address_id']);
            $table->dropColumn([
                'shipping_address_id', 'billing_address_id', 'shipping_amount', 
                'payment_status', 'payment_method', 'cancellation_reason', 
                'cancelled_at', 'tdsynnex_order_id'
            ]);
        });
    }
};

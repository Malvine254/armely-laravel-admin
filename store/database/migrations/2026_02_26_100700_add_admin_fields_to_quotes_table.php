<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('expires_at');
            $table->timestamp('approved_at')->nullable()->after('expires_at');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
            $table->text('admin_notes')->nullable()->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['approved_by']);
            $table->dropColumn(['approved_by', 'approved_at', 'rejected_at', 'rejection_reason', 'admin_notes']);
        });
    }
};

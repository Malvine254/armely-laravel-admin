<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('quotes')) {
            Schema::create('quotes', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('quote_id')->unique();
                $table->string('status')->default('draft');
                $table->text('description')->nullable();
                $table->decimal('total_amount', 15, 2)->nullable();
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->json('items')->nullable();
                $table->json('raw_data')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('rejected_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->index('quote_id');
                $table->index('status');
            });

            return;
        }

        Schema::table('quotes', function (Blueprint $table): void {
            if (!Schema::hasColumn('quotes', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('expires_at');
            }
            if (!Schema::hasColumn('quotes', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('quotes', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('quotes', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('rejected_at');
            }
            if (!Schema::hasColumn('quotes', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('rejection_reason');
            }
        });
    }

    public function down(): void
    {
        // Keep as non-destructive repair migration.
    }
};

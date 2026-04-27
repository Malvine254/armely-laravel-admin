<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('admin_activities')) {
            Schema::create('admin_activities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->string('action', 64);
                $table->string('entity_type', 128);
                $table->string('entity_id', 128)->nullable();
                $table->text('description')->nullable();
                $table->timestamp('created_at')->nullable();

                // If admin table uses different PK, adjust accordingly
                // No foreign key to avoid cross-env mismatches, but index for lookups
                $table->index(['admin_id', 'entity_type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activities');
    }
};

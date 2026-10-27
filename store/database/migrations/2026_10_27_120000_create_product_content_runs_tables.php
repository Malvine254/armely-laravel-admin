<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_content_runs', function (Blueprint $table): void {
            $table->id();
            $table->string('mode', 32);
            $table->unsignedSmallInteger('min_description_length')->default(0);
            $table->string('status', 16)->index();
            $table->unsignedBigInteger('after_id')->default(0);
            $table->unsignedInteger('total_candidates')->default(0);
            $table->unsignedInteger('processed')->default(0);
            $table->unsignedInteger('ready')->default(0);
            $table->unsignedInteger('needs_review')->default(0);
            $table->unsignedInteger('failed')->default(0);
            $table->string('current_product_name')->nullable();
            $table->unsignedBigInteger('started_by')->nullable();
            $table->timestamp('heartbeat_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });

        Schema::create('product_content_run_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('run_id')->constrained('product_content_runs')->cascadeOnDelete();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('product_name');
            $table->string('result', 16)->index();
            $table->text('old_description')->nullable();
            $table->text('new_description')->nullable();
            $table->string('description_source')->nullable();
            $table->text('image_url')->nullable();
            $table->json('warnings')->nullable();
            $table->unsignedInteger('duration_ms')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_content_run_items');
        Schema::dropIfExists('product_content_runs');
    }
};

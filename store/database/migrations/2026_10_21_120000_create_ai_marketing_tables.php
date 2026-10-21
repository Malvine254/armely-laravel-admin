<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('features');
            $table->text('summary')->nullable();
            $table->decimal('confidence', 4, 3)->nullable();
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->timestamp('data_freshness_at')->index();
            $table->timestamps();
        });

        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('type', 64)->unique();
            $table->string('name', 120);
            $table->string('status', 24)->default('disabled')->index();
            $table->unsignedSmallInteger('priority')->default(100);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        DB::table('marketing_campaigns')->insert([
            'type' => 'personalized_recommendation',
            'name' => 'Personalized product recommendations',
            'status' => 'disabled',
            'priority' => 100,
            'settings' => json_encode(['enabled' => false]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::create('marketing_campaign_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('marketing_campaigns')->nullOnDelete();
            $table->string('campaign_type', 64)->index();
            $table->string('status', 32)->index();
            $table->string('reason', 255);
            $table->json('product_ids')->nullable();
            $table->json('model_metadata')->nullable();
            $table->string('idempotency_key', 191)->unique();
            $table->timestamp('decided_at')->index();
            $table->timestamps();

            $table->index(['user_id', 'decided_at'], 'mcd_user_decided_idx');
        });

        Schema::create('marketing_email_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('decision_id')->nullable()->constrained('marketing_campaign_decisions')->nullOnDelete();
            $table->string('campaign_type', 64)->index();
            $table->string('status', 32)->index();
            $table->string('idempotency_key', 191)->unique();
            $table->string('subject', 255)->nullable();
            $table->longText('content')->nullable();
            $table->json('product_ids')->nullable();
            $table->string('provider_message_id', 191)->nullable()->index();
            $table->text('failure_reason')->nullable();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'sent_at'], 'meh_user_sent_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_email_history');
        Schema::dropIfExists('marketing_campaign_decisions');
        Schema::dropIfExists('marketing_campaigns');
        Schema::dropIfExists('marketing_customer_profiles');
    }
};

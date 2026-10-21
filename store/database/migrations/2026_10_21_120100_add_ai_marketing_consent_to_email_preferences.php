<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_preferences', function (Blueprint $table): void {
            $table->boolean('ai_marketing_enabled')->default(false)->after('marketing_enabled');
            $table->timestamp('ai_marketing_consent_at')->nullable()->after('ai_marketing_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('email_preferences', function (Blueprint $table): void {
            $table->dropColumn(['ai_marketing_enabled', 'ai_marketing_consent_at']);
        });
    }
};

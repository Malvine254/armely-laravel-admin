<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mela_conversations', function (Blueprint $table) {
            $table->char('country_code', 2)->nullable();
        });
        Schema::create('mela_index_settings', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->unsignedInteger('interval_minutes')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->string('site_fingerprint', 64)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mela_index_settings');
        Schema::table('mela_conversations', fn (Blueprint $table) => $table->dropColumn('country_code'));
    }
};

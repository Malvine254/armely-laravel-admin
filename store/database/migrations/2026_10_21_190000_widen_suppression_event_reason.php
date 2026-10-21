<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppression_events', function (Blueprint $table): void {
            $table->string('reason', 96)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Retain the widened column so rollback cannot truncate existing send markers.
    }
};

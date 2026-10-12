<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_cart_snapshots', function (Blueprint $table) {
            $table->unsignedInteger('revision')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('user_cart_snapshots', function (Blueprint $table) {
            $table->dropColumn('revision');
        });
    }
};

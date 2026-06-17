<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        $now = Carbon::now();

        DB::table('app_settings')->updateOrInsert(
            ['key' => 'price_sync.timezone'],
            [
                'value' => json_encode('America/Chicago'),
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        DB::table('app_settings')->updateOrInsert(
            ['key' => 'price_sync.enable_http_fallback'],
            [
                'value' => json_encode(true),
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );
    }

    public function down(): void
    {
        $now = Carbon::now();

        DB::table('app_settings')->updateOrInsert(
            ['key' => 'price_sync.timezone'],
            [
                'value' => json_encode('Africa/Nairobi'),
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        DB::table('app_settings')->updateOrInsert(
            ['key' => 'price_sync.enable_http_fallback'],
            [
                'value' => json_encode(false),
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );
    }
};

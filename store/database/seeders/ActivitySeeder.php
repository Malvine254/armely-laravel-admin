<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Activity;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed activities for user 1 (Malvine Owuor)
        $userId = 1;
        
        Activity::create([
            'user_id' => $userId,
            'type' => 'quote',
            'action' => 'created',
            'description' => 'Quote requested for 5 items',
            'metadata' => ['quote_id' => 'Q001', 'item_count' => 5],
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        Activity::create([
            'user_id' => $userId,
            'type' => 'favorite',
            'action' => 'updated',
            'description' => 'Added 3 items to favorites',
            'metadata' => ['item_count' => 3],
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        Activity::create([
            'user_id' => $userId,
            'type' => 'profile',
            'action' => 'updated',
            'description' => 'Updated account profile',
            'metadata' => [],
            'created_at' => now()->subDays(7),
            'updated_at' => now()->subDays(7),
        ]);

        Activity::create([
            'user_id' => $userId,
            'type' => 'order',
            'action' => 'created',
            'description' => 'Converted quote to order #ORD-001',
            'metadata' => ['quote_id' => 'Q001', 'order_number' => 'ORD-001'],
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        Activity::create([
            'user_id' => $userId,
            'type' => 'quote',
            'action' => 'created',
            'description' => 'Quote requested for 3 items',
            'metadata' => ['quote_id' => 'Q002', 'item_count' => 3],
            'created_at' => now()->subDays(12),
            'updated_at' => now()->subDays(12),
        ]);
    }
}

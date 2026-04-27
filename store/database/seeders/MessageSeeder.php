<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Message;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = 1; // Malvine Owuor
        
        // Order approval message
        Message::create([
            'user_id' => $userId,
            'type' => 'order',
            'title' => 'Order Approved',
            'message' => 'Your order #ORD-001 has been approved and is now being processed. Expected delivery: 3-5 business days.',
            'reference_id' => 'ORD-001',
            'status' => 'unread',
            'priority' => 'high',
            'metadata' => ['order_number' => 'ORD-001', 'action' => 'approved'],
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHours(2),
        ]);

        // Quote accepted message
        Message::create([
            'user_id' => $userId,
            'type' => 'quote',
            'title' => 'Quote Accepted',
            'message' => 'Your quote request Q-2024-001 has been accepted. Total amount: $2,456.00. You can now convert this to an order.',
            'reference_id' => 'Q-2024-001',
            'status' => 'unread',
            'priority' => 'normal',
            'metadata' => ['quote_id' => 'Q-2024-001', 'amount' => 2456.00],
            'created_at' => now()->subHours(5),
            'updated_at' => now()->subHours(5),
        ]);

        // Invoice ready message
        Message::create([
            'user_id' => $userId,
            'type' => 'invoice',
            'title' => 'Invoice Ready',
            'message' => 'Invoice #INV-2024-001 is now available for order ORD-002. Amount due: $1,234.56. Due date: March 10, 2026.',
            'reference_id' => 'INV-2024-001',
            'status' => 'unread',
            'priority' => 'high',
            'metadata' => ['invoice_number' => 'INV-2024-001', 'amount' => 1234.56],
            'created_at' => now()->subHours(8),
            'updated_at' => now()->subHours(8),
        ]);

        // Order shipped message
        Message::create([
            'user_id' => $userId,
            'type' => 'order',
            'title' => 'Order Shipped',
            'message' => 'Great news! Your order #ORD-003 has been shipped. Tracking number: TRK123456789. Expected delivery: Feb 28, 2026.',
            'reference_id' => 'ORD-003',
            'status' => 'read',
            'priority' => 'normal',
            'metadata' => ['order_number' => 'ORD-003', 'tracking' => 'TRK123456789'],
            'read_at' => now()->subHours(1),
            'created_at' => now()->subDay(),
            'updated_at' => now()->subHours(1),
        ]);

        // System notification
        Message::create([
            'user_id' => $userId,
            'type' => 'system',
            'title' => 'Welcome to Armely Store',
            'message' => 'Thank you for choosing Armely Store for your B2B hardware procurement needs. Explore our catalog and request quotes with ease!',
            'reference_id' => null,
            'status' => 'read',
            'priority' => 'low',
            'metadata' => ['source' => 'system'],
            'read_at' => now()->subDays(2),
            'created_at' => now()->subDays(7),
            'updated_at' => now()->subDays(2),
        ]);

        // Quote expired message
        Message::create([
            'user_id' => $userId,
            'type' => 'quote',
            'title' => 'Quote Expiring Soon',
            'message' => 'Your quote Q-2024-002 will expire in 2 days. Please review and convert to an order if interested.',
            'reference_id' => 'Q-2024-002',
            'status' => 'read',
            'priority' => 'normal',
            'metadata' => ['quote_id' => 'Q-2024-002', 'expires_in_days' => 2],
            'read_at' => now()->subDays(1),
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(1),
        ]);
    }
}

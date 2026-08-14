<?php

namespace Tests\Unit;

use App\Http\Controllers\MessageController;
use App\Services\AzureOpenAiChatService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class MessageControllerFollowUpTest extends TestCase
{
    public function test_account_intent_is_inherited_only_by_a_real_follow_up(): void
    {
        $reflection = new ReflectionClass(MessageController::class);
        $controller = $reflection->newInstanceWithoutConstructor();
        $method = $reflection->getMethod('inferFollowUpTopic');
        $history = [[
            'role' => 'assistant',
            'intent' => 'invoice_payment',
            'content' => 'You have two outstanding invoices.',
        ]];

        $this->assertSame('invoice', $method->invoke($controller, 'what about the oldest one?', $history));
        $this->assertSame('invoice', $method->invoke($controller, 'show me more details', $history));
        $this->assertSame('invoice', $method->invoke($controller, 'what was the price?', $history));
        $this->assertNull($method->invoke($controller, 'can we have a talk', $history));
        $this->assertNull($method->invoke($controller, 'help me write an email', $history));
    }

    public function test_budget_language_and_monitor_privacy_accessories_are_understood(): void
    {
        $reflection = new ReflectionClass(MessageController::class);
        $controller = $reflection->newInstanceWithoutConstructor();

        $contextMethod = $reflection->getMethod('buildProductSearchContext');
        $context = $contextMethod->invoke($controller, 'I need a budget friendly monitor', []);
        $this->assertTrue($context['budget_priority']);
        $this->assertSame('monitor', $context['device_type']);

        $accessoryMethod = $reflection->getMethod('isAccessoryLikeProduct');
        $this->assertTrue($accessoryMethod->invoke($controller, [
            'name' => 'PRIVACYVIEW 23.5IN WIDESCREEN MONITOR PR',
            'description' => 'Privacy filter for displays',
        ]));
    }

    public function test_order_agent_answers_line_item_name_and_price_follow_ups(): void
    {
        $reflection = new ReflectionClass(AzureOpenAiChatService::class);
        $service = $reflection->newInstanceWithoutConstructor();
        $method = $reflection->getMethod('runOrderAgent');
        $context = ['recent_orders' => [[
            'order_number' => '161663243',
            'status' => 'delivered',
            'total_amount' => 210,
            'items' => [[
                'name' => 'APC BACK-UPS CS 350VA 120V',
                'quantity' => 1,
                'unit_price' => 113.60,
            ]],
        ], [
            'order_number' => 'ORD-PENDING',
            'status' => 'pending',
            'total_amount' => 3684.39,
            'items' => [],
        ]]];

        $history = [['role' => 'assistant', 'intent' => 'order_status', 'content' => 'Order 161663243 was delivered.']];
        $nameReply = $method->invoke($service, 'what was the name of the product that was delivered', $context, $history);
        $priceReply = $method->invoke($service, 'how much was it', $context, $history);

        $this->assertStringContainsString('APC BACK-UPS CS 350VA 120V', $nameReply['reply']);
        $this->assertStringContainsString('$113.60', $priceReply['reply']);
        $this->assertStringNotContainsString('$210.00', $priceReply['reply']);
    }

    public function test_order_agent_filters_completed_orders(): void
    {
        $reflection = new ReflectionClass(AzureOpenAiChatService::class);
        $service = $reflection->newInstanceWithoutConstructor();
        $method = $reflection->getMethod('runOrderAgent');
        $context = ['recent_orders' => [
            ['order_number' => 'DELIVERED-1', 'status' => 'delivered', 'total_amount' => 10, 'items' => []],
            ['order_number' => 'PENDING-1', 'status' => 'pending', 'total_amount' => 20, 'items' => []],
        ]];

        $result = $method->invoke($service, 'check my complete orders', $context, []);

        $this->assertStringContainsString('DELIVERED-1', $result['reply']);
        $this->assertStringNotContainsString('PENDING-1', $result['reply']);
    }
}

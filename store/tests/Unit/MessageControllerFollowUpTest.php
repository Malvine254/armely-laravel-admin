<?php

namespace Tests\Unit;

use App\Http\Controllers\MessageController;
use App\Services\AzureOpenAiChatService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class MessageControllerFollowUpTest extends TestCase
{
    public function test_quote_delivery_follow_up_returns_only_the_linked_delivered_quote(): void
    {
        $reflection = new ReflectionClass(AzureOpenAiChatService::class);
        $service = $reflection->newInstanceWithoutConstructor();
        $context = ['completed_paid_quotes' => [
            ['quote_id' => 'Q-DELIVERED', 'status' => 'approved', 'order_number' => '161663243', 'order_status' => 'delivered'],
            ['quote_id' => 'Q-OTHER', 'status' => 'approved', 'order_status' => null],
        ]];
        foreach (['do we have any that was delivered and what was the quote number', 'which one has the status marked as delivered'] as $question) {
            $controllerReflection = new ReflectionClass(MessageController::class);
            $intent = $controllerReflection->getMethod('resolveSmartIntent')->invoke(
                $controllerReflection->newInstanceWithoutConstructor(), $question, [],
                [['role' => 'assistant', 'intent' => 'quote_management', 'content' => 'Here are your quotes.']]
            );
            $this->assertSame('quote_management', $intent);
            $result = $reflection->getMethod('runQuoteAgent')->invoke($service, $question, $context, []);
            $this->assertStringContainsString('Q-DELIVERED', $result['reply']);
            $this->assertStringContainsString('161663243', $result['reply']);
            $this->assertStringNotContainsString('Q-OTHER', $result['reply']);
        }
        $result = $reflection->getMethod('runQuoteAgent')->invoke($service, 'any rejected quotes?', $context, []);
        $this->assertStringContainsString('matching **rejected**', $result['reply']);
        $this->assertStringNotContainsString('Q-OTHER', $result['reply']);
    }

    public function test_cart_and_quote_requests_keep_quantity_and_require_an_unambiguous_product(): void
    {
        $reflection = new ReflectionClass(MessageController::class);
        $controller = $reflection->newInstanceWithoutConstructor();
        $method = $reflection->getMethod('resolveProductCartOperation');
        $context = [
            'product_suggestions' => [['product_id' => '15359167', 'name' => 'Lenovo ThinkPad', 'price' => 1125]],
            'recent_chat_turns' => [
                ['role' => 'user', 'content' => 'add 5 laptops to cart'],
                ['role' => 'user', 'content' => 'i need lenovo laptops please'],
            ],
        ];
        $result = $method->invoke($controller, 'add it to the cart', $context);
        $this->assertSame('add_to_cart', $result['type']);
        $this->assertSame(5, $result['items'][0]['quantity']);
        $this->assertSame('15359167', $result['items'][0]['productId']);
        $history = [['role' => 'assistant', 'intent' => 'product_search', 'product_suggestions' => $context['product_suggestions']]];
        $this->assertSame('product_search', $reflection->getMethod('resolveSmartIntent')->invoke($controller, 'add it to the cart', [], $history));
        $this->assertSame('product_search', $reflection->getMethod('resolveSmartIntent')->invoke($controller, 'create a quote for it then', [], $history));
        $quote = $method->invoke($controller, 'create a quote for it then', $context);
        $this->assertSame('prepare_quote', $quote['type']);
        $this->assertSame(5, $quote['items'][0]['quantity']);
        $this->assertSame(2, $method->invoke($controller, 'add 2 to cart', $context)['items'][0]['quantity']);
        $this->assertNull($method->invoke($controller, 'do not add it to cart', $context));
        $this->assertNull($method->invoke($controller, 'how do I add it to cart?', $context));
        $context['product_suggestions'][] = ['product_id' => '2', 'name' => 'Another laptop'];
        $this->assertSame([], $method->invoke($controller, 'add it to cart', $context)['items']);
    }

    public function test_name_and_quantity_preferences_survive_the_rolling_history_window(): void
    {
        $reflection = new ReflectionClass(MessageController::class);
        $controller = $reflection->newInstanceWithoutConstructor();
        $name = $reflection->getMethod('resolveNamePreference');
        $this->assertTrue($name->invoke($controller, 'address me by my name always', []));
        $history = [['role' => 'assistant', 'address_by_name' => true, 'requested_quantity' => 5]];
        $this->assertTrue($name->invoke($controller, 'check my quotes', $history));
        $this->assertFalse($name->invoke($controller, 'stop using my name', $history));
        $this->assertSame(5, $reflection->getMethod('resolveRequestedQuantity')->invoke($controller, 'add it to cart', $history));
        $reply = $reflection->getMethod('personalizeAssistantReply')->invoke($controller, 'One quote was delivered.', [
            'customer' => ['name' => 'Malvine Owuor'], 'address_by_name' => true,
        ]);
        $this->assertStringStartsWith('Malvine Owuor,', $reply);
    }

    public function test_usb_port_expanders_are_not_laptops(): void
    {
        $reflection = new ReflectionClass(MessageController::class);
        $this->assertTrue($reflection->getMethod('isAccessoryLikeProduct')->invoke($reflection->newInstanceWithoutConstructor(), [
            'name' => 'ADD ONE USB TYPE-C AND THREE USB TYPE-A PORTS (5GBPS) TO YOUR LAPTOP -USB MULTIP',
        ]));
    }

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

    public function test_product_requirements_are_extracted_from_common_spec_formats(): void
    {
        $reflection = new ReflectionClass(MessageController::class);
        $controller = $reflection->newInstanceWithoutConstructor();
        $method = $reflection->getMethod('buildProductSearchContext');

        $wordWarranty = $method->invoke(
            $controller,
            'Laptop under $1500 with 32 GB RAM, USB-C, and three-year warranty',
            []
        );
        $numericWarranty = $method->invoke(
            $controller,
            'Notebook below $1500 with 32GB memory, USB C, and 3 year warranty',
            []
        );

        $this->assertSame(1500.0, $wordWarranty['max_budget']);
        $this->assertSame(['32 GB RAM', 'USB-C', '3-year warranty'], array_column($wordWarranty['required_specs'], 'label'));
        $this->assertSame(['32 GB RAM', 'USB-C', '3-year warranty'], array_column($numericWarranty['required_specs'], 'label'));
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

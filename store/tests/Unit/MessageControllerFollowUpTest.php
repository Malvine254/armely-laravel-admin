<?php

namespace Tests\Unit;

use App\Http\Controllers\MessageController;
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
}

<?php

namespace Tests\Unit;

use App\Services\AzureOpenAiChatService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AzureOpenAiChatServiceTest extends TestCase
{
    public function test_the_offline_conversation_fallback_does_not_leak_account_or_product_data(): void
    {
        $reflection = new ReflectionClass(AzureOpenAiChatService::class);
        $service = $reflection->newInstanceWithoutConstructor();
        $method = $reflection->getMethod('buildCasualConversationReply');

        $reply = $method->invoke($service, 'I like your vibe', 'Mela');

        $this->assertSame('Thank you — I like your vibe too. What are we working on today?', $reply);
        $this->assertStringNotContainsString('product', strtolower($reply));
        $this->assertStringNotContainsString('account', strtolower($reply));
    }
}

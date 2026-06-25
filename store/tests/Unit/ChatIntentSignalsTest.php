<?php

namespace Tests\Unit;

use App\Support\ChatIntentSignals;
use PHPUnit\Framework\TestCase;

class ChatIntentSignalsTest extends TestCase
{
    public function test_it_identifies_greetings_and_thanks(): void
    {
        $this->assertTrue(ChatIntentSignals::isGeneralConversationQuery('hey'));
        $this->assertTrue(ChatIntentSignals::isGeneralConversationQuery('hi buddy'));
        $this->assertTrue(ChatIntentSignals::isGeneralConversationQuery('thank you'));
    }

    public function test_it_identifies_capability_questions_without_overrouting(): void
    {
        $this->assertTrue(ChatIntentSignals::isCapabilityQuestion('what can you do'));
        $this->assertTrue(ChatIntentSignals::isCapabilityQuestion('what are your roles'));
        $this->assertFalse(ChatIntentSignals::isProductLookupIntent('what can you do'));
    }

    public function test_it_identifies_product_search_queries(): void
    {
        $this->assertTrue(ChatIntentSignals::isProductLookupIntent('search for me meraki'));
        $this->assertTrue(ChatIntentSignals::isProductLookupIntent('check for hp monitors between 200 and 500 dollars'));
    }

    public function test_it_keeps_account_queries_separate_from_product_search(): void
    {
        $this->assertTrue(ChatIntentSignals::isQuoteIntentQuery('check my last 3 quotes'));
        $this->assertFalse(ChatIntentSignals::isProductLookupIntent('check my last 3 quotes'));
    }
}

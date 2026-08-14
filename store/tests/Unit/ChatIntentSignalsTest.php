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
        $this->assertTrue(ChatIntentSignals::isSmallTalkQuery('how are you'));
        $this->assertTrue(ChatIntentSignals::isSmallTalkQuery('what is your name'));
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
        $this->assertFalse(ChatIntentSignals::isProductLookupIntent('how are you'));
        $this->assertFalse(ChatIntentSignals::isProductLookupIntent('what is your name'));
        $this->assertFalse(ChatIntentSignals::isProductLookupIntent('tell me a joke'));
    }

    public function test_it_classifies_intent_using_a_single_authority(): void
    {
        $this->assertSame('general_support', ChatIntentSignals::classifyAssistantIntent('how are you'));
        $this->assertSame('invoice_payment', ChatIntentSignals::classifyAssistantIntent('show my unpaid invoices'));
        $this->assertSame('quote_management', ChatIntentSignals::classifyAssistantIntent('show my last 2 quotes'));
        $this->assertSame('product_search', ChatIntentSignals::classifyAssistantIntent('find me cisco switches'));
        $this->assertSame('general_support', ChatIntentSignals::classifyAssistantIntent('check my quotes and orders'));
        $this->assertSame('invoice_payment', ChatIntentSignals::classifyAssistantIntent('how much is due for the order/quotes'));
    }

    public function test_it_detects_due_amount_questions(): void
    {
        $this->assertTrue(ChatIntentSignals::isDueAmountQuestion('How much is due for my account?'));
        $this->assertTrue(ChatIntentSignals::isDueAmountQuestion('What do I owe right now?'));
        $this->assertFalse(ChatIntentSignals::isDueAmountQuestion('show my latest quotes'));
    }

    public function test_it_keeps_account_queries_separate_from_product_search(): void
    {
        $this->assertTrue(ChatIntentSignals::isQuoteIntentQuery('check my last 3 quotes'));
        $this->assertFalse(ChatIntentSignals::isProductLookupIntent('check my last 3 quotes'));
    }

    public function test_it_extracts_the_requested_catalog_phrase_without_command_words(): void
    {
        $this->assertSame(
            'SAMSUNG SERVICE MODULE JIG FOR IER SERIES',
            ChatIntentSignals::extractCatalogSearchPhrase('search for me SAMSUNG SERVICE MODULE JIG FOR IER SERIES')
        );
    }

    public function test_query_audit_is_not_treated_as_a_new_product_keyword_search(): void
    {
        $this->assertTrue(ChatIntentSignals::isCatalogQueryAudit('which query did you use'));
        $this->assertSame([], ChatIntentSignals::extractProductSearchKeywords('which query did you use'));
    }

    public function test_it_reduces_a_conversational_recommendation_to_the_catalog_term(): void
    {
        $question = 'i need monitors which one do you recommend';

        $this->assertSame('monitor', ChatIntentSignals::extractCatalogSearchPhrase($question));
        $this->assertSame(['monitor'], ChatIntentSignals::extractProductSearchKeywords(
            ChatIntentSignals::extractCatalogSearchPhrase($question)
        ));
    }

    public function test_it_splits_multiple_product_types_into_independent_searches(): void
    {
        $this->assertSame(
            ['monitor', 'printer'],
            ChatIntentSignals::extractCatalogSearchPhrases('I need monitors and printers which one do you recommend')
        );

        $this->assertSame(
            ['monitor with HDMI and USB-C'],
            ChatIntentSignals::extractCatalogSearchPhrases('monitor with HDMI and USB-C')
        );

        $this->assertSame(
            ['Samsung monitor', 'LG monitors'],
            ChatIntentSignals::extractCatalogSearchPhrases('compare Samsung and LG monitors')
        );
    }

    public function test_it_removes_conversational_inventory_language_from_searches(): void
    {
        $this->assertSame(
            ['HP laptop', 'Dell laptops'],
            ChatIntentSignals::extractCatalogSearchPhrases('search for me the HP and Dell laptops we have')
        );

        $this->assertSame(
            ['dell', 'laptop', 'notebook'],
            ChatIntentSignals::extractProductSearchKeywords('the Dell laptops we have')
        );
    }

    public function test_recommendation_follow_up_does_not_become_a_new_catalog_query(): void
    {
        $this->assertSame('', ChatIntentSignals::extractCatalogSearchPhrase('which one do you recommend'));
        $this->assertSame([], ChatIntentSignals::extractCatalogSearchPhrases('which one do you recommend'));
    }

    public function test_it_understands_natural_search_corrections_and_product_word_order(): void
    {
        $this->assertSame('cisco', ChatIntentSignals::extractCatalogSearchPhrase('search cisco instead'));
        $this->assertSame(['cisco'], ChatIntentSignals::extractProductSearchKeywords('search cisco instead'));

        $this->assertSame('printers hp', ChatIntentSignals::extractCatalogSearchPhrase('i need printers hp ones'));
        $this->assertSame(['printer', 'hp'], ChatIntentSignals::extractProductSearchKeywords('i need printers hp ones'));
    }

    public function test_arbitrary_catalog_nouns_are_valid_search_terms(): void
    {
        $this->assertSame(['camera'], ChatIntentSignals::extractProductSearchKeywords('i need some camera'));
        $this->assertSame(['sony'], ChatIntentSignals::extractProductSearchKeywords('sony?'));
        $this->assertSame(['phone'], ChatIntentSignals::extractProductSearchKeywords('now do for phones'));
    }

    public function test_refinement_filler_does_not_pollute_the_search(): void
    {
        $this->assertSame('Sony', ChatIntentSignals::extractCatalogSearchPhrase('Show me Sony options instead'));
        $this->assertSame(['sony'], ChatIntentSignals::extractProductSearchKeywords('Show me Sony options instead'));
    }

    public function test_account_requests_cannot_become_product_searches(): void
    {
        $question = 'my quotes and pending invoices';

        $this->assertTrue(ChatIntentSignals::isQuoteIntentQuery($question));
        $this->assertTrue(ChatIntentSignals::isInvoiceIntentQuery($question));
        $this->assertFalse(ChatIntentSignals::isProductLookupIntent($question));
    }

    public function test_exclusions_are_separated_from_positive_search_terms(): void
    {
        $question = 'Find a wide-angle camera for a conference room. Don’t show laptop cameras, cables, mounts, or accessories.';

        $this->assertStringNotContainsString('laptop', strtolower(ChatIntentSignals::extractCatalogSearchPhrase($question)));
        $this->assertSame(
            ['laptop', 'camera', 'cable', 'mount', 'notebook'],
            ChatIntentSignals::extractExcludedProductTerms($question)
        );
    }
}

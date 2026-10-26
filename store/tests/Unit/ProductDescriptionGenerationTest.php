<?php

namespace Tests\Unit;

use App\Services\AzureOpenAiChatService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductDescriptionGenerationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.azure_openai' => [
            'endpoint' => 'https://description.example.com',
            'api_key' => 'test-key',
            'deployment' => 'test-model',
            'api_version' => '2024-10-21',
        ]]);
        Http::preventStrayRequests();
    }

    public function test_generates_plain_text_using_supplied_facts_and_json_output(): void
    {
        $description = 'HP ZBook 8 workstation, manufacturer part ABC123, with 32 GB memory.';
        Http::fakeSequence('description.example.com/*')->push([
            'choices' => [['message' => ['content' => json_encode(['description' => $description])]]],
        ])->push(['choices' => [['message' => ['content' => '{"supported":true}']]]]);
        $facts = ['product_name' => 'HP ZBook 8', 'manufacturer' => 'HP', 'part_number' => 'ABC123',
            'verified_specifications' => [['name' => 'Memory', 'value' => '32 GB']], 'allow_title_fallback' => true];
        $this->assertSame($description, app(AzureOpenAiChatService::class)->generateProductDescription($facts));
        Http::assertSent(fn ($request) => $request['response_format']['type'] === 'json_object'
            && json_decode($request['messages'][1]['content'], true) === $facts
            && str_contains($request['messages'][0]['content'], 'Do not infer specifications')
            && str_contains($request['messages'][0]['content'], 'Format the plain-text description as a short overview paragraph')
            && str_contains($request['messages'][0]['content'], 'Website evidence is available'));
    }

    public function test_invalid_empty_html_overlong_and_title_only_results_are_rejected(): void
    {
        $contents = ['not JSON', '{}', '{"description":42}', '{"description":""}',
            '{"description":"<p>Untrusted HTML description</p>"}',
            json_encode(['description' => str_repeat('x', 4001)]),
            '{"description":"HP ZBook 8 workstation"}'];
        $responses = Http::fakeSequence('description.example.com/*');
        foreach ($contents as $content) {
            $responses->push([
                'choices' => [['message' => ['content' => $content]]],
            ]);
        }
        foreach ($contents as $content) {
            $this->assertNull(app(AzureOpenAiChatService::class)->generateProductDescription([
                'product_name' => 'HP ZBook 8 workstation', 'verified_website_description' => 'HP workstation with 32 GB memory.',
            ]));
        }
    }

    public function test_ram_component_generation_requests_relevant_labels_and_quality_check_without_inventing_computer_specs(): void
    {
        $description = "Total Micro memory upgrade module.\n\n- RAM: 16 GB DDR4\n- Memory speed: 3200 MHz\n- Form factor: SODIMM\n- Voltage: 1.2 V";
        Http::fakeSequence('description.example.com/*')
            ->push(['choices' => [['message' => ['content' => json_encode(['description' => $description])]]]])
            ->push(['choices' => [['message' => ['content' => '{"supported":true}']]]]);
        $this->assertSame($description, app(AzureOpenAiChatService::class)->generateProductDescription([
            'product_name' => 'Total Micro 16GB DDR4 3200MHz 1.2V SODIMM',
            'allow_title_fallback' => true,
        ]));
        Http::assertSent(fn ($request) => str_contains($request['messages'][0]['content'], 'For a RAM module, use RAM capacity')
            && str_contains($request['messages'][0]['content'], 'Call SSD/HDD capacity Storage, not ROM')
            && str_contains($request['messages'][0]['content'], 'Skip unknown fields'));
        Http::assertSent(fn ($request) => str_contains($request['messages'][0]['content'], 'Also reject confusing or low-quality descriptions')
            && str_contains($request['messages'][0]['content'], 'host-device specifications attributed to an accessory'));
        Http::assertSentCount(2);
    }

    public function test_provider_failure_and_missing_configuration_do_not_generate_content(): void
    {
        Http::fake(['description.example.com/*' => Http::response(['error' => 'unavailable'], 503)]);
        $this->assertNull(app(AzureOpenAiChatService::class)->generateProductDescription(['verified_website_description' => 'HP workstation.']));
        config(['services.azure_openai.deployment' => '']);
        Http::fake();
        $this->assertNull(app(AzureOpenAiChatService::class)->generateProductDescription([]));
        Http::assertNothingSent();
    }

    public function test_catalog_identity_without_website_evidence_does_not_trigger_generation(): void
    {
        Http::fake();
        $this->assertNull(app(AzureOpenAiChatService::class)->generateProductDescription([
            'product_name' => 'HP E-Series E24u G5', 'manufacturer' => 'HP', 'part_number' => '9VH73AA',
        ]));
        Http::assertNothingSent();
    }

    public function test_explicit_title_fallback_generates_and_checks_only_title_attributes_and_category_knowledge(): void
    {
        $description = 'The HP ZBook workstation is a computer for working with desktop applications. Select the configuration and accessories that suit your requirements.';
        Http::fakeSequence('description.example.com/*')
            ->push(['choices' => [['message' => ['content' => json_encode(['description' => $description])]]]])
            ->push(['choices' => [['message' => ['content' => '{"supported":true}']]]]);
        $this->assertSame($description, app(AzureOpenAiChatService::class)->generateProductDescription([
            'product_name' => 'HP ZBook workstation', 'manufacturer' => 'HP', 'allow_title_fallback' => true,
        ]));
        Http::assertSent(fn ($request) => str_contains($request['messages'][0]['content'], 'No website evidence is available')
            && str_contains($request['messages'][0]['content'], 'Do not recall model-specific facts from memory'));
        Http::assertSent(fn ($request) => str_contains($request['messages'][0]['content'], 'For this title-based fallback'));
        Http::assertSentCount(2);
    }

    public function test_title_fallback_still_rejects_unsupported_claims_and_missing_titles(): void
    {
        Http::fakeSequence('description.example.com/*')
            ->push(['choices' => [['message' => ['content' => '{"description":"HP ZBook workstation includes 128 GB of memory and a five-year warranty."}']]]])
            ->push(['choices' => [['message' => ['content' => '{"supported":false}']]]]);
        $this->assertNull(app(AzureOpenAiChatService::class)->generateProductDescription([
            'product_name' => 'HP ZBook workstation', 'allow_title_fallback' => true,
        ]));
        Http::assertSentCount(2);
        Http::fake();
        $this->assertNull(app(AzureOpenAiChatService::class)->generateProductDescription([
            'product_name' => ' ', 'allow_title_fallback' => true,
        ]));
        Http::assertNothingSent();
    }

    public function test_generated_claims_rejected_by_fact_check_are_not_published(): void
    {
        $responses = Http::fakeSequence('description.example.com/*');
        foreach (['{"supported":false}', '{"supported":"true"}', 'invalid', '{}'] as $verdict) {
            $responses->push(['choices' => [['message' => ['content' => json_encode([
                'description' => 'HP ZBook workstation includes an unsupported five-year warranty.',
            ])]]]]);
            $responses->push(['choices' => [['message' => ['content' => $verdict]]]]);
        }
        for ($i = 0; $i < 4; $i++) {
            $this->assertNull(app(AzureOpenAiChatService::class)->generateProductDescription([
                'product_name' => 'HP ZBook 8', 'verified_website_description' => 'HP ZBook 8 workstation.',
            ]));
        }
    }
}

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
        Http::fake(['description.example.com/*' => Http::response([
            'choices' => [['message' => ['content' => json_encode(['description' => $description])]]],
        ])]);
        $facts = ['product_name' => 'HP ZBook 8', 'manufacturer' => 'HP', 'part_number' => 'ABC123',
            'verified_specifications' => [['name' => 'Memory', 'value' => '32 GB']]];
        $this->assertSame($description, app(AzureOpenAiChatService::class)->generateProductDescription($facts));
        Http::assertSent(fn ($request) => $request['response_format']['type'] === 'json_object'
            && json_decode($request['messages'][1]['content'], true) === $facts
            && str_contains($request['messages'][0]['content'], 'Do not infer specifications'));
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
            $this->assertNull(app(AzureOpenAiChatService::class)->generateProductDescription(['product_name' => 'HP ZBook 8 workstation']));
        }
    }

    public function test_provider_failure_and_missing_configuration_do_not_generate_content(): void
    {
        Http::fake(['description.example.com/*' => Http::response(['error' => 'unavailable'], 503)]);
        $this->assertNull(app(AzureOpenAiChatService::class)->generateProductDescription([]));
        config(['services.azure_openai.deployment' => '']);
        Http::fake();
        $this->assertNull(app(AzureOpenAiChatService::class)->generateProductDescription([]));
        Http::assertNothingSent();
    }
}

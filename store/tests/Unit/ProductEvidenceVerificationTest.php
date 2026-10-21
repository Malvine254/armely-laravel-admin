<?php

namespace Tests\Unit;

use App\Services\AzureOpenAiChatService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductEvidenceVerificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.azure_openai' => [
            'endpoint' => 'https://verification.example.com',
            'api_key' => 'test-key',
            'deployment' => 'test-vision',
            'api_version' => '2024-10-21',
        ]]);
        Http::preventStrayRequests();
    }

    private function details(): array
    {
        return [
            'description' => 'HP ZBook 8 workstation.',
            'specifications' => [['name' => 'Memory', 'value' => '32 GB']],
            'sources' => [['name' => 'Manufacturer', 'url' => 'https://hp.com/zbook']],
            'image_candidates' => [[
                'image_url' => 'https://images.example.com/zbook.jpg',
                'title' => 'HP ZBook 8',
                'source_url' => 'https://hp.com/zbook',
            ]],
        ];
    }

    private function fakeResult(array $result): void
    {
        Http::fake(['verification.example.com/*' => Http::response([
            'choices' => [['message' => ['content' => json_encode($result)]]],
        ])]);
    }

    public function test_verification_sends_actual_images_and_only_returns_indices_into_existing_evidence(): void
    {
        $this->fakeResult(['description_matches' => true, 'specification_indices' => [0, 0], 'image_index' => 0, 'image_confidence' => 0.95]);
        $result = app(AzureOpenAiChatService::class)->verifyProductEvidence(['productName' => 'HP ZBook 8'], $this->details());
        $this->assertSame(['description_matches' => true, 'specification_indices' => [0], 'image_index' => 0], $result);
        Http::assertSent(fn ($request) => $request['response_format']['type'] === 'json_object'
            && $request['messages'][1]['content'][2]['type'] === 'image_url'
            && $request['messages'][1]['content'][2]['image_url']['url'] === 'https://images.example.com/zbook.jpg');
    }

    public function test_images_below_the_exact_confidence_threshold_are_rejected(): void
    {
        $this->fakeResult(['description_matches' => false, 'specification_indices' => [], 'image_index' => 0, 'image_confidence' => 0.949]);
        $result = app(AzureOpenAiChatService::class)->verifyProductEvidence([], $this->details());
        $this->assertSame(['description_matches' => false, 'specification_indices' => [], 'image_index' => null], $result);
    }

    public function test_out_of_bounds_indices_and_invalid_types_or_confidence_are_rejected(): void
    {
        $valid = ['description_matches' => true, 'specification_indices' => [0], 'image_index' => 0, 'image_confidence' => 0.99];
        foreach ([
            ['specification_indices' => [1]], ['specification_indices' => ['0']],
            ['image_index' => 1], ['image_index' => -1], ['description_matches' => 'true'],
            ['image_confidence' => 1.01], ['image_confidence' => -0.1],
        ] as $invalid) {
            $this->fakeResult([...$valid, ...$invalid]);
            $this->assertNull(app(AzureOpenAiChatService::class)->verifyProductEvidence([], $this->details()));
        }
    }

    public function test_no_image_is_selected_when_the_model_cannot_verify_identity(): void
    {
        $this->fakeResult(['description_matches' => false, 'specification_indices' => [], 'image_index' => null, 'image_confidence' => 0]);
        $this->assertSame(['description_matches' => false, 'specification_indices' => [], 'image_index' => null],
            app(AzureOpenAiChatService::class)->verifyProductEvidence([], $this->details()));
    }

    public function test_malformed_responses_provider_failure_and_missing_configuration_fail_closed(): void
    {
        Http::fake(['verification.example.com/*' => Http::response(['choices' => [['message' => ['content' => 'not JSON']]]])]);
        $this->assertNull(app(AzureOpenAiChatService::class)->verifyProductEvidence([], $this->details()));
        Http::fake(['verification.example.com/*' => Http::response(['error' => 'unavailable'], 503)]);
        $this->assertNull(app(AzureOpenAiChatService::class)->verifyProductEvidence([], $this->details()));
        config(['services.azure_openai.deployment' => '']);
        Http::fake();
        $this->assertNull(app(AzureOpenAiChatService::class)->verifyProductEvidence([], $this->details()));
        Http::assertNothingSent();
    }
}

<?php

namespace Tests\Unit;

use App\Services\BingProductSearchService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BingProductSearchServiceTest extends TestCase
{
    public function test_parses_web_results_with_encoded_destination_and_unicode(): void
    {
        $url = 'https://www.bing.com/ck/a?u=a1'.rtrim(strtr(base64_encode('https://hp.com/9VH73AA'), '+/', '-_'), '=');
        $html = '<li class="b_algo extra"><h2><a href="'.$url.'">HP E24u G5</a></h2><p>Screen: 24 inches; Connection: USB-C &amp; DisplayPort</p></li>';
        $rows = app(BingProductSearchService::class)->parse($html, false);
        $this->assertSame('https://hp.com/9VH73AA', $rows[0]['link']);
        $this->assertSame('Screen: 24 inches; Connection: USB-C & DisplayPort', $rows[0]['snippet']);
    }

    public function test_image_candidates_keep_original_image_and_source_page_for_ai_verification(): void
    {
        $metadata = htmlspecialchars(json_encode(['t' => 'HP E24u G5', 'murl' => 'https://hp.com/image.jpg',
            'purl' => 'https://hp.com/9VH73AA']), ENT_QUOTES);
        $rows = app(BingProductSearchService::class)->parse('<a class="iusc" m="'.$metadata.'"></a>', true);
        $this->assertSame([['title' => 'HP E24u G5', 'link' => 'https://hp.com/9VH73AA',
            'original' => 'https://hp.com/image.jpg']], $rows);
    }

    public function test_direct_search_uses_bing_without_api_keys_and_surfaces_blocked_searches(): void
    {
        Http::preventStrayRequests();
        Http::fake(['www.bing.com/*' => Http::response('<div id="b_captcha">Verify you are human</div>')]);
        $this->expectException(\UnexpectedValueException::class);
        try {
            app(BingProductSearchService::class)->search('HP 9VH73AA');
        } finally {
            Http::assertSent(fn ($request) => str_contains($request->url(), 'www.bing.com/search')
                && $request['q'] === 'HP 9VH73AA' && ! array_key_exists('api_key', $request->data()));
        }
    }
}

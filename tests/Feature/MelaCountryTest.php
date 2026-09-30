<?php

namespace Tests\Feature;

use App\Services\Mela\Memory\VisitorCountry;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MelaCountryTest extends TestCase
{
    private function resolver(): VisitorCountry
    {
        return new class extends VisitorCountry {
            protected function localCountry(string $ip): ?string { return null; }
        };
    }

    public function test_api_country_is_normalized_and_cached(): void
    {
        Http::fake(['ipwho.is/*' => Http::response(['success' => true, 'country_code' => 'ke'])]);
        $this->assertSame('KE', $this->resolver()->resolve('8.8.8.8'));
        $this->assertSame('KE', $this->resolver()->resolve('8.8.8.8'));
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request['fields'] === 'success,country_code');
    }

    public function test_failed_api_is_cached_and_private_ips_are_not_sent(): void
    {
        Http::fake(['*' => Http::response(['success' => false], 429)]);
        $resolver = $this->resolver();
        foreach (['127.0.0.1', '10.0.0.1', '::1', 'invalid', null] as $ip) {
            $this->assertNull($resolver->resolve($ip));
        }
        Http::assertNothingSent();
        $this->assertNull($resolver->resolve('8.8.4.4'));
        $this->assertNull($resolver->resolve('8.8.4.4'));
        Http::assertSentCount(1);
    }

    public function test_api_can_be_disabled_and_invalid_country_is_rejected(): void
    {
        Http::fake(['*' => Http::response(['success' => true, 'country_code' => ['US']])]);
        config(['mela.geoip.api_enabled' => false]);
        $this->assertNull($this->resolver()->resolve('1.1.1.1'));
        Http::assertNothingSent();
        config(['mela.geoip.api_enabled' => true]);
        $this->assertNull($this->resolver()->resolve('8.8.8.8'));
        Http::assertSentCount(1);
    }
}

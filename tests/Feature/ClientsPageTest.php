<?php

namespace Tests\Feature;

use App\Http\Middleware\LogActivity;
use Tests\TestCase;

class ClientsPageTest extends TestCase
{
    public function test_clients_page_renders(): void
    {
        $this->withoutMiddleware(LogActivity::class);

        $this->get(route('clients.index'))
            ->assertOk()
            ->assertViewIs('clients.index')
            ->assertSee('href="'.asset('css/mela-chat.css').'?v='.filemtime(public_path('css/mela-chat.css')).'"', false)
            ->assertSee('<title>Our Clients | Armely</title>', false)
            ->assertSee('Sage Butte Energy');
    }

    public function test_homepage_hero_links_to_the_clients_page(): void
    {
        $this->assertSame('/clients', route('clients.index', [], false));

        $html = view('partials.home-hero-1000', ['industryListings' => []])->render();

        $this->assertStringContainsString('href="'.route('clients.index').'"', $html);
    }
}

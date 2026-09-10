@extends('layouts.public')

@section('title', 'Our Clients | Armely')
@section('meta_description', 'Explore organizations across enterprise, public sector, healthcare, energy, finance, legal, and education that have worked with Armely.')
@section('canonical_url', route('clients.index'))
@section('robots', 'index,follow')

@php
    use Illuminate\Support\Str;

    $logo = fn (string $filename, string $directory = 'brand-partners') => is_file(public_path('images/' . $directory . '/' . $filename))
        ? asset('images/' . $directory . '/' . $filename)
        : null;

    $clientSectors = [
        'Enterprise & Logistics' => [
            ['name' => 'Delta Air Lines', 'logo' => $logo('delta-air-lines.png')],
            ['name' => 'BNSF Railway', 'logo' => $logo('bnsf.png')],
            ['name' => 'Colonial Pipeline', 'logo' => $logo('colonial-pipeline.png')],
            ['name' => 'DH Pace', 'logo' => $logo('dh-pace.png')],
        ],
        'Public Sector & Municipal' => [
            ['name' => 'City of San Diego', 'logo' => $logo('city-of-san-diego.png')],
            ['name' => 'Dallas County', 'logo' => $logo('dallas_county.jpg')],
            ['name' => 'City of Frisco', 'logo' => $logo('frisco.jpeg')],
            ['name' => 'City of Topeka', 'logo' => $logo('city-of-topeka.png')],
        ],
        'Healthcare & Life Sciences' => [
            ['name' => 'UNMC', 'logo' => $logo('university_of_nebrask1.png')],
            ['name' => 'Esse Health', 'logo' => $logo('esse_health.jpg')],
            ['name' => 'Kettering Health', 'logo' => $logo('kettering-health.png')],
            ['name' => 'Swope Health', 'logo' => $logo('swope_health.png')],
            ['name' => 'MHC', 'logo' => $logo('mhc.png')],
        ],
        'Energy' => [
            ['name' => 'Sage Butte Energy', 'logo' => $logo('sage_bute.webp')],
            ['name' => 'QB Energy', 'logo' => $logo('qb_energy.jpg')],
        ],
        'Finance, Legal & Education' => [
            ['name' => 'Bank OZK', 'logo' => $logo('bank-ozk.png')],
            ['name' => 'Plano ISD', 'logo' => $logo('Plano.png', 'partners')],
            ['name' => 'UT Dallas', 'logo' => $logo('ut-dallas.png')],
            ['name' => 'Lambda Legal', 'logo' => $logo('lambda.png')],
        ],
        'Nonprofit & Social Services' => [
            ['name' => 'Homeward Bound', 'logo' => $logo('homeward_bound.png')],
        ],
    ];
@endphp

@push('styles')
<style>
    .clients-page,
    .clients-page * { box-sizing: border-box; }
    .clients-page { background: #f7f9fd; color: #172b4d; }
    .clients-hero { padding: 150px 24px 70px; background: #10213b; color: #ffffff; }
    .clients-shell { width: min(1120px, calc(100% - 48px)); margin: 0 auto; }
    .clients-eyebrow { margin-bottom: 16px; color: #8eb6ee; font-size: 0.78rem; font-weight: 700; letter-spacing: 0; text-transform: uppercase; }
    .clients-hero h1 { max-width: 760px; margin: 0; font-size: clamp(2.2rem, 5vw, 4.4rem); line-height: 1.02; letter-spacing: 0; }
    .clients-hero p { max-width: 680px; margin: 22px 0 0; color: #ced9e9; font-size: 1.05rem; line-height: 1.7; }
    .clients-directory { padding: 72px 0 88px; }
    .clients-sector + .clients-sector { margin-top: 56px; }
    .clients-sector-head { display: flex; align-items: baseline; justify-content: space-between; gap: 24px; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid #d7e0ed; }
    .clients-sector h2 { margin: 0; color: #203a63; font-size: 1.25rem; line-height: 1.3; letter-spacing: 0; }
    .clients-count { color: #72809a; font-size: 0.88rem; }
    .clients-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
    .clients-card { min-width: 0; padding: 14px; background: #ffffff; border: 1px solid #dfe6f0; border-radius: 8px; box-shadow: 0 8px 20px rgba(31, 53, 96, 0.05); }
    .clients-logo { min-height: 112px; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 14px; border: 1px solid #e5eaf2; border-radius: 6px; background: #ffffff; }
    .clients-logo img { display: block; max-width: 100%; max-height: 80px; object-fit: contain; }
    .clients-logo-fallback { color: #203a63; font-size: 1rem; font-weight: 800; line-height: 1.2; text-align: center; }
    .clients-name { margin: 13px 0 2px; color: #172b4d; font-size: 0.95rem; font-weight: 600; line-height: 1.3; text-align: center; }
    .clients-cta { margin-top: 72px; padding: 34px; display: flex; align-items: center; justify-content: space-between; gap: 24px; background: #ffffff; border: 1px solid #dfe6f0; border-radius: 8px; }
    .clients-cta h2 { margin: 0 0 6px; color: #203a63; font-size: 1.35rem; letter-spacing: 0; }
    .clients-cta p { margin: 0; color: #65738c; }
    .clients-cta a { flex: none; padding: 13px 22px; color: #ffffff; font-weight: 700; text-decoration: none; background: #2f5597; border-radius: 6px; }
    @media (max-width: 900px) { .clients-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 600px) {
        .clients-shell { width: min(100%, calc(100% - 28px)); }
        .clients-hero { padding: 116px 0 54px; }
        .clients-directory { padding: 52px 0 64px; }
        .clients-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .clients-card { padding: 10px; }
        .clients-logo { min-height: 92px; padding: 10px; }
        .clients-cta { margin-top: 52px; padding: 24px; align-items: flex-start; flex-direction: column; }
    }
</style>
@endpush

@section('content')
<main class="clients-page">
    <header class="clients-hero">
        <div class="clients-shell">
            <div class="clients-eyebrow">Organizations We Serve</div>
            <h1>Trusted across industries where delivery matters.</h1>
            <p>From public institutions and healthcare organizations to transportation, energy, finance, legal, and education teams.</p>
        </div>
    </header>

    <section class="clients-directory" aria-label="Client directory">
        <div class="clients-shell">
            @foreach($clientSectors as $sector => $clients)
                <section class="clients-sector">
                    <div class="clients-sector-head">
                        <h2>{{ $sector }}</h2>
                        <span class="clients-count">{{ count($clients) }} {{ Str::plural('organization', count($clients)) }}</span>
                    </div>
                    <div class="clients-grid">
                        @foreach($clients as $client)
                            <article class="clients-card">
                                <div class="clients-logo">
                                    @if($client['logo'])
                                        <img src="{{ $client['logo'] }}" alt="{{ $client['name'] }} logo" loading="lazy" decoding="async">
                                    @else
                                        <span class="clients-logo-fallback">{{ $client['name'] }}</span>
                                    @endif
                                </div>
                                <h3 class="clients-name">{{ $client['name'] }}</h3>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach

            <div class="clients-cta">
                <div>
                    <h2>Hear directly from our clients</h2>
                    <p>Read verified feedback from the teams that worked with Armely.</p>
                </div>
                <a href="{{ route('customer-stories.index') }}">What Our Clients Say</a>
            </div>
        </div>
    </section>
</main>
@endsection
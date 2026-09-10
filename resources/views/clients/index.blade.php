@extends('layouts.public')

@section('title', 'Our Clients | Armely')
@section('meta_description', 'Explore organizations across enterprise, public sector, healthcare, energy, finance, legal, and education that have worked with Armely.')
@section('canonical_url', route('clients.index'))
@section('robots', 'index,follow')

@php
    $logo = fn (string $filename, string $directory = 'brand-partners') => is_file(public_path('images/' . $directory . '/' . $filename))
        ? asset('images/' . $directory . '/' . $filename)
        : null;

    $clientSectors = [
        'Enterprise & Logistics' => [
            ['name' => 'Sage Butte Energy', 'logo' => $logo('sage_bute.webp'), 'href' => route('case-studies.index', ['industry' => 'energy-oil-gas'])],
            ['name' => 'BNSF Railway', 'logo' => $logo('bnsf.png'), 'href' => route('case-studies.index', ['industry' => 'transportation-logistics'])],
            ['name' => 'Colonial Pipeline', 'logo' => $logo('ColonialPipeline.webp'), 'href' => route('case-studies.index', ['industry' => 'energy-oil-gas'])],
            ['name' => 'DH Pace', 'logo' => $logo('dh-pace-logo.png'), 'logo_scale' => 2.2, 'href' => route('case-studies.index', ['industry' => 'transportation-logistics'])],
        ],
        'Public Sector & Municipal' => [
            ['name' => 'City of San Diego', 'logo' => $logo('City of San Diego.png'), 'href' => route('case-studies.index', ['industry' => 'government-public-sector'])],
            ['name' => 'Dallas County', 'logo' => $logo('dallas_county.jpg'), 'href' => route('case-studies.index', ['industry' => 'government-public-sector'])],
            ['name' => 'City of Frisco', 'logo' => $logo('frisco.jpeg'), 'href' => route('case-studies.index', ['industry' => 'government-public-sector'])],
            ['name' => 'City of Topeka', 'logo' => $logo('City of Topeka.png'), 'href' => route('case-studies.index', ['industry' => 'government-public-sector'])],
        ],
        'Healthcare & Life Sciences' => [
            ['name' => 'UNMC', 'logo' => $logo('university_of_nebrask1.png'), 'href' => route('case-studies.index', ['industry' => 'healthcare'])],
            ['name' => 'Esse Health', 'logo' => $logo('esseHealth.png'), 'logo_scale' => 1.4, 'href' => route('case-studies.index', ['industry' => 'healthcare'])],
            ['name' => 'American Medical Staffing', 'logo' => $logo('ams.svg'), 'href' => route('case-studies.index', ['industry' => 'healthcare'])],
            ['name' => 'Swope Health', 'logo' => $logo('swope_health.png'), 'href' => route('case-studies.index', ['industry' => 'healthcare'])],
            ['name' => 'MHC', 'logo' => $logo('mhc.png'), 'href' => route('case-studies.index', ['industry' => 'transportation-logistics'])],
        ],
        'Energy' => [
            ['name' => 'QB Energy', 'logo' => $logo('qb_energy.jpg'), 'href' => route('case-studies.index', ['industry' => 'energy-oil-gas'])],
        ],
        'Finance, Legal & Education' => [
            ['name' => 'Bank OZK', 'logo' => $logo('Bank_OZK_Logo.png'), 'href' => route('case-studies.index', ['industry' => 'financial-services'])],
            ['name' => 'Plano ISD', 'logo' => $logo('Plano.png', 'partners'), 'href' => route('case-studies.index', ['industry' => 'government-public-sector'])],
            ['name' => 'UT Dallas', 'logo' => $logo('UTDallas.png'), 'logo_scale' => 2, 'href' => route('case-studies.index', ['industry' => 'higher-education'])],
            ['name' => 'Lambda Legal', 'logo' => $logo('lambda.png'), 'href' => route('case-studies.index', ['industry' => 'legal-social-services'])],
        ],
        'Nonprofit & Social Services' => [
            ['name' => 'Homeward Bound', 'logo' => $logo('homeward_bound.png'), 'href' => route('case-studies.index', ['industry' => 'legal-social-services'])],
        ],
    ];

    $clients = array_merge(...array_values($clientSectors));
@endphp

@push('styles')
<style>
    .clients-page,
    .clients-page * { box-sizing: border-box; }
    .clients-page { background: #f7f9fd; color: #172b4d; font-family: Poppins, sans-serif; }
    .clients-hero { padding: 72px 24px 70px; background: #10213b; color: #ffffff; }
    .clients-shell { width: min(1120px, calc(100% - 48px)); margin: 0 auto; }
    .clients-eyebrow { margin-bottom: 16px; color: #8eb6ee; font-size: 0.78rem; font-weight: 700; letter-spacing: 0; text-transform: uppercase; }
    .clients-page .clients-hero h1 { max-width: 760px; margin: 0; color: #ffffff; font-size: clamp(2.2rem, 5vw, 4.4rem); line-height: 1.02; letter-spacing: 0; }
    .clients-hero p { max-width: 680px; margin: 22px 0 0; color: #ced9e9; font-size: 1.05rem; line-height: 1.7; }
    .clients-directory { padding: 72px 0 88px; }
    .clients-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
    .clients-card { min-width: 0; display: flex; flex-direction: column; padding: 14px; color: inherit; text-decoration: none; background: #ffffff; border: 1px solid #dfe6f0; border-radius: 8px; box-shadow: 0 8px 20px rgba(31, 53, 96, 0.05); transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease; }
    .clients-card:hover { transform: translateY(-2px); border-color: rgba(47, 85, 151, 0.2); box-shadow: 0 12px 24px rgba(31, 53, 96, 0.09); }
    .clients-card:focus-visible { outline: 3px solid rgba(47, 85, 151, 0.35); outline-offset: 3px; }
    .clients-logo { height: 112px; min-height: 112px; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 14px; border: 1px solid #e5eaf2; border-radius: 6px; background: #ffffff; }
    .clients-logo img { display: block; max-width: 100%; max-height: 80px; object-fit: contain; }
    .clients-logo-fallback { color: #203a63; font-size: 1rem; font-weight: 800; line-height: 1.2; text-align: center; }
    .clients-name { min-height: 2.6em; margin: 13px 0 2px; display: flex; align-items: center; justify-content: center; color: #172b4d; font-size: 0.95rem; font-weight: 600; line-height: 1.3; text-align: center; }
    .clients-cta { margin-top: 72px; padding: 34px; display: flex; align-items: center; justify-content: space-between; gap: 24px; background: #ffffff; border: 1px solid #dfe6f0; border-radius: 8px; }
    .clients-cta h2 { margin: 0 0 6px; color: #203a63; font-size: 1.35rem; letter-spacing: 0; }
    .clients-cta p { margin: 0; color: #65738c; }
    .clients-cta a { flex: none; padding: 13px 22px; color: #ffffff; font-weight: 700; text-decoration: none; background: #2f5597; border-radius: 6px; }
    @media (max-width: 900px) { .clients-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 600px) {
        .clients-shell { width: min(100%, calc(100% - 28px)); }
        .clients-hero { padding: 48px 0 54px; }
        .clients-directory { padding: 52px 0 64px; }
        .clients-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .clients-card { padding: 10px; }
        .clients-logo { height: 92px; min-height: 92px; padding: 10px; }
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
            <div class="clients-grid">
                @foreach($clients as $client)
                    <a class="clients-card" href="{{ $client['href'] }}" aria-label="View {{ $client['name'] }} case studies">
                        <div class="clients-logo">
                            @if($client['logo'])
                                <img
                                    src="{{ $client['logo'] }}"
                                    alt="{{ $client['name'] }} logo"
                                    loading="lazy"
                                    decoding="async"
                                    @if(!empty($client['logo_scale']))
                                    style="transform: scale({{ $client['logo_scale'] }}); transform-origin: center;"
                                    @endif
                                >
                            @else
                                <span class="clients-logo-fallback">{{ $client['name'] }}</span>
                            @endif
                        </div>
                        <h3 class="clients-name">{{ $client['name'] }}</h3>
                    </a>
                @endforeach
            </div>

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
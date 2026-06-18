@extends('layouts.public')

@section('title', 'Industries | Armely')
@section('meta_description', 'Explore Armely industry solutions for healthcare, energy, financial services, higher education, manufacturing, nonprofit, professional services, state and local government, transportation, and agriculture.')
@section('canonical_url', url('/industries'))

@push('styles')
<style>
.industries-index-page {
  background: linear-gradient(180deg, #07111f 0%, #0f2747 52%, #eef4fb 52%, #eef4fb 100%);
  color: #172033;
}
.industries-index-hero {
  min-height: 100svh;
  padding: 96px 56px 28px;
  background:
    radial-gradient(900px 420px at 14% 18%, rgba(70, 125, 214, 0.34), rgba(70, 125, 214, 0) 58%),
    radial-gradient(640px 360px at 88% 12%, rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0) 62%),
    linear-gradient(135deg, #142f55 0%, #1d4a7d 54%, #234f86 100%);
  display: flex;
  align-items: stretch;
  position: relative;
  overflow: hidden;
  color: #fff;
}
.industries-index-hero::before,
.industries-index-hero::after {
  content: '';
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
}
.industries-index-hero::before {
  width: 420px;
  height: 420px;
  top: -160px;
  right: -120px;
  background: rgba(255, 255, 255, 0.06);
}
.industries-index-hero::after {
  width: 280px;
  height: 280px;
  left: 12%;
  bottom: -130px;
  background: rgba(255, 255, 255, 0.05);
}
.industries-index-inner {
  max-width: 1180px;
  margin: 0 auto;
}
.industries-index-hero-inner {
  min-height: calc(100svh - 124px);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  position: relative;
  z-index: 1;
}
.industries-index-hero-copy {
  max-width: 860px;
  padding-top: 36px;
}
.industries-index-kicker {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 6px 14px;
  border-radius: 999px;
  background: rgba(255,255,255,0.12);
  border: 1px solid rgba(255,255,255,0.2);
  color: rgba(255,255,255,0.9);
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  margin-bottom: 18px;
}
.industries-index-hero h1 {
  font-size: clamp(2.2rem, 4vw, 3.7rem);
  line-height: 1.08;
  font-weight: 800;
  letter-spacing: -0.03em;
  max-width: 820px;
  margin: 0 0 16px;
}
.industries-index-hero p {
  max-width: 760px;
  margin: 0;
  color: rgba(255,255,255,0.82);
  font-size: 1rem;
  line-height: 1.8;
}
.industries-index-tabs-label {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 14px;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: rgba(255,255,255,0.68);
}
.industries-index-tabs {
  display: flex;
  gap: 12px;
  overflow-x: auto;
  padding: 14px;
  margin-top: 26px;
  border-radius: 22px;
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.16);
  backdrop-filter: blur(14px);
  box-shadow: 0 18px 44px rgba(8, 20, 40, 0.18);
  scrollbar-width: none;
}
.industries-index-tabs::-webkit-scrollbar { display: none; }
.industries-index-tab {
  flex: 0 0 auto;
  min-width: 180px;
  padding: 14px 16px;
  border-radius: 16px;
  text-decoration: none;
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.12);
  color: #fff;
  transition: transform 0.18s ease, background 0.18s ease, border-color 0.18s ease;
}
.industries-index-tab:hover {
  transform: translateY(-2px);
  background: rgba(255,255,255,0.14);
  border-color: rgba(255,255,255,0.24);
  text-decoration: none;
}
.industries-index-tab strong {
  display: block;
  font-size: 0.92rem;
  font-weight: 800;
  line-height: 1.2;
  margin-bottom: 4px;
}
.industries-index-tab span {
  display: block;
  font-size: 0.72rem;
  line-height: 1.35;
  color: rgba(255,255,255,0.62);
}
.industries-index-grid-wrap {
  padding: 38px 56px 84px;
}
.industries-index-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 18px;
}
.industry-index-card {
  display: flex;
  flex-direction: column;
  gap: 14px;
  min-height: 220px;
  padding: 22px;
  background: #fff;
  border: 1px solid rgba(47,85,151,0.12);
  border-radius: 18px;
  box-shadow: 0 10px 30px rgba(18,47,82,0.06);
  text-decoration: none;
  transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
}
.industry-index-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 16px 38px rgba(18,47,82,0.1);
  border-color: rgba(47,85,151,0.22);
}
.industry-index-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}
.industry-index-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  border-radius: 14px;
  background: rgba(47,85,151,0.1);
  color: #2f5597;
  font-size: 1rem;
}
.industry-index-slug {
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #2f5597;
  background: rgba(47,85,151,0.08);
  border: 1px solid rgba(47,85,151,0.12);
  border-radius: 999px;
  padding: 4px 10px;
  white-space: nowrap;
}
.industry-index-title {
  font-size: 1.2rem;
  font-weight: 800;
  line-height: 1.25;
  color: #172033;
  margin: 0;
}
.industry-index-desc {
  font-size: 0.92rem;
  line-height: 1.7;
  color: #5f6f86;
  margin: 0;
}
.industry-index-footer {
  margin-top: auto;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #2f5597;
  font-weight: 700;
  font-size: 0.9rem;
}
.industry-index-arrow {
  font-size: 1.15rem;
  line-height: 1;
}
.industries-index-note {
  margin-top: 20px;
  color: #6b7b92;
  font-size: 0.85rem;
}
@media (max-width: 900px) {
  .industries-index-hero,
  .industries-index-grid-wrap {
    padding-left: 24px;
    padding-right: 24px;
  }
  .industries-index-hero {
    min-height: auto;
    padding-top: 72px;
    padding-bottom: 22px;
  }
  .industries-index-hero-inner { min-height: auto; gap: 28px; }
  .industries-index-hero-copy { padding-top: 0; }
  .industries-index-tab { min-width: 160px; }
}
</style>
@endpush

@section('content')
<div class="industries-index-page">
  <section class="industries-index-hero">
    <div class="industries-index-inner industries-index-hero-inner">
      <div class="industries-index-hero-copy">
        <div class="industries-index-kicker">Industries</div>
        <h1>Choose the industry track that matches your environment.</h1>
        <p>Each industry page below maps to a verified route in the resources/views/industries/ folder and opens the full industry-specific content for that sector.</p>
      </div>

      <div>
        <div class="industries-index-tabs-label">Industry navigation</div>
        <div class="industries-index-tabs" aria-label="Industry navigation">
          @foreach($industryPages as $slug => $page)
            <a class="industries-index-tab" href="{{ route('industries.show', ['industry' => $page['route_label']]) }}">
              <strong>{{ $page['label'] }}</strong>
              <span>{{ $page['route_label'] }}</span>
            </a>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <section class="industries-index-grid-wrap">
    <div class="industries-index-inner">
      <div class="industries-index-grid">
        @foreach($industryPages as $slug => $page)
          <a class="industry-index-card" href="{{ route('industries.show', ['industry' => $page['route_label']]) }}">
            <div class="industry-index-top">
              <div class="industry-index-badge"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
              <span class="industry-index-slug">{{ $page['route_label'] }}</span>
            </div>
            <h2 class="industry-index-title">{{ $page['label'] }}</h2>
            <p class="industry-index-desc">{{ $page['description'] }}</p>
            <span class="industry-index-footer">View industry <span class="industry-index-arrow">-&gt;</span></span>
          </a>
        @endforeach
      </div>
      <div class="industries-index-note">Canonical industry routes are shown above. Common aliases like oil-gas or education are also normalized to the right page.</div>
    </div>
  </section>
</div>
@endsection

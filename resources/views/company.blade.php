@extends('layouts.public')

@section('title', 'About Armely | Microsoft Data and AI Partner Since 2016')
@section('meta_description', 'Armely is a Microsoft Solutions Partner founded in Dallas in 2016. We implement Microsoft Fabric, Power BI, Copilot, Power Platform, and Dynamics 365 for healthcare, energy, government, and enterprise organizations.')

@push('styles')
<style>

.armely-company-page *, .armely-company-page *::before, .armely-company-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-company-page {
    --navy:      #FFFFFF;
    --navy-mid:  #f6f8fc;
    --navy-card: #ffffff;
    --blue:      #2f5597;
    --blue-lt:   #4779bd;
    --blue-dim:  rgba(47, 85, 151, 0.09);
    --blue-dim2: rgba(47, 85, 151, 0.18);
    --accent:      #2f5597;
    --accent-soft: rgba(47, 85, 151, 0.10);
    --text-body: #334155;
    --text-muted: #667085;
    --border:    rgba(47, 85, 151, 0.14);
    font-family: 'Poppins', sans-serif;
    background: var(--navy);
    color: var(--text-body);
    line-height: 1.6;
  }

.armely-company-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-company-page .section-title { font-size: clamp(1.35rem, 2.4vw, 1.9rem); font-weight: 800; color: #1A2540; line-height: 1.15; letter-spacing: -0.02em; margin-bottom: 18px; max-width: 640px; padding: 0; text-align: left; }
.armely-company-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.7; color: var(--text-body); margin-bottom: 28px; }
.armely-company-page .icon-svg { width: 22px; height: 22px; display: block; color: var(--blue); fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

/* HERO */
.armely-company-page .co-hero { background:linear-gradient(135deg,#173b67 0%,#234f86 100%); padding:48px 56px 42px; }
.armely-company-page .co-hero-inner { max-width:1120px; margin:0 auto; text-align:left; }
.armely-company-page .co-hero .section-eyebrow { display:inline-flex; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.22); color:rgba(255,255,255,0.88); border-radius:999px; padding:6px 14px; margin-bottom:16px; }
.armely-company-page .co-hero h1 { font-size:clamp(1.4rem,2.4vw,2rem); font-weight:800; color:#fff; letter-spacing:-0.02em; line-height:1.15; margin-bottom:14px; }
.armely-company-page .co-hero h1 em { font-style:normal; font-weight:300; color:rgba(255,255,255,0.7); }
.armely-company-page .co-hero-sub { font-size:1rem; font-weight:300; color:rgba(255,255,255,0.65); line-height:1.75; max-width:none; margin-bottom:32px; }
.armely-company-page .co-hero-stats { display:flex; gap:32px; flex-wrap:wrap; padding-top:28px; border-top:1px solid rgba(255,255,255,0.15); }
.armely-company-page .co-hero-stat-num { font-size:1.6rem; font-weight:800; color:#fff; line-height:1; letter-spacing:-0.02em; margin-bottom:3px; }
.armely-company-page .co-hero-stat-num span { color:#8fb3e0; }
.armely-company-page .co-hero-stat-label { font-size:0.72rem; color:rgba(255,255,255,0.5); }

/* STORY */
.armely-company-page .co-story { padding:44px 56px; background:#fff; }
.armely-company-page .co-story-inner { max-width:1120px; margin:0 auto; display:grid; grid-template-columns:1fr 1fr; gap:56px; align-items:start; }
.armely-company-page .co-story-facts { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-top:26px; }
.armely-company-page .co-story-fact { border:1px solid var(--border); border-radius:12px; padding:18px 20px; background:var(--navy-mid); }
.armely-company-page .co-story-fact-num { font-size:1.15rem; font-weight:800; color:#162b49; line-height:1.2; letter-spacing:-0.01em; }
.armely-company-page .co-story-fact-num span { color:var(--accent); }
.armely-company-page .co-story-fact-label { font-size:0.74rem; color:var(--text-muted); line-height:1.45; margin-top:5px; }
.armely-company-page .co-story-fact { transition:transform 0.2s ease, box-shadow 0.2s ease; }
.armely-company-page .co-story-fact:hover { transform:translateY(-2px); box-shadow:0 12px 28px rgba(18,47,82,0.08); }
.armely-company-page .co-story-quote { position:relative; margin-top:28px; padding:28px 28px 28px 32px; border-left:4px solid var(--accent); background:var(--accent-soft); border-radius:0 14px 14px 0; font-size:1.05rem; font-style:italic; font-weight:500; color:#162b49; line-height:1.6; }
.armely-company-page .co-story-quote::before { content:"\201C"; position:absolute; top:14px; right:20px; font-family:Georgia, serif; font-size:3.2rem; line-height:1; color:var(--accent); opacity:0.22; pointer-events:none; }
.armely-company-page .co-story-body { font-size:0.975rem; color:var(--text-body); line-height:1.85; }
.armely-company-page .co-story-body p { margin-bottom:18px; }
.armely-company-page .co-story-body p:last-child { margin-bottom:0; }
.armely-company-page .co-story-body strong { color:var(--blue); font-weight:700; }
/* Lead paragraph opens the story with more presence + a navy drop cap */
.armely-company-page .co-story-body p:first-of-type { font-size:1.075rem; color:#1e2d4a; line-height:1.7; }
.armely-company-page .co-story-body p:first-of-type::first-letter { float:left; font-family:'Poppins', sans-serif; font-weight:800; font-size:3.2rem; line-height:0.82; color:var(--blue); margin:6px 12px 0 0; }
/* Closing line of the story gets a quiet emphasis */
.armely-company-page .co-story-body p:last-of-type { padding-top:18px; border-top:1px solid var(--border); color:#1e2d4a; }
.armely-company-page .co-story .section-title { font-size:clamp(1.2rem,1.9vw,1.5rem); margin-bottom:18px; }
.armely-company-page .co-milestones { position:relative; display:flex; flex-direction:column; padding-left:4px; }
.armely-company-page .co-milestones::before { content:""; position:absolute; left:11px; top:8px; bottom:10px; width:2px; background:linear-gradient(180deg, var(--accent) 0%, var(--blue-dim2) 60%, transparent 100%); }
.armely-company-page .co-milestone { position:relative; display:block; padding:0 0 28px 40px; }
.armely-company-page .co-milestone:last-child { padding-bottom:0; }
.armely-company-page .co-milestone::before { content:""; position:absolute; left:4px; top:3px; width:16px; height:16px; border-radius:50%; background:#fff; border:3px solid var(--accent); box-shadow:0 0 0 4px var(--accent-soft); transition:transform 0.2s ease; }
.armely-company-page .co-milestone:hover::before { transform:scale(1.18); }
.armely-company-page .co-milestone-year { display:inline-block; background:linear-gradient(135deg, var(--blue) 0%, var(--blue-lt) 100%); color:#fff; padding:3px 11px; border-radius:999px; font-size:0.66rem; font-weight:800; letter-spacing:0.08em; margin-bottom:8px; }
.armely-company-page .co-milestone-text { font-size:0.9rem; color:var(--text-body); line-height:1.65; }
.armely-company-page .co-milestone-text strong { color:#162b49; font-weight:700; display:block; margin-bottom:3px; font-size:0.95rem; }
.armely-company-page .co-mini-title { font-size:1rem; font-weight:800; color:#162b49; margin-bottom:16px; }

/* PRINCIPLES / VALUES */
.armely-company-page .co-principles { padding:44px 56px; background:var(--navy-mid); }
.armely-company-page .co-principles-inner { max-width:1120px; margin:0 auto; }
.armely-company-page .co-principles .section-eyebrow,
.armely-company-page .co-affil .section-eyebrow,
.armely-company-page .co-brands .section-eyebrow { width:fit-content; margin:0 auto 14px; padding:6px 14px; border-radius:999px; background:var(--blue-dim); border:1px solid var(--blue-dim2); text-align:center; }
.armely-company-page .co-principles .section-title,
.armely-company-page .co-affil .section-title,
.armely-company-page .co-brands .section-title { max-width:880px; margin-left:auto; margin-right:auto; text-align:center; }
.armely-company-page .co-principles .section-body,
.armely-company-page .co-affil .section-body,
.armely-company-page .co-brands .section-body { max-width:820px; margin-left:auto; margin-right:auto; text-align:center; }
.armely-company-page .co-principles-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; margin-top:24px; }
.armely-company-page .co-principle { position:relative; overflow:hidden; background:#fff; border:1px solid var(--border); border-radius:14px; padding:30px 28px 28px; box-shadow:0 14px 36px rgba(18,47,82,0.06); transition:transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease; }
.armely-company-page .co-principle:hover { transform:translateY(-4px); box-shadow:0 24px 48px rgba(18,47,82,0.12); border-color:var(--accent-soft); }
.armely-company-page .co-principle-num { position:absolute; top:10px; right:22px; font-size:3rem; font-weight:800; line-height:1; letter-spacing:-0.02em; color:var(--accent); opacity:0.16; margin:0; }
.armely-company-page .co-principle-title { font-size:1rem; font-weight:800; color:#162b49; margin-bottom:8px; padding-right:48px; }
.armely-company-page .co-principle-body { font-size:0.82rem; color:var(--text-muted); line-height:1.7; }

/* AD BANNER */
.armely-company-page .co-banners { padding:24px 56px 0; background:#fff; }
.armely-company-page .co-banners-inner { max-width:1120px; margin:0 auto; }
.armely-company-page .company-ad-banner { border-radius:16px; padding:28px; color:#fff; box-shadow:0 10px 30px rgba(23,39,67,0.18); overflow:hidden; position:relative; }
.armely-company-page .company-ad-banner::before { content:''; position:absolute; inset:0; background:linear-gradient(120deg,rgba(255,255,255,0.12),rgba(255,255,255,0)); pointer-events:none; }
.armely-company-page .company-ad-banner h3 { font-size:1.45rem; font-weight:700; margin-bottom:0.75rem; color:#fff; }
.armely-company-page .company-ad-banner p { font-size:1.02rem; line-height:1.7; margin-bottom:0; color:rgba(255,255,255,0.95); }
.armely-company-page .banner-image { max-width:180px; width:100%; border-radius:12px; border:1px solid rgba(255,255,255,0.3); }
.armely-company-page .banner-cta-btn { display:inline-flex; align-items:center; justify-content:center; min-height:42px; padding:10px 18px; border-radius:10px; background:#fff; color:#1f3f80 !important; font-weight:700; text-decoration:none; box-shadow:0 8px 24px rgba(0,0,0,0.15); }

/* AFFILIATIONS */
.armely-company-page .co-affil { padding:40px 56px; background:#fff; border-top:1px solid var(--border); }
.armely-company-page .co-affil-inner { max-width:1120px; margin:0 auto; text-align:center; }
.armely-company-page .co-affil-logos { display:flex; flex-direction:column; align-items:center; gap:24px; margin-top:26px; }
.armely-company-page .co-affil-row { display:flex; flex-wrap:wrap; justify-content:center; align-items:center; gap:32px 64px; }
.armely-company-page .co-affil-logos img { position:relative; z-index:1; height:104px; width:auto; object-fit:contain; filter:grayscale(20%); opacity:0.85; transition:transform 0.3s ease, filter 0.3s ease, opacity 0.3s ease; }
.armely-company-page .co-affil-logos img.co-affil-badge { height:150px; }
.armely-company-page .co-affil-logos img.co-affil-nobox { height:150px; clip-path: inset(12%); }
.armely-company-page .co-affil-logos img:hover { transform:scale(1.35); z-index:3; filter:grayscale(0%) drop-shadow(0 10px 22px rgba(15,23,42,0.18)); opacity:1; }

/* INNOVATION BRANDS */
.armely-company-page .co-brands { padding:44px 56px; background:var(--navy-mid); }
.armely-company-page .co-brands-inner { max-width:1120px; margin:0 auto; }
.armely-company-page .co-brands-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; margin-top:24px; }
.armely-company-page .co-brand-card { background:#fff; border:1px solid var(--border); border-radius:14px; padding:28px; box-shadow:0 14px 36px rgba(18,47,82,0.08); display:flex; flex-direction:column; }
.armely-company-page .co-brand-logo { width:54px; height:54px; border-radius:12px; background:var(--navy-mid); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; padding:8px; margin-bottom:16px; }
.armely-company-page .co-brand-logo img { max-width:100%; max-height:100%; object-fit:contain; }
.armely-company-page .co-brand-eyebrow { font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.12em; color:var(--blue); margin-bottom:6px; }
.armely-company-page .co-brand-title { font-size:1.05rem; font-weight:800; color:#162b49; margin-bottom:6px; }
.armely-company-page .co-brand-sub { font-size:0.78rem; font-weight:600; color:var(--text-muted); margin-bottom:10px; }
.armely-company-page .co-brand-body { font-size:0.82rem; color:var(--text-muted); line-height:1.7; margin-bottom:14px; }
.armely-company-page .co-brand-features { list-style:none; padding:0; margin:0 0 16px; }
.armely-company-page .co-brand-features li { display:flex; gap:9px; align-items:flex-start; font-size:0.8rem; color:var(--text-body); line-height:1.5; margin-bottom:8px; }
.armely-company-page .co-brand-features .icon-svg { width:14px; height:14px; stroke-width:3; margin-top:3px; flex-shrink:0; }
.armely-company-page .co-brand-link { font-size:0.78rem; font-weight:600; color:var(--blue); display:inline-flex; align-items:center; gap:5px; text-decoration:none; margin-top:auto; }
.armely-company-page .co-brand-link .icon-svg { width:14px; height:14px; }
.armely-company-page .co-brand-link:hover { color:#173b67; }
.armely-company-page .co-brands-empty { grid-column:1/-1; border:1px dashed var(--blue-dim2); border-radius:14px; padding:24px; text-align:center; color:var(--text-muted); background:#fff; }

/* CTA */
.armely-company-page .co-cta { background:linear-gradient(135deg,#173b67 0%,#234f86 100%); padding:44px 56px; text-align:center; }
.armely-company-page .co-cta .section-eyebrow { display:inline-flex; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.22); color:rgba(255,255,255,0.88); border-radius:999px; padding:6px 14px; margin-bottom:16px; }
.armely-company-page .co-cta h2 { font-size:1.35rem; font-weight:800; color:#fff; letter-spacing:-0.02em; margin-bottom:10px; }
.armely-company-page .co-cta p { font-size:0.925rem; color:rgba(255,255,255,0.65); max-width:480px; margin:0 auto 28px; line-height:1.7; }
.armely-company-page .co-cta-btns { display:flex; align-items:center; justify-content:center; gap:12px; }
.armely-company-page .co-cta-btn-p { background:#fff; color:var(--blue); border-radius:8px; padding:13px 28px; font-size:0.875rem; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:7px; }
.armely-company-page .co-cta-btn-p .icon-svg { width:13px; height:13px; }
.armely-company-page .co-cta-btn-o { background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.85); border:1px solid rgba(255,255,255,0.22); border-radius:8px; padding:13px 28px; font-size:0.875rem; font-weight:500; text-decoration:none; }

@media (max-width:960px) {
  .armely-company-page .co-story-inner { grid-template-columns:1fr; gap:48px; }
  .armely-company-page .co-principles-grid, .armely-company-page .co-brands-grid { grid-template-columns:1fr; }
  .armely-company-page .co-story, .armely-company-page .co-principles, .armely-company-page .co-affil, .armely-company-page .co-brands, .armely-company-page .co-cta, .armely-company-page .co-hero, .armely-company-page .co-banners { padding-left:24px; padding-right:24px; }
  .armely-company-page .co-cta-btns { flex-direction:column; }
  .armely-company-page .co-affil-logos { gap:28px 36px; }
}
@media (max-width:560px) {
  .armely-company-page .co-affil-logos { grid-template-columns:repeat(2, auto); }
  .armely-company-page .co-affil-logos img { height:84px; }
  .armely-company-page .co-affil-logos img.co-affil-badge { height:120px; }
  .armely-company-page .co-affil-logos img.co-affil-nobox { height:120px; }
}
</style>
@endpush

@section('content')
<div class="armely-company-page">

  {{-- HERO --}}
  <section class="co-hero">
    <div class="co-hero-inner">
      <div class="section-eyebrow">About Armely</div>
      <h1>We implement Microsoft technology. <em>We stand behind what we build.</em></h1>
      <p class="co-hero-sub">Armely is a certified Microsoft Solutions Partner founded in Dallas in 2016. We work with healthcare, energy, government, and enterprise organizations to implement data platforms, AI solutions, and business applications that deliver measurable outcomes, not just working software.</p>
      <div class="co-hero-stats">
        <div>
          <div class="co-hero-stat-num">{{ date('Y') - 2016 }} yrs</div>
          <div class="co-hero-stat-label">Delivery experience</div>
        </div>
        <div>
          <div class="co-hero-stat-num">85%+</div>
          <div class="co-hero-stat-label">Client retention rate</div>
        </div>
        <div>
          <div class="co-hero-stat-num">90%+</div>
          <div class="co-hero-stat-label">Client satisfaction</div>
        </div>
        @if(($caseStudyCount ?? 0) > 0)
        <div>
          <div class="co-hero-stat-num">{{ $caseStudyCount }}+</div>
          <div class="co-hero-stat-label">Published case studies</div>
        </div>
        @endif
      </div>
    </div>
  </section>

  {{-- STORY --}}
  <section class="co-story">
    <div class="co-story-inner">
      <div>
        <div class="section-eyebrow">Our Story</div>
        <h2 class="section-title">Built by someone who saw how implementation should work.</h2>
        <div class="co-story-body">
          <p>Armely was founded in Dallas in 2016 by someone who spent years inside Microsoft's partner ecosystem before deciding to build something different. The observation was straightforward: <strong>mid-market organizations</strong> (hospitals, school districts, energy operators, nonprofits) were not getting the same caliber of Microsoft implementation that large enterprises received. They were getting junior teams, templated solutions, and handoffs to support queues.</p>
          <p>Armely was built to close that gap. <strong>A small, senior team. Deep Microsoft expertise. Full-stack delivery from strategy through deployment.</strong> And a commitment to staying involved after go-live rather than moving on to the next contract.</p>
          <p>Some of Armely's longest client relationships started with a single person who trusted the work enough to bring Armely with them when they moved to a new organization. That pattern, one successful engagement leading to another because <strong>the relationship outlasted the project</strong>, has shaped how Armely grows. Not through marketing, but through work that holds up over time.</p>
          <p>Nearly a decade later, Armely works across healthcare, energy, government, education, legal services, and enterprise organizations. The service lines have expanded to include Microsoft Fabric, Copilot, Dynamics 365, and proprietary products like InvoiceLens. <strong>The approach has not changed.</strong></p>
        </div>

        <div class="co-story-quote">"The people who scope your project are the same people who build it, deploy it, and answer the phone when something breaks."</div>

        <div class="co-story-facts">
          <div class="co-story-fact">
            <div class="co-story-fact-num">{{ date('Y') - 2016 }}<span>+ yrs</span></div>
            <div class="co-story-fact-label">Delivering since 2016</div>
          </div>
          <div class="co-story-fact">
            <div class="co-story-fact-num">Dallas, <span>TX</span></div>
            <div class="co-story-fact-label">Headquarters</div>
          </div>
          <div class="co-story-fact">
            <div class="co-story-fact-num">Microsoft <span>Partner</span></div>
            <div class="co-story-fact-label">Solutions Partner — Data &amp; AI</div>
          </div>
          <div class="co-story-fact">
            <div class="co-story-fact-num">MBE <span>Certified</span></div>
            <div class="co-story-fact-label">Minority Business Enterprise</div>
          </div>
        </div>

        @if(!empty($dbErrorMessage))
          <div class="alert alert-warning mt-4" role="alert">
            <i class="icofont-warning-alt"></i> {{ $dbErrorMessage }}
          </div>
        @endif
      </div>
      <div>
        <div class="section-eyebrow">Timeline</div>
        <div class="co-mini-title">Key milestones</div>
        <div class="co-milestones">
          <div class="co-milestone">
            <span class="co-milestone-year">2016</span>
            <div class="co-milestone-text"><strong>Founded in Dallas</strong>Armely was started by a Microsoft partner ecosystem veteran who saw that mid-market organizations were not getting the implementation quality they deserved. Armely set out to build a firm that delivered it.</div>
          </div>
          <div class="co-milestone">
            <span class="co-milestone-year">2017</span>
            <div class="co-milestone-text"><strong>First long-term client relationships</strong>Early healthcare and public sector engagements established Armely's delivery model: understand the real problem, build the right solution, stay accountable for the outcome.</div>
          </div>
          <div class="co-milestone">
            <span class="co-milestone-year">2019</span>
            <div class="co-milestone-text"><strong>Government and education practice</strong>Armely began working with school districts and city governments across Texas. Microsoft 365 governance and Power Platform became core service lines.</div>
          </div>
          <div class="co-milestone">
            <span class="co-milestone-year">2021</span>
            <div class="co-milestone-text"><strong>Microsoft Solutions Partner</strong>Armely achieved Microsoft Solutions Partner status in Data and AI and Modern Work, a recognition that requires demonstrated client outcomes, not just certifications.</div>
          </div>
          <div class="co-milestone">
            <span class="co-milestone-year">2023</span>
            <div class="co-milestone-text"><strong>Energy sector and InvoiceLens</strong>Work with oil and gas operators established Armely's energy practice. InvoiceLens, a proprietary product for Enverus OpenInvoice operators, emerged from client work in this sector.</div>
          </div>
          <div class="co-milestone">
            <span class="co-milestone-year">2024</span>
            <div class="co-milestone-text"><strong>Training and Managed Services formalized</strong>Power BI, Power Platform, Generative AI, and Copilot training launched as a dedicated service line. Managed Services expanded to cover ongoing governance and optimization engagements.</div>
          </div>
          <div class="co-milestone">
            <span class="co-milestone-year">2025</span>
            <div class="co-milestone-text"><strong>Generative and Agentic AI practice</strong>Armely launched a formal Generative AI and Agentic AI service line. It covers building production-grade AI agents grounded in client data using Azure AI Foundry and Microsoft Copilot Studio.</div>
          </div>
          <div class="co-milestone">
            <span class="co-milestone-year">2026</span>
            <div class="co-milestone-text"><strong>Armely Store and Mela AI live</strong>The Armely Store launched as a direct channel for business technology products. Mela AI, Armely's meeting intelligence product, went live for general availability.</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- PRINCIPLES / VALUES --}}
  <section class="co-principles">
    <div class="co-principles-inner">
      <div class="section-eyebrow">How We Work</div>
      <h2 class="section-title">Four things clients say about working with Armely.</h2>
      <p class="section-body">Not a values statement. Not aspirational copy. Four specific things that show up consistently in client feedback and distinguish Armely from the alternatives.</p>
      <div class="co-principles-grid">
        <div class="co-principle">
          <div class="co-principle-num">01</div>
          <div class="co-principle-title">We stay involved after go-live</div>
          <div class="co-principle-body">Most implementation firms hand over a finished project and move on. Armely stays. The people who built your solution are the same people who support it, optimize it, and answer the phone when something breaks. Clients notice this because it is genuinely rare.</div>
        </div>
        <div class="co-principle">
          <div class="co-principle-num">02</div>
          <div class="co-principle-title">Small enough to be responsive. Experienced enough to deliver.</div>
          <div class="co-principle-body">Armely is not a 5,000-person firm that assigns a junior team to mid-market clients. The consultants who scope your project are the consultants who build it. You get senior attention on every engagement, not just the kickoff call.</div>
        </div>
        <div class="co-principle">
          <div class="co-principle-num">03</div>
          <div class="co-principle-title">We publish what we build</div>
          <div class="co-principle-body">Armely publishes full case studies for every major engagement, including named clients, problem statements, and outcomes. This is not common in this industry. It reflects a simple belief: if the work is good, it should be visible.</div>
        </div>
        <div class="co-principle">
          <div class="co-principle-num">04</div>
          <div class="co-principle-title">We measure what matters</div>
          <div class="co-principle-body">Armely does not consider an engagement complete until the client can point to a measurable change. A report that used to take three days now takes three minutes, a process that required four people now runs automatically, a decision that was made on instinct is now made on data.</div>
        </div>
      </div>
    </div>
  </section>

  {{-- AFFILIATIONS --}}
  <section class="co-affil">
    <div class="co-affil-inner">
      <div class="section-eyebrow">Certifications and Affiliations</div>
      <h2 class="section-title">Recognized where it matters.</h2>
      <p class="section-body">Armely holds certifications that matter to the clients we serve. Microsoft Solutions Partner status requires demonstrated client outcomes, not just technical exams. MBE certification reflects who we are as an organization.</p>
      <div class="co-affil-logos">
        <div class="co-affil-row">
          <img class="co-affil-badge" src="{{ asset('images/affiliation/mbe.svg') }}" alt="Minority Business Enterprise certified">
          <img class="co-affil-badge" src="{{ asset('images/affiliation/smb.svg') }}" alt="SMB certification">
          <img src="{{ asset('images/affiliation/affliation1.png') }}" alt="Technology affiliation">
          <img src="{{ asset('images/affiliation/fid.png') }}" alt="Federal ID certification">
        </div>
        <div class="co-affil-row">
          <img src="{{ asset('images/affiliation/partner.png') }}" alt="Microsoft Solutions Partner - Data & AI, Azure">
          <img class="co-affil-nobox" src="{{ asset('images/affiliation/digital_logo.png') }}" alt="Microsoft Solutions Partner - Digital & App Innovation, Azure">
          <img src="{{ asset('images/affiliation/xiad_badge.png') }}" alt="XIAD certification badge">
        </div>
      </div>
    </div>
  </section>

  {{-- INNOVATION BRANDS --}}
  <section class="co-brands">
    <div class="co-brands-inner">
      <div class="section-eyebrow">Innovation Brands</div>
      <h2 class="section-title">We build products, not just client solutions.</h2>
      <p class="section-body">Armely develops its own products to demonstrate how the technology works in practice, and because some problems are common enough to deserve a purpose-built solution rather than a custom engagement each time.</p>
      <div class="co-brands-grid">

        <div class="co-brand-card">
          <div class="co-brand-eyebrow">Everyone</div>
          <div class="co-brand-title">Mela AI</div>
          <div class="co-brand-sub">AI meeting capture, summaries, and follow-ups</div>
          <div class="co-brand-body">Mela is Armely's AI product for meeting intelligence, capturing conversations, generating summaries, and surfacing follow-up actions automatically. Built on Azure AI and Microsoft Copilot Studio, it demonstrates how enterprise AI can be embedded into daily workflows without disrupting how teams already work.</div>
          <a href="/mela-ai" class="co-brand-link">Explore Mela
            <svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
        </div>

        <div class="co-brand-card">
          <div class="co-brand-eyebrow">Oil and Gas</div>
          <div class="co-brand-title">InvoiceLens</div>
          <div class="co-brand-sub">OpenInvoice visibility and AP intelligence for operators</div>
          <div class="co-brand-body">InvoiceLens gives Enverus OpenInvoice operators real-time visibility into pending invoice spend, accrual exposure, and approval workflow status before invoices reach the ERP. Built on Microsoft Fabric inside the operator's own Azure tenant. Deployed and supported by Armely.</div>
          <a href="/invoice-lens" class="co-brand-link">Learn about InvoiceLens
            <svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
        </div>

        <div class="co-brand-card">
          <div class="co-brand-eyebrow">Data Analytics and BI</div>
          <div class="co-brand-title">Step &amp; Sip</div>
          <div class="co-brand-sub">A data-driven retail experience built on Microsoft Fabric</div>
          <div class="co-brand-body">Step &amp; Sip is Armely's retail analytics experience, a working demonstration of how modern data platforms connect retail operations end to end. Built on Microsoft Fabric and Power Platform, it showcases real-time inventory, sales forecasting, customer segmentation, and workflow automation in a context that any business audience can understand without a technical background.</div>
        </div>

      </div>
    </div>
  </section>

  {{-- CTA (hidden)
  <section class="co-cta">
    <div class="section-eyebrow">Work With Armely</div>
    <h2>Talk to the people who will actually build your solution.</h2>
    <p>At Armely, the consultants who scope your project are the same consultants who build it. No handoffs to a delivery team you have never met. Start with a conversation.</p>
    <div class="co-cta-btns">
      <a href="{{ route('contact') }}" class="co-cta-btn-p">Let's Talk
        <svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
      </a>
      <a href="{{ route('case-studies.index') }}" class="co-cta-btn-o">See Our Work</a>
    </div>
  </section>
  --}}

</div>
@endsection

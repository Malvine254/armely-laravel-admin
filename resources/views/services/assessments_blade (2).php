@extends('layouts.public')
@section('title', 'Technology Assessments and Discovery | Armely')
@section('meta_description', 'Armely offers two tiers of assessments: free health checks and paid discovery engagements with fees credited to implementation.')
@section('canonical_url', url('/assessments'))

@push('styles')
<style>

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --blue:      #2f5597;
  --blue-lt:   #4477bd;
  --blue-dim:  rgba(47,85,151,0.08);
  --blue-dim2: rgba(47,85,151,0.16);
  --navy:      #1a2e52;
  --navy-mid:  #f6f8fc;
  --text-body: #334155;
  --text-muted:#667085;
  --border:    rgba(47,85,151,0.13);
}
body { font-family:'Poppins',sans-serif; background:#fff; color:var(--text-body); line-height:1.6; }
.icon-svg { width:20px; height:20px; display:block; fill:none; stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }

/* HERO */
.hero { background:linear-gradient(135deg,#173b67 0%,#234f86 100%); padding:72px 56px 56px; }
.hero-inner { max-width:1120px; margin:0 auto; }
.hero-eyebrow { display:flex; align-items:center; gap:10px; margin-bottom:16px; flex-wrap:wrap; }
.eyebrow-badge { display:inline-flex; background:rgba(255,255,255,0.10); border:1px solid rgba(255,255,255,0.22); border-radius:999px; padding:6px 14px; color:rgba(255,255,255,0.88); font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.12em; }
.eyebrow-partner { font-size:0.74rem; color:rgba(255,255,255,0.5); }
.hero h1 { font-size:clamp(2rem,4.2vw,3.1rem); font-weight:800; color:#fff; line-height:1.12; max-width:780px; margin-bottom:16px; letter-spacing:-0.03em; }
.hero-sub { font-size:0.975rem; font-weight:300; color:rgba(255,255,255,0.80); max-width:680px; margin-bottom:24px; line-height:1.7; }
.hero-actions { display:flex; gap:10px; flex-wrap:wrap; }
.btn-primary { background:var(--blue); color:#fff; border:none; border-radius:8px; padding:12px 26px; font-family:'Poppins',sans-serif; font-size:0.875rem; font-weight:600; cursor:pointer; text-decoration:none; display:inline-block; }
.btn-primary:hover { background:var(--blue-lt); }
.btn-outline { background:transparent; color:rgba(255,255,255,0.85); border:1px solid rgba(255,255,255,0.28); border-radius:8px; padding:12px 26px; font-family:'Poppins',sans-serif; font-size:0.875rem; font-weight:500; cursor:pointer; text-decoration:none; display:inline-block; }

/* SECTIONS */
.section { padding:56px 56px; background:#fff; }
.section.alt { background:var(--navy-mid); }
.inner { max-width:1120px; margin:0 auto; }
.section-eyebrow { width:fit-content; margin:0 auto 10px; padding:5px 13px; border-radius:999px; background:var(--blue-dim); border:1px solid var(--blue-dim2); font-size:0.69rem; font-weight:600; text-transform:uppercase; letter-spacing:0.12em; color:var(--blue); display:block; }
.section-title { font-size:clamp(1.4rem,2.6vw,2rem); font-weight:800; color:#1a2540; line-height:1.15; letter-spacing:-0.02em; margin-bottom:10px; text-align:center; max-width:820px; margin-left:auto; margin-right:auto; background:none !important; background-color:transparent !important; -webkit-text-fill-color:#1a2540 !important; padding:0 !important; border-radius:0 !important; }
.section-title span, .section-title mark { background:none !important; background-color:transparent !important; color:inherit !important; padding:0 !important; }
.section-body { font-size:0.925rem; font-weight:300; max-width:780px; line-height:1.72; color:var(--text-body); margin-bottom:28px; text-align:center; margin-left:auto; margin-right:auto; }

/* TIER COMPARE */
.compare-grid { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-top:28px; }
.compare-card { background:#fff; border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:0 4px 18px rgba(18,47,82,0.06); }
.compare-header { padding:18px 22px; border-bottom:1px solid var(--border); }
.compare-header.t1 { background:var(--blue-dim); }
.compare-header.t2 { background:var(--navy); }
.compare-badge { font-size:0.82rem; font-weight:700; color:#1a2540; margin-bottom:3px; }
.compare-header.t2 .compare-badge { color:#fff; }
.compare-sub { font-size:0.75rem; color:var(--text-muted); }
.compare-header.t2 .compare-sub { color:rgba(255,255,255,0.55); }
.compare-body { padding:18px 22px; display:flex; flex-direction:column; gap:8px; }
.cmp-row { display:flex; align-items:center; gap:9px; font-size:0.82rem; color:var(--text-body); }
.cmp-row .icon-svg { width:15px; height:15px; color:var(--blue); flex-shrink:0; }
.compare-divider { height:1px; background:var(--border); margin:4px 0; }
.compare-label { font-size:0.67rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--blue); }
.cmp-item { display:flex; align-items:center; gap:8px; font-size:0.79rem; color:var(--text-body); }
.cmp-item .icon-svg { width:15px; height:15px; color:var(--blue); flex-shrink:0; }

/* TIER LABEL ROW */
.tier-label-row { display:flex; align-items:center; gap:14px; margin-bottom:14px; flex-wrap:wrap; }
.tier-pill { display:inline-block; border-radius:999px; padding:5px 14px; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; }
.tier-pill.t1 { background:var(--blue-dim); border:1px solid var(--blue-dim2); color:var(--blue); }
.tier-pill.t2 { background:var(--navy); color:#fff; }
.tier-desc { font-size:0.82rem; color:var(--text-muted); }

/* ASSESSMENT CARDS */
.acard-grid { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-top:24px; }
.acard { background:#fff; border:1px solid var(--border); border-radius:14px; padding:24px 22px; display:flex; flex-direction:column; gap:10px; box-shadow:0 4px 18px rgba(18,47,82,0.06); transition:border-color 0.2s, transform 0.2s; }
.acard:hover { border-color:rgba(47,85,151,0.3); transform:translateY(-2px); }
.acard-t1 { border-top:3px solid var(--blue); }
.acard-t2 { border-top:3px solid var(--navy); }
.acard-top { display:flex; align-items:center; justify-content:space-between; gap:10px; }
.acard-icon { width:40px; height:40px; background:var(--blue-dim); border:1px solid var(--blue-dim2); border-radius:10px; display:flex; align-items:center; justify-content:center; color:var(--blue); flex-shrink:0; }
.tier-badge { font-size:0.63rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; padding:3px 9px; border-radius:4px; white-space:nowrap; }
.badge-free { background:var(--blue-dim); border:1px solid var(--blue-dim2); color:var(--blue); }
.badge-paid { background:var(--navy); color:#fff; }
.acard-name { font-size:0.925rem; font-weight:700; color:#1a2540; line-height:1.3; }
.acard-tagline { font-size:0.8rem; color:var(--blue); font-weight:500; }
.acard-time { display:flex; align-items:center; gap:6px; font-size:0.75rem; color:var(--text-muted); }
.acard-time .icon-svg { width:13px; height:13px; }
.acard-what { font-size:0.8rem; line-height:1.65; color:var(--text-body); padding-top:8px; border-top:1px solid var(--border); }
.acard-delivers-label { font-size:0.67rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--blue); }
.acard-points { display:flex; flex-direction:column; gap:6px; }
.check-item { display:flex; align-items:flex-start; gap:8px; font-size:0.775rem; color:var(--text-body); line-height:1.5; }
.check-icon { color:var(--blue); flex-shrink:0; margin-top:1px; }
.check-icon .icon-svg { width:14px; height:14px; }
.acard-who { font-size:0.775rem; color:var(--text-muted); line-height:1.5; padding-top:8px; border-top:1px solid var(--border); margin-top:auto; }
.acard-who strong { color:#1a2540; }
.acard-cta { display:block; text-align:center; border-radius:7px; padding:10px 14px; font-size:0.8rem; font-weight:600; text-decoration:none; transition:background 0.2s; }
.cta-free { background:var(--blue-dim); border:1px solid var(--blue-dim2); color:var(--blue); }
.cta-free:hover { background:var(--blue-dim2); }
.cta-paid { background:var(--navy); color:#fff; }
.cta-paid:hover { background:#234268; }

/* STEPS */
.steps-row { display:grid; grid-template-columns:repeat(5,1fr); gap:0; margin-top:26px; border:1px solid var(--border); border-radius:12px; overflow:hidden; box-shadow:0 2px 14px rgba(18,47,82,0.05); }
.step { padding:20px 16px; border-right:1px solid var(--border); background:#fff; }
.step:last-child { border-right:none; }
.step-num { font-size:1.8rem; font-weight:800; color:rgba(47,85,151,0.15); line-height:1; margin-bottom:8px; }
.step-title { font-size:0.845rem; font-weight:700; color:#1a2540; margin-bottom:6px; }
.step-desc { font-size:0.77rem; line-height:1.6; color:var(--text-body); }
.step-tag { display:inline-block; margin-top:10px; background:var(--blue-dim); color:var(--blue); font-size:0.65rem; padding:2px 8px; border-radius:4px; font-weight:600; text-transform:uppercase; }

/* WHY */
.two-col { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-top:26px; align-items:stretch; }
.why-list { list-style:none; }
.why-list li { display:flex; gap:12px; padding:16px 0; border-bottom:1px solid var(--border); }
.why-list li:last-child { border-bottom:none; }
.why-icon { width:38px; height:38px; flex-shrink:0; background:var(--blue-dim); border:1px solid var(--blue-dim2); border-radius:9px; display:flex; align-items:center; justify-content:center; color:var(--blue); }
.why-title { font-weight:600; color:#1a2540; font-size:0.845rem; margin-bottom:3px; }
.why-desc { font-size:0.79rem; color:var(--text-body); line-height:1.62; }

/* PARTNER BLOCK */
.partner-block { background:#fff; border:1px solid var(--border); border-radius:12px; overflow:hidden; box-shadow:0 2px 14px rgba(18,47,82,0.05); }
.partner-top { padding:20px; border-bottom:1px solid var(--border); }
.partner-label { font-size:0.64rem; text-transform:uppercase; letter-spacing:0.14em; color:var(--blue); font-weight:700; margin-bottom:8px; }
.partner-text { font-size:0.825rem; color:var(--text-body); line-height:1.68; }
.stats-grid { display:grid; grid-template-columns:1fr 1fr; }
.stat { padding:16px 20px; border-right:1px solid var(--border); border-bottom:1px solid var(--border); }
.stat:nth-child(2) { border-right:none; }
.stat:nth-child(3) { border-bottom:none; }
.stat:nth-child(4) { border-right:none; border-bottom:none; }
.stat-num { font-size:1.55rem; font-weight:800; color:#1a2540; line-height:1; margin-bottom:4px; }
.stat-num span { color:var(--blue); }
.stat-label { font-size:0.7rem; color:var(--text-muted); line-height:1.4; }

/* CTA */
.cta-section { background:var(--navy-mid); padding:56px 56px; }
.cta-inner { max-width:1120px; margin:0 auto; display:grid; grid-template-columns:1fr 1fr; gap:48px; align-items:start; }
.cta-form { background:#fff; border:1px solid var(--border); border-radius:12px; padding:28px 24px; box-shadow:0 4px 22px rgba(18,47,82,0.08); }
.form-title { font-size:1rem; font-weight:700; color:#1a2540; margin-bottom:4px; }
.form-sub { font-size:0.79rem; color:var(--text-muted); margin-bottom:18px; }
.form-row { margin-bottom:11px; }
.form-row label { display:block; font-size:0.69rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:4px; }
.form-row input,.form-row select,.form-row textarea { width:100%; background:#fff; border:1px solid rgba(47,85,151,0.16); border-radius:7px; padding:9px 12px; font-family:'Poppins',sans-serif; font-size:0.835rem; color:#1a2540; outline:none; }
.form-submit { width:100%; background:var(--blue); color:#fff; border:none; border-radius:7px; padding:12px; margin-top:6px; font-family:'Poppins',sans-serif; font-size:0.875rem; font-weight:600; cursor:pointer; }
.form-submit:hover { background:var(--blue-lt); }
.form-note { text-align:center; margin-top:9px; font-size:0.7rem; color:var(--text-muted); }
.trust-item { display:flex; align-items:center; gap:9px; margin-bottom:8px; font-size:0.82rem; color:var(--text-body); }
.trust-dot { width:7px; height:7px; border-radius:50%; background:var(--blue); flex-shrink:0; }

@media (max-width:900px) {
  .hero { padding:72px 24px 48px; }
  .section,.cta-section { padding:44px 24px; }
  .compare-grid,.acard-grid,.two-col { grid-template-columns:1fr; }
  .steps-row { grid-template-columns:1fr; }
  .step { border-right:none; border-bottom:1px solid var(--border); }
  .step:last-child { border-bottom:none; }
  .cta-inner { grid-template-columns:1fr; gap:28px; }
}
@media (max-width:600px) {
  .hero h1 { font-size:clamp(1.8rem,8vw,2.6rem); }
}

</style>
@endpush

@section('content')
<!-- HERO -->
<section class="hero">
  <div class="hero-inner">
    <div class="hero-eyebrow">
      <span class="eyebrow-badge">Assessments</span>
      <span class="eyebrow-partner">Know before you invest</span>
    </div>
    <h1>Two tiers. One purpose.<br>Know what your environment<br>actually needs.</h1>
    <p class="hero-sub">Armely offers two types of assessments. Short, free health checks that take one week and cost Armely an hour of time. And structured discovery engagements that take one to three weeks, carry a fee, and have that fee credited in full to your implementation if you proceed with Armely.</p>
    <div class="hero-actions">
      <a href="#tier1" class="btn-primary">See Free Assessments</a>
      <a href="#tier2" class="btn-outline">See Paid Assessments</a>
    </div>
  </div>
</section>

<!-- HOW THE TIERS WORK -->
<section class="section alt">
  <div class="inner">
    <div class="section-eyebrow">How the Two Tiers Work</div>
    <h2 class="section-title">Free where it is genuinely free. Paid where real work is involved.</h2>
    <p class="section-body">Armely structures its assessments to reflect actual delivery cost and genuine value. Tier 1 is a quick scan and a conversation. Tier 2 is a professional services engagement with a deliverable worth paying for.</p>
    <div class="compare-grid">
      <div class="compare-card">
        <div class="compare-header t1">
          <div class="compare-badge">Tier 1 - Free</div>
          <div class="compare-sub">Automated scan plus a structured 30-minute call</div>
        </div>
        <div class="compare-body">
          <div class="cmp-row"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>Delivered within one week</span></div>
          <div class="cmp-row"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg><span>One-page written summary of findings</span></div>
          <div class="cmp-row"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg><span>No obligation, no follow-up pressure</span></div>
          <div class="cmp-row"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg><span>You own the summary outright</span></div>
          <div class="compare-divider"></div>
          <div class="compare-label">Includes</div>
          <div class="cmp-item"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg><span>Microsoft 365 Copilot Readiness Check</span></div>
          <div class="cmp-item"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg><span>Microsoft 365 Tenant Health Check</span></div>
        </div>
      </div>
      <div class="compare-card">
        <div class="compare-header t2">
          <div class="compare-badge">Tier 2 - Paid, fee credited to implementation</div>
          <div class="compare-sub">Structured engagement with a full written deliverable</div>
        </div>
        <div class="compare-body">
          <div class="cmp-row"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>1-3 week structured engagement</span></div>
          <div class="cmp-row"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg><span>Full strategy document and implementation roadmap</span></div>
          <div class="cmp-row"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg><span>Assessment fee credited 100% to your Armely implementation</span></div>
          <div class="cmp-row"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg><span>You own the deliverable outright regardless of next steps</span></div>
          <div class="compare-divider"></div>
          <div class="compare-label">Includes</div>
          <div class="cmp-item"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 8V4H8"/><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 11v2"/><path d="M15 11v2"/></svg><span>AI Readiness Assessment</span></div>
          <div class="cmp-item"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z"/><path d="M9 3v15"/><path d="M15 6v15"/></svg><span>Generative AI Use Case Assessment</span></div>
          <div class="cmp-item"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg><span>Microsoft Fabric Data Assessment</span></div>
          <div class="cmp-item"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg><span>Snowflake Migration Assessment</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TIER 1 -->
<section class="section" id="tier1">
  <div class="inner">
    <div class="tier-label-row">
      <span class="tier-pill t1">Tier 1 - Free</span>
      <span class="tier-desc">Automated scan and a 30-minute call. Written summary within one week.</span>
    </div>
    <h2 class="section-title">Free health checks. One week. One page of findings.</h2>
    <p class="section-body">These assessments are genuinely free because they require minimal Armely time. An automated scan runs against your tenant, an engineer reviews the results, and you receive a concise written summary within one week.</p>
    <div class="acard-grid">
      <div class="acard acard-t1">
  <div class="acard-top">
    <div class="acard-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg></div>
    <span class="tier-badge badge-free">Free</span>
  </div>
  <div class="acard-name">Microsoft 365 Copilot Readiness Check</div>
  <div class="acard-tagline">Know if your tenant is ready for Copilot before you buy.</div>
  <div class="acard-time"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>30-min call + report within 1 week</span></div>
  <div class="acard-what">An automated scan of your Microsoft 365 tenant combined with a structured 30-minute call. We check your security posture, external sharing configuration, sensitivity label coverage, and license options against Microsoft's published Copilot readiness criteria. You receive a short written summary within one week.</div>
  <div class="acard-delivers-label">What you receive</div>
  <div class="acard-points"><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Copilot readiness score against Microsoft's published criteria</span></div><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Top three gaps to address before activation</span></div><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>License recommendation with Armely partner pricing</span></div></div>
  <div class="acard-who"><strong>Best for:</strong> Organizations considering Microsoft 365 Copilot who want to confirm readiness before committing to a license.</div>
  <a href="#contact" class="acard-cta cta-free">Request This Assessment</a>
</div>
      <div class="acard acard-t1">
  <div class="acard-top">
    <div class="acard-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
    <span class="tier-badge badge-free">Free</span>
  </div>
  <div class="acard-name">Microsoft 365 Tenant Health Check</div>
  <div class="acard-tagline">Find out what is actually wrong with your Microsoft 365 environment.</div>
  <div class="acard-time"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>30-min call + report within 1 week</span></div>
  <div class="acard-what">An automated and manual review of your Microsoft 365 tenant covering external sharing exposure, Teams and SharePoint site sprawl, inactive accounts, license utilization, and security configuration against Microsoft's recommended baselines.</div>
  <div class="acard-delivers-label">What you receive</div>
  <div class="acard-points"><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>External sharing and guest access exposure summary</span></div><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Security posture score against Microsoft's baseline</span></div><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Top five prioritized remediation items</span></div></div>
  <div class="acard-who"><strong>Best for:</strong> Any organization running Microsoft 365 that has not had a formal governance or security review in the past 12 months.</div>
  <a href="#contact" class="acard-cta cta-free">Request This Assessment</a>
</div>
    </div>
  </div>
</section>

<!-- TIER 2 -->
<section class="section alt" id="tier2">
  <div class="inner">
    <div class="tier-label-row">
      <span class="tier-pill t2">Tier 2 - Paid, credited to implementation</span>
      <span class="tier-desc">1-3 week engagement. Full written deliverable. Fee credited if Armely implements.</span>
    </div>
    <h2 class="section-title">Paid discovery with a deliverable worth having.</h2>
    <p class="section-body">These assessments involve real work by Armely engineers: stakeholder interviews, environment review, architecture design, and a written strategy document. They carry a fee that reflects that work. If you proceed with Armely for implementation, the full assessment fee is credited against your project invoice.</p>
    <div class="acard-grid">
      <div class="acard acard-t2">
  <div class="acard-top">
    <div class="acard-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 8V4H8"/><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 11v2"/><path d="M15 11v2"/></svg></div>
    <span class="tier-badge badge-paid">Fee credited to implementation</span>
  </div>
  <div class="acard-name">AI Readiness Assessment</div>
  <div class="acard-tagline">Identify your highest-value AI opportunities before committing budget.</div>
  <div class="acard-time"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>2-3 week engagement</span></div>
  <div class="acard-what">A structured evaluation of your organization's readiness to adopt AI. We assess data quality and accessibility, existing Microsoft platform configuration, governance posture, and security controls. We then identify and prioritize AI use cases based on business value, data readiness, and implementation complexity.</div>
  <div class="acard-delivers-label">What you receive</div>
  <div class="acard-points"><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>AI readiness score across data, governance, security, and platform dimensions</span></div><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Ranked use case list with business value and complexity ratings</span></div><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Platform recommendations specific to your Microsoft stack</span></div><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Written roadmap and business case for leadership review</span></div></div>
  <div class="acard-who"><strong>Best for:</strong> Organizations that want to invest in AI but need clarity on where to start and how to justify the investment internally.</div>
  <a href="#contact" class="acard-cta cta-paid">Request This Assessment</a>
</div>
      <div class="acard acard-t2">
  <div class="acard-top">
    <div class="acard-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z"/><path d="M9 3v15"/><path d="M15 6v15"/></svg></div>
    <span class="tier-badge badge-paid">Fee credited to implementation</span>
  </div>
  <div class="acard-name">Generative AI Use Case Assessment</div>
  <div class="acard-tagline">Identify which AI use cases will deliver real value for your business.</div>
  <div class="acard-time"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>2-3 week engagement</span></div>
  <div class="acard-what">Working with your leadership and operational teams, we surface, document, and prioritize generative AI and agentic AI use cases against your actual business data, systems, and compliance requirements. Each use case is scored for business value, data readiness, and implementation complexity.</div>
  <div class="acard-delivers-label">What you receive</div>
  <div class="acard-points"><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Documented AI use case inventory across all departments</span></div><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Each use case scored for value, data readiness, and complexity</span></div><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Ranked priority list with a clear recommendation on where to start</span></div><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Technology platform recommendations and indicative timeline for top use cases</span></div></div>
  <div class="acard-who"><strong>Best for:</strong> Organizations with multiple competing AI ideas and no consensus on where the investment will deliver the most value.</div>
  <a href="#contact" class="acard-cta cta-paid">Request This Assessment</a>
</div>
      <div class="acard acard-t2">
  <div class="acard-top">
    <div class="acard-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg></div>
    <span class="tier-badge badge-paid">Fee credited to implementation</span>
  </div>
  <div class="acard-name">Microsoft Fabric Data Assessment</div>
  <div class="acard-tagline">Understand your data landscape before choosing a platform.</div>
  <div class="acard-time"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>1-2 week engagement</span></div>
  <div class="acard-what">We audit your current data sources, existing tools, and reporting processes. We assess migration complexity from your current stack, identify quick wins, and design the target Fabric architecture. We also produce a right-sized capacity recommendation and licensing cost model.</div>
  <div class="acard-delivers-label">What you receive</div>
  <div class="acard-points"><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Data source inventory with gap analysis against your reporting needs</span></div><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Migration complexity rating from your current stack</span></div><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Target Fabric architecture design for your workloads</span></div><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Capacity sizing, licensing recommendation, and implementation roadmap</span></div></div>
  <div class="acard-who"><strong>Best for:</strong> Organizations running scattered data tools or manual reporting who are evaluating Microsoft Fabric.</div>
  <a href="#contact" class="acard-cta cta-paid">Request This Assessment</a>
</div>
      <div class="acard acard-t2">
  <div class="acard-top">
    <div class="acard-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg></div>
    <span class="tier-badge badge-paid">Fee credited to implementation</span>
  </div>
  <div class="acard-name">Snowflake Migration Assessment</div>
  <div class="acard-tagline">Know exactly what a Snowflake migration involves before starting.</div>
  <div class="acard-time"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>1-2 week engagement</span></div>
  <div class="acard-what">We assess your current data warehouse or analytics environment, inventory objects and dependencies, estimate migration complexity, and design the target Snowflake architecture. We include a cost comparison between your current spend and a right-sized Snowflake deployment at Armely partner pricing.</div>
  <div class="acard-delivers-label">What you receive</div>
  <div class="acard-points"><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Current environment inventory and migration complexity rating</span></div><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Target Snowflake architecture and account structure design</span></div><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Cost comparison between current stack and Snowflake at partner pricing</span></div><div class="check-item"><span class="check-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span>Phased migration plan with timeline and resource requirements</span></div></div>
  <div class="acard-who"><strong>Best for:</strong> Organizations running on SQL Server, Oracle, Teradata, Redshift, or Azure Synapse who are evaluating Snowflake.</div>
  <a href="#contact" class="acard-cta cta-paid">Request This Assessment</a>
</div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="section">
  <div class="inner">
    <div class="section-eyebrow">How It Works</div>
    <h2 class="section-title">Request, scope, assess, receive.</h2>
    <p class="section-body">Both tiers follow the same five steps. The difference is in depth and duration, not in process.</p>
    <div class="steps-row">
      <div class="step"><div class="step-num">01</div><div class="step-title">Request</div><div class="step-desc">Complete the form or call us. Tell us which assessment you are interested in and a brief description of your situation.</div><span class="step-tag">Free</span></div>
      <div class="step"><div class="step-num">02</div><div class="step-title">Scoping Call</div><div class="step-desc">A 30-minute call with an Armely engineer to confirm scope, set expectations, and schedule the engagement.</div><span class="step-tag">Week 1</span></div>
      <div class="step"><div class="step-num">03</div><div class="step-title">Assessment</div><div class="step-desc">Tier 1 uses automated tooling and a single call. Tier 2 involves structured interviews and environment review over one to three weeks.</div><span class="step-tag">Weeks 1-3</span></div>
      <div class="step"><div class="step-num">04</div><div class="step-title">Written Findings</div><div class="step-desc">Tier 1 delivers a one-page summary. Tier 2 delivers a full strategy document and implementation roadmap. You own both outright.</div><span class="step-tag">End of engagement</span></div>
      <div class="step"><div class="step-num">05</div><div class="step-title">Next Steps</div><div class="step-desc">We present findings and a clear recommendation. If Armely implements, the Tier 2 fee is credited in full. If you go elsewhere, the report is yours.</div><span class="step-tag">Your decision</span></div>
    </div>
  </div>
</section>

<!-- WHY THIS STRUCTURE -->
<section class="section alt">
  <div class="inner">
    <div class="section-eyebrow">Why This Structure</div>
    <h2 class="section-title">Honest pricing builds better client relationships than free promises.</h2>
    <p class="section-body">Most firms that offer free assessments either deliver low-quality work or use the process as a thinly disguised sales call. Armely's two-tier structure is transparent about what each assessment involves.</p>
    <div class="two-col">
      <div>
        <ul class="why-list">
          <li>
            <div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
            <div><div class="why-title">Tier 1 is free because the work is genuinely quick</div><div class="why-desc">An automated tenant scan and a 30-minute structured call. Armely invests about an hour. The output is a concise, accurate summary of your top issues. That is worth offering at no cost as a first step in the relationship.</div></div>
          </li>
          <li>
            <div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
            <div><div class="why-title">Tier 2 is paid because the work is real</div><div class="why-desc">Stakeholder interviews, environment review, architecture design, and a written strategy document involve multiple Armely engineers over one to three weeks. Charging for that work ensures we attract serious buyers and deliver serious output.</div></div>
          </li>
          <li>
            <div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 17 6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg></div>
            <div><div class="why-title">The credit removes the financial risk for clients</div><div class="why-desc">If you engage Armely for implementation after a Tier 2 assessment, the full assessment fee is deducted from your implementation invoice. You paid for discovery that directly informed the build.</div></div>
          </li>
          <li>
            <div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg></div>
            <div><div class="why-title">You own every deliverable outright</div><div class="why-desc">Whether you proceed with Armely or not, every written finding and strategy document produced in an assessment belongs to you. Present it internally, share it with your board, or take it to any other vendor.</div></div>
          </li>
        </ul>
      </div>
      <div>
        <div class="partner-block">
          <div class="partner-top">
            <div class="partner-label">Microsoft Authorized Partner</div>
            <p class="partner-text">Armely's Microsoft partnership gives our assessment practice access to Microsoft Secure Score benchmarking tools, Fabric capacity sizing resources, Copilot readiness frameworks, and Azure migration assessment tooling. Our assessments are conducted against current Microsoft best-practice standards.</p>
          </div>
          <div class="stats-grid">
            <div class="stat"><div class="stat-num">2<span></span></div><div class="stat-label">free Tier 1 health checks with one-week turnaround</div></div>
            <div class="stat"><div class="stat-num">4<span></span></div><div class="stat-label">paid Tier 2 assessments with 100% fee credit to implementation</div></div>
            <div class="stat"><div class="stat-num">1<span>wk</span></div><div class="stat-label">maximum turnaround for a Tier 1 health check from first call to written findings</div></div>
            <div class="stat"><div class="stat-num">100<span>%</span></div><div class="stat-label">of all assessment deliverables owned outright by the client</div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section" id="contact">
  <div class="cta-inner">
    <div>
      <div class="section-eyebrow">Get Started</div>
      <h2 class="section-title">Tell us what you need to understand. We will run the right assessment.</h2>
      <p class="section-body">Not sure which assessment applies? Select the option closest to your question and we will confirm the right approach on the scoping call.</p>
      <div style="margin-top:20px;display:flex;flex-direction:column;gap:9px;">
        <div class="trust-item"><span class="trust-dot"></span><span>Tier 1 health checks delivered within one week at no cost</span></div>
        <div class="trust-item"><span class="trust-dot"></span><span>Tier 2 assessment fees credited 100% to your implementation</span></div>
        <div class="trust-item"><span class="trust-dot"></span><span>All written deliverables owned by you outright</span></div>
        <div class="trust-item"><span class="trust-dot"></span><span>Response within one business day</span></div>
      </div>
    </div>
    <div class="cta-form">
      <div class="form-title">Request an Assessment</div>
      <div class="form-sub">Tell us which assessment interests you.</div>
      <div class="form-row"><label>Full Name</label><input type="text" placeholder="Jane Smith"></div>
      <div class="form-row"><label>Business Email</label><input type="email" placeholder="jane@yourcompany.com"></div>
      <div class="form-row"><label>Company Name</label><input type="text" placeholder="Acme Corp"></div>
      <div class="form-row">
        <label>Assessment Requested</label>
        <select>
          <option value="">Select...</option>
          <optgroup label="Tier 1 - Free">
            <option>Microsoft 365 Copilot Readiness Check (Free)</option>
            <option>Microsoft 365 Tenant Health Check (Free)</option>
          </optgroup>
          <optgroup label="Tier 2 - Paid, credited to implementation">
            <option>AI Readiness Assessment</option>
            <option>Generative AI Use Case Assessment</option>
            <option>Microsoft Fabric Data Assessment</option>
            <option>Snowflake Migration Assessment</option>
          </optgroup>
          <option>Not sure, need a recommendation</option>
        </select>
      </div>
      <div class="form-row">
        <label>Brief Description</label>
        <textarea rows="3" style="resize:vertical;" placeholder="What are you trying to understand or decide?"></textarea>
      </div>
      <button class="form-submit">Request Assessment</button>
      <div class="form-note">No spam. No sales pressure. Just a useful conversation.</div>
    </div>
  </div>
</section>
@endsection

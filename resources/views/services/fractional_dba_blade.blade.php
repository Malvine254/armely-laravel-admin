@section('title', 'Fractional DBA Services | SQL Server, Azure SQL, and Snowflake | Armely')
@section('meta_description', 'Armely provides fractional DBA services for SQL Server, Azure SQL, and Snowflake. Senior database administration on a monthly retainer, by the hour, or as a fixed-scope project. No headcount required.')

<style>
.armely-fractional-dba-page *, .armely-fractional-dba-page *::before, .armely-fractional-dba-page *::after { box-sizing: border-box; margin: 0; padding: 0; }
.armely-fractional-dba-page {
  --blue: #2f5597; --blue-lt: #3d6ab5;
  --blue-dim: rgba(47,85,151,0.08); --blue-dim2: rgba(47,85,151,0.16);
  --navy: #FFFFFF; --navy-mid: #F3F6FB;
  --text-body: #3D4F6B; --text-muted: #6B7FA3;
  --border: rgba(47,85,151,0.10);
  font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; scroll-behavior: smooth;
}

/* HERO */
.armely-fractional-dba-page .hero { min-height: 0; display: flex; flex-direction: column; justify-content: flex-start; background: #1a2e52; padding: 72px 56px 80px; position: relative; overflow: hidden; }
.armely-fractional-dba-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-fractional-dba-page .hero-inner { max-width: 1120px; margin: 0 auto; display: grid; grid-template-columns: 1fr 380px; gap: 52px; align-items: center; }
.armely-fractional-dba-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 20px; }
.armely-fractional-dba-page .eyebrow-badge { background: rgba(47,85,151,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-fractional-dba-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); }
.armely-fractional-dba-page .hero h1 { font-family: 'Segoe UI', Arial, sans-serif; font-size: clamp(2rem, 3.5vw, 3rem); font-weight: 700; line-height: 1.12; color: #fff; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.025em; }
.armely-fractional-dba-page .hero h1 .hero-title-break { display: block; margin-top: 7px; color: #fff; font-size: 0.45em; font-weight: 500; line-height: 1.25; letter-spacing: 0; }
.armely-fractional-dba-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.8; }
.armely-fractional-dba-page .hero-platforms { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 32px; }
.armely-fractional-dba-page .hero-platform { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.18); color: rgba(255,255,255,0.8); border-radius: 999px; padding: 5px 13px; }
.armely-fractional-dba-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 0; }
.armely-fractional-dba-page .btn-primary { background: var(--blue); color: #fff; border-radius: 7px; padding: 13px 28px; font-family: 'Poppins', sans-serif; font-size: 0.925rem; font-weight: 600; text-decoration: none; display: inline-block; transition: background 0.2s; }
.armely-fractional-dba-page .btn-primary:hover { background: var(--blue-lt); }
.armely-fractional-dba-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 13px 28px; font-family: 'Poppins', sans-serif; font-size: 0.925rem; font-weight: 500; text-decoration: none; display: inline-block; }

/* Availability card */
.armely-fractional-dba-page .avail-card { background: rgba(8,18,42,0.7); border: 1px solid rgba(255,255,255,0.1); border-radius: 14px; overflow: hidden; }
.armely-fractional-dba-page .avail-head { padding: 13px 20px; border-bottom: 1px solid rgba(255,255,255,0.08); display: flex; align-items: center; gap: 8px; }
.armely-fractional-dba-page .avail-dot { width: 8px; height: 8px; border-radius: 50%; background: #1D9E75; }
.armely-fractional-dba-page .avail-lbl { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.4); }
.armely-fractional-dba-page .avail-row { padding: 13px 20px; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; gap: 12px; }
.armely-fractional-dba-page .avail-row:last-child { border-bottom: none; }
.armely-fractional-dba-page .avail-icon { width: 32px; height: 32px; border-radius: 8px; background: rgba(47,85,151,0.2); border: 1px solid rgba(47,85,151,0.35); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.armely-fractional-dba-page .avail-icon svg { width: 15px; height: 15px; stroke: #7fb4e8; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.armely-fractional-dba-page .avail-strong { font-size: 0.8rem; font-weight: 600; color: rgba(255,255,255,0.88); display: block; margin-bottom: 1px; }
.armely-fractional-dba-page .avail-desc { font-size: 0.72rem; color: rgba(255,255,255,0.5); }

/* Problem strip */
.armely-fractional-dba-page .strip { background: var(--navy-mid); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-fractional-dba-page .strip-inner { max-width: 1120px; margin: 0 auto; padding: 28px 56px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.armely-fractional-dba-page .strip-item { min-height: 156px; padding: 22px 22px 20px; background: #fff; border: 1px solid var(--border); border-top: 3px solid var(--blue); border-radius: 12px; box-shadow: 0 5px 18px rgba(18,47,82,0.06); }
.armely-fractional-dba-page .strip-item:last-child { border-right: 1px solid var(--border); padding-right: 22px; padding-left: 22px; border-top-color: #1d9e75; }
.armely-fractional-dba-page .strip-item:nth-child(2) { padding-left: 22px; border-top-color: #c9893f; }
.armely-fractional-dba-page .strip-lbl { display: inline-block; font-size: 0.62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 12px; }
.armely-fractional-dba-page .strip-item:nth-child(2) .strip-lbl { color: #a66d2d; }
.armely-fractional-dba-page .strip-item:last-child .strip-lbl { color: #168161; }
.armely-fractional-dba-page .strip-text { font-size: 0.875rem; font-weight: 500; color: #294e8b; line-height: 1.65; }

/* Sections */
.armely-fractional-dba-page section { padding: 60px 56px 52px; }
  .armely-fractional-dba-page .alt-bg { background: var(--navy-mid); }
  .armely-fractional-dba-page .section-inner { max-width: 1120px; margin: 0 auto; }
  .armely-fractional-dba-page .section-heading { max-width: 820px; margin-bottom: 28px; }
  .armely-fractional-dba-page .section-heading-centered { margin-left: auto; margin-right: auto; text-align: center; }
  .armely-fractional-dba-page .section-heading-centered .section-eyebrow::after { margin-left: auto; margin-right: auto; }
  .armely-fractional-dba-page .section-heading-centered .section-body { margin-left: auto; margin-right: auto; }
  .armely-fractional-dba-page .section-heading-wide { max-width: 100%; }
  .armely-fractional-dba-page .section-heading-wide .section-title { max-width: 100%; text-wrap: wrap; }
  .armely-fractional-dba-page .section-eyebrow {
    display: inline-block;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.13em;
    color: var(--blue);
    margin-bottom: 14px;
    font-weight: 700;
    line-height: 1.35;
  }
  .armely-fractional-dba-page .section-eyebrow::after {
    content: "";
    display: block;
    width: 64px;
    height: 2px;
    margin-top: 10px;
    border-radius: 999px;
    background: rgba(47,85,151,0.25);
  }
  .armely-fractional-dba-page .section-title {
    font-family: 'Segoe UI', Arial, sans-serif;
    font-size: clamp(1.7rem, 2.5vw, 2.25rem);
    font-weight: 700;
    color: #294e8b;
    line-height: 1.2;
    letter-spacing: -0.025em;
    margin: 0 0 14px;
    max-width: 760px;
    text-wrap: balance;
  }
  .armely-fractional-dba-page .section-body {
    font-size: 1rem;
    font-weight: 300;
    max-width: 660px;
    line-height: 1.7;
    color: var(--text-body);
    margin: 0;
  }

/* Who cards */
.armely-fractional-dba-page .who-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.armely-fractional-dba-page .who-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 24px; box-shadow: 0 4px 16px rgba(18,47,82,0.05); }
.armely-fractional-dba-page .who-icon { width: 44px; height: 44px; border-radius: 12px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
.armely-fractional-dba-page .who-icon svg { width: 22px; height: 22px; stroke: var(--blue); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.armely-fractional-dba-page .who-title { min-height: 2.6em; font-size: 0.925rem; font-weight: 700; color: #294e8b; margin-bottom: 8px; line-height: 1.3; }
.armely-fractional-dba-page .who-desc { font-size: 0.8rem; color: var(--text-muted); line-height: 1.68; }

/* Cover cards */
.armely-fractional-dba-page .cover-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }
.armely-fractional-dba-page .cover-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; gap: 14px; }
.armely-fractional-dba-page .cover-icon { width: 38px; height: 38px; border-radius: 10px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.armely-fractional-dba-page .cover-icon svg { width: 18px; height: 18px; stroke: var(--blue); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.armely-fractional-dba-page .cover-title { font-size: 0.875rem; font-weight: 700; color: #294e8b; margin-bottom: 4px; }
.armely-fractional-dba-page .cover-desc { font-size: 0.78rem; color: var(--text-muted); line-height: 1.65; }

/* Platform cards */
.armely-fractional-dba-page .plat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.armely-fractional-dba-page .plat-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 24px; text-align: center; box-shadow: 0 4px 16px rgba(18,47,82,0.05); }
.armely-fractional-dba-page .plat-name { font-size: 1.1rem; font-weight: 800; color: #294e8b; margin-bottom: 6px; }
.armely-fractional-dba-page .plat-desc { font-size: 0.78rem; color: var(--text-muted); line-height: 1.65; margin-bottom: 10px; }
.armely-fractional-dba-page .plat-tag { display: inline-block; font-size: 0.62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--blue); background: var(--blue-dim); border-radius: 4px; padding: 2px 8px; }

/* Engagement cards */
.armely-fractional-dba-page .eng-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.armely-fractional-dba-page .eng-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 16px rgba(18,47,82,0.05); }
.armely-fractional-dba-page .eng-head { padding: 16px 20px; border-bottom: 1px solid var(--border); background: var(--navy-mid); }
.armely-fractional-dba-page .eng-lbl { font-size: 0.62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); margin-bottom: 4px; }
.armely-fractional-dba-page .eng-name { font-size: 1rem; font-weight: 700; color: #294e8b; }
.armely-fractional-dba-page .eng-body { padding: 18px 20px; }
.armely-fractional-dba-page .eng-desc { font-size: 0.8rem; color: var(--text-muted); line-height: 1.68; margin-bottom: 12px; }
.armely-fractional-dba-page .eng-best { font-size: 0.72rem; font-weight: 600; color: var(--blue); }

/* Client results */
.armely-fractional-dba-page .cr-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-top: 28px; margin-bottom: 28px; }
.armely-fractional-dba-page .cr-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 22px; box-shadow: 0 4px 16px rgba(18,47,82,0.06); }
.armely-fractional-dba-page .cr-label { display: flex; align-items: center; gap: 9px; margin-bottom: 10px; }
.armely-fractional-dba-page .cr-check { width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 50%; flex-shrink: 0; }
.armely-fractional-dba-page .cr-check svg { width: 11px; height: 11px; stroke: var(--blue); fill: none; stroke-width: 3; stroke-linecap: round; stroke-linejoin: round; }
.armely-fractional-dba-page .cr-industry { font-size: 0.875rem; font-weight: 700; color: #294e8b; }
.armely-fractional-dba-page .cr-desc { font-size: 0.8rem; color: var(--text-muted); line-height: 1.65; }
.armely-fractional-dba-page .cr-cta { text-align: center; }
.armely-fractional-dba-page .cr-btn { display: inline-flex; align-items: center; gap: 10px; background: var(--blue); color: #fff; border-radius: 8px; padding: 12px 24px; font-size: 0.875rem; font-weight: 600; text-decoration: none; }
.armely-fractional-dba-page .cr-btn svg { width: 16px; height: 16px; stroke: #fff; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

/* CTA section */
.armely-fractional-dba-page .cta-section { background: var(--navy-mid); }
.armely-fractional-dba-page .cta-inner { max-width: 1120px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: start; }
.armely-fractional-dba-page .cta-copy .section-title { max-width: 100%; }
.armely-fractional-dba-page .cta-copy .section-body { max-width: 100%; }
.armely-fractional-dba-page .cta-form { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px; box-shadow: 0 8px 32px rgba(18,47,82,0.08); }
.armely-fractional-dba-page .form-row { margin-bottom: 14px; }
.armely-fractional-dba-page .form-row label { display: block; font-size: 0.72rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 5px; }
.armely-fractional-dba-page .form-row input, .armely-fractional-dba-page .form-row select { width: 100%; background: #fff; border: 1px solid var(--border); border-radius: 7px; padding: 10px 13px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; }
.armely-fractional-dba-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 13px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.925rem; font-weight: 600; cursor: pointer; }
.armely-fractional-dba-page .form-submit:hover { background: var(--blue-lt); }
.armely-fractional-dba-page .form-note { text-align: center; margin-top: 10px; font-size: 0.72rem; color: var(--text-muted); }

/* SVG icon helper */
.armely-fractional-dba-page .svg-db { stroke: var(--blue); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

/* Responsive */
@media (max-width: 960px) {
  .armely-fractional-dba-page .hero-inner,
  .armely-fractional-dba-page .cta-inner { grid-template-columns: 1fr; }
  .armely-fractional-dba-page .who-grid,
  .armely-fractional-dba-page .plat-grid,
  .armely-fractional-dba-page .eng-grid { grid-template-columns: 1fr 1fr; }
  .armely-fractional-dba-page .cover-grid { grid-template-columns: 1fr; }
  .armely-fractional-dba-page .cr-grid { grid-template-columns: 1fr 1fr; }
  .armely-fractional-dba-page .strip-inner { grid-template-columns: 1fr; }
  .armely-fractional-dba-page .strip-inner { gap: 14px; }
  .armely-fractional-dba-page .strip-item { min-height: 0; border-right: 1px solid var(--border); padding: 20px; }
  .armely-fractional-dba-page .strip-item:last-child { border-bottom: 1px solid var(--border); padding: 20px; }
  .armely-fractional-dba-page .strip-item:nth-child(2) { padding-left: 20px; }
  .armely-fractional-dba-page .hero,
  .armely-fractional-dba-page section,
  .armely-fractional-dba-page .strip-inner { padding-left: 24px; padding-right: 24px; }
}
@media (max-width: 600px) {
  .armely-fractional-dba-page .strip-inner { padding: 22px 20px; grid-template-columns: 1fr; }
  .armely-fractional-dba-page .section-eyebrow { letter-spacing: 0.12em; }
  .armely-fractional-dba-page .section-title { font-size: clamp(1.7rem, 7vw, 2.3rem); }
  .armely-fractional-dba-page .who-grid,
  .armely-fractional-dba-page .plat-grid,
  .armely-fractional-dba-page .eng-grid,
  .armely-fractional-dba-page .cr-grid { grid-template-columns: 1fr; }
}
</style>

<div class="armely-fractional-dba-page">

{{-- HERO --}}
<section class="hero">
  <div class="hero-bg-glow" aria-hidden="true"></div>
  <div class="hero-inner">
    <div>
      <div class="hero-eyebrow">
        <span class="eyebrow-badge">Fractional DBA Services</span>
        <span class="eyebrow-partner">Armely</span>
      </div>
      <h1>Senior database expertise.<span class="hero-title-break">No headcount required.</span></h1>
      <p class="hero-sub">Armely provides experienced database administration for organizations that need senior DBA coverage without a full-time hire. Available on a monthly retainer, by the hour, or as a fixed-scope project.</p>
      <div class="hero-platforms">
        <span class="hero-platform">SQL Server</span>
        <span class="hero-platform">Azure SQL</span>
        <span class="hero-platform">Snowflake</span>
      </div>
      <div class="hero-actions">
        <a href="#contact" class="btn-primary">Talk to a DBA &rarr;</a>
        <a href="#what-we-cover" class="btn-outline">See What We Cover</a>
      </div>
    </div>

    <div class="avail-card">
      <div class="avail-head">
        <span class="avail-dot"></span>
        <span class="avail-lbl">What you get</span>
      </div>
      <div class="avail-row">
        <div class="avail-icon"><svg viewBox="0 0 24 24"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4.03 3-9 3S3 13.66 3 12"/><path d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5"/></svg></div>
        <div><span class="avail-strong">SQL Server, Azure SQL, Snowflake</span><span class="avail-desc">All three platforms covered under one engagement</span></div>
      </div>
      <div class="avail-row">
        <div class="avail-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        <div><span class="avail-strong">Response within one business day</span><span class="avail-desc">For standard requests. Critical issues prioritized.</span></div>
      </div>
      <div class="avail-row">
        <div class="avail-icon"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
        <div><span class="avail-strong">Monthly health report</span><span class="avail-desc">Performance, security, and maintenance summary</span></div>
      </div>
      <div class="avail-row">
        <div class="avail-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
        <div><span class="avail-strong">Works with your existing team</span><span class="avail-desc">Alongside your developers, IT, or no internal team at all</span></div>
      </div>
    </div>
  </div>
</section>

{{-- STRIP --}}
<div class="strip">
  <div class="strip-inner">
    <div class="strip-item">
      <div class="strip-lbl">The problem</div>
      <div class="strip-text">Your databases are running in production without a dedicated DBA. Someone handles it when something breaks.</div>
    </div>
    <div class="strip-item">
      <div class="strip-lbl">The risk</div>
      <div class="strip-text">Performance issues, security gaps, and backup failures do not announce themselves before they cause an outage or a breach.</div>
    </div>
    <div class="strip-item">
      <div class="strip-lbl">The fix</div>
      <div class="strip-text">A senior DBA on retainer who knows your environment, watches your databases, and responds when something needs attention.</div>
    </div>
  </div>
</div>

{{-- WHO THIS IS FOR --}}
<section>
  <div class="section-inner">
    <div class="section-heading section-heading-centered">
      <div class="section-eyebrow">Who this is for</div>
      <h2 class="section-title">When a fractional DBA makes sense.</h2>
      <p class="section-body">Most teams do not need a full-time database hire. They need a steady senior resource who can keep systems healthy without adding headcount.</p>
    </div>
    <div class="who-grid">
      <div class="who-card">
        <div class="who-icon"><svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div>
        <div class="who-title">Companies that cannot justify a full-time DBA hire</div>
        <div class="who-desc">You have SQL Server, Azure SQL, or Snowflake running in production. You do not have a dedicated DBA. Someone on the team handles database issues when they come up, which is not the same thing.</div>
      </div>
      <div class="who-card">
        <div class="who-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
        <div class="who-title">Teams whose DBA just left</div>
        <div class="who-desc">Your database administrator resigned or was let go. You need senior coverage immediately while you figure out a permanent solution. Armely can step in within days, not weeks.</div>
      </div>
      <div class="who-card">
        <div class="who-icon"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
        <div class="who-title">Enterprises that need senior expertise without headcount</div>
        <div class="who-desc">You have budget for the work but not for a full-time hire. A fractional DBA gives you senior-level database expertise on a retainer without a headcount addition or benefits obligation.</div>
      </div>
    </div>
  </div>
</section>

{{-- WHAT WE COVER --}}
<section class="alt-bg" id="what-we-cover">
  <div class="section-inner">
    <div class="section-heading section-heading-centered section-heading-wide">
      <div class="section-eyebrow">What we cover</div>
      <h2 class="section-title">What an Armely fractional DBA actually handles.</h2>
      <p class="section-body">This is not a reactive helpdesk model. It is hands-on database leadership that keeps the environment stable, secure, and ready for growth.</p>
    </div>
    <div class="cover-grid">
      <div class="cover-card">
        <div class="cover-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
        <div>
          <div class="cover-title">Performance tuning and query optimization</div>
          <div class="cover-desc">Identify and fix slow queries, missing indexes, inefficient execution plans, and blocking issues before they affect production. Regular performance reviews included.</div>
        </div>
      </div>
      <div class="cover-card">
        <div class="cover-icon"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
        <div>
          <div class="cover-title">Security, compliance, and access governance</div>
          <div class="cover-desc">Audit database permissions, configure row-level security, implement encryption at rest and in transit, and ensure your environment meets HIPAA, SOC 2, or other compliance requirements.</div>
        </div>
      </div>
      <div class="cover-card">
        <div class="cover-icon"><svg viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.95"/></svg></div>
        <div>
          <div class="cover-title">Backup, recovery, and business continuity</div>
          <div class="cover-desc">Design and test backup and recovery procedures. Confirm your recovery time objectives are achievable. Document runbooks your team can execute without calling anyone.</div>
        </div>
      </div>
      <div class="cover-card">
        <div class="cover-icon"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div>
        <div>
          <div class="cover-title">Monitoring and proactive alerting</div>
          <div class="cover-desc">Set up monitoring for CPU, memory, disk, blocking, and long-running queries. Receive alerts before users do. Monthly health reports included in every engagement.</div>
        </div>
      </div>
      <div class="cover-card">
        <div class="cover-icon"><svg viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg></div>
        <div>
          <div class="cover-title">Schema design and migration support</div>
          <div class="cover-desc">Review schema changes before they go to production. Advise on data modeling decisions. Support application teams with database design for new features or system migrations.</div>
        </div>
      </div>
      <div class="cover-card">
        <div class="cover-icon"><svg viewBox="0 0 24 24"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9z"/></svg></div>
        <div>
          <div class="cover-title">Cloud database migration and modernization</div>
          <div class="cover-desc">Plan and execute migrations from on-premises SQL Server to Azure SQL, or from any source into Snowflake. Includes assessment, migration plan, testing, and cutover support.</div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- PLATFORMS --}}
<section>
  <div class="section-inner">
    <div class="section-heading section-heading-centered section-heading-wide">
      <div class="section-eyebrow">Platforms</div>
      <h2 class="section-title">Support across the platforms your team already depends on.</h2>
      <p class="section-body">One engagement can cover the database platforms your team already relies on, without splitting the work across multiple specialists.</p>
    </div>
    <div class="plat-grid">
      <div class="plat-card">
        <div class="plat-name">SQL Server</div>
        <div class="plat-desc">On-premises and virtual machine deployments. All versions from SQL Server 2012 through 2022. AlwaysOn, clustering, replication, and log shipping.</div>
        <span class="plat-tag">On-premises and cloud</span>
      </div>
      <div class="plat-card">
        <div class="plat-name">Azure SQL</div>
        <div class="plat-desc">Azure SQL Database, Azure SQL Managed Instance, and Azure SQL Edge. Elastic pools, auto-failover groups, and Azure Active Directory authentication.</div>
        <span class="plat-tag">Microsoft cloud</span>
      </div>
      <div class="plat-card">
        <div class="plat-name">Snowflake</div>
        <div class="plat-desc">Snowflake architecture, query optimization, role-based access control, data sharing, and integration with Microsoft Fabric and Power BI reporting layers.</div>
        <span class="plat-tag">Cloud data platform</span>
      </div>
    </div>
  </div>
</section>

{{-- ENGAGEMENT MODELS --}}
<section class="alt-bg">
  <div class="section-inner">
    <div class="section-heading section-heading-centered">
      <div class="section-eyebrow">Engagement models</div>
      <h2 class="section-title">How we work with your team.</h2>
      <p class="section-body">Most clients begin with a focused project, then move to a retainer once the right level of support is clear.</p>
    </div>
    <div class="eng-grid">
      <div class="eng-card">
        <div class="eng-head">
          <div class="eng-lbl">Monthly retainer</div>
          <div class="eng-name">Set monthly fee</div>
        </div>
        <div class="eng-body">
          <div class="eng-desc">A fixed monthly fee for an agreed scope of work. Covers monitoring, routine maintenance, a defined number of support requests, and monthly health reporting. Predictable cost, no surprises.</div>
          <div class="eng-best">Best for: Organizations that want ongoing coverage with a predictable monthly budget.</div>
        </div>
      </div>
      <div class="eng-card">
        <div class="eng-head">
          <div class="eng-lbl">Hour blocks</div>
          <div class="eng-name">Monthly hour bank</div>
        </div>
        <div class="eng-body">
          <div class="eng-desc">Purchase a block of DBA hours each month. Use them for whatever the month requires, whether that is a performance issue, a schema review, or a migration project. Unused hours do not roll over.</div>
          <div class="eng-best">Best for: Companies with variable database needs that are hard to scope in advance.</div>
        </div>
      </div>
      <div class="eng-card">
        <div class="eng-head">
          <div class="eng-lbl">Project scope</div>
          <div class="eng-name">Fixed-scope engagement</div>
        </div>
        <div class="eng-body">
          <div class="eng-desc">A defined project with a clear deliverable. A migration, a security audit, a performance review, a monitoring setup. Scoped, priced, and delivered against agreed milestones.</div>
          <div class="eng-best">Best for: One-time needs or organizations evaluating Armely before committing to a retainer.</div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- CLIENT RESULTS --}}
<section style="background: var(--navy-mid);">
  <div class="section-inner">
    <div class="section-heading section-heading-centered section-heading-wide">
      <div class="section-eyebrow">Client results</div>
      <h2 class="section-title">Results from real database and platform work.</h2>
    </div>
    <div class="cr-grid">
      <div class="cr-card">
        <div class="cr-label">
          <div class="cr-check"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
          <span class="cr-industry">Energy</span>
        </div>
        <p class="cr-desc">Sage Butte Energy: SQL Server administration, OpenInvoice API integration, and Microsoft Power Platform services. Multi-year ongoing engagement.</p>
      </div>
      <div class="cr-card">
        <div class="cr-label">
          <div class="cr-check"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
          <span class="cr-industry">Healthcare</span>
        </div>
        <p class="cr-desc">UNMC: Sybase to SQL Server migration including AlwaysOn high availability, TDE encryption, and Always Encrypted column-level security.</p>
      </div>
      <div class="cr-card">
        <div class="cr-label">
          <div class="cr-check"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
          <span class="cr-industry">Automotive</span>
        </div>
        <p class="cr-desc">MHC Kenworth: Snowflake implementation and Power BI integration. Semantic data model built to support enterprise-wide reporting across dealership operations.</p>
      </div>
    </div>
    <div class="cr-cta">
      <a href="{{ route('case-studies.index') }}" class="cr-btn">
        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>See Published Case Studies</span>
        <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </a>
    </div>
  </div>
</section>

{{-- CONTACT FORM --}}
<section class="cta-section" id="contact">
  <div class="cta-inner">
    <div class="cta-copy">
      <div class="section-heading section-heading-centered">
        <div class="section-eyebrow">Talk to a DBA</div>
        <h2 class="section-title">Start with a practical database review.</h2>
        <p class="section-body">We review your current environment, point out the issues most likely to cause trouble, and recommend the right level of support for your team. No commitment required beyond the conversation.</p>
      </div>
    </div>
    <div class="cta-form">
      <div class="form-row"><label>Full Name</label><input type="text" placeholder="Jane Smith"></div>
      <div class="form-row"><label>Business Email</label><input type="email" placeholder="jane@yourcompany.com"></div>
      <div class="form-row"><label>Company</label><input type="text" placeholder="Your company name"></div>
      <div class="form-row"><label>Database platform</label>
        <select>
          <option value="">Select platform...</option>
          <option>SQL Server (on-premises)</option>
          <option>Azure SQL Database</option>
          <option>Azure SQL Managed Instance</option>
          <option>Snowflake</option>
          <option>Multiple platforms</option>
          <option>Not sure</option>
        </select>
      </div>
      <div class="form-row"><label>Current situation</label>
        <select>
          <option value="">Select...</option>
          <option>No dedicated DBA on staff</option>
          <option>DBA recently left, need coverage</option>
          <option>Need senior expertise for a specific project</option>
          <option>Evaluating ongoing DBA support options</option>
        </select>
      </div>
      <button class="form-submit">Request Free Review</button>
      <div class="form-note">An Armely DBA will follow up within one business day.</div>
    </div>
  </div>
</section>

</div>

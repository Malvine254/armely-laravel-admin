<style>
.armely-service-page *, .armely-service-page *::before, .armely-service-page *::after{ box-sizing: border-box; margin: 0; padding: 0; }.armely-service-page{
    --navy:      #FFFFFF;
    --navy-mid:  #F3F6FB;
    --navy-card: #EBF0F8;
    --blue:      #294e8b;
    --blue-lt:   #3d6ab5;
    --blue-dim:  rgba(41,78,139,0.08);
    --blue-dim2: rgba(41,78,139,0.16);
    --text-body: #3D4F6B;
    --text-muted:#6B7FA3;
    --border:    rgba(41,78,139,0.1);
  }.armely-service-page{ scroll-behavior: smooth; }.armely-service-page{ font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }.armely-service-page nav{
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    display: flex; justify-content: space-between; align-items: center;
    padding: 18px 56px;
    background: rgba(26,46,82,0.96);
    backdrop-filter: blur(14px);
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }.armely-service-page .logo{ display: flex; align-items: center; gap: 10px; }.armely-service-page .logo-mark{ width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }.armely-service-page .logo-text{ font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }.armely-service-page .nav-links{ display: flex; gap: 32px; align-items: center; list-style: none; }.armely-service-page .nav-links a{ color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }.armely-service-page .nav-links a:hover{ color: #fff; }.armely-service-page .nav-cta{ background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }.armely-service-page .nav-cta:hover{ background: var(--blue-lt) !important; }.armely-service-page .hero{
    min-height: 100vh;
    display: flex; flex-direction: column; justify-content: center;
    padding: 140px 56px 100px;
    position: relative; overflow: hidden;
    background: #1a2e52;
  }.armely-service-page .hero-bg-glow{ position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }.armely-service-page .hero-eyebrow{ display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }.armely-service-page .eyebrow-badge{ background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }.armely-service-page .eyebrow-partner{ font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }.armely-service-page .hero h1{ font-size: clamp(2.6rem, 5.5vw, 4.8rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 780px; margin-bottom: 24px; letter-spacing: -0.03em; }.armely-service-page .hero h1 .hl{ color: #FFFFFF; opacity: 0.92; }.armely-service-page .hero-sub{ font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 540px; margin-bottom: 40px; line-height: 1.8; }.armely-service-page .hero-actions{ display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }.armely-service-page .btn-primary{ background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }.armely-service-page .btn-primary:hover{ background: var(--blue-lt); transform: translateY(-2px); }.armely-service-page .btn-outline{ background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }.armely-service-page .btn-outline:hover{ border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }.armely-service-page .hero-trust{ display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }.armely-service-page .trust-item{ display: flex; align-items: center; gap: 10px; }.armely-service-page .trust-dot{ width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }.armely-service-page .trust-text{ font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }.armely-service-page .trust-text strong{ color: #fff; font-weight: 600; }.armely-service-page section{ padding: 96px 56px; }.armely-service-page .section-inner{ max-width: 1100px; margin: 0 auto; }.armely-service-page .section-eyebrow{ font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }.armely-service-page .section-title{ font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 620px; }.armely-service-page .section-body{ font-size: 0.975rem; font-weight: 300; max-width: 540px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }.armely-service-page .intro{ background: var(--navy-mid); }.armely-service-page .intro-grid{ display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }.armely-service-page .stack-card{ background: #fff; border: 1px solid var(--border); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }.armely-service-page .stack-header{ padding: 16px 22px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 10px; background: var(--navy-mid); }.armely-service-page .stack-dots{ display: flex; gap: 6px; }.armely-service-page .stack-dots span{ width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }.armely-service-page .stack-title{ font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }.armely-service-page .stack-body{ padding: 20px; display: flex; flex-direction: column; gap: 6px; }.armely-service-page .stack-layer{ border-radius: 9px; padding: 12px 16px; }.armely-service-page .stack-layer-label{ font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px; }.armely-service-page .stack-chips{ display: flex; flex-wrap: wrap; gap: 5px; }.armely-service-page .stack-chip{ font-size: 0.71rem; font-weight: 600; padding: 3px 9px; border-radius: 20px; }.armely-service-page .layer-ai{ background: rgba(41,78,139,0.07); }.armely-service-page .layer-ai .stack-layer-label{ color: var(--blue); }.armely-service-page .layer-ai .stack-chip{ background: var(--blue-dim2); color: var(--blue); }.armely-service-page .layer-compute{ background: rgba(41,78,139,0.05); }.armely-service-page .layer-compute .stack-layer-label{ color: var(--blue); }.armely-service-page .layer-compute .stack-chip{ background: var(--blue-dim); color: var(--blue); }.armely-service-page .layer-services{ background: rgba(41,78,139,0.03); }.armely-service-page .layer-services .stack-layer-label{ color: var(--text-muted); }.armely-service-page .layer-services .stack-chip{ background: rgba(41,78,139,0.06); color: var(--text-muted); }.armely-service-page .layer-storage{ background: var(--blue); }.armely-service-page .layer-storage .stack-layer-label{ color: rgba(255,255,255,0.7); }.armely-service-page .layer-storage .stack-chip{ background: rgba(255,255,255,0.15); color: #fff; }.armely-service-page .stack-arrow{ text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2px 0; }.armely-service-page .cap-grid{ display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 28px; }.armely-service-page .cap-pill{ background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; gap: 10px; }.armely-service-page .cap-icon{ font-size: 1.1rem; flex-shrink: 0; }.armely-service-page .cap-label{ font-size: 0.8rem; font-weight: 600; color: var(--blue); line-height: 1.3; }.armely-service-page .vs-callout{ margin-top: 20px; background: var(--blue); border-radius: 10px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; }.armely-service-page .vs-callout-icon{ font-size: 1.4rem; flex-shrink: 0; }.armely-service-page .vs-callout-text{ font-size: 0.85rem; color: rgba(255,255,255,0.9); line-height: 1.55; }.armely-service-page .vs-callout-text strong{ color: #fff; }.armely-service-page .delivers{ background: var(--navy); }.armely-service-page .delivers-grid{ display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }.armely-service-page .deliver-card{ background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }.armely-service-page .deliver-card:hover{ border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }.armely-service-page .deliver-icon{ width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }.armely-service-page .deliver-title{ font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }.armely-service-page .deliver-desc{ font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }.armely-service-page .journey{ background: var(--navy-mid); }.armely-service-page .steps-row{ display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }.armely-service-page .step{ padding: 32px 22px; border-right: 1px solid var(--border); }.armely-service-page .step:last-child{ border-right: none; }.armely-service-page .step-num{ font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }.armely-service-page .step-title{ font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }.armely-service-page .step-desc{ font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }.armely-service-page .step-tag{ display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }.armely-service-page .usecases{ background: var(--navy); }.armely-service-page .uc-grid{ display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }.armely-service-page .uc-card{ background: var(--navy-card); border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }.armely-service-page .uc-card:hover{ border-color: rgba(41,78,139,0.25); }.armely-service-page .uc-icon{ font-size: 1.6rem; margin-bottom: 14px; display: block; }.armely-service-page .uc-title{ font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }.armely-service-page .uc-desc{ font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }.armely-service-page .why{ background: var(--navy-mid); }.armely-service-page .why-two-col{ display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }.armely-service-page .why-list{ list-style: none; margin-top: 36px; }.armely-service-page .why-list li{ display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }.armely-service-page .why-list li:last-child{ border-bottom: none; }.armely-service-page .why-icon{ width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }.armely-service-page .why-item-title{ font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }.armely-service-page .why-item-desc{ font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }.armely-service-page .partner-block{ background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }.armely-service-page .partner-block-top{ padding: 28px; border-bottom: 1px solid var(--border); }.armely-service-page .partner-label{ font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }.armely-service-page .partner-text{ font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }.armely-service-page .partner-stats{ display: grid; grid-template-columns: 1fr 1fr; }.armely-service-page .p-stat{ padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }.armely-service-page .p-stat:nth-child(2){ border-right: none; }.armely-service-page .p-stat:nth-child(3){ border-bottom: none; }.armely-service-page .p-stat:nth-child(4){ border-right: none; border-bottom: none; }.armely-service-page .p-stat-num{ font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }.armely-service-page .p-stat-num span{ color: var(--blue); }.armely-service-page .p-stat-label{ font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }.armely-service-page .cta-section{ background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }.armely-service-page .cta-inner{ max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }.armely-service-page .cta-form{ background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }.armely-service-page .form-title{ font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }.armely-service-page .form-sub{ font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }.armely-service-page .form-row{ margin-bottom: 14px; }.armely-service-page .form-row label{ display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }.armely-service-page .form-row input, .armely-service-page .form-row select{ width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }.armely-service-page .form-row input:focus, .armely-service-page .form-row select:focus{ border-color: rgba(41,78,139,0.4); }.armely-service-page .form-row select option{ background: #fff; color: #1A2540; }.armely-service-page .form-submit{ width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }.armely-service-page .form-submit:hover{ background: var(--blue-lt); }.armely-service-page .form-note{ text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }.armely-service-page footer{ background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }.armely-service-page .footer-logo-row{ display: flex; align-items: center; gap: 10px; }.armely-service-page .footer-lm{ width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }.armely-service-page .footer-lt{ font-size: 1rem; font-weight: 700; color: #fff; }.armely-service-page .footer-note{ font-size: 0.78rem; color: rgba(255,255,255,0.4); }.armely-service-page .footer-badges{ display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }.armely-service-page .badge-chip{ border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }@media (max-width: 900px){.armely-service-page nav{ padding: 16px 24px; }.armely-service-page .nav-links{ display: none; }.armely-service-page section{ padding: 72px 24px; }.armely-service-page .hero{ padding: 110px 24px 72px; }.armely-service-page .intro-grid, .armely-service-page .why-two-col{ grid-template-columns: 1fr; gap: 40px; }.armely-service-page .delivers-grid, .armely-service-page .uc-grid{ grid-template-columns: 1fr 1fr; }.armely-service-page .steps-row{ grid-template-columns: 1fr; }.armely-service-page .step{ border-right: none; border-bottom: 1px solid var(--border); }.armely-service-page .step:last-child{ border-bottom: none; }.armely-service-page .cta-inner{ grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }.armely-service-page footer{ padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }@media (max-width: 600px){.armely-service-page .delivers-grid, .armely-service-page .uc-grid{ grid-template-columns: 1fr; }.armely-service-page .cap-grid{ grid-template-columns: 1fr; }.armely-service-page .partner-stats{ grid-template-columns: 1fr; }.armely-service-page .hero-trust{ gap: 20px; }
  }@media (prefers-reduced-motion: reduce){.armely-service-page *{ transition: none !important; animation: none !important; } }

.armely-service-page {
    width: 100vw;
    margin-left: calc(50% - 50vw);
    font-family: 'Poppins', sans-serif;
    background: #fff;
    color: #3D4F6B;
    overflow-x: hidden;
}
.armely-service-page a:hover {
    text-decoration: none;
}
</style>
<div class="armely-service-page armely-service-page-snowflake" data-service="Snowflake">
<!-- HERO -->
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-eyebrow">
    <span class="eyebrow-badge">Snowflake AI Data Cloud</span>
    <span class="eyebrow-partner">Delivered by a certified Snowflake partner</span>
  </div>
  <h1>Your data, your cloud,<br></h1>
  <p class="hero-sub">Armely architects, implements, and manages Snowflake environments that give your business a fast, governed, AI-ready data platform — without the infrastructure headaches.</p>
  <div class="hero-actions">
    <a href="#consultation" class="btn-primary">Book a Free Discovery Call</a>
    <a href="#what-we-deliver" class="btn-outline">See What We Build</a>
  </div>
  <div class="hero-trust">
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Separate compute & storage</strong> — pay only for what you use</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Cortex AI</strong> built directly into SQL</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">Runs on <strong>AWS, Azure & Google Cloud</strong></span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Partner pricing</strong> available</span>
    </div>
  </div>
</section>

<!-- WHAT IS SNOWFLAKE -->
<section class="intro">
  <div class="section-inner">
    <div class="intro-grid">
      <div>
        <div class="section-eyebrow">What is Snowflake?</div>
        <h2 class="section-title">The cloud data platform built for analytics, AI, and scale.</h2>
        <p class="section-body">Snowflake is the AI Data Cloud — a fully managed platform that separates compute from storage so you can scale each independently, query structured and unstructured data in the same place, and run AI workloads directly on your data using Cortex AI. No infrastructure to manage. No performance tuning. No data silos.</p>
        <div class="cap-grid">
          <div class="cap-pill"><span class="cap-icon">🏔️</span><span class="cap-label">Data Warehouse & Analytics</span></div>
          <div class="cap-pill"><span class="cap-icon">🤖</span><span class="cap-label">Cortex AI & LLM Functions</span></div>
          <div class="cap-pill"><span class="cap-icon">🔬</span><span class="cap-label">Data Science & Snowpark</span></div>
          <div class="cap-pill"><span class="cap-icon">🔗</span><span class="cap-label">Data Sharing & Marketplace</span></div>
          <div class="cap-pill"><span class="cap-icon">⚡</span><span class="cap-label">Real-Time & Streaming</span></div>
          <div class="cap-pill"><span class="cap-icon">🛡️</span><span class="cap-label">Horizon Governance</span></div>
        </div>
        <div class="vs-callout">
          <span class="vs-callout-icon">💡</span>
          <span class="vs-callout-text"><strong>Already using Microsoft Fabric?</strong> Snowflake and Fabric are complementary — Fabric excels inside the Microsoft ecosystem; Snowflake shines when your data spans multiple clouds or platforms. Armely can help you decide which fits, or run both.</span>
        </div>
      </div>
      <div>
        <div class="stack-card">
          <div class="stack-header">
            <div class="stack-dots"><span></span><span></span><span></span></div>
            <span class="stack-title">Snowflake Architecture</span>
          </div>
          <div class="stack-body">
            <div class="stack-layer layer-ai">
              <div class="stack-layer-label">Cortex AI Layer</div>
              <div class="stack-chips">
                <span class="stack-chip">Cortex Agents</span>
                <span class="stack-chip">Snowflake Intelligence</span>
                <span class="stack-chip">LLM Functions in SQL</span>
                <span class="stack-chip">Cortex Code</span>
              </div>
            </div>
            <div class="stack-arrow">↕</div>
            <div class="stack-layer layer-compute">
              <div class="stack-layer-label">Compute Layer</div>
              <div class="stack-chips">
                <span class="stack-chip">Virtual Warehouses</span>
                <span class="stack-chip">Snowpark</span>
                <span class="stack-chip">Dynamic Tables</span>
                <span class="stack-chip">Notebooks</span>
              </div>
            </div>
            <div class="stack-arrow">↕</div>
            <div class="stack-layer layer-services">
              <div class="stack-layer-label">Services Layer</div>
              <div class="stack-chips">
                <span class="stack-chip">Horizon Governance</span>
                <span class="stack-chip">Data Sharing</span>
                <span class="stack-chip">Marketplace</span>
                <span class="stack-chip">Security & Access</span>
              </div>
            </div>
            <div class="stack-arrow">↕</div>
            <div class="stack-layer layer-storage">
              <div class="stack-layer-label">Storage Layer — Decoupled from Compute</div>
              <div class="stack-chips">
                <span class="stack-chip">Columnar Micro-Partitions</span>
                <span class="stack-chip">AWS / Azure / GCP</span>
                <span class="stack-chip">Apache Iceberg</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- WHAT ARMELY DELIVERS -->
<section class="delivers" id="what-we-deliver">
  <div class="section-inner">
    <div class="section-eyebrow">What Armely Delivers</div>
    <h2 class="section-title">End-to-end Snowflake implementation — from first query to production.</h2>
    <p class="section-body">Armely handles every layer of your Snowflake environment — architecture, ingestion, transformation, analytics, and AI — so your team spends time on insights, not infrastructure.</p>
    <div class="delivers-grid">
      <div class="deliver-card">
        <div class="deliver-icon">🗺️</div>
        <div class="deliver-title">Architecture & Environment Setup</div>
        <div class="deliver-desc">We design your Snowflake account structure, virtual warehouse sizing, role hierarchy, and network policies before writing a single query — so performance and cost are right from day one.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🔌</div>
        <div class="deliver-title">Data Ingestion & Pipelines</div>
        <div class="deliver-desc">We connect your source systems — databases, SaaS apps, files, and streams — into Snowflake using Snowpipe, Fivetran, dbt, or custom pipelines. Fresh data, on schedule, automatically.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🏗️</div>
        <div class="deliver-title">Data Modelling & Transformation</div>
        <div class="deliver-desc">We build clean, governed data models using dbt or Snowpark so every dashboard and report draws from a consistent, trusted source. No more conflicting numbers across teams.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">📊</div>
        <div class="deliver-title">Analytics & BI Dashboards</div>
        <div class="deliver-desc">We connect your BI tool of choice — Power BI, Tableau, Sigma, Looker — to Snowflake and build the dashboards your business actually needs. Fast, accurate, and always live.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🤖</div>
        <div class="deliver-title">Cortex AI Implementation</div>
        <div class="deliver-desc">We configure Snowflake Cortex so your analysts can run sentiment analysis, LLM completions, and natural-language queries directly in SQL — AI on your data, with no data leaving Snowflake.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🛡️</div>
        <div class="deliver-title">Governance & Ongoing Management</div>
        <div class="deliver-desc">Horizon governance, dynamic data masking, row-level access policies, and cost monitoring — configured from the start. Plus a dedicated Armely contact for ongoing optimisation and support.</div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNEY -->
<section class="journey" id="journey">
  <div class="section-inner">
    <div class="section-eyebrow">The Armely Snowflake Journey</div>
    <h2 class="section-title">From legacy data stack to cloud-native analytics — on a clear timeline.</h2>
    <p class="section-body">Whether you're migrating from an on-premise warehouse, consolidating cloud tools, or starting fresh, we follow a proven methodology that gets you to production fast and right.</p>
    <div class="steps-row">
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-title">Discovery & Assessment</div>
        <div class="step-desc">We audit your current data stack, sources, and analytics needs. Free for new clients — results in a clear Snowflake migration or build plan.</div>
        <span class="step-tag">Free</span>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-title">Architecture & Licensing</div>
        <div class="step-desc">We design your Snowflake environment and source the right capacity at partner pricing — sized for today, scalable for tomorrow.</div>
        <span class="step-tag">1–2 weeks</span>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-title">Build & Migrate</div>
        <div class="step-desc">Pipelines, data models, and initial dashboards built and validated against your real data. Migrations handled without downtime.</div>
        <span class="step-tag">Weeks 3–6</span>
      </div>
      <div class="step">
        <div class="step-num">04</div>
        <div class="step-title">Handover & Training</div>
        <div class="step-desc">Full documentation, runbooks, and role-specific training so your team owns the environment and can extend it independently.</div>
        <span class="step-tag">Week 7–8</span>
      </div>
      <div class="step">
        <div class="step-num">05</div>
        <div class="step-title">Managed Support</div>
        <div class="step-desc">Cost optimisation, performance tuning, new workload onboarding, and a single Armely contact as your Snowflake environment grows.</div>
        <span class="step-tag">Ongoing</span>
      </div>
    </div>
  </div>
</section>

<!-- USE CASES -->
<section class="usecases">
  <div class="section-inner">
    <div class="section-eyebrow">What Businesses Use It For</div>
    <h2 class="section-title">Snowflake in practice — across every industry.</h2>
    <p class="section-body">From consolidating a fragmented data stack to running AI models on live business data, here's what Armely-built Snowflake environments deliver.</p>
    <div class="uc-grid">
      <div class="uc-card">
        <span class="uc-icon">🏢</span>
        <div class="uc-title">Data Warehouse Modernisation</div>
        <div class="uc-desc">Migrate off on-premise SQL Server, Oracle, or Teradata to a fully managed cloud warehouse that scales automatically and costs a fraction of what you're paying today.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">🔗</span>
        <div class="uc-title">Multi-Cloud Data Consolidation</div>
        <div class="uc-desc">Pull data from AWS, Azure, and GCP into a single governed platform. Snowflake runs across all three clouds — your data follows your business, not the other way round.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">🧠</span>
        <div class="uc-title">AI & Machine Learning</div>
        <div class="uc-desc">Use Snowpark to train ML models on your Snowflake data without moving it. Deploy Cortex AI functions to enrich records with sentiment, classification, and LLM completions — all in SQL.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">🤝</span>
        <div class="uc-title">Secure Data Sharing</div>
        <div class="uc-desc">Share live data with partners, suppliers, or customers without copying or moving it. Snowflake's zero-copy sharing means collaborators see the same data you do, in real time.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">⚡</span>
        <div class="uc-title">Real-Time Analytics</div>
        <div class="uc-desc">Snowpipe Streaming and Dynamic Tables ingest and transform data in near real time. Operations, finance, and customer teams get dashboards that reflect what's happening now.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">💬</span>
        <div class="uc-title">Natural Language Queries</div>
        <div class="uc-desc">Snowflake Intelligence lets anyone ask questions in plain English and get answers from your live Snowflake data — no SQL skills needed. The right insight, to the right person, instantly.</div>
      </div>
    </div>
  </div>
</section>

<!-- WHY ARMELY -->
<section class="why" id="why-armely">
  <div class="section-inner">
    <div class="why-two-col">
      <div>
        <div class="section-eyebrow">Why Armely</div>
        <h2 class="section-title">Snowflake expertise, delivered at the pace your business needs.</h2>
        <p class="section-body">We're not a generalist IT firm that dabbles in data. Armely has built data platforms for healthcare, education, and enterprise clients — and we bring that depth to every Snowflake engagement.</p>
        <ul class="why-list">
          <li>
            <div class="why-icon">🎯</div>
            <div>
              <div class="why-item-title">Certified Snowflake Implementors</div>
              <div class="why-item-desc">Our team carries Snowflake implementation certifications and hands-on experience across data engineering, Snowpark, Cortex AI, and dbt — not just SnowPro Core.</div>
            </div>
          </li>
          <li>
            <div class="why-icon">🏥</div>
            <div>
              <div class="why-item-title">Proven in Regulated Industries</div>
              <div class="why-item-desc">We've delivered data projects for Swope Health Systems and UNMC — environments with strict HIPAA and data governance requirements. We know how to build secure by design.</div>
            </div>
          </li>
          <li>
            <div class="why-icon">💰</div>
            <div>
              <div class="why-item-title">Cost Optimisation From Day One</div>
              <div class="why-item-desc">Snowflake bills by compute consumption. We right-size warehouses, implement auto-suspend, and monitor query efficiency so your bill reflects your usage — not our oversight.</div>
            </div>
          </li>
          <li>
            <div class="why-icon">🤝</div>
            <div>
              <div class="why-item-title">You Own Everything</div>
              <div class="why-item-desc">Full documentation, source-controlled pipelines, and team training from day one. We build to hand over — not to create a support dependency.</div>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <div class="partner-block">
          <div class="partner-block-top">
            <div class="partner-label">Snowflake Authorised Partner</div>
            <p class="partner-text">Armely's Snowflake partner status gives us access to technical resources, licensing options, and implementation support that direct customers can't reach. That means better pricing, faster onboarding, and a build backed by Snowflake's own ecosystem.</p>
          </div>
          <div class="partner-stats">
            <div class="p-stat">
              <div class="p-stat-num">10<span>K+</span></div>
              <div class="p-stat-label">organisations running Snowflake globally</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">3</div>
              <div class="p-stat-label">major clouds — AWS, Azure, and Google Cloud</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">50<span>%</span></div>
              <div class="p-stat-label">of Snowflake customers now use Cortex Code for development</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">0</div>
              <div class="p-stat-label">infrastructure to manage — fully serverless SaaS</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
</div>
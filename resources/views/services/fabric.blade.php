<style>
.armely-service-page *, .armely-service-page *::before, .armely-service-page *::after{ box-sizing: border-box; margin: 0; padding: 0; }.armely-service-page{
    --navy:      #FFFFFF;
    --navy-mid:  #F3F6FB;
    --navy-card: #EBF0F8;
    --blue:      #294e8b;
    --blue-lt:   #3d6ab5;
    --blue-dim:  rgba(41,78,139,0.08);
    --blue-dim2: rgba(41,78,139,0.16);
    --white:     #1A2540;
    --off-white: #F5F7FA;
    --text-body: #3D4F6B;
    --text-muted:#6B7FA3;
    --border:    rgba(41,78,139,0.1);
  }.armely-service-page{ scroll-behavior: smooth; }.armely-service-page{
    font-family: 'Poppins', sans-serif;
    background: var(--navy);
    color: var(--text-body);
    line-height: 1.6;
  }.armely-service-page nav{
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    display: flex; justify-content: space-between; align-items: center;
    padding: 18px 56px;
    background: rgba(26,46,82,0.96);
    backdrop-filter: blur(14px);
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }.armely-service-page .logo{ display: flex; align-items: center; gap: 10px; }.armely-service-page .logo-mark{
    width: 36px; height: 36px;
    background: var(--blue);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 1rem; color: #fff;
  }.armely-service-page .logo-text{ font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }.armely-service-page .nav-links{ display: flex; gap: 32px; align-items: center; list-style: none; }.armely-service-page .nav-links a{
    color: rgba(255,255,255,0.6); text-decoration: none;
    font-size: 0.875rem; font-weight: 500; transition: color 0.2s;
  }.armely-service-page .nav-links a:hover{ color: #fff; }.armely-service-page .nav-cta{
    background: var(--blue); color: #fff !important;
    padding: 10px 22px; border-radius: 6px;
    font-size: 0.875rem; font-weight: 600 !important;
    transition: background 0.2s !important;
  }.armely-service-page .nav-cta:hover{ background: var(--blue-lt) !important; }.armely-service-page .hero{
    min-height: 100vh;
    display: flex; flex-direction: column; justify-content: center;
    padding: 140px 56px 100px;
    position: relative; overflow: hidden;
    background: #1a2e52;
  }.armely-service-page .hero-bg-glow{
    position: absolute; top: -180px; right: -100px;
    width: 720px; height: 720px;
    background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%);
    pointer-events: none;
  }.armely-service-page .hero h1{
    font-size: clamp(2.6rem, 5.5vw, 4.8rem);
    font-weight: 800; line-height: 1.08;
    color: #FFFFFF; max-width: 780px;
    margin-bottom: 24px; letter-spacing: -0.03em;
  }.armely-service-page .hero h1 .hl{ color: #FFFFFF; opacity: 0.92; }.armely-service-page .hero-sub{
    font-size: 1.05rem; font-weight: 300;
    color: rgba(255,255,255,0.82); max-width: 540px;
    margin-bottom: 40px; line-height: 1.8;
  }.armely-service-page .hero-eyebrow{ display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }.armely-service-page .eyebrow-badge{
    background: rgba(41,78,139,0.35);
    border: 1px solid rgba(255,255,255,0.2);
    color: rgba(255,255,255,0.9);
    font-size: 0.72rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.12em;
    padding: 5px 14px; border-radius: 40px;
  }.armely-service-page .eyebrow-partner{ font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }.armely-service-page .hero-actions{ display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }.armely-service-page .btn-primary{
    background: var(--blue); color: #fff;
    border: none; border-radius: 7px; padding: 14px 32px;
    font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600;
    cursor: pointer; text-decoration: none;
    transition: background 0.2s, transform 0.15s; display: inline-block;
  }.armely-service-page .btn-primary:hover{ background: var(--blue-lt); transform: translateY(-2px); }.armely-service-page .btn-outline{
    background: transparent; color: rgba(255,255,255,0.85);
    border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px;
    font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500;
    cursor: pointer; text-decoration: none;
    transition: border-color 0.2s, background 0.2s; display: inline-block;
  }.armely-service-page .btn-outline:hover{ border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }.armely-service-page .hero-trust{
    display: flex; gap: 40px; flex-wrap: wrap;
    padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12);
  }.armely-service-page .trust-item{ display: flex; align-items: center; gap: 10px; }.armely-service-page .trust-dot{ width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }.armely-service-page .trust-text{ font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }.armely-service-page .trust-text strong{ color: #fff; font-weight: 600; }.armely-service-page section{ padding: 96px 56px; }.armely-service-page .section-inner{ max-width: 1100px; margin: 0 auto; }.armely-service-page .section-eyebrow{
    font-size: 0.72rem; text-transform: uppercase;
    letter-spacing: 0.14em; color: var(--blue);
    margin-bottom: 14px; font-weight: 600;
  }.armely-service-page .section-title{
    font-size: clamp(1.7rem, 3.2vw, 2.6rem);
    font-weight: 800; color: #1A2540;
    line-height: 1.12; letter-spacing: -0.025em;
    margin-bottom: 18px; max-width: 620px;
  }.armely-service-page .section-body{
    font-size: 0.975rem; font-weight: 300;
    max-width: 540px; line-height: 1.8;
    color: var(--text-body); margin-bottom: 48px;
  }.armely-service-page .intro{ background: var(--navy-mid); }.armely-service-page .intro-grid{ display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }.armely-service-page .workload-grid{ display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 28px; }.armely-service-page .workload-pill{
    background: var(--blue-dim);
    border: 1px solid var(--blue-dim2);
    border-radius: 10px; padding: 12px 16px;
    display: flex; align-items: center; gap: 10px;
  }.armely-service-page .workload-pill-icon{ font-size: 1.1rem; flex-shrink: 0; }.armely-service-page .workload-pill-label{ font-size: 0.8rem; font-weight: 600; color: var(--blue); line-height: 1.3; }.armely-service-page .onelake-callout{
    margin-top: 20px;
    background: var(--blue);
    border-radius: 10px; padding: 16px 20px;
    display: flex; align-items: center; gap: 14px;
  }.armely-service-page .onelake-callout-icon{ font-size: 1.4rem; flex-shrink: 0; }.armely-service-page .onelake-callout-text{ font-size: 0.85rem; color: rgba(255,255,255,0.9); line-height: 1.55; }.armely-service-page .onelake-callout-text strong{ color: #fff; }.armely-service-page .diagram-card{
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px; overflow: hidden;
    box-shadow: 0 4px 24px rgba(41,78,139,0.08);
  }.armely-service-page .diagram-header{
    padding: 16px 22px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 10px;
    background: var(--navy-mid);
  }.armely-service-page .diagram-dots{ display: flex; gap: 6px; }.armely-service-page .diagram-dots span{ width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }.armely-service-page .diagram-title{ font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }.armely-service-page .diagram-body{ padding: 24px; }.armely-service-page .diag-layer{
    border-radius: 10px; padding: 14px 18px;
    margin-bottom: 8px; text-align: center;
  }.armely-service-page .diag-layer-label{ font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }.armely-service-page .diag-layer-items{ display: flex; gap: 6px; flex-wrap: wrap; justify-content: center; }.armely-service-page .diag-chip{
    font-size: 0.72rem; font-weight: 600; padding: 4px 10px;
    border-radius: 20px;
  }.armely-service-page .layer-workloads{ background: rgba(41,78,139,0.07); }.armely-service-page .layer-workloads .diag-layer-label{ color: var(--blue); }.armely-service-page .layer-workloads .diag-chip{ background: var(--blue-dim2); color: var(--blue); }.armely-service-page .layer-platform{ background: rgba(41,78,139,0.12); }.armely-service-page .layer-platform .diag-layer-label{ color: var(--blue); }.armely-service-page .layer-platform .diag-chip{ background: rgba(41,78,139,0.2); color: var(--blue); }.armely-service-page .layer-storage{ background: var(--blue); }.armely-service-page .layer-storage .diag-layer-label{ color: rgba(255,255,255,0.7); }.armely-service-page .layer-storage .diag-chip{ background: rgba(255,255,255,0.15); color: #fff; }.armely-service-page .diag-arrow{ text-align: center; color: var(--text-muted); font-size: 1rem; margin: 4px 0; }.armely-service-page .delivers{ background: var(--navy); }.armely-service-page .delivers-grid{ display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }.armely-service-page .deliver-card{
    background: var(--navy-card);
    border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px;
    transition: border-color 0.2s, transform 0.2s;
  }.armely-service-page .deliver-card:hover{ border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }.armely-service-page .deliver-icon{
    width: 48px; height: 48px; background: var(--blue-dim);
    border: 1px solid var(--blue-dim2); border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; margin-bottom: 20px;
  }.armely-service-page .deliver-title{ font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }.armely-service-page .deliver-desc{ font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }.armely-service-page .journey{ background: var(--navy-mid); }.armely-service-page .steps-row{
    display: grid; grid-template-columns: repeat(5, 1fr);
    gap: 0; margin-top: 56px;
    border: 1px solid var(--border); border-radius: 14px; overflow: hidden;
  }.armely-service-page .step{ padding: 32px 22px; border-right: 1px solid var(--border); }.armely-service-page .step:last-child{ border-right: none; }.armely-service-page .step-num{ font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }.armely-service-page .step-title{ font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }.armely-service-page .step-desc{ font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }.armely-service-page .step-tag{
    display: inline-block; margin-top: 14px;
    background: var(--blue-dim); color: var(--blue);
    font-size: 0.7rem; padding: 3px 10px; border-radius: 4px;
    font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em;
  }.armely-service-page .usecases{ background: var(--navy); }.armely-service-page .uc-grid{ display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }.armely-service-page .uc-card{
    background: var(--navy-card); border: 1px solid var(--border);
    border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s;
  }.armely-service-page .uc-card:hover{ border-color: rgba(41,78,139,0.25); }.armely-service-page .uc-icon{ font-size: 1.6rem; margin-bottom: 14px; display: block; }.armely-service-page .uc-title{ font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }.armely-service-page .uc-desc{ font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }.armely-service-page .why{ background: var(--navy-mid); }.armely-service-page .why-two-col{ display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }.armely-service-page .why-list{ list-style: none; margin-top: 36px; }.armely-service-page .why-list li{ display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }.armely-service-page .why-list li:last-child{ border-bottom: none; }.armely-service-page .why-icon{
    width: 42px; height: 42px; flex-shrink: 0;
    background: var(--blue-dim); border: 1px solid var(--blue-dim2);
    border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
  }.armely-service-page .why-item-title{ font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }.armely-service-page .why-item-desc{ font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }.armely-service-page .partner-block{ background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }.armely-service-page .partner-block-top{ padding: 28px; border-bottom: 1px solid var(--border); }.armely-service-page .partner-label{ font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }.armely-service-page .partner-text{ font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }.armely-service-page .partner-stats{ display: grid; grid-template-columns: 1fr 1fr; }.armely-service-page .p-stat{ padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }.armely-service-page .p-stat:nth-child(2){ border-right: none; }.armely-service-page .p-stat:nth-child(3){ border-bottom: none; }.armely-service-page .p-stat:nth-child(4){ border-right: none; border-bottom: none; }.armely-service-page .p-stat-num{ font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }.armely-service-page .p-stat-num span{ color: var(--blue); }.armely-service-page .p-stat-label{ font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }.armely-service-page .cta-section{ background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }.armely-service-page .cta-inner{
    max-width: 1100px; margin: 0 auto; padding: 96px 56px;
    display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center;
  }.armely-service-page .cta-form{
    background: #FFFFFF; border: 1px solid var(--border);
    border-radius: 14px; padding: 36px 32px;
    box-shadow: 0 4px 24px rgba(41,78,139,0.08);
  }.armely-service-page .form-title{ font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }.armely-service-page .form-sub{ font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }.armely-service-page .form-row{ margin-bottom: 14px; }.armely-service-page .form-row label{
    display: block; font-size: 0.75rem; font-weight: 600;
    color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px;
  }.armely-service-page .form-row input, .armely-service-page .form-row select{
    width: 100%; background: #FFFFFF;
    border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px;
    font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540;
    outline: none; transition: border-color 0.2s;
  }.armely-service-page .form-row input:focus, .armely-service-page .form-row select:focus{ border-color: rgba(41,78,139,0.4); }.armely-service-page .form-row select option{ background: #fff; color: #1A2540; }.armely-service-page .form-submit{
    width: 100%; background: var(--blue); color: #fff;
    border: none; border-radius: 7px; padding: 14px; margin-top: 8px;
    font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600;
    cursor: pointer; transition: background 0.2s;
  }.armely-service-page .form-submit:hover{ background: var(--blue-lt); }.armely-service-page .form-note{ text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }.armely-service-page footer{
    background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08);
    padding: 36px 56px;
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;
  }.armely-service-page .footer-logo-row{ display: flex; align-items: center; gap: 10px; }.armely-service-page .footer-lm{ width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }.armely-service-page .footer-lt{ font-size: 1rem; font-weight: 700; color: #fff; }.armely-service-page .footer-note{ font-size: 0.78rem; color: rgba(255,255,255,0.4); }.armely-service-page .footer-badges{ display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }.armely-service-page .badge-chip{ border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }@media (max-width: 900px){.armely-service-page nav{ padding: 16px 24px; }.armely-service-page .nav-links{ display: none; }.armely-service-page section{ padding: 72px 24px; }.armely-service-page .hero{ padding: 110px 24px 72px; }.armely-service-page .intro-grid, .armely-service-page .why-two-col{ grid-template-columns: 1fr; gap: 40px; }.armely-service-page .delivers-grid, .armely-service-page .uc-grid{ grid-template-columns: 1fr 1fr; }.armely-service-page .steps-row{ grid-template-columns: 1fr; }.armely-service-page .step{ border-right: none; border-bottom: 1px solid var(--border); }.armely-service-page .step:last-child{ border-bottom: none; }.armely-service-page .cta-inner{ grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }.armely-service-page footer{ padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }@media (max-width: 600px){.armely-service-page .delivers-grid, .armely-service-page .uc-grid{ grid-template-columns: 1fr; }.armely-service-page .workload-grid{ grid-template-columns: 1fr; }.armely-service-page .partner-stats{ grid-template-columns: 1fr; }.armely-service-page .hero-trust{ gap: 20px; }
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
<div class="armely-service-page armely-service-page-fabric" data-service="Microsoft Fabric">
<!-- HERO -->
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-eyebrow">
    <span class="eyebrow-badge">Microsoft Fabric</span>
    <span class="eyebrow-partner">Delivered by a certified Microsoft partner</span>
  </div>
  <h1>All your data.<br>One platform.<br><span class="hl">Actual insights.</span></h1>
  <p class="hero-sub">Armely designs, builds, and runs Microsoft Fabric environments that turn scattered business data into dashboards, reports, and decisions — without the usual chaos.</p>
  <div class="hero-actions">
    <a href="#consultation" class="btn-primary">Book a Free Discovery Call</a>
    <a href="#what-we-deliver" class="btn-outline">See What We Build</a>
  </div>
  <div class="hero-trust">
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>One platform</strong> for all data & analytics</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">Replaces <strong>Power BI, Azure Data Factory</strong> & more</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>AI-ready</strong> — Copilot built in</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Partner pricing</strong> available</span>
    </div>
  </div>
</section>

<!-- WHAT IS FABRIC -->
<section class="intro">
  <div class="section-inner">
    <div class="intro-grid">
      <div>
        <div class="section-eyebrow">What is Microsoft Fabric?</div>
        <h2 class="section-title">One platform for every step of your data journey.</h2>
        <p class="section-body">Microsoft Fabric is a unified SaaS analytics platform that replaces the fragmented stack of separate tools most businesses have accumulated. Data engineering, warehousing, real-time analytics, business intelligence, and AI — all in one environment, all sharing a single data foundation called OneLake.</p>
        <div class="workload-grid">
          <div class="workload-pill"><span class="workload-pill-icon">🏭</span><span class="workload-pill-label">Data Factory</span></div>
          <div class="workload-pill"><span class="workload-pill-icon">⚙️</span><span class="workload-pill-label">Data Engineering</span></div>
          <div class="workload-pill"><span class="workload-pill-icon">🏦</span><span class="workload-pill-label">Data Warehouse</span></div>
          <div class="workload-pill"><span class="workload-pill-icon">🔬</span><span class="workload-pill-label">Data Science</span></div>
          <div class="workload-pill"><span class="workload-pill-icon">⚡</span><span class="workload-pill-label">Real-Time Intelligence</span></div>
          <div class="workload-pill"><span class="workload-pill-icon">📊</span><span class="workload-pill-label">Power BI</span></div>
        </div>
        <div class="onelake-callout">
          <span class="onelake-callout-icon">🗄️</span>
          <span class="onelake-callout-text"><strong>OneLake</strong> — one shared data lake underneath it all. Every workload reads from the same source. No duplication, no sync issues, no silos.</span>
        </div>
      </div>
      <div>
        <div class="diagram-card">
          <div class="diagram-header">
            <div class="diagram-dots"><span></span><span></span><span></span></div>
            <span class="diagram-title">Microsoft Fabric Architecture</span>
          </div>
          <div class="diagram-body">
            <div class="diag-layer layer-workloads">
              <div class="diag-layer-label">Workloads</div>
              <div class="diag-layer-items">
                <span class="diag-chip">Power BI</span>
                <span class="diag-chip">Data Factory</span>
                <span class="diag-chip">Data Engineering</span>
                <span class="diag-chip">Data Warehouse</span>
                <span class="diag-chip">Real-Time Intelligence</span>
                <span class="diag-chip">Data Science</span>
              </div>
            </div>
            <div class="diag-arrow">↕</div>
            <div class="diag-layer layer-platform">
              <div class="diag-layer-label">Shared Platform</div>
              <div class="diag-layer-items">
                <span class="diag-chip">Copilot AI</span>
                <span class="diag-chip">Purview Governance</span>
                <span class="diag-chip">Entra ID Security</span>
                <span class="diag-chip">Unified Capacity</span>
              </div>
            </div>
            <div class="diag-arrow">↕</div>
            <div class="diag-layer layer-storage">
              <div class="diag-layer-label">OneLake — Single Storage Layer</div>
              <div class="diag-layer-items">
                <span class="diag-chip">Delta Parquet Format</span>
                <span class="diag-chip">Zero-Copy Access</span>
                <span class="diag-chip">Azure Data Lake Gen2</span>
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
    <h2 class="section-title">We don't just set up Fabric. We make your data work for you.</h2>
    <p class="section-body">As a certified Microsoft partner, Armely handles the full implementation — from architecture design and data migration to dashboards your team will actually use.</p>
    <div class="delivers-grid">
      <div class="deliver-card">
        <div class="deliver-icon">🗺️</div>
        <div class="deliver-title">Data Architecture Design</div>
        <div class="deliver-desc">Before writing a single pipeline, we map your data sources, business questions, and reporting needs. You get an architecture built for your organisation — not a generic template.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🔌</div>
        <div class="deliver-title">Data Integration & Pipelines</div>
        <div class="deliver-desc">We connect your existing systems — ERP, CRM, databases, cloud apps — into Fabric using Data Factory pipelines. Data flows automatically, on schedule, without manual exports.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🏠</div>
        <div class="deliver-title">Lakehouse & Warehouse Build</div>
        <div class="deliver-desc">We design and build your OneLake foundation — Lakehouse or Warehouse depending on your workloads — so all data lives in one place, accessible to every tool in your stack.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">📊</div>
        <div class="deliver-title">Power BI Dashboards & Reports</div>
        <div class="deliver-desc">We build the dashboards your leadership and operations teams will actually open every morning. Designed for clarity, not complexity — with Direct Lake speed on large datasets.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🤖</div>
        <div class="deliver-title">AI & Copilot Integration</div>
        <div class="deliver-desc">Microsoft Fabric has Copilot built in. We configure it so your team can query data, generate reports, and get insights in plain English — no SQL required.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🛡️</div>
        <div class="deliver-title">Governance & Ongoing Support</div>
        <div class="deliver-desc">Data governance through Microsoft Purview, role-based access, data lineage tracking, and a dedicated Armely contact to keep your Fabric environment healthy as your business scales.</div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNEY -->
<section class="journey" id="journey">
  <div class="section-inner">
    <div class="section-eyebrow">The Armely Fabric Journey</div>
    <h2 class="section-title">From scattered spreadsheets to a single source of truth.</h2>
    <p class="section-body">We follow a structured implementation methodology refined across data projects for healthcare, education, and enterprise clients — so nothing gets missed and your data is right from day one.</p>
    <div class="steps-row">
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-title">Discovery & Data Audit</div>
        <div class="step-desc">We map your current data sources, tools, and reporting pain points. Free for new clients — no obligation to proceed.</div>
        <span class="step-tag">Free</span>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-title">Architecture & Licensing</div>
        <div class="step-desc">We design your Fabric architecture and source the right capacity licence at partner pricing for your workload needs.</div>
        <span class="step-tag">1–2 weeks</span>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-title">Build & Integrate</div>
        <div class="step-desc">Pipelines, Lakehouse, data models, and your first Power BI dashboards — built and tested against your real data.</div>
        <span class="step-tag">Weeks 3–6</span>
      </div>
      <div class="step">
        <div class="step-num">04</div>
        <div class="step-title">Handover & Training</div>
        <div class="step-desc">Your team learns to use, maintain, and extend the environment. We document everything so you're never dependent on us.</div>
        <span class="step-tag">Week 7–8</span>
      </div>
      <div class="step">
        <div class="step-num">05</div>
        <div class="step-title">Managed Support</div>
        <div class="step-desc">Ongoing optimisation, new dashboard requests, governance reviews, and a single contact who knows your environment.</div>
        <span class="step-tag">Ongoing</span>
      </div>
    </div>
  </div>
</section>

<!-- USE CASES -->
<section class="usecases">
  <div class="section-inner">
    <div class="section-eyebrow">What Businesses Use It For</div>
    <h2 class="section-title">Real answers to real business questions.</h2>
    <p class="section-body">Fabric isn't just a data tool — it's the foundation for every business decision your team makes. Here's what Armely-built Fabric environments deliver in practice.</p>
    <div class="uc-grid">
      <div class="uc-card">
        <span class="uc-icon">📈</span>
        <div class="uc-title">Executive Dashboards</div>
        <div class="uc-desc">Live KPI dashboards that pull from every system — finance, ops, sales — into a single view. Leadership sees the truth, not last week's export.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">💰</span>
        <div class="uc-title">Financial Reporting</div>
        <div class="uc-desc">Automate month-end reporting, budget vs actual analysis, and cost centre breakdowns. Finance teams reclaim hours previously spent wrangling spreadsheets.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">🏥</span>
        <div class="uc-title">Operational Intelligence</div>
        <div class="uc-desc">Track productivity, resource utilisation, and service delivery in real time. Spot problems before they become complaints — not after.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">🔗</span>
        <div class="uc-title">System Consolidation</div>
        <div class="uc-desc">Connect ERP, CRM, HR, and third-party data into one governed data layer. Stop manually reconciling reports from different systems that disagree.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">🧬</span>
        <div class="uc-title">Predictive Analytics</div>
        <div class="uc-desc">Use Fabric's Data Science workload to build models that forecast demand, flag churn risk, or surface patterns hidden in your historical data.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">💬</span>
        <div class="uc-title">Natural Language Queries</div>
        <div class="uc-desc">With Copilot embedded in Fabric, anyone on your team can ask questions in plain English and get answers from your live data — without touching a report.</div>
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
        <h2 class="section-title">We've done this before — for organisations like yours.</h2>
        <p class="section-body">Armely has delivered data and analytics projects for healthcare providers, universities, and enterprise clients. Microsoft Fabric brings those capabilities to every business — and we know how to make it land.</p>
        <ul class="why-list">
          <li>
            <div class="why-icon">🎯</div>
            <div>
              <div class="why-item-title">Proven Implementation Track Record</div>
              <div class="why-item-desc">We've implemented Microsoft data solutions for Plano ISD, Swope Health Systems, and the University of Nebraska Medical Center — complex environments with real data governance requirements.</div>
            </div>
          </li>
          <li>
            <div class="why-icon">🔒</div>
            <div>
              <div class="why-item-title">Security & Governance Built In</div>
              <div class="why-item-desc">We configure Microsoft Purview, Entra ID access controls, and data lineage from day one — so your Fabric environment is audit-ready, not bolted on later.</div>
            </div>
          </li>
          <li>
            <div class="why-icon">💰</div>
            <div>
              <div class="why-item-title">Right-Sized Licensing</div>
              <div class="why-item-desc">As a Microsoft-authorised CSP partner, we access Fabric capacity pricing and bundle options not available to direct buyers — and we help you start at the right scale, not the biggest.</div>
            </div>
          </li>
          <li>
            <div class="why-icon">🤝</div>
            <div>
              <div class="why-item-title">You Own the Environment</div>
              <div class="why-item-desc">We document everything, train your team, and build so you can manage it yourselves. Our goal is capability transfer — not dependency.</div>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <div class="partner-block">
          <div class="partner-block-top">
            <div class="partner-label">Microsoft Authorised Partner</div>
            <p class="partner-text">Armely's Microsoft partnership gives us access to Fabric licensing, technical resources, and implementation support that independent buyers can't reach. That means better value for you and a faster, cleaner build backed by the full Microsoft ecosystem.</p>
          </div>
          <div class="partner-stats">
            <div class="p-stat">
              <div class="p-stat-num">31<span>K+</span></div>
              <div class="p-stat-label">paying organisations on Microsoft Fabric as of 2026</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">7</div>
              <div class="p-stat-label">unified workloads — one platform, one licence</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">300<span>+</span></div>
              <div class="p-stat-label">data source connectors in Data Factory</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">1</div>
              <div class="p-stat-label">copy of your data — OneLake eliminates duplication</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
</div>
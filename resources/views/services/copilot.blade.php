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
  }.armely-service-page .logo{
    display: flex; align-items: center; gap: 10px;
  }.armely-service-page .logo-mark{
    width: 36px; height: 36px;
    background: var(--blue);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 1rem; color: #fff;
    letter-spacing: -0.03em;
  }.armely-service-page .logo-text{
    font-size: 1.25rem; font-weight: 700;
    color: var(--white); letter-spacing: -0.01em;
  }.armely-service-page .nav-links{
    display: flex; gap: 32px; align-items: center;
    list-style: none;
  }.armely-service-page .nav-links a{
    color: var(--text-muted); text-decoration: none;
    font-size: 0.875rem; font-weight: 500;
    transition: color 0.2s;
  }.armely-service-page .nav-links a:hover{ color: var(--white); }.armely-service-page .nav-cta{
    background: var(--blue);
    color: var(--white) !important;
    padding: 10px 22px;
    border-radius: 6px;
    font-size: 0.875rem; font-weight: 600 !important;
    transition: background 0.2s !important;
  }.armely-service-page .nav-cta:hover{ background: var(--blue-lt) !important; color: var(--white) !important; }.armely-service-page .hero{
    min-height: 100vh;
    display: flex; flex-direction: column; justify-content: center;
    padding: 140px 56px 100px;
    position: relative; overflow: hidden;
    background: #1a2e52;
  }.armely-service-page .hero h1{ color: #FFFFFF; }.armely-service-page .hero h1 .hl{ color: #FFFFFF; }.armely-service-page .hero .hero-sub{ color: rgba(255,255,255,0.82); }.armely-service-page .hero .trust-text{ color: rgba(255,255,255,0.65); }.armely-service-page .hero .trust-text strong{ color: #FFFFFF; }.armely-service-page .hero .hero-trust{ border-top-color: rgba(255,255,255,0.12); }.armely-service-page .hero .trust-dot{ background: rgba(255,255,255,0.5); }.armely-service-page .hero-bg-glow{
    position: absolute; top: -180px; right: -100px;
    width: 720px; height: 720px;
    background: radial-gradient(circle, rgba(41,78,139,0.07) 0%, transparent 68%);
    pointer-events: none;
  }.armely-service-page .hero-bg-glow2{ display: none; }.armely-service-page .hero-eyebrow{
    display: inline-flex; align-items: center; gap: 10px;
    margin-bottom: 24px;
  }.armely-service-page .eyebrow-badge{
    background: var(--blue-dim);
    border: 1px solid var(--blue-dim2);
    color: var(--blue);
    font-size: 0.72rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.12em;
    padding: 5px 14px; border-radius: 40px;
  }.armely-service-page .eyebrow-partner{
    font-size: 0.78rem; color: var(--text-muted);
    font-weight: 400;
  }.armely-service-page .hero h1{
    font-size: clamp(2.6rem, 5.5vw, 4.8rem);
    font-weight: 800;
    line-height: 1.08;
    color: #FFFFFF;
    max-width: 780px;
    margin-bottom: 24px;
    letter-spacing: -0.03em;
  }.armely-service-page .hero h1 .hl{
    color: #FFFFFF;
    opacity: 0.92;
  }.armely-service-page .hero-sub{
    font-size: 1.05rem; font-weight: 300;
    color: var(--text-body);
    max-width: 540px;
    margin-bottom: 40px;
    line-height: 1.8;
  }.armely-service-page .hero-actions{ display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }.armely-service-page .btn-primary{
    background: var(--blue);
    color: var(--white);
    border: none; border-radius: 7px;
    padding: 14px 32px;
    font-family: 'Poppins', sans-serif;
    font-size: 0.95rem; font-weight: 600;
    cursor: pointer; text-decoration: none;
    transition: background 0.2s, transform 0.15s;
    display: inline-block;
  }.armely-service-page .btn-primary:hover{ background: var(--blue-lt); transform: translateY(-2px); }.armely-service-page .btn-outline{
    background: transparent;
    color: var(--blue);
    border: 1px solid rgba(41,78,139,0.25);
    border-radius: 7px;
    padding: 14px 32px;
    font-family: 'Poppins', sans-serif;
    font-size: 0.95rem; font-weight: 500;
    cursor: pointer; text-decoration: none;
    transition: border-color 0.2s, background 0.2s;
    display: inline-block;
  }.armely-service-page .btn-outline:hover{ border-color: rgba(41,78,139,0.5); background: rgba(41,78,139,0.04); }.armely-service-page .hero-trust{
    display: flex; gap: 40px; flex-wrap: wrap;
    padding-top: 40px;
    border-top: 1px solid var(--border);
  }.armely-service-page .trust-item{ display: flex; align-items: center; gap: 10px; }.armely-service-page .trust-dot{ width: 8px; height: 8px; border-radius: 50%; background: var(--blue); flex-shrink: 0; }.armely-service-page .trust-text{ font-size: 0.82rem; color: var(--text-muted); font-weight: 400; }.armely-service-page .trust-text strong{ color: var(--white); font-weight: 600; }.armely-service-page section{ padding: 96px 56px; }.armely-service-page .section-inner{ max-width: 1100px; margin: 0 auto; }.armely-service-page .section-eyebrow{
    font-size: 0.72rem; text-transform: uppercase;
    letter-spacing: 0.14em; color: var(--blue);
    margin-bottom: 14px; font-weight: 600;
  }.armely-service-page .section-title{
    font-size: clamp(1.7rem, 3.2vw, 2.6rem);
    font-weight: 800; color: #1A2540;
    line-height: 1.12; letter-spacing: -0.025em;
    margin-bottom: 18px;
    max-width: 620px;
  }.armely-service-page .section-body{
    font-size: 0.975rem; font-weight: 300;
    max-width: 540px; line-height: 1.8;
    color: var(--text-body); margin-bottom: 48px;
  }.armely-service-page .intro{ background: var(--navy-mid); }.armely-service-page .intro-grid{
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 72px; align-items: center;
  }.armely-service-page .app-pills{ display: flex; flex-wrap: wrap; gap: 10px; margin-top: 28px; }.armely-service-page .pill{
    background: var(--blue-dim);
    border: 1px solid var(--blue-dim2);
    color: var(--blue);
    padding: 6px 16px; border-radius: 40px;
    font-size: 0.8rem; font-weight: 500;
  }.armely-service-page .demo-card{
    background: var(--navy-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
  }.armely-service-page .demo-header{
    padding: 16px 22px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 10px;
  }.armely-service-page .demo-dots{ display: flex; gap: 6px; }.armely-service-page .demo-dots span{
    width: 10px; height: 10px; border-radius: 50%;
    background: rgba(41,78,139,0.15);
  }.armely-service-page .demo-app-name{
    font-size: 0.78rem; font-weight: 600; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: 0.1em;
  }.armely-service-page .demo-body{ padding: 24px; }.armely-service-page .chat-bubble{
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 12px;
    font-size: 0.85rem; line-height: 1.65;
  }.armely-service-page .chat-bubble.user{
    background: var(--blue-dim);
    border-left: 3px solid var(--blue);
    color: var(--blue);
  }.armely-service-page .chat-bubble.copilot{
    background: rgba(41,78,139,0.04);
    border-left: 3px solid rgba(41,78,139,0.2);
    color: var(--text-body);
  }.armely-service-page .bubble-label{
    font-size: 0.67rem; text-transform: uppercase;
    letter-spacing: 0.1em; font-weight: 700;
    margin-bottom: 6px;
  }.armely-service-page .bubble-label.u{ color: var(--blue); }.armely-service-page .bubble-label.c{ color: var(--text-muted); }.armely-service-page .delivers{ background: var(--navy); }.armely-service-page .delivers-grid{
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 20px; margin-top: 48px;
  }.armely-service-page .deliver-card{
    background: var(--navy-card);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 32px 26px;
    transition: border-color 0.2s, transform 0.2s;
  }.armely-service-page .deliver-card:hover{ border-color: var(--blue-dim2); transform: translateY(-3px); }.armely-service-page .deliver-icon{
    width: 48px; height: 48px;
    background: var(--blue-dim);
    border: 1px solid var(--blue-dim2);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; margin-bottom: 20px;
  }.armely-service-page .deliver-title{
    font-size: 1rem; font-weight: 700;
    color: #1A2540; margin-bottom: 10px;
  }.armely-service-page .deliver-desc{
    font-size: 0.875rem; line-height: 1.7;
    color: var(--text-body);
  }.armely-service-page .journey{ background: var(--navy-mid); }.armely-service-page .steps-row{
    display: grid; grid-template-columns: repeat(5, 1fr);
    gap: 0; margin-top: 56px;
    border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
  }.armely-service-page .step{
    padding: 32px 22px;
    border-right: 1px solid var(--border);
    position: relative;
  }.armely-service-page .step:last-child{ border-right: none; }.armely-service-page .step-num{
    font-size: 2.4rem; font-weight: 800;
    color: rgba(41,78,139,0.18); line-height: 1;
    margin-bottom: 14px;
  }.armely-service-page .step-title{
    font-size: 0.95rem; font-weight: 700;
    color: #1A2540; margin-bottom: 10px;
  }.armely-service-page .step-desc{ font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }.armely-service-page .step-tag{
    display: inline-block; margin-top: 14px;
    background: var(--blue-dim);
    color: var(--blue);
    font-size: 0.7rem; padding: 3px 10px;
    border-radius: 4px; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.06em;
  }.armely-service-page .usecases{ background: var(--navy); }.armely-service-page .uc-grid{
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 18px; margin-top: 48px;
  }.armely-service-page .uc-card{
    background: var(--navy-card);
    border: 1px solid var(--border);
    border-radius: 12px; padding: 28px 24px;
    transition: border-color 0.2s;
  }.armely-service-page .uc-card:hover{ border-color: rgba(41,78,139,0.35); }.armely-service-page .uc-icon{ font-size: 1.6rem; margin-bottom: 14px; display: block; }.armely-service-page .uc-title{
    font-size: 0.95rem; font-weight: 700;
    color: #1A2540; margin-bottom: 8px;
  }.armely-service-page .uc-desc{ font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }.armely-service-page .why{ background: var(--navy-mid); }.armely-service-page .why-two-col{
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 72px; align-items: start;
  }.armely-service-page .why-list{ list-style: none; margin-top: 36px; }.armely-service-page .why-list li{
    display: flex; gap: 16px;
    padding: 20px 0;
    border-bottom: 1px solid var(--border);
  }.armely-service-page .why-list li:last-child{ border-bottom: none; }.armely-service-page .why-icon{
    width: 42px; height: 42px; flex-shrink: 0;
    background: var(--blue-dim);
    border: 1px solid rgba(41,78,139,0.2);
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
  }.armely-service-page .why-item-title{ font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }.armely-service-page .why-item-desc{ font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }.armely-service-page .partner-block{
    margin-top: 0;
    background: var(--navy-card);
    border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
  }.armely-service-page .partner-block-top{
    padding: 28px;
    border-bottom: 1px solid var(--border);
  }.armely-service-page .partner-label{
    font-size: 0.68rem; text-transform: uppercase;
    letter-spacing: 0.14em; color: var(--blue); font-weight: 700;
    margin-bottom: 10px;
  }.armely-service-page .partner-text{ font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }.armely-service-page .partner-stats{
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 0;
  }.armely-service-page .p-stat{
    padding: 24px 28px;
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
  }.armely-service-page .p-stat:nth-child(2){ border-right: none; }.armely-service-page .p-stat:nth-child(3){ border-bottom: none; }.armely-service-page .p-stat:nth-child(4){ border-right: none; border-bottom: none; }.armely-service-page .p-stat-num{
    font-size: 1.8rem; font-weight: 800;
    color: #1A2540; line-height: 1;
    margin-bottom: 4px;
  }.armely-service-page .p-stat-num span{ color: var(--blue); }.armely-service-page .p-stat-label{ font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }.armely-service-page .cta-section{
    background: var(--navy-card);
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
  }.armely-service-page .cta-inner{
    max-width: 1100px; margin: 0 auto;
    padding: 96px 56px;
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 72px; align-items: center;
  }.armely-service-page .cta-form{
    background: #FFFFFF;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 36px 32px;
  }.armely-service-page .form-title{
    font-size: 1.1rem; font-weight: 700;
    color: #1A2540; margin-bottom: 6px;
  }.armely-service-page .form-sub{ font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }.armely-service-page .form-row{ margin-bottom: 14px; }.armely-service-page .form-row label{
    display: block; font-size: 0.75rem; font-weight: 600;
    color: var(--text-muted); text-transform: uppercase;
    letter-spacing: 0.08em; margin-bottom: 6px;
  }.armely-service-page .form-row input, .armely-service-page .form-row select{
    width: 100%; background: #FFFFFF;
    border: 1px solid rgba(41,78,139,0.15);
    border-radius: 7px; padding: 11px 14px;
    font-family: 'Poppins', sans-serif;
    font-size: 0.875rem; color: #1A2540;
    outline: none;
    transition: border-color 0.2s;
  }.armely-service-page .form-row input:focus, .armely-service-page .form-row select:focus{
    border-color: rgba(41,78,139,0.4);
  }.armely-service-page .form-row select option{ background: #FFFFFF; color: #1A2540; }.armely-service-page .form-submit{
    width: 100%; background: var(--blue);
    color: var(--white); border: none; border-radius: 7px;
    padding: 14px; margin-top: 8px;
    font-family: 'Poppins', sans-serif;
    font-size: 0.95rem; font-weight: 600;
    cursor: pointer; transition: background 0.2s;
  }.armely-service-page .form-submit:hover{ background: var(--blue-lt); }.armely-service-page .form-note{
    text-align: center; margin-top: 12px;
    font-size: 0.75rem; color: var(--text-muted);
  }.armely-service-page footer{
    background: #1a2e52;
    border-top: 1px solid rgba(255,255,255,0.08);
    padding: 36px 56px;
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 16px;
  }.armely-service-page .footer-logo-row{ display: flex; align-items: center; gap: 10px; }.armely-service-page .footer-lm{
    width: 30px; height: 30px; background: var(--blue);
    border-radius: 6px; display: flex; align-items: center;
    justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff;
  }.armely-service-page .footer-lt{ font-size: 1rem; font-weight: 700; color: #FFFFFF; }.armely-service-page .footer-note{ font-size: 0.78rem; color: rgba(255,255,255,0.45); }.armely-service-page .footer-badges{
    display: flex; gap: 16px; align-items: center;
    flex-wrap: wrap;
  }.armely-service-page .badge-chip{
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 6px; padding: 5px 12px;
    font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500;
  }@media (max-width: 900px){.armely-service-page nav{ padding: 16px 24px; }.armely-service-page .nav-links{ display: none; }.armely-service-page section{ padding: 72px 24px; }.armely-service-page .hero{ padding: 110px 24px 72px; }.armely-service-page .intro-grid, .armely-service-page .why-two-col{ grid-template-columns: 1fr; gap: 40px; }.armely-service-page .delivers-grid, .armely-service-page .uc-grid{ grid-template-columns: 1fr 1fr; }.armely-service-page .steps-row{ grid-template-columns: 1fr; }.armely-service-page .step{ border-right: none; border-bottom: 1px solid var(--border); }.armely-service-page .step:last-child{ border-bottom: none; }.armely-service-page .cta-inner{ grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }.armely-service-page footer{ padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }@media (max-width: 600px){.armely-service-page .delivers-grid, .armely-service-page .uc-grid{ grid-template-columns: 1fr; }.armely-service-page .partner-stats{ grid-template-columns: 1fr; }.armely-service-page .hero-trust{ gap: 20px; }
  }@media (prefers-reduced-motion: reduce){.armely-service-page *{ transition: none !important; animation: none !important; }
  }

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
<div class="armely-service-page armely-service-page-copilot" data-service="Microsoft Copilot">
<!-- HERO -->
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-bg-glow2"></div>
  <div class="hero-eyebrow">
    <span class="eyebrow-badge">Microsoft 365 Copilot Business</span>
    <span class="eyebrow-partner">Delivered by a certified Microsoft partner</span>
  </div>
  <h1>AI that works<br>the way <span class="hl">your business</span><br>actually works.</h1>
  <p class="hero-sub">Armely licences, deploys, and embeds Microsoft 365 Copilot into your team's daily workflows — so adoption is real, not just access.</p>
  <div class="hero-actions">
    <a href="#consultation" class="btn-primary">Book a Free Assessment</a>
    <a href="#what-we-deliver" class="btn-outline">See What We Do</a>
  </div>
  <div class="hero-trust">
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Full Microsoft 365</strong> Copilot feature set</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">Works with <strong>any existing</strong> M365 Business plan</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>No seat minimums</strong> — start with one team</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">Exclusive <strong>partner pricing</strong> available</span>
    </div>
  </div>
</section>

<!-- WHAT IS COPILOT -->
<section class="intro">
  <div class="section-inner">
    <div class="intro-grid">
      <div>
        <div class="section-eyebrow">What is Microsoft 365 Copilot?</div>
        <h2 class="section-title">AI built into the tools your team already uses every day.</h2>
        <p class="section-body">Microsoft 365 Copilot Business is an AI assistant woven directly into Word, Excel, PowerPoint, Outlook, and Teams. It drafts, summarises, analyses, and responds — freeing your people from admin so they can focus on the work that matters.</p>
        <div class="app-pills">
          <span class="pill">Word</span>
          <span class="pill">Excel</span>
          <span class="pill">PowerPoint</span>
          <span class="pill">Outlook</span>
          <span class="pill">Teams</span>
          <span class="pill">M365 Chat</span>
        </div>
      </div>
      <div>
        <div class="demo-card">
          <div class="demo-header">
            <div class="demo-dots"><span></span><span></span><span></span></div>
            <span class="demo-app-name">Copilot in Outlook</span>
          </div>
          <div class="demo-body">
            <div class="chat-bubble user">
              <div class="bubble-label u">You</div>
              Summarise last week's project emails and flag anything that needs a reply today.
            </div>
            <div class="chat-bubble copilot">
              <div class="bubble-label c">Copilot</div>
              Found 14 relevant threads. Two need replies: a contract review from Sarah due Friday, and a vendor quote requiring sign-off. The rest are informational — here's a 3-line summary of each.
            </div>
            <div class="chat-bubble user">
              <div class="bubble-label u">You</div>
              Draft a reply to Sarah — professional but concise.
            </div>
            <div class="chat-bubble copilot">
              <div class="bubble-label c">Copilot</div>
              Done. Draft is in your compose window. Review and send when ready.
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
    <h2 class="section-title">We don't just sell licences. We make Copilot work for your business.</h2>
    <p class="section-body">As a certified Microsoft partner, Armely handles the full picture — from securing the best licensing deal to building the habits that make AI stick.</p>
    <div class="delivers-grid">
      <div class="deliver-card">
        <div class="deliver-icon">🔍</div>
        <div class="deliver-title">Readiness Assessment</div>
        <div class="deliver-desc">Before a single licence is activated, we audit your Microsoft 365 environment, data governance, permissions, and security posture. Copilot lands on a clean, safe foundation — not into a messy tenant.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🏷️</div>
        <div class="deliver-title">Best-Value Licensing</div>
        <div class="deliver-desc">Our Microsoft partnership gives us access to SMB bundle pricing and promotional offers that aren't available through direct purchase. We find the right plan for your team size and budget — often at a significant discount.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">⚙️</div>
        <div class="deliver-title">Hands-On Implementation</div>
        <div class="deliver-desc">We don't hand you a login and a help link. Our engineers configure Copilot for your specific workflows, integrate it with your existing systems, and run role-by-role deployment so every team hits the ground running.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🎓</div>
        <div class="deliver-title">Adoption Training</div>
        <div class="deliver-desc">People use tools they understand. We run targeted training sessions for each department — showing your team exactly how Copilot accelerates their specific work, not just a generic demo.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">📊</div>
        <div class="deliver-title">Adoption Tracking</div>
        <div class="deliver-desc">Usage reports, quarterly business reviews, and proactive check-ins mean we catch low adoption early and fix it before licences go to waste. You always know your Copilot ROI.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🛡️</div>
        <div class="deliver-title">Ongoing Managed Support</div>
        <div class="deliver-desc">One dedicated account manager who knows your environment. Not a ticket queue. As your business grows and Microsoft updates Copilot, we keep you ahead of the curve.</div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNEY -->
<section class="journey" id="journey">
  <div class="section-inner">
    <div class="section-eyebrow">The Armely QuickStart Journey</div>
    <h2 class="section-title">From first conversation to full productivity — fast.</h2>
    <p class="section-body">We follow the Microsoft 365 Copilot QuickStart framework, refined across hundreds of SMB deployments, to get your team seeing real value in weeks not months.</p>
    <div class="steps-row">
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-title">Discovery & Assessment</div>
        <div class="step-desc">Free environment audit covering your current M365 setup, data hygiene, and security posture.</div>
        <span class="step-tag">Free</span>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-title">Licensing & Planning</div>
        <div class="step-desc">We source the best bundle and pricing, then build a deployment plan tailored to your teams and workflows.</div>
        <span class="step-tag">1–2 days</span>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-title">Pilot Rollout</div>
        <div class="step-desc">Start with a target team. Real workflows, real feedback, measurable results before going organisation-wide.</div>
        <span class="step-tag">Week 1–2</span>
      </div>
      <div class="step">
        <div class="step-num">04</div>
        <div class="step-title">Full Deployment</div>
        <div class="step-desc">Scale across the organisation with role-specific training and Armely managing every step of rollout.</div>
        <span class="step-tag">Week 3–4</span>
      </div>
      <div class="step">
        <div class="step-num">05</div>
        <div class="step-title">Continuous Success</div>
        <div class="step-desc">Monthly usage reviews, proactive support, and ongoing optimisation as your team and Microsoft's AI evolve.</div>
        <span class="step-tag">Ongoing</span>
      </div>
    </div>
  </div>
</section>

<!-- USE CASES -->
<section class="usecases">
  <div class="section-inner">
    <div class="section-eyebrow">What Your Team Will Actually Do With It</div>
    <h2 class="section-title">Real work, done faster across every role.</h2>
    <p class="section-body">Copilot Business delivers measurable time savings across every department — from operations and finance to sales and leadership.</p>
    <div class="uc-grid">
      <div class="uc-card">
        <span class="uc-icon">📧</span>
        <div class="uc-title">Email & Communications</div>
        <div class="uc-desc">Copilot in Outlook summarises long threads, drafts replies in your tone, and flags what genuinely needs attention. Inbox zero is no longer a myth.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">📊</span>
        <div class="uc-title">Data & Reporting</div>
        <div class="uc-desc">Ask Copilot in Excel to analyse a spreadsheet, spot anomalies, or build a summary chart — in plain English. No formula expertise required.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">🎤</span>
        <div class="uc-title">Meetings & Follow-ups</div>
        <div class="uc-desc">Copilot in Teams transcribes, summarises, and extracts action items from every meeting. Stop taking notes and actually contribute.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">📝</span>
        <div class="uc-title">Documents & Proposals</div>
        <div class="uc-desc">Turn bullet points into polished proposals in Word. Summarise a 40-page report into a one-page brief. First drafts in seconds, not hours.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">🎨</span>
        <div class="uc-title">Presentations</div>
        <div class="uc-desc">Copilot in PowerPoint builds structured slide decks from a document or prompt — branded and ready for your edits, not built from scratch under pressure.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">🔍</span>
        <div class="uc-title">Knowledge & Search</div>
        <div class="uc-desc">Microsoft 365 Chat searches across all your files, emails, and chats to surface what you need instantly. No more digging through folders or asking colleagues.</div>
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
        <h2 class="section-title">The difference between access and adoption.</h2>
        <p class="section-body">Most businesses activate Copilot and wonder why nobody's using it six months later. We're built specifically to prevent that — combining licensing expertise, technical implementation, and hands-on change management.</p>
        <ul class="why-list">
          <li>
            <div class="why-icon">🎯</div>
            <div>
              <div class="why-item-title">Certified Copilot Implementors</div>
              <div class="why-item-desc">Our engineers are Microsoft-certified Copilot implementors — trained in Copilot Practice Builder methodology, CloudLabs deployment, and hands-on change management across SMB environments.</div>
            </div>
          </li>
          <li>
            <div class="why-icon">🔒</div>
            <div>
              <div class="why-item-title">Security-First by Default</div>
              <div class="why-item-desc">AI introduces new data exposure risks. We assess and harden your environment before go-live so Copilot runs securely inside your existing Microsoft 365 tenant — your data never leaves.</div>
            </div>
          </li>
          <li>
            <div class="why-icon">💰</div>
            <div>
              <div class="why-item-title">Access to Partner-Only Pricing</div>
              <div class="why-item-desc">As a Microsoft-authorised CSP partner, we access SMB bundle promotions and volume pricing that aren't available to direct buyers — and we pass those savings on to you.</div>
            </div>
          </li>
          <li>
            <div class="why-icon">📈</div>
            <div>
              <div class="why-item-title">Proven SMB Track Record</div>
              <div class="why-item-desc">We've implemented Microsoft solutions for organisations including Plano ISD, Swope Health Systems, and UNMC — bringing enterprise-grade delivery to businesses of every size.</div>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <div class="partner-block">
          <div class="partner-block-top">
            <div class="partner-label">Microsoft Authorised Partner</div>
            <p class="partner-text">Armely's Microsoft partnership gives us access to licensing, technical resources, and bundle pricing that independent buyers can't reach. That means better value for you, faster deployment, and support backed by the full Microsoft ecosystem.</p>
          </div>
          <div class="partner-stats">
            <div class="p-stat">
              <div class="p-stat-num">70<span>%</span></div>
              <div class="p-stat-label">of Fortune 500 already using Microsoft Copilot</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">300</div>
              <div class="p-stat-label">user maximum — purpose-built for SMBs</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">6</div>
              <div class="p-stat-label">Microsoft 365 apps with native Copilot integration</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">87<span>%</span></div>
              <div class="p-stat-label">of organisations say AI gives a competitive edge</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA / CONTACT -->
</div>
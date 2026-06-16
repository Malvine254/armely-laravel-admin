@section('title', 'Microsoft Managed Services | Armely')

<style>


.armely-managed-services-page *, .armely-managed-services-page *::before, .armely-managed-services-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-managed-services-page {
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
  }

.armely-managed-services-page { scroll-behavior: smooth; }
.armely-managed-services-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* NAV */
.armely-managed-services-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-managed-services-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-managed-services-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-managed-services-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-managed-services-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-managed-services-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-managed-services-page .nav-links a:hover { color: #fff; }
.armely-managed-services-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-managed-services-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* HERO */
.armely-managed-services-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-managed-services-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-managed-services-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-managed-services-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-managed-services-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-managed-services-page .hero h1 { font-size: clamp(1.75rem, 3.2vw, 2.7rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-managed-services-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-managed-services-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.8; }
.armely-managed-services-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-managed-services-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-managed-services-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-managed-services-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-managed-services-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-managed-services-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-managed-services-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-managed-services-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-managed-services-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-managed-services-page .trust-text strong { color: #fff; font-weight: 600; }

  /* SECTIONS */
.armely-managed-services-page section { padding: 96px 56px; }
.armely-managed-services-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-managed-services-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-managed-services-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 640px; }
.armely-managed-services-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* AI SPECTRUM */
.armely-managed-services-page .spectrum { background: var(--navy-mid); }
.armely-managed-services-page .spectrum-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-managed-services-page .spectrum-row { display: flex; flex-direction: column; gap: 10px; margin-top: 32px; }
.armely-managed-services-page .spectrum-level { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; transition: border-color 0.2s; }
.armely-managed-services-page .spectrum-level:hover { border-color: rgba(41,78,139,0.3); }
.armely-managed-services-page .spectrum-level.highlight { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-managed-services-page .spectrum-num { font-size: 0.68rem; font-weight: 800; color: var(--blue); background: var(--blue-dim2); border-radius: 4px; padding: 2px 7px; flex-shrink: 0; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.08em; }
.armely-managed-services-page .spectrum-content-title { font-size: 0.875rem; font-weight: 700; color: #1A2540; margin-bottom: 3px; }
.armely-managed-services-page .spectrum-content-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }

  /* Platform visual */
.armely-managed-services-page .platform-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-managed-services-page .platform-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-managed-services-page .platform-dots { display: flex; gap: 6px; }
.armely-managed-services-page .platform-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-managed-services-page .platform-header-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-managed-services-page .platform-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.armely-managed-services-page .plat-band { border-radius: 9px; padding: 13px 16px; }
.armely-managed-services-page .plat-band-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
.armely-managed-services-page .plat-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.armely-managed-services-page .plat-chip { font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.armely-managed-services-page .band-tools { background: var(--blue-dim); }
.armely-managed-services-page .band-tools .plat-band-label { color: var(--blue); }
.armely-managed-services-page .band-tools .plat-chip { background: var(--blue-dim2); color: var(--blue); }
.armely-managed-services-page .band-data { background: rgba(41,78,139,0.05); }
.armely-managed-services-page .band-data .plat-band-label { color: var(--blue); }
.armely-managed-services-page .band-data .plat-chip { background: rgba(41,78,139,0.1); color: var(--blue); }
.armely-managed-services-page .band-gov { background: var(--blue); }
.armely-managed-services-page .band-gov .plat-band-label { color: rgba(255,255,255,0.7); }
.armely-managed-services-page .band-gov .plat-chip { background: rgba(255,255,255,0.15); color: #fff; }
.armely-managed-services-page .band-arrow { text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2px 0; }

  /* VIBE CODING CALLOUT */
.armely-managed-services-page .vibe-section { background: var(--navy); }
.armely-managed-services-page .vibe-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: start; }
.armely-managed-services-page .vibe-left { }
.armely-managed-services-page .vibe-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-managed-services-page .vibe-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
.armely-managed-services-page .vibe-card-icon { font-size: 1.4rem; }
.armely-managed-services-page .vibe-card-title { font-size: 1rem; font-weight: 700; color: #1A2540; }
.armely-managed-services-page .vibe-card-subtitle { font-size: 0.78rem; color: var(--text-muted); }
.armely-managed-services-page .vibe-card-body { padding: 24px; }
.armely-managed-services-page .vibe-risk { display: flex; align-items: flex-start; gap: 12px; padding: 14px 0; border-bottom: 1px solid var(--border); }
.armely-managed-services-page .vibe-risk:last-child { border-bottom: none; }
.armely-managed-services-page .vibe-risk-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
.armely-managed-services-page .vibe-risk-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 2px; }
.armely-managed-services-page .vibe-risk-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }
.armely-managed-services-page .vibe-right { }
.armely-managed-services-page .vibe-answer-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 28px; margin-bottom: 12px; }
.armely-managed-services-page .vibe-answer-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--blue); margin-bottom: 10px; }
.armely-managed-services-page .vibe-answer-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.75; }

  /* DELIVERS */
.armely-managed-services-page .delivers { background: var(--navy-mid); }
.armely-managed-services-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-managed-services-page .deliver-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-managed-services-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-managed-services-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-managed-services-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-managed-services-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-managed-services-page .journey { background: var(--navy); }
.armely-managed-services-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-managed-services-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-managed-services-page .step:last-child { border-right: none; }
.armely-managed-services-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-managed-services-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-managed-services-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-managed-services-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* USE CASES */
.armely-managed-services-page .usecases { background: var(--navy-mid); }
.armely-managed-services-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-managed-services-page .uc-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-managed-services-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-managed-services-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-managed-services-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-managed-services-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* TESTIMONIALS */
.armely-managed-services-page .testimonials { background: var(--navy); padding: 96px 56px; }
.armely-managed-services-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-managed-services-page .testi-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-managed-services-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-managed-services-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-managed-services-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-managed-services-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-managed-services-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-managed-services-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-managed-services-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY ARMELY */
.armely-managed-services-page .why { background: var(--navy-mid); }
.armely-managed-services-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-managed-services-page .why-list { list-style: none; margin-top: 36px; }
.armely-managed-services-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-managed-services-page .why-list li:last-child { border-bottom: none; }
.armely-managed-services-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-managed-services-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-managed-services-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-managed-services-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-managed-services-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-managed-services-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-managed-services-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-managed-services-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-managed-services-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-managed-services-page .p-stat:nth-child(2) { border-right: none; }
.armely-managed-services-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-managed-services-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-managed-services-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-managed-services-page .p-stat-num span { color: var(--blue); }
.armely-managed-services-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* CTA */
.armely-managed-services-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-managed-services-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-managed-services-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-managed-services-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-managed-services-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-managed-services-page .form-row { margin-bottom: 14px; }
.armely-managed-services-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-managed-services-page .form-row input, .armely-managed-services-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-managed-services-page .form-row input:focus, .armely-managed-services-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-managed-services-page .form-row select option { background: #fff; color: #1A2540; }
.armely-managed-services-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-managed-services-page .form-submit:hover { background: var(--blue-lt); }
.armely-managed-services-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* FOOTER */
.armely-managed-services-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-managed-services-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-managed-services-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-managed-services-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-managed-services-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-managed-services-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-managed-services-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* RESPONSIVE */
  @media (max-width: 900px) {
.armely-managed-services-page nav { padding: 16px 24px; }
.armely-managed-services-page .nav-links { display: none; }
.armely-managed-services-page section { padding: 72px 24px; }
.armely-managed-services-page .hero { padding: 110px 24px 72px; }
.armely-managed-services-page .spectrum-grid, .armely-managed-services-page .vibe-two-col, .armely-managed-services-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-managed-services-page .delivers-grid, .armely-managed-services-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-managed-services-page .steps-row { grid-template-columns: 1fr; }
.armely-managed-services-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-managed-services-page .step:last-child { border-bottom: none; }
.armely-managed-services-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-managed-services-page .testimonials { padding: 72px 24px; }
.armely-managed-services-page .testi-grid { grid-template-columns: 1fr; }
.armely-managed-services-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-managed-services-page .delivers-grid, .armely-managed-services-page .uc-grid { grid-template-columns: 1fr; }
.armely-managed-services-page .partner-stats { grid-template-columns: 1fr; }
.armely-managed-services-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-managed-services-page {
  --navy: #ffffff;
  --navy-mid: #f5f8fc;
  --navy-card: #ffffff;
  --blue: #2f5597;
  --blue-lt: #4477bd;
  --blue-dim: rgba(47, 85, 151, 0.09);
  --blue-dim2: rgba(47, 85, 151, 0.18);
  --text-body: #334155;
  --text-muted: #667085;
  --border: rgba(47, 85, 151, 0.14);
}
.armely-managed-services-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-managed-services-page .hero::after {
  content: '';
  position: absolute;
  inset: auto 8% 8% auto;
  width: min(340px, 48vw);
  height: min(340px, 48vw);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.10);
  filter: blur(2px);
  pointer-events: none;
}
.armely-managed-services-page .section-title,
.armely-managed-services-page .deliver-title,
.armely-managed-services-page .uc-title,
.armely-managed-services-page .step-title,
.armely-managed-services-page .why-item-title,
.armely-managed-services-page .form-title {
  color: #162b49;
}
.armely-managed-services-page .deliver-card,
.armely-managed-services-page .uc-card,
.armely-managed-services-page .testi-card,
.armely-managed-services-page .platform-card,
.armely-managed-services-page .partner-block,
.armely-managed-services-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-managed-services-page .deliver-card:hover,
.armely-managed-services-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-managed-services-page .btn-primary,
.armely-managed-services-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-managed-services-page .btn-primary:hover,
.armely-managed-services-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-managed-services-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-managed-services-page nav,
.armely-managed-services-page footer {
  display: none;
}


/* Modern layout update: tighter first section and SVG icon system */
.armely-managed-services-page .hero {
  min-height: auto;
  padding: 128px 32px 86px;
  isolation: isolate;
}
.armely-managed-services-page .hero-inner {
  width: min(1160px, 100%);
  margin: 0 auto;
  display: grid;
  grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.75fr);
  align-items: center;
  gap: 56px;
  position: relative;
  z-index: 1;
}
.armely-managed-services-page .hero-copy { max-width: 760px; }
.armely-managed-services-page .hero h1 { max-width: 760px; margin-bottom: 20px; }
.armely-managed-services-page .hero-sub { max-width: 640px; margin-bottom: 30px; font-size: 1.02rem; line-height: 1.72; }
.armely-managed-services-page .hero-actions { margin-bottom: 34px; }
.armely-managed-services-page .hero-trust {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  padding-top: 0;
  border-top: 0;
  max-width: 720px;
}
.armely-managed-services-page .hero .trust-item {
  align-items: flex-start;
  background: rgba(255,255,255,0.10);
  border: 1px solid rgba(255,255,255,0.16);
  border-radius: 14px;
  padding: 14px 15px;
  backdrop-filter: blur(10px);
}
.armely-managed-services-page .hero .trust-dot {
  width: 22px;
  height: 22px;
  border-radius: 999px;
  background: rgba(255,255,255,0.16);
  border: 1px solid rgba(255,255,255,0.24);
  position: relative;
  margin-top: 1px;
}
.armely-managed-services-page .hero .trust-dot::after {
  content: '';
  position: absolute;
  left: 7px;
  top: 5px;
  width: 6px;
  height: 10px;
  border: solid #fff;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}
.armely-managed-services-page .hero .trust-text { color: rgba(255,255,255,0.78); line-height: 1.5; }
.armely-managed-services-page .hero-visual {
  min-height: 420px;
  border-radius: 28px;
  position: relative;
  background: linear-gradient(145deg, rgba(255,255,255,0.16), rgba(255,255,255,0.05));
  border: 1px solid rgba(255,255,255,0.18);
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.18), 0 28px 70px rgba(6, 22, 48, 0.24);
  overflow: hidden;
}
.armely-managed-services-page .hero-visual::before {
  content: '';
  position: absolute;
  inset: 36px;
  border: 1px solid rgba(255,255,255,0.14);
  border-radius: 999px;
}
.armely-managed-services-page .hero-visual::after {
  content: '';
  position: absolute;
  width: 190px;
  height: 190px;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  border-radius: 999px;
  background: radial-gradient(circle, rgba(255,255,255,0.24), rgba(255,255,255,0.06));
  border: 1px solid rgba(255,255,255,0.22);
}
.armely-managed-services-page .hero-orbit span {
  position: absolute;
  width: 52px;
  height: 52px;
  border-radius: 16px;
  background: rgba(255,255,255,0.14);
  border: 1px solid rgba(255,255,255,0.22);
}
.armely-managed-services-page .hero-orbit span:nth-child(1) { left: 56px; top: 88px; }
.armely-managed-services-page .hero-orbit span:nth-child(2) { right: 72px; top: 138px; }
.armely-managed-services-page .hero-orbit span:nth-child(3) { left: 48%; bottom: 76px; }
.armely-managed-services-page .hero-visual-card {
  position: absolute;
  z-index: 2;
  width: 230px;
  padding: 18px;
  border-radius: 18px;
  background: rgba(255,255,255,0.92);
  box-shadow: 0 24px 54px rgba(9, 31, 63, 0.22);
}
.armely-managed-services-page .hero-visual-card.top { top: 42px; right: 30px; }
.armely-managed-services-page .hero-visual-card.bottom { bottom: 46px; left: 30px; }
.armely-managed-services-page .hero-visual-label {
  display: block;
  font-size: 0.78rem;
  font-weight: 700;
  color: #162b49;
  margin-bottom: 12px;
}
.armely-managed-services-page .hero-visual-line {
  display: block;
  height: 8px;
  width: 100%;
  border-radius: 999px;
  background: rgba(47, 85, 151, 0.16);
  margin-top: 8px;
}
.armely-managed-services-page .hero-visual-line.short { width: 68%; }
.armely-managed-services-page .icon-svg {
  width: 22px;
  height: 22px;
  display: block;
  color: var(--blue);
  fill: none;
  stroke: currentColor;
  stroke-width: 2;
  stroke-linecap: round;
  stroke-linejoin: round;
}
.armely-managed-services-page .vibe-card-icon,
.armely-managed-services-page .vibe-risk-icon,
.armely-managed-services-page .deliver-icon,
.armely-managed-services-page .uc-icon,
.armely-managed-services-page .why-icon {
  color: var(--blue);
}
.armely-managed-services-page .vibe-card-icon,
.armely-managed-services-page .vibe-risk-icon {
  width: 42px;
  height: 42px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-managed-services-page .vibe-risk-icon { width: 36px; height: 36px; border-radius: 10px; }
.armely-managed-services-page .deliver-icon .icon-svg,
.armely-managed-services-page .uc-icon .icon-svg,
.armely-managed-services-page .why-icon .icon-svg { width: 23px; height: 23px; }
.armely-managed-services-page .uc-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  display: flex;
  align-items: center;
  justify-content: center;
}
@media (max-width: 980px) {
  .armely-managed-services-page .hero-inner { grid-template-columns: 1fr; gap: 32px; }
  .armely-managed-services-page .hero-visual { min-height: 320px; }
}
@media (max-width: 680px) {
  .armely-managed-services-page .hero { padding: 104px 22px 64px; }
  .armely-managed-services-page .hero-trust { grid-template-columns: 1fr; }
  .armely-managed-services-page .hero-visual { display: none; }
  .armely-managed-services-page .hero-actions a { width: 100%; text-align: center; }
}

/* Focused cleanup for the Generative AI detail page */
.armely-managed-services-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  align-items: center;
  background: #173b67;
  border-radius: 0;
}
.armely-managed-services-page .hero::after,
.armely-managed-services-page .hero-bg-glow,
.armely-managed-services-page .hero-visual {
  display: none;
}
.armely-managed-services-page .hero-inner {
  width: min(1040px, 100%);
  display: block;
}
.armely-managed-services-page .hero-copy {
  max-width: 760px;
}
.armely-managed-services-page .hero-eyebrow {
  margin-bottom: 18px;
}
.armely-managed-services-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.72);
}
.armely-managed-services-page .eyebrow-partner,
.armely-managed-services-page .hero-trust {
  display: none;
}
.armely-managed-services-page .hero h1 {
  max-width: 760px;
  margin-bottom: 22px;
}
.armely-managed-services-page .hero-sub {
  max-width: 680px;
  margin-bottom: 34px;
}
.armely-managed-services-page .hero-actions {
  margin-bottom: 0;
}
.armely-managed-services-page .hero .btn-primary,
.armely-managed-services-page .hero .btn-outline {
  border-radius: 0;
}
.armely-managed-services-page .vibe-section {
  background: #fff;
  padding: 84px 56px;
}
.armely-managed-services-page .vibe-section .section-inner {
  max-width: 920px;
}
.armely-managed-services-page .vibe-section .section-title,
.armely-managed-services-page .vibe-section .section-body {
  max-width: 820px;
}
.armely-managed-services-page .vibe-section .section-body {
  margin-bottom: 28px;
}
.armely-managed-services-page .vibe-two-col {
  grid-template-columns: 1fr;
  gap: 18px;
}
.armely-managed-services-page .vibe-card,
.armely-managed-services-page .vibe-answer-card {
  border-radius: 0;
  box-shadow: none;
}
.armely-managed-services-page .vibe-card-header {
  padding: 18px 20px;
}
.armely-managed-services-page .vibe-card-body {
  padding: 8px 20px 12px;
}
.armely-managed-services-page .vibe-risk {
  padding: 12px 0;
}
.armely-managed-services-page .vibe-right {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
}
.armely-managed-services-page .vibe-answer-card {
  margin-bottom: 0;
  padding: 20px;
  background: #f7f9fc;
}
.armely-managed-services-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-managed-services-page .why > .section-inner > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-managed-services-page section:not(.hero) > .section-inner > .section-title,
.armely-managed-services-page .why > .section-inner > .section-title {
  max-width: 880px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-managed-services-page section:not(.hero) > .section-inner > .section-body,
.armely-managed-services-page .why > .section-inner > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-managed-services-page .spectrum-grid,
.armely-managed-services-page .delivers-grid,
.armely-managed-services-page .steps-row,
.armely-managed-services-page .uc-grid,
.armely-managed-services-page .testi-grid,
.armely-managed-services-page .why-two-col {
  margin-top: 56px;
}
.armely-managed-services-page .why-two-col {
  align-items: stretch;
}
.armely-managed-services-page .why-list {
  margin-top: 0;
}
.armely-managed-services-page .why-list,
.armely-managed-services-page .partner-block {
  height: 100%;
}
@media (max-width: 900px) {
  .armely-managed-services-page .hero {
    padding: 118px 24px 76px;
  }
  .armely-managed-services-page .vibe-section {
    padding: 72px 24px;
  }
  .armely-managed-services-page .vibe-right {
    grid-template-columns: 1fr;
  }
}



/* Final compact modern cleanup */
.armely-managed-services-page {
  --blue: #2f5597;
  --blue-lt: #4779bd;
  --navy-mid: #f6f8fc;
  --navy-card: #ffffff;
  --text-body: #334155;
  --text-muted: #667085;
  --border: rgba(47, 85, 151, 0.14);
}
.armely-managed-services-page .hero {
  min-height: auto !important;
  padding: 86px 56px 70px !important;
  background: linear-gradient(135deg, #173b67 0%, #234f86 100%) !important;
  border-radius: 0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}
.armely-managed-services-page .hero::after,
.armely-managed-services-page .hero-bg-glow,
.armely-managed-services-page .hero-visual {
  display: none !important;
}
.armely-managed-services-page .hero-inner {
  width: min(1120px, 100%) !important;
  margin: 0 auto !important;
  display: block !important;
}
.armely-managed-services-page .hero-copy {
  max-width: 860px !important;
}
.armely-managed-services-page .hero-eyebrow {
  margin-bottom: 18px !important;
}
.armely-managed-services-page .eyebrow-badge {
  display: inline-flex !important;
  background: rgba(255,255,255,0.10) !important;
  border: 1px solid rgba(255,255,255,0.22) !important;
  border-radius: 999px !important;
  padding: 7px 14px !important;
  color: rgba(255,255,255,0.88) !important;
}
.armely-managed-services-page .eyebrow-partner {
  display: inline-flex !important;
  color: rgba(255,255,255,0.66) !important;
}
.armely-managed-services-page .hero h1 {
  max-width: 900px !important;
  margin-bottom: 18px !important;
  font-size: clamp(1.75rem, 3.2vw, 2.7rem);
  line-height: 1.05 !important;
  letter-spacing: -0.04em !important;
}
.armely-managed-services-page .hero-sub {
  max-width: 760px !important;
  margin-bottom: 28px !important;
  font-size: 1rem !important;
  line-height: 1.7 !important;
}
.armely-managed-services-page .hero-actions {
  margin-bottom: 0 !important;
  gap: 12px !important;
}
.armely-managed-services-page .hero .btn-primary,
.armely-managed-services-page .hero .btn-outline,
.armely-managed-services-page .btn-primary,
.armely-managed-services-page .btn-outline,
.armely-managed-services-page .form-submit {
  border-radius: 8px !important;
}
.armely-managed-services-page section {
  padding: 68px 56px !important;
}
.armely-managed-services-page .section-inner {
  max-width: 1120px !important;
}
.armely-managed-services-page .section-eyebrow {
  margin-bottom: 10px !important;
}
.armely-managed-services-page .section-title {
  margin-bottom: 14px !important;
}
.armely-managed-services-page .section-body {
  margin-bottom: 28px !important;
  line-height: 1.65 !important;
}
.armely-managed-services-page .spectrum-grid,
.armely-managed-services-page .vibe-two-col,
.armely-managed-services-page .delivers-grid,
.armely-managed-services-page .steps-row,
.armely-managed-services-page .uc-grid,
.armely-managed-services-page .testi-grid,
.armely-managed-services-page .why-two-col {
  margin-top: 34px !important;
  gap: 20px !important;
}
.armely-managed-services-page .spectrum-grid,
.armely-managed-services-page .why-two-col {
  grid-template-columns: 1fr 1fr !important;
}
.armely-managed-services-page .spectrum-row {
  margin-top: 0 !important;
  gap: 10px !important;
}
.armely-managed-services-page .spectrum-level,
.armely-managed-services-page .deliver-card,
.armely-managed-services-page .uc-card,
.armely-managed-services-page .testi-card,
.armely-managed-services-page .vibe-answer-card,
.armely-managed-services-page .partner-block,
.armely-managed-services-page .cta-form,
.armely-managed-services-page .vibe-card,
.armely-managed-services-page .platform-card {
  border-radius: 14px !important;
  box-shadow: 0 14px 36px rgba(18, 47, 82, 0.08) !important;
}
.armely-managed-services-page .deliver-card,
.armely-managed-services-page .uc-card,
.armely-managed-services-page .testi-card {
  padding: 24px 22px !important;
}
.armely-managed-services-page .deliver-icon,
.armely-managed-services-page .uc-icon,
.armely-managed-services-page .why-icon,
.armely-managed-services-page .vibe-card-icon,
.armely-managed-services-page .vibe-risk-icon {
  border-radius: 12px !important;
  color: var(--blue) !important;
}
.armely-managed-services-page .vibe-section {
  padding: 68px 56px !important;
  background: #fff !important;
}
.armely-managed-services-page .vibe-section .section-inner {
  max-width: 1120px !important;
}
.armely-managed-services-page .vibe-two-col {
  grid-template-columns: 1fr 1fr !important;
  align-items: stretch !important;
}
.armely-managed-services-page .vibe-card-body {
  padding: 18px 20px !important;
}
.armely-managed-services-page .vibe-right {
  display: flex !important;
  flex-direction: column !important;
  gap: 12px !important;
}
.armely-managed-services-page .vibe-answer-card {
  padding: 20px !important;
  background: #f8fafd !important;
}
.armely-managed-services-page .steps-row {
  border-radius: 14px !important;
  overflow: hidden !important;
}
.armely-managed-services-page .step {
  padding: 24px 18px !important;
}
.armely-managed-services-page .step-num {
  font-size: 2rem !important;
  margin-bottom: 10px !important;
}
.armely-managed-services-page .why-list {
  margin-top: 0 !important;
}
.armely-managed-services-page .why-list li {
  padding: 16px 0 !important;
}
.armely-managed-services-page .partner-block-top,
.armely-managed-services-page .p-stat {
  padding: 22px !important;
}
.armely-managed-services-page .cta-inner {
  padding: 68px 56px !important;
  gap: 40px !important;
}
@media (max-width: 900px) {
  .armely-managed-services-page .hero { padding: 88px 24px 58px !important; }
  .armely-managed-services-page section,
  .armely-managed-services-page .vibe-section { padding: 56px 24px !important; }
  .armely-managed-services-page .spectrum-grid,
  .armely-managed-services-page .vibe-two-col,
  .armely-managed-services-page .why-two-col { grid-template-columns: 1fr !important; }
  .armely-managed-services-page .delivers-grid,
  .armely-managed-services-page .uc-grid { grid-template-columns: 1fr 1fr !important; }
  .armely-managed-services-page .cta-inner { padding: 56px 24px !important; grid-template-columns: 1fr !important; }
}
@media (max-width: 600px) {
  .armely-managed-services-page .hero h1 { font-size: clamp(1.75rem, 3.2vw, 2.7rem); }
  .armely-managed-services-page .hero-eyebrow { flex-direction: column !important; align-items: flex-start !important; gap: 8px !important; }
  .armely-managed-services-page .hero-actions a { width: 100% !important; text-align: center !important; }
  .armely-managed-services-page .delivers-grid,
  .armely-managed-services-page .uc-grid { grid-template-columns: 1fr !important; }
}



.armely-managed-services-page .cr-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:28px; margin-bottom:28px; }
.armely-managed-services-page .cr-card { background:#fff; border:1px solid var(--border); border-radius:14px; padding:22px; box-shadow:0 14px 36px rgba(18,47,82,0.08); }
.armely-managed-services-page .cr-label { display:flex; align-items:center; gap:9px; margin-bottom:10px; }
.armely-managed-services-page .cr-check { width:20px; height:20px; display:flex; align-items:center; justify-content:center; background:var(--blue-dim); border:1px solid var(--blue-dim2); border-radius:50%; flex-shrink:0; color:var(--blue); }
.armely-managed-services-page .cr-check .icon-svg { width:11px; height:11px; stroke-width:3; }
.armely-managed-services-page .cr-industry { font-size:0.875rem; font-weight:700; color:#162b49; }
.armely-managed-services-page .cr-desc { font-size:0.84rem; color:var(--text-muted); line-height:1.65; margin:0; }
.armely-managed-services-page .cr-cta { text-align:center; margin-top:8px; }
.armely-managed-services-page .cr-btn { display:inline-flex; align-items:center; gap:10px; background:#1a2e52; color:#fff; border-radius:8px; padding:13px 28px; text-decoration:none; font-size:0.875rem; font-weight:600; }
.armely-managed-services-page .cr-btn:hover { background:var(--blue); }
.armely-managed-services-page .cr-btn .icon-svg { width:18px; height:18px; }
@media (max-width:900px) { .armely-managed-services-page .cr-grid { grid-template-columns:1fr 1fr; } }
@media (max-width:600px) { .armely-managed-services-page .cr-grid { grid-template-columns:1fr; } }
</style>
<div class="armely-managed-services-page">
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-inner">
    <div class="hero-copy">
      <div class="hero-eyebrow">
        <span class="eyebrow-badge">Managed Services</span>
        <span class="eyebrow-partner">Delivered by a certified Microsoft partner</span>
      </div>
      <h1>Your Microsoft environment,<br>managed by the team<br>that built it.</h1>
      <p class="hero-sub">Armely Managed Services provides ongoing monitoring, support, optimization, and administration for the Microsoft platforms and custom solutions we implement, with a dedicated account team that knows your environment.</p>
      <div class="hero-actions">
        <a href="#contact" class="btn-primary">Book a Free Assessment</a>
        <a href="#delivers" class="btn-outline">See What We Do</a>
      </div>
    </div>
  </div>
</section>

<section class="spectrum"><div class="section-inner"><div class="section-eyebrow">What Managed Services Means at Armely</div><h2 class="section-title">Not a help desk. A dedicated technical team that knows your business.</h2><p class="section-body">Most managed service providers offer a ticket queue staffed by generalists. Armely Managed Services is structured differently. Your account team is the same team that implemented your environment.</p>
<div class="spectrum-grid"><div class="spectrum-row">
<div class="spectrum-level highlight"><span class="spectrum-num">Monitor</span><div><div class="spectrum-content-title">Proactive Monitoring and Alerting</div><div class="spectrum-content-desc">We monitor your Microsoft 365 tenant health, Azure infrastructure, application performance, and security posture continuously and alert you before issues affect your users.</div></div></div>
<div class="spectrum-level highlight"><span class="spectrum-num">Admin</span><div><div class="spectrum-content-title">Ongoing Administration</div><div class="spectrum-content-desc">User provisioning, license management, configuration changes, patch management, and routine administrative tasks handled by Armely so your internal team is not diverted from primary work.</div></div></div>
<div class="spectrum-level highlight"><span class="spectrum-num">Optimize</span><div><div class="spectrum-content-title">Optimization and Advisory</div><div class="spectrum-content-desc">Quarterly business reviews covering usage, performance, cost, and upcoming Microsoft releases. We identify optimization opportunities proactively before problems develop.</div></div></div>
<div class="spectrum-level"><span class="spectrum-num">Build</span><div><div class="spectrum-content-title">Ongoing Development and Enhancements</div><div class="spectrum-content-desc">New requirements, feature requests, and enhancements to existing solutions handled within the managed services agreement rather than as separate project engagements at Enterprise tier.</div></div></div>
</div><div><div class="platform-card"><div class="platform-header"><div class="platform-dots"><span></span><span></span><span></span></div><span class="platform-header-title">Platform Coverage</span></div><div class="platform-body"><div class="plat-band band-tools"><div class="plat-band-label">Microsoft 365 and Azure</div><div class="plat-chips"><span class="plat-chip">Microsoft 365 and Teams</span><span class="plat-chip">Azure Infrastructure</span><span class="plat-chip">SharePoint</span><span class="plat-chip">Exchange Online</span><span class="plat-chip">Entra ID</span><span class="plat-chip">Power Platform</span></div></div><div class="band-arrow">&#8597;</div><div class="plat-band band-data"><div class="plat-band-label">Business Applications and Data</div><div class="plat-chips"><span class="plat-chip">Dynamics 365</span><span class="plat-chip">SQL Server and Databases</span><span class="plat-chip">Custom Applications</span><span class="plat-chip">AI Agents and Foundry</span><span class="plat-chip">Power BI and Fabric</span></div></div><div class="band-arrow">&#8597;</div><div class="plat-band band-gov"><div class="plat-band-label">Support and Governance</div><div class="plat-chips"><span class="plat-chip">SLA-Backed Response Times</span><span class="plat-chip">Quarterly Reviews</span><span class="plat-chip">Patch Management</span><span class="plat-chip">Security Posture</span><span class="plat-chip">Cost Monitoring</span></div></div></div></div></div></div></div></section>
<section class="delivers" id="delivers"><div class="section-inner"><div class="section-eyebrow">Service Tiers</div><h2 class="section-title">Three tiers for different levels of support need and environment complexity.</h2><p class="section-body">Pricing is based on your environment scope and complexity. Contact Armely for a proposal tailored to your specific platforms and user count.</p>
<div class="delivers-grid"><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg></div><div class="deliver-title">Foundation</div><div class="deliver-desc">Microsoft 365 tenant monitoring and health alerts, security and compliance dashboard review, 8x5 support with next business day response, monthly platform health report, license management and user provisioning, and access to Armely partner pricing.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div><div class="deliver-title">Professional (Most Common)</div><div class="deliver-desc">Everything in Foundation plus Azure, Dynamics 365, and Power Platform coverage, proactive monitoring and alerting, 4-hour response for critical issues, quarterly business reviews, patch and update management, and minor enhancement requests included.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg></div><div class="deliver-title">Enterprise</div><div class="deliver-desc">Everything in Professional plus custom application and database coverage, 1-hour response for critical issues, included development hours each month, monthly business reviews, AI and data platform management, and security posture reviews.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg></div><div class="deliver-title">SQL Server and Database Management</div><div class="deliver-desc">SQL Server monitoring, backup management, performance tuning, index maintenance, patch management, and capacity planning for on-premises and Azure-hosted SQL Server environments.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 8V4H8"/><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 11v2"/><path d="M15 11v2"/></svg></div><div class="deliver-title">AI and Agent Management</div><div class="deliver-desc">Monitoring and governance of deployed Copilot Studio agents and Azure AI Foundry applications, including usage analytics, permission audits, model updates, and content review.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg></div><div class="deliver-title">Custom Application Support</div><div class="deliver-desc">Monitoring, incident response, bug fixes, and minor enhancements for custom applications built on the Azure and .NET stack, with defined SLAs and a named technical contact.</div></div></div></div></section>
<section class="journey"><div class="section-inner"><div class="section-eyebrow">How Armely Managed Services Works</div><h2 class="section-title">From scoping call to a managed environment your team can rely on.</h2><p class="section-body">Onboarding to Armely Managed Services takes two to three weeks.</p>
<div class="steps-row"><div class="step"><div class="step-num">01</div><div class="step-title">Scoping Call</div><div class="step-desc">We understand your environment, platforms, team structure, and support requirements. We identify the right tier and produce a fixed monthly proposal.</div><span class="step-tag">Free</span></div><div class="step"><div class="step-num">02</div><div class="step-title">Proposal and Agreement</div><div class="step-desc">Fixed monthly fee proposal covering all platforms and agreed SLAs. No surprises and no hourly charges for routine support within scope.</div><span class="step-tag">Week 1</span></div><div class="step"><div class="step-num">03</div><div class="step-title">Onboarding Assessment</div><div class="step-desc">For environments we did not build, we run a structured onboarding assessment to document your environment, establish baselines, and identify any urgent items.</div><span class="step-tag">Weeks 1-2</span></div><div class="step"><div class="step-num">04</div><div class="step-title">Monitoring and Administration</div><div class="step-desc">Monitoring agents deployed, admin access configured, escalation paths documented, and your named account manager introduced. Support begins from day one.</div><span class="step-tag">Week 3</span></div><div class="step"><div class="step-num">05</div><div class="step-title">Ongoing Management</div><div class="step-desc">Proactive monitoring, administration, quarterly reviews, and enhancement requests handled by your Armely account team as an extension of your IT function.</div><span class="step-tag">Ongoing</span></div></div></div></section>
<section class="usecases"><div class="section-inner"><div class="section-eyebrow">What We Manage</div><h2 class="section-title">Managed services across the full Microsoft platform and beyond.</h2><p class="section-body">Armely Managed Services covers the platforms we implement.</p>
<div class="uc-grid"><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></span><div class="uc-title">Microsoft 365 Administration</div><div class="uc-desc">Tenant health monitoring, user lifecycle management, license optimization, Exchange Online, Teams administration, SharePoint governance, and security and compliance policy management.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg></span><div class="uc-title">Azure Infrastructure Management</div><div class="uc-desc">Azure resource monitoring, cost optimization, backup verification, performance management, security patching, and infrastructure scaling.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg></span><div class="uc-title">Dynamics 365 and Power Platform</div><div class="uc-desc">Dynamics 365 environment management, release wave update planning, Power Platform governance, flow monitoring, connector management, and minor configuration requests.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg></span><div class="uc-title">SQL Server and Database Management</div><div class="uc-desc">SQL Server monitoring, backup management, performance tuning, index maintenance, patch management, and capacity planning.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg></span><div class="uc-title">Custom Application Support</div><div class="uc-desc">Monitoring, incident response, bug fixes, and minor enhancements for custom applications built by Armely on the Azure and .NET stack.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 8V4H8"/><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 11v2"/><path d="M15 11v2"/></svg></span><div class="uc-title">AI and Agent Management</div><div class="uc-desc">Monitoring and governance of deployed Copilot Studio agents and Azure AI Foundry applications, including usage analytics, permission audits, and content review.</div></div></div></div></section>
<section class="testimonials">
  <div class="section-inner">
    <div class="section-eyebrow">Client Results</div>
    <h2 class="section-title">Real outcomes for real organizations.</h2>
    <p class="section-body">Armely has delivered Microsoft platform and AI solutions for healthcare providers, school districts, energy operators, professional services firms, government agencies, and non-profit organizations.</p>
    <div class="cr-grid">
      <div class="cr-card"><div class="cr-label"><span class="cr-check"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span class="cr-industry">Healthcare</span></div><p class="cr-desc">Swope Health Services and UNMC: data platform and clinical workflow modernization on Microsoft Azure.</p></div>
      <div class="cr-card"><div class="cr-label"><span class="cr-check"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span class="cr-industry">Education</span></div><p class="cr-desc">Plano ISD: Microsoft 365 governance, SharePoint, and Power Platform implementations across district operations.</p></div>
      <div class="cr-card"><div class="cr-label"><span class="cr-check"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span class="cr-industry">Energy</span></div><p class="cr-desc">Oil and gas operators: OpenInvoice visibility and AP workflow automation through Invoice Lens.</p></div>
      <div class="cr-card"><div class="cr-label"><span class="cr-check"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span class="cr-industry">Professional Services</span></div><p class="cr-desc">Consulting and legal firms: Dynamics 365, Power Automate approval workflows, and AI knowledge agents.</p></div>
      <div class="cr-card"><div class="cr-label"><span class="cr-check"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span class="cr-industry">Government</span></div><p class="cr-desc">State and local agencies: Microsoft 365 Government deployment and compliance configuration.</p></div>
      <div class="cr-card"><div class="cr-label"><span class="cr-check"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></span><span class="cr-industry">Non-Profit</span></div><p class="cr-desc">Social services organizations: Microsoft 365 optimization, Power BI grant reporting, and SharePoint governance.</p></div>
    </div>
    <div class="cr-cta">
      <a href="https://armely.com/customer-stories" class="cr-btn"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg><span>Read Client Stories on armely.com</span><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>
    </div>
  </div>
</section>
<section class="why"><div class="section-inner"><div class="section-eyebrow">Why Armely Managed Services</div><h2 class="section-title">Managed by the team that built it.</h2><p class="section-body">The difference between Armely Managed Services and a generic managed services provider is the institutional knowledge that comes from being the team that built and implemented the environment.</p>
<div class="why-two-col"><div><ul class="why-list"><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg></div><div><div class="why-item-title">Context That Cannot Be Transferred</div><div class="why-item-desc">When Armely manages an environment we implemented, we know why every decision was made, what the constraints were, and what the business depends on.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 17 6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg></div><div><div class="why-item-title">Proactive Rather Than Reactive</div><div class="why-item-desc">We monitor continuously and review quarterly. Most issues we resolve before clients are aware of them.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></div><div><div class="why-item-title">One Team Across the Full Stack</div><div class="why-item-desc">Armely covers Microsoft 365, Azure, Dynamics 365, Power Platform, SharePoint, SQL Server, and custom applications under a single managed services agreement.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div><div><div class="why-item-title">Predictable Fixed Monthly Fee</div><div class="why-item-desc">Your managed services fee is fixed based on your environment scope. No unexpected charges for routine support, monitoring, or the minor enhancements included in your tier.</div></div></li></ul></div>
<div><div class="partner-block"><div class="partner-block-top"><div class="partner-label">Microsoft Authorized Partner</div><p class="partner-text">Armely's Microsoft partnership gives our managed services practice access to partner support channels, early release information, and Microsoft licensing at rates not available to direct buyers.</p></div><div class="partner-stats"><div class="p-stat"><div class="p-stat-num">1<span></span></div><div class="p-stat-label">dedicated account manager per client who knows your environment personally</div></div><div class="p-stat"><div class="p-stat-num">7<span>+</span></div><div class="p-stat-label">Microsoft platforms covered under a single managed services agreement</div></div><div class="p-stat"><div class="p-stat-num">QBR<span></span></div><div class="p-stat-label">quarterly business reviews included at Professional and Enterprise tiers</div></div><div class="p-stat"><div class="p-stat-num">0<span></span></div><div class="p-stat-label">surprise charges for routine support, monitoring, or included enhancement hours</div></div></div></div></div></div></div></section>
<section class="cta-section" id="contact"><div class="cta-inner"><div><div class="section-eyebrow">Get Started</div><h2 class="section-title">Tell us what you are running. We will tell you what managing it looks like.</h2><p class="section-body">Book a free 30-minute scoping call. We will review your current Microsoft environment and come back with a managed services proposal and fixed monthly fee.</p><div style="margin-top:28px;display:flex;flex-direction:column;gap:12px;"><div class="trust-item"><span class="trust-dot" style="background:var(--blue);"></span><span class="trust-text" style="color:var(--text-body);">Free assessment, no commitment required</span></div><div class="trust-item"><span class="trust-dot" style="background:var(--blue);"></span><span class="trust-text" style="color:var(--text-body);">Recommendation and partner pricing included</span></div><div class="trust-item"><span class="trust-dot" style="background:var(--blue);"></span><span class="trust-text" style="color:var(--text-body);">Response within one business day</span></div></div></div><div class="cta-form"><div class="form-title">Book Your Free Assessment</div><div class="form-sub">Tell us about your situation.</div><div class="form-row"><label>Full Name</label><input type="text" placeholder="Jane Smith"></div><div class="form-row"><label>Business Email</label><input type="email" placeholder="jane@yourcompany.com"></div><div class="form-row"><label>Company Name</label><input type="text" placeholder="Acme Corp"></div><div class="form-row"><label>Primary Need</label><select><option value="">Select...</option><option>Microsoft 365 and Teams only</option><option>Microsoft 365 plus Azure infrastructure</option><option>Microsoft 365 plus Dynamics 365</option><option>Full Microsoft stack including custom applications</option><option>SQL Server and database management</option><option>AI agents and Azure AI management</option><option>Multiple, need a full assessment</option></select></div><button class="form-submit">Request Managed Services Proposal</button><div class="form-note">No spam. No sales pressure. Just a useful conversation.</div></div></div></section>
</div>
@section('title', 'Custom Application Development | Armely')

<style>


.armely-custom-development-page *, .armely-custom-development-page *::before, .armely-custom-development-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-custom-development-page {
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

.armely-custom-development-page { scroll-behavior: smooth; }
.armely-custom-development-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* NAV */
.armely-custom-development-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-custom-development-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-custom-development-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-custom-development-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-custom-development-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-custom-development-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-custom-development-page .nav-links a:hover { color: #fff; }
.armely-custom-development-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-custom-development-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* HERO */
.armely-custom-development-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-custom-development-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-custom-development-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-custom-development-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-custom-development-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-custom-development-page .hero h1 { font-size: clamp(1.75rem, 3.2vw, 2.7rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-custom-development-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-custom-development-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.8; }
.armely-custom-development-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-custom-development-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-custom-development-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-custom-development-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-custom-development-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-custom-development-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-custom-development-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-custom-development-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-custom-development-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-custom-development-page .trust-text strong { color: #fff; font-weight: 600; }

  /* SECTIONS */
.armely-custom-development-page section { padding: 96px 56px; }
.armely-custom-development-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-custom-development-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-custom-development-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 640px; }
.armely-custom-development-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* AI SPECTRUM */
.armely-custom-development-page .spectrum { background: var(--navy-mid); }
.armely-custom-development-page .spectrum-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-custom-development-page .spectrum-row { display: flex; flex-direction: column; gap: 10px; margin-top: 32px; }
.armely-custom-development-page .spectrum-level { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; transition: border-color 0.2s; }
.armely-custom-development-page .spectrum-level:hover { border-color: rgba(41,78,139,0.3); }
.armely-custom-development-page .spectrum-level.highlight { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-custom-development-page .spectrum-num { font-size: 0.68rem; font-weight: 800; color: var(--blue); background: var(--blue-dim2); border-radius: 4px; padding: 2px 7px; flex-shrink: 0; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.08em; }
.armely-custom-development-page .spectrum-content-title { font-size: 0.875rem; font-weight: 700; color: #1A2540; margin-bottom: 3px; }
.armely-custom-development-page .spectrum-content-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }

  /* Platform visual */
.armely-custom-development-page .platform-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-custom-development-page .platform-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-custom-development-page .platform-dots { display: flex; gap: 6px; }
.armely-custom-development-page .platform-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-custom-development-page .platform-header-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-custom-development-page .platform-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.armely-custom-development-page .plat-band { border-radius: 9px; padding: 13px 16px; }
.armely-custom-development-page .plat-band-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
.armely-custom-development-page .plat-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.armely-custom-development-page .plat-chip { font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.armely-custom-development-page .band-tools { background: var(--blue-dim); }
.armely-custom-development-page .band-tools .plat-band-label { color: var(--blue); }
.armely-custom-development-page .band-tools .plat-chip { background: var(--blue-dim2); color: var(--blue); }
.armely-custom-development-page .band-data { background: rgba(41,78,139,0.05); }
.armely-custom-development-page .band-data .plat-band-label { color: var(--blue); }
.armely-custom-development-page .band-data .plat-chip { background: rgba(41,78,139,0.1); color: var(--blue); }
.armely-custom-development-page .band-gov { background: var(--blue); }
.armely-custom-development-page .band-gov .plat-band-label { color: rgba(255,255,255,0.7); }
.armely-custom-development-page .band-gov .plat-chip { background: rgba(255,255,255,0.15); color: #fff; }
.armely-custom-development-page .band-arrow { text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2px 0; }

  /* VIBE CODING CALLOUT */
.armely-custom-development-page .vibe-section { background: var(--navy); }
.armely-custom-development-page .vibe-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: start; }
.armely-custom-development-page .vibe-left { }
.armely-custom-development-page .vibe-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-custom-development-page .vibe-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
.armely-custom-development-page .vibe-card-icon { font-size: 1.4rem; }
.armely-custom-development-page .vibe-card-title { font-size: 1rem; font-weight: 700; color: #1A2540; }
.armely-custom-development-page .vibe-card-subtitle { font-size: 0.78rem; color: var(--text-muted); }
.armely-custom-development-page .vibe-card-body { padding: 24px; }
.armely-custom-development-page .vibe-risk { display: flex; align-items: flex-start; gap: 12px; padding: 14px 0; border-bottom: 1px solid var(--border); }
.armely-custom-development-page .vibe-risk:last-child { border-bottom: none; }
.armely-custom-development-page .vibe-risk-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
.armely-custom-development-page .vibe-risk-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 2px; }
.armely-custom-development-page .vibe-risk-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }
.armely-custom-development-page .vibe-right { }
.armely-custom-development-page .vibe-answer-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 28px; margin-bottom: 12px; }
.armely-custom-development-page .vibe-answer-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--blue); margin-bottom: 10px; }
.armely-custom-development-page .vibe-answer-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.75; }

  /* DELIVERS */
.armely-custom-development-page .delivers { background: var(--navy-mid); }
.armely-custom-development-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-custom-development-page .deliver-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-custom-development-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-custom-development-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-custom-development-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-custom-development-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-custom-development-page .journey { background: var(--navy); }
.armely-custom-development-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-custom-development-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-custom-development-page .step:last-child { border-right: none; }
.armely-custom-development-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-custom-development-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-custom-development-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-custom-development-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* USE CASES */
.armely-custom-development-page .usecases { background: var(--navy-mid); }
.armely-custom-development-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-custom-development-page .uc-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-custom-development-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-custom-development-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-custom-development-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-custom-development-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* TESTIMONIALS */
.armely-custom-development-page .testimonials { background: var(--navy); padding: 96px 56px; }
.armely-custom-development-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-custom-development-page .testi-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-custom-development-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-custom-development-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-custom-development-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-custom-development-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-custom-development-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-custom-development-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-custom-development-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY ARMELY */
.armely-custom-development-page .why { background: var(--navy-mid); }
.armely-custom-development-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-custom-development-page .why-list { list-style: none; margin-top: 36px; }
.armely-custom-development-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-custom-development-page .why-list li:last-child { border-bottom: none; }
.armely-custom-development-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-custom-development-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-custom-development-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-custom-development-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-custom-development-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-custom-development-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-custom-development-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-custom-development-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-custom-development-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-custom-development-page .p-stat:nth-child(2) { border-right: none; }
.armely-custom-development-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-custom-development-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-custom-development-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-custom-development-page .p-stat-num span { color: var(--blue); }
.armely-custom-development-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* CTA */
.armely-custom-development-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-custom-development-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-custom-development-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-custom-development-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-custom-development-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-custom-development-page .form-row { margin-bottom: 14px; }
.armely-custom-development-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-custom-development-page .form-row input, .armely-custom-development-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-custom-development-page .form-row input:focus, .armely-custom-development-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-custom-development-page .form-row select option { background: #fff; color: #1A2540; }
.armely-custom-development-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-custom-development-page .form-submit:hover { background: var(--blue-lt); }
.armely-custom-development-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* FOOTER */
.armely-custom-development-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-custom-development-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-custom-development-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-custom-development-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-custom-development-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-custom-development-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-custom-development-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* RESPONSIVE */
  @media (max-width: 900px) {
.armely-custom-development-page nav { padding: 16px 24px; }
.armely-custom-development-page .nav-links { display: none; }
.armely-custom-development-page section { padding: 72px 24px; }
.armely-custom-development-page .hero { padding: 110px 24px 72px; }
.armely-custom-development-page .spectrum-grid, .armely-custom-development-page .vibe-two-col, .armely-custom-development-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-custom-development-page .delivers-grid, .armely-custom-development-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-custom-development-page .steps-row { grid-template-columns: 1fr; }
.armely-custom-development-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-custom-development-page .step:last-child { border-bottom: none; }
.armely-custom-development-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-custom-development-page .testimonials { padding: 72px 24px; }
.armely-custom-development-page .testi-grid { grid-template-columns: 1fr; }
.armely-custom-development-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-custom-development-page .delivers-grid, .armely-custom-development-page .uc-grid { grid-template-columns: 1fr; }
.armely-custom-development-page .partner-stats { grid-template-columns: 1fr; }
.armely-custom-development-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-custom-development-page {
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
.armely-custom-development-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-custom-development-page .hero::after {
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
.armely-custom-development-page .section-title,
.armely-custom-development-page .deliver-title,
.armely-custom-development-page .uc-title,
.armely-custom-development-page .step-title,
.armely-custom-development-page .why-item-title,
.armely-custom-development-page .form-title {
  color: #162b49;
}
.armely-custom-development-page .deliver-card,
.armely-custom-development-page .uc-card,
.armely-custom-development-page .testi-card,
.armely-custom-development-page .platform-card,
.armely-custom-development-page .partner-block,
.armely-custom-development-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-custom-development-page .deliver-card:hover,
.armely-custom-development-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-custom-development-page .btn-primary,
.armely-custom-development-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-custom-development-page .btn-primary:hover,
.armely-custom-development-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-custom-development-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-custom-development-page nav,
.armely-custom-development-page footer {
  display: none;
}


/* Modern layout update: tighter first section and SVG icon system */
.armely-custom-development-page .hero {
  min-height: auto;
  padding: 128px 32px 86px;
  isolation: isolate;
}
.armely-custom-development-page .hero-inner {
  width: min(1160px, 100%);
  margin: 0 auto;
  display: grid;
  grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.75fr);
  align-items: center;
  gap: 56px;
  position: relative;
  z-index: 1;
}
.armely-custom-development-page .hero-copy { max-width: 760px; }
.armely-custom-development-page .hero h1 { max-width: 760px; margin-bottom: 20px; }
.armely-custom-development-page .hero-sub { max-width: 640px; margin-bottom: 30px; font-size: 1.02rem; line-height: 1.72; }
.armely-custom-development-page .hero-actions { margin-bottom: 34px; }
.armely-custom-development-page .hero-trust {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  padding-top: 0;
  border-top: 0;
  max-width: 720px;
}
.armely-custom-development-page .hero .trust-item {
  align-items: flex-start;
  background: rgba(255,255,255,0.10);
  border: 1px solid rgba(255,255,255,0.16);
  border-radius: 14px;
  padding: 14px 15px;
  backdrop-filter: blur(10px);
}
.armely-custom-development-page .hero .trust-dot {
  width: 22px;
  height: 22px;
  border-radius: 999px;
  background: rgba(255,255,255,0.16);
  border: 1px solid rgba(255,255,255,0.24);
  position: relative;
  margin-top: 1px;
}
.armely-custom-development-page .hero .trust-dot::after {
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
.armely-custom-development-page .hero .trust-text { color: rgba(255,255,255,0.78); line-height: 1.5; }
.armely-custom-development-page .hero-visual {
  min-height: 420px;
  border-radius: 28px;
  position: relative;
  background: linear-gradient(145deg, rgba(255,255,255,0.16), rgba(255,255,255,0.05));
  border: 1px solid rgba(255,255,255,0.18);
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.18), 0 28px 70px rgba(6, 22, 48, 0.24);
  overflow: hidden;
}
.armely-custom-development-page .hero-visual::before {
  content: '';
  position: absolute;
  inset: 36px;
  border: 1px solid rgba(255,255,255,0.14);
  border-radius: 999px;
}
.armely-custom-development-page .hero-visual::after {
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
.armely-custom-development-page .hero-orbit span {
  position: absolute;
  width: 52px;
  height: 52px;
  border-radius: 16px;
  background: rgba(255,255,255,0.14);
  border: 1px solid rgba(255,255,255,0.22);
}
.armely-custom-development-page .hero-orbit span:nth-child(1) { left: 56px; top: 88px; }
.armely-custom-development-page .hero-orbit span:nth-child(2) { right: 72px; top: 138px; }
.armely-custom-development-page .hero-orbit span:nth-child(3) { left: 48%; bottom: 76px; }
.armely-custom-development-page .hero-visual-card {
  position: absolute;
  z-index: 2;
  width: 230px;
  padding: 18px;
  border-radius: 18px;
  background: rgba(255,255,255,0.92);
  box-shadow: 0 24px 54px rgba(9, 31, 63, 0.22);
}
.armely-custom-development-page .hero-visual-card.top { top: 42px; right: 30px; }
.armely-custom-development-page .hero-visual-card.bottom { bottom: 46px; left: 30px; }
.armely-custom-development-page .hero-visual-label {
  display: block;
  font-size: 0.78rem;
  font-weight: 700;
  color: #162b49;
  margin-bottom: 12px;
}
.armely-custom-development-page .hero-visual-line {
  display: block;
  height: 8px;
  width: 100%;
  border-radius: 999px;
  background: rgba(47, 85, 151, 0.16);
  margin-top: 8px;
}
.armely-custom-development-page .hero-visual-line.short { width: 68%; }
.armely-custom-development-page .icon-svg {
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
.armely-custom-development-page .vibe-card-icon,
.armely-custom-development-page .vibe-risk-icon,
.armely-custom-development-page .deliver-icon,
.armely-custom-development-page .uc-icon,
.armely-custom-development-page .why-icon {
  color: var(--blue);
}
.armely-custom-development-page .vibe-card-icon,
.armely-custom-development-page .vibe-risk-icon {
  width: 42px;
  height: 42px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-custom-development-page .vibe-risk-icon { width: 36px; height: 36px; border-radius: 10px; }
.armely-custom-development-page .deliver-icon .icon-svg,
.armely-custom-development-page .uc-icon .icon-svg,
.armely-custom-development-page .why-icon .icon-svg { width: 23px; height: 23px; }
.armely-custom-development-page .uc-icon {
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
  .armely-custom-development-page .hero-inner { grid-template-columns: 1fr; gap: 32px; }
  .armely-custom-development-page .hero-visual { min-height: 320px; }
}
@media (max-width: 680px) {
  .armely-custom-development-page .hero { padding: 104px 22px 64px; }
  .armely-custom-development-page .hero-trust { grid-template-columns: 1fr; }
  .armely-custom-development-page .hero-visual { display: none; }
  .armely-custom-development-page .hero-actions a { width: 100%; text-align: center; }
}

/* Focused cleanup for the Generative AI detail page */
.armely-custom-development-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  align-items: center;
  background: #173b67;
  border-radius: 0;
}
.armely-custom-development-page .hero::after,
.armely-custom-development-page .hero-bg-glow,
.armely-custom-development-page .hero-visual {
  display: none;
}
.armely-custom-development-page .hero-inner {
  width: min(1040px, 100%);
  display: block;
}
.armely-custom-development-page .hero-copy {
  max-width: 760px;
}
.armely-custom-development-page .hero-eyebrow {
  margin-bottom: 18px;
}
.armely-custom-development-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.72);
}
.armely-custom-development-page .eyebrow-partner,
.armely-custom-development-page .hero-trust {
  display: none;
}
.armely-custom-development-page .hero h1 {
  max-width: 760px;
  margin-bottom: 22px;
}
.armely-custom-development-page .hero-sub {
  max-width: 680px;
  margin-bottom: 34px;
}
.armely-custom-development-page .hero-actions {
  margin-bottom: 0;
}
.armely-custom-development-page .hero .btn-primary,
.armely-custom-development-page .hero .btn-outline {
  border-radius: 0;
}
.armely-custom-development-page .vibe-section {
  background: #fff;
  padding: 84px 56px;
}
.armely-custom-development-page .vibe-section .section-inner {
  max-width: 920px;
}
.armely-custom-development-page .vibe-section .section-title,
.armely-custom-development-page .vibe-section .section-body {
  max-width: 820px;
}
.armely-custom-development-page .vibe-section .section-body {
  margin-bottom: 28px;
}
.armely-custom-development-page .vibe-two-col {
  grid-template-columns: 1fr;
  gap: 18px;
}
.armely-custom-development-page .vibe-card,
.armely-custom-development-page .vibe-answer-card {
  border-radius: 0;
  box-shadow: none;
}
.armely-custom-development-page .vibe-card-header {
  padding: 18px 20px;
}
.armely-custom-development-page .vibe-card-body {
  padding: 8px 20px 12px;
}
.armely-custom-development-page .vibe-risk {
  padding: 12px 0;
}
.armely-custom-development-page .vibe-right {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
}
.armely-custom-development-page .vibe-answer-card {
  margin-bottom: 0;
  padding: 20px;
  background: #f7f9fc;
}
.armely-custom-development-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-custom-development-page .why > .section-inner > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-custom-development-page section:not(.hero) > .section-inner > .section-title,
.armely-custom-development-page .why > .section-inner > .section-title {
  max-width: 880px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-custom-development-page section:not(.hero) > .section-inner > .section-body,
.armely-custom-development-page .why > .section-inner > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-custom-development-page .spectrum-grid,
.armely-custom-development-page .delivers-grid,
.armely-custom-development-page .steps-row,
.armely-custom-development-page .uc-grid,
.armely-custom-development-page .testi-grid,
.armely-custom-development-page .why-two-col {
  margin-top: 56px;
}
.armely-custom-development-page .why-two-col {
  align-items: stretch;
}
.armely-custom-development-page .why-list {
  margin-top: 0;
}
.armely-custom-development-page .why-list,
.armely-custom-development-page .partner-block {
  height: 100%;
}
@media (max-width: 900px) {
  .armely-custom-development-page .hero {
    padding: 118px 24px 76px;
  }
  .armely-custom-development-page .vibe-section {
    padding: 72px 24px;
  }
  .armely-custom-development-page .vibe-right {
    grid-template-columns: 1fr;
  }
}



/* Final compact modern cleanup */
.armely-custom-development-page {
  --blue: #2f5597;
  --blue-lt: #4779bd;
  --navy-mid: #f6f8fc;
  --navy-card: #ffffff;
  --text-body: #334155;
  --text-muted: #667085;
  --border: rgba(47, 85, 151, 0.14);
}
.armely-custom-development-page .hero {
  min-height: auto !important;
  padding: 86px 56px 70px !important;
  background: linear-gradient(135deg, #173b67 0%, #234f86 100%) !important;
  border-radius: 0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}
.armely-custom-development-page .hero::after,
.armely-custom-development-page .hero-bg-glow,
.armely-custom-development-page .hero-visual {
  display: none !important;
}
.armely-custom-development-page .hero-inner {
  width: min(1120px, 100%) !important;
  margin: 0 auto !important;
  display: block !important;
}
.armely-custom-development-page .hero-copy {
  max-width: 860px !important;
}
.armely-custom-development-page .hero-eyebrow {
  margin-bottom: 18px !important;
}
.armely-custom-development-page .eyebrow-badge {
  display: inline-flex !important;
  background: rgba(255,255,255,0.10) !important;
  border: 1px solid rgba(255,255,255,0.22) !important;
  border-radius: 999px !important;
  padding: 7px 14px !important;
  color: rgba(255,255,255,0.88) !important;
}
.armely-custom-development-page .eyebrow-partner {
  display: inline-flex !important;
  color: rgba(255,255,255,0.66) !important;
}
.armely-custom-development-page .hero h1 {
  max-width: 900px !important;
  margin-bottom: 18px !important;
  font-size: clamp(1.75rem, 3.2vw, 2.7rem);
  line-height: 1.05 !important;
  letter-spacing: -0.04em !important;
}
.armely-custom-development-page .hero-sub {
  max-width: 760px !important;
  margin-bottom: 28px !important;
  font-size: 1rem !important;
  line-height: 1.7 !important;
}
.armely-custom-development-page .hero-actions {
  margin-bottom: 0 !important;
  gap: 12px !important;
}
.armely-custom-development-page .hero .btn-primary,
.armely-custom-development-page .hero .btn-outline,
.armely-custom-development-page .btn-primary,
.armely-custom-development-page .btn-outline,
.armely-custom-development-page .form-submit {
  border-radius: 8px !important;
}
.armely-custom-development-page section {
  padding: 68px 56px !important;
}
.armely-custom-development-page .section-inner {
  max-width: 1120px !important;
}
.armely-custom-development-page .section-eyebrow {
  margin-bottom: 10px !important;
}
.armely-custom-development-page .section-title {
  margin-bottom: 14px !important;
}
.armely-custom-development-page .section-body {
  margin-bottom: 28px !important;
  line-height: 1.65 !important;
}
.armely-custom-development-page .spectrum-grid,
.armely-custom-development-page .vibe-two-col,
.armely-custom-development-page .delivers-grid,
.armely-custom-development-page .steps-row,
.armely-custom-development-page .uc-grid,
.armely-custom-development-page .testi-grid,
.armely-custom-development-page .why-two-col {
  margin-top: 34px !important;
  gap: 20px !important;
}
.armely-custom-development-page .spectrum-grid,
.armely-custom-development-page .why-two-col {
  grid-template-columns: 1fr 1fr !important;
}
.armely-custom-development-page .spectrum-row {
  margin-top: 0 !important;
  gap: 10px !important;
}
.armely-custom-development-page .spectrum-level,
.armely-custom-development-page .deliver-card,
.armely-custom-development-page .uc-card,
.armely-custom-development-page .testi-card,
.armely-custom-development-page .vibe-answer-card,
.armely-custom-development-page .partner-block,
.armely-custom-development-page .cta-form,
.armely-custom-development-page .vibe-card,
.armely-custom-development-page .platform-card {
  border-radius: 14px !important;
  box-shadow: 0 14px 36px rgba(18, 47, 82, 0.08) !important;
}
.armely-custom-development-page .deliver-card,
.armely-custom-development-page .uc-card,
.armely-custom-development-page .testi-card {
  padding: 24px 22px !important;
}
.armely-custom-development-page .deliver-icon,
.armely-custom-development-page .uc-icon,
.armely-custom-development-page .why-icon,
.armely-custom-development-page .vibe-card-icon,
.armely-custom-development-page .vibe-risk-icon {
  border-radius: 12px !important;
  color: var(--blue) !important;
}
.armely-custom-development-page .vibe-section {
  padding: 68px 56px !important;
  background: #fff !important;
}
.armely-custom-development-page .vibe-section .section-inner {
  max-width: 1120px !important;
}
.armely-custom-development-page .vibe-two-col {
  grid-template-columns: 1fr 1fr !important;
  align-items: stretch !important;
}
.armely-custom-development-page .vibe-card-body {
  padding: 18px 20px !important;
}
.armely-custom-development-page .vibe-right {
  display: flex !important;
  flex-direction: column !important;
  gap: 12px !important;
}
.armely-custom-development-page .vibe-answer-card {
  padding: 20px !important;
  background: #f8fafd !important;
}
.armely-custom-development-page .steps-row {
  border-radius: 14px !important;
  overflow: hidden !important;
}
.armely-custom-development-page .step {
  padding: 24px 18px !important;
}
.armely-custom-development-page .step-num {
  font-size: 2rem !important;
  margin-bottom: 10px !important;
}
.armely-custom-development-page .why-list {
  margin-top: 0 !important;
}
.armely-custom-development-page .why-list li {
  padding: 16px 0 !important;
}
.armely-custom-development-page .partner-block-top,
.armely-custom-development-page .p-stat {
  padding: 22px !important;
}
.armely-custom-development-page .cta-inner {
  padding: 68px 56px !important;
  gap: 40px !important;
}
@media (max-width: 900px) {
  .armely-custom-development-page .hero { padding: 88px 24px 58px !important; }
  .armely-custom-development-page section,
  .armely-custom-development-page .vibe-section { padding: 56px 24px !important; }
  .armely-custom-development-page .spectrum-grid,
  .armely-custom-development-page .vibe-two-col,
  .armely-custom-development-page .why-two-col { grid-template-columns: 1fr !important; }
  .armely-custom-development-page .delivers-grid,
  .armely-custom-development-page .uc-grid { grid-template-columns: 1fr 1fr !important; }
  .armely-custom-development-page .cta-inner { padding: 56px 24px !important; grid-template-columns: 1fr !important; }
}
@media (max-width: 600px) {
  .armely-custom-development-page .hero h1 { font-size: clamp(1.75rem, 3.2vw, 2.7rem); }
  .armely-custom-development-page .hero-eyebrow { flex-direction: column !important; align-items: flex-start !important; gap: 8px !important; }
  .armely-custom-development-page .hero-actions a { width: 100% !important; text-align: center !important; }
  .armely-custom-development-page .delivers-grid,
  .armely-custom-development-page .uc-grid { grid-template-columns: 1fr !important; }
}



.armely-custom-development-page .cr-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:28px; margin-bottom:28px; }
.armely-custom-development-page .cr-card { background:#fff; border:1px solid var(--border); border-radius:14px; padding:22px; box-shadow:0 14px 36px rgba(18,47,82,0.08); }
.armely-custom-development-page .cr-label { display:flex; align-items:center; gap:9px; margin-bottom:10px; }
.armely-custom-development-page .cr-check { width:20px; height:20px; display:flex; align-items:center; justify-content:center; background:var(--blue-dim); border:1px solid var(--blue-dim2); border-radius:50%; flex-shrink:0; color:var(--blue); }
.armely-custom-development-page .cr-check .icon-svg { width:11px; height:11px; stroke-width:3; }
.armely-custom-development-page .cr-industry { font-size:0.875rem; font-weight:700; color:#162b49; }
.armely-custom-development-page .cr-desc { font-size:0.84rem; color:var(--text-muted); line-height:1.65; margin:0; }
.armely-custom-development-page .cr-cta { text-align:center; margin-top:8px; }
.armely-custom-development-page .cr-btn { display:inline-flex; align-items:center; gap:10px; background:#1a2e52; color:#fff; border-radius:8px; padding:13px 28px; text-decoration:none; font-size:0.875rem; font-weight:600; }
.armely-custom-development-page .cr-btn:hover { background:var(--blue); }
.armely-custom-development-page .cr-btn .icon-svg { width:18px; height:18px; }
@media (max-width:900px) { .armely-custom-development-page .cr-grid { grid-template-columns:1fr 1fr; } }
@media (max-width:600px) { .armely-custom-development-page .cr-grid { grid-template-columns:1fr; } }
</style>
<div class="armely-custom-development-page">
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-inner">
    <div class="hero-copy">
      <div class="hero-eyebrow">
        <span class="eyebrow-badge">Custom Application and Web Development</span>
        <span class="eyebrow-partner">Delivered by a certified Microsoft partner</span>
      </div>
      <h1>When off-the-shelf software<br>stops fitting your business.</h1>
      <p class="hero-sub">Armely designs and builds custom web applications, internal tools, customer-facing portals, and data-driven platforms on the Microsoft stack, delivered with the documentation and governance your team needs to own them.</p>
      <div class="hero-actions">
        <a href="#contact" class="btn-primary">Book a Free Assessment</a>
        <a href="#delivers" class="btn-outline">See What We Do</a>
      </div>
    </div>
  </div>
</section>

<section class="spectrum"><div class="section-inner"><div class="section-eyebrow">When Custom Development Makes Sense</div><h2 class="section-title">Not every problem needs custom software. When it does, it really does.</h2><p class="section-body">Before recommending custom development, Armely evaluates whether Power Platform, SharePoint, or a configured Microsoft product can solve the problem faster and at lower cost. When the answer is no, we build.</p>
<div class="spectrum-grid"><div class="spectrum-row">
<div class="spectrum-level highlight"><span class="spectrum-num">Build</span><div><div class="spectrum-content-title">When your workflow is genuinely unique</div><div class="spectrum-content-desc">Your process does not map to any standard product. Compromises in existing tools are affecting how your business operates. A custom application built around your actual workflow is more efficient than forcing your team to work around a product's assumptions.</div></div></div>
<div class="spectrum-level highlight"><span class="spectrum-num">Build</span><div><div class="spectrum-content-title">When you need full control over user experience</div><div class="spectrum-content-desc">Customer-facing applications where brand, performance, and user experience are competitive differentiators. A portal, marketplace, or self-service tool where the interface is part of the product itself.</div></div></div>
<div class="spectrum-level highlight"><span class="spectrum-num">Build</span><div><div class="spectrum-content-title">When replacing a legacy system with no viable migration target</div><div class="spectrum-content-desc">An on-premises application your business depends on that no commercial product replicates. A rebuild on a modern, maintainable stack with documentation and a CI/CD pipeline.</div></div></div>
<div class="spectrum-level"><span class="spectrum-num">Configure</span><div><div class="spectrum-content-title">Consider Power Platform first for internal tooling</div><div class="spectrum-content-desc">Many internal workflow and data capture needs are faster and cheaper to solve with Power Apps and Power Automate. Armely will tell you honestly when that is the better path.</div></div></div>
</div><div><div class="platform-card"><div class="platform-header"><div class="platform-dots"><span></span><span></span><span></span></div><span class="platform-header-title">Armely Technology Stack</span></div><div class="platform-body"><div class="plat-band band-tools"><div class="plat-band-label">Front End</div><div class="plat-chips"><span class="plat-chip">React</span><span class="plat-chip">TypeScript</span><span class="plat-chip">Next.js</span><span class="plat-chip">Tailwind CSS</span><span class="plat-chip">Blazor</span></div></div><div class="band-arrow">&#8597;</div><div class="plat-band band-data"><div class="plat-band-label">Back End and Data</div><div class="plat-chips"><span class="plat-chip">ASP.NET Core</span><span class="plat-chip">C#</span><span class="plat-chip">Python</span><span class="plat-chip">Node.js</span><span class="plat-chip">Azure SQL</span><span class="plat-chip">SQL Server</span><span class="plat-chip">Cosmos DB</span><span class="plat-chip">Dataverse</span></div></div><div class="band-arrow">&#8597;</div><div class="plat-band band-gov"><div class="plat-band-label">Infrastructure and DevOps</div><div class="plat-chips"><span class="plat-chip">Azure App Service</span><span class="plat-chip">Azure Functions</span><span class="plat-chip">Azure DevOps</span><span class="plat-chip">GitHub Actions</span><span class="plat-chip">Docker</span><span class="plat-chip">Azure API Management</span></div></div></div></div></div></div></div></section>
<section class="delivers" id="delivers"><div class="section-inner"><div class="section-eyebrow">What Armely Delivers</div><h2 class="section-title">Custom software built to be owned, maintained, and extended by your team.</h2><p class="section-body">Every Armely application is delivered with full source code ownership, documentation, and a handover process designed so your team or a future vendor can maintain and extend it.</p>
<div class="delivers-grid"><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div><div class="deliver-title">Web Application Development</div><div class="deliver-desc">Full-stack web applications built on ASP.NET Core and React, deployed on Azure. From internal business tools and management dashboards to customer-facing platforms.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg></div><div class="deliver-title">Customer and Partner Portals</div><div class="deliver-desc">Secure, branded portals that give customers, partners, or suppliers authenticated access to your business data and services, built on Azure with Entra ID authentication.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M21 12a9 9 0 0 1-15.5 6.2L3 16"/><path d="M3 21v-5h5"/><path d="M3 12A9 9 0 0 1 18.5 5.8L21 8"/><path d="M21 3v5h-5"/></svg></div><div class="deliver-title">Legacy Application Modernization</div><div class="deliver-desc">We rebuild aging on-premises applications on a modern, maintainable stack, preserving the business logic and data your organization depends on.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 8V4H8"/><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 11v2"/><path d="M15 11v2"/></svg></div><div class="deliver-title">AI-Integrated Applications</div><div class="deliver-desc">Web applications with Azure AI capabilities embedded directly into the user experience, including natural language search, document processing, and copilot-style assistants.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg></div><div class="deliver-title">Data Dashboards and Reporting Tools</div><div class="deliver-desc">Custom reporting applications and operational dashboards built when Power BI does not meet the interaction requirements, with direct database connections and real-time data.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22v-5"/><path d="M9 8V2"/><path d="M15 8V2"/><path d="M18 8v5a4 4 0 0 1-4 4h-4a4 4 0 0 1-4-4V8Z"/></svg></div><div class="deliver-title">Microsoft 365 and Dynamics 365 Extensions</div><div class="deliver-desc">Custom extensions beyond what Power Platform can deliver, including SharePoint Framework web parts, Dynamics 365 plugins, custom connectors, and Azure Function-based integrations.</div></div></div></div></section>
<section class="journey"><div class="section-inner"><div class="section-eyebrow">The Armely Delivery Process</div><h2 class="section-title">From requirements to a production application your team can maintain.</h2><p class="section-body">Custom software projects fail most often because requirements are not properly defined, scope expands without governance, or the delivered code is not maintainable.</p>
<div class="steps-row"><div class="step"><div class="step-num">01</div><div class="step-title">Discovery and Scoping</div><div class="step-desc">We document requirements, user stories, data flows, and integration points, and confirm whether a low-code approach could solve the problem instead.</div><span class="step-tag">Free</span></div><div class="step"><div class="step-num">02</div><div class="step-title">Architecture and Design</div><div class="step-desc">Technical architecture, data model, API contracts, and UX wireframes produced and reviewed. Azure infrastructure sized and costed at this stage.</div><span class="step-tag">Weeks 1-2</span></div><div class="step"><div class="step-num">03</div><div class="step-title">Iterative Build</div><div class="step-desc">Development in two-week sprints with working software demonstrated at the end of each sprint. Scope changes are evaluated and priced transparently.</div><span class="step-tag">Weeks 3 onward</span></div><div class="step"><div class="step-num">04</div><div class="step-title">Testing and Launch</div><div class="step-desc">User acceptance testing, security review, performance testing, and a managed production deployment with rollback procedures in place before go-live.</div><span class="step-tag">Final 2 weeks</span></div><div class="step"><div class="step-num">05</div><div class="step-title">Handover and Support</div><div class="step-desc">Full source code, documentation, runbooks, and team training. Post-launch support and ongoing development available on a retained basis.</div><span class="step-tag">Ongoing</span></div></div></div></section>
<section class="usecases"><div class="section-inner"><div class="section-eyebrow">What We Build Most Often</div><h2 class="section-title">The application types Armely delivers across industries.</h2><p class="section-body">Each starts with a business problem, not a technology preference.</p>
<div class="uc-grid"><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></span><div class="uc-title">Healthcare and Clinical Applications</div><div class="uc-desc">Custom applications for patient data management, clinical workflow support, reporting tools, and staff scheduling built to HIPAA compliance standards on Azure.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg></span><div class="uc-title">Education Management Systems</div><div class="uc-desc">Student information tools, enrollment portals, staff scheduling applications, and reporting dashboards for school districts and higher education institutions.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg></span><div class="uc-title">Operations and Logistics Tools</div><div class="uc-desc">Custom inventory management, dispatch and scheduling applications, field operations tools, and supply chain dashboards built when commercial products do not fit.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span><div class="uc-title">Financial and Compliance Applications</div><div class="uc-desc">Custom reporting tools, audit management applications, budget tracking systems, and compliance workflow platforms for finance teams.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span><div class="uc-title">Customer-Facing Web Platforms</div><div class="uc-desc">Self-service portals, booking systems, account management tools, and product configuration applications where quality of experience directly affects revenue.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M21 12a9 9 0 0 1-15.5 6.2L3 16"/><path d="M3 21v-5h5"/><path d="M3 12A9 9 0 0 1 18.5 5.8L21 8"/><path d="M21 3v5h-5"/></svg></span><div class="uc-title">Legacy System Replacement</div><div class="uc-desc">On-premises applications that the business cannot replace with a commercial product because of unique business logic, data structures, or integration requirements.</div></div></div></div></section>
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
<section class="why"><div class="section-inner"><div class="section-eyebrow">Why Armely</div><h2 class="section-title">Custom development that you own, understand, and can maintain.</h2><p class="section-body">The most common failure in custom software is not in the initial delivery. It is what happens six months later when a requirement changes and no one knows how the application works.</p>
<div class="why-two-col"><div><ul class="why-list"><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg></div><div><div class="why-item-title">Documentation Is Not Optional</div><div class="why-item-desc">Every application Armely delivers includes technical documentation, a deployment runbook, and user guides. We write documentation as part of the build.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></div><div><div class="why-item-title">Built on the Microsoft Stack You Already Own</div><div class="why-item-desc">We build on Azure, .NET, and the Microsoft ecosystem because your organization already has licenses, security controls, and operational familiarity.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div><div><div class="why-item-title">Regulated Industry Experience</div><div class="why-item-desc">We have delivered custom applications for healthcare providers, school districts, and enterprise clients where security, compliance, and data governance are non-negotiable.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg></div><div><div class="why-item-title">We Will Tell You When Not to Build</div><div class="why-item-desc">If Power Platform or a configured Microsoft product can solve your problem faster and at lower cost, we will tell you that before starting a custom development engagement.</div></div></li></ul></div>
<div><div class="partner-block"><div class="partner-block-top"><div class="partner-label">Microsoft Authorized Partner</div><p class="partner-text">Armely's Microsoft partnership gives us access to Azure credits, technical pre-sales resources, and architectural guidance for complex custom development engagements.</p></div><div class="partner-stats"><div class="p-stat"><div class="p-stat-num">.NET<span></span></div><div class="p-stat-label">consistently ranked among top-performing web frameworks in independent benchmarks</div></div><div class="p-stat"><div class="p-stat-num">60<span>+</span></div><div class="p-stat-label">Azure regions for scalable, compliant cloud deployments globally</div></div><div class="p-stat"><div class="p-stat-num">100<span>%</span></div><div class="p-stat-label">of Armely applications delivered with full source code ownership and documentation</div></div><div class="p-stat"><div class="p-stat-num">0<span></span></div><div class="p-stat-label">applications delivered without a CI/CD pipeline and automated test coverage</div></div></div></div></div></div></div></section>
<section class="cta-section" id="contact"><div class="cta-inner"><div><div class="section-eyebrow">Get Started</div><h2 class="section-title">Tell us what you need to build. We will tell you the best way to build it.</h2><p class="section-body">Book a free 30-minute discovery call. We will assess whether a custom build or a configured Microsoft product is the right answer.</p><div style="margin-top:28px;display:flex;flex-direction:column;gap:12px;"><div class="trust-item"><span class="trust-dot" style="background:var(--blue);"></span><span class="trust-text" style="color:var(--text-body);">Free assessment, no commitment required</span></div><div class="trust-item"><span class="trust-dot" style="background:var(--blue);"></span><span class="trust-text" style="color:var(--text-body);">Recommendation and partner pricing included</span></div><div class="trust-item"><span class="trust-dot" style="background:var(--blue);"></span><span class="trust-text" style="color:var(--text-body);">Response within one business day</span></div></div></div><div class="cta-form"><div class="form-title">Book Your Free Assessment</div><div class="form-sub">Tell us about your situation.</div><div class="form-row"><label>Full Name</label><input type="text" placeholder="Jane Smith"></div><div class="form-row"><label>Business Email</label><input type="email" placeholder="jane@yourcompany.com"></div><div class="form-row"><label>Company Name</label><input type="text" placeholder="Acme Corp"></div><div class="form-row"><label>Primary Need</label><select><option value="">Select...</option><option>Customer or partner portal</option><option>Internal business tool or workflow application</option><option>Replace a legacy on-premises application</option><option>Data dashboard or reporting tool</option><option>AI-integrated web application</option><option>Microsoft 365 or Dynamics 365 extension</option><option>Not sure yet, need a recommendation</option></select></div><button class="form-submit">Request Free Discovery Call</button><div class="form-note">No spam. No sales pressure. Just a useful conversation.</div></div></div></section>
</div>
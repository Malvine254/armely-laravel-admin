<style>


.armely-training-page *, .armely-training-page *::before, .armely-training-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-training-page {
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

.armely-training-page { scroll-behavior: smooth; }
.armely-training-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* NAV */
.armely-training-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-training-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-training-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-training-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-training-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-training-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-training-page .nav-links a:hover { color: #fff; }
.armely-training-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-training-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* HERO */
.armely-training-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-training-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-training-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-training-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-training-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-training-page .hero h1 { font-size: clamp(1.75rem, 3.2vw, 2.7rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-training-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-training-page .hero-sub { font-size: 0.98rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.72; }
.armely-training-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-training-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-training-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-training-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-training-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-training-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-training-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-training-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-training-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-training-page .trust-text strong { color: #fff; font-weight: 600; }

  /* SECTIONS */
.armely-training-page section { padding: 96px 56px; }
.armely-training-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-training-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-training-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 640px; }
.armely-training-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* AI SPECTRUM */
.armely-training-page .spectrum { background: var(--navy-mid); }
.armely-training-page .spectrum-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-training-page .spectrum-row { display: flex; flex-direction: column; gap: 10px; margin-top: 32px; }
.armely-training-page .spectrum-level { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; transition: border-color 0.2s; }
.armely-training-page .spectrum-level:hover { border-color: rgba(41,78,139,0.3); }
.armely-training-page .spectrum-level.highlight { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-training-page .spectrum-num { font-size: 0.68rem; font-weight: 800; color: var(--blue); background: var(--blue-dim2); border-radius: 4px; padding: 2px 7px; flex-shrink: 0; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.08em; }
.armely-training-page .spectrum-content-title { font-size: 0.875rem; font-weight: 700; color: #1A2540; margin-bottom: 3px; }
.armely-training-page .spectrum-content-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }

  /* Platform visual */
.armely-training-page .platform-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-training-page .platform-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-training-page .platform-dots { display: flex; gap: 6px; }
.armely-training-page .platform-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-training-page .platform-header-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-training-page .platform-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.armely-training-page .plat-band { border-radius: 9px; padding: 13px 16px; }
.armely-training-page .plat-band-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
.armely-training-page .plat-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.armely-training-page .plat-chip { font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.armely-training-page .band-tools { background: var(--blue-dim); }
.armely-training-page .band-tools .plat-band-label { color: var(--blue); }
.armely-training-page .band-tools .plat-chip { background: var(--blue-dim2); color: var(--blue); }
.armely-training-page .band-data { background: rgba(41,78,139,0.05); }
.armely-training-page .band-data .plat-band-label { color: var(--blue); }
.armely-training-page .band-data .plat-chip { background: rgba(41,78,139,0.1); color: var(--blue); }
.armely-training-page .band-gov { background: var(--blue); }
.armely-training-page .band-gov .plat-band-label { color: rgba(255,255,255,0.7); }
.armely-training-page .band-gov .plat-chip { background: rgba(255,255,255,0.15); color: #fff; }
.armely-training-page .band-arrow { text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2px 0; }

  /* VIBE CODING CALLOUT */
.armely-training-page .vibe-section { background: var(--navy); }
.armely-training-page .vibe-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: start; }
.armely-training-page .vibe-left { }
.armely-training-page .vibe-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-training-page .vibe-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
.armely-training-page .vibe-card-icon { font-size: 1.4rem; }
.armely-training-page .vibe-card-title { font-size: 1rem; font-weight: 700; color: #1A2540; }
.armely-training-page .vibe-card-subtitle { font-size: 0.78rem; color: var(--text-muted); }
.armely-training-page .vibe-card-body { padding: 24px; }
.armely-training-page .vibe-risk { display: flex; align-items: flex-start; gap: 12px; padding: 14px 0; border-bottom: 1px solid var(--border); }
.armely-training-page .vibe-risk:last-child { border-bottom: none; }
.armely-training-page .vibe-risk-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
.armely-training-page .vibe-risk-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 2px; }
.armely-training-page .vibe-risk-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }
.armely-training-page .vibe-right { }
.armely-training-page .vibe-answer-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 28px; margin-bottom: 12px; }
.armely-training-page .vibe-answer-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--blue); margin-bottom: 10px; }
.armely-training-page .vibe-answer-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.75; }

  /* DELIVERS */
.armely-training-page .delivers { background: var(--navy-mid); }
.armely-training-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-training-page .deliver-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-training-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-training-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-training-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-training-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-training-page .journey { background: var(--navy); }
.armely-training-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-training-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-training-page .step:last-child { border-right: none; }
.armely-training-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-training-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-training-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-training-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* USE CASES */
.armely-training-page .usecases { background: var(--navy-mid); }
.armely-training-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-training-page .uc-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-training-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-training-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-training-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-training-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* TESTIMONIALS */
.armely-training-page .testimonials { background: var(--navy); padding: 96px 56px; }
.armely-training-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-training-page .testi-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-training-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-training-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-training-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-training-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-training-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-training-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-training-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY ARMELY */
.armely-training-page .why { background: var(--navy-mid); }
.armely-training-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-training-page .why-list { list-style: none; margin-top: 36px; }
.armely-training-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-training-page .why-list li:last-child { border-bottom: none; }
.armely-training-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-training-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-training-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-training-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-training-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-training-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-training-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-training-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-training-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-training-page .p-stat:nth-child(2) { border-right: none; }
.armely-training-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-training-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-training-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-training-page .p-stat-num span { color: var(--blue); }
.armely-training-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* CTA */
.armely-training-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-training-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-training-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-training-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-training-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-training-page .form-row { margin-bottom: 14px; }
.armely-training-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-training-page .form-row input, .armely-training-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-training-page .form-row input:focus, .armely-training-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-training-page .form-row select option { background: #fff; color: #1A2540; }
.armely-training-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-training-page .form-submit:hover { background: var(--blue-lt); }
.armely-training-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* FOOTER */
.armely-training-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-training-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-training-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-training-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-training-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-training-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-training-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* RESPONSIVE */
  @media (max-width: 900px) {
.armely-training-page nav { padding: 16px 24px; }
.armely-training-page .nav-links { display: none; }
.armely-training-page section { padding: 72px 24px; }
.armely-training-page .hero { padding: 110px 24px 72px; }
.armely-training-page .spectrum-grid, .armely-training-page .vibe-two-col, .armely-training-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-training-page .delivers-grid, .armely-training-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-training-page .steps-row { grid-template-columns: 1fr; }
.armely-training-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-training-page .step:last-child { border-bottom: none; }
.armely-training-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-training-page .testimonials { padding: 72px 24px; }
.armely-training-page .testi-grid { grid-template-columns: 1fr; }
.armely-training-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-training-page .delivers-grid, .armely-training-page .uc-grid { grid-template-columns: 1fr; }
.armely-training-page .partner-stats { grid-template-columns: 1fr; }
.armely-training-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-training-page {
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
.armely-training-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-training-page .hero::after {
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
.armely-training-page .section-title,
.armely-training-page .deliver-title,
.armely-training-page .uc-title,
.armely-training-page .step-title,
.armely-training-page .why-item-title,
.armely-training-page .form-title {
  color: #162b49;
}
.armely-training-page .deliver-card,
.armely-training-page .uc-card,
.armely-training-page .testi-card,
.armely-training-page .platform-card,
.armely-training-page .partner-block,
.armely-training-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-training-page .deliver-card:hover,
.armely-training-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-training-page .btn-primary,
.armely-training-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-training-page .btn-primary:hover,
.armely-training-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-training-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-training-page nav,
.armely-training-page footer {
  display: none;
}


/* Modern layout update: tighter first section and SVG icon system */
.armely-training-page .hero {
  min-height: auto;
  padding: 128px 32px 86px;
  isolation: isolate;
}
.armely-training-page .hero-inner {
  width: min(1160px, 100%);
  margin: 0 auto;
  display: grid;
  grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.75fr);
  align-items: center;
  gap: 56px;
  position: relative;
  z-index: 1;
}
.armely-training-page .hero-copy { max-width: 760px; }
.armely-training-page .hero h1 { max-width: 760px; margin-bottom: 20px; }
.armely-training-page .hero-sub { max-width: 640px; margin-bottom: 30px; font-size: 1.02rem; line-height: 1.72; }
.armely-training-page .hero-actions { margin-bottom: 34px; }
.armely-training-page .hero-trust {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  padding-top: 0;
  border-top: 0;
  max-width: 720px;
}
.armely-training-page .hero .trust-item {
  align-items: flex-start;
  background: rgba(255,255,255,0.10);
  border: 1px solid rgba(255,255,255,0.16);
  border-radius: 14px;
  padding: 14px 15px;
  backdrop-filter: blur(10px);
}
.armely-training-page .hero .trust-dot {
  width: 22px;
  height: 22px;
  border-radius: 999px;
  background: rgba(255,255,255,0.16);
  border: 1px solid rgba(255,255,255,0.24);
  position: relative;
  margin-top: 1px;
}
.armely-training-page .hero .trust-dot::after {
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
.armely-training-page .hero .trust-text { color: rgba(255,255,255,0.78); line-height: 1.5; }
.armely-training-page .hero-visual {
  min-height: 420px;
  border-radius: 28px;
  position: relative;
  background: linear-gradient(145deg, rgba(255,255,255,0.16), rgba(255,255,255,0.05));
  border: 1px solid rgba(255,255,255,0.18);
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.18), 0 28px 70px rgba(6, 22, 48, 0.24);
  overflow: hidden;
}
.armely-training-page .hero-visual::before {
  content: '';
  position: absolute;
  inset: 36px;
  border: 1px solid rgba(255,255,255,0.14);
  border-radius: 999px;
}
.armely-training-page .hero-visual::after {
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
.armely-training-page .hero-orbit span {
  position: absolute;
  width: 52px;
  height: 52px;
  border-radius: 16px;
  background: rgba(255,255,255,0.14);
  border: 1px solid rgba(255,255,255,0.22);
}
.armely-training-page .hero-orbit span:nth-child(1) { left: 56px; top: 88px; }
.armely-training-page .hero-orbit span:nth-child(2) { right: 72px; top: 138px; }
.armely-training-page .hero-orbit span:nth-child(3) { left: 48%; bottom: 76px; }
.armely-training-page .hero-visual-card {
  position: absolute;
  z-index: 2;
  width: 230px;
  padding: 18px;
  border-radius: 18px;
  background: rgba(255,255,255,0.92);
  box-shadow: 0 24px 54px rgba(9, 31, 63, 0.22);
}
.armely-training-page .hero-visual-card.top { top: 42px; right: 30px; }
.armely-training-page .hero-visual-card.bottom { bottom: 46px; left: 30px; }
.armely-training-page .hero-visual-label {
  display: block;
  font-size: 0.78rem;
  font-weight: 700;
  color: #162b49;
  margin-bottom: 12px;
}
.armely-training-page .hero-visual-line {
  display: block;
  height: 8px;
  width: 100%;
  border-radius: 999px;
  background: rgba(47, 85, 151, 0.16);
  margin-top: 8px;
}
.armely-training-page .hero-visual-line.short { width: 68%; }
.armely-training-page .icon-svg {
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
.armely-training-page .vibe-card-icon,
.armely-training-page .vibe-risk-icon,
.armely-training-page .deliver-icon,
.armely-training-page .uc-icon,
.armely-training-page .why-icon {
  color: var(--blue);
}
.armely-training-page .vibe-card-icon,
.armely-training-page .vibe-risk-icon {
  width: 42px;
  height: 42px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-training-page .vibe-risk-icon { width: 36px; height: 36px; border-radius: 10px; }
.armely-training-page .deliver-icon .icon-svg,
.armely-training-page .uc-icon .icon-svg,
.armely-training-page .why-icon .icon-svg { width: 23px; height: 23px; }
.armely-training-page .uc-icon {
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
  .armely-training-page .hero-inner { grid-template-columns: 1fr; gap: 32px; }
  .armely-training-page .hero-visual { min-height: 320px; }
}
@media (max-width: 680px) {
  .armely-training-page .hero { padding: 104px 22px 64px; }
  .armely-training-page .hero-trust { grid-template-columns: 1fr; }
  .armely-training-page .hero-visual { display: none; }
  .armely-training-page .hero-actions a { width: 100%; text-align: center; }
}

/* Focused cleanup for the Generative AI detail page */
.armely-training-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  align-items: center;
  background: #173b67;
  border-radius: 0;
}
.armely-training-page .hero::after,
.armely-training-page .hero-bg-glow,
.armely-training-page .hero-visual {
  display: none;
}
.armely-training-page .hero-inner {
  width: min(1040px, 100%);
  display: block;
}
.armely-training-page .hero-copy {
  max-width: 760px;
}
.armely-training-page .hero-eyebrow {
  margin-bottom: 18px;
}
.armely-training-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.72);
}
.armely-training-page .eyebrow-partner,
.armely-training-page .hero-trust {
  display: none;
}
.armely-training-page .hero h1 {
  max-width: 760px;
  margin-bottom: 22px;
}
.armely-training-page .hero-sub {
  max-width: 680px;
  margin-bottom: 34px;
}
.armely-training-page .hero-actions {
  margin-bottom: 0;
}
.armely-training-page .hero .btn-primary,
.armely-training-page .hero .btn-outline {
  border-radius: 0;
}
.armely-training-page .vibe-section {
  background: #fff;
  padding: 84px 56px;
}
.armely-training-page .vibe-section .section-inner {
  max-width: 920px;
}
.armely-training-page .vibe-section .section-title,
.armely-training-page .vibe-section .section-body {
  max-width: 820px;
}
.armely-training-page .vibe-section .section-body {
  margin-bottom: 28px;
}
.armely-training-page .vibe-two-col {
  grid-template-columns: 1fr;
  gap: 18px;
}
.armely-training-page .vibe-card,
.armely-training-page .vibe-answer-card {
  border-radius: 0;
  box-shadow: none;
}
.armely-training-page .vibe-card-header {
  padding: 18px 20px;
}
.armely-training-page .vibe-card-body {
  padding: 8px 20px 12px;
}
.armely-training-page .vibe-risk {
  padding: 12px 0;
}
.armely-training-page .vibe-right {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
}
.armely-training-page .vibe-answer-card {
  margin-bottom: 0;
  padding: 20px;
  background: #f7f9fc;
}
.armely-training-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-training-page .why > .section-inner > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-training-page section:not(.hero) > .section-inner > .section-title,
.armely-training-page .why > .section-inner > .section-title {
  max-width: 880px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-training-page section:not(.hero) > .section-inner > .section-body,
.armely-training-page .why > .section-inner > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-training-page .spectrum-grid,
.armely-training-page .delivers-grid,
.armely-training-page .steps-row,
.armely-training-page .uc-grid,
.armely-training-page .testi-grid,
.armely-training-page .why-two-col {
  margin-top: 56px;
}
.armely-training-page .why-two-col {
  align-items: stretch;
}
.armely-training-page .why-list {
  margin-top: 0;
}
.armely-training-page .why-list,
.armely-training-page .partner-block {
  height: 100%;
}
@media (max-width: 900px) {
  .armely-training-page .hero {
    padding: 118px 24px 76px;
  }
  .armely-training-page .vibe-section {
    padding: 72px 24px;
  }
  .armely-training-page .vibe-right {
    grid-template-columns: 1fr;
  }
}



/* Final compact modern cleanup */
.armely-training-page {
  --blue: #2f5597;
  --blue-lt: #4779bd;
  --navy-mid: #f6f8fc;
  --navy-card: #ffffff;
  --text-body: #334155;
  --text-muted: #667085;
  --border: rgba(47, 85, 151, 0.14);
}
.armely-training-page .hero {
  min-height: auto !important;
  padding: 86px 56px 70px !important;
  background: linear-gradient(135deg, #173b67 0%, #234f86 100%) !important;
  border-radius: 0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}
.armely-training-page .hero::after,
.armely-training-page .hero-bg-glow,
.armely-training-page .hero-visual {
  display: none !important;
}
.armely-training-page .hero-inner {
  width: min(1120px, 100%) !important;
  margin: 0 auto !important;
  display: block !important;
}
.armely-training-page .hero-copy {
  max-width: 860px !important;
}
.armely-training-page .hero-eyebrow {
  margin-bottom: 18px !important;
}
.armely-training-page .eyebrow-badge {
  display: inline-flex !important;
  background: rgba(255,255,255,0.10) !important;
  border: 1px solid rgba(255,255,255,0.22) !important;
  border-radius: 999px !important;
  padding: 7px 14px !important;
  color: rgba(255,255,255,0.88) !important;
}
.armely-training-page .eyebrow-partner {
  display: inline-flex !important;
  color: rgba(255,255,255,0.66) !important;
}
.armely-training-page .hero h1 {
  max-width: 900px !important;
  margin-bottom: 18px !important;
  font-size: clamp(1.75rem, 3.2vw, 2.7rem) !important;
  line-height: 1.05 !important;
  letter-spacing: -0.04em !important;
}
.armely-training-page .hero-sub {
  max-width: 760px !important;
  margin-bottom: 28px !important;
  font-size: 0.98rem !important;
  line-height: 1.72 !important;
}
.armely-training-page .hero-actions {
  margin-bottom: 0 !important;
  gap: 12px !important;
}
.armely-training-page .hero .btn-primary,
.armely-training-page .hero .btn-outline,
.armely-training-page .btn-primary,
.armely-training-page .btn-outline,
.armely-training-page .form-submit {
  border-radius: 8px !important;
}
.armely-training-page section {
  padding: 68px 56px !important;
}
.armely-training-page .section-inner {
  max-width: 1120px !important;
}
.armely-training-page .section-eyebrow {
  margin-bottom: 10px !important;
}
.armely-training-page .section-title {
  margin-bottom: 14px !important;
}
.armely-training-page .section-body {
  margin-bottom: 28px !important;
  line-height: 1.65 !important;
}
.armely-training-page .spectrum-grid,
.armely-training-page .vibe-two-col,
.armely-training-page .delivers-grid,
.armely-training-page .steps-row,
.armely-training-page .uc-grid,
.armely-training-page .testi-grid,
.armely-training-page .why-two-col {
  margin-top: 34px !important;
  gap: 20px !important;
}
.armely-training-page .spectrum-grid,
.armely-training-page .why-two-col {
  grid-template-columns: 1fr 1fr !important;
}
.armely-training-page .spectrum-row {
  margin-top: 0 !important;
  gap: 10px !important;
}
.armely-training-page .spectrum-level,
.armely-training-page .deliver-card,
.armely-training-page .uc-card,
.armely-training-page .testi-card,
.armely-training-page .vibe-answer-card,
.armely-training-page .partner-block,
.armely-training-page .cta-form,
.armely-training-page .vibe-card,
.armely-training-page .platform-card {
  border-radius: 14px !important;
  box-shadow: 0 14px 36px rgba(18, 47, 82, 0.08) !important;
}
.armely-training-page .deliver-card,
.armely-training-page .uc-card,
.armely-training-page .testi-card {
  padding: 24px 22px !important;
}
.armely-training-page .deliver-icon,
.armely-training-page .uc-icon,
.armely-training-page .why-icon,
.armely-training-page .vibe-card-icon,
.armely-training-page .vibe-risk-icon {
  border-radius: 12px !important;
  color: var(--blue) !important;
}
.armely-training-page .vibe-section {
  padding: 68px 56px !important;
  background: #fff !important;
}
.armely-training-page .vibe-section .section-inner {
  max-width: 1120px !important;
}
.armely-training-page .vibe-two-col {
  grid-template-columns: 1fr 1fr !important;
  align-items: stretch !important;
}
.armely-training-page .vibe-card-body {
  padding: 18px 20px !important;
}
.armely-training-page .vibe-right {
  display: flex !important;
  flex-direction: column !important;
  gap: 12px !important;
}
.armely-training-page .vibe-answer-card {
  padding: 20px !important;
  background: #f8fafd !important;
}
.armely-training-page .steps-row {
  border-radius: 14px !important;
  overflow: hidden !important;
}
.armely-training-page .step {
  padding: 24px 18px !important;
}
.armely-training-page .step-num {
  font-size: 2rem !important;
  margin-bottom: 10px !important;
}
.armely-training-page .why-list {
  margin-top: 0 !important;
}
.armely-training-page .why-list li {
  padding: 16px 0 !important;
}
.armely-training-page .partner-block-top,
.armely-training-page .p-stat {
  padding: 22px !important;
}
.armely-training-page .cta-inner {
  padding: 68px 56px !important;
  gap: 40px !important;
}
@media (max-width: 900px) {
  .armely-training-page .hero { padding: 88px 24px 58px !important; }
  .armely-training-page section,
  .armely-training-page .vibe-section { padding: 56px 24px !important; }
  .armely-training-page .spectrum-grid,
  .armely-training-page .vibe-two-col,
  .armely-training-page .why-two-col { grid-template-columns: 1fr !important; }
  .armely-training-page .delivers-grid,
  .armely-training-page .uc-grid { grid-template-columns: 1fr 1fr !important; }
  .armely-training-page .cta-inner { padding: 56px 24px !important; grid-template-columns: 1fr !important; }
}
@media (max-width: 600px) {
  .armely-training-page .hero h1 { font-size: clamp(1.6rem, 8vw, 2.2rem) !important; line-height: 1.08 !important; max-width: 820px !important; }
  .armely-training-page .hero-eyebrow { flex-direction: column !important; align-items: flex-start !important; gap: 8px !important; }
  .armely-training-page .hero-actions a { width: 100% !important; text-align: center !important; }
  .armely-training-page .delivers-grid,
  .armely-training-page .uc-grid { grid-template-columns: 1fr !important; }
}



.armely-training-page .cr-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:28px; margin-bottom:28px; }
.armely-training-page .cr-card { background:#fff; border:1px solid var(--border); border-radius:14px; padding:22px; box-shadow:0 14px 36px rgba(18,47,82,0.08); }
.armely-training-page .cr-label { display:flex; align-items:center; gap:9px; margin-bottom:10px; }
.armely-training-page .cr-check { width:20px; height:20px; display:flex; align-items:center; justify-content:center; background:var(--blue-dim); border:1px solid var(--blue-dim2); border-radius:50%; flex-shrink:0; color:var(--blue); }
.armely-training-page .cr-check .icon-svg { width:11px; height:11px; stroke-width:3; }
.armely-training-page .cr-industry { font-size:0.875rem; font-weight:700; color:#162b49; }
.armely-training-page .cr-desc { font-size:0.84rem; color:var(--text-muted); line-height:1.65; margin:0; }
.armely-training-page .cr-cta { text-align:center; margin-top:8px; }
.armely-training-page .cr-btn { display:inline-flex; align-items:center; gap:10px; background:#1a2e52; color:#fff; border-radius:8px; padding:13px 28px; text-decoration:none; font-size:0.875rem; font-weight:600; }
.armely-training-page .cr-btn:hover { background:var(--blue); }
.armely-training-page .cr-btn .icon-svg { width:18px; height:18px; }
@media (max-width:900px) { .armely-training-page .cr-grid { grid-template-columns:1fr 1fr; } }
@media (max-width:600px) { .armely-training-page .cr-grid { grid-template-columns:1fr; } }

.armely-training-page .training-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:20px; margin-top:32px; }
.armely-training-page .training-card { background:#fff; border:1px solid var(--border); border-radius:16px; overflow:hidden; box-shadow:0 14px 36px rgba(18,47,82,0.08); }
.armely-training-page .training-card-header { padding:28px 28px 20px; background:var(--blue-dim); border-bottom:1px solid var(--border); }
.armely-training-page .training-card-eyebrow { font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.12em; color:var(--blue); margin-bottom:8px; }
.armely-training-page .training-card-title { font-size:1.15rem; font-weight:800; color:#162b49; margin-bottom:6px; line-height:1.2; }
.armely-training-page .training-card-sub { font-size:0.82rem; color:var(--text-muted); line-height:1.6; }
.armely-training-page .training-card-body { padding:22px 28px; }
.armely-training-page .training-module { display:flex; align-items:flex-start; gap:10px; margin-bottom:14px; }
.armely-training-page .training-module:last-child { margin-bottom:0; }
.armely-training-page .training-module-icon { width:28px; height:28px; border-radius:8px; background:var(--blue-dim); border:1px solid var(--blue-dim2); display:flex; align-items:center; justify-content:center; flex-shrink:0; color:var(--blue); margin-top:1px; }
.armely-training-page .training-module-icon .icon-svg { width:14px; height:14px; }
.armely-training-page .training-module-text strong { display:block; font-size:0.84rem; font-weight:700; color:#162b49; margin-bottom:2px; }
.armely-training-page .training-module-text span { font-size:0.79rem; color:var(--text-muted); line-height:1.55; }
.armely-training-page .training-card-footer { padding:16px 28px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; background:#fafbfe; }
.armely-training-page .training-card-footer .format-tag { font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.1em; color:var(--text-muted); }
.armely-training-page .training-card-footer .duration-tag { font-size:0.7rem; font-weight:600; color:var(--blue); }
.armely-training-page .delivery-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:28px; }
.armely-training-page .delivery-card { background:#fff; border:1px solid var(--border); border-radius:12px; padding:20px; box-shadow:0 8px 24px rgba(18,47,82,0.06); text-align:center; }
.armely-training-page .delivery-icon { width:44px; height:44px; border-radius:12px; background:var(--blue-dim); border:1px solid var(--blue-dim2); display:flex; align-items:center; justify-content:center; color:var(--blue); margin:0 auto 12px; }
.armely-training-page .delivery-icon .icon-svg { width:22px; height:22px; }
.armely-training-page .delivery-title { font-size:0.9rem; font-weight:700; color:#162b49; margin-bottom:5px; }
.armely-training-page .delivery-desc { font-size:0.8rem; color:var(--text-muted); line-height:1.6; }
.armely-training-page .outcome-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-top:28px; }
.armely-training-page .outcome-card { background:var(--navy-mid); border:1px solid var(--border); border-radius:12px; padding:18px 20px; display:flex; align-items:flex-start; gap:10px; }
.armely-training-page .outcome-check { width:20px; height:20px; border-radius:50%; background:var(--blue-dim); border:1px solid var(--blue-dim2); display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px; color:var(--blue); }
.armely-training-page .outcome-check .icon-svg { width:10px; height:10px; stroke-width:3; }
.armely-training-page .outcome-text { font-size:0.83rem; color:var(--text-body); line-height:1.6; }
@media (max-width:900px) {
  .armely-training-page .training-grid { grid-template-columns:1fr; }
  .armely-training-page .delivery-grid { grid-template-columns:1fr 1fr; }
  .armely-training-page .outcome-grid { grid-template-columns:1fr 1fr; }
}
@media (max-width:600px) {
  .armely-training-page .delivery-grid, .armely-training-page .outcome-grid { grid-template-columns:1fr; }
}


/* Tighter section spacing */
.armely-training-page .spectrum,
.armely-training-page .delivers,
.armely-training-page .usecases,
.armely-training-page .testimonials,
.armely-training-page .why { padding-top:48px !important; padding-bottom:48px !important; }
/* Why section grid fix */
.armely-training-page .why-grid { display:grid; grid-template-columns:1fr 420px; gap:56px; align-items:start; }
.armely-training-page .why-items { margin-top:28px; display:flex; flex-direction:column; gap:0; }
.armely-training-page .why-item { display:flex; align-items:flex-start; gap:14px; padding:18px 0; border-bottom:1px solid var(--border); }
.armely-training-page .why-item:last-child { border-bottom:none; }
.armely-training-page .why-item .why-icon { width:38px; height:38px; border-radius:10px; background:var(--blue-dim); border:1px solid var(--blue-dim2); display:flex; align-items:center; justify-content:center; flex-shrink:0; color:var(--blue); }
.armely-training-page .why-item .why-icon .icon-svg { width:18px; height:18px; }
.armely-training-page .why-item div strong { display:block; font-size:0.9rem; font-weight:700; color:#162b49; margin-bottom:4px; }
.armely-training-page .why-item div p { font-size:0.82rem; color:var(--text-muted); line-height:1.65; margin:0; }
.armely-training-page .partner-col { position:sticky; top:24px; }
.armely-training-page .partner-block { background:#fff; border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:0 14px 36px rgba(18,47,82,0.08); }
.armely-training-page .partner-header { background:var(--blue); color:#fff; font-size:0.78rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; padding:14px 22px; }
.armely-training-page .partner-body { padding:22px; }
.armely-training-page .partner-badge-row { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:16px; }
.armely-training-page .partner-badge { font-size:0.72rem; font-weight:600; color:var(--blue); background:var(--blue-dim); border:1px solid var(--blue-dim2); border-radius:999px; padding:4px 12px; }
.armely-training-page .partner-text { font-size:0.82rem; color:var(--text-muted); line-height:1.7; margin-bottom:20px; }
.armely-training-page .partner-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; padding-top:16px; border-top:1px solid var(--border); }
.armely-training-page .partner-stat { text-align:center; }
.armely-training-page .stat-num { display:block; font-size:1.4rem; font-weight:800; color:var(--blue); letter-spacing:-0.02em; }
.armely-training-page .stat-label { font-size:0.7rem; color:var(--text-muted); line-height:1.3; }
@media (max-width:900px) {
  .armely-training-page .why-grid { grid-template-columns:1fr; gap:32px; }
  .armely-training-page .partner-col { position:static; }
  .armely-training-page .partner-stats { grid-template-columns:repeat(3,1fr); }
}

</style>
<div class="armely-training-page">

<section class="hero">
  <div class="hero-inner">
    <div class="hero-eyebrow-wrap">
      <span class="hero-eyebrow">Training</span>
      <span class="eyebrow-partner">Delivered by a certified Microsoft partner</span>
    </div>
    <h1>Training on your systems. <span class="hl">Not generic demos.</span></h1>
    <p class="hero-sub">Armely delivers hands-on training for Power BI, Power Platform, Generative AI, and Microsoft Copilot. Built around your actual tools, data, and workflows. Not a course. A capability transfer.</p>
    <div class="hero-actions">
      <a href="#contact" class="btn-primary">Book a Training Conversation</a>
      <a href="#programs" class="btn-outline">See Training Programs</a>
    </div>
  </div>
</section>

<section class="spectrum" id="programs">
  <div class="section-inner">
    <div class="section-eyebrow">Training Programs</div>
    <h2 class="section-title">Four programs. Practical outcomes. Real tools.</h2>
    <p class="section-body">Each program is built around what your team actually needs to do, not what looks good in a course catalog. Armely trains on the platforms it implements, which means trainers know the edge cases, the gotchas, and the shortcuts that matter in production.</p>
    <div class="training-grid"><div class="training-card">
      <div class="training-card-header">
        <div class="training-card-eyebrow">Power BI Training</div>
        <div class="training-card-title">Power BI for Analysts and Decision-Makers</div>
        <div class="training-card-sub">From raw data to dashboard. Practical training for the teams who build reports and the leaders who use them.</div>
      </div>
      <div class="training-card-body"><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg></div>
          <div class="training-module-text"><strong>Power BI Desktop Fundamentals</strong><span>Building queries, data models, and calculated columns from scratch.</span></div>
        </div><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m3 17 6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg></div>
          <div class="training-module-text"><strong>DAX Measures and Calculated Fields</strong><span>Writing the formulas that power your KPIs and business metrics.</span></div>
        </div><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
          <div class="training-module-text"><strong>Report Design and UX</strong><span>Building dashboards that communicate clearly, not just display data.</span></div>
        </div><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
          <div class="training-module-text"><strong>Power BI Service and Sharing</strong><span>Publishing, scheduling refreshes, workspaces, and row-level security.</span></div>
        </div><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div>
          <div class="training-module-text"><strong>Gateway and Data Source Setup</strong><span>Connecting to SQL Server, SharePoint, Excel, and cloud sources reliably.</span></div>
        </div></div>
      <div class="training-card-footer">
        <span class="format-tag">Instructor-led or self-paced</span>
        <span class="duration-tag">Half-day, full-day, or 4-week cohort</span>
      </div>
    </div><div class="training-card">
      <div class="training-card-header">
        <div class="training-card-eyebrow">Power Platform Training</div>
        <div class="training-card-title">Power Apps and Power Automate for Business Teams</div>
        <div class="training-card-sub">Train the people closest to your processes to build the tools they need without waiting on IT.</div>
      </div>
      <div class="training-card-body"><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M13 2 3 14h9l-1 8 11-14h-9l1-6Z"/></svg></div>
          <div class="training-module-text"><strong>Power Apps Canvas App Fundamentals</strong><span>Building forms, galleries, and data-connected apps for real workflows.</span></div>
        </div><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M21 12a9 9 0 0 1-15.5 6.2L3 16"/><path d="M3 21v-5h5"/><path d="M3 12A9 9 0 0 1 18.5 5.8L21 8"/><path d="M21 3v5h-5"/></svg></div>
          <div class="training-module-text"><strong>Power Automate Flow Design</strong><span>Automating approvals, notifications, and data movement across systems.</span></div>
        </div><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg></div>
          <div class="training-module-text"><strong>Dataverse Basics</strong><span>Understanding the data layer that powers enterprise Power Platform solutions.</span></div>
        </div><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
          <div class="training-module-text"><strong>Governance and Environment Management</strong><span>ALM, DLP policies, and environment strategy for sustainable adoption.</span></div>
        </div><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg></div>
          <div class="training-module-text"><strong>Maker Enablement Program</strong><span>A structured program to identify, train, and support citizen developers.</span></div>
        </div></div>
      <div class="training-card-footer">
        <span class="format-tag">Instructor-led workshops</span>
        <span class="duration-tag">2-day intensive or 6-week program</span>
      </div>
    </div><div class="training-card">
      <div class="training-card-header">
        <div class="training-card-eyebrow">Generative and Agentic AI Training</div>
        <div class="training-card-title">Generative AI for Business Teams and Developers</div>
        <div class="training-card-sub">Practical AI skills for the people building and using AI tools in your organization, not a general overview of ChatGPT.</div>
      </div>
      <div class="training-card-body"><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 8V4H8"/><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 11v2"/><path d="M15 11v2"/></svg></div>
          <div class="training-module-text"><strong>Prompt Engineering for Business Use</strong><span>Writing effective prompts for document generation, analysis, and summarization.</span></div>
        </div><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg></div>
          <div class="training-module-text"><strong>Azure AI Foundry and Copilot Studio</strong><span>Building custom AI agents grounded in your business data and processes.</span></div>
        </div><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div>
          <div class="training-module-text"><strong>AI Governance and Responsible Use</strong><span>Acceptable use policies, data privacy considerations, and risk frameworks.</span></div>
        </div><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg></div>
          <div class="training-module-text"><strong>Retrieval-Augmented Generation</strong><span>Connecting AI to your internal knowledge bases for accurate, cited responses.</span></div>
        </div><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M13 2 3 14h9l-1 8 11-14h-9l1-6Z"/></svg></div>
          <div class="training-module-text"><strong>Agentic Workflows</strong><span>Building AI agents that complete multi-step tasks autonomously within guardrails.</span></div>
        </div></div>
      <div class="training-card-footer">
        <span class="format-tag">Role-based tracks available</span>
        <span class="duration-tag">1-day overview or 3-week practitioner track</span>
      </div>
    </div><div class="training-card">
      <div class="training-card-header">
        <div class="training-card-eyebrow">Microsoft Copilot Training</div>
        <div class="training-card-title">Microsoft 365 Copilot Adoption and Enablement</div>
        <div class="training-card-sub">Most Copilot rollouts underdeliver because adoption was an afterthought. This training changes that.</div>
      </div>
      <div class="training-card-body"><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
          <div class="training-module-text"><strong>Copilot for End Users</strong><span>Practical use in Teams, Outlook, Word, Excel, and PowerPoint for daily work.</span></div>
        </div><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m3 17 6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg></div>
          <div class="training-module-text"><strong>Copilot for Managers</strong><span>Meeting summaries, coaching, performance review drafting, and status reporting.</span></div>
        </div><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg></div>
          <div class="training-module-text"><strong>Copilot for Analysts</strong><span>Excel, Power BI, and data analysis prompting for faster insight delivery.</span></div>
        </div><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg></div>
          <div class="training-module-text"><strong>Copilot Studio for IT and Developers</strong><span>Building custom Copilot agents for internal workflows and knowledge access.</span></div>
        </div><div class="training-module">
          <div class="training-module-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div>
          <div class="training-module-text"><strong>Change Management and Governance</strong><span>Driving adoption, measuring ROI, and governing usage at the organizational level.</span></div>
        </div></div>
      <div class="training-card-footer">
        <span class="format-tag">Role-based sessions</span>
        <span class="duration-tag">Half-day per role or 4-week rollout program</span>
      </div>
    </div></div>
  </div>
</section>

<section class="delivers">
  <div class="section-inner">
    <div class="section-eyebrow">Delivery Format</div>
    <h2 class="section-title">Training that works for how your team actually operates.</h2>
    <p class="section-body">Armely does not sell pre-recorded video libraries. Every training engagement is live, interactive, and adapted to your organization's environment.</p>
    <div class="delivery-grid"><div class="delivery-card">
      <div class="delivery-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
      <div class="delivery-title">On-Site at Your Location</div>
      <div class="delivery-desc">Armely trainers come to you. Works best for full-team rollouts and hands-on workshops with real data.</div>
    </div><div class="delivery-card">
      <div class="delivery-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
      <div class="delivery-title">Live Virtual Instructor-Led</div>
      <div class="delivery-desc">Real-time sessions with live Q&A, breakout exercises, and follow-up office hours. No pre-recorded content.</div>
    </div><div class="delivery-card">
      <div class="delivery-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg></div>
      <div class="delivery-title">Custom Cohort Programs</div>
      <div class="delivery-desc">Multi-week structured programs with weekly sessions, assignments, and a final applied project using your actual systems.</div>
    </div></div>
  </div>
</section>

<section class="usecases">
  <div class="section-inner">
    <div class="section-eyebrow">What Changes After Training</div>
    <h2 class="section-title">Skills your team will use the week after training ends.</h2>
    <p class="section-body">The measure of good training is what people do differently on Monday morning. Here is what Armely training participants can do independently after program completion.</p>
    <div class="outcome-grid"><div class="outcome-card">
      <div class="outcome-check"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
      <div class="outcome-text">Staff can build Power BI reports without analyst support for every request</div>
    </div><div class="outcome-card">
      <div class="outcome-check"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
      <div class="outcome-text">Business teams automate their own approval and notification workflows in Power Automate</div>
    </div><div class="outcome-card">
      <div class="outcome-check"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
      <div class="outcome-text">Copilot adoption rates measurable within 30 days of training completion</div>
    </div><div class="outcome-card">
      <div class="outcome-check"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
      <div class="outcome-text">Developers can build and deploy custom AI agents in Azure AI Foundry</div>
    </div><div class="outcome-card">
      <div class="outcome-check"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
      <div class="outcome-text">Managers use AI tools daily for meeting follow-up, drafting, and analysis</div>
    </div><div class="outcome-card">
      <div class="outcome-check"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
      <div class="outcome-text">IT has a governance framework and DLP policy in place before users go live</div>
    </div></div>
  </div>
</section>

<section class="testimonials">
  <div class="section-inner">
    <div class="section-eyebrow">Client Results</div>
    <h2 class="section-title">Real outcomes for real organizations.</h2>
    <p class="section-body">Armely's training practice is built on the same client relationships as its implementation work. We train the teams we implement for.</p>
    <div class="cr-grid"><div class="cr-card">
        <div class="cr-label">
          <div class="cr-check"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
          <span class="cr-industry">Healthcare</span>
        </div>
        <p class="cr-desc">Swope Health Services and UNMC: data platform and clinical workflow modernization on Microsoft Azure.</p>
      </div><div class="cr-card">
        <div class="cr-label">
          <div class="cr-check"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
          <span class="cr-industry">Education</span>
        </div>
        <p class="cr-desc">Plano ISD: Microsoft 365 governance, SharePoint, and Power Platform implementations across district operations.</p>
      </div><div class="cr-card">
        <div class="cr-label">
          <div class="cr-check"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
          <span class="cr-industry">Energy</span>
        </div>
        <p class="cr-desc">Oil and gas operators: OpenInvoice visibility and AP workflow automation through Invoice Lens.</p>
      </div><div class="cr-card">
        <div class="cr-label">
          <div class="cr-check"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
          <span class="cr-industry">Professional Services</span>
        </div>
        <p class="cr-desc">Consulting and legal firms: Dynamics 365, Power Automate approval workflows, and AI knowledge agents.</p>
      </div><div class="cr-card">
        <div class="cr-label">
          <div class="cr-check"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
          <span class="cr-industry">Government</span>
        </div>
        <p class="cr-desc">State and local agencies: Microsoft 365 Government deployment and compliance configuration.</p>
      </div><div class="cr-card">
        <div class="cr-label">
          <div class="cr-check"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
          <span class="cr-industry">Non-Profit</span>
        </div>
        <p class="cr-desc">Social services organizations: Microsoft 365 optimization, Power BI grant reporting, and SharePoint governance.</p>
      </div></div>
    <div class="cr-cta">
      <a href="https://armely.com/customer-stories" class="cr-btn"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg><span>Read Client Stories on armely.com</span><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>
    </div>
  </div>
</section>

<section class="why">
  <div class="section-inner">
    <div class="why-grid">
      <div class="why-list">
        <div class="section-eyebrow">Why Armely Training</div>
        <h2 class="section-title" style="text-align:left;margin-left:0;">We train on the platforms we implement. That is not a small thing.</h2>
        <div class="why-items">
          <div class="why-item"><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg></div><div><strong>Real environment, not sandboxes</strong><p>Training uses your actual tenant, your data structures, and your existing workflows wherever possible.</p></div></div>
          <div class="why-item"><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><div><strong>Trainers who have shipped production solutions</strong><p>Armely trainers are the same consultants who build and deploy these platforms for clients. They know what breaks and why.</p></div></div>
          <div class="why-item"><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div><div><strong>Adoption support built in</strong><p>Every program includes governance guidance and a 30-day check-in so adoption does not fall off after day one.</p></div></div>
          <div class="why-item"><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg></div><div><strong>Tailored to your role mix</strong><p>Separate tracks for end users, analysts, developers, and IT so no one sits through content that is not relevant to their job.</p></div></div>
        </div>
      </div>
      <div class="partner-col">
        <div class="partner-block">
          <div class="partner-header">Microsoft Partner</div>
          <div class="partner-body">
            <div class="partner-badge-row">
              <div class="partner-badge">Solutions Partner</div>
              <div class="partner-badge">Data &amp; AI</div>
              <div class="partner-badge">Modern Work</div>
            </div>
            <p class="partner-text">Armely's Microsoft partnership gives our training practice access to the latest certification frameworks, Copilot readiness tools, and platform roadmaps. Training content is reviewed against current Microsoft standards.</p>
            <div class="partner-stats">
              <div class="partner-stat"><span class="stat-num">4</span><span class="stat-label">Training programs</span></div>
              <div class="partner-stat"><span class="stat-num">10+</span><span class="stat-label">Years delivery experience</span></div>
              <div class="partner-stat"><span class="stat-num">90%+</span><span class="stat-label">Client satisfaction</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="cta-section" id="contact">
  <div class="cta-inner">
    <div class="cta-copy">
      <div class="section-eyebrow">Get Started</div>
      <h2 class="section-title" style="text-align:left;margin-left:0;">Tell us which platform your team needs to master.</h2>
      <p class="section-body" style="text-align:left;margin-left:0;">We will scope the right program format, duration, and delivery approach for your team size and skill level. No commitment required for the scoping conversation.</p>
    </div>
    <div class="cta-form-wrap">
      <div class="cta-form">
        <div class="form-row"><label>Full Name</label><input type="text" placeholder="Jane Smith"></div>
        <div class="form-row"><label>Business Email</label><input type="email" placeholder="jane@yourorg.com"></div>
        <div class="form-row"><label>Organization</label><input type="text" placeholder="Your organization name"></div>
        <div class="form-row"><label>Training Program</label>
          <select>
            <option value="">Select a program...</option>
            <option>Power BI for Analysts and Decision-Makers</option>
            <option>Power Apps and Power Automate for Business Teams</option>
            <option>Generative AI for Business Teams and Developers</option>
            <option>Microsoft 365 Copilot Adoption and Enablement</option>
            <option>Multiple programs, need a recommendation</option>
          </select>
        </div>
        <div class="form-row"><label>Team Size</label>
          <select>
            <option value="">Select...</option>
            <option>1 to 10 people</option>
            <option>11 to 25 people</option>
            <option>26 to 50 people</option>
            <option>More than 50 people</option>
          </select>
        </div>
        <button class="form-submit">Request Training Scope</button>
        <div class="form-note">No commitment required. We will come back with a program recommendation and timeline within 2 business days.</div>
      </div>
    </div>
  </div>
</section>

</div>

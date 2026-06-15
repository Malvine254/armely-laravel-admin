<style>


.armely-transportation-logistics-page *, .armely-transportation-logistics-page *::before, .armely-transportation-logistics-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-transportation-logistics-page {
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

.armely-transportation-logistics-page { scroll-behavior: smooth; }
.armely-transportation-logistics-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* NAV */
.armely-transportation-logistics-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-transportation-logistics-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-transportation-logistics-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-transportation-logistics-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-transportation-logistics-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-transportation-logistics-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-transportation-logistics-page .nav-links a:hover { color: #fff; }
.armely-transportation-logistics-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-transportation-logistics-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* HERO */
.armely-transportation-logistics-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-transportation-logistics-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-transportation-logistics-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-transportation-logistics-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-transportation-logistics-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-transportation-logistics-page .hero h1 { font-size: clamp(2.6rem, 5.5vw, 4.8rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-transportation-logistics-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-transportation-logistics-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.8; }
.armely-transportation-logistics-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-transportation-logistics-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-transportation-logistics-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-transportation-logistics-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-transportation-logistics-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-transportation-logistics-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-transportation-logistics-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-transportation-logistics-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-transportation-logistics-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-transportation-logistics-page .trust-text strong { color: #fff; font-weight: 600; }

  /* SECTIONS */
.armely-transportation-logistics-page section { padding: 96px 56px; }
.armely-transportation-logistics-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-transportation-logistics-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-transportation-logistics-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 640px; }
.armely-transportation-logistics-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* AI SPECTRUM */
.armely-transportation-logistics-page .spectrum { background: var(--navy-mid); }
.armely-transportation-logistics-page .spectrum-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-transportation-logistics-page .spectrum-row { display: flex; flex-direction: column; gap: 10px; margin-top: 32px; }
.armely-transportation-logistics-page .spectrum-level { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; transition: border-color 0.2s; }
.armely-transportation-logistics-page .spectrum-level:hover { border-color: rgba(41,78,139,0.3); }
.armely-transportation-logistics-page .spectrum-level.highlight { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-transportation-logistics-page .spectrum-num { font-size: 0.68rem; font-weight: 800; color: var(--blue); background: var(--blue-dim2); border-radius: 4px; padding: 2px 7px; flex-shrink: 0; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.08em; }
.armely-transportation-logistics-page .spectrum-content-title { font-size: 0.875rem; font-weight: 700; color: #1A2540; margin-bottom: 3px; }
.armely-transportation-logistics-page .spectrum-content-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }

  /* Platform visual */
.armely-transportation-logistics-page .platform-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-transportation-logistics-page .platform-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-transportation-logistics-page .platform-dots { display: flex; gap: 6px; }
.armely-transportation-logistics-page .platform-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-transportation-logistics-page .platform-header-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-transportation-logistics-page .platform-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.armely-transportation-logistics-page .plat-band { border-radius: 9px; padding: 13px 16px; }
.armely-transportation-logistics-page .plat-band-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
.armely-transportation-logistics-page .plat-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.armely-transportation-logistics-page .plat-chip { font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.armely-transportation-logistics-page .band-tools { background: var(--blue-dim); }
.armely-transportation-logistics-page .band-tools .plat-band-label { color: var(--blue); }
.armely-transportation-logistics-page .band-tools .plat-chip { background: var(--blue-dim2); color: var(--blue); }
.armely-transportation-logistics-page .band-data { background: rgba(41,78,139,0.05); }
.armely-transportation-logistics-page .band-data .plat-band-label { color: var(--blue); }
.armely-transportation-logistics-page .band-data .plat-chip { background: rgba(41,78,139,0.1); color: var(--blue); }
.armely-transportation-logistics-page .band-gov { background: var(--blue); }
.armely-transportation-logistics-page .band-gov .plat-band-label { color: rgba(255,255,255,0.7); }
.armely-transportation-logistics-page .band-gov .plat-chip { background: rgba(255,255,255,0.15); color: #fff; }
.armely-transportation-logistics-page .band-arrow { text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2px 0; }

  /* VIBE CODING CALLOUT */
.armely-transportation-logistics-page .vibe-section { background: var(--navy); }
.armely-transportation-logistics-page .vibe-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: start; }
.armely-transportation-logistics-page .vibe-left { }
.armely-transportation-logistics-page .vibe-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-transportation-logistics-page .vibe-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
.armely-transportation-logistics-page .vibe-card-icon { font-size: 1.4rem; }
.armely-transportation-logistics-page .vibe-card-title { font-size: 1rem; font-weight: 700; color: #1A2540; }
.armely-transportation-logistics-page .vibe-card-subtitle { font-size: 0.78rem; color: var(--text-muted); }
.armely-transportation-logistics-page .vibe-card-body { padding: 24px; }
.armely-transportation-logistics-page .vibe-risk { display: flex; align-items: flex-start; gap: 12px; padding: 14px 0; border-bottom: 1px solid var(--border); }
.armely-transportation-logistics-page .vibe-risk:last-child { border-bottom: none; }
.armely-transportation-logistics-page .vibe-risk-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
.armely-transportation-logistics-page .vibe-risk-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 2px; }
.armely-transportation-logistics-page .vibe-risk-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }
.armely-transportation-logistics-page .vibe-right { }
.armely-transportation-logistics-page .vibe-answer-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 28px; margin-bottom: 12px; }
.armely-transportation-logistics-page .vibe-answer-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--blue); margin-bottom: 10px; }
.armely-transportation-logistics-page .vibe-answer-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.75; }

  /* DELIVERS */
.armely-transportation-logistics-page .delivers { background: var(--navy-mid); }
.armely-transportation-logistics-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-transportation-logistics-page .deliver-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-transportation-logistics-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-transportation-logistics-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-transportation-logistics-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-transportation-logistics-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-transportation-logistics-page .journey { background: var(--navy); }
.armely-transportation-logistics-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-transportation-logistics-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-transportation-logistics-page .step:last-child { border-right: none; }
.armely-transportation-logistics-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-transportation-logistics-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-transportation-logistics-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-transportation-logistics-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* USE CASES */
.armely-transportation-logistics-page .usecases { background: var(--navy-mid); }
.armely-transportation-logistics-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-transportation-logistics-page .uc-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-transportation-logistics-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-transportation-logistics-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-transportation-logistics-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-transportation-logistics-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* TESTIMONIALS */
.armely-transportation-logistics-page .testimonials { background: var(--navy); padding: 96px 56px; }
.armely-transportation-logistics-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-transportation-logistics-page .testi-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-transportation-logistics-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-transportation-logistics-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-transportation-logistics-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-transportation-logistics-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-transportation-logistics-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-transportation-logistics-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-transportation-logistics-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY ARMELY */
.armely-transportation-logistics-page .why { background: var(--navy-mid); }
.armely-transportation-logistics-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-transportation-logistics-page .why-list { list-style: none; margin-top: 36px; }
.armely-transportation-logistics-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-transportation-logistics-page .why-list li:last-child { border-bottom: none; }
.armely-transportation-logistics-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-transportation-logistics-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-transportation-logistics-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-transportation-logistics-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-transportation-logistics-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-transportation-logistics-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-transportation-logistics-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-transportation-logistics-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-transportation-logistics-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-transportation-logistics-page .p-stat:nth-child(2) { border-right: none; }
.armely-transportation-logistics-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-transportation-logistics-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-transportation-logistics-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-transportation-logistics-page .p-stat-num span { color: var(--blue); }
.armely-transportation-logistics-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* CTA */
.armely-transportation-logistics-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-transportation-logistics-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-transportation-logistics-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-transportation-logistics-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-transportation-logistics-page .form-row { margin-bottom: 14px; }
.armely-transportation-logistics-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-transportation-logistics-page .form-row input, .armely-transportation-logistics-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-transportation-logistics-page .form-row input:focus, .armely-transportation-logistics-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-transportation-logistics-page .form-row select option { background: #fff; color: #1A2540; }
.armely-transportation-logistics-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-transportation-logistics-page .form-submit:hover { background: var(--blue-lt); }
.armely-transportation-logistics-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* FOOTER */
.armely-transportation-logistics-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-transportation-logistics-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-transportation-logistics-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-transportation-logistics-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-transportation-logistics-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-transportation-logistics-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-transportation-logistics-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* RESPONSIVE */
  @media (max-width: 900px) {
.armely-transportation-logistics-page nav { padding: 16px 24px; }
.armely-transportation-logistics-page .nav-links { display: none; }
.armely-transportation-logistics-page section { padding: 72px 24px; }
.armely-transportation-logistics-page .hero { padding: 110px 24px 72px; }
.armely-transportation-logistics-page .spectrum-grid, .armely-transportation-logistics-page .vibe-two-col, .armely-transportation-logistics-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-transportation-logistics-page .delivers-grid, .armely-transportation-logistics-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-transportation-logistics-page .steps-row { grid-template-columns: 1fr; }
.armely-transportation-logistics-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-transportation-logistics-page .step:last-child { border-bottom: none; }
.armely-transportation-logistics-page .testimonials { padding: 72px 24px; }
.armely-transportation-logistics-page .testi-grid { grid-template-columns: 1fr; }
.armely-transportation-logistics-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-transportation-logistics-page .delivers-grid, .armely-transportation-logistics-page .uc-grid { grid-template-columns: 1fr; }
.armely-transportation-logistics-page .partner-stats { grid-template-columns: 1fr; }
.armely-transportation-logistics-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-transportation-logistics-page {
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
.armely-transportation-logistics-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-transportation-logistics-page .hero::after {
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
.armely-transportation-logistics-page .section-title,
.armely-transportation-logistics-page .deliver-title,
.armely-transportation-logistics-page .uc-title,
.armely-transportation-logistics-page .step-title,
.armely-transportation-logistics-page .why-item-title,
.armely-transportation-logistics-page .form-title {
  color: #162b49;
}
.armely-transportation-logistics-page .deliver-card,
.armely-transportation-logistics-page .uc-card,
.armely-transportation-logistics-page .testi-card,
.armely-transportation-logistics-page .platform-card,
.armely-transportation-logistics-page .partner-block,
.armely-transportation-logistics-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-transportation-logistics-page .deliver-card:hover,
.armely-transportation-logistics-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-transportation-logistics-page .btn-primary,
.armely-transportation-logistics-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-transportation-logistics-page .btn-primary:hover,
.armely-transportation-logistics-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-transportation-logistics-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-transportation-logistics-page nav,
.armely-transportation-logistics-page footer {
  display: none;
}


/* Modern layout update: tighter first section and SVG icon system */
.armely-transportation-logistics-page .hero {
  min-height: auto;
  padding: 128px 32px 86px;
  isolation: isolate;
}
.armely-transportation-logistics-page .hero-inner {
  width: min(1160px, 100%);
  margin: 0 auto;
  display: grid;
  grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.75fr);
  align-items: center;
  gap: 56px;
  position: relative;
  z-index: 1;
}
.armely-transportation-logistics-page .hero-copy { max-width: 760px; }
.armely-transportation-logistics-page .hero h1 { max-width: 760px; margin-bottom: 20px; }
.armely-transportation-logistics-page .hero-sub { max-width: 640px; margin-bottom: 30px; font-size: 1.02rem; line-height: 1.72; }
.armely-transportation-logistics-page .hero-actions { margin-bottom: 34px; }
.armely-transportation-logistics-page .hero-trust {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  padding-top: 0;
  border-top: 0;
  max-width: 720px;
}
.armely-transportation-logistics-page .hero .trust-item {
  align-items: flex-start;
  background: rgba(255,255,255,0.10);
  border: 1px solid rgba(255,255,255,0.16);
  border-radius: 14px;
  padding: 14px 15px;
  backdrop-filter: blur(10px);
}
.armely-transportation-logistics-page .hero .trust-dot {
  width: 22px;
  height: 22px;
  border-radius: 999px;
  background: rgba(255,255,255,0.16);
  border: 1px solid rgba(255,255,255,0.24);
  position: relative;
  margin-top: 1px;
}
.armely-transportation-logistics-page .hero .trust-dot::after {
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
.armely-transportation-logistics-page .hero .trust-text { color: rgba(255,255,255,0.78); line-height: 1.5; }
.armely-transportation-logistics-page .hero-visual {
  min-height: 420px;
  border-radius: 28px;
  position: relative;
  background: linear-gradient(145deg, rgba(255,255,255,0.16), rgba(255,255,255,0.05));
  border: 1px solid rgba(255,255,255,0.18);
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.18), 0 28px 70px rgba(6, 22, 48, 0.24);
  overflow: hidden;
}
.armely-transportation-logistics-page .hero-visual::before {
  content: '';
  position: absolute;
  inset: 36px;
  border: 1px solid rgba(255,255,255,0.14);
  border-radius: 999px;
}
.armely-transportation-logistics-page .hero-visual::after {
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
.armely-transportation-logistics-page .hero-orbit span {
  position: absolute;
  width: 52px;
  height: 52px;
  border-radius: 16px;
  background: rgba(255,255,255,0.14);
  border: 1px solid rgba(255,255,255,0.22);
}
.armely-transportation-logistics-page .hero-orbit span:nth-child(1) { left: 56px; top: 88px; }
.armely-transportation-logistics-page .hero-orbit span:nth-child(2) { right: 72px; top: 138px; }
.armely-transportation-logistics-page .hero-orbit span:nth-child(3) { left: 48%; bottom: 76px; }
.armely-transportation-logistics-page .hero-visual-card {
  position: absolute;
  z-index: 2;
  width: 230px;
  padding: 18px;
  border-radius: 18px;
  background: rgba(255,255,255,0.92);
  box-shadow: 0 24px 54px rgba(9, 31, 63, 0.22);
}
.armely-transportation-logistics-page .hero-visual-card.top { top: 42px; right: 30px; }
.armely-transportation-logistics-page .hero-visual-card.bottom { bottom: 46px; left: 30px; }
.armely-transportation-logistics-page .hero-visual-label {
  display: block;
  font-size: 0.78rem;
  font-weight: 700;
  color: #162b49;
  margin-bottom: 12px;
}
.armely-transportation-logistics-page .hero-visual-line {
  display: block;
  height: 8px;
  width: 100%;
  border-radius: 999px;
  background: rgba(47, 85, 151, 0.16);
  margin-top: 8px;
}
.armely-transportation-logistics-page .hero-visual-line.short { width: 68%; }
.armely-transportation-logistics-page .icon-svg {
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
.armely-transportation-logistics-page .vibe-card-icon,
.armely-transportation-logistics-page .vibe-risk-icon,
.armely-transportation-logistics-page .deliver-icon,
.armely-transportation-logistics-page .uc-icon,
.armely-transportation-logistics-page .why-icon {
  color: var(--blue);
}
.armely-transportation-logistics-page .vibe-card-icon,
.armely-transportation-logistics-page .vibe-risk-icon {
  width: 42px;
  height: 42px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-transportation-logistics-page .vibe-risk-icon { width: 36px; height: 36px; border-radius: 10px; }
.armely-transportation-logistics-page .deliver-icon .icon-svg,
.armely-transportation-logistics-page .uc-icon .icon-svg,
.armely-transportation-logistics-page .why-icon .icon-svg { width: 23px; height: 23px; }
.armely-transportation-logistics-page .uc-icon {
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
  .armely-transportation-logistics-page .hero-inner { grid-template-columns: 1fr; gap: 32px; }
  .armely-transportation-logistics-page .hero-visual { min-height: 320px; }
}
@media (max-width: 680px) {
  .armely-transportation-logistics-page .hero { padding: 104px 22px 64px; }
  .armely-transportation-logistics-page .hero-trust { grid-template-columns: 1fr; }
  .armely-transportation-logistics-page .hero-visual { display: none; }
  .armely-transportation-logistics-page .hero-actions a { width: 100%; text-align: center; }
}

/* Focused cleanup for the Generative AI detail page */
.armely-transportation-logistics-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  align-items: center;
  background: #173b67;
  border-radius: 0;
}
.armely-transportation-logistics-page .hero::after,
.armely-transportation-logistics-page .hero-bg-glow,
.armely-transportation-logistics-page .hero-visual {
  display: none;
}
.armely-transportation-logistics-page .hero-inner {
  width: min(1040px, 100%);
  display: block;
}
.armely-transportation-logistics-page .hero-copy {
  max-width: 760px;
}
.armely-transportation-logistics-page .hero-eyebrow {
  margin-bottom: 18px;
}
.armely-transportation-logistics-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.72);
}
.armely-transportation-logistics-page .eyebrow-partner,
.armely-transportation-logistics-page .hero-trust {
  display: none;
}
.armely-transportation-logistics-page .hero h1 {
  max-width: 760px;
  margin-bottom: 22px;
}
.armely-transportation-logistics-page .hero-sub {
  max-width: 680px;
  margin-bottom: 34px;
}
.armely-transportation-logistics-page .hero-actions {
  margin-bottom: 0;
}
.armely-transportation-logistics-page .hero .btn-primary,
.armely-transportation-logistics-page .hero .btn-outline {
  border-radius: 0;
}
.armely-transportation-logistics-page .vibe-section {
  background: #fff;
  padding: 84px 56px;
}
.armely-transportation-logistics-page .vibe-section .section-inner {
  max-width: 920px;
}
.armely-transportation-logistics-page .vibe-section .section-title,
.armely-transportation-logistics-page .vibe-section .section-body {
  max-width: 820px;
}
.armely-transportation-logistics-page .vibe-section .section-body {
  margin-bottom: 28px;
}
.armely-transportation-logistics-page .vibe-two-col {
  grid-template-columns: 1fr;
  gap: 18px;
}
.armely-transportation-logistics-page .vibe-card,
.armely-transportation-logistics-page .vibe-answer-card {
  border-radius: 0;
  box-shadow: none;
}
.armely-transportation-logistics-page .vibe-card-header {
  padding: 18px 20px;
}
.armely-transportation-logistics-page .vibe-card-body {
  padding: 8px 20px 12px;
}
.armely-transportation-logistics-page .vibe-risk {
  padding: 12px 0;
}
.armely-transportation-logistics-page .vibe-right {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
}
.armely-transportation-logistics-page .vibe-answer-card {
  margin-bottom: 0;
  padding: 20px;
  background: #f7f9fc;
}
.armely-transportation-logistics-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-transportation-logistics-page .why > .section-inner > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-transportation-logistics-page section:not(.hero) > .section-inner > .section-title,
.armely-transportation-logistics-page .why > .section-inner > .section-title {
  max-width: 880px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-transportation-logistics-page section:not(.hero) > .section-inner > .section-body,
.armely-transportation-logistics-page .why > .section-inner > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-transportation-logistics-page .spectrum-grid,
.armely-transportation-logistics-page .delivers-grid,
.armely-transportation-logistics-page .steps-row,
.armely-transportation-logistics-page .uc-grid,
.armely-transportation-logistics-page .testi-grid,
.armely-transportation-logistics-page .why-two-col {
  margin-top: 56px;
}
.armely-transportation-logistics-page .why-two-col {
  align-items: stretch;
}
.armely-transportation-logistics-page .why-list {
  margin-top: 0;
}
.armely-transportation-logistics-page .why-list,
.armely-transportation-logistics-page .partner-block {
  height: 100%;
}
@media (max-width: 900px) {
  .armely-transportation-logistics-page .hero {
    padding: 118px 24px 76px;
  }
  .armely-transportation-logistics-page .vibe-section {
    padding: 72px 24px;
  }
  .armely-transportation-logistics-page .vibe-right {
    grid-template-columns: 1fr;
  }
}



/* Final compact modern cleanup */
.armely-transportation-logistics-page {
  --blue: #2f5597;
  --blue-lt: #4779bd;
  --navy-mid: #f6f8fc;
  --navy-card: #ffffff;
  --text-body: #334155;
  --text-muted: #667085;
  --border: rgba(47, 85, 151, 0.14);
}
.armely-transportation-logistics-page .hero {
  min-height: auto !important;
  padding: 86px 56px 70px !important;
  background: linear-gradient(135deg, #173b67 0%, #234f86 100%) !important;
  border-radius: 0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}
.armely-transportation-logistics-page .hero::after,
.armely-transportation-logistics-page .hero-bg-glow,
.armely-transportation-logistics-page .hero-visual {
  display: none !important;
}
.armely-transportation-logistics-page .hero-inner {
  width: min(1120px, 100%) !important;
  margin: 0 auto !important;
  display: block !important;
}
.armely-transportation-logistics-page .hero-copy {
  max-width: 860px !important;
}
.armely-transportation-logistics-page .hero-eyebrow {
  margin-bottom: 18px !important;
}
.armely-transportation-logistics-page .eyebrow-badge {
  display: inline-flex !important;
  background: rgba(255,255,255,0.10) !important;
  border: 1px solid rgba(255,255,255,0.22) !important;
  border-radius: 999px !important;
  padding: 7px 14px !important;
  color: rgba(255,255,255,0.88) !important;
}
.armely-transportation-logistics-page .eyebrow-partner {
  display: inline-flex !important;
  color: rgba(255,255,255,0.66) !important;
}
.armely-transportation-logistics-page .hero h1 {
  max-width: 900px !important;
  margin-bottom: 18px !important;
  font-size: clamp(2.5rem, 5vw, 4.9rem) !important;
  line-height: 1.05 !important;
  letter-spacing: -0.04em !important;
}
.armely-transportation-logistics-page .hero-sub {
  max-width: 760px !important;
  margin-bottom: 28px !important;
  font-size: 1rem !important;
  line-height: 1.7 !important;
}
.armely-transportation-logistics-page .hero-actions {
  margin-bottom: 0 !important;
  gap: 12px !important;
}
.armely-transportation-logistics-page .hero .btn-primary,
.armely-transportation-logistics-page .hero .btn-outline,
.armely-transportation-logistics-page .btn-primary,
.armely-transportation-logistics-page .btn-outline,
.armely-transportation-logistics-page .form-submit {
  border-radius: 8px !important;
}
.armely-transportation-logistics-page section {
  padding: 68px 56px !important;
}
.armely-transportation-logistics-page .section-inner {
  max-width: 1120px !important;
}
.armely-transportation-logistics-page .section-eyebrow {
  margin-bottom: 10px !important;
}
.armely-transportation-logistics-page .section-title {
  margin-bottom: 14px !important;
}
.armely-transportation-logistics-page .section-body {
  margin-bottom: 28px !important;
  line-height: 1.65 !important;
}
.armely-transportation-logistics-page .spectrum-grid,
.armely-transportation-logistics-page .vibe-two-col,
.armely-transportation-logistics-page .delivers-grid,
.armely-transportation-logistics-page .steps-row,
.armely-transportation-logistics-page .uc-grid,
.armely-transportation-logistics-page .testi-grid,
.armely-transportation-logistics-page .why-two-col {
  margin-top: 34px !important;
  gap: 20px !important;
}
.armely-transportation-logistics-page .spectrum-grid,
.armely-transportation-logistics-page .why-two-col {
  grid-template-columns: 1fr 1fr !important;
}
.armely-transportation-logistics-page .spectrum-row {
  margin-top: 0 !important;
  gap: 10px !important;
}
.armely-transportation-logistics-page .spectrum-level,
.armely-transportation-logistics-page .deliver-card,
.armely-transportation-logistics-page .uc-card,
.armely-transportation-logistics-page .testi-card,
.armely-transportation-logistics-page .vibe-answer-card,
.armely-transportation-logistics-page .partner-block,
.armely-transportation-logistics-page .cta-form,
.armely-transportation-logistics-page .vibe-card,
.armely-transportation-logistics-page .platform-card {
  border-radius: 14px !important;
  box-shadow: 0 14px 36px rgba(18, 47, 82, 0.08) !important;
}
.armely-transportation-logistics-page .deliver-card,
.armely-transportation-logistics-page .uc-card,
.armely-transportation-logistics-page .testi-card {
  padding: 24px 22px !important;
}
.armely-transportation-logistics-page .deliver-icon,
.armely-transportation-logistics-page .uc-icon,
.armely-transportation-logistics-page .why-icon,
.armely-transportation-logistics-page .vibe-card-icon,
.armely-transportation-logistics-page .vibe-risk-icon {
  border-radius: 12px !important;
  color: var(--blue) !important;
}
.armely-transportation-logistics-page .vibe-section {
  padding: 68px 56px !important;
  background: #fff !important;
}
.armely-transportation-logistics-page .vibe-section .section-inner {
  max-width: 1120px !important;
}
.armely-transportation-logistics-page .vibe-two-col {
  grid-template-columns: 1fr 1fr !important;
  align-items: stretch !important;
}
.armely-transportation-logistics-page .vibe-card-body {
  padding: 18px 20px !important;
}
.armely-transportation-logistics-page .vibe-right {
  display: flex !important;
  flex-direction: column !important;
  gap: 12px !important;
}
.armely-transportation-logistics-page .vibe-answer-card {
  padding: 20px !important;
  background: #f8fafd !important;
}
.armely-transportation-logistics-page .steps-row {
  border-radius: 14px !important;
  overflow: hidden !important;
}
.armely-transportation-logistics-page .step {
  padding: 24px 18px !important;
}
.armely-transportation-logistics-page .step-num {
  font-size: 2rem !important;
  margin-bottom: 10px !important;
}
.armely-transportation-logistics-page .why-list {
  margin-top: 0 !important;
}
.armely-transportation-logistics-page .why-list li {
  padding: 16px 0 !important;
}
.armely-transportation-logistics-page .partner-block-top,
.armely-transportation-logistics-page .p-stat {
  padding: 22px !important;
}
@media (max-width: 900px) {
  .armely-transportation-logistics-page .hero { padding: 88px 24px 58px !important; }
  .armely-transportation-logistics-page section,
  .armely-transportation-logistics-page .vibe-section { padding: 56px 24px !important; }
  .armely-transportation-logistics-page .spectrum-grid,
  .armely-transportation-logistics-page .vibe-two-col,
  .armely-transportation-logistics-page .why-two-col { grid-template-columns: 1fr !important; }
  .armely-transportation-logistics-page .delivers-grid,
  .armely-transportation-logistics-page .uc-grid { grid-template-columns: 1fr 1fr !important; }
  .armely-transportation-logistics-page .cta-inner { padding: 56px 24px !important; grid-template-columns: 1fr !important; }
}
@media (max-width: 600px) {
  .armely-transportation-logistics-page .hero h1 { font-size: clamp(2.15rem, 11vw, 3.2rem) !important; }
  .armely-transportation-logistics-page .hero-eyebrow { flex-direction: column !important; align-items: flex-start !important; gap: 8px !important; }
  .armely-transportation-logistics-page .hero-actions a { width: 100% !important; text-align: center !important; }
  .armely-transportation-logistics-page .delivers-grid,
  .armely-transportation-logistics-page .uc-grid { grid-template-columns: 1fr !important; }
}



.armely-transportation-logistics-page .cr-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:28px; margin-bottom:28px; }
.armely-transportation-logistics-page .cr-card { background:#fff; border:1px solid var(--border); border-radius:14px; padding:22px; box-shadow:0 14px 36px rgba(18,47,82,0.08); }
.armely-transportation-logistics-page .cr-label { display:flex; align-items:center; gap:9px; margin-bottom:10px; }
.armely-transportation-logistics-page .cr-check { width:20px; height:20px; display:flex; align-items:center; justify-content:center; background:var(--blue-dim); border:1px solid var(--blue-dim2); border-radius:50%; flex-shrink:0; color:var(--blue); }
.armely-transportation-logistics-page .cr-check .icon-svg { width:11px; height:11px; stroke-width:3; }
.armely-transportation-logistics-page .cr-industry { font-size:0.875rem; font-weight:700; color:#162b49; }
.armely-transportation-logistics-page .cr-desc { font-size:0.84rem; color:var(--text-muted); line-height:1.65; margin:0; }
.armely-transportation-logistics-page .cr-cta { text-align:center; margin-top:8px; }
.armely-transportation-logistics-page .cr-btn { display:inline-flex; align-items:center; gap:10px; background:#1a2e52; color:#fff; border-radius:8px; padding:13px 28px; text-decoration:none; font-size:0.875rem; font-weight:600; }
.armely-transportation-logistics-page .cr-btn:hover { background:var(--blue); }
.armely-transportation-logistics-page .cr-btn .icon-svg { width:18px; height:18px; }
@media (max-width:900px) { .armely-transportation-logistics-page .cr-grid { grid-template-columns:1fr 1fr; } }
@media (max-width:600px) { .armely-transportation-logistics-page .cr-grid { grid-template-columns:1fr; } }

.armely-transportation-logistics-page .ind-hero-inner { max-width:1120px; margin:0 auto; }
.armely-transportation-logistics-page .ind-eyebrow { display:inline-flex; align-items:center; gap:8px; background:rgba(255,255,255,0.10); border:1px solid rgba(255,255,255,0.22); border-radius:999px; padding:6px 14px; color:rgba(255,255,255,0.88); font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.12em; margin-bottom:18px; }
.armely-transportation-logistics-page .ind-hero-sub { font-size:1rem; font-weight:300; color:rgba(255,255,255,0.80); max-width:680px; margin-bottom:28px; line-height:1.72; }
.armely-transportation-logistics-page .ind-hero-actions { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:36px; }
.armely-transportation-logistics-page .ind-trust-row { display:flex; flex-direction:row; flex-wrap:nowrap; gap:36px; padding-top:24px; border-top:1px solid rgba(255,255,255,0.12); align-items:flex-start; }
.armely-transportation-logistics-page .ind-trust-item { display:flex; flex-direction:row; align-items:flex-start; gap:8px; flex:1 1 0; min-width:0; }
.armely-transportation-logistics-page .ind-trust-dot { width:7px; height:7px; border-radius:50%; background:rgba(255,255,255,0.45); flex-shrink:0; margin-top:5px; }
.armely-transportation-logistics-page .ind-trust-item strong { display:block; font-size:0.79rem; font-weight:600; color:#fff; white-space:nowrap; }
.armely-transportation-logistics-page .ind-trust-item span { font-size:0.73rem; color:rgba(255,255,255,0.55); }
.armely-transportation-logistics-page .ind-challenge-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:34px; }
.armely-transportation-logistics-page .ind-challenge-card { background:#fff; border:1px solid var(--border); border-radius:14px; padding:22px 20px; box-shadow:0 14px 36px rgba(18,47,82,0.08); }
.armely-transportation-logistics-page .ind-challenge-icon { width:40px; height:40px; background:var(--blue-dim); border:1px solid var(--blue-dim2); border-radius:12px; display:flex; align-items:center; justify-content:center; color:var(--blue); margin-bottom:14px; }
.armely-transportation-logistics-page .ind-challenge-icon .icon-svg { width:20px; height:20px; }
.armely-transportation-logistics-page .ind-challenge-title { font-size:0.9rem; font-weight:700; color:#162b49; margin-bottom:6px; }
.armely-transportation-logistics-page .ind-challenge-desc { font-size:0.82rem; line-height:1.65; color:var(--text-body); }
.armely-transportation-logistics-page .ind-deliver-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:34px; }
.armely-transportation-logistics-page .ind-deliver-card { background:#fff; border:1px solid var(--border); border-radius:14px; padding:22px 20px; box-shadow:0 14px 36px rgba(18,47,82,0.08); }
.armely-transportation-logistics-page .ind-deliver-icon { width:42px; height:42px; background:var(--blue-dim); border:1px solid var(--blue-dim2); border-radius:12px; display:flex; align-items:center; justify-content:center; color:var(--blue); margin-bottom:14px; }
.armely-transportation-logistics-page .ind-deliver-icon .icon-svg { width:21px; height:21px; }
.armely-transportation-logistics-page .ind-deliver-title { font-size:0.9rem; font-weight:700; color:#162b49; margin-bottom:6px; }
.armely-transportation-logistics-page .ind-deliver-desc { font-size:0.82rem; line-height:1.68; color:var(--text-body); }
.armely-transportation-logistics-page .ind-cta-form-wrap { background:#fff; border:1px solid var(--border); border-radius:14px; padding:28px 24px; max-width:560px; margin:0 auto; box-shadow:0 14px 36px rgba(18,47,82,0.08); }
.armely-transportation-logistics-page .ind-form-row { margin-bottom:12px; }
.armely-transportation-logistics-page .ind-form-label { display:block; font-size:0.69rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.08em; margin-bottom:5px; }
.armely-transportation-logistics-page .ind-form-input, .armely-transportation-logistics-page .ind-form-select { width:100%; background:#fff; border:1px solid rgba(47,85,151,0.15); border-radius:7px; padding:11px 14px; font-family:'Poppins',sans-serif; font-size:0.875rem; color:#1a2540; outline:none; box-sizing:border-box; }
.armely-transportation-logistics-page .ind-form-submit { width:100%; background:var(--blue); color:#fff; border:none; border-radius:7px; padding:13px; margin-top:6px; font-family:'Poppins',sans-serif; font-size:0.9rem; font-weight:600; cursor:pointer; }
.armely-transportation-logistics-page .ind-form-note { text-align:center; margin-top:10px; font-size:0.72rem; color:var(--text-muted); }
/* Hero and section sizing */
.armely-transportation-logistics-page .hero { padding:72px 56px 60px !important; }
.armely-transportation-logistics-page .hero h1 { font-size:clamp(1.75rem,3.2vw,2.7rem) !important; line-height:1.1 !important; letter-spacing:-0.025em !important; max-width:820px !important; margin-bottom:18px !important; }
.armely-transportation-logistics-page .hero-sub { font-size:0.95rem !important; max-width:600px !important; margin-bottom:28px !important; }
.armely-transportation-logistics-page section { padding-top:48px !important; padding-bottom:48px !important; }
.armely-transportation-logistics-page .spectrum { padding-top:52px !important; padding-bottom:40px !important; }
.armely-transportation-logistics-page .delivers { padding-top:40px !important; padding-bottom:52px !important; }
.armely-transportation-logistics-page .cr-primary { background:#fff; border:1px solid var(--border); border-radius:14px; padding:28px 30px; box-shadow:0 14px 36px rgba(18,47,82,0.08); margin-top:28px; display:flex; align-items:flex-start; gap:18px; }
.armely-transportation-logistics-page .cr-primary-icon { width:44px; height:44px; display:flex; align-items:center; justify-content:center; background:var(--blue-dim); border:1px solid var(--blue-dim2); border-radius:12px; flex-shrink:0; color:var(--blue); }
.armely-transportation-logistics-page .cr-primary-icon .icon-svg { width:22px; height:22px; }
.armely-transportation-logistics-page .cr-primary-sector { font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.12em; color:var(--blue); margin-bottom:6px; }
.armely-transportation-logistics-page .cr-primary-desc { font-size:0.95rem; font-weight:500; color:#162b49; line-height:1.6; margin:0; }
.armely-transportation-logistics-page .cr-sup-row { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-top:14px; }
.armely-transportation-logistics-page .cr-sup-card { background:var(--navy-mid); border:1px solid var(--border); border-radius:12px; padding:16px 18px; }
.armely-transportation-logistics-page .cr-sup-label { display:block; font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--blue); margin-bottom:6px; }
.armely-transportation-logistics-page .cr-sup-desc { font-size:0.78rem; color:var(--text-muted); line-height:1.55; margin:0; }
.armely-transportation-logistics-page .cr-cta { text-align:center; margin-top:24px; }
.armely-transportation-logistics-page .cr-btn { display:inline-flex; align-items:center; gap:10px; background:#1a2e52; color:#fff; border-radius:8px; padding:12px 28px; text-decoration:none; font-size:0.875rem; font-weight:600; }
.armely-transportation-logistics-page .cr-btn:hover { background:var(--blue); }
.armely-transportation-logistics-page .cr-btn .icon-svg { width:18px; height:18px; }
@media (max-width:900px) {
  .armely-transportation-logistics-page .hero { padding:72px 24px 52px !important; }
  .armely-transportation-logistics-page .hero h1 { font-size:clamp(1.6rem,5.5vw,2.2rem) !important; }
  .armely-transportation-logistics-page .ind-trust-row { flex-wrap:wrap; gap:20px; }
  .armely-transportation-logistics-page .ind-challenge-grid, .armely-transportation-logistics-page .ind-deliver-grid { grid-template-columns:1fr 1fr; }
  .armely-transportation-logistics-page section { padding-top:40px !important; padding-bottom:40px !important; }
  .armely-transportation-logistics-page .cr-sup-row { grid-template-columns:1fr; }
}
@media (max-width:600px) {
  .armely-transportation-logistics-page .hero h1 { font-size:clamp(1.45rem,7.5vw,1.9rem) !important; }
  .armely-transportation-logistics-page .ind-trust-row { flex-direction:column; gap:12px; }
  .armely-transportation-logistics-page .ind-challenge-grid, .armely-transportation-logistics-page .ind-deliver-grid { grid-template-columns:1fr; }
}



.armely-transportation-logistics-page .ind-cta-section { background:var(--navy-mid); padding-top:36px !important; padding-bottom:52px !important; }
.armely-transportation-logistics-page .ind-cta-wrap { max-width:640px; margin:0 auto; text-align:center; padding:0 24px; }
.armely-transportation-logistics-page .ind-cta-wrap .section-title { max-width:100%; text-align:center; }
.armely-transportation-logistics-page .ind-cta-wrap .section-body { max-width:100%; text-align:center; margin-bottom:28px; }
.armely-transportation-logistics-page .ind-cta-form-wrap { text-align:left; }

</style>
<div class="armely-transportation-logistics-page">
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-inner ind-hero-inner">
    <div class="ind-eyebrow">Transportation and Logistics</div>
    <h1>Operational visibility, integrated systems,<br>and less time reconciling data across tools.</h1>
    <p class="ind-hero-sub">Armely helps transportation and logistics organizations connect their operational systems, build real-time visibility into dispatch and fleet performance, and automate the administrative processes that slow billing, compliance, and carrier management.</p>
    <div class="ind-hero-actions">
      <a href="#contact" class="btn-primary">Book a Free Assessment</a>
      <a href="#delivers" class="btn-outline">See What We Do</a>
    </div>
    <div class="ind-trust-row"><div class="ind-trust-item"><span class="ind-trust-dot"></span><div><strong>Operations Analytics</strong><span>Real-time dashboards for dispatch, fleet, and delivery performance</span></div></div><div class="ind-trust-item"><span class="ind-trust-dot"></span><div><strong>System Integration</strong><span>API integrations connecting TMS, ERP, fleet, and financial tools</span></div></div><div class="ind-trust-item"><span class="ind-trust-dot"></span><div><strong>Process Automation</strong><span>Workflows for billing, carrier onboarding, and DOT compliance</span></div></div></div>
  </div>
</section>
<section class="spectrum">
  <div class="section-inner">
    <div class="section-eyebrow">Industry Challenges</div>
    <h2 class="section-title">The problems Armely solves for transportation and logistics organizations.</h2>
    <p class="section-body">These are the technology and operational challenges that come up most consistently in our work with transportation and logistics clients.</p>
    <div class="ind-challenge-grid"><div class="ind-challenge-card"><div class="ind-challenge-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg></div><div class="ind-challenge-title">No Single View of Operational Performance</div><div class="ind-challenge-desc">Dispatch performance, fleet utilization, on-time delivery, and cost-per-mile data exist in multiple disconnected systems. Producing operational reports is manual and always a day behind reality.</div></div><div class="ind-challenge-card"><div class="ind-challenge-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M8 12h8"/><path d="M8 16h6"/></svg></div><div class="ind-challenge-title">Manual Back-Office and Compliance Processes</div><div class="ind-challenge-desc">Freight billing, carrier onboarding, compliance documentation, and load confirmation run through email and spreadsheets, creating errors, delays, and audit exposure.</div></div><div class="ind-challenge-card"><div class="ind-challenge-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></div><div class="ind-challenge-title">Systems That Do Not Talk to Each Other</div><div class="ind-challenge-desc">TMS, ERP, fleet management, and financial systems that do not communicate create re-entry work, reconciliation errors, and reporting gaps that no one has time to close manually.</div></div><div class="ind-challenge-card"><div class="ind-challenge-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div><div class="ind-challenge-title">Customer and Partner Visibility Gaps</div><div class="ind-challenge-desc">Customers expect real-time shipment visibility and proactive exception notifications. Providing this requires data integration and automation that most logistics operations have not built.</div></div><div class="ind-challenge-card"><div class="ind-challenge-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg></div><div class="ind-challenge-title">DOT and Regulatory Compliance</div><div class="ind-challenge-desc">Hours of service records, carrier qualification documentation, hazmat certification management, and safety audit preparation require structured workflows and reliable recordkeeping.</div></div><div class="ind-challenge-card"><div class="ind-challenge-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><div class="ind-challenge-title">Distributed Teams Across Multiple Locations</div><div class="ind-challenge-desc">Drivers, dispatchers, warehouse staff, and back-office teams working across locations need reliable tools for communication, document access, and task completion.</div></div></div>
  </div>
</section>
<section class="delivers" id="delivers">
  <div class="section-inner">
    <div class="section-eyebrow">What Armely Delivers</div>
    <h2 class="section-title">The right technology for the job, not the same stack for every industry.</h2>
    <p class="section-body">Armely selects and implements the platforms that fit your operational requirements, compliance obligations, and existing systems. That may include Microsoft, Snowflake, custom-built applications, or a combination.</p>
    <div class="ind-deliver-grid"><div class="ind-deliver-card"><div class="ind-deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg></div><div class="ind-deliver-title">Operational Dashboards and Analytics</div><div class="ind-deliver-desc">Power BI connected to your TMS, ERP, and fleet data so operations leadership has real-time visibility into dispatch performance, fleet utilization, on-time delivery rates, and cost-per-mile metrics.</div></div><div class="ind-deliver-card"><div class="ind-deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></div><div class="ind-deliver-title">System Integration and API Development</div><div class="ind-deliver-desc">We build the API integration layer between your TMS, ERP, fleet management, and financial systems so data flows automatically and your team stops reconciling numbers across disconnected platforms.</div></div><div class="ind-deliver-card"><div class="ind-deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M21 12a9 9 0 0 1-15.5 6.2L3 16"/><path d="M3 21v-5h5"/><path d="M3 12A9 9 0 0 1 18.5 5.8L21 8"/><path d="M21 3v5h-5"/></svg></div><div class="ind-deliver-title">Back-Office and Compliance Automation</div><div class="ind-deliver-desc">Power Automate workflows for freight billing, carrier onboarding, load confirmation, and DOT compliance documentation that replace email chains with governed, time-stamped, auditable processes.</div></div><div class="ind-deliver-card"><div class="ind-deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg></div><div class="ind-deliver-title">Operational Data Platform</div><div class="ind-deliver-desc">If your reporting needs go beyond Power BI, we implement Microsoft Fabric or Snowflake to consolidate TMS, GPS, ERP, and financial data into a unified analytics environment.</div></div><div class="ind-deliver-card"><div class="ind-deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 8V4H8"/><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 11v2"/><path d="M15 11v2"/></svg></div><div class="ind-deliver-title">AI for Customer Service and Operations</div><div class="ind-deliver-desc">Copilot Studio agents that handle routine shipment inquiries, exception notifications, and internal knowledge queries so your operations team spends less time on repetitive communication.</div></div><div class="ind-deliver-card"><div class="ind-deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div><div class="ind-deliver-title">Microsoft 365 for Distributed Operations</div><div class="ind-deliver-desc">Teams governance, SharePoint document management, and staff training so your dispersed operations team has reliable tools for communication, document access, and task management.</div></div></div>
  </div>
</section>
<section class="testimonials">
  <div class="section-inner">
    <div class="section-eyebrow">Client Results</div>
    <h2 class="section-title">Real outcomes for real organizations.</h2>
    <p class="section-body">See what Armely has delivered for transportation organizations and across the sectors we serve.</p>
    <div class="cr-primary">
      <div class="cr-primary-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
      <div>
        <div class="cr-primary-sector">Transportation</div>
        <p class="cr-primary-desc">Logistics operators: TMS and ERP integration, Power BI dispatch dashboards, and carrier onboarding automation.</p>
      </div>
    </div>
    <div class="cr-sup-row"><div class="cr-sup-card"><span class="cr-sup-label">Healthcare</span><p class="cr-sup-desc">Swope Health Services and UNMC: data platform and clinical workflow modernization on Microsoft Azure.</p></div><div class="cr-sup-card"><span class="cr-sup-label">Education</span><p class="cr-sup-desc">Plano ISD: Microsoft 365 governance, SharePoint, and Power Platform implementations across district operations.</p></div><div class="cr-sup-card"><span class="cr-sup-label">Energy</span><p class="cr-sup-desc">Oil and gas operators: OpenInvoice visibility and AP workflow automation through Invoice Lens.</p></div></div>
    <div class="cr-cta">
      <a href="https://armely.com/customer-stories" class="cr-btn"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg><span>Read Client Stories on armely.com</span><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>
    </div>
  </div>
</section>
<section class="cta-section ind-cta-section" id="contact">
  <div class="ind-cta-wrap">
    <div class="section-eyebrow">Get Started</div>
    <h2 class="section-title">Ready to discuss your logistics technology needs?</h2>
    <p class="section-body">Book a free 30-minute assessment. We will review your current systems and come back with clear recommendations.</p>
    <div class="ind-cta-form-wrap">
      <div class="ind-form-row"><label class="ind-form-label">Full Name</label><input class="ind-form-input" type="text" placeholder="Jane Smith"></div>
      <div class="ind-form-row"><label class="ind-form-label">Business Email</label><input class="ind-form-input" type="email" placeholder="jane@yourorg.com"></div>
      <div class="ind-form-row"><label class="ind-form-label">Organization</label><input class="ind-form-input" type="text" placeholder="Your organization name"></div>
      <div class="ind-form-row"><label class="ind-form-label">Primary Need</label><select class="ind-form-select"><option value="">Select...</option><option>Operational visibility and analytics dashboards</option><option>System integration between TMS, ERP, or fleet tools</option><option>Back-office and compliance process automation</option><option>Operational data platform for analytics</option><option>AI for customer service or operations support</option><option>Microsoft 365 for distributed teams</option><option>Not sure, need a recommendation</option></select></div>
      <button class="ind-form-submit">Request Free Assessment</button>
      <div class="ind-form-note">No spam. No sales pressure. Just a useful conversation.</div>
    </div>
  </div>
</section>
</div>
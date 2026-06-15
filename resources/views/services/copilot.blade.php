<style>


.armely-copilot-page *, .armely-copilot-page *::before, .armely-copilot-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-copilot-page {
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

.armely-copilot-page { scroll-behavior: smooth; }
.armely-copilot-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* NAV */
.armely-copilot-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-copilot-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-copilot-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-copilot-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-copilot-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-copilot-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-copilot-page .nav-links a:hover { color: #fff; }
.armely-copilot-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-copilot-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* HERO */
.armely-copilot-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-copilot-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-copilot-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-copilot-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-copilot-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-copilot-page .hero h1 { font-size: clamp(2.6rem, 5.5vw, 4.8rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-copilot-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-copilot-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.8; }
.armely-copilot-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-copilot-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-copilot-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-copilot-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-copilot-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-copilot-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-copilot-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-copilot-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-copilot-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-copilot-page .trust-text strong { color: #fff; font-weight: 600; }

  /* SECTIONS */
.armely-copilot-page section { padding: 96px 56px; }
.armely-copilot-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-copilot-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-copilot-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 640px; }
.armely-copilot-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* AI SPECTRUM */
.armely-copilot-page .spectrum { background: var(--navy-mid); }
.armely-copilot-page .spectrum-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-copilot-page .spectrum-row { display: flex; flex-direction: column; gap: 10px; margin-top: 32px; }
.armely-copilot-page .spectrum-level { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; transition: border-color 0.2s; }
.armely-copilot-page .spectrum-level:hover { border-color: rgba(41,78,139,0.3); }
.armely-copilot-page .spectrum-level.highlight { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-copilot-page .spectrum-num { font-size: 0.68rem; font-weight: 800; color: var(--blue); background: var(--blue-dim2); border-radius: 4px; padding: 2px 7px; flex-shrink: 0; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.08em; }
.armely-copilot-page .spectrum-content-title { font-size: 0.875rem; font-weight: 700; color: #1A2540; margin-bottom: 3px; }
.armely-copilot-page .spectrum-content-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }

  /* Platform visual */
.armely-copilot-page .platform-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-copilot-page .platform-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-copilot-page .platform-dots { display: flex; gap: 6px; }
.armely-copilot-page .platform-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-copilot-page .platform-header-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-copilot-page .platform-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.armely-copilot-page .plat-band { border-radius: 9px; padding: 13px 16px; }
.armely-copilot-page .plat-band-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
.armely-copilot-page .plat-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.armely-copilot-page .plat-chip { font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.armely-copilot-page .band-tools { background: var(--blue-dim); }
.armely-copilot-page .band-tools .plat-band-label { color: var(--blue); }
.armely-copilot-page .band-tools .plat-chip { background: var(--blue-dim2); color: var(--blue); }
.armely-copilot-page .band-data { background: rgba(41,78,139,0.05); }
.armely-copilot-page .band-data .plat-band-label { color: var(--blue); }
.armely-copilot-page .band-data .plat-chip { background: rgba(41,78,139,0.1); color: var(--blue); }
.armely-copilot-page .band-gov { background: var(--blue); }
.armely-copilot-page .band-gov .plat-band-label { color: rgba(255,255,255,0.7); }
.armely-copilot-page .band-gov .plat-chip { background: rgba(255,255,255,0.15); color: #fff; }
.armely-copilot-page .band-arrow { text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2px 0; }

  /* VIBE CODING CALLOUT */
.armely-copilot-page .vibe-section { background: var(--navy); }
.armely-copilot-page .vibe-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: start; }
.armely-copilot-page .vibe-left { }
.armely-copilot-page .vibe-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-copilot-page .vibe-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
.armely-copilot-page .vibe-card-icon { font-size: 1.4rem; }
.armely-copilot-page .vibe-card-title { font-size: 1rem; font-weight: 700; color: #1A2540; }
.armely-copilot-page .vibe-card-subtitle { font-size: 0.78rem; color: var(--text-muted); }
.armely-copilot-page .vibe-card-body { padding: 24px; }
.armely-copilot-page .vibe-risk { display: flex; align-items: flex-start; gap: 12px; padding: 14px 0; border-bottom: 1px solid var(--border); }
.armely-copilot-page .vibe-risk:last-child { border-bottom: none; }
.armely-copilot-page .vibe-risk-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
.armely-copilot-page .vibe-risk-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 2px; }
.armely-copilot-page .vibe-risk-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }
.armely-copilot-page .vibe-right { }
.armely-copilot-page .vibe-answer-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 28px; margin-bottom: 12px; }
.armely-copilot-page .vibe-answer-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--blue); margin-bottom: 10px; }
.armely-copilot-page .vibe-answer-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.75; }

  /* DELIVERS */
.armely-copilot-page .delivers { background: var(--navy-mid); }
.armely-copilot-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-copilot-page .deliver-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-copilot-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-copilot-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-copilot-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-copilot-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-copilot-page .journey { background: var(--navy); }
.armely-copilot-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-copilot-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-copilot-page .step:last-child { border-right: none; }
.armely-copilot-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-copilot-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-copilot-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-copilot-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* USE CASES */
.armely-copilot-page .usecases { background: var(--navy-mid); }
.armely-copilot-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-copilot-page .uc-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-copilot-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-copilot-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-copilot-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-copilot-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* TESTIMONIALS */
.armely-copilot-page .testimonials { background: var(--navy); padding: 96px 56px; }
.armely-copilot-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-copilot-page .testi-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-copilot-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-copilot-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-copilot-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-copilot-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-copilot-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-copilot-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-copilot-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY ARMELY */
.armely-copilot-page .why { background: var(--navy-mid); }
.armely-copilot-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-copilot-page .why-list { list-style: none; margin-top: 36px; }
.armely-copilot-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-copilot-page .why-list li:last-child { border-bottom: none; }
.armely-copilot-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-copilot-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-copilot-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-copilot-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-copilot-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-copilot-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-copilot-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-copilot-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-copilot-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-copilot-page .p-stat:nth-child(2) { border-right: none; }
.armely-copilot-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-copilot-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-copilot-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-copilot-page .p-stat-num span { color: var(--blue); }
.armely-copilot-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* CTA */
.armely-copilot-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-copilot-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-copilot-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-copilot-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-copilot-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-copilot-page .form-row { margin-bottom: 14px; }
.armely-copilot-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-copilot-page .form-row input, .armely-copilot-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-copilot-page .form-row input:focus, .armely-copilot-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-copilot-page .form-row select option { background: #fff; color: #1A2540; }
.armely-copilot-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-copilot-page .form-submit:hover { background: var(--blue-lt); }
.armely-copilot-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* FOOTER */
.armely-copilot-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-copilot-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-copilot-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-copilot-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-copilot-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-copilot-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-copilot-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* RESPONSIVE */
  @media (max-width: 900px) {
.armely-copilot-page nav { padding: 16px 24px; }
.armely-copilot-page .nav-links { display: none; }
.armely-copilot-page section { padding: 72px 24px; }
.armely-copilot-page .hero { padding: 110px 24px 72px; }
.armely-copilot-page .spectrum-grid, .armely-copilot-page .vibe-two-col, .armely-copilot-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-copilot-page .delivers-grid, .armely-copilot-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-copilot-page .steps-row { grid-template-columns: 1fr; }
.armely-copilot-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-copilot-page .step:last-child { border-bottom: none; }
.armely-copilot-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-copilot-page .testimonials { padding: 72px 24px; }
.armely-copilot-page .testi-grid { grid-template-columns: 1fr; }
.armely-copilot-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-copilot-page .delivers-grid, .armely-copilot-page .uc-grid { grid-template-columns: 1fr; }
.armely-copilot-page .partner-stats { grid-template-columns: 1fr; }
.armely-copilot-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-copilot-page {
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
.armely-copilot-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-copilot-page .hero::after {
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
.armely-copilot-page .section-title,
.armely-copilot-page .deliver-title,
.armely-copilot-page .uc-title,
.armely-copilot-page .step-title,
.armely-copilot-page .why-item-title,
.armely-copilot-page .form-title {
  color: #162b49;
}
.armely-copilot-page .deliver-card,
.armely-copilot-page .uc-card,
.armely-copilot-page .testi-card,
.armely-copilot-page .platform-card,
.armely-copilot-page .partner-block,
.armely-copilot-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-copilot-page .deliver-card:hover,
.armely-copilot-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-copilot-page .btn-primary,
.armely-copilot-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-copilot-page .btn-primary:hover,
.armely-copilot-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-copilot-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-copilot-page nav,
.armely-copilot-page footer {
  display: none;
}


/* Modern layout update: tighter first section and SVG icon system */
.armely-copilot-page .hero {
  min-height: auto;
  padding: 128px 32px 86px;
  isolation: isolate;
}
.armely-copilot-page .hero-inner {
  width: min(1160px, 100%);
  margin: 0 auto;
  display: grid;
  grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.75fr);
  align-items: center;
  gap: 56px;
  position: relative;
  z-index: 1;
}
.armely-copilot-page .hero-copy { max-width: 760px; }
.armely-copilot-page .hero h1 { max-width: 760px; margin-bottom: 20px; }
.armely-copilot-page .hero-sub { max-width: 640px; margin-bottom: 30px; font-size: 1.02rem; line-height: 1.72; }
.armely-copilot-page .hero-actions { margin-bottom: 34px; }
.armely-copilot-page .hero-trust {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  padding-top: 0;
  border-top: 0;
  max-width: 720px;
}
.armely-copilot-page .hero .trust-item {
  align-items: flex-start;
  background: rgba(255,255,255,0.10);
  border: 1px solid rgba(255,255,255,0.16);
  border-radius: 14px;
  padding: 14px 15px;
  backdrop-filter: blur(10px);
}
.armely-copilot-page .hero .trust-dot {
  width: 22px;
  height: 22px;
  border-radius: 999px;
  background: rgba(255,255,255,0.16);
  border: 1px solid rgba(255,255,255,0.24);
  position: relative;
  margin-top: 1px;
}
.armely-copilot-page .hero .trust-dot::after {
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
.armely-copilot-page .hero .trust-text { color: rgba(255,255,255,0.78); line-height: 1.5; }
.armely-copilot-page .hero-visual {
  min-height: 420px;
  border-radius: 28px;
  position: relative;
  background: linear-gradient(145deg, rgba(255,255,255,0.16), rgba(255,255,255,0.05));
  border: 1px solid rgba(255,255,255,0.18);
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.18), 0 28px 70px rgba(6, 22, 48, 0.24);
  overflow: hidden;
}
.armely-copilot-page .hero-visual::before {
  content: '';
  position: absolute;
  inset: 36px;
  border: 1px solid rgba(255,255,255,0.14);
  border-radius: 999px;
}
.armely-copilot-page .hero-visual::after {
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
.armely-copilot-page .hero-orbit span {
  position: absolute;
  width: 52px;
  height: 52px;
  border-radius: 16px;
  background: rgba(255,255,255,0.14);
  border: 1px solid rgba(255,255,255,0.22);
}
.armely-copilot-page .hero-orbit span:nth-child(1) { left: 56px; top: 88px; }
.armely-copilot-page .hero-orbit span:nth-child(2) { right: 72px; top: 138px; }
.armely-copilot-page .hero-orbit span:nth-child(3) { left: 48%; bottom: 76px; }
.armely-copilot-page .hero-visual-card {
  position: absolute;
  z-index: 2;
  width: 230px;
  padding: 18px;
  border-radius: 18px;
  background: rgba(255,255,255,0.92);
  box-shadow: 0 24px 54px rgba(9, 31, 63, 0.22);
}
.armely-copilot-page .hero-visual-card.top { top: 42px; right: 30px; }
.armely-copilot-page .hero-visual-card.bottom { bottom: 46px; left: 30px; }
.armely-copilot-page .hero-visual-label {
  display: block;
  font-size: 0.78rem;
  font-weight: 700;
  color: #162b49;
  margin-bottom: 12px;
}
.armely-copilot-page .hero-visual-line {
  display: block;
  height: 8px;
  width: 100%;
  border-radius: 999px;
  background: rgba(47, 85, 151, 0.16);
  margin-top: 8px;
}
.armely-copilot-page .hero-visual-line.short { width: 68%; }
.armely-copilot-page .icon-svg {
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
.armely-copilot-page .vibe-card-icon,
.armely-copilot-page .vibe-risk-icon,
.armely-copilot-page .deliver-icon,
.armely-copilot-page .uc-icon,
.armely-copilot-page .why-icon {
  color: var(--blue);
}
.armely-copilot-page .vibe-card-icon,
.armely-copilot-page .vibe-risk-icon {
  width: 42px;
  height: 42px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-copilot-page .vibe-risk-icon { width: 36px; height: 36px; border-radius: 10px; }
.armely-copilot-page .deliver-icon .icon-svg,
.armely-copilot-page .uc-icon .icon-svg,
.armely-copilot-page .why-icon .icon-svg { width: 23px; height: 23px; }
.armely-copilot-page .uc-icon {
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
  .armely-copilot-page .hero-inner { grid-template-columns: 1fr; gap: 32px; }
  .armely-copilot-page .hero-visual { min-height: 320px; }
}
@media (max-width: 680px) {
  .armely-copilot-page .hero { padding: 104px 22px 64px; }
  .armely-copilot-page .hero-trust { grid-template-columns: 1fr; }
  .armely-copilot-page .hero-visual { display: none; }
  .armely-copilot-page .hero-actions a { width: 100%; text-align: center; }
}

/* Focused cleanup for the Generative AI detail page */
.armely-copilot-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  align-items: center;
  background: #173b67;
  border-radius: 0;
}
.armely-copilot-page .hero::after,
.armely-copilot-page .hero-bg-glow,
.armely-copilot-page .hero-visual {
  display: none;
}
.armely-copilot-page .hero-inner {
  width: min(1040px, 100%);
  display: block;
}
.armely-copilot-page .hero-copy {
  max-width: 760px;
}
.armely-copilot-page .hero-eyebrow {
  margin-bottom: 18px;
}
.armely-copilot-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.72);
}
.armely-copilot-page .eyebrow-partner,
.armely-copilot-page .hero-trust {
  display: none;
}
.armely-copilot-page .hero h1 {
  max-width: 760px;
  margin-bottom: 22px;
}
.armely-copilot-page .hero-sub {
  max-width: 680px;
  margin-bottom: 34px;
}
.armely-copilot-page .hero-actions {
  margin-bottom: 0;
}
.armely-copilot-page .hero .btn-primary,
.armely-copilot-page .hero .btn-outline {
  border-radius: 0;
}
.armely-copilot-page .vibe-section {
  background: #fff;
  padding: 84px 56px;
}
.armely-copilot-page .vibe-section .section-inner {
  max-width: 920px;
}
.armely-copilot-page .vibe-section .section-title,
.armely-copilot-page .vibe-section .section-body {
  max-width: 820px;
}
.armely-copilot-page .vibe-section .section-body {
  margin-bottom: 28px;
}
.armely-copilot-page .vibe-two-col {
  grid-template-columns: 1fr;
  gap: 18px;
}
.armely-copilot-page .vibe-card,
.armely-copilot-page .vibe-answer-card {
  border-radius: 0;
  box-shadow: none;
}
.armely-copilot-page .vibe-card-header {
  padding: 18px 20px;
}
.armely-copilot-page .vibe-card-body {
  padding: 8px 20px 12px;
}
.armely-copilot-page .vibe-risk {
  padding: 12px 0;
}
.armely-copilot-page .vibe-right {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
}
.armely-copilot-page .vibe-answer-card {
  margin-bottom: 0;
  padding: 20px;
  background: #f7f9fc;
}
.armely-copilot-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-copilot-page .why > .section-inner > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-copilot-page section:not(.hero) > .section-inner > .section-title,
.armely-copilot-page .why > .section-inner > .section-title {
  max-width: 880px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-copilot-page section:not(.hero) > .section-inner > .section-body,
.armely-copilot-page .why > .section-inner > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-copilot-page .spectrum-grid,
.armely-copilot-page .delivers-grid,
.armely-copilot-page .steps-row,
.armely-copilot-page .uc-grid,
.armely-copilot-page .testi-grid,
.armely-copilot-page .why-two-col {
  margin-top: 56px;
}
.armely-copilot-page .why-two-col {
  align-items: stretch;
}
.armely-copilot-page .why-list {
  margin-top: 0;
}
.armely-copilot-page .why-list,
.armely-copilot-page .partner-block {
  height: 100%;
}
@media (max-width: 900px) {
  .armely-copilot-page .hero {
    padding: 118px 24px 76px;
  }
  .armely-copilot-page .vibe-section {
    padding: 72px 24px;
  }
  .armely-copilot-page .vibe-right {
    grid-template-columns: 1fr;
  }
}



/* Final compact modern cleanup */
.armely-copilot-page {
  --blue: #2f5597;
  --blue-lt: #4779bd;
  --navy-mid: #f6f8fc;
  --navy-card: #ffffff;
  --text-body: #334155;
  --text-muted: #667085;
  --border: rgba(47, 85, 151, 0.14);
}
.armely-copilot-page .hero {
  min-height: auto !important;
  padding: 86px 56px 70px !important;
  background: linear-gradient(135deg, #173b67 0%, #234f86 100%) !important;
  border-radius: 0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}
.armely-copilot-page .hero::after,
.armely-copilot-page .hero-bg-glow,
.armely-copilot-page .hero-visual {
  display: none !important;
}
.armely-copilot-page .hero-inner {
  width: min(1120px, 100%) !important;
  margin: 0 auto !important;
  display: block !important;
}
.armely-copilot-page .hero-copy {
  max-width: 860px !important;
}
.armely-copilot-page .hero-eyebrow {
  margin-bottom: 18px !important;
}
.armely-copilot-page .eyebrow-badge {
  display: inline-flex !important;
  background: rgba(255,255,255,0.10) !important;
  border: 1px solid rgba(255,255,255,0.22) !important;
  border-radius: 999px !important;
  padding: 7px 14px !important;
  color: rgba(255,255,255,0.88) !important;
}
.armely-copilot-page .eyebrow-partner {
  display: inline-flex !important;
  color: rgba(255,255,255,0.66) !important;
}
.armely-copilot-page .hero h1 {
  max-width: 900px !important;
  margin-bottom: 18px !important;
  font-size: clamp(2.5rem, 5vw, 4.9rem) !important;
  line-height: 1.05 !important;
  letter-spacing: -0.04em !important;
}
.armely-copilot-page .hero-sub {
  max-width: 760px !important;
  margin-bottom: 28px !important;
  font-size: 1rem !important;
  line-height: 1.7 !important;
}
.armely-copilot-page .hero-actions {
  margin-bottom: 0 !important;
  gap: 12px !important;
}
.armely-copilot-page .hero .btn-primary,
.armely-copilot-page .hero .btn-outline,
.armely-copilot-page .btn-primary,
.armely-copilot-page .btn-outline,
.armely-copilot-page .form-submit {
  border-radius: 8px !important;
}
.armely-copilot-page section {
  padding: 68px 56px !important;
}
.armely-copilot-page .section-inner {
  max-width: 1120px !important;
}
.armely-copilot-page .section-eyebrow {
  margin-bottom: 10px !important;
}
.armely-copilot-page .section-title {
  margin-bottom: 14px !important;
}
.armely-copilot-page .section-body {
  margin-bottom: 28px !important;
  line-height: 1.65 !important;
}
.armely-copilot-page .spectrum-grid,
.armely-copilot-page .vibe-two-col,
.armely-copilot-page .delivers-grid,
.armely-copilot-page .steps-row,
.armely-copilot-page .uc-grid,
.armely-copilot-page .testi-grid,
.armely-copilot-page .why-two-col {
  margin-top: 34px !important;
  gap: 20px !important;
}
.armely-copilot-page .spectrum-grid,
.armely-copilot-page .why-two-col {
  grid-template-columns: 1fr 1fr !important;
}
.armely-copilot-page .spectrum-row {
  margin-top: 0 !important;
  gap: 10px !important;
}
.armely-copilot-page .spectrum-level,
.armely-copilot-page .deliver-card,
.armely-copilot-page .uc-card,
.armely-copilot-page .testi-card,
.armely-copilot-page .vibe-answer-card,
.armely-copilot-page .partner-block,
.armely-copilot-page .cta-form,
.armely-copilot-page .vibe-card,
.armely-copilot-page .platform-card {
  border-radius: 14px !important;
  box-shadow: 0 14px 36px rgba(18, 47, 82, 0.08) !important;
}
.armely-copilot-page .deliver-card,
.armely-copilot-page .uc-card,
.armely-copilot-page .testi-card {
  padding: 24px 22px !important;
}
.armely-copilot-page .deliver-icon,
.armely-copilot-page .uc-icon,
.armely-copilot-page .why-icon,
.armely-copilot-page .vibe-card-icon,
.armely-copilot-page .vibe-risk-icon {
  border-radius: 12px !important;
  color: var(--blue) !important;
}
.armely-copilot-page .vibe-section {
  padding: 68px 56px !important;
  background: #fff !important;
}
.armely-copilot-page .vibe-section .section-inner {
  max-width: 1120px !important;
}
.armely-copilot-page .vibe-two-col {
  grid-template-columns: 1fr 1fr !important;
  align-items: stretch !important;
}
.armely-copilot-page .vibe-card-body {
  padding: 18px 20px !important;
}
.armely-copilot-page .vibe-right {
  display: flex !important;
  flex-direction: column !important;
  gap: 12px !important;
}
.armely-copilot-page .vibe-answer-card {
  padding: 20px !important;
  background: #f8fafd !important;
}
.armely-copilot-page .steps-row {
  border-radius: 14px !important;
  overflow: hidden !important;
}
.armely-copilot-page .step {
  padding: 24px 18px !important;
}
.armely-copilot-page .step-num {
  font-size: 2rem !important;
  margin-bottom: 10px !important;
}
.armely-copilot-page .why-list {
  margin-top: 0 !important;
}
.armely-copilot-page .why-list li {
  padding: 16px 0 !important;
}
.armely-copilot-page .partner-block-top,
.armely-copilot-page .p-stat {
  padding: 22px !important;
}
.armely-copilot-page .cta-inner {
  padding: 68px 56px !important;
  gap: 40px !important;
}
@media (max-width: 900px) {
  .armely-copilot-page .hero { padding: 88px 24px 58px !important; }
  .armely-copilot-page section,
  .armely-copilot-page .vibe-section { padding: 56px 24px !important; }
  .armely-copilot-page .spectrum-grid,
  .armely-copilot-page .vibe-two-col,
  .armely-copilot-page .why-two-col { grid-template-columns: 1fr !important; }
  .armely-copilot-page .delivers-grid,
  .armely-copilot-page .uc-grid { grid-template-columns: 1fr 1fr !important; }
  .armely-copilot-page .cta-inner { padding: 56px 24px !important; grid-template-columns: 1fr !important; }
}
@media (max-width: 600px) {
  .armely-copilot-page .hero h1 { font-size: clamp(2.15rem, 11vw, 3.2rem) !important; }
  .armely-copilot-page .hero-eyebrow { flex-direction: column !important; align-items: flex-start !important; gap: 8px !important; }
  .armely-copilot-page .hero-actions a { width: 100% !important; text-align: center !important; }
  .armely-copilot-page .delivers-grid,
  .armely-copilot-page .uc-grid { grid-template-columns: 1fr !important; }
}


</style>
<div class="armely-copilot-page">

<!-- NAV -->
<nav>
  <div class="logo">
    <div class="logo-mark">A</div>
    <span class="logo-text">armely</span>
  </div>
  <ul class="nav-links">
    <li><a href="#what-we-deliver">Services</a></li>
    <li><a href="#journey">Our Process</a></li>
    <li><a href="#why-armely">Why Armely</a></li>
    <li><a href="#contact" class="nav-cta">Get Started</a></li>
  </ul>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-bg-glow2"></div>
  <div class="hero-eyebrow">
    <span class="eyebrow-badge">Microsoft 365 Copilot Business</span>
    <span class="eyebrow-partner">Delivered by a certified Microsoft partner</span>
  </div>
  <h1>AI that works<br>the way <span class="hl">your business</span><br>actually works.</h1>
  <p class="hero-sub">Armely licenses, deploys, and embeds Microsoft 365 Copilot into your team's daily workflows, so adoption is real, not just access.</p>
  <div class="hero-actions">
    <a href="#contact" class="btn-primary">Book a Free Assessment</a>
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
      <span class="trust-text"><strong>No seat minimums</strong>, start with one team</span>
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
        <p class="section-body">Microsoft 365 Copilot Business is an AI assistant woven directly into Word, Excel, PowerPoint, Outlook, and Teams. It drafts, summarises, analyses, and responds, freeing your people from admin so they can focus on the work that matters.</p>
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
              Summarize last week's project emails and flag anything that needs a reply today.
            </div>
            <div class="chat-bubble copilot">
              <div class="bubble-label c">Copilot</div>
              Found 14 relevant threads. Two need replies: a contract review from Sarah due Friday, and a vendor quote requiring sign-off. The rest are informational, here's a 3-line summary of each.
            </div>
            <div class="chat-bubble user">
              <div class="bubble-label u">You</div>
              Draft a reply to Sarah, professional but concise.
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
    <h2 class="section-title">We don't just sell licenses. We make Copilot work for your business.</h2>
    <p class="section-body">As a certified Microsoft partner, Armely handles the full picture, from securing the best licensing deal to building the habits that make AI stick.</p>
    <div class="delivers-grid">
      <div class="deliver-card">
        <div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div>
        <div class="deliver-title">Readiness Assessment</div>
        <div class="deliver-desc">Before a single license is activated, we audit your Microsoft 365 environment, data governance, permissions, and security posture. Copilot lands on a clean, safe foundation, not into a messy tenant.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🏷️</div>
        <div class="deliver-title">Best-Value Licensing</div>
        <div class="deliver-desc">Our Microsoft partnership gives us access to SMB bundle pricing and promotional offers that aren't available through direct purchase. We find the right plan for your team size and budget, often at a significant discount.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg></div>
        <div class="deliver-title">Hands-On Implementation</div>
        <div class="deliver-desc">We don't hand you a login and a help link. Our engineers configure Copilot for your specific workflows, integrate it with your existing systems, and run role-by-role deployment so every team hits the ground running.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></div>
        <div class="deliver-title">Adoption Training</div>
        <div class="deliver-desc">People use tools they understand. We run targeted training sessions for each department, showing your team exactly how Copilot accelerates their specific work, not just a generic demo.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg></div>
        <div class="deliver-title">Adoption Tracking</div>
        <div class="deliver-desc">Usage reports, quarterly business reviews, and proactive check-ins mean we catch low adoption early and fix it before licenses go to waste. You always know your Copilot ROI.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div>
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
    <h2 class="section-title">From first conversation to full productivity, fast.</h2>
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
        <span class="step-tag">1-2 days</span>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-title">Pilot Rollout</div>
        <div class="step-desc">Start with a target team. Real workflows, real feedback, measurable results before going organization-wide.</div>
        <span class="step-tag">Week 1-2</span>
      </div>
      <div class="step">
        <div class="step-num">04</div>
        <div class="step-title">Full Deployment</div>
        <div class="step-desc">Scale across the organization with role-specific training and Armely managing every step of rollout.</div>
        <span class="step-tag">Week 3-4</span>
      </div>
      <div class="step">
        <div class="step-num">05</div>
        <div class="step-title">Continuous Success</div>
        <div class="step-desc">Monthly usage reviews, proactive support, and ongoing optimization as your team and Microsoft's AI evolve.</div>
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
    <p class="section-body">Copilot Business delivers measurable time savings across every department, from operations and finance to sales and leadership.</p>
    <div class="uc-grid">
      <div class="uc-card">
        <span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></span>
        <div class="uc-title">Email & Communications</div>
        <div class="uc-desc">Copilot in Outlook summarises long threads, drafts replies in your tone, and flags what genuinely needs attention. Inbox zero is no longer a myth.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg></span>
        <div class="uc-title">Data & Reporting</div>
        <div class="uc-desc">Ask Copilot in Excel to analyze a spreadsheet, spot anomalies, or build a summary chart, in plain English. No formula expertise required.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg></span>
        <div class="uc-title">Meetings & Follow-ups</div>
        <div class="uc-desc">Copilot in Teams transcribes, summarises, and extracts action items from every meeting. Stop taking notes and actually contribute.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg></span>
        <div class="uc-title">Documents & Proposals</div>
        <div class="uc-desc">Turn bullet points into polished proposals in Word. Summarize a 40-page report into a one-page brief. First drafts in seconds, not hours.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true"><circle cx="13.5" cy="6.5" r=".5"/><circle cx="17.5" cy="10.5" r=".5"/><circle cx="8.5" cy="7.5" r=".5"/><circle cx="6.5" cy="12.5" r=".5"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/></svg></span>
        <div class="uc-title">Presentations</div>
        <div class="uc-desc">Copilot in PowerPoint builds structured slide decks from a document or prompt, branded and ready for your edits, not built from scratch under pressure.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></span>
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
        <p class="section-body">Most businesses activate Copilot and wonder why nobody's using it six months later. We're built specifically to prevent that, combining licensing expertise, technical implementation, and hands-on change management.</p>
        <ul class="why-list">
          <li>
            <div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg></div>
            <div>
              <div class="why-item-title">Certified Copilot Implementors</div>
              <div class="why-item-desc">Our engineers are Microsoft-certified Copilot implementors, trained in Copilot Practice Builder methodology, CloudLabs deployment, and hands-on change management across SMB environments.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
            <div>
              <div class="why-item-title">Security-First by Default</div>
              <div class="why-item-desc">AI introduces new data exposure risks. We assess and harden your environment before go-live so Copilot runs securely inside your existing Microsoft 365 tenant, your data never leaves.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
            <div>
              <div class="why-item-title">Access to Partner-Only Pricing</div>
              <div class="why-item-desc">As a Microsoft-authorized CSP partner, we access SMB bundle promotions and volume pricing that aren't available to direct buyers, and we pass those savings on to you.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 17 6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg></div>
            <div>
              <div class="why-item-title">Proven SMB Track Record</div>
              <div class="why-item-desc">We've implemented Microsoft solutions for organizations including Plano ISD, Swope Health Systems, and UNMC, bringing enterprise-grade delivery to businesses of every size.</div>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <div class="partner-block">
          <div class="partner-block-top">
            <div class="partner-label">Microsoft Authorized Partner</div>
            <p class="partner-text">Armely's Microsoft partnership gives us access to licensing, technical resources, and bundle pricing that independent buyers can't reach. That means better value for you, faster deployment, and support backed by the full Microsoft ecosystem.</p>
          </div>
          <div class="partner-stats">
            <div class="p-stat">
              <div class="p-stat-num">70<span>%</span></div>
              <div class="p-stat-label">of Fortune 500 already using Microsoft Copilot</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">300</div>
              <div class="p-stat-label">user maximum, purpose-built for SMBs</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">6</div>
              <div class="p-stat-label">Microsoft 365 apps with native Copilot integration</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">87<span>%</span></div>
              <div class="p-stat-label">of organizations say AI gives a competitive edge</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA / CONTACT -->
<section class="cta-section" id="contact">
  <div class="cta-inner">
    <div>
      <div class="section-eyebrow">Get Started</div>
      <h2 class="section-title">Let's find the right Copilot plan for your business.</h2>
      <p class="section-body">Book a free 30-minute Copilot Readiness Assessment. We'll review your Microsoft 365 environment, understand your team's workflows, and come back with a clear recommendation and a pricing quote tailored to your situation, no obligation.</p>
      <div style="margin-top: 28px; display: flex; flex-direction: column; gap: 12px;">
        <div class="trust-item">
          <span class="trust-dot"></span>
          <span class="trust-text">Free assessment, no commitment required</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot"></span>
          <span class="trust-text">Custom quote with partner pricing included</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot"></span>
          <span class="trust-text">Response within one business day</span>
        </div>
      </div>
    </div>
    <div class="cta-form">
      <div class="form-title">Book Your Free Assessment</div>
      <div class="form-sub">We'll be in touch within one business day.</div>
      <div class="form-row">
        <label>Full Name</label>
        <input type="text" placeholder="Jane Smith">
      </div>
      <div class="form-row">
        <label>Business Email</label>
        <input type="email" placeholder="jane@yourcompany.com">
      </div>
      <div class="form-row">
        <label>Company Name</label>
        <input type="text" placeholder="Acme Corp">
      </div>
      <div class="form-row">
        <label>Team Size</label>
        <select>
          <option value="">Select...</option>
          <option>1-10 people</option>
          <option>11-50 people</option>
          <option>51-150 people</option>
          <option>151-300 people</option>
        </select>
      </div>
      <button class="form-submit">Request Free Assessment →</button>
      <div class="form-note">No spam. No sales pressure. Just a useful conversation.</div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-logo-row">
    <div class="footer-lm">A</div>
    <span class="footer-lt">armely</span>
  </div>
  <div class="footer-note">© 2026 Armely · www.armely.com · Your Trusted Source for Digital Excellence</div>
  <div class="footer-badges">
    <span class="badge-chip">Microsoft CSP Partner</span>
    <span class="badge-chip">Microsoft Authorized Reseller</span>
    <span class="badge-chip">Copilot Certified</span>
  </div>
</footer>

</div>
<style>

.armely-snowflake-page *, .armely-snowflake-page *::before, .armely-snowflake-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-snowflake-page {
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

.armely-snowflake-page { scroll-behavior: smooth; }
.armely-snowflake-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* NAV */
.armely-snowflake-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-snowflake-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-snowflake-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-snowflake-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-snowflake-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-snowflake-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-snowflake-page .nav-links a:hover { color: #fff; }
.armely-snowflake-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-snowflake-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* HERO */
.armely-snowflake-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-snowflake-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-snowflake-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-snowflake-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-snowflake-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-snowflake-page .hero h1 { font-size: clamp(2.6rem, 5.5vw, 4.8rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-snowflake-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-snowflake-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.8; }
.armely-snowflake-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-snowflake-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-snowflake-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-snowflake-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-snowflake-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-snowflake-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-snowflake-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-snowflake-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-snowflake-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-snowflake-page .trust-text strong { color: #fff; font-weight: 600; }

  /* SECTIONS */
.armely-snowflake-page section { padding: 96px 56px; }
.armely-snowflake-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-snowflake-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-snowflake-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 640px; }
.armely-snowflake-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* AI SPECTRUM */
.armely-snowflake-page .spectrum { background: var(--navy-mid); }
.armely-snowflake-page .spectrum-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-snowflake-page .spectrum-row { display: flex; flex-direction: column; gap: 10px; margin-top: 32px; }
.armely-snowflake-page .spectrum-level { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; transition: border-color 0.2s; }
.armely-snowflake-page .spectrum-level:hover { border-color: rgba(41,78,139,0.3); }
.armely-snowflake-page .spectrum-level.highlight { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-snowflake-page .spectrum-num { font-size: 0.68rem; font-weight: 800; color: var(--blue); background: var(--blue-dim2); border-radius: 4px; padding: 2px 7px; flex-shrink: 0; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.08em; }
.armely-snowflake-page .spectrum-content-title { font-size: 0.875rem; font-weight: 700; color: #1A2540; margin-bottom: 3px; }
.armely-snowflake-page .spectrum-content-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }

  /* Platform visual */
.armely-snowflake-page .platform-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-snowflake-page .platform-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-snowflake-page .platform-dots { display: flex; gap: 6px; }
.armely-snowflake-page .platform-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-snowflake-page .platform-header-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-snowflake-page .platform-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.armely-snowflake-page .plat-band { border-radius: 9px; padding: 13px 16px; }
.armely-snowflake-page .plat-band-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
.armely-snowflake-page .plat-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.armely-snowflake-page .plat-chip { font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.armely-snowflake-page .band-tools { background: var(--blue-dim); }
.armely-snowflake-page .band-tools .plat-band-label { color: var(--blue); }
.armely-snowflake-page .band-tools .plat-chip { background: var(--blue-dim2); color: var(--blue); }
.armely-snowflake-page .band-data { background: rgba(41,78,139,0.05); }
.armely-snowflake-page .band-data .plat-band-label { color: var(--blue); }
.armely-snowflake-page .band-data .plat-chip { background: rgba(41,78,139,0.1); color: var(--blue); }
.armely-snowflake-page .band-gov { background: var(--blue); }
.armely-snowflake-page .band-gov .plat-band-label { color: rgba(255,255,255,0.7); }
.armely-snowflake-page .band-gov .plat-chip { background: rgba(255,255,255,0.15); color: #fff; }
.armely-snowflake-page .band-arrow { text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2px 0; }

  /* VIBE CODING CALLOUT */
.armely-snowflake-page .vibe-section { background: var(--navy); }
.armely-snowflake-page .vibe-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: start; }
.armely-snowflake-page .vibe-left { }
.armely-snowflake-page .vibe-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-snowflake-page .vibe-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
.armely-snowflake-page .vibe-card-icon { font-size: 1.4rem; }
.armely-snowflake-page .vibe-card-title { font-size: 1rem; font-weight: 700; color: #1A2540; }
.armely-snowflake-page .vibe-card-subtitle { font-size: 0.78rem; color: var(--text-muted); }
.armely-snowflake-page .vibe-card-body { padding: 24px; }
.armely-snowflake-page .vibe-risk { display: flex; align-items: flex-start; gap: 12px; padding: 14px 0; border-bottom: 1px solid var(--border); }
.armely-snowflake-page .vibe-risk:last-child { border-bottom: none; }
.armely-snowflake-page .vibe-risk-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
.armely-snowflake-page .vibe-risk-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 2px; }
.armely-snowflake-page .vibe-risk-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }
.armely-snowflake-page .vibe-right { }
.armely-snowflake-page .vibe-answer-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 28px; margin-bottom: 12px; }
.armely-snowflake-page .vibe-answer-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--blue); margin-bottom: 10px; }
.armely-snowflake-page .vibe-answer-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.75; }

  /* DELIVERS */
.armely-snowflake-page .delivers { background: var(--navy-mid); }
.armely-snowflake-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-snowflake-page .deliver-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-snowflake-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-snowflake-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-snowflake-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-snowflake-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-snowflake-page .journey { background: var(--navy); }
.armely-snowflake-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-snowflake-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-snowflake-page .step:last-child { border-right: none; }
.armely-snowflake-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-snowflake-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-snowflake-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-snowflake-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* USE CASES */
.armely-snowflake-page .usecases { background: var(--navy-mid); }
.armely-snowflake-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-snowflake-page .uc-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-snowflake-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-snowflake-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-snowflake-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-snowflake-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* TESTIMONIALS */
.armely-snowflake-page .testimonials { background: var(--navy); padding: 96px 56px; }
.armely-snowflake-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-snowflake-page .testi-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-snowflake-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-snowflake-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-snowflake-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-snowflake-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-snowflake-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-snowflake-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-snowflake-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY ARMELY */
.armely-snowflake-page .why { background: var(--navy-mid); }
.armely-snowflake-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-snowflake-page .why-list { list-style: none; margin-top: 36px; }
.armely-snowflake-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-snowflake-page .why-list li:last-child { border-bottom: none; }
.armely-snowflake-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-snowflake-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-snowflake-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-snowflake-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-snowflake-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-snowflake-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-snowflake-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-snowflake-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-snowflake-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-snowflake-page .p-stat:nth-child(2) { border-right: none; }
.armely-snowflake-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-snowflake-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-snowflake-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-snowflake-page .p-stat-num span { color: var(--blue); }
.armely-snowflake-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* CTA */
.armely-snowflake-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-snowflake-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-snowflake-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-snowflake-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-snowflake-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-snowflake-page .form-row { margin-bottom: 14px; }
.armely-snowflake-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-snowflake-page .form-row input, .armely-snowflake-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-snowflake-page .form-row input:focus, .armely-snowflake-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-snowflake-page .form-row select option { background: #fff; color: #1A2540; }
.armely-snowflake-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-snowflake-page .form-submit:hover { background: var(--blue-lt); }
.armely-snowflake-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* FOOTER */
.armely-snowflake-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-snowflake-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-snowflake-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-snowflake-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-snowflake-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-snowflake-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-snowflake-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* RESPONSIVE */
  @media (max-width: 900px) {
.armely-snowflake-page nav { padding: 16px 24px; }
.armely-snowflake-page .nav-links { display: none; }
.armely-snowflake-page section { padding: 72px 24px; }
.armely-snowflake-page .hero { padding: 110px 24px 72px; }
.armely-snowflake-page .spectrum-grid, .armely-snowflake-page .vibe-two-col, .armely-snowflake-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-snowflake-page .delivers-grid, .armely-snowflake-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-snowflake-page .steps-row { grid-template-columns: 1fr; }
.armely-snowflake-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-snowflake-page .step:last-child { border-bottom: none; }
.armely-snowflake-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-snowflake-page .testimonials { padding: 72px 24px; }
.armely-snowflake-page .testi-grid { grid-template-columns: 1fr; }
.armely-snowflake-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-snowflake-page .delivers-grid, .armely-snowflake-page .uc-grid { grid-template-columns: 1fr; }
.armely-snowflake-page .partner-stats { grid-template-columns: 1fr; }
.armely-snowflake-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-snowflake-page {
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
.armely-snowflake-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-snowflake-page .hero::after {
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
.armely-snowflake-page .section-title,
.armely-snowflake-page .deliver-title,
.armely-snowflake-page .uc-title,
.armely-snowflake-page .step-title,
.armely-snowflake-page .why-item-title,
.armely-snowflake-page .form-title {
  color: #162b49;
}
.armely-snowflake-page .deliver-card,
.armely-snowflake-page .uc-card,
.armely-snowflake-page .testi-card,
.armely-snowflake-page .platform-card,
.armely-snowflake-page .partner-block,
.armely-snowflake-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-snowflake-page .deliver-card:hover,
.armely-snowflake-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-snowflake-page .btn-primary,
.armely-snowflake-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-snowflake-page .btn-primary:hover,
.armely-snowflake-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-snowflake-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-snowflake-page nav,
.armely-snowflake-page footer {
  display: none;
}


/* Modern layout update: tighter first section and SVG icon system */
.armely-snowflake-page .hero {
  min-height: auto;
  padding: 128px 32px 86px;
  isolation: isolate;
}
.armely-snowflake-page .hero-inner {
  width: min(1160px, 100%);
  margin: 0 auto;
  display: grid;
  grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.75fr);
  align-items: center;
  gap: 56px;
  position: relative;
  z-index: 1;
}
.armely-snowflake-page .hero-copy { max-width: 760px; }
.armely-snowflake-page .hero h1 { max-width: 760px; margin-bottom: 20px; }
.armely-snowflake-page .hero-sub { max-width: 640px; margin-bottom: 30px; font-size: 1.02rem; line-height: 1.72; }
.armely-snowflake-page .hero-actions { margin-bottom: 34px; }
.armely-snowflake-page .hero-trust {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  padding-top: 0;
  border-top: 0;
  max-width: 720px;
}
.armely-snowflake-page .hero .trust-item {
  align-items: flex-start;
  background: rgba(255,255,255,0.10);
  border: 1px solid rgba(255,255,255,0.16);
  border-radius: 14px;
  padding: 14px 15px;
  backdrop-filter: blur(10px);
}
.armely-snowflake-page .hero .trust-dot {
  width: 22px;
  height: 22px;
  border-radius: 999px;
  background: rgba(255,255,255,0.16);
  border: 1px solid rgba(255,255,255,0.24);
  position: relative;
  margin-top: 1px;
}
.armely-snowflake-page .hero .trust-dot::after {
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
.armely-snowflake-page .hero .trust-text { color: rgba(255,255,255,0.78); line-height: 1.5; }
.armely-snowflake-page .hero-visual {
  min-height: 420px;
  border-radius: 28px;
  position: relative;
  background: linear-gradient(145deg, rgba(255,255,255,0.16), rgba(255,255,255,0.05));
  border: 1px solid rgba(255,255,255,0.18);
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.18), 0 28px 70px rgba(6, 22, 48, 0.24);
  overflow: hidden;
}
.armely-snowflake-page .hero-visual::before {
  content: '';
  position: absolute;
  inset: 36px;
  border: 1px solid rgba(255,255,255,0.14);
  border-radius: 999px;
}
.armely-snowflake-page .hero-visual::after {
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
.armely-snowflake-page .hero-orbit span {
  position: absolute;
  width: 52px;
  height: 52px;
  border-radius: 16px;
  background: rgba(255,255,255,0.14);
  border: 1px solid rgba(255,255,255,0.22);
}
.armely-snowflake-page .hero-orbit span:nth-child(1) { left: 56px; top: 88px; }
.armely-snowflake-page .hero-orbit span:nth-child(2) { right: 72px; top: 138px; }
.armely-snowflake-page .hero-orbit span:nth-child(3) { left: 48%; bottom: 76px; }
.armely-snowflake-page .hero-visual-card {
  position: absolute;
  z-index: 2;
  width: 230px;
  padding: 18px;
  border-radius: 18px;
  background: rgba(255,255,255,0.92);
  box-shadow: 0 24px 54px rgba(9, 31, 63, 0.22);
}
.armely-snowflake-page .hero-visual-card.top { top: 42px; right: 30px; }
.armely-snowflake-page .hero-visual-card.bottom { bottom: 46px; left: 30px; }
.armely-snowflake-page .hero-visual-label {
  display: block;
  font-size: 0.78rem;
  font-weight: 700;
  color: #162b49;
  margin-bottom: 12px;
}
.armely-snowflake-page .hero-visual-line {
  display: block;
  height: 8px;
  width: 100%;
  border-radius: 999px;
  background: rgba(47, 85, 151, 0.16);
  margin-top: 8px;
}
.armely-snowflake-page .hero-visual-line.short { width: 68%; }
.armely-snowflake-page .icon-svg {
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
.armely-snowflake-page .vibe-card-icon,
.armely-snowflake-page .vibe-risk-icon,
.armely-snowflake-page .deliver-icon,
.armely-snowflake-page .uc-icon,
.armely-snowflake-page .why-icon {
  color: var(--blue);
}
.armely-snowflake-page .vibe-card-icon,
.armely-snowflake-page .vibe-risk-icon {
  width: 42px;
  height: 42px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-snowflake-page .vibe-risk-icon { width: 36px; height: 36px; border-radius: 10px; }
.armely-snowflake-page .deliver-icon .icon-svg,
.armely-snowflake-page .uc-icon .icon-svg,
.armely-snowflake-page .why-icon .icon-svg { width: 23px; height: 23px; }
.armely-snowflake-page .uc-icon {
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
  .armely-snowflake-page .hero-inner { grid-template-columns: 1fr; gap: 32px; }
  .armely-snowflake-page .hero-visual { min-height: 320px; }
}
@media (max-width: 680px) {
  .armely-snowflake-page .hero { padding: 104px 22px 64px; }
  .armely-snowflake-page .hero-trust { grid-template-columns: 1fr; }
  .armely-snowflake-page .hero-visual { display: none; }
  .armely-snowflake-page .hero-actions a { width: 100%; text-align: center; }
}

/* Focused cleanup for the Generative AI detail page */
.armely-snowflake-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  align-items: center;
  background: #173b67;
  border-radius: 0;
}
.armely-snowflake-page .hero::after,
.armely-snowflake-page .hero-bg-glow,
.armely-snowflake-page .hero-visual {
  display: none;
}
.armely-snowflake-page .hero-inner {
  width: min(1040px, 100%);
  display: block;
}
.armely-snowflake-page .hero-copy {
  max-width: 760px;
}
.armely-snowflake-page .hero-eyebrow {
  margin-bottom: 18px;
}
.armely-snowflake-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.72);
}
.armely-snowflake-page .eyebrow-partner,
.armely-snowflake-page .hero-trust {
  display: none;
}
.armely-snowflake-page .hero h1 {
  max-width: 760px;
  margin-bottom: 22px;
}
.armely-snowflake-page .hero-sub {
  max-width: 680px;
  margin-bottom: 34px;
}
.armely-snowflake-page .hero-actions {
  margin-bottom: 0;
}
.armely-snowflake-page .hero .btn-primary,
.armely-snowflake-page .hero .btn-outline {
  border-radius: 0;
}
.armely-snowflake-page .vibe-section {
  background: #fff;
  padding: 84px 56px;
}
.armely-snowflake-page .vibe-section .section-inner {
  max-width: 920px;
}
.armely-snowflake-page .vibe-section .section-title,
.armely-snowflake-page .vibe-section .section-body {
  max-width: 820px;
}
.armely-snowflake-page .vibe-section .section-body {
  margin-bottom: 28px;
}
.armely-snowflake-page .vibe-two-col {
  grid-template-columns: 1fr;
  gap: 18px;
}
.armely-snowflake-page .vibe-card,
.armely-snowflake-page .vibe-answer-card {
  border-radius: 0;
  box-shadow: none;
}
.armely-snowflake-page .vibe-card-header {
  padding: 18px 20px;
}
.armely-snowflake-page .vibe-card-body {
  padding: 8px 20px 12px;
}
.armely-snowflake-page .vibe-risk {
  padding: 12px 0;
}
.armely-snowflake-page .vibe-right {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
}
.armely-snowflake-page .vibe-answer-card {
  margin-bottom: 0;
  padding: 20px;
  background: #f7f9fc;
}
.armely-snowflake-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-snowflake-page .why > .section-inner > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-snowflake-page section:not(.hero) > .section-inner > .section-title,
.armely-snowflake-page .why > .section-inner > .section-title {
  max-width: 880px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-snowflake-page section:not(.hero) > .section-inner > .section-body,
.armely-snowflake-page .why > .section-inner > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-snowflake-page .spectrum-grid,
.armely-snowflake-page .delivers-grid,
.armely-snowflake-page .steps-row,
.armely-snowflake-page .uc-grid,
.armely-snowflake-page .testi-grid,
.armely-snowflake-page .why-two-col {
  margin-top: 56px;
}
.armely-snowflake-page .why-two-col {
  align-items: stretch;
}
.armely-snowflake-page .why-list {
  margin-top: 0;
}
.armely-snowflake-page .why-list,
.armely-snowflake-page .partner-block {
  height: 100%;
}
@media (max-width: 900px) {
  .armely-snowflake-page .hero {
    padding: 118px 24px 76px;
  }
  .armely-snowflake-page .vibe-section {
    padding: 72px 24px;
  }
  .armely-snowflake-page .vibe-right {
    grid-template-columns: 1fr;
  }
}



/* Final compact modern cleanup */
.armely-snowflake-page {
  --blue: #2f5597;
  --blue-lt: #4779bd;
  --navy-mid: #f6f8fc;
  --navy-card: #ffffff;
  --text-body: #334155;
  --text-muted: #667085;
  --border: rgba(47, 85, 151, 0.14);
}
.armely-snowflake-page .hero {
  min-height: auto !important;
  padding: 86px 56px 70px !important;
  background: linear-gradient(135deg, #173b67 0%, #234f86 100%) !important;
  border-radius: 0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}
.armely-snowflake-page .hero::after,
.armely-snowflake-page .hero-bg-glow,
.armely-snowflake-page .hero-visual {
  display: none !important;
}
.armely-snowflake-page .hero-inner {
  width: min(1120px, 100%) !important;
  margin: 0 auto !important;
  display: block !important;
}
.armely-snowflake-page .hero-copy {
  max-width: 860px !important;
}
.armely-snowflake-page .hero-eyebrow {
  margin-bottom: 18px !important;
}
.armely-snowflake-page .eyebrow-badge {
  display: inline-flex !important;
  background: rgba(255,255,255,0.10) !important;
  border: 1px solid rgba(255,255,255,0.22) !important;
  border-radius: 999px !important;
  padding: 7px 14px !important;
  color: rgba(255,255,255,0.88) !important;
}
.armely-snowflake-page .eyebrow-partner {
  display: inline-flex !important;
  color: rgba(255,255,255,0.66) !important;
}
.armely-snowflake-page .hero h1 {
  max-width: 900px !important;
  margin-bottom: 18px !important;
  font-size: clamp(2.5rem, 5vw, 4.9rem) !important;
  line-height: 1.05 !important;
  letter-spacing: -0.04em !important;
}
.armely-snowflake-page .hero-sub {
  max-width: 760px !important;
  margin-bottom: 28px !important;
  font-size: 1rem !important;
  line-height: 1.7 !important;
}
.armely-snowflake-page .hero-actions {
  margin-bottom: 0 !important;
  gap: 12px !important;
}
.armely-snowflake-page .hero .btn-primary,
.armely-snowflake-page .hero .btn-outline,
.armely-snowflake-page .btn-primary,
.armely-snowflake-page .btn-outline,
.armely-snowflake-page .form-submit {
  border-radius: 8px !important;
}
.armely-snowflake-page section {
  padding: 68px 56px !important;
}
.armely-snowflake-page .section-inner {
  max-width: 1120px !important;
}
.armely-snowflake-page .section-eyebrow {
  margin-bottom: 10px !important;
}
.armely-snowflake-page .section-title {
  margin-bottom: 14px !important;
}
.armely-snowflake-page .section-body {
  margin-bottom: 28px !important;
  line-height: 1.65 !important;
}
.armely-snowflake-page .spectrum-grid,
.armely-snowflake-page .vibe-two-col,
.armely-snowflake-page .delivers-grid,
.armely-snowflake-page .steps-row,
.armely-snowflake-page .uc-grid,
.armely-snowflake-page .testi-grid,
.armely-snowflake-page .why-two-col {
  margin-top: 34px !important;
  gap: 20px !important;
}
.armely-snowflake-page .spectrum-grid,
.armely-snowflake-page .why-two-col {
  grid-template-columns: 1fr 1fr !important;
}
.armely-snowflake-page .spectrum-row {
  margin-top: 0 !important;
  gap: 10px !important;
}
.armely-snowflake-page .spectrum-level,
.armely-snowflake-page .deliver-card,
.armely-snowflake-page .uc-card,
.armely-snowflake-page .testi-card,
.armely-snowflake-page .vibe-answer-card,
.armely-snowflake-page .partner-block,
.armely-snowflake-page .cta-form,
.armely-snowflake-page .vibe-card,
.armely-snowflake-page .platform-card {
  border-radius: 14px !important;
  box-shadow: 0 14px 36px rgba(18, 47, 82, 0.08) !important;
}
.armely-snowflake-page .deliver-card,
.armely-snowflake-page .uc-card,
.armely-snowflake-page .testi-card {
  padding: 24px 22px !important;
}
.armely-snowflake-page .deliver-icon,
.armely-snowflake-page .uc-icon,
.armely-snowflake-page .why-icon,
.armely-snowflake-page .vibe-card-icon,
.armely-snowflake-page .vibe-risk-icon {
  border-radius: 12px !important;
  color: var(--blue) !important;
}
.armely-snowflake-page .vibe-section {
  padding: 68px 56px !important;
  background: #fff !important;
}
.armely-snowflake-page .vibe-section .section-inner {
  max-width: 1120px !important;
}
.armely-snowflake-page .vibe-two-col {
  grid-template-columns: 1fr 1fr !important;
  align-items: stretch !important;
}
.armely-snowflake-page .vibe-card-body {
  padding: 18px 20px !important;
}
.armely-snowflake-page .vibe-right {
  display: flex !important;
  flex-direction: column !important;
  gap: 12px !important;
}
.armely-snowflake-page .vibe-answer-card {
  padding: 20px !important;
  background: #f8fafd !important;
}
.armely-snowflake-page .steps-row {
  border-radius: 14px !important;
  overflow: hidden !important;
}
.armely-snowflake-page .step {
  padding: 24px 18px !important;
}
.armely-snowflake-page .step-num {
  font-size: 2rem !important;
  margin-bottom: 10px !important;
}
.armely-snowflake-page .why-list {
  margin-top: 0 !important;
}
.armely-snowflake-page .why-list li {
  padding: 16px 0 !important;
}
.armely-snowflake-page .partner-block-top,
.armely-snowflake-page .p-stat {
  padding: 22px !important;
}
.armely-snowflake-page .cta-inner {
  padding: 68px 56px !important;
  gap: 40px !important;
}
@media (max-width: 900px) {
  .armely-snowflake-page .hero { padding: 88px 24px 58px !important; }
  .armely-snowflake-page section,
  .armely-snowflake-page .vibe-section { padding: 56px 24px !important; }
  .armely-snowflake-page .spectrum-grid,
  .armely-snowflake-page .vibe-two-col,
  .armely-snowflake-page .why-two-col { grid-template-columns: 1fr !important; }
  .armely-snowflake-page .delivers-grid,
  .armely-snowflake-page .uc-grid { grid-template-columns: 1fr 1fr !important; }
  .armely-snowflake-page .cta-inner { padding: 56px 24px !important; grid-template-columns: 1fr !important; }
}
@media (max-width: 600px) {
  .armely-snowflake-page .hero h1 { font-size: clamp(2.15rem, 11vw, 3.2rem) !important; }
  .armely-snowflake-page .hero-eyebrow { flex-direction: column !important; align-items: flex-start !important; gap: 8px !important; }
  .armely-snowflake-page .hero-actions a { width: 100% !important; text-align: center !important; }
  .armely-snowflake-page .delivers-grid,
  .armely-snowflake-page .uc-grid { grid-template-columns: 1fr !important; }
}


.armely-snowflake-page .cr-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:28px; margin-bottom:28px; }
.armely-snowflake-page .cr-card { background:#fff; border:1px solid var(--border); border-radius:12px; padding:20px 22px; box-shadow:0 2px 10px rgba(18,47,82,0.04); }
.armely-snowflake-page .cr-label { display:flex; align-items:center; gap:9px; margin-bottom:10px; }
.armely-snowflake-page .cr-check { width:20px; height:20px; display:flex; align-items:center; justify-content:center; background:var(--blue-dim); border:1px solid var(--blue-dim2); border-radius:50%; flex-shrink:0; color:var(--blue); }
.armely-snowflake-page .cr-check .icon-svg { width:11px; height:11px; stroke-width:3; }
.armely-snowflake-page .cr-industry { font-size:0.875rem; font-weight:700; color:#1A2540; }
.armely-snowflake-page .cr-desc { font-size:0.82rem; color:var(--text-muted); line-height:1.65; margin:0; }
.armely-snowflake-page .cr-cta { text-align:center; }
.armely-snowflake-page .cr-btn { display:inline-flex; align-items:center; gap:10px; background:#1a2e52; color:#fff; border-radius:8px; padding:12px 28px; text-decoration:none; font-size:0.875rem; font-weight:600; }
.armely-snowflake-page .cr-btn:hover { background:var(--blue); }
.armely-snowflake-page .cr-btn .icon-svg { width:18px; height:18px; }
@media (max-width:900px) { .armely-snowflake-page .cr-grid { grid-template-columns:1fr 1fr; } }
@media (max-width:600px) { .armely-snowflake-page .cr-grid { grid-template-columns:1fr; } }
</style>
<div class="armely-snowflake-page">
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-inner">
    <div class="hero-copy">
      <div class="hero-eyebrow"><span class="eyebrow-badge">Snowflake AI Data Cloud</span><span class="eyebrow-partner">Delivered by a certified Microsoft partner</span></div>
      <h1>Your data, your cloud,<br>no limits on scale.</h1>
      <p class="hero-sub">Armely architects, implements, and manages Snowflake environments that give your business a fast, governed, AI-ready data platform without the infrastructure headaches.</p>
      <div class="hero-actions">
        <a href="#contact" class="btn-primary">Book a Free Assessment</a>
        <a href="#delivers" class="btn-outline">See What We Do</a>
      </div>
    </div>
  </div>
</section>

<section class="spectrum"><div class="section-inner"><div class="section-eyebrow">What is Snowflake?</div><h2 class="section-title">The cloud data platform built for analytics, AI, and scale.</h2><p class="section-body">Snowflake is the AI Data Cloud, a fully managed platform that separates compute from storage so you can scale each independently, query structured and unstructured data in the same place, and run AI workloads directly on your data using Cortex AI.</p>
<div class="spectrum-grid"><div><div class="spectrum-row">
<div class="spectrum-level highlight"><span class="spectrum-num">01</span><div><div class="spectrum-content-title">Separate Compute and Storage</div><div class="spectrum-content-desc">Scale compute and storage independently. Pay only for what you use. No infrastructure to manage and no performance tuning required.</div></div></div>
<div class="spectrum-level highlight"><span class="spectrum-num">02</span><div><div class="spectrum-content-title">Cortex AI and LLM Functions</div><div class="spectrum-content-desc">Run sentiment analysis, LLM completions, and natural-language queries directly in SQL. AI runs on your data without moving it to a separate system.</div></div></div>
<div class="spectrum-level highlight"><span class="spectrum-num">03</span><div><div class="spectrum-content-title">Data Sharing and Marketplace</div><div class="spectrum-content-desc">Share live data with partners, suppliers, or customers without copying or moving it. Zero-copy sharing means collaborators see the same data you do.</div></div></div>
<div class="spectrum-level"><span class="spectrum-num">04</span><div><div class="spectrum-content-title">Multi-Cloud and Horizon Governance</div><div class="spectrum-content-desc">Runs on AWS, Azure, and Google Cloud with a consistent engine. Horizon governance covers access controls, data masking, and lineage tracking.</div></div></div>
</div></div><div><div class="platform-card"><div class="platform-header"><div class="platform-dots"><span></span><span></span><span></span></div><span class="platform-header-title">Snowflake Architecture</span></div><div class="platform-body"><div class="plat-band band-tools"><div class="plat-band-label">Cortex AI Layer</div><div class="plat-chips"><span class="plat-chip">Cortex Agents</span><span class="plat-chip">Snowflake Intelligence</span><span class="plat-chip">LLM Functions in SQL</span><span class="plat-chip">Cortex Code</span></div></div><div class="band-arrow">&#8597;</div><div class="plat-band band-data"><div class="plat-band-label">Compute and Services</div><div class="plat-chips"><span class="plat-chip">Virtual Warehouses</span><span class="plat-chip">Snowpark</span><span class="plat-chip">Dynamic Tables</span><span class="plat-chip">Horizon Governance</span><span class="plat-chip">Data Sharing</span><span class="plat-chip">Marketplace</span></div></div><div class="band-arrow">&#8597;</div><div class="plat-band band-gov"><div class="plat-band-label">Storage - Decoupled from Compute</div><div class="plat-chips"><span class="plat-chip">Columnar Micro-Partitions</span><span class="plat-chip">AWS / Azure / GCP</span><span class="plat-chip">Apache Iceberg</span></div></div></div></div></div></div></div></section>
<section class="delivers" id="delivers"><div class="section-inner"><div class="section-eyebrow">What Armely Delivers</div><h2 class="section-title">End-to-end Snowflake implementation, from first query to production.</h2><p class="section-body">Armely handles every layer of your Snowflake environment, architecture, ingestion, transformation, analytics, and AI.</p>
<div class="delivers-grid"><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg></div><div class="deliver-title">Architecture and Environment Setup</div><div class="deliver-desc">We design your Snowflake account structure, virtual warehouse sizing, role hierarchy, and network policies before writing a single query.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22v-5"/><path d="M9 8V2"/><path d="M15 8V2"/><path d="M18 8v5a4 4 0 0 1-4 4h-4a4 4 0 0 1-4-4V8Z"/></svg></div><div class="deliver-title">Data Ingestion and Pipelines</div><div class="deliver-desc">We connect your source systems into Snowflake using Snowpipe, Fivetran, dbt, or custom pipelines. Fresh data, on schedule, automatically.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/></svg></div><div class="deliver-title">Data Modeling and Transformation</div><div class="deliver-desc">We build clean, governed data models using dbt or Snowpark so every dashboard and report draws from a consistent, trusted source.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg></div><div class="deliver-title">Analytics and BI Dashboards</div><div class="deliver-desc">We connect your BI tool of choice to Snowflake and build the dashboards your business needs. Fast, accurate, and always live.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 8V4H8"/><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 11v2"/><path d="M15 11v2"/></svg></div><div class="deliver-title">Cortex AI Implementation</div><div class="deliver-desc">We configure Snowflake Cortex so your analysts can run sentiment analysis, LLM completions, and natural-language queries directly in SQL.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div><div class="deliver-title">Governance and Ongoing Management</div><div class="deliver-desc">Horizon governance, dynamic data masking, row-level access policies, and cost monitoring configured from the start.</div></div></div></div></section>
<section class="journey"><div class="section-inner"><div class="section-eyebrow">The Armely Snowflake Journey</div><h2 class="section-title">From legacy data stack to cloud-native analytics on a clear timeline.</h2><p class="section-body">Whether you are migrating from an on-premises warehouse or starting fresh, our methodology gets you to production fast and right.</p>
<div class="steps-row"><div class="step"><div class="step-num">01</div><div class="step-title">Discovery and Assessment</div><div class="step-desc">We audit your current data stack, sources, and analytics needs. Free for new clients, results in a clear migration or build plan.</div><span class="step-tag">Free</span></div><div class="step"><div class="step-num">02</div><div class="step-title">Architecture and Licensing</div><div class="step-desc">We design your Snowflake environment and source the right capacity at partner pricing.</div><span class="step-tag">1-2 Weeks</span></div><div class="step"><div class="step-num">03</div><div class="step-title">Build and Migrate</div><div class="step-desc">Pipelines, data models, and initial dashboards built and validated against your real data.</div><span class="step-tag">Weeks 3-6</span></div><div class="step"><div class="step-num">04</div><div class="step-title">Handover and Training</div><div class="step-desc">Full documentation, runbooks, and role-specific training so your team owns the environment.</div><span class="step-tag">Weeks 7-8</span></div><div class="step"><div class="step-num">05</div><div class="step-title">Managed Support</div><div class="step-desc">Cost optimization, performance tuning, new workload onboarding, and a single Armely contact as your environment grows.</div><span class="step-tag">Ongoing</span></div></div></div></section>
<section class="usecases"><div class="section-inner"><div class="section-eyebrow">Common Engagements</div><h2 class="section-title">Snowflake in practice, across every industry.</h2><p class="section-body">From consolidating a fragmented data stack to running AI models on live business data.</p>
<div class="uc-grid"><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg></span><div class="uc-title">Data Warehouse Modernization</div><div class="uc-desc">Migrate off on-premises SQL Server, Oracle, or Teradata to a fully managed cloud warehouse that scales automatically.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg></span><div class="uc-title">Multi-Cloud Data Consolidation</div><div class="uc-desc">Pull data from AWS, Azure, and GCP into a single governed platform. Snowflake runs across all three clouds.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 8V4H8"/><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 11v2"/><path d="M15 11v2"/></svg></span><div class="uc-title">AI and Machine Learning</div><div class="uc-desc">Use Snowpark to train ML models on your Snowflake data without moving it. Deploy Cortex AI functions in SQL.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span><div class="uc-title">Secure Data Sharing</div><div class="uc-desc">Share live data with partners or customers without copying or moving it. Zero-copy sharing in real time.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M13 2 3 14h9l-1 8 11-14h-9l1-6Z"/></svg></span><div class="uc-title">Real-Time Analytics</div><div class="uc-desc">Snowpipe Streaming and Dynamic Tables ingest and transform data in near real time.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/></svg></span><div class="uc-title">Natural Language Queries</div><div class="uc-desc">Snowflake Intelligence lets anyone ask questions in plain English and get answers from your live data.</div></div></div></div></section>
<section class="testimonials">
  <div class="section-inner">
    <div class="section-eyebrow">Client Results</div>
    <h2 class="section-title">Real outcomes for real organizations.</h2>
    <p class="section-body">Armely has delivered Microsoft platform and AI solutions for healthcare providers, school districts, energy operators, professional services firms, government agencies, and non-profit organizations. See the full story on our Customer Stories page.</p>
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
<section class="why"><div class="section-inner"><div class="section-eyebrow">Why Armely</div><h2 class="section-title">Snowflake expertise delivered at the pace your business needs.</h2><p class="section-body">Armely has built data platforms for healthcare, education, and enterprise clients.</p>
<div class="why-two-col"><div><ul class="why-list"><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg></div><div><div class="why-item-title">Certified Snowflake Implementors</div><div class="why-item-desc">Our team carries Snowflake implementation certifications and hands-on experience across data engineering, Snowpark, Cortex AI, and dbt.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div><div><div class="why-item-title">Proven in Regulated Industries</div><div class="why-item-desc">We have delivered data projects for Swope Health Systems and UNMC, environments with strict HIPAA and data governance requirements.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div><div><div class="why-item-title">Cost Optimization From Day One</div><div class="why-item-desc">Snowflake bills by compute consumption. We right-size warehouses, implement auto-suspend, and monitor query efficiency.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div><div><div class="why-item-title">You Own Everything</div><div class="why-item-desc">Full documentation, source-controlled pipelines, and team training from day one.</div></div></li></ul></div>
<div><div class="partner-block"><div class="partner-block-top"><div class="partner-label">Snowflake Authorized Partner</div><p class="partner-text">Armely's Snowflake partner status gives us access to technical resources, licensing options, and implementation support that direct customers cannot reach.</p></div><div class="partner-stats"><div class="p-stat"><div class="p-stat-num">10<span>K+</span></div><div class="p-stat-label">organizations running Snowflake globally</div></div><div class="p-stat"><div class="p-stat-num">3<span></span></div><div class="p-stat-label">major clouds, AWS, Azure, and Google Cloud</div></div><div class="p-stat"><div class="p-stat-num">50<span>%</span></div><div class="p-stat-label">of Snowflake customers now use Cortex Code for development</div></div><div class="p-stat"><div class="p-stat-num">0<span></span></div><div class="p-stat-label">infrastructure to manage, fully serverless SaaS</div></div></div></div></div></div></div></section>
<section class="cta-section" id="contact"><div class="cta-inner"><div><div class="section-eyebrow">Get Started</div><h2 class="section-title">Let's talk about your data stack.</h2><p class="section-body">Book a free 30-minute discovery call. We will review your current environment and come back with a clear Snowflake implementation proposal.</p><div style="margin-top:20px;display:flex;flex-direction:column;gap:9px;"><div class="trust-item"><span class="trust-dot"></span><span class="trust-text">Free assessment, no commitment required</span></div><div class="trust-item"><span class="trust-dot"></span><span class="trust-text">Recommendation and partner pricing included</span></div><div class="trust-item"><span class="trust-dot"></span><span class="trust-text">Response within one business day</span></div></div></div><div class="cta-form"><div class="form-title">Book Your Free Assessment</div><div class="form-sub">Tell us about your situation.</div><div class="form-row"><label>Full Name</label><input type="text" placeholder="Jane Smith"></div><div class="form-row"><label>Business Email</label><input type="email" placeholder="jane@yourcompany.com"></div><div class="form-row"><label>Company Name</label><input type="text" placeholder="Acme Corp"></div><div class="form-row"><label>Primary Need</label><select><option value="">Select...</option><option>On-premises SQL Server or Oracle</option><option>Azure Synapse or Microsoft Fabric</option><option>AWS Redshift</option><option>Google BigQuery</option><option>Scattered across spreadsheets and tools</option><option>No formal data platform yet</option><option>Other or multiple</option></select></div><button class="form-submit">Request Free Discovery Call</button><div class="form-note">No spam. No sales pressure. Just a useful conversation.</div></div></div></section>
</div>
<style>

.armely-ai-data-strategy-page *, .armely-ai-data-strategy-page *::before, .armely-ai-data-strategy-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-ai-data-strategy-page {
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

.armely-ai-data-strategy-page { scroll-behavior: smooth; }
.armely-ai-data-strategy-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* NAV */
.armely-ai-data-strategy-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-ai-data-strategy-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-ai-data-strategy-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-ai-data-strategy-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-ai-data-strategy-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-ai-data-strategy-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-ai-data-strategy-page .nav-links a:hover { color: #fff; }
.armely-ai-data-strategy-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-ai-data-strategy-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* HERO */
.armely-ai-data-strategy-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-ai-data-strategy-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-ai-data-strategy-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-ai-data-strategy-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-ai-data-strategy-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-ai-data-strategy-page .hero h1 { font-size: clamp(2.6rem, 5.5vw, 4.8rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-ai-data-strategy-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-ai-data-strategy-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.8; }
.armely-ai-data-strategy-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-ai-data-strategy-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-ai-data-strategy-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-ai-data-strategy-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-ai-data-strategy-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-ai-data-strategy-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-ai-data-strategy-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-ai-data-strategy-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-ai-data-strategy-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-ai-data-strategy-page .trust-text strong { color: #fff; font-weight: 600; }

  /* SECTIONS */
.armely-ai-data-strategy-page section { padding: 96px 56px; }
.armely-ai-data-strategy-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-ai-data-strategy-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-ai-data-strategy-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 640px; }
.armely-ai-data-strategy-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* AI SPECTRUM */
.armely-ai-data-strategy-page .spectrum { background: var(--navy-mid); }
.armely-ai-data-strategy-page .spectrum-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-ai-data-strategy-page .spectrum-row { display: flex; flex-direction: column; gap: 10px; margin-top: 32px; }
.armely-ai-data-strategy-page .spectrum-level { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; transition: border-color 0.2s; }
.armely-ai-data-strategy-page .spectrum-level:hover { border-color: rgba(41,78,139,0.3); }
.armely-ai-data-strategy-page .spectrum-level.highlight { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-ai-data-strategy-page .spectrum-num { font-size: 0.68rem; font-weight: 800; color: var(--blue); background: var(--blue-dim2); border-radius: 4px; padding: 2px 7px; flex-shrink: 0; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.08em; }
.armely-ai-data-strategy-page .spectrum-content-title { font-size: 0.875rem; font-weight: 700; color: #1A2540; margin-bottom: 3px; }
.armely-ai-data-strategy-page .spectrum-content-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }

  /* Platform visual */
.armely-ai-data-strategy-page .platform-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-ai-data-strategy-page .platform-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-ai-data-strategy-page .platform-dots { display: flex; gap: 6px; }
.armely-ai-data-strategy-page .platform-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-ai-data-strategy-page .platform-header-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-ai-data-strategy-page .platform-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.armely-ai-data-strategy-page .plat-band { border-radius: 9px; padding: 13px 16px; }
.armely-ai-data-strategy-page .plat-band-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
.armely-ai-data-strategy-page .plat-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.armely-ai-data-strategy-page .plat-chip { font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.armely-ai-data-strategy-page .band-tools { background: var(--blue-dim); }
.armely-ai-data-strategy-page .band-tools .plat-band-label { color: var(--blue); }
.armely-ai-data-strategy-page .band-tools .plat-chip { background: var(--blue-dim2); color: var(--blue); }
.armely-ai-data-strategy-page .band-data { background: rgba(41,78,139,0.05); }
.armely-ai-data-strategy-page .band-data .plat-band-label { color: var(--blue); }
.armely-ai-data-strategy-page .band-data .plat-chip { background: rgba(41,78,139,0.1); color: var(--blue); }
.armely-ai-data-strategy-page .band-gov { background: var(--blue); }
.armely-ai-data-strategy-page .band-gov .plat-band-label { color: rgba(255,255,255,0.7); }
.armely-ai-data-strategy-page .band-gov .plat-chip { background: rgba(255,255,255,0.15); color: #fff; }
.armely-ai-data-strategy-page .band-arrow { text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2px 0; }

  /* VIBE CODING CALLOUT */
.armely-ai-data-strategy-page .vibe-section { background: var(--navy); }
.armely-ai-data-strategy-page .vibe-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: start; }
.armely-ai-data-strategy-page .vibe-left { }
.armely-ai-data-strategy-page .vibe-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-ai-data-strategy-page .vibe-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
.armely-ai-data-strategy-page .vibe-card-icon { font-size: 1.4rem; }
.armely-ai-data-strategy-page .vibe-card-title { font-size: 1rem; font-weight: 700; color: #1A2540; }
.armely-ai-data-strategy-page .vibe-card-subtitle { font-size: 0.78rem; color: var(--text-muted); }
.armely-ai-data-strategy-page .vibe-card-body { padding: 24px; }
.armely-ai-data-strategy-page .vibe-risk { display: flex; align-items: flex-start; gap: 12px; padding: 14px 0; border-bottom: 1px solid var(--border); }
.armely-ai-data-strategy-page .vibe-risk:last-child { border-bottom: none; }
.armely-ai-data-strategy-page .vibe-risk-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
.armely-ai-data-strategy-page .vibe-risk-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 2px; }
.armely-ai-data-strategy-page .vibe-risk-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }
.armely-ai-data-strategy-page .vibe-right { }
.armely-ai-data-strategy-page .vibe-answer-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 28px; margin-bottom: 12px; }
.armely-ai-data-strategy-page .vibe-answer-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--blue); margin-bottom: 10px; }
.armely-ai-data-strategy-page .vibe-answer-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.75; }

  /* DELIVERS */
.armely-ai-data-strategy-page .delivers { background: var(--navy-mid); }
.armely-ai-data-strategy-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-ai-data-strategy-page .deliver-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-ai-data-strategy-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-ai-data-strategy-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-ai-data-strategy-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-ai-data-strategy-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-ai-data-strategy-page .journey { background: var(--navy); }
.armely-ai-data-strategy-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-ai-data-strategy-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-ai-data-strategy-page .step:last-child { border-right: none; }
.armely-ai-data-strategy-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-ai-data-strategy-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-ai-data-strategy-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-ai-data-strategy-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* USE CASES */
.armely-ai-data-strategy-page .usecases { background: var(--navy-mid); }
.armely-ai-data-strategy-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-ai-data-strategy-page .uc-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-ai-data-strategy-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-ai-data-strategy-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-ai-data-strategy-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-ai-data-strategy-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* TESTIMONIALS */
.armely-ai-data-strategy-page .testimonials { background: var(--navy); padding: 96px 56px; }
.armely-ai-data-strategy-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-ai-data-strategy-page .testi-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-ai-data-strategy-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-ai-data-strategy-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-ai-data-strategy-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-ai-data-strategy-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-ai-data-strategy-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-ai-data-strategy-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-ai-data-strategy-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY ARMELY */
.armely-ai-data-strategy-page .why { background: var(--navy-mid); }
.armely-ai-data-strategy-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-ai-data-strategy-page .why-list { list-style: none; margin-top: 36px; }
.armely-ai-data-strategy-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-ai-data-strategy-page .why-list li:last-child { border-bottom: none; }
.armely-ai-data-strategy-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-ai-data-strategy-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-ai-data-strategy-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-ai-data-strategy-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-ai-data-strategy-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-ai-data-strategy-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-ai-data-strategy-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-ai-data-strategy-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-ai-data-strategy-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-ai-data-strategy-page .p-stat:nth-child(2) { border-right: none; }
.armely-ai-data-strategy-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-ai-data-strategy-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-ai-data-strategy-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-ai-data-strategy-page .p-stat-num span { color: var(--blue); }
.armely-ai-data-strategy-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* CTA */
.armely-ai-data-strategy-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-ai-data-strategy-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-ai-data-strategy-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-ai-data-strategy-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-ai-data-strategy-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-ai-data-strategy-page .form-row { margin-bottom: 14px; }
.armely-ai-data-strategy-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-ai-data-strategy-page .form-row input, .armely-ai-data-strategy-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-ai-data-strategy-page .form-row input:focus, .armely-ai-data-strategy-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-ai-data-strategy-page .form-row select option { background: #fff; color: #1A2540; }
.armely-ai-data-strategy-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-ai-data-strategy-page .form-submit:hover { background: var(--blue-lt); }
.armely-ai-data-strategy-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* FOOTER */
.armely-ai-data-strategy-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-ai-data-strategy-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-ai-data-strategy-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-ai-data-strategy-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-ai-data-strategy-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-ai-data-strategy-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-ai-data-strategy-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* RESPONSIVE */
  @media (max-width: 900px) {
.armely-ai-data-strategy-page nav { padding: 16px 24px; }
.armely-ai-data-strategy-page .nav-links { display: none; }
.armely-ai-data-strategy-page section { padding: 72px 24px; }
.armely-ai-data-strategy-page .hero { padding: 110px 24px 72px; }
.armely-ai-data-strategy-page .spectrum-grid, .armely-ai-data-strategy-page .vibe-two-col, .armely-ai-data-strategy-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-ai-data-strategy-page .delivers-grid, .armely-ai-data-strategy-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-ai-data-strategy-page .steps-row { grid-template-columns: 1fr; }
.armely-ai-data-strategy-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-ai-data-strategy-page .step:last-child { border-bottom: none; }
.armely-ai-data-strategy-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-ai-data-strategy-page .testimonials { padding: 72px 24px; }
.armely-ai-data-strategy-page .testi-grid { grid-template-columns: 1fr; }
.armely-ai-data-strategy-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-ai-data-strategy-page .delivers-grid, .armely-ai-data-strategy-page .uc-grid { grid-template-columns: 1fr; }
.armely-ai-data-strategy-page .partner-stats { grid-template-columns: 1fr; }
.armely-ai-data-strategy-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-ai-data-strategy-page {
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
.armely-ai-data-strategy-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-ai-data-strategy-page .hero::after {
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
.armely-ai-data-strategy-page .section-title,
.armely-ai-data-strategy-page .deliver-title,
.armely-ai-data-strategy-page .uc-title,
.armely-ai-data-strategy-page .step-title,
.armely-ai-data-strategy-page .why-item-title,
.armely-ai-data-strategy-page .form-title {
  color: #162b49;
}
.armely-ai-data-strategy-page .deliver-card,
.armely-ai-data-strategy-page .uc-card,
.armely-ai-data-strategy-page .testi-card,
.armely-ai-data-strategy-page .platform-card,
.armely-ai-data-strategy-page .partner-block,
.armely-ai-data-strategy-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-ai-data-strategy-page .deliver-card:hover,
.armely-ai-data-strategy-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-ai-data-strategy-page .btn-primary,
.armely-ai-data-strategy-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-ai-data-strategy-page .btn-primary:hover,
.armely-ai-data-strategy-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-ai-data-strategy-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-ai-data-strategy-page nav,
.armely-ai-data-strategy-page footer {
  display: none;
}


/* Modern layout update: tighter first section and SVG icon system */
.armely-ai-data-strategy-page .hero {
  min-height: auto;
  padding: 128px 32px 86px;
  isolation: isolate;
}
.armely-ai-data-strategy-page .hero-inner {
  width: min(1160px, 100%);
  margin: 0 auto;
  display: grid;
  grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.75fr);
  align-items: center;
  gap: 56px;
  position: relative;
  z-index: 1;
}
.armely-ai-data-strategy-page .hero-copy { max-width: 760px; }
.armely-ai-data-strategy-page .hero h1 { max-width: 760px; margin-bottom: 20px; }
.armely-ai-data-strategy-page .hero-sub { max-width: 640px; margin-bottom: 30px; font-size: 1.02rem; line-height: 1.72; }
.armely-ai-data-strategy-page .hero-actions { margin-bottom: 34px; }
.armely-ai-data-strategy-page .hero-trust {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  padding-top: 0;
  border-top: 0;
  max-width: 720px;
}
.armely-ai-data-strategy-page .hero .trust-item {
  align-items: flex-start;
  background: rgba(255,255,255,0.10);
  border: 1px solid rgba(255,255,255,0.16);
  border-radius: 14px;
  padding: 14px 15px;
  backdrop-filter: blur(10px);
}
.armely-ai-data-strategy-page .hero .trust-dot {
  width: 22px;
  height: 22px;
  border-radius: 999px;
  background: rgba(255,255,255,0.16);
  border: 1px solid rgba(255,255,255,0.24);
  position: relative;
  margin-top: 1px;
}
.armely-ai-data-strategy-page .hero .trust-dot::after {
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
.armely-ai-data-strategy-page .hero .trust-text { color: rgba(255,255,255,0.78); line-height: 1.5; }
.armely-ai-data-strategy-page .hero-visual {
  min-height: 420px;
  border-radius: 28px;
  position: relative;
  background: linear-gradient(145deg, rgba(255,255,255,0.16), rgba(255,255,255,0.05));
  border: 1px solid rgba(255,255,255,0.18);
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.18), 0 28px 70px rgba(6, 22, 48, 0.24);
  overflow: hidden;
}
.armely-ai-data-strategy-page .hero-visual::before {
  content: '';
  position: absolute;
  inset: 36px;
  border: 1px solid rgba(255,255,255,0.14);
  border-radius: 999px;
}
.armely-ai-data-strategy-page .hero-visual::after {
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
.armely-ai-data-strategy-page .hero-orbit span {
  position: absolute;
  width: 52px;
  height: 52px;
  border-radius: 16px;
  background: rgba(255,255,255,0.14);
  border: 1px solid rgba(255,255,255,0.22);
}
.armely-ai-data-strategy-page .hero-orbit span:nth-child(1) { left: 56px; top: 88px; }
.armely-ai-data-strategy-page .hero-orbit span:nth-child(2) { right: 72px; top: 138px; }
.armely-ai-data-strategy-page .hero-orbit span:nth-child(3) { left: 48%; bottom: 76px; }
.armely-ai-data-strategy-page .hero-visual-card {
  position: absolute;
  z-index: 2;
  width: 230px;
  padding: 18px;
  border-radius: 18px;
  background: rgba(255,255,255,0.92);
  box-shadow: 0 24px 54px rgba(9, 31, 63, 0.22);
}
.armely-ai-data-strategy-page .hero-visual-card.top { top: 42px; right: 30px; }
.armely-ai-data-strategy-page .hero-visual-card.bottom { bottom: 46px; left: 30px; }
.armely-ai-data-strategy-page .hero-visual-label {
  display: block;
  font-size: 0.78rem;
  font-weight: 700;
  color: #162b49;
  margin-bottom: 12px;
}
.armely-ai-data-strategy-page .hero-visual-line {
  display: block;
  height: 8px;
  width: 100%;
  border-radius: 999px;
  background: rgba(47, 85, 151, 0.16);
  margin-top: 8px;
}
.armely-ai-data-strategy-page .hero-visual-line.short { width: 68%; }
.armely-ai-data-strategy-page .icon-svg {
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
.armely-ai-data-strategy-page .vibe-card-icon,
.armely-ai-data-strategy-page .vibe-risk-icon,
.armely-ai-data-strategy-page .deliver-icon,
.armely-ai-data-strategy-page .uc-icon,
.armely-ai-data-strategy-page .why-icon {
  color: var(--blue);
}
.armely-ai-data-strategy-page .vibe-card-icon,
.armely-ai-data-strategy-page .vibe-risk-icon {
  width: 42px;
  height: 42px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-ai-data-strategy-page .vibe-risk-icon { width: 36px; height: 36px; border-radius: 10px; }
.armely-ai-data-strategy-page .deliver-icon .icon-svg,
.armely-ai-data-strategy-page .uc-icon .icon-svg,
.armely-ai-data-strategy-page .why-icon .icon-svg { width: 23px; height: 23px; }
.armely-ai-data-strategy-page .uc-icon {
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
  .armely-ai-data-strategy-page .hero-inner { grid-template-columns: 1fr; gap: 32px; }
  .armely-ai-data-strategy-page .hero-visual { min-height: 320px; }
}
@media (max-width: 680px) {
  .armely-ai-data-strategy-page .hero { padding: 104px 22px 64px; }
  .armely-ai-data-strategy-page .hero-trust { grid-template-columns: 1fr; }
  .armely-ai-data-strategy-page .hero-visual { display: none; }
  .armely-ai-data-strategy-page .hero-actions a { width: 100%; text-align: center; }
}

/* Focused cleanup for the Generative AI detail page */
.armely-ai-data-strategy-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  align-items: center;
  background: #173b67;
  border-radius: 0;
}
.armely-ai-data-strategy-page .hero::after,
.armely-ai-data-strategy-page .hero-bg-glow,
.armely-ai-data-strategy-page .hero-visual {
  display: none;
}
.armely-ai-data-strategy-page .hero-inner {
  width: min(1040px, 100%);
  display: block;
}
.armely-ai-data-strategy-page .hero-copy {
  max-width: 760px;
}
.armely-ai-data-strategy-page .hero-eyebrow {
  margin-bottom: 18px;
}
.armely-ai-data-strategy-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.72);
}
.armely-ai-data-strategy-page .eyebrow-partner,
.armely-ai-data-strategy-page .hero-trust {
  display: none;
}
.armely-ai-data-strategy-page .hero h1 {
  max-width: 760px;
  margin-bottom: 22px;
}
.armely-ai-data-strategy-page .hero-sub {
  max-width: 680px;
  margin-bottom: 34px;
}
.armely-ai-data-strategy-page .hero-actions {
  margin-bottom: 0;
}
.armely-ai-data-strategy-page .hero .btn-primary,
.armely-ai-data-strategy-page .hero .btn-outline {
  border-radius: 0;
}
.armely-ai-data-strategy-page .vibe-section {
  background: #fff;
  padding: 84px 56px;
}
.armely-ai-data-strategy-page .vibe-section .section-inner {
  max-width: 920px;
}
.armely-ai-data-strategy-page .vibe-section .section-title,
.armely-ai-data-strategy-page .vibe-section .section-body {
  max-width: 820px;
}
.armely-ai-data-strategy-page .vibe-section .section-body {
  margin-bottom: 28px;
}
.armely-ai-data-strategy-page .vibe-two-col {
  grid-template-columns: 1fr;
  gap: 18px;
}
.armely-ai-data-strategy-page .vibe-card,
.armely-ai-data-strategy-page .vibe-answer-card {
  border-radius: 0;
  box-shadow: none;
}
.armely-ai-data-strategy-page .vibe-card-header {
  padding: 18px 20px;
}
.armely-ai-data-strategy-page .vibe-card-body {
  padding: 8px 20px 12px;
}
.armely-ai-data-strategy-page .vibe-risk {
  padding: 12px 0;
}
.armely-ai-data-strategy-page .vibe-right {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
}
.armely-ai-data-strategy-page .vibe-answer-card {
  margin-bottom: 0;
  padding: 20px;
  background: #f7f9fc;
}
.armely-ai-data-strategy-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-ai-data-strategy-page .why > .section-inner > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-ai-data-strategy-page section:not(.hero) > .section-inner > .section-title,
.armely-ai-data-strategy-page .why > .section-inner > .section-title {
  max-width: 880px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-ai-data-strategy-page section:not(.hero) > .section-inner > .section-body,
.armely-ai-data-strategy-page .why > .section-inner > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-ai-data-strategy-page .spectrum-grid,
.armely-ai-data-strategy-page .delivers-grid,
.armely-ai-data-strategy-page .steps-row,
.armely-ai-data-strategy-page .uc-grid,
.armely-ai-data-strategy-page .testi-grid,
.armely-ai-data-strategy-page .why-two-col {
  margin-top: 56px;
}
.armely-ai-data-strategy-page .why-two-col {
  align-items: stretch;
}
.armely-ai-data-strategy-page .why-list {
  margin-top: 0;
}
.armely-ai-data-strategy-page .why-list,
.armely-ai-data-strategy-page .partner-block {
  height: 100%;
}
@media (max-width: 900px) {
  .armely-ai-data-strategy-page .hero {
    padding: 118px 24px 76px;
  }
  .armely-ai-data-strategy-page .vibe-section {
    padding: 72px 24px;
  }
  .armely-ai-data-strategy-page .vibe-right {
    grid-template-columns: 1fr;
  }
}



/* Final compact modern cleanup */
.armely-ai-data-strategy-page {
  --blue: #2f5597;
  --blue-lt: #4779bd;
  --navy-mid: #f6f8fc;
  --navy-card: #ffffff;
  --text-body: #334155;
  --text-muted: #667085;
  --border: rgba(47, 85, 151, 0.14);
}
.armely-ai-data-strategy-page .hero {
  min-height: auto !important;
  padding: 86px 56px 70px !important;
  background: linear-gradient(135deg, #173b67 0%, #234f86 100%) !important;
  border-radius: 0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}
.armely-ai-data-strategy-page .hero::after,
.armely-ai-data-strategy-page .hero-bg-glow,
.armely-ai-data-strategy-page .hero-visual {
  display: none !important;
}
.armely-ai-data-strategy-page .hero-inner {
  width: min(1120px, 100%) !important;
  margin: 0 auto !important;
  display: block !important;
}
.armely-ai-data-strategy-page .hero-copy {
  max-width: 860px !important;
}
.armely-ai-data-strategy-page .hero-eyebrow {
  margin-bottom: 18px !important;
}
.armely-ai-data-strategy-page .eyebrow-badge {
  display: inline-flex !important;
  background: rgba(255,255,255,0.10) !important;
  border: 1px solid rgba(255,255,255,0.22) !important;
  border-radius: 999px !important;
  padding: 7px 14px !important;
  color: rgba(255,255,255,0.88) !important;
}
.armely-ai-data-strategy-page .eyebrow-partner {
  display: inline-flex !important;
  color: rgba(255,255,255,0.66) !important;
}
.armely-ai-data-strategy-page .hero h1 {
  max-width: 900px !important;
  margin-bottom: 18px !important;
  font-size: clamp(2.5rem, 5vw, 4.9rem) !important;
  line-height: 1.05 !important;
  letter-spacing: -0.04em !important;
}
.armely-ai-data-strategy-page .hero-sub {
  max-width: 760px !important;
  margin-bottom: 28px !important;
  font-size: 1rem !important;
  line-height: 1.7 !important;
}
.armely-ai-data-strategy-page .hero-actions {
  margin-bottom: 0 !important;
  gap: 12px !important;
}
.armely-ai-data-strategy-page .hero .btn-primary,
.armely-ai-data-strategy-page .hero .btn-outline,
.armely-ai-data-strategy-page .btn-primary,
.armely-ai-data-strategy-page .btn-outline,
.armely-ai-data-strategy-page .form-submit {
  border-radius: 8px !important;
}
.armely-ai-data-strategy-page section {
  padding: 68px 56px !important;
}
.armely-ai-data-strategy-page .section-inner {
  max-width: 1120px !important;
}
.armely-ai-data-strategy-page .section-eyebrow {
  margin-bottom: 10px !important;
}
.armely-ai-data-strategy-page .section-title {
  margin-bottom: 14px !important;
}
.armely-ai-data-strategy-page .section-body {
  margin-bottom: 28px !important;
  line-height: 1.65 !important;
}
.armely-ai-data-strategy-page .spectrum-grid,
.armely-ai-data-strategy-page .vibe-two-col,
.armely-ai-data-strategy-page .delivers-grid,
.armely-ai-data-strategy-page .steps-row,
.armely-ai-data-strategy-page .uc-grid,
.armely-ai-data-strategy-page .testi-grid,
.armely-ai-data-strategy-page .why-two-col {
  margin-top: 34px !important;
  gap: 20px !important;
}
.armely-ai-data-strategy-page .spectrum-grid,
.armely-ai-data-strategy-page .why-two-col {
  grid-template-columns: 1fr 1fr !important;
}
.armely-ai-data-strategy-page .spectrum-row {
  margin-top: 0 !important;
  gap: 10px !important;
}
.armely-ai-data-strategy-page .spectrum-level,
.armely-ai-data-strategy-page .deliver-card,
.armely-ai-data-strategy-page .uc-card,
.armely-ai-data-strategy-page .testi-card,
.armely-ai-data-strategy-page .vibe-answer-card,
.armely-ai-data-strategy-page .partner-block,
.armely-ai-data-strategy-page .cta-form,
.armely-ai-data-strategy-page .vibe-card,
.armely-ai-data-strategy-page .platform-card {
  border-radius: 14px !important;
  box-shadow: 0 14px 36px rgba(18, 47, 82, 0.08) !important;
}
.armely-ai-data-strategy-page .deliver-card,
.armely-ai-data-strategy-page .uc-card,
.armely-ai-data-strategy-page .testi-card {
  padding: 24px 22px !important;
}
.armely-ai-data-strategy-page .deliver-icon,
.armely-ai-data-strategy-page .uc-icon,
.armely-ai-data-strategy-page .why-icon,
.armely-ai-data-strategy-page .vibe-card-icon,
.armely-ai-data-strategy-page .vibe-risk-icon {
  border-radius: 12px !important;
  color: var(--blue) !important;
}
.armely-ai-data-strategy-page .vibe-section {
  padding: 68px 56px !important;
  background: #fff !important;
}
.armely-ai-data-strategy-page .vibe-section .section-inner {
  max-width: 1120px !important;
}
.armely-ai-data-strategy-page .vibe-two-col {
  grid-template-columns: 1fr 1fr !important;
  align-items: stretch !important;
}
.armely-ai-data-strategy-page .vibe-card-body {
  padding: 18px 20px !important;
}
.armely-ai-data-strategy-page .vibe-right {
  display: flex !important;
  flex-direction: column !important;
  gap: 12px !important;
}
.armely-ai-data-strategy-page .vibe-answer-card {
  padding: 20px !important;
  background: #f8fafd !important;
}
.armely-ai-data-strategy-page .steps-row {
  border-radius: 14px !important;
  overflow: hidden !important;
}
.armely-ai-data-strategy-page .step {
  padding: 24px 18px !important;
}
.armely-ai-data-strategy-page .step-num {
  font-size: 2rem !important;
  margin-bottom: 10px !important;
}
.armely-ai-data-strategy-page .why-list {
  margin-top: 0 !important;
}
.armely-ai-data-strategy-page .why-list li {
  padding: 16px 0 !important;
}
.armely-ai-data-strategy-page .partner-block-top,
.armely-ai-data-strategy-page .p-stat {
  padding: 22px !important;
}
.armely-ai-data-strategy-page .cta-inner {
  padding: 68px 56px !important;
  gap: 40px !important;
}
@media (max-width: 900px) {
  .armely-ai-data-strategy-page .hero { padding: 88px 24px 58px !important; }
  .armely-ai-data-strategy-page section,
  .armely-ai-data-strategy-page .vibe-section { padding: 56px 24px !important; }
  .armely-ai-data-strategy-page .spectrum-grid,
  .armely-ai-data-strategy-page .vibe-two-col,
  .armely-ai-data-strategy-page .why-two-col { grid-template-columns: 1fr !important; }
  .armely-ai-data-strategy-page .delivers-grid,
  .armely-ai-data-strategy-page .uc-grid { grid-template-columns: 1fr 1fr !important; }
  .armely-ai-data-strategy-page .cta-inner { padding: 56px 24px !important; grid-template-columns: 1fr !important; }
}
@media (max-width: 600px) {
  .armely-ai-data-strategy-page .hero h1 { font-size: clamp(2.15rem, 11vw, 3.2rem) !important; }
  .armely-ai-data-strategy-page .hero-eyebrow { flex-direction: column !important; align-items: flex-start !important; gap: 8px !important; }
  .armely-ai-data-strategy-page .hero-actions a { width: 100% !important; text-align: center !important; }
  .armely-ai-data-strategy-page .delivers-grid,
  .armely-ai-data-strategy-page .uc-grid { grid-template-columns: 1fr !important; }
}


.armely-ai-data-strategy-page .cr-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:28px; margin-bottom:28px; }
.armely-ai-data-strategy-page .cr-card { background:#fff; border:1px solid var(--border); border-radius:12px; padding:20px 22px; box-shadow:0 2px 10px rgba(18,47,82,0.04); }
.armely-ai-data-strategy-page .cr-label { display:flex; align-items:center; gap:9px; margin-bottom:10px; }
.armely-ai-data-strategy-page .cr-check { width:20px; height:20px; display:flex; align-items:center; justify-content:center; background:var(--blue-dim); border:1px solid var(--blue-dim2); border-radius:50%; flex-shrink:0; color:var(--blue); }
.armely-ai-data-strategy-page .cr-check .icon-svg { width:11px; height:11px; stroke-width:3; }
.armely-ai-data-strategy-page .cr-industry { font-size:0.875rem; font-weight:700; color:#1A2540; }
.armely-ai-data-strategy-page .cr-desc { font-size:0.82rem; color:var(--text-muted); line-height:1.65; margin:0; }
.armely-ai-data-strategy-page .cr-cta { text-align:center; }
.armely-ai-data-strategy-page .cr-btn { display:inline-flex; align-items:center; gap:10px; background:#1a2e52; color:#fff; border-radius:8px; padding:12px 28px; text-decoration:none; font-size:0.875rem; font-weight:600; }
.armely-ai-data-strategy-page .cr-btn:hover { background:var(--blue); }
.armely-ai-data-strategy-page .cr-btn .icon-svg { width:18px; height:18px; }
@media (max-width:900px) { .armely-ai-data-strategy-page .cr-grid { grid-template-columns:1fr 1fr; } }
@media (max-width:600px) { .armely-ai-data-strategy-page .cr-grid { grid-template-columns:1fr; } }
</style>
<div class="armely-ai-data-strategy-page">
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-inner">
    <div class="hero-copy">
      <div class="hero-eyebrow"><span class="eyebrow-badge">AI and Data Strategy Advisory</span><span class="eyebrow-partner">Delivered by a certified Microsoft partner</span></div>
      <h1>Not sure where to start<br>with AI or your data?<br>Start here.</h1>
      <p class="hero-sub">Armely's advisory practice helps organizations cut through the noise, assess what they have, and build a clear roadmap before committing to any technology investment.</p>
      <div class="hero-actions">
        <a href="#contact" class="btn-primary">Book a Free Assessment</a>
        <a href="#delivers" class="btn-outline">See What We Do</a>
      </div>
    </div>
  </div>
</section>

<section class="spectrum"><div class="section-inner"><div class="section-eyebrow">Is This You?</div><h2 class="section-title">The conversations that lead organizations to Armely advisory.</h2><p class="section-body">Most organizations that engage Armely for advisory are not short of enthusiasm for AI or data. They are short of clarity on what to do first, what the realistic outcomes are, and how to make a defensible investment decision.</p>
<div class="spectrum-grid"><div><div class="spectrum-row">
<div class="spectrum-level highlight"><span class="spectrum-num">AI</span><div><div class="spectrum-content-title">Everyone is talking about AI but no one agrees on where to start</div><div class="spectrum-content-desc">Multiple departments have different AI requests, multiple vendors are pitching different platforms, and leadership wants a strategy before approving any spend.</div></div></div>
<div class="spectrum-level highlight"><span class="spectrum-num">Data</span><div><div class="spectrum-content-title">Your data is everywhere and you know it is costing you</div><div class="spectrum-content-desc">Decisions are made from spreadsheets, reports take days to produce, and no one is confident the numbers are right.</div></div></div>
<div class="spectrum-level highlight"><span class="spectrum-num">Review</span><div><div class="spectrum-content-title">A previous technology investment did not deliver what was promised</div><div class="spectrum-content-desc">You want an independent assessment before committing again, and you need someone who has actually implemented these platforms rather than just sold them.</div></div></div>
<div class="spectrum-level"><span class="spectrum-num">Roadmap</span><div><div class="spectrum-content-title">You have a roadmap but no confidence it is the right one</div><div class="spectrum-content-desc">A vendor or internal team has produced a plan. You want an experienced second opinion before the board approves the budget.</div></div></div>
</div></div><div><div class="platform-card"><div class="platform-header"><div class="platform-dots"><span></span><span></span><span></span></div><span class="platform-header-title">What an Advisory Engagement Produces</span></div><div class="platform-body"><div class="plat-band band-tools"><div class="plat-band-label">Current State Assessment</div><div class="plat-chips"><span class="plat-chip">Data Source Inventory</span><span class="plat-chip">AI Readiness Score</span><span class="plat-chip">Governance Posture Review</span><span class="plat-chip">Security Controls Review</span><span class="plat-chip">Platform Audit</span></div></div><div class="band-arrow">&#8597;</div><div class="plat-band band-data"><div class="plat-band-label">Use Case Prioritization</div><div class="plat-chips"><span class="plat-chip">Business Value Scoring</span><span class="plat-chip">Data Readiness Assessment</span><span class="plat-chip">Complexity Ranking</span><span class="plat-chip">Ranked Roadmap</span><span class="plat-chip">Quick Win Identification</span></div></div><div class="band-arrow">&#8597;</div><div class="plat-band band-gov"><div class="plat-band-label">Deliverable</div><div class="plat-chips"><span class="plat-chip">Written Strategy Document</span><span class="plat-chip">Technology Recommendations</span><span class="plat-chip">Phased Roadmap</span><span class="plat-chip">Business Case for Leadership</span><span class="plat-chip">Presentation Deck</span></div></div></div></div></div></div></div></section>
<section class="delivers" id="delivers"><div class="section-inner"><div class="section-eyebrow">Advisory Services</div><h2 class="section-title">Six advisory engagements Armely leads for organizations.</h2><p class="section-body">Each engagement is scoped, time-bounded, and produces a written deliverable. We do not run open-ended advisory retainers that produce slide decks without outcomes.</p>
<div class="delivers-grid"><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg></div><div class="deliver-title">AI Readiness Assessment</div><div class="deliver-desc">A structured evaluation of your organization's readiness to adopt AI, covering data quality, governance posture, security controls, workforce capability, and existing Microsoft platform configuration.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg></div><div class="deliver-title">Data Strategy and Architecture Review</div><div class="deliver-desc">An assessment of your current data landscape with a recommended target architecture for analytics, reporting, and AI workloads. Platform recommendations are specific to your situation.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z"/><path d="M9 3v15"/><path d="M15 6v15"/></svg></div><div class="deliver-title">Technology Roadmap Development</div><div class="deliver-desc">A phased, prioritized roadmap covering AI, data, and digital platform investments over a 12 to 36 month horizon, structured for presentation to leadership and boards.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div><div class="deliver-title">Platform Selection Advisory</div><div class="deliver-desc">Independent guidance on technology selection for a specific problem. We present honest trade-offs rather than defaulting to the platform with the largest implementation engagement.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div><div class="deliver-title">AI Governance Framework Design</div><div class="deliver-desc">A governance framework for AI adoption covering acceptable use policies, data access controls, agent management standards, audit requirements, and escalation procedures.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><line x1="7" x2="17" y1="12" y2="12"/></svg></div><div class="deliver-title">Investment Validation Review</div><div class="deliver-desc">An independent review of a proposed or existing technology investment, assessing whether the platform, architecture, and implementation approach are appropriate for the stated business outcomes.</div></div></div></div></section>
<section class="journey"><div class="section-inner"><div class="section-eyebrow">How an Advisory Engagement Works</div><h2 class="section-title">Structured, time-bounded, and documented. Not open-ended.</h2><p class="section-body">A typical Armely advisory engagement runs four to six weeks and produces a written deliverable your organization owns.</p>
<div class="steps-row"><div class="step"><div class="step-num">01</div><div class="step-title">Scoping Call</div><div class="step-desc">We understand your situation, agree on the specific advisory deliverable, identify stakeholders, and set a timeline. No charge for this conversation.</div><span class="step-tag">Free</span></div><div class="step"><div class="step-num">02</div><div class="step-title">Discovery and Interviews</div><div class="step-desc">Structured interviews with leadership, IT, and operational stakeholders. Review of relevant documentation, existing systems, and any prior technology assessments.</div><span class="step-tag">Weeks 1-2</span></div><div class="step"><div class="step-num">03</div><div class="step-title">Analysis and Drafting</div><div class="step-desc">We synthesize findings, develop recommendations, and draft the strategy document. A working session reviews the draft before finalization.</div><span class="step-tag">Weeks 3-4</span></div><div class="step"><div class="step-num">04</div><div class="step-title">Presentation and Handover</div><div class="step-desc">Final document and presentation deck delivered. We present to your leadership team and answer questions. You own all deliverables outright.</div><span class="step-tag">Weeks 5-6</span></div><div class="step"><div class="step-num">05</div><div class="step-title">Implementation</div><div class="step-desc">Most advisory engagements lead to implementation. Armely can deliver on the roadmap recommendations across Fabric, Snowflake, Azure AI, and the Microsoft platform.</div><span class="step-tag">Ongoing</span></div></div></div></section>
<section class="usecases"><div class="section-inner"><div class="section-eyebrow">Where Advisory Leads</div><h2 class="section-title">The roadmap tells you what to build. Armely can build it.</h2><p class="section-body">Most Armely advisory engagements lead to implementation work.</p>
<div class="uc-grid"><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg></span><div class="uc-title">Data Platform Implementation</div><div class="uc-desc">Microsoft Fabric, Snowflake, or Azure Synapse implemented on the architecture designed during the advisory engagement.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 8V4H8"/><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 11v2"/><path d="M15 11v2"/></svg></span><div class="uc-title">AI Agent and Application Build</div><div class="uc-desc">Generative AI and agentic solutions built on the Microsoft AI stack, governed by the framework developed in the advisory phase.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg></span><div class="uc-title">Platform Configuration and Integration</div><div class="uc-desc">Dynamics 365, Power Platform, SharePoint, and Microsoft 365 implementations recommended in the roadmap delivered by certified Armely engineers.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></span><div class="uc-title">Governance Implementation</div><div class="uc-desc">The AI governance framework and data governance policies designed in the advisory engagement implemented technically across your Microsoft 365 tenant and Azure environment.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg></span><div class="uc-title">Analytics and Reporting</div><div class="uc-desc">Power BI dashboards and reporting environments built against the data architecture recommended in the strategy document.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg></span><div class="uc-title">Microsoft Licensing Optimization</div><div class="uc-desc">Licensing recommendations from the advisory engagement sourced at Microsoft partner pricing through Armely's authorized CSP partnership.</div></div></div></div></section>
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
<section class="why"><div class="section-inner"><div class="section-eyebrow">Why Armely for Advisory</div><h2 class="section-title">Strategy advice is only credible when it comes from people who implement.</h2><p class="section-body">The limitation of purely advisory firms is that they recommend platforms they have never delivered. Armely's advisory is credible because every recommendation is made by engineers and architects who have built on the platforms they are recommending.</p>
<div class="why-two-col"><div><ul class="why-list"><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></div><div><div class="why-item-title">Advisors Who Also Implement</div><div class="why-item-desc">Every Armely advisory engagement is led by engineers and architects with hands-on delivery experience across Microsoft Fabric, Snowflake, Azure AI Foundry, and Dynamics 365.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg></div><div><div class="why-item-title">No Predetermined Outcome</div><div class="why-item-desc">We are not paid by platform vendors to recommend their products. Our advisory is only commercially valuable to us if it leads to the right implementation.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg></div><div><div class="why-item-title">Written Deliverables You Own</div><div class="why-item-desc">Every advisory engagement produces a written document your organization owns outright. Strategy documents, assessment reports, and roadmaps that you can present internally or share with your board without restriction.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div><div><div class="why-item-title">Regulated Industry Experience</div><div class="why-item-desc">Our advisory work in healthcare, education, and professional services means we understand the compliance and governance constraints that shape technology decisions in regulated industries.</div></div></li></ul></div>
<div><div class="partner-block"><div class="partner-block-top"><div class="partner-label">Microsoft Authorized Partner</div><p class="partner-text">Armely's Microsoft partnership gives our advisory practice access to Microsoft technical briefings, early product roadmap information, and architectural guidance that helps us give clients accurate, current guidance on the Microsoft platform.</p></div><div class="partner-stats"><div class="p-stat"><div class="p-stat-num">4-6<span>wks</span></div><div class="p-stat-label">for a complete AI and data strategy advisory engagement</div></div><div class="p-stat"><div class="p-stat-num">100<span>%</span></div><div class="p-stat-label">of advisory deliverables owned outright by the client</div></div><div class="p-stat"><div class="p-stat-num">0<span></span></div><div class="p-stat-label">platform vendor referral fees received by Armely for any recommendation</div></div><div class="p-stat"><div class="p-stat-num">3<span></span></div><div class="p-stat-label">industries with deep advisory experience: healthcare, education, and professional services</div></div></div></div></div></div></div></section>
<section class="cta-section" id="contact"><div class="cta-inner"><div><div class="section-eyebrow">Get Started</div><h2 class="section-title">Tell us where you are. We will help you figure out where to go.</h2><p class="section-body">Book a free 30-minute strategy call. We will listen to your situation and tell you honestly whether advisory or implementation is the right first step.</p><div style="margin-top:20px;display:flex;flex-direction:column;gap:9px;"><div class="trust-item"><span class="trust-dot"></span><span class="trust-text">Free assessment, no commitment required</span></div><div class="trust-item"><span class="trust-dot"></span><span class="trust-text">Recommendation and partner pricing included</span></div><div class="trust-item"><span class="trust-dot"></span><span class="trust-text">Response within one business day</span></div></div></div><div class="cta-form"><div class="form-title">Book Your Free Assessment</div><div class="form-sub">Tell us about your situation.</div><div class="form-row"><label>Full Name</label><input type="text" placeholder="Jane Smith"></div><div class="form-row"><label>Business Email</label><input type="email" placeholder="jane@yourcompany.com"></div><div class="form-row"><label>Company Name</label><input type="text" placeholder="Acme Corp"></div><div class="form-row"><label>Primary Need</label><select><option value="">Select...</option><option>We need an AI strategy before committing budget</option><option>We need a data strategy before any analytics investment</option><option>We need help choosing between platforms or vendors</option><option>A previous investment did not deliver and we need a review</option><option>We need an AI governance framework</option><option>Not sure yet, need a conversation first</option></select></div><button class="form-submit">Request Free Strategy Call</button><div class="form-note">No spam. No sales pressure. Just a useful conversation.</div></div></div></section>
</div>
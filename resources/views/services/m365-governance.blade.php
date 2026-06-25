<style>


.armely-m365-governance-page *, .armely-m365-governance-page *::before, .armely-m365-governance-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-m365-governance-page {
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

.armely-m365-governance-page { scroll-behavior: smooth; }
.armely-m365-governance-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* NAV */
.armely-m365-governance-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-m365-governance-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-m365-governance-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-m365-governance-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-m365-governance-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-m365-governance-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-m365-governance-page .nav-links a:hover { color: #fff; }
.armely-m365-governance-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-m365-governance-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* HERO */
.armely-m365-governance-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-m365-governance-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-m365-governance-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-m365-governance-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-m365-governance-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-m365-governance-page .hero h1 { font-size: clamp(1.75rem, 3.2vw, 2.7rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-m365-governance-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-m365-governance-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.8; }
.armely-m365-governance-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-m365-governance-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-m365-governance-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-m365-governance-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-m365-governance-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-m365-governance-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-m365-governance-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-m365-governance-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-m365-governance-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-m365-governance-page .trust-text strong { color: #fff; font-weight: 600; }

  /* SECTIONS */
.armely-m365-governance-page section { padding: 96px 56px; }
.armely-m365-governance-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-m365-governance-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-m365-governance-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 640px; }
.armely-m365-governance-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* AI SPECTRUM */
.armely-m365-governance-page .spectrum { background: var(--navy-mid); }
.armely-m365-governance-page .spectrum-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-m365-governance-page .spectrum-row { display: flex; flex-direction: column; gap: 10px; margin-top: 32px; }
.armely-m365-governance-page .spectrum-level { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; transition: border-color 0.2s; }
.armely-m365-governance-page .spectrum-level:hover { border-color: rgba(41,78,139,0.3); }
.armely-m365-governance-page .spectrum-level.highlight { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-m365-governance-page .spectrum-num { font-size: 0.68rem; font-weight: 800; color: var(--blue); background: var(--blue-dim2); border-radius: 4px; padding: 2px 7px; flex-shrink: 0; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.08em; }
.armely-m365-governance-page .spectrum-content-title { font-size: 0.875rem; font-weight: 700; color: #1A2540; margin-bottom: 3px; }
.armely-m365-governance-page .spectrum-content-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }

  /* Platform visual */
.armely-m365-governance-page .platform-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-m365-governance-page .platform-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-m365-governance-page .platform-dots { display: flex; gap: 6px; }
.armely-m365-governance-page .platform-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-m365-governance-page .platform-header-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-m365-governance-page .platform-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.armely-m365-governance-page .plat-band { border-radius: 9px; padding: 13px 16px; }
.armely-m365-governance-page .plat-band-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
.armely-m365-governance-page .plat-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.armely-m365-governance-page .plat-chip { font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.armely-m365-governance-page .band-tools { background: var(--blue-dim); }
.armely-m365-governance-page .band-tools .plat-band-label { color: var(--blue); }
.armely-m365-governance-page .band-tools .plat-chip { background: var(--blue-dim2); color: var(--blue); }
.armely-m365-governance-page .band-data { background: rgba(41,78,139,0.05); }
.armely-m365-governance-page .band-data .plat-band-label { color: var(--blue); }
.armely-m365-governance-page .band-data .plat-chip { background: rgba(41,78,139,0.1); color: var(--blue); }
.armely-m365-governance-page .band-gov { background: var(--blue); }
.armely-m365-governance-page .band-gov .plat-band-label { color: rgba(255,255,255,0.7); }
.armely-m365-governance-page .band-gov .plat-chip { background: rgba(255,255,255,0.15); color: #fff; }
.armely-m365-governance-page .band-arrow { text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2px 0; }

  /* VIBE CODING CALLOUT */
.armely-m365-governance-page .vibe-section { background: var(--navy); }
.armely-m365-governance-page .vibe-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: start; }
.armely-m365-governance-page .vibe-left { }
.armely-m365-governance-page .vibe-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-m365-governance-page .vibe-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
.armely-m365-governance-page .vibe-card-icon { font-size: 1.4rem; }
.armely-m365-governance-page .vibe-card-title { font-size: 1rem; font-weight: 700; color: #1A2540; }
.armely-m365-governance-page .vibe-card-subtitle { font-size: 0.78rem; color: var(--text-muted); }
.armely-m365-governance-page .vibe-card-body { padding: 24px; }
.armely-m365-governance-page .vibe-risk { display: flex; align-items: flex-start; gap: 12px; padding: 14px 0; border-bottom: 1px solid var(--border); }
.armely-m365-governance-page .vibe-risk:last-child { border-bottom: none; }
.armely-m365-governance-page .vibe-risk-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
.armely-m365-governance-page .vibe-risk-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 2px; }
.armely-m365-governance-page .vibe-risk-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }
.armely-m365-governance-page .vibe-right { }
.armely-m365-governance-page .vibe-answer-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 28px; margin-bottom: 12px; }
.armely-m365-governance-page .vibe-answer-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--blue); margin-bottom: 10px; }
.armely-m365-governance-page .vibe-answer-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.75; }

  /* DELIVERS */
.armely-m365-governance-page .delivers { background: var(--navy-mid); }
.armely-m365-governance-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-m365-governance-page .deliver-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-m365-governance-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-m365-governance-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-m365-governance-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-m365-governance-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-m365-governance-page .journey { background: var(--navy); }
.armely-m365-governance-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-m365-governance-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-m365-governance-page .step:last-child { border-right: none; }
.armely-m365-governance-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-m365-governance-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-m365-governance-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-m365-governance-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* USE CASES */
.armely-m365-governance-page .usecases { background: var(--navy-mid); }
.armely-m365-governance-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-m365-governance-page .uc-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-m365-governance-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-m365-governance-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-m365-governance-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-m365-governance-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* TESTIMONIALS */
.armely-m365-governance-page .testimonials { background: var(--navy); padding: 96px 56px; }
.armely-m365-governance-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-m365-governance-page .testi-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-m365-governance-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-m365-governance-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-m365-governance-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-m365-governance-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-m365-governance-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-m365-governance-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-m365-governance-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY ARMELY */
.armely-m365-governance-page .why { background: var(--navy-mid); }
.armely-m365-governance-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-m365-governance-page .why-list { list-style: none; margin-top: 36px; }
.armely-m365-governance-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-m365-governance-page .why-list li:last-child { border-bottom: none; }
.armely-m365-governance-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-m365-governance-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-m365-governance-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-m365-governance-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-m365-governance-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-m365-governance-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-m365-governance-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-m365-governance-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-m365-governance-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-m365-governance-page .p-stat:nth-child(2) { border-right: none; }
.armely-m365-governance-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-m365-governance-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-m365-governance-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-m365-governance-page .p-stat-num span { color: var(--blue); }
.armely-m365-governance-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* CTA */
.armely-m365-governance-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-m365-governance-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-m365-governance-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-m365-governance-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-m365-governance-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-m365-governance-page .form-row { margin-bottom: 14px; }
.armely-m365-governance-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-m365-governance-page .form-row input, .armely-m365-governance-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-m365-governance-page .form-row input:focus, .armely-m365-governance-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-m365-governance-page .form-row select option { background: #fff; color: #1A2540; }
.armely-m365-governance-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-m365-governance-page .form-submit:hover { background: var(--blue-lt); }
.armely-m365-governance-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* FOOTER */
.armely-m365-governance-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-m365-governance-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-m365-governance-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-m365-governance-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-m365-governance-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-m365-governance-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-m365-governance-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* RESPONSIVE */
  @media (max-width: 900px) {
.armely-m365-governance-page nav { padding: 16px 24px; }
.armely-m365-governance-page .nav-links { display: none; }
.armely-m365-governance-page section { padding: 72px 24px; }
.armely-m365-governance-page .hero { padding: 110px 24px 72px; }
.armely-m365-governance-page .spectrum-grid, .armely-m365-governance-page .vibe-two-col, .armely-m365-governance-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-m365-governance-page .delivers-grid, .armely-m365-governance-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-m365-governance-page .steps-row { grid-template-columns: 1fr; }
.armely-m365-governance-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-m365-governance-page .step:last-child { border-bottom: none; }
.armely-m365-governance-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-m365-governance-page .testimonials { padding: 72px 24px; }
.armely-m365-governance-page .testi-grid { grid-template-columns: 1fr; }
.armely-m365-governance-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-m365-governance-page .delivers-grid, .armely-m365-governance-page .uc-grid { grid-template-columns: 1fr; }
.armely-m365-governance-page .partner-stats { grid-template-columns: 1fr; }
.armely-m365-governance-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-m365-governance-page {
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
.armely-m365-governance-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-m365-governance-page .hero::after {
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
.armely-m365-governance-page .section-title,
.armely-m365-governance-page .deliver-title,
.armely-m365-governance-page .uc-title,
.armely-m365-governance-page .step-title,
.armely-m365-governance-page .why-item-title,
.armely-m365-governance-page .form-title {
  color: #162b49;
}
.armely-m365-governance-page .deliver-card,
.armely-m365-governance-page .uc-card,
.armely-m365-governance-page .testi-card,
.armely-m365-governance-page .platform-card,
.armely-m365-governance-page .partner-block,
.armely-m365-governance-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-m365-governance-page .deliver-card:hover,
.armely-m365-governance-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-m365-governance-page .btn-primary,
.armely-m365-governance-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-m365-governance-page .btn-primary:hover,
.armely-m365-governance-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-m365-governance-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-m365-governance-page nav,
.armely-m365-governance-page footer {
  display: none;
}


/* Modern layout update: tighter first section and SVG icon system */
.armely-m365-governance-page .hero {
  min-height: auto;
  padding: 128px 32px 86px;
  isolation: isolate;
}
.armely-m365-governance-page .hero-inner {
  width: min(1160px, 100%);
  margin: 0 auto;
  display: grid;
  grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.75fr);
  align-items: center;
  gap: 56px;
  position: relative;
  z-index: 1;
}
.armely-m365-governance-page .hero-copy { max-width: 760px; }
.armely-m365-governance-page .hero h1 { max-width: 760px; margin-bottom: 20px; }
.armely-m365-governance-page .hero-sub { max-width: 640px; margin-bottom: 30px; font-size: 1.02rem; line-height: 1.72; }
.armely-m365-governance-page .hero-actions { margin-bottom: 34px; }
.armely-m365-governance-page .hero-trust {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  padding-top: 0;
  border-top: 0;
  max-width: 720px;
}
.armely-m365-governance-page .hero .trust-item {
  align-items: flex-start;
  background: rgba(255,255,255,0.10);
  border: 1px solid rgba(255,255,255,0.16);
  border-radius: 14px;
  padding: 14px 15px;
  backdrop-filter: blur(10px);
}
.armely-m365-governance-page .hero .trust-dot {
  width: 22px;
  height: 22px;
  border-radius: 999px;
  background: rgba(255,255,255,0.16);
  border: 1px solid rgba(255,255,255,0.24);
  position: relative;
  margin-top: 1px;
}
.armely-m365-governance-page .hero .trust-dot::after {
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
.armely-m365-governance-page .hero .trust-text { color: rgba(255,255,255,0.78); line-height: 1.5; }
.armely-m365-governance-page .hero-visual {
  min-height: 420px;
  border-radius: 28px;
  position: relative;
  background: linear-gradient(145deg, rgba(255,255,255,0.16), rgba(255,255,255,0.05));
  border: 1px solid rgba(255,255,255,0.18);
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.18), 0 28px 70px rgba(6, 22, 48, 0.24);
  overflow: hidden;
}
.armely-m365-governance-page .hero-visual::before {
  content: '';
  position: absolute;
  inset: 36px;
  border: 1px solid rgba(255,255,255,0.14);
  border-radius: 999px;
}
.armely-m365-governance-page .hero-visual::after {
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
.armely-m365-governance-page .hero-orbit span {
  position: absolute;
  width: 52px;
  height: 52px;
  border-radius: 16px;
  background: rgba(255,255,255,0.14);
  border: 1px solid rgba(255,255,255,0.22);
}
.armely-m365-governance-page .hero-orbit span:nth-child(1) { left: 56px; top: 88px; }
.armely-m365-governance-page .hero-orbit span:nth-child(2) { right: 72px; top: 138px; }
.armely-m365-governance-page .hero-orbit span:nth-child(3) { left: 48%; bottom: 76px; }
.armely-m365-governance-page .hero-visual-card {
  position: absolute;
  z-index: 2;
  width: 230px;
  padding: 18px;
  border-radius: 18px;
  background: rgba(255,255,255,0.92);
  box-shadow: 0 24px 54px rgba(9, 31, 63, 0.22);
}
.armely-m365-governance-page .hero-visual-card.top { top: 42px; right: 30px; }
.armely-m365-governance-page .hero-visual-card.bottom { bottom: 46px; left: 30px; }
.armely-m365-governance-page .hero-visual-label {
  display: block;
  font-size: 0.78rem;
  font-weight: 700;
  color: #162b49;
  margin-bottom: 12px;
}
.armely-m365-governance-page .hero-visual-line {
  display: block;
  height: 8px;
  width: 100%;
  border-radius: 999px;
  background: rgba(47, 85, 151, 0.16);
  margin-top: 8px;
}
.armely-m365-governance-page .hero-visual-line.short { width: 68%; }
.armely-m365-governance-page .icon-svg {
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
.armely-m365-governance-page .vibe-card-icon,
.armely-m365-governance-page .vibe-risk-icon,
.armely-m365-governance-page .deliver-icon,
.armely-m365-governance-page .uc-icon,
.armely-m365-governance-page .why-icon {
  color: var(--blue);
}
.armely-m365-governance-page .vibe-card-icon,
.armely-m365-governance-page .vibe-risk-icon {
  width: 42px;
  height: 42px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-m365-governance-page .vibe-risk-icon { width: 36px; height: 36px; border-radius: 10px; }
.armely-m365-governance-page .deliver-icon .icon-svg,
.armely-m365-governance-page .uc-icon .icon-svg,
.armely-m365-governance-page .why-icon .icon-svg { width: 23px; height: 23px; }
.armely-m365-governance-page .uc-icon {
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
  .armely-m365-governance-page .hero-inner { grid-template-columns: 1fr; gap: 32px; }
  .armely-m365-governance-page .hero-visual { min-height: 320px; }
}
@media (max-width: 680px) {
  .armely-m365-governance-page .hero { padding: 104px 22px 64px; }
  .armely-m365-governance-page .hero-trust { grid-template-columns: 1fr; }
  .armely-m365-governance-page .hero-visual { display: none; }
  .armely-m365-governance-page .hero-actions a { width: 100%; text-align: center; }
}

/* Focused cleanup for the Generative AI detail page */
.armely-m365-governance-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  align-items: center;
  background: #173b67;
  border-radius: 0;
}
.armely-m365-governance-page .hero::after,
.armely-m365-governance-page .hero-bg-glow,
.armely-m365-governance-page .hero-visual {
  display: none;
}
.armely-m365-governance-page .hero-inner {
  width: min(1040px, 100%);
  display: block;
}
.armely-m365-governance-page .hero-copy {
  max-width: 760px;
}
.armely-m365-governance-page .hero-eyebrow {
  margin-bottom: 18px;
}
.armely-m365-governance-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.72);
}
.armely-m365-governance-page .eyebrow-partner,
.armely-m365-governance-page .hero-trust {
  display: none;
}
.armely-m365-governance-page .hero h1 {
  max-width: 760px;
  margin-bottom: 22px;
}
.armely-m365-governance-page .hero-sub {
  max-width: 680px;
  margin-bottom: 34px;
}
.armely-m365-governance-page .hero-actions {
  margin-bottom: 0;
}
.armely-m365-governance-page .hero .btn-primary,
.armely-m365-governance-page .hero .btn-outline {
  border-radius: 0;
}
.armely-m365-governance-page .vibe-section {
  background: #fff;
  padding: 84px 56px;
}
.armely-m365-governance-page .vibe-section .section-inner {
  max-width: 920px;
}
.armely-m365-governance-page .vibe-section .section-title,
.armely-m365-governance-page .vibe-section .section-body {
  max-width: 820px;
}
.armely-m365-governance-page .vibe-section .section-body {
  margin-bottom: 28px;
}
.armely-m365-governance-page .vibe-two-col {
  grid-template-columns: 1fr;
  gap: 18px;
}
.armely-m365-governance-page .vibe-card,
.armely-m365-governance-page .vibe-answer-card {
  border-radius: 0;
  box-shadow: none;
}
.armely-m365-governance-page .vibe-card-header {
  padding: 18px 20px;
}
.armely-m365-governance-page .vibe-card-body {
  padding: 8px 20px 12px;
}
.armely-m365-governance-page .vibe-risk {
  padding: 12px 0;
}
.armely-m365-governance-page .vibe-right {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
}
.armely-m365-governance-page .vibe-answer-card {
  margin-bottom: 0;
  padding: 20px;
  background: #f7f9fc;
}
.armely-m365-governance-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-m365-governance-page .why > .section-inner > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-m365-governance-page section:not(.hero) > .section-inner > .section-title,
.armely-m365-governance-page .why > .section-inner > .section-title {
  max-width: 880px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-m365-governance-page section:not(.hero) > .section-inner > .section-body,
.armely-m365-governance-page .why > .section-inner > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-m365-governance-page .spectrum-grid,
.armely-m365-governance-page .delivers-grid,
.armely-m365-governance-page .steps-row,
.armely-m365-governance-page .uc-grid,
.armely-m365-governance-page .testi-grid,
.armely-m365-governance-page .why-two-col {
  margin-top: 56px;
}
.armely-m365-governance-page .why-two-col {
  align-items: stretch;
}
.armely-m365-governance-page .why-list {
  margin-top: 0;
}
.armely-m365-governance-page .why-list,
.armely-m365-governance-page .partner-block {
  height: 100%;
}
@media (max-width: 900px) {
  .armely-m365-governance-page .hero {
    padding: 118px 24px 76px;
  }
  .armely-m365-governance-page .vibe-section {
    padding: 72px 24px;
  }
  .armely-m365-governance-page .vibe-right {
    grid-template-columns: 1fr;
  }
}



/* Final compact modern cleanup */
.armely-m365-governance-page {
  --blue: #2f5597;
  --blue-lt: #4779bd;
  --navy-mid: #f6f8fc;
  --navy-card: #ffffff;
  --text-body: #334155;
  --text-muted: #667085;
  --border: rgba(47, 85, 151, 0.14);
}
.armely-m365-governance-page .hero {
  min-height: auto !important;
  padding: 86px 56px 70px !important;
  background: linear-gradient(135deg, #173b67 0%, #234f86 100%) !important;
  border-radius: 0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}
.armely-m365-governance-page .hero::after,
.armely-m365-governance-page .hero-bg-glow,
.armely-m365-governance-page .hero-visual {
  display: none !important;
}
.armely-m365-governance-page .hero-inner {
  width: min(1120px, 100%) !important;
  margin: 0 auto !important;
  display: block !important;
}
.armely-m365-governance-page .hero-copy {
  max-width: 860px !important;
}
.armely-m365-governance-page .hero-eyebrow {
  margin-bottom: 18px !important;
}
.armely-m365-governance-page .eyebrow-badge {
  display: inline-flex !important;
  background: rgba(255,255,255,0.10) !important;
  border: 1px solid rgba(255,255,255,0.22) !important;
  border-radius: 999px !important;
  padding: 7px 14px !important;
  color: rgba(255,255,255,0.88) !important;
}
.armely-m365-governance-page .eyebrow-partner {
  display: inline-flex !important;
  color: rgba(255,255,255,0.66) !important;
}
.armely-m365-governance-page .hero h1 {
  max-width: 900px !important;
  margin-bottom: 18px !important;
  font-size: clamp(1.75rem, 3.2vw, 2.7rem);
  line-height: 1.05 !important;
  letter-spacing: -0.04em !important;
}
.armely-m365-governance-page .hero-sub {
  max-width: 760px !important;
  margin-bottom: 28px !important;
  font-size: 1rem !important;
  line-height: 1.7 !important;
}
.armely-m365-governance-page .hero-actions {
  margin-bottom: 0 !important;
  gap: 12px !important;
}
.armely-m365-governance-page .hero .btn-primary,
.armely-m365-governance-page .hero .btn-outline,
.armely-m365-governance-page .btn-primary,
.armely-m365-governance-page .btn-outline,
.armely-m365-governance-page .form-submit {
  border-radius: 8px !important;
}
.armely-m365-governance-page section {
  padding: 68px 56px !important;
}
.armely-m365-governance-page .section-inner {
  max-width: 1120px !important;
}
.armely-m365-governance-page .section-eyebrow {
  margin-bottom: 10px !important;
}
.armely-m365-governance-page .section-title {
  margin-bottom: 14px !important;
}
.armely-m365-governance-page .section-body {
  margin-bottom: 28px !important;
  line-height: 1.65 !important;
}
.armely-m365-governance-page .spectrum-grid,
.armely-m365-governance-page .vibe-two-col,
.armely-m365-governance-page .delivers-grid,
.armely-m365-governance-page .steps-row,
.armely-m365-governance-page .uc-grid,
.armely-m365-governance-page .testi-grid,
.armely-m365-governance-page .why-two-col {
  margin-top: 34px !important;
  gap: 20px !important;
}
.armely-m365-governance-page .spectrum-grid,
.armely-m365-governance-page .why-two-col {
  grid-template-columns: 1fr 1fr !important;
}
.armely-m365-governance-page .spectrum-row {
  margin-top: 0 !important;
  gap: 10px !important;
}
.armely-m365-governance-page .spectrum-level,
.armely-m365-governance-page .deliver-card,
.armely-m365-governance-page .uc-card,
.armely-m365-governance-page .testi-card,
.armely-m365-governance-page .vibe-answer-card,
.armely-m365-governance-page .partner-block,
.armely-m365-governance-page .cta-form,
.armely-m365-governance-page .vibe-card,
.armely-m365-governance-page .platform-card {
  border-radius: 14px !important;
  box-shadow: 0 14px 36px rgba(18, 47, 82, 0.08) !important;
}
.armely-m365-governance-page .deliver-card,
.armely-m365-governance-page .uc-card,
.armely-m365-governance-page .testi-card {
  padding: 24px 22px !important;
}
.armely-m365-governance-page .deliver-icon,
.armely-m365-governance-page .uc-icon,
.armely-m365-governance-page .why-icon,
.armely-m365-governance-page .vibe-card-icon,
.armely-m365-governance-page .vibe-risk-icon {
  border-radius: 12px !important;
  color: var(--blue) !important;
}
.armely-m365-governance-page .vibe-section {
  padding: 68px 56px !important;
  background: #fff !important;
}
.armely-m365-governance-page .vibe-section .section-inner {
  max-width: 1120px !important;
}
.armely-m365-governance-page .vibe-two-col {
  grid-template-columns: 1fr 1fr !important;
  align-items: stretch !important;
}
.armely-m365-governance-page .vibe-card-body {
  padding: 18px 20px !important;
}
.armely-m365-governance-page .vibe-right {
  display: flex !important;
  flex-direction: column !important;
  gap: 12px !important;
}
.armely-m365-governance-page .vibe-answer-card {
  padding: 20px !important;
  background: #f8fafd !important;
}
.armely-m365-governance-page .steps-row {
  border-radius: 14px !important;
  overflow: hidden !important;
}
.armely-m365-governance-page .step {
  padding: 24px 18px !important;
}
.armely-m365-governance-page .step-num {
  font-size: 2rem !important;
  margin-bottom: 10px !important;
}
.armely-m365-governance-page .why-list {
  margin-top: 0 !important;
}
.armely-m365-governance-page .why-list li {
  padding: 16px 0 !important;
}
.armely-m365-governance-page .partner-block-top,
.armely-m365-governance-page .p-stat {
  padding: 22px !important;
}
.armely-m365-governance-page .cta-inner {
  padding: 68px 56px !important;
  gap: 40px !important;
}
@media (max-width: 900px) {
  .armely-m365-governance-page .hero { padding: 88px 24px 58px !important; }
  .armely-m365-governance-page section,
  .armely-m365-governance-page .vibe-section { padding: 56px 24px !important; }
  .armely-m365-governance-page .spectrum-grid,
  .armely-m365-governance-page .vibe-two-col,
  .armely-m365-governance-page .why-two-col { grid-template-columns: 1fr !important; }
  .armely-m365-governance-page .delivers-grid,
  .armely-m365-governance-page .uc-grid { grid-template-columns: 1fr 1fr !important; }
  .armely-m365-governance-page .cta-inner { padding: 56px 24px !important; grid-template-columns: 1fr !important; }
}
@media (max-width: 600px) {
  .armely-m365-governance-page .hero h1 { font-size: clamp(1.75rem, 3.2vw, 2.7rem); }
  .armely-m365-governance-page .hero-eyebrow { flex-direction: column !important; align-items: flex-start !important; gap: 8px !important; }
  .armely-m365-governance-page .hero-actions a { width: 100% !important; text-align: center !important; }
  .armely-m365-governance-page .delivers-grid,
  .armely-m365-governance-page .uc-grid { grid-template-columns: 1fr !important; }
}



.armely-m365-governance-page .cr-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:28px; margin-bottom:28px; }
.armely-m365-governance-page .cr-card { background:#fff; border:1px solid var(--border); border-radius:14px; padding:22px; box-shadow:0 14px 36px rgba(18,47,82,0.08); }
.armely-m365-governance-page .cr-label { display:flex; align-items:center; gap:9px; margin-bottom:10px; }
.armely-m365-governance-page .cr-check { width:20px; height:20px; display:flex; align-items:center; justify-content:center; background:var(--blue-dim); border:1px solid var(--blue-dim2); border-radius:50%; flex-shrink:0; color:var(--blue); }
.armely-m365-governance-page .cr-check .icon-svg { width:11px; height:11px; stroke-width:3; }
.armely-m365-governance-page .cr-industry { font-size:0.875rem; font-weight:700; color:#162b49; }
.armely-m365-governance-page .cr-desc { font-size:0.84rem; color:var(--text-muted); line-height:1.65; margin:0; }
.armely-m365-governance-page .cr-cta { text-align:center; margin-top:8px; }
.armely-m365-governance-page .cr-btn { display:inline-flex; align-items:center; gap:10px; background:#1a2e52; color:#fff; border-radius:8px; padding:13px 28px; text-decoration:none; font-size:0.875rem; font-weight:600; }
.armely-m365-governance-page .cr-btn:hover { background:var(--blue); }
.armely-m365-governance-page .cr-btn .icon-svg { width:18px; height:18px; }
@media (max-width:900px) { .armely-m365-governance-page .cr-grid { grid-template-columns:1fr 1fr; } }
@media (max-width:600px) { .armely-m365-governance-page .cr-grid { grid-template-columns:1fr; } }
</style>
<div class="armely-m365-governance-page">
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-inner">
    <div class="hero-copy">
      <div class="hero-eyebrow">
        <span class="eyebrow-badge">Microsoft 365 Governance and Adoption</span>
        <span class="eyebrow-partner">Delivered by a certified Microsoft partner</span>
      </div>
      <h1>You have Microsoft 365.<br>Is your organization<br>actually using it well?</h1>
      <p class="hero-sub">Armely helps organizations secure, govern, and drive adoption of Microsoft 365 so they get full value from their investment and are ready for Copilot AI.</p>
      <div class="hero-actions">
        <a href="#contact" class="btn-primary">Book a Free Assessment</a>
        <a href="#delivers" class="btn-outline">See What We Do</a>
      </div>
    </div>
  </div>
</section>

<section class="spectrum"><div class="section-inner"><div class="section-eyebrow">Does This Sound Familiar?</div><h2 class="section-title">Signs your Microsoft 365 environment needs attention.</h2><p class="section-body">These are the situations Armely finds in almost every Microsoft 365 tenant that has been running without a governance review.</p>
<div class="spectrum-grid"><div class="spectrum-row">
<div class="spectrum-level highlight"><span class="spectrum-num">Risk</span><div><div class="spectrum-content-title">External sharing is on and no one knows what has been shared</div><div class="spectrum-content-desc">Files, SharePoint sites, and Teams channels shared with external users months or years ago, with no record and no expiry. This is the most common finding in every tenant audit Armely runs.</div></div></div>
<div class="spectrum-level highlight"><span class="spectrum-num">Risk</span><div><div class="spectrum-content-title">Hundreds of Teams and SharePoint sites with no owners</div><div class="spectrum-content-desc">Abandoned workspaces, duplicate sites, and content no one can find, managed by no one. Each one is a security liability and a search quality problem.</div></div></div>
<div class="spectrum-level highlight"><span class="spectrum-num">Risk</span><div><div class="spectrum-content-title">Former employees still have active accounts or access</div><div class="spectrum-content-desc">Offboarding processes that leave licenses running, mailboxes accessible, and permissions intact long after a person has left the organization.</div></div></div>
<div class="spectrum-level"><span class="spectrum-num">AI</span><div><div class="spectrum-content-title">You want to add Copilot but the tenant is not ready</div><div class="spectrum-content-desc">Copilot surfaces data based on existing permissions. If permissions and data classification are not clean, Copilot will surface things it should not. Governance is a prerequisite.</div></div></div>
</div><div><div class="platform-card"><div class="platform-header"><div class="platform-dots"><span></span><span></span><span></span></div><span class="platform-header-title">Governance Framework Areas</span></div><div class="platform-body"><div class="plat-band band-tools"><div class="plat-band-label">Security and Access</div><div class="plat-chips"><span class="plat-chip">Entra ID Conditional Access</span><span class="plat-chip">MFA Enforcement</span><span class="plat-chip">External Sharing Controls</span><span class="plat-chip">Privileged Identity Management</span><span class="plat-chip">Guest Access Policies</span><span class="plat-chip">Defender for M365</span></div></div><div class="band-arrow">&#8597;</div><div class="plat-band band-data"><div class="plat-band-label">Structure and Adoption</div><div class="plat-chips"><span class="plat-chip">Teams Lifecycle Policies</span><span class="plat-chip">SharePoint Site Standards</span><span class="plat-chip">Naming Conventions</span><span class="plat-chip">Owner Accountability</span><span class="plat-chip">User Training</span><span class="plat-chip">Adoption Measurement</span></div></div><div class="band-arrow">&#8597;</div><div class="plat-band band-gov"><div class="plat-band-label">Compliance and Data Protection</div><div class="plat-chips"><span class="plat-chip">Microsoft Purview</span><span class="plat-chip">Sensitivity Labels</span><span class="plat-chip">Retention Policies</span><span class="plat-chip">DLP Policies</span><span class="plat-chip">Audit Logging</span><span class="plat-chip">eDiscovery</span></div></div></div></div></div></div></div></section>
<section class="delivers" id="delivers"><div class="section-inner"><div class="section-eyebrow">What Armely Delivers</div><h2 class="section-title">A governed, secure, well-adopted Microsoft 365 environment.</h2><p class="section-body">Armely's Microsoft 365 Governance and Adoption practice covers the security controls, structural policies, compliance configuration, and user enablement that turn a chaotic tenant into one your organization can rely on.</p>
<div class="delivers-grid"><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div><div class="deliver-title">Tenant Health Assessment</div><div class="deliver-desc">A structured audit of your Microsoft 365 tenant covering security posture, external sharing exposure, guest access, inactive accounts, Teams and SharePoint sprawl, license utilization, and compliance configuration gaps.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div><div class="deliver-title">Security Hardening</div><div class="deliver-desc">Implementation of Microsoft's recommended security baselines including Conditional Access policies, MFA enforcement, Entra ID Privileged Identity Management, external sharing controls, and Defender for Microsoft 365.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div><div class="deliver-title">Governance Framework Implementation</div><div class="deliver-desc">Teams lifecycle policies, SharePoint site provisioning standards, naming conventions, owner accountability processes, and admin center configuration that prevents ungoverned sprawl.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M8 12h8"/><path d="M8 16h6"/></svg></div><div class="deliver-title">Microsoft Purview and Compliance</div><div class="deliver-desc">Sensitivity label taxonomy, data loss prevention policies, retention schedules, and audit logging configured through Microsoft Purview so your content is classified, protected, and auditable.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><div class="deliver-title">Adoption and Training</div><div class="deliver-desc">Role-specific training programs for Microsoft 365, including Teams, SharePoint, OneDrive, and Copilot where relevant. Adoption measurement through Microsoft Viva Insights.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 8V4H8"/><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 11v2"/><path d="M15 11v2"/></svg></div><div class="deliver-title">Copilot Readiness Preparation</div><div class="deliver-desc">Overshared content remediation, sensitivity label coverage, permission cleanup, and data governance configuration that ensures Microsoft 365 Copilot surfaces only what it should.</div></div></div></div></section>
<section class="journey"><div class="section-inner"><div class="section-eyebrow">How a Governance Engagement Works</div><h2 class="section-title">From tenant audit to a clean, governed Microsoft 365 environment.</h2><p class="section-body">Governance work is not glamorous but the impact is immediate and lasting.</p>
<div class="steps-row"><div class="step"><div class="step-num">01</div><div class="step-title">Tenant Health Check</div><div class="step-desc">Free automated and manual review of your Microsoft 365 tenant covering the most critical security, governance, and adoption gaps. Written report delivered within one week.</div><span class="step-tag">Free</span></div><div class="step"><div class="step-num">02</div><div class="step-title">Prioritized Remediation Plan</div><div class="step-desc">We present findings ranked by risk and impact, agree on the remediation scope, and confirm a timeline and fixed fee for the governance implementation.</div><span class="step-tag">Week 1</span></div><div class="step"><div class="step-num">03</div><div class="step-title">Security and Governance Build</div><div class="step-desc">Security controls, governance policies, Purview configuration, and structural standards implemented in your tenant with minimal disruption to day-to-day operations.</div><span class="step-tag">Weeks 2-5</span></div><div class="step"><div class="step-num">04</div><div class="step-title">Training and Adoption</div><div class="step-desc">Staff training on the new structures and tools, administrator training on the governance controls, and adoption measurement established for ongoing tracking.</div><span class="step-tag">Week 6</span></div><div class="step"><div class="step-num">05</div><div class="step-title">Ongoing Governance</div><div class="step-desc">Quarterly governance reviews, new employee onboarding support, policy updates as Microsoft releases new capabilities, and Copilot readiness validation when relevant.</div><span class="step-tag">Ongoing</span></div></div></div></section>
<section class="usecases"><div class="section-inner"><div class="section-eyebrow">Common Situations</div><h2 class="section-title">The governance challenges we resolve most often.</h2><p class="section-body">Every Microsoft 365 tenant is different but the governance problems that accumulate over time follow recognizable patterns.</p>
<div class="uc-grid"><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span><div class="uc-title">Oversharing and External Access Cleanup</div><div class="uc-desc">An audit and remediation of external sharing across SharePoint, OneDrive, and Teams. We identify what has been shared externally, review appropriateness, remove unnecessary access, and implement controls.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg></span><div class="uc-title">Teams and SharePoint Sprawl</div><div class="uc-desc">Organizations with hundreds of Teams and SharePoint sites that have accumulated without structure or ownership. We audit, archive, merge, or delete abandoned workspaces and implement lifecycle management.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></span><div class="uc-title">Security Baseline Implementation</div><div class="uc-desc">Organizations that deployed Microsoft 365 without implementing Microsoft's recommended security controls. We implement Conditional Access, MFA, Entra ID protection, and Defender for Microsoft 365.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M8 12h8"/><path d="M8 16h6"/></svg></span><div class="uc-title">Compliance and Audit Preparation</div><div class="uc-desc">Organizations facing a HIPAA, SOC 2, or other compliance audit that need their Microsoft 365 environment to demonstrate appropriate data governance, retention policies, and access controls.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 17 6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg></span><div class="uc-title">Low Adoption Remediation</div><div class="uc-desc">Organizations paying for Microsoft 365 licenses but finding most staff still email attachments and store files on personal drives. We assess barriers and address structural and training gaps.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 8V4H8"/><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 11v2"/><path d="M15 11v2"/></svg></span><div class="uc-title">Copilot Readiness Assessment</div><div class="uc-desc">For organizations planning to deploy Microsoft 365 Copilot, we assess whether the tenant's permissions, sensitivity labels, and data governance are ready, and remediate the gaps.</div></div></div></div></section>
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
<section class="why"><div class="section-inner"><div class="section-eyebrow">Why Armely</div><h2 class="section-title">Microsoft 365 governance done properly, once.</h2><p class="section-body">Governance work done badly just moves the problem. Armely implements controls that actually hold, trains administrators who understand why the controls are there, and measures adoption.</p>
<div class="why-two-col"><div><ul class="why-list"><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div><div><div class="why-item-title">Free Tenant Health Check to Start</div><div class="why-item-desc">Every engagement starts with a free automated and manual assessment of your tenant. You see exactly what the problems are before committing to any remediation work.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 8V4H8"/><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 11v2"/><path d="M15 11v2"/></svg></div><div><div class="why-item-title">Copilot Readiness Built In</div><div class="why-item-desc">Every governance engagement Armely delivers prepares your tenant for Microsoft 365 Copilot, whether or not you plan to deploy it immediately.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 17 6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg></div><div><div class="why-item-title">Adoption Is Measured, Not Assumed</div><div class="why-item-desc">We configure Microsoft 365 usage analytics and Viva Insights reporting so you can see adoption levels by team and tool over time.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div><div><div class="why-item-title">Regulated Industry Experience</div><div class="why-item-desc">We have implemented Microsoft 365 governance for healthcare providers and educational institutions where HIPAA compliance and audit readiness are not optional.</div></div></li></ul></div>
<div><div class="partner-block"><div class="partner-block-top"><div class="partner-label">Microsoft Authorized Partner</div><p class="partner-text">Armely's Microsoft partnership gives us access to Microsoft Secure Score benchmarking tools, Microsoft 365 admin center partner features, and early access to governance capability updates.</p></div><div class="partner-stats"><div class="p-stat"><div class="p-stat-num">190<span>M</span></div><div class="p-stat-label">people use Microsoft 365, most in environments without formal governance</div></div><div class="p-stat"><div class="p-stat-num">Free<span></span></div><div class="p-stat-label">tenant health check to identify your specific risks before any commitment</div></div><div class="p-stat"><div class="p-stat-num">6-8<span>wks</span></div><div class="p-stat-label">for a complete governance implementation in a standard tenant</div></div><div class="p-stat"><div class="p-stat-num">100<span>%</span></div><div class="p-stat-label">of Armely governance engagements include Copilot readiness preparation</div></div></div></div></div></div></div></section>
<section class="cta-section" id="contact"><div class="cta-inner"><div><div class="section-eyebrow">Get Started</div><h2 class="section-title">Start with a free tenant health check. No commitment required.</h2><p class="section-body">Book a free Microsoft 365 Tenant Health Check. We will run a structured assessment and deliver a written report identifying your most significant security, governance, and adoption gaps within one week.</p><div style="margin-top:28px;display:flex;flex-direction:column;gap:12px;"><div class="trust-item"><span class="trust-dot" style="background:var(--blue);"></span><span class="trust-text" style="color:var(--text-body);">Free assessment, no commitment required</span></div><div class="trust-item"><span class="trust-dot" style="background:var(--blue);"></span><span class="trust-text" style="color:var(--text-body);">Recommendation and partner pricing included</span></div><div class="trust-item"><span class="trust-dot" style="background:var(--blue);"></span><span class="trust-text" style="color:var(--text-body);">Response within one business day</span></div></div></div><div class="cta-form"><div class="form-title">Book Your Free Assessment</div><div class="form-sub">Tell us about your situation.</div><div class="form-row"><label>Full Name</label><input type="text" placeholder="Jane Smith"></div><div class="form-row"><label>Business Email</label><input type="email" placeholder="jane@yourcompany.com"></div><div class="form-row"><label>Company Name</label><input type="text" placeholder="Acme Corp"></div><div class="form-row"><label>Primary Need</label><select><option value="">Select...</option><option>Security and external sharing exposure</option><option>Teams and SharePoint sprawl and governance</option><option>Compliance and audit readiness</option><option>Low staff adoption of Microsoft 365</option><option>Copilot readiness preparation</option><option>General tenant health review</option><option>Not sure, want an assessment first</option></select></div><button class="form-submit">Request Free Tenant Health Check</button><div class="form-note">No spam. No sales pressure. Just a useful conversation.</div></div></div></section>
</div>

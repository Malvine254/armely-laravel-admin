<style>

.armely-power-platform-page *, .armely-power-platform-page *::before, .armely-power-platform-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-power-platform-page {
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

.armely-power-platform-page { scroll-behavior: smooth; }
.armely-power-platform-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* NAV */
.armely-power-platform-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-power-platform-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-power-platform-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-power-platform-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-power-platform-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-power-platform-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-power-platform-page .nav-links a:hover { color: #fff; }
.armely-power-platform-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-power-platform-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* HERO */
.armely-power-platform-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-power-platform-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-power-platform-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-power-platform-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-power-platform-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-power-platform-page .hero h1 { font-size: clamp(2.6rem, 5.5vw, 4.8rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 780px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-power-platform-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-power-platform-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 560px; margin-bottom: 40px; line-height: 1.8; }
.armely-power-platform-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-power-platform-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-power-platform-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-power-platform-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-power-platform-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-power-platform-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-power-platform-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-power-platform-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-power-platform-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-power-platform-page .trust-text strong { color: #fff; font-weight: 600; }

  /* SECTIONS */
.armely-power-platform-page section { padding: 96px 56px; }
.armely-power-platform-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-power-platform-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-power-platform-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 620px; }
.armely-power-platform-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 540px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* PRODUCTS SECTION */
.armely-power-platform-page .products { background: var(--navy-mid); }
.armely-power-platform-page .products-intro { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }

.armely-power-platform-page .product-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 32px; }
.armely-power-platform-page .product-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 18px 16px; transition: border-color 0.2s, box-shadow 0.2s; }
.armely-power-platform-page .product-card:hover { border-color: rgba(41,78,139,0.3); box-shadow: 0 4px 16px rgba(41,78,139,0.08); }
.armely-power-platform-page .product-card-icon { font-size: 1.3rem; margin-bottom: 10px; display: block; }
.armely-power-platform-page .product-card-title { font-size: 0.85rem; font-weight: 700; color: #1A2540; margin-bottom: 4px; }
.armely-power-platform-page .product-card-desc { font-size: 0.75rem; color: var(--text-muted); line-height: 1.55; }
.armely-power-platform-page .product-card.span2 { grid-column: 1 / -1; display: flex; align-items: flex-start; gap: 14px; }
.armely-power-platform-page .product-card.span2 .product-card-icon { flex-shrink: 0; font-size: 1.3rem; margin-bottom: 0; }

  /* Platform visual */
.armely-power-platform-page .platform-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-power-platform-page .platform-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-power-platform-page .platform-dots { display: flex; gap: 6px; }
.armely-power-platform-page .platform-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-power-platform-page .platform-header-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-power-platform-page .platform-body { padding: 20px; }
.armely-power-platform-page .platform-layer { border-radius: 9px; padding: 13px 16px; margin-bottom: 8px; }
.armely-power-platform-page .platform-layer-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
.armely-power-platform-page .platform-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.armely-power-platform-page .platform-chip { font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.armely-power-platform-page .layer-apps { background: var(--blue-dim); }
.armely-power-platform-page .layer-apps .platform-layer-label { color: var(--blue); }
.armely-power-platform-page .layer-apps .platform-chip { background: var(--blue-dim2); color: var(--blue); }
.armely-power-platform-page .layer-data { background: rgba(41,78,139,0.05); }
.armely-power-platform-page .layer-data .platform-layer-label { color: var(--blue); }
.armely-power-platform-page .layer-data .platform-chip { background: rgba(41,78,139,0.1); color: var(--blue); }
.armely-power-platform-page .layer-connectors { background: #fff; border: 1px solid var(--border); }
.armely-power-platform-page .layer-connectors .platform-layer-label { color: var(--text-muted); }
.armely-power-platform-page .layer-connectors .platform-chip { background: var(--navy-card); color: var(--text-muted); }
.armely-power-platform-page .layer-foundation { background: var(--blue); }
.armely-power-platform-page .layer-foundation .platform-layer-label { color: rgba(255,255,255,0.7); }
.armely-power-platform-page .layer-foundation .platform-chip { background: rgba(255,255,255,0.15); color: #fff; }
.armely-power-platform-page .layer-arrow { text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2px 0; }

  /* DELIVERS */
.armely-power-platform-page .delivers { background: var(--navy); }
.armely-power-platform-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-power-platform-page .deliver-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-power-platform-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-power-platform-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-power-platform-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-power-platform-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-power-platform-page .journey { background: var(--navy-mid); }
.armely-power-platform-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-power-platform-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-power-platform-page .step:last-child { border-right: none; }
.armely-power-platform-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-power-platform-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-power-platform-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-power-platform-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* USE CASES */
.armely-power-platform-page .usecases { background: var(--navy); }
.armely-power-platform-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-power-platform-page .uc-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-power-platform-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-power-platform-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-power-platform-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-power-platform-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }
.armely-power-platform-page .uc-tag { display: inline-block; margin-top: 12px; font-size: 0.7rem; font-weight: 600; color: var(--blue); background: var(--blue-dim); padding: 3px 10px; border-radius: 4px; }

  /* TESTIMONIALS */
.armely-power-platform-page .testimonials { background: var(--navy-mid); padding: 96px 56px; }
.armely-power-platform-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-power-platform-page .testi-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-power-platform-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-power-platform-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-power-platform-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-power-platform-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-power-platform-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-power-platform-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-power-platform-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY ARMELY */
.armely-power-platform-page .why { background: var(--navy); }
.armely-power-platform-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-power-platform-page .why-list { list-style: none; margin-top: 36px; }
.armely-power-platform-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-power-platform-page .why-list li:last-child { border-bottom: none; }
.armely-power-platform-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-power-platform-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-power-platform-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-power-platform-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-power-platform-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-power-platform-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-power-platform-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-power-platform-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-power-platform-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-power-platform-page .p-stat:nth-child(2) { border-right: none; }
.armely-power-platform-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-power-platform-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-power-platform-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-power-platform-page .p-stat-num span { color: var(--blue); }
.armely-power-platform-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* CTA */
.armely-power-platform-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-power-platform-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-power-platform-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-power-platform-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-power-platform-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-power-platform-page .form-row { margin-bottom: 14px; }
.armely-power-platform-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-power-platform-page .form-row input, .armely-power-platform-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-power-platform-page .form-row input:focus, .armely-power-platform-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-power-platform-page .form-row select option { background: #fff; color: #1A2540; }
.armely-power-platform-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-power-platform-page .form-submit:hover { background: var(--blue-lt); }
.armely-power-platform-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* FOOTER */
.armely-power-platform-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-power-platform-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-power-platform-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-power-platform-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-power-platform-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-power-platform-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-power-platform-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* RESPONSIVE */
  @media (max-width: 900px) {
.armely-power-platform-page nav { padding: 16px 24px; }
.armely-power-platform-page .nav-links { display: none; }
.armely-power-platform-page section { padding: 72px 24px; }
.armely-power-platform-page .hero { padding: 110px 24px 72px; }
.armely-power-platform-page .products-intro, .armely-power-platform-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-power-platform-page .delivers-grid, .armely-power-platform-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-power-platform-page .steps-row { grid-template-columns: 1fr; }
.armely-power-platform-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-power-platform-page .step:last-child { border-bottom: none; }
.armely-power-platform-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-power-platform-page .testimonials { padding: 72px 24px; }
.armely-power-platform-page .testi-grid { grid-template-columns: 1fr; }
.armely-power-platform-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-power-platform-page .delivers-grid, .armely-power-platform-page .uc-grid, .armely-power-platform-page .product-cards { grid-template-columns: 1fr; }
.armely-power-platform-page .product-card.span2 { grid-column: auto; }
.armely-power-platform-page .partner-stats { grid-template-columns: 1fr; }
.armely-power-platform-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-power-platform-page {
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
.armely-power-platform-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-power-platform-page .hero::after {
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
.armely-power-platform-page .section-title,
.armely-power-platform-page .deliver-title,
.armely-power-platform-page .uc-title,
.armely-power-platform-page .step-title,
.armely-power-platform-page .why-item-title,
.armely-power-platform-page .form-title {
  color: #162b49;
}
.armely-power-platform-page .deliver-card,
.armely-power-platform-page .uc-card,
.armely-power-platform-page .testi-card,
.armely-power-platform-page .platform-card,
.armely-power-platform-page .partner-block,
.armely-power-platform-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-power-platform-page .deliver-card:hover,
.armely-power-platform-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-power-platform-page .btn-primary,
.armely-power-platform-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-power-platform-page .btn-primary:hover,
.armely-power-platform-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-power-platform-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-power-platform-page nav,
.armely-power-platform-page footer {
  display: none;
}

/* Shared modern service page refresh */
.armely-power-platform-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: #173b67;
  border-radius: 0;
}
.armely-power-platform-page .hero::after,
.armely-power-platform-page .hero-bg-glow,
.armely-power-platform-page .hero-trust {
  display: none;
}
.armely-power-platform-page .hero h1 {
  max-width: 820px;
  margin-bottom: 22px;
}
.armely-power-platform-page .hero-sub {
  max-width: 700px;
  margin-bottom: 34px;
}
.armely-power-platform-page .hero-actions {
  margin-bottom: 0;
}
.armely-power-platform-page .hero .btn-primary,
.armely-power-platform-page .hero .btn-outline {
  border-radius: 0;
}
.armely-power-platform-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.74);
}
.armely-power-platform-page .eyebrow-partner {
  display: none;
}
.armely-power-platform-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-power-platform-page .cta-inner > div > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-power-platform-page section:not(.hero) > .section-inner > .section-title,
.armely-power-platform-page .cta-inner > div > .section-title {
  max-width: 900px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-power-platform-page section:not(.hero) > .section-inner > .section-body,
.armely-power-platform-page .cta-inner > div > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-power-platform-page .intro-grid,
.armely-power-platform-page .symptoms-grid,
.armely-power-platform-page .when-grid,
.armely-power-platform-page .why-two-col,
.armely-power-platform-page .cta-inner {
  align-items: stretch;
}
.armely-power-platform-page .intro-grid,
.armely-power-platform-page .symptoms-grid,
.armely-power-platform-page .when-grid,
.armely-power-platform-page .delivers-grid,
.armely-power-platform-page .tier-grid,
.armely-power-platform-page .covers-grid,
.armely-power-platform-page .steps-row,
.armely-power-platform-page .uc-grid,
.armely-power-platform-page .testi-grid,
.armely-power-platform-page .why-two-col,
.armely-power-platform-page .pathway-grid {
  margin-top: 56px;
}
.armely-power-platform-page .deliver-icon,
.armely-power-platform-page .uc-icon,
.armely-power-platform-page .why-icon,
.armely-power-platform-page .symptom-icon,
.armely-power-platform-page .what-card-icon,
.armely-power-platform-page .cov-item-icon,
.armely-power-platform-page .cover-icon,
.armely-power-platform-page .product-card-icon,
.armely-power-platform-page .cap-icon,
.armely-power-platform-page .workload-pill-icon,
.armely-power-platform-page .decision-icon,
.armely-power-platform-page .sign-icon,
.armely-power-platform-page .pathway-icon,
.armely-power-platform-page .onelake-callout-icon,
.armely-power-platform-page .vs-callout-icon {
  color: var(--blue);
  font-size: 1.1rem;
  line-height: 1;
}
.armely-power-platform-page .deliver-icon,
.armely-power-platform-page .uc-icon,
.armely-power-platform-page .why-icon {
  width: 48px;
  height: 48px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-power-platform-page .deliver-card,
.armely-power-platform-page .uc-card,
.armely-power-platform-page .testi-card,
.armely-power-platform-page .tier-card,
.armely-power-platform-page .cover-card,
.armely-power-platform-page .pathway-card,
.armely-power-platform-page .partner-block,
.armely-power-platform-page .cta-form {
  background: linear-gradient(180deg, #ffffff 0%, #f9fbfe 100%);
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
@media (max-width: 900px) {
  .armely-power-platform-page .hero { padding: 118px 24px 76px; }
  .armely-power-platform-page section:not(.hero) > .section-inner > .section-title,
  .armely-power-platform-page .cta-inner > div > .section-title { max-width: 100%; }
}
</style>
<div class="armely-power-platform-page">
<!-- NAV -->


<!-- HERO -->
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-eyebrow">
    <span class="eyebrow-badge">Microsoft Power Platform</span>
    <span class="eyebrow-partner">Delivered by a certified Microsoft partner</span>
  </div>
  <h1>Build apps. Automate work.<br><span class="hl">Eliminate the tools gap.</span></h1>
  <p class="hero-sub">Armely designs and delivers Microsoft Power Platform solutions that replace manual processes, connect your business systems, and put the right information in front of the right people, without months of custom development.</p>
  <div class="hero-actions">
    <a href="#contact" class="btn-primary">Book a Free Discovery Call</a>
    <a href="#products" class="btn-outline">Explore the Platform</a>
  </div>
  <div class="hero-trust">
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Low-code</strong> development, enterprise-grade results</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">Native integration with <strong>Microsoft 365 and Dynamics 365</strong></span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Copilot AI</strong> built into every product</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>900 plus</strong> pre-built connectors</span>
    </div>
  </div>
</section>

<!-- WHAT IS POWER PLATFORM -->
<section class="products" id="products">
  <div class="section-inner">
    <div class="products-intro">
      <div>
        <div class="section-eyebrow">What is Microsoft Power Platform?</div>
        <h2 class="section-title">Five products that work individually and are far more powerful together.</h2>
        <p class="section-body">Power Platform is Microsoft's low-code suite for building applications, automating workflows, analyzing data, and creating external-facing websites, all sharing the same data layer, security model, and Copilot AI capabilities. Most organizations start with one product and expand as they see what is possible.</p>
        <div class="product-cards">
          <div class="product-card">
            <span class="product-card-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
            <div class="product-card-title">Power Apps</div>
            <div class="product-card-desc">Build custom business applications without extensive development. Canvas apps for flexible UI design, model-driven apps for data-centric workflows.</div>
          </div>
          <div class="product-card">
            <span class="product-card-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
            <div class="product-card-title">Power Automate</div>
            <div class="product-card-desc">Automate repetitive tasks and multi-system workflows using cloud flows, desktop flows for RPA, and AI-assisted process mining.</div>
          </div>
          <div class="product-card">
            <span class="product-card-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
            <div class="product-card-title">Power BI</div>
            <div class="product-card-desc">Transform raw data into interactive dashboards and reports that give every team in your organization clear, real-time visibility.</div>
          </div>
          <div class="product-card">
            <span class="product-card-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
            <div class="product-card-title">Power Pages</div>
            <div class="product-card-desc">Build secure, professional external-facing websites and portals connected to your business data, with low-code design tools and built-in governance.</div>
          </div>
          <div class="product-card span2">
            <span class="product-card-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
            <div>
              <div class="product-card-title">Copilot Studio</div>
              <div class="product-card-desc">Build AI agents and custom copilots that answer questions, take actions, and automate workflows across your business, connected to your own data and systems.</div>
            </div>
          </div>
        </div>
      </div>
      <div>
        <div class="platform-card">
          <div class="platform-header">
            <div class="platform-dots"><span></span><span></span><span></span></div>
            <span class="platform-header-title">Power Platform Architecture</span>
          </div>
          <div class="platform-body">
            <div class="platform-layer layer-apps">
              <div class="platform-layer-label">Products</div>
              <div class="platform-chips">
                <span class="platform-chip">Power Apps</span>
                <span class="platform-chip">Power Automate</span>
                <span class="platform-chip">Power BI</span>
                <span class="platform-chip">Power Pages</span>
                <span class="platform-chip">Copilot Studio</span>
              </div>
            </div>
            <div class="layer-arrow">&#8597;</div>
            <div class="platform-layer layer-data">
              <div class="platform-layer-label">Shared Data and AI</div>
              <div class="platform-chips">
                <span class="platform-chip">Dataverse</span>
                <span class="platform-chip">Copilot AI</span>
                <span class="platform-chip">AI Builder</span>
                <span class="platform-chip">Power Fx</span>
              </div>
            </div>
            <div class="layer-arrow">&#8597;</div>
            <div class="platform-layer layer-connectors">
              <div class="platform-layer-label">900+ Connectors</div>
              <div class="platform-chips">
                <span class="platform-chip">Microsoft 365</span>
                <span class="platform-chip">Dynamics 365</span>
                <span class="platform-chip">SharePoint</span>
                <span class="platform-chip">SQL Server</span>
                <span class="platform-chip">Salesforce</span>
                <span class="platform-chip">SAP</span>
                <span class="platform-chip">ServiceNow</span>
              </div>
            </div>
            <div class="layer-arrow">&#8597;</div>
            <div class="platform-layer layer-foundation">
              <div class="platform-layer-label">Governance and Security</div>
              <div class="platform-chips">
                <span class="platform-chip">Azure AD / Entra ID</span>
                <span class="platform-chip">DLP Policies</span>
                <span class="platform-chip">Managed Environments</span>
                <span class="platform-chip">Audit Logs</span>
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
    <h2 class="section-title">Solutions that solve real business problems, not technical exercises.</h2>
    <p class="section-body">Armely builds Power Platform solutions around your workflows and business outcomes. Every engagement starts with understanding the problem before selecting the product.</p>
    <div class="delivers-grid">
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Custom Application Development</div>
        <div class="deliver-desc">We build Power Apps solutions that replace paper forms, spreadsheet-based processes, and legacy internal tools. From simple data capture apps to complex model-driven applications connected to Dataverse, built and delivered in weeks.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Workflow and Process Automation</div>
        <div class="deliver-desc">We automate approval workflows, document routing, data synchronization across systems, and repetitive desktop tasks using Power Automate cloud flows and RPA. We identify the highest-value processes to automate first, so you see return quickly.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Power BI Dashboards and Reports</div>
        <div class="deliver-desc">We connect Power BI to your data sources, build semantic models, and design dashboards that give leadership and operations teams real-time visibility across the business. Reports that get opened every morning, not quarterly.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">External Portals with Power Pages</div>
        <div class="deliver-desc">We build customer portals, partner sites, and application forms using Power Pages, connected to your Dataverse or Dynamics 365 data. Secure external access to business data without custom web development costs.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">AI Agents and Copilot Studio</div>
        <div class="deliver-desc">We design and deploy AI agents using Copilot Studio that answer employee or customer questions, trigger workflows, and surface business data in natural language conversations, connected to your own systems and data sources.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Governance and Center of Excellence</div>
        <div class="deliver-desc">Power Platform grows fast and ungoverned environments become liabilities. We implement the Microsoft Center of Excellence Starter Kit, DLP policies, managed environments, and admin tooling so your investment scales safely.</div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNEY -->
<section class="journey" id="journey">
  <div class="section-inner">
    <div class="section-eyebrow">The Armely Power Platform Journey</div>
    <h2 class="section-title">From process pain point to working solution, on a clear timeline.</h2>
    <p class="section-body">Power Platform's strength is speed. Our delivery approach is designed to get you to a working solution fast, validate it with real users, and expand from there rather than spending months in design before anyone sees anything.</p>
    <div class="steps-row">
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-title">Discovery Workshop</div>
        <div class="step-desc">We identify your highest-value automation and application opportunities, map your data sources, and confirm which Power Platform products apply to your situation.</div>
        <span class="step-tag">Free</span>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-title">Solution Design</div>
        <div class="step-desc">We design the solution architecture, confirm licensing requirements at partner pricing, and align on scope before any build work begins.</div>
        <span class="step-tag">1 week</span>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-title">Build and Review</div>
        <div class="step-desc">We build iteratively with regular checkpoints so you see working software throughout the project, not just at the end.</div>
        <span class="step-tag">Weeks 2-5</span>
      </div>
      <div class="step">
        <div class="step-num">04</div>
        <div class="step-title">Training and Launch</div>
        <div class="step-desc">User training, administrator documentation, and a managed go-live with Armely available to support your team through the first weeks of adoption.</div>
        <span class="step-tag">Week 6</span>
      </div>
      <div class="step">
        <div class="step-num">05</div>
        <div class="step-title">Expand and Govern</div>
        <div class="step-desc">Additional solutions built on the same platform foundation, with governance controls and admin tooling that keep your Power Platform environment healthy as it grows.</div>
        <span class="step-tag">Ongoing</span>
      </div>
    </div>
  </div>
</section>

<!-- USE CASES -->
<section class="usecases">
  <div class="section-inner">
    <div class="section-eyebrow">Common Engagements</div>
    <h2 class="section-title">What organizations build and automate with Power Platform.</h2>
    <p class="section-body">These are the scenarios we deliver most frequently, across industries and organization sizes. Most start with one clear problem and expand from there.</p>
    <div class="uc-grid">
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Replace Paper and Spreadsheet Processes</div>
        <div class="uc-desc">Inspection forms, expense submissions, time tracking, onboarding checklists, and incident reports built as Power Apps and submitted directly to a central data store, with automated routing and notifications.</div>
        <span class="uc-tag">Power Apps + Power Automate</span>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Multi-Step Approval Workflows</div>
        <div class="uc-desc">Purchase order approvals, contract reviews, leave requests, and capital expenditure sign-offs automated end to end, with Teams notifications, audit trails, and escalation paths when approvers do not respond.</div>
        <span class="uc-tag">Power Automate</span>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Executive and Operational Dashboards</div>
        <div class="uc-desc">Live Power BI reports connected to your ERP, CRM, or operational systems, giving leadership and team managers a single view of performance without waiting for weekly extracts or manual report runs.</div>
        <span class="uc-tag">Power BI</span>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Cross-System Data Synchronization</div>
        <div class="uc-desc">Automated flows that keep customer records, order data, and employee information consistent across Dynamics 365, SharePoint, external databases, and third-party systems without manual re-entry or batch file imports.</div>
        <span class="uc-tag">Power Automate</span>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Customer and Partner Portals</div>
        <div class="uc-desc">Secure external portals where customers can submit requests, view their account status, upload documents, or complete applications, connected directly to your Dynamics 365 or Dataverse data.</div>
        <span class="uc-tag">Power Pages</span>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Internal AI Assistants</div>
        <div class="uc-desc">Custom AI agents built in Copilot Studio that answer employee questions about HR policies, IT procedures, or product information by searching your actual documentation, not generic web content.</div>
        <span class="uc-tag">Copilot Studio</span>
      </div>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials">
  <div class="section-inner">
    <div class="section-eyebrow">Client Results</div>
    <h2 class="section-title">What our clients say about working with Armely.</h2>
    <div class="testi-grid">

      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">Our field inspection process was entirely paper-based. Armely built a Power Apps solution in three weeks that our technicians use on tablets in the field. Submissions go directly into SharePoint, and a Power Automate flow routes completed reports to the right manager automatically. What used to take two days of data entry now happens in real time.</p>
        <div class="testi-footer">
          <div class="testi-avatar">OM</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Operations Manager</div>
            <div class="testi-role">Infrastructure Services Company, Texas</div>
          </div>
        </div>
      </div>

      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">We needed consolidated reporting across three different systems our leadership team used daily. Armely connected Power BI to all three sources, built a unified semantic model, and delivered a dashboard suite that our executive team now opens every morning. The project was scoped, built, and live in under six weeks.</p>
        <div class="testi-footer">
          <div class="testi-avatar">CFO</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Chief Financial Officer</div>
            <div class="testi-role">Healthcare Organization, Midwest</div>
          </div>
        </div>
      </div>

      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">Our purchase approval process ran entirely through email and had no audit trail. Armely built a Power Automate flow that routes requests based on amount and department, sends Teams notifications to approvers, escalates after 48 hours, and writes every decision to a SharePoint log. Our finance team now has full visibility and our auditors are satisfied.</p>
        <div class="testi-footer">
          <div class="testi-avatar">FC</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Finance Controller</div>
            <div class="testi-role">Professional Services Firm, Southeast</div>
          </div>
        </div>
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
        <h2 class="section-title">Power Platform works best when the implementor understands your business, not just the technology.</h2>
        <p class="section-body">Low-code does not mean no skill required. The difference between a Power Platform solution that gets used and one that gets abandoned is whether it was designed around how people actually work.</p>
        <ul class="why-list">
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Certified Power Platform Developers</div>
              <div class="why-item-desc">Our team holds Microsoft Power Platform certifications across Power Apps, Power Automate, and Power BI, with production delivery experience across healthcare, education, and professional services organizations.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Full Microsoft Ecosystem Coverage</div>
              <div class="why-item-desc">Power Platform integrates most deeply with Microsoft 365, Dynamics 365, Azure, and SQL Server. Armely covers all of these, so your Power Platform solutions are designed to work with your existing Microsoft environment from day one.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Governance Built In from the Start</div>
              <div class="why-item-desc">Ungoverned Power Platform environments accumulate technical debt quickly. We implement DLP policies, naming standards, environment strategy, and admin tooling alongside every solution we deliver, not as an afterthought.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Licensing at Partner Pricing</div>
              <div class="why-item-desc">As a Microsoft-authorized CSP partner, we source Power Platform licensing at rates not available through direct purchase and help you select the right license tier for your use case rather than overbuying.</div>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <div class="partner-block">
          <div class="partner-block-top">
            <div class="partner-label">Microsoft Authorized Partner</div>
            <p class="partner-text">Armely's Microsoft partnership gives us access to Power Platform licensing, technical pre-sales resources, and implementation support not available to direct customers. That means better pricing, faster starts, and solutions built on Microsoft's own recommended architectures and governance frameworks.</p>
          </div>
          <div class="partner-stats">
            <div class="p-stat">
              <div class="p-stat-num">900<span>+</span></div>
              <div class="p-stat-label">pre-built connectors to external systems and services</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">5</div>
              <div class="p-stat-label">products in one platform sharing data, security, and AI</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">75<span>%</span></div>
              <div class="p-stat-label">of new apps projected to be built with low-code tools by 2026 (Gartner)</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">300<span>M+</span></div>
              <div class="p-stat-label">automated workflows processed monthly across the Power Platform</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section" id="contact">
  <div class="cta-inner">
    <div>
      <div class="section-eyebrow">Get Started</div>
      <h2 class="section-title">Tell us the process you want to fix. We will show you what is possible.</h2>
      <p class="section-body">Book a free 30-minute discovery call. We will review your current tools and workflows, identify the right Power Platform products for your situation, and come back with a solution proposal and licensing recommendation at no obligation.</p>
      <div style="margin-top: 28px; display: flex; flex-direction: column; gap: 12px;">
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Free discovery, no commitment required</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Solution recommendation and partner pricing included</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Response within one business day</span>
        </div>
      </div>
    </div>
    <div class="cta-form">
      <div class="form-title">Book Your Free Discovery Call</div>
      <div class="form-sub">Tell us what you are trying to build or automate.</div>
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
        <label>Primary Interest</label>
        <select>
          <option value="">Select...</option>
          <option>Build a custom business application (Power Apps)</option>
          <option>Automate a manual or approval-based process (Power Automate)</option>
          <option>Create dashboards and reporting (Power BI)</option>
          <option>Build a customer or partner portal (Power Pages)</option>
          <option>Build an AI agent or internal assistant (Copilot Studio)</option>
          <option>Not sure, need a recommendation</option>
        </select>
      </div>
      <button class="form-submit">Request Free Discovery Call</button>
      <div class="form-note">No spam. No sales pressure. Just a useful conversation.</div>
    </div>
  </div>
</section>

<!-- FOOTER -->
</div>

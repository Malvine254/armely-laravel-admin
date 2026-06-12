<style>

.armely-sql-server-page *, .armely-sql-server-page *::before, .armely-sql-server-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-sql-server-page {
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
    --red-dim:   rgba(180,30,30,0.08);
    --red:       #b41e1e;
  }

.armely-sql-server-page { scroll-behavior: smooth; }
.armely-sql-server-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* ── NAV ── */
.armely-sql-server-page nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    display: flex; justify-content: space-between; align-items: center;
    padding: 18px 56px;
    background: rgba(26,46,82,0.96);
    backdrop-filter: blur(14px);
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }
.armely-sql-server-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-sql-server-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-sql-server-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-sql-server-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-sql-server-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-sql-server-page .nav-links a:hover { color: #fff; }
.armely-sql-server-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-sql-server-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* ── HERO ── */
.armely-sql-server-page .hero {
    min-height: 100vh;
    display: flex; flex-direction: column; justify-content: center;
    padding: 140px 56px 100px;
    position: relative; overflow: hidden;
    background: #1a2e52;
  }
.armely-sql-server-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-sql-server-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-sql-server-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-sql-server-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-sql-server-page .hero h1 { font-size: clamp(2.6rem, 5.5vw, 4.8rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 780px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-sql-server-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-sql-server-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 560px; margin-bottom: 40px; line-height: 1.8; }
.armely-sql-server-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-sql-server-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-sql-server-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-sql-server-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-sql-server-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-sql-server-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-sql-server-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-sql-server-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-sql-server-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-sql-server-page .trust-text strong { color: #fff; font-weight: 600; }

  /* ── SECTIONS ── */
.armely-sql-server-page section { padding: 96px 56px; }
.armely-sql-server-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-sql-server-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-sql-server-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 620px; }
.armely-sql-server-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 540px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* ── EOL BANNER ── */
.armely-sql-server-page .eol-banner { background: var(--navy-mid); padding: 0 56px; }
.armely-sql-server-page .eol-banner-inner { max-width: 1100px; margin: 0 auto; padding: 28px 0; border-top: 1px solid var(--border); }
.armely-sql-server-page .eol-alert {
    background: rgba(180,30,30,0.06);
    border: 1px solid rgba(180,30,30,0.2);
    border-radius: 10px;
    padding: 18px 24px;
    display: flex; align-items: center; gap: 16px;
  }
.armely-sql-server-page .eol-icon { font-size: 1.3rem; flex-shrink: 0; }
.armely-sql-server-page .eol-text { font-size: 0.875rem; color: #1A2540; line-height: 1.6; }
.armely-sql-server-page .eol-text strong { color: var(--red); }
.armely-sql-server-page .eol-link { color: var(--blue); font-weight: 600; font-size: 0.82rem; white-space: nowrap; flex-shrink: 0; text-decoration: none; border: 1px solid var(--blue-dim2); border-radius: 6px; padding: 8px 16px; margin-left: auto; transition: background 0.2s; }
.armely-sql-server-page .eol-link:hover { background: var(--blue-dim); }

  /* ── WHAT IS SQL SERVER ── */
.armely-sql-server-page .intro { background: var(--navy-mid); }
.armely-sql-server-page .intro-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }

  /* Capability grid */
.armely-sql-server-page .cap-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 28px; }
.armely-sql-server-page .cap-card { background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 16px; }
.armely-sql-server-page .cap-card-icon { font-size: 1.1rem; margin-bottom: 8px; display: block; }
.armely-sql-server-page .cap-card-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 4px; }
.armely-sql-server-page .cap-card-desc { font-size: 0.75rem; color: var(--text-muted); line-height: 1.5; }

  /* Deployment visual */
.armely-sql-server-page .deploy-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-sql-server-page .deploy-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-sql-server-page .deploy-dots { display: flex; gap: 6px; }
.armely-sql-server-page .deploy-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-sql-server-page .deploy-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-sql-server-page .deploy-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.armely-sql-server-page .deploy-option { border-radius: 9px; padding: 14px 16px; border: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
.armely-sql-server-page .deploy-option-icon { font-size: 1.2rem; flex-shrink: 0; }
.armely-sql-server-page .deploy-option-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 2px; }
.armely-sql-server-page .deploy-option-desc { font-size: 0.73rem; color: var(--text-muted); line-height: 1.4; }
.armely-sql-server-page .deploy-option.active { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-sql-server-page .deploy-ai-callout { background: var(--blue); border-radius: 9px; padding: 14px 16px; display: flex; align-items: center; gap: 12px; margin-top: 4px; }
.armely-sql-server-page .deploy-ai-callout-text { font-size: 0.82rem; color: rgba(255,255,255,0.9); line-height: 1.5; }
.armely-sql-server-page .deploy-ai-callout-text strong { color: #fff; }

  /* ── DELIVERS ── */
.armely-sql-server-page .delivers { background: var(--navy); }
.armely-sql-server-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-sql-server-page .deliver-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-sql-server-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-sql-server-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-sql-server-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-sql-server-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* ── JOURNEY ── */
.armely-sql-server-page .journey { background: var(--navy-mid); }
.armely-sql-server-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-sql-server-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-sql-server-page .step:last-child { border-right: none; }
.armely-sql-server-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-sql-server-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-sql-server-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-sql-server-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* ── USE CASES ── */
.armely-sql-server-page .usecases { background: var(--navy); }
.armely-sql-server-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-sql-server-page .uc-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-sql-server-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-sql-server-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-sql-server-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-sql-server-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* ── TESTIMONIALS ── */
.armely-sql-server-page .testimonials { background: var(--navy-mid); padding: 96px 56px; }
.armely-sql-server-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-sql-server-page .testi-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-sql-server-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-sql-server-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-sql-server-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-sql-server-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-sql-server-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-sql-server-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-sql-server-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* ── WHY ARMELY ── */
.armely-sql-server-page .why { background: var(--navy); }
.armely-sql-server-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-sql-server-page .why-list { list-style: none; margin-top: 36px; }
.armely-sql-server-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-sql-server-page .why-list li:last-child { border-bottom: none; }
.armely-sql-server-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-sql-server-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-sql-server-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-sql-server-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-sql-server-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-sql-server-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-sql-server-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-sql-server-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-sql-server-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-sql-server-page .p-stat:nth-child(2) { border-right: none; }
.armely-sql-server-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-sql-server-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-sql-server-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-sql-server-page .p-stat-num span { color: var(--blue); }
.armely-sql-server-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* ── CTA ── */
.armely-sql-server-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-sql-server-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-sql-server-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-sql-server-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-sql-server-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-sql-server-page .form-row { margin-bottom: 14px; }
.armely-sql-server-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-sql-server-page .form-row input, .armely-sql-server-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-sql-server-page .form-row input:focus, .armely-sql-server-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-sql-server-page .form-row select option { background: #fff; color: #1A2540; }
.armely-sql-server-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-sql-server-page .form-submit:hover { background: var(--blue-lt); }
.armely-sql-server-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* ── FOOTER ── */
.armely-sql-server-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-sql-server-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-sql-server-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-sql-server-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-sql-server-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-sql-server-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-sql-server-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* ── RESPONSIVE ── */
  @media (max-width: 900px) {
.armely-sql-server-page nav { padding: 16px 24px; }
.armely-sql-server-page .nav-links { display: none; }
.armely-sql-server-page section { padding: 72px 24px; }
.armely-sql-server-page .hero { padding: 110px 24px 72px; }
.armely-sql-server-page .eol-banner { padding: 0 24px; }
.armely-sql-server-page .eol-alert { flex-wrap: wrap; }
.armely-sql-server-page .eol-link { margin-left: 0; }
.armely-sql-server-page .intro-grid, .armely-sql-server-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-sql-server-page .delivers-grid, .armely-sql-server-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-sql-server-page .steps-row { grid-template-columns: 1fr; }
.armely-sql-server-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-sql-server-page .step:last-child { border-bottom: none; }
.armely-sql-server-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-sql-server-page .testimonials { padding: 72px 24px; }
.armely-sql-server-page .testi-grid { grid-template-columns: 1fr; }
.armely-sql-server-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-sql-server-page .delivers-grid, .armely-sql-server-page .uc-grid, .armely-sql-server-page .cap-grid { grid-template-columns: 1fr; }
.armely-sql-server-page .partner-stats { grid-template-columns: 1fr; }
.armely-sql-server-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-sql-server-page {
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
.armely-sql-server-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-sql-server-page .hero::after {
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
.armely-sql-server-page .section-title,
.armely-sql-server-page .deliver-title,
.armely-sql-server-page .uc-title,
.armely-sql-server-page .step-title,
.armely-sql-server-page .why-item-title,
.armely-sql-server-page .form-title {
  color: #162b49;
}
.armely-sql-server-page .deliver-card,
.armely-sql-server-page .uc-card,
.armely-sql-server-page .testi-card,
.armely-sql-server-page .platform-card,
.armely-sql-server-page .partner-block,
.armely-sql-server-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-sql-server-page .deliver-card:hover,
.armely-sql-server-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-sql-server-page .btn-primary,
.armely-sql-server-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-sql-server-page .btn-primary:hover,
.armely-sql-server-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-sql-server-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-sql-server-page nav,
.armely-sql-server-page footer {
  display: none;
}

/* Shared modern service page refresh */
.armely-sql-server-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: #173b67;
  border-radius: 0;
}
.armely-sql-server-page .hero::after,
.armely-sql-server-page .hero-bg-glow,
.armely-sql-server-page .hero-trust {
  display: none;
}
.armely-sql-server-page .hero h1 {
  max-width: 820px;
  margin-bottom: 22px;
}
.armely-sql-server-page .hero-sub {
  max-width: 700px;
  margin-bottom: 34px;
}
.armely-sql-server-page .hero-actions {
  margin-bottom: 0;
}
.armely-sql-server-page .hero .btn-primary,
.armely-sql-server-page .hero .btn-outline {
  border-radius: 0;
}
.armely-sql-server-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.74);
}
.armely-sql-server-page .eyebrow-partner {
  display: none;
}
.armely-sql-server-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-sql-server-page .cta-inner > div > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-sql-server-page section:not(.hero) > .section-inner > .section-title,
.armely-sql-server-page .cta-inner > div > .section-title {
  max-width: 900px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-sql-server-page section:not(.hero) > .section-inner > .section-body,
.armely-sql-server-page .cta-inner > div > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-sql-server-page .intro-grid,
.armely-sql-server-page .symptoms-grid,
.armely-sql-server-page .when-grid,
.armely-sql-server-page .why-two-col,
.armely-sql-server-page .cta-inner {
  align-items: stretch;
}
.armely-sql-server-page .intro-grid,
.armely-sql-server-page .symptoms-grid,
.armely-sql-server-page .when-grid,
.armely-sql-server-page .delivers-grid,
.armely-sql-server-page .tier-grid,
.armely-sql-server-page .covers-grid,
.armely-sql-server-page .steps-row,
.armely-sql-server-page .uc-grid,
.armely-sql-server-page .testi-grid,
.armely-sql-server-page .why-two-col,
.armely-sql-server-page .pathway-grid {
  margin-top: 56px;
}
.armely-sql-server-page .deliver-icon,
.armely-sql-server-page .uc-icon,
.armely-sql-server-page .why-icon,
.armely-sql-server-page .symptom-icon,
.armely-sql-server-page .what-card-icon,
.armely-sql-server-page .cov-item-icon,
.armely-sql-server-page .cover-icon,
.armely-sql-server-page .product-card-icon,
.armely-sql-server-page .cap-icon,
.armely-sql-server-page .workload-pill-icon,
.armely-sql-server-page .decision-icon,
.armely-sql-server-page .sign-icon,
.armely-sql-server-page .pathway-icon,
.armely-sql-server-page .onelake-callout-icon,
.armely-sql-server-page .vs-callout-icon {
  color: var(--blue);
  font-size: 1.1rem;
  line-height: 1;
}
.armely-sql-server-page .deliver-icon,
.armely-sql-server-page .uc-icon,
.armely-sql-server-page .why-icon {
  width: 48px;
  height: 48px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-sql-server-page .deliver-card,
.armely-sql-server-page .uc-card,
.armely-sql-server-page .testi-card,
.armely-sql-server-page .tier-card,
.armely-sql-server-page .cover-card,
.armely-sql-server-page .pathway-card,
.armely-sql-server-page .partner-block,
.armely-sql-server-page .cta-form {
  background: linear-gradient(180deg, #ffffff 0%, #f9fbfe 100%);
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
@media (max-width: 900px) {
  .armely-sql-server-page .hero { padding: 118px 24px 76px; }
  .armely-sql-server-page section:not(.hero) > .section-inner > .section-title,
  .armely-sql-server-page .cta-inner > div > .section-title { max-width: 100%; }
}
</style>
<div class="armely-sql-server-page">
<!-- NAV -->


<!-- HERO -->
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-eyebrow">
    <span class="eyebrow-badge">Microsoft SQL Server</span>
    <span class="eyebrow-partner">Delivered by a certified Microsoft partner</span>
  </div>
  <h1>Your database,<br>running the way<br><span class="hl">your business demands.</span></h1>
  <p class="hero-sub">Armely designs, implements, migrates, and manages Microsoft SQL Server environments so your critical data is fast, secure, always available, and ready for AI workloads.</p>
  <div class="hero-actions">
    <a href="#contact" class="btn-primary">Book a Free Assessment</a>
    <a href="#what-we-deliver" class="btn-outline">See What We Do</a>
  </div>
  <div class="hero-trust">
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>On-premises, cloud, or hybrid</strong> deployment</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>AI-ready</strong> with built-in vector search</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">Connects natively to <strong>Microsoft Fabric</strong> and Azure</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>30-year</strong> Microsoft platform heritage</span>
    </div>
  </div>
</section>

<!-- EOL URGENCY BANNER -->
<div class="eol-banner">
  <div class="eol-banner-inner">
    <div class="eol-alert">
      <span class="eol-icon"><i class="fa-solid fa-circle-nodes" aria-hidden="true"></i></span>
      <div class="eol-text">
        <strong>End-of-support deadlines are approaching.</strong> Several widely deployed versions of SQL Server are reaching or have passed their extended support end dates, leaving databases exposed to security vulnerabilities and compliance risk. If your organization is running an older SQL Server version, now is the time to act.
      </div>
      <a href="#contact" class="eol-link">Check Your Version</a>
    </div>
  </div>
</div>

<!-- WHAT IS SQL SERVER -->
<section class="intro">
  <div class="section-inner">
    <div class="intro-grid">
      <div>
        <div class="section-eyebrow">What is Microsoft SQL Server?</div>
        <h2 class="section-title">The enterprise database platform behind some of the world's most demanding workloads.</h2>
        <p class="section-body">Microsoft SQL Server is a relational database management system used by organizations of every size to store, manage, and analyze business-critical data. The latest release extends that foundation with built-in AI capabilities, native vector search, real-time event streaming, and deep integration with Microsoft Fabric and Azure, making it one of the most capable data platforms available on-premises or in the cloud.</p>
        <div class="cap-grid">
          <div class="cap-card">
            <span class="cap-card-icon"><i class="fa-solid fa-database" aria-hidden="true"></i></span>
            <div class="cap-card-title">Built-In AI and Vector Search</div>
            <div class="cap-card-desc">Run AI models and vector search directly in T-SQL. Build retrieval-augmented generation apps on your existing SQL data without moving it to a separate system.</div>
          </div>
          <div class="cap-card">
            <span class="cap-card-icon"><i class="fa-solid fa-database" aria-hidden="true"></i></span>
            <div class="cap-card-title">Enterprise-Grade Security</div>
            <div class="cap-card-desc">Always Encrypted, row-level security, dynamic data masking, and transparent data encryption protect sensitive data at rest and in transit.</div>
          </div>
          <div class="cap-card">
            <span class="cap-card-icon"><i class="fa-solid fa-database" aria-hidden="true"></i></span>
            <div class="cap-card-title">High Availability</div>
            <div class="cap-card-desc">Always On Availability Groups, failover clustering, and automated backups keep your databases online even during planned maintenance or unexpected failures.</div>
          </div>
          <div class="cap-card">
            <span class="cap-card-icon"><i class="fa-solid fa-database" aria-hidden="true"></i></span>
            <div class="cap-card-title">Fabric and Azure Integration</div>
            <div class="cap-card-desc">SQL Server connects natively to Microsoft Fabric, Azure Synapse, and Power BI, making it the on-premises anchor for a hybrid analytics architecture.</div>
          </div>
        </div>
      </div>
      <div>
        <div class="deploy-card">
          <div class="deploy-header">
            <div class="deploy-dots"><span></span><span></span><span></span></div>
            <span class="deploy-title">Deployment Options</span>
          </div>
          <div class="deploy-body">
            <div class="deploy-option active">
              <span class="deploy-option-icon"><i class="fa-solid fa-circle-nodes" aria-hidden="true"></i></span>
              <div>
                <div class="deploy-option-title">On-Premises</div>
                <div class="deploy-option-desc">Full control over hardware, performance tuning, and data residency. Best for regulated industries and latency-sensitive workloads.</div>
              </div>
            </div>
            <div class="deploy-option">
              <span class="deploy-option-icon"><i class="fa-solid fa-circle-nodes" aria-hidden="true"></i></span>
              <div>
                <div class="deploy-option-title">SQL Server on Azure VM</div>
                <div class="deploy-option-desc">Lift your existing SQL Server to Azure with full feature parity. Eliminates hardware management while preserving your current applications and processes.</div>
              </div>
            </div>
            <div class="deploy-option">
              <span class="deploy-option-icon"><i class="fa-solid fa-circle-nodes" aria-hidden="true"></i></span>
              <div>
                <div class="deploy-option-title">Azure SQL Managed Instance</div>
                <div class="deploy-option-desc">Near-complete SQL Server compatibility as a fully managed PaaS service. Ideal for migrations that want cloud economics without application rewrites.</div>
              </div>
            </div>
            <div class="deploy-option">
              <span class="deploy-option-icon"><i class="fa-solid fa-circle-nodes" aria-hidden="true"></i></span>
              <div>
                <div class="deploy-option-title">Hybrid with Azure Arc</div>
                <div class="deploy-option-desc">Manage on-premises SQL Server instances through the Azure portal with unified governance, billing, and security policies across environments.</div>
              </div>
            </div>
            <div class="deploy-ai-callout">
              <span style="font-size:1.2rem;"><i class="fa-solid fa-robot" aria-hidden="true"></i></span>
              <div class="deploy-ai-callout-text"><strong>GitHub Copilot in SSMS</strong> is now in preview, bringing AI-assisted query writing, schema exploration, and performance recommendations directly into your SQL Server Management Studio workflow.</div>
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
    <h2 class="section-title">SQL Server expertise across the full database lifecycle.</h2>
    <p class="section-body">From a first-time SQL Server installation to a complex migration off aging infrastructure, Armely covers every stage of your database environment with certified engineers and a structured delivery methodology.</p>
    <div class="delivers-grid">
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Environment Assessment</div>
        <div class="deliver-desc">We audit your existing SQL Server environment, including instance configurations, database sizes, query performance, backup strategies, and support status. You receive a clear picture of your current risk profile and a prioritized action plan.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Installation and Configuration</div>
        <div class="deliver-desc">New SQL Server deployments configured to production standards from day one, including memory allocation, tempdb optimization, backup schedules, maintenance plans, and security hardening aligned to Microsoft best practices.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Upgrades and Migration</div>
        <div class="deliver-desc">We plan and execute SQL Server upgrades and migrations, including end-of-support remediation, lift-and-shift to Azure, and migrations to Azure SQL Managed Instance, with compatibility testing and minimal downtime cutover plans.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Performance Tuning</div>
        <div class="deliver-desc">We identify and resolve performance bottlenecks through query analysis, index optimization, execution plan review, and wait statistics analysis. Slow queries and blocked processes are diagnosed with precision, not guesswork.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Security and Compliance</div>
        <div class="deliver-desc">We implement SQL Server security controls including role-based access, data classification, auditing, encryption at rest and in transit, and vulnerability assessments to meet HIPAA, SOC 2, and other compliance requirements.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Managed DBA Services</div>
        <div class="deliver-desc">Ongoing database administration on a managed basis, covering monitoring, alerting, patching, backup verification, capacity planning, and a dedicated Armely contact available when issues arise.</div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNEY -->
<section class="journey" id="journey">
  <div class="section-inner">
    <div class="section-eyebrow">The Armely SQL Server Journey</div>
    <h2 class="section-title">From assessment to production, on a timeline you can plan around.</h2>
    <p class="section-body">Whether you need an urgent upgrade before an end-of-support deadline or a planned migration to the cloud, we deliver against a structured timeline with clear milestones and no surprises at go-live.</p>
    <div class="steps-row">
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-title">Discovery Assessment</div>
        <div class="step-desc">We audit your SQL Server estate, identify version risk, review performance and security posture, and document your business-critical databases.</div>
        <span class="step-tag">Free</span>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-title">Planning and Design</div>
        <div class="step-desc">We design your target environment, whether that is an in-place upgrade, a new server build, or a migration to Azure, and confirm the licensing approach at partner pricing.</div>
        <span class="step-tag">1 week</span>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-title">Build and Test</div>
        <div class="step-desc">The target environment is built and configured. Databases are migrated or upgraded in a staging environment and validated for application compatibility.</div>
        <span class="step-tag">Weeks 2-4</span>
      </div>
      <div class="step">
        <div class="step-num">04</div>
        <div class="step-title">Cutover and Validation</div>
        <div class="step-desc">Production cutover is executed during a planned maintenance window with rollback procedures in place. Armely validates performance and availability post-cutover.</div>
        <span class="step-tag">Week 5</span>
      </div>
      <div class="step">
        <div class="step-num">05</div>
        <div class="step-title">Managed Support</div>
        <div class="step-desc">Ongoing monitoring, patching, performance management, and a dedicated Armely DBA contact for day-to-day database operations and strategic planning.</div>
        <span class="step-tag">Ongoing</span>
      </div>
    </div>
  </div>
</section>

<!-- USE CASES -->
<section class="usecases">
  <div class="section-inner">
    <div class="section-eyebrow">Common Engagements</div>
    <h2 class="section-title">The database situations we see most, and solve every time.</h2>
    <p class="section-body">Every SQL Server environment is different, but the business problems that drive organizations to call Armely tend to follow recognizable patterns.</p>
    <div class="uc-grid">
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">End-of-Support Remediation</div>
        <div class="uc-desc">Running a version of SQL Server past its extended support end date exposes your organization to unpatched security vulnerabilities. We assess the risk, plan the path forward, and execute the upgrade or migration before a breach or audit finding forces your hand.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Slow Database Performance</div>
        <div class="uc-desc">Application slowdowns, report timeouts, and blocked transactions often trace back to missing indexes, poor query plans, or misconfigured memory settings. We diagnose the root cause and resolve it, not just the symptom.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Cloud Migration</div>
        <div class="uc-desc">We migrate SQL Server workloads to Azure, including lift-and-shift to Azure VMs, migration to Azure SQL Managed Instance, and hybrid configurations with Azure Arc, with full compatibility testing before cutover.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Security and Compliance Gaps</div>
        <div class="uc-desc">We conduct SQL Server vulnerability assessments against HIPAA, SOC 2, and CIS benchmark standards, implement required controls, and produce the audit documentation your compliance team needs.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Connect SQL Server to Analytics</div>
        <div class="uc-desc">We configure SQL Server as the on-premises data source for Microsoft Fabric, Power BI, and Azure Synapse, establishing the data pipelines that give your analytics team access to live operational data.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">AI Readiness on Existing Data</div>
        <div class="uc-desc">The latest SQL Server release introduces native vector search and AI model integration directly in T-SQL. We help organizations evaluate and implement these capabilities on their existing SQL Server data estate without a full platform migration.</div>
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
        <p class="testi-body">We had been running an unsupported version of SQL Server for longer than we should have, and a compliance audit made that very clear. Armely completed the assessment, designed the migration path, and executed the cutover without any production downtime. The audit finding was closed within the quarter.</p>
        <div class="testi-footer">
          <div class="testi-avatar">IT</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">IT Director</div>
            <div class="testi-role">Healthcare Network, Kansas City</div>
          </div>
        </div>
      </div>

      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">Our finance application was timing out on month-end reporting runs and our internal team had spent months trying to resolve it. Armely came in, identified the core query performance issues within two days of the engagement, and we saw material improvements in report run times within the week.</p>
        <div class="testi-footer">
          <div class="testi-avatar">VP</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">VP of Technology</div>
            <div class="testi-role">Financial Services Firm, Texas</div>
          </div>
        </div>
      </div>

      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">We needed to move a complex SQL Server environment to Azure without disrupting our student information systems during an active semester. Armely planned the migration in detail, ran full compatibility testing, and executed the cutover over a single weekend. Everything was running in Azure by Monday morning.</p>
        <div class="testi-footer">
          <div class="testi-avatar">CTO</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Chief Technology Officer</div>
            <div class="testi-role">Educational Institution, Nebraska</div>
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
        <h2 class="section-title">Database expertise backed by real delivery experience.</h2>
        <p class="section-body">SQL Server is the engine behind some of your most critical business processes. The partner you choose to manage or migrate it needs proven credentials and a track record in environments where getting it wrong is not an option.</p>
        <ul class="why-list">
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Certified SQL Server Engineers</div>
              <div class="why-item-desc">Our engineers hold Microsoft certifications in SQL Server administration and Azure database services, with production experience across on-premises, hybrid, and cloud deployments.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Experience in Regulated Industries</div>
              <div class="why-item-desc">We have delivered database projects for Swope Health Systems and the University of Nebraska Medical Center, where HIPAA compliance, audit readiness, and zero-downtime requirements are non-negotiable.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Full Microsoft Stack Coverage</div>
              <div class="why-item-desc">SQL Server does not exist in isolation. Armely covers the surrounding Microsoft ecosystem, including Azure, Microsoft Fabric, Power BI, and Microsoft 365, so your database architecture is designed to work with your analytics and application stack from the start.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Licensing at Partner Pricing</div>
              <div class="why-item-desc">As a Microsoft-authorized CSP partner, we source SQL Server and Azure SQL licensing at rates not available through direct purchase, and we help you select the right edition and deployment model for your workload and budget.</div>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <div class="partner-block">
          <div class="partner-block-top">
            <div class="partner-label">Microsoft Authorized Partner</div>
            <p class="partner-text">Armely's Microsoft partnership gives us access to SQL Server licensing, technical resources, and Azure migration support that direct customers cannot access independently. That means better pricing, access to Microsoft engineering support on complex migrations, and a delivery approach aligned with Microsoft's own recommended frameworks.</p>
          </div>
          <div class="partner-stats">
            <div class="p-stat">
              <div class="p-stat-num">30<span>+</span></div>
              <div class="p-stat-label">years of SQL Server platform history and enterprise reliability</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">4</div>
              <div class="p-stat-label">deployment options: on-premises, Azure VM, Managed Instance, and hybrid Arc</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">AI</div>
              <div class="p-stat-label">vector search and model integration built natively into the latest release</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">1</div>
              <div class="p-stat-label">consistent SQL engine across on-premises and cloud deployments</div>
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
      <h2 class="section-title">Let's review your SQL Server environment.</h2>
      <p class="section-body">Book a free 30-minute discovery call. We will review your current SQL Server deployment, identify any end-of-support risk or performance concerns, and provide a clear recommendation and pricing proposal with no obligation.</p>
      <div style="margin-top: 28px; display: flex; flex-direction: column; gap: 12px;">
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Free assessment, no commitment required</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">End-of-support risk report included at no charge</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Response within one business day</span>
        </div>
      </div>
    </div>
    <div class="cta-form">
      <div class="form-title">Book Your Free Assessment</div>
      <div class="form-sub">Tell us about your current SQL Server environment.</div>
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
        <label>Primary Need</label>
        <select>
          <option value="">Select...</option>
          <option>Upgrade or migrate an older SQL Server</option>
          <option>Resolve performance or query issues</option>
          <option>Migrate SQL Server to Azure</option>
          <option>Security audit or compliance remediation</option>
          <option>Ongoing managed DBA support</option>
          <option>Connect SQL Server to Fabric or Power BI</option>
          <option>Not sure, need an assessment first</option>
        </select>
      </div>
      <button class="form-submit">Request Free Assessment</button>
      <div class="form-note">No spam. No sales pressure. Just a useful conversation.</div>
    </div>
  </div>
</section>

<!-- FOOTER -->
</div>

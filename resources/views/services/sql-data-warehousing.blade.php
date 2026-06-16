@section('title', 'SQL Server Migration and Management | Armely')

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
  }

.armely-sql-server-page { scroll-behavior: smooth; }
.armely-sql-server-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* NAV */
.armely-sql-server-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-sql-server-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-sql-server-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-sql-server-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-sql-server-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-sql-server-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-sql-server-page .nav-links a:hover { color: #fff; }
.armely-sql-server-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-sql-server-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* HERO */
.armely-sql-server-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-sql-server-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-sql-server-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-sql-server-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-sql-server-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-sql-server-page .hero h1 { font-size: clamp(1.75rem, 3.2vw, 2.7rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-sql-server-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-sql-server-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.8; }
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

  /* SECTIONS */
.armely-sql-server-page section { padding: 96px 56px; }
.armely-sql-server-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-sql-server-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-sql-server-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 640px; }
.armely-sql-server-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* AI SPECTRUM */
.armely-sql-server-page .spectrum { background: var(--navy-mid); }
.armely-sql-server-page .spectrum-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-sql-server-page .spectrum-row { display: flex; flex-direction: column; gap: 10px; margin-top: 32px; }
.armely-sql-server-page .spectrum-level { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; transition: border-color 0.2s; }
.armely-sql-server-page .spectrum-level:hover { border-color: rgba(41,78,139,0.3); }
.armely-sql-server-page .spectrum-level.highlight { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-sql-server-page .spectrum-num { font-size: 0.68rem; font-weight: 800; color: var(--blue); background: var(--blue-dim2); border-radius: 4px; padding: 2px 7px; flex-shrink: 0; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.08em; }
.armely-sql-server-page .spectrum-content-title { font-size: 0.875rem; font-weight: 700; color: #1A2540; margin-bottom: 3px; }
.armely-sql-server-page .spectrum-content-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }

  /* Platform visual */
.armely-sql-server-page .platform-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-sql-server-page .platform-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-sql-server-page .platform-dots { display: flex; gap: 6px; }
.armely-sql-server-page .platform-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-sql-server-page .platform-header-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-sql-server-page .platform-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.armely-sql-server-page .plat-band { border-radius: 9px; padding: 13px 16px; }
.armely-sql-server-page .plat-band-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
.armely-sql-server-page .plat-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.armely-sql-server-page .plat-chip { font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.armely-sql-server-page .band-tools { background: var(--blue-dim); }
.armely-sql-server-page .band-tools .plat-band-label { color: var(--blue); }
.armely-sql-server-page .band-tools .plat-chip { background: var(--blue-dim2); color: var(--blue); }
.armely-sql-server-page .band-data { background: rgba(41,78,139,0.05); }
.armely-sql-server-page .band-data .plat-band-label { color: var(--blue); }
.armely-sql-server-page .band-data .plat-chip { background: rgba(41,78,139,0.1); color: var(--blue); }
.armely-sql-server-page .band-gov { background: var(--blue); }
.armely-sql-server-page .band-gov .plat-band-label { color: rgba(255,255,255,0.7); }
.armely-sql-server-page .band-gov .plat-chip { background: rgba(255,255,255,0.15); color: #fff; }
.armely-sql-server-page .band-arrow { text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2px 0; }

  /* VIBE CODING CALLOUT */
.armely-sql-server-page .vibe-section { background: var(--navy); }
.armely-sql-server-page .vibe-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: start; }
.armely-sql-server-page .vibe-left { }
.armely-sql-server-page .vibe-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-sql-server-page .vibe-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
.armely-sql-server-page .vibe-card-icon { font-size: 1.4rem; }
.armely-sql-server-page .vibe-card-title { font-size: 1rem; font-weight: 700; color: #1A2540; }
.armely-sql-server-page .vibe-card-subtitle { font-size: 0.78rem; color: var(--text-muted); }
.armely-sql-server-page .vibe-card-body { padding: 24px; }
.armely-sql-server-page .vibe-risk { display: flex; align-items: flex-start; gap: 12px; padding: 14px 0; border-bottom: 1px solid var(--border); }
.armely-sql-server-page .vibe-risk:last-child { border-bottom: none; }
.armely-sql-server-page .vibe-risk-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
.armely-sql-server-page .vibe-risk-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 2px; }
.armely-sql-server-page .vibe-risk-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }
.armely-sql-server-page .vibe-right { }
.armely-sql-server-page .vibe-answer-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 28px; margin-bottom: 12px; }
.armely-sql-server-page .vibe-answer-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--blue); margin-bottom: 10px; }
.armely-sql-server-page .vibe-answer-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.75; }

  /* DELIVERS */
.armely-sql-server-page .delivers { background: var(--navy-mid); }
.armely-sql-server-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-sql-server-page .deliver-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-sql-server-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-sql-server-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-sql-server-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-sql-server-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-sql-server-page .journey { background: var(--navy); }
.armely-sql-server-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-sql-server-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-sql-server-page .step:last-child { border-right: none; }
.armely-sql-server-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-sql-server-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-sql-server-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-sql-server-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* USE CASES */
.armely-sql-server-page .usecases { background: var(--navy-mid); }
.armely-sql-server-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-sql-server-page .uc-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-sql-server-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-sql-server-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-sql-server-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-sql-server-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* TESTIMONIALS */
.armely-sql-server-page .testimonials { background: var(--navy); padding: 96px 56px; }
.armely-sql-server-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-sql-server-page .testi-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-sql-server-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-sql-server-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-sql-server-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-sql-server-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-sql-server-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-sql-server-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-sql-server-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY ARMELY */
.armely-sql-server-page .why { background: var(--navy-mid); }
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

  /* CTA */
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

  /* FOOTER */
.armely-sql-server-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-sql-server-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-sql-server-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-sql-server-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-sql-server-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-sql-server-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-sql-server-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* RESPONSIVE */
  @media (max-width: 900px) {
.armely-sql-server-page nav { padding: 16px 24px; }
.armely-sql-server-page .nav-links { display: none; }
.armely-sql-server-page section { padding: 72px 24px; }
.armely-sql-server-page .hero { padding: 110px 24px 72px; }
.armely-sql-server-page .spectrum-grid, .armely-sql-server-page .vibe-two-col, .armely-sql-server-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
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
.armely-sql-server-page .delivers-grid, .armely-sql-server-page .uc-grid { grid-template-columns: 1fr; }
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


/* Modern layout update: tighter first section and SVG icon system */
.armely-sql-server-page .hero {
  min-height: auto;
  padding: 128px 32px 86px;
  isolation: isolate;
}
.armely-sql-server-page .hero-inner {
  width: min(1160px, 100%);
  margin: 0 auto;
  display: grid;
  grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.75fr);
  align-items: center;
  gap: 56px;
  position: relative;
  z-index: 1;
}
.armely-sql-server-page .hero-copy { max-width: 760px; }
.armely-sql-server-page .hero h1 { max-width: 760px; margin-bottom: 20px; }
.armely-sql-server-page .hero-sub { max-width: 640px; margin-bottom: 30px; font-size: 1.02rem; line-height: 1.72; }
.armely-sql-server-page .hero-actions { margin-bottom: 34px; }
.armely-sql-server-page .hero-trust {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  padding-top: 0;
  border-top: 0;
  max-width: 720px;
}
.armely-sql-server-page .hero .trust-item {
  align-items: flex-start;
  background: rgba(255,255,255,0.10);
  border: 1px solid rgba(255,255,255,0.16);
  border-radius: 14px;
  padding: 14px 15px;
  backdrop-filter: blur(10px);
}
.armely-sql-server-page .hero .trust-dot {
  width: 22px;
  height: 22px;
  border-radius: 999px;
  background: rgba(255,255,255,0.16);
  border: 1px solid rgba(255,255,255,0.24);
  position: relative;
  margin-top: 1px;
}
.armely-sql-server-page .hero .trust-dot::after {
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
.armely-sql-server-page .hero .trust-text { color: rgba(255,255,255,0.78); line-height: 1.5; }
.armely-sql-server-page .hero-visual {
  min-height: 420px;
  border-radius: 28px;
  position: relative;
  background: linear-gradient(145deg, rgba(255,255,255,0.16), rgba(255,255,255,0.05));
  border: 1px solid rgba(255,255,255,0.18);
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.18), 0 28px 70px rgba(6, 22, 48, 0.24);
  overflow: hidden;
}
.armely-sql-server-page .hero-visual::before {
  content: '';
  position: absolute;
  inset: 36px;
  border: 1px solid rgba(255,255,255,0.14);
  border-radius: 999px;
}
.armely-sql-server-page .hero-visual::after {
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
.armely-sql-server-page .hero-orbit span {
  position: absolute;
  width: 52px;
  height: 52px;
  border-radius: 16px;
  background: rgba(255,255,255,0.14);
  border: 1px solid rgba(255,255,255,0.22);
}
.armely-sql-server-page .hero-orbit span:nth-child(1) { left: 56px; top: 88px; }
.armely-sql-server-page .hero-orbit span:nth-child(2) { right: 72px; top: 138px; }
.armely-sql-server-page .hero-orbit span:nth-child(3) { left: 48%; bottom: 76px; }
.armely-sql-server-page .hero-visual-card {
  position: absolute;
  z-index: 2;
  width: 230px;
  padding: 18px;
  border-radius: 18px;
  background: rgba(255,255,255,0.92);
  box-shadow: 0 24px 54px rgba(9, 31, 63, 0.22);
}
.armely-sql-server-page .hero-visual-card.top { top: 42px; right: 30px; }
.armely-sql-server-page .hero-visual-card.bottom { bottom: 46px; left: 30px; }
.armely-sql-server-page .hero-visual-label {
  display: block;
  font-size: 0.78rem;
  font-weight: 700;
  color: #162b49;
  margin-bottom: 12px;
}
.armely-sql-server-page .hero-visual-line {
  display: block;
  height: 8px;
  width: 100%;
  border-radius: 999px;
  background: rgba(47, 85, 151, 0.16);
  margin-top: 8px;
}
.armely-sql-server-page .hero-visual-line.short { width: 68%; }
.armely-sql-server-page .icon-svg {
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
.armely-sql-server-page .vibe-card-icon,
.armely-sql-server-page .vibe-risk-icon,
.armely-sql-server-page .deliver-icon,
.armely-sql-server-page .uc-icon,
.armely-sql-server-page .why-icon {
  color: var(--blue);
}
.armely-sql-server-page .vibe-card-icon,
.armely-sql-server-page .vibe-risk-icon {
  width: 42px;
  height: 42px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-sql-server-page .vibe-risk-icon { width: 36px; height: 36px; border-radius: 10px; }
.armely-sql-server-page .deliver-icon .icon-svg,
.armely-sql-server-page .uc-icon .icon-svg,
.armely-sql-server-page .why-icon .icon-svg { width: 23px; height: 23px; }
.armely-sql-server-page .uc-icon {
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
  .armely-sql-server-page .hero-inner { grid-template-columns: 1fr; gap: 32px; }
  .armely-sql-server-page .hero-visual { min-height: 320px; }
}
@media (max-width: 680px) {
  .armely-sql-server-page .hero { padding: 104px 22px 64px; }
  .armely-sql-server-page .hero-trust { grid-template-columns: 1fr; }
  .armely-sql-server-page .hero-visual { display: none; }
  .armely-sql-server-page .hero-actions a { width: 100%; text-align: center; }
}

/* Focused cleanup for the Generative AI detail page */
.armely-sql-server-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  align-items: center;
  background: #173b67;
  border-radius: 0;
}
.armely-sql-server-page .hero::after,
.armely-sql-server-page .hero-bg-glow,
.armely-sql-server-page .hero-visual {
  display: none;
}
.armely-sql-server-page .hero-inner {
  width: min(1040px, 100%);
  display: block;
}
.armely-sql-server-page .hero-copy {
  max-width: 760px;
}
.armely-sql-server-page .hero-eyebrow {
  margin-bottom: 18px;
}
.armely-sql-server-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.72);
}
.armely-sql-server-page .eyebrow-partner,
.armely-sql-server-page .hero-trust {
  display: none;
}
.armely-sql-server-page .hero h1 {
  max-width: 760px;
  margin-bottom: 22px;
}
.armely-sql-server-page .hero-sub {
  max-width: 680px;
  margin-bottom: 34px;
}
.armely-sql-server-page .hero-actions {
  margin-bottom: 0;
}
.armely-sql-server-page .hero .btn-primary,
.armely-sql-server-page .hero .btn-outline {
  border-radius: 0;
}
.armely-sql-server-page .vibe-section {
  background: #fff;
  padding: 84px 56px;
}
.armely-sql-server-page .vibe-section .section-inner {
  max-width: 920px;
}
.armely-sql-server-page .vibe-section .section-title,
.armely-sql-server-page .vibe-section .section-body {
  max-width: 820px;
}
.armely-sql-server-page .vibe-section .section-body {
  margin-bottom: 28px;
}
.armely-sql-server-page .vibe-two-col {
  grid-template-columns: 1fr;
  gap: 18px;
}
.armely-sql-server-page .vibe-card,
.armely-sql-server-page .vibe-answer-card {
  border-radius: 0;
  box-shadow: none;
}
.armely-sql-server-page .vibe-card-header {
  padding: 18px 20px;
}
.armely-sql-server-page .vibe-card-body {
  padding: 8px 20px 12px;
}
.armely-sql-server-page .vibe-risk {
  padding: 12px 0;
}
.armely-sql-server-page .vibe-right {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
}
.armely-sql-server-page .vibe-answer-card {
  margin-bottom: 0;
  padding: 20px;
  background: #f7f9fc;
}
.armely-sql-server-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-sql-server-page .why > .section-inner > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-sql-server-page section:not(.hero) > .section-inner > .section-title,
.armely-sql-server-page .why > .section-inner > .section-title {
  max-width: 880px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-sql-server-page section:not(.hero) > .section-inner > .section-body,
.armely-sql-server-page .why > .section-inner > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-sql-server-page .spectrum-grid,
.armely-sql-server-page .delivers-grid,
.armely-sql-server-page .steps-row,
.armely-sql-server-page .uc-grid,
.armely-sql-server-page .testi-grid,
.armely-sql-server-page .why-two-col {
  margin-top: 56px;
}
.armely-sql-server-page .why-two-col {
  align-items: stretch;
}
.armely-sql-server-page .why-list {
  margin-top: 0;
}
.armely-sql-server-page .why-list,
.armely-sql-server-page .partner-block {
  height: 100%;
}
@media (max-width: 900px) {
  .armely-sql-server-page .hero {
    padding: 118px 24px 76px;
  }
  .armely-sql-server-page .vibe-section {
    padding: 72px 24px;
  }
  .armely-sql-server-page .vibe-right {
    grid-template-columns: 1fr;
  }
}



/* Final compact modern cleanup */
.armely-sql-server-page {
  --blue: #2f5597;
  --blue-lt: #4779bd;
  --navy-mid: #f6f8fc;
  --navy-card: #ffffff;
  --text-body: #334155;
  --text-muted: #667085;
  --border: rgba(47, 85, 151, 0.14);
}
.armely-sql-server-page .hero {
  min-height: auto !important;
  padding: 86px 56px 70px !important;
  background: linear-gradient(135deg, #173b67 0%, #234f86 100%) !important;
  border-radius: 0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}
.armely-sql-server-page .hero::after,
.armely-sql-server-page .hero-bg-glow,
.armely-sql-server-page .hero-visual {
  display: none !important;
}
.armely-sql-server-page .hero-inner {
  width: min(1120px, 100%) !important;
  margin: 0 auto !important;
  display: block !important;
}
.armely-sql-server-page .hero-copy {
  max-width: 860px !important;
}
.armely-sql-server-page .hero-eyebrow {
  margin-bottom: 18px !important;
}
.armely-sql-server-page .eyebrow-badge {
  display: inline-flex !important;
  background: rgba(255,255,255,0.10) !important;
  border: 1px solid rgba(255,255,255,0.22) !important;
  border-radius: 999px !important;
  padding: 7px 14px !important;
  color: rgba(255,255,255,0.88) !important;
}
.armely-sql-server-page .eyebrow-partner {
  display: inline-flex !important;
  color: rgba(255,255,255,0.66) !important;
}
.armely-sql-server-page .hero h1 {
  max-width: 900px !important;
  margin-bottom: 18px !important;
  font-size: clamp(1.75rem, 3.2vw, 2.7rem);
  line-height: 1.05 !important;
  letter-spacing: -0.04em !important;
}
.armely-sql-server-page .hero-sub {
  max-width: 760px !important;
  margin-bottom: 28px !important;
  font-size: 1rem !important;
  line-height: 1.7 !important;
}
.armely-sql-server-page .hero-actions {
  margin-bottom: 0 !important;
  gap: 12px !important;
}
.armely-sql-server-page .hero .btn-primary,
.armely-sql-server-page .hero .btn-outline,
.armely-sql-server-page .btn-primary,
.armely-sql-server-page .btn-outline,
.armely-sql-server-page .form-submit {
  border-radius: 8px !important;
}
.armely-sql-server-page section {
  padding: 68px 56px !important;
}
.armely-sql-server-page .section-inner {
  max-width: 1120px !important;
}
.armely-sql-server-page .section-eyebrow {
  margin-bottom: 10px !important;
}
.armely-sql-server-page .section-title {
  margin-bottom: 14px !important;
}
.armely-sql-server-page .section-body {
  margin-bottom: 28px !important;
  line-height: 1.65 !important;
}
.armely-sql-server-page .spectrum-grid,
.armely-sql-server-page .vibe-two-col,
.armely-sql-server-page .delivers-grid,
.armely-sql-server-page .steps-row,
.armely-sql-server-page .uc-grid,
.armely-sql-server-page .testi-grid,
.armely-sql-server-page .why-two-col {
  margin-top: 34px !important;
  gap: 20px !important;
}
.armely-sql-server-page .spectrum-grid,
.armely-sql-server-page .why-two-col {
  grid-template-columns: 1fr 1fr !important;
}
.armely-sql-server-page .spectrum-row {
  margin-top: 0 !important;
  gap: 10px !important;
}
.armely-sql-server-page .spectrum-level,
.armely-sql-server-page .deliver-card,
.armely-sql-server-page .uc-card,
.armely-sql-server-page .testi-card,
.armely-sql-server-page .vibe-answer-card,
.armely-sql-server-page .partner-block,
.armely-sql-server-page .cta-form,
.armely-sql-server-page .vibe-card,
.armely-sql-server-page .platform-card {
  border-radius: 14px !important;
  box-shadow: 0 14px 36px rgba(18, 47, 82, 0.08) !important;
}
.armely-sql-server-page .deliver-card,
.armely-sql-server-page .uc-card,
.armely-sql-server-page .testi-card {
  padding: 24px 22px !important;
}
.armely-sql-server-page .deliver-icon,
.armely-sql-server-page .uc-icon,
.armely-sql-server-page .why-icon,
.armely-sql-server-page .vibe-card-icon,
.armely-sql-server-page .vibe-risk-icon {
  border-radius: 12px !important;
  color: var(--blue) !important;
}
.armely-sql-server-page .vibe-section {
  padding: 68px 56px !important;
  background: #fff !important;
}
.armely-sql-server-page .vibe-section .section-inner {
  max-width: 1120px !important;
}
.armely-sql-server-page .vibe-two-col {
  grid-template-columns: 1fr 1fr !important;
  align-items: stretch !important;
}
.armely-sql-server-page .vibe-card-body {
  padding: 18px 20px !important;
}
.armely-sql-server-page .vibe-right {
  display: flex !important;
  flex-direction: column !important;
  gap: 12px !important;
}
.armely-sql-server-page .vibe-answer-card {
  padding: 20px !important;
  background: #f8fafd !important;
}
.armely-sql-server-page .steps-row {
  border-radius: 14px !important;
  overflow: hidden !important;
}
.armely-sql-server-page .step {
  padding: 24px 18px !important;
}
.armely-sql-server-page .step-num {
  font-size: 2rem !important;
  margin-bottom: 10px !important;
}
.armely-sql-server-page .why-list {
  margin-top: 0 !important;
}
.armely-sql-server-page .why-list li {
  padding: 16px 0 !important;
}
.armely-sql-server-page .partner-block-top,
.armely-sql-server-page .p-stat {
  padding: 22px !important;
}
.armely-sql-server-page .cta-inner {
  padding: 68px 56px !important;
  gap: 40px !important;
}
@media (max-width: 900px) {
  .armely-sql-server-page .hero { padding: 88px 24px 58px !important; }
  .armely-sql-server-page section,
  .armely-sql-server-page .vibe-section { padding: 56px 24px !important; }
  .armely-sql-server-page .spectrum-grid,
  .armely-sql-server-page .vibe-two-col,
  .armely-sql-server-page .why-two-col { grid-template-columns: 1fr !important; }
  .armely-sql-server-page .delivers-grid,
  .armely-sql-server-page .uc-grid { grid-template-columns: 1fr 1fr !important; }
  .armely-sql-server-page .cta-inner { padding: 56px 24px !important; grid-template-columns: 1fr !important; }
}
@media (max-width: 600px) {
  .armely-sql-server-page .hero h1 { font-size: clamp(1.75rem, 3.2vw, 2.7rem); }
  .armely-sql-server-page .hero-eyebrow { flex-direction: column !important; align-items: flex-start !important; gap: 8px !important; }
  .armely-sql-server-page .hero-actions a { width: 100% !important; text-align: center !important; }
  .armely-sql-server-page .delivers-grid,
  .armely-sql-server-page .uc-grid { grid-template-columns: 1fr !important; }
}



.armely-sql-server-page .cr-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:28px; margin-bottom:28px; }
.armely-sql-server-page .cr-card { background:#fff; border:1px solid var(--border); border-radius:14px; padding:22px; box-shadow:0 14px 36px rgba(18,47,82,0.08); }
.armely-sql-server-page .cr-label { display:flex; align-items:center; gap:9px; margin-bottom:10px; }
.armely-sql-server-page .cr-check { width:20px; height:20px; display:flex; align-items:center; justify-content:center; background:var(--blue-dim); border:1px solid var(--blue-dim2); border-radius:50%; flex-shrink:0; color:var(--blue); }
.armely-sql-server-page .cr-check .icon-svg { width:11px; height:11px; stroke-width:3; }
.armely-sql-server-page .cr-industry { font-size:0.875rem; font-weight:700; color:#162b49; }
.armely-sql-server-page .cr-desc { font-size:0.84rem; color:var(--text-muted); line-height:1.65; margin:0; }
.armely-sql-server-page .cr-cta { text-align:center; margin-top:8px; }
.armely-sql-server-page .cr-btn { display:inline-flex; align-items:center; gap:10px; background:#1a2e52; color:#fff; border-radius:8px; padding:13px 28px; text-decoration:none; font-size:0.875rem; font-weight:600; }
.armely-sql-server-page .cr-btn:hover { background:var(--blue); }
.armely-sql-server-page .cr-btn .icon-svg { width:18px; height:18px; }
@media (max-width:900px) { .armely-sql-server-page .cr-grid { grid-template-columns:1fr 1fr; } }
@media (max-width:600px) { .armely-sql-server-page .cr-grid { grid-template-columns:1fr; } }
</style>
<div class="armely-sql-server-page">
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-inner">
    <div class="hero-copy">
      <div class="hero-eyebrow">
        <span class="eyebrow-badge">Microsoft SQL Server</span>
        <span class="eyebrow-partner">Delivered by a certified Microsoft partner</span>
      </div>
      <h1>Your databases, running<br>fast, secure, and available.</h1>
      <p class="hero-sub">Armely designs, implements, migrates, and manages Microsoft SQL Server environments on-premises and on Azure so your critical business data is always accessible, protected, and performing.</p>
      <div class="hero-actions">
        <a href="#contact" class="btn-primary">Book a Free Assessment</a>
        <a href="#delivers" class="btn-outline">See What We Do</a>
      </div>
    </div>
  </div>
</section>

<section class="spectrum"><div class="section-inner"><div class="section-eyebrow">SQL Server and Azure SQL</div><h2 class="section-title">Your business data is only as reliable as the database behind it.</h2><p class="section-body">Microsoft SQL Server is the most widely deployed relational database in the enterprise. Armely designs, implements, migrates, and manages SQL Server environments on-premises and on Azure.</p>
<div class="spectrum-grid"><div class="spectrum-row">
<div class="spectrum-level highlight"><span class="spectrum-num">On-Prem</span><div><div class="spectrum-content-title">SQL Server 2019 and 2022</div><div class="spectrum-content-desc">On-premises deployments for organizations that require local data residency, existing infrastructure investment, or low-latency access to large datasets.</div></div></div>
<div class="spectrum-level highlight"><span class="spectrum-num">Cloud</span><div><div class="spectrum-content-title">Azure SQL Database</div><div class="spectrum-content-desc">Fully managed PaaS with automatic patching, built-in high availability, and elastic scaling. No infrastructure to manage.</div></div></div>
<div class="spectrum-level highlight"><span class="spectrum-num">VM</span><div><div class="spectrum-content-title">SQL Server on Azure VM</div><div class="spectrum-content-desc">Full SQL Server on Azure infrastructure for applications that require OS-level access, third-party integrations, or compatibility features.</div></div></div>
<div class="spectrum-level"><span class="spectrum-num">MI</span><div><div class="spectrum-content-title">Azure SQL Managed Instance</div><div class="spectrum-content-desc">Near-100% compatibility with on-premises SQL Server in a fully managed cloud environment. The right path for complex migrations.</div></div></div>
</div><div><div class="platform-card"><div class="platform-header"><div class="platform-dots"><span></span><span></span><span></span></div><span class="platform-header-title">SQL Server and Azure SQL</span></div><div class="platform-body"><div class="plat-band band-tools"><div class="plat-band-label">Deployment Options</div><div class="plat-chips"><span class="plat-chip">SQL Server 2022</span><span class="plat-chip">Azure SQL Database</span><span class="plat-chip">Azure SQL Managed Instance</span><span class="plat-chip">SQL Server on Azure VM</span></div></div><div class="band-arrow">&#8597;</div><div class="plat-band band-data"><div class="plat-band-label">Management and Performance</div><div class="plat-chips"><span class="plat-chip">Always On Availability Groups</span><span class="plat-chip">Automated Backups</span><span class="plat-chip">Query Performance Insight</span><span class="plat-chip">Elastic Pools</span><span class="plat-chip">Auto-Tune</span></div></div><div class="band-arrow">&#8597;</div><div class="plat-band band-gov"><div class="plat-band-label">Security and Compliance</div><div class="plat-chips"><span class="plat-chip">Transparent Data Encryption</span><span class="plat-chip">Azure Defender for SQL</span><span class="plat-chip">Microsoft Entra ID</span><span class="plat-chip">Row-Level Security</span><span class="plat-chip">Advanced Threat Protection</span></div></div></div></div></div></div></div></section>
<section class="delivers" id="delivers"><div class="section-inner"><div class="section-eyebrow">What Armely Delivers</div><h2 class="section-title">SQL Server expertise from design through ongoing management.</h2><p class="section-body">Armely covers every phase of the SQL Server lifecycle, from new implementations and cloud migrations to performance remediation and ongoing managed administration.</p>
<div class="delivers-grid"><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg></div><div class="deliver-title">SQL Server Implementation</div><div class="deliver-desc">New SQL Server environments designed for your workload from the ground up, with correct sizing, file layout, memory configuration, tempdb optimization, and backup strategy.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg></div><div class="deliver-title">Cloud Migration</div><div class="deliver-desc">Structured migration from on-premises SQL Server to Azure SQL Database, Azure SQL Managed Instance, or SQL Server on Azure VM, with compatibility assessment and testing.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div><div class="deliver-title">Performance Tuning and Remediation</div><div class="deliver-desc">Index analysis, query plan review, wait statistics investigation, and configuration remediation for SQL Server environments suffering from slow queries or blocking.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div><div class="deliver-title">Security Hardening</div><div class="deliver-desc">SQL Server security baseline implementation covering encryption, login audit, least-privilege role assignment, Transparent Data Encryption, and Entra ID integration.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg></div><div class="deliver-title">Backup and High Availability</div><div class="deliver-desc">Always On Availability Groups, log shipping, and Azure Backup integration so your critical databases have a tested, documented recovery path.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg></div><div class="deliver-title">Managed Database Administration</div><div class="deliver-desc">Ongoing monitoring, patch management, backup verification, capacity planning, and a named Armely DBA for organizations without a full-time hire.</div></div></div></div></section>
<section class="journey"><div class="section-inner"><div class="section-eyebrow">How Armely Works</div><h2 class="section-title">From assessment to optimized, managed SQL Server environment.</h2><p class="section-body">Whether you need a new implementation, a cloud migration, or emergency performance remediation, we follow a structured process that eliminates surprises.</p>
<div class="steps-row"><div class="step"><div class="step-num">01</div><div class="step-title">Environment Assessment</div><div class="step-desc">We review your current SQL Server configuration, version, workloads, backup posture, and security controls.</div><span class="step-tag">Free</span></div><div class="step"><div class="step-num">02</div><div class="step-title">Architecture and Plan</div><div class="step-desc">We design the target environment, migration or implementation approach, and a test plan before any changes to production.</div><span class="step-tag">Week 1</span></div><div class="step"><div class="step-num">03</div><div class="step-title">Build or Migrate</div><div class="step-desc">Implementation or migration executed against the agreed plan with staging and validation at each stage.</div><span class="step-tag">Weeks 2-5</span></div><div class="step"><div class="step-num">04</div><div class="step-title">Validate and Harden</div><div class="step-desc">Post-migration validation, performance baseline, security configuration review, and backup verification.</div><span class="step-tag">Week 6</span></div><div class="step"><div class="step-num">05</div><div class="step-title">Managed Support</div><div class="step-desc">Ongoing monitoring, patching, performance management, and a named DBA contact for new requirements and incident response.</div><span class="step-tag">Ongoing</span></div></div></div></section>
<section class="usecases"><div class="section-inner"><div class="section-eyebrow">Common Engagements</div><h2 class="section-title">The SQL Server situations we resolve most often.</h2><p class="section-body">These are the scenarios organizations bring to Armely. Each one has a clear resolution path.</p>
<div class="uc-grid"><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg></span><div class="uc-title">SQL Server to Azure Migration</div><div class="uc-desc">Migrate from an aging on-premises SQL Server to Azure SQL Database or Managed Instance, with compatibility assessment, data migration, and cutover planning.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></span><div class="uc-title">Slow Query and Performance Issues</div><div class="uc-desc">Identify and resolve performance problems caused by missing indexes, parameter sniffing, stale statistics, blocking, or hardware misconfiguration.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></span><div class="uc-title">Security and Compliance Audit</div><div class="uc-desc">SQL Server environments that have grown without formal security review often have overprivileged logins and no audit trail. We assess and remediate.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg></span><div class="uc-title">High Availability Implementation</div><div class="uc-desc">Implement Always On Availability Groups or Azure-native high availability for databases where downtime is not acceptable.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg></span><div class="uc-title">Version Upgrade</div><div class="uc-desc">Upgrade from SQL Server 2012, 2014, or 2016 to SQL Server 2022 or Azure SQL, capturing security, performance, and licensing benefits.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg></span><div class="uc-title">Ongoing DBA Services</div><div class="uc-desc">Organizations without a dedicated DBA use Armely for patch management, backup verification, capacity planning, index maintenance, and on-call support.</div></div></div></div></section>
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
<section class="why"><div class="section-inner"><div class="section-eyebrow">Why Armely</div><h2 class="section-title">Database work done right requires both depth and discipline.</h2><p class="section-body">SQL Server problems are often caused by configuration decisions made years ago that were never revisited. Armely brings the depth to find root causes and the discipline to fix them.</p>
<div class="why-two-col"><div><ul class="why-list"><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg></div><div><div class="why-item-title">Microsoft-Certified Database Engineers</div><div class="why-item-desc">Our engineers carry Microsoft certifications covering SQL Server and Azure data services.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div><div><div class="why-item-title">Security-First Configuration</div><div class="why-item-desc">We apply Microsoft's SQL Server security baseline to every implementation and migration.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg></div><div><div class="why-item-title">Azure Partnership</div><div class="why-item-desc">Our Microsoft partnership gives us access to Azure migration tooling and Azure Hybrid Benefit licensing for SQL Server.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div><div><div class="why-item-title">No Surprises on Migration Day</div><div class="why-item-desc">We run a full compatibility assessment and validate application behavior before any production cutover.</div></div></li></ul></div>
<div><div class="partner-block"><div class="partner-block-top"><div class="partner-label">Microsoft Authorized Partner</div><p class="partner-text">Armely's Microsoft partnership gives us access to Azure SQL licensing at partner rates and Azure migration assessment tooling.</p></div><div class="partner-stats"><div class="p-stat"><div class="p-stat-num">78<span>%</span></div><div class="p-stat-label">of Fortune 500 organizations run Microsoft SQL Server</div></div><div class="p-stat"><div class="p-stat-num">40<span>%</span></div><div class="p-stat-label">average cost reduction when migrating to Azure SQL vs on-premises licensing</div></div><div class="p-stat"><div class="p-stat-num">99.99<span>%</span></div><div class="p-stat-label">SLA for Azure SQL Database and Managed Instance</div></div><div class="p-stat"><div class="p-stat-num">0<span></span></div><div class="p-stat-label">migrations Armely completes without a compatibility assessment and test plan</div></div></div></div></div></div></div></section>
<section class="cta-section" id="contact"><div class="cta-inner"><div><div class="section-eyebrow">Get Started</div><h2 class="section-title">Tell us about your SQL Server environment.</h2><p class="section-body">Book a free 30-minute database assessment. We will review your current configuration and come back with a clear recommendation.</p><div style="margin-top:28px;display:flex;flex-direction:column;gap:12px;"><div class="trust-item"><span class="trust-dot" style="background:var(--blue);"></span><span class="trust-text" style="color:var(--text-body);">Free assessment, no commitment required</span></div><div class="trust-item"><span class="trust-dot" style="background:var(--blue);"></span><span class="trust-text" style="color:var(--text-body);">Recommendation and partner pricing included</span></div><div class="trust-item"><span class="trust-dot" style="background:var(--blue);"></span><span class="trust-text" style="color:var(--text-body);">Response within one business day</span></div></div></div><div class="cta-form"><div class="form-title">Book Your Free Assessment</div><div class="form-sub">Tell us about your situation.</div><div class="form-row"><label>Full Name</label><input type="text" placeholder="Jane Smith"></div><div class="form-row"><label>Business Email</label><input type="email" placeholder="jane@yourcompany.com"></div><div class="form-row"><label>Company Name</label><input type="text" placeholder="Acme Corp"></div><div class="form-row"><label>Primary Need</label><select><option value="">Select...</option><option>Migrate SQL Server to Azure</option><option>Resolve performance or query problems</option><option>Implement high availability</option><option>Security and compliance review</option><option>Version upgrade</option><option>Ongoing managed DBA services</option><option>Not sure, need a review first</option></select></div><button class="form-submit">Request Free Assessment</button><div class="form-note">No spam. No sales pressure. Just a useful conversation.</div></div></div></section>
</div>
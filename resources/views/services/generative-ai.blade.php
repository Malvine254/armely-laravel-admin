@section('title', 'Generative AI and Agentic AI Solutions | Armely')

<style>


.armely-generative-ai-page *, .armely-generative-ai-page *::before, .armely-generative-ai-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-generative-ai-page {
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

.armely-generative-ai-page { scroll-behavior: smooth; }
.armely-generative-ai-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* NAV */
.armely-generative-ai-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-generative-ai-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-generative-ai-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-generative-ai-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-generative-ai-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-generative-ai-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-generative-ai-page .nav-links a:hover { color: #fff; }
.armely-generative-ai-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-generative-ai-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* HERO */
.armely-generative-ai-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-generative-ai-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-generative-ai-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-generative-ai-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-generative-ai-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-generative-ai-page .hero h1 { font-size: clamp(1.75rem, 3.2vw, 2.7rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-generative-ai-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-generative-ai-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.8; }
.armely-generative-ai-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-generative-ai-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-generative-ai-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-generative-ai-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-generative-ai-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-generative-ai-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-generative-ai-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-generative-ai-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-generative-ai-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-generative-ai-page .trust-text strong { color: #fff; font-weight: 600; }

  /* SECTIONS */
.armely-generative-ai-page section { padding: 96px 56px; }
.armely-generative-ai-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-generative-ai-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-generative-ai-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 640px; }
.armely-generative-ai-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* AI SPECTRUM */
.armely-generative-ai-page .spectrum { background: var(--navy-mid); }
.armely-generative-ai-page .spectrum-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-generative-ai-page .spectrum-row { display: flex; flex-direction: column; gap: 10px; margin-top: 32px; }
.armely-generative-ai-page .spectrum-level { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; transition: border-color 0.2s; }
.armely-generative-ai-page .spectrum-level:hover { border-color: rgba(41,78,139,0.3); }
.armely-generative-ai-page .spectrum-level.highlight { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-generative-ai-page .spectrum-num { font-size: 0.68rem; font-weight: 800; color: var(--blue); background: var(--blue-dim2); border-radius: 4px; padding: 2px 7px; flex-shrink: 0; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.08em; }
.armely-generative-ai-page .spectrum-content-title { font-size: 0.875rem; font-weight: 700; color: #1A2540; margin-bottom: 3px; }
.armely-generative-ai-page .spectrum-content-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }

  /* Platform visual */
.armely-generative-ai-page .platform-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-generative-ai-page .platform-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-generative-ai-page .platform-dots { display: flex; gap: 6px; }
.armely-generative-ai-page .platform-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-generative-ai-page .platform-header-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-generative-ai-page .platform-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.armely-generative-ai-page .plat-band { border-radius: 9px; padding: 13px 16px; }
.armely-generative-ai-page .plat-band-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
.armely-generative-ai-page .plat-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.armely-generative-ai-page .plat-chip { font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.armely-generative-ai-page .band-tools { background: var(--blue-dim); }
.armely-generative-ai-page .band-tools .plat-band-label { color: var(--blue); }
.armely-generative-ai-page .band-tools .plat-chip { background: var(--blue-dim2); color: var(--blue); }
.armely-generative-ai-page .band-data { background: rgba(41,78,139,0.05); }
.armely-generative-ai-page .band-data .plat-band-label { color: var(--blue); }
.armely-generative-ai-page .band-data .plat-chip { background: rgba(41,78,139,0.1); color: var(--blue); }
.armely-generative-ai-page .band-gov { background: var(--blue); }
.armely-generative-ai-page .band-gov .plat-band-label { color: rgba(255,255,255,0.7); }
.armely-generative-ai-page .band-gov .plat-chip { background: rgba(255,255,255,0.15); color: #fff; }
.armely-generative-ai-page .band-arrow { text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2px 0; }

  /* VIBE CODING CALLOUT */
.armely-generative-ai-page .vibe-section { background: var(--navy); }
.armely-generative-ai-page .vibe-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: start; }
.armely-generative-ai-page .vibe-left { }
.armely-generative-ai-page .vibe-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-generative-ai-page .vibe-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
.armely-generative-ai-page .vibe-card-icon { font-size: 1.4rem; }
.armely-generative-ai-page .vibe-card-title { font-size: 1rem; font-weight: 700; color: #1A2540; }
.armely-generative-ai-page .vibe-card-subtitle { font-size: 0.78rem; color: var(--text-muted); }
.armely-generative-ai-page .vibe-card-body { padding: 24px; }
.armely-generative-ai-page .vibe-risk { display: flex; align-items: flex-start; gap: 12px; padding: 14px 0; border-bottom: 1px solid var(--border); }
.armely-generative-ai-page .vibe-risk:last-child { border-bottom: none; }
.armely-generative-ai-page .vibe-risk-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
.armely-generative-ai-page .vibe-risk-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 2px; }
.armely-generative-ai-page .vibe-risk-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }
.armely-generative-ai-page .vibe-right { }
.armely-generative-ai-page .vibe-answer-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 28px; margin-bottom: 12px; }
.armely-generative-ai-page .vibe-answer-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--blue); margin-bottom: 10px; }
.armely-generative-ai-page .vibe-answer-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.75; }

  /* DELIVERS */
.armely-generative-ai-page .delivers { background: var(--navy-mid); }
.armely-generative-ai-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-generative-ai-page .deliver-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-generative-ai-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-generative-ai-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-generative-ai-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-generative-ai-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-generative-ai-page .journey { background: var(--navy); }
.armely-generative-ai-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-generative-ai-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-generative-ai-page .step:last-child { border-right: none; }
.armely-generative-ai-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-generative-ai-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-generative-ai-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-generative-ai-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* USE CASES */
.armely-generative-ai-page .usecases { background: var(--navy-mid); }
.armely-generative-ai-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-generative-ai-page .uc-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-generative-ai-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-generative-ai-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-generative-ai-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-generative-ai-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* TESTIMONIALS */
.armely-generative-ai-page .testimonials { background: var(--navy); padding: 96px 56px; }
.armely-generative-ai-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-generative-ai-page .testi-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-generative-ai-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-generative-ai-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-generative-ai-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-generative-ai-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-generative-ai-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-generative-ai-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-generative-ai-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY ARMELY */
.armely-generative-ai-page .why { background: var(--navy-mid); }
.armely-generative-ai-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-generative-ai-page .why-list { list-style: none; margin-top: 36px; }
.armely-generative-ai-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-generative-ai-page .why-list li:last-child { border-bottom: none; }
.armely-generative-ai-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-generative-ai-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-generative-ai-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-generative-ai-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-generative-ai-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-generative-ai-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-generative-ai-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-generative-ai-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-generative-ai-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-generative-ai-page .p-stat:nth-child(2) { border-right: none; }
.armely-generative-ai-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-generative-ai-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-generative-ai-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-generative-ai-page .p-stat-num span { color: var(--blue); }
.armely-generative-ai-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* CTA */
.armely-generative-ai-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-generative-ai-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-generative-ai-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-generative-ai-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-generative-ai-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-generative-ai-page .form-row { margin-bottom: 14px; }
.armely-generative-ai-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-generative-ai-page .form-row input, .armely-generative-ai-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-generative-ai-page .form-row input:focus, .armely-generative-ai-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-generative-ai-page .form-row select option { background: #fff; color: #1A2540; }
.armely-generative-ai-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-generative-ai-page .form-submit:hover { background: var(--blue-lt); }
.armely-generative-ai-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* FOOTER */
.armely-generative-ai-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-generative-ai-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-generative-ai-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-generative-ai-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-generative-ai-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-generative-ai-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-generative-ai-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* RESPONSIVE */
  @media (max-width: 900px) {
.armely-generative-ai-page nav { padding: 16px 24px; }
.armely-generative-ai-page .nav-links { display: none; }
.armely-generative-ai-page section { padding: 72px 24px; }
.armely-generative-ai-page .hero { padding: 110px 24px 72px; }
.armely-generative-ai-page .spectrum-grid, .armely-generative-ai-page .vibe-two-col, .armely-generative-ai-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-generative-ai-page .delivers-grid, .armely-generative-ai-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-generative-ai-page .steps-row { grid-template-columns: 1fr; }
.armely-generative-ai-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-generative-ai-page .step:last-child { border-bottom: none; }
.armely-generative-ai-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-generative-ai-page .testimonials { padding: 72px 24px; }
.armely-generative-ai-page .testi-grid { grid-template-columns: 1fr; }
.armely-generative-ai-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-generative-ai-page .delivers-grid, .armely-generative-ai-page .uc-grid { grid-template-columns: 1fr; }
.armely-generative-ai-page .partner-stats { grid-template-columns: 1fr; }
.armely-generative-ai-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-generative-ai-page {
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
.armely-generative-ai-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-generative-ai-page .hero::after {
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
.armely-generative-ai-page .section-title,
.armely-generative-ai-page .deliver-title,
.armely-generative-ai-page .uc-title,
.armely-generative-ai-page .step-title,
.armely-generative-ai-page .why-item-title,
.armely-generative-ai-page .form-title {
  color: #162b49;
}
.armely-generative-ai-page .deliver-card,
.armely-generative-ai-page .uc-card,
.armely-generative-ai-page .testi-card,
.armely-generative-ai-page .platform-card,
.armely-generative-ai-page .partner-block,
.armely-generative-ai-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-generative-ai-page .deliver-card:hover,
.armely-generative-ai-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-generative-ai-page .btn-primary,
.armely-generative-ai-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-generative-ai-page .btn-primary:hover,
.armely-generative-ai-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-generative-ai-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-generative-ai-page nav,
.armely-generative-ai-page footer {
  display: none;
}


/* Modern layout update: tighter first section and SVG icon system */
.armely-generative-ai-page .hero {
  min-height: auto;
  padding: 128px 32px 86px;
  isolation: isolate;
}
.armely-generative-ai-page .hero-inner {
  width: min(1160px, 100%);
  margin: 0 auto;
  display: grid;
  grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.75fr);
  align-items: center;
  gap: 56px;
  position: relative;
  z-index: 1;
}
.armely-generative-ai-page .hero-copy { max-width: 760px; }
.armely-generative-ai-page .hero h1 { max-width: 760px; margin-bottom: 20px; }
.armely-generative-ai-page .hero-sub { max-width: 640px; margin-bottom: 30px; font-size: 1.02rem; line-height: 1.72; }
.armely-generative-ai-page .hero-actions { margin-bottom: 34px; }
.armely-generative-ai-page .hero-trust {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  padding-top: 0;
  border-top: 0;
  max-width: 720px;
}
.armely-generative-ai-page .hero .trust-item {
  align-items: flex-start;
  background: rgba(255,255,255,0.10);
  border: 1px solid rgba(255,255,255,0.16);
  border-radius: 14px;
  padding: 14px 15px;
  backdrop-filter: blur(10px);
}
.armely-generative-ai-page .hero .trust-dot {
  width: 22px;
  height: 22px;
  border-radius: 999px;
  background: rgba(255,255,255,0.16);
  border: 1px solid rgba(255,255,255,0.24);
  position: relative;
  margin-top: 1px;
}
.armely-generative-ai-page .hero .trust-dot::after {
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
.armely-generative-ai-page .hero .trust-text { color: rgba(255,255,255,0.78); line-height: 1.5; }
.armely-generative-ai-page .hero-visual {
  min-height: 420px;
  border-radius: 28px;
  position: relative;
  background: linear-gradient(145deg, rgba(255,255,255,0.16), rgba(255,255,255,0.05));
  border: 1px solid rgba(255,255,255,0.18);
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.18), 0 28px 70px rgba(6, 22, 48, 0.24);
  overflow: hidden;
}
.armely-generative-ai-page .hero-visual::before {
  content: '';
  position: absolute;
  inset: 36px;
  border: 1px solid rgba(255,255,255,0.14);
  border-radius: 999px;
}
.armely-generative-ai-page .hero-visual::after {
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
.armely-generative-ai-page .hero-orbit span {
  position: absolute;
  width: 52px;
  height: 52px;
  border-radius: 16px;
  background: rgba(255,255,255,0.14);
  border: 1px solid rgba(255,255,255,0.22);
}
.armely-generative-ai-page .hero-orbit span:nth-child(1) { left: 56px; top: 88px; }
.armely-generative-ai-page .hero-orbit span:nth-child(2) { right: 72px; top: 138px; }
.armely-generative-ai-page .hero-orbit span:nth-child(3) { left: 48%; bottom: 76px; }
.armely-generative-ai-page .hero-visual-card {
  position: absolute;
  z-index: 2;
  width: 230px;
  padding: 18px;
  border-radius: 18px;
  background: rgba(255,255,255,0.92);
  box-shadow: 0 24px 54px rgba(9, 31, 63, 0.22);
}
.armely-generative-ai-page .hero-visual-card.top { top: 42px; right: 30px; }
.armely-generative-ai-page .hero-visual-card.bottom { bottom: 46px; left: 30px; }
.armely-generative-ai-page .hero-visual-label {
  display: block;
  font-size: 0.78rem;
  font-weight: 700;
  color: #162b49;
  margin-bottom: 12px;
}
.armely-generative-ai-page .hero-visual-line {
  display: block;
  height: 8px;
  width: 100%;
  border-radius: 999px;
  background: rgba(47, 85, 151, 0.16);
  margin-top: 8px;
}
.armely-generative-ai-page .hero-visual-line.short { width: 68%; }
.armely-generative-ai-page .icon-svg {
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
.armely-generative-ai-page .vibe-card-icon,
.armely-generative-ai-page .vibe-risk-icon,
.armely-generative-ai-page .deliver-icon,
.armely-generative-ai-page .uc-icon,
.armely-generative-ai-page .why-icon {
  color: var(--blue);
}
.armely-generative-ai-page .vibe-card-icon,
.armely-generative-ai-page .vibe-risk-icon {
  width: 42px;
  height: 42px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-generative-ai-page .vibe-risk-icon { width: 36px; height: 36px; border-radius: 10px; }
.armely-generative-ai-page .deliver-icon .icon-svg,
.armely-generative-ai-page .uc-icon .icon-svg,
.armely-generative-ai-page .why-icon .icon-svg { width: 23px; height: 23px; }
.armely-generative-ai-page .uc-icon {
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
  .armely-generative-ai-page .hero-inner { grid-template-columns: 1fr; gap: 32px; }
  .armely-generative-ai-page .hero-visual { min-height: 320px; }
}
@media (max-width: 680px) {
  .armely-generative-ai-page .hero { padding: 104px 22px 64px; }
  .armely-generative-ai-page .hero-trust { grid-template-columns: 1fr; }
  .armely-generative-ai-page .hero-visual { display: none; }
  .armely-generative-ai-page .hero-actions a { width: 100%; text-align: center; }
}

/* Focused cleanup for the Generative AI detail page */
.armely-generative-ai-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  align-items: center;
  background: #173b67;
  border-radius: 0;
}
.armely-generative-ai-page .hero::after,
.armely-generative-ai-page .hero-bg-glow,
.armely-generative-ai-page .hero-visual {
  display: none;
}
.armely-generative-ai-page .hero-inner {
  width: min(1040px, 100%);
  display: block;
}
.armely-generative-ai-page .hero-copy {
  max-width: 760px;
}
.armely-generative-ai-page .hero-eyebrow {
  margin-bottom: 18px;
}
.armely-generative-ai-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.72);
}
.armely-generative-ai-page .eyebrow-partner,
.armely-generative-ai-page .hero-trust {
  display: none;
}
.armely-generative-ai-page .hero h1 {
  max-width: 760px;
  margin-bottom: 22px;
}
.armely-generative-ai-page .hero-sub {
  max-width: 680px;
  margin-bottom: 34px;
}
.armely-generative-ai-page .hero-actions {
  margin-bottom: 0;
}
.armely-generative-ai-page .hero .btn-primary,
.armely-generative-ai-page .hero .btn-outline {
  border-radius: 0;
}
.armely-generative-ai-page .vibe-section {
  background: #fff;
  padding: 84px 56px;
}
.armely-generative-ai-page .vibe-section .section-inner {
  max-width: 920px;
}
.armely-generative-ai-page .vibe-section .section-title,
.armely-generative-ai-page .vibe-section .section-body {
  max-width: 820px;
}
.armely-generative-ai-page .vibe-section .section-body {
  margin-bottom: 28px;
}
.armely-generative-ai-page .vibe-two-col {
  grid-template-columns: 1fr;
  gap: 18px;
}
.armely-generative-ai-page .vibe-card,
.armely-generative-ai-page .vibe-answer-card {
  border-radius: 0;
  box-shadow: none;
}
.armely-generative-ai-page .vibe-card-header {
  padding: 18px 20px;
}
.armely-generative-ai-page .vibe-card-body {
  padding: 8px 20px 12px;
}
.armely-generative-ai-page .vibe-risk {
  padding: 12px 0;
}
.armely-generative-ai-page .vibe-right {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
}
.armely-generative-ai-page .vibe-answer-card {
  margin-bottom: 0;
  padding: 20px;
  background: #f7f9fc;
}
.armely-generative-ai-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-generative-ai-page .why > .section-inner > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-generative-ai-page section:not(.hero) > .section-inner > .section-title,
.armely-generative-ai-page .why > .section-inner > .section-title {
  max-width: 880px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-generative-ai-page section:not(.hero) > .section-inner > .section-body,
.armely-generative-ai-page .why > .section-inner > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-generative-ai-page .spectrum-grid,
.armely-generative-ai-page .delivers-grid,
.armely-generative-ai-page .steps-row,
.armely-generative-ai-page .uc-grid,
.armely-generative-ai-page .testi-grid,
.armely-generative-ai-page .why-two-col {
  margin-top: 56px;
}
.armely-generative-ai-page .why-two-col {
  align-items: stretch;
}
.armely-generative-ai-page .why-list {
  margin-top: 0;
}
.armely-generative-ai-page .why-list,
.armely-generative-ai-page .partner-block {
  height: 100%;
}
@media (max-width: 900px) {
  .armely-generative-ai-page .hero {
    padding: 118px 24px 76px;
  }
  .armely-generative-ai-page .vibe-section {
    padding: 72px 24px;
  }
  .armely-generative-ai-page .vibe-right {
    grid-template-columns: 1fr;
  }
}



/* Final compact modern cleanup */
.armely-generative-ai-page {
  --blue: #2f5597;
  --blue-lt: #4779bd;
  --navy-mid: #f6f8fc;
  --navy-card: #ffffff;
  --text-body: #334155;
  --text-muted: #667085;
  --border: rgba(47, 85, 151, 0.14);
}
.armely-generative-ai-page .hero {
  min-height: auto !important;
  padding: 86px 56px 70px !important;
  background: linear-gradient(135deg, #173b67 0%, #234f86 100%) !important;
  border-radius: 0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}
.armely-generative-ai-page .hero::after,
.armely-generative-ai-page .hero-bg-glow,
.armely-generative-ai-page .hero-visual {
  display: none !important;
}
.armely-generative-ai-page .hero-inner {
  width: min(1120px, 100%) !important;
  margin: 0 auto !important;
  display: block !important;
}
.armely-generative-ai-page .hero-copy {
  max-width: 860px !important;
}
.armely-generative-ai-page .hero-eyebrow {
  margin-bottom: 18px !important;
}
.armely-generative-ai-page .eyebrow-badge {
  display: inline-flex !important;
  background: rgba(255,255,255,0.10) !important;
  border: 1px solid rgba(255,255,255,0.22) !important;
  border-radius: 999px !important;
  padding: 7px 14px !important;
  color: rgba(255,255,255,0.88) !important;
}
.armely-generative-ai-page .eyebrow-partner {
  display: inline-flex !important;
  color: rgba(255,255,255,0.66) !important;
}
.armely-generative-ai-page .hero h1 {
  max-width: 900px !important;
  margin-bottom: 18px !important;
  font-size: clamp(1.75rem, 3.2vw, 2.7rem);
  line-height: 1.05 !important;
  letter-spacing: -0.04em !important;
}
.armely-generative-ai-page .hero-sub {
  max-width: 760px !important;
  margin-bottom: 28px !important;
  font-size: 1rem !important;
  line-height: 1.7 !important;
}
.armely-generative-ai-page .hero-actions {
  margin-bottom: 0 !important;
  gap: 12px !important;
}
.armely-generative-ai-page .hero .btn-primary,
.armely-generative-ai-page .hero .btn-outline,
.armely-generative-ai-page .btn-primary,
.armely-generative-ai-page .btn-outline,
.armely-generative-ai-page .form-submit {
  border-radius: 8px !important;
}
.armely-generative-ai-page section {
  padding: 68px 56px !important;
}
.armely-generative-ai-page .section-inner {
  max-width: 1120px !important;
}
.armely-generative-ai-page .section-eyebrow {
  margin-bottom: 10px !important;
}
.armely-generative-ai-page .section-title {
  margin-bottom: 14px !important;
}
.armely-generative-ai-page .section-body {
  margin-bottom: 28px !important;
  line-height: 1.65 !important;
}
.armely-generative-ai-page .spectrum-grid,
.armely-generative-ai-page .vibe-two-col,
.armely-generative-ai-page .delivers-grid,
.armely-generative-ai-page .steps-row,
.armely-generative-ai-page .uc-grid,
.armely-generative-ai-page .testi-grid,
.armely-generative-ai-page .why-two-col {
  margin-top: 34px !important;
  gap: 20px !important;
}
.armely-generative-ai-page .spectrum-grid,
.armely-generative-ai-page .why-two-col {
  grid-template-columns: 1fr 1fr !important;
}
.armely-generative-ai-page .spectrum-row {
  margin-top: 0 !important;
  gap: 10px !important;
}
.armely-generative-ai-page .spectrum-level,
.armely-generative-ai-page .deliver-card,
.armely-generative-ai-page .uc-card,
.armely-generative-ai-page .testi-card,
.armely-generative-ai-page .vibe-answer-card,
.armely-generative-ai-page .partner-block,
.armely-generative-ai-page .cta-form,
.armely-generative-ai-page .vibe-card,
.armely-generative-ai-page .platform-card {
  border-radius: 14px !important;
  box-shadow: 0 14px 36px rgba(18, 47, 82, 0.08) !important;
}
.armely-generative-ai-page .deliver-card,
.armely-generative-ai-page .uc-card,
.armely-generative-ai-page .testi-card {
  padding: 24px 22px !important;
}
.armely-generative-ai-page .deliver-icon,
.armely-generative-ai-page .uc-icon,
.armely-generative-ai-page .why-icon,
.armely-generative-ai-page .vibe-card-icon,
.armely-generative-ai-page .vibe-risk-icon {
  border-radius: 12px !important;
  color: var(--blue) !important;
}
.armely-generative-ai-page .vibe-section {
  padding: 68px 56px !important;
  background: #fff !important;
}
.armely-generative-ai-page .vibe-section .section-inner {
  max-width: 1120px !important;
}
.armely-generative-ai-page .vibe-two-col {
  grid-template-columns: 1fr 1fr !important;
  align-items: stretch !important;
}
.armely-generative-ai-page .vibe-card-body {
  padding: 18px 20px !important;
}
.armely-generative-ai-page .vibe-right {
  display: flex !important;
  flex-direction: column !important;
  gap: 12px !important;
}
.armely-generative-ai-page .vibe-answer-card {
  padding: 20px !important;
  background: #f8fafd !important;
}
.armely-generative-ai-page .steps-row {
  border-radius: 14px !important;
  overflow: hidden !important;
}
.armely-generative-ai-page .step {
  padding: 24px 18px !important;
}
.armely-generative-ai-page .step-num {
  font-size: 2rem !important;
  margin-bottom: 10px !important;
}
.armely-generative-ai-page .why-list {
  margin-top: 0 !important;
}
.armely-generative-ai-page .why-list li {
  padding: 16px 0 !important;
}
.armely-generative-ai-page .partner-block-top,
.armely-generative-ai-page .p-stat {
  padding: 22px !important;
}
.armely-generative-ai-page .cta-inner {
  padding: 68px 56px !important;
  gap: 40px !important;
}
@media (max-width: 900px) {
  .armely-generative-ai-page .hero { padding: 88px 24px 58px !important; }
  .armely-generative-ai-page section,
  .armely-generative-ai-page .vibe-section { padding: 56px 24px !important; }
  .armely-generative-ai-page .spectrum-grid,
  .armely-generative-ai-page .vibe-two-col,
  .armely-generative-ai-page .why-two-col { grid-template-columns: 1fr !important; }
  .armely-generative-ai-page .delivers-grid,
  .armely-generative-ai-page .uc-grid { grid-template-columns: 1fr 1fr !important; }
  .armely-generative-ai-page .cta-inner { padding: 56px 24px !important; grid-template-columns: 1fr !important; }
}
@media (max-width: 600px) {
  .armely-generative-ai-page .hero h1 { font-size: clamp(1.75rem, 3.2vw, 2.7rem); }
  .armely-generative-ai-page .hero-eyebrow { flex-direction: column !important; align-items: flex-start !important; gap: 8px !important; }
  .armely-generative-ai-page .hero-actions a { width: 100% !important; text-align: center !important; }
  .armely-generative-ai-page .delivers-grid,
  .armely-generative-ai-page .uc-grid { grid-template-columns: 1fr !important; }
}



.armely-generative-ai-page .cr-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:28px; margin-bottom:28px; }
.armely-generative-ai-page .cr-card { background:#fff; border:1px solid var(--border); border-radius:14px; padding:22px; box-shadow:0 14px 36px rgba(18,47,82,0.08); }
.armely-generative-ai-page .cr-label { display:flex; align-items:center; gap:9px; margin-bottom:10px; }
.armely-generative-ai-page .cr-check { width:20px; height:20px; display:flex; align-items:center; justify-content:center; background:var(--blue-dim); border:1px solid var(--blue-dim2); border-radius:50%; flex-shrink:0; color:var(--blue); }
.armely-generative-ai-page .cr-check .icon-svg { width:11px; height:11px; stroke-width:3; }
.armely-generative-ai-page .cr-industry { font-size:0.875rem; font-weight:700; color:#162b49; }
.armely-generative-ai-page .cr-desc { font-size:0.84rem; color:var(--text-muted); line-height:1.65; margin:0; }
.armely-generative-ai-page .cr-cta { text-align:center; margin-top:8px; }
.armely-generative-ai-page .cr-btn { display:inline-flex; align-items:center; gap:10px; background:#1a2e52; color:#fff; border-radius:8px; padding:13px 28px; text-decoration:none; font-size:0.875rem; font-weight:600; }
.armely-generative-ai-page .cr-btn:hover { background:var(--blue); }
.armely-generative-ai-page .cr-btn .icon-svg { width:18px; height:18px; }
@media (max-width:900px) { .armely-generative-ai-page .cr-grid { grid-template-columns:1fr 1fr; } }
@media (max-width:600px) { .armely-generative-ai-page .cr-grid { grid-template-columns:1fr; } }
</style>
<div class="armely-generative-ai-page">
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-inner">
    <div class="hero-copy">
      <div class="hero-eyebrow">
        <span class="eyebrow-badge">Generative and Agentic AI</span>
        <span class="eyebrow-partner">Delivered by a certified Microsoft partner</span>
      </div>
      <h1>AI that takes action,<br>not just answers questions.</h1>
      <p class="hero-sub">Armely designs and deploys generative AI and autonomous agent solutions on the Microsoft platform, grounded in your business data, governed to your compliance requirements, and built to run in production, not just a demo.</p>
      <div class="hero-actions">
        <a href="#contact" class="btn-primary">Book a Free Assessment</a>
        <a href="#delivers" class="btn-outline">See What We Do</a>
      </div>
    </div>
  </div>
</section>

<section class="spectrum"><div class="section-inner"><div class="section-eyebrow">Understanding the Landscape</div><h2 class="section-title">From assistants to autonomous agents. Where does your business need AI?</h2><p class="section-body">AI capability exists on a spectrum. Most organizations start at the assistive end and move toward automation as confidence and governance mature. Armely helps you identify where to start and how to progress deliberately rather than reactively.</p>
<div class="spectrum-grid"><div class="spectrum-row">
<div class="spectrum-level"><span class="spectrum-num">01</span><div><div class="spectrum-content-title">Assistive AI</div><div class="spectrum-content-desc">AI that helps individuals work faster. Microsoft 365 Copilot drafting emails, summarizing meetings, and generating first drafts. Triggered by a human, reviewed before use.</div></div></div>
<div class="spectrum-level highlight"><span class="spectrum-num">02</span><div><div class="spectrum-content-title">Generative AI Applications</div><div class="spectrum-content-desc">Custom applications that generate content, answers, or analysis from your business data. Internal knowledge assistants, document summarization tools, and customer-facing chatbots grounded in your own systems.</div></div></div>
<div class="spectrum-level highlight"><span class="spectrum-num">03</span><div><div class="spectrum-content-title">Agentic AI Workflows</div><div class="spectrum-content-desc">AI agents that pursue a goal autonomously across multiple steps, tools, and systems without a human in the loop for each action. Case triage, order processing, and onboarding workflows executed end to end.</div></div></div>
<div class="spectrum-level"><span class="spectrum-num">04</span><div><div class="spectrum-content-title">Multi-Agent Orchestration</div><div class="spectrum-content-desc">Networks of specialized agents that coordinate to complete complex business processes. One agent researches, one drafts, one reviews, one routes, each within defined guardrails and audit trails.</div></div></div>
</div><div><div class="platform-card"><div class="platform-header"><div class="platform-dots"><span></span><span></span><span></span></div><span class="platform-header-title">Microsoft AI Stack</span></div><div class="platform-body"><div class="plat-band band-tools"><div class="plat-band-label">Build Tools</div><div class="plat-chips"><span class="plat-chip">Copilot Studio</span><span class="plat-chip">Azure AI Foundry</span><span class="plat-chip">GitHub Copilot</span><span class="plat-chip">Agent Builder</span></div></div><div class="band-arrow">&#8597;</div><div class="plat-band band-data"><div class="plat-band-label">AI Models and Data</div><div class="plat-chips"><span class="plat-chip">Azure OpenAI (GPT)</span><span class="plat-chip">Microsoft Phi</span><span class="plat-chip">Azure AI Search</span><span class="plat-chip">Dataverse</span><span class="plat-chip">SharePoint</span><span class="plat-chip">SQL Server</span></div></div><div class="band-arrow">&#8597;</div><div class="plat-band band-gov"><div class="plat-band-label">Governance and Safety</div><div class="plat-chips"><span class="plat-chip">Microsoft Purview</span><span class="plat-chip">Entra ID</span><span class="plat-chip">DLP Policies</span><span class="plat-chip">Audit Logs</span><span class="plat-chip">Responsible AI Controls</span></div></div></div></div></div></div></div></section>

<section class="vibe-section" id="vibe-coding"><div class="section-inner">
<div class="section-eyebrow">A Word on AI-Assisted Development</div><h2 class="section-title">The prototype is not the product.</h2><p class="section-body">AI-assisted development can produce a working prototype in hours. The problem is that a prototype built without security review, governed data access, or a maintainable architecture is not ready for production, and most organizations discover this the hard way.</p>
<div class="vibe-two-col">
<div class="vibe-left"><div class="vibe-card">
<div class="vibe-card-header"><span class="vibe-card-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg></span><div><div class="vibe-card-title">Where AI-Assisted Development Breaks Down in Production</div><div class="vibe-card-subtitle">Common issues Armely is brought in to resolve</div></div></div>
<div class="vibe-card-body">
<div class="vibe-risk"><span class="vibe-risk-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span><div><div class="vibe-risk-title">Security Vulnerabilities</div><div class="vibe-risk-desc">AI-generated code passes basic functional tests but fails security review. Improper input validation, over-permissive access roles, and hardcoded credentials are common patterns that reach production undetected.</div></div></div>
<div class="vibe-risk"><span class="vibe-risk-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></span><div><div class="vibe-risk-title">Ungoverned Data Access</div><div class="vibe-risk-desc">Agents built without proper permission scoping expose content that users should not see. A customer-facing agent answering from an unscopeable knowledge base is a data governance incident waiting to happen.</div></div></div>
<div class="vibe-risk"><span class="vibe-risk-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg></span><div><div class="vibe-risk-title">No Maintainable Architecture</div><div class="vibe-risk-desc">Prototype code accepted without review accumulates technical debt rapidly. When the business need evolves or the underlying model changes, there is no structured codebase to update.</div></div></div>
<div class="vibe-risk"><span class="vibe-risk-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M8 12h8"/><path d="M8 16h6"/></svg></span><div><div class="vibe-risk-title">No Audit Trail</div><div class="vibe-risk-desc">Regulated industries require documentation of what an AI system did and why. Agents deployed without audit logging cannot satisfy compliance requirements when they are audited.</div></div></div>
</div></div></div>
<div class="vibe-right">
<div class="vibe-answer-card"><div class="vibe-answer-label">The Armely Approach</div><div class="vibe-answer-text">We treat AI-assisted development as a starting point, not a finishing line. Where your team or ours uses AI tools to accelerate building, we apply the security review, governance configuration, architecture standards, and testing discipline that turns a fast prototype into a production-grade solution your business can rely on.</div></div>
<div class="vibe-answer-card"><div class="vibe-answer-label">What That Means in Practice</div><div class="vibe-answer-text">Every AI solution Armely deploys includes defined permission scoping so agents only see what they should, sensitivity label enforcement through Microsoft Purview, audit logging for compliance, a documented architecture that survives a model or platform change, and a governance framework that scales as new agents are added.</div></div>
<div class="vibe-answer-card"><div class="vibe-answer-label">Already Have a Prototype?</div><div class="vibe-answer-text">If your team has already built an AI agent or generative AI application and you are not confident it is production-ready, Armely can conduct an AI readiness review, identify gaps, and either remediate the existing solution or rebuild it on a governed foundation.</div></div>
</div></div></div></section>

<section class="delivers" id="delivers"><div class="section-inner"><div class="section-eyebrow">What Armely Delivers</div><h2 class="section-title">AI solutions designed for your business, built for production.</h2><p class="section-body">Every Armely AI engagement starts with a use case that has a clear business outcome, not a technology looking for a problem.</p>
<div class="delivers-grid"><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z"/><path d="M9 3v15"/><path d="M15 6v15"/></svg></div><div class="deliver-title">AI Use Case Assessment</div><div class="deliver-desc">We work with your leadership and operations teams to identify, prioritize, and scope AI use cases based on business value, data availability, and implementation complexity.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/></svg></div><div class="deliver-title">Custom AI Agents</div><div class="deliver-desc">We design and build AI agents using Copilot Studio and Azure AI Foundry that answer questions, retrieve information, trigger workflows, and complete tasks, grounded in your data sources.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg></div><div class="deliver-title">Document Intelligence</div><div class="deliver-desc">We build generative AI solutions that read, classify, extract, and summarize documents at scale, including contracts, invoices, clinical notes, and compliance filings.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M13 2 3 14h9l-1 8 11-14h-9l1-6Z"/></svg></div><div class="deliver-title">Agentic Process Automation</div><div class="deliver-desc">We design and deploy autonomous agent workflows that handle multi-step business processes end to end, with human review gates where the business requires them.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div><div class="deliver-title">AI Governance and Security</div><div class="deliver-desc">We implement the governance layer that every AI deployment requires: permission scoping, sensitivity label enforcement, DLP policies, audit logging, and responsible AI controls.</div></div><div class="deliver-card"><div class="deliver-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div><div class="deliver-title">AI Readiness Review</div><div class="deliver-desc">For organizations that have already deployed AI solutions and are unsure whether they are production-ready, we conduct a structured review with a remediation plan where gaps exist.</div></div></div></div></section>
<section class="journey"><div class="section-inner"><div class="section-eyebrow">The Armely AI Journey</div><h2 class="section-title">From identified use case to governed production deployment.</h2><p class="section-body">AI projects fail most often because use cases are selected without clear success criteria, or because governance is treated as a post-launch concern.</p>
<div class="steps-row"><div class="step"><div class="step-num">01</div><div class="step-title">Use Case Discovery</div><div class="step-desc">We identify and prioritize AI opportunities based on business value, data readiness, and implementation risk. You leave with a ranked list, not a blank canvas.</div><span class="step-tag">Free</span></div><div class="step"><div class="step-num">02</div><div class="step-title">Data and Governance Design</div><div class="step-desc">We map your data sources, design the permission and governance model, and confirm the Microsoft platform tools required before any build begins.</div><span class="step-tag">Week 1</span></div><div class="step"><div class="step-num">03</div><div class="step-title">Build and Test</div><div class="step-desc">We build the agent or application, test it against real data with realistic user scenarios, and apply security and governance controls throughout.</div><span class="step-tag">Weeks 2-5</span></div><div class="step"><div class="step-num">04</div><div class="step-title">Pilot and Validate</div><div class="step-desc">A controlled pilot with a defined user group validates business outcomes before broad deployment. We measure against the success criteria defined in discovery.</div><span class="step-tag">Week 6</span></div><div class="step"><div class="step-num">05</div><div class="step-title">Scale and Govern</div><div class="step-desc">Broader rollout with training, an agent registry, ongoing monitoring, and governance reviews as new AI solutions are added to your environment.</div><span class="step-tag">Ongoing</span></div></div></div></section>
<section class="usecases"><div class="section-inner"><div class="section-eyebrow">Common Engagements</div><h2 class="section-title">The AI use cases delivering measurable value for organizations today.</h2><p class="section-body">These are the scenarios where generative and agentic AI is delivering clear, verifiable business outcomes.</p>
<div class="uc-grid"><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/></svg></span><div class="uc-title">Internal Knowledge Assistants</div><div class="uc-desc">AI agents grounded in your SharePoint, policies, and documentation that answer employee questions accurately, with citations, in natural language.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M8 12h8"/><path d="M8 16h6"/></svg></span><div class="uc-title">Automated Case and Ticket Triage</div><div class="uc-desc">Agents that read incoming service requests, classify them by type and urgency, extract relevant information, and route to the correct team or trigger an automated resolution.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg></span><div class="uc-title">Contract and Document Review</div><div class="uc-desc">Generative AI that reads contracts, extracts key clauses, flags non-standard terms, and produces a structured summary for legal or procurement review.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 8V4H8"/><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 11v2"/><path d="M15 11v2"/></svg></span><div class="uc-title">Business Report Generation</div><div class="uc-desc">Agents connected to your data sources that generate structured reports and narrative summaries on a schedule or on demand, populated with live figures.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span><div class="uc-title">Customer-Facing AI Assistants</div><div class="uc-desc">Governed customer-facing agents deployed on your website or portal that answer product questions, check order status, handle routine service requests, and escalate when needed.</div></div><div class="uc-card"><span class="uc-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M21 12a9 9 0 0 1-15.5 6.2L3 16"/><path d="M3 21v-5h5"/><path d="M3 12A9 9 0 0 1 18.5 5.8L21 8"/><path d="M21 3v5h-5"/></svg></span><div class="uc-title">Multi-Step Agentic Workflows</div><div class="uc-desc">End-to-end autonomous workflows where an agent receives a trigger, gathers information from multiple systems, takes action, and notifies stakeholders.</div></div></div></div></section>
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
<section class="why"><div class="section-inner"><div class="section-eyebrow">Why Armely</div><h2 class="section-title">AI implementation requires more than AI enthusiasm.</h2><p class="section-body">The organizations that get the most from AI are those that treat it as a governed business capability rather than an experiment.</p>
<div class="why-two-col"><div><ul class="why-list"><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg></div><div><div class="why-item-title">Microsoft AI Platform Certified</div><div class="why-item-desc">Our team holds certifications in Copilot Studio, Azure AI Foundry, and Microsoft 365, with production delivery experience building AI agents across healthcare, education, and enterprise.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div><div><div class="why-item-title">Governance Is Not Optional for Us Either</div><div class="why-item-desc">Every solution Armely deploys includes Microsoft Purview integration, permission scoping, audit logging, and a documented governance framework.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></div><div><div class="why-item-title">Your Data Stays in Your Environment</div><div class="why-item-desc">We build on the Microsoft AI stack because it processes your data inside your Microsoft 365 tenant with your existing security controls applied.</div></div></li><li><div class="why-icon"><svg class="icon-svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 17 6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg></div><div><div class="why-item-title">Measured Against Business Outcomes</div><div class="why-item-desc">We define success criteria before we build, not after. Every AI engagement includes a measurement framework so you can demonstrate value with verifiable evidence.</div></div></li></ul></div>
<div><div class="partner-block"><div class="partner-block-top"><div class="partner-label">Microsoft Authorized Partner</div><p class="partner-text">Armely's Microsoft partnership gives us access to Azure AI Foundry technical resources, Copilot Studio enterprise support, and early access to new AI capabilities before they reach general availability.</p></div><div class="partner-stats"><div class="p-stat"><div class="p-stat-num">1.3<span>B</span></div><div class="p-stat-label">AI agents projected to be in production globally by 2028 (IDC)</div></div><div class="p-stat"><div class="p-stat-num">41<span>%</span></div><div class="p-stat-label">of all code written today is AI-assisted (GitHub, 2026)</div></div><div class="p-stat"><div class="p-stat-num">35<span>%</span></div><div class="p-stat-label">of enterprise development teams already using AI-assisted coding tools (JetBrains, 2026)</div></div><div class="p-stat"><div class="p-stat-num">0<span></span></div><div class="p-stat-label">AI solutions Armely deploys without a defined governance and audit framework</div></div></div></div></div></div></div></section>
<section class="cta-section" id="contact"><div class="cta-inner"><div><div class="section-eyebrow">Get Started</div><h2 class="section-title">Tell us what you want AI to do for your business.</h2><p class="section-body">Book a free 30-minute AI assessment. We will review your current environment, identify the highest-value AI opportunities, and come back with a prioritized roadmap at no obligation.</p><div style="margin-top:28px;display:flex;flex-direction:column;gap:12px;"><div class="trust-item"><span class="trust-dot" style="background:var(--blue);"></span><span class="trust-text" style="color:var(--text-body);">Free assessment, no commitment required</span></div><div class="trust-item"><span class="trust-dot" style="background:var(--blue);"></span><span class="trust-text" style="color:var(--text-body);">Recommendation and partner pricing included</span></div><div class="trust-item"><span class="trust-dot" style="background:var(--blue);"></span><span class="trust-text" style="color:var(--text-body);">Response within one business day</span></div></div></div><div class="cta-form"><div class="form-title">Book Your Free Assessment</div><div class="form-sub">Tell us about your situation.</div><div class="form-row"><label>Full Name</label><input type="text" placeholder="Jane Smith"></div><div class="form-row"><label>Business Email</label><input type="email" placeholder="jane@yourcompany.com"></div><div class="form-row"><label>Company Name</label><input type="text" placeholder="Acme Corp"></div><div class="form-row"><label>Primary Need</label><select><option value="">Select...</option><option>We have not started with AI yet</option><option>We use Microsoft 365 Copilot but want to go further</option><option>We have a prototype that needs to go to production</option><option>We want to build a custom AI agent or assistant</option><option>We need an AI governance framework</option><option>We want an AI readiness review of existing solutions</option><option>Not sure, need a recommendation</option></select></div><button class="form-submit">Request Free AI Assessment</button><div class="form-note">No spam. No sales pressure. Just a useful conversation.</div></div></div></section>
</div>
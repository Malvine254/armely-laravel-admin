<style>

.armely-api-development-page *, .armely-api-development-page *::before, .armely-api-development-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-api-development-page {
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

.armely-api-development-page { scroll-behavior: smooth; }
.armely-api-development-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* NAV */
.armely-api-development-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-api-development-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-api-development-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-api-development-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-api-development-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-api-development-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-api-development-page .nav-links a:hover { color: #fff; }
.armely-api-development-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-api-development-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* HERO */
.armely-api-development-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-api-development-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-api-development-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-api-development-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-api-development-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-api-development-page .hero h1 { font-size: clamp(2.6rem, 5.5vw, 4.8rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-api-development-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-api-development-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.8; }
.armely-api-development-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-api-development-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-api-development-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-api-development-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-api-development-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-api-development-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-api-development-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-api-development-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-api-development-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-api-development-page .trust-text strong { color: #fff; font-weight: 600; }

  /* SECTIONS */
.armely-api-development-page section { padding: 96px 56px; }
.armely-api-development-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-api-development-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-api-development-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 640px; }
.armely-api-development-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* WHAT WE BUILD */
.armely-api-development-page .intro { background: var(--navy-mid); }
.armely-api-development-page .intro-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }

  /* API type cards */
.armely-api-development-page .api-types { display: flex; flex-direction: column; gap: 10px; margin-top: 28px; }
.armely-api-development-page .api-type { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; transition: border-color 0.2s; }
.armely-api-development-page .api-type:hover { border-color: rgba(41,78,139,0.3); }
.armely-api-development-page .api-type.primary { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-api-development-page .api-type-badge { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--blue); background: var(--blue-dim2); border-radius: 4px; padding: 2px 8px; flex-shrink: 0; margin-top: 2px; white-space: nowrap; }
.armely-api-development-page .api-type-title { font-size: 0.875rem; font-weight: 700; color: #1A2540; margin-bottom: 3px; }
.armely-api-development-page .api-type-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }

  /* API lifecycle visual */
.armely-api-development-page .lifecycle-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-api-development-page .lifecycle-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-api-development-page .lifecycle-dots { display: flex; gap: 6px; }
.armely-api-development-page .lifecycle-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-api-development-page .lifecycle-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-api-development-page .lifecycle-body { padding: 20px; }
.armely-api-development-page .lifecycle-step { display: flex; align-items: flex-start; gap: 14px; padding: 12px 0; border-bottom: 1px solid var(--border); }
.armely-api-development-page .lifecycle-step:last-child { border-bottom: none; }
.armely-api-development-page .lifecycle-step-num { width: 28px; height: 28px; border-radius: 50%; background: var(--blue); color: #fff; font-size: 0.72rem; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
.armely-api-development-page .lifecycle-step-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 2px; }
.armely-api-development-page .lifecycle-step-desc { font-size: 0.74rem; color: var(--text-muted); line-height: 1.5; }
.armely-api-development-page .apim-callout { background: var(--blue); border-radius: 9px; padding: 14px 16px; display: flex; align-items: center; gap: 12px; margin-top: 12px; }
.armely-api-development-page .apim-callout-text { font-size: 0.82rem; color: rgba(255,255,255,0.9); line-height: 1.5; }
.armely-api-development-page .apim-callout-text strong { color: #fff; }

  /* DELIVERS */
.armely-api-development-page .delivers { background: var(--navy); }
.armely-api-development-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-api-development-page .deliver-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-api-development-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-api-development-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-api-development-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-api-development-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-api-development-page .journey { background: var(--navy-mid); }
.armely-api-development-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-api-development-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-api-development-page .step:last-child { border-right: none; }
.armely-api-development-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-api-development-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-api-development-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-api-development-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* USE CASES */
.armely-api-development-page .usecases { background: var(--navy); }
.armely-api-development-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-api-development-page .uc-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-api-development-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-api-development-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-api-development-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-api-development-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* TESTIMONIALS */
.armely-api-development-page .testimonials { background: var(--navy-mid); padding: 96px 56px; }
.armely-api-development-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-api-development-page .testi-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-api-development-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-api-development-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-api-development-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-api-development-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-api-development-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-api-development-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-api-development-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY ARMELY */
.armely-api-development-page .why { background: var(--navy); }
.armely-api-development-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-api-development-page .why-list { list-style: none; margin-top: 36px; }
.armely-api-development-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-api-development-page .why-list li:last-child { border-bottom: none; }
.armely-api-development-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-api-development-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-api-development-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-api-development-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-api-development-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-api-development-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-api-development-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-api-development-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-api-development-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-api-development-page .p-stat:nth-child(2) { border-right: none; }
.armely-api-development-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-api-development-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-api-development-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-api-development-page .p-stat-num span { color: var(--blue); }
.armely-api-development-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* CTA */
.armely-api-development-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-api-development-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-api-development-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-api-development-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-api-development-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-api-development-page .form-row { margin-bottom: 14px; }
.armely-api-development-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-api-development-page .form-row input, .armely-api-development-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-api-development-page .form-row input:focus, .armely-api-development-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-api-development-page .form-row select option { background: #fff; color: #1A2540; }
.armely-api-development-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-api-development-page .form-submit:hover { background: var(--blue-lt); }
.armely-api-development-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* FOOTER */
.armely-api-development-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-api-development-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-api-development-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-api-development-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-api-development-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-api-development-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-api-development-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* RESPONSIVE */
  @media (max-width: 900px) {
.armely-api-development-page nav { padding: 16px 24px; }
.armely-api-development-page .nav-links { display: none; }
.armely-api-development-page section { padding: 72px 24px; }
.armely-api-development-page .hero { padding: 110px 24px 72px; }
.armely-api-development-page .intro-grid, .armely-api-development-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-api-development-page .delivers-grid, .armely-api-development-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-api-development-page .steps-row { grid-template-columns: 1fr; }
.armely-api-development-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-api-development-page .step:last-child { border-bottom: none; }
.armely-api-development-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-api-development-page .testimonials { padding: 72px 24px; }
.armely-api-development-page .testi-grid { grid-template-columns: 1fr; }
.armely-api-development-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-api-development-page .delivers-grid, .armely-api-development-page .uc-grid { grid-template-columns: 1fr; }
.armely-api-development-page .partner-stats { grid-template-columns: 1fr; }
.armely-api-development-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-api-development-page {
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
.armely-api-development-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-api-development-page .hero::after {
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
.armely-api-development-page .section-title,
.armely-api-development-page .deliver-title,
.armely-api-development-page .uc-title,
.armely-api-development-page .step-title,
.armely-api-development-page .why-item-title,
.armely-api-development-page .form-title {
  color: #162b49;
}
.armely-api-development-page .deliver-card,
.armely-api-development-page .uc-card,
.armely-api-development-page .testi-card,
.armely-api-development-page .platform-card,
.armely-api-development-page .partner-block,
.armely-api-development-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-api-development-page .deliver-card:hover,
.armely-api-development-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-api-development-page .btn-primary,
.armely-api-development-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-api-development-page .btn-primary:hover,
.armely-api-development-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-api-development-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-api-development-page nav,
.armely-api-development-page footer {
  display: none;
}
</style>
<div class="armely-api-development-page">
<!-- NAV -->


<!-- HERO -->
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-eyebrow">
    <span class="eyebrow-badge">API Development and Integration</span>
    <span class="eyebrow-partner">Certified Microsoft and Azure partner</span>
  </div>
  <h1>Connect your systems.<br>Expose your data.<br><span class="hl">Stop rebuilding the same thing twice.</span></h1>
  <p class="hero-sub">Armely designs, builds, and manages APIs that connect your business systems, expose your data to internal and external consumers, and form the integration layer your AI and automation investments depend on.</p>
  <div class="hero-actions">
    <a href="#contact" class="btn-primary">Book a Free Discovery Call</a>
    <a href="#what-we-deliver" class="btn-outline">See What We Build</a>
  </div>
  <div class="hero-trust">
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>REST, GraphQL, gRPC, and SOAP</strong> across all environments</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>OpenAPI-first</strong> design on every engagement</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">Managed through <strong>Azure API Management</strong></span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">AI-ready with <strong>MCP and agent-to-agent</strong> support</span>
    </div>
  </div>
</section>

<!-- WHAT WE BUILD -->
<section class="intro">
  <div class="section-inner">
    <div class="intro-grid">
      <div>
        <div class="section-eyebrow">What We Build</div>
        <h2 class="section-title">The right API design for the right job. No defaults, no overengineering.</h2>
        <p class="section-body">REST remains the standard for 89% of enterprise APIs and is Armely's default for most business integration work. We use GraphQL where data flexibility requirements justify it, gRPC for high-performance internal service communication, and SOAP where legacy systems or regulated industry requirements demand it. Every engagement starts with understanding the consumer before choosing the architecture.</p>
        <div class="api-types">
          <div class="api-type primary">
            <span class="api-type-badge">Default</span>
            <div>
              <div class="api-type-title">REST APIs</div>
              <div class="api-type-desc">The standard for business integration. Stateless, cacheable, and supported by every platform and toolchain. We deliver OpenAPI specifications alongside working code so every consumer has a contract they can build against.</div>
            </div>
          </div>
          <div class="api-type">
            <span class="api-type-badge">Flexible data</span>
            <div>
              <div class="api-type-title">GraphQL APIs</div>
              <div class="api-type-desc">When clients need to query complex, related data without multiple round trips. Appropriate for applications consuming data from several sources simultaneously, where over-fetching from fixed REST endpoints creates performance or bandwidth problems.</div>
            </div>
          </div>
          <div class="api-type">
            <span class="api-type-badge">Internal services</span>
            <div>
              <div class="api-type-title">gRPC and Microservices</div>
              <div class="api-type-desc">For high-throughput internal service communication where latency and payload size matter. Used in microservice architectures where services need to communicate at scale without the overhead of REST.</div>
            </div>
          </div>
          <div class="api-type">
            <span class="api-type-badge">Legacy and regulated</span>
            <div>
              <div class="api-type-title">SOAP and Enterprise Integration</div>
              <div class="api-type-desc">Where existing enterprise systems, financial platforms, or regulated industry requirements specify SOAP. We also build wrapper APIs that expose legacy SOAP services as modern REST endpoints for consuming applications.</div>
            </div>
          </div>
        </div>
      </div>
      <div>
        <div class="lifecycle-card">
          <div class="lifecycle-header">
            <div class="lifecycle-dots"><span></span><span></span><span></span></div>
            <span class="lifecycle-title">API Delivery Lifecycle</span>
          </div>
          <div class="lifecycle-body">
            <div class="lifecycle-step">
              <div class="lifecycle-step-num">1</div>
              <div>
                <div class="lifecycle-step-title">Design and Specification</div>
                <div class="lifecycle-step-desc">OpenAPI specification written before code. Consumer teams receive a contract and mock server to build against while the API is in development.</div>
              </div>
            </div>
            <div class="lifecycle-step">
              <div class="lifecycle-step-num">2</div>
              <div>
                <div class="lifecycle-step-title">Security by Default</div>
                <div class="lifecycle-step-desc">OAuth 2.0 or API key authentication, input validation, rate limiting, and least-privilege access controls applied at the design stage, not retrofitted after launch.</div>
              </div>
            </div>
            <div class="lifecycle-step">
              <div class="lifecycle-step-num">3</div>
              <div>
                <div class="lifecycle-step-title">Build and Test</div>
                <div class="lifecycle-step-desc">Implementation with automated test suites generated from the OpenAPI specification. Contract tests confirm the API behaves as documented before any consumer integrates.</div>
              </div>
            </div>
            <div class="lifecycle-step">
              <div class="lifecycle-step-num">4</div>
              <div>
                <div class="lifecycle-step-title">Gateway and Versioning</div>
                <div class="lifecycle-step-desc">Deployed behind Azure API Management with rate limiting, analytics, developer portal, and a versioning strategy that lets the API evolve without breaking existing consumers.</div>
              </div>
            </div>
            <div class="lifecycle-step">
              <div class="lifecycle-step-num">5</div>
              <div>
                <div class="lifecycle-step-title">Documentation and Handover</div>
                <div class="lifecycle-step-desc">Living documentation published to a developer portal. Consumer onboarding guides, authentication walkthroughs, and code samples in relevant languages.</div>
              </div>
            </div>
            <div class="apim-callout">
              <span style="font-size:1.2rem;">🤖</span>
              <div class="apim-callout-text"><strong>Azure API Management</strong> now supports MCP server management and agent-to-agent communication, making it the governance layer for AI workloads as well as traditional APIs.</div>
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
    <h2 class="section-title">API development across the full engagement lifecycle.</h2>
    <p class="section-body">From a single integration connecting two systems to a managed API program governing dozens of endpoints, Armely covers every stage with certified engineers and a delivery process built around the OpenAPI standard.</p>
    <div class="delivers-grid">
      <div class="deliver-card">
        <div class="deliver-icon">📐</div>
        <div class="deliver-title">API Design and Architecture</div>
        <div class="deliver-desc">We design your API before writing code. OpenAPI specifications, data models, error schemas, authentication patterns, and versioning strategy are agreed and documented upfront so every consumer knows exactly what to expect before integration begins.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">⚙️</div>
        <div class="deliver-title">Custom API Development</div>
        <div class="deliver-desc">We build REST, GraphQL, and gRPC APIs in .NET, Python, and Node.js, with automated test suites, CI/CD pipelines, and deployment to Azure App Service, Azure Functions, or containerized environments depending on your workload requirements.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🔗</div>
        <div class="deliver-title">System Integration</div>
        <div class="deliver-desc">We build the integration layer between your business systems, connecting ERP, CRM, databases, SaaS platforms, and third-party services through reliable, monitored API connections so data flows automatically rather than being moved manually or by batch file.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🛡️</div>
        <div class="deliver-title">Azure API Management</div>
        <div class="deliver-desc">We deploy and configure Azure API Management as the gateway for your API estate, including authentication enforcement, rate limiting, analytics, developer portal setup, and policy configuration for security and traffic management at scale.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🤖</div>
        <div class="deliver-title">AI-Ready API Infrastructure</div>
        <div class="deliver-desc">We configure Azure API Management as an AI gateway for Azure OpenAI endpoints and AI agents, including token governance, semantic caching, MCP server registration, and agent-to-agent communication policies for organizations building agentic AI applications.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">📊</div>
        <div class="deliver-title">API Audit and Remediation</div>
        <div class="deliver-desc">For organizations with existing APIs that lack documentation, versioning, security controls, or a governance framework, we conduct a structured audit and deliver a remediation plan that brings your API estate to a production-ready standard.</div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNEY -->
<section class="journey" id="journey">
  <div class="section-inner">
    <div class="section-eyebrow">The Armely API Delivery Process</div>
    <h2 class="section-title">From requirements to a production API your consumers can rely on.</h2>
    <p class="section-body">APIs built without a specification upfront and security designed in from the start create technical debt that compounds every time a new consumer integrates. Our process is structured to prevent those problems rather than fix them later.</p>
    <div class="steps-row">
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-title">Discovery</div>
        <div class="step-desc">We document the consumer requirements, data sources, authentication needs, and performance expectations before any design decisions are made.</div>
        <span class="step-tag">Free</span>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-title">Specification</div>
        <div class="step-desc">OpenAPI specification, data model, error schema, and versioning strategy produced and reviewed. Mock server made available to consumer teams immediately.</div>
        <span class="step-tag">Week 1</span>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-title">Build and Test</div>
        <div class="step-desc">Implementation against the agreed specification with contract tests, unit tests, and security scanning integrated into the CI/CD pipeline from the start.</div>
        <span class="step-tag">Weeks 2-5</span>
      </div>
      <div class="step">
        <div class="step-num">04</div>
        <div class="step-title">Gateway and Launch</div>
        <div class="step-desc">Deployed behind Azure API Management with authentication, rate limiting, and analytics configured. Developer portal and consumer documentation published.</div>
        <span class="step-tag">Week 6</span>
      </div>
      <div class="step">
        <div class="step-num">05</div>
        <div class="step-title">Support and Evolution</div>
        <div class="step-desc">Ongoing monitoring, performance optimization, versioned updates that do not break existing consumers, and Armely support for new integration requirements.</div>
        <span class="step-tag">Ongoing</span>
      </div>
    </div>
  </div>
</section>

<!-- USE CASES -->
<section class="usecases">
  <div class="section-inner">
    <div class="section-eyebrow">Common Engagements</div>
    <h2 class="section-title">The integration and API problems we solve most frequently.</h2>
    <p class="section-body">Most API engagements start with a specific business problem, not a technology preference. These are the situations we encounter most often across our client base.</p>
    <div class="uc-grid">
      <div class="uc-card">
        <span class="uc-icon">🔗</span>
        <div class="uc-title">Connect Two Systems That Do Not Talk</div>
        <div class="uc-desc">CRM and ERP that require manual data re-entry between them. A field service platform and a billing system that require exports and imports. We build the API integration layer that eliminates the manual step and keeps data consistent in real time.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">🌐</span>
        <div class="uc-title">Expose Internal Data to External Consumers</div>
        <div class="uc-desc">A secure, documented API that allows customers, partners, or third-party applications to access your business data within defined permission boundaries, with authentication, rate limiting, and usage analytics managed through Azure API Management.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">🏗️</span>
        <div class="uc-title">Modernize a Legacy Integration</div>
        <div class="uc-desc">A SOAP service or point-to-point database integration that has no documentation, no versioning, and no monitoring. We wrap it in a modern REST API, document it properly, and place it behind a gateway so it can be maintained and evolved safely.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">🤖</span>
        <div class="uc-title">Build the API Layer for an AI Agent</div>
        <div class="uc-desc">AI agents need governed, authenticated access to business systems. We build and register the APIs that your Copilot Studio or Azure AI Foundry agents call to retrieve data, trigger actions, and write results back to source systems.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">📱</span>
        <div class="uc-title">Backend for a Mobile or Web Application</div>
        <div class="uc-desc">A secure, performant API backend for a mobile application or web portal, designed API-first so the front-end team can build against a specification and mock server while the backend is in development, rather than waiting for working code.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">🔍</span>
        <div class="uc-title">API Estate Audit</div>
        <div class="uc-desc">Organizations that have accumulated APIs over time often have endpoints without documentation, inconsistent authentication, no versioning strategy, and no central governance. We audit the estate, identify risk, and deliver a structured remediation plan.</div>
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
        <p class="testi-body">We had been manually exporting data between our ERP and CRM every evening for years. Armely built a REST API integration that keeps both systems synchronized in real time. The project was delivered in five weeks with full OpenAPI documentation, and our operations team has not touched an export file since go-live.</p>
        <div class="testi-footer">
          <div class="testi-avatar">CTO</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Chief Technology Officer</div>
            <div class="testi-role">Distribution Company, Texas</div>
          </div>
        </div>
      </div>

      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">We needed to expose patient data to a third-party analytics platform in a HIPAA-compliant way. Armely designed and built a secure REST API with OAuth 2.0 authentication, field-level data masking, and full audit logging managed through Azure API Management. The security review passed on the first submission.</p>
        <div class="testi-footer">
          <div class="testi-avatar">IT</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Director of IT</div>
            <div class="testi-role">Healthcare Organization, Midwest</div>
          </div>
        </div>
      </div>

      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">Our AI agent project stalled because we had no clean API layer for the agent to call into our business systems. Armely built three APIs in four weeks that gave the agent governed access to our CRM, document store, and scheduling system. The agent went live the following month and the API work was never a blocker again.</p>
        <div class="testi-footer">
          <div class="testi-avatar">VP</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">VP of Technology</div>
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
        <h2 class="section-title">API development is only valuable if the API is reliable, documented, and maintainable.</h2>
        <p class="section-body">Many organizations have APIs that work but are undocumented, have no versioning strategy, and would break consumers if changed. Armely builds APIs that are production-grade from the first deployment.</p>
        <ul class="why-list">
          <li>
            <div class="why-icon">📐</div>
            <div>
              <div class="why-item-title">OpenAPI-First on Every Engagement</div>
              <div class="why-item-desc">We write the specification before writing code. Consumer teams receive a documented contract and a mock server immediately, rather than waiting for working code before integration work can begin. This is not optional for us.</div>
            </div>
          </li>
          <li>
            <div class="why-icon">🛡️</div>
            <div>
              <div class="why-item-title">Security Designed In, Not Added On</div>
              <div class="why-item-desc">Authentication, authorization, input validation, and rate limiting are designed into the API specification before implementation begins. We do not retrofit security after a functional build is complete.</div>
            </div>
          </li>
          <li>
            <div class="why-icon">🔗</div>
            <div>
              <div class="why-item-title">Microsoft Ecosystem Integration</div>
              <div class="why-item-desc">Our APIs integrate natively with Azure API Management, Microsoft 365, Dynamics 365, Power Platform, and Azure AI Foundry. If your business runs on Microsoft, Armely builds APIs that fit the architecture you already have rather than introducing new platform dependencies.</div>
            </div>
          </li>
          <li>
            <div class="why-icon">📈</div>
            <div>
              <div class="why-item-title">Versioning and Governance from Day One</div>
              <div class="why-item-desc">We implement a versioning strategy, deprecation policy, and API gateway configuration in the initial deployment so your API estate can evolve over time without breaking existing consumers or accumulating ungoverned endpoints.</div>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <div class="partner-block">
          <div class="partner-block-top">
            <div class="partner-label">Microsoft Authorized Partner</div>
            <p class="partner-text">Armely's Microsoft partnership gives us certified access to Azure API Management, Azure Functions, Azure App Service, and the broader Azure integration platform. We build API infrastructure on the same managed services that Microsoft's enterprise customers rely on, with the licensing and technical support that partnership provides.</p>
          </div>
          <div class="partner-stats">
            <div class="p-stat">
              <div class="p-stat-num">89<span>%</span></div>
              <div class="p-stat-label">of enterprise organizations use REST as their primary API format (Postman, 2025)</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">&#8470;1</div>
              <div class="p-stat-label">Azure API Management named a Gartner Magic Quadrant Leader for Integration PaaS, 2026</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">AI</div>
              <div class="p-stat-label">Azure APIM now governs AI models, MCP servers, and agent-to-agent communication</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">0</div>
              <div class="p-stat-label">APIs delivered by Armely without an OpenAPI specification and a versioning strategy</div>
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
      <h2 class="section-title">Tell us the integration problem. We will design the API.</h2>
      <p class="section-body">Book a free 30-minute discovery call. We will review your integration requirements, recommend an API architecture, and come back with a delivery proposal and Azure licensing recommendation at no obligation.</p>
      <div style="margin-top: 28px; display: flex; flex-direction: column; gap: 12px;">
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Free discovery, no commitment required</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">API architecture recommendation included</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Response within one business day</span>
        </div>
      </div>
    </div>
    <div class="cta-form">
      <div class="form-title">Book Your Free Discovery Call</div>
      <div class="form-sub">Tell us about your integration or API challenge.</div>
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
          <option>Connect two or more business systems</option>
          <option>Build a new API for internal or external consumers</option>
          <option>Expose our data securely to a third party</option>
          <option>Build the API layer for an AI agent</option>
          <option>Modernize or document an existing API</option>
          <option>Set up Azure API Management</option>
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

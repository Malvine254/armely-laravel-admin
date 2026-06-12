<style>

.armely-sharepoint-page *, .armely-sharepoint-page *::before, .armely-sharepoint-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-sharepoint-page {
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

.armely-sharepoint-page { scroll-behavior: smooth; }
.armely-sharepoint-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* NAV */
.armely-sharepoint-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-sharepoint-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-sharepoint-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-sharepoint-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-sharepoint-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-sharepoint-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-sharepoint-page .nav-links a:hover { color: #fff; }
.armely-sharepoint-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-sharepoint-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* HERO */
.armely-sharepoint-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-sharepoint-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-sharepoint-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-sharepoint-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-sharepoint-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-sharepoint-page .hero h1 { font-size: clamp(2.6rem, 5.5vw, 4.8rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 780px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-sharepoint-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-sharepoint-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 560px; margin-bottom: 40px; line-height: 1.8; }
.armely-sharepoint-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-sharepoint-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-sharepoint-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-sharepoint-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-sharepoint-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-sharepoint-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-sharepoint-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-sharepoint-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-sharepoint-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-sharepoint-page .trust-text strong { color: #fff; font-weight: 600; }

  /* SECTIONS */
.armely-sharepoint-page section { padding: 96px 56px; }
.armely-sharepoint-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-sharepoint-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-sharepoint-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 620px; }
.armely-sharepoint-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 540px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* WHAT IS SHAREPOINT */
.armely-sharepoint-page .intro { background: var(--navy-mid); }
.armely-sharepoint-page .intro-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }

.armely-sharepoint-page .cap-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 28px; }
.armely-sharepoint-page .cap-card { background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 16px; transition: border-color 0.2s; }
.armely-sharepoint-page .cap-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-sharepoint-page .cap-card-icon { font-size: 1.1rem; margin-bottom: 8px; display: block; }
.armely-sharepoint-page .cap-card-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 4px; }
.armely-sharepoint-page .cap-card-desc { font-size: 0.75rem; color: var(--text-muted); line-height: 1.5; }

  /* Site type visual */
.armely-sharepoint-page .site-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-sharepoint-page .site-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-sharepoint-page .site-dots { display: flex; gap: 6px; }
.armely-sharepoint-page .site-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-sharepoint-page .site-header-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-sharepoint-page .site-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.armely-sharepoint-page .site-type { border-radius: 9px; padding: 14px 16px; display: flex; align-items: flex-start; gap: 12px; border: 1px solid var(--border); }
.armely-sharepoint-page .site-type.active { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-sharepoint-page .site-type-icon { font-size: 1.2rem; flex-shrink: 0; margin-top: 1px; }
.armely-sharepoint-page .site-type-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 2px; }
.armely-sharepoint-page .site-type-desc { font-size: 0.73rem; color: var(--text-muted); line-height: 1.45; }
.armely-sharepoint-page .ai-callout { background: var(--blue); border-radius: 9px; padding: 14px 16px; display: flex; align-items: center; gap: 12px; margin-top: 4px; }
.armely-sharepoint-page .ai-callout-text { font-size: 0.82rem; color: rgba(255,255,255,0.9); line-height: 1.5; }
.armely-sharepoint-page .ai-callout-text strong { color: #fff; }

  /* DELIVERS */
.armely-sharepoint-page .delivers { background: var(--navy); }
.armely-sharepoint-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-sharepoint-page .deliver-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-sharepoint-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-sharepoint-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-sharepoint-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-sharepoint-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-sharepoint-page .journey { background: var(--navy-mid); }
.armely-sharepoint-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-sharepoint-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-sharepoint-page .step:last-child { border-right: none; }
.armely-sharepoint-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-sharepoint-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-sharepoint-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-sharepoint-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* USE CASES */
.armely-sharepoint-page .usecases { background: var(--navy); }
.armely-sharepoint-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-sharepoint-page .uc-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-sharepoint-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-sharepoint-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-sharepoint-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-sharepoint-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* TESTIMONIALS */
.armely-sharepoint-page .testimonials { background: var(--navy-mid); padding: 96px 56px; }
.armely-sharepoint-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-sharepoint-page .testi-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-sharepoint-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-sharepoint-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-sharepoint-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-sharepoint-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-sharepoint-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-sharepoint-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-sharepoint-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY ARMELY */
.armely-sharepoint-page .why { background: var(--navy); }
.armely-sharepoint-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-sharepoint-page .why-list { list-style: none; margin-top: 36px; }
.armely-sharepoint-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-sharepoint-page .why-list li:last-child { border-bottom: none; }
.armely-sharepoint-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-sharepoint-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-sharepoint-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-sharepoint-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-sharepoint-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-sharepoint-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-sharepoint-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-sharepoint-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-sharepoint-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-sharepoint-page .p-stat:nth-child(2) { border-right: none; }
.armely-sharepoint-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-sharepoint-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-sharepoint-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-sharepoint-page .p-stat-num span { color: var(--blue); }
.armely-sharepoint-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* CTA */
.armely-sharepoint-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-sharepoint-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-sharepoint-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-sharepoint-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-sharepoint-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-sharepoint-page .form-row { margin-bottom: 14px; }
.armely-sharepoint-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-sharepoint-page .form-row input, .armely-sharepoint-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-sharepoint-page .form-row input:focus, .armely-sharepoint-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-sharepoint-page .form-row select option { background: #fff; color: #1A2540; }
.armely-sharepoint-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-sharepoint-page .form-submit:hover { background: var(--blue-lt); }
.armely-sharepoint-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* FOOTER */
.armely-sharepoint-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-sharepoint-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-sharepoint-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-sharepoint-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-sharepoint-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-sharepoint-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-sharepoint-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* RESPONSIVE */
  @media (max-width: 900px) {
.armely-sharepoint-page nav { padding: 16px 24px; }
.armely-sharepoint-page .nav-links { display: none; }
.armely-sharepoint-page section { padding: 72px 24px; }
.armely-sharepoint-page .hero { padding: 110px 24px 72px; }
.armely-sharepoint-page .intro-grid, .armely-sharepoint-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-sharepoint-page .delivers-grid, .armely-sharepoint-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-sharepoint-page .steps-row { grid-template-columns: 1fr; }
.armely-sharepoint-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-sharepoint-page .step:last-child { border-bottom: none; }
.armely-sharepoint-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-sharepoint-page .testimonials { padding: 72px 24px; }
.armely-sharepoint-page .testi-grid { grid-template-columns: 1fr; }
.armely-sharepoint-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-sharepoint-page .delivers-grid, .armely-sharepoint-page .uc-grid, .armely-sharepoint-page .cap-grid { grid-template-columns: 1fr; }
.armely-sharepoint-page .partner-stats { grid-template-columns: 1fr; }
.armely-sharepoint-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-sharepoint-page {
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
.armely-sharepoint-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-sharepoint-page .hero::after {
  content: '';
  position: absolute;
  inset: auto 8% 8% auto;
  width: min(340px, 48vw);
  height: min(340px, 48vw);
  border-radius: 0;
  background: rgba(255, 255, 255, 0.10);
  filter: blur(2px);
  pointer-events: none;
}
.armely-sharepoint-page .section-title,
.armely-sharepoint-page .deliver-title,
.armely-sharepoint-page .uc-title,
.armely-sharepoint-page .step-title,
.armely-sharepoint-page .why-item-title,
.armely-sharepoint-page .form-title {
  color: #162b49;
}
.armely-sharepoint-page .deliver-card,
.armely-sharepoint-page .uc-card,
.armely-sharepoint-page .testi-card,
.armely-sharepoint-page .platform-card,
.armely-sharepoint-page .partner-block,
.armely-sharepoint-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-sharepoint-page .deliver-card:hover,
.armely-sharepoint-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-sharepoint-page .btn-primary,
.armely-sharepoint-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-sharepoint-page .btn-primary:hover,
.armely-sharepoint-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-sharepoint-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-sharepoint-page nav,
.armely-sharepoint-page footer {
  display: none;
}
.armely-sharepoint-page .hero {
  min-height: 82vh;
  display: grid;
  grid-template-columns: minmax(0, 1fr) minmax(320px, 430px);
  align-items: center;
  gap: clamp(44px, 7vw, 96px);
  padding: 126px clamp(24px, 5vw, 72px) 92px;
  background:
    linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px) 0 0 / 88px 88px,
    linear-gradient(135deg, #102848 0%, #173b67 52%, #2f5597 100%);
  border-radius: 0;
}
.armely-sharepoint-page .hero::after {
  content: '';
  position: absolute;
  inset: 0 0 auto auto;
  width: 34%;
  height: 100%;
  background: linear-gradient(135deg, transparent 0%, rgba(255,255,255,0.06) 50%, transparent 50.2%);
  border-radius: 0;
  filter: none;
  pointer-events: none;
}
.armely-sharepoint-page .hero-bg-glow {
  display: none;
}
.armely-sharepoint-page .hero-copy,
.armely-sharepoint-page .hero-system {
  position: relative;
  z-index: 1;
}
.armely-sharepoint-page .hero-eyebrow {
  gap: 0;
}
.armely-sharepoint-page .eyebrow-partner {
  display: none;
}
.armely-sharepoint-page .hero h1 {
  max-width: 780px;
  font-size: clamp(2.55rem, 5.1vw, 4.55rem);
}
.armely-sharepoint-page .hero-sub {
  max-width: 590px;
  margin-bottom: 34px;
}
.armely-sharepoint-page .hero-actions {
  margin-bottom: 42px;
}
.armely-sharepoint-page .hero-trust {
  display: flex;
  max-width: 720px;
  gap: 18px 30px;
  padding-top: 28px;
}
.armely-sharepoint-page .hero-trust .trust-item {
  max-width: 220px;
}
.armely-sharepoint-page .hero-trust .trust-text {
  color: rgba(255,255,255,0.72);
}
.armely-sharepoint-page .sharepoint-workspace-card {
  position: relative;
  z-index: 1;
  width: 100%;
  background: rgba(255,255,255,0.97);
  border: 1px solid rgba(255,255,255,0.42);
  border-radius: 14px;
  box-shadow: 0 26px 70px rgba(5, 18, 36, 0.28);
  overflow: hidden;
}
.armely-sharepoint-page .workspace-card-header {
  padding: 18px 20px;
  border-bottom: 1px solid var(--border);
  background: #f5f8fc;
}
.armely-sharepoint-page .workspace-card-kicker {
  color: var(--blue);
  font-size: 0.68rem;
  font-weight: 800;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  margin-bottom: 6px;
}
.armely-sharepoint-page .workspace-card-title {
  color: #162b49;
  font-size: 1.05rem;
  font-weight: 800;
  line-height: 1.35;
}
.armely-sharepoint-page .workspace-card-body {
  padding: 20px;
}
.armely-sharepoint-page .workspace-flow {
  display: grid;
  gap: 10px;
}
.armely-sharepoint-page .workspace-flow-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 14px 16px;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 10px;
}
.armely-sharepoint-page .workspace-flow-item strong {
  color: #162b49;
  font-size: 0.88rem;
}
.armely-sharepoint-page .workspace-flow-item span {
  color: var(--text-muted);
  font-size: 0.76rem;
  font-weight: 600;
}
.armely-sharepoint-page .workspace-divider {
  height: 1px;
  margin: 18px 0;
  background: var(--border);
}
.armely-sharepoint-page .workspace-note {
  color: var(--text-body);
  font-size: 0.86rem;
  line-height: 1.65;
}
.armely-sharepoint-page .workspace-note strong {
  color: var(--blue);
}
@media (max-width: 980px) {
  .armely-sharepoint-page .hero {
    grid-template-columns: 1fr;
    min-height: auto;
    padding: 112px 24px 72px;
  }
}
@media (max-width: 700px) {
  .armely-sharepoint-page .hero-trust {
    flex-direction: column;
  }
  .armely-sharepoint-page .hero-trust .trust-item {
    max-width: none;
  }
}
</style>
<div class="armely-sharepoint-page">
<!-- NAV -->


<!-- HERO -->
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-copy">
    <div class="hero-eyebrow">
      <span class="eyebrow-badge">Microsoft SharePoint</span>
      <span class="eyebrow-partner">Delivered by a certified Microsoft partner</span>
    </div>
    <h1>SharePoint environments your team can actually trust.</h1>
    <p class="hero-sub">Armely designs structured SharePoint intranets, document management systems, and knowledge sites that make content easier to find, govern, and use every day.</p>
    <div class="hero-actions">
      <a href="#contact" class="btn-primary">Book a Free Discovery Call</a>
      <a href="#what-we-deliver" class="btn-outline">See What We Build</a>
    </div>
    <div class="hero-trust">
      <div class="trust-item">
        <span class="trust-dot"></span>
        <span class="trust-text"><strong>Intranet</strong> design and build</span>
      </div>
      <div class="trust-item">
        <span class="trust-dot"></span>
        <span class="trust-text"><strong>Document</strong> governance</span>
      </div>
      <div class="trust-item">
        <span class="trust-dot"></span>
        <span class="trust-text"><strong>Microsoft 365</strong> integration</span>
      </div>
    </div>
  </div>
  <div class="hero-system" aria-hidden="true">
    <div class="sharepoint-workspace-card">
      <div class="workspace-card-header">
        <div class="workspace-card-kicker">Modern Digital Workplace</div>
        <div class="workspace-card-title">A clear home for pages, files, policies, and team knowledge.</div>
      </div>
      <div class="workspace-card-body">
        <div class="workspace-flow">
          <div class="workspace-flow-item"><strong>Intranet</strong><span>News and resources</span></div>
          <div class="workspace-flow-item"><strong>Libraries</strong><span>Versioned documents</span></div>
          <div class="workspace-flow-item"><strong>Governance</strong><span>Permissions and lifecycle</span></div>
        </div>
        <div class="workspace-divider"></div>
        <p class="workspace-note"><strong>Built by Armely:</strong> clean information architecture, modern pages, metadata, permissions, and launch support that drives adoption.</p>
      </div>
    </div>
  </div>
</section>

<!-- WHAT IS SHAREPOINT -->
<section class="intro">
  <div class="section-inner">
    <div class="intro-grid">
      <div>
        <div class="section-eyebrow">What is Microsoft SharePoint?</div>
        <h2 class="section-title">More than file storage. A structured digital workplace for your entire organization.</h2>
        <p class="section-body">SharePoint is Microsoft's web-based platform for document management, intranet portals, team collaboration, and business process automation. It is included in Microsoft 365 and integrates natively with Teams, Power Platform, and Dynamics 365. The 2026 experience introduces a redesigned interface centered on discovery, publishing, and building, with Copilot AI agents available directly on every SharePoint site.</p>
        <div class="cap-grid">
          <div class="cap-card">
            <span class="cap-card-icon"><i class="fa fa-file-text" aria-hidden="true"></i></span>
            <div class="cap-card-title">Document Management</div>
            <div class="cap-card-desc">Centralized, version-controlled storage with metadata tagging, co-authoring, and granular permissions at the site, library, and item level.</div>
          </div>
          <div class="cap-card">
            <span class="cap-card-icon"><i class="fa fa-home" aria-hidden="true"></i></span>
            <div class="cap-card-title">Intranet and Portals</div>
            <div class="cap-card-desc">Modern communication sites for company news, HR resources, policies, and employee tools, built to look professional without custom web development.</div>
          </div>
          <div class="cap-card">
            <span class="cap-card-icon"><i class="fa fa-search" aria-hidden="true"></i></span>
            <div class="cap-card-title">Enterprise Search</div>
            <div class="cap-card-desc">AI-powered search across all SharePoint content, Teams messages, and connected systems so employees find what they need without asking a colleague.</div>
          </div>
          <div class="cap-card">
            <span class="cap-card-icon"><i class="fa fa-lock" aria-hidden="true"></i></span>
            <div class="cap-card-title">Governance and Compliance</div>
            <div class="cap-card-desc">Sensitivity labels, retention policies, audit logs, and Microsoft Purview integration keep your content governed, compliant, and auditable.</div>
          </div>
        </div>
      </div>
      <div>
        <div class="site-card">
          <div class="site-header">
            <div class="site-dots"><span></span><span></span><span></span></div>
            <span class="site-header-title">SharePoint Site Types</span>
          </div>
          <div class="site-body">
            <div class="site-type active">
              <span class="site-type-icon"><i class="fa fa-bullhorn" aria-hidden="true"></i></span>
              <div>
                <div class="site-type-title">Communication Sites</div>
                <div class="site-type-desc">Company intranet, department homepages, HR portals, and policy hubs. Broadcast information to the whole organization with news, events, and announcements.</div>
              </div>
            </div>
            <div class="site-type">
              <span class="site-type-icon"><i class="fa fa-users" aria-hidden="true"></i></span>
              <div>
                <div class="site-type-title">Team Sites</div>
                <div class="site-type-desc">Project workspaces, department collaboration hubs, and document libraries for specific teams. Connected to Microsoft Teams channels automatically.</div>
              </div>
            </div>
            <div class="site-type">
              <span class="site-type-icon"><i class="fa fa-book" aria-hidden="true"></i></span>
              <div>
                <div class="site-type-title">Document Centers</div>
                <div class="site-type-desc">Centralized repositories for contracts, policies, standard operating procedures, and compliance documentation, with structured metadata and approval workflows.</div>
              </div>
            </div>
            <div class="site-type">
              <span class="site-type-icon"><i class="fa fa-building" aria-hidden="true"></i></span>
              <div>
                <div class="site-type-title">Hub Sites</div>
                <div class="site-type-desc">Connect related sites under a shared navigation, search scope, and visual identity, making large SharePoint environments coherent and navigable.</div>
              </div>
            </div>
            <div class="ai-callout">
              <span class="site-type-icon"><i class="fa fa-magic" aria-hidden="true"></i></span>
              <div class="ai-callout-text"><strong>Copilot agents</strong> are now built natively into SharePoint sites. Employees can ask questions and get answers from your site content in natural language, without digging through folders or calling the help desk.</div>
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
    <h2 class="section-title">SharePoint environments built to be used, not abandoned after launch.</h2>
    <p class="section-body">Most SharePoint projects fail not because of technology but because they were designed without a clear information architecture or adoption plan. Armely builds with both from the start.</p>
    <div class="delivers-grid">
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa fa-map" aria-hidden="true"></i></div>
        <div class="deliver-title">Information Architecture Design</div>
        <div class="deliver-desc">Before building a single site, we design your SharePoint structure, including site hierarchy, hub connections, metadata taxonomy, and navigation, so content is findable by anyone in the organization from day one.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa fa-home" aria-hidden="true"></i></div>
        <div class="deliver-title">Intranet Design and Build</div>
        <div class="deliver-desc">We design and build modern SharePoint intranets that employees actually use, including a branded homepage, department sites, HR and policy hubs, news feeds, and an employee directory. Professional design without custom development costs.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa fa-file-text" aria-hidden="true"></i></div>
        <div class="deliver-title">Document Management Systems</div>
        <div class="deliver-desc">We structure your document libraries with consistent metadata, naming conventions, version control, and permission models so teams stop emailing files and start collaborating in a single, governed location.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa fa-refresh" aria-hidden="true"></i></div>
        <div class="deliver-title">Migration from Legacy Systems</div>
        <div class="deliver-desc">We migrate content from file servers, older SharePoint versions, Google Drive, Box, and other platforms to SharePoint Online, with metadata preservation, permission mapping, and content clean-up before migration to avoid transferring years of clutter.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa fa-magic" aria-hidden="true"></i></div>
        <div class="deliver-title">Copilot Agent Configuration</div>
        <div class="deliver-desc">We configure and govern SharePoint Copilot agents that answer employee questions using your actual site content, policy documents, and knowledge bases, with appropriate permission scoping and governance controls applied before deployment.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa fa-shield" aria-hidden="true"></i></div>
        <div class="deliver-title">Governance and Ongoing Management</div>
        <div class="deliver-desc">SharePoint without governance becomes ungoverned quickly. We implement site lifecycle policies, external sharing controls, sensitivity labels, retention schedules, and admin tooling, with ongoing managed support to keep your environment clean.</div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNEY -->
<section class="journey" id="journey">
  <div class="section-inner">
    <div class="section-eyebrow">The Armely SharePoint Journey</div>
    <h2 class="section-title">From scattered files and outdated intranets to a digital workplace your team trusts.</h2>
    <p class="section-body">Armely has delivered SharePoint environments for school districts, healthcare systems, and enterprise clients. Our process is refined to avoid the two most common failure points: poor architecture upfront and low adoption after launch.</p>
    <div class="steps-row">
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-title">Discovery and Audit</div>
        <div class="step-desc">We review your current content landscape, pain points, and requirements. For migrations, we audit the source environment to understand scope and clean-up needs.</div>
        <span class="step-tag">Free</span>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-title">Architecture and Design</div>
        <div class="step-desc">We design your site hierarchy, navigation, metadata model, and permission structure, and present wireframes for key pages before any build begins.</div>
        <span class="step-tag">Weeks 1-2</span>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-title">Build and Migrate</div>
        <div class="step-desc">Sites are built, branded, and configured. Content is migrated in structured phases with validation at each stage. Integrations with Teams and Power Platform are configured.</div>
        <span class="step-tag">Weeks 3-7</span>
      </div>
      <div class="step">
        <div class="step-num">04</div>
        <div class="step-title">Training and Launch</div>
        <div class="step-desc">Role-specific training for end users, site owners, and administrators. A managed launch with communications support to drive adoption from day one.</div>
        <span class="step-tag">Week 8</span>
      </div>
      <div class="step">
        <div class="step-num">05</div>
        <div class="step-title">Governance and Support</div>
        <div class="step-desc">Ongoing site owner support, governance reviews, new site requests managed within your architecture standards, and a dedicated Armely contact.</div>
        <span class="step-tag">Ongoing</span>
      </div>
    </div>
  </div>
</section>

<!-- USE CASES -->
<section class="usecases">
  <div class="section-inner">
    <div class="section-eyebrow">Common Engagements</div>
    <h2 class="section-title">The SharePoint challenges we help organizations solve.</h2>
    <p class="section-body">Every SharePoint engagement starts with a specific problem. These are the situations we encounter most often across our client base.</p>
    <div class="uc-grid">
      <div class="uc-card">
        <span class="uc-icon"><i class="fa fa-folder-open" aria-hidden="true"></i></span>
        <div class="uc-title">Replace the File Server</div>
        <div class="uc-desc">Organizations still running shared network drives face security gaps, no version history, and files that cannot be accessed remotely. We migrate to SharePoint Online with a clean structure, proper permissions, and Teams integration so staff can work from anywhere.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa fa-home" aria-hidden="true"></i></span>
        <div class="uc-title">Build a Modern Company Intranet</div>
        <div class="uc-desc">A SharePoint intranet that employees check daily rather than ignore. We design and build a branded company hub with news, announcements, department pages, quick links, an employee directory, and HR resources, configured for your organization's structure.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa fa-clipboard" aria-hidden="true"></i></span>
        <div class="uc-title">Centralize Policies and Procedures</div>
        <div class="uc-desc">We build governed policy libraries where documents go through a review and approval workflow before publishing, are version-controlled and date-stamped, and are searchable by all staff without emailing HR to ask where something is.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa fa-code-fork" aria-hidden="true"></i></span>
        <div class="uc-title">Migrate from an Older SharePoint</div>
        <div class="uc-desc">Organizations running on older on-premises SharePoint versions face increasing maintenance cost and security risk. We plan and execute migrations to SharePoint Online, modernizing page designs and cleaning up legacy content in the process.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa fa-users" aria-hidden="true"></i></span>
        <div class="uc-title">Project and Team Collaboration Sites</div>
        <div class="uc-desc">Structured SharePoint team sites for every department or project, connected to Teams channels, with consistent document libraries, task lists, and site templates that can be provisioned automatically when a new project or team is created.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa fa-comments" aria-hidden="true"></i></span>
        <div class="uc-title">AI-Powered Knowledge Search</div>
        <div class="uc-desc">We deploy and govern SharePoint Copilot agents scoped to specific knowledge bases, such as HR policies, IT procedures, or product documentation, so employees get accurate answers from your actual content rather than searching through folders.</div>
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
        <p class="testi-body">Armely partnered with us to implement a customized SharePoint Online environment that transformed how our staff collaborate and access information. The information architecture they designed made it immediately clear where everything belonged, and adoption across the district was strong from the first week. It was exactly what our organization needed.</p>
        <div class="testi-footer">
          <div class="testi-avatar">IT</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Director of Technology</div>
            <div class="testi-role">Plano Independent School District, Texas</div>
          </div>
        </div>
      </div>

      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">We had years of documents scattered across a shared drive with no consistent structure and no version control. Armely migrated everything to SharePoint Online, applied a metadata model that made content searchable, and built a governance framework so the problem does not return. The compliance team now has the audit trail they always needed.</p>
        <div class="testi-footer">
          <div class="testi-avatar">CO</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Chief Operating Officer</div>
            <div class="testi-role">Healthcare Services Organization, Midwest</div>
          </div>
        </div>
      </div>

      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">Our previous intranet had not been updated in years and staff had stopped using it. Armely redesigned and rebuilt it on modern SharePoint with a clear structure, department pages, and a news feed. Within a month of launch, traffic was up significantly and our HR team reported a meaningful drop in routine information requests from employees.</p>
        <div class="testi-footer">
          <div class="testi-avatar">HR</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">HR Director</div>
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
        <h2 class="section-title">SharePoint expertise backed by a real delivery track record.</h2>
        <p class="section-body">SharePoint is one of the most widely deployed platforms in the Microsoft ecosystem and one of the most commonly underutilized. Armely brings the architecture knowledge and adoption discipline to ensure your investment delivers lasting value.</p>
        <ul class="why-list">
          <li>
            <div class="why-icon"><i class="fa fa-bullseye" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Certified SharePoint Architects</div>
              <div class="why-item-desc">Our team holds Microsoft certifications in SharePoint and Microsoft 365 administration, with hands-on delivery experience across intranet builds, file server migrations, and governance implementations for organizations of varying sizes.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa fa-university" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Proven Delivery for Complex Organizations</div>
              <div class="why-item-desc">We have delivered SharePoint implementations for Plano ISD, Swope Health Systems, and the University of Nebraska Medical Center, organizations where large user populations, compliance requirements, and structured content management are all critical.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa fa-link" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Full Microsoft 365 Integration</div>
              <div class="why-item-desc">SharePoint works best as part of a connected Microsoft 365 environment. Armely designs SharePoint to integrate with Teams, Power Automate, Power Apps, and Dynamics 365 so your digital workplace functions as a unified system rather than a collection of separate tools.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa fa-line-chart" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Adoption as Part of Delivery</div>
              <div class="why-item-desc">A SharePoint site no one uses is a failed project regardless of how well it was built. We include change management, user communications, and role-specific training in every engagement because adoption is not optional.</div>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <div class="partner-block">
          <div class="partner-block-top">
            <div class="partner-label">Microsoft Authorized Partner</div>
            <p class="partner-text">Armely's Microsoft partnership gives us access to SharePoint and Microsoft 365 licensing, technical resources, and migration tooling that direct customers cannot access independently. That means better pricing, faster project delivery, and implementations aligned with Microsoft's own recommended architecture patterns.</p>
          </div>
          <div class="partner-stats">
            <div class="p-stat">
              <div class="p-stat-num">190<span>M</span></div>
              <div class="p-stat-label">people use SharePoint across more than 200,000 organizations worldwide</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">25<span>+</span></div>
              <div class="p-stat-label">years of SharePoint platform history and enterprise adoption</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">M365</div>
              <div class="p-stat-label">included in most Microsoft 365 subscriptions at no additional license cost</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">AI</div>
              <div class="p-stat-label">Copilot agents now built natively into SharePoint sites in the 2026 experience</div>
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
      <h2 class="section-title">Tell us your SharePoint challenge. We will show you the path forward.</h2>
      <p class="section-body">Book a free 30-minute discovery call. We will review your current environment, understand what you are trying to achieve, and come back with a clear SharePoint proposal and Microsoft 365 licensing recommendation at no obligation.</p>
      <div style="margin-top: 28px; display: flex; flex-direction: column; gap: 12px;">
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Free discovery, no commitment required</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Architecture recommendation and partner pricing included</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Response within one business day</span>
        </div>
      </div>
    </div>
    <div class="cta-form">
      <div class="form-title">Book Your Free Discovery Call</div>
      <div class="form-sub">Tell us about your current SharePoint situation.</div>
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
          <option>Build or redesign our company intranet</option>
          <option>Migrate from a file server or shared drive</option>
          <option>Migrate from an older SharePoint version</option>
          <option>Set up document management and governance</option>
          <option>Configure Copilot agents on SharePoint</option>
          <option>Connect SharePoint to Teams or Power Platform</option>
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

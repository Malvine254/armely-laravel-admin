<style>

.armely-custom-development-page *, .armely-custom-development-page *::before, .armely-custom-development-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-custom-development-page {
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

.armely-custom-development-page { scroll-behavior: smooth; }
.armely-custom-development-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* NAV */
.armely-custom-development-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-custom-development-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-custom-development-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-custom-development-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-custom-development-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-custom-development-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-custom-development-page .nav-links a:hover { color: #fff; }
.armely-custom-development-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-custom-development-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* HERO */
.armely-custom-development-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-custom-development-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-custom-development-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-custom-development-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-custom-development-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-custom-development-page .hero h1 { font-size: clamp(2.6rem, 5.5vw, 4.8rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-custom-development-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-custom-development-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.8; }
.armely-custom-development-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-custom-development-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-custom-development-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-custom-development-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-custom-development-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-custom-development-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-custom-development-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-custom-development-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-custom-development-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-custom-development-page .trust-text strong { color: #fff; font-weight: 600; }

  /* SECTIONS */
.armely-custom-development-page section { padding: 96px 56px; }
.armely-custom-development-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-custom-development-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-custom-development-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 640px; }
.armely-custom-development-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* WHEN TO BUILD */
.armely-custom-development-page .when { background: var(--navy-mid); }
.armely-custom-development-page .when-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }

.armely-custom-development-page .decision-cards { display: flex; flex-direction: column; gap: 10px; margin-top: 28px; }
.armely-custom-development-page .decision-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; transition: border-color 0.2s; }
.armely-custom-development-page .decision-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-custom-development-page .decision-card.alt { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-custom-development-page .decision-icon { font-size: 1.2rem; flex-shrink: 0; margin-top: 2px; }
.armely-custom-development-page .decision-title { font-size: 0.875rem; font-weight: 700; color: #1A2540; margin-bottom: 3px; }
.armely-custom-development-page .decision-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }

  /* Tech stack visual */
.armely-custom-development-page .stack-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-custom-development-page .stack-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-custom-development-page .stack-dots { display: flex; gap: 6px; }
.armely-custom-development-page .stack-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-custom-development-page .stack-header-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-custom-development-page .stack-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.armely-custom-development-page .stack-row { border-radius: 9px; padding: 13px 16px; }
.armely-custom-development-page .stack-row-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
.armely-custom-development-page .stack-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.armely-custom-development-page .stack-chip { font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.armely-custom-development-page .row-front { background: var(--blue-dim); }
.armely-custom-development-page .row-front .stack-row-label { color: var(--blue); }
.armely-custom-development-page .row-front .stack-chip { background: var(--blue-dim2); color: var(--blue); }
.armely-custom-development-page .row-back { background: rgba(41,78,139,0.05); }
.armely-custom-development-page .row-back .stack-row-label { color: var(--blue); }
.armely-custom-development-page .row-back .stack-chip { background: rgba(41,78,139,0.1); color: var(--blue); }
.armely-custom-development-page .row-infra { background: var(--blue); }
.armely-custom-development-page .row-infra .stack-row-label { color: rgba(255,255,255,0.7); }
.armely-custom-development-page .row-infra .stack-chip { background: rgba(255,255,255,0.15); color: #fff; }
.armely-custom-development-page .row-arrow { text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2px 0; }

  /* DELIVERS */
.armely-custom-development-page .delivers { background: var(--navy); }
.armely-custom-development-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-custom-development-page .deliver-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-custom-development-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-custom-development-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-custom-development-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-custom-development-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-custom-development-page .journey { background: var(--navy-mid); }
.armely-custom-development-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-custom-development-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-custom-development-page .step:last-child { border-right: none; }
.armely-custom-development-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-custom-development-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-custom-development-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-custom-development-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* USE CASES */
.armely-custom-development-page .usecases { background: var(--navy); }
.armely-custom-development-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-custom-development-page .uc-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-custom-development-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-custom-development-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-custom-development-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-custom-development-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* TESTIMONIALS */
.armely-custom-development-page .testimonials { background: var(--navy-mid); padding: 96px 56px; }
.armely-custom-development-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-custom-development-page .testi-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-custom-development-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-custom-development-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-custom-development-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-custom-development-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-custom-development-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-custom-development-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-custom-development-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY ARMELY */
.armely-custom-development-page .why { background: var(--navy); }
.armely-custom-development-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-custom-development-page .why-list { list-style: none; margin-top: 36px; }
.armely-custom-development-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-custom-development-page .why-list li:last-child { border-bottom: none; }
.armely-custom-development-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-custom-development-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-custom-development-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-custom-development-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-custom-development-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-custom-development-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-custom-development-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-custom-development-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-custom-development-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-custom-development-page .p-stat:nth-child(2) { border-right: none; }
.armely-custom-development-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-custom-development-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-custom-development-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-custom-development-page .p-stat-num span { color: var(--blue); }
.armely-custom-development-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* CTA */
.armely-custom-development-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-custom-development-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-custom-development-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-custom-development-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-custom-development-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-custom-development-page .form-row { margin-bottom: 14px; }
.armely-custom-development-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-custom-development-page .form-row input, .armely-custom-development-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-custom-development-page .form-row input:focus, .armely-custom-development-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-custom-development-page .form-row select option { background: #fff; color: #1A2540; }
.armely-custom-development-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-custom-development-page .form-submit:hover { background: var(--blue-lt); }
.armely-custom-development-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* FOOTER */
.armely-custom-development-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-custom-development-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-custom-development-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-custom-development-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-custom-development-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-custom-development-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-custom-development-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* RESPONSIVE */
  @media (max-width: 900px) {
.armely-custom-development-page nav { padding: 16px 24px; }
.armely-custom-development-page .nav-links { display: none; }
.armely-custom-development-page section { padding: 72px 24px; }
.armely-custom-development-page .hero { padding: 110px 24px 72px; }
.armely-custom-development-page .when-grid, .armely-custom-development-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-custom-development-page .delivers-grid, .armely-custom-development-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-custom-development-page .steps-row { grid-template-columns: 1fr; }
.armely-custom-development-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-custom-development-page .step:last-child { border-bottom: none; }
.armely-custom-development-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-custom-development-page .testimonials { padding: 72px 24px; }
.armely-custom-development-page .testi-grid { grid-template-columns: 1fr; }
.armely-custom-development-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-custom-development-page .delivers-grid, .armely-custom-development-page .uc-grid { grid-template-columns: 1fr; }
.armely-custom-development-page .partner-stats { grid-template-columns: 1fr; }
.armely-custom-development-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-custom-development-page {
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
.armely-custom-development-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-custom-development-page .hero::after {
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
.armely-custom-development-page .section-title,
.armely-custom-development-page .deliver-title,
.armely-custom-development-page .uc-title,
.armely-custom-development-page .step-title,
.armely-custom-development-page .why-item-title,
.armely-custom-development-page .form-title {
  color: #162b49;
}
.armely-custom-development-page .deliver-card,
.armely-custom-development-page .uc-card,
.armely-custom-development-page .testi-card,
.armely-custom-development-page .platform-card,
.armely-custom-development-page .partner-block,
.armely-custom-development-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-custom-development-page .deliver-card:hover,
.armely-custom-development-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-custom-development-page .btn-primary,
.armely-custom-development-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-custom-development-page .btn-primary:hover,
.armely-custom-development-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-custom-development-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-custom-development-page nav,
.armely-custom-development-page footer {
  display: none;
}

/* Shared modern service page refresh */
.armely-custom-development-page .hero {
  min-height: 72vh;
  padding: 112px 56px 74px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: #173b67;
  border-radius: 0;
}
.armely-custom-development-page .hero::after,
.armely-custom-development-page .hero-bg-glow,
.armely-custom-development-page .hero-trust {
  display: none;
}
.armely-custom-development-page .hero h1 {
  max-width: 820px;
  margin-bottom: 18px;
}
.armely-custom-development-page .hero-sub {
  max-width: 700px;
  margin-bottom: 28px;
}
.armely-custom-development-page .hero-actions {
  margin-bottom: 0;
}
.armely-custom-development-page .hero .btn-primary,
.armely-custom-development-page .hero .btn-outline {
  border-radius: 0;
}
.armely-custom-development-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.74);
}
.armely-custom-development-page .eyebrow-partner {
  display: none;
}
.armely-custom-development-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-custom-development-page .cta-inner > div > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-custom-development-page section:not(.hero) > .section-inner > .section-title,
.armely-custom-development-page .cta-inner > div > .section-title {
  max-width: 900px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-custom-development-page section:not(.hero) > .section-inner > .section-body,
.armely-custom-development-page .cta-inner > div > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 34px;
  text-align: center;
}
.armely-custom-development-page section:not(.hero) {
  padding-top: 68px;
  padding-bottom: 68px;
}
.armely-custom-development-page .testimonials {
  padding-top: 68px;
  padding-bottom: 68px;
}
.armely-custom-development-page .cta-inner {
  padding-top: 68px;
  padding-bottom: 68px;
}
.armely-custom-development-page .intro-grid,
.armely-custom-development-page .symptoms-grid,
.armely-custom-development-page .when-grid,
.armely-custom-development-page .why-two-col,
.armely-custom-development-page .cta-inner {
  align-items: stretch;
}
.armely-custom-development-page .intro-grid,
.armely-custom-development-page .symptoms-grid,
.armely-custom-development-page .when-grid,
.armely-custom-development-page .delivers-grid,
.armely-custom-development-page .tier-grid,
.armely-custom-development-page .covers-grid,
.armely-custom-development-page .steps-row,
.armely-custom-development-page .uc-grid,
.armely-custom-development-page .testi-grid,
.armely-custom-development-page .why-two-col,
.armely-custom-development-page .pathway-grid {
  margin-top: 38px;
}
.armely-custom-development-page .deliver-icon,
.armely-custom-development-page .uc-icon,
.armely-custom-development-page .why-icon,
.armely-custom-development-page .symptom-icon,
.armely-custom-development-page .what-card-icon,
.armely-custom-development-page .cov-item-icon,
.armely-custom-development-page .cover-icon,
.armely-custom-development-page .product-card-icon,
.armely-custom-development-page .cap-icon,
.armely-custom-development-page .workload-pill-icon,
.armely-custom-development-page .decision-icon,
.armely-custom-development-page .sign-icon,
.armely-custom-development-page .pathway-icon,
.armely-custom-development-page .onelake-callout-icon,
.armely-custom-development-page .vs-callout-icon {
  color: var(--blue);
  font-size: 1.1rem;
  line-height: 1;
}
.armely-custom-development-page .deliver-icon,
.armely-custom-development-page .uc-icon,
.armely-custom-development-page .why-icon {
  width: 48px;
  height: 48px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-custom-development-page .deliver-card,
.armely-custom-development-page .uc-card,
.armely-custom-development-page .testi-card,
.armely-custom-development-page .tier-card,
.armely-custom-development-page .cover-card,
.armely-custom-development-page .pathway-card,
.armely-custom-development-page .partner-block,
.armely-custom-development-page .cta-form {
  background: linear-gradient(180deg, #ffffff 0%, #f9fbfe 100%);
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-custom-development-page .cta-form .form-row {
  display: block;
  margin-bottom: 18px;
}
.armely-custom-development-page .cta-form .form-row label {
  display: block;
  color: #475467;
  font-size: 0.78rem;
  letter-spacing: 0.06em;
}
.armely-custom-development-page .cta-form .form-row input,
.armely-custom-development-page .cta-form .form-row select {
  display: block;
  width: 100%;
  min-height: 52px;
  padding: 14px 16px;
  border-color: rgba(47, 85, 151, 0.2);
  font-size: 0.95rem;
  line-height: 1.4;
}
.armely-custom-development-page .cta-form .form-row .nice-select {
  display: block;
  float: none;
  width: 100%;
  min-height: 52px;
  line-height: 50px;
}
.armely-custom-development-page .cta-form .form-row .nice-select .list {
  width: 100%;
}
.armely-custom-development-page .cta-form .form-row select {
  cursor: pointer;
}
.armely-custom-development-page .cta-form .form-row input::placeholder {
  color: #98a2b3;
}
.armely-custom-development-page .cta-form .form-message {
  display: none;
  margin-bottom: 18px;
  border-radius: 8px;
  font-size: 0.88rem;
  line-height: 1.55;
}
.armely-custom-development-page .cta-form .captcha-row {
  margin-top: 4px;
}
.armely-custom-development-page .cta-form .captcha-row .alert {
  margin: 0;
  border-radius: 8px;
  font-size: 0.86rem;
}
@media (max-width: 900px) {
  .armely-custom-development-page .hero { min-height: auto; padding: 86px 24px 56px; }
  .armely-custom-development-page section:not(.hero),
  .armely-custom-development-page .testimonials { padding-top: 52px; padding-bottom: 52px; }
  .armely-custom-development-page .cta-inner { padding-top: 52px; padding-bottom: 52px; }
  .armely-custom-development-page section:not(.hero) > .section-inner > .section-title,
  .armely-custom-development-page .cta-inner > div > .section-title { max-width: 100%; }
}
</style>
<div class="armely-custom-development-page">
<!-- NAV -->


<!-- HERO -->
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-eyebrow">
    <span class="eyebrow-badge">Custom Application and Web Development</span>
    <span class="eyebrow-partner">Certified Microsoft and Azure partner</span>
  </div>
  <h1>When off-the-shelf<br>software stops fitting<br><span class="hl">your business.</span></h1>
  <p class="hero-sub">Armely designs and builds custom web applications, internal tools, customer-facing portals, and data-driven platforms on the Microsoft stack, delivered with the same discipline and governance we apply to every engagement.</p>
  <div class="hero-actions">
    <a href="#contact" class="btn-primary">Book a Free Discovery Call</a>
    <a href="#what-we-deliver" class="btn-outline">See What We Build</a>
  </div>
  <div class="hero-trust">
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>.NET, React, and Azure</strong> as our primary stack</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>AI-integrated</strong> from the design stage</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">Native integration with <strong>Microsoft 365, Dynamics 365, and Power Platform</strong></span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>You own the code</strong> from day one</span>
    </div>
  </div>
</section>

<!-- WHEN TO BUILD CUSTOM -->
<section class="when">
  <div class="section-inner">
    <div class="when-grid">
      <div>
        <div class="section-eyebrow">When Custom Development Makes Sense</div>
        <h2 class="section-title">Not every problem needs custom software. When it does, it really does.</h2>
        <p class="section-body">Before recommending custom development, Armely evaluates whether Power Platform, SharePoint, or a configured Microsoft product can solve the problem faster and at lower cost. When the answer is no, we build. Here is how that decision looks in practice.</p>
        <div class="decision-cards">
          <div class="decision-card alt">
            <span class="decision-icon"><i class="fa fa-sitemap" aria-hidden="true"></i></span>
            <div>
              <div class="decision-title">Build custom when your workflow is genuinely unique</div>
              <div class="decision-desc">Your process does not map to any standard product. You have tried configuring existing tools and the compromises are affecting how your business operates. A custom application built around your actual workflow is more efficient than forcing your team to work around a product's assumptions.</div>
            </div>
          </div>
          <div class="decision-card alt">
            <span class="decision-icon"><i class="fa fa-balance-scale" aria-hidden="true"></i></span>
            <div>
              <div class="decision-title">Build custom when you need full control over the user experience</div>
              <div class="decision-desc">Customer-facing applications where brand, performance, and user experience are competitive differentiators. A portal, a marketplace, a self-service tool, or a mobile application where the interface is part of the product itself.</div>
            </div>
          </div>
          <div class="decision-card alt">
            <span class="decision-icon"><i class="fa fa-plug" aria-hidden="true"></i></span>
            <div>
              <div class="decision-title">Build custom when you are replacing a legacy system with no viable migration target</div>
              <div class="decision-desc">An on-premises application built decades ago that the business depends on and that no commercial product replicates. A rebuild on a modern, maintainable stack with proper documentation and a CI/CD pipeline.</div>
            </div>
          </div>
          <div class="decision-card">
            <span class="decision-icon"><i class="fa fa-cubes" aria-hidden="true"></i></span>
            <div>
              <div class="decision-title">Consider Power Platform first for internal tooling</div>
              <div class="decision-desc">Many internal workflow and data capture needs are faster and cheaper to solve with Power Apps, Power Automate, and SharePoint. Armely will tell you honestly when that is the better path, even when the conversation started as a custom development request.</div>
            </div>
          </div>
        </div>
      </div>
      <div>
        <div class="stack-card">
          <div class="stack-header">
            <div class="stack-dots"><span></span><span></span><span></span></div>
            <span class="stack-header-title">Armely Technology Stack</span>
          </div>
          <div class="stack-body">
            <div class="stack-row row-front">
              <div class="stack-row-label">Front End</div>
              <div class="stack-chips">
                <span class="stack-chip">React</span>
                <span class="stack-chip">TypeScript</span>
                <span class="stack-chip">Next.js</span>
                <span class="stack-chip">Tailwind CSS</span>
                <span class="stack-chip">Blazor</span>
              </div>
            </div>
            <div class="row-arrow">&#8597;</div>
            <div class="stack-row row-back">
              <div class="stack-row-label">Back End</div>
              <div class="stack-chips">
                <span class="stack-chip">ASP.NET Core</span>
                <span class="stack-chip">C#</span>
                <span class="stack-chip">Python</span>
                <span class="stack-chip">Node.js</span>
                <span class="stack-chip">REST APIs</span>
                <span class="stack-chip">GraphQL</span>
              </div>
            </div>
            <div class="row-arrow">&#8597;</div>
            <div class="stack-row row-back" style="background: rgba(41,78,139,0.08);">
              <div class="stack-row-label">Data</div>
              <div class="stack-chips">
                <span class="stack-chip">Azure SQL</span>
                <span class="stack-chip">SQL Server</span>
                <span class="stack-chip">Cosmos DB</span>
                <span class="stack-chip">Dataverse</span>
                <span class="stack-chip">Azure Blob Storage</span>
              </div>
            </div>
            <div class="row-arrow">&#8597;</div>
            <div class="stack-row row-infra">
              <div class="stack-row-label">Infrastructure and DevOps</div>
              <div class="stack-chips">
                <span class="stack-chip">Azure App Service</span>
                <span class="stack-chip">Azure Functions</span>
                <span class="stack-chip">Azure DevOps</span>
                <span class="stack-chip">GitHub Actions</span>
                <span class="stack-chip">Docker</span>
                <span class="stack-chip">Azure API Management</span>
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
    <h2 class="section-title">Custom software built to be owned, maintained, and extended by your team.</h2>
    <p class="section-body">Every Armely application is delivered with full source code ownership, documentation, and a handover process designed so your team or a future vendor can maintain and extend it without depending on us.</p>
    <div class="delivers-grid">
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa fa-window-restore" aria-hidden="true"></i></div>
        <div class="deliver-title">Web Application Development</div>
        <div class="deliver-desc">Full-stack web applications built on ASP.NET Core and React, deployed on Azure. From internal business tools and management dashboards to customer-facing platforms that require performance, security, and a polished user experience.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa fa-id-card" aria-hidden="true"></i></div>
        <div class="deliver-title">Customer and Partner Portals</div>
        <div class="deliver-desc">Secure, branded portals that give customers, partners, or suppliers authenticated access to your business data and services. Built on Azure with Entra ID authentication, role-based access, and integration into your existing Dynamics 365 or SQL Server data.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa fa-code-fork" aria-hidden="true"></i></div>
        <div class="deliver-title">Legacy Application Modernization</div>
        <div class="deliver-desc">We rebuild aging on-premises applications on a modern, maintainable stack, preserving the business logic and data your organization depends on while replacing the infrastructure that is becoming a liability. Delivered with full documentation and a CI/CD pipeline.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa fa-magic" aria-hidden="true"></i></div>
        <div class="deliver-title">AI-Integrated Applications</div>
        <div class="deliver-desc">Web applications with Azure AI capabilities embedded directly into the user experience, including natural language search, document processing, copilot-style assistants, and generative content, governed within your Microsoft security and compliance framework.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa fa-line-chart" aria-hidden="true"></i></div>
        <div class="deliver-title">Data-Driven Dashboards and Reporting Tools</div>
        <div class="deliver-desc">Custom reporting applications and operational dashboards built when Power BI does not meet the interaction requirements. Built on Azure with direct database connections, real-time data, and user interfaces designed for the specific decisions your leadership team needs to make.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa fa-puzzle-piece" aria-hidden="true"></i></div>
        <div class="deliver-title">Microsoft 365 and Dynamics 365 Extensions</div>
        <div class="deliver-desc">Custom extensions that go beyond what Power Platform can deliver, including SharePoint Framework (SPFx) web parts, Dynamics 365 plugins, custom connectors, and Azure Function-based integrations that extend your existing Microsoft platform investments.</div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNEY -->
<section class="journey" id="journey">
  <div class="section-inner">
    <div class="section-eyebrow">The Armely Delivery Process</div>
    <h2 class="section-title">From requirements to a production application your team can maintain.</h2>
    <p class="section-body">Custom software projects fail most often because requirements are not properly defined, scope expands without governance, or the delivered code is not maintainable. Our process addresses all three before a line of code is written.</p>
    <div class="steps-row">
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-title">Discovery and Scoping</div>
        <div class="step-desc">We document requirements, user stories, data flows, and integration points. We also confirm whether a low-code approach could solve the problem before committing to custom development.</div>
        <span class="step-tag">Free</span>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-title">Architecture and Design</div>
        <div class="step-desc">Technical architecture, data model, API contracts, and UX wireframes produced and reviewed before build begins. Azure infrastructure is sized and costed at this stage.</div>
        <span class="step-tag">Weeks 1-2</span>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-title">Iterative Build</div>
        <div class="step-desc">Development in two-week sprints with working software demonstrated at the end of each sprint. Scope changes are evaluated and priced transparently rather than absorbed silently.</div>
        <span class="step-tag">Weeks 3 onward</span>
      </div>
      <div class="step">
        <div class="step-num">04</div>
        <div class="step-title">Testing and Launch</div>
        <div class="step-desc">User acceptance testing, security review, performance testing, and a managed production deployment with rollback procedures in place before go-live.</div>
        <span class="step-tag">Final 2 weeks</span>
      </div>
      <div class="step">
        <div class="step-num">05</div>
        <div class="step-title">Handover and Support</div>
        <div class="step-desc">Full source code, documentation, runbooks, and team training. Post-launch support, ongoing feature development, and Azure infrastructure management available on a retained basis.</div>
        <span class="step-tag">Ongoing</span>
      </div>
    </div>
  </div>
</section>

<!-- USE CASES -->
<section class="usecases">
  <div class="section-inner">
    <div class="section-eyebrow">What We Build Most Often</div>
    <h2 class="section-title">The application types we deliver across industries.</h2>
    <p class="section-body">These are the categories of custom applications Armely builds most frequently. Each starts with a business problem, not a technology preference.</p>
    <div class="uc-grid">
      <div class="uc-card">
        <span class="uc-icon"><i class="fa fa-hospital-o" aria-hidden="true"></i></span>
        <div class="uc-title">Healthcare and Clinical Applications</div>
        <div class="uc-desc">Custom applications for patient data management, clinical workflow support, reporting tools, and staff scheduling built to HIPAA compliance standards on Azure, with integration into existing EMR systems and Microsoft 365 environments.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa fa-university" aria-hidden="true"></i></span>
        <div class="uc-title">Education Management Systems</div>
        <div class="uc-desc">Student information tools, enrollment portals, staff scheduling applications, and reporting dashboards for school districts and higher education institutions, integrated with Microsoft 365 and built on Azure for security and scalability.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa fa-truck" aria-hidden="true"></i></span>
        <div class="uc-title">Operations and Logistics Tools</div>
        <div class="uc-desc">Custom inventory management, dispatch and scheduling applications, field operations tools, and supply chain dashboards built when commercial products do not fit the specific operational model of the business.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa fa-shield" aria-hidden="true"></i></span>
        <div class="uc-title">Financial and Compliance Applications</div>
        <div class="uc-desc">Custom reporting tools, audit management applications, budget tracking systems, and compliance workflow platforms built for finance teams whose requirements exceed what standard products provide, with full audit trails and role-based access controls.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa fa-globe" aria-hidden="true"></i></span>
        <div class="uc-title">Customer-Facing Web Platforms</div>
        <div class="uc-desc">Public-facing web applications that represent your brand and serve your customers, including self-service portals, booking systems, account management tools, and product configuration applications where quality of experience directly affects revenue.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa fa-server" aria-hidden="true"></i></span>
        <div class="uc-title">Legacy System Replacement</div>
        <div class="uc-desc">On-premises applications built on aging technology stacks that the business cannot replace with a commercial product because of unique business logic, data structures, or integration requirements built up over many years of operation.</div>
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
        <p class="testi-body">We had been running a patient tracking system on a server that was nearly a decade old with no documentation and no one who fully understood how it worked. Armely rebuilt it on Azure with a modern .NET backend and a React front end. They took the time to understand what the system actually did before writing a line of code, and the new version was delivered with full documentation and a training program our clinical staff completed in a day.</p>
        <div class="testi-footer">
          <div class="testi-avatar">CIO</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Chief Information Officer</div>
            <div class="testi-role">Healthcare Network, Midwest</div>
          </div>
        </div>
      </div>

      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">We needed a customer portal that connected to our Dynamics 365 data and allowed clients to view their account, submit service requests, and download invoices without calling us. Armely delivered a fully branded portal in eight weeks. The design was clean, the integration worked correctly from day one, and our support call volume dropped noticeably within the first month of launch.</p>
        <div class="testi-footer">
          <div class="testi-avatar">COO</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Chief Operating Officer</div>
            <div class="testi-role">Professional Services Firm, Texas</div>
          </div>
        </div>
      </div>

      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">We came to Armely with what we thought was a custom development requirement. After their discovery session, they recommended a Power Apps solution instead and delivered it in three weeks at a fraction of the estimated cost. The fact that they gave us the honest answer rather than the more expensive one was the reason we brought them back for our next project, which did require custom development.</p>
        <div class="testi-footer">
          <div class="testi-avatar">IT</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Director of IT</div>
            <div class="testi-role">Education Organization, Nebraska</div>
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
        <h2 class="section-title">Custom development that you own, understand, and can maintain.</h2>
        <p class="section-body">The most common failure in custom software development is not in the initial delivery. It is in what happens six months later when a requirement changes and the original developer is unavailable, the code is undocumented, and no one knows how the application works.</p>
        <ul class="why-list">
          <li>
            <div class="why-icon"><i class="fa fa-file-text" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Documentation Is Not Optional</div>
              <div class="why-item-desc">Every application Armely delivers includes technical documentation, a deployment runbook, and user guides. We write documentation as part of the build, not as a rushed afterthought at handover.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa fa-windows" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Built on the Microsoft Stack You Already Own</div>
              <div class="why-item-desc">We build on Azure, .NET, and the Microsoft ecosystem because your organization already has licenses, security controls, and operational familiarity with these platforms. We do not introduce new infrastructure dependencies without a clear justification.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa fa-shield" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Regulated Industry Experience</div>
              <div class="why-item-desc">We have delivered custom applications for healthcare providers, school districts, and enterprise clients where security, compliance, and data governance are non-negotiable requirements, not an afterthought requested at go-live.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa fa-handshake-o" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">We Will Tell You When Not to Build</div>
              <div class="why-item-desc">If Power Platform, SharePoint, or a configured Microsoft product can solve your problem faster and at lower total cost, we will tell you that before starting a custom development engagement. Our long-term client relationships matter more than any single project.</div>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <div class="partner-block">
          <div class="partner-block-top">
            <div class="partner-label">Microsoft Authorized Partner</div>
            <p class="partner-text">Armely's Microsoft partnership gives us access to Azure credits, technical pre-sales resources, and architectural guidance for complex custom development engagements. We build on the same Azure platform that Microsoft's enterprise customers rely on, with the licensing and support infrastructure that partnership provides.</p>
          </div>
          <div class="partner-stats">
            <div class="p-stat">
              <div class="p-stat-num">.NET</div>
              <div class="p-stat-label">consistently ranked among the top-performing web frameworks in independent benchmarks</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">Azure</div>
              <div class="p-stat-label">global cloud infrastructure across 60-plus regions for scalable, compliant deployments</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">100<span>%</span></div>
              <div class="p-stat-label">of Armely applications delivered with full source code ownership and documentation</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">0</div>
              <div class="p-stat-label">applications delivered without a CI/CD pipeline and automated test coverage</div>
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
      <h2 class="section-title">Tell us what you need to build. We will tell you the best way to build it.</h2>
      <p class="section-body">Book a free 30-minute discovery call. We will review your requirements, assess whether a custom build or a configured Microsoft product is the right answer, and come back with a clear proposal at no obligation.</p>
      <div style="margin-top: 28px; display: flex; flex-direction: column; gap: 12px;">
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Free discovery, no commitment required</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Honest recommendation on build versus configure</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Response within one business day</span>
        </div>
      </div>
    </div>
    <form class="cta-form" id="contact-form" method="post" action="{{ route('submit-consultation') }}">
      @csrf
      <div id="ServiceDetailsMessage" class="form-message alert"></div>
      <input type="hidden" name="message" value="Custom development discovery call request from the service page.">
      <input type="text" name="website" style="display:none;" tabindex="-1" autocomplete="off">
      <div class="form-title">Book Your Free Discovery Call</div>
      <div class="form-sub">Tell us about the application you need.</div>
      @if(session('success'))
        <div class="form-message alert alert-success" style="display:block;">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="form-message alert alert-danger" style="display:block;">{{ $errors->first() }}</div>
      @endif
      <div class="form-row">
        <label for="custom-dev-name">Full Name</label>
        <input id="custom-dev-name" name="name" type="text" placeholder="Jane Smith" value="{{ old('name') }}" required>
      </div>
      <div class="form-row">
        <label for="custom-dev-email">Business Email</label>
        <input id="custom-dev-email" name="email" type="email" placeholder="jane@yourcompany.com" value="{{ old('email') }}" required>
      </div>
      <div class="form-row">
        <label for="custom-dev-company">Company Name</label>
        <input id="custom-dev-company" name="organization" type="text" placeholder="Acme Corp" value="{{ old('organization') }}">
      </div>
      <div class="form-row">
        <label for="custom-dev-service-type">What Are You Looking to Build?</label>
        <select id="custom-dev-service-type" name="service_type" required>
          <option value="">Select...</option>
          <option value="Customer or partner portal" {{ old('service_type') === 'Customer or partner portal' ? 'selected' : '' }}>Customer or partner portal</option>
          <option value="Internal business tool or workflow application" {{ old('service_type') === 'Internal business tool or workflow application' ? 'selected' : '' }}>Internal business tool or workflow application</option>
          <option value="Replace a legacy on-premises application" {{ old('service_type') === 'Replace a legacy on-premises application' ? 'selected' : '' }}>Replace a legacy on-premises application</option>
          <option value="Data dashboard or reporting tool" {{ old('service_type') === 'Data dashboard or reporting tool' ? 'selected' : '' }}>Data dashboard or reporting tool</option>
          <option value="AI-integrated web application" {{ old('service_type') === 'AI-integrated web application' ? 'selected' : '' }}>AI-integrated web application</option>
          <option value="Microsoft 365 or Dynamics 365 extension" {{ old('service_type') === 'Microsoft 365 or Dynamics 365 extension' ? 'selected' : '' }}>Microsoft 365 or Dynamics 365 extension</option>
          <option value="Not sure yet, need a recommendation" {{ old('service_type') === 'Not sure yet, need a recommendation' ? 'selected' : '' }}>Not sure yet, need a recommendation</option>
        </select>
      </div>
      <div class="form-row captcha-row">
        @if(!empty($recaptchaSiteKey))
          <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
        @else
          <div class="alert alert-warning">reCAPTCHA is not configured. Please set <strong>CAPTURE_SITE_KEY</strong>.</div>
        @endif
      </div>
      <button class="form-submit" type="submit">Request Free Discovery Call</button>
      <div class="form-note">No spam. No sales pressure. Just a useful conversation.</div>
    </form>
  </div>
</section>

<!-- FOOTER -->
</div>

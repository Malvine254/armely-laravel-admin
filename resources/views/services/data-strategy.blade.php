<style>

.armely-ai-data-strategy-page *, .armely-ai-data-strategy-page *::before, .armely-ai-data-strategy-page *::after { box-sizing: border-box; margin: 0; padding: 0; }
.armely-ai-data-strategy-page {
    --navy: #FFFFFF; --navy-mid: #F3F6FB; --navy-card: #EBF0F8;
    --blue: #294e8b; --blue-lt: #3d6ab5;
    --blue-dim: rgba(41,78,139,0.08); --blue-dim2: rgba(41,78,139,0.16);
    --text-body: #3D4F6B; --text-muted: #6B7FA3; --border: rgba(41,78,139,0.1);
  }
.armely-ai-data-strategy-page { scroll-behavior: smooth; }
.armely-ai-data-strategy-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

.armely-ai-data-strategy-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-ai-data-strategy-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-ai-data-strategy-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-ai-data-strategy-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-ai-data-strategy-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-ai-data-strategy-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-ai-data-strategy-page .nav-links a:hover { color: #fff; }
.armely-ai-data-strategy-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; }
.armely-ai-data-strategy-page .nav-cta:hover { background: var(--blue-lt) !important; }

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

.armely-ai-data-strategy-page section { padding: 96px 56px; }
.armely-ai-data-strategy-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-ai-data-strategy-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-ai-data-strategy-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 640px; }
.armely-ai-data-strategy-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* SIGNS */
.armely-ai-data-strategy-page .signs { background: var(--navy-mid); }
.armely-ai-data-strategy-page .signs-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-ai-data-strategy-page .sign-list { display: flex; flex-direction: column; gap: 10px; margin-top: 28px; }
.armely-ai-data-strategy-page .sign-item { background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 16px 18px; display: flex; align-items: flex-start; gap: 12px; }
.armely-ai-data-strategy-page .sign-item.active { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-ai-data-strategy-page .sign-icon { font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }
.armely-ai-data-strategy-page .sign-text { font-size: 0.85rem; color: var(--text-body); line-height: 1.6; }
.armely-ai-data-strategy-page .sign-text strong { color: #1A2540; font-weight: 600; }

  /* Advisory visual */
.armely-ai-data-strategy-page .advisory-visual { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-ai-data-strategy-page .adv-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-ai-data-strategy-page .adv-dots { display: flex; gap: 6px; }
.armely-ai-data-strategy-page .adv-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-ai-data-strategy-page .adv-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-ai-data-strategy-page .adv-body { padding: 20px; }
.armely-ai-data-strategy-page .adv-phase { border-radius: 9px; padding: 14px 16px; margin-bottom: 8px; border: 1px solid var(--border); }
.armely-ai-data-strategy-page .adv-phase-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
.armely-ai-data-strategy-page .adv-phase-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; }
.armely-ai-data-strategy-page .adv-phase-tag { font-size: 0.67rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--blue); background: var(--blue-dim2); padding: 2px 8px; border-radius: 4px; }
.armely-ai-data-strategy-page .adv-phase-desc { font-size: 0.75rem; color: var(--text-muted); line-height: 1.5; }
.armely-ai-data-strategy-page .adv-output { background: var(--blue); border-radius: 9px; padding: 14px 16px; margin-top: 4px; display: flex; align-items: center; gap: 10px; }
.armely-ai-data-strategy-page .adv-output-text { font-size: 0.82rem; color: rgba(255,255,255,0.9); line-height: 1.5; }
.armely-ai-data-strategy-page .adv-output-text strong { color: #fff; }

  /* DELIVERS */
.armely-ai-data-strategy-page .delivers { background: var(--navy); }
.armely-ai-data-strategy-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-ai-data-strategy-page .deliver-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-ai-data-strategy-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-ai-data-strategy-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-ai-data-strategy-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-ai-data-strategy-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-ai-data-strategy-page .journey { background: var(--navy-mid); }
.armely-ai-data-strategy-page .steps-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-ai-data-strategy-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-ai-data-strategy-page .step:last-child { border-right: none; }
.armely-ai-data-strategy-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-ai-data-strategy-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-ai-data-strategy-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-ai-data-strategy-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* PATHWAYS */
.armely-ai-data-strategy-page .pathways { background: var(--navy); }
.armely-ai-data-strategy-page .pathway-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-ai-data-strategy-page .pathway-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; }
.armely-ai-data-strategy-page .pathway-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-ai-data-strategy-page .pathway-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-ai-data-strategy-page .pathway-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); margin-bottom: 14px; }
.armely-ai-data-strategy-page .pathway-link { font-size: 0.78rem; font-weight: 600; color: var(--blue); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }

  /* TESTIMONIALS */
.armely-ai-data-strategy-page .testimonials { background: var(--navy-mid); padding: 96px 56px; }
.armely-ai-data-strategy-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-ai-data-strategy-page .testi-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-ai-data-strategy-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-ai-data-strategy-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-ai-data-strategy-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-ai-data-strategy-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; }
.armely-ai-data-strategy-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-ai-data-strategy-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-ai-data-strategy-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY */
.armely-ai-data-strategy-page .why { background: var(--navy); }
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

.armely-ai-data-strategy-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-ai-data-strategy-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-ai-data-strategy-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-ai-data-strategy-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-ai-data-strategy-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-ai-data-strategy-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-ai-data-strategy-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  @media (max-width: 900px) {
.armely-ai-data-strategy-page nav { padding: 16px 24px; }
.armely-ai-data-strategy-page .nav-links { display: none; }
.armely-ai-data-strategy-page section { padding: 72px 24px; }
.armely-ai-data-strategy-page .hero { padding: 110px 24px 72px; }
.armely-ai-data-strategy-page .signs-grid, .armely-ai-data-strategy-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-ai-data-strategy-page .delivers-grid, .armely-ai-data-strategy-page .pathway-grid { grid-template-columns: 1fr 1fr; }
.armely-ai-data-strategy-page .steps-row { grid-template-columns: 1fr; }
.armely-ai-data-strategy-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-ai-data-strategy-page .step:last-child { border-bottom: none; }
.armely-ai-data-strategy-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-ai-data-strategy-page .testimonials { padding: 72px 24px; }
.armely-ai-data-strategy-page .testi-grid { grid-template-columns: 1fr; }
.armely-ai-data-strategy-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-ai-data-strategy-page .delivers-grid, .armely-ai-data-strategy-page .pathway-grid { grid-template-columns: 1fr; }
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

/* Shared modern service page refresh */
.armely-ai-data-strategy-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: #173b67;
  border-radius: 0;
}
.armely-ai-data-strategy-page .hero::after,
.armely-ai-data-strategy-page .hero-bg-glow,
.armely-ai-data-strategy-page .hero-trust {
  display: none;
}
.armely-ai-data-strategy-page .hero h1 {
  max-width: 820px;
  margin-bottom: 22px;
}
.armely-ai-data-strategy-page .hero-sub {
  max-width: 700px;
  margin-bottom: 34px;
}
.armely-ai-data-strategy-page .hero-actions {
  margin-bottom: 0;
}
.armely-ai-data-strategy-page .hero .btn-primary,
.armely-ai-data-strategy-page .hero .btn-outline {
  border-radius: 0;
}
.armely-ai-data-strategy-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.74);
}
.armely-ai-data-strategy-page .eyebrow-partner {
  display: none;
}
.armely-ai-data-strategy-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-ai-data-strategy-page .cta-inner > div > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-ai-data-strategy-page section:not(.hero) > .section-inner > .section-title,
.armely-ai-data-strategy-page .cta-inner > div > .section-title {
  max-width: 900px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-ai-data-strategy-page section:not(.hero) > .section-inner > .section-body,
.armely-ai-data-strategy-page .cta-inner > div > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-ai-data-strategy-page .intro-grid,
.armely-ai-data-strategy-page .symptoms-grid,
.armely-ai-data-strategy-page .when-grid,
.armely-ai-data-strategy-page .why-two-col,
.armely-ai-data-strategy-page .cta-inner {
  align-items: stretch;
}
.armely-ai-data-strategy-page .intro-grid,
.armely-ai-data-strategy-page .symptoms-grid,
.armely-ai-data-strategy-page .when-grid,
.armely-ai-data-strategy-page .delivers-grid,
.armely-ai-data-strategy-page .tier-grid,
.armely-ai-data-strategy-page .covers-grid,
.armely-ai-data-strategy-page .steps-row,
.armely-ai-data-strategy-page .uc-grid,
.armely-ai-data-strategy-page .testi-grid,
.armely-ai-data-strategy-page .why-two-col,
.armely-ai-data-strategy-page .pathway-grid {
  margin-top: 56px;
}
.armely-ai-data-strategy-page .deliver-icon,
.armely-ai-data-strategy-page .uc-icon,
.armely-ai-data-strategy-page .why-icon,
.armely-ai-data-strategy-page .symptom-icon,
.armely-ai-data-strategy-page .what-card-icon,
.armely-ai-data-strategy-page .cov-item-icon,
.armely-ai-data-strategy-page .cover-icon,
.armely-ai-data-strategy-page .product-card-icon,
.armely-ai-data-strategy-page .cap-icon,
.armely-ai-data-strategy-page .workload-pill-icon,
.armely-ai-data-strategy-page .decision-icon,
.armely-ai-data-strategy-page .sign-icon,
.armely-ai-data-strategy-page .pathway-icon,
.armely-ai-data-strategy-page .onelake-callout-icon,
.armely-ai-data-strategy-page .vs-callout-icon {
  color: var(--blue);
  font-size: 1.1rem;
  line-height: 1;
}
.armely-ai-data-strategy-page .deliver-icon,
.armely-ai-data-strategy-page .uc-icon,
.armely-ai-data-strategy-page .why-icon {
  width: 48px;
  height: 48px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-ai-data-strategy-page .deliver-card,
.armely-ai-data-strategy-page .uc-card,
.armely-ai-data-strategy-page .testi-card,
.armely-ai-data-strategy-page .tier-card,
.armely-ai-data-strategy-page .cover-card,
.armely-ai-data-strategy-page .pathway-card,
.armely-ai-data-strategy-page .partner-block,
.armely-ai-data-strategy-page .cta-form {
  background: linear-gradient(180deg, #ffffff 0%, #f9fbfe 100%);
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
@media (max-width: 900px) {
  .armely-ai-data-strategy-page .hero { padding: 118px 24px 76px; }
  .armely-ai-data-strategy-page section:not(.hero) > .section-inner > .section-title,
  .armely-ai-data-strategy-page .cta-inner > div > .section-title { max-width: 100%; }
}
</style>
<div class="armely-ai-data-strategy-page">
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-eyebrow">
    <span class="eyebrow-badge">AI and Data Strategy Advisory</span>
    <span class="eyebrow-partner">Certified Microsoft and Azure partner</span>
  </div>
  <h1>Not sure where<br>to start with AI<br><span class="hl">or your data? Start here.</span></h1>
  <p class="hero-sub">Armely's advisory practice helps organizations cut through the noise, assess what they actually have, and build a clear, prioritized roadmap before committing to any technology investment.</p>
  <div class="hero-actions">
    <a href="#contact" class="btn-primary">Book a Free Strategy Call</a>
    <a href="#what-we-deliver" class="btn-outline">See What We Deliver</a>
  </div>
  <div class="hero-trust">
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Vendor-informed</strong> but not vendor-driven recommendations</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Documented roadmaps</strong> you can present to leadership</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>No obligation</strong> to proceed to implementation with Armely</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">Backed by <strong>real implementation experience</strong> across every recommendation</span>
    </div>
  </div>
</section>

<!-- SIGNS YOU NEED ADVISORY -->
<section class="signs">
  <div class="section-inner">
    <div class="signs-grid">
      <div>
        <div class="section-eyebrow">Is This You?</div>
        <h2 class="section-title">The conversations that lead organizations to Armely advisory.</h2>
        <p class="section-body">Most organizations that engage Armely for advisory are not short of enthusiasm for AI or data. They are short of clarity on what to do first, what the realistic outcomes are, and how to make a defensible investment decision.</p>
        <div class="sign-list">
          <div class="sign-item active">
            <span class="sign-icon"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></span>
            <div class="sign-text"><strong>Everyone is talking about AI but no one agrees on where to start.</strong> Multiple departments have different AI requests, multiple vendors are pitching different platforms, and leadership wants a strategy before approving any spend.</div>
          </div>
          <div class="sign-item active">
            <span class="sign-icon"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></span>
            <div class="sign-text"><strong>Your data is everywhere and you know it is costing you.</strong> Decisions are made from spreadsheets, reports take days to produce, and no one is confident the numbers are right. You need a data foundation before any analytics or AI investment makes sense.</div>
          </div>
          <div class="sign-item active">
            <span class="sign-icon"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></span>
            <div class="sign-text"><strong>A previous technology investment did not deliver what was promised.</strong> You want an independent assessment before committing again, and you need someone who has actually implemented these platforms rather than just sold them.</div>
          </div>
          <div class="sign-item">
            <span class="sign-icon"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></span>
            <div class="sign-text"><strong>You have a roadmap but no confidence it is the right one.</strong> A vendor or internal team has produced a plan. You want an experienced second opinion before the board approves the budget.</div>
          </div>
        </div>
      </div>
      <div>
        <div class="advisory-visual">
          <div class="adv-header">
            <div class="adv-dots"><span></span><span></span><span></span></div>
            <span class="adv-title">What an Advisory Engagement Produces</span>
          </div>
          <div class="adv-body">
            <div class="adv-phase">
              <div class="adv-phase-top">
                <div class="adv-phase-title">Current State Assessment</div>
                <span class="adv-phase-tag">Week 1</span>
              </div>
              <div class="adv-phase-desc">Structured review of your existing data sources, systems, AI tools, governance posture, and organizational readiness. Documented findings your leadership team can act on.</div>
            </div>
            <div class="adv-phase">
              <div class="adv-phase-top">
                <div class="adv-phase-title">Use Case Identification and Prioritization</div>
                <span class="adv-phase-tag">Week 2</span>
              </div>
              <div class="adv-phase-desc">Workshops with key stakeholders to surface AI and data opportunities. Each use case evaluated against business value, data readiness, and implementation complexity. Output is a ranked list, not a generic catalogue.</div>
            </div>
            <div class="adv-phase">
              <div class="adv-phase-top">
                <div class="adv-phase-title">Platform and Architecture Recommendation</div>
                <span class="adv-phase-tag">Week 3</span>
              </div>
              <div class="adv-phase-desc">Technology selection guidance for your specific situation, including Microsoft Fabric, Snowflake, Azure AI Foundry, Copilot Studio, and where each fits or does not fit your requirements.</div>
            </div>
            <div class="adv-phase">
              <div class="adv-phase-top">
                <div class="adv-phase-title">Roadmap and Business Case</div>
                <span class="adv-phase-tag">Week 4</span>
              </div>
              <div class="adv-phase-desc">A phased implementation roadmap with timeline, indicative cost ranges, resource requirements, and a business case structured for leadership review and board approval.</div>
            </div>
            <div class="adv-output">
              <span style="font-size:1.2rem;"><i class="fa-solid fa-file-lines" aria-hidden="true"></i></span>
              <div class="adv-output-text"><strong>Deliverable:</strong> A written strategy document and presentation deck you own and can use internally, regardless of whether Armely implements anything.</div>
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
    <div class="section-eyebrow">Advisory Services</div>
    <h2 class="section-title">Six advisory engagements organizations engage Armely to lead.</h2>
    <p class="section-body">Each engagement is scoped, time-bounded, and produces a written deliverable. We do not run open-ended advisory retainers that produce slide decks without outcomes.</p>
    <div class="delivers-grid">
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">AI Readiness Assessment</div>
        <div class="deliver-desc">A structured evaluation of your organization's readiness to adopt AI, covering data quality, governance posture, security controls, workforce capability, and existing Microsoft platform configuration. Delivered as a written report with a prioritized remediation list.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Data Strategy and Architecture Review</div>
        <div class="deliver-desc">An assessment of your current data landscape, including sources, quality, accessibility, and governance, with a recommended target architecture for analytics, reporting, and AI workloads. Platform recommendations are specific to your situation, not generic.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Technology Roadmap Development</div>
        <div class="deliver-desc">A phased, prioritized roadmap covering AI, data, and digital platform investments over a 12 to 36 month horizon, with indicative costs, resource requirements, and dependencies. Structured for presentation to leadership and boards.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Platform Selection Advisory</div>
        <div class="deliver-desc">Independent guidance on technology selection for a specific problem, such as choosing between Microsoft Fabric and Snowflake, or evaluating Copilot Studio against Azure AI Foundry. We present the honest trade-offs rather than defaulting to the platform that generates the largest implementation engagement.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">AI Governance Framework Design</div>
        <div class="deliver-desc">A governance framework for AI adoption covering acceptable use policies, data access controls, agent management standards, audit requirements, and escalation procedures. Designed to satisfy audit and compliance requirements before AI deployments scale.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Investment Validation Review</div>
        <div class="deliver-desc">An independent review of a proposed or existing technology investment, assessing whether the platform, architecture, and implementation approach are appropriate for the stated business outcomes. Useful before committing a significant budget or after a delivery that has not met expectations.</div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNEY -->
<section class="journey" id="journey">
  <div class="section-inner">
    <div class="section-eyebrow">How an Advisory Engagement Works</div>
    <h2 class="section-title">Structured, time-bounded, and documented. Not open-ended.</h2>
    <p class="section-body">A typical Armely advisory engagement runs four to six weeks and produces a written deliverable your organization owns. We work with your internal stakeholders, not around them.</p>
    <div class="steps-row">
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-title">Scoping Call</div>
        <div class="step-desc">We understand your situation, agree on the specific advisory deliverable, identify the stakeholders we need to engage, and set a timeline. No charge for this conversation.</div>
        <span class="step-tag">Free</span>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-title">Discovery and Interviews</div>
        <div class="step-desc">Structured interviews with leadership, IT, and operational stakeholders. Review of relevant documentation, existing systems, and any prior technology assessments or roadmaps.</div>
        <span class="step-tag">Weeks 1-2</span>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-title">Analysis and Drafting</div>
        <div class="step-desc">We synthesize findings, develop recommendations, and draft the strategy document or roadmap. A working session with your team reviews the draft before finalization.</div>
        <span class="step-tag">Weeks 3-4</span>
      </div>
      <div class="step">
        <div class="step-num">04</div>
        <div class="step-title">Presentation and Handover</div>
        <div class="step-desc">Final document and presentation deck delivered. We present to your leadership team and answer questions. You own all deliverables outright.</div>
        <span class="step-tag">Week 5-6</span>
      </div>
    </div>
  </div>
</section>

<!-- IMPLEMENTATION PATHWAYS -->
<section class="pathways" id="pathways">
  <div class="section-inner">
    <div class="section-eyebrow">Where Advisory Leads</div>
    <h2 class="section-title">The roadmap tells you what to build. Armely can build it.</h2>
    <p class="section-body">Most Armely advisory engagements lead to implementation work, but that outcome is never assumed or pressured. If a roadmap points to a platform or capability Armely implements, we are well positioned to deliver it. If it points elsewhere, we will say so.</p>
    <div class="pathway-grid">
      <div class="pathway-card">
        <span class="pathway-icon"><i class="fa-solid fa-route" aria-hidden="true"></i></span>
        <div class="pathway-title">Data Platform Implementation</div>
        <div class="pathway-desc">Microsoft Fabric, Snowflake, or Azure Synapse implemented on the architecture designed during the advisory engagement, with pipelines, data models, and dashboards built by the same team that designed the strategy.</div>
        <a href="{{ url('/fabric') }}" class="pathway-link">Microsoft Fabric &#8594;</a>
      </div>
      <div class="pathway-card">
        <span class="pathway-icon"><i class="fa-solid fa-route" aria-hidden="true"></i></span>
        <div class="pathway-title">AI Agent and Application Build</div>
        <div class="pathway-desc">Generative AI and agentic solutions built on the Microsoft AI stack, governed by the framework developed in the advisory phase, implemented against the use cases prioritized in the roadmap.</div>
        <a href="{{ url('/genai') }}" class="pathway-link">Generative and Agentic AI &#8594;</a>
      </div>
      <div class="pathway-card">
        <span class="pathway-icon"><i class="fa-solid fa-route" aria-hidden="true"></i></span>
        <div class="pathway-title">Platform Configuration and Integration</div>
        <div class="pathway-desc">Dynamics 365, Power Platform, SharePoint, and Microsoft 365 implementations and integrations recommended in the roadmap delivered by certified Armely engineers on the Microsoft stack.</div>
        <a href="{{ url('/dynamics365') }}" class="pathway-link">Dynamics 365 &#8594;</a>
      </div>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials">
  <div class="section-inner">
    <div class="section-eyebrow">Client Results</div>
    <h2 class="section-title">What our clients say about working with Armely on strategy.</h2>
    <div class="testi-grid">
      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">We had three different vendors all telling us their platform was the right answer for our data needs. Armely came in, assessed our actual situation without a product to sell, and told us clearly which platform fit and which did not. The recommendation was not the most expensive option. That was the moment we knew we had the right advisor.</p>
        <div class="testi-footer">
          <div class="testi-avatar">CTO</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Chief Technology Officer</div>
            <div class="testi-role">Healthcare Organization, Midwest</div>
          </div>
        </div>
      </div>
      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">Our board asked us to present an AI strategy before approving any investment. We did not have one. Armely ran a four-week advisory engagement with our leadership team, produced a written roadmap with a business case, and presented alongside us at the board meeting. The board approved the budget. Implementation started the following month.</p>
        <div class="testi-footer">
          <div class="testi-avatar">CEO</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Chief Executive Officer</div>
            <div class="testi-role">Professional Services Organization, Texas</div>
          </div>
        </div>
      </div>
      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">We had invested in a data platform that was not delivering what we expected. Rather than defending the original decision, Armely conducted an honest review, identified where the implementation had gone wrong, and gave us a clear remediation path. Their independence was exactly what we needed after a difficult project.</p>
        <div class="testi-footer">
          <div class="testi-avatar">CFO</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Chief Financial Officer</div>
            <div class="testi-role">Education Institution, Nebraska</div>
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
        <div class="section-eyebrow">Why Armely for Advisory</div>
        <h2 class="section-title">Strategy advice is only credible when it comes from people who implement.</h2>
        <p class="section-body">The limitation of purely advisory firms is that they recommend platforms they have never had to deliver. Armely's advisory practice is credible because every recommendation is made by engineers and architects who have built on the platforms they are recommending.</p>
        <ul class="why-list">
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Advisors Who Also Implement</div>
              <div class="why-item-desc">Every Armely advisory engagement is led by engineers and architects with hands-on delivery experience across Microsoft Fabric, Snowflake, Azure AI Foundry, Dynamics 365, and the broader Microsoft platform. We know what the platforms can and cannot do in practice, not just in vendor documentation.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">No Predetermined Outcome</div>
              <div class="why-item-desc">We are not paid by platform vendors to recommend their products. Our revenue comes from implementation and managed services work, which means our advisory is only commercially valuable to us if it leads to the right implementation, not just any implementation.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Written Deliverables You Own</div>
              <div class="why-item-desc">Every advisory engagement produces a written document your organization owns outright. Strategy documents, assessment reports, and roadmaps that you can present internally, share with your board, or take to any other vendor without restriction.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Experience in Regulated Environments</div>
              <div class="why-item-desc">Our advisory work in healthcare, education, and professional services means we understand the compliance, governance, and risk management constraints that shape technology decisions in regulated industries, not just the technology itself.</div>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <div class="partner-block">
          <div class="partner-block-top">
            <div class="partner-label">Microsoft Authorized Partner</div>
            <p class="partner-text">Armely's Microsoft partnership gives our advisory practice access to Microsoft technical briefings, early product roadmap information, and architectural guidance resources that help us give clients accurate, current guidance on the Microsoft platform rather than advice based on documentation that may be months behind the current product state.</p>
          </div>
          <div class="partner-stats">
            <div class="p-stat">
              <div class="p-stat-num">4-6</div>
              <div class="p-stat-label">weeks for a complete AI and data strategy advisory engagement</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">100<span>%</span></div>
              <div class="p-stat-label">of advisory deliverables owned outright by the client</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">0</div>
              <div class="p-stat-label">platform vendor referral fees received by Armely for any recommendation</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">3</div>
              <div class="p-stat-label">industries with deep advisory experience: healthcare, education, and professional services</div>
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
      <h2 class="section-title">Tell us where you are. We will help you figure out where to go.</h2>
      <p class="section-body">Book a free 30-minute strategy call. We will listen to your current situation, ask the right questions, and tell you honestly whether an advisory engagement is the right next step or whether you are ready to move directly to implementation.</p>
      <div style="margin-top: 28px; display: flex; flex-direction: column; gap: 12px;">
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Free strategy call, no commitment required</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Honest assessment of whether advisory or implementation is the right first step</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Response within one business day</span>
        </div>
      </div>
    </div>
    <div class="cta-form">
      <div class="form-title">Book Your Free Strategy Call</div>
      <div class="form-sub">Tell us where you are in your AI and data journey.</div>
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
        <label>What Best Describes Your Situation?</label>
        <select>
          <option value="">Select...</option>
          <option>We need an AI strategy before committing budget</option>
          <option>We need a data strategy before any analytics investment</option>
          <option>We need help choosing between platforms or vendors</option>
          <option>A previous investment did not deliver and we need a review</option>
          <option>We have a roadmap and want an independent review</option>
          <option>We need an AI governance framework</option>
          <option>Not sure yet, need a conversation first</option>
        </select>
      </div>
      <button class="form-submit">Request Free Strategy Call</button>
      <div class="form-note">No spam. No sales pressure. Just a useful conversation.</div>
    </div>
  </div>
</section>
</div>

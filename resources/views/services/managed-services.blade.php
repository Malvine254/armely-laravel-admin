<style>

.armely-managed-services-page *, .armely-managed-services-page *::before, .armely-managed-services-page *::after { box-sizing: border-box; margin: 0; padding: 0; }
.armely-managed-services-page {
    --navy: #FFFFFF; --navy-mid: #F3F6FB; --navy-card: #EBF0F8;
    --blue: #294e8b; --blue-lt: #3d6ab5;
    --blue-dim: rgba(41,78,139,0.08); --blue-dim2: rgba(41,78,139,0.16);
    --text-body: #3D4F6B; --text-muted: #6B7FA3; --border: rgba(41,78,139,0.1);
  }
.armely-managed-services-page { scroll-behavior: smooth; }
.armely-managed-services-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

.armely-managed-services-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-managed-services-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-managed-services-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-managed-services-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-managed-services-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-managed-services-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-managed-services-page .nav-links a:hover { color: #fff; }
.armely-managed-services-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; }
.armely-managed-services-page .nav-cta:hover { background: var(--blue-lt) !important; }

.armely-managed-services-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-managed-services-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-managed-services-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-managed-services-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-managed-services-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-managed-services-page .hero h1 { font-size: clamp(2.6rem, 5.5vw, 4.8rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-managed-services-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-managed-services-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.8; }
.armely-managed-services-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-managed-services-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-managed-services-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-managed-services-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-managed-services-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-managed-services-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-managed-services-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-managed-services-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-managed-services-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-managed-services-page .trust-text strong { color: #fff; font-weight: 600; }

.armely-managed-services-page section { padding: 96px 56px; }
.armely-managed-services-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-managed-services-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-managed-services-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 640px; }
.armely-managed-services-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* WHAT IS MANAGED SERVICES */
.armely-managed-services-page .intro { background: var(--navy-mid); }
.armely-managed-services-page .intro-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }

.armely-managed-services-page .what-cards { display: flex; flex-direction: column; gap: 10px; margin-top: 28px; }
.armely-managed-services-page .what-card { background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 16px 18px; display: flex; align-items: flex-start; gap: 12px; }
.armely-managed-services-page .what-card-icon { font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }
.armely-managed-services-page .what-card-title { font-size: 0.85rem; font-weight: 700; color: #1A2540; margin-bottom: 3px; }
.armely-managed-services-page .what-card-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }

  /* Coverage visual */
.armely-managed-services-page .coverage-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-managed-services-page .cov-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-managed-services-page .cov-dots { display: flex; gap: 6px; }
.armely-managed-services-page .cov-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-managed-services-page .cov-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-managed-services-page .cov-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.armely-managed-services-page .cov-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-radius: 8px; background: var(--navy-mid); border: 1px solid var(--border); }
.armely-managed-services-page .cov-item-left { display: flex; align-items: center; gap: 10px; }
.armely-managed-services-page .cov-item-icon { font-size: 1rem; }
.armely-managed-services-page .cov-item-name { font-size: 0.82rem; font-weight: 600; color: #1A2540; }
.armely-managed-services-page .cov-badge { font-size: 0.67rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 2px 8px; border-radius: 4px; }
.armely-managed-services-page .badge-on { background: rgba(41,78,139,0.15); color: var(--blue); }
.armely-managed-services-page .badge-proactive { background: rgba(41,139,80,0.12); color: #1a6b38; }
.armely-managed-services-page .cov-sla { background: var(--blue); border-radius: 9px; padding: 14px 16px; display: flex; align-items: center; gap: 10px; margin-top: 4px; }
.armely-managed-services-page .cov-sla-text { font-size: 0.82rem; color: rgba(255,255,255,0.9); line-height: 1.5; }
.armely-managed-services-page .cov-sla-text strong { color: #fff; }

  /* TIERS */
.armely-managed-services-page .tiers { background: var(--navy); }
.armely-managed-services-page .tier-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-managed-services-page .tier-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; position: relative; transition: border-color 0.2s, transform 0.2s; }
.armely-managed-services-page .tier-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-managed-services-page .tier-card.featured { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-managed-services-page .tier-badge { position: absolute; top: -13px; left: 50%; transform: translateX(-50%); background: var(--blue); color: #fff; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 3px 14px; border-radius: 20px; white-space: nowrap; }
.armely-managed-services-page .tier-name { font-size: 1.1rem; font-weight: 800; color: #1A2540; margin-bottom: 6px; }
.armely-managed-services-page .tier-desc { font-size: 0.82rem; color: var(--text-muted); margin-bottom: 20px; line-height: 1.5; }
.armely-managed-services-page .tier-features { list-style: none; }
.armely-managed-services-page .tier-features li { display: flex; align-items: flex-start; gap: 10px; font-size: 0.85rem; color: var(--text-body); margin-bottom: 10px; line-height: 1.5; }
.armely-managed-services-page .tier-features li::before { content: '&#10003;'; color: var(--blue); font-weight: 700; flex-shrink: 0; }
.armely-managed-services-page .tier-note { margin-top: 20px; font-size: 0.75rem; color: var(--text-muted); font-style: italic; }

  /* COVERS */
.armely-managed-services-page .covers { background: var(--navy-mid); }
.armely-managed-services-page .covers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-managed-services-page .cover-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-managed-services-page .cover-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-managed-services-page .cover-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-managed-services-page .cover-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-managed-services-page .cover-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* TESTIMONIALS */
.armely-managed-services-page .testimonials { background: var(--navy); padding: 96px 56px; }
.armely-managed-services-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-managed-services-page .testi-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-managed-services-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-managed-services-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-managed-services-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-managed-services-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; }
.armely-managed-services-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-managed-services-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-managed-services-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY */
.armely-managed-services-page .why { background: var(--navy-mid); }
.armely-managed-services-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-managed-services-page .why-list { list-style: none; margin-top: 36px; }
.armely-managed-services-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-managed-services-page .why-list li:last-child { border-bottom: none; }
.armely-managed-services-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-managed-services-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-managed-services-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-managed-services-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-managed-services-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-managed-services-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-managed-services-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-managed-services-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-managed-services-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-managed-services-page .p-stat:nth-child(2) { border-right: none; }
.armely-managed-services-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-managed-services-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-managed-services-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-managed-services-page .p-stat-num span { color: var(--blue); }
.armely-managed-services-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* CTA */
.armely-managed-services-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-managed-services-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-managed-services-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-managed-services-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-managed-services-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-managed-services-page .form-row { margin-bottom: 14px; }
.armely-managed-services-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-managed-services-page .form-row input, .armely-managed-services-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-managed-services-page .form-row input:focus, .armely-managed-services-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-managed-services-page .form-row select option { background: #fff; color: #1A2540; }
.armely-managed-services-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-managed-services-page .form-submit:hover { background: var(--blue-lt); }
.armely-managed-services-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

.armely-managed-services-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-managed-services-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-managed-services-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-managed-services-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-managed-services-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-managed-services-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-managed-services-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  @media (max-width: 900px) {
.armely-managed-services-page nav { padding: 16px 24px; }
.armely-managed-services-page .nav-links { display: none; }
.armely-managed-services-page section { padding: 72px 24px; }
.armely-managed-services-page .hero { padding: 110px 24px 72px; }
.armely-managed-services-page .intro-grid, .armely-managed-services-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-managed-services-page .tier-grid, .armely-managed-services-page .covers-grid { grid-template-columns: 1fr 1fr; }
.armely-managed-services-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-managed-services-page .testimonials { padding: 72px 24px; }
.armely-managed-services-page .testi-grid { grid-template-columns: 1fr; }
.armely-managed-services-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-managed-services-page .tier-grid, .armely-managed-services-page .covers-grid { grid-template-columns: 1fr; }
.armely-managed-services-page .partner-stats { grid-template-columns: 1fr; }
.armely-managed-services-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-managed-services-page {
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
.armely-managed-services-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-managed-services-page .hero::after {
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
.armely-managed-services-page .section-title,
.armely-managed-services-page .deliver-title,
.armely-managed-services-page .uc-title,
.armely-managed-services-page .step-title,
.armely-managed-services-page .why-item-title,
.armely-managed-services-page .form-title {
  color: #162b49;
}
.armely-managed-services-page .deliver-card,
.armely-managed-services-page .uc-card,
.armely-managed-services-page .testi-card,
.armely-managed-services-page .platform-card,
.armely-managed-services-page .partner-block,
.armely-managed-services-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-managed-services-page .deliver-card:hover,
.armely-managed-services-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-managed-services-page .btn-primary,
.armely-managed-services-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-managed-services-page .btn-primary:hover,
.armely-managed-services-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-managed-services-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-managed-services-page nav,
.armely-managed-services-page footer {
  display: none;
}
.armely-managed-services-page .intro-grid,
.armely-managed-services-page .why-two-col,
.armely-managed-services-page .cta-inner {
  align-items: stretch;
}
.armely-managed-services-page .coverage-card,
.armely-managed-services-page .partner-block,
.armely-managed-services-page .cta-form {
  height: 100%;
}
.armely-managed-services-page .tier-grid,
.armely-managed-services-page .covers-grid,
.armely-managed-services-page .testi-grid {
  align-items: stretch;
}
.armely-managed-services-page .tier-card,
.armely-managed-services-page .cover-card,
.armely-managed-services-page .testi-card {
  min-height: 100%;
}
.armely-managed-services-page .what-card,
.armely-managed-services-page .tier-card,
.armely-managed-services-page .cover-card,
.armely-managed-services-page .testi-card,
.armely-managed-services-page .partner-block,
.armely-managed-services-page .cta-form {
  background: linear-gradient(180deg, #ffffff 0%, #f9fbfe 100%);
}
.armely-managed-services-page .tier-card.featured {
  background: linear-gradient(180deg, rgba(47, 85, 151, 0.12) 0%, rgba(79, 134, 198, 0.08) 100%);
}
.armely-managed-services-page .coverage-card,
.armely-managed-services-page .partner-block,
.armely-managed-services-page .cta-form {
  border-radius: 12px;
}
.armely-managed-services-page .section-body {
  max-width: 680px;
}

/* Shared modern service page refresh */
.armely-managed-services-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: #173b67;
  border-radius: 0;
}
.armely-managed-services-page .hero::after,
.armely-managed-services-page .hero-bg-glow,
.armely-managed-services-page .hero-trust {
  display: none;
}
.armely-managed-services-page .hero h1 {
  max-width: 820px;
  margin-bottom: 22px;
}
.armely-managed-services-page .hero-sub {
  max-width: 700px;
  margin-bottom: 34px;
}
.armely-managed-services-page .hero-actions {
  margin-bottom: 0;
}
.armely-managed-services-page .hero .btn-primary,
.armely-managed-services-page .hero .btn-outline {
  border-radius: 0;
}
.armely-managed-services-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.74);
}
.armely-managed-services-page .eyebrow-partner {
  display: none;
}
.armely-managed-services-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-managed-services-page .cta-inner > div > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-managed-services-page section:not(.hero) > .section-inner > .section-title,
.armely-managed-services-page .cta-inner > div > .section-title {
  max-width: 900px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-managed-services-page section:not(.hero) > .section-inner > .section-body,
.armely-managed-services-page .cta-inner > div > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-managed-services-page .intro-grid,
.armely-managed-services-page .symptoms-grid,
.armely-managed-services-page .when-grid,
.armely-managed-services-page .why-two-col,
.armely-managed-services-page .cta-inner {
  align-items: stretch;
}
.armely-managed-services-page .intro-grid,
.armely-managed-services-page .symptoms-grid,
.armely-managed-services-page .when-grid,
.armely-managed-services-page .delivers-grid,
.armely-managed-services-page .tier-grid,
.armely-managed-services-page .covers-grid,
.armely-managed-services-page .steps-row,
.armely-managed-services-page .uc-grid,
.armely-managed-services-page .testi-grid,
.armely-managed-services-page .why-two-col,
.armely-managed-services-page .pathway-grid {
  margin-top: 56px;
}
.armely-managed-services-page .deliver-icon,
.armely-managed-services-page .uc-icon,
.armely-managed-services-page .why-icon,
.armely-managed-services-page .symptom-icon,
.armely-managed-services-page .what-card-icon,
.armely-managed-services-page .cov-item-icon,
.armely-managed-services-page .cover-icon,
.armely-managed-services-page .product-card-icon,
.armely-managed-services-page .cap-icon,
.armely-managed-services-page .workload-pill-icon,
.armely-managed-services-page .decision-icon,
.armely-managed-services-page .sign-icon,
.armely-managed-services-page .pathway-icon,
.armely-managed-services-page .onelake-callout-icon,
.armely-managed-services-page .vs-callout-icon {
  color: var(--blue);
  font-size: 1.1rem;
  line-height: 1;
}
.armely-managed-services-page .deliver-icon,
.armely-managed-services-page .uc-icon,
.armely-managed-services-page .why-icon {
  width: 48px;
  height: 48px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-managed-services-page .deliver-card,
.armely-managed-services-page .uc-card,
.armely-managed-services-page .testi-card,
.armely-managed-services-page .tier-card,
.armely-managed-services-page .cover-card,
.armely-managed-services-page .pathway-card,
.armely-managed-services-page .partner-block,
.armely-managed-services-page .cta-form {
  background: linear-gradient(180deg, #ffffff 0%, #f9fbfe 100%);
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
@media (max-width: 900px) {
  .armely-managed-services-page .hero { padding: 118px 24px 76px; }
  .armely-managed-services-page section:not(.hero) > .section-inner > .section-title,
  .armely-managed-services-page .cta-inner > div > .section-title { max-width: 100%; }
}
</style>
<div class="armely-managed-services-page">
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-eyebrow">
    <span class="eyebrow-badge">Managed Services</span>
    <span class="eyebrow-partner">Certified Microsoft partner</span>
  </div>
  <h1>Your Microsoft environment,<br>managed by the team<br><span class="hl">that built it.</span></h1>
  <p class="hero-sub">Armely Managed Services provides ongoing monitoring, support, optimization, and administration for the Microsoft platforms and custom solutions we implement, with a dedicated account team that knows your environment.</p>
  <div class="hero-actions">
    <a href="#contact" class="btn-primary">Get a Managed Services Proposal</a>
    <a href="#tiers" class="btn-outline">View Service Tiers</a>
  </div>
  <div class="hero-trust">
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Dedicated account manager</strong> who knows your environment</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Proactive monitoring</strong> not just reactive support</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">Covers <strong>Microsoft 365, Dynamics 365, Azure, and custom applications</strong></span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Fixed monthly fee</strong> with no surprise charges</span>
    </div>
  </div>
</section>

<!-- WHAT IS MANAGED SERVICES -->
<section class="intro">
  <div class="section-inner">
    <div class="intro-grid">
      <div>
        <div class="section-eyebrow">What Managed Services Means at Armely</div>
        <h2 class="section-title">Not a help desk. A dedicated technical team that knows your business.</h2>
        <p class="section-body">Most managed service providers offer a ticket queue staffed by generalists. Armely Managed Services is structured differently. Your account team is the same team that implemented your environment, so support is provided by people who already understand how your systems were built and why.</p>
        <div class="what-cards">
          <div class="what-card">
            <span class="what-card-icon"><i class="fa-solid fa-circle-nodes" aria-hidden="true"></i></span>
            <div>
              <div class="what-card-title">Proactive Monitoring and Alerting</div>
              <div class="what-card-desc">We monitor your Microsoft 365 tenant health, Azure infrastructure, application performance, and security posture continuously and alert you before issues affect your users, not after.</div>
            </div>
          </div>
          <div class="what-card">
            <span class="what-card-icon"><i class="fa-solid fa-circle-nodes" aria-hidden="true"></i></span>
            <div>
              <div class="what-card-title">Ongoing Administration</div>
              <div class="what-card-desc">User provisioning, license management, configuration changes, patch management, and routine administrative tasks handled by Armely so your internal team is not diverted from their primary work.</div>
            </div>
          </div>
          <div class="what-card">
            <span class="what-card-icon"><i class="fa-solid fa-circle-nodes" aria-hidden="true"></i></span>
            <div>
              <div class="what-card-title">Optimization and Advisory</div>
              <div class="what-card-desc">Quarterly business reviews covering usage, performance, cost, and upcoming Microsoft releases. We identify optimization opportunities and recommend changes before problems develop rather than responding to complaints.</div>
            </div>
          </div>
          <div class="what-card">
            <span class="what-card-icon"><i class="fa-solid fa-circle-nodes" aria-hidden="true"></i></span>
            <div>
              <div class="what-card-title">Ongoing Development and Enhancements</div>
              <div class="what-card-desc">New requirements, feature requests, and enhancements to existing solutions handled within the managed services agreement rather than as separate project engagements, depending on your chosen tier.</div>
            </div>
          </div>
        </div>
      </div>
      <div>
        <div class="coverage-card">
          <div class="cov-header">
            <div class="cov-dots"><span></span><span></span><span></span></div>
            <span class="cov-title">Platform Coverage</span>
          </div>
          <div class="cov-body">
            <div class="cov-item">
              <div class="cov-item-left">
                <span class="cov-item-icon"><i class="fa-solid fa-server" aria-hidden="true"></i></span>
                <span class="cov-item-name">Microsoft 365 and Teams</span>
              </div>
              <span class="cov-badge badge-on">Monitoring</span>
            </div>
            <div class="cov-item">
              <div class="cov-item-left">
                <span class="cov-item-icon"><i class="fa-solid fa-server" aria-hidden="true"></i></span>
                <span class="cov-item-name">Azure Infrastructure</span>
              </div>
              <span class="cov-badge badge-proactive">Proactive</span>
            </div>
            <div class="cov-item">
              <div class="cov-item-left">
                <span class="cov-item-icon"><i class="fa-solid fa-server" aria-hidden="true"></i></span>
                <span class="cov-item-name">Dynamics 365</span>
              </div>
              <span class="cov-badge badge-on">Monitoring</span>
            </div>
            <div class="cov-item">
              <div class="cov-item-left">
                <span class="cov-item-icon"><i class="fa-solid fa-server" aria-hidden="true"></i></span>
                <span class="cov-item-name">Power Platform</span>
              </div>
              <span class="cov-badge badge-on">Monitoring</span>
            </div>
            <div class="cov-item">
              <div class="cov-item-left">
                <span class="cov-item-icon"><i class="fa-solid fa-server" aria-hidden="true"></i></span>
                <span class="cov-item-name">SharePoint</span>
              </div>
              <span class="cov-badge badge-on">Monitoring</span>
            </div>
            <div class="cov-item">
              <div class="cov-item-left">
                <span class="cov-item-icon"><i class="fa-solid fa-server" aria-hidden="true"></i></span>
                <span class="cov-item-name">SQL Server and Databases</span>
              </div>
              <span class="cov-badge badge-proactive">Proactive</span>
            </div>
            <div class="cov-item">
              <div class="cov-item-left">
                <span class="cov-item-icon"><i class="fa-solid fa-server" aria-hidden="true"></i></span>
                <span class="cov-item-name">Custom Applications</span>
              </div>
              <span class="cov-badge badge-proactive">Proactive</span>
            </div>
            <div class="cov-sla">
              <span style="font-size:1.1rem;"><i class="fa-solid fa-clock" aria-hidden="true"></i></span>
              <div class="cov-sla-text"><strong>Response SLAs</strong> defined per tier from 1-hour critical response to next business day for routine requests, documented in your service agreement.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- SERVICE TIERS -->
<section class="tiers" id="tiers">
  <div class="section-inner">
    <div class="section-eyebrow">Service Tiers</div>
    <h2 class="section-title">Three tiers designed for different levels of support need and environment complexity.</h2>
    <p class="section-body">Pricing is based on your environment scope and complexity. Contact Armely for a proposal tailored to your specific platforms and user count.</p>
    <div class="tier-grid">
      <div class="tier-card">
        <div class="tier-name">Foundation</div>
        <div class="tier-desc">For organizations that need reliable monitoring and responsive support without dedicated administration.</div>
        <ul class="tier-features">
          <li>Microsoft 365 tenant monitoring and health alerts</li>
          <li>Security and compliance dashboard review</li>
          <li>8x5 support with next business day response</li>
          <li>Monthly platform health report</li>
          <li>License management and user provisioning</li>
          <li>Access to Armely's Microsoft licensing at partner pricing</li>
          <li>Annual Microsoft release wave briefing</li>
        </ul>
        <div class="tier-note">Suitable for organizations with a stable Microsoft 365 environment and an internal IT resource handling day-to-day administration.</div>
      </div>
      <div class="tier-card featured">
        <div class="tier-badge">Most Common</div>
        <div class="tier-name">Professional</div>
        <div class="tier-desc">For organizations that need proactive management across multiple Microsoft platforms and faster response times.</div>
        <ul class="tier-features">
          <li>Everything in Foundation, plus:</li>
          <li>Azure, Dynamics 365, and Power Platform coverage</li>
          <li>Proactive performance monitoring and alerting</li>
          <li>4-hour response for critical issues</li>
          <li>Quarterly business reviews with Armely account manager</li>
          <li>Patch and update management</li>
          <li>Minor enhancement requests included</li>
          <li>Dedicated named account manager</li>
        </ul>
        <div class="tier-note">Suitable for organizations running multiple Microsoft platforms without a specialist internal team to manage them.</div>
      </div>
      <div class="tier-card">
        <div class="tier-name">Enterprise</div>
        <div class="tier-desc">For organizations with complex environments, custom applications, and requirements for continuous optimization and development.</div>
        <ul class="tier-features">
          <li>Everything in Professional, plus:</li>
          <li>Custom application and database coverage</li>
          <li>1-hour response for critical issues</li>
          <li>Included development hours each month</li>
          <li>Monthly business reviews</li>
          <li>AI and data platform management</li>
          <li>Security posture reviews and remediation</li>
          <li>Dedicated senior technical account manager</li>
        </ul>
        <div class="tier-note">Suitable for organizations with custom Azure-hosted applications, SQL Server environments, or AI deployments requiring active management.</div>
      </div>
    </div>
  </div>
</section>

<!-- WHAT WE COVER -->
<section class="covers" id="covers">
  <div class="section-inner">
    <div class="section-eyebrow">What We Cover</div>
    <h2 class="section-title">Managed services across the full Microsoft platform and beyond.</h2>
    <p class="section-body">Armely Managed Services covers the platforms we implement. If Armely built it or configured it, we can manage it. If you have an existing environment we did not build, we conduct an onboarding assessment before taking responsibility for it.</p>
    <div class="covers-grid">
      <div class="cover-card">
        <span class="cover-icon"><i class="fa-solid fa-server" aria-hidden="true"></i></span>
        <div class="cover-title">Microsoft 365 Administration</div>
        <div class="cover-desc">Tenant health monitoring, user lifecycle management, license optimization, Exchange Online, Teams administration, SharePoint governance, and security and compliance policy management.</div>
      </div>
      <div class="cover-card">
        <span class="cover-icon"><i class="fa-solid fa-server" aria-hidden="true"></i></span>
        <div class="cover-title">Azure Infrastructure Management</div>
        <div class="cover-desc">Azure resource monitoring, cost optimization, backup verification, performance management, security patching, and infrastructure scaling for Azure App Service, Azure Functions, and Azure SQL environments.</div>
      </div>
      <div class="cover-card">
        <span class="cover-icon"><i class="fa-solid fa-server" aria-hidden="true"></i></span>
        <div class="cover-title">Dynamics 365 and Power Platform</div>
        <div class="cover-desc">Dynamics 365 environment management, release wave update planning, Power Platform governance, flow monitoring, connector management, and minor configuration and enhancement requests.</div>
      </div>
      <div class="cover-card">
        <span class="cover-icon"><i class="fa-solid fa-server" aria-hidden="true"></i></span>
        <div class="cover-title">SQL Server and Database Management</div>
        <div class="cover-desc">SQL Server monitoring, backup management, performance tuning, index maintenance, patch management, and capacity planning for on-premises and Azure-hosted SQL Server environments.</div>
      </div>
      <div class="cover-card">
        <span class="cover-icon"><i class="fa-solid fa-server" aria-hidden="true"></i></span>
        <div class="cover-title">Custom Application Support</div>
        <div class="cover-desc">Monitoring, incident response, bug fixes, and minor enhancements for custom applications built by Armely on the Azure and .NET stack, with defined SLAs and a named technical contact familiar with the codebase.</div>
      </div>
      <div class="cover-card">
        <span class="cover-icon"><i class="fa-solid fa-server" aria-hidden="true"></i></span>
        <div class="cover-title">AI and Agent Management</div>
        <div class="cover-desc">Monitoring and governance of deployed Copilot Studio agents and Azure AI Foundry applications, including usage analytics, permission audits, model updates, and content review to ensure agents continue performing as designed.</div>
      </div>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials">
  <div class="section-inner">
    <div class="section-eyebrow">Client Results</div>
    <h2 class="section-title">What our clients say about Armely Managed Services.</h2>
    <div class="testi-grid">
      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">The biggest difference with Armely is that when we call, we are talking to someone who already knows our environment. We do not have to explain what we built or why it works the way it does. They built it. That context saves time on every single support interaction and means issues get resolved faster than with any previous provider.</p>
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
        <p class="testi-body">We moved to Armely Managed Services after our Dynamics 365 implementation. The quarterly business reviews have been particularly valuable. They come to each one with a list of things they have noticed and recommend addressing before they become problems. That proactive approach has prevented at least two significant issues in the past year.</p>
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
        <p class="testi-body">We are a small IT team managing a complex Microsoft environment across multiple campuses. The Armely managed services agreement effectively extends our team with specialists in every platform we run. The fixed monthly fee means we can budget for it accurately, and the included enhancement hours mean small requests do not pile up waiting for a project budget.</p>
        <div class="testi-footer">
          <div class="testi-avatar">CIO</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Chief Information Officer</div>
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
        <div class="section-eyebrow">Why Armely Managed Services</div>
        <h2 class="section-title">Managed services from the team that knows your environment.</h2>
        <p class="section-body">The difference between Armely Managed Services and a generic managed services provider is not just technical capability. It is the institutional knowledge that comes from being the team that built and implemented the environment you are managing.</p>
        <ul class="why-list">
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Context That Cannot Be Transferred</div>
              <div class="why-item-desc">When Armely manages an environment we implemented, we know why every decision was made, what the constraints were, and what the business depends on. That context makes every support interaction faster and every recommendation more relevant.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Proactive Rather Than Reactive</div>
              <div class="why-item-desc">We monitor continuously and review quarterly. Most issues we resolve before clients are aware of them. The measure of good managed services is problems that did not happen, not tickets that were closed quickly.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">One Team Across the Full Stack</div>
              <div class="why-item-desc">Armely covers Microsoft 365, Azure, Dynamics 365, Power Platform, SharePoint, SQL Server, and custom applications under a single managed services agreement. You have one account manager and one invoice, not a different vendor for each platform layer.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Predictable Fixed Monthly Fee</div>
              <div class="why-item-desc">Your managed services fee is fixed based on your environment scope. There are no unexpected charges for routine support, monitoring, or the minor enhancements included in your tier. Budget certainty is part of the value.</div>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <div class="partner-block">
          <div class="partner-block-top">
            <div class="partner-label">Microsoft Authorized Partner</div>
            <p class="partner-text">Armely's Microsoft partnership gives our managed services practice access to partner support channels, early release information, and Microsoft licensing at rates not available to direct buyers. That means faster escalation paths for complex issues and licensing cost savings passed directly to managed services clients.</p>
          </div>
          <div class="partner-stats">
            <div class="p-stat">
              <div class="p-stat-num">1</div>
              <div class="p-stat-label">dedicated account manager per client who knows your environment personally</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">7</div>
              <div class="p-stat-label">Microsoft platforms covered under a single managed services agreement</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">QBR</div>
              <div class="p-stat-label">quarterly business reviews included at Professional and Enterprise tiers</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">0</div>
              <div class="p-stat-label">surprise charges for routine support, monitoring, or included enhancement hours</div>
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
      <h2 class="section-title">Tell us what you are running. We will tell you what managing it looks like.</h2>
      <p class="section-body">Book a free 30-minute scoping call. We will review your current Microsoft environment, understand your support needs, and come back with a managed services proposal and fixed monthly fee at no obligation.</p>
      <div style="margin-top: 28px; display: flex; flex-direction: column; gap: 12px;">
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Free scoping call, no commitment required</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Fixed monthly proposal included, no hourly surprises</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Response within one business day</span>
        </div>
      </div>
    </div>
    <div class="cta-form">
      <div class="form-title">Get a Managed Services Proposal</div>
      <div class="form-sub">Tell us about your current environment.</div>
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
        <label>Platforms You Need Managed</label>
        <select>
          <option value="">Select primary platform...</option>
          <option>Microsoft 365 and Teams only</option>
          <option>Microsoft 365 plus Azure infrastructure</option>
          <option>Microsoft 365 plus Dynamics 365</option>
          <option>Full Microsoft stack including custom applications</option>
          <option>SQL Server and database management</option>
          <option>AI agents and Azure AI management</option>
          <option>Multiple, need a full assessment</option>
        </select>
      </div>
      <button class="form-submit">Request Managed Services Proposal</button>
      <div class="form-note">No spam. No sales pressure. Just a useful conversation.</div>
    </div>
  </div>
</section>
</div>

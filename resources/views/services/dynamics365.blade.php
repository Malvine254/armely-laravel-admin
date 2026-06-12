<style>

.armely-dynamics365-page *, .armely-dynamics365-page *::before, .armely-dynamics365-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-dynamics365-page {
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

.armely-dynamics365-page { scroll-behavior: smooth; }
.armely-dynamics365-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* ── NAV ── */
.armely-dynamics365-page nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    display: flex; justify-content: space-between; align-items: center;
    padding: 18px 56px;
    background: rgba(26,46,82,0.96);
    backdrop-filter: blur(14px);
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }
.armely-dynamics365-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-dynamics365-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-dynamics365-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-dynamics365-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-dynamics365-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-dynamics365-page .nav-links a:hover { color: #fff; }
.armely-dynamics365-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-dynamics365-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* ── HERO ── */
.armely-dynamics365-page .hero {
    min-height: 100vh;
    display: flex; flex-direction: column; justify-content: center;
    padding: 140px 56px 100px;
    position: relative; overflow: hidden;
    background: #1a2e52;
  }
.armely-dynamics365-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-dynamics365-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-dynamics365-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-dynamics365-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-dynamics365-page .hero h1 { font-size: clamp(2.6rem, 5.5vw, 4.8rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 780px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-dynamics365-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-dynamics365-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 560px; margin-bottom: 40px; line-height: 1.8; }
.armely-dynamics365-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-dynamics365-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-dynamics365-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-dynamics365-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-dynamics365-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-dynamics365-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-dynamics365-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-dynamics365-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-dynamics365-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-dynamics365-page .trust-text strong { color: #fff; font-weight: 600; }

  /* ── SECTIONS ── */
.armely-dynamics365-page section { padding: 96px 56px; }
.armely-dynamics365-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-dynamics365-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-dynamics365-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 620px; }
.armely-dynamics365-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 540px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* ── MODULES ── */
.armely-dynamics365-page .modules { background: var(--navy-mid); }
.armely-dynamics365-page .modules-intro { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }

.armely-dynamics365-page .module-tabs { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 32px; }
.armely-dynamics365-page .module-tab {
    background: #fff; border: 1px solid var(--border); border-radius: 10px;
    padding: 14px 16px; cursor: pointer;
    display: flex; align-items: flex-start; gap: 12px;
    transition: border-color 0.2s, background 0.2s;
  }
.armely-dynamics365-page .module-tab:hover { border-color: rgba(41,78,139,0.3); background: var(--blue-dim); }
.armely-dynamics365-page .module-tab-icon { font-size: 1.1rem; flex-shrink: 0; width: 34px; height: 34px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 7px; display: flex; align-items: center; justify-content: center; }
.armely-dynamics365-page .module-tab-content {}
.armely-dynamics365-page .module-tab-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 2px; }
.armely-dynamics365-page .module-tab-desc { font-size: 0.73rem; color: var(--text-muted); line-height: 1.4; }

  /* CRM / ERP visual */
.armely-dynamics365-page .platform-visual { display: flex; flex-direction: column; gap: 12px; }
.armely-dynamics365-page .platform-band {
    border-radius: 12px; padding: 20px 22px;
    border: 1px solid var(--border);
  }
.armely-dynamics365-page .band-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 10px; }
.armely-dynamics365-page .band-chips { display: flex; flex-wrap: wrap; gap: 7px; }
.armely-dynamics365-page .band-chip { font-size: 0.75rem; font-weight: 600; padding: 5px 12px; border-radius: 20px; }
.armely-dynamics365-page .band-crm { background: var(--blue-dim); }
.armely-dynamics365-page .band-crm .band-label { color: var(--blue); }
.armely-dynamics365-page .band-crm .band-chip { background: var(--blue-dim2); color: var(--blue); }
.armely-dynamics365-page .band-erp { background: rgba(41,78,139,0.05); }
.armely-dynamics365-page .band-erp .band-label { color: var(--blue); }
.armely-dynamics365-page .band-erp .band-chip { background: rgba(41,78,139,0.1); color: var(--blue); }
.armely-dynamics365-page .band-foundation { background: var(--blue); }
.armely-dynamics365-page .band-foundation .band-label { color: rgba(255,255,255,0.7); }
.armely-dynamics365-page .band-foundation .band-chip { background: rgba(255,255,255,0.15); color: #fff; }
.armely-dynamics365-page .band-connector { text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2px 0; }

  /* ── DELIVERS ── */
.armely-dynamics365-page .delivers { background: var(--navy); }
.armely-dynamics365-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-dynamics365-page .deliver-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-dynamics365-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-dynamics365-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-dynamics365-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-dynamics365-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* ── JOURNEY ── */
.armely-dynamics365-page .journey { background: var(--navy-mid); }
.armely-dynamics365-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-dynamics365-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-dynamics365-page .step:last-child { border-right: none; }
.armely-dynamics365-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-dynamics365-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-dynamics365-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-dynamics365-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* ── USE CASES ── */
.armely-dynamics365-page .usecases { background: var(--navy); }
.armely-dynamics365-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-dynamics365-page .uc-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-dynamics365-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-dynamics365-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-dynamics365-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-dynamics365-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* ── WHY ARMELY ── */
.armely-dynamics365-page .why { background: var(--navy-mid); }
.armely-dynamics365-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-dynamics365-page .why-list { list-style: none; margin-top: 36px; }
.armely-dynamics365-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-dynamics365-page .why-list li:last-child { border-bottom: none; }
.armely-dynamics365-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-dynamics365-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-dynamics365-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-dynamics365-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-dynamics365-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-dynamics365-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-dynamics365-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-dynamics365-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-dynamics365-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-dynamics365-page .p-stat:nth-child(2) { border-right: none; }
.armely-dynamics365-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-dynamics365-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-dynamics365-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-dynamics365-page .p-stat-num span { color: var(--blue); }
.armely-dynamics365-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* ── CTA ── */
.armely-dynamics365-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-dynamics365-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-dynamics365-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-dynamics365-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-dynamics365-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-dynamics365-page .form-row { margin-bottom: 14px; }
.armely-dynamics365-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-dynamics365-page .form-row input, .armely-dynamics365-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-dynamics365-page .form-row input:focus, .armely-dynamics365-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-dynamics365-page .form-row select option { background: #fff; color: #1A2540; }
.armely-dynamics365-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-dynamics365-page .form-submit:hover { background: var(--blue-lt); }
.armely-dynamics365-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* ── FOOTER ── */
.armely-dynamics365-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-dynamics365-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-dynamics365-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-dynamics365-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-dynamics365-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-dynamics365-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-dynamics365-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* ── RESPONSIVE ── */
  @media (max-width: 900px) {
.armely-dynamics365-page nav { padding: 16px 24px; }
.armely-dynamics365-page .nav-links { display: none; }
.armely-dynamics365-page section { padding: 72px 24px; }
.armely-dynamics365-page .hero { padding: 110px 24px 72px; }
.armely-dynamics365-page .modules-intro, .armely-dynamics365-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-dynamics365-page .delivers-grid, .armely-dynamics365-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-dynamics365-page .steps-row { grid-template-columns: 1fr; }
.armely-dynamics365-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-dynamics365-page .step:last-child { border-bottom: none; }
.armely-dynamics365-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-dynamics365-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-dynamics365-page .delivers-grid, .armely-dynamics365-page .uc-grid { grid-template-columns: 1fr; }
.armely-dynamics365-page .partner-stats { grid-template-columns: 1fr; }
.armely-dynamics365-page .hero-trust { gap: 20px; }
.armely-dynamics365-page .module-tabs { grid-template-columns: 1fr; }
.armely-dynamics365-page .module-tab[style*="grid-column"] { grid-column: auto; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

  /* ── TESTIMONIALS ── */
.armely-dynamics365-page .testimonials { background: var(--navy-mid); padding: 96px 56px; }
.armely-dynamics365-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-dynamics365-page .testi-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-dynamics365-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-dynamics365-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-dynamics365-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-dynamics365-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-dynamics365-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-dynamics365-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-dynamics365-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }
  @media (max-width: 900px) { .testimonials { padding: 72px 24px; } .testi-grid { grid-template-columns: 1fr; } }

/* Armely service-page polish */
.armely-dynamics365-page {
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
.armely-dynamics365-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-dynamics365-page .hero::after {
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
.armely-dynamics365-page .section-title,
.armely-dynamics365-page .deliver-title,
.armely-dynamics365-page .uc-title,
.armely-dynamics365-page .step-title,
.armely-dynamics365-page .why-item-title,
.armely-dynamics365-page .form-title {
  color: #162b49;
}
.armely-dynamics365-page .deliver-card,
.armely-dynamics365-page .uc-card,
.armely-dynamics365-page .testi-card,
.armely-dynamics365-page .platform-card,
.armely-dynamics365-page .partner-block,
.armely-dynamics365-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-dynamics365-page .deliver-card:hover,
.armely-dynamics365-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-dynamics365-page .btn-primary,
.armely-dynamics365-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-dynamics365-page .btn-primary:hover,
.armely-dynamics365-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-dynamics365-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-dynamics365-page nav,
.armely-dynamics365-page footer {
  display: none;
}
</style>
<div class="armely-dynamics365-page">
<!-- NAV -->


<!-- HERO -->
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-eyebrow">
    <span class="eyebrow-badge">Microsoft Dynamics 365</span>
    <span class="eyebrow-partner">Delivered by a certified Microsoft partner</span>
  </div>
  <h1>CRM and ERP, finally<br><span class="hl">working as one.</span></h1>
  <p class="hero-sub">Armely implements and customizes Microsoft Dynamics 365 so your sales, finance, operations, and customer service teams share the same data, and the same source of truth.</p>
  <div class="hero-actions">
    <a href="#contact" class="btn-primary">Book a Free Discovery Call</a>
    <a href="#modules" class="btn-outline">Explore the Modules</a>
  </div>
  <div class="hero-trust">
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>ERP + CRM</strong> in one connected platform</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">Native integration with <strong>Microsoft 365 & Teams</strong></span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Copilot AI agents</strong> across every module</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Partner pricing</strong> available</span>
    </div>
  </div>
</section>

<!-- WHAT IS DYNAMICS 365 -->
<section class="modules" id="modules">
  <div class="section-inner">
    <div class="modules-intro">
      <div>
        <div class="section-eyebrow">What is Microsoft Dynamics 365?</div>
        <h2 class="section-title">One platform for every team that touches your customer or your numbers.</h2>
        <p class="section-body">Dynamics 365 is Microsoft's cloud platform that unifies CRM and ERP into a single, modular system. You choose the apps your business needs today and add more as you grow, all sharing the same data, the same security model, and the same Copilot AI layer.</p>
        <div class="module-tabs">
          <div class="module-tab">
            <div class="module-tab-icon">💼</div>
            <div class="module-tab-content">
              <div class="module-tab-title">Business Central</div>
              <div class="module-tab-desc">All-in-one ERP for SMBs covering finance, inventory, purchasing, projects, and manufacturing in one place. Forbes' #1 cloud ERP for SMBs in 2025.</div>
            </div>
          </div>
          <div class="module-tab">
            <div class="module-tab-icon">🎯</div>
            <div class="module-tab-content">
              <div class="module-tab-title">Sales</div>
              <div class="module-tab-desc">AI-powered CRM that automates lead research, drafts emails, summarises opportunities, and surfaces deal risks before they cost you.</div>
            </div>
          </div>
          <div class="module-tab">
            <div class="module-tab-icon">🎧</div>
            <div class="module-tab-content">
              <div class="module-tab-title">Customer Service</div>
              <div class="module-tab-desc">Case management, AI routing, and autonomous agents that resolve issues faster across voice, chat, and digital channels.</div>
            </div>
          </div>
          <div class="module-tab">
            <div class="module-tab-icon">🔧</div>
            <div class="module-tab-content">
              <div class="module-tab-title">Field Service</div>
              <div class="module-tab-desc">AI scheduling, work order management, and proactive maintenance for teams that send technicians to customers, with 65%+ first-time fix rate improvements reported.</div>
            </div>
          </div>
          <div class="module-tab" style="grid-column: 1 / -1;">
            <div class="module-tab-icon">📣</div>
            <div class="module-tab-content">
              <div class="module-tab-title">Customer Insights</div>
              <div class="module-tab-desc">Unified customer profiles and AI-driven marketing journeys that engage the right person at the right moment across every channel.</div>
            </div>
          </div>
        </div>
      </div>
      <div>
        <div class="platform-visual">
          <div class="platform-band band-crm">
            <div class="band-label">CRM Applications</div>
            <div class="band-chips">
              <span class="band-chip">Sales</span>
              <span class="band-chip">Customer Service</span>
              <span class="band-chip">Field Service</span>
              <span class="band-chip">Customer Insights</span>
              <span class="band-chip">Contact Center</span>
            </div>
          </div>
          <div class="band-connector">↕</div>
          <div class="platform-band band-erp">
            <div class="band-label">ERP Applications</div>
            <div class="band-chips">
              <span class="band-chip">Business Central</span>
              <span class="band-chip">Finance</span>
              <span class="band-chip">Supply Chain</span>
              <span class="band-chip">Project Operations</span>
              <span class="band-chip">Human Resources</span>
            </div>
          </div>
          <div class="band-connector">↕</div>
          <div class="platform-band band-foundation">
            <div class="band-label">Shared Foundation</div>
            <div class="band-chips">
              <span class="band-chip">Copilot AI Agents</span>
              <span class="band-chip">Power Platform</span>
              <span class="band-chip">Microsoft 365</span>
              <span class="band-chip">Dataverse</span>
              <span class="band-chip">Power BI</span>
              <span class="band-chip">Azure</span>
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
    <h2 class="section-title">Implementation that fits your business, not the other way around.</h2>
    <p class="section-body">Dynamics 365 is powerful out of the box and endlessly configurable. Armely makes sure you get the right modules, the right configuration, and the right training without months of scope creep.</p>
    <div class="delivers-grid">
      <div class="deliver-card">
        <div class="deliver-icon">🗺️</div>
        <div class="deliver-title">Business Process Discovery</div>
        <div class="deliver-desc">Before touching a single setting, we map how your business actually works: your sales process, finance workflows, service operations, and reporting needs, and design a Dynamics 365 configuration that fits them.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">⚙️</div>
        <div class="deliver-title">Configuration & Customization</div>
        <div class="deliver-desc">We configure Dynamics 365 to match your workflows, terminology, and approval processes. Where standard configuration isn't enough, we extend with Power Apps and Power Automate rather than bespoke code that breaks on upgrades.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🔗</div>
        <div class="deliver-title">System Integration</div>
        <div class="deliver-desc">We connect Dynamics 365 to your existing tools, including accounting software, e-commerce platforms, marketing systems, and data sources, so information flows automatically and your team stops re-entering the same data twice.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">📦</div>
        <div class="deliver-title">Data Migration</div>
        <div class="deliver-desc">We migrate your customer records, financial history, open orders, and contact data from your legacy system into Dynamics 365, clean, validated, and complete. No fresh starts, no lost history.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🤖</div>
        <div class="deliver-title">Copilot AI Configuration</div>
        <div class="deliver-desc">Dynamics 365 Copilot agents are activated and tuned for your team, including drafting sales emails, summarizing service cases, proposing journal entries, and automating scheduling. AI that works in your context, not a generic demo.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon">🎓</div>
        <div class="deliver-title">Training & Ongoing Support</div>
        <div class="deliver-desc">Role-specific training for every team, plus a dedicated Armely account manager for post-go-live support, new module rollouts, and the inevitable "can we add this?" requests that come six months in.</div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNEY -->
<section class="journey" id="journey">
  <div class="section-inner">
    <div class="section-eyebrow">The Armely Dynamics 365 Journey</div>
    <h2 class="section-title">From messy spreadsheets and disconnected tools to one system that works.</h2>
    <p class="section-body">We follow Microsoft's Success by Design methodology, refined through real implementations across healthcare, education, and professional services, so your go-live is an event, not a crisis.</p>
    <div class="steps-row">
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-title">Discovery & Scoping</div>
        <div class="step-desc">We document your processes, pain points, and must-haves. You get a clear module recommendation and implementation plan before committing.</div>
        <span class="step-tag">Free</span>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-title">Licensing & Design</div>
        <div class="step-desc">We source the right licenses at partner pricing and design your Dynamics 365 environment, data model, and integration architecture.</div>
        <span class="step-tag">Weeks 1–2</span>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-title">Build & Configure</div>
        <div class="step-desc">Configuration, customization, integrations, and data migration, built iteratively with your team's input at every checkpoint.</div>
        <span class="step-tag">Weeks 3–8</span>
      </div>
      <div class="step">
        <div class="step-num">04</div>
        <div class="step-title">Test & Go Live</div>
        <div class="step-desc">User acceptance testing, parallel running where needed, and a managed go-live with Armely on hand for every issue on day one.</div>
        <span class="step-tag">Weeks 9–10</span>
      </div>
      <div class="step">
        <div class="step-num">05</div>
        <div class="step-title">Optimise & Grow</div>
        <div class="step-desc">Post-go-live support, adoption tracking, release wave updates, and new modules added as your business evolves.</div>
        <span class="step-tag">Ongoing</span>
      </div>
    </div>
  </div>
</section>

<!-- USE CASES -->
<section class="usecases">
  <div class="section-inner">
    <div class="section-eyebrow">What It Looks Like in Practice</div>
    <h2 class="section-title">Real business problems, solved with Dynamics 365.</h2>
    <p class="section-body">Every Dynamics 365 engagement is different, but these are the situations we hear most often, and where a well-implemented system delivers the clearest, fastest return.</p>
    <div class="uc-grid">
      <div class="uc-card">
        <span class="uc-icon">📋</span>
        <div class="uc-title">Replace Disconnected Tools</div>
        <div class="uc-desc">Retiring a mix of Sage, spreadsheets, and a legacy CRM into one platform. Sales sees customer history. Finance sees open orders. Service sees account status. Everyone works from the same data.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">💰</span>
        <div class="uc-title">Automate Finance & Reporting</div>
        <div class="uc-desc">Business Central automates AP/AR, period-end closing, bank reconciliation, and cash flow forecasting, so your finance team stops spending three days on month-end and starts spending an hour.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">🎯</span>
        <div class="uc-title">Give Sales Teams an Edge</div>
        <div class="uc-desc">Dynamics 365 Sales with Copilot researches leads, drafts outreach emails, surfaces deal risks, and keeps CRM updated automatically, so your sellers sell instead of administrate.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">🔧</span>
        <div class="uc-title">Field Service That Predicts Problems</div>
        <div class="uc-desc">AI scheduling dispatches the right technician with the right parts. IoT-connected assets trigger work orders automatically. Customers get proactive updates before they call you.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">📦</span>
        <div class="uc-title">Take Control of Inventory</div>
        <div class="uc-desc">Real-time inventory, purchase order automation, and demand forecasting in Business Central mean you stop running out of stock and avoid tying up cash in stock you don't need.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon">📊</span>
        <div class="uc-title">Leadership Dashboards That Update Themselves</div>
        <div class="uc-desc">Power BI connected to live Dynamics 365 data gives leadership real-time visibility across sales pipeline, service performance, cash position, and operations without weekly report runs.</div>
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
        <p class="testi-body">Armely took the time to understand how our finance team actually operated before configuring anything. The Business Central implementation came in on schedule, and our month-end close process dropped from five days to under two. The training was practical and role-specific, which made adoption straightforward.</p>
        <div class="testi-footer">
          <div class="testi-avatar">CF</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Chief Financial Officer</div>
            <div class="testi-role">Healthcare Services Organization, Midwest</div>
          </div>
        </div>
      </div>

      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">We had been running a legacy CRM and a separate accounting system that never talked to each other. Armely consolidated everything into Dynamics 365 Sales and Business Central. Our sales team now sees customer payment history before they walk into a meeting, and finance closes the books without waiting on exports from anyone.</p>
        <div class="testi-footer">
          <div class="testi-avatar">VP</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">VP of Operations</div>
            <div class="testi-role">Professional Services Firm, Texas</div>
          </div>
        </div>
      </div>

      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">Our field service scheduling was entirely manual and we were missing SLAs regularly. Armely implemented Dynamics 365 Field Service with AI-assisted scheduling and automated customer notifications. First-time fix rates improved significantly, and our dispatch team spends far less time on the phone coordinating logistics.</p>
        <div class="testi-footer">
          <div class="testi-avatar">DO</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Director of Operations</div>
            <div class="testi-role">Facilities Management Company, Southeast</div>
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
        <h2 class="section-title">Dynamics 365 implementations succeed when the partner knows your industry.</h2>
        <p class="section-body">Most Dynamics 365 projects that struggle do so because of poor requirements gathering, generic configuration, and training that didn't match how people actually work. We've built our process to fix all three.</p>
        <ul class="why-list">
          <li>
            <div class="why-icon">🎯</div>
            <div>
              <div class="why-item-title">Certified Dynamics 365 Implementors</div>
              <div class="why-item-desc">Our team holds Microsoft Dynamics 365 certifications across Business Central, Sales, and Customer Service, with hands-on delivery experience, not just exam passes.</div>
            </div>
          </li>
          <li>
            <div class="why-icon">🏥</div>
            <div>
              <div class="why-item-title">Proven Across Healthcare & Education</div>
              <div class="why-item-desc">We've delivered Microsoft solutions for Swope Health Systems, Plano ISD, and UNMC, organizations where data governance, compliance, and user adoption all matter equally.</div>
            </div>
          </li>
          <li>
            <div class="why-icon">🔗</div>
            <div>
              <div class="why-item-title">Full Microsoft Stack Expertise</div>
              <div class="why-item-desc">Dynamics 365 works best alongside Microsoft 365, Power BI, Power Platform, and Azure. Armely covers the whole stack so your ERP, CRM, AI, and analytics are designed to work together from day one.</div>
            </div>
          </li>
          <li>
            <div class="why-icon">💰</div>
            <div>
              <div class="why-item-title">Right Licenses at Partner Pricing</div>
              <div class="why-item-desc">As a Microsoft-authorized CSP partner, we access Business Central and Dynamics 365 licensing at rates not available through direct purchase, and we help you start with exactly what you need, not a bundle you'll never use.</div>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <div class="partner-block">
          <div class="partner-block-top">
            <div class="partner-label">Microsoft Authorized Partner</div>
            <p class="partner-text">Armely's Microsoft partnership gives us access to Dynamics 365 licensing, technical pre-sales support, and implementation resources not available to direct buyers. That means better pricing, a faster start, and a build backed by Microsoft's own best-practice frameworks.</p>
          </div>
          <div class="partner-stats">
            <div class="p-stat">
              <div class="p-stat-num">#1</div>
              <div class="p-stat-label">Business Central ranked best cloud ERP for SMBs by Forbes, 2025</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">10<span>+</span></div>
              <div class="p-stat-label">Dynamics 365 modules. Start with one, grow from there</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">65<span>%</span></div>
              <div class="p-stat-label">of Field Service users report improved first-time fix rates with Copilot AI</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">1</div>
              <div class="p-stat-label">shared data platform across CRM, ERP, AI, and analytics</div>
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
      <h2 class="section-title">Let's find the right Dynamics 365 fit for your business.</h2>
      <p class="section-body">Book a free 30-minute discovery call. We'll understand your current tools and processes, identify which Dynamics 365 modules apply to your situation, and come back with a clear implementation proposal and licensing quote, with no obligation.</p>
      <div style="margin-top: 28px; display: flex; flex-direction: column; gap: 12px;">
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Free discovery, no commitment required</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Module recommendation and partner pricing included</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Response within one business day</span>
        </div>
      </div>
    </div>
    <div class="cta-form">
      <div class="form-title">Book Your Free Discovery Call</div>
      <div class="form-sub">Tell us what you're trying to fix or replace.</div>
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
        <label>What are you looking to solve?</label>
        <select>
          <option value="">Select...</option>
          <option>Replace our current ERP (Sage, QuickBooks, etc.)</option>
          <option>Replace or add a CRM system</option>
          <option>Connect our finance and sales teams</option>
          <option>Improve customer service operations</option>
          <option>Manage field service / scheduling</option>
          <option>Get better reporting and dashboards</option>
          <option>Not sure, need advice on where to start</option>
        </select>
      </div>
      <button class="form-submit">Request Free Discovery Call →</button>
      <div class="form-note">No spam. No sales pressure. Just a useful conversation.</div>
    </div>
  </div>
</section>

<!-- FOOTER -->
</div>

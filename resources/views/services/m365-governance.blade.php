<style>

.armely-m365-governance-page *, .armely-m365-governance-page *::before, .armely-m365-governance-page *::after { box-sizing: border-box; margin: 0; padding: 0; }
.armely-m365-governance-page {
    --navy: #FFFFFF; --navy-mid: #F3F6FB; --navy-card: #EBF0F8;
    --blue: #294e8b; --blue-lt: #3d6ab5;
    --blue-dim: rgba(41,78,139,0.08); --blue-dim2: rgba(41,78,139,0.16);
    --text-body: #3D4F6B; --text-muted: #6B7FA3; --border: rgba(41,78,139,0.1);
  }
.armely-m365-governance-page { scroll-behavior: smooth; }
.armely-m365-governance-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

.armely-m365-governance-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-m365-governance-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-m365-governance-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-m365-governance-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-m365-governance-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-m365-governance-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-m365-governance-page .nav-links a:hover { color: #fff; }
.armely-m365-governance-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; }
.armely-m365-governance-page .nav-cta:hover { background: var(--blue-lt) !important; }

.armely-m365-governance-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-m365-governance-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-m365-governance-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-m365-governance-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-m365-governance-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-m365-governance-page .hero h1 { font-size: clamp(2.6rem, 5.5vw, 4.8rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-m365-governance-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-m365-governance-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.8; }
.armely-m365-governance-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-m365-governance-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-m365-governance-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-m365-governance-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-m365-governance-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-m365-governance-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-m365-governance-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-m365-governance-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-m365-governance-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-m365-governance-page .trust-text strong { color: #fff; font-weight: 600; }

.armely-m365-governance-page section { padding: 96px 56px; }
.armely-m365-governance-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-m365-governance-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-m365-governance-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 640px; }
.armely-m365-governance-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* SYMPTOMS */
.armely-m365-governance-page .symptoms { background: var(--navy-mid); }
.armely-m365-governance-page .symptoms-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-m365-governance-page .symptom-list { display: flex; flex-direction: column; gap: 10px; margin-top: 28px; }
.armely-m365-governance-page .symptom { background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 14px 18px; display: flex; align-items: flex-start; gap: 12px; }
.armely-m365-governance-page .symptom.flagged { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-m365-governance-page .symptom-icon { font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }
.armely-m365-governance-page .symptom-text { font-size: 0.84rem; color: var(--text-body); line-height: 1.6; }
.armely-m365-governance-page .symptom-text strong { color: #1A2540; }

  /* Governance visual */
.armely-m365-governance-page .gov-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-m365-governance-page .gov-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-m365-governance-page .gov-dots { display: flex; gap: 6px; }
.armely-m365-governance-page .gov-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-m365-governance-page .gov-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-m365-governance-page .gov-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.armely-m365-governance-page .gov-area { border-radius: 9px; padding: 13px 16px; }
.armely-m365-governance-page .gov-area-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
.armely-m365-governance-page .gov-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.armely-m365-governance-page .gov-chip { font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.armely-m365-governance-page .area-security { background: var(--blue-dim); }
.armely-m365-governance-page .area-security .gov-area-label { color: var(--blue); }
.armely-m365-governance-page .area-security .gov-chip { background: var(--blue-dim2); color: var(--blue); }
.armely-m365-governance-page .area-adoption { background: rgba(41,78,139,0.05); }
.armely-m365-governance-page .area-adoption .gov-area-label { color: var(--blue); }
.armely-m365-governance-page .area-adoption .gov-chip { background: rgba(41,78,139,0.1); color: var(--blue); }
.armely-m365-governance-page .area-compliance { background: var(--blue); }
.armely-m365-governance-page .area-compliance .gov-area-label { color: rgba(255,255,255,0.7); }
.armely-m365-governance-page .area-compliance .gov-chip { background: rgba(255,255,255,0.15); color: #fff; }
.armely-m365-governance-page .gov-arrow { text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2px 0; }

  /* DELIVERS */
.armely-m365-governance-page .delivers { background: var(--navy); }
.armely-m365-governance-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-m365-governance-page .deliver-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-m365-governance-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-m365-governance-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-m365-governance-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-m365-governance-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-m365-governance-page .journey { background: var(--navy-mid); }
.armely-m365-governance-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-m365-governance-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-m365-governance-page .step:last-child { border-right: none; }
.armely-m365-governance-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-m365-governance-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-m365-governance-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-m365-governance-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* USE CASES */
.armely-m365-governance-page .usecases { background: var(--navy); }
.armely-m365-governance-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-m365-governance-page .uc-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-m365-governance-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-m365-governance-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-m365-governance-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-m365-governance-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* TESTIMONIALS */
.armely-m365-governance-page .testimonials { background: var(--navy-mid); padding: 96px 56px; }
.armely-m365-governance-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-m365-governance-page .testi-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-m365-governance-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-m365-governance-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-m365-governance-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-m365-governance-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; }
.armely-m365-governance-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-m365-governance-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-m365-governance-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY */
.armely-m365-governance-page .why { background: var(--navy); }
.armely-m365-governance-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-m365-governance-page .why-list { list-style: none; margin-top: 36px; }
.armely-m365-governance-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-m365-governance-page .why-list li:last-child { border-bottom: none; }
.armely-m365-governance-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-m365-governance-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-m365-governance-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-m365-governance-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-m365-governance-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-m365-governance-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-m365-governance-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-m365-governance-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-m365-governance-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-m365-governance-page .p-stat:nth-child(2) { border-right: none; }
.armely-m365-governance-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-m365-governance-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-m365-governance-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-m365-governance-page .p-stat-num span { color: var(--blue); }
.armely-m365-governance-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* CTA */
.armely-m365-governance-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-m365-governance-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 96px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-m365-governance-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-m365-governance-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-m365-governance-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-m365-governance-page .form-row { margin-bottom: 14px; }
.armely-m365-governance-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-m365-governance-page .form-row input, .armely-m365-governance-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-m365-governance-page .form-row input:focus, .armely-m365-governance-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-m365-governance-page .form-row select option { background: #fff; color: #1A2540; }
.armely-m365-governance-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-m365-governance-page .form-submit:hover { background: var(--blue-lt); }
.armely-m365-governance-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

.armely-m365-governance-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-m365-governance-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-m365-governance-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-m365-governance-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-m365-governance-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-m365-governance-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-m365-governance-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  @media (max-width: 900px) {
.armely-m365-governance-page nav { padding: 16px 24px; }
.armely-m365-governance-page .nav-links { display: none; }
.armely-m365-governance-page section { padding: 72px 24px; }
.armely-m365-governance-page .hero { padding: 110px 24px 72px; }
.armely-m365-governance-page .symptoms-grid, .armely-m365-governance-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-m365-governance-page .delivers-grid, .armely-m365-governance-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-m365-governance-page .steps-row { grid-template-columns: 1fr; }
.armely-m365-governance-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-m365-governance-page .step:last-child { border-bottom: none; }
.armely-m365-governance-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-m365-governance-page .testimonials { padding: 72px 24px; }
.armely-m365-governance-page .testi-grid { grid-template-columns: 1fr; }
.armely-m365-governance-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-m365-governance-page .delivers-grid, .armely-m365-governance-page .uc-grid { grid-template-columns: 1fr; }
.armely-m365-governance-page .partner-stats { grid-template-columns: 1fr; }
.armely-m365-governance-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-m365-governance-page {
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
.armely-m365-governance-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-m365-governance-page .hero::after {
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
.armely-m365-governance-page .section-title,
.armely-m365-governance-page .deliver-title,
.armely-m365-governance-page .uc-title,
.armely-m365-governance-page .step-title,
.armely-m365-governance-page .why-item-title,
.armely-m365-governance-page .form-title {
  color: #162b49;
}
.armely-m365-governance-page .deliver-card,
.armely-m365-governance-page .uc-card,
.armely-m365-governance-page .testi-card,
.armely-m365-governance-page .platform-card,
.armely-m365-governance-page .partner-block,
.armely-m365-governance-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-m365-governance-page .deliver-card:hover,
.armely-m365-governance-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-m365-governance-page .btn-primary,
.armely-m365-governance-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-m365-governance-page .btn-primary:hover,
.armely-m365-governance-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-m365-governance-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-m365-governance-page nav,
.armely-m365-governance-page footer {
  display: none;
}
.armely-m365-governance-page .symptoms-grid,
.armely-m365-governance-page .why-two-col,
.armely-m365-governance-page .cta-inner {
  align-items: stretch;
}
.armely-m365-governance-page .gov-card,
.armely-m365-governance-page .partner-block,
.armely-m365-governance-page .cta-form {
  height: 100%;
}
.armely-m365-governance-page .delivers-grid,
.armely-m365-governance-page .uc-grid,
.armely-m365-governance-page .testi-grid {
  align-items: stretch;
}
.armely-m365-governance-page .deliver-card,
.armely-m365-governance-page .uc-card,
.armely-m365-governance-page .testi-card {
  min-height: 100%;
}
.armely-m365-governance-page .symptom,
.armely-m365-governance-page .deliver-card,
.armely-m365-governance-page .uc-card,
.armely-m365-governance-page .testi-card,
.armely-m365-governance-page .partner-block,
.armely-m365-governance-page .cta-form {
  background: linear-gradient(180deg, #ffffff 0%, #f9fbfe 100%);
}
.armely-m365-governance-page .gov-card,
.armely-m365-governance-page .partner-block,
.armely-m365-governance-page .cta-form {
  border-radius: 12px;
}
.armely-m365-governance-page .section-body {
  max-width: 680px;
}

/* Shared modern service page refresh */
.armely-m365-governance-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: #173b67;
  border-radius: 0;
}
.armely-m365-governance-page .hero::after,
.armely-m365-governance-page .hero-bg-glow,
.armely-m365-governance-page .hero-trust {
  display: none;
}
.armely-m365-governance-page .hero h1 {
  max-width: 820px;
  margin-bottom: 22px;
}
.armely-m365-governance-page .hero-sub {
  max-width: 700px;
  margin-bottom: 34px;
}
.armely-m365-governance-page .hero-actions {
  margin-bottom: 0;
}
.armely-m365-governance-page .hero .btn-primary,
.armely-m365-governance-page .hero .btn-outline {
  border-radius: 0;
}
.armely-m365-governance-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.74);
}
.armely-m365-governance-page .eyebrow-partner {
  display: none;
}
.armely-m365-governance-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-m365-governance-page .cta-inner > div > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-m365-governance-page section:not(.hero) > .section-inner > .section-title,
.armely-m365-governance-page .cta-inner > div > .section-title {
  max-width: 900px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-m365-governance-page section:not(.hero) > .section-inner > .section-body,
.armely-m365-governance-page .cta-inner > div > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-m365-governance-page .intro-grid,
.armely-m365-governance-page .symptoms-grid,
.armely-m365-governance-page .when-grid,
.armely-m365-governance-page .why-two-col,
.armely-m365-governance-page .cta-inner {
  align-items: stretch;
}
.armely-m365-governance-page .intro-grid,
.armely-m365-governance-page .symptoms-grid,
.armely-m365-governance-page .when-grid,
.armely-m365-governance-page .delivers-grid,
.armely-m365-governance-page .tier-grid,
.armely-m365-governance-page .covers-grid,
.armely-m365-governance-page .steps-row,
.armely-m365-governance-page .uc-grid,
.armely-m365-governance-page .testi-grid,
.armely-m365-governance-page .why-two-col,
.armely-m365-governance-page .pathway-grid {
  margin-top: 56px;
}
.armely-m365-governance-page .deliver-icon,
.armely-m365-governance-page .uc-icon,
.armely-m365-governance-page .why-icon,
.armely-m365-governance-page .symptom-icon,
.armely-m365-governance-page .what-card-icon,
.armely-m365-governance-page .cov-item-icon,
.armely-m365-governance-page .cover-icon,
.armely-m365-governance-page .product-card-icon,
.armely-m365-governance-page .cap-icon,
.armely-m365-governance-page .workload-pill-icon,
.armely-m365-governance-page .decision-icon,
.armely-m365-governance-page .sign-icon,
.armely-m365-governance-page .pathway-icon,
.armely-m365-governance-page .onelake-callout-icon,
.armely-m365-governance-page .vs-callout-icon {
  color: var(--blue);
  font-size: 1.1rem;
  line-height: 1;
}
.armely-m365-governance-page .deliver-icon,
.armely-m365-governance-page .uc-icon,
.armely-m365-governance-page .why-icon {
  width: 48px;
  height: 48px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-m365-governance-page .deliver-card,
.armely-m365-governance-page .uc-card,
.armely-m365-governance-page .testi-card,
.armely-m365-governance-page .tier-card,
.armely-m365-governance-page .cover-card,
.armely-m365-governance-page .pathway-card,
.armely-m365-governance-page .partner-block,
.armely-m365-governance-page .cta-form {
  background: linear-gradient(180deg, #ffffff 0%, #f9fbfe 100%);
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
@media (max-width: 900px) {
  .armely-m365-governance-page .hero { padding: 118px 24px 76px; }
  .armely-m365-governance-page section:not(.hero) > .section-inner > .section-title,
  .armely-m365-governance-page .cta-inner > div > .section-title { max-width: 100%; }
}
</style>
<div class="armely-m365-governance-page">
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-eyebrow">
    <span class="eyebrow-badge">Microsoft 365 Governance and Adoption</span>
    <span class="eyebrow-partner">Certified Microsoft partner</span>
  </div>
  <h1>You have Microsoft 365.<br>Is your organization<br><span class="hl">actually using it well?</span></h1>
  <p class="hero-sub">Most organizations have Microsoft 365. Many are using a fraction of what they are paying for, running it without governance, and heading toward a security or compliance problem they do not yet know about. Armely fixes that.</p>
  <div class="hero-actions">
    <a href="#contact" class="btn-primary">Book a Free Tenant Health Check</a>
    <a href="#what-we-deliver" class="btn-outline">See What We Do</a>
  </div>
  <div class="hero-trust">
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Free tenant health check</strong> to start</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">Covers <strong>Teams, SharePoint, OneDrive, Exchange, and Entra ID</strong></span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Security, governance, and adoption</strong> in one engagement</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">Prepares your tenant for <strong>Copilot AI</strong> when you are ready</span>
    </div>
  </div>
</section>

<!-- SYMPTOMS -->
<section class="symptoms">
  <div class="section-inner">
    <div class="symptoms-grid">
      <div>
        <div class="section-eyebrow">Does This Sound Familiar?</div>
        <h2 class="section-title">Signs your Microsoft 365 environment needs attention.</h2>
        <p class="section-body">These are not theoretical concerns. They are the situations Armely finds in almost every Microsoft 365 tenant that has been running without a governance review. Any one of them creates risk. Several together mean it is urgent.</p>
        <div class="symptom-list">
          <div class="symptom flagged">
            <span class="symptom-icon"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></span>
            <div class="symptom-text"><strong>External sharing is on and no one is sure what has been shared.</strong> Files, SharePoint sites, and Teams channels shared with external users months or years ago, with no record and no expiry.</div>
          </div>
          <div class="symptom flagged">
            <span class="symptom-icon"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></span>
            <div class="symptom-text"><strong>Hundreds of Teams and SharePoint sites with no owners.</strong> Abandoned workspaces, duplicate sites, and content no one can find, managed by no one.</div>
          </div>
          <div class="symptom flagged">
            <span class="symptom-icon"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></span>
            <div class="symptom-text"><strong>Former employees still have active accounts or access.</strong> Offboarding processes that leave licenses running, mailboxes accessible, and permissions intact long after a person has left.</div>
          </div>
          <div class="symptom">
            <span class="symptom-icon"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></span>
            <div class="symptom-text"><strong>Staff are not using the tools they have.</strong> Microsoft 365 licenses are paid for but people still email attachments, use personal file storage, and hold meetings without recording or transcription.</div>
          </div>
          <div class="symptom">
            <span class="symptom-icon"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i></span>
            <div class="symptom-text"><strong>You want to add Copilot AI but your tenant is not ready.</strong> Copilot surfaces data based on existing permissions. If your permissions and data classification are not clean, Copilot will expose things it should not.</div>
          </div>
        </div>
      </div>
      <div>
        <div class="gov-card">
          <div class="gov-header">
            <div class="gov-dots"><span></span><span></span><span></span></div>
            <span class="gov-title">Governance Framework Areas</span>
          </div>
          <div class="gov-body">
            <div class="gov-area area-security">
              <div class="gov-area-label">Security and Access</div>
              <div class="gov-chips">
                <span class="gov-chip">Entra ID Conditional Access</span>
                <span class="gov-chip">MFA Enforcement</span>
                <span class="gov-chip">External Sharing Controls</span>
                <span class="gov-chip">Privileged Identity Management</span>
                <span class="gov-chip">Guest Access Policies</span>
              </div>
            </div>
            <div class="gov-arrow">&#8597;</div>
            <div class="gov-area area-adoption">
              <div class="gov-area-label">Structure and Adoption</div>
              <div class="gov-chips">
                <span class="gov-chip">Teams Lifecycle Policies</span>
                <span class="gov-chip">SharePoint Site Standards</span>
                <span class="gov-chip">Naming Conventions</span>
                <span class="gov-chip">Owner Accountability</span>
                <span class="gov-chip">User Training</span>
              </div>
            </div>
            <div class="gov-arrow">&#8597;</div>
            <div class="gov-area area-compliance">
              <div class="gov-area-label">Compliance and Data Protection</div>
              <div class="gov-chips">
                <span class="gov-chip">Microsoft Purview</span>
                <span class="gov-chip">Sensitivity Labels</span>
                <span class="gov-chip">Retention Policies</span>
                <span class="gov-chip">DLP Policies</span>
                <span class="gov-chip">Audit Logging</span>
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
    <h2 class="section-title">A governed, secure, well-adopted Microsoft 365 environment.</h2>
    <p class="section-body">Armely's Microsoft 365 Governance and Adoption practice covers the security controls, structural policies, compliance configuration, and user enablement that turn a chaotic tenant into one your organization can rely on and build from.</p>
    <div class="delivers-grid">
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Tenant Health Assessment</div>
        <div class="deliver-desc">A structured audit of your Microsoft 365 tenant covering security posture, external sharing exposure, guest access, inactive accounts, Teams and SharePoint sprawl, license utilization, and compliance configuration gaps. Delivered as a written report with a prioritized remediation list.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Security Hardening</div>
        <div class="deliver-desc">Implementation of Microsoft's recommended security baselines including Conditional Access policies, MFA enforcement, Entra ID Privileged Identity Management, external sharing controls, and Defender for Microsoft 365 configuration across your tenant.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Governance Framework Implementation</div>
        <div class="deliver-desc">Teams lifecycle policies, SharePoint site provisioning standards, naming conventions, owner accountability processes, guest access policies, and an admin center configuration that prevents ungoverned sprawl from accumulating again after the initial cleanup.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Microsoft Purview and Compliance</div>
        <div class="deliver-desc">Sensitivity label taxonomy, data loss prevention policies, retention schedules, and audit logging configured through Microsoft Purview so your content is classified, protected, and auditable in line with your compliance requirements.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Adoption and Training</div>
        <div class="deliver-desc">Role-specific training programs that show your staff how to use Microsoft 365 effectively, including Teams, SharePoint, OneDrive, and Copilot when relevant. Adoption measurement through Microsoft Viva Insights and the Microsoft 365 admin usage reports.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Copilot Readiness Preparation</div>
        <div class="deliver-desc">If your organization is planning to deploy Microsoft 365 Copilot, we prepare your tenant first. Overshared content remediation, sensitivity label coverage, permission cleanup, and data governance configuration that ensures Copilot surfaces only what it should.</div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNEY -->
<section class="journey" id="journey">
  <div class="section-inner">
    <div class="section-eyebrow">How a Governance Engagement Works</div>
    <h2 class="section-title">From tenant audit to a clean, governed Microsoft 365 environment.</h2>
    <p class="section-body">Governance work is not glamorous but the impact is immediate and lasting. Organizations that complete a governance engagement report fewer security incidents, better compliance audit results, and higher staff adoption of the tools they are already paying for.</p>
    <div class="steps-row">
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-title">Tenant Health Check</div>
        <div class="step-desc">Free automated and manual review of your Microsoft 365 tenant covering the most critical security, governance, and adoption gaps. Written report delivered within one week.</div>
        <span class="step-tag">Free</span>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-title">Prioritized Remediation Plan</div>
        <div class="step-desc">We present findings ranked by risk and impact, agree on the remediation scope, and confirm a timeline and fixed fee for the governance implementation.</div>
        <span class="step-tag">Week 1</span>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-title">Security and Governance Build</div>
        <div class="step-desc">Security controls, governance policies, Purview configuration, and structural standards implemented in your tenant with minimal disruption to day-to-day operations.</div>
        <span class="step-tag">Weeks 2-5</span>
      </div>
      <div class="step">
        <div class="step-num">04</div>
        <div class="step-title">Training and Adoption</div>
        <div class="step-desc">Staff training on the new structures and tools, administrator training on the governance controls, and adoption measurement established for ongoing tracking.</div>
        <span class="step-tag">Week 6</span>
      </div>
      <div class="step">
        <div class="step-num">05</div>
        <div class="step-title">Ongoing Governance</div>
        <div class="step-desc">Quarterly governance reviews, new employee onboarding support, policy updates as Microsoft releases new capabilities, and Copilot readiness validation when relevant.</div>
        <span class="step-tag">Ongoing</span>
      </div>
    </div>
  </div>
</section>

<!-- USE CASES -->
<section class="usecases">
  <div class="section-inner">
    <div class="section-eyebrow">Common Situations</div>
    <h2 class="section-title">The governance challenges we resolve most often.</h2>
    <p class="section-body">Every Microsoft 365 tenant is different but the governance problems that accumulate over time follow recognizable patterns. These are the situations Armely addresses most frequently.</p>
    <div class="uc-grid">
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Oversharing and External Access Cleanup</div>
        <div class="uc-desc">An audit and remediation of external sharing across SharePoint, OneDrive, and Teams. We identify what has been shared externally, review appropriateness, remove unnecessary access, and implement controls that prevent uncontrolled sharing going forward.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Teams and SharePoint Sprawl</div>
        <div class="uc-desc">Organizations with hundreds of Teams and SharePoint sites that have accumulated without structure or ownership. We audit, archive, merge, or delete abandoned workspaces and implement provisioning policies and lifecycle management that prevent the same problem recurring.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Security Baseline Implementation</div>
        <div class="uc-desc">Organizations that deployed Microsoft 365 without implementing Microsoft's recommended security controls. We implement Conditional Access, MFA, Entra ID protection, and Defender for Microsoft 365 configuration against the current Microsoft security benchmark.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Compliance and Audit Preparation</div>
        <div class="uc-desc">Organizations facing a HIPAA, SOC 2, or other compliance audit that need their Microsoft 365 environment to demonstrate appropriate data governance, retention policies, audit logging, and access controls within a defined timeline.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Low Adoption Remediation</div>
        <div class="uc-desc">Organizations paying for Microsoft 365 licenses but finding most staff still email attachments and store files on personal drives. We assess the barriers to adoption, address the structural and training gaps, and establish measurement so progress is visible.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Copilot Readiness Assessment</div>
        <div class="uc-desc">For organizations planning to deploy Microsoft 365 Copilot, we assess whether the tenant's permissions, sensitivity labels, and data governance are ready, remediate the gaps, and confirm the environment is safe for Copilot before activation.</div>
      </div>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials">
  <div class="section-inner">
    <div class="section-eyebrow">Client Results</div>
    <h2 class="section-title">What our clients say about Microsoft 365 governance work with Armely.</h2>
    <div class="testi-grid">
      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">The tenant health check revealed that we had sensitive patient documents accessible to external parties through sharing links that had never been reviewed. We had no idea. Armely remediated the exposure, implemented external sharing controls, and configured sensitivity labels within six weeks. Our compliance officer signed off on the remediation without any further findings.</p>
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
        <p class="testi-body">We had been paying for Microsoft 365 Business Premium for two years but had implemented almost none of the security features included in the license. Armely ran the audit, showed us exactly what we were leaving unused, and implemented the security baseline in four weeks. We now have Conditional Access, MFA, and Defender all running correctly, all included in the license we were already paying for.</p>
        <div class="testi-footer">
          <div class="testi-avatar">CFO</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Chief Financial Officer</div>
            <div class="testi-role">Professional Services Firm, Texas</div>
          </div>
        </div>
      </div>
      <div class="testi-card">
        <span class="testi-quote">&ldquo;</span>
        <p class="testi-body">We wanted to deploy Copilot but our IT team flagged that our tenant was not ready. Armely did the readiness assessment, cleaned up three years of oversharing and abandoned Teams, configured sensitivity labels across our content, and declared the tenant Copilot-ready in eight weeks. We deployed Copilot the following month without any data exposure concerns.</p>
        <div class="testi-footer">
          <div class="testi-avatar">CTO</div>
          <div>
            <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
            <div class="testi-name">Chief Technology Officer</div>
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
        <h2 class="section-title">Microsoft 365 governance done properly, once.</h2>
        <p class="section-body">Governance work done badly just moves the problem. Armely implements controls that actually hold, trains administrators who understand why the controls are there, and measures adoption so the investment is verifiable.</p>
        <ul class="why-list">
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Free Tenant Health Check to Start</div>
              <div class="why-item-desc">Every engagement starts with a free automated and manual assessment of your tenant. You see exactly what the problems are before committing to any remediation work, and you own the report regardless of whether you proceed with Armely.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Copilot Readiness Built In</div>
              <div class="why-item-desc">Every governance engagement Armely delivers prepares your tenant for Microsoft 365 Copilot, whether or not you plan to deploy it immediately. Clean permissions, sensitivity labels, and data governance are prerequisites for safe AI deployment, and we build them in from day one.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Adoption Is Measured, Not Assumed</div>
              <div class="why-item-desc">We configure Microsoft 365 usage analytics and Viva Insights reporting so you can see adoption levels by team and tool over time, not just receive a training completion certificate and hope for the best.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Regulated Industry Experience</div>
              <div class="why-item-desc">We have implemented Microsoft 365 governance for healthcare providers and educational institutions where HIPAA compliance, student data protection, and audit readiness are not optional. We understand what governance needs to look like in environments where getting it wrong has real consequences.</div>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <div class="partner-block">
          <div class="partner-block-top">
            <div class="partner-label">Microsoft Authorized Partner</div>
            <p class="partner-text">Armely's Microsoft partnership gives us access to the Microsoft Secure Score benchmarking tools, Microsoft 365 admin center partner features, and early access to governance capability updates before they reach general availability. That means your governance framework is built against current Microsoft best practices, not documentation that is several months behind the product.</p>
          </div>
          <div class="partner-stats">
            <div class="p-stat">
              <div class="p-stat-num">190<span>M</span></div>
              <div class="p-stat-label">people use Microsoft 365, most in environments without formal governance</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">Free</div>
              <div class="p-stat-label">tenant health check to identify your specific risks before any commitment</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">6-8</div>
              <div class="p-stat-label">weeks for a complete governance implementation in a standard tenant</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">100<span>%</span></div>
              <div class="p-stat-label">of Armely governance engagements include Copilot readiness preparation</div>
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
      <h2 class="section-title">Start with a free tenant health check. No commitment required.</h2>
      <p class="section-body">Book a free Microsoft 365 Tenant Health Check. We will run a structured assessment of your environment and deliver a written report identifying your most significant security, governance, and adoption gaps within one week. You own the report regardless of whether you proceed with Armely.</p>
      <div style="margin-top: 28px; display: flex; flex-direction: column; gap: 12px;">
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Free tenant health check, written report delivered within one week</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">You own the report regardless of next steps</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Response within one business day</span>
        </div>
      </div>
    </div>
    <div class="cta-form">
      <div class="form-title">Book Your Free Tenant Health Check</div>
      <div class="form-sub">Tell us about your Microsoft 365 environment.</div>
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
        <label>Primary Concern</label>
        <select>
          <option value="">Select...</option>
          <option>Security and external sharing exposure</option>
          <option>Teams and SharePoint sprawl and governance</option>
          <option>Compliance and audit readiness</option>
          <option>Low staff adoption of Microsoft 365</option>
          <option>Copilot readiness preparation</option>
          <option>General tenant health review</option>
          <option>Not sure, want an assessment first</option>
        </select>
      </div>
      <button class="form-submit">Request Free Tenant Health Check</button>
      <div class="form-note">No spam. No sales pressure. Just a useful conversation.</div>
    </div>
  </div>
</section>
</div>

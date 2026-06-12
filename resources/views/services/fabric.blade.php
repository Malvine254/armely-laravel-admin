<style>

.armely-fabric-page *, .armely-fabric-page *::before, .armely-fabric-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-fabric-page {
    --navy:      #FFFFFF;
    --navy-mid:  #F3F6FB;
    --navy-card: #EBF0F8;
    --blue:      #294e8b;
    --blue-lt:   #3d6ab5;
    --blue-dim:  rgba(41,78,139,0.08);
    --blue-dim2: rgba(41,78,139,0.16);
    --white:     #1A2540;
    --off-white: #F5F7FA;
    --text-body: #3D4F6B;
    --text-muted:#6B7FA3;
    --border:    rgba(41,78,139,0.1);
  }

.armely-fabric-page { scroll-behavior: smooth; }

.armely-fabric-page {
    font-family: 'Poppins', sans-serif;
    background: var(--navy);
    color: var(--text-body);
    line-height: 1.6;
  }

  /* ── NAV ── */
.armely-fabric-page nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    display: flex; justify-content: space-between; align-items: center;
    padding: 18px 56px;
    background: rgba(26,46,82,0.96);
    backdrop-filter: blur(14px);
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }
.armely-fabric-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-fabric-page .logo-mark {
    width: 36px; height: 36px;
    background: var(--blue);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 1rem; color: #fff;
  }
.armely-fabric-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-fabric-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-fabric-page .nav-links a {
    color: rgba(255,255,255,0.6); text-decoration: none;
    font-size: 0.875rem; font-weight: 500; transition: color 0.2s;
  }
.armely-fabric-page .nav-links a:hover { color: #fff; }
.armely-fabric-page .nav-cta {
    background: var(--blue); color: #fff !important;
    padding: 10px 22px; border-radius: 6px;
    font-size: 0.875rem; font-weight: 600 !important;
    transition: background 0.2s !important;
  }
.armely-fabric-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* ── HERO ── */
.armely-fabric-page .hero {
    min-height: 100vh;
    display: flex; flex-direction: column; justify-content: center;
    padding: 140px 56px 100px;
    position: relative; overflow: hidden;
    background: #1a2e52;
  }
.armely-fabric-page .hero-bg-glow {
    position: absolute; top: -180px; right: -100px;
    width: 720px; height: 720px;
    background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%);
    pointer-events: none;
  }
.armely-fabric-page .hero h1 {
    font-size: clamp(2.6rem, 5.5vw, 4.8rem);
    font-weight: 800; line-height: 1.08;
    color: #FFFFFF; max-width: 780px;
    margin-bottom: 24px; letter-spacing: -0.03em;
  }
.armely-fabric-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-fabric-page .hero-sub {
    font-size: 1.05rem; font-weight: 300;
    color: rgba(255,255,255,0.82); max-width: 540px;
    margin-bottom: 40px; line-height: 1.8;
  }
.armely-fabric-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-fabric-page .eyebrow-badge {
    background: rgba(41,78,139,0.35);
    border: 1px solid rgba(255,255,255,0.2);
    color: rgba(255,255,255,0.9);
    font-size: 0.72rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.12em;
    padding: 5px 14px; border-radius: 40px;
  }
.armely-fabric-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-fabric-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-fabric-page .btn-primary {
    background: var(--blue); color: #fff;
    border: none; border-radius: 7px; padding: 14px 32px;
    font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600;
    cursor: pointer; text-decoration: none;
    transition: background 0.2s, transform 0.15s; display: inline-block;
  }
.armely-fabric-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-fabric-page .btn-outline {
    background: transparent; color: rgba(255,255,255,0.85);
    border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px;
    font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500;
    cursor: pointer; text-decoration: none;
    transition: border-color 0.2s, background 0.2s; display: inline-block;
  }
.armely-fabric-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-fabric-page .hero-trust {
    display: flex; gap: 40px; flex-wrap: wrap;
    padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12);
  }
.armely-fabric-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-fabric-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-fabric-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-fabric-page .trust-text strong { color: #fff; font-weight: 600; }

  /* ── SECTION SHELL ── */
.armely-fabric-page section { padding: 96px 56px; }
.armely-fabric-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-fabric-page .section-eyebrow {
    font-size: 0.72rem; text-transform: uppercase;
    letter-spacing: 0.14em; color: var(--blue);
    margin-bottom: 14px; font-weight: 600;
  }
.armely-fabric-page .section-title {
    font-size: clamp(1.7rem, 3.2vw, 2.6rem);
    font-weight: 800; color: #1A2540;
    line-height: 1.12; letter-spacing: -0.025em;
    margin-bottom: 18px; max-width: 620px;
  }
.armely-fabric-page .section-body {
    font-size: 0.975rem; font-weight: 300;
    max-width: 540px; line-height: 1.8;
    color: var(--text-body); margin-bottom: 48px;
  }

  /* ── WHAT IS FABRIC ── */
.armely-fabric-page .intro { background: var(--navy-mid); }
.armely-fabric-page .intro-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-fabric-page .workload-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 28px; }
.armely-fabric-page .workload-pill {
    background: var(--blue-dim);
    border: 1px solid var(--blue-dim2);
    border-radius: 10px; padding: 12px 16px;
    display: flex; align-items: center; gap: 10px;
  }
.armely-fabric-page .workload-pill-icon { font-size: 1.1rem; flex-shrink: 0; }
.armely-fabric-page .workload-pill-label { font-size: 0.8rem; font-weight: 600; color: var(--blue); line-height: 1.3; }
.armely-fabric-page .onelake-callout {
    margin-top: 20px;
    background: var(--blue);
    border-radius: 10px; padding: 16px 20px;
    display: flex; align-items: center; gap: 14px;
  }
.armely-fabric-page .onelake-callout-icon { font-size: 1.4rem; flex-shrink: 0; }
.armely-fabric-page .onelake-callout-text { font-size: 0.85rem; color: rgba(255,255,255,0.9); line-height: 1.55; }
.armely-fabric-page .onelake-callout-text strong { color: #fff; }

  /* ── DIAGRAM ── */
.armely-fabric-page .diagram-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px; overflow: hidden;
    box-shadow: 0 4px 24px rgba(41,78,139,0.08);
  }
.armely-fabric-page .diagram-header {
    padding: 16px 22px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 10px;
    background: var(--navy-mid);
  }
.armely-fabric-page .diagram-dots { display: flex; gap: 6px; }
.armely-fabric-page .diagram-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-fabric-page .diagram-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-fabric-page .diagram-body { padding: 24px; }
.armely-fabric-page .diag-layer {
    border-radius: 10px; padding: 14px 18px;
    margin-bottom: 8px; text-align: center;
  }
.armely-fabric-page .diag-layer-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
.armely-fabric-page .diag-layer-items { display: flex; gap: 6px; flex-wrap: wrap; justify-content: center; }
.armely-fabric-page .diag-chip {
    font-size: 0.72rem; font-weight: 600; padding: 4px 10px;
    border-radius: 20px;
  }
.armely-fabric-page .layer-workloads { background: rgba(41,78,139,0.07); }
.armely-fabric-page .layer-workloads .diag-layer-label { color: var(--blue); }
.armely-fabric-page .layer-workloads .diag-chip { background: var(--blue-dim2); color: var(--blue); }
.armely-fabric-page .layer-platform { background: rgba(41,78,139,0.12); }
.armely-fabric-page .layer-platform .diag-layer-label { color: var(--blue); }
.armely-fabric-page .layer-platform .diag-chip { background: rgba(41,78,139,0.2); color: var(--blue); }
.armely-fabric-page .layer-storage { background: var(--blue); }
.armely-fabric-page .layer-storage .diag-layer-label { color: rgba(255,255,255,0.7); }
.armely-fabric-page .layer-storage .diag-chip { background: rgba(255,255,255,0.15); color: #fff; }
.armely-fabric-page .diag-arrow { text-align: center; color: var(--text-muted); font-size: 1rem; margin: 4px 0; }

  /* ── WHAT ARMELY DELIVERS ── */
.armely-fabric-page .delivers { background: var(--navy); }
.armely-fabric-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-fabric-page .deliver-card {
    background: var(--navy-card);
    border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px;
    transition: border-color 0.2s, transform 0.2s;
  }
.armely-fabric-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-fabric-page .deliver-icon {
    width: 48px; height: 48px; background: var(--blue-dim);
    border: 1px solid var(--blue-dim2); border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; margin-bottom: 20px;
  }
.armely-fabric-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-fabric-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* ── JOURNEY ── */
.armely-fabric-page .journey { background: var(--navy-mid); }
.armely-fabric-page .steps-row {
    display: grid; grid-template-columns: repeat(5, 1fr);
    gap: 0; margin-top: 56px;
    border: 1px solid var(--border); border-radius: 14px; overflow: hidden;
  }
.armely-fabric-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-fabric-page .step:last-child { border-right: none; }
.armely-fabric-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-fabric-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-fabric-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-fabric-page .step-tag {
    display: inline-block; margin-top: 14px;
    background: var(--blue-dim); color: var(--blue);
    font-size: 0.7rem; padding: 3px 10px; border-radius: 4px;
    font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em;
  }

  /* ── USE CASES ── */
.armely-fabric-page .usecases { background: var(--navy); }
.armely-fabric-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-fabric-page .uc-card {
    background: var(--navy-card); border: 1px solid var(--border);
    border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s;
  }
.armely-fabric-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-fabric-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-fabric-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-fabric-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* ── WHY ARMELY ── */
.armely-fabric-page .why { background: var(--navy-mid); }
.armely-fabric-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-fabric-page .why-list { list-style: none; margin-top: 36px; }
.armely-fabric-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-fabric-page .why-list li:last-child { border-bottom: none; }
.armely-fabric-page .why-icon {
    width: 42px; height: 42px; flex-shrink: 0;
    background: var(--blue-dim); border: 1px solid var(--blue-dim2);
    border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
  }
.armely-fabric-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-fabric-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-fabric-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-fabric-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-fabric-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-fabric-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-fabric-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-fabric-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-fabric-page .p-stat:nth-child(2) { border-right: none; }
.armely-fabric-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-fabric-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-fabric-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-fabric-page .p-stat-num span { color: var(--blue); }
.armely-fabric-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* ── CTA ── */
.armely-fabric-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-fabric-page .cta-inner {
    max-width: 1100px; margin: 0 auto; padding: 96px 56px;
    display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center;
  }
.armely-fabric-page .cta-form {
    background: #FFFFFF; border: 1px solid var(--border);
    border-radius: 14px; padding: 36px 32px;
    box-shadow: 0 4px 24px rgba(41,78,139,0.08);
  }
.armely-fabric-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-fabric-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-fabric-page .form-row { margin-bottom: 14px; }
.armely-fabric-page .form-row label {
    display: block; font-size: 0.75rem; font-weight: 600;
    color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px;
  }
.armely-fabric-page .form-row input, .armely-fabric-page .form-row select {
    width: 100%; background: #FFFFFF;
    border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px;
    font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540;
    outline: none; transition: border-color 0.2s;
  }
.armely-fabric-page .form-row input:focus, .armely-fabric-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-fabric-page .form-row select option { background: #fff; color: #1A2540; }
.armely-fabric-page .form-submit {
    width: 100%; background: var(--blue); color: #fff;
    border: none; border-radius: 7px; padding: 14px; margin-top: 8px;
    font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600;
    cursor: pointer; transition: background 0.2s;
  }
.armely-fabric-page .form-submit:hover { background: var(--blue-lt); }
.armely-fabric-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* ── FOOTER ── */
.armely-fabric-page footer {
    background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08);
    padding: 36px 56px;
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;
  }
.armely-fabric-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-fabric-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-fabric-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-fabric-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-fabric-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-fabric-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* ── RESPONSIVE ── */
  @media (max-width: 900px) {
.armely-fabric-page nav { padding: 16px 24px; }
.armely-fabric-page .nav-links { display: none; }
.armely-fabric-page section { padding: 72px 24px; }
.armely-fabric-page .hero { padding: 110px 24px 72px; }
.armely-fabric-page .intro-grid, .armely-fabric-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-fabric-page .delivers-grid, .armely-fabric-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-fabric-page .steps-row { grid-template-columns: 1fr; }
.armely-fabric-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-fabric-page .step:last-child { border-bottom: none; }
.armely-fabric-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-fabric-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-fabric-page .delivers-grid, .armely-fabric-page .uc-grid { grid-template-columns: 1fr; }
.armely-fabric-page .workload-grid { grid-template-columns: 1fr; }
.armely-fabric-page .partner-stats { grid-template-columns: 1fr; }
.armely-fabric-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-fabric-page {
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
.armely-fabric-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-fabric-page .hero::after {
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
.armely-fabric-page .section-title,
.armely-fabric-page .deliver-title,
.armely-fabric-page .uc-title,
.armely-fabric-page .step-title,
.armely-fabric-page .why-item-title,
.armely-fabric-page .form-title {
  color: #162b49;
}
.armely-fabric-page .deliver-card,
.armely-fabric-page .uc-card,
.armely-fabric-page .testi-card,
.armely-fabric-page .platform-card,
.armely-fabric-page .partner-block,
.armely-fabric-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-fabric-page .deliver-card:hover,
.armely-fabric-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-fabric-page .btn-primary,
.armely-fabric-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-fabric-page .btn-primary:hover,
.armely-fabric-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-fabric-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-fabric-page nav,
.armely-fabric-page footer {
  display: none;
}

/* Shared modern service page refresh */
.armely-fabric-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: #173b67;
  border-radius: 0;
}
.armely-fabric-page .hero::after,
.armely-fabric-page .hero-bg-glow,
.armely-fabric-page .hero-trust {
  display: none;
}
.armely-fabric-page .hero h1 {
  max-width: 820px;
  margin-bottom: 22px;
}
.armely-fabric-page .hero-sub {
  max-width: 700px;
  margin-bottom: 34px;
}
.armely-fabric-page .hero-actions {
  margin-bottom: 0;
}
.armely-fabric-page .hero .btn-primary,
.armely-fabric-page .hero .btn-outline {
  border-radius: 0;
}
.armely-fabric-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.74);
}
.armely-fabric-page .eyebrow-partner {
  display: none;
}
.armely-fabric-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-fabric-page .cta-inner > div > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-fabric-page section:not(.hero) > .section-inner > .section-title,
.armely-fabric-page .cta-inner > div > .section-title {
  max-width: 900px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-fabric-page section:not(.hero) > .section-inner > .section-body,
.armely-fabric-page .cta-inner > div > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-fabric-page .intro-grid,
.armely-fabric-page .symptoms-grid,
.armely-fabric-page .when-grid,
.armely-fabric-page .why-two-col,
.armely-fabric-page .cta-inner {
  align-items: stretch;
}
.armely-fabric-page .intro-grid,
.armely-fabric-page .symptoms-grid,
.armely-fabric-page .when-grid,
.armely-fabric-page .delivers-grid,
.armely-fabric-page .tier-grid,
.armely-fabric-page .covers-grid,
.armely-fabric-page .steps-row,
.armely-fabric-page .uc-grid,
.armely-fabric-page .testi-grid,
.armely-fabric-page .why-two-col,
.armely-fabric-page .pathway-grid {
  margin-top: 56px;
}
.armely-fabric-page .deliver-icon,
.armely-fabric-page .uc-icon,
.armely-fabric-page .why-icon,
.armely-fabric-page .symptom-icon,
.armely-fabric-page .what-card-icon,
.armely-fabric-page .cov-item-icon,
.armely-fabric-page .cover-icon,
.armely-fabric-page .product-card-icon,
.armely-fabric-page .cap-icon,
.armely-fabric-page .workload-pill-icon,
.armely-fabric-page .decision-icon,
.armely-fabric-page .sign-icon,
.armely-fabric-page .pathway-icon,
.armely-fabric-page .onelake-callout-icon,
.armely-fabric-page .vs-callout-icon {
  color: var(--blue);
  font-size: 1.1rem;
  line-height: 1;
}
.armely-fabric-page .deliver-icon,
.armely-fabric-page .uc-icon,
.armely-fabric-page .why-icon {
  width: 48px;
  height: 48px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-fabric-page .deliver-card,
.armely-fabric-page .uc-card,
.armely-fabric-page .testi-card,
.armely-fabric-page .tier-card,
.armely-fabric-page .cover-card,
.armely-fabric-page .pathway-card,
.armely-fabric-page .partner-block,
.armely-fabric-page .cta-form {
  background: linear-gradient(180deg, #ffffff 0%, #f9fbfe 100%);
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
@media (max-width: 900px) {
  .armely-fabric-page .hero { padding: 118px 24px 76px; }
  .armely-fabric-page section:not(.hero) > .section-inner > .section-title,
  .armely-fabric-page .cta-inner > div > .section-title { max-width: 100%; }
}


/* Final clean hero fix */
.armely-fabric-page .hero {
  min-height: auto !important;
  padding: 86px 56px 68px !important;
  background: #173b67 !important;
  border-radius: 0 !important;
  display: block !important;
  overflow: hidden !important;
}
.armely-fabric-page .hero-bg-glow,
.armely-fabric-page .hero::after,
.armely-fabric-page .hero-trust {
  display: none !important;
}
.armely-fabric-page .hero-eyebrow {
  display: block !important;
  margin: 0 0 24px 0 !important;
}
.armely-fabric-page .eyebrow-badge {
  display: inline-block !important;
  background: transparent !important;
  border: 0 !important;
  border-radius: 0 !important;
  padding: 0 !important;
  color: rgba(255,255,255,0.78) !important;
  font-size: 0.78rem !important;
  font-weight: 700 !important;
  letter-spacing: 0.14em !important;
}
.armely-fabric-page .eyebrow-partner {
  display: none !important;
}
.armely-fabric-page .hero h1 {
  max-width: 920px !important;
  margin: 0 0 24px 0 !important;
  color: #ffffff !important;
  font-size: clamp(3rem, 5.2vw, 5.3rem) !important;
  line-height: 1.05 !important;
  letter-spacing: -0.045em !important;
  text-align: left !important;
}
.armely-fabric-page .hero h1 .hl {
  display: inline !important;
  color: #ffffff !important;
  opacity: 1 !important;
}
.armely-fabric-page .hero-sub {
  max-width: 760px !important;
  margin: 0 0 34px 0 !important;
  color: rgba(255,255,255,0.84) !important;
  font-size: 1.04rem !important;
  line-height: 1.7 !important;
  text-align: left !important;
}
.armely-fabric-page .hero-actions {
  display: flex !important;
  gap: 16px !important;
  flex-wrap: wrap !important;
  margin: 0 !important;
}
.armely-fabric-page .hero .btn-primary,
.armely-fabric-page .hero .btn-outline {
  border-radius: 6px !important;
  padding: 14px 32px !important;
}
.armely-fabric-page section:not(.hero) {
  padding-top: 72px !important;
  padding-bottom: 72px !important;
}
.armely-fabric-page .intro-grid,
.armely-fabric-page .delivers-grid,
.armely-fabric-page .steps-row,
.armely-fabric-page .uc-grid,
.armely-fabric-page .why-two-col {
  margin-top: 36px !important;
}
@media (max-width: 900px) {
  .armely-fabric-page .hero {
    padding: 70px 24px 56px !important;
  }
  .armely-fabric-page .hero h1 {
    font-size: clamp(2.35rem, 11vw, 3.8rem) !important;
  }
}

</style>
<div class="armely-fabric-page">
<!-- NAV -->


<!-- HERO -->
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-eyebrow">
    <span class="eyebrow-badge">Microsoft Fabric</span>
    <span class="eyebrow-partner">Delivered by a certified Microsoft partner</span>
  </div>
  <h1>All your data.<br>One platform.<br><span class="hl">Actual insights.</span></h1>
  <p class="hero-sub">Armely designs, builds, and runs Microsoft Fabric environments that turn scattered business data into dashboards, reports, and decisions — without the usual chaos.</p>
  <div class="hero-actions">
    <a href="#contact" class="btn-primary">Book a Free Discovery Call</a>
    <a href="#what-we-deliver" class="btn-outline">See What We Build</a>
  </div>
  <div class="hero-trust">
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>One platform</strong> for all data & analytics</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">Replaces <strong>Power BI, Azure Data Factory</strong> & more</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>AI-ready</strong> — Copilot built in</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Partner pricing</strong> available</span>
    </div>
  </div>
</section>

<!-- WHAT IS FABRIC -->
<section class="intro">
  <div class="section-inner">
    <div class="intro-grid">
      <div>
        <div class="section-eyebrow">What is Microsoft Fabric?</div>
        <h2 class="section-title">One platform for every step of your data journey.</h2>
        <p class="section-body">Microsoft Fabric is a unified SaaS analytics platform that replaces the fragmented stack of separate tools most businesses have accumulated. Data engineering, warehousing, real-time analytics, business intelligence, and AI — all in one environment, all sharing a single data foundation called OneLake.</p>
        <div class="workload-grid">
          <div class="workload-pill"><span class="workload-pill-icon"><i class="fa-solid fa-database" aria-hidden="true"></i></span><span class="workload-pill-label">Data Factory</span></div>
          <div class="workload-pill"><span class="workload-pill-icon"><i class="fa-solid fa-database" aria-hidden="true"></i></span><span class="workload-pill-label">Data Engineering</span></div>
          <div class="workload-pill"><span class="workload-pill-icon"><i class="fa-solid fa-database" aria-hidden="true"></i></span><span class="workload-pill-label">Data Warehouse</span></div>
          <div class="workload-pill"><span class="workload-pill-icon"><i class="fa-solid fa-database" aria-hidden="true"></i></span><span class="workload-pill-label">Data Science</span></div>
          <div class="workload-pill"><span class="workload-pill-icon"><i class="fa-solid fa-database" aria-hidden="true"></i></span><span class="workload-pill-label">Real-Time Intelligence</span></div>
          <div class="workload-pill"><span class="workload-pill-icon"><i class="fa-solid fa-database" aria-hidden="true"></i></span><span class="workload-pill-label">Power BI</span></div>
        </div>
        <div class="onelake-callout">
          <span class="onelake-callout-icon"><i class="fa-solid fa-database" aria-hidden="true"></i></span>
          <span class="onelake-callout-text"><strong>OneLake</strong> — one shared data lake underneath it all. Every workload reads from the same source. No duplication, no sync issues, no silos.</span>
        </div>
      </div>
      <div>
        <div class="diagram-card">
          <div class="diagram-header">
            <div class="diagram-dots"><span></span><span></span><span></span></div>
            <span class="diagram-title">Microsoft Fabric Architecture</span>
          </div>
          <div class="diagram-body">
            <div class="diag-layer layer-workloads">
              <div class="diag-layer-label">Workloads</div>
              <div class="diag-layer-items">
                <span class="diag-chip">Power BI</span>
                <span class="diag-chip">Data Factory</span>
                <span class="diag-chip">Data Engineering</span>
                <span class="diag-chip">Data Warehouse</span>
                <span class="diag-chip">Real-Time Intelligence</span>
                <span class="diag-chip">Data Science</span>
              </div>
            </div>
            <div class="diag-arrow">↕</div>
            <div class="diag-layer layer-platform">
              <div class="diag-layer-label">Shared Platform</div>
              <div class="diag-layer-items">
                <span class="diag-chip">Copilot AI</span>
                <span class="diag-chip">Purview Governance</span>
                <span class="diag-chip">Entra ID Security</span>
                <span class="diag-chip">Unified Capacity</span>
              </div>
            </div>
            <div class="diag-arrow">↕</div>
            <div class="diag-layer layer-storage">
              <div class="diag-layer-label">OneLake — Single Storage Layer</div>
              <div class="diag-layer-items">
                <span class="diag-chip">Delta Parquet Format</span>
                <span class="diag-chip">Zero-Copy Access</span>
                <span class="diag-chip">Azure Data Lake Gen2</span>
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
    <h2 class="section-title">We don't just set up Fabric. We make your data work for you.</h2>
    <p class="section-body">As a certified Microsoft partner, Armely handles the full implementation — from architecture design and data migration to dashboards your team will actually use.</p>
    <div class="delivers-grid">
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Data Architecture Design</div>
        <div class="deliver-desc">Before writing a single pipeline, we map your data sources, business questions, and reporting needs. You get an architecture built for your organisation — not a generic template.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Data Integration & Pipelines</div>
        <div class="deliver-desc">We connect your existing systems — ERP, CRM, databases, cloud apps — into Fabric using Data Factory pipelines. Data flows automatically, on schedule, without manual exports.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Lakehouse & Warehouse Build</div>
        <div class="deliver-desc">We design and build your OneLake foundation — Lakehouse or Warehouse depending on your workloads — so all data lives in one place, accessible to every tool in your stack.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Power BI Dashboards & Reports</div>
        <div class="deliver-desc">We build the dashboards your leadership and operations teams will actually open every morning. Designed for clarity, not complexity — with Direct Lake speed on large datasets.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">AI & Copilot Integration</div>
        <div class="deliver-desc">Microsoft Fabric has Copilot built in. We configure it so your team can query data, generate reports, and get insights in plain English — no SQL required.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Governance & Ongoing Support</div>
        <div class="deliver-desc">Data governance through Microsoft Purview, role-based access, data lineage tracking, and a dedicated Armely contact to keep your Fabric environment healthy as your business scales.</div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNEY -->
<section class="journey" id="journey">
  <div class="section-inner">
    <div class="section-eyebrow">The Armely Fabric Journey</div>
    <h2 class="section-title">From scattered spreadsheets to a single source of truth.</h2>
    <p class="section-body">We follow a structured implementation methodology refined across data projects for healthcare, education, and enterprise clients — so nothing gets missed and your data is right from day one.</p>
    <div class="steps-row">
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-title">Discovery & Data Audit</div>
        <div class="step-desc">We map your current data sources, tools, and reporting pain points. Free for new clients — no obligation to proceed.</div>
        <span class="step-tag">Free</span>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-title">Architecture & Licensing</div>
        <div class="step-desc">We design your Fabric architecture and source the right capacity licence at partner pricing for your workload needs.</div>
        <span class="step-tag">1–2 weeks</span>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-title">Build & Integrate</div>
        <div class="step-desc">Pipelines, Lakehouse, data models, and your first Power BI dashboards — built and tested against your real data.</div>
        <span class="step-tag">Weeks 3–6</span>
      </div>
      <div class="step">
        <div class="step-num">04</div>
        <div class="step-title">Handover & Training</div>
        <div class="step-desc">Your team learns to use, maintain, and extend the environment. We document everything so you're never dependent on us.</div>
        <span class="step-tag">Week 7–8</span>
      </div>
      <div class="step">
        <div class="step-num">05</div>
        <div class="step-title">Managed Support</div>
        <div class="step-desc">Ongoing optimisation, new dashboard requests, governance reviews, and a single contact who knows your environment.</div>
        <span class="step-tag">Ongoing</span>
      </div>
    </div>
  </div>
</section>

<!-- USE CASES -->
<section class="usecases">
  <div class="section-inner">
    <div class="section-eyebrow">What Businesses Use It For</div>
    <h2 class="section-title">Real answers to real business questions.</h2>
    <p class="section-body">Fabric isn't just a data tool — it's the foundation for every business decision your team makes. Here's what Armely-built Fabric environments deliver in practice.</p>
    <div class="uc-grid">
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Executive Dashboards</div>
        <div class="uc-desc">Live KPI dashboards that pull from every system — finance, ops, sales — into a single view. Leadership sees the truth, not last week's export.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Financial Reporting</div>
        <div class="uc-desc">Automate month-end reporting, budget vs actual analysis, and cost centre breakdowns. Finance teams reclaim hours previously spent wrangling spreadsheets.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Operational Intelligence</div>
        <div class="uc-desc">Track productivity, resource utilisation, and service delivery in real time. Spot problems before they become complaints — not after.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">System Consolidation</div>
        <div class="uc-desc">Connect ERP, CRM, HR, and third-party data into one governed data layer. Stop manually reconciling reports from different systems that disagree.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Predictive Analytics</div>
        <div class="uc-desc">Use Fabric's Data Science workload to build models that forecast demand, flag churn risk, or surface patterns hidden in your historical data.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Natural Language Queries</div>
        <div class="uc-desc">With Copilot embedded in Fabric, anyone on your team can ask questions in plain English and get answers from your live data — without touching a report.</div>
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
        <h2 class="section-title">We've done this before — for organisations like yours.</h2>
        <p class="section-body">Armely has delivered data and analytics projects for healthcare providers, universities, and enterprise clients. Microsoft Fabric brings those capabilities to every business — and we know how to make it land.</p>
        <ul class="why-list">
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Proven Implementation Track Record</div>
              <div class="why-item-desc">We've implemented Microsoft data solutions for Plano ISD, Swope Health Systems, and the University of Nebraska Medical Center — complex environments with real data governance requirements.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Security & Governance Built In</div>
              <div class="why-item-desc">We configure Microsoft Purview, Entra ID access controls, and data lineage from day one — so your Fabric environment is audit-ready, not bolted on later.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Right-Sized Licensing</div>
              <div class="why-item-desc">As a Microsoft-authorised CSP partner, we access Fabric capacity pricing and bundle options not available to direct buyers — and we help you start at the right scale, not the biggest.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">You Own the Environment</div>
              <div class="why-item-desc">We document everything, train your team, and build so you can manage it yourselves. Our goal is capability transfer — not dependency.</div>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <div class="partner-block">
          <div class="partner-block-top">
            <div class="partner-label">Microsoft Authorised Partner</div>
            <p class="partner-text">Armely's Microsoft partnership gives us access to Fabric licensing, technical resources, and implementation support that independent buyers can't reach. That means better value for you and a faster, cleaner build backed by the full Microsoft ecosystem.</p>
          </div>
          <div class="partner-stats">
            <div class="p-stat">
              <div class="p-stat-num">31<span>K+</span></div>
              <div class="p-stat-label">paying organisations on Microsoft Fabric as of 2026</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">7</div>
              <div class="p-stat-label">unified workloads — one platform, one licence</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">300<span>+</span></div>
              <div class="p-stat-label">data source connectors in Data Factory</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">1</div>
              <div class="p-stat-label">copy of your data — OneLake eliminates duplication</div>
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
      <h2 class="section-title">Let's talk about your data.</h2>
      <p class="section-body">Book a free 30-minute discovery call. We'll listen to your current data situation, show you what Fabric could look like for your business, and give you a clear implementation proposal — no obligation.</p>
      <div style="margin-top: 28px; display: flex; flex-direction: column; gap: 12px;">
        <div class="trust-item" style="--trust-dot-color: var(--blue);">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Free discovery call — no commitment required</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Custom proposal with partner pricing included</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot" style="background: var(--blue);"></span>
          <span class="trust-text" style="color: var(--text-body);">Response within one business day</span>
        </div>
      </div>
    </div>
    <div class="cta-form">
      <div class="form-title">Book Your Free Discovery Call</div>
      <div class="form-sub">Tell us a little about your data situation.</div>
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
        <label>Primary Data Challenge</label>
        <select>
          <option value="">Select...</option>
          <option>Reporting takes too long / is manual</option>
          <option>Data is scattered across too many systems</option>
          <option>No single source of truth</option>
          <option>Need real-time dashboards</option>
          <option>Migrating from legacy tools (Synapse, ADF)</option>
          <option>Want to add AI / Copilot to our data</option>
        </select>
      </div>
      <button class="form-submit">Request Free Discovery Call →</button>
      <div class="form-note">No spam. No sales pressure. Just a useful conversation.</div>
    </div>
  </div>
</section>

<!-- FOOTER -->
</div>

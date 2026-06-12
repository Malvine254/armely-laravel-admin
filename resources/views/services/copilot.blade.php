<style>

.armely-copilot-page *, .armely-copilot-page *::before, .armely-copilot-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-copilot-page {
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

.armely-copilot-page { scroll-behavior: smooth; }

.armely-copilot-page {
    font-family: 'Poppins', sans-serif;
    background: var(--navy);
    color: var(--text-body);
    line-height: 1.6;
  }

  /* ── NAV ── */
.armely-copilot-page nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    display: flex; justify-content: space-between; align-items: center;
    padding: 18px 56px;
    background: rgba(26,46,82,0.96);
    backdrop-filter: blur(14px);
    border-bottom: 1px solid rgba(255,255,255,0.08);
  }
.armely-copilot-page .logo {
    display: flex; align-items: center; gap: 10px;
  }
.armely-copilot-page .logo-mark {
    width: 36px; height: 36px;
    background: var(--blue);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 1rem; color: #fff;
    letter-spacing: -0.03em;
  }
.armely-copilot-page .logo-text {
    font-size: 1.25rem; font-weight: 700;
    color: var(--white); letter-spacing: -0.01em;
  }
.armely-copilot-page .nav-links {
    display: flex; gap: 32px; align-items: center;
    list-style: none;
  }
.armely-copilot-page .nav-links a {
    color: var(--text-muted); text-decoration: none;
    font-size: 0.875rem; font-weight: 500;
    transition: color 0.2s;
  }
.armely-copilot-page .nav-links a:hover { color: var(--white); }
.armely-copilot-page .nav-cta {
    background: var(--blue);
    color: var(--white) !important;
    padding: 10px 22px;
    border-radius: 6px;
    font-size: 0.875rem; font-weight: 600 !important;
    transition: background 0.2s !important;
  }
.armely-copilot-page .nav-cta:hover { background: var(--blue-lt) !important; color: var(--white) !important; }

  /* ── HERO ── */
.armely-copilot-page .hero {
    min-height: 100vh;
    display: flex; flex-direction: column; justify-content: center;
    padding: 140px 56px 100px;
    position: relative; overflow: hidden;
    background: #1a2e52;
  }
.armely-copilot-page .hero h1 { color: #FFFFFF; }
.armely-copilot-page .hero h1 .hl { color: #FFFFFF; }
.armely-copilot-page .hero .hero-sub { color: rgba(255,255,255,0.82); }
.armely-copilot-page .hero .trust-text { color: rgba(255,255,255,0.65); }
.armely-copilot-page .hero .trust-text strong { color: #FFFFFF; }
.armely-copilot-page .hero .hero-trust { border-top-color: rgba(255,255,255,0.12); }
.armely-copilot-page .hero .trust-dot { background: rgba(255,255,255,0.5); }
.armely-copilot-page .hero-bg-glow {
    position: absolute; top: -180px; right: -100px;
    width: 720px; height: 720px;
    background: radial-gradient(circle, rgba(41,78,139,0.07) 0%, transparent 68%);
    pointer-events: none;
  }
.armely-copilot-page .hero-bg-glow2 { display: none; }

.armely-copilot-page .hero-eyebrow {
    display: inline-flex; align-items: center; gap: 10px;
    margin-bottom: 24px;
  }
.armely-copilot-page .eyebrow-badge {
    background: var(--blue-dim);
    border: 1px solid var(--blue-dim2);
    color: var(--blue);
    font-size: 0.72rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.12em;
    padding: 5px 14px; border-radius: 40px;
  }
.armely-copilot-page .eyebrow-partner {
    font-size: 0.78rem; color: var(--text-muted);
    font-weight: 400;
  }

.armely-copilot-page .hero h1 {
    font-size: clamp(2.6rem, 5.5vw, 4.8rem);
    font-weight: 800;
    line-height: 1.08;
    color: #FFFFFF;
    max-width: 780px;
    margin-bottom: 24px;
    letter-spacing: -0.03em;
  }
.armely-copilot-page .hero h1 .hl {
    color: #FFFFFF;
    opacity: 0.92;
  }

.armely-copilot-page .hero-sub {
    font-size: 1.05rem; font-weight: 300;
    color: var(--text-body);
    max-width: 540px;
    margin-bottom: 40px;
    line-height: 1.8;
  }

.armely-copilot-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-copilot-page .btn-primary {
    background: var(--blue);
    color: var(--white);
    border: none; border-radius: 7px;
    padding: 14px 32px;
    font-family: 'Poppins', sans-serif;
    font-size: 0.95rem; font-weight: 600;
    cursor: pointer; text-decoration: none;
    transition: background 0.2s, transform 0.15s;
    display: inline-block;
  }
.armely-copilot-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-copilot-page .btn-outline {
    background: transparent;
    color: var(--blue);
    border: 1px solid rgba(41,78,139,0.25);
    border-radius: 7px;
    padding: 14px 32px;
    font-family: 'Poppins', sans-serif;
    font-size: 0.95rem; font-weight: 500;
    cursor: pointer; text-decoration: none;
    transition: border-color 0.2s, background 0.2s;
    display: inline-block;
  }
.armely-copilot-page .btn-outline:hover { border-color: rgba(41,78,139,0.5); background: rgba(41,78,139,0.04); }

.armely-copilot-page .hero-trust {
    display: flex; gap: 40px; flex-wrap: wrap;
    padding-top: 40px;
    border-top: 1px solid var(--border);
  }
.armely-copilot-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-copilot-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--blue); flex-shrink: 0; }
.armely-copilot-page .trust-text { font-size: 0.82rem; color: var(--text-muted); font-weight: 400; }
.armely-copilot-page .trust-text strong { color: var(--white); font-weight: 600; }

  /* ── SECTION SHELL ── */
.armely-copilot-page section { padding: 96px 56px; }
.armely-copilot-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-copilot-page .section-eyebrow {
    font-size: 0.72rem; text-transform: uppercase;
    letter-spacing: 0.14em; color: var(--blue);
    margin-bottom: 14px; font-weight: 600;
  }
.armely-copilot-page .section-title {
    font-size: clamp(1.7rem, 3.2vw, 2.6rem);
    font-weight: 800; color: #1A2540;
    line-height: 1.12; letter-spacing: -0.025em;
    margin-bottom: 18px;
    max-width: 620px;
  }
.armely-copilot-page .section-body {
    font-size: 0.975rem; font-weight: 300;
    max-width: 540px; line-height: 1.8;
    color: var(--text-body); margin-bottom: 48px;
  }

  /* ── WHAT IS COPILOT ── */
.armely-copilot-page .intro { background: var(--navy-mid); }
.armely-copilot-page .intro-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 72px; align-items: center;
  }
.armely-copilot-page .app-pills { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 28px; }
.armely-copilot-page .pill {
    background: var(--blue-dim);
    border: 1px solid var(--blue-dim2);
    color: var(--blue);
    padding: 6px 16px; border-radius: 40px;
    font-size: 0.8rem; font-weight: 500;
  }

.armely-copilot-page .demo-card {
    background: var(--navy-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
  }
.armely-copilot-page .demo-header {
    padding: 16px 22px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 10px;
  }
.armely-copilot-page .demo-dots { display: flex; gap: 6px; }
.armely-copilot-page .demo-dots span {
    width: 10px; height: 10px; border-radius: 50%;
    background: rgba(41,78,139,0.15);
  }
.armely-copilot-page .demo-app-name {
    font-size: 0.78rem; font-weight: 600; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: 0.1em;
  }
.armely-copilot-page .demo-body { padding: 24px; }
.armely-copilot-page .chat-bubble {
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 12px;
    font-size: 0.85rem; line-height: 1.65;
  }
.armely-copilot-page .chat-bubble.user {
    background: var(--blue-dim);
    border-left: 3px solid var(--blue);
    color: var(--blue);
  }
.armely-copilot-page .chat-bubble.copilot {
    background: rgba(41,78,139,0.04);
    border-left: 3px solid rgba(41,78,139,0.2);
    color: var(--text-body);
  }
.armely-copilot-page .bubble-label {
    font-size: 0.67rem; text-transform: uppercase;
    letter-spacing: 0.1em; font-weight: 700;
    margin-bottom: 6px;
  }
.armely-copilot-page .bubble-label.u { color: var(--blue); }
.armely-copilot-page .bubble-label.c { color: var(--text-muted); }

  /* ── WHAT ARMELY DELIVERS (implementor section) ── */
.armely-copilot-page .delivers { background: var(--navy); }
.armely-copilot-page .delivers-grid {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 20px; margin-top: 48px;
  }
.armely-copilot-page .deliver-card {
    background: var(--navy-card);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 32px 26px;
    transition: border-color 0.2s, transform 0.2s;
  }
.armely-copilot-page .deliver-card:hover { border-color: var(--blue-dim2); transform: translateY(-3px); }
.armely-copilot-page .deliver-icon {
    width: 48px; height: 48px;
    background: var(--blue-dim);
    border: 1px solid var(--blue-dim2);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; margin-bottom: 20px;
  }
.armely-copilot-page .deliver-title {
    font-size: 1rem; font-weight: 700;
    color: #1A2540; margin-bottom: 10px;
  }
.armely-copilot-page .deliver-desc {
    font-size: 0.875rem; line-height: 1.7;
    color: var(--text-body);
  }

  /* ── JOURNEY STEPS ── */
.armely-copilot-page .journey { background: var(--navy-mid); }
.armely-copilot-page .steps-row {
    display: grid; grid-template-columns: repeat(5, 1fr);
    gap: 0; margin-top: 56px;
    border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
  }
.armely-copilot-page .step {
    padding: 32px 22px;
    border-right: 1px solid var(--border);
    position: relative;
  }
.armely-copilot-page .step:last-child { border-right: none; }
.armely-copilot-page .step-num {
    font-size: 2.4rem; font-weight: 800;
    color: rgba(41,78,139,0.18); line-height: 1;
    margin-bottom: 14px;
  }
.armely-copilot-page .step-title {
    font-size: 0.95rem; font-weight: 700;
    color: #1A2540; margin-bottom: 10px;
  }
.armely-copilot-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-copilot-page .step-tag {
    display: inline-block; margin-top: 14px;
    background: var(--blue-dim);
    color: var(--blue);
    font-size: 0.7rem; padding: 3px 10px;
    border-radius: 4px; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.06em;
  }

  /* ── USE CASES ── */
.armely-copilot-page .usecases { background: var(--navy); }
.armely-copilot-page .uc-grid {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 18px; margin-top: 48px;
  }
.armely-copilot-page .uc-card {
    background: var(--navy-card);
    border: 1px solid var(--border);
    border-radius: 12px; padding: 28px 24px;
    transition: border-color 0.2s;
  }
.armely-copilot-page .uc-card:hover { border-color: rgba(41,78,139,0.35); }
.armely-copilot-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-copilot-page .uc-title {
    font-size: 0.95rem; font-weight: 700;
    color: #1A2540; margin-bottom: 8px;
  }
.armely-copilot-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* ── WHY ARMELY ── */
.armely-copilot-page .why { background: var(--navy-mid); }
.armely-copilot-page .why-two-col {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 72px; align-items: start;
  }
.armely-copilot-page .why-list { list-style: none; margin-top: 36px; }
.armely-copilot-page .why-list li {
    display: flex; gap: 16px;
    padding: 20px 0;
    border-bottom: 1px solid var(--border);
  }
.armely-copilot-page .why-list li:last-child { border-bottom: none; }
.armely-copilot-page .why-icon {
    width: 42px; height: 42px; flex-shrink: 0;
    background: var(--blue-dim);
    border: 1px solid rgba(41,78,139,0.2);
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
  }
.armely-copilot-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-copilot-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }

.armely-copilot-page .partner-block {
    margin-top: 0;
    background: var(--navy-card);
    border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
  }
.armely-copilot-page .partner-block-top {
    padding: 28px;
    border-bottom: 1px solid var(--border);
  }
.armely-copilot-page .partner-label {
    font-size: 0.68rem; text-transform: uppercase;
    letter-spacing: 0.14em; color: var(--blue); font-weight: 700;
    margin-bottom: 10px;
  }
.armely-copilot-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-copilot-page .partner-stats {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 0;
  }
.armely-copilot-page .p-stat {
    padding: 24px 28px;
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
  }
.armely-copilot-page .p-stat:nth-child(2) { border-right: none; }
.armely-copilot-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-copilot-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-copilot-page .p-stat-num {
    font-size: 1.8rem; font-weight: 800;
    color: #1A2540; line-height: 1;
    margin-bottom: 4px;
  }
.armely-copilot-page .p-stat-num span { color: var(--blue); }
.armely-copilot-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* ── CTA ── */
.armely-copilot-page .cta-section {
    background: var(--navy-card);
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
  }
.armely-copilot-page .cta-inner {
    max-width: 1100px; margin: 0 auto;
    padding: 96px 56px;
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 72px; align-items: center;
  }
.armely-copilot-page .cta-form {
    background: #FFFFFF;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 36px 32px;
  }
.armely-copilot-page .form-title {
    font-size: 1.1rem; font-weight: 700;
    color: #1A2540; margin-bottom: 6px;
  }
.armely-copilot-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-copilot-page .form-row { margin-bottom: 14px; }
.armely-copilot-page .form-row label {
    display: block; font-size: 0.75rem; font-weight: 600;
    color: var(--text-muted); text-transform: uppercase;
    letter-spacing: 0.08em; margin-bottom: 6px;
  }
.armely-copilot-page .form-row input, .armely-copilot-page .form-row select {
    width: 100%; background: #FFFFFF;
    border: 1px solid rgba(41,78,139,0.15);
    border-radius: 7px; padding: 11px 14px;
    font-family: 'Poppins', sans-serif;
    font-size: 0.875rem; color: #1A2540;
    outline: none;
    transition: border-color 0.2s;
  }
.armely-copilot-page .form-row input:focus, .armely-copilot-page .form-row select:focus {
    border-color: rgba(41,78,139,0.4);
  }
.armely-copilot-page .form-row select option { background: #FFFFFF; color: #1A2540; }
.armely-copilot-page .form-submit {
    width: 100%; background: var(--blue);
    color: var(--white); border: none; border-radius: 7px;
    padding: 14px; margin-top: 8px;
    font-family: 'Poppins', sans-serif;
    font-size: 0.95rem; font-weight: 600;
    cursor: pointer; transition: background 0.2s;
  }
.armely-copilot-page .form-submit:hover { background: var(--blue-lt); }
.armely-copilot-page .form-note {
    text-align: center; margin-top: 12px;
    font-size: 0.75rem; color: var(--text-muted);
  }

  /* ── FOOTER ── */
.armely-copilot-page footer {
    background: #1a2e52;
    border-top: 1px solid rgba(255,255,255,0.08);
    padding: 36px 56px;
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 16px;
  }
.armely-copilot-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-copilot-page .footer-lm {
    width: 30px; height: 30px; background: var(--blue);
    border-radius: 6px; display: flex; align-items: center;
    justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff;
  }
.armely-copilot-page .footer-lt { font-size: 1rem; font-weight: 700; color: #FFFFFF; }
.armely-copilot-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.45); }
.armely-copilot-page .footer-badges {
    display: flex; gap: 16px; align-items: center;
    flex-wrap: wrap;
  }
.armely-copilot-page .badge-chip {
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 6px; padding: 5px 12px;
    font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500;
  }

  /* ── RESPONSIVE ── */
  @media (max-width: 900px) {
.armely-copilot-page nav { padding: 16px 24px; }
.armely-copilot-page .nav-links { display: none; }
.armely-copilot-page section { padding: 72px 24px; }
.armely-copilot-page .hero { padding: 110px 24px 72px; }
.armely-copilot-page .intro-grid, .armely-copilot-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-copilot-page .delivers-grid, .armely-copilot-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-copilot-page .steps-row { grid-template-columns: 1fr; }
.armely-copilot-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-copilot-page .step:last-child { border-bottom: none; }
.armely-copilot-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-copilot-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-copilot-page .delivers-grid, .armely-copilot-page .uc-grid { grid-template-columns: 1fr; }
.armely-copilot-page .partner-stats { grid-template-columns: 1fr; }
.armely-copilot-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) {
.armely-copilot-page * { transition: none !important; animation: none !important; }
  }

/* Armely service-page polish */
.armely-copilot-page {
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
.armely-copilot-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-copilot-page .hero::after {
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
.armely-copilot-page .section-title,
.armely-copilot-page .deliver-title,
.armely-copilot-page .uc-title,
.armely-copilot-page .step-title,
.armely-copilot-page .why-item-title,
.armely-copilot-page .form-title {
  color: #162b49;
}
.armely-copilot-page .deliver-card,
.armely-copilot-page .uc-card,
.armely-copilot-page .testi-card,
.armely-copilot-page .platform-card,
.armely-copilot-page .partner-block,
.armely-copilot-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-copilot-page .deliver-card:hover,
.armely-copilot-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-copilot-page .btn-primary,
.armely-copilot-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-copilot-page .btn-primary:hover,
.armely-copilot-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-copilot-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-copilot-page nav,
.armely-copilot-page footer {
  display: none;
}

/* Shared modern service page refresh */
.armely-copilot-page .hero {
  min-height: 100vh;
  padding: 150px 56px 96px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: #173b67;
  border-radius: 0;
}
.armely-copilot-page .hero::after,
.armely-copilot-page .hero-bg-glow,
.armely-copilot-page .hero-trust {
  display: none;
}
.armely-copilot-page .hero h1 {
  max-width: 820px;
  margin-bottom: 22px;
}
.armely-copilot-page .hero-sub {
  max-width: 700px;
  margin-bottom: 34px;
}
.armely-copilot-page .hero-actions {
  margin-bottom: 0;
}
.armely-copilot-page .hero .btn-primary,
.armely-copilot-page .hero .btn-outline {
  border-radius: 0;
}
.armely-copilot-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.74);
}
.armely-copilot-page .eyebrow-partner {
  display: none;
}
.armely-copilot-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-copilot-page .cta-inner > div > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-copilot-page section:not(.hero) > .section-inner > .section-title,
.armely-copilot-page .cta-inner > div > .section-title {
  max-width: 900px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-copilot-page section:not(.hero) > .section-inner > .section-body,
.armely-copilot-page .cta-inner > div > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-copilot-page .intro-grid,
.armely-copilot-page .symptoms-grid,
.armely-copilot-page .when-grid,
.armely-copilot-page .why-two-col,
.armely-copilot-page .cta-inner {
  align-items: stretch;
}
.armely-copilot-page .intro-grid,
.armely-copilot-page .symptoms-grid,
.armely-copilot-page .when-grid,
.armely-copilot-page .delivers-grid,
.armely-copilot-page .tier-grid,
.armely-copilot-page .covers-grid,
.armely-copilot-page .steps-row,
.armely-copilot-page .uc-grid,
.armely-copilot-page .testi-grid,
.armely-copilot-page .why-two-col,
.armely-copilot-page .pathway-grid {
  margin-top: 56px;
}
.armely-copilot-page .deliver-icon,
.armely-copilot-page .uc-icon,
.armely-copilot-page .why-icon,
.armely-copilot-page .symptom-icon,
.armely-copilot-page .what-card-icon,
.armely-copilot-page .cov-item-icon,
.armely-copilot-page .cover-icon,
.armely-copilot-page .product-card-icon,
.armely-copilot-page .cap-icon,
.armely-copilot-page .workload-pill-icon,
.armely-copilot-page .decision-icon,
.armely-copilot-page .sign-icon,
.armely-copilot-page .pathway-icon,
.armely-copilot-page .onelake-callout-icon,
.armely-copilot-page .vs-callout-icon {
  color: var(--blue);
  font-size: 1.1rem;
  line-height: 1;
}
.armely-copilot-page .deliver-icon,
.armely-copilot-page .uc-icon,
.armely-copilot-page .why-icon {
  width: 48px;
  height: 48px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-copilot-page .deliver-card,
.armely-copilot-page .uc-card,
.armely-copilot-page .testi-card,
.armely-copilot-page .tier-card,
.armely-copilot-page .cover-card,
.armely-copilot-page .pathway-card,
.armely-copilot-page .partner-block,
.armely-copilot-page .cta-form {
  background: linear-gradient(180deg, #ffffff 0%, #f9fbfe 100%);
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
@media (max-width: 900px) {
  .armely-copilot-page .hero { padding: 118px 24px 76px; }
  .armely-copilot-page section:not(.hero) > .section-inner > .section-title,
  .armely-copilot-page .cta-inner > div > .section-title { max-width: 100%; }
}
</style>
<div class="armely-copilot-page">
<!-- NAV -->


<!-- HERO -->
<section class="hero">
  <div class="hero-bg-glow"></div>
  <div class="hero-bg-glow2"></div>
  <div class="hero-eyebrow">
    <span class="eyebrow-badge">Microsoft 365 Copilot Business</span>
    <span class="eyebrow-partner">Delivered by a certified Microsoft partner</span>
  </div>
  <h1>AI that works<br>the way <span class="hl">your business</span><br>actually works.</h1>
  <p class="hero-sub">Armely licences, deploys, and embeds Microsoft 365 Copilot into your team's daily workflows — so adoption is real, not just access.</p>
  <div class="hero-actions">
    <a href="#contact" class="btn-primary">Book a Free Assessment</a>
    <a href="#what-we-deliver" class="btn-outline">See What We Do</a>
  </div>
  <div class="hero-trust">
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>Full Microsoft 365</strong> Copilot feature set</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">Works with <strong>any existing</strong> M365 Business plan</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text"><strong>No seat minimums</strong> — start with one team</span>
    </div>
    <div class="trust-item">
      <span class="trust-dot"></span>
      <span class="trust-text">Exclusive <strong>partner pricing</strong> available</span>
    </div>
  </div>
</section>

<!-- WHAT IS COPILOT -->
<section class="intro">
  <div class="section-inner">
    <div class="intro-grid">
      <div>
        <div class="section-eyebrow">What is Microsoft 365 Copilot?</div>
        <h2 class="section-title">AI built into the tools your team already uses every day.</h2>
        <p class="section-body">Microsoft 365 Copilot Business is an AI assistant woven directly into Word, Excel, PowerPoint, Outlook, and Teams. It drafts, summarises, analyses, and responds — freeing your people from admin so they can focus on the work that matters.</p>
        <div class="app-pills">
          <span class="pill">Word</span>
          <span class="pill">Excel</span>
          <span class="pill">PowerPoint</span>
          <span class="pill">Outlook</span>
          <span class="pill">Teams</span>
          <span class="pill">M365 Chat</span>
        </div>
      </div>
      <div>
        <div class="demo-card">
          <div class="demo-header">
            <div class="demo-dots"><span></span><span></span><span></span></div>
            <span class="demo-app-name">Copilot in Outlook</span>
          </div>
          <div class="demo-body">
            <div class="chat-bubble user">
              <div class="bubble-label u">You</div>
              Summarise last week's project emails and flag anything that needs a reply today.
            </div>
            <div class="chat-bubble copilot">
              <div class="bubble-label c">Copilot</div>
              Found 14 relevant threads. Two need replies: a contract review from Sarah due Friday, and a vendor quote requiring sign-off. The rest are informational — here's a 3-line summary of each.
            </div>
            <div class="chat-bubble user">
              <div class="bubble-label u">You</div>
              Draft a reply to Sarah — professional but concise.
            </div>
            <div class="chat-bubble copilot">
              <div class="bubble-label c">Copilot</div>
              Done. Draft is in your compose window. Review and send when ready.
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
    <h2 class="section-title">We don't just sell licences. We make Copilot work for your business.</h2>
    <p class="section-body">As a certified Microsoft partner, Armely handles the full picture — from securing the best licensing deal to building the habits that make AI stick.</p>
    <div class="delivers-grid">
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Readiness Assessment</div>
        <div class="deliver-desc">Before a single licence is activated, we audit your Microsoft 365 environment, data governance, permissions, and security posture. Copilot lands on a clean, safe foundation — not into a messy tenant.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Best-Value Licensing</div>
        <div class="deliver-desc">Our Microsoft partnership gives us access to SMB bundle pricing and promotional offers that aren't available through direct purchase. We find the right plan for your team size and budget — often at a significant discount.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Hands-On Implementation</div>
        <div class="deliver-desc">We don't hand you a login and a help link. Our engineers configure Copilot for your specific workflows, integrate it with your existing systems, and run role-by-role deployment so every team hits the ground running.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Adoption Training</div>
        <div class="deliver-desc">People use tools they understand. We run targeted training sessions for each department — showing your team exactly how Copilot accelerates their specific work, not just a generic demo.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Adoption Tracking</div>
        <div class="deliver-desc">Usage reports, quarterly business reviews, and proactive check-ins mean we catch low adoption early and fix it before licences go to waste. You always know your Copilot ROI.</div>
      </div>
      <div class="deliver-card">
        <div class="deliver-icon"><i class="fa-solid fa-layer-group" aria-hidden="true"></i></div>
        <div class="deliver-title">Ongoing Managed Support</div>
        <div class="deliver-desc">One dedicated account manager who knows your environment. Not a ticket queue. As your business grows and Microsoft updates Copilot, we keep you ahead of the curve.</div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNEY -->
<section class="journey" id="journey">
  <div class="section-inner">
    <div class="section-eyebrow">The Armely QuickStart Journey</div>
    <h2 class="section-title">From first conversation to full productivity — fast.</h2>
    <p class="section-body">We follow the Microsoft 365 Copilot QuickStart framework, refined across hundreds of SMB deployments, to get your team seeing real value in weeks not months.</p>
    <div class="steps-row">
      <div class="step">
        <div class="step-num">01</div>
        <div class="step-title">Discovery & Assessment</div>
        <div class="step-desc">Free environment audit covering your current M365 setup, data hygiene, and security posture.</div>
        <span class="step-tag">Free</span>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <div class="step-title">Licensing & Planning</div>
        <div class="step-desc">We source the best bundle and pricing, then build a deployment plan tailored to your teams and workflows.</div>
        <span class="step-tag">1–2 days</span>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <div class="step-title">Pilot Rollout</div>
        <div class="step-desc">Start with a target team. Real workflows, real feedback, measurable results before going organisation-wide.</div>
        <span class="step-tag">Week 1–2</span>
      </div>
      <div class="step">
        <div class="step-num">04</div>
        <div class="step-title">Full Deployment</div>
        <div class="step-desc">Scale across the organisation with role-specific training and Armely managing every step of rollout.</div>
        <span class="step-tag">Week 3–4</span>
      </div>
      <div class="step">
        <div class="step-num">05</div>
        <div class="step-title">Continuous Success</div>
        <div class="step-desc">Monthly usage reviews, proactive support, and ongoing optimisation as your team and Microsoft's AI evolve.</div>
        <span class="step-tag">Ongoing</span>
      </div>
    </div>
  </div>
</section>

<!-- USE CASES -->
<section class="usecases">
  <div class="section-inner">
    <div class="section-eyebrow">What Your Team Will Actually Do With It</div>
    <h2 class="section-title">Real work, done faster across every role.</h2>
    <p class="section-body">Copilot Business delivers measurable time savings across every department — from operations and finance to sales and leadership.</p>
    <div class="uc-grid">
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Email & Communications</div>
        <div class="uc-desc">Copilot in Outlook summarises long threads, drafts replies in your tone, and flags what genuinely needs attention. Inbox zero is no longer a myth.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Data & Reporting</div>
        <div class="uc-desc">Ask Copilot in Excel to analyse a spreadsheet, spot anomalies, or build a summary chart — in plain English. No formula expertise required.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Meetings & Follow-ups</div>
        <div class="uc-desc">Copilot in Teams transcribes, summarises, and extracts action items from every meeting. Stop taking notes and actually contribute.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Documents & Proposals</div>
        <div class="uc-desc">Turn bullet points into polished proposals in Word. Summarise a 40-page report into a one-page brief. First drafts in seconds, not hours.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Presentations</div>
        <div class="uc-desc">Copilot in PowerPoint builds structured slide decks from a document or prompt — branded and ready for your edits, not built from scratch under pressure.</div>
      </div>
      <div class="uc-card">
        <span class="uc-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
        <div class="uc-title">Knowledge & Search</div>
        <div class="uc-desc">Microsoft 365 Chat searches across all your files, emails, and chats to surface what you need instantly. No more digging through folders or asking colleagues.</div>
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
        <h2 class="section-title">The difference between access and adoption.</h2>
        <p class="section-body">Most businesses activate Copilot and wonder why nobody's using it six months later. We're built specifically to prevent that — combining licensing expertise, technical implementation, and hands-on change management.</p>
        <ul class="why-list">
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Certified Copilot Implementors</div>
              <div class="why-item-desc">Our engineers are Microsoft-certified Copilot implementors — trained in Copilot Practice Builder methodology, CloudLabs deployment, and hands-on change management across SMB environments.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Security-First by Default</div>
              <div class="why-item-desc">AI introduces new data exposure risks. We assess and harden your environment before go-live so Copilot runs securely inside your existing Microsoft 365 tenant — your data never leaves.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Access to Partner-Only Pricing</div>
              <div class="why-item-desc">As a Microsoft-authorised CSP partner, we access SMB bundle promotions and volume pricing that aren't available to direct buyers — and we pass those savings on to you.</div>
            </div>
          </li>
          <li>
            <div class="why-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div>
              <div class="why-item-title">Proven SMB Track Record</div>
              <div class="why-item-desc">We've implemented Microsoft solutions for organisations including Plano ISD, Swope Health Systems, and UNMC — bringing enterprise-grade delivery to businesses of every size.</div>
            </div>
          </li>
        </ul>
      </div>
      <div>
        <div class="partner-block">
          <div class="partner-block-top">
            <div class="partner-label">Microsoft Authorised Partner</div>
            <p class="partner-text">Armely's Microsoft partnership gives us access to licensing, technical resources, and bundle pricing that independent buyers can't reach. That means better value for you, faster deployment, and support backed by the full Microsoft ecosystem.</p>
          </div>
          <div class="partner-stats">
            <div class="p-stat">
              <div class="p-stat-num">70<span>%</span></div>
              <div class="p-stat-label">of Fortune 500 already using Microsoft Copilot</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">300</div>
              <div class="p-stat-label">user maximum — purpose-built for SMBs</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">6</div>
              <div class="p-stat-label">Microsoft 365 apps with native Copilot integration</div>
            </div>
            <div class="p-stat">
              <div class="p-stat-num">87<span>%</span></div>
              <div class="p-stat-label">of organisations say AI gives a competitive edge</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA / CONTACT -->
<section class="cta-section" id="contact">
  <div class="cta-inner">
    <div>
      <div class="section-eyebrow">Get Started</div>
      <h2 class="section-title">Let's find the right Copilot plan for your business.</h2>
      <p class="section-body">Book a free 30-minute Copilot Readiness Assessment. We'll review your Microsoft 365 environment, understand your team's workflows, and come back with a clear recommendation and a pricing quote tailored to your situation — no obligation.</p>
      <div style="margin-top: 28px; display: flex; flex-direction: column; gap: 12px;">
        <div class="trust-item">
          <span class="trust-dot"></span>
          <span class="trust-text">Free assessment — no commitment required</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot"></span>
          <span class="trust-text">Custom quote with partner pricing included</span>
        </div>
        <div class="trust-item">
          <span class="trust-dot"></span>
          <span class="trust-text">Response within one business day</span>
        </div>
      </div>
    </div>
    <div class="cta-form">
      <div class="form-title">Book Your Free Assessment</div>
      <div class="form-sub">We'll be in touch within one business day.</div>
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
        <label>Team Size</label>
        <select>
          <option value="">Select...</option>
          <option>1–10 people</option>
          <option>11–50 people</option>
          <option>51–150 people</option>
          <option>151–300 people</option>
        </select>
      </div>
      <button class="form-submit">Request Free Assessment →</button>
      <div class="form-note">No spam. No sales pressure. Just a useful conversation.</div>
    </div>
  </div>
</section>

<!-- FOOTER -->
</div>

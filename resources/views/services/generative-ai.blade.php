@extends('layouts.public')

@section('title', 'Armely - Generative and Agentic AI')
@section('meta_description', 'Generative AI, agentic AI, enterprise copilots, intelligent automation, AI governance, and production AI implementation services from Armely.')
@section('meta_keywords', 'generative AI, agentic AI, AI agents, copilots, Azure AI Foundry, Armely')
@section('canonical_url', url('/service-details/generative-ai'))

@push('head')
<meta property="og:title" content="Armely - Generative and Agentic AI">
<meta property="og:description" content="Generative AI, agentic AI, enterprise copilots, intelligent automation, AI governance, and production AI implementation services from Armely.">
<meta property="og:url" content="{{ url('/service-details/generative-ai') }}">
<meta name="twitter:title" content="Armely - Generative and Agentic AI">
<meta name="twitter:description" content="Generative AI, agentic AI, enterprise copilots, intelligent automation, AI governance, and production AI implementation services from Armely.">
@endpush

@push('styles')
<style>
.armely-generative-ai-page *, .armely-generative-ai-page *::before, .armely-generative-ai-page *::after { box-sizing: border-box; margin: 0; padding: 0; }
.armely-generative-ai-page { --navy:      #FFFFFF;
    --navy-mid:  #F3F6FB;
    --navy-card: #EBF0F8;
    --blue:      #294e8b;
    --blue-lt:   #3d6ab5;
    --blue-dim:  rgba(41,78,139,0.08);
    --blue-dim2: rgba(41,78,139,0.16);
    --text-body: #3D4F6B;
    --text-muted:#6B7FA3;
    --border:    rgba(41,78,139,0.1); }
.armely-generative-ai-page { font-family: "Poppins", sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }
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
.armely-generative-ai-page .hero h1 { font-size: clamp(2.6rem, 5.5vw, 4.8rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-generative-ai-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-generative-ai-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.8; }
.armely-generative-ai-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-generative-ai-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: "Poppins", sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-generative-ai-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-generative-ai-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: "Poppins", sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
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
.armely-generative-ai-page .vibe-left {  }
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
.armely-generative-ai-page .vibe-right {  }
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
.armely-generative-ai-page .form-row input, .armely-generative-ai-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: "Poppins", sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-generative-ai-page .form-row input:focus, .armely-generative-ai-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-generative-ai-page .form-row select option { background: #fff; color: #1A2540; }
.armely-generative-ai-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: "Poppins", sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
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
@media (prefers-reduced-motion: reduce) {
.armely-generative-ai-page * { transition: none !important; animation: none !important; }
}

</style>
@endpush

@section('content')
<div class="armely-generative-ai-page">
NAV 

 HERO 
<section class="hero">
<div class="hero-bg-glow"></div>
<div class="hero-eyebrow">
<span class="eyebrow-badge">Generative and Agentic AI</span>
<span class="eyebrow-partner">Delivered by a certified Microsoft partner</span>
</div>
<h1>AI that takes action,<br/>not just <span class="hl">answers questions.</span></h1>
<p class="hero-sub">Armely designs and deploys generative AI and autonomous agent solutions on the Microsoft platform, grounded in your business data, governed to your compliance requirements, and built to run in production, not just a demo.</p>
<div class="hero-actions">
<a class="btn-primary" href="#contact">Book a Free AI Assessment</a>
<a class="btn-outline" href="#what-we-deliver">See What We Build</a>
</div>
<div class="hero-trust">
<div class="trust-item">
<span class="trust-dot"></span>
<span class="trust-text">Built on <strong>Microsoft Copilot Studio and Azure AI Foundry</strong></span>
</div>
<div class="trust-item">
<span class="trust-dot"></span>
<span class="trust-text"><strong>Grounded in your data</strong>, not public web content</span>
</div>
<div class="trust-item">
<span class="trust-dot"></span>
<span class="trust-text"><strong>Governed and auditable</strong> from day one</span>
</div>
<div class="trust-item">
<span class="trust-dot"></span>
<span class="trust-text">Integrates with <strong>Microsoft 365, Dynamics 365, and Power Platform</strong></span>
</div>
</div>
</section>
 AI SPECTRUM 
<section class="spectrum">
<div class="section-inner">
<div class="spectrum-grid">
<div>
<div class="section-eyebrow">Understanding the Landscape</div>
<h2 class="section-title">From assistants to autonomous agents. Where does your business need AI?</h2>
<p class="section-body">AI capability exists on a spectrum. Most organizations start at the assistive end and move toward automation as confidence and governance mature. Armely helps you identify where to start and how to progress deliberately rather than reactively.</p>
<div class="spectrum-row">
<div class="spectrum-level">
<span class="spectrum-num">01</span>
<div>
<div class="spectrum-content-title">Assistive AI</div>
<div class="spectrum-content-desc">AI that helps individuals work faster. Microsoft 365 Copilot drafting emails, summarizing meetings, and generating first drafts. Triggered by a human, reviewed before use.</div>
</div>
</div>
<div class="spectrum-level highlight">
<span class="spectrum-num">02</span>
<div>
<div class="spectrum-content-title">Generative AI Applications</div>
<div class="spectrum-content-desc">Custom applications that generate content, answers, or analysis from your business data. Internal knowledge assistants, document summarization tools, and customer-facing chatbots grounded in your own systems.</div>
</div>
</div>
<div class="spectrum-level highlight">
<span class="spectrum-num">03</span>
<div>
<div class="spectrum-content-title">Agentic AI Workflows</div>
<div class="spectrum-content-desc">AI agents that pursue a goal autonomously across multiple steps, tools, and systems without a human in the loop for each action. Case triage, order processing, onboarding workflows, and monitoring tasks executed end to end.</div>
</div>
</div>
<div class="spectrum-level">
<span class="spectrum-num">04</span>
<div>
<div class="spectrum-content-title">Multi-Agent Orchestration</div>
<div class="spectrum-content-desc">Networks of specialized agents that coordinate to complete complex business processes. One agent researches, one drafts, one reviews, one routes, each within defined guardrails and audit trails.</div>
</div>
</div>
</div>
</div>
<div>
<div class="platform-card">
<div class="platform-header">
<div class="platform-dots"><span></span><span></span><span></span></div>
<span class="platform-header-title">Microsoft AI Stack</span>
</div>
<div class="platform-body">
<div class="plat-band band-tools">
<div class="plat-band-label">Build Tools</div>
<div class="plat-chips">
<span class="plat-chip">Copilot Studio</span>
<span class="plat-chip">Azure AI Foundry</span>
<span class="plat-chip">GitHub Copilot</span>
<span class="plat-chip">Copilot Studio Agent Builder</span>
</div>
</div>
<div class="band-arrow">↕</div>
<div class="plat-band band-data">
<div class="plat-band-label">AI Models and Data</div>
<div class="plat-chips">
<span class="plat-chip">Azure OpenAI (GPT)</span>
<span class="plat-chip">Microsoft Phi</span>
<span class="plat-chip">Azure AI Search</span>
<span class="plat-chip">Dataverse</span>
<span class="plat-chip">SharePoint</span>
<span class="plat-chip">SQL Server</span>
</div>
</div>
<div class="band-arrow">↕</div>
<div class="plat-band band-tools" style="background: rgba(41,78,139,0.05);">
<div class="plat-band-label" style="color: var(--blue);">Integration and Actions</div>
<div class="plat-chips">
<span class="plat-chip">Power Automate</span>
<span class="plat-chip">Dynamics 365</span>
<span class="plat-chip">Microsoft 365</span>
<span class="plat-chip">REST APIs</span>
<span class="plat-chip">MCP Connectors</span>
</div>
</div>
<div class="band-arrow">↕</div>
<div class="plat-band band-gov">
<div class="plat-band-label">Governance and Safety</div>
<div class="plat-chips">
<span class="plat-chip">Microsoft Purview</span>
<span class="plat-chip">Entra ID</span>
<span class="plat-chip">DLP Policies</span>
<span class="plat-chip">Audit Logs</span>
<span class="plat-chip">Responsible AI Controls</span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
 VIBE CODING 
<section class="vibe-section" id="vibe-coding">
<div class="section-inner">
<div class="section-eyebrow">A Word on Vibe Coding</div>
<h2 class="section-title">The prototype is not the product.</h2>
<p class="section-body">Vibe coding, building applications by describing what you want to an AI and accepting whatever it generates, is a genuine productivity shift. It can produce a working prototype in hours. The problem is that a prototype built without security review, governed data access, or a maintainable architecture is not ready for production, and most organizations discover this the hard way.</p>
<div class="vibe-two-col">
<div class="vibe-left">
<div class="vibe-card">
<div class="vibe-card-header">
<span class="vibe-card-icon">⚠️</span>
<div>
<div class="vibe-card-title">Where Vibe Coding Breaks Down in Production</div>
<div class="vibe-card-subtitle">Common issues Armely is brought in to resolve</div>
</div>
</div>
<div class="vibe-card-body">
<div class="vibe-risk">
<span class="vibe-risk-icon">🔓</span>
<div>
<div class="vibe-risk-title">Security Vulnerabilities</div>
<div class="vibe-risk-desc">AI-generated code passes basic functional tests but fails security review. Improper input validation, over-permissive access roles, and hardcoded credentials are common patterns that reach production undetected.</div>
</div>
</div>
<div class="vibe-risk">
<span class="vibe-risk-icon">🔀</span>
<div>
<div class="vibe-risk-title">Ungoverned Data Access</div>
<div class="vibe-risk-desc">Agents built without proper permission scoping expose content that users should not see. A customer-facing agent answering from an unscopeable knowledge base is a data governance incident waiting to happen.</div>
</div>
</div>
<div class="vibe-risk">
<span class="vibe-risk-icon">🧩</span>
<div>
<div class="vibe-risk-title">No Maintainable Architecture</div>
<div class="vibe-risk-desc">Prototype code accepted without review accumulates technical debt rapidly. When the business need evolves or the underlying model changes, there is no structured codebase to update.</div>
</div>
</div>
<div class="vibe-risk">
<span class="vibe-risk-icon">📋</span>
<div>
<div class="vibe-risk-title">No Audit Trail</div>
<div class="vibe-risk-desc">Regulated industries require documentation of what an AI system did and why. Vibe-coded agents deployed without audit logging cannot satisfy compliance requirements when they are audited.</div>
</div>
</div>
</div>
</div>
</div>
<div class="vibe-right">
<div class="vibe-answer-card">
<div class="vibe-answer-label">The Armely Approach</div>
<div class="vibe-answer-text">We embrace AI-assisted development as a starting point, not a finishing line. Where your team or ours uses AI tools to accelerate building, we apply the security review, governance configuration, architecture standards, and testing discipline that turns a fast prototype into a production-grade solution your business can rely on.</div>
</div>
<div class="vibe-answer-card">
<div class="vibe-answer-label">What That Means in Practice</div>
<div class="vibe-answer-text">Every AI solution Armely deploys includes defined permission scoping so agents only see what they should, sensitivity label enforcement through Microsoft Purview, audit logging for compliance, a documented architecture that survives a model or platform change, and a governance framework that scales as new agents are added.</div>
</div>
<div class="vibe-answer-card">
<div class="vibe-answer-label">Already Have a Prototype?</div>
<div class="vibe-answer-text">If your team has already built an AI agent or generative AI application and you are not confident it is production-ready, Armely can conduct an AI readiness review, identify gaps, and either remediate the existing solution or rebuild it on a governed foundation.</div>
</div>
</div>
</div>
</div>
</section>
 WHAT ARMELY DELIVERS 
<section class="delivers" id="what-we-deliver">
<div class="section-inner">
<div class="section-eyebrow">What Armely Delivers</div>
<h2 class="section-title">AI solutions designed for your business, built for production.</h2>
<p class="section-body">Every Armely AI engagement starts with a use case that has a clear business outcome, not a technology looking for a problem. We build on the Microsoft AI stack because it integrates with the systems your organization already runs.</p>
<div class="delivers-grid">
<div class="deliver-card">
<div class="deliver-icon">🗺️</div>
<div class="deliver-title">AI Use Case Assessment</div>
<div class="deliver-desc">We work with your leadership and operations teams to identify, prioritize, and scope AI use cases based on business value, data availability, and implementation complexity. You receive a ranked roadmap rather than a technology wish list.</div>
</div>
<div class="deliver-card">
<div class="deliver-icon">💬</div>
<div class="deliver-title">Custom AI Agents</div>
<div class="deliver-desc">We design and build AI agents using Copilot Studio and Azure AI Foundry that answer questions, retrieve information, trigger workflows, and complete tasks, grounded in your SharePoint, Dataverse, SQL Server, or third-party data sources.</div>
</div>
<div class="deliver-card">
<div class="deliver-icon">📄</div>
<div class="deliver-title">Document Intelligence</div>
<div class="deliver-desc">We build generative AI solutions that read, classify, extract, and summarize documents at scale, including contracts, invoices, clinical notes, applications, and compliance filings, reducing manual review time and human error in document-heavy processes.</div>
</div>
<div class="deliver-card">
<div class="deliver-icon">⚡</div>
<div class="deliver-title">Agentic Process Automation</div>
<div class="deliver-desc">We design and deploy autonomous agent workflows that handle multi-step business processes end to end, including case triage, approval routing, data enrichment, exception handling, and escalation, with human review gates where the business requires them.</div>
</div>
<div class="deliver-card">
<div class="deliver-icon">🛡️</div>
<div class="deliver-title">AI Governance and Security</div>
<div class="deliver-desc">We implement the governance layer that every AI deployment requires: permission scoping, sensitivity label enforcement, DLP policies, audit logging, responsible AI controls, and a framework for managing new agents as your AI footprint grows.</div>
</div>
<div class="deliver-card">
<div class="deliver-icon">🔍</div>
<div class="deliver-title">AI Readiness Review</div>
<div class="deliver-desc">For organizations that have already deployed AI solutions and are unsure whether they are production-ready, we conduct a structured review covering security, data governance, architecture quality, and compliance readiness, with a remediation plan where gaps exist.</div>
</div>
</div>
</div>
</section>
 JOURNEY 
<section class="journey" id="journey">
<div class="section-inner">
<div class="section-eyebrow">The Armely AI Journey</div>
<h2 class="section-title">From identified use case to governed production deployment.</h2>
<p class="section-body">AI projects fail most often because use cases are selected without clear success criteria, or because governance is treated as a post-launch concern. Our process addresses both from the start.</p>
<div class="steps-row">
<div class="step">
<div class="step-num">01</div>
<div class="step-title">Use Case Discovery</div>
<div class="step-desc">We identify and prioritize AI opportunities based on business value, data readiness, and implementation risk. You leave with a ranked list, not a blank canvas.</div>
<span class="step-tag">Free</span>
</div>
<div class="step">
<div class="step-num">02</div>
<div class="step-title">Data and Governance Design</div>
<div class="step-desc">We map your data sources, design the permission and governance model, and confirm the Microsoft platform tools required before any build begins.</div>
<span class="step-tag">Week 1</span>
</div>
<div class="step">
<div class="step-num">03</div>
<div class="step-title">Build and Test</div>
<div class="step-desc">We build the agent or application, test it against real data with realistic user scenarios, and apply security and governance controls throughout rather than at the end.</div>
<span class="step-tag">Weeks 2-5</span>
</div>
<div class="step">
<div class="step-num">04</div>
<div class="step-title">Pilot and Validate</div>
<div class="step-desc">A controlled pilot with a defined user group validates business outcomes before broad deployment. We measure against the success criteria defined in discovery.</div>
<span class="step-tag">Week 6</span>
</div>
<div class="step">
<div class="step-num">05</div>
<div class="step-title">Scale and Govern</div>
<div class="step-desc">Broader rollout with training, an agent registry, ongoing monitoring, and governance reviews as new AI solutions are added to your environment.</div>
<span class="step-tag">Ongoing</span>
</div>
</div>
</div>
</section>
 USE CASES 
<section class="usecases">
<div class="section-inner">
<div class="section-eyebrow">Common Engagements</div>
<h2 class="section-title">The AI use cases delivering measurable value for organizations today.</h2>
<p class="section-body">These are the scenarios where generative and agentic AI is delivering clear, verifiable business outcomes rather than experimental value. Each can be built on the Microsoft platform using your existing data and systems.</p>
<div class="uc-grid">
<div class="uc-card">
<span class="uc-icon">💬</span>
<div class="uc-title">Internal Knowledge Assistants</div>
<div class="uc-desc">AI agents grounded in your SharePoint, policies, and documentation that answer employee questions accurately, with citations, in natural language. HR policy queries, IT support questions, and onboarding guidance handled without routing to a person.</div>
</div>
<div class="uc-card">
<span class="uc-icon">📋</span>
<div class="uc-title">Automated Case and Ticket Triage</div>
<div class="uc-desc">Agents that read incoming service requests, classify them by type and urgency, extract relevant information, match against known issues, and route to the correct team or trigger an automated resolution, without a human reading every submission first.</div>
</div>
<div class="uc-card">
<span class="uc-icon">📄</span>
<div class="uc-title">Contract and Document Review</div>
<div class="uc-desc">Generative AI that reads contracts, extracts key clauses, flags non-standard terms, and produces a structured summary for legal or procurement review, reducing the time spent on initial document review before a human judgment is required.</div>
</div>
<div class="uc-card">
<span class="uc-icon">📊</span>
<div class="uc-title">Business Report Generation</div>
<div class="uc-desc">Agents connected to your data sources that generate structured reports, narrative summaries, and variance analyses on a schedule or on demand, in the format and tone your leadership uses, populated with live figures rather than copied from a spreadsheet.</div>
</div>
<div class="uc-card">
<span class="uc-icon">🎧</span>
<div class="uc-title">Customer-Facing AI Assistants</div>
<div class="uc-desc">Governed customer-facing agents deployed on your website or portal that answer product questions, check order status, handle routine service requests, and escalate to a human when the situation requires judgment, available around the clock.</div>
</div>
<div class="uc-card">
<span class="uc-icon">🔄</span>
<div class="uc-title">Multi-Step Agentic Workflows</div>
<div class="uc-desc">End-to-end autonomous workflows where an agent receives a trigger, gathers information from multiple systems, takes action, and notifies stakeholders, all without human intervention at each step. Employee onboarding, vendor registration, and data reconciliation are common examples.</div>
</div>
</div>
</div>
</section>
 TESTIMONIALS 
<section class="testimonials">
<div class="section-inner">
<div class="section-eyebrow">Client Results</div>
<h2 class="section-title">What our clients say about working with Armely on AI.</h2>
<div class="testi-grid">
<div class="testi-card">
<span class="testi-quote">“</span>
<p class="testi-body">We had a team member who built an AI agent quickly using vibe coding tools and it worked well in testing. When we brought Armely in to review it before production deployment, they identified several data access gaps where the agent could surface content that should have been restricted. They rebuilt it on a governed foundation in two weeks and the compliance review passed without issues.</p>
<div class="testi-footer">
<div class="testi-avatar">CIO</div>
<div>
<div class="testi-stars">★★★★★</div>
<div class="testi-name">Chief Information Officer</div>
<div class="testi-role">Healthcare Network, Midwest</div>
</div>
</div>
</div>
<div class="testi-card">
<span class="testi-quote">“</span>
<p class="testi-body">Armely built an internal knowledge agent grounded in our policy library and SharePoint content. Our HR team used to handle a high volume of routine policy questions from staff every week. Within the first month of the agent being live, that volume dropped meaningfully. Staff get accurate, sourced answers immediately rather than waiting for a response.</p>
<div class="testi-footer">
<div class="testi-avatar">HR</div>
<div>
<div class="testi-stars">★★★★★</div>
<div class="testi-name">VP of Human Resources</div>
<div class="testi-role">Professional Services Organization, Texas</div>
</div>
</div>
</div>
<div class="testi-card">
<span class="testi-quote">“</span>
<p class="testi-body">We wanted to use AI to accelerate contract review but were not sure where to start. Armely ran a structured use case assessment, recommended a document intelligence solution built on Azure AI Foundry, and delivered a working system in six weeks. Our legal team now uses it as the first pass on every incoming contract before a lawyer touches it.</p>
<div class="testi-footer">
<div class="testi-avatar">GC</div>
<div>
<div class="testi-stars">★★★★★</div>
<div class="testi-name">General Counsel</div>
<div class="testi-role">Financial Services Firm, Southeast</div>
</div>
</div>
</div>
</div>
</div>
</section>
 WHY ARMELY 
<section class="why" id="why-armely">
<div class="section-inner">
<div class="why-two-col">
<div>
<div class="section-eyebrow">Why Armely</div>
<h2 class="section-title">AI implementation requires more than AI enthusiasm.</h2>
<p class="section-body">The organizations that get the most from AI are those that treat it as a governed business capability rather than an experiment. Armely brings the architecture discipline, compliance knowledge, and Microsoft platform expertise to make that happen.</p>
<ul class="why-list">
<li>
<div class="why-icon">🎯</div>
<div>
<div class="why-item-title">Microsoft AI Platform Certified</div>
<div class="why-item-desc">Our team holds certifications in Copilot Studio, Azure AI Foundry, and Microsoft 365, with production delivery experience building AI agents and generative AI applications on the Microsoft stack across healthcare, education, and enterprise environments.</div>
</div>
</li>
<li>
<div class="why-icon">🛡️</div>
<div>
<div class="why-item-title">Governance Is Not Optional for Us Either</div>
<div class="why-item-desc">Every solution Armely deploys includes Microsoft Purview integration, permission scoping, audit logging, and a documented governance framework. We do not ship an AI solution that we would not be comfortable presenting to a compliance auditor ourselves.</div>
</div>
</li>
<li>
<div class="why-icon">🔗</div>
<div>
<div class="why-item-title">Your Data Stays in Your Environment</div>
<div class="why-item-desc">We build on the Microsoft AI stack because it processes your data inside your Microsoft 365 tenant with your existing security controls applied. Your data does not pass through third-party AI services without your explicit knowledge and consent.</div>
</div>
</li>
<li>
<div class="why-icon">📈</div>
<div>
<div class="why-item-title">Measured Against Business Outcomes</div>
<div class="why-item-desc">We define success criteria before we build, not after. Every AI engagement includes a measurement framework so you can demonstrate the value of your investment to leadership with verifiable evidence rather than anecdote.</div>
</div>
</li>
</ul>
</div>
<div>
<div class="partner-block">
<div class="partner-block-top">
<div class="partner-label">Microsoft Authorized Partner</div>
<p class="partner-text">Armely's Microsoft partnership gives us access to Azure AI Foundry technical resources, Copilot Studio enterprise support, and early access to new AI capabilities before they reach general availability. That means your AI solutions are built on the most current Microsoft patterns and governed by the frameworks Microsoft's own enterprise teams use.</p>
</div>
<div class="partner-stats">
<div class="p-stat">
<div class="p-stat-num">1.3<span>B</span></div>
<div class="p-stat-label">AI agents projected to be in production globally by 2028 (IDC)</div>
</div>
<div class="p-stat">
<div class="p-stat-num">41<span>%</span></div>
<div class="p-stat-label">of all code written today is AI-assisted (GitHub, 2026)</div>
</div>
<div class="p-stat">
<div class="p-stat-num">35<span>%</span></div>
<div class="p-stat-label">of enterprise development teams already using AI-assisted coding tools (JetBrains, 2026)</div>
</div>
<div class="p-stat">
<div class="p-stat-num">0</div>
<div class="p-stat-label">AI solutions Armely deploys without a defined governance and audit framework</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
 CTA 
<section class="cta-section" id="contact">
<div class="cta-inner">
<div>
<div class="section-eyebrow">Get Started</div>
<h2 class="section-title">Tell us what you want AI to do for your business.</h2>
<p class="section-body">Book a free 30-minute AI assessment. We will review your current environment, identify the highest-value AI opportunities, and come back with a prioritized roadmap and implementation proposal at no obligation.</p>
<div style="margin-top: 28px; display: flex; flex-direction: column; gap: 12px;">
<div class="trust-item">
<span class="trust-dot" style="background: var(--blue);"></span>
<span class="trust-text" style="color: var(--text-body);">Free assessment, no commitment required</span>
</div>
<div class="trust-item">
<span class="trust-dot" style="background: var(--blue);"></span>
<span class="trust-text" style="color: var(--text-body);">Prioritized use case roadmap included</span>
</div>
<div class="trust-item">
<span class="trust-dot" style="background: var(--blue);"></span>
<span class="trust-text" style="color: var(--text-body);">Response within one business day</span>
</div>
</div>
</div>
<div class="cta-form">
<div class="form-title">Book Your Free AI Assessment</div>
<div class="form-sub">Tell us where you are today with AI.</div>
<div class="form-row">
<label>Full Name</label>
<input placeholder="Jane Smith" type="text"/>
</div>
<div class="form-row">
<label>Business Email</label>
<input placeholder="jane@yourcompany.com" type="email"/>
</div>
<div class="form-row">
<label>Company Name</label>
<input placeholder="Acme Corp" type="text"/>
</div>
<div class="form-row">
<label>Where Are You Today?</label>
<select>
<option value="">Select...</option>
<option>We have not started with AI yet</option>
<option>We use Microsoft 365 Copilot but want to go further</option>
<option>We have a prototype that needs to go to production</option>
<option>We want to build a custom AI agent or assistant</option>
<option>We need an AI governance framework</option>
<option>We want an AI readiness review of existing solutions</option>
<option>Not sure, need a recommendation</option>
</select>
</div>
<button class="form-submit">Request Free AI Assessment</button>
<div class="form-note">No spam. No sales pressure. Just a useful conversation.</div>
</div>
</div>
</section>
 FOOTER
</div>
@endsection

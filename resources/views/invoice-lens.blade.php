@extends('layouts.public')

@section('title', 'Armely - InvoiceLens')
@section('meta_description', 'Invoice Lens from Armely gives Enverus OpenInvoice operators real-time visibility into pending invoices before they reach AP and ERP systems.')
@section('meta_keywords', 'Invoice Lens, OpenInvoice, Enverus OpenInvoice, AP visibility, invoice search, oil and gas finance, Armely')
@section('canonical_url', route('invoice-lens'))

@push('head')
<meta property="og:title" content="Armely - InvoiceLens">
<meta property="og:description" content="Real-time invoice visibility for Enverus OpenInvoice operators, deployed inside your environment by Armely.">
<meta property="og:url" content="{{ route('invoice-lens') }}">
<meta name="twitter:title" content="Armely - InvoiceLens">
<meta name="twitter:description" content="See pending OpenInvoice spend before it reaches your ERP or AP system.">
@endpush

@push('styles')
<style>


.armely-invoice-lens-page *, .armely-invoice-lens-page *::before, .armely-invoice-lens-page *::after { box-sizing: border-box; margin: 0; padding: 0; }

.armely-invoice-lens-page {
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

.armely-invoice-lens-page { scroll-behavior: smooth; }
.armely-invoice-lens-page { font-family: 'Poppins', sans-serif; background: var(--navy); color: var(--text-body); line-height: 1.6; }

  /* NAV */
.armely-invoice-lens-page nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 18px 56px; background: rgba(26,46,82,0.96); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(255,255,255,0.08); }
.armely-invoice-lens-page .logo { display: flex; align-items: center; gap: 10px; }
.armely-invoice-lens-page .logo-mark { width: 36px; height: 36px; background: var(--blue); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #fff; }
.armely-invoice-lens-page .logo-text { font-size: 1.25rem; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
.armely-invoice-lens-page .nav-links { display: flex; gap: 32px; align-items: center; list-style: none; }
.armely-invoice-lens-page .nav-links a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
.armely-invoice-lens-page .nav-links a:hover { color: #fff; }
.armely-invoice-lens-page .nav-cta { background: var(--blue); color: #fff !important; padding: 10px 22px; border-radius: 6px; font-size: 0.875rem; font-weight: 600 !important; transition: background 0.2s !important; }
.armely-invoice-lens-page .nav-cta:hover { background: var(--blue-lt) !important; }

  /* HERO */
.armely-invoice-lens-page .hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; padding: 140px 56px 100px; position: relative; overflow: hidden; background: #1a2e52; }
.armely-invoice-lens-page .hero-bg-glow { position: absolute; top: -180px; right: -100px; width: 720px; height: 720px; background: radial-gradient(circle, rgba(41,78,139,0.2) 0%, transparent 68%); pointer-events: none; }
.armely-invoice-lens-page .hero-eyebrow { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 24px; }
.armely-invoice-lens-page .eyebrow-badge { background: rgba(41,78,139,0.35); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; padding: 5px 14px; border-radius: 40px; }
.armely-invoice-lens-page .eyebrow-partner { font-size: 0.78rem; color: rgba(255,255,255,0.5); font-weight: 400; }
.armely-invoice-lens-page .hero h1 { font-size: clamp(2.6rem, 5.5vw, 4.8rem); font-weight: 800; line-height: 1.08; color: #FFFFFF; max-width: 820px; margin-bottom: 24px; letter-spacing: -0.03em; }
.armely-invoice-lens-page .hero h1 .hl { color: #FFFFFF; opacity: 0.92; }
.armely-invoice-lens-page .hero-sub { font-size: 1.05rem; font-weight: 300; color: rgba(255,255,255,0.82); max-width: 580px; margin-bottom: 40px; line-height: 1.8; }
.armely-invoice-lens-page .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 72px; }
.armely-invoice-lens-page .btn-primary { background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s, transform 0.15s; display: inline-block; }
.armely-invoice-lens-page .btn-primary:hover { background: var(--blue-lt); transform: translateY(-2px); }
.armely-invoice-lens-page .btn-outline { background: transparent; color: rgba(255,255,255,0.85); border: 1px solid rgba(255,255,255,0.25); border-radius: 7px; padding: 14px 32px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 500; cursor: pointer; text-decoration: none; transition: border-color 0.2s, background 0.2s; display: inline-block; }
.armely-invoice-lens-page .btn-outline:hover { border-color: rgba(255,255,255,0.5); background: rgba(255,255,255,0.06); }
.armely-invoice-lens-page .hero-trust { display: flex; gap: 40px; flex-wrap: wrap; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.12); }
.armely-invoice-lens-page .trust-item { display: flex; align-items: center; gap: 10px; }
.armely-invoice-lens-page .trust-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); flex-shrink: 0; }
.armely-invoice-lens-page .trust-text { font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 400; }
.armely-invoice-lens-page .trust-text strong { color: #fff; font-weight: 600; }

  /* SECTIONS */
.armely-invoice-lens-page section { padding: 48px 56px; }
.armely-invoice-lens-page .section-inner { max-width: 1100px; margin: 0 auto; }
.armely-invoice-lens-page .section-eyebrow { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); margin-bottom: 14px; font-weight: 600; }
.armely-invoice-lens-page .section-title { font-size: clamp(1.7rem, 3.2vw, 2.6rem); font-weight: 800; color: #1A2540; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 18px; max-width: 640px; }
.armely-invoice-lens-page .section-body { font-size: 0.975rem; font-weight: 300; max-width: 560px; line-height: 1.8; color: var(--text-body); margin-bottom: 48px; }

  /* AI SPECTRUM */
.armely-invoice-lens-page .spectrum { background: var(--navy-mid); }
.armely-invoice-lens-page .spectrum-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-invoice-lens-page .spectrum-row { display: flex; flex-direction: column; gap: 10px; margin-top: 32px; }
.armely-invoice-lens-page .spectrum-level { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px; display: flex; align-items: flex-start; gap: 14px; transition: border-color 0.2s; }
.armely-invoice-lens-page .spectrum-level:hover { border-color: rgba(41,78,139,0.3); }
.armely-invoice-lens-page .spectrum-level.highlight { background: var(--blue-dim); border-color: var(--blue-dim2); }
.armely-invoice-lens-page .spectrum-num { font-size: 0.68rem; font-weight: 800; color: var(--blue); background: var(--blue-dim2); border-radius: 4px; padding: 2px 7px; flex-shrink: 0; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.08em; }
.armely-invoice-lens-page .spectrum-content-title { font-size: 0.875rem; font-weight: 700; color: #1A2540; margin-bottom: 3px; }
.armely-invoice-lens-page .spectrum-content-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }

  /* Platform visual */
.armely-invoice-lens-page .platform-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(41,78,139,0.07); }
.armely-invoice-lens-page .platform-header { padding: 16px 22px; border-bottom: 1px solid var(--border); background: var(--navy-mid); display: flex; align-items: center; gap: 10px; }
.armely-invoice-lens-page .platform-dots { display: flex; gap: 6px; }
.armely-invoice-lens-page .platform-dots span { width: 10px; height: 10px; border-radius: 50%; background: rgba(41,78,139,0.15); }
.armely-invoice-lens-page .platform-header-title { font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; }
.armely-invoice-lens-page .platform-body { padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.armely-invoice-lens-page .plat-band { border-radius: 9px; padding: 13px 16px; }
.armely-invoice-lens-page .plat-band-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; }
.armely-invoice-lens-page .plat-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.armely-invoice-lens-page .plat-chip { font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
.armely-invoice-lens-page .band-tools { background: var(--blue-dim); }
.armely-invoice-lens-page .band-tools .plat-band-label { color: var(--blue); }
.armely-invoice-lens-page .band-tools .plat-chip { background: var(--blue-dim2); color: var(--blue); }
.armely-invoice-lens-page .band-data { background: rgba(41,78,139,0.05); }
.armely-invoice-lens-page .band-data .plat-band-label { color: var(--blue); }
.armely-invoice-lens-page .band-data .plat-chip { background: rgba(41,78,139,0.1); color: var(--blue); }
.armely-invoice-lens-page .band-gov { background: var(--blue); }
.armely-invoice-lens-page .band-gov .plat-band-label { color: rgba(255,255,255,0.7); }
.armely-invoice-lens-page .band-gov .plat-chip { background: rgba(255,255,255,0.15); color: #fff; }
.armely-invoice-lens-page .band-arrow { text-align: center; color: var(--text-muted); font-size: 0.85rem; padding: 2px 0; }

  /* VIBE CODING CALLOUT */
.armely-invoice-lens-page .vibe-section { background: var(--navy); }
.armely-invoice-lens-page .vibe-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: start; }
.armely-invoice-lens-page .vibe-left { }
.armely-invoice-lens-page .vibe-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-invoice-lens-page .vibe-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
.armely-invoice-lens-page .vibe-card-icon { font-size: 1.4rem; }
.armely-invoice-lens-page .vibe-card-title { font-size: 1rem; font-weight: 700; color: #1A2540; }
.armely-invoice-lens-page .vibe-card-subtitle { font-size: 0.78rem; color: var(--text-muted); }
.armely-invoice-lens-page .vibe-card-body { padding: 24px; }
.armely-invoice-lens-page .vibe-risk { display: flex; align-items: flex-start; gap: 12px; padding: 14px 0; border-bottom: 1px solid var(--border); }
.armely-invoice-lens-page .vibe-risk:last-child { border-bottom: none; }
.armely-invoice-lens-page .vibe-risk-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
.armely-invoice-lens-page .vibe-risk-title { font-size: 0.82rem; font-weight: 700; color: #1A2540; margin-bottom: 2px; }
.armely-invoice-lens-page .vibe-risk-desc { font-size: 0.77rem; color: var(--text-muted); line-height: 1.5; }
.armely-invoice-lens-page .vibe-right { }
.armely-invoice-lens-page .vibe-answer-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 28px; margin-bottom: 12px; }
.armely-invoice-lens-page .vibe-answer-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--blue); margin-bottom: 10px; }
.armely-invoice-lens-page .vibe-answer-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.75; }

  /* DELIVERS */
.armely-invoice-lens-page .delivers { background: var(--navy-mid); }
.armely-invoice-lens-page .delivers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-invoice-lens-page .deliver-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 32px 26px; transition: border-color 0.2s, transform 0.2s; }
.armely-invoice-lens-page .deliver-card:hover { border-color: rgba(41,78,139,0.35); transform: translateY(-3px); }
.armely-invoice-lens-page .deliver-icon { width: 48px; height: 48px; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 20px; }
.armely-invoice-lens-page .deliver-title { font-size: 1rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-invoice-lens-page .deliver-desc { font-size: 0.875rem; line-height: 1.7; color: var(--text-body); }

  /* JOURNEY */
.armely-invoice-lens-page .journey { background: var(--navy); }
.armely-invoice-lens-page .steps-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; margin-top: 56px; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-invoice-lens-page .step { padding: 32px 22px; border-right: 1px solid var(--border); }
.armely-invoice-lens-page .step:last-child { border-right: none; }
.armely-invoice-lens-page .step-num { font-size: 2.4rem; font-weight: 800; color: rgba(41,78,139,0.18); line-height: 1; margin-bottom: 14px; }
.armely-invoice-lens-page .step-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 10px; }
.armely-invoice-lens-page .step-desc { font-size: 0.82rem; line-height: 1.65; color: var(--text-body); }
.armely-invoice-lens-page .step-tag { display: inline-block; margin-top: 14px; background: var(--blue-dim); color: var(--blue); font-size: 0.7rem; padding: 3px 10px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }

  /* USE CASES */
.armely-invoice-lens-page .usecases { background: var(--navy-mid); }
.armely-invoice-lens-page .uc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 48px; }
.armely-invoice-lens-page .uc-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 28px 24px; transition: border-color 0.2s; }
.armely-invoice-lens-page .uc-card:hover { border-color: rgba(41,78,139,0.25); }
.armely-invoice-lens-page .uc-icon { font-size: 1.6rem; margin-bottom: 14px; display: block; }
.armely-invoice-lens-page .uc-title { font-size: 0.95rem; font-weight: 700; color: #1A2540; margin-bottom: 8px; }
.armely-invoice-lens-page .uc-desc { font-size: 0.85rem; line-height: 1.68; color: var(--text-body); }

  /* TESTIMONIALS */
.armely-invoice-lens-page .testimonials { background: var(--navy-mid); padding: 48px 56px; }
.armely-invoice-lens-page .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.armely-invoice-lens-page .testi-card { background: var(--navy-card); border: 1px solid var(--border); border-radius: 14px; padding: 32px 28px; display: flex; flex-direction: column; }
.armely-invoice-lens-page .testi-quote { font-size: 3.5rem; line-height: 0.9; color: var(--blue); opacity: 0.15; font-family: Georgia, serif; margin-bottom: 8px; display: block; }
.armely-invoice-lens-page .testi-body { font-size: 0.875rem; line-height: 1.8; color: var(--text-body); flex: 1; margin-bottom: 24px; font-style: italic; }
.armely-invoice-lens-page .testi-footer { display: flex; align-items: center; gap: 14px; }
.armely-invoice-lens-page .testi-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; letter-spacing: 0.02em; }
.armely-invoice-lens-page .testi-name { font-size: 0.875rem; font-weight: 700; color: #1A2540; }
.armely-invoice-lens-page .testi-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
.armely-invoice-lens-page .testi-stars { color: var(--blue); font-size: 0.72rem; letter-spacing: 1px; margin-bottom: 3px; }

  /* WHY ARMELY */
.armely-invoice-lens-page .why { background: var(--navy-mid); }
.armely-invoice-lens-page .why-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: start; }
.armely-invoice-lens-page .why-list { list-style: none; margin-top: 36px; }
.armely-invoice-lens-page .why-list li { display: flex; gap: 16px; padding: 20px 0; border-bottom: 1px solid var(--border); }
.armely-invoice-lens-page .why-list li:last-child { border-bottom: none; }
.armely-invoice-lens-page .why-icon { width: 42px; height: 42px; flex-shrink: 0; background: var(--blue-dim); border: 1px solid var(--blue-dim2); border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.armely-invoice-lens-page .why-item-title { font-weight: 600; color: #1A2540; font-size: 0.9rem; margin-bottom: 4px; }
.armely-invoice-lens-page .why-item-desc { font-size: 0.84rem; color: var(--text-body); line-height: 1.65; }
.armely-invoice-lens-page .partner-block { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.armely-invoice-lens-page .partner-block-top { padding: 28px; border-bottom: 1px solid var(--border); }
.armely-invoice-lens-page .partner-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--blue); font-weight: 700; margin-bottom: 10px; }
.armely-invoice-lens-page .partner-text { font-size: 0.875rem; color: var(--text-body); line-height: 1.7; }
.armely-invoice-lens-page .partner-stats { display: grid; grid-template-columns: 1fr 1fr; }
.armely-invoice-lens-page .p-stat { padding: 24px 28px; border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-invoice-lens-page .p-stat:nth-child(2) { border-right: none; }
.armely-invoice-lens-page .p-stat:nth-child(3) { border-bottom: none; }
.armely-invoice-lens-page .p-stat:nth-child(4) { border-right: none; border-bottom: none; }
.armely-invoice-lens-page .p-stat-num { font-size: 1.8rem; font-weight: 800; color: #1A2540; line-height: 1; margin-bottom: 4px; }
.armely-invoice-lens-page .p-stat-num span { color: var(--blue); }
.armely-invoice-lens-page .p-stat-label { font-size: 0.75rem; color: var(--text-muted); line-height: 1.4; }

  /* CTA */
.armely-invoice-lens-page .cta-section { background: var(--navy-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.armely-invoice-lens-page .cta-inner { max-width: 1100px; margin: 0 auto; padding: 48px 56px; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
.armely-invoice-lens-page .cta-form { background: #FFFFFF; border: 1px solid var(--border); border-radius: 14px; padding: 36px 32px; box-shadow: 0 4px 24px rgba(41,78,139,0.08); }
.armely-invoice-lens-page .form-title { font-size: 1.1rem; font-weight: 700; color: #1A2540; margin-bottom: 6px; }
.armely-invoice-lens-page .form-sub { font-size: 0.84rem; color: var(--text-muted); margin-bottom: 24px; }
.armely-invoice-lens-page .form-row { margin-bottom: 14px; }
.armely-invoice-lens-page .form-row label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
.armely-invoice-lens-page .form-row input, .armely-invoice-lens-page .form-row select { width: 100%; background: #FFFFFF; border: 1px solid rgba(41,78,139,0.15); border-radius: 7px; padding: 11px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #1A2540; outline: none; transition: border-color 0.2s; }
.armely-invoice-lens-page .form-row input:focus, .armely-invoice-lens-page .form-row select:focus { border-color: rgba(41,78,139,0.4); }
.armely-invoice-lens-page .form-row select option { background: #fff; color: #1A2540; }
.armely-invoice-lens-page .form-submit { width: 100%; background: var(--blue); color: #fff; border: none; border-radius: 7px; padding: 14px; margin-top: 8px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.armely-invoice-lens-page .form-submit:hover { background: var(--blue-lt); }
.armely-invoice-lens-page .form-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: var(--text-muted); }

  /* FOOTER */
.armely-invoice-lens-page footer { background: #1a2e52; border-top: 1px solid rgba(255,255,255,0.08); padding: 36px 56px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.armely-invoice-lens-page .footer-logo-row { display: flex; align-items: center; gap: 10px; }
.armely-invoice-lens-page .footer-lm { width: 30px; height: 30px; background: var(--blue); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; color: #fff; }
.armely-invoice-lens-page .footer-lt { font-size: 1rem; font-weight: 700; color: #fff; }
.armely-invoice-lens-page .footer-note { font-size: 0.78rem; color: rgba(255,255,255,0.4); }
.armely-invoice-lens-page .footer-badges { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
.armely-invoice-lens-page .badge-chip { border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; padding: 5px 12px; font-size: 0.72rem; color: rgba(255,255,255,0.5); font-weight: 500; }

  /* RESPONSIVE */
  @media (max-width: 900px) {
.armely-invoice-lens-page nav { padding: 16px 24px; }
.armely-invoice-lens-page .nav-links { display: none; }
.armely-invoice-lens-page section { padding: 72px 24px; }
.armely-invoice-lens-page .hero { padding: 110px 24px 72px; }
.armely-invoice-lens-page .spectrum-grid, .armely-invoice-lens-page .vibe-two-col, .armely-invoice-lens-page .why-two-col { grid-template-columns: 1fr; gap: 40px; }
.armely-invoice-lens-page .delivers-grid, .armely-invoice-lens-page .uc-grid { grid-template-columns: 1fr 1fr; }
.armely-invoice-lens-page .steps-row { grid-template-columns: 1fr; }
.armely-invoice-lens-page .step { border-right: none; border-bottom: 1px solid var(--border); }
.armely-invoice-lens-page .step:last-child { border-bottom: none; }
.armely-invoice-lens-page .cta-inner { grid-template-columns: 1fr; gap: 40px; padding: 72px 24px; }
.armely-invoice-lens-page .testimonials { padding: 72px 24px; }
.armely-invoice-lens-page .testi-grid { grid-template-columns: 1fr; }
.armely-invoice-lens-page footer { padding: 32px 24px; flex-direction: column; align-items: flex-start; }
  }
  @media (max-width: 600px) {
.armely-invoice-lens-page .delivers-grid, .armely-invoice-lens-page .uc-grid { grid-template-columns: 1fr; }
.armely-invoice-lens-page .partner-stats { grid-template-columns: 1fr; }
.armely-invoice-lens-page .hero-trust { gap: 20px; }
  }
  @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }

/* Armely service-page polish */
.armely-invoice-lens-page {
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
.armely-invoice-lens-page .hero {
  min-height: 100vh;
  background: linear-gradient(135deg, #173b67 0%, #2f5597 58%, #4f86c6 100%);
}
.armely-invoice-lens-page .hero::after {
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
.armely-invoice-lens-page .section-title,
.armely-invoice-lens-page .deliver-title,
.armely-invoice-lens-page .uc-title,
.armely-invoice-lens-page .step-title,
.armely-invoice-lens-page .why-item-title,
.armely-invoice-lens-page .form-title {
  color: #162b49;
}
.armely-invoice-lens-page .deliver-card,
.armely-invoice-lens-page .uc-card,
.armely-invoice-lens-page .testi-card,
.armely-invoice-lens-page .platform-card,
.armely-invoice-lens-page .partner-block,
.armely-invoice-lens-page .cta-form {
  box-shadow: 0 16px 42px rgba(18, 47, 82, 0.08);
}
.armely-invoice-lens-page .deliver-card:hover,
.armely-invoice-lens-page .uc-card:hover {
  box-shadow: 0 20px 46px rgba(18, 47, 82, 0.13);
}
.armely-invoice-lens-page .btn-primary,
.armely-invoice-lens-page .form-submit {
  background: linear-gradient(135deg, #2f5597, #4477bd);
  box-shadow: 0 10px 24px rgba(47, 85, 151, 0.24);
}
.armely-invoice-lens-page .btn-primary:hover,
.armely-invoice-lens-page .form-submit:hover {
  background: linear-gradient(135deg, #274a83, #3c6dac);
}
.armely-invoice-lens-page .btn-outline:hover {
  background: rgba(255,255,255,0.11);
}
.armely-invoice-lens-page nav,
.armely-invoice-lens-page footer {
  display: none;
}


/* Modern layout update: tighter first section and SVG icon system */
.armely-invoice-lens-page .hero {
  min-height: auto;
  padding: 128px 32px 86px;
  isolation: isolate;
}
.armely-invoice-lens-page .hero-inner {
  width: min(1160px, 100%);
  margin: 0 auto;
  display: grid;
  grid-template-columns: minmax(0, 1.05fr) minmax(320px, 0.75fr);
  align-items: center;
  gap: 56px;
  position: relative;
  z-index: 1;
}
.armely-invoice-lens-page .hero-copy { max-width: 760px; }
.armely-invoice-lens-page .hero h1 { max-width: 760px; margin-bottom: 20px; }
.armely-invoice-lens-page .hero-sub { max-width: 640px; margin-bottom: 30px; font-size: 1.02rem; line-height: 1.72; }
.armely-invoice-lens-page .hero-actions { margin-bottom: 34px; }
.armely-invoice-lens-page .hero-trust {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  padding-top: 0;
  border-top: 0;
  max-width: 720px;
}
.armely-invoice-lens-page .hero .trust-item {
  align-items: flex-start;
  background: rgba(255,255,255,0.10);
  border: 1px solid rgba(255,255,255,0.16);
  border-radius: 14px;
  padding: 14px 15px;
  backdrop-filter: blur(10px);
}
.armely-invoice-lens-page .hero .trust-dot {
  width: 22px;
  height: 22px;
  border-radius: 999px;
  background: rgba(255,255,255,0.16);
  border: 1px solid rgba(255,255,255,0.24);
  position: relative;
  margin-top: 1px;
}
.armely-invoice-lens-page .hero .trust-dot::after {
  content: '';
  position: absolute;
  left: 7px;
  top: 5px;
  width: 6px;
  height: 10px;
  border: solid #fff;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}
.armely-invoice-lens-page .hero .trust-text { color: rgba(255,255,255,0.78); line-height: 1.5; }
.armely-invoice-lens-page .hero-visual {
  min-height: 420px;
  border-radius: 28px;
  position: relative;
  background: linear-gradient(145deg, rgba(255,255,255,0.16), rgba(255,255,255,0.05));
  border: 1px solid rgba(255,255,255,0.18);
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.18), 0 28px 70px rgba(6, 22, 48, 0.24);
  overflow: hidden;
}
.armely-invoice-lens-page .hero-visual::before {
  content: '';
  position: absolute;
  inset: 36px;
  border: 1px solid rgba(255,255,255,0.14);
  border-radius: 999px;
}
.armely-invoice-lens-page .hero-visual::after {
  content: '';
  position: absolute;
  width: 190px;
  height: 190px;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  border-radius: 999px;
  background: radial-gradient(circle, rgba(255,255,255,0.24), rgba(255,255,255,0.06));
  border: 1px solid rgba(255,255,255,0.22);
}
.armely-invoice-lens-page .hero-orbit span {
  position: absolute;
  width: 52px;
  height: 52px;
  border-radius: 16px;
  background: rgba(255,255,255,0.14);
  border: 1px solid rgba(255,255,255,0.22);
}
.armely-invoice-lens-page .hero-orbit span:nth-child(1) { left: 56px; top: 88px; }
.armely-invoice-lens-page .hero-orbit span:nth-child(2) { right: 72px; top: 138px; }
.armely-invoice-lens-page .hero-orbit span:nth-child(3) { left: 48%; bottom: 76px; }
.armely-invoice-lens-page .hero-visual-card {
  position: absolute;
  z-index: 2;
  width: 230px;
  padding: 18px;
  border-radius: 18px;
  background: rgba(255,255,255,0.92);
  box-shadow: 0 24px 54px rgba(9, 31, 63, 0.22);
}
.armely-invoice-lens-page .hero-visual-card.top { top: 42px; right: 30px; }
.armely-invoice-lens-page .hero-visual-card.bottom { bottom: 46px; left: 30px; }
.armely-invoice-lens-page .hero-visual-label {
  display: block;
  font-size: 0.78rem;
  font-weight: 700;
  color: #162b49;
  margin-bottom: 12px;
}
.armely-invoice-lens-page .hero-visual-line {
  display: block;
  height: 8px;
  width: 100%;
  border-radius: 999px;
  background: rgba(47, 85, 151, 0.16);
  margin-top: 8px;
}
.armely-invoice-lens-page .hero-visual-line.short { width: 68%; }
.armely-invoice-lens-page .icon-svg {
  width: 22px;
  height: 22px;
  display: block;
  color: var(--blue);
  fill: none;
  stroke: currentColor;
  stroke-width: 2;
  stroke-linecap: round;
  stroke-linejoin: round;
}
.armely-invoice-lens-page .vibe-card-icon,
.armely-invoice-lens-page .vibe-risk-icon,
.armely-invoice-lens-page .deliver-icon,
.armely-invoice-lens-page .uc-icon,
.armely-invoice-lens-page .why-icon {
  color: var(--blue);
}
.armely-invoice-lens-page .vibe-card-icon,
.armely-invoice-lens-page .vibe-risk-icon {
  width: 42px;
  height: 42px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
}
.armely-invoice-lens-page .vibe-risk-icon { width: 36px; height: 36px; border-radius: 10px; }
.armely-invoice-lens-page .deliver-icon .icon-svg,
.armely-invoice-lens-page .uc-icon .icon-svg,
.armely-invoice-lens-page .why-icon .icon-svg { width: 23px; height: 23px; }
.armely-invoice-lens-page .uc-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  display: flex;
  align-items: center;
  justify-content: center;
}
@media (max-width: 980px) {
  .armely-invoice-lens-page .hero-inner { grid-template-columns: 1fr; gap: 32px; }
  .armely-invoice-lens-page .hero-visual { min-height: 320px; }
}
@media (max-width: 680px) {
  .armely-invoice-lens-page .hero { padding: 104px 22px 64px; }
  .armely-invoice-lens-page .hero-trust { grid-template-columns: 1fr; }
  .armely-invoice-lens-page .hero-visual { display: none; }
  .armely-invoice-lens-page .hero-actions a { width: 100%; text-align: center; }
}

/* Focused cleanup for the Generative AI detail page */
.armely-invoice-lens-page .hero {
  min-height: 100vh;
  padding: 80px 56px 48px;
  display: flex;
  align-items: center;
  background: #173b67;
  border-radius: 0;
}
.armely-invoice-lens-page .hero::after,
.armely-invoice-lens-page .hero-bg-glow,
.armely-invoice-lens-page .hero-visual {
  display: none;
}
.armely-invoice-lens-page .hero-inner {
  width: min(1040px, 100%);
  display: block;
}
.armely-invoice-lens-page .hero-copy {
  max-width: 760px;
}
.armely-invoice-lens-page .hero-eyebrow {
  margin-bottom: 18px;
}
.armely-invoice-lens-page .eyebrow-badge {
  background: transparent;
  border: 0;
  border-radius: 0;
  padding: 0;
  color: rgba(255,255,255,0.72);
}
.armely-invoice-lens-page .eyebrow-partner,
.armely-invoice-lens-page .hero-trust {
  display: none;
}
.armely-invoice-lens-page .hero h1 {
  max-width: 760px;
  margin-bottom: 22px;
}
.armely-invoice-lens-page .hero-sub {
  max-width: 680px;
  margin-bottom: 34px;
}
.armely-invoice-lens-page .hero-actions {
  margin-bottom: 0;
}
.armely-invoice-lens-page .hero .btn-primary,
.armely-invoice-lens-page .hero .btn-outline {
  border-radius: 0;
}
.armely-invoice-lens-page .vibe-section {
  background: #fff;
  padding: 48px 56px;
}
.armely-invoice-lens-page .vibe-section .section-inner {
  max-width: 920px;
}
.armely-invoice-lens-page .vibe-section .section-title,
.armely-invoice-lens-page .vibe-section .section-body {
  max-width: 820px;
}
.armely-invoice-lens-page .vibe-section .section-body {
  margin-bottom: 28px;
}
.armely-invoice-lens-page .vibe-two-col {
  grid-template-columns: 1fr;
  gap: 18px;
}
.armely-invoice-lens-page .vibe-card,
.armely-invoice-lens-page .vibe-answer-card {
  border-radius: 0;
  box-shadow: none;
}
.armely-invoice-lens-page .vibe-card-header {
  padding: 18px 20px;
}
.armely-invoice-lens-page .vibe-card-body {
  padding: 8px 20px 12px;
}
.armely-invoice-lens-page .vibe-risk {
  padding: 12px 0;
}
.armely-invoice-lens-page .vibe-right {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
}
.armely-invoice-lens-page .vibe-answer-card {
  margin-bottom: 0;
  padding: 20px;
  background: #f7f9fc;
}
.armely-invoice-lens-page section:not(.hero) > .section-inner > .section-eyebrow,
.armely-invoice-lens-page .why > .section-inner > .section-eyebrow {
  width: fit-content;
  margin: 0 auto 14px;
  padding: 6px 14px;
  border-radius: 999px;
  background: var(--blue-dim);
  border: 1px solid var(--blue-dim2);
  text-align: center;
}
.armely-invoice-lens-page section:not(.hero) > .section-inner > .section-title,
.armely-invoice-lens-page .why > .section-inner > .section-title {
  max-width: 880px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-invoice-lens-page section:not(.hero) > .section-inner > .section-body,
.armely-invoice-lens-page .why > .section-inner > .section-body {
  max-width: 820px;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
}
.armely-invoice-lens-page .spectrum-grid,
.armely-invoice-lens-page .delivers-grid,
.armely-invoice-lens-page .steps-row,
.armely-invoice-lens-page .uc-grid,
.armely-invoice-lens-page .testi-grid,
.armely-invoice-lens-page .why-two-col {
  margin-top: 56px;
}
.armely-invoice-lens-page .why-two-col {
  align-items: stretch;
}
.armely-invoice-lens-page .why-list {
  margin-top: 0;
}
.armely-invoice-lens-page .why-list,
.armely-invoice-lens-page .partner-block {
  height: 100%;
}
@media (max-width: 900px) {
  .armely-invoice-lens-page .hero {
    padding: 118px 24px 76px;
  }
  .armely-invoice-lens-page .vibe-section {
    padding: 72px 24px;
  }
  .armely-invoice-lens-page .vibe-right {
    grid-template-columns: 1fr;
  }
}



/* Final compact modern cleanup */
.armely-invoice-lens-page {
  --blue: #2f5597;
  --blue-lt: #4779bd;
  --navy-mid: #f6f8fc;
  --navy-card: #ffffff;
  --text-body: #334155;
  --text-muted: #667085;
  --border: rgba(47, 85, 151, 0.14);
}
.armely-invoice-lens-page .hero {
  min-height: auto !important;
  padding: 48px 56px !important;
  background: linear-gradient(135deg, #173b67 0%, #234f86 100%) !important;
  border-radius: 0 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}
.armely-invoice-lens-page .hero::after,
.armely-invoice-lens-page .hero-bg-glow,
.armely-invoice-lens-page .hero-visual {
  display: none !important;
}
.armely-invoice-lens-page .hero-inner {
  width: min(1120px, 100%) !important;
  margin: 0 auto !important;
  display: block !important;
}
.armely-invoice-lens-page .hero-copy {
  max-width: 860px !important;
}
.armely-invoice-lens-page .hero-eyebrow {
  margin-bottom: 18px !important;
}
.armely-invoice-lens-page .eyebrow-badge {
  display: inline-flex !important;
  background: rgba(255,255,255,0.10) !important;
  border: 1px solid rgba(255,255,255,0.22) !important;
  border-radius: 999px !important;
  padding: 7px 14px !important;
  color: rgba(255,255,255,0.88) !important;
}
.armely-invoice-lens-page .eyebrow-partner {
  display: inline-flex !important;
  color: rgba(255,255,255,0.66) !important;
}
.armely-invoice-lens-page .hero h1 {
  max-width: 900px !important;
  margin-bottom: 18px !important;
  font-size: clamp(2.5rem, 5vw, 4.9rem) !important;
  line-height: 1.05 !important;
  letter-spacing: -0.04em !important;
}
.armely-invoice-lens-page .hero-sub {
  max-width: 760px !important;
  margin-bottom: 28px !important;
  font-size: 1rem !important;
  line-height: 1.7 !important;
}
.armely-invoice-lens-page .hero-actions {
  margin-bottom: 0 !important;
  gap: 12px !important;
}
.armely-invoice-lens-page .hero .btn-primary,
.armely-invoice-lens-page .hero .btn-outline,
.armely-invoice-lens-page .btn-primary,
.armely-invoice-lens-page .btn-outline,
.armely-invoice-lens-page .form-submit {
  border-radius: 8px !important;
}
.armely-invoice-lens-page section {
  padding: 44px 56px !important;
}
.armely-invoice-lens-page .section-inner {
  max-width: 1120px !important;
}
.armely-invoice-lens-page .section-eyebrow {
  margin-bottom: 10px !important;
}
.armely-invoice-lens-page .section-title {
  margin-bottom: 14px !important;
}
.armely-invoice-lens-page .section-body {
  margin-bottom: 28px !important;
  line-height: 1.65 !important;
}
.armely-invoice-lens-page .spectrum-grid,
.armely-invoice-lens-page .vibe-two-col,
.armely-invoice-lens-page .delivers-grid,
.armely-invoice-lens-page .steps-row,
.armely-invoice-lens-page .uc-grid,
.armely-invoice-lens-page .testi-grid,
.armely-invoice-lens-page .why-two-col {
  margin-top: 34px !important;
  gap: 20px !important;
}
.armely-invoice-lens-page .spectrum-grid,
.armely-invoice-lens-page .why-two-col {
  grid-template-columns: 1fr 1fr !important;
}
.armely-invoice-lens-page .spectrum-row {
  margin-top: 0 !important;
  gap: 10px !important;
}
.armely-invoice-lens-page .spectrum-level,
.armely-invoice-lens-page .deliver-card,
.armely-invoice-lens-page .uc-card,
.armely-invoice-lens-page .testi-card,
.armely-invoice-lens-page .vibe-answer-card,
.armely-invoice-lens-page .partner-block,
.armely-invoice-lens-page .cta-form,
.armely-invoice-lens-page .vibe-card,
.armely-invoice-lens-page .platform-card {
  border-radius: 14px !important;
  box-shadow: 0 14px 36px rgba(18, 47, 82, 0.08) !important;
}
.armely-invoice-lens-page .deliver-card,
.armely-invoice-lens-page .uc-card,
.armely-invoice-lens-page .testi-card {
  padding: 24px 22px !important;
}
.armely-invoice-lens-page .deliver-icon,
.armely-invoice-lens-page .uc-icon,
.armely-invoice-lens-page .why-icon,
.armely-invoice-lens-page .vibe-card-icon,
.armely-invoice-lens-page .vibe-risk-icon {
  border-radius: 12px !important;
  color: var(--blue) !important;
}
.armely-invoice-lens-page .vibe-section {
  padding: 44px 56px !important;
  background: #fff !important;
}
.armely-invoice-lens-page .vibe-section .section-inner {
  max-width: 1120px !important;
}
.armely-invoice-lens-page .vibe-two-col {
  grid-template-columns: 1fr 1fr !important;
  align-items: stretch !important;
}
.armely-invoice-lens-page .vibe-card-body {
  padding: 18px 20px !important;
}
.armely-invoice-lens-page .vibe-right {
  display: flex !important;
  flex-direction: column !important;
  gap: 12px !important;
}
.armely-invoice-lens-page .vibe-answer-card {
  padding: 20px !important;
  background: #f8fafd !important;
}
.armely-invoice-lens-page .steps-row {
  border-radius: 14px !important;
  overflow: hidden !important;
}
.armely-invoice-lens-page .step {
  padding: 24px 18px !important;
}
.armely-invoice-lens-page .step-num {
  font-size: 2rem !important;
  margin-bottom: 10px !important;
}
.armely-invoice-lens-page .why-list {
  margin-top: 0 !important;
}
.armely-invoice-lens-page .why-list li {
  padding: 16px 0 !important;
}
.armely-invoice-lens-page .partner-block-top,
.armely-invoice-lens-page .p-stat {
  padding: 22px !important;
}
.armely-invoice-lens-page .cta-inner {
  padding: 34px 56px 40px !important;
  gap: 34px !important;
  align-items: start !important;
}
.armely-invoice-lens-page .cta-copy {
  padding-top: 8px !important;
}
.armely-invoice-lens-page .cta-copy .section-title {
  max-width: 760px !important;
  font-size: clamp(2rem, 4vw, 3rem) !important;
  line-height: 1.06 !important;
}
.armely-invoice-lens-page .cta-copy .section-body {
  max-width: 680px !important;
  margin-bottom: 0 !important;
}
@media (max-width: 900px) {
  .armely-invoice-lens-page .hero { padding: 88px 24px 58px !important; }
  .armely-invoice-lens-page section,
  .armely-invoice-lens-page .vibe-section { padding: 56px 24px !important; }
  .armely-invoice-lens-page .spectrum-grid,
  .armely-invoice-lens-page .vibe-two-col,
  .armely-invoice-lens-page .why-two-col { grid-template-columns: 1fr !important; }
  .armely-invoice-lens-page .delivers-grid,
  .armely-invoice-lens-page .uc-grid { grid-template-columns: 1fr 1fr !important; }
  .armely-invoice-lens-page .cta-inner { padding: 44px 24px !important; grid-template-columns: 1fr !important; }
}
@media (max-width: 600px) {
  .armely-invoice-lens-page .hero h1 { font-size: clamp(2.15rem, 11vw, 3.2rem) !important; }
  .armely-invoice-lens-page .hero-eyebrow { flex-direction: column !important; align-items: flex-start !important; gap: 8px !important; }
  .armely-invoice-lens-page .hero-actions a { width: 100% !important; text-align: center !important; }
  .armely-invoice-lens-page .delivers-grid,
  .armely-invoice-lens-page .uc-grid { grid-template-columns: 1fr !important; }
}



.armely-invoice-lens-page .cr-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:28px; margin-bottom:28px; }
.armely-invoice-lens-page .cr-card { background:#fff; border:1px solid var(--border); border-radius:14px; padding:22px; box-shadow:0 14px 36px rgba(18,47,82,0.08); }
.armely-invoice-lens-page .cr-label { display:flex; align-items:center; gap:9px; margin-bottom:10px; }
.armely-invoice-lens-page .cr-check { width:20px; height:20px; display:flex; align-items:center; justify-content:center; background:var(--blue-dim); border:1px solid var(--blue-dim2); border-radius:50%; flex-shrink:0; color:var(--blue); }
.armely-invoice-lens-page .cr-check .icon-svg { width:11px; height:11px; stroke-width:3; }
.armely-invoice-lens-page .cr-industry { font-size:0.875rem; font-weight:700; color:#162b49; }
.armely-invoice-lens-page .cr-desc { font-size:0.84rem; color:var(--text-muted); line-height:1.65; margin:0; }
.armely-invoice-lens-page .cr-cta { text-align:center; margin-top:8px; }
.armely-invoice-lens-page .cr-btn { display:inline-flex; align-items:center; gap:10px; background:#1a2e52; color:#fff; border-radius:8px; padding:13px 28px; text-decoration:none; font-size:0.875rem; font-weight:600; }
.armely-invoice-lens-page .cr-btn:hover { background:var(--blue); }
.armely-invoice-lens-page .cr-btn .icon-svg { width:18px; height:18px; }
@media (max-width:900px) { .armely-invoice-lens-page .cr-grid { grid-template-columns:1fr 1fr; } }
@media (max-width:600px) { .armely-invoice-lens-page .cr-grid { grid-template-columns:1fr; } }

.armely-invoice-lens-page { --il: #C45E1A; --il-dim: rgba(196,94,26,0.09); --il-dim2: rgba(196,94,26,0.2); --il-light: #fff8f3; }

/* Hero */
.armely-invoice-lens-page .il-hero { background:linear-gradient(135deg,#1a0f06 0%,#2d1a0a 50%,#1f1208 100%); padding:48px 56px 40px; position:relative; overflow:hidden; }
.armely-invoice-lens-page .il-hero::before { content:''; position:absolute; top:-80px; right:-60px; width:360px; height:360px; border-radius:50%; background:rgba(196,94,26,0.1); pointer-events:none; }
.armely-invoice-lens-page .il-hero::after { content:''; position:absolute; bottom:-40px; left:35%; width:240px; height:240px; border-radius:50%; background:rgba(196,94,26,0.06); pointer-events:none; }
.armely-invoice-lens-page .il-hero-inner { max-width:1120px; margin:0 auto; display:grid; grid-template-columns:1fr 320px; gap:52px; align-items:center; position:relative; z-index:1; }
.armely-invoice-lens-page .il-product-pill { display:inline-flex; align-items:center; gap:7px; background:rgba(196,94,26,0.15); border:1px solid rgba(196,94,26,0.3); border-radius:999px; padding:4px 13px; margin-bottom:16px; }
.armely-invoice-lens-page .il-product-pill-dot { width:7px; height:7px; border-radius:50%; background:#f0a060; }
.armely-invoice-lens-page .il-product-pill-text { font-size:0.68rem; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:#f0a060; }
.armely-invoice-lens-page .il-hero h1 { font-size:clamp(1.6rem,3vw,2.4rem); font-weight:800; color:#fff; line-height:1.1; letter-spacing:-0.03em; margin-bottom:14px; }
.armely-invoice-lens-page .il-hero h1 .hl { color:#f0a060; opacity:1; }
.armely-invoice-lens-page .il-hero-sub { font-size:0.925rem; font-weight:300; color:rgba(255,255,255,0.65); line-height:1.78; max-width:520px; margin-bottom:28px; }
.armely-invoice-lens-page .il-live-badge { display:inline-flex; align-items:center; gap:6px; background:rgba(29,158,117,0.15); border:1px solid rgba(29,158,117,0.3); border-radius:999px; padding:6px 14px; margin-bottom:28px; }
.armely-invoice-lens-page .il-live-dot { width:7px; height:7px; border-radius:50%; background:#1D9E75; animation:il-pulse 2s infinite; }
@keyframes il-pulse { 0%,100% { opacity:1; } 50% { opacity:0.4; } }
.armely-invoice-lens-page .il-live-text { font-size:0.72rem; font-weight:600; color:#1D9E75; }

/* Workflow card (right side of hero) */
.armely-invoice-lens-page .il-workflow-card { background:rgba(8,18,42,0.85); border:1px solid rgba(255,255,255,0.12); border-radius:14px; overflow:hidden; }
.armely-invoice-lens-page .il-wf-head { padding:12px 18px; border-bottom:1px solid rgba(255,255,255,0.07); display:flex; align-items:center; gap:7px; }
.armely-invoice-lens-page .il-wf-dot { width:6px; height:6px; border-radius:50%; background:rgba(255,255,255,0.25); }
.armely-invoice-lens-page .il-wf-label { font-size:0.6rem; font-weight:700; letter-spacing:0.14em; text-transform:uppercase; color:rgba(255,255,255,0.3); }
.armely-invoice-lens-page .il-wf-step { padding:10px 18px; border-bottom:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; gap:10px; }
.armely-invoice-lens-page .il-wf-step:last-of-type { border-bottom:none; }
.armely-invoice-lens-page .il-wf-num { width:20px; height:20px; border-radius:50%; background:rgba(196,94,26,0.2); border:1px solid rgba(196,94,26,0.3); display:flex; align-items:center; justify-content:center; font-size:0.6rem; font-weight:800; color:#f0a060; flex-shrink:0; }
.armely-invoice-lens-page .il-wf-step-text { font-size:0.75rem; color:rgba(255,255,255,0.6); line-height:1.4; }
.armely-invoice-lens-page .il-wf-step-text span { color:rgba(196,94,26,0.8); font-weight:600; }
.armely-invoice-lens-page .il-wf-foot { padding:10px 18px; background:rgba(196,94,26,0.08); border-top:1px solid rgba(196,94,26,0.2); font-size:0.7rem; font-weight:600; color:#f0a060; text-align:center; }

/* Problem strip */
.armely-invoice-lens-page .il-strip { background:#fff; border-top:3px solid var(--il); border-bottom:1px solid var(--border); }
.armely-invoice-lens-page .il-strip-inner { max-width:1120px; margin:0 auto; padding:0 56px; display:grid; grid-template-columns:1fr 1fr 1fr; }
.armely-invoice-lens-page .il-strip-item { padding:22px 24px 22px 0; border-right:1px solid var(--border); }
.armely-invoice-lens-page .il-strip-item:last-child { border-right:none; padding-right:0; padding-left:24px; }
.armely-invoice-lens-page .il-strip-item:nth-child(2) { padding-left:24px; }
.armely-invoice-lens-page .il-strip-lbl { font-size:0.62rem; font-weight:700; text-transform:uppercase; letter-spacing:0.14em; color:var(--il); margin-bottom:5px; }
.armely-invoice-lens-page .il-strip-text { font-size:0.875rem; font-weight:600; color:#162b49; line-height:1.55; }

/* Feature grid */
.armely-invoice-lens-page .il-feat-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-top:32px; }
.armely-invoice-lens-page .il-feat-card { background:#fff; border:1px solid var(--border); border-radius:14px; padding:22px; box-shadow:0 8px 24px rgba(18,47,82,0.06); }
.armely-invoice-lens-page .il-feat-icon { width:40px; height:40px; border-radius:11px; background:var(--il-dim); border:1px solid var(--il-dim2); display:flex; align-items:center; justify-content:center; color:var(--il); margin-bottom:12px; }
.armely-invoice-lens-page .il-feat-icon .icon-svg { width:20px; height:20px; }
.armely-invoice-lens-page .il-feat-title { font-size:0.875rem; font-weight:700; color:#162b49; margin-bottom:5px; }
.armely-invoice-lens-page .il-feat-desc { font-size:0.8rem; color:var(--text-muted); line-height:1.65; }
.armely-invoice-lens-page .il-feat-badge { display:inline-block; font-size:0.6rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; background:rgba(186,117,23,0.1); color:#854F0B; border:1px solid rgba(186,117,23,0.2); border-radius:4px; padding:2px 7px; margin-top:8px; }

/* Steps */
.armely-invoice-lens-page .il-steps { display:grid; grid-template-columns:repeat(4,1fr); gap:0; margin-top:32px; position:relative; }
.armely-invoice-lens-page .il-steps::before { content:''; position:absolute; top:21px; left:12%; right:12%; height:1px; background:var(--il-dim2); z-index:0; }
.armely-invoice-lens-page .il-step { text-align:center; padding:0 10px; position:relative; z-index:1; }
.armely-invoice-lens-page .il-step-num { width:42px; height:42px; border-radius:50%; background:var(--il); color:#fff; font-size:0.82rem; font-weight:800; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; }
.armely-invoice-lens-page .il-step-title { font-size:0.82rem; font-weight:700; color:#162b49; margin-bottom:4px; }
.armely-invoice-lens-page .il-step-desc { font-size:0.76rem; color:var(--text-muted); line-height:1.6; }

/* Compare */
.armely-invoice-lens-page .il-compare-wrap { margin-top:32px; border:1px solid var(--border); border-radius:14px; overflow:hidden; }
.armely-invoice-lens-page .il-compare-head { display:grid; grid-template-columns:1.6fr 1fr 1fr 1fr; background:#f6f8fc; border-bottom:1px solid var(--border); }
.armely-invoice-lens-page .il-compare-row { display:grid; grid-template-columns:1.6fr 1fr 1fr 1fr; border-bottom:1px solid var(--border); }
.armely-invoice-lens-page .il-compare-row:last-child { border-bottom:none; }
.armely-invoice-lens-page .il-compare-cell { padding:12px 16px; font-size:0.8rem; color:var(--text-body); border-right:1px solid var(--border); display:flex; align-items:center; }
.armely-invoice-lens-page .il-compare-cell:last-child { border-right:none; }
.armely-invoice-lens-page .il-compare-head .il-compare-cell { font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--text-muted); }
.armely-invoice-lens-page .il-compare-head .il-act { background:var(--il-dim); color:var(--il); }
.armely-invoice-lens-page .il-compare-row .il-act { background:rgba(196,94,26,0.02); }
.armely-invoice-lens-page .il-row-label { font-weight:500; color:#162b49; font-size:0.82rem; }
.armely-invoice-lens-page .il-yes { color:#1D9E75; font-weight:700; font-size:0.82rem; }
.armely-invoice-lens-page .il-no { color:#cbd5e1; font-size:0.82rem; }
.armely-invoice-lens-page .il-road { color:var(--text-muted); font-size:0.76rem; font-style:italic; }

/* Why section */
.armely-invoice-lens-page .il-why-grid { display:grid; grid-template-columns:1fr 400px; gap:56px; align-items:start; }
.armely-invoice-lens-page .il-why-items { margin-top:24px; display:flex; flex-direction:column; }
.armely-invoice-lens-page .il-why-item { display:flex; align-items:flex-start; gap:14px; padding:16px 0; border-bottom:1px solid var(--border); }
.armely-invoice-lens-page .il-why-item:last-child { border-bottom:none; }
.armely-invoice-lens-page .il-why-icon { width:36px; height:36px; border-radius:9px; background:var(--il-dim); border:1px solid var(--il-dim2); display:flex; align-items:center; justify-content:center; flex-shrink:0; color:var(--il); }
.armely-invoice-lens-page .il-why-icon .icon-svg { width:17px; height:17px; }
.armely-invoice-lens-page .il-why-item div strong { display:block; font-size:0.875rem; font-weight:700; color:#162b49; margin-bottom:3px; }
.armely-invoice-lens-page .il-why-item div p { font-size:0.8rem; color:var(--text-muted); line-height:1.65; margin:0; }
.armely-invoice-lens-page .il-status-card { background:#fff; border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:0 8px 24px rgba(18,47,82,0.08); position:sticky; top:24px; }
.armely-invoice-lens-page .il-status-head { background:var(--il); padding:14px 20px; font-size:0.72rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:#fff; }
.armely-invoice-lens-page .il-status-body { padding:18px 20px; }
.armely-invoice-lens-page .il-status-row { display:flex; align-items:flex-start; gap:10px; padding:10px 0; border-bottom:1px solid var(--border); }
.armely-invoice-lens-page .il-status-row:last-child { border-bottom:none; }
.armely-invoice-lens-page .il-status-icon { width:28px; height:28px; border-radius:7px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.armely-invoice-lens-page .il-status-icon.live { background:rgba(29,158,117,0.1); color:#1D9E75; }
.armely-invoice-lens-page .il-status-icon.road { background:rgba(186,117,23,0.1); color:#854F0B; }
.armely-invoice-lens-page .il-status-icon .icon-svg { width:13px; height:13px; }
.armely-invoice-lens-page .il-status-label { font-size:0.72rem; font-weight:700; color:#162b49; margin-bottom:2px; }
.armely-invoice-lens-page .il-status-desc { font-size:0.68rem; color:var(--text-muted); line-height:1.5; }
.armely-invoice-lens-page .il-status-tag { display:inline-block; font-size:0.6rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; border-radius:4px; padding:1px 6px; }
.armely-invoice-lens-page .il-status-tag.live { background:rgba(29,158,117,0.1); color:#1D9E75; }
.armely-invoice-lens-page .il-status-tag.road { background:rgba(186,117,23,0.1); color:#854F0B; }

/* Hero CTA refresh */
.armely-invoice-lens-page .il-hero .hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 28px;
}
.armely-invoice-lens-page .il-hero .il-cta {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
  min-height: 40px;
  padding: 0 16px;
  border-radius: 999px;
  font-size: 0.76rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  text-transform: none;
  text-decoration: none;
  white-space: nowrap;
  transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease, border-color 0.18s ease;
}
.armely-invoice-lens-page .il-hero .il-cta .icon-svg {
  width: 12px;
  height: 12px;
  flex-shrink: 0;
}
.armely-invoice-lens-page .il-hero .il-cta-primary {
  background: linear-gradient(135deg, #f08a3d 0%, #c45e1a 100%);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 12px 24px rgba(196, 94, 26, 0.28);
}
.armely-invoice-lens-page .il-hero .il-cta-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 16px 30px rgba(196, 94, 26, 0.34);
}
.armely-invoice-lens-page .il-hero .il-cta-secondary {
  background: rgba(255, 255, 255, 0.06);
  color: rgba(255, 255, 255, 0.9);
  border: 1px solid rgba(255, 255, 255, 0.16);
  backdrop-filter: blur(10px);
}
.armely-invoice-lens-page .il-hero .il-cta-secondary:hover {
  transform: translateY(-2px);
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.28);
}

@media (max-width:960px) {
  .armely-invoice-lens-page .il-hero-inner { grid-template-columns:1fr; }
  .armely-invoice-lens-page .il-workflow-card { max-width:400px; }
  .armely-invoice-lens-page .il-feat-grid { grid-template-columns:1fr 1fr; }
  .armely-invoice-lens-page .il-steps { grid-template-columns:1fr 1fr; gap:24px; }
  .armely-invoice-lens-page .il-steps::before { display:none; }
  .armely-invoice-lens-page .il-strip-inner { grid-template-columns:1fr; }
  .armely-invoice-lens-page .il-strip-item { border-right:none; border-bottom:1px solid var(--border); padding:18px 0; }
  .armely-invoice-lens-page .il-strip-item:last-child { border-bottom:none; }
  .armely-invoice-lens-page .il-why-grid { grid-template-columns:1fr; }
  .armely-invoice-lens-page .il-status-card { position:static; }
  .armely-invoice-lens-page .il-compare-wrap { overflow-x:auto; }
  .armely-invoice-lens-page .il-hero, .armely-invoice-lens-page .il-strip-inner { padding-left:24px; padding-right:24px; }
}
@media (max-width:600px) {
  .armely-invoice-lens-page .il-feat-grid { grid-template-columns:1fr; }
  .armely-invoice-lens-page .il-steps { grid-template-columns:1fr; }
  .armely-invoice-lens-page .il-hero .il-cta { width: 100%; }
}


/* Tight section spacing */
.armely-invoice-lens-page .spectrum,
.armely-invoice-lens-page .delivers,
.armely-invoice-lens-page .usecases,
.armely-invoice-lens-page .testimonials,
.armely-invoice-lens-page .why { padding-top:48px !important; padding-bottom:48px !important; }
.armely-invoice-lens-page .il-strip-item { padding-top:18px; padding-bottom:18px; }

</style>
@endpush

@section('content')
<div class="armely-invoice-lens-page">

<section class="il-hero">
  <div class="il-hero-inner">
    <div>
      <div class="il-product-pill">
        <span class="il-product-pill-dot"></span>
        <span class="il-product-pill-text">Oil and Gas Product</span>
      </div>
      <div class="hero-eyebrow-wrap">
        <span class="hero-eyebrow" style="background:rgba(196,94,26,0.15);border-color:rgba(196,94,26,0.35);color:#f0a060;">InvoiceLens</span>
        <span class="eyebrow-partner">Built by Armely on Microsoft Fabric</span>
      </div>
      <h1>Stop switching between OpenInvoice, <span class="hl">your spreadsheet, and your email.</span></h1>
      <div class="il-live-badge">
        <span class="il-live-dot"></span>
        <span class="il-live-text">Live in production with an oil and gas operator</span>
      </div>
      <p class="il-hero-sub">InvoiceLens gives Enverus OpenInvoice operators a single Power BI dashboard showing every pending invoice by vendor, AFE, and cost center. No email hunting. No manual tracking spreadsheet. No logging into OpenInvoice to find what is waiting.</p>
      <div class="hero-actions">
        <a href="#contact" class="il-cta il-cta-primary">Request a Demo <svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>
        <a href="#how-it-works" class="il-cta il-cta-secondary">See How It Works</a>
      </div>
    </div>

    <div class="il-workflow-card">
      <div class="il-wf-head">
        <span class="il-wf-dot"></span>
        <span class="il-wf-label">The workflow today</span>
      </div>
      <div class="il-wf-step">
        <div class="il-wf-num">1</div>
        <div class="il-wf-step-text">Log into <span>OpenInvoice</span>. Find what is pending one invoice at a time.</div>
      </div>
      <div class="il-wf-step">
        <div class="il-wf-num">2</div>
        <div class="il-wf-step-text">Switch to <span>Excel</span>. Cross-reference vendor, PO, and amount manually.</div>
      </div>
      <div class="il-wf-step">
        <div class="il-wf-num">3</div>
        <div class="il-wf-step-text">Search <span>email</span> for the original invoice to verify details.</div>
      </div>
      <div class="il-wf-step">
        <div class="il-wf-num">4</div>
        <div class="il-wf-step-text">Return to <span>OpenInvoice</span> to certify coding and approve.</div>
      </div>
      <div class="il-wf-step">
        <div class="il-wf-num">5</div>
        <div class="il-wf-step-text">Update <span>Excel</span> manually to record the approval.</div>
      </div>
      <div class="il-wf-foot">InvoiceLens replaces steps 1, 2, 3, and 5.</div>
    </div>
  </div>
</section>

<div class="il-strip">
  <div class="il-strip-inner">
    <div class="il-strip-item">
      <div class="il-strip-lbl">The problem</div>
      <div class="il-strip-text">AP teams process invoices across three systems with no single view of what is pending, what is stalled, or what looks wrong.</div>
    </div>
    <div class="il-strip-item">
      <div class="il-strip-lbl">The gap</div>
      <div class="il-strip-text">OpenInvoice manages the workflow. Your ERP holds the budget. Nothing connects them in a way your finance team can act on in real time.</div>
    </div>
    <div class="il-strip-item">
      <div class="il-strip-lbl">InvoiceLens</div>
      <div class="il-strip-text">One dashboard. Every pending invoice. Deployed inside your own Azure tenant on Microsoft Fabric. Live in days.</div>
    </div>
  </div>
</div>

<section class="spectrum">
  <div class="section-inner">
    <div class="section-eyebrow">What InvoiceLens Does</div>
    <h2 class="section-title">Five capabilities your OpenInvoice environment is missing.</h2>
    <p class="section-body">InvoiceLens is not a replacement for OpenInvoice or your ERP integration. It is the visibility layer that sits between them, giving AP teams, finance managers, and operations leadership what neither system provides alone.</p>
    <div class="il-feat-grid"><div class="il-feat-card">
      <div class="il-feat-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div>
      <div class="il-feat-title">All pending invoices in one view</div>
      <div class="il-feat-desc">Every invoice submitted to OpenInvoice appears automatically in a single Power BI dashboard. Filter by vendor, AFE, cost center, amount, or approval stage. No logging in to OpenInvoice to find what is waiting.</div>
    </div><div class="il-feat-card">
      <div class="il-feat-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg></div>
      <div class="il-feat-title">Replace the tracking spreadsheet</div>
      <div class="il-feat-desc">The Excel file your team uses to track vendors, PO numbers, amounts, and approval status is replaced by a live dashboard that updates automatically from OpenInvoice. No more manual entry.</div>
    </div><div class="il-feat-card">
      <div class="il-feat-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg></div>
      <div class="il-feat-title">Verify coding before you certify</div>
      <div class="il-feat-desc">See AFE and cost center coding for every invoice before opening it in OpenInvoice. Catch miscoding without clicking into each invoice individually. One trip to certify, not two.</div>
    </div><div class="il-feat-card">
      <div class="il-feat-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
      <div class="il-feat-title">Accrual and cash flow visibility</div>
      <div class="il-feat-desc">Know your total pending AP liability by AFE, well, or cost center before month-end close. Finance teams stop asking AP what is outstanding. Closer faster, with more accurate numbers.</div>
    </div><div class="il-feat-card">
      <div class="il-feat-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg></div>
      <div class="il-feat-title">Microsoft Fabric data platform</div>
      <div class="il-feat-desc">Your OpenInvoice data lands in a governed Fabric lakehouse inside your own Azure tenant. Live data, not a stale SQL export. Connected to your existing Power BI environment.</div>
    </div><div class="il-feat-card">
      <div class="il-feat-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 8V4H8"/><rect x="3" y="8" width="18" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M9 11v2"/><path d="M15 11v2"/></svg></div>
      <div class="il-feat-title">AI anomaly detection</div>
      <div class="il-feat-desc">Will automatically flag duplicate submissions and invoices that deviate from the vendor's historical pricing pattern before they reach AP. Being built now.</div>
      <span class="il-feat-badge">Roadmap</span>
    </div></div>
  </div>
</section>

<section class="delivers" id="how-it-works">
  <div class="section-inner">
    <div class="section-eyebrow">How It Works</div>
    <h2 class="section-title">Live in days. No changes to your existing workflow.</h2>
    <p class="section-body">InvoiceLens connects to your existing OpenInvoice environment without changing your ERP integration or AP approval process. Your historical tracking spreadsheet data is imported on day&nbsp;one.</p>
    <div class="il-steps"><div class="il-step">
      <div class="il-step-num">1</div>
      <div class="il-step-title">Connect OpenInvoice</div>
      <div class="il-step-desc">Armely connects InvoiceLens to your OpenInvoice environment via API. No changes to your existing ERP integration or AP workflow.</div>
    </div><div class="il-step">
      <div class="il-step-num">2</div>
      <div class="il-step-title">Deploy on Fabric</div>
      <div class="il-step-desc">Your OpenInvoice data lands in a governed Microsoft Fabric lakehouse inside your own Azure tenant. You own it entirely.</div>
    </div><div class="il-step">
      <div class="il-step-num">3</div>
      <div class="il-step-title">Configure Dashboards</div>
      <div class="il-step-desc">Armely configures Power BI dashboards for spend visibility, accrual tracking, workflow reporting, and coding review.</div>
    </div><div class="il-step">
      <div class="il-step-num">4</div>
      <div class="il-step-title">Go Live</div>
      <div class="il-step-desc">First live dashboard within one week of kickoff. Your existing tracking spreadsheet data is imported on day&nbsp;one.</div>
    </div></div>
  </div>
</section>

<section class="usecases">
  <div class="section-inner">
    <div class="section-eyebrow">How InvoiceLens Compares</div>
    <h2 class="section-title">What InvoiceLens adds to your existing OpenInvoice workflow.</h2>
    <p class="section-body">OpenInvoice manages the approval workflow. Your manual spreadsheet tracks the rest. InvoiceLens replaces the spreadsheet and adds visibility that OpenInvoice alone does not provide.</p>
    <div class="il-compare-wrap">
  <div class="il-compare-head">
    <div class="il-compare-cell">Capability</div>
    <div class="il-compare-cell il-act">InvoiceLens</div>
    <div class="il-compare-cell">OpenInvoice alone</div>
    <div class="il-compare-cell">Manual spreadsheet</div>
  </div><div class="il-compare-row">
      <div class="il-compare-cell il-row-label">All pending invoices in one dashboard</div>
      <div class="il-compare-cell il-act"><span class="il-yes">Yes</span></div>
      <div class="il-compare-cell"><span class="il-no">No</span></div>
      <div class="il-compare-cell"><span class="il-no">No</span></div>
    </div><div class="il-compare-row">
      <div class="il-compare-cell il-row-label">Replace the manual tracking spreadsheet</div>
      <div class="il-compare-cell il-act"><span class="il-yes">Yes</span></div>
      <div class="il-compare-cell"><span class="il-no">No</span></div>
      <div class="il-compare-cell"><span class="il-no">No</span></div>
    </div><div class="il-compare-row">
      <div class="il-compare-cell il-row-label">Coding visibility before certification</div>
      <div class="il-compare-cell il-act"><span class="il-yes">Yes</span></div>
      <div class="il-compare-cell"><span class="il-no">No</span></div>
      <div class="il-compare-cell"><span class="il-no">No</span></div>
    </div><div class="il-compare-row">
      <div class="il-compare-cell il-row-label">Accrual and cash flow tracking</div>
      <div class="il-compare-cell il-act"><span class="il-yes">Yes</span></div>
      <div class="il-compare-cell"><span class="il-road">Manual</span></div>
      <div class="il-compare-cell"><span class="il-road">Manual</span></div>
    </div><div class="il-compare-row">
      <div class="il-compare-cell il-row-label">Export data to Excel or CSV</div>
      <div class="il-compare-cell il-act"><span class="il-yes">Yes</span></div>
      <div class="il-compare-cell"><span class="il-yes">Yes</span></div>
      <div class="il-compare-cell"><span class="il-yes">Yes</span></div>
    </div><div class="il-compare-row">
      <div class="il-compare-cell il-row-label">Import existing tracking spreadsheet</div>
      <div class="il-compare-cell il-act"><span class="il-yes">Yes</span></div>
      <div class="il-compare-cell"><span class="il-no">No</span></div>
      <div class="il-compare-cell"><span class="il-yes">Yes</span></div>
    </div><div class="il-compare-row">
      <div class="il-compare-cell il-row-label">OpenInvoice to ERP integration</div>
      <div class="il-compare-cell il-act"><span class="il-yes">Yes</span></div>
      <div class="il-compare-cell"><span class="il-no">No</span></div>
      <div class="il-compare-cell"><span class="il-no">No</span></div>
    </div><div class="il-compare-row">
      <div class="il-compare-cell il-row-label">Data in your own Azure tenant</div>
      <div class="il-compare-cell il-act"><span class="il-yes">Yes</span></div>
      <div class="il-compare-cell"><span class="il-no">No</span></div>
      <div class="il-compare-cell"><span class="il-no">No</span></div>
    </div><div class="il-compare-row">
      <div class="il-compare-cell il-row-label">AI anomaly and duplicate detection</div>
      <div class="il-compare-cell il-act"><span class="il-road">Roadmap</span></div>
      <div class="il-compare-cell"><span class="il-no">No</span></div>
      <div class="il-compare-cell"><span class="il-no">No</span></div>
    </div></div>
  </div>
</section>

<section class="why">
  <div class="section-inner">
    <div class="il-why-grid">
      <div>
        <div class="section-eyebrow">Why InvoiceLens</div>
        <h2 class="section-title" style="text-align:left;margin-left:0;">Built for oil and gas by people who know OpenInvoice.</h2>
        <div class="il-why-items"><div class="il-why-item">
        <div class="il-why-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
        <div><strong>Your data never leaves your environment</strong><p>InvoiceLens deploys inside your own Azure tenant. Armely never holds or hosts your invoice data. You own it, you control it, and no one else can access it.</p></div>
      </div><div class="il-why-item">
        <div class="il-why-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg></div>
        <div><strong>Built on Microsoft Fabric, not a SQL export</strong><p>Your OpenInvoice data is live in a governed Fabric lakehouse, not a flat SQL Server dump that goes stale between updates. Every dashboard reflects current data.</p></div>
      </div><div class="il-why-item">
        <div class="il-why-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></div>
        <div><strong>Works alongside your existing ERP integration</strong><p>If you already have an OpenInvoice-to-ERP integration in place, InvoiceLens adds visibility on top. You do not need to replace anything to get the dashboards.</p></div>
      </div><div class="il-why-item">
        <div class="il-why-icon"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg></div>
        <div><strong>Implemented and supported by the team that built it</strong><p>InvoiceLens is an Armely product, not a consulting engagement handed to a support queue. If something needs changing, you call the people who built it.</p></div>
      </div></div>
      </div>
      <div>
        <div class="il-status-card">
          <div class="il-status-head">What is live today vs roadmap</div>
          <div class="il-status-body"><div class="il-status-row">
        <div class="il-status-icon live"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
        <div>
          <div class="il-status-label">Invoice visibility dashboards <span class="il-status-tag live">LIVE</span></div>
          <div class="il-status-desc">Live and running in production. Power BI dashboards pulling from OpenInvoice via API into a Fabric lakehouse.</div>
        </div>
      </div><div class="il-status-row">
        <div class="il-status-icon live"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
        <div>
          <div class="il-status-label">Tracking spreadsheet replacement <span class="il-status-tag live">LIVE</span></div>
          <div class="il-status-desc">Live. AP teams replace their manual Excel tracking with a dashboard that updates automatically.</div>
        </div>
      </div><div class="il-status-row">
        <div class="il-status-icon live"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
        <div>
          <div class="il-status-label">Accrual and cash flow reporting <span class="il-status-tag live">LIVE</span></div>
          <div class="il-status-desc">Live. Pending AP liability visible by AFE and cost center before month-end close.</div>
        </div>
      </div><div class="il-status-row">
        <div class="il-status-icon live"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
        <div>
          <div class="il-status-label">Data export to Excel and CSV <span class="il-status-tag live">LIVE</span></div>
          <div class="il-status-desc">Live. Export all invoice data at any time with one click.</div>
        </div>
      </div><div class="il-status-row">
        <div class="il-status-icon live"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg></div>
        <div>
          <div class="il-status-label">Historical data import <span class="il-status-tag live">LIVE</span></div>
          <div class="il-status-desc">Live. Import your existing tracking spreadsheet on day&nbsp;one.</div>
        </div>
      </div><div class="il-status-row">
        <div class="il-status-icon road"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        <div>
          <div class="il-status-label">AI anomaly and duplicate detection <span class="il-status-tag road">ROADMAP</span></div>
          <div class="il-status-desc">In development. Will flag duplicate submissions and pricing outliers automatically.</div>
        </div>
      </div><div class="il-status-row">
        <div class="il-status-icon road"><svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
        <div>
          <div class="il-status-label">SaaS hosted deployment <span class="il-status-tag road">ROADMAP</span></div>
          <div class="il-status-desc">In development. Armely-hosted multi-tenant option for operators without Azure infrastructure.</div>
        </div>
      </div></div>
        </div>
      </div>
    </div>
  </div>
</section>



<section class="cta-section" id="contact">
  <div class="cta-inner">
    <div class="cta-copy">
      <div class="section-eyebrow">Request a Demo</div>
      <h2 class="section-title" style="text-align:left;margin-left:0;">See your OpenInvoice data in a live InvoiceLens dashboard.</h2>
      <p class="section-body" style="text-align:left;margin-left:0;">Armely connects to your OpenInvoice environment, configures a dashboard with your own data, and walks you through what InvoiceLens shows your AP and finance team in 30 minutes.</p>
    </div>
    <div class="cta-form-wrap">
      @include('partials.service-contact-form', [
        'serviceContact' => [
          'surface' => 'card',
          'title' => 'Book Your Free Assessment',
          'subtitle' => 'Tell us about your situation.',
          'button_label' => 'Request Free Assessment',
          'note' => 'A member of the Armely energy team will follow up within one business day.',
          'options' => [
            'Need a live InvoiceLens demo',
            'Need help with OpenInvoice visibility',
            'Need ERP or AP workflow pricing',
            'Not sure, need a recommendation',
          ],
        ],
      ])
    </div>
  </div>
</section>

</div>
@endsection

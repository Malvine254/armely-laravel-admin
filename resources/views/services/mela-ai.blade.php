@extends('layouts.public')

@php($isMeetingAssistant = $isMeetingAssistant ?? false)
@section('title', $isMeetingAssistant ? 'Mela Meeting Assistant | Microsoft Teams Meeting Automation' : 'Mela AI | AI Products Built for Modern Work')
@section('meta_description', $isMeetingAssistant ? 'Mela automates meeting capture, Microsoft Planner task syncing, and Outlook summaries across Microsoft 365.' : 'Explore the Mela AI product family from Armely—purpose-built AI assistants that turn everyday work into intelligent, automated workflows.')
@section('meta_keywords', $isMeetingAssistant ? 'Mela AI, Microsoft Teams meeting assistant, Microsoft Planner, meeting automation, Microsoft 365' : 'Mela AI, Armely AI products, enterprise AI assistants, workflow automation, Microsoft 365 AI')
@section('canonical_url', $isMeetingAssistant ? route('mela-meeting-assistant') : route('mela-ai'))
@section('meta_image', asset('images/logo/mela-recorder.png'))
@section('meta_image_alt', 'Mela AI Meeting Assistant')
@section('favicon', asset('images/logo/mela-recorder.png'))

@push('head')
<meta name="robots" content="index,follow">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $isMeetingAssistant ? 'Mela Meeting Assistant | Microsoft Teams Meeting Automation' : 'Mela AI | AI Products Built for Modern Work' }}">
<meta property="og:description" content="{{ $isMeetingAssistant ? 'Automate post-meeting execution across Microsoft 365 with Mela AI.' : 'Explore purpose-built Mela AI assistants for meetings, knowledge, and modern work.' }}">
<meta property="og:url" content="{{ $isMeetingAssistant ? route('mela-meeting-assistant') : route('mela-ai') }}">
<meta property="og:site_name" content="Armely">
<meta name="twitter:card" content="summary">
@endpush

@push('styles')
<style>
.mela-page {
    --mela-primary: #2f5597;
    --mela-primary-hover: #1f447f;
    --mela-accent: #5d82c4;
    --mela-dark: #142b52;
    --mela-light: #f4f7fc;
    --mela-border: #dbe5f3;
    --mela-text: #1a2540;
    --mela-muted: #5b6780;
    color: var(--mela-text);
    font-family: Poppins, sans-serif;
    font-size: 16px;
    line-height: 1.7;
}
.mela-page * { box-sizing: border-box; }
.mela-page .mela-container { width: min(1140px, calc(100% - 40px)); margin: 0 auto; }
.mela-page .mela-hero { position: relative; overflow: hidden; background: radial-gradient(circle at 82% 18%, rgba(106,145,211,.3), transparent 28%), linear-gradient(135deg, #142b52, #2f5597); color: #fff; padding: 58px 0; }
.mela-page .mela-hero::before { content: ""; position: absolute; width: 420px; height: 420px; left: -230px; bottom: -300px; border: 1px solid rgba(255,255,255,.12); border-radius: 50%; }
.mela-page .mela-hero-grid { position: relative; z-index: 1; }
.mela-page .mela-hero-grid, .mela-page .mela-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: center; }
.mela-page .mela-badge { display: inline-flex; align-items: center; gap: 9px; padding: 9px 16px; margin-bottom: 20px; border: 1px solid rgba(255,255,255,.28); border-radius: 30px; background: rgba(255,255,255,.12); color: #e9f2ff; font-size: .92rem; font-weight: 600; backdrop-filter: blur(8px); }
.mela-page .mela-badge i { font-size: 1rem; }
.mela-page h1 { margin: 0 0 18px; color: #fff; font-size: clamp(2.25rem, 5vw, 3.45rem); line-height: 1.08; letter-spacing: -.035em; }
.mela-page .mela-hero h1 { max-width: 620px; font-size: clamp(2rem, 3.5vw, 2.8rem); line-height: 1.14; letter-spacing: -.025em; }
.mela-page .mela-hero-copy > p { max-width: 640px; margin: 0 0 28px; color: #d4deed; font-size: 1.16rem; line-height: 1.75; }
.mela-page .mela-btn { display: inline-flex; min-height: 50px; align-items: center; justify-content: center; gap: 10px; padding: 13px 26px; margin: 0 8px 10px 0; border: 2px solid transparent; border-radius: 8px; font-family: inherit; font-size: 1rem; font-weight: 600; line-height: 1.3; text-decoration: none; transition: .2s ease; }
.mela-page .mela-btn i { font-size: .95rem; }
.mela-page .mela-hero .mela-btn-primary { background: #fff; color: var(--mela-primary); box-shadow: 0 8px 22px rgba(5,21,48,.2); }
.mela-page .mela-hero .mela-btn-primary:hover { background: #f3f7ff; color: var(--mela-primary-hover); }
.mela-page .mela-btn-primary { background: var(--mela-primary); color: #fff; }
.mela-page .mela-btn-primary:hover { background: var(--mela-primary-hover); color: #fff; transform: translateY(-1px); }
.mela-page .mela-btn-secondary { border-color: #fff; color: #fff; }
.mela-page .mela-btn-secondary:hover { background: rgba(255,255,255,.1); color: #fff; }
.mela-page .mela-note { margin-top: 3px !important; color: #d4deed !important; font-size: .9rem !important; }
.mela-page .mela-media { overflow: hidden; border: 1px solid rgba(255,255,255,.3); border-radius: 20px; background: #091a36; box-shadow: 0 24px 55px rgba(5,21,48,.42); }
.mela-page .mela-media-bar { display: flex; min-height: 54px; align-items: center; justify-content: space-between; gap: 16px; padding: 11px 16px; border-bottom: 1px solid rgba(255,255,255,.12); background: rgba(7,23,48,.96); color: #fff; }
.mela-page .mela-media-title { display: flex; min-width: 0; align-items: center; gap: 10px; font-size: .9rem; font-weight: 600; }
.mela-page .mela-media-title i { display: grid; width: 30px; height: 30px; flex: 0 0 30px; place-items: center; border-radius: 8px; background: rgba(255,255,255,.12); color: #dbe8fb; }
.mela-page .mela-media-title span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.mela-page .mela-media-provider { flex: 0 0 auto; padding: 5px 9px; border: 1px solid rgba(255,255,255,.16); border-radius: 999px; color: #c8d7eb; font-size: .72rem; font-weight: 600; }
.mela-page .mela-player { position: relative; width: 100%; aspect-ratio: 16 / 9; background: #061226; }
.mela-page .mela-player iframe, .mela-page .mela-player video { position: absolute; inset: 0; width: 100%; height: 100%; display: block; border: 0; background: #061226; object-fit: contain; }
.mela-page .mela-video-placeholder { display: grid; width: 100%; height: 100%; padding: 40px; place-items: center; background: radial-gradient(circle at 50% 25%, rgba(89,129,196,.36), transparent 38%), linear-gradient(145deg, #0d2144, #183c70); text-align: center; }
.mela-page .mela-video-placeholder-icon { display: inline-grid; width: 78px; height: 78px; margin-bottom: 18px; place-items: center; border: 1px solid rgba(255,255,255,.35); border-radius: 50%; background: rgba(255,255,255,.14); color: #fff; font-size: 1.65rem; box-shadow: 0 10px 28px rgba(0,0,0,.18); }
.mela-page .mela-video-placeholder strong { display: block; color: #fff; font-size: 1.15rem; }
.mela-page .mela-video-placeholder span { display: block; margin-top: 5px; color: #c8d7eb; font-size: .95rem; }
.mela-page .mela-trust { display: flex; justify-content: center; flex-wrap: wrap; gap: 14px 32px; padding: 20px; border-bottom: 1px solid #dce5f2; background: #fff; color: var(--mela-text); text-align: center; font-size: .96rem; box-shadow: 0 5px 18px rgba(30,58,109,.06); }
.mela-page .mela-trust i { margin-right: 8px; color: var(--mela-primary); font-size: 1rem; }
.mela-page .mela-trust span { white-space: nowrap; }
.mela-page .mela-section { padding: 64px 0; border-bottom: 1px solid var(--mela-border); }
.mela-page .mela-section-tag { color: var(--mela-primary); font-size: .9rem; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; }
.mela-page .mela-title { margin: 9px 0 22px; color: var(--mela-text); font-size: clamp(1.8rem, 4vw, 2.35rem); line-height: 1.2; }
.mela-page .mela-feature-title { max-width: 780px; margin-inline: auto; font-size: clamp(1.55rem, 2.6vw, 2rem); line-height: 1.3; text-align: center !important; }
.mela-page .mela-muted { color: var(--mela-muted); }
.mela-page .mela-features { padding: 0; margin: 0; list-style: none; }
.mela-page .mela-features li { position: relative; padding-left: 34px; margin-bottom: 17px; }
.mela-page .mela-features li > i { position: absolute; left: 0; top: 4px; display: grid; width: 22px; height: 22px; place-items: center; border-radius: 50%; background: #e9f0fb; color: var(--mela-primary); font-size: .7rem; }
.mela-page code { padding: 2px 7px; border-radius: 4px; background: #ebecf0; color: var(--mela-primary); font-weight: 700; }
.mela-page .mela-roadmap { display: flex; gap: 12px; align-items: flex-start; margin-top: 22px; padding: 17px 19px; border: 1px solid #cddbf0; border-radius: 10px; background: #f5f8fd; color: var(--mela-muted); font-size: .96rem; }
.mela-page .mela-roadmap > i { margin-top: 3px; color: var(--mela-primary); }
.mela-page .mela-roadmap a { color: var(--mela-primary); font-weight: 700; text-decoration: none; }
.mela-page .mela-task-card { max-width: 440px; margin: 0 auto; overflow: hidden; border: 1px solid #e1dfdd; border-radius: 10px; background: #fff; box-shadow: 0 6px 22px rgba(9,30,66,.12); }
.mela-page .mela-task-head { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-bottom: 1px solid #e1dfdd; background: #f3f2f1; }
.mela-page .mela-avatar { display: grid; width: 36px; height: 36px; place-items: center; border-radius: 50%; background: var(--mela-primary); color: #fff; font-size: .9rem; font-weight: 700; }
.mela-page .mela-task-head strong, .mela-page .mela-task-head small { display: block; line-height: 1.3; }
.mela-page .mela-task-head small { color: #605e5c; }
.mela-page .mela-task-body { padding: 21px; }
.mela-page .mela-task-body h3 { margin: 0 0 6px; font-size: 1.2rem; }
.mela-page .mela-task-body > p { margin-bottom: 15px; color: #605e5c; font-size: .92rem; }
.mela-page .mela-task { padding: 12px; margin-bottom: 14px; border: 1px solid #e1dfdd; border-radius: 6px; background: #faf9f8; }
.mela-page .mela-task-row { display: flex; justify-content: space-between; margin-bottom: 8px; color: var(--mela-primary); font-size: .85rem; font-weight: 700; }
.mela-page .mela-task label { display: block; margin: 7px 0 4px; font-size: .85rem; font-weight: 600; }
.mela-page .mela-task input[type="text"] { width: 100%; padding: 8px 10px; border: 1px solid #8a8886; border-radius: 5px; background: #fff; color: #201f1e; font-family: inherit; font-size: .9rem; }
.mela-page .mela-task-actions { display: flex; gap: 10px; }
.mela-page .mela-task-actions button { min-height: 42px; padding: 10px 16px; border: 0; border-radius: 6px; color: #fff; font-family: inherit; font-size: .9rem; font-weight: 600; }
.mela-page .mela-task-actions button:first-child { flex: 1; background: var(--mela-primary); }
.mela-page .mela-task-actions button:last-child { background: #a80000; }
.mela-page .mela-action-preview { max-width: 560px; margin: 0 auto; padding: 10px; border: 1px solid var(--mela-border); border-radius: 18px; background: #fff; box-shadow: 0 18px 42px rgba(30,58,109,.14); }
.mela-page .mela-action-preview img { width: 100%; height: auto; display: block; border-radius: 11px; }
.mela-page .mela-steps, .mela-page .mela-deploy-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 17px; margin-top: 36px; }
.mela-page .mela-step, .mela-page .mela-deploy-card { padding: 24px 19px; border: 1px solid var(--mela-border); border-radius: 13px; background: #fff; box-shadow: 0 8px 24px rgba(30,58,109,.07); transition: transform .2s ease, box-shadow .2s ease; }
.mela-page .mela-step:hover, .mela-page .mela-deploy-card:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(30,58,109,.12); }
.mela-page .mela-step { text-align: center; }
.mela-page .mela-num { display: inline-grid; width: 32px; height: 32px; margin-bottom: 11px; place-items: center; border-radius: 50%; background: var(--mela-primary); color: #fff; font-size: .9rem; font-weight: 800; }
.mela-page .mela-step-icon { display: inline-grid; width: 52px; height: 52px; margin-bottom: 16px; place-items: center; border-radius: 14px; background: linear-gradient(135deg, #e7eef9, #f4f7fc); color: var(--mela-primary); font-size: 1.25rem; }
.mela-page .mela-step h3, .mela-page .mela-deploy-card h3 { margin: 0 0 8px; color: var(--mela-text); font-size: 1.08rem; }
.mela-page .mela-step p, .mela-page .mela-deploy-card p { margin: 0; color: var(--mela-muted); font-size: .95rem; }
.mela-page .mela-comparison { padding: 64px 0; background: var(--mela-light); }
.mela-page .mela-table-wrap { overflow-x: auto; border-radius: 10px; box-shadow: 0 4px 12px rgba(9,30,66,.08); }
.mela-page table { width: 100%; min-width: 760px; border-collapse: collapse; background: #fff; }
.mela-page th, .mela-page td { padding: 16px 20px; border-bottom: 1px solid var(--mela-border); text-align: left; vertical-align: top; }
.mela-page th { background: #ebecf0; font-size: .9rem; text-transform: uppercase; }
.mela-page td { font-size: .96rem; }
.mela-page .mela-highlight { background: #e6fcff; }
.mela-page .mela-faq { background: var(--mela-light); }
.mela-page .mela-faq-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 24px; }
.mela-page .mela-faq-card { padding: 24px; border: 1px solid var(--mela-border); border-radius: 13px; background: #fff; box-shadow: 0 7px 22px rgba(30,58,109,.06); }
.mela-page .mela-faq-card h3 i { margin-right: 9px; color: var(--mela-accent); }
.mela-page .mela-faq-card h3 { margin: 0 0 9px; color: var(--mela-primary); font-size: 1.12rem; }
.mela-page .mela-faq-card h3 i { font-size: 1.05rem; }
.mela-page .mela-faq-card p { margin: 0; color: var(--mela-muted); font-size: .98rem; }
.mela-page .mela-cta { margin-top: 52px; padding: 52px 20px 8px; border-top: 1px solid var(--mela-border); background: transparent; color: var(--mela-text); text-align: center; }
.mela-page .mela-cta h2 { margin: 0 0 10px; color: var(--mela-text); }
.mela-page .mela-cta p { margin-bottom: 24px; color: var(--mela-muted); }
.mela-page .mela-cta .mela-btn-primary { background: var(--mela-primary); color: #fff; box-shadow: 0 8px 20px rgba(47,85,151,.18); }
.mela-page .mela-cta .mela-btn-primary:hover { background: var(--mela-primary-hover); color: #fff; }
.mela-page .mela-cta .mela-btn-secondary { border-color: var(--mela-primary); background: #fff; color: var(--mela-primary); }
.mela-page .mela-cta .mela-btn-secondary:hover { background: #edf3fc; color: var(--mela-primary-hover); }
.mela-collection-hero { position: relative; overflow: hidden; padding: 104px 0 92px; background: radial-gradient(circle at 78% 22%, rgba(119,157,222,.32), transparent 28%), linear-gradient(135deg, #142b52, #2f5597); color: #fff; text-align: center; }
.mela-collection-hero::after { content: ""; position: absolute; width: 520px; height: 520px; right: -310px; bottom: -360px; border: 1px solid rgba(255,255,255,.15); border-radius: 50%; }
.mela-collection-hero .mela-container { position: relative; z-index: 1; }
.mela-collection-mark { display: inline-grid; width: 66px; height: 66px; margin-bottom: 22px; place-items: center; border: 1px solid rgba(255,255,255,.28); border-radius: 19px; background: rgba(255,255,255,.12); color: #fff; font-size: 1.7rem; backdrop-filter: blur(10px); }
.mela-collection-hero h1 { max-width: 820px; margin-inline: auto; }
.mela-collection-hero p { max-width: 720px; margin: 0 auto; color: #d8e4f6; font-size: 1.12rem; }
.mela-collection-intro { padding: 66px 0 26px; text-align: center; }
.mela-collection-intro .mela-title { max-width: 730px; margin-inline: auto; }
.mela-collection-intro p { max-width: 700px; margin: 0 auto; }
.mela-product-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px; padding: 28px 0 76px; }
.mela-product { position: relative; display: flex; min-height: 400px; flex-direction: column; overflow: hidden; padding: 32px; border: 1px solid var(--mela-border); border-radius: 18px; background: #fff; box-shadow: 0 12px 35px rgba(30,58,109,.08); transition: transform .25s ease, box-shadow .25s ease; }
.mela-product:hover { transform: translateY(-6px); box-shadow: 0 22px 45px rgba(30,58,109,.14); }
.mela-product-featured::before { content: ""; position: absolute; inset: 0 0 auto; height: 5px; background: linear-gradient(90deg, var(--mela-primary), #7395ce); }
.mela-product-icon { display: grid; width: 52px; height: 52px; margin-bottom: 22px; place-items: center; border-radius: 14px; background: #eaf0fa; color: var(--mela-primary); font-size: 1.25rem; }
.mela-product-status { position: absolute; top: 28px; right: 28px; padding: 6px 10px; border-radius: 30px; background: #e9f7f0; color: #14724b; font-size: .7rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
.mela-product-status.mela-coming-soon { background: #edf1f7; color: #667085; }
.mela-product h2 { margin: 0 0 8px; color: var(--mela-text); font-size: 1.5rem; }
.mela-product > p { color: var(--mela-muted); }
.mela-product-points { display: grid; gap: 10px; padding: 0; margin: 18px 0 25px; list-style: none; }
.mela-product-points li { display: flex; gap: 10px; color: #3e4c67; font-size: .9rem; }
.mela-product-points i { margin-top: 5px; color: var(--mela-primary); font-size: .72rem; }
.mela-product .mela-btn { align-self: flex-start; margin-top: auto; }
.mela-product-muted { background: linear-gradient(145deg, #fbfcfe, #f3f6fb); }
.mela-page .mela-service-hero { min-height: auto; display: flex; align-items: center; justify-content: center; padding: 86px 56px 70px; background: linear-gradient(135deg, #173b67 0%, #234f86 100%); }
.mela-page .mela-service-hero::before, .mela-page .mela-service-hero::after { display: none; }
.mela-page .mela-service-hero .mela-hero-grid { display: grid; width: min(1120px, 100%); margin: 0 auto; grid-template-columns: minmax(0, 1fr) minmax(360px, .9fr); gap: 48px; align-items: center; }
.mela-page .mela-service-hero .mela-hero-copy { max-width: 560px; }
.mela-page .mela-service-eyebrow { display: inline-flex; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 18px; }
.mela-page .mela-service-eyebrow .mela-badge { margin: 0; padding: 7px 14px; border: 1px solid rgba(255,255,255,.22); border-radius: 999px; background: rgba(255,255,255,.1); color: rgba(255,255,255,.88); font-size: .72rem; letter-spacing: .12em; text-transform: uppercase; }
.mela-page .mela-service-partner { color: rgba(255,255,255,.66); font-size: .78rem; }
.mela-page .mela-service-hero h1 { max-width: 900px; margin-bottom: 18px; font-size: clamp(1.75rem, 3.2vw, 2.7rem); font-weight: 800; line-height: 1.05; letter-spacing: -.04em; }
.mela-page .mela-service-hero .mela-hero-copy > p { max-width: 760px; margin-bottom: 28px; color: rgba(255,255,255,.82); font-size: 1rem; font-weight: 300; line-height: 1.7; }
.mela-page .mela-service-actions { display: flex; flex-wrap: wrap; gap: 12px; }
.mela-page .mela-service-actions .mela-btn { min-width: 190px; margin: 0; border-radius: 8px; padding: 14px 32px; font-size: .95rem; }
.mela-page .mela-service-hero .mela-hero-copy > .mela-note { margin: 14px 0 0; color: rgba(255,255,255,.72); font-size: .82rem; line-height: 1.55; }
.mela-page .mela-service-hero .mela-btn-primary { background: #2f5597; color: #fff; box-shadow: none; }
.mela-page .mela-service-hero .mela-btn-primary:hover { background: #4779bd; color: #fff; }
.mela-page .mela-service-hero .mela-btn-secondary { border-width: 1px; border-color: rgba(255,255,255,.25); color: rgba(255,255,255,.85); }
.mela-page .mela-service-hero .mela-media { width: 100%; margin: 0; }
.mela-page .mela-contact { padding: 72px 0; background: #eef3f9; border-top: 1px solid var(--mela-border); }
.mela-page .mela-contact-inner { display: grid; grid-template-columns: minmax(0, .9fr) minmax(420px, 1.1fr); gap: 64px; align-items: start; }
.mela-page .mela-contact-copy { padding-top: 18px; }
.mela-page .mela-contact-copy .mela-title { max-width: 520px; margin-bottom: 16px; }
.mela-page .mela-contact-copy > p { max-width: 520px; color: var(--mela-muted); }
.mela-page .mela-contact-points { display: grid; gap: 12px; margin: 28px 0 0; padding: 0; list-style: none; }
.mela-page .mela-contact-points li { display: flex; align-items: center; gap: 10px; color: var(--mela-text); font-size: .9rem; }
.mela-page .mela-contact-points i { color: var(--mela-primary); }
.mela-page .mela-contact-form { padding: 34px 32px; border: 1px solid var(--mela-border); border-radius: 14px; background: #fff; box-shadow: 0 14px 36px rgba(18,47,82,.08); }
.mela-page .mela-form-title { margin-bottom: 5px; color: var(--mela-text); font-size: 1.12rem; font-weight: 700; }
.mela-page .mela-form-sub { margin-bottom: 22px; color: var(--mela-muted); font-size: .85rem; }
.mela-page .mela-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 14px; }
.mela-page .mela-form-row { margin-bottom: 14px; }
.mela-page .mela-form-row-full { grid-column: 1 / -1; width: 100%; min-width: 0; }
.mela-page .mela-form-row label { display: block; margin-bottom: 6px; color: var(--mela-muted); font-size: .75rem; font-weight: 600; letter-spacing: .04em; }
.mela-page .mela-form-row input, .mela-page .mela-form-row select, .mela-page .mela-form-row textarea { width: 100%; padding: 11px 14px; border: 1px solid rgba(41,78,139,.18); border-radius: 7px; background: #fff; color: var(--mela-text); font: inherit; font-size: .875rem; outline: none; }
.mela-page .mela-form-row select { display: block; width: 100% !important; max-width: none !important; min-width: 0; }
.mela-page .mela-form-row .nice-select { display: block; float: none; width: 100% !important; max-width: none !important; min-width: 0; height: auto; min-height: 48px; padding: 11px 42px 11px 14px; border: 1px solid rgba(41,78,139,.18); border-radius: 7px; line-height: 24px; }
.mela-page .mela-form-row .nice-select .list { width: 100%; }
.mela-page .mela-form-row textarea { min-height: 112px; resize: vertical; }
.mela-page .mela-form-row input:focus, .mela-page .mela-form-row select:focus, .mela-page .mela-form-row textarea:focus { border-color: rgba(47,85,151,.55); box-shadow: 0 0 0 3px rgba(47,85,151,.08); }
.mela-page .mela-form-submit { width: 100%; padding: 14px; border: 0; border-radius: 8px; background: linear-gradient(135deg, #2f5597, #4477bd); color: #fff; font: inherit; font-size: .95rem; font-weight: 600; cursor: pointer; }
.mela-page .mela-form-submit:disabled { cursor: wait; opacity: .7; }
.mela-page .mela-form-note { margin-top: 12px; color: var(--mela-muted); font-size: .75rem; text-align: center; }
.mela-collection-cta { padding: 66px 20px; background: #f4f7fc; text-align: center; }
.mela-collection-cta h2 { color: var(--mela-text); }
.mela-collection-cta p { max-width: 620px; margin: 0 auto 22px; color: var(--mela-muted); }
@media (max-width: 900px) {
    .mela-page .mela-steps, .mela-page .mela-deploy-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 767px) {
    .mela-page .mela-hero { padding: 42px 0; }
    .mela-page .mela-hero-grid, .mela-page .mela-grid-2 { grid-template-columns: 1fr; gap: 34px; }
    .mela-page .mela-steps, .mela-page .mela-deploy-grid, .mela-page .mela-faq-grid { grid-template-columns: 1fr; }
    .mela-product-grid { grid-template-columns: 1fr; }
    .mela-page .mela-media-bar { min-height: 50px; }
    .mela-page .mela-section, .mela-page .mela-comparison { padding: 48px 0; }
    .mela-page .mela-service-hero { padding: 64px 22px 56px; }
    .mela-page .mela-service-hero .mela-hero-grid { grid-template-columns: 1fr; gap: 34px; }
    .mela-page .mela-service-hero .mela-hero-copy { max-width: 100%; }
    .mela-page .mela-service-hero h1 { font-size: clamp(1.9rem, 9vw, 2.5rem); }
    .mela-page .mela-service-actions { display: grid; }
    .mela-page .mela-service-actions .mela-btn { width: 100%; }
    .mela-page .mela-contact-inner, .mela-page .mela-form-grid { grid-template-columns: 1fr; }
    .mela-page .mela-contact-inner { gap: 34px; }
    .mela-page .mela-contact-form { padding: 26px 20px; }
    .mela-page .mela-form-row-full { grid-column: auto; }
}
</style>
@endpush

@section('content')
@if(!$isMeetingAssistant)
<main class="mela-page">
    <section class="mela-collection-hero">
        <div class="mela-container">
            <span class="mela-collection-mark"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i></span>
            <span class="mela-badge"><i class="fa-solid fa-layer-group" aria-hidden="true"></i> The Mela AI Collection</span>
            <h1>Purpose-Built AI for the Work Your Teams Do Every Day</h1>
            <p>Mela is a growing family of intelligent assistants that fit into existing workflows, reduce manual effort, and help people move from information to action.</p>
        </div>
    </section>

    <section>
        <div class="mela-container">
            <div class="mela-collection-intro">
                <span class="mela-section-tag">Explore the collection</span>
                <h2 class="mela-title">One AI family. Focused products for real business work.</h2>
                <p class="mela-muted">Each Mela product solves a specific workflow while sharing the same focus on secure, practical, enterprise-ready AI.</p>
            </div>

            <div class="mela-product-grid">
                <article class="mela-product mela-product-featured">
                    <span class="mela-product-status">Available now</span>
                    <span class="mela-product-icon"><i class="fa-solid fa-microphone-lines" aria-hidden="true"></i></span>
                    <h2>Mela Meeting Assistant</h2>
                    <p>Turn Microsoft Teams conversations into clear summaries, assigned actions, Planner tasks, and Outlook follow-ups.</p>
                    <ul class="mela-product-points">
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> On-demand meeting capture through Teams chat</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> One-click Microsoft Planner task sync</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Automated attendee summaries and follow-ups</li>
                    </ul>
                    <a href="{{ route('mela-meeting-assistant') }}" class="mela-btn mela-btn-primary">Explore Meeting Assistant <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </article>

                <article class="mela-product mela-product-muted">
                    <span class="mela-product-status mela-coming-soon">Coming soon</span>
                    <span class="mela-product-icon"><i class="fa-solid fa-comments" aria-hidden="true"></i></span>
                    <h2>Mela Organization Chat</h2>
                    <p>Give employees a secure conversational layer across organizational knowledge, documents, policies, and communications.</p>
                    <ul class="mela-product-points">
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Answers grounded in approved internal sources</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Role-aware access and enterprise governance</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Natural-language knowledge discovery</li>
                    </ul>
                    <a href="{{ route('contact') }}" class="mela-btn mela-btn-secondary" style="border-color:var(--mela-primary);color:var(--mela-primary)">Join the Waitlist <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </article>
            </div>
        </div>
    </section>

    <section class="mela-collection-cta">
        <div class="mela-container">
            <span class="mela-section-tag">Build with Mela</span>
            <h2 class="mela-title">Have a workflow that needs its own AI assistant?</h2>
            <p>Talk with Armely about the next Mela product, a tailored pilot, or an AI workflow designed for your organization.</p>
            <a href="{{ route('contact') }}" class="mela-btn mela-btn-primary"><i class="fa-regular fa-calendar" aria-hidden="true"></i> Talk to Our AI Team</a>
        </div>
    </section>
</main>
@else
<main class="mela-page">
    <section class="mela-hero mela-service-hero">
        <div class="mela-container mela-hero-grid">
            <div class="mela-hero-copy">
                <div class="mela-service-eyebrow">
                    <span class="mela-badge"><i class="fa-solid fa-bolt" aria-hidden="true"></i> Native Microsoft Teams &amp; Planner Integration</span>
                </div>
                <h1>Intelligence Embedded Into How Your Business Works</h1>
                <p>From real-time meeting capture to direct task syncing and automated email summaries, Mela automates post-meeting execution across Microsoft 365.</p>
                <div class="mela-service-actions">
                    <a href="#mela-contact" class="mela-btn mela-btn-primary">Request Free 14-Day Pilot <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </div>
                <p class="mela-note"><em>Requires Microsoft Teams Admin Access • Guided 10-Minute Onboarding</em></p>
            </div>
            <div class="mela-media" id="mela-demo">
                <?php
                    $playerTitle = trim((string) ($meetingVideo->title ?? 'Mela Meeting Assistant Product Demo'));
                    $playerProvider = trim((string) ($meetingVideo->provider ?? (file_exists(public_path('mela-demo.mp4')) ? 'Video' : 'Demo')));
                ?>
                <div class="mela-media-bar">
                    <div class="mela-media-title"><i class="fa-solid fa-circle-play" aria-hidden="true"></i><span>{{ $playerTitle }}</span></div>
                    <span class="mela-media-provider">{{ $playerProvider }}</span>
                </div>
                <div class="mela-player">
                    @if(!empty($meetingVideo) && ($meetingVideo->type ?? '') === 'embed')
                        <iframe
                            src="{{ $meetingVideo->url }}"
                            title="{{ $playerTitle }}"
                            loading="lazy"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen>
                        </iframe>
                    @elseif(!empty($meetingVideo) && ($meetingVideo->type ?? '') === 'video')
                        <video controls playsinline preload="metadata" aria-label="{{ $playerTitle }}">
                            <source src="{{ $meetingVideo->url }}">
                            Your browser does not support the video tag.
                        </video>
                    @elseif(file_exists(public_path('mela-demo.mp4')))
                        <video controls playsinline preload="metadata" aria-label="Mela Meeting Assistant product demonstration">
                            <source src="{{ asset('mela-demo.mp4') }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <div class="mela-video-placeholder" role="img" aria-label="Mela Meeting Assistant product video placeholder">
                            <div>
                                <span class="mela-video-placeholder-icon"><i class="fa-solid fa-play" aria-hidden="true"></i></span>
                                <strong>Mela Meeting Assistant</strong>
                                <span>Add a YouTube, OneDrive, SharePoint, or direct video URL in Admin Videos.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <div class="mela-trust">
        <span><i class="fa-solid fa-shield-halved" aria-hidden="true"></i>Zero Recording Consent Pop-Ups</span>
        <span><i class="fa-solid fa-terminal" aria-hidden="true"></i>Controlled via <code>@mela</code> Chat Commands</span>
        <span><i class="fa-brands fa-microsoft" aria-hidden="true"></i>100% Native M365 Integration</span>
    </div>

    <section class="mela-section">
        <div class="mela-container">
            <h2 class="mela-title mela-feature-title">Turn Meeting Action Items into Microsoft Planner Tasks in One Click</h2>
            <div class="mela-grid-2">
                <div>
                    <p class="mela-muted">Capture 100% of meeting commitments and create tasks without requiring video or audio recordings.</p>
                    <ul class="mela-features">
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i><strong>Zero “Recording Anxiety”:</strong> Captures transcriptions independently, eliminating awkward recording pop-ups and compliance roadblocks.</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i><strong>Command-Driven Privacy:</strong> Type <code>@mela please join</code> when business begins, and <code>@mela please leave</code> when private chatter starts. 
                        
                        <code>@mela what did last person say</code> - live meeting notes, 
                        <code>@mela summarize, generate minutes, or fetch transcript</code>, 
                         <code>@mela help</code> - to get more help regarding how to use or configure Mela 
                    
                    </li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i><strong>Native Microsoft Planner Sync:</strong> Review tasks in chat and click <strong>“Post to Planner”</strong> to publish action items instantly.</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i><strong>Automated Email Summaries:</strong> Every attendee automatically receives a structured Outlook email recap with key decisions and assigned tasks.</li>
                    </ul>
                    <!-- <div class="mela-roadmap"><i class="fa-solid fa-rocket" aria-hidden="true"></i><span><strong>Multi-Platform Roadmap:</strong> Need Jira, Slack, or Asana integrations? <a href="{{ route('contact') }}">Request Enterprise Pilot Access <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></span></div> -->
                </div>
                <figure class="mela-action-preview">
                    <img
                        src="{{ asset('images/mela/meeting-action-items.png') }}"
                        alt="Mela Meeting Assistant action-item card showing an Azure Storage and Cloud Security meeting task ready to post to Microsoft Planner"
                        loading="lazy"
                        width="796"
                        height="615">
                </figure>
            </div>
            <div class="mela-steps">
                <article class="mela-step"><span class="mela-step-icon"><i class="fa-solid fa-comment-dots" aria-hidden="true"></i></span><h3>Invite On-Demand</h3><p>Type <code>@mela please join</code> in your meeting chat to start taking notes.</p></article>
                <article class="mela-step"><span class="mela-step-icon"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span><h3>Review In-Chat Tasks</h3><p>Mela delivers an interactive card in chat immediately as the call wraps up.</p></article>
                <article class="mela-step"><span class="mela-step-icon"><i class="fa-solid fa-arrow-up-right-dots" aria-hidden="true"></i></span><h3>Sync to Planner</h3><p>Refine titles, pick due dates, and hit <strong>“Post to Planner.”</strong></p></article>
                <article class="mela-step"><span class="mela-step-icon"><i class="fa-solid fa-envelope-open-text" aria-hidden="true"></i></span><h3>Automated Email Recap</h3><p>All attendees receive a full meeting summary email in Outlook instantly.</p></article>
            </div>
            <div class="mela-steps">
                <article class="mela-step"><span class="mela-step-icon"><i class="fa-solid fa-door-closed" aria-hidden="true">
                    </i></span><h3>Leave Meeting</h3><p>Type <code>@mela please leave </code> respectifully leaves the meeting and stops recording.</p>
                </article>
                <article class="mela-step"><span class="mela-step-icon"><i class="fa-solid fa-circle-pause" aria-hidden="true">
                    </i></span><h3>Pause Recording</h3><p>Type <code>@mela pause recording or @mela unpause recording </code> </p>
                </article>
                <article class="mela-step"><span class="mela-step-icon"><i class="fa-solid fa-envelope" aria-hidden="true">
                    </i></span><h3>Draft Emails</h3><p>Type new email, invitation, or sample message.</p>
                </article>
                <article class="mela-step"><span class="mela-step-icon"><i class="fa-solid fa-calendar" aria-hidden="true">
                    </i></span><h3>Calendar</h3><p>Reads your daily meetings, weekly and past meetings.</p>
                </article>

            </div>
        </div>
    </section>
    <section class="mela-section">
    <div class="mela-container">
        <h2 class="mela-title mela-feature-title">
            Go Beyond Notes. Turn Every Conversation Into Action.
        </h2>

        <p class="mela-muted" style="max-width:760px;margin:0 auto;text-align:center">
            Mela understands your meetings, captures important discussions, identifies decisions,
            and helps your teams move from conversations to execution.
        </p>

        <div class="mela-steps">

            <article class="mela-step">
                <span class="mela-step-icon">
                    <i class="fa-solid fa-wave-square" aria-hidden="true"></i>
                </span>
                <h3>Real-Time Transcription</h3>
                <p>
                    Capture meeting conversations automatically with AI-powered transcription
                    designed for modern collaboration.
                </p>
            </article>

            <article class="mela-step">
                <span class="mela-step-icon">
                    <i class="fa-solid fa-user-group" aria-hidden="true"></i>
                </span>
                <h3>Speaker Attribution</h3>
                <p>
                    Understand who said what with speaker-aware transcripts that make meetings
                    easier to review.
                </p>
            </article>

            <article class="mela-step">
                <span class="mela-step-icon">
                    <i class="fa-solid fa-lightbulb" aria-hidden="true"></i>
                </span>
                <h3>Decision Intelligence</h3>
                <p>
                    Automatically identify decisions, owners, commitments, and follow-up actions
                    from every discussion.
                </p>
            </article>

            <article class="mela-step">
                <span class="mela-step-icon">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                </span>
                <h3>Searchable Meeting Archive</h3>
                <p>
                    Quickly find past conversations, decisions, and action items across your
                    organization's meeting history.
                </p>
            </article>

        </div>
    </div>
</section>


<section class="mela-section" style="background:var(--mela-light)">
    <div class="mela-container">


        <h2 class="mela-title mela-feature-title">
            Designed Around How Teams Actually Work
        </h2>

        <div class="mela-grid-2">

            <div>
                <ul class="mela-features">

                    <li>
                        <i class="fa-solid fa-check" aria-hidden="true"></i>
                        <strong>Sales Teams:</strong>
                        Capture customer conversations, commitments, and next steps automatically.
                    </li>

                    <li>
                        <i class="fa-solid fa-check" aria-hidden="true"></i>
                        <strong>Leadership Teams:</strong>
                        Preserve important decisions from executive meetings and reviews.
                    </li>

                    <li>
                        <i class="fa-solid fa-check" aria-hidden="true"></i>
                        <strong>Engineering Teams:</strong>
                        Convert technical discussions into actionable work items.
                    </li>

                    <li>
                        <i class="fa-solid fa-check" aria-hidden="true"></i>
                        <strong>Operations Teams:</strong>
                        Maintain searchable knowledge from recurring meetings.
                    </li>

                </ul>
            </div>

            <div>
                <div class="mela-action-preview">
                    <div style="padding:30px;text-align:center">
                        <span class="mela-step-icon">
                            <i class="fa-solid fa-plug" aria-hidden="true"></i>
                        </span>

                        <h3 style="margin-top:15px">
                            Connect Your Existing Tools
                        </h3>

                        <p class="mela-muted">
                            Extend meeting intelligence across your workflow with integrations
                            for Planner, Jira, Salesforce, Asana, SharePoint, and other business systems.
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>


<section class="mela-section">
    <div class="mela-container">

        <h2 class="mela-title mela-feature-title">
            AI Built With Security and Governance in Mind
        </h2>

        <div class="mela-deploy-grid">

            <article class="mela-deploy-card">
                <span class="mela-num">1</span>
                <h3>Microsoft 365 Native</h3>
                <p>
                    Designed to work within your Microsoft ecosystem using existing collaboration tools.
                </p>
            </article>

            <article class="mela-deploy-card">
                <span class="mela-num">2</span>
                <h3>Role-Based Access</h3>
                <p>
                    Control access and protect meeting information using enterprise permissions.
                </p>
            </article>

            <article class="mela-deploy-card">
                <span class="mela-num">3</span>
                <h3>Secure Data Handling</h3>
                <p>
                    Keep business conversations protected with enterprise-grade security practices.
                </p>
            </article>

            <article class="mela-deploy-card">
                <span class="mela-num">4</span>
                <h3>Scalable AI Workflows</h3>
                <p>
                    Expand meeting intelligence into the tools your organization already uses.
                </p>
            </article>

        </div>

    </div>
</section>

    <section class="mela-comparison">
        <div class="mela-container">
            <h2 class="mela-title" style="text-align:center">How Mela Compares to the Competition</h2>
            <div class="mela-table-wrap"><table>
                <thead><tr><th>Capability</th><th>Standard Teams</th><th>3rd-Party Bots</th><th class="mela-highlight">Mela Meeting Assistant</th></tr></thead>
                <tbody>
                    <tr><td><strong>Recording Dependency</strong></td><td>Requires active video/audio recording with consent pop-ups.</td><td>Forces call recording; inserts intrusive guest bots.</td><td class="mela-highlight"><strong>Independent of call recording</strong> via <code>@mela</code> commands.</td></tr>
                    <tr><td><strong>Privacy Control</strong></td><td>Full-call recording or admin rules.</td><td>Stays for the full call, capturing private side-chatter.</td><td class="mela-highlight"><strong>On-Demand Privacy:</strong> Join/leave anytime via <code>@mela please join/leave</code>.</td></tr>
                    <tr><td><strong>Data Governance</strong></td><td>Standard OneDrive / SharePoint storage.</td><td>Stored on external third-party servers.</td><td class="mela-highlight"><strong>100% Secure M365:</strong> Operates entirely within your enterprise environment.</td></tr>
                    <tr><td><strong>Task Execution &amp; Recaps</strong></td><td>Static text notes requiring copy-pasting.</td><td>Isolated lists on external web portals.</td><td class="mela-highlight"><strong>1-Click Planner Sync + Email Summaries:</strong> Direct task export and attendee recaps.</td></tr>
                </tbody>
            </table></div>
        </div>
    </section>
    

    <section id="deployment" class="mela-section">
        <div class="mela-container">
            <span class="mela-section-tag">Enterprise IT Onboarding</span>
            <h2 class="mela-title">Deployed to Your Tenant in Under 10 Minutes</h2>
            <p class="mela-muted" style="max-width:760px">Mela deploys using standard Microsoft 365 governance with no third-party software installation, no infrastructure overhead, and complete IT admin control.</p>
            <div class="mela-deploy-grid">
                <article class="mela-deploy-card"><span class="mela-num">1</span><h3>Add to Enterprise Catalog</h3><p>Add Mela to your Microsoft Teams Admin Center as an approved organizational app.</p></article>
                <article class="mela-deploy-card"><span class="mela-num">2</span><h3>Grant Entra ID Consent</h3><p>Authorize standard Microsoft Entra ID permissions via a single OAuth consent link.</p></article>
                <article class="mela-deploy-card"><span class="mela-num">3</span><h3>Apply Tenant Policy</h3><p>Enable meeting access policies across your enterprise using standard Teams admin controls.</p></article>
                <article class="mela-deploy-card"><span class="mela-num">4</span><h3>Activate Access</h3><p>Armely activates your Tenant ID on our secure allowlist for instant meeting access.</p></article>
            </div>
        </div>
    </section>

    <section class="mela-section mela-faq">
        <div class="mela-container">
            <span class="mela-section-tag">Pricing &amp; Plans</span>
            <h2 class="mela-title">Frequently Asked Questions</h2>
            <div class="mela-faq-grid">
                <article class="mela-faq-card"><h3><i class="fa-solid fa-tags" aria-hidden="true"></i>How is Mela priced?</h3><p>Mela offers flexible per-user monthly plans and flat organizational tenant licensing. Pricing scales based on your team size and active meeting volume with zero hidden setup fees.</p></article>
                <article class="mela-faq-card"><h3><i class="fa-solid fa-plug" aria-hidden="true"></i>Does Mela integrate with Jira or Slack?</h3><p>Mela natively syncs out-of-the-box with Microsoft Planner and Teams. Custom integrations for Jira, Slack, and Asana are available for Enterprise pilot clients upon request.</p></article>
                <article class="mela-faq-card"><h3><i class="fa-solid fa-layer-group" aria-hidden="true"></i>Do I need Copilot or Teams Premium?</h3><p>No additional Microsoft Copilot or Teams Premium add-on licenses are required. Mela integrates directly with your standard Microsoft Teams and Planner setup.</p></article>
                <article class="mela-faq-card"><h3><i class="fa-solid fa-stopwatch" aria-hidden="true"></i>How fast is onboarding?</h3><p>Deployment takes under 10 minutes. Your IT administrator adds Mela to the Teams Admin Center, approves Entra ID consent, and enables meeting policies.</p></article>
            </div>
            <div class="mela-cta">
                <h2>Ready to Elevate Your Team’s Productivity?</h2>
                <p>Embed intelligent automation into your daily Microsoft 365 workflow today.</p>
                <a href="#mela-contact" class="mela-btn mela-btn-primary"><i class="fa-solid fa-rocket" aria-hidden="true"></i> Start Free IT Trial</a>
                <a href="#mela-contact" class="mela-btn mela-btn-secondary"><i class="fa-regular fa-calendar" aria-hidden="true"></i> Schedule a Demo</a>
            </div>
        </div>
    </section>

    <section class="mela-contact" id="mela-contact">
        <div class="mela-container mela-contact-inner">
            <div class="mela-contact-copy">
                <span class="mela-kicker">Get Started</span>
                <h2 class="mela-title">Let's talk about your meeting workflow.</h2>
                <p>Book a free 30-minute discovery call. We will review your Microsoft 365 environment and recommend the right Mela Meeting Assistant pilot for your team.</p>
                <ul class="mela-contact-points">
                    <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Free assessment, no commitment required</li>
                    <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Pilot and tenant-readiness recommendation included</li>
                    <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Response within one business day</li>
                </ul>
            </div>
            <form class="mela-contact-form" id="mela-meeting-contact-form" method="post" action="{{ route('contact.submit') }}">
                @csrf
                <div class="alert" id="MelaMeetingSubmitMessage" role="alert" style="display:none"></div>
                <div class="mela-form-title">Book Your Free Assessment</div>
                <div class="mela-form-sub">Tell us about your situation.</div>
                <div class="mela-form-grid">
                    <div class="mela-form-row">
                        <label for="mela-contact-name">Full Name *</label>
                        <input id="mela-contact-name" type="text" name="name" placeholder="Jane Smith" autocomplete="name" required>
                    </div>
                    <div class="mela-form-row">
                        <label for="mela-contact-email">Business Email *</label>
                        <input id="mela-contact-email" type="email" name="email" placeholder="jane@yourcompany.com" autocomplete="email" required>
                    </div>
                    <div class="mela-form-row">
                        <label for="mela-contact-organization">Organization</label>
                        <input id="mela-contact-organization" type="text" name="organization" placeholder="Acme Corp" autocomplete="organization">
                    </div>
                    <div class="mela-form-row">
                        <label for="mela-contact-phone">Phone</label>
                        <input id="mela-contact-phone" type="tel" name="phone" placeholder="Optional" autocomplete="tel">
                    </div>
                    <div class="mela-form-row mela-form-row-full">
                        <label for="mela-contact-subject">Primary Need *</label>
                        <select id="mela-contact-subject" name="subject" required>
                            <option value="Mela Meeting Assistant" selected>Mela Meeting Assistant</option>
                        </select>
                    </div>
                    <div class="mela-form-row mela-form-row-full">
                        <label for="mela-contact-message">Message *</label>
                        <textarea id="mela-contact-message" name="message" placeholder="Tell us what you need help with..." required></textarea>
                    </div>
                    <input type="text" name="website" tabindex="-1" autocomplete="off" style="display:none" aria-hidden="true">
                    <div class="mela-form-row mela-form-row-full">
                        <label>Confirm you are not a robot *</label>
                        @if(!empty(config('services.recaptcha.site_key')))
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                        @else
                            <div class="alert alert-warning">reCAPTCHA is not configured. Please set <strong>CAPTURE_SITE_KEY</strong>.</div>
                        @endif
                    </div>
                </div>
                <button class="mela-form-submit" name="submit_form" type="submit">Request Free Discovery Call</button>
                <div class="mela-form-note">No spam. No sales pressure. Just a useful conversation.</div>
                <div class="mela-form-note"><a href="{{ route('mela.terms') }}">Mela Terms of Use</a> &nbsp;•&nbsp; <a href="{{ route('mela.privacy') }}">Mela AI Privacy Policy</a></div>
            </form>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('mela-meeting-contact-form');
    if (!form) return;

    var submitButton = form.querySelector('button[name="submit_form"]');
    var messageBox = document.getElementById('MelaMeetingSubmitMessage');
    var originalButtonText = submitButton.textContent;

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        var captchaField = form.querySelector('textarea[name="g-recaptcha-response"]');
        var captchaResponse = captchaField ? captchaField.value.trim() : '';

        messageBox.style.display = 'none';
        messageBox.className = 'alert';
        if (!captchaResponse) {
            messageBox.classList.add('alert-danger');
            messageBox.textContent = 'Please verify that you are not a robot.';
            messageBox.style.display = 'block';
            return;
        }

        submitButton.disabled = true;
        submitButton.textContent = 'Sending...';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: new FormData(form)
        })
        .then(function (response) {
            return response.json().then(function (data) { return { ok: response.ok, data: data }; });
        })
        .then(function (result) {
            messageBox.classList.add(result.ok && result.data.success ? 'alert-success' : 'alert-danger');
            messageBox.textContent = result.data.message || (result.ok ? 'Your request was sent successfully.' : 'An error occurred. Please try again.');
            messageBox.style.display = 'block';
            if (result.ok && result.data.success) {
                form.reset();
                if (window.grecaptcha) window.grecaptcha.reset();
            }
        })
        .catch(function () {
            messageBox.classList.add('alert-danger');
            messageBox.textContent = 'An error occurred. Please try again.';
            messageBox.style.display = 'block';
        })
        .finally(function () {
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        });
    });
});
</script>
@endif
@endsection

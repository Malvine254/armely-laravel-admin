@extends('layouts.public')

@php($isWhitePaperPage = !empty($isWhitePaperPage))
@php($detailKindLabel = $isWhitePaperPage ? 'White Paper' : 'Case Study')
@php($detailPluralLabel = $isWhitePaperPage ? 'White Papers' : 'Case Studies')
@php($detailPrimaryActionLabel = $isWhitePaperPage ? 'Download full white paper' : 'Download full case study')
@php($detailPreviewActionLabel = $isWhitePaperPage ? 'Request full white paper' : 'Request full case study')
@php($detailModalTitle = $isWhitePaperPage ? 'Download Full White Paper' : 'Download Full Case Study')
@php($detailModalSubmitLabel = $isWhitePaperPage ? 'Email White Paper Link' : 'Email Download Link')
@php($detailModalCopy = 'Complete the form and we will email your secure download link. No phone number required.')
@php($detailRequestAction = $detailRequestAction ?? ($isWhitePaperPage ? route('whitepapers.request', $caseStudy->slug) : route('case-studies.lead.submit')))
@php($detailLeadInterest = $detailLeadInterest ?? ($isWhitePaperPage ? 'white-papers' : 'case-studies'))
@php($detailLeadIdField = $detailLeadIdField ?? 'case_study_id')
@php($detailLeadIdValue = $detailLeadIdValue ?? $caseStudy->id)

@section('title', $caseStudy->display_title . ' ' . $detailKindLabel . ' | ' . ($caseStudy->technology_label ?? 'Microsoft Platform') . ' | Armely')
@section('meta_description', $metaDescription)

@push('head')
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $caseStudy->display_title }} {{ $detailKindLabel }} | {{ $caseStudy->technology_label ?? 'Microsoft Platform' }} | Armely">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ request()->url() }}">
@if(!empty($caseStudy->listing_image) && file_exists(public_path('images/case-study/' . $caseStudy->listing_image)))
<meta property="og:image" content="{{ asset('images/case-study/' . $caseStudy->listing_image) }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $caseStudy->display_title }} {{ $detailKindLabel }} | {{ $caseStudy->technology_label ?? 'Microsoft Platform' }} | Armely">
<meta name="twitter:description" content="{{ $metaDescription }}">
@if(!empty($caseStudy->listing_image) && file_exists(public_path('images/case-study/' . $caseStudy->listing_image)))
<meta name="twitter:image" content="{{ asset('images/case-study/' . $caseStudy->listing_image) }}">
@endif
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/case-studies-modern.css') }}">
<style>
.case-detail-page {
	--case-ink: #172033;
	--case-muted: #46586f;
	--case-line: #d9e4f2;
	--case-panel: #ffffff;
	--case-soft: #f5f8fc;
	--case-blue: #2f5597;
	--case-navy: #18345f;
	--case-teal: #2f5597;
	--case-gold: #22447d;
	background:
		radial-gradient(1200px 560px at 12% 0%, rgba(47, 85, 151, .08), rgba(47, 85, 151, 0) 60%),
		linear-gradient(180deg, #f7faff 0%, #ffffff 100%);
	color: var(--case-ink);
}
.case-detail-hero {
	background:
		linear-gradient(135deg, #162f5d 0%, #214a89 56%, #18345f 100%);
	padding: 36px 0 44px;
	position: relative;
	overflow: hidden;
}
.case-detail-hero::after {
	content: '';
	position: absolute;
	inset: 0;
	background:
		radial-gradient(560px 220px at 18% 18%, rgba(255, 255, 255, .12), rgba(255, 255, 255, 0) 68%),
		radial-gradient(420px 180px at 84% 8%, rgba(255, 255, 255, .08), rgba(255, 255, 255, 0) 70%);
	pointer-events: none;
}
.case-back-link {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	color: rgba(255,255,255,.88);
	font-weight: 800;
	font-size: .92rem;
	margin-bottom: 28px;
	position: relative;
	z-index: 1;
}
.case-back-link:hover { color: #fff; text-decoration: none; }
.case-hero-layout {
	display: grid;
	grid-template-columns: minmax(0, 1fr);
	gap: 0;
	align-items: start;
	position: relative;
	z-index: 1;
}
.case-detail-eyebrow {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	color: rgba(255,255,255,.88);
	font-weight: 800;
	text-transform: uppercase;
	font-size: .78rem;
	letter-spacing: .08em;
	margin-bottom: 14px;
}
.case-detail-title {
	color: #fff;
	font-size: 2.9rem;
	line-height: 1.08;
	font-weight: 900;
	margin: 0 0 18px;
	max-width: 880px;
}
.case-detail-summary {
	color: rgba(255, 255, 255, .92);
	font-size: 1.1rem;
	line-height: 1.8;
	max-width: 850px;
	margin-bottom: 24px;
}
.case-hero-actions { display: flex; flex-wrap: wrap; gap: 12px; }
.case-share-toast {
	display: none;
	margin-top: 14px;
	padding: 10px 12px;
	border: 1px solid #b9cbe4;
	background: rgba(255, 255, 255, .12);
	color: rgba(255, 255, 255, .95);
	font-size: .92rem;
	font-weight: 700;
	border-radius: 12px;
	max-width: 520px;
}
.case-primary-btn,
.case-secondary-btn {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 9px;
	min-height: 46px;
	padding: 12px 18px;
	font-weight: 800;
	text-decoration: none;
	border: 1px solid transparent;
}
.case-primary-btn { background: var(--case-blue); color: #fff; box-shadow: 0 10px 24px rgba(7, 18, 39, .16); }
.case-primary-btn:hover { color: #fff; background: var(--case-navy); text-decoration: none; }
.case-secondary-btn { background: rgba(255,255,255,.08); color: #fff; border-color: rgba(255,255,255,.22); backdrop-filter: blur(8px); }
.case-secondary-btn:hover { color: #fff; border-color: rgba(255,255,255,.45); text-decoration: none; }
.case-result-band {
	background: #fff;
	padding: 0 0 38px;
	margin-top: -22px;
	position: relative;
	z-index: 2;
}
.case-result-grid {
	display: grid;
	grid-template-columns: repeat(3, minmax(0, 1fr));
	gap: 16px;
}
.case-result {
	border: 1px solid var(--case-line);
	background: #fff;
	box-shadow: 0 14px 32px rgba(28, 54, 93, .08);
	padding: 20px;
	min-height: 126px;
	display: grid;
	gap: 12px;
	align-content: start;
}
.case-result i { color: var(--case-teal); font-size: 1.25rem; }
.case-result span { color: var(--case-ink); font-size: 1.03rem; line-height: 1.45; font-weight: 850; }
.case-detail-band { padding: 38px 0 72px; background: #fff; }
.case-preview-band {
	background:
		radial-gradient(1200px 260px at 15% -20%, rgba(47, 85, 151, .12), rgba(47, 85, 151, 0) 58%),
		linear-gradient(180deg, #f7faff 0%, #fff 100%);
	padding: 0 0 34px;
}
.case-preview-band .container {
	max-width: 1400px;
	padding-left: 24px;
	padding-right: 24px;
}
.case-preview-shell {
	width: 100%;
	margin: 0 auto;
	padding: 0;
}
.case-preview-grid {
	display: grid;
	grid-template-columns: minmax(0, 1fr);
	gap: 0;
	align-items: start;
}
.case-preview-card,
.case-preview-side {
	background: #fff;
}
.case-preview-card { padding: 28px; }
.case-preview-side { padding: 22px; }
.case-pdf-stage {
	position: relative;
	overflow: hidden;
	background:
		linear-gradient(180deg, rgba(255,255,255,.95), rgba(248,251,255,.95)),
		#fff;
	min-height: 640px;
	padding: 24px;
	display: flex;
	justify-content: center;
	align-items: flex-start;
}
.case-pdf-stage-inner {
	width: min(100%, 1120px);
	margin: 0 auto;
	position: relative;
}
.case-pdf-page {
	width: 100%;
	border: 1px solid #dbe6f3;
	border-radius: 18px;
	background: linear-gradient(180deg, #ffffff 0%, #f7faff 100%);
	box-shadow: none;
	padding: 28px;
}
.case-pdf-page-title {
	color: var(--case-ink);
	font-size: 1.55rem;
	line-height: 1.18;
	font-weight: 900;
	margin: 0;
	max-width: 22ch;
}
.case-pdf-page-body {
	position: relative;
	color: #32465f;
	font-size: 1rem;
	line-height: 1.85;
	max-height: 520px;
	overflow: hidden;
	padding-right: 6px;
}
.case-pdf-preview-meta {
	display: flex;
	flex-wrap: wrap;
	gap: 8px;
	margin-bottom: 16px;
}
.case-pdf-meta-chip {
	display: inline-flex;
	align-items: center;
	min-height: 28px;
	padding: 4px 10px;
	border-radius: 999px;
	background: #f7faff;
	color: var(--case-navy);
	font-size: .72rem;
	font-weight: 900;
	letter-spacing: .08em;
	text-transform: uppercase;
}
.case-pdf-meta-chip.is-muted {
	background: #fff;
	color: #5d6f86;
}
.case-pdf-page-copy {
	display: grid;
	gap: 14px;
}
.case-pdf-sections {
	display: grid;
	gap: 14px;
}
.case-pdf-section {
	padding: 16px 0 2px;
}
.case-pdf-section:first-child {
	padding-top: 0;
}
.case-pdf-section-head {
	display: grid;
	gap: 6px;
	margin-bottom: 10px;
}
.case-pdf-section-kicker {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	color: var(--case-blue);
	font-size: .72rem;
	font-weight: 900;
	text-transform: uppercase;
	letter-spacing: .12em;
}
.case-pdf-section-head h4 {
	margin: 0;
	color: var(--case-ink);
	font-size: 1.03rem;
	line-height: 1.25;
	font-weight: 900;
	letter-spacing: .01em;
}
.case-pdf-section-body {
	display: grid;
	gap: 12px;
}
.case-pdf-paragraph {
	margin: 0;
}
.case-pdf-paragraph.is-lead {
	font-size: 1.04rem;
	line-height: 1.95;
	color: #243752;
}
.case-pdf-highlight-grid {
	display: grid;
	grid-template-columns: repeat(3, minmax(0, 1fr));
	gap: 10px;
	margin: 18px 0 8px;
}
.case-pdf-highlight {
	background: linear-gradient(180deg, #fff 0%, #f7fbff 100%);
	border-radius: 14px;
	padding: 14px;
	min-height: 110px;
}
.case-pdf-highlight strong {
	display: block;
	color: var(--case-blue);
	font-size: .82rem;
	font-weight: 900;
	letter-spacing: .08em;
	text-transform: uppercase;
	margin-bottom: 6px;
}
.case-pdf-highlight span {
	color: #44566f;
	font-size: .92rem;
	line-height: 1.55;
}
.case-pdf-service-list {
	display: flex;
	flex-wrap: wrap;
	gap: 8px;
	margin: 14px 0 0;
	padding: 0;
	list-style: none;
}
.case-pdf-service-list li {
	display: inline-flex;
	align-items: center;
	min-height: 30px;
	padding: 4px 10px;
	border-radius: 999px;
	background: #fff;
	color: var(--case-navy);
	font-size: .72rem;
	font-weight: 800;
}
.case-pdf-preview-note {
	margin-top: 16px;
	padding: 14px 16px;
	border-radius: 12px;
	background: linear-gradient(135deg, rgba(47, 85, 151, .08), rgba(47, 85, 151, .03));
	color: #46586f;
	font-size: .92rem;
	line-height: 1.6;
}
.case-pdf-page-body::after {
	content: '';
	position: absolute;
	left: 0;
	right: 0;
	bottom: 0;
	height: 130px;
	background: linear-gradient(180deg, rgba(247, 250, 255, 0), rgba(247, 250, 255, 1) 82%);
	pointer-events: none;
}
.case-pdf-request {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 16px;
	margin-top: 18px;
	padding: 16px 18px;
	border-radius: 14px;
	background: linear-gradient(135deg, rgba(47, 85, 151, .08), rgba(47, 85, 151, .03));
	border: 1px solid #d7e2f1;
}
.case-pdf-request strong {
	display: block;
	color: var(--case-ink);
	font-size: .98rem;
	font-weight: 900;
	margin-bottom: 4px;
}
.case-pdf-request span {
	color: #4d627f;
	line-height: 1.45;
	font-size: .92rem;
}
.case-pdf-request a {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	min-height: 44px;
	padding: 10px 16px;
	border-radius: 12px;
	background: var(--case-blue);
	color: #fff;
	font-weight: 900;
	text-decoration: none;
	white-space: nowrap;
	box-shadow: 0 12px 24px rgba(31, 63, 118, .18);
}
.case-pdf-request a:hover {
	background: var(--case-navy);
	color: #fff;
	text-decoration: none;
}
.case-pdf-loading {
	display: grid;
	place-items: center;
	min-height: 640px;
	border: 1px dashed #c8d7ea;
	border-radius: 14px;
	background: linear-gradient(180deg, #fbfdff 0%, #f5f9ff 100%);
	color: var(--case-muted);
	font-weight: 800;
	text-align: center;
	padding: 24px;
}
.case-preview-footer {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 16px;
	padding-top: 18px;
	margin-top: 18px;
}
.case-preview-footer p {
	margin: 0;
	color: #46586f;
	font-size: .96rem;
	line-height: 1.6;
}
.case-preview-footer .case-primary-btn {
	min-width: 240px;
	background: var(--case-blue);
	color: #fff;
	box-shadow: 0 12px 24px rgba(31, 63, 118, .22);
}
.case-preview-footer .case-primary-btn:hover {
	background: var(--case-navy);
	color: #fff;
}
.case-pdf-canvas {
	display: none;
	width: 100%;
	max-width: 100%;
	height: auto;
	border-radius: 14px;
	box-shadow: 0 16px 32px rgba(28, 54, 93, .12);
	background: #fff;
}
.case-pdf-snapshot {
	display: none;
	width: 100%;
	max-width: 100%;
	height: auto;
	border-radius: 14px;
	box-shadow: 0 16px 32px rgba(28, 54, 93, .12);
	background: #fff;
}
.case-pdf-mock {
	border-radius: 14px;
	background:
		linear-gradient(180deg, #fff 0%, #fbfdff 100%);
	border: 1px solid #dbe6f3;
	box-shadow: 0 16px 32px rgba(28, 54, 93, .12);
	padding: 20px;
	min-height: 700px;
}
.case-mock-topbar {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 12px;
	padding-bottom: 14px;
	border-bottom: 1px solid #e3ebf7;
	margin-bottom: 16px;
}
.case-mock-brand {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	color: var(--case-blue);
	font-weight: 900;
	letter-spacing: .08em;
	text-transform: uppercase;
	font-size: .78rem;
}
.case-mock-chip {
	display: inline-flex;
	align-items: center;
	min-height: 26px;
	padding: 3px 10px;
	border-radius: 999px;
	border: 1px solid #d1def0;
	background: #f7faff;
	color: var(--case-navy);
	font-size: .68rem;
	font-weight: 900;
	letter-spacing: .08em;
	text-transform: uppercase;
}
.case-mock-hero {
	background:
		linear-gradient(135deg, rgba(47, 85, 151, .12), rgba(47, 85, 151, .04)),
		#fff;
	border: 1px solid #dbe6f3;
	border-radius: 16px;
	padding: 22px;
	margin-bottom: 14px;
}
.case-mock-title {
	color: var(--case-ink);
	font-size: 2.05rem;
	line-height: 1.08;
	font-weight: 900;
	margin: 0 0 12px;
	max-width: 12ch;
}
.case-mock-summary {
	color: #44566f;
	font-size: 1rem;
	line-height: 1.72;
	max-width: 58ch;
	margin: 0 0 18px;
}
.case-mock-metrics {
	display: grid;
	grid-template-columns: repeat(3, minmax(0, 1fr));
	gap: 10px;
	margin-bottom: 14px;
}
.case-mock-metric {
	border: 1px solid #d9e5f4;
	background: linear-gradient(180deg, #fff 0%, #f7fbff 100%);
	border-radius: 14px;
	padding: 14px;
	min-height: 104px;
}
.case-mock-metric strong {
	display: block;
	color: var(--case-blue);
	font-size: 1.25rem;
	font-weight: 900;
	margin-bottom: 6px;
}
.case-mock-metric span {
	color: #44566f;
	font-size: .86rem;
	line-height: 1.5;
}
.case-mock-section {
	border-top: 1px solid #e3ebf7;
	padding-top: 14px;
	margin-top: 14px;
}
.case-mock-section h4 {
	color: var(--case-ink);
	font-size: 1rem;
	font-weight: 900;
	margin: 0 0 8px;
}
.case-mock-line {
	height: 11px;
	border-radius: 999px;
	background: linear-gradient(90deg, #e6edf7 0%, #dce7f6 100%);
	margin-bottom: 9px;
}
.case-mock-line.short { width: 72%; }
.case-mock-line.mid { width: 86%; }
.case-mock-line.long { width: 96%; }
.case-pdf-empty {
	min-height: 420px;
	display: grid;
	place-items: center;
	padding: 28px;
	text-align: center;
	color: var(--case-muted);
	font-weight: 700;
	border-radius: 14px;
	background: linear-gradient(180deg, #fbfdff, #f4f8fd);
}
.case-pdf-overlay {
	position: absolute;
	left: 24px;
	right: 24px;
	bottom: 24px;
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 12px;
	padding: 14px 16px;
	border-radius: 14px;
	background: rgba(23, 32, 51, .86);
	color: #fff;
	box-shadow: 0 16px 28px rgba(11, 28, 57, .28);
}
.case-pdf-overlay strong {
	display: block;
	font-size: .92rem;
	font-weight: 900;
	margin-bottom: 2px;
}
.case-pdf-overlay span {
	color: rgba(241, 246, 255, .9);
	font-size: .82rem;
	line-height: 1.4;
}
.case-pdf-overlay a {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	min-height: 40px;
	padding: 10px 14px;
	border-radius: 10px;
	background: #fff;
	color: var(--case-blue);
	font-weight: 900;
	text-decoration: none;
	white-space: nowrap;
}
.case-pdf-overlay a:hover { color: var(--case-navy); text-decoration: none; }
.case-preview-side h3 {
	color: var(--case-ink);
	font-size: 1.1rem;
	font-weight: 900;
	margin-bottom: 10px;
}
.case-preview-side p {
	color: #46586f;
	line-height: 1.7;
	margin-bottom: 16px;
}
.case-preview-list {
	display: grid;
	gap: 10px;
	margin: 0 0 18px;
	padding: 0;
	list-style: none;
}
.case-preview-list li {
	display: flex;
	align-items: flex-start;
	gap: 10px;
	color: var(--case-ink);
	line-height: 1.5;
}
.case-preview-list li::before {
	content: '\f00c';
	font-family: 'Font Awesome 6 Free';
	font-weight: 900;
	color: var(--case-blue);
	margin-top: 2px;
}
.case-preview-side .case-primary-btn,
.case-preview-side .case-secondary-btn {
	width: 100%;
}
.case-detail-grid {
	display: grid;
	grid-template-columns: minmax(0, 1.48fr) minmax(310px, .82fr);
	gap: 32px;
	align-items: start;
}
.case-content-card,
.case-panel,
.case-share-card,
.case-lead-card {
	border: 1px solid var(--case-line);
	background: var(--case-panel);
	box-shadow: 0 14px 34px rgba(28, 54, 93, .07);
}
.case-content-card { padding: 34px; }
.case-content-card h2,
.case-panel h3,
.case-share-card h3,
.case-lead-card h3 {
	color: var(--case-ink);
	font-weight: 900;
	line-height: 1.25;
	margin-bottom: 14px;
}
.case-detail-body { color: #3f4d63; line-height: 1.9; }
.case-detail-body h1,
.case-detail-body h2,
.case-detail-body h3 {
	color: var(--case-ink);
	font-weight: 900;
	margin-top: 1.2em;
	margin-bottom: .55em;
}
.case-detail-body p { margin-bottom: 1rem; }
.case-detail-body ul,
.case-detail-body ol { padding-left: 22px; margin-bottom: 1rem; }
.case-panel { padding: 24px; margin-top: 18px; }
.case-panel ul { padding-left: 0; margin: 0; color: var(--case-muted); line-height: 1.8; list-style: none; }
.case-panel li { display: flex; gap: 10px; margin-bottom: 10px; }
.case-panel li::before { content: '\f00c'; font-family: 'Font Awesome 6 Free'; font-weight: 900; color: var(--case-teal); }
.case-sidebar { position: sticky; top: 98px; }
.case-share-card { padding: 20px; margin-bottom: 16px; }
.case-share-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 9px; }
.case-share-btn {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 8px;
	width: 100%;
	min-height: 42px;
	padding: 10px 12px;
	border: 1px solid #c9d8eb;
	background: var(--case-soft);
	color: var(--case-navy);
	font-size: .86rem;
	font-weight: 800;
	text-decoration: none;
	transition: all .18s ease;
}
.case-share-btn:hover { background: #fff; color: var(--case-blue); border-color: #aebfda; text-decoration: none; }
.case-share-toast {
	margin-top: 10px;
	font-size: .82rem;
	color: #145b45;
	background: #e9faf4;
	border: 1px solid #b9e7d7;
	padding: 9px 10px;
	display: none;
}
.case-lead-card { padding: 24px; }
.case-lead-card p { color: var(--case-muted); }
.case-download-cta {
	border: 1px solid var(--case-line);
	background: #fff;
	box-shadow: 0 14px 34px rgba(28, 54, 93, .07);
	padding: 22px;
	margin-bottom: 16px;
}
.case-download-cta h3 {
	color: var(--case-ink);
	font-size: 1.08rem;
	font-weight: 900;
	margin-bottom: 10px;
}
.case-download-cta p {
	color: var(--case-muted);
	margin-bottom: 14px;
}
.case-download-open {
	width: 100%;
	min-height: 46px;
	border: 0;
	background: var(--case-blue);
	color: #fff;
	font-weight: 900;
	padding: 12px 16px;
	transition: background .2s ease;
}
.case-download-open:hover { background: var(--case-navy); }
.case-download-open:focus {
	outline: none;
	box-shadow: 0 0 0 4px rgba(47,85,151,.2);
}
.case-modal {
	position: fixed;
	inset: 0;
	background: rgba(11, 28, 57, 0.64);
	z-index: 10050;
	display: none;
	padding: 18px;
	overflow-y: auto;
}
.case-modal.is-open { display: block; }
.case-modal-dialog {
	max-width: 760px;
	margin: 36px auto;
	position: relative;
	background: #fff;
	border: 1px solid var(--case-line);
	box-shadow: 0 24px 46px rgba(16, 35, 68, .28);
	padding: 26px;
}
.case-modal-close {
	position: absolute;
	top: 10px;
	right: 10px;
	width: 36px;
	height: 36px;
	border: 1px solid #c7d8ed;
	background: #f5f9ff;
	color: var(--case-navy);
	font-size: 1.15rem;
	line-height: 1;
	cursor: pointer;
}
.case-modal-title {
	font-size: 1.45rem;
	font-weight: 900;
	color: var(--case-ink);
	margin-bottom: 6px;
}
.case-modal-copy {
	font-size: .95rem;
	color: var(--case-muted);
	margin-bottom: 18px;
}
.lead-grid {
	display: grid;
	grid-template-columns: repeat(2, minmax(0, 1fr));
	gap: 14px;
}
.lead-grid .lead-col-full { grid-column: 1 / -1; }
.field-label { display:block; font-size:.88rem; font-weight:800; color:var(--case-navy); margin-bottom:8px; }
.lead-field {
	width:100%;
	border:1px solid #c9d8eb;
	background:#f8fbff;
	min-height:50px;
	padding:0 14px;
	color:var(--case-ink);
	transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
}
.lead-field:focus {
	outline: none;
	border-color: var(--case-blue);
	background: #fff;
	box-shadow: 0 0 0 4px rgba(47,85,151,.12);
}
.lead-form .form-group { margin-bottom: 15px; }
.lead-form .captcha-wrap {
	margin-top: 2px;
	margin-bottom: 14px;
	min-height: 78px;
}
.lead-form .case-recaptcha {
	display: inline-block;
	max-width: 100%;
	transform-origin: left top;
}
.field-meta {
	font-size: .78rem;
	font-weight: 700;
	color: var(--case-muted);
	margin-left: 4px;
}
.lead-form .btn {
	width: 100%;
	min-height: 48px;
	border: 0;
	padding: 12px 18px;
	font-weight: 900;
	background: var(--case-blue);
	position: relative;
	z-index: 1;
}
.lead-form .btn[disabled] {
	opacity: .75;
	cursor: not-allowed;
}
.btn-content {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 10px;
}
.btn-loader {
	display: none;
	width: 14px;
	height: 14px;
	border: 2px solid rgba(255,255,255,.45);
	border-top-color: #fff;
	border-radius: 50%;
	animation: caseSpin .75s linear infinite;
}
.lead-form .btn.is-loading .btn-loader { display: inline-block; }
.case-form-status {
	display: none;
	margin-top: 12px;
	padding: 10px 12px;
	border: 1px solid transparent;
	font-size: .88rem;
	font-weight: 700;
}
.case-form-status.is-success {
	display: block;
	color: #145b45;
	background: #e9faf4;
	border-color: #b9e7d7;
}
.case-form-status.is-error {
	display: block;
	color: #8a1f1f;
	background: #fff0f0;
	border-color: #f3c4c4;
}
.case-direct-download {
	display: none;
	margin-top: 10px;
	padding: 12px;
	border: 1px solid #b9cbe4;
	background: #f5f9ff;
	font-size: .88rem;
	color: var(--case-navy);
}
.case-direct-download.is-visible { display: block; }
.case-direct-download a {
	font-weight: 800;
	color: var(--case-blue);
	text-decoration: underline;
}
.case-form-note { color: var(--case-muted); font-size: .84rem; margin-top: 12px; margin-bottom: 0; }
@keyframes caseSpin {
	to { transform: rotate(360deg); }
}
.related-card {
	display:block;
	border:1px solid var(--case-line);
	padding:16px;
	color:var(--case-ink);
	margin-bottom:10px;
	background:#fff;
	text-decoration:none;
}
.related-card strong { display: block; margin-bottom: 6px; }
.related-card:hover { color:var(--case-blue); border-color:#b9cbe4; text-decoration:none; }
@media (max-width: 991px) {
	.case-detail-hero { padding-top: 24px; }
	.case-hero-layout,
	.case-detail-grid,
	.case-result-grid,
	.case-preview-grid { grid-template-columns: 1fr; }
	.case-detail-title { font-size: 2.15rem; }
	.case-sidebar { position: static; }
	.case-pdf-stage { min-height: 520px; padding: 18px; }
	.case-mock-metrics { grid-template-columns: 1fr; }
	.case-pdf-highlight-grid { grid-template-columns: 1fr; }
	.case-preview-footer { flex-direction: column; align-items: stretch; }
	.case-preview-footer .case-primary-btn { width: 100%; min-width: 0; }
}
@media (max-width: 575px) {
	.case-detail-title { font-size: 1.85rem; }
	.case-content-card,
	.case-panel,
	.case-share-card,
	.case-lead-card,
	.case-preview-card,
	.case-preview-side { padding: 18px; }
	.case-modal { padding: 10px; }
	.case-modal-dialog { padding: 16px; margin: 10px auto; }
	.case-share-grid { grid-template-columns: 1fr; }
	.lead-grid { grid-template-columns: 1fr; }
	.lead-form .captcha-wrap {
		overflow-x: auto;
		padding-bottom: 2px;
	}
	.case-hero-actions { flex-direction: column; }
	.case-primary-btn,
	.case-secondary-btn { width: 100%; }
	.case-pdf-stage { min-height: 420px; padding: 12px; }
	.case-pdf-overlay {
		left: 12px;
		right: 12px;
		bottom: 12px;
		flex-direction: column;
		align-items: stretch;
	}
	.case-pdf-mock { padding: 14px; min-height: 520px; }
	.case-mock-title { font-size: 1.55rem; }
	.case-preview-shell { padding: 0 14px; }
}
</style>
@endpush

@section('content')
@php($caseStudyOutcomeLabel = trim((string) ($caseStudy->outcome_tag ?? '')))
<main class="case-detail-page">
	<section class="case-detail-hero">
		<div class="container">
			<a class="case-back-link text-light" href="{{ $isWhitePaperPage ? route('case-studies.index') . '#white-papers' : route('case-studies.index') }}"><i class="fa fa-arrow-left"></i> Back to {{ $detailPluralLabel }}</a>
			<div class="case-hero-layout">
				<div>
					<div class="case-detail-eyebrow">{{ $caseStudy->category ?? $detailKindLabel }}</div>
					<h1 class="case-detail-title">{{ $caseStudy->display_title }}</h1>
					<p class="case-detail-summary text-light">{{ $caseStudy->preview }}</p>
					<div class="case-hero-actions">
						<button type="button" class="case-primary-btn" id="openCaseDownloadModal"><i class="fa fa-file-arrow-down"></i> {{ $detailPrimaryActionLabel }}</button>
						<button type="button" class="case-secondary-btn" id="shareNativeBtnHero"><i class="fa fa-share-alt"></i> Share</button>
					</div>
					<div class="case-share-toast" id="caseShareToast" aria-live="polite"></div>
				</div>
			</div>
		</div>
	</section>

	<section class="case-preview-band">
	<div class="container">
			<div class="case-preview-shell">
				<div class="case-preview-card">
					<div class="case-pdf-stage">
						<div class="case-pdf-stage-inner">
							@if(!empty($caseStudy->pdf_preview_text))
								<div class="case-pdf-page" aria-label="{{ $detailKindLabel }} page one text preview">
									<h3 class="case-pdf-page-title">{{ $caseStudy->display_title }}</h3>
									<div class="case-pdf-page-body">
										<div class="case-pdf-page-copy">
											<div class="case-pdf-preview-meta" aria-label="Preview metadata">
												<span class="case-pdf-meta-chip">{{ $caseStudy->category ?? $detailKindLabel }}</span>
												<span class="case-pdf-meta-chip is-muted">{{ $caseStudy->technology_label ?? 'Microsoft Platform' }}</span>
												<span class="case-pdf-meta-chip is-muted">{{ $caseStudyOutcomeLabel !== '' ? $caseStudyOutcomeLabel : 'Measurable business outcome' }}</span>
											</div>
											@if(!empty($caseStudy->pdf_preview_sections))
												<div class="case-pdf-sections" aria-label="Formatted preview sections">
													@foreach($caseStudy->pdf_preview_sections as $sectionIndex => $section)
														<section class="case-pdf-section">
															@if(!empty($section['heading']))
																<div class="case-pdf-section-head">
																	<span class="case-pdf-section-kicker">Section {{ $sectionIndex + 1 }}</span>
																	<h4>{{ $section['heading'] }}</h4>
																</div>
															@endif
															<div class="case-pdf-section-body">
																@foreach($section['paragraphs'] ?? [] as $paragraphIndex => $paragraph)
																	<p class="case-pdf-paragraph {{ $sectionIndex === 0 && $paragraphIndex === 0 ? 'is-lead' : '' }}">{{ $paragraph }}</p>
																@endforeach
															</div>
														</section>
													@endforeach
												</div>
												<div class="case-pdf-preview-note">
													This preview is formatted from the first page of the PDF file so the section titles stay visible and readable.
												</div>
											@else
												@foreach($caseStudy->pdf_preview_paragraphs ?? [] as $index => $paragraph)
													<p class="case-pdf-paragraph {{ $index === 0 ? 'is-lead' : '' }}">{{ $paragraph }}</p>
												@endforeach
											@endif
										</div>
									</div>
									<div class="case-pdf-request">
										<div>
											<strong>Need the full {{ strtolower($detailKindLabel) }}?</strong>
											<span>Request secure access for the complete PDF and any remaining pages.</span>
										</div>
										<button type="button" class="case-primary-btn" id="openCaseDownloadModalPreviewInline">{{ $detailPreviewActionLabel }}</button>
									</div>
								</div>
							@else
								<div class="case-pdf-empty" aria-label="{{ $detailKindLabel }} preview">
									<div>
										<strong>First page text is unavailable</strong>
										<div>We could not extract readable text from the PDF file for this {{ strtolower($detailKindLabel) }}.</div>
									</div>
								</div>
							@endif
						</div>
					</div>
					<div class="case-preview-footer">
						<p>Need the full document? Request a secure download link and we will email it to your work address.</p>
						<button type="button" class="case-primary-btn" id="openCaseDownloadModalPreview"><i class="fa fa-file-arrow-down"></i> {{ $detailPreviewActionLabel }}</button>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>

<div class="case-modal" id="caseDownloadModal" aria-hidden="true" role="dialog" aria-labelledby="caseDownloadModalTitle">
	<div class="case-modal-dialog">
		<button type="button" class="case-modal-close" id="closeCaseDownloadModal" aria-label="Close">&times;</button>
		<h2 class="case-modal-title" id="caseDownloadModalTitle">{{ $detailModalTitle }}</h2>
		<p class="case-modal-copy">{{ $detailModalCopy }}</p>

		<form class="form lead-form" id="caseLeadForm" method="post" action="{{ $detailRequestAction }}">
			@csrf
			<input type="hidden" name="interest" value="{{ $detailLeadInterest }}">
			@if(!empty($detailLeadIdField) && !empty($detailLeadIdValue))
				<input type="hidden" name="{{ $detailLeadIdField }}" value="{{ $detailLeadIdValue }}">
			@endif
			<input type="hidden" name="requested_resource" value="{{ $caseStudy->display_title }}">
			<input style="display:none;" type="text" name="website" class="honeypot">

			<div class="lead-grid">
				<div class="form-group">
					<label class="field-label" for="lead_name">First name * <span class="field-meta">Required</span></label>
					<input class="lead-field" id="lead_name" type="text" name="name" placeholder="Enter your first name" autocomplete="given-name" required>
				</div>
				<div class="form-group">
					<label class="field-label" for="lead_email">Work email * <span class="field-meta">Required</span></label>
					<input class="lead-field" id="lead_email" type="email" name="email" placeholder="name@company.com" autocomplete="email" required>
				</div>
				<div class="form-group">
					<label class="field-label" for="lead_org">Company <span class="field-meta">Optional</span></label>
					<input class="lead-field" id="lead_org" type="text" name="organization" placeholder="Your company name" autocomplete="organization">
				</div>
				<div class="form-group">
					<label class="field-label" for="lead_job_title">Job title <span class="field-meta">Optional</span></label>
					<select class="lead-field" id="lead_job_title" name="job_title">
						<option value="" selected>Select your job title</option>
					<option>Executive leader</option>
					<option>Technology leader</option>
					<option>Data leader</option>
					<option>Operations leader</option>
					<option>Practitioner or analyst</option>
					</select>
				</div>
			</div>
			@if(!empty($recaptchaSiteKey))
				<div class="form-group captcha-wrap">
					<div id="caseRecaptcha" class="case-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
				</div>
			@endif
			<button class="btn default-background text-light" id="caseLeadSubmitBtn" type="submit">
				<span class="btn-content">
					<span class="btn-loader" aria-hidden="true"></span>
				<span id="caseLeadSubmitText">{{ $detailModalSubmitLabel }}</span>
			</span>
		</button>
			<div class="case-form-status" id="caseFormStatus" aria-live="polite"></div>
			<div class="case-direct-download" id="caseDirectDownload" aria-live="polite"></div>
			<p class="case-form-note">We will send a secure download link to your work email. The link expires in 1 hour.</p>
		</form>
	</div>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
(function () {
	var shareUrl = @json(request()->url());
			var shareTitle = @json($caseStudy->display_title . ' ' . $detailKindLabel . ' | Armely');
	var shareText = @json($metaDescription);

	var nativeHeroBtn = document.getElementById('shareNativeBtnHero');
	var toast = document.getElementById('caseShareToast');
	var modal = document.getElementById('caseDownloadModal');
	var openModalBtn = document.getElementById('openCaseDownloadModal');
	var openModalPreviewBtn = document.getElementById('openCaseDownloadModalPreview');
	var openModalPreviewInlineBtn = document.getElementById('openCaseDownloadModalPreviewInline');
	var closeModalBtn = document.getElementById('closeCaseDownloadModal');
	var firstInput = document.getElementById('lead_name');
	var recaptchaContainer = document.getElementById('caseRecaptcha');
	var recaptchaRendered = false;
	var recaptchaWidgetId = null;
	var leadForm = document.getElementById('caseLeadForm');
	var submitBtn = document.getElementById('caseLeadSubmitBtn');
	var submitText = document.getElementById('caseLeadSubmitText');
	var formStatus = document.getElementById('caseFormStatus');
	var directDownload = document.getElementById('caseDirectDownload');

	function showToast(text) {
		if (!toast) {
			if (window.console && console.info) {
				console.info(text);
			}
			return;
		}
		toast.textContent = text;
		toast.style.display = 'block';
		window.setTimeout(function () {
			toast.style.display = 'none';
		}, 2600);
	}

	function copyLink() {
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(shareUrl).then(function () {
				showToast('Link copied to clipboard.');
			}).catch(function () {
				showToast('Copy failed. Please copy from address bar.');
			});
			return;
		}

		var tempInput = document.createElement('input');
		tempInput.value = shareUrl;
		document.body.appendChild(tempInput);
		tempInput.select();
		try {
			document.execCommand('copy');
			showToast('Link copied to clipboard.');
		} catch (e) {
			showToast('Copy failed. Please copy from address bar.');
		}
		document.body.removeChild(tempInput);
	}

	function nativeShare() {
		if (navigator.share) {
			navigator.share({
				title: shareTitle,
				text: shareText,
				url: shareUrl
			}).then(function () {
				showToast('Share sheet opened.');
			}).catch(function (error) {
				if (!error || error.name !== 'AbortError') {
					copyLink();
				}
			});
			return;
		}

		copyLink();
	}

	if (nativeHeroBtn) {
		nativeHeroBtn.addEventListener('click', nativeShare);
	}

	function openDownloadModal() {
		if (!modal) {
			return;
		}
		modal.classList.add('is-open');
		modal.setAttribute('aria-hidden', 'false');
		document.body.style.overflow = 'hidden';
		setFormStatus('', '');
		setDirectDownload('', '');
		window.setTimeout(function () {
			if (firstInput) {
				firstInput.focus();
			}
			renderRecaptchaWhenReady(0);
		}, 20);
	}

	function closeDownloadModal() {
		if (!modal) {
			return;
		}
		modal.classList.remove('is-open');
		modal.setAttribute('aria-hidden', 'true');
		document.body.style.overflow = '';
	}

	if (openModalBtn) {
		openModalBtn.addEventListener('click', openDownloadModal);
	}

	if (openModalPreviewBtn) {
		openModalPreviewBtn.addEventListener('click', openDownloadModal);
	}

	if (openModalPreviewInlineBtn) {
		openModalPreviewInlineBtn.addEventListener('click', openDownloadModal);
	}

	function setSubmitting(isSubmitting) {
		if (!submitBtn) {
			return;
		}

		submitBtn.disabled = isSubmitting;
		submitBtn.classList.toggle('is-loading', isSubmitting);
		if (submitText) {
			submitText.textContent = isSubmitting ? 'Sending secure link...' : 'Email Download Link';
		}
	}

	function setFormStatus(message, type) {
		if (!formStatus) {
			return;
		}

		formStatus.className = 'case-form-status';
		formStatus.textContent = '';
		if (!message) {
			return;
		}

		formStatus.classList.add(type === 'success' ? 'is-success' : 'is-error');
		formStatus.textContent = message;
	}

	function setDirectDownload(url, expiresAt) {
		if (!directDownload) {
			return;
		}

		directDownload.className = 'case-direct-download';
		directDownload.innerHTML = '';

		if (!url) {
			return;
		}

		var expiresText = expiresAt ? (' Link expires at ' + expiresAt + '.') : '';
		directDownload.classList.add('is-visible');
		directDownload.innerHTML = 'Download now: <a href="' + url + '" target="_blank" rel="noopener noreferrer">Open secure file</a>.' + expiresText;
	}

	if (leadForm) {
		leadForm.addEventListener('submit', function (event) {
			event.preventDefault();
			setFormStatus('', '');
			setDirectDownload('', '');
			setSubmitting(true);

			var formData = new FormData(leadForm);

			fetch(leadForm.action, {
				method: 'POST',
				headers: {
					'Accept': 'application/json',
					'X-Requested-With': 'XMLHttpRequest'
				},
				body: formData,
				credentials: 'same-origin'
			}).then(function (response) {
				return response.json().then(function (payload) {
					return { ok: response.ok, status: response.status, payload: payload };
				});
			}).then(function (result) {
				if (result.ok) {
					var emailSent = !(result.payload && result.payload.email_sent === false);
					var successType = emailSent ? 'success' : 'error';
					setFormStatus(result.payload.message || 'Thanks! Your secure download link has been sent.', successType);
					if (emailSent) {
						setDirectDownload('', '');
					} else {
						setDirectDownload(result.payload.download_url || '', result.payload.expires_at || '');
					}
					leadForm.reset();
					if (typeof grecaptcha !== 'undefined' && grecaptcha.reset && recaptchaRendered) {
						if (recaptchaWidgetId !== null) {
							grecaptcha.reset(recaptchaWidgetId);
						} else {
							grecaptcha.reset();
						}
					}
					return;
				}

				var firstError = 'Unable to submit right now. Please try again.';
				if (result.payload && result.payload.errors) {
					for (var key in result.payload.errors) {
						if (Object.prototype.hasOwnProperty.call(result.payload.errors, key)) {
							firstError = result.payload.errors[key][0];
							break;
						}
					}
				} else if (result.payload && result.payload.message) {
					firstError = result.payload.message;
				}

				setFormStatus(firstError, 'error');
			}).catch(function () {
				setFormStatus('Unable to submit right now. Please check your connection and try again.', 'error');
			}).finally(function () {
				setSubmitting(false);
			});
		});
	}

	function renderRecaptchaWhenReady(retryCount) {
		if (!recaptchaContainer) {
			return;
		}

		if (typeof grecaptcha === 'undefined' || !grecaptcha.render) {
			if (retryCount < 12) {
				window.setTimeout(function () {
					renderRecaptchaWhenReady(retryCount + 1);
				}, 120);
			}
			return;
		}

		if (!recaptchaRendered) {
			recaptchaWidgetId = grecaptcha.render('caseRecaptcha', {
				sitekey: recaptchaContainer.getAttribute('data-sitekey')
			});
			recaptchaRendered = true;
		}
	}

	if (closeModalBtn) {
		closeModalBtn.addEventListener('click', closeDownloadModal);
	}

	if (modal) {
		modal.addEventListener('click', function (event) {
			if (event.target === modal) {
				closeDownloadModal();
			}
		});
	}

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape' && modal && modal.classList.contains('is-open')) {
			closeDownloadModal();
		}
	});
})();
</script>
@endsection

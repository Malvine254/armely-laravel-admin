@php
	$isWhitePaperPage = (bool) ($isWhitePaperPage ?? false);
	$detailKindLabel = $isWhitePaperPage ? 'White Paper' : 'Case Study';
	$detailPluralLabel = $isWhitePaperPage ? 'White Papers' : 'Case Studies';
	$detailPrimaryActionLabel = $isWhitePaperPage ? 'Download full white paper' : 'Request Full Case Study';
	$detailPreviewActionLabel = $isWhitePaperPage ? 'Request full white paper' : 'Request Full Case Study';
	$detailModalTitle = $isWhitePaperPage ? 'Download Full White Paper' : 'Request Full Case Study';
	$detailModalSubmitLabel = $isWhitePaperPage ? 'Email White Paper Link' : 'Request Full Case Study';
	$detailModalCopy = $isWhitePaperPage ? 'Complete the form and we will email your secure download link. No phone number required.' : 'Complete the form and we will follow up with access to the full case study. No phone number required.';
	$detailRequestAction = $detailRequestAction ?? ($isWhitePaperPage ? route('whitepapers.request', $caseStudy->slug) : route('case-studies.lead.submit'));
	$detailLeadInterest = $detailLeadInterest ?? ($isWhitePaperPage ? 'white-papers' : 'case-studies');
	$detailLeadIdField = $detailLeadIdField ?? 'case_study_id';
	$detailLeadIdValue = $detailLeadIdValue ?? ($caseStudy->id ?? null);
	$metaDescription = $metaDescription ?? '';
	$casePreviewSectionTitle = $isWhitePaperPage ? 'White Paper Preview' : 'One-Page Summary';
	$casePreviewSectionDescription = $isWhitePaperPage
		? 'Preview the white paper document before requesting the full file.'
		: 'Preview the one-page summary for a quick overview of the project, outcomes, and solution.';
	$casePreviewOpenLabel = $isWhitePaperPage ? 'Open White Paper' : 'Open One-Pager';
	$caseOnePagerPreviewUrl = trim((string) ($caseStudy->preview_source_url ?? ''));
	$caseOnePagerText = trim((string) ($caseStudy->pdf_preview_text ?? ''));
	$caseOnePagerSections = (array) ($caseStudy->pdf_preview_sections ?? []);
	$caseOnePagerParagraphs = (array) ($caseStudy->pdf_preview_paragraphs ?? []);
	$caseOnePagerDocument = (array) ($caseStudy->one_pager_document ?? []);
	$caseOnePagerContent = trim((string) ($caseStudy->one_pager_content ?? ''));
	$caseOnePagerContentHasH1 = preg_match('/<h1\b/i', $caseOnePagerContent) === 1;
	$caseOnePagerIsPdf = $caseOnePagerPreviewUrl !== ''
		&& \Illuminate\Support\Str::contains(strtolower((string) parse_url($caseOnePagerPreviewUrl, PHP_URL_PATH)), '.pdf');
	$caseHasPublicPreview = $isWhitePaperPage
		? $caseOnePagerPreviewUrl !== ''
		: ($caseOnePagerContent !== '' || $caseOnePagerText !== '' || $caseOnePagerPreviewUrl !== '');
@endphp

@extends('layouts.public')

@section('title', $caseStudy->display_title . ' ' . $detailKindLabel . ' | ' . ($caseStudy->technology_label ?? 'Microsoft Platform') . ' | Armely')
@section('meta_description', $metaDescription)
@section('canonical_url', $isWhitePaperPage ? route('white-papers.view', ['slug' => $caseStudy->slug]) : route('case-studies.show', ['slug' => $caseStudy->slug]))

@push('head')
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $caseStudy->display_title }} {{ $detailKindLabel }} | {{ $caseStudy->technology_label ?? 'Microsoft Platform' }} | Armely">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="@yield('canonical_url')">
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
	--case-document-width: 920px;
	background:
		radial-gradient(1200px 560px at 12% 0%, rgba(47, 85, 151, .08), rgba(47, 85, 151, 0) 60%),
		linear-gradient(180deg, #f7faff 0%, #ffffff 100%);
	color: var(--case-ink);
}
.case-detail-hero .container {
	width: 100%;
	max-width: var(--case-document-width);
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
.case-impact-band {
	padding: 0 0 22px;
	margin-top: -18px;
	position: relative;
	z-index: 2;
}
.case-impact-grid {
	display: grid;
	grid-template-columns: repeat(4, minmax(0, 1fr));
	gap: 14px;
}
.case-impact-card {
	border: 1px solid var(--case-line);
	background: #fff;
	border-radius: 16px;
	box-shadow: 0 14px 32px rgba(28, 54, 93, .08);
	padding: 16px;
	min-height: 118px;
	display: grid;
	gap: 8px;
	align-content: start;
}
.case-impact-card i {
	color: var(--case-blue);
	font-size: 1.05rem;
}
.case-impact-card strong {
	color: var(--case-ink);
	font-size: .96rem;
	font-weight: 900;
	line-height: 1.35;
}
.case-impact-card span {
	color: #50647e;
	font-size: .88rem;
	line-height: 1.5;
}
.case-story-band {
	padding: 18px 0 12px;
}
.case-story-shell {
	max-width: 1040px;
	margin: 0 auto;
	display: grid;
	gap: 16px;
}
.case-story-card {
	border: 1px solid var(--case-line);
	background: #fff;
	box-shadow: 0 14px 34px rgba(28, 54, 93, .07);
	border-radius: 20px;
	padding: 28px;
}
.case-story-card h2 {
	color: var(--case-ink);
	font-size: 1.55rem;
	line-height: 1.15;
	font-weight: 900;
	margin: 0 0 8px;
}
.case-story-card p {
	color: #46586f;
	line-height: 1.75;
	margin: 0;
}
.case-story-grid {
	display: grid;
	grid-template-columns: repeat(2, minmax(0, 1fr));
	gap: 14px;
}
.case-story-block {
	border: 1px solid #e4edf8;
	background: #fbfdff;
	border-radius: 16px;
	padding: 18px;
}
.case-story-block--wide {
	grid-column: 1 / -1;
}
.case-story-block h3 {
	color: var(--case-ink);
	font-size: 1.02rem;
	font-weight: 900;
	margin: 0 0 8px;
}
.case-story-block ul {
	margin: 0;
	padding-left: 18px;
	color: #46586f;
	line-height: 1.7;
}
.case-story-block li {
	margin-bottom: 8px;
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
	background: #edf1f5;
	padding: 42px 0 72px;
}
.case-preview-band.is-document { padding: 0 0 38px; }
.case-document-toolbar-band {
	background: #edf1f5;
	padding: 24px 24px 0;
}
.case-document-toolbar {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 18px;
	width: 100%;
	max-width: var(--case-document-width);
	margin: 0 auto;
	padding: 15px 18px;
	border-bottom: 0;
	background: var(--case-blue);
	position: relative;
	z-index: 2;
	box-shadow: 0 10px 28px rgba(20, 34, 52, .14);
}
.case-document-toolbar-main {
	display: flex;
	align-items: center;
	flex-wrap: wrap;
	gap: 12px 20px;
}
.case-document-back,
.case-document-category,
.case-document-share {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	font-size: .86rem;
	font-weight: 850;
}
.case-document-back,
.case-document-back:visited,
.case-document-back i { color: #fff !important; }
.case-document-back:hover { color: #fff !important; text-decoration: none; opacity: .84; }
.case-document-category {
	color: rgba(255, 255, 255, .82);
	font-size: .74rem;
	letter-spacing: .07em;
	text-transform: uppercase;
}
.case-document-share {
	flex: 0 0 auto;
	padding: 9px 14px;
	border: 1px solid rgba(255, 255, 255, .42);
	background: rgba(255, 255, 255, .10);
	color: #fff;
	cursor: pointer;
}
.case-document-share:hover { border-color: #fff; color: #fff; background: rgba(255, 255, 255, .18); }
.case-document-toolbar-band .case-share-toast {
	max-width: var(--case-document-width);
	margin: 10px auto 0;
	color: var(--case-navy);
	background: #fff;
}
.case-preview-band .container {
	max-width: 100%;
	padding-left: 24px;
	padding-right: 24px;
}
.case-preview-shell {
	width: 100%;
	max-width: var(--case-document-width);
	margin: 0 auto;
	padding: 0;
}
.case-full-case-study {
	display: grid;
	grid-template-columns: 1fr;
	gap: 22px;
	align-items: start;
}
.case-full-case-study-content {
	display: grid;
	gap: 14px;
	align-content: start;
	min-width: 0;
}
.case-full-case-study-card {
	min-width: 0;
}
.case-full-case-study-header {
	display: grid;
	gap: 10px;
	max-width: none;
}
.case-full-case-study-kicker {
	color: var(--case-blue);
	font-size: .8rem;
	font-weight: 900;
	text-transform: uppercase;
	letter-spacing: .12em;
}
.case-full-case-study-title {
	color: var(--case-ink);
	font-size: clamp(1.65rem, 2.8vw, 2.25rem);
	line-height: 1.1;
	font-weight: 900;
	margin: 0;
}
.case-full-case-study-description {
	color: #46586f;
	font-size: 1rem;
	line-height: 1.7;
	margin: 0;
	max-width: 32ch;
}
.case-full-case-study-actions {
	display: grid;
	gap: 10px;
	align-items: start;
}
.case-full-case-study-actions .case-primary-btn {
	width: 100%;
}
.case-full-case-study-card {
	background: #fff;
	border: 1px solid var(--case-line);
	border-radius: 22px;
	box-shadow: 0 18px 42px rgba(28, 54, 93, .10);
	overflow: hidden;
	width: 100%;
	min-height: 520px;
}
.case-full-case-study-viewer {
	width: 100%;
	height: clamp(560px, 58vw, 700px);
	background: #fff;
}
.case-full-case-study-viewer iframe {
	display: block;
	width: 100%;
	height: 100%;
	border: 0;
	background: #fff;
}
.case-onepager-image {
	display: block;
	width: 100%;
	height: 100%;
	object-fit: cover;
	object-position: top center;
	background: #fff;
}
.case-onepager-text {
	position: relative;
	width: min(100%, 920px);
	min-height: 0;
	margin: 0 auto;
	padding: clamp(34px, 4.5vw, 54px);
	color: #26364b;
	font-family: Arial, Helvetica, sans-serif;
	font-size: .96rem;
	line-height: 1.7;
	background: #fff;
	border: 1px solid #d7dce2;
	box-shadow:
		0 2px 3px rgba(20, 34, 52, .08),
		0 24px 65px rgba(20, 34, 52, .18);
}
.case-onepager-text::after {
	content: '1';
	position: absolute;
	right: clamp(34px, 4.5vw, 54px);
	bottom: 18px;
	color: #8b96a5;
	font-size: .72rem;
}
.case-onepager-card.is-document {
	min-height: 0;
	border: 0;
	border-radius: 0;
	background: transparent;
	box-shadow: none;
	overflow: visible;
}
.case-onepager-card.is-document::before { display: none; }
.case-doc-brandbar {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 18px;
	margin: 0 0 25px;
	padding: 0 0 11px;
	border-bottom: 2px solid var(--case-blue);
	color: var(--case-blue);
}
.case-doc-brand {
	font-size: 1.45rem;
	font-weight: 950;
	letter-spacing: .12em;
}
.case-doc-brandbar span:last-child {
	font-size: .76rem;
	font-weight: 900;
	letter-spacing: .12em;
	text-transform: uppercase;
	color: #59687a;
}
.case-doc-headline {
	max-width: 730px;
	margin: 0;
	color: var(--case-ink);
	font-family: Georgia, 'Times New Roman', serif;
	font-size: clamp(1.7rem, 3vw, 2.4rem);
	line-height: 1.16;
	font-weight: 950;
}
.case-onepager-authored-content > :first-child { margin-top: 0; }
.case-onepager-authored-content h1 {
	max-width: 760px;
	margin: 0 0 16px;
	color: var(--case-ink);
	font-family: Georgia, 'Times New Roman', serif;
	font-size: clamp(1.7rem, 3vw, 2.4rem);
	line-height: 1.16;
	font-weight: 900;
}
.case-onepager-authored-content h2,
.case-onepager-authored-content h3,
.case-onepager-authored-content h4 {
	margin: 24px 0 10px;
	color: var(--case-blue);
	font-family: Arial, Helvetica, sans-serif;
	font-size: .86rem;
	line-height: 1.35;
	font-weight: 900;
	letter-spacing: .08em;
	text-transform: uppercase;
}
.case-onepager-authored-content p {
	margin: 0 0 12px;
	line-height: 1.6;
}
.case-onepager-authored-content ul,
.case-onepager-authored-content ol {
	margin: 10px 0 18px;
	padding-left: 1.3rem;
}
.case-onepager-authored-content li { margin-bottom: 7px; line-height: 1.48; }
.case-onepager-authored-content blockquote {
	margin: 22px 0;
	padding: 14px 18px;
	border-left: 3px solid var(--case-blue);
	background: #f5f7fa;
	font-family: Georgia, 'Times New Roman', serif;
}
.case-onepager-authored-content table {
	width: 100%;
	margin: 20px 0;
	border-collapse: collapse;
	font-size: .92rem;
}
.case-onepager-authored-content th,
.case-onepager-authored-content td {
	padding: 11px 14px;
	border: 1px solid var(--case-line);
	text-align: left;
	vertical-align: top;
}
.case-onepager-authored-content th { background: #293d5c; color: #fff; text-transform: uppercase; letter-spacing: .06em; }
.case-onepager-authored-content th:first-child { background: #a94145; }
.case-onepager-authored-content th:last-child { background: #2f7d57; }
.case-onepager-authored-content tr:nth-child(even) td { background: #f8fafc; }
.case-doc-intro {
	max-width: 760px;
	margin: 13px 0 0;
	color: var(--case-muted);
	font-family: Georgia, 'Times New Roman', serif;
	font-size: 1.03rem;
	line-height: 1.58;
}
.case-doc-two-column {
	display: grid;
	grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
	gap: 0;
	margin-top: 25px;
	padding: 20px 0;
	border-top: 1px solid #b9c2ce;
	border-bottom: 1px solid #b9c2ce;
}
.case-doc-panel {
	padding: 0 22px 0 0;
	border: 0;
	border-radius: 0;
	background: transparent;
}
.case-doc-panel.is-solution {
	padding: 0 0 0 22px;
	border-left: 1px solid #c9d0d9;
	background: transparent;
}
.case-doc-panel h4,
.case-doc-comparison h4 {
	margin: 0 0 11px;
	color: var(--case-blue);
	font-family: Arial, Helvetica, sans-serif;
	font-size: .82rem;
	line-height: 1.35;
	font-weight: 900;
	letter-spacing: .08em;
	text-transform: uppercase;
}
.case-doc-list {
	display: grid;
	gap: 8px;
	margin: 0;
	padding: 0;
	list-style: none;
}
.case-doc-list li {
	display: grid;
	grid-template-columns: 22px 1fr;
	gap: 9px;
	line-height: 1.42;
}
.case-doc-list i { color: var(--case-blue); margin-top: 5px; font-size: .78rem; }
.case-doc-panel p { margin: 0; line-height: 1.55; }
.case-doc-panel p + p { margin-top: 9px; }
.case-doc-comparison { margin-top: 25px; }
.case-doc-table-wrap {
	overflow-x: auto;
	border: 1px solid var(--case-line);
	border-radius: 0;
}
.case-doc-table {
	width: 100%;
	border-collapse: collapse;
	background: #fff;
}
.case-doc-table th {
	width: 50%;
	padding: 11px 14px;
	color: #fff;
	background: #a94145;
	font-size: .78rem;
	letter-spacing: .1em;
	text-transform: uppercase;
	text-align: left;
}
.case-doc-table th:last-child { background: #2f7d57; }
.case-doc-table td {
	padding: 11px 14px;
	vertical-align: top;
	line-height: 1.4;
	border-top: 1px solid var(--case-line);
}
.case-doc-table td + td { border-left: 1px solid var(--case-line); }
.case-doc-table tr:nth-child(even) td { background: #f8fafc; }
.case-doc-cta {
	display: grid;
	grid-template-columns: 1fr auto;
	align-items: center;
	gap: 24px;
	margin-top: 25px;
	padding: 18px 21px;
	border-radius: 0;
	background: var(--case-navy);
	color: #fff;
}
.case-doc-cta h4 { margin: 0 0 6px; color: #fff; font-size: 1.2rem; font-weight: 900; }
.case-doc-cta p { margin: 0; color: rgba(255,255,255,.82); line-height: 1.55; }
.case-doc-cta a {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 8px;
	min-height: 46px;
	padding: 11px 20px;
	border-radius: 10px;
	background: #fff;
	color: var(--case-navy);
	font-weight: 900;
	white-space: nowrap;
}
.case-onepager-text-header {
	padding-bottom: 24px;
	margin-bottom: 12px;
	border-bottom: 1px solid var(--case-line);
}
.case-onepager-text-header span {
	display: block;
	color: #1b7a4f;
	font-size: .76rem;
	font-weight: 900;
	letter-spacing: .12em;
	text-transform: uppercase;
	margin-bottom: 10px;
}
.case-onepager-text-header h1,
.case-onepager-text-header h3 {
	margin: 0;
	color: var(--case-ink);
	font-size: clamp(1.45rem, 2.6vw, 2rem);
	line-height: 1.2;
	font-weight: 900;
}
.case-onepager-fallback-copy {
	max-width: 760px;
	margin: 18px 0 0;
	color: var(--case-muted);
	font-family: Georgia, 'Times New Roman', serif;
	font-size: 1.03rem;
	line-height: 1.65;
}
.case-onepager-fallback-note {
	display: flex;
	align-items: flex-start;
	gap: 10px;
	margin-top: 26px;
	padding: 14px 16px;
	border-left: 3px solid var(--case-blue);
	background: #f5f7fa;
	color: #536277;
	font-size: .86rem;
	line-height: 1.55;
}
.case-onepager-text .case-pdf-section h4 {
	font-size: 1.15rem;
}
.case-onepager-text .case-pdf-paragraph + .case-pdf-paragraph {
	margin-top: 12px;
}
.case-onepager-text .case-pdf-paragraph.is-bullet {
	padding-left: 1.15rem;
	text-indent: -1.15rem;
}
.case-onepager-source-link {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	margin-top: 22px;
	padding-top: 10px;
	border-top: 1px solid #d9dee5;
	color: #667486;
	font-size: .78rem;
	font-weight: 700;
}
@media (max-width: 767px) {
	.case-document-toolbar-band { padding: 18px 16px 0; }
	.case-document-toolbar { align-items: flex-start; }
	.case-document-toolbar-main { display: grid; gap: 8px; }
	.case-onepager-text { min-height: 0; padding: 34px 24px 56px; }
	.case-onepager-text::after { right: 24px; bottom: 18px; }
	.case-doc-brandbar { margin-bottom: 28px; }
	.case-doc-two-column { grid-template-columns: 1fr; gap: 26px; }
	.case-doc-panel { padding: 0; }
	.case-doc-panel.is-solution { padding: 26px 0 0; border-left: 0; border-top: 1px solid #c9d0d9; }
	.case-doc-cta { grid-template-columns: 1fr; }
	.case-doc-cta a { width: 100%; }
	.case-doc-table { min-width: 620px; }
}
@media (min-width: 992px) {
	.case-pdf-iframe {
		transform: scale(1.05);
		transform-origin: top center;
	}
}
.case-full-case-study-mobile {
	display: none;
	padding: 24px;
	gap: 16px;
	background: #fff;
}
.case-full-case-study-mobile strong {
	display: block;
	color: var(--case-ink);
	font-size: 1.05rem;
	font-weight: 900;
	margin-bottom: 6px;
}
.case-full-case-study-mobile p {
	margin: 0;
	color: #46586f;
	line-height: 1.65;
}
.case-full-case-study-mobile-actions {
	display: flex;
	flex-wrap: wrap;
	gap: 12px;
}
.case-full-case-study-fallback {
	display: grid;
	gap: 16px;
	padding: 28px;
	min-height: 360px;
	align-content: center;
	background:
		linear-gradient(180deg, rgba(47, 85, 151, .04), rgba(255, 255, 255, 0)),
		#fff;
}
.case-full-case-study-fallback-visual {
	display: inline-grid;
	place-items: center;
	width: 68px;
	height: 68px;
	border-radius: 18px;
	background: rgba(47, 85, 151, .10);
	color: var(--case-blue);
	font-size: 1.7rem;
}
.case-full-case-study-fallback h3 {
	color: var(--case-ink);
	font-size: 1.2rem;
	font-weight: 900;
	margin: 0;
}
.case-full-case-study-fallback p {
	margin: 0;
	color: #46586f;
	line-height: 1.7;
	max-width: 60ch;
}
.case-full-case-study-fallback-actions {
	display: flex;
	flex-wrap: wrap;
	gap: 12px;
	align-items: center;
}
.case-summary-download-link {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	color: var(--case-blue);
	font-weight: 800;
	text-decoration: none;
	font-size: .95rem;
}
.case-summary-download-link:hover {
	color: var(--case-navy);
	text-decoration: underline;
}
.case-request-band {
	padding: 10px 0 52px;
}
.case-request-card {
	max-width: var(--case-document-width);
	margin: 0 auto;
	border: 1px solid var(--case-line);
	background: linear-gradient(135deg, rgba(47, 85, 151, .05), #fff 62%);
	box-shadow: 0 16px 36px rgba(28, 54, 93, .08);
	border-radius: 22px;
	padding: 24px clamp(28px, 4.5vw, 54px);
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 18px;
}
.case-request-card h2 {
	color: var(--case-ink);
	font-size: 1.45rem;
	font-weight: 900;
	margin: 0 0 8px;
}
.case-request-card p {
	margin: 0;
	color: #46586f;
	line-height: 1.65;
}
.case-request-card .case-primary-btn {
	min-width: 240px;
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
.case-preview-card {
	padding: 0;
	border-radius: 0;
	box-shadow: none;
}
.case-preview-side { padding: 22px; }
.case-pdf-stage {
	position: relative;
	overflow: hidden;
	background: #fff;
	min-height: 0;
	padding: 0;
	display: block;
}
.case-pdf-stage-inner {
	width: 100%;
	margin: 0 auto;
	position: relative;
}
.case-pdf-page {
	width: 100%;
	border: 0;
	border-radius: 0;
	background: transparent;
	box-shadow: none;
	padding: 0;
}
.case-pdf-page-title {
	display: none;
}
.case-pdf-page-body {
	position: relative;
	color: #32465f;
	font-size: 1rem;
	line-height: 1.85;
	max-height: none;
	overflow: visible;
	padding-right: 0;
}
.case-pdf-preview-meta {
	display: none;
}
.case-pdf-meta-chip {
	display: none;
}
.case-pdf-meta-chip.is-muted {
	display: none;
}
.case-pdf-page-copy {
	display: grid;
	gap: 0;
}
.case-pdf-iframe-shell {
	width: 100%;
	border: 0;
	border-radius: 0;
	overflow: hidden;
	background: transparent;
	box-shadow: none;
}
.case-pdf-iframe {
	display: block;
	width: 100%;
	min-height: 1120px;
	border: 0;
	background: transparent;
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
	display: none;
}
.case-pdf-request {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 16px;
	margin-top: 14px;
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
	padding-top: 12px;
	margin-top: 12px;
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
textarea.lead-field {
	padding-top: 12px;
	padding-bottom: 12px;
	min-height: 120px;
	resize: vertical;
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
/* --- Free / locked framing --- */
.case-section-head {
	display: grid;
	gap: 10px;
	justify-items: center;
	text-align: center;
	max-width: 720px;
	margin: 0 auto 24px;
}
.case-section-head h2 {
	color: var(--case-ink);
	font-size: clamp(1.5rem, 2.4vw, 2rem);
	font-weight: 900;
	line-height: 1.14;
	margin: 0;
}
.case-section-head p {
	color: var(--case-muted);
	font-size: 1rem;
	line-height: 1.7;
	margin: 0;
}
.case-eyebrow-badge {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	padding: 6px 14px;
	border-radius: 999px;
	font-size: .74rem;
	font-weight: 900;
	letter-spacing: .1em;
	text-transform: uppercase;
}
.case-eyebrow-badge.is-free {
	background: rgba(27, 122, 79, .12);
	color: #1b7a4f;
	border: 1px solid rgba(27, 122, 79, .26);
}
.case-eyebrow-badge.is-locked {
	background: rgba(47, 85, 151, .12);
	color: var(--case-blue);
	border: 1px solid rgba(47, 85, 151, .26);
}
.case-onepager-card { position: relative; }
.case-onepager-card::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	height: 4px;
	background: linear-gradient(90deg, #1b7a4f, #2bb673);
	z-index: 3;
}
.case-locked-band { padding: 8px 0 72px; }
.case-locked-shell {
	max-width: var(--case-document-width);
	margin: 0 auto;
}
.case-locked-card {
	position: relative;
	border: 1px solid var(--case-line);
	border-radius: 22px;
	overflow: hidden;
	background: #fff;
	box-shadow: 0 22px 50px rgba(28, 54, 93, .12);
	min-height: 480px;
}
.case-locked-card::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	height: 4px;
	background: linear-gradient(90deg, var(--case-blue), var(--case-navy));
	z-index: 4;
}
.case-locked-doc {
	filter: blur(7px);
	opacity: .5;
	transform: scale(1.02);
	pointer-events: none;
	user-select: none;
	padding: 30px;
}
.case-locked-overlay {
	position: absolute;
	inset: 0;
	display: grid;
	place-items: center;
	padding: 28px 24px;
	z-index: 2;
	background:
		radial-gradient(640px 340px at 50% 36%, rgba(255, 255, 255, .35), rgba(255, 255, 255, .85) 70%),
		linear-gradient(180deg, rgba(255, 255, 255, .55), rgba(247, 250, 255, .94));
}
.case-locked-inner {
	width: 100%;
	max-width: 520px;
	display: grid;
	gap: 16px;
	justify-items: center;
	text-align: center;
}
.case-lock-badge {
	display: inline-grid;
	place-items: center;
	width: 64px;
	height: 64px;
	border-radius: 20px;
	background: linear-gradient(135deg, var(--case-blue), var(--case-navy));
	color: #fff;
	font-size: 1.5rem;
	box-shadow: 0 16px 34px rgba(31, 63, 118, .30);
}
.case-locked-inner h3 {
	color: var(--case-ink);
	font-size: clamp(1.3rem, 2.2vw, 1.7rem);
	font-weight: 900;
	margin: 0;
}
.case-locked-inner > p {
	color: var(--case-muted);
	line-height: 1.7;
	margin: 0;
	max-width: 46ch;
}
.case-locked-points {
	display: grid;
	gap: 9px;
	margin: 2px 0;
	padding: 0;
	list-style: none;
	text-align: left;
}
.case-locked-points li {
	display: flex;
	align-items: flex-start;
	gap: 10px;
	color: var(--case-ink);
	font-weight: 700;
	font-size: .95rem;
	line-height: 1.45;
}
.case-locked-points li i {
	color: var(--case-blue);
	margin-top: 3px;
}
.case-locked-cta {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 9px;
	min-height: 52px;
	padding: 13px 28px;
	border: 0;
	border-radius: 12px;
	background: var(--case-blue);
	color: #fff;
	font-weight: 900;
	font-size: 1rem;
	cursor: pointer;
	box-shadow: 0 16px 30px rgba(31, 63, 118, .26);
	transition: background .2s ease, transform .15s ease;
}
.case-locked-cta:hover {
	background: var(--case-navy);
	transform: translateY(-1px);
}
.case-locked-note {
	color: var(--case-muted);
	font-size: .85rem;
	margin: 0;
}
@media (max-width: 991px) {
	.case-detail-hero { padding-top: 24px; }
	.case-hero-layout,
	.case-detail-grid,
	.case-result-grid,
	.case-preview-grid { grid-template-columns: 1fr; }
	.case-detail-title { font-size: 2.15rem; }
	.case-sidebar { position: static; }
	.case-impact-grid,
	.case-story-grid { grid-template-columns: 1fr 1fr; }
	.case-mock-metrics { grid-template-columns: 1fr; }
	.case-pdf-highlight-grid { grid-template-columns: 1fr; }
	.case-full-case-study { grid-template-columns: 1fr; }
	.case-full-case-study-content,
	.case-full-case-study-card { grid-column: auto; }
	.case-full-case-study-actions { display: none; }
	.case-full-case-study-viewer { display: none; }
	.case-full-case-study-mobile { display: grid; }
	.case-request-card { flex-direction: column; align-items: stretch; }
	.case-request-card .case-primary-btn { width: 100%; min-width: 0; }
}
@media (max-width: 575px) {
	.case-detail-title { font-size: 1.85rem; }
	.case-content-card,
	.case-panel,
	.case-share-card,
	.case-lead-card,
	.case-preview-card,
	.case-preview-side,
	.case-story-card { padding: 18px; }
	.case-impact-grid,
	.case-story-grid { grid-template-columns: 1fr; }
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
	.case-pdf-overlay {
		left: 12px;
		right: 12px;
		bottom: 12px;
		flex-direction: column;
		align-items: stretch;
	}
	.case-pdf-mock { padding: 14px; min-height: 520px; }
	.case-mock-title { font-size: 1.55rem; }
	.case-preview-band .container { padding-left: 16px; padding-right: 16px; }
	.case-full-case-study-card { border-radius: 18px; }
	.case-full-case-study-mobile { padding: 18px; }
	.case-full-case-study-fallback { padding: 20px; min-height: 0; }
	.case-full-case-study-mobile-actions .case-primary-btn,
	.case-full-case-study-mobile-actions .case-secondary-btn,
	.case-full-case-study-actions .case-primary-btn,
	.case-full-case-study-actions .case-secondary-btn { width: 100%; min-width: 0; }
	.case-request-band { padding-bottom: 50px; }
	.case-locked-band { padding-bottom: 52px; }
	.case-locked-doc { padding: 18px; }
	.case-locked-overlay { padding: 20px 14px; }
	.case-locked-cta { width: 100%; }
}
</style>
@endpush

@section('content')
@php($caseStudyOutcomeLabel = trim((string) ($caseStudy->outcome_tag ?? '')))
<main class="case-detail-page">
	@if($isWhitePaperPage)
	<section class="case-detail-hero">
		<div class="container">
			<a class="case-back-link text-light" href="{{ ($isWhitePaperPage ?? false) ? route('case-studies.index') . '#white-papers' : route('case-studies.index') }}"><i class="fa fa-arrow-left"></i> Back to {{ $detailPluralLabel }}</a>
			<div class="case-hero-layout">
				<div>
					<div class="case-detail-eyebrow"><i class="fa fa-layer-group"></i> {{ $caseStudy->category ?? $detailKindLabel }}</div>
					<h1 class="case-detail-title">{{ $caseStudy->display_title }}</h1>
					<p class="case-detail-summary text-light">{{ $caseStudy->preview }}</p>
					<div class="case-hero-actions">
						<button type="button" class="case-primary-btn" data-open-case-modal><i class="fa fa-file-arrow-down"></i> {{ $detailPrimaryActionLabel }}</button>
						<button type="button" class="case-secondary-btn" id="shareNativeBtnHero"><i class="fa fa-share-alt"></i> Share</button>
					</div>
					<div class="case-share-toast" id="caseShareToast" aria-live="polite"></div>
				</div>
			</div>
		</div>
	</section>
	@else
		<section class="case-document-toolbar-band" aria-label="Case study navigation">
			<div class="case-document-toolbar">
				<div class="case-document-toolbar-main">
					<a class="case-document-back" href="{{ route('case-studies.index') }}"><i class="fa fa-arrow-left"></i> Back to Case Studies</a>
					<span class="case-document-category"><i class="fa fa-layer-group"></i> {{ $caseStudy->category ?? $detailKindLabel }}</span>
				</div>
				<button type="button" class="case-document-share" id="shareNativeBtnHero"><i class="fa fa-share-alt"></i> Share</button>
			</div>
			<div class="case-share-toast" id="caseShareToast" aria-live="polite"></div>
		</section>
	@endif

	@if($caseHasPublicPreview)
		<section class="case-preview-band{{ !$isWhitePaperPage ? ' is-document' : '' }}" id="caseOnePager">
			<div class="container">
				@if($isWhitePaperPage)
					<div class="case-section-head">
						<span class="case-eyebrow-badge is-free"><i class="fa fa-unlock"></i> Free preview &mdash; no email needed</span>
						<h2>{{ $casePreviewSectionTitle }}</h2>
						<p>{{ $casePreviewSectionDescription }}</p>
					</div>
				@endif
				<div class="case-preview-shell case-full-case-study">
					<div class="case-full-case-study-card case-onepager-card{{ !$isWhitePaperPage ? ' is-document' : '' }}" aria-label="{{ $casePreviewSectionTitle }} preview">
						@if(!$isWhitePaperPage)
							<article class="case-onepager-text">
								<div class="case-doc-brandbar">
									<strong class="case-doc-brand">ARMELY</strong>
									<span>{{ $caseOnePagerDocument['eyebrow'] ?? 'Case Study' }}</span>
								</div>
								@if(!empty($caseOnePagerDocument))
									<header>
										<h1 class="case-doc-headline">{{ $caseOnePagerDocument['headline'] ?? $caseStudy->display_title }}</h1>
										@if(!empty($caseOnePagerDocument['intro']))
											<p class="case-doc-intro">{{ $caseOnePagerDocument['intro'] }}</p>
										@endif
									</header>
									@if(!empty($caseOnePagerDocument['challenges']) || !empty($caseOnePagerDocument['solution']))
										<div class="case-doc-two-column">
											<section class="case-doc-panel">
												<h4>{{ $caseOnePagerDocument['challenge_heading'] ?? 'The challenge' }}</h4>
												<ul class="case-doc-list">
													@foreach((array) ($caseOnePagerDocument['challenges'] ?? []) as $challenge)
														<li><i class="fa fa-circle-check"></i><span>{{ $challenge }}</span></li>
													@endforeach
												</ul>
											</section>
											<section class="case-doc-panel is-solution">
												<h4>{{ $caseOnePagerDocument['solution_heading'] ?? 'What Armely built' }}</h4>
												@foreach((array) ($caseOnePagerDocument['solution'] ?? []) as $paragraph)
													<p>{{ $paragraph }}</p>
												@endforeach
											</section>
										</div>
									@endif
									@if(!empty($caseOnePagerDocument['comparisons']))
										<section class="case-doc-comparison">
											<h4>{{ $caseOnePagerDocument['table_heading'] ?? 'At a glance' }}</h4>
											<div class="case-doc-table-wrap">
												<table class="case-doc-table">
													<thead><tr><th>{{ $caseOnePagerDocument['before_heading'] ?? 'Before' }}</th><th>{{ $caseOnePagerDocument['after_heading'] ?? 'After' }}</th></tr></thead>
													<tbody>
														@foreach($caseOnePagerDocument['comparisons'] as $comparison)
															<tr><td>{{ $comparison['before'] }}</td><td>{{ $comparison['after'] }}</td></tr>
														@endforeach
													</tbody>
												</table>
											</div>
										</section>
									@endif
									@if(!empty($caseOnePagerDocument['cta_heading']))
										<aside class="case-doc-cta">
											<div><h4>{{ $caseOnePagerDocument['cta_heading'] }}</h4><p>{{ $caseOnePagerDocument['cta_body'] ?? '' }}</p></div>
											<a href="{{ route('contact') }}">Let's talk <i class="fa fa-arrow-right"></i></a>
										</aside>
									@endif
								@elseif($caseOnePagerContent !== '')
									@if(!$caseOnePagerContentHasH1)
										<h1 class="case-doc-headline">{{ $caseStudy->display_title }}</h1>
									@endif
									<div class="case-onepager-authored-content">{!! $caseOnePagerContent !!}</div>
								@else
									<h1 class="case-doc-headline">{{ $caseStudy->display_title }}</h1>
									<p class="case-doc-intro">{{ $caseStudy->preview }}</p>
								@endif
								@if($caseOnePagerPreviewUrl !== '')
									<a class="case-onepager-source-link" href="{{ $caseOnePagerPreviewUrl }}" target="_blank" rel="noopener">
										<i class="fa fa-up-right-from-square"></i> Open source one-pager PDF
									</a>
								@endif
							</article>
						@else
							<div class="case-full-case-study-viewer">
							@if($caseOnePagerIsPdf)
								<iframe
									class="case-pdf-iframe"
									src="{{ $caseOnePagerPreviewUrl }}#page=1&zoom=115&toolbar=0&navpanes=0&scrollbar=0"
									title="{{ $casePreviewSectionTitle }} of {{ $caseStudy->display_title }}"
									loading="lazy"
									referrerpolicy="no-referrer"
								></iframe>
							@else
								<img
									class="case-onepager-image"
									src="{{ $caseOnePagerPreviewUrl }}"
									alt="{{ $casePreviewSectionTitle }} of {{ $caseStudy->display_title }}"
									loading="lazy"
								>
							@endif
							</div>
						@endif
						@if($isWhitePaperPage)
						<div class="case-full-case-study-mobile">
							<div>
								<strong>{{ $casePreviewSectionTitle }}</strong>
								<p>{{ $casePreviewSectionDescription }} Open it in a new tab on mobile.</p>
							</div>
							<div class="case-full-case-study-mobile-actions">
								<a class="case-summary-download-link" href="{{ $caseOnePagerPreviewUrl }}" target="_blank" rel="noopener">
									<i class="fa fa-up-right-from-square"></i> {{ $casePreviewOpenLabel }}
								</a>
								<button type="button" class="case-primary-btn" data-open-case-modal>
									<i class="fa fa-file-arrow-down"></i> {{ $detailPrimaryActionLabel }}
								</button>
							</div>
						</div>
						@endif
					</div>
					
				</div>
			</div>
		</section>
	@endif

	@if(!$isWhitePaperPage)
		<section class="case-request-band">
			<div class="container">
				<div class="case-request-card">
					<div>
						<h2>Want the full case study?</h2>
						<p>Request access and our team will share the full case study with you.</p>
						<p class="case-full-case-study-note">The full case study is available by request.</p>
					</div>
					<button type="button" class="case-primary-btn" data-open-case-modal>
						<i class="fa fa-file-arrow-down"></i> Request Full Case Study
					</button>
				</div>
			</div>
		</section>
	@else
	<section class="case-locked-band">
		<div class="container">
			<div class="case-section-head">
				<span class="case-eyebrow-badge is-locked"><i class="fa fa-lock"></i> Full {{ $detailKindLabel }}</span>
				<h2>Unlock the complete {{ strtolower($detailKindLabel) }}</h2>
				<p>The one-pager above is yours free. Request the full {{ strtolower($detailKindLabel) }} for the complete challenge, solution approach, and quantified results.</p>
			</div>
			<div class="case-locked-shell">
				<div class="case-locked-card">
					<div class="case-locked-doc" aria-hidden="true">
						<div class="case-mock-hero">
							<div class="case-mock-title">{{ $caseStudy->display_title }}</div>
							<div class="case-mock-summary">{{ $caseStudy->preview }}</div>
						</div>
						<div class="case-mock-metrics">
							<div class="case-mock-metric"><strong>&bull;&bull;&bull;</strong><span>Reporting speed</span></div>
							<div class="case-mock-metric"><strong>&bull;&bull;&bull;</strong><span>Manual effort</span></div>
							<div class="case-mock-metric"><strong>&bull;&bull;&bull;</strong><span>Time to insight</span></div>
						</div>
						<div class="case-mock-section">
							<h4>The challenge</h4>
							<div class="case-mock-line long"></div>
							<div class="case-mock-line mid"></div>
							<div class="case-mock-line short"></div>
						</div>
						<div class="case-mock-section">
							<h4>The solution</h4>
							<div class="case-mock-line long"></div>
							<div class="case-mock-line mid"></div>
							<div class="case-mock-line long"></div>
							<div class="case-mock-line short"></div>
						</div>
					</div>
					<div class="case-locked-overlay">
						<div class="case-locked-inner">
							<span class="case-lock-badge"><i class="fa fa-lock"></i></span>
							<h3>Get the full {{ strtolower($detailKindLabel) }}</h3>
							<p>{{ $detailModalCopy }}</p>
							<ul class="case-locked-points">
								<li><i class="fa fa-check"></i> Full challenge, approach, and architecture</li>
								<li><i class="fa fa-check"></i> Quantified business outcomes and results</li>
								<li><i class="fa fa-check"></i> Secure PDF link emailed instantly</li>
							</ul>
							<button type="button" class="case-locked-cta" data-open-case-modal>
								<i class="fa fa-unlock-keyhole"></i> {{ $detailPrimaryActionLabel }}
							</button>
							<p class="case-locked-note">No phone number required. The link expires in 1 hour.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	@endif
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
				<div class="form-group lead-col-full">
					<label class="field-label" for="lead_message">Message or reason for request <span class="field-meta">Optional</span></label>
					<textarea class="lead-field" id="lead_message" name="message" placeholder="Tell us why you need the full case study and how your team plans to use it."></textarea>
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
			<p class="case-form-note">We will send a secure access link to your work email. The link expires in 1 hour.</p>
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

	var openModalButtons = document.querySelectorAll('[data-open-case-modal]');
	if (openModalButtons && openModalButtons.length) {
		openModalButtons.forEach(function (button) {
			button.addEventListener('click', openDownloadModal);
		});
	}

	function setSubmitting(isSubmitting) {
		if (!submitBtn) {
			return;
		}

		submitBtn.disabled = isSubmitting;
		submitBtn.classList.toggle('is-loading', isSubmitting);
		if (submitText) {
			submitText.textContent = isSubmitting ? 'Sending request...' : @json($detailModalSubmitLabel);
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
		directDownload.innerHTML = 'Access now: <a href="' + url + '" target="_blank" rel="noopener noreferrer">Open secure link</a>.' + expiresText;
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
					setFormStatus(result.payload.message || 'Thanks! Your request has been received. We will email your access link shortly.', successType);
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

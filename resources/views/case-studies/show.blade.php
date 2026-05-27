@extends('layouts.public')

@section('title', $caseStudy->display_title . ' Case Study | ' . ($caseStudy->technology_label ?? 'Microsoft Platform') . ' | Armely')
@section('meta_description', $metaDescription)

@push('head')
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $caseStudy->display_title }} Case Study | {{ $caseStudy->technology_label ?? 'Microsoft Platform' }} | Armely">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ request()->url() }}">
@if(!empty($caseStudy->listing_image) && file_exists(public_path('images/case-study/' . $caseStudy->listing_image)))
<meta property="og:image" content="{{ asset('images/case-study/' . $caseStudy->listing_image) }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $caseStudy->display_title }} Case Study | {{ $caseStudy->technology_label ?? 'Microsoft Platform' }} | Armely">
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
	--case-muted: #5f6f86;
	--case-line: #d9e4f2;
	--case-panel: #ffffff;
	--case-soft: #f5f8fc;
	--case-blue: #2f5597;
	--case-navy: #18345f;
	--case-teal: #2f5597;
	--case-gold: #22447d;
	background: #fff;
	color: var(--case-ink);
}
.case-detail-hero {
	background:
		linear-gradient(90deg, rgba(47, 85, 151, .14) 0, rgba(47, 85, 151, 0) 36%),
		linear-gradient(180deg, #eef4fb 0%, #fff 100%);
	border-bottom: 1px solid var(--case-line);
	padding: 34px 0 46px;
}
.case-back-link {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	color: var(--case-navy);
	font-weight: 800;
	font-size: .92rem;
	margin-bottom: 28px;
}
.case-back-link:hover { color: var(--case-blue); text-decoration: none; }
.case-hero-layout {
	display: grid;
	grid-template-columns: minmax(0, 1.24fr) minmax(300px, .76fr);
	gap: 34px;
	align-items: stretch;
}
.case-detail-eyebrow {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	color: var(--case-navy);
	font-weight: 800;
	text-transform: uppercase;
	font-size: .78rem;
	letter-spacing: .08em;
	margin-bottom: 14px;
}
.case-detail-title {
	color: var(--case-ink);
	font-size: 3.05rem;
	line-height: 1.08;
	font-weight: 900;
	margin: 0 0 18px;
	max-width: 880px;
}
.case-detail-summary {
	color: var(--case-muted);
	font-size: 1.1rem;
	line-height: 1.8;
	max-width: 850px;
	margin-bottom: 24px;
}
.case-hero-actions { display: flex; flex-wrap: wrap; gap: 12px; }
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
.case-primary-btn { background: var(--case-blue); color: #fff; }
.case-primary-btn:hover { color: #fff; background: var(--case-navy); text-decoration: none; }
.case-secondary-btn { background: #fff; color: var(--case-navy); border-color: var(--case-line); }
.case-secondary-btn:hover { color: var(--case-blue); border-color: #b9cbe4; text-decoration: none; }
.case-hero-card {
	background: #fff;
	border: 1px solid var(--case-line);
	box-shadow: 0 20px 50px rgba(35, 62, 105, .12);
	display: flex;
	flex-direction: column;
	min-height: 100%;
}
.case-hero-visual {
	height: 214px;
	background: #e8eff8;
	display: flex;
	align-items: center;
	justify-content: center;
	overflow: hidden;
}
.case-hero-visual img { width: 100%; height: 100%; object-fit: cover; }
.case-hero-fallback {
	width: 100%;
	height: 100%;
	display: flex;
	align-items: center;
	justify-content: center;
	background:
		linear-gradient(135deg, rgba(47,85,151,.18), rgba(30,58,109,.16)),
		#f3f7fc;
	color: var(--case-blue);
	font-size: 4rem;
}
.case-hero-card-body { padding: 22px; }
.case-hero-card-title { color: var(--case-ink); font-weight: 900; font-size: 1.25rem; margin: 8px 0 10px; }
.case-hero-card-copy { color: var(--case-muted); line-height: 1.65; margin: 0; }
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
.lead-form .g-recaptcha {
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
	.case-result-grid { grid-template-columns: 1fr; }
	.case-detail-title { font-size: 2.15rem; }
	.case-sidebar { position: static; }
}
@media (max-width: 575px) {
	.case-detail-title { font-size: 1.85rem; }
	.case-content-card,
	.case-panel,
	.case-share-card,
	.case-lead-card { padding: 18px; }
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
}
</style>
@endpush

@section('content')
<main class="case-detail-page">
	<section class="case-detail-hero">
		<div class="container">
			<a class="case-back-link" href="{{ route('case-studies.index') }}"><i class="fa fa-arrow-left"></i> Back to case studies</a>
			<div class="case-hero-layout">
				<div>
					<div class="case-detail-eyebrow">{{ $caseStudy->category ?? 'Case Study' }}</div>
					<h1 class="case-detail-title">{{ $caseStudy->display_title }}</h1>
					<p class="case-detail-summary">{{ $caseStudy->preview }}</p>
					<div class="case-hero-actions">
						<button type="button" class="case-primary-btn" id="openCaseDownloadModal"><i class="fa fa-file-arrow-down"></i> Download full case study</button>
						<button type="button" class="case-secondary-btn" id="shareNativeBtnHero"><i class="fa fa-share-alt"></i> Share</button>
					</div>
				</div>

				<aside class="case-hero-card" aria-label="Case study overview">
					<div class="case-hero-visual">
						@if(!empty($caseStudy->listing_image) && file_exists(public_path('images/case-study/' . $caseStudy->listing_image)))
							<img src="{{ asset('images/case-study/' . $caseStudy->listing_image) }}" alt="{{ $caseStudy->display_title }}" loading="lazy">
						@else
							<div class="case-hero-fallback" aria-hidden="true"><i class="fa fa-chart-line"></i></div>
						@endif
					</div>
					<div class="case-hero-card-body">
						<div class="case-hero-card-title">{{ $caseStudy->technology_label ?? 'Microsoft Platform' }}</div>
						<p class="case-hero-card-copy">Preview the challenge, the Armely solution approach, and the measurable results before requesting the full PDF.</p>
					</div>
				</aside>
			</div>
		</div>
	</section>

	<section class="case-detail-band">
		<div class="container">
			<div class="case-detail-grid">
				<article>
					<div class="case-content-card">
						<h2>What Changed</h2>
						<div class="case-detail-body">
							{!! $caseStudy->body !!}
						</div>
					</div>

					<div class="case-panel">
						<h3>Armely Services Involved</h3>
						<ul>
							@foreach($caseStudy->services as $service)
								<li>{{ $service }}</li>
							@endforeach
						</ul>
					</div>
				</article>

				<aside class="case-sidebar">
					<div class="case-download-cta" id="download">
						<h3>Download Full Case Study</h3>
						<p>Request the full PDF and we will email a secure link to your work email.</p>
						<button type="button" class="case-download-open" id="openCaseDownloadModalSidebar">Open Download Form</button>
					</div>

					<div class="case-share-card">
						<h3>Share This Case Study</h3>
						<div class="case-share-grid">
							<button type="button" class="case-share-btn" id="shareNativeBtn"><i class="fa fa-share-alt"></i>Share</button>
							<button type="button" class="case-share-btn" id="shareCopyBtn"><i class="fa fa-link"></i>Copy Link</button>
							<a class="case-share-btn" href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-linkedin"></i>LinkedIn</a>
							<a class="case-share-btn" href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($caseStudy->display_title . ' Case Study') }}" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-x-twitter"></i>X</a>
						</div>
						<div class="case-share-toast" id="caseShareToast">Link copied to clipboard.</div>
					</div>

					@if($relatedCaseStudies->isNotEmpty())
						<div class="case-panel">
							<h3>Related Case Studies</h3>
							@foreach($relatedCaseStudies as $related)
								<a class="related-card" href="{{ route('case-studies.show', $related->slug) }}">
									<strong>{{ trim((string) ($related->title ?? '')) ?: (($related->category ?? 'Case Study') . ' Case Study') }}</strong>
									<div class="text-muted">{{ $related->preview }}</div>
								</a>
							@endforeach
						</div>
					@endif
				</aside>
			</div>
		</div>
	</section>
</main>

<div class="case-modal" id="caseDownloadModal" aria-hidden="true" role="dialog" aria-labelledby="caseDownloadModalTitle">
	<div class="case-modal-dialog">
		<button type="button" class="case-modal-close" id="closeCaseDownloadModal" aria-label="Close">&times;</button>
		<h2 class="case-modal-title" id="caseDownloadModalTitle">Download Full Case Study</h2>
		<p class="case-modal-copy">Complete the form and we will email your secure download link. No phone number required.</p>

		<form class="form lead-form" id="caseLeadForm" method="post" action="{{ route('case-studies.lead.submit') }}">
			@csrf
			<input type="hidden" name="interest" value="case-studies">
			<input type="hidden" name="case_study_id" value="{{ $caseStudy->id }}">
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
					<div id="caseRecaptcha" class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
				</div>
			@endif
			<button class="btn default-background text-light" id="caseLeadSubmitBtn" type="submit">
				<span class="btn-content">
					<span class="btn-loader" aria-hidden="true"></span>
					<span id="caseLeadSubmitText">Email Download Link</span>
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
	var shareUrl = '{{ request()->url() }}';
	var shareTitle = '{{ addslashes($caseStudy->display_title) }} Case Study | Armely';
	var shareText = '{{ addslashes($metaDescription) }}';

	var nativeBtn = document.getElementById('shareNativeBtn');
	var nativeHeroBtn = document.getElementById('shareNativeBtnHero');
	var copyBtn = document.getElementById('shareCopyBtn');
	var toast = document.getElementById('caseShareToast');
	var modal = document.getElementById('caseDownloadModal');
	var openModalBtn = document.getElementById('openCaseDownloadModal');
	var openModalSidebarBtn = document.getElementById('openCaseDownloadModalSidebar');
	var closeModalBtn = document.getElementById('closeCaseDownloadModal');
	var firstInput = document.getElementById('lead_name');
	var recaptchaContainer = document.getElementById('caseRecaptcha');
	var recaptchaRendered = false;
	var leadForm = document.getElementById('caseLeadForm');
	var submitBtn = document.getElementById('caseLeadSubmitBtn');
	var submitText = document.getElementById('caseLeadSubmitText');
	var formStatus = document.getElementById('caseFormStatus');
	var directDownload = document.getElementById('caseDirectDownload');

	function showToast(text) {
		if (!toast) {
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
			}).catch(function () {
				// User canceled or share unavailable.
			});
			return;
		}

		copyLink();
	}

	if (copyBtn) {
		copyBtn.addEventListener('click', copyLink);
	}

	if (nativeBtn) {
		nativeBtn.addEventListener('click', nativeShare);
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

	if (openModalSidebarBtn) {
		openModalSidebarBtn.addEventListener('click', openDownloadModal);
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
					var successType = result.payload && result.payload.email_sent === false ? 'error' : 'success';
					setFormStatus(result.payload.message || 'Thanks! Your secure download link has been sent.', successType);
					setDirectDownload(result.payload.download_url || '', result.payload.expires_at || '');
					leadForm.reset();
					if (typeof grecaptcha !== 'undefined' && grecaptcha.reset && recaptchaRendered) {
						grecaptcha.reset();
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
			grecaptcha.render('caseRecaptcha', {
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

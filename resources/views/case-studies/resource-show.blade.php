@extends('layouts.public')

@section('title', ($paper->title ?? 'White Paper') . ' | Microsoft Platform Guidance | Armely')
@section('meta_description', $metaDescription)

@push('head')
<meta property="og:type" content="article">
<meta property="og:title" content="{{ ($paper->title ?? 'White Paper') }} | Microsoft Platform Guidance | Armely">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ request()->url() }}">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ ($paper->title ?? 'White Paper') }} | Microsoft Platform Guidance | Armely">
<meta name="twitter:description" content="{{ $metaDescription }}">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/case-studies-modern.css') }}">
<style>
.resource-hero { background:#f8f9fa; padding:72px 0 42px; }
.resource-eyebrow { color:#2f5597; font-weight:800; text-transform:uppercase; font-size:.82rem; letter-spacing:.08em; }
.resource-title { color:#1e3357; font-size:2.5rem; line-height:1.15; font-weight:800; margin:12px 0 16px; }
.resource-preview { color:#536176; font-size:1.08rem; line-height:1.75; max-width:850px; }
.resource-band { padding:48px 0; background:#fff; }
.resource-grid { display:grid; grid-template-columns:1.4fr .9fr; gap:32px; align-items:start; }
.resource-summary { border:1px solid #dfe8f7; background:#f8fbff; padding:24px; color:#42506a; line-height:1.85; }
.resource-summary h2, .lead-card h3 { color:#1e3357; font-weight:800; }
.lead-card { border:1px solid #e3ebfa; box-shadow:0 14px 34px rgba(47,85,151,.12); padding:24px; background:#fff; }
.field-label { display:block; font-size:.9rem; font-weight:700; color:#294a84; margin-bottom:8px; }
.lead-field { width:100%; border:1px solid #c9d8f3; background:#f7faff; height:50px; padding:0 14px; color:#1e3357; }
.lead-form .form-group { margin-bottom:16px; }
.lead-form .btn { padding:12px 18px; font-weight:800; }
.btn-content { display:inline-flex; align-items:center; justify-content:center; gap:10px; }
.btn-loader {
	display:none;
	width:14px;
	height:14px;
	border:2px solid rgba(255,255,255,.45);
	border-top-color:#fff;
	border-radius:50%;
	animation: caseSpin .75s linear infinite;
}
.lead-form .btn.is-loading .btn-loader { display:inline-block; }
.case-form-status {
	display:none;
	margin-top:12px;
	padding:10px 12px;
	border:1px solid transparent;
	border-radius:10px;
	font-size:.9rem;
	font-weight:600;
}
.case-form-status.is-success {
	display:block;
	color:#0f5132;
	background:#e8f9ef;
	border-color:#b7e7cc;
}
.case-form-status.is-error {
	display:block;
	color:#7a1f2a;
	background:#fdecef;
	border-color:#f2bec6;
}
.case-direct-download {
	display:none;
	margin-top:12px;
	padding:10px 12px;
	border-radius:10px;
	border:1px dashed #9eb5db;
	background:#f7fbff;
	color:#274879;
	font-size:.9rem;
}
.case-direct-download.is-visible { display:block; }
.case-direct-download a { font-weight:700; color:#1f4d99; text-decoration:underline; }
@keyframes caseSpin { to { transform: rotate(360deg); } }
@media (max-width: 991px) { .resource-grid { grid-template-columns:1fr; } .resource-title { font-size:2rem; } }
</style>
@endpush

@section('content')
<section class="resource-hero">
	<div class="container">
		<div class="resource-eyebrow">White Paper and Resource</div>
		<h1 class="resource-title">{{ $paper->title ?? 'White Paper' }}</h1>
		<p class="resource-preview">{{ $paper->preview }}</p>
	</div>
</section>

<section class="resource-band">
	<div class="container">
		<div class="resource-grid">
			<article class="resource-summary">
				<h2>Preview Summary</h2>
				<p>{{ $paper->preview }}</p>
				<p>This summary is available without a gate so leaders can evaluate relevance before requesting the full document. The complete download includes the detailed planning guidance, implementation considerations, and governance checkpoints.</p>
				@if(!empty($paper->body))
					<div>{!! $paper->body !!}</div>
				@endif
			</article>

			<aside class="lead-card" id="download">
				<h3>Download Full Resource</h3>
				<p class="text-muted">Get the full document by email. No phone number required.</p>
				<form class="form lead-form" method="post" action="{{ route('case-studies.lead.submit') }}">
					@csrf
					<input type="hidden" name="interest" value="white-papers">
					@if(!empty($paper->id))
						<input type="hidden" name="white_paper_id" value="{{ $paper->id }}">
					@endif
					<input type="hidden" name="requested_resource" value="{{ $paper->title ?? 'White Paper' }}">
					<input style="display:none;" type="text" name="website" class="honeypot">
					<label class="field-label">First name *</label>
					<div class="form-group"><input class="lead-field" type="text" name="name" required></div>
					<label class="field-label">Work email *</label>
					<div class="form-group"><input class="lead-field" type="email" name="email" required></div>
					<label class="field-label">Company</label>
					<div class="form-group"><input class="lead-field" type="text" name="organization"></div>
					<label class="field-label">Job title</label>
					<div class="form-group">
						<select class="lead-field" name="job_title">
							<option value="">Select job title</option>
							<option>Executive leader</option>
							<option>Technology leader</option>
							<option>Data leader</option>
							<option>Operations leader</option>
							<option>Practitioner or analyst</option>
						</select>
					</div>
					@if(!empty($recaptchaSiteKey))
						<div class="form-group"><div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div></div>
					@endif
					<button class="btn default-background text-light" id="whitePaperLeadSubmitBtn" type="submit">
						<span class="btn-content">
							<span class="btn-loader" aria-hidden="true"></span>
							<span id="whitePaperLeadSubmitText">Email Download Link</span>
						</span>
					</button>
					<div class="case-form-status" id="whitePaperFormStatus" aria-live="polite"></div>
					<div class="case-direct-download" id="whitePaperDirectDownload" aria-live="polite"></div>
					<p class="text-muted mt-2 mb-0" style="font-size:.9rem;">We will send a secure download link to your work email. The link expires in 1 hour.</p>
				</form>
			</aside>
		</div>
	</div>
</section>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var form = document.querySelector('.lead-card form.lead-form');
	if (!form) {
		return;
	}

	var submitBtn = document.getElementById('whitePaperLeadSubmitBtn');
	var submitText = document.getElementById('whitePaperLeadSubmitText');
	var formStatus = document.getElementById('whitePaperFormStatus');
	var directDownload = document.getElementById('whitePaperDirectDownload');

	var setSubmitting = function (isSubmitting) {
		if (!submitBtn) {
			return;
		}

		submitBtn.disabled = isSubmitting;
		submitBtn.classList.toggle('is-loading', isSubmitting);
		if (submitText) {
			submitText.textContent = isSubmitting ? 'Sending secure link...' : 'Email Download Link';
		}
	};

	var setFormStatus = function (message, type) {
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
	};

	var setDirectDownload = function (url, expiresAt) {
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
	};

	form.addEventListener('submit', function (event) {
		event.preventDefault();
		setFormStatus('', '');
		setDirectDownload('', '');
		setSubmitting(true);

		var formData = new FormData(form);

		fetch(form.action, {
			method: 'POST',
			headers: {
				'Accept': 'application/json',
				'X-Requested-With': 'XMLHttpRequest'
			},
			body: formData,
			credentials: 'same-origin'
		}).then(function (response) {
			return response.json().catch(function () {
				return {};
			}).then(function (payload) {
				return { ok: response.ok, status: response.status, payload: payload };
			});
		}).then(function (result) {
			if (result.ok) {
				var emailSent = !(result.payload && result.payload.email_sent === false);
				var statusType = emailSent ? 'success' : 'error';
				setFormStatus(result.payload.message || 'Thanks! Your secure download link has been sent.', statusType);

				if (emailSent) {
					setDirectDownload('', '');
				} else {
					setDirectDownload(result.payload.download_url || '', result.payload.expires_at || '');
				}

				form.reset();
				if (typeof grecaptcha !== 'undefined' && grecaptcha.reset) {
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
});
</script>
@endsection

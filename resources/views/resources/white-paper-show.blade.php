@extends('layouts.public')

@section('title', ($resource->title ?? 'White Paper') . ' | White Paper | Armely')
@section('meta_description', $resource->description ?: 'Armely white papers and leadership guidance for Microsoft platform planning.')
@section('robots', $resource->is_noindex ? 'noindex,nofollow' : 'index,follow')

@push('styles')
<style>
.white-paper-page {
	--case-ink: #172033;
	--case-muted: #46586f;
	--case-line: #d9e4f2;
	--case-panel: #ffffff;
	--case-blue: #2f5597;
	--case-navy: #18345f;
	background:
		radial-gradient(1200px 560px at 12% 0%, rgba(47, 85, 151, .08), rgba(47, 85, 151, 0) 60%),
		linear-gradient(180deg, #f7faff 0%, #ffffff 100%);
	color: var(--case-ink);
}
.white-paper-hero {
	background: linear-gradient(135deg, #162f5d 0%, #214a89 56%, #18345f 100%);
	padding: 36px 0 44px;
	position: relative;
	overflow: hidden;
}
.white-paper-hero::after {
	content: '';
	position: absolute;
	inset: 0;
	background:
		radial-gradient(560px 220px at 18% 18%, rgba(255, 255, 255, .12), rgba(255, 255, 255, 0) 68%),
		radial-gradient(420px 180px at 84% 8%, rgba(255, 255, 255, .08), rgba(255, 255, 255, 0) 70%);
	pointer-events: none;
}
.white-paper-back {
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
.white-paper-back:hover { color: #fff; text-decoration: none; }
.white-paper-hero-grid {
	display: grid;
	grid-template-columns: minmax(0, 1.2fr) minmax(300px, .8fr);
	gap: 34px;
	align-items: stretch;
	position: relative;
	z-index: 1;
}
.white-paper-kicker {
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
.white-paper-title {
	color: #fff;
	font-size: 2.9rem;
	line-height: 1.08;
	font-weight: 900;
	margin: 0 0 18px;
	max-width: 880px;
}
.white-paper-summary {
	color: rgba(255, 255, 255, .92);
	font-size: 1.1rem;
	line-height: 1.8;
	max-width: 850px;
	margin-bottom: 24px;
}
.white-paper-actions { display: flex; flex-wrap: wrap; gap: 12px; }
.white-paper-btn,
.white-paper-btn-secondary {
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
.white-paper-btn { background: var(--case-blue); color: #fff; box-shadow: 0 10px 24px rgba(7, 18, 39, .16); }
.white-paper-btn:hover { color: #fff; background: var(--case-navy); text-decoration: none; }
.white-paper-btn-secondary { background: rgba(255,255,255,.08); color: #fff; border-color: rgba(255,255,255,.22); backdrop-filter: blur(8px); }
.white-paper-btn-secondary:hover { color: #fff; border-color: rgba(255,255,255,.45); text-decoration: none; }
.white-paper-meta {
	display: flex;
	flex-wrap: wrap;
	gap: 8px;
	margin-top: 14px;
}
.white-paper-pill {
	display: inline-flex;
	align-items: center;
	min-height: 28px;
	padding: 4px 10px;
	border-radius: 999px;
	background: rgba(255,255,255,.11);
	color: #fff;
	font-size: .72rem;
	font-weight: 900;
	letter-spacing: .08em;
	text-transform: uppercase;
}
.white-paper-band {
	padding: 0 0 72px;
	background: #fff;
}
.white-paper-shell {
	width: 100%;
	margin: 0 auto;
	padding: 0;
}
.white-paper-layout {
	display: grid;
	grid-template-columns: minmax(0, 1.48fr) minmax(310px, .82fr);
	gap: 32px;
	align-items: start;
}
.white-paper-preview-card,
.white-paper-request-card,
.white-paper-related-card {
	background: #fff;
}
.white-paper-preview-card { padding: 0; }
.white-paper-preview-kicker {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	text-transform: uppercase;
	letter-spacing: .1em;
	font-size: .75rem;
	font-weight: 900;
	color: var(--case-blue);
	margin-bottom: 10px;
}
.white-paper-preview-head {
	display: flex;
	align-items: flex-start;
	justify-content: space-between;
	gap: 18px;
	margin-bottom: 20px;
}
.white-paper-preview-head h2 {
	color: var(--case-ink);
	font-size: 1.55rem;
	line-height: 1.2;
	font-weight: 900;
	margin: 0 0 8px;
}
.white-paper-preview-head p {
	color: var(--case-muted);
	line-height: 1.7;
	margin: 0;
	max-width: 62ch;
}
.white-paper-preview-pills { display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end; }
.white-paper-preview-pill {
	display: inline-flex;
	align-items: center;
	min-height: 28px;
	padding: 4px 10px;
	border-radius: 999px;
	background: #f7faff;
	color: var(--case-navy);
	font-size: .7rem;
	font-weight: 900;
	letter-spacing: .08em;
	text-transform: uppercase;
}
.white-paper-preview-pill.is-muted { background: #fff; color: #5d6f86; }
.white-paper-pdf-stage {
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
.white-paper-pdf-page {
	width: 100%;
	border: 1px solid #dbe6f3;
	border-radius: 18px;
	background: linear-gradient(180deg, #ffffff 0%, #f7faff 100%);
	box-shadow: none;
	padding: 28px;
}
.white-paper-pdf-top {
	display: flex;
	align-items: flex-start;
	justify-content: space-between;
	gap: 18px;
	flex-wrap: wrap;
	margin-bottom: 22px;
	padding-bottom: 18px;
}
.white-paper-pdf-title {
	color: var(--case-ink);
	font-size: 1.55rem;
	line-height: 1.18;
	font-weight: 900;
	margin: 0;
	max-width: 22ch;
}
.white-paper-pdf-body {
	position: relative;
	color: #32465f;
	font-size: 1rem;
	line-height: 1.85;
	max-height: 520px;
	overflow: hidden;
	padding-right: 6px;
}
.white-paper-text-preview {
	display: grid;
	gap: 14px;
	padding: 10px 2px 4px;
}
.white-paper-text-preview-title {
	color: var(--case-ink);
	font-size: 1.42rem;
	line-height: 1.22;
	font-weight: 900;
	margin-bottom: 2px;
	max-width: 24ch;
}
.white-paper-text-preview-body {
	color: #32465f;
	font-size: 1rem;
	line-height: 1.85;
	max-height: 420px;
	overflow: hidden;
	padding: 18px 18px 20px;
	border: 1px solid #dce7f2;
	border-radius: 16px;
	background:
		linear-gradient(180deg, rgba(247, 250, 255, .96), rgba(255,255,255,.98)),
		#fff;
	box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
}
.white-paper-pdf-preview-note {
	margin-top: 16px;
	padding: 14px 16px;
	border-radius: 12px;
	background: linear-gradient(135deg, rgba(47, 85, 151, .08), rgba(47, 85, 151, .03));
	color: #46586f;
	font-size: .92rem;
	line-height: 1.6;
}
.white-paper-pdf-empty {
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
.white-paper-request-card {
	border: 1px solid #dce7fb;
	border-radius: 14px;
	background: linear-gradient(180deg, #ffffff 0%, #f7faff 100%);
	box-shadow: 0 12px 26px rgba(17, 44, 86, 0.08);
	padding: 22px;
	position: sticky;
	top: 92px;
}
.white-paper-request-kicker {
	color: #2f5597;
	font-size: 0.78rem;
	font-weight: 800;
	text-transform: uppercase;
	letter-spacing: 0.08em;
	margin-bottom: 8px;
}
.white-paper-request-title {
	color: #16386b;
	font-size: 1.22rem;
	font-weight: 900;
	margin: 0 0 8px;
}
.white-paper-request-copy {
	color: #4b6187;
	line-height: 1.65;
	font-size: 0.94rem;
	margin-bottom: 16px;
}
.white-paper-request-form .form-group { margin-bottom: 14px; }
.white-paper-request-form label {
	display: block;
	color: #27497f;
	font-size: 0.86rem;
	font-weight: 700;
	margin-bottom: 7px;
}
.white-paper-request-form .form-control,
.white-paper-request-form textarea {
	border: 1px solid #cbd9f4;
	border-radius: 12px;
	background: #f7faff;
	color: #193763;
	padding: 12px 14px;
	min-height: 48px;
	box-shadow: none;
	width: 100%;
}
.white-paper-request-form textarea {
	min-height: 112px;
	resize: vertical;
}
.white-paper-request-form .form-control:focus,
.white-paper-request-form textarea:focus {
	border-color: #2f5597;
	background: #fff;
	box-shadow: 0 0 0 4px rgba(47, 85, 151, 0.12);
}
.white-paper-request-note {
	margin: 10px 0 0;
	color: #5b6f91;
	font-size: 0.86rem;
	line-height: 1.55;
}
.white-paper-related-wrap { margin-top: 28px; }
.white-paper-related-title {
	color: #16386b;
	font-size: 1.1rem;
	font-weight: 900;
	margin: 0 0 12px;
}
.white-paper-related-grid {
	display: grid;
	grid-template-columns: repeat(3, minmax(0, 1fr));
	gap: 12px;
}
.white-paper-related-card {
	border: 1px solid #d7e4fb;
	border-radius: 12px;
	text-decoration: none;
	color: #173868;
	padding: 12px;
	transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}
.white-paper-related-card:hover {
	text-decoration: none;
	color: #12345f;
	transform: translateY(-1px);
	border-color: #bdd3f8;
	box-shadow: 0 10px 20px rgba(18, 46, 87, 0.1);
}
.white-paper-related-card .meta {
	font-size: 0.74rem;
	font-weight: 700;
	color: #4b6288;
	margin-bottom: 4px;
}
.white-paper-related-card .name {
	margin: 0;
	font-size: 0.92rem;
	font-weight: 800;
	line-height: 1.35;
}
@media (max-width: 992px) {
	.white-paper-hero-grid,
	.white-paper-layout {
		grid-template-columns: 1fr;
	}
	.white-paper-request-card {
		position: static;
	}
	.white-paper-related-grid { grid-template-columns: 1fr; }
}
@media (max-width: 767px) {
	.white-paper-hero { padding: 88px 0 56px; }
	.white-paper-title { font-size: 2rem; }
	.white-paper-pdf-stage { min-height: 420px; padding: 12px; }
	.white-paper-pdf-page { padding: 18px; }
	.white-paper-text-preview-body { padding: 14px 14px 16px; }
}

/* One-pager document frame — mirrors the case-study preview format. */
.white-paper-doc-stage {
	background: #edf1f5;
	padding: clamp(16px, 2.4vw, 28px);
	border-radius: 18px;
}
.white-paper-onepager {
	position: relative;
	width: 100%;
	margin: 0 auto;
	padding: clamp(30px, 4.2vw, 52px);
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
.white-paper-doc-brandbar {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 18px;
	margin: 0 0 25px;
	padding: 0 0 11px;
	border-bottom: 2px solid var(--case-blue);
	color: var(--case-blue);
}
.white-paper-doc-brand {
	font-size: 1.45rem;
	font-weight: 950;
	letter-spacing: .12em;
}
.white-paper-doc-brandbar span:last-child {
	font-size: .76rem;
	font-weight: 900;
	letter-spacing: .12em;
	text-transform: uppercase;
	color: #59687a;
}
.white-paper-doc-headline {
	max-width: 730px;
	margin: 0;
	color: var(--case-ink);
	font-family: Georgia, 'Times New Roman', serif;
	font-size: clamp(1.7rem, 3vw, 2.4rem);
	line-height: 1.16;
	font-weight: 950;
}
.white-paper-doc-intro {
	max-width: 760px;
	margin: 13px 0 0;
	color: var(--case-muted);
	font-family: Georgia, 'Times New Roman', serif;
	font-size: 1.03rem;
	line-height: 1.58;
}
.white-paper-doc-embed {
	margin-top: 25px;
	padding-top: 20px;
	border-top: 1px solid #b9c2ce;
}
.white-paper-doc-kicker {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	color: var(--case-blue);
	font-size: .82rem;
	font-weight: 900;
	letter-spacing: .08em;
	text-transform: uppercase;
	margin-bottom: 12px;
}
.white-paper-doc-embed iframe {
	width: 100%;
	min-height: 560px;
	border: 0;
	display: block;
	background: #fff;
}
@media (max-width: 767px) {
	.white-paper-doc-stage { padding: 12px; }
	.white-paper-onepager { padding: 26px 20px 34px; }
	.white-paper-doc-embed iframe { min-height: 420px; }
}
</style>
@endpush

@section('content')
@php
	$resourceTitle = trim((string) ($resource->title ?? 'White Paper'));
	$resourceDescription = trim((string) ($resource->description ?? ''));
	$resourceCategory = trim((string) ($resource->category ?? 'White Papers'));
	$resourceAssetUrl = trim((string) ($resourceAssetUrl ?? ''));
	$resourceInlineUrl = trim((string) ($resourceInlineUrl ?? ''));
	$resourcePreviewSource = trim((string) ($resource->preview ?? ''));
	if ($resourcePreviewSource === '') {
		$resourcePreviewSource = $resourceDescription;
	}
	if ($resourcePreviewSource === '') {
		$resourcePreviewSource = trim((string) ($resource->body ?? ''));
	}
	$resourcePreviewText = $resourcePreviewSource !== ''
		? \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', strip_tags($resourcePreviewSource)), 520)
		: '';
	if ($resourceInlineUrl === '' && $resourceAssetUrl !== '') {
		$resourceInlineUrl = $resourceAssetUrl;
	}
	$isPdfPreview = str_contains(strtolower((string) ($resourceAssetUrl ?: $resource->file_url ?: '')), '.pdf') || strtolower((string) ($resource->resource_type ?? '')) === 'pdf';
@endphp

<main class="white-paper-page">
	<section class="white-paper-hero">
		<div class="container">
			<a class="white-paper-back" href="{{ route('case-studies.index') }}#white-papers"><i class="fa fa-arrow-left"></i> Back to White Papers</a>
			<div class="white-paper-hero-grid">
				<div>
					<div class="white-paper-kicker"><i class="fa fa-file-pdf-o"></i> White Paper</div>
					<h1 class="white-paper-title">{{ $resourceTitle }}</h1>
					<p class="white-paper-summary">{{ $resourceDescription ?: 'A practical white paper for Microsoft platform planning, governance, and delivery.' }}</p>
					<div class="white-paper-actions">
						<a class="white-paper-btn" href="#resource-request-form"><i class="fa fa-lock"></i> Request Full White Paper</a>
						<a class="white-paper-btn-secondary" href="{{ route('resources.index') }}"><i class="fa fa-book"></i> Browse Resources</a>
					</div>
					<div class="white-paper-meta">
						<span class="white-paper-pill">{{ $resourceCategory }}</span>
						<span class="white-paper-pill">PDF Resource</span>
					</div>
				</div>
				<div></div>
			</div>
		</div>
	</section>

	<section class="white-paper-band">
		<div class="container">
			<div class="white-paper-shell">
				<div class="white-paper-layout">
					<div class="white-paper-preview-card">
						<div class="white-paper-doc-stage">
							<article class="white-paper-onepager" aria-label="White paper preview">
								<div class="white-paper-doc-brandbar">
									<strong class="white-paper-doc-brand">ARMELY</strong>
									<span>White Paper</span>
								</div>
								<header>
									<h1 class="white-paper-doc-headline">{{ $resourceTitle }}</h1>
									@if($resourceDescription !== '')
										<p class="white-paper-doc-intro">{{ $resourceDescription }}</p>
									@endif
								</header>
								<div class="white-paper-doc-embed">
									<div class="white-paper-doc-kicker"><i class="fa fa-file-pdf-o"></i> First page preview</div>
									@if($resourceInlineUrl !== '' && $isPdfPreview)
										<iframe src="{{ $resourceInlineUrl }}#view=FitH" title="{{ $resourceTitle }}" loading="lazy"></iframe>
									@else
										<div class="white-paper-text-preview-body">
											{!! nl2br(e($resourcePreviewText !== '' ? $resourcePreviewText : ($resourceDescription ?: 'This white paper is available by request.'))) !!}
										</div>
									@endif
									<div class="white-paper-pdf-preview-note">
										@if($resourceInlineUrl !== '' && $isPdfPreview)
											This preview comes from the file itself. Request the full white paper for secure access to the complete download.
										@else
											This preview is generated from the white paper content. Request the full white paper for secure access to the complete download.
										@endif
									</div>
								</div>
							</article>
						</div>
					</div>

					<aside class="white-paper-request-card" id="resource-request-form">
						<div class="white-paper-request-kicker">Request by Email</div>
						<h2 class="white-paper-request-title">Get this white paper in your inbox</h2>
						<p class="white-paper-request-copy">Fill in your details and Armely will send the secure download link to your email.</p>
						<div id="resourceRequestAjaxAlert" class="alert d-none" role="alert"></div>

						<form id="resourceRequestForm" method="POST" action="{{ route('whitepapers.request', $resource->slug) }}" class="white-paper-request-form">
							@csrf
							<div class="form-group">
								<label for="resourceRequestName">Name</label>
								<input id="resourceRequestName" type="text" name="name" class="form-control" value="{{ old('name') }}" required>
							</div>
							<div class="form-group">
								<label for="resourceRequestEmail">Work Email</label>
								<input id="resourceRequestEmail" type="email" name="email" class="form-control" value="{{ old('email') }}" required>
							</div>
							<div class="form-group">
								<label for="resourceRequestOrganization">Company</label>
								<input id="resourceRequestOrganization" type="text" name="organization" class="form-control" value="{{ old('organization') }}">
							</div>
							<div class="form-group">
								<label for="resourceRequestRole">Job Title</label>
								<input id="resourceRequestRole" type="text" name="job_title" class="form-control" value="{{ old('job_title') }}">
							</div>
							<div class="form-group">
								<label for="resourceRequestMessage">What are you interested in?</label>
								<textarea id="resourceRequestMessage" name="message" class="form-control">{{ old('message') }}</textarea>
							</div>

							<button type="submit" class="white-paper-btn w-100" id="resourceSubmitBtn">Email Me This White Paper</button>
							<p class="white-paper-request-note">The submitted details are recorded so the team can follow up with related content if needed.</p>
						</form>
					</aside>
				</div>

				@if(($relatedResources ?? collect())->isNotEmpty())
					<div class="white-paper-related-wrap">
						<h3 class="white-paper-related-title">Recommended Next Resources</h3>
						<div class="white-paper-related-grid">
							@foreach($relatedResources as $related)
								@php($relatedType = strtolower((string) $related->resource_type))
								@php($relatedRouteName = $relatedType === 'pdf' || str_contains($relatedType, 'white') ? 'whitepapers.show' : 'resources.show')
								<a href="{{ route($relatedRouteName, $related->slug) }}" class="white-paper-related-card">
									<div class="meta">{{ ucfirst($related->resource_type) }} @if($related->category) · {{ $related->category }} @endif</div>
									<p class="name">{{ $related->title }}</p>
								</a>
							@endforeach
						</div>
					</div>
				@endif
			</div>
		</div>
	</section>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
	var form = document.getElementById('resourceRequestForm');
	if (!form) {
		return;
	}

	var submitBtn = document.getElementById('resourceSubmitBtn');
	var alertBox = document.getElementById('resourceRequestAjaxAlert');
	var originalBtnText = submitBtn ? submitBtn.textContent : 'Email Me This White Paper';

	var showAlert = function (message, type) {
		if (!alertBox) {
			return;
		}

		alertBox.textContent = message;
		alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
		alertBox.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');
	};

	var setSubmitting = function (isSubmitting) {
		if (!submitBtn) {
			return;
		}

		submitBtn.disabled = isSubmitting;
		submitBtn.textContent = isSubmitting ? 'Sending...' : originalBtnText;
	};

	form.addEventListener('submit', function (event) {
		event.preventDefault();
		setSubmitting(true);

		var formData = new FormData(form);

		fetch(form.action, {
			method: 'POST',
			headers: {
				'Accept': 'application/json',
				'X-Requested-With': 'XMLHttpRequest'
			},
			credentials: 'same-origin',
			body: formData
		})
			.then(function (response) {
				return response.json().catch(function () {
					return {};
				}).then(function (json) {
					return { ok: response.ok, status: response.status, json: json };
				});
			})
			.then(function (result) {
				if (!result.ok || !(result.json && result.json.success)) {
					if (result.status === 422 && result.json && result.json.errors) {
						var firstField = Object.keys(result.json.errors)[0];
						var firstError = firstField && result.json.errors[firstField] && result.json.errors[firstField][0]
							? result.json.errors[firstField][0]
							: 'Please check the form and try again.';
						showAlert(firstError, 'error');
						return;
					}

					showAlert((result.json && result.json.message) || 'We could not send your request right now. Please try again.', 'error');
					return;
				}

				showAlert(result.json.message || 'White paper link sent successfully.', 'success');
				form.reset();
			})
			.catch(function () {
				showAlert('Network issue. Please try again.', 'error');
			})
			.finally(function () {
				setSubmitting(false);
			});
	});
});
</script>
@endpush

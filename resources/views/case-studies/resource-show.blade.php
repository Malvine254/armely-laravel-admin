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
					<button class="btn default-background text-light" type="submit">Email Download Link</button>
				</form>
			</aside>
		</div>
	</div>
</section>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection

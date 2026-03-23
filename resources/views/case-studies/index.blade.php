@extends('layouts.public')

@section('title', 'Case Studies & White Papers - Armely')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/case-studies-modern.css') }}">
<style>
.case-lead-section {
	background: #f8f9fa;
	padding: 70px 0;
}
.case-lead-card {
	background: #fff;
	border: 1px solid #e3ebfa;
	border-radius: 16px;
	box-shadow: 0 14px 34px rgba(47, 85, 151, 0.12);
	padding: 28px 24px;
}
.case-lead-title {
	font-size: 1.85rem;
	color: #1e3a6d;
	font-weight: 800;
	margin-bottom: 10px;
}
.case-lead-subtitle {
	font-size: 1.02rem;
	color: #55657d;
	margin-bottom: 24px;
}
.case-lead-note {
	font-size: 0.9rem;
	color: #6a768a;
	margin-top: 12px;
}
.lead-form .field-label {
	display: block;
	font-size: 0.9rem;
	font-weight: 700;
	color: #294a84;
	margin-bottom: 8px;
}
.lead-form .form-group {
	margin-bottom: 16px;
}
.lead-form .lead-field,
.lead-form textarea.lead-field,
.lead-form select.lead-field {
	width: 100%;
	border: 1px solid #c9d8f3;
	background: #f7faff;
	border-radius: 12px;
	height: 50px;
	padding: 0 14px;
	color: #1e3357;
	font-size: 0.95rem;
	transition: all .2s ease;
}
.lead-form textarea.lead-field {
	min-height: 130px;
	height: auto;
	padding: 12px 14px;
	resize: vertical;
}
.lead-form .lead-field:focus {
	outline: none;
	border-color: #2f5597;
	box-shadow: 0 0 0 4px rgba(47, 85, 151, 0.13);
	background: #fff;
}
.lead-form .submit-btn-wrap .btn {
	border-radius: 12px;
	padding: 12px 22px;
	font-weight: 700;
	min-width: 180px;
}
.case-download-modal {
	display: none;
	position: fixed;
	inset: 0;
	z-index: 9999;
	background: rgba(10, 21, 45, 0.62);
	padding: 24px 12px;
	overflow-y: auto;
}
.case-download-modal.is-open {
	display: block;
}
.case-download-modal-dialog {
	max-width: 880px;
	margin: 24px auto;
}
.case-download-modal .case-lead-card {
	margin: 0;
	position: relative;
}
.case-modal-close {
	position: absolute;
	top: 14px;
	right: 14px;
	width: 34px;
	height: 34px;
	border: 0;
	border-radius: 50%;
	background: #eef3ff;
	color: #1e3a6d;
	font-size: 1.1rem;
	font-weight: 700;
	line-height: 1;
	cursor: pointer;
}
.case-modal-selected {
	display: inline-block;
	margin: 8px 0 20px;
	padding: 8px 12px;
	background: #edf3ff;
	border: 1px solid #d8e5ff;
	border-radius: 999px;
	color: #25447a;
	font-size: 0.85rem;
	font-weight: 700;
}
@media (max-width: 767px) {
	.case-lead-card {
		padding: 22px 16px;
	}
	.case-lead-title {
		font-size: 1.45rem;
	}
}
</style>
@endpush

@section('content')

<!-- Breadcrumbs -->
<div class="breadcrumbs overlay">
	<div class="container">
		<div class="bread-inner">
			<div class="row">
				<div class="col-12">
					<h2>Case Studies & Resources</h2>
					<ul class="bread-list">
						<li><a href="{{ route('home') }}">Home</a></li>
						<li><i class="icofont-simple-right"></i></li>
						<li class="active">Case Studies</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Breadcrumbs -->

<!-- Case Studies Section -->
<section class="case-studies-section">
<div class="container">
	<!-- <div class="row">
		<div class="col-lg-12">
			<div class="section-header">
				<div class="section-badge">
					<i class="icofont-briefcase"></i> Case Studies
				</div>
				<h2 class="section-title">Success Stories</h2>
				<p class="section-subtitle">See how our solutions have delivered measurable impact</p>
			</div>
		</div>
	</div> -->
</div>
<div class="container">
	<div class="row">
		@forelse($caseStudies as $caseStudy)
			@php
				$caseStudyAccessUrl = route('case-studies.access', ['caseStudy' => $caseStudy->id]);
				$caseStudyUnlocked = in_array((int) $caseStudy->id, $grantedCaseStudyIds ?? [], true);
			@endphp
			<div class="col-md-4 mb-4">
				<div class="case-study-card">
					<div class="card-image-wrapper">
						@if($caseStudy->listing_image && file_exists(public_path('images/case-study/' . $caseStudy->listing_image)))
							<img src="{{ asset('images/case-study/' . $caseStudy->listing_image) }}" class="card-image lazy-img" alt="{{ $caseStudy->category }}">
						@else
							<div style="width: 100%; height: 100%; background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%); display: flex; align-items: center; justify-content: center;">
								<i class="icofont-briefcase" style="font-size: 4rem; color: rgba(255,255,255,0.3);"></i>
							</div>
						@endif
						<div class="card-overlay"></div>
						<div class="card-badge">{{ $caseStudy->category }}</div>
					</div>
					<div class="card-content">
						<h5 class="card-title">{{ $caseStudy->category }} Solution</h5>
						<p class="card-description">{{ $caseStudy->preview ?? '' }}</p>
						<div class="card-footer">
							<a class="read-more-btn text-light case-study-gated-link"
							   href="{{ $caseStudyAccessUrl }}"
							   data-access-url="{{ $caseStudyAccessUrl }}"
							   data-case-study-id="{{ $caseStudy->id }}"
							   data-is-unlocked="{{ $caseStudyUnlocked ? '1' : '0' }}"
							   data-resource-title="{{ $caseStudy->category }} Solution">
								Read Case Study <i class="fa fa-arrow-right"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
		@empty
			<div class="col-12 text-center text-muted py-5">
				<p>No case studies available at this time.</p>
			</div>
		@endforelse
	</div>

	<!-- Pagination for Case Studies -->
	<div class="row mt-5">
		<div class="col-12">
			<nav class="pagination-nav" role="navigation" aria-label="Pagination Navigation">
				<div class="pagination-container">
					<!-- Previous Button -->
					@if ($caseStudies->onFirstPage())
						<span class="pagination-btn pagination-btn-disabled" disabled>
							<i class="fa fa-chevron-left"></i> Previous
						</span>
					@else
						<a href="{{ $caseStudies->previousPageUrl() }}" class="pagination-btn pagination-btn-prev">
							<i class="fa fa-chevron-left"></i> Previous
						</a>
					@endif

					<!-- Page Numbers -->
					<div class="pagination-numbers">
						@foreach ($caseStudies->getUrlRange(1, $caseStudies->lastPage()) as $page => $url)
							@if ($page == $caseStudies->currentPage())
								<span class="pagination-number pagination-number-active">{{ $page }}</span>
							@else
								<a href="{{ $url }}" class="pagination-number">{{ $page }}</a>
							@endif
						@endforeach
					</div>

					<!-- Next Button -->
					@if ($caseStudies->hasMorePages())
						<a href="{{ $caseStudies->nextPageUrl() }}" class="pagination-btn pagination-btn-next">
							Next <i class="fa fa-chevron-right"></i>
						</a>
					@else
						<span class="pagination-btn pagination-btn-disabled" disabled>
							Next <i class="fa fa-chevron-right"></i>
						</span>
					@endif
				</div>
			</nav>
		</div>
	</div>
</div>
</section>

@if(session('status'))
	<section class="case-lead-section" id="resource-request-form">
		<div class="container col-12 col-lg-9 col-md-11 col-sm-12">
			<div class="alert alert-success">{{ session('status') }}</div>
		</div>
	</section>
@endif

<!-- Case Study Lead Modal -->
<div id="caseStudyLeadModal" class="case-download-modal" aria-hidden="true" role="dialog" aria-labelledby="caseStudyModalTitle">
	<div class="case-download-modal-dialog">
		<div class="case-lead-card">
			<button type="button" class="case-modal-close" id="caseModalCloseBtn" aria-label="Close">&times;</button>
			<h2 class="case-lead-title" id="caseStudyModalTitle">Access This Case Study</h2>
			<p class="case-lead-subtitle">Please complete this short form to unlock the selected case study.</p>
			<div class="case-modal-selected" id="selectedCaseStudyLabel">Selected: Case Study</div>

			<form class="form lead-form" method="post" action="{{ route('case-studies.lead.submit') }}">
				@csrf
				<input type="hidden" name="interest" value="case-studies">
				<input type="hidden" name="case_study_id" id="caseStudyId" value="">
				<input type="hidden" name="requested_resource" id="caseStudyRequestedResource" value="">
				<input style="display:none;" type="text" name="website" class="honeypot">

				<div class="row">
					<div class="col-lg-6">
						<label class="field-label text-start">Name *</label>
						<div class="form-group">
							<input class="lead-field" type="text" name="name" placeholder="Name" required>
						</div>
					</div>
					<div class="col-lg-6">
						<label class="field-label text-start">Work email *</label>
						<div class="form-group">
							<input class="lead-field" type="email" name="email" placeholder="Work email" required>
						</div>
					</div>
					<div class="col-lg-6">
						<label class="field-label text-start">Work phone number *</label>
						<div class="form-group">
							<input class="lead-field" type="text" name="phone" placeholder="Phone number" required>
						</div>
					</div>
					<div class="col-lg-6">
						<label class="field-label text-start">Company name *</label>
						<div class="form-group">
							<input class="lead-field" type="text" name="organization" placeholder="Company name" required>
						</div>
					</div>
					<div class="col-lg-6">
						<label class="field-label text-start">Job title</label>
						<div class="form-group">
							<input class="lead-field" type="text" name="job_title" placeholder="Job title">
						</div>
					</div>
					<div class="col-lg-6">
						<label class="field-label text-start">Country/Region</label>
						<div class="form-group">
							<input class="lead-field" type="text" name="country" placeholder="Country/Region">
						</div>
					</div>
					<div class="col-lg-12">
						<label class="field-label text-start">Additional notes</label>
						<div class="form-group">
							<textarea class="lead-field" name="message" placeholder="Tell us what topics or industries you want to explore"></textarea>
						</div>
					</div>
					<div class="col-lg-12">
						<div class="form-group">
							@if(!empty($recaptchaSiteKey ?? config('services.recaptcha.site_key')))
								<div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey ?? config('services.recaptcha.site_key') }}"></div>
							@else
								<div class="alert alert-warning">reCAPTCHA is not configured. Please set <strong>CAPTURE_SITE_KEY</strong>.</div>
							@endif
						</div>
					</div>
					<div class="col-md-5 col-sm-8">
						<div class="form-group login-btn submit-btn-wrap">
							<button class="btn default-background" type="submit">Submit & Open Case Study</button>
						</div>
					</div>
				</div>
				<p class="case-lead-note">By submitting this form, you agree to be contacted by Armely about relevant resources.</p>
			</form>
		</div>
	</div>
</div>

<!-- White Papers Section -->
<section class="white-papers-section">
<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<div class="section-header">
				<div class="section-badge">
					<i class="icofont-document-multiple"></i> Resources
				</div>
				<h2 class="section-title">White Papers</h2>
				<p class="section-subtitle">In-depth insights and strategic guidance for digital transformation</p>
				<div class="section-divider"></div>
			</div>
		</div>
	</div>
</div>
<div class="container">
	<div class="row">
		@forelse($whitePapers as $paper)
			<div class="col-md-4 mb-4">
				<div class="white-paper-card">
					<div class="card-image-wrapper">
						@if($paper->images && file_exists(public_path('images/white-papers/' . $paper->images)))
							<img src="{{ asset('images/white-papers/' . $paper->images) }}" class="card-image lazy-img" alt="{{ $paper->title }}">
						@else
							<div style="width: 100%; height: 100%; background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%); display: flex; align-items: center; justify-content: center;">
								<i class="icofont-document" style="font-size: 4rem; color: rgba(255,255,255,0.3);"></i>
							</div>
						@endif
						<div class="card-overlay"></div>
						<div class="card-badge white-paper-badge">
							<i class="icofont-document"></i> Resource
						</div>
					</div>
					<div class="card-content">
						<h5 class="card-title">{{ $paper->title }}</h5>
						<p class="card-description">{{ $paper->preview ?? '' }}</p>
						<div class="card-footer">
							<a class="read-more-btn" target="_blank" href="{{ $paper->pdf ? (str_starts_with($paper->pdf, 'http') ? $paper->pdf : asset('white_paper_docs/' . $paper->pdf)) : '#' }}">
								Download Paper <i class="fa fa-download"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
		@empty
			<div class="col-12 text-center text-muted py-5">
				<p>No white papers available at this time.</p>
			</div>
		@endforelse
	</div>

	<!-- Pagination for White Papers -->
	<div class="row mt-5">
		<div class="col-12">
			<nav class="pagination-nav" role="navigation" aria-label="Pagination Navigation">
				<div class="pagination-container">
					<!-- Previous Button -->
					@if ($whitePapers->onFirstPage())
						<span class="pagination-btn pagination-btn-disabled" disabled>
							<i class="fa fa-chevron-left"></i> Previous
						</span>
					@else
						<a href="{{ $whitePapers->previousPageUrl() }}" class="pagination-btn pagination-btn-prev">
							<i class="fa fa-chevron-left"></i> Previous
						</a>
					@endif

					<!-- Page Numbers -->
					<div class="pagination-numbers">
						@foreach ($whitePapers->getUrlRange(1, $whitePapers->lastPage()) as $page => $url)
							@if ($page == $whitePapers->currentPage())
								<span class="pagination-number pagination-number-active">{{ $page }}</span>
							@else
								<a href="{{ $url }}" class="pagination-number">{{ $page }}</a>
							@endif
						@endforeach
					</div>

					<!-- Next Button -->
					@if ($whitePapers->hasMorePages())
						<a href="{{ $whitePapers->nextPageUrl() }}" class="pagination-btn pagination-btn-next">
							Next <i class="fa fa-chevron-right"></i>
						</a>
					@else
						<span class="pagination-btn pagination-btn-disabled" disabled>
							Next <i class="fa fa-chevron-right"></i>
						</span>
					@endif
				</div>
			</nav>
		</div>
	</div>
</div>
</section>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
(function () {
	var modal = document.getElementById('caseStudyLeadModal');
	if (!modal) {
		return;
	}

	var label = document.getElementById('selectedCaseStudyLabel');
	var caseStudyIdInput = document.getElementById('caseStudyId');
	var hiddenResource = document.getElementById('caseStudyRequestedResource');
	var closeBtn = document.getElementById('caseModalCloseBtn');

	function openModal(accessUrl, caseStudyId, resourceTitle) {
		if (!accessUrl || !caseStudyId) {
			return;
		}
		caseStudyIdInput.value = caseStudyId;
		hiddenResource.value = resourceTitle || 'Case Study';
		label.textContent = 'Selected: ' + (resourceTitle || 'Case Study');
		modal.classList.add('is-open');
		modal.setAttribute('aria-hidden', 'false');
		document.body.style.overflow = 'hidden';
	}

	function closeModal() {
		modal.classList.remove('is-open');
		modal.setAttribute('aria-hidden', 'true');
		document.body.style.overflow = '';
	}

	document.querySelectorAll('.case-study-gated-link').forEach(function (link) {
		link.addEventListener('click', function (event) {
			var isUnlocked = link.getAttribute('data-is-unlocked') === '1';
			var accessUrl = link.getAttribute('data-access-url') || '';
			var caseStudyId = link.getAttribute('data-case-study-id') || '';

			if (isUnlocked && accessUrl) {
				event.preventDefault();
				window.open(accessUrl, '_blank', 'noopener');
				return;
			}

			event.preventDefault();
			openModal(accessUrl, caseStudyId, link.getAttribute('data-resource-title') || 'Case Study');
		});
	});

	if (closeBtn) {
		closeBtn.addEventListener('click', closeModal);
	}

	modal.addEventListener('click', function (event) {
		if (event.target === modal) {
			closeModal();
		}
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape' && modal.classList.contains('is-open')) {
			closeModal();
		}
	});
})();
</script>

@endsection

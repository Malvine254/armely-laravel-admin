@extends('layouts.public')

@section('title', 'Case Studies')

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
#white-papers {
	scroll-margin-top: 110px;
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
.case-toast-stack {
	position: fixed;
	top: 18px;
	right: 18px;
	z-index: 10020;
	display: flex;
	flex-direction: column;
	gap: 10px;
	max-width: 360px;
}
.case-toast {
	padding: 12px 14px;
	border-radius: 12px;
	font-size: 0.92rem;
	font-weight: 600;
	box-shadow: 0 12px 30px rgba(18, 40, 77, 0.22);
	animation: caseToastIn .25s ease;
}
.case-toast-success {
	background: #e9f9ef;
	border: 1px solid #98ddb0;
	color: #1f6b39;
}
.case-toast-error {
	background: #fff2f2;
	border: 1px solid #f0b2b2;
	color: #942c2c;
}
@keyframes caseToastIn {
	from {
		opacity: 0;
		transform: translateY(-6px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}
@media (max-width: 767px) {
	.case-lead-card {
		padding: 22px 16px;
	}
	.case-lead-title {
		font-size: 1.45rem;
	}
	.case-toast-stack {
		left: 12px;
		right: 12px;
		max-width: none;
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
	<div class="row">
		<div class="col-lg-12">
			<div class="section-header">
				<h2 class="section-title">Success Stories</h2>
				<p class="section-subtitle">Real transformation outcomes across industries, platforms, and teams.</p>
				<div class="section-divider"></div>
			</div>
		</div>
	</div>
</div>
<div class="container">
	<div class="row">
		@forelse($caseStudies as $caseStudy)
			@php($caseStudyTitle = trim((string) ($caseStudy->title ?? '')))
			@php($caseStudyDisplayTitle = $caseStudyTitle !== '' ? $caseStudyTitle : (string) ($caseStudy->category ?? 'Case Study'))
			@php($caseStudyFullTitle = trim($caseStudyDisplayTitle . ' Solution'))
			@php($caseStudyPlainPreview = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($caseStudy->preview ?? '')))))
			@php($caseStudyFullDetails = trim($caseStudyFullTitle . "\n" . ($caseStudyPlainPreview !== '' ? $caseStudyPlainPreview : 'No summary available.')))
			<div class="col-md-4 mb-4">
				<div class="case-study-card">
					<div class="card-image-wrapper">
						@if($caseStudy->listing_image && file_exists(public_path('images/case-study/' . $caseStudy->listing_image)))
							<img src="{{ asset('images/case-study/' . $caseStudy->listing_image) }}" class="card-image lazy-img" alt="{{ $caseStudyFullTitle }}">
						@else
							<div class="case-study-default-image" aria-hidden="true">
								<i class="icofont-briefcase"></i>
							</div>
						@endif
						<div class="card-overlay"></div>
						<div class="card-badge">{{ $caseStudy->category }}</div>
					</div>
					<div class="card-content" data-full-details="{{ $caseStudyFullDetails }}">
						<h5 class="card-title" title="{{ $caseStudyFullTitle }}">{{ $caseStudyFullTitle }}</h5>
						<p class="card-description" title="{{ $caseStudyPlainPreview }}">{{ $caseStudy->preview ?? '' }}</p>
						<div class="card-footer">
							<a class="read-more-btn text-light case-study-gated-link"
							   href="#"
							   data-case-study-id="{{ $caseStudy->id }}"
							   data-resource-title="{{ $caseStudyFullTitle }}">
								Request Download Link <i class="fa fa-envelope"></i>
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

	@if ($caseStudies->hasPages())
		<!-- Pagination for Case Studies -->
		<div class="row mt-5">
			<div class="col-12">
				<nav class="pagination-nav" role="navigation" aria-label="Pagination Navigation">
					<div class="pagination-container">
						@if ($caseStudies->onFirstPage())
							<span class="pagination-btn pagination-btn-disabled" aria-disabled="true">
								<i class="fa fa-chevron-left"></i>
							</span>
						@else
							<a href="{{ $caseStudies->previousPageUrl() }}" class="pagination-btn pagination-btn-prev" aria-label="Previous page">
								<i class="fa fa-chevron-left"></i>
							</a>
						@endif

						<div class="pagination-numbers">
							@foreach ($caseStudies->getUrlRange(1, $caseStudies->lastPage()) as $page => $url)
								@if ($page == $caseStudies->currentPage())
									<span class="pagination-number pagination-number-active" aria-current="page">{{ $page }}</span>
								@else
									<a href="{{ $url }}" class="pagination-number" aria-label="Go to page {{ $page }}">{{ $page }}</a>
								@endif
							@endforeach
						</div>

						@if ($caseStudies->hasMorePages())
							<a href="{{ $caseStudies->nextPageUrl() }}" class="pagination-btn pagination-btn-next" aria-label="Next page">
								<i class="fa fa-chevron-right"></i>
							</a>
						@else
							<span class="pagination-btn pagination-btn-disabled" aria-disabled="true">
								<i class="fa fa-chevron-right"></i>
							</span>
						@endif
					</div>
				</nav>
			</div>
		</div>
	@endif
</div>
</section>

@if(session('status'))
	<section class="case-lead-section" id="resource-request-form">
		<div class="container col-12 col-lg-9 col-md-11 col-sm-12">
			<div class="alert alert-success" style="display:none;">{{ session('status') }}</div>
		</div>
	</section>
@endif

<div id="caseToastStack" class="case-toast-stack" aria-live="polite" aria-atomic="true"></div>

<!-- Case Study Lead Modal -->
<div id="caseStudyLeadModal" class="case-download-modal" aria-hidden="true" role="dialog" aria-labelledby="caseStudyModalTitle">
	<div class="case-download-modal-dialog">
		<div class="case-lead-card">
			<button type="button" class="case-modal-close" id="caseModalCloseBtn" aria-label="Close">&times;</button>
			<h2 class="case-lead-title" id="caseStudyModalTitle">Request Secure Download Link</h2>
			<p class="case-lead-subtitle">Complete this form and we will email a secure link that expires in 1 hour.</p>
			<div class="case-modal-selected" id="selectedCaseStudyLabel">Selected: Case Study</div>

			<form class="form lead-form" method="post" action="{{ route('case-studies.lead.submit') }}">
				@csrf
				<input type="hidden" name="interest" value="case-studies">
				<input type="hidden" name="white_paper_id" id="whitePaperId" value="">
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
							<button class="btn default-background" id="resourceSubmitBtn" type="submit">Submit & Email Download Link</button>
						</div>
					</div>
				</div>
				<p class="case-lead-note">By submitting this form, you agree to be contacted by Armely about relevant resources.</p>
			</form>
		</div>
	</div>
</div>

<!-- White Papers Section -->
<section id="white-papers" class="white-papers-section">
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
			@php($whitePaperFullTitle = trim((string) ($paper->title ?? 'White Paper')))
			@php($whitePaperPlainPreview = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($paper->preview ?? '')))))
			@php($whitePaperFullDetails = trim($whitePaperFullTitle . "\n" . ($whitePaperPlainPreview !== '' ? $whitePaperPlainPreview : 'No summary available.')))
			<div class="col-md-4 mb-4">
				<div class="white-paper-card">
					<div class="card-image-wrapper">
						@if($paper->images && file_exists(public_path('images/white-papers/' . $paper->images)))
							<img src="{{ asset('images/white-papers/' . $paper->images) }}" class="card-image lazy-img" alt="{{ $whitePaperFullTitle }}">
						@else
							<div class="white-paper-default-image" aria-hidden="true">
								<i class="icofont-document"></i>
							</div>
						@endif
						<div class="card-overlay"></div>
						<div class="card-badge white-paper-badge">
							<i class="icofont-document"></i> Resource
						</div>
					</div>
					<div class="card-content" data-full-details="{{ $whitePaperFullDetails }}">
						<h5 class="card-title" title="{{ $whitePaperFullTitle }}">{{ $whitePaperFullTitle }}</h5>
						<p class="card-description" title="{{ $whitePaperPlainPreview }}">{{ $paper->preview ?? '' }}</p>
						<div class="card-footer">
							<a class="read-more-btn white-paper-gated-link"
							   href="#"
							   data-white-paper-id="{{ $paper->id }}"
							   data-resource-type="white-paper"
							   data-resource-title="{{ $whitePaperFullTitle }}">
								Request Download Link <i class="fa fa-envelope"></i>
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

	@if ($whitePapers->hasPages())
		<!-- Pagination for White Papers -->
		<div class="row mt-5">
			<div class="col-12">
				<nav class="pagination-nav" role="navigation" aria-label="Pagination Navigation">
					<div class="pagination-container">
						@if ($whitePapers->onFirstPage())
							<span class="pagination-btn pagination-btn-disabled" aria-disabled="true">
								<i class="fa fa-chevron-left"></i>
							</span>
						@else
							<a href="{{ $whitePapers->previousPageUrl() }}" class="pagination-btn pagination-btn-prev" aria-label="Previous page">
								<i class="fa fa-chevron-left"></i>
							</a>
						@endif

						<div class="pagination-numbers">
							@foreach ($whitePapers->getUrlRange(1, $whitePapers->lastPage()) as $page => $url)
								@if ($page == $whitePapers->currentPage())
									<span class="pagination-number pagination-number-active" aria-current="page">{{ $page }}</span>
								@else
									<a href="{{ $url }}" class="pagination-number" aria-label="Go to page {{ $page }}">{{ $page }}</a>
								@endif
							@endforeach
						</div>

						@if ($whitePapers->hasMorePages())
							<a href="{{ $whitePapers->nextPageUrl() }}" class="pagination-btn pagination-btn-next" aria-label="Next page">
								<i class="fa fa-chevron-right"></i>
							</a>
						@else
							<span class="pagination-btn pagination-btn-disabled" aria-disabled="true">
								<i class="fa fa-chevron-right"></i>
							</span>
						@endif
					</div>
				</nav>
			</div>
		</div>
	@endif
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
	var whitePaperIdInput = document.getElementById('whitePaperId');
	var hiddenResource = document.getElementById('caseStudyRequestedResource');
	var interestInput = document.querySelector('input[name="interest"]');
	var submitBtn = document.getElementById('resourceSubmitBtn');
	var closeBtn = document.getElementById('caseModalCloseBtn');
	var toastStack = document.getElementById('caseToastStack');
	var leadForm = modal.querySelector('form.lead-form');
	var originalBtnText = submitBtn ? submitBtn.textContent : 'Submit & Email Download Link';

	function showToast(message, type) {
		if (!toastStack || !message) {
			return;
		}

		var toast = document.createElement('div');
		toast.className = 'case-toast ' + (type === 'error' ? 'case-toast-error' : 'case-toast-success');
		toast.textContent = message;
		toastStack.appendChild(toast);

		window.setTimeout(function () {
			if (toast.parentNode) {
				toast.parentNode.removeChild(toast);
			}
		}, 5200);
	}

	function openModal(resourceType, resourceId, resourceTitle) {
		if (!resourceId) {
			return;
		}

		caseStudyIdInput.value = '';
		whitePaperIdInput.value = '';

		if (resourceType === 'white-paper') {
			whitePaperIdInput.value = resourceId;
			interestInput.value = 'white-papers';
			submitBtn.textContent = 'Submit & Email White Paper Link';
		} else {
			caseStudyIdInput.value = resourceId;
			interestInput.value = 'case-studies';
			submitBtn.textContent = 'Submit & Email Case Study Link';
		}

		hiddenResource.value = resourceTitle || 'Resource';
		label.textContent = 'Selected: ' + (resourceTitle || 'Resource');
		modal.classList.add('is-open');
		modal.setAttribute('aria-hidden', 'false');
		document.body.style.overflow = 'hidden';
	}

	function closeModal() {
		modal.classList.remove('is-open');
		modal.setAttribute('aria-hidden', 'true');
		document.body.style.overflow = '';
	}

	function setSubmitting(isSubmitting) {
		if (!submitBtn) {
			return;
		}
		submitBtn.disabled = isSubmitting;
		submitBtn.textContent = isSubmitting ? 'Submitting...' : originalBtnText;
	}

	document.querySelectorAll('.case-study-gated-link').forEach(function (link) {
		link.addEventListener('click', function (event) {
			var caseStudyId = link.getAttribute('data-case-study-id') || '';

			event.preventDefault();
			openModal('case-study', caseStudyId, link.getAttribute('data-resource-title') || 'Case Study');
		});
	});

	document.querySelectorAll('.white-paper-gated-link').forEach(function (link) {
		link.addEventListener('click', function (event) {
			var whitePaperId = link.getAttribute('data-white-paper-id') || '';

			event.preventDefault();
			openModal('white-paper', whitePaperId, link.getAttribute('data-resource-title') || 'White Paper');
		});
	});

	if (closeBtn) {
		closeBtn.addEventListener('click', closeModal);
	}

	if (leadForm) {
		leadForm.addEventListener('submit', function (event) {
			event.preventDefault();
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
			})
				.then(function (response) {
					return response.json().catch(function () {
						return {};
					}).then(function (json) {
						return { ok: response.ok, status: response.status, json: json };
					});
				})
				.then(function (result) {
					if (!result.ok) {
						if (result.status === 422 && result.json && result.json.errors) {
							var firstField = Object.keys(result.json.errors)[0];
							var firstMessage = firstField && result.json.errors[firstField] && result.json.errors[firstField][0]
								? result.json.errors[firstField][0]
								: 'Please check the form fields and try again.';
							showToast(firstMessage, 'error');
						} else {
							showToast('Unable to submit right now. Please try again.', 'error');
						}
						return;
					}

					showToast((result.json && result.json.message) || 'Download link sent successfully.', 'success');
					leadForm.reset();
					if (window.grecaptcha && typeof window.grecaptcha.reset === 'function') {
						window.grecaptcha.reset();
					}
					closeModal();
				})
				.catch(function () {
					showToast('Network issue. Please try again.', 'error');
				})
				.finally(function () {
					setSubmitting(false);
				});
		});
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

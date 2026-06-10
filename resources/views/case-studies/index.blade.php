@extends('layouts.public')

@section('title', 'Case Studies | Microsoft Data and AI Results | Armely')
@section('meta_description', 'See how Armely has delivered Microsoft Fabric, Power BI, Copilot, and Power Platform results for healthcare, energy, state and local government, legal social services, transportation and logistics, and agriculture clients. Download case studies.')

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
.case-stats-band {
	background:
		radial-gradient(1200px 260px at 12% -30%, rgba(84, 140, 255, 0.36), rgba(84, 140, 255, 0) 65%),
		linear-gradient(125deg, #10213f 0%, #1f3f76 45%, #173b6f 100%);
	padding: 34px 0 26px;
	position: relative;
	overflow: hidden;
}
.case-stats-band::after {
	content: '';
	position: absolute;
	inset: 0;
	background: linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0));
	pointer-events: none;
}
.case-stat-grid {
	display: grid;
	grid-template-columns: repeat(4, minmax(0, 1fr));
	gap: 14px;
}
.case-stat-item {
	border: 1px solid rgba(177, 205, 255, 0.28);
	background: rgba(255, 255, 255, 0.08);
	backdrop-filter: blur(7px);
	padding: 20px 16px;
	border-radius: 14px;
	text-align: center;
	box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.22);
}
.case-stat-value {
	color: #f4f8ff;
	font-size: 2.1rem;
	font-weight: 800;
	line-height: 1;
	letter-spacing: -0.02em;
}
.case-stat-label {
	color: rgba(226, 237, 255, 0.92);
	font-size: 0.83rem;
	font-weight: 700;
	margin-top: 8px;
	text-transform: uppercase;
	letter-spacing: 0.05em;
}
.case-filter-panel {
	background:
		linear-gradient(155deg, rgba(255, 255, 255, 0.99), rgba(244, 249, 255, 0.99));
	border: 1px solid #d4e1f5;
	border-radius: 18px;
	padding: 16px;
	margin-bottom: 28px;
	box-shadow: 0 15px 34px rgba(22, 48, 91, 0.09);
	position: sticky;
	top: 90px;
	z-index: 12;
	align-self: flex-start;
}
.case-studies-section > .container {
	max-width: 1180px;
}
.case-studies-layout {
	align-items: flex-start;
}
.case-filter-column .case-filter-panel {
	margin-bottom: 0;
}
@media (min-width: 992px) {
	.case-filter-column .case-filter-toolbar {
		flex-direction: column;
		align-items: flex-start;
	}
	.case-filter-column .case-filter-list {
		display: grid;
		grid-template-columns: minmax(0, 1fr);
	}
	.case-filter-column .case-filter-chip {
		width: 100%;
		justify-content: flex-start;
		align-items: flex-start;
		white-space: normal;
		overflow-wrap: anywhere;
		line-height: 1.25;
	}
}
.case-filter-toolbar {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 12px;
	margin-bottom: 14px;
	padding-bottom: 12px;
	border-bottom: 1px solid #e2ebfa;
}
.case-filter-title {
	font-size: 1.04rem;
	font-weight: 800;
	color: #163365;
	margin: 0;
}
.case-filter-meta {
	display: inline-flex;
	align-items: center;
	gap: 10px;
	font-size: 0.83rem;
	font-weight: 700;
	color: #4a5f85;
}
.case-filter-count {
	padding: 6px 10px;
	border-radius: 999px;
	background: #edf4ff;
	border: 1px solid #d4e3fb;
}
.case-filter-reset {
	color: #1f4e96;
	text-decoration: none;
}
.case-filter-reset:hover {
	text-decoration: underline;
}
.case-filter-group {
	margin-bottom: 16px;
	display: block;
}
.case-filter-label {
	color: #1e3a6d;
	font-weight: 800;
	font-size: 0.77rem;
	text-transform: uppercase;
	letter-spacing: 0.1em;
	margin-bottom: 12px;
}
.case-filter-list {
	display: flex;
	flex-wrap: wrap;
	gap: 10px;
}
.case-filter-chip {
	border: 1px solid #c4d6f3;
	background: #ffffff;
	color: #26457d;
	padding: 10px 13px;
	font-size: 0.85rem;
	font-weight: 700;
	border-radius: 999px;
	line-height: 1.25;
	transition: all 0.2s ease;
	display: inline-flex;
	align-items: flex-start;
	gap: 8px;
	white-space: normal;
	overflow-wrap: anywhere;
}
.case-filter-chip::before {
	content: '';
	flex: 0 0 7px;
	width: 7px;
	height: 7px;
	border-radius: 50%;
	background: #87a7de;
	box-shadow: 0 0 0 3px rgba(135, 167, 222, 0.2);
	margin-top: 4px;
}
.case-filter-chip.active {
	background: linear-gradient(135deg, #2f5597 0%, #22447d 100%);
	border-color: #22447d;
	color: #fff;
	box-shadow: 0 8px 18px rgba(47, 85, 151, 0.3);
}
.case-filter-chip.active::before {
	background: #cfe0ff;
	box-shadow: 0 0 0 3px rgba(207, 224, 255, 0.25);
}
.case-filter-chip:hover {
	color: #18366b;
	border-color: #9eb8e7;
	background: #edf3ff;
	text-decoration: none;
	transform: translateY(-1px);
}
.case-filter-chip.active:hover {
	color: #fff;
	background: linear-gradient(135deg, #2f5597 0%, #22447d 100%);
}
#white-papers {
	scroll-margin-top: 110px;
}
.white-papers-section > .container {
	max-width: 1180px;
}
.resource-side-panel {
	background: #ffffff;
	border: 1px solid #dce7fb;
	border-radius: 14px;
	padding: 18px;
	box-shadow: 0 10px 22px rgba(24, 54, 107, 0.08);
	position: sticky;
	top: 90px;
	align-self: flex-start;
}
.resource-side-label {
	color: #2f5597;
	font-size: 0.78rem;
	font-weight: 800;
	letter-spacing: 0.1em;
	text-transform: uppercase;
	margin-bottom: 10px;
}
.resource-side-title {
	color: #163365;
	font-size: 1.18rem;
	font-weight: 800;
	line-height: 1.35;
	margin-bottom: 10px;
}
.resource-side-text {
	color: #4f6181;
	line-height: 1.6;
	margin-bottom: 18px;
}
.resource-topic-list {
	display: grid;
	gap: 10px;
	margin: 0;
	padding: 0;
	list-style: none;
}
.resource-topic-list li {
	border: 1px solid #dce7fb;
	border-radius: 10px;
	color: #294a84;
	font-size: 0.9rem;
	font-weight: 700;
	padding: 10px 12px;
	background: #f7faff;
	line-height: 1.28;
	white-space: normal;
	overflow-wrap: anywhere;
}
.resource-topic-list li a {
	display: block;
	color: inherit;
	text-decoration: none;
}
.resource-topic-list li a.case-filter-chip {
	padding: 0;
	border: 0;
	background: transparent;
	box-shadow: none;
	width: 100%;
	justify-content: flex-start;
	color: inherit;
}
.resource-topic-list li a.case-filter-chip::before {
	display: none;
}
.resource-topic-list li.active {
	background: linear-gradient(135deg, #2f5597 0%, #22447d 100%);
	border-color: #22447d;
	color: #ffffff;
	box-shadow: 0 8px 18px rgba(47, 85, 151, 0.25);
}
.resource-topic-list li:hover {
	border-color: #9eb8e7;
	background: #edf3ff;
}
.resource-topic-list li.active:hover {
	background: linear-gradient(135deg, #2f5597 0%, #22447d 100%);
}
.resource-filter-actions {
	margin-top: 12px;
}
.filter-empty-state {
	display: none;
	border: 1px dashed #c8d8f3;
	background: #f7faff;
	color: #2f4f85;
	border-radius: 12px;
	padding: 18px 16px;
	font-weight: 600;
	line-height: 1.45;
}

.case-study-card,
.white-paper-card {
	border-radius: 14px;
	border: 1px solid #dce7fb;
	background: #fff;
	box-shadow: 0 10px 22px rgba(24, 54, 107, 0.08);
	overflow: hidden;
	transition: transform .25s ease, box-shadow .25s ease;
}
.case-study-card:hover,
.white-paper-card:hover {
	transform: translateY(-6px);
	box-shadow: 0 20px 34px rgba(24, 54, 107, 0.16);
}
.card-image-wrapper {
	min-height: 180px;
	height: auto;
}
.card-content {
	padding: 18px;
}
.card-title {
	font-size: 1.08rem;
	line-height: 1.35;
	min-height: 3.2em;
	margin-bottom: 8px;
}
.card-description {
	color: #4f6181;
	font-size: 0.94rem;
	line-height: 1.55;
	min-height: 4.65em;
}
.card-footer {
	padding-top: 12px;
	border-top: 1px solid #e8eefb;
}
.read-more-btn {
	border-radius: 10px;
	font-weight: 800;
	background: linear-gradient(135deg, #2f5597 0%, #23457f 100%);
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
	.case-stat-grid {
		grid-template-columns: repeat(2, minmax(0, 1fr));
	}
	.case-stats-band {
		padding-bottom: 16px;
	}
	.case-stat-value {
		font-size: 1.65rem;
	}
	.case-filter-panel {
		padding: 16px;
		border-radius: 14px;
		top: 74px;
		position: static;
		width: 100%;
		margin-left: 0;
		margin-right: 0;
	}
	.case-studies-section > .container {
		padding-left: 14px;
		padding-right: 14px;
	}
	.case-filter-toolbar {
		flex-direction: column;
		align-items: flex-start;
	}
	.case-filter-list {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 8px;
	}
	.case-filter-chip {
		padding: 10px 11px;
		font-size: 0.8rem;
		width: 100%;
		justify-content: flex-start;
		white-space: normal;
		line-height: 1.25;
	}
	.resource-side-panel {
		position: static;
		margin-bottom: 22px;
	}
}
@media (max-width: 479px) {
	.case-filter-list {
		grid-template-columns: minmax(0, 1fr);
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
	@php($activeFilterCount = ($selectedIndustry !== '' ? 1 : 0) + ($selectedTopic !== '' ? 1 : 0))
	<div class="row case-studies-layout justify-content-center">
		<div class="col-12 col-lg-4 col-xl-3 mb-4 mb-lg-0 case-filter-column">
			<div class="case-filter-panel">
				<div class="case-filter-toolbar">
					<h3 class="case-filter-title">Find Relevant Stories Faster</h3>
					<div class="case-filter-meta">
						<span id="caseFilterCount" class="case-filter-count">{{ $activeFilterCount }} active {{ $activeFilterCount === 1 ? 'filter' : 'filters' }}</span>
						<a id="caseFilterReset" class="case-filter-reset" href="{{ route('case-studies.index') }}">Clear all</a>
					</div>
				</div>
				<div class="case-filter-group" data-filter-panel="technology">
					<div class="case-filter-label">Technology</div>
					<div class="case-filter-list">
						<a class="case-filter-chip {{ $selectedTopic === '' ? 'active' : '' }}" data-filter-scope="case" data-filter-group="topic" data-filter-value="" href="{{ route('case-studies.index', array_filter(['industry' => $selectedIndustry, 'white_topic' => $selectedWhiteTopic])) }}">All technologies</a>
						@foreach($topicFilters as $key => $label)
							<a class="case-filter-chip {{ $selectedTopic === $key ? 'active' : '' }}" data-filter-scope="case" data-filter-group="topic" data-filter-value="{{ $key }}" href="{{ route('case-studies.index', array_filter(['industry' => $selectedIndustry, 'case_topic' => $key, 'white_topic' => $selectedWhiteTopic])) }}">{{ $label }}</a>
						@endforeach
					</div>
				</div>
				<div class="case-filter-group mb-0" data-filter-panel="industry">
					<div class="case-filter-label">Category</div>
					<div class="case-filter-list">
						<a class="case-filter-chip {{ $selectedIndustry === '' ? 'active' : '' }}" data-filter-scope="case" data-filter-group="industry" data-filter-value="" href="{{ route('case-studies.index', array_filter(['case_topic' => $selectedTopic, 'white_topic' => $selectedWhiteTopic])) }}">All categories</a>
						@foreach($industryFilters as $key => $label)
							<a class="case-filter-chip {{ $selectedIndustry === $key ? 'active' : '' }}" data-filter-scope="case" data-filter-group="industry" data-filter-value="{{ $key }}" href="{{ route('case-studies.index', array_filter(['industry' => $key, 'case_topic' => $selectedTopic, 'white_topic' => $selectedWhiteTopic])) }}">{{ $label }}</a>
						@endforeach
					</div>
				</div>
			</div>
		</div>
		<div class="col-12 col-lg-8 col-xl-9">
			<div class="row">
		@forelse($caseStudies as $caseStudy)
			@php($caseStudyTitle = trim((string) ($caseStudy->title ?? '')))
			@php($caseStudyDisplayTitle = $caseStudyTitle !== '' ? $caseStudyTitle : (string) ($caseStudy->category ?? 'Case Study'))
			@php($caseStudyFullTitle = trim($caseStudyDisplayTitle . ' Solution'))
			@php($caseStudyPlainPreview = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($caseStudy->preview ?? '')))))
			@php($caseStudyFullDetails = trim($caseStudyFullTitle . "\n" . ($caseStudyPlainPreview !== '' ? $caseStudyPlainPreview : 'No summary available.')))
			<div class="col-12 col-md-6 col-lg-4 mb-4 js-case-card" data-industry="{{ $caseStudy->industry_filter ?? '' }}" data-topics="{{ implode(',', $caseStudy->technology_filters ?? []) }}">
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
							<a class="read-more-btn text-light"
							   href="{{ route('case-studies.show', $caseStudy->slug) }}"
							   data-case-study-id="{{ $caseStudy->id }}"
							   data-resource-title="{{ $caseStudyFullTitle }}">
								View Case Study <i class="fa fa-arrow-right"></i>
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

			<div id="caseFilterEmptyState" class="filter-empty-state mt-2" role="status" aria-live="polite" @if($caseStudies->isEmpty() && $activeFilterCount > 0) style="display:block;" @endif>
				No case studies match the selected filters. Try another Category or Technology.
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
	</div>
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
						<label class="field-label text-start">First name *</label>
						<div class="form-group">
							<input class="lead-field" type="text" name="name" placeholder="First name" required>
						</div>
					</div>
					<div class="col-lg-6">
						<label class="field-label text-start">Work email *</label>
						<div class="form-group">
							<input class="lead-field" type="email" name="email" placeholder="Work email" required>
						</div>
					</div>
					<div class="col-lg-6">
						<label class="field-label text-start">Company</label>
						<div class="form-group">
							<input class="lead-field" type="text" name="organization" placeholder="Company">
						</div>
					</div>
					<div class="col-lg-6">
						<label class="field-label text-start">Job title</label>
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
	<div class="row case-studies-layout justify-content-center">
		<div class="col-12 col-lg-4 col-xl-3 mb-4 mb-lg-0">
			<aside class="resource-side-panel">
				<div class="resource-side-label">White Papers</div>
				<h2 class="resource-side-title">Microsoft platform guidance for leaders</h2>
				<p class="resource-side-text">In-depth resources for teams planning data, AI, governance, and Copilot initiatives.</p>
				<ul class="resource-topic-list">
					<li class="{{ $selectedWhiteTopic === '' ? 'active' : '' }}">
						<a class="case-filter-chip js-filter-chip {{ $selectedWhiteTopic === '' ? 'active' : '' }}" data-filter-scope="white" data-filter-group="topic" data-filter-value="" href="{{ route('case-studies.index', array_filter(['industry' => $selectedIndustry, 'case_topic' => $selectedTopic])) }}#white-papers">All topics</a>
					</li>
					@foreach($topicFilters as $key => $label)
						<li class="{{ $selectedWhiteTopic === $key ? 'active' : '' }}">
							<a class="case-filter-chip js-filter-chip {{ $selectedWhiteTopic === $key ? 'active' : '' }}" data-filter-scope="white" data-filter-group="topic" data-filter-value="{{ $key }}" href="{{ route('case-studies.index', array_filter(['industry' => $selectedIndustry, 'case_topic' => $selectedTopic, 'white_topic' => $key])) }}#white-papers">{{ $label }}</a>
						</li>
					@endforeach
				</ul>
				<div class="resource-filter-actions">
					<a id="whiteFilterReset" class="case-filter-reset" href="{{ route('case-studies.index', array_filter(['industry' => $selectedIndustry, 'case_topic' => $selectedTopic])) }}#white-papers">Clear white paper filters</a>
				</div>
			</aside>
		</div>
		<div class="col-12 col-lg-8 col-xl-9">
			<div class="row">
			@forelse($whitePapers as $paper)
				@php($whitePaperFullTitle = trim((string) ($paper->title ?? 'White Paper')))
				@php($whitePaperPlainPreview = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($paper->preview ?? '')))))
				@php($whitePaperFullDetails = trim($whitePaperFullTitle . "\n" . ($whitePaperPlainPreview !== '' ? $whitePaperPlainPreview : 'No summary available.')))
				@php($whitePaperFilterText = strtolower(trim($whitePaperFullTitle . ' ' . $whitePaperPlainPreview)))
				<div class="col-12 col-md-6 col-lg-4 mb-4 js-white-paper-card" data-filter-text="{{ $whitePaperFilterText }}">
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
								<a class="read-more-btn text-light"
								   href="{{ route('resources.show', $paper->slug) }}"
								   data-white-paper-id="{{ $paper->id }}"
								   data-resource-type="white-paper"
								   data-resource-title="{{ $whitePaperFullTitle }}">
									View Resource <i class="fa fa-arrow-right"></i>
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

			<div id="whitePaperFilterEmptyState" class="filter-empty-state mt-2" role="status" aria-live="polite" @if($whitePapers->isEmpty() && $selectedWhiteTopic !== '') style="display:block;" @endif>
				No white papers match the selected filters. Try another topic or clear filters.
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

(function () {
	var caseFilterChips = Array.prototype.slice.call(document.querySelectorAll('.case-filter-chip[data-filter-scope="case"][data-filter-group]'));
	var whiteFilterChips = Array.prototype.slice.call(document.querySelectorAll('.case-filter-chip[data-filter-scope="white"][data-filter-group]'));
	if (!caseFilterChips.length && !whiteFilterChips.length) {
		return;
	}

	// Filters are resolved on the server so matches include records from every paginated page.
	return;

	var caseCards = Array.prototype.slice.call(document.querySelectorAll('.js-case-card'));
	var whitePaperCards = Array.prototype.slice.call(document.querySelectorAll('.js-white-paper-card'));
	var caseFilterCount = document.getElementById('caseFilterCount');
	var resetLink = document.getElementById('caseFilterReset');
	var whiteResetLink = document.getElementById('whiteFilterReset');
	var casePagination = document.querySelector('.case-studies-section .pagination-nav');
	var whitePagination = document.querySelector('.white-papers-section .pagination-nav');
	var caseEmptyState = document.getElementById('caseFilterEmptyState');
	var whiteEmptyState = document.getElementById('whitePaperFilterEmptyState');

	var url = new URL(window.location.href);
	var selectedCase = {
		industry: url.searchParams.get('case_industry') || url.searchParams.get('industry') || @json((string) $selectedIndustry),
		topic: url.searchParams.get('case_topic') || url.searchParams.get('topic') || @json((string) $selectedTopic)
	};
	var selectedWhite = {
		topic: url.searchParams.get('white_topic') || @json((string) $selectedTopic)
	};

	var topicTermsByKey = {
		'fabric-data': ['fabric', 'power bi', 'data', 'analytics', 'warehouse', 'lakehouse'],
		'power-platform': ['power platform', 'power apps', 'power automate', 'power pages'],
		'ai-cognitive-services': ['ai', 'copilot', 'cognitive', 'agent'],
		'sharepoint-collaboration': ['sharepoint', 'teams', 'collaboration', 'intranet']
	};

	var industryTermsByKey = {
		healthcare: ['health', 'healthcare', 'swope', 'unmc', 'patient', 'medical'],
		'energy-oil-gas': ['energy', 'oil', 'gas', 'utility', 'utilities', 'sage', 'butte'],
		'state-local-government': ['government', 'public sector', 'state', 'city', 'county', 'agency', 'municipal', 'plano'],
		'legal-social-services': ['legal', 'social services', 'social service', 'nonprofit', 'community'],
		'transportation-logistics': ['transportation', 'logistics', 'supply chain', 'fleet', 'shipping', 'freight', 'mhc'],
		'agriculture-cannabis': ['agriculture', 'agri', 'farming', 'farm', 'cannabis', 'cultivation']
	};

	function normalizeIndustryKey(value) {
		var key = String(value || '').toLowerCase().trim();
		if (!key) {
			return '';
		}

		var map = {
			'state-local-government': 'government-public-sector',
			'local-government': 'government-public-sector',
			'public-sector': 'government-public-sector',
			'government-public-sector': 'government-public-sector',
			'energy-utilities': 'energy-oil-gas',
			'oil-gas': 'energy-oil-gas',
			'oil-and-gas': 'energy-oil-gas',
			'energy-oil-gas': 'energy-oil-gas',
			'social-services': 'legal-social-services',
			'legal-social-services': 'legal-social-services'
		};

		return map[key] || key;
	}

	selectedCase.industry = normalizeIndustryKey(selectedCase.industry);

	function setActiveChip(scope, group, value) {
		var chips = scope === 'white' ? whiteFilterChips : caseFilterChips;
		chips
			.filter(function (chip) { return chip.getAttribute('data-filter-group') === group; })
			.forEach(function (chip) {
				chip.classList.toggle('active', chip.getAttribute('data-filter-value') === value);
				var parent = chip.closest('li');
				if (parent) {
					parent.classList.toggle('active', chip.classList.contains('active'));
				}
			});
	}

	function updateActiveCount() {
		var count = 0;
		if (selectedCase.industry) {
			count += 1;
		}
		if (selectedCase.topic) {
			count += 1;
		}

		if (caseFilterCount) {
			caseFilterCount.textContent = count + ' active ' + (count === 1 ? 'filter' : 'filters');
		}
	}

	function applyCaseFilters() {
		var visibleCaseCards = 0;
		caseCards.forEach(function (card) {
			var cardIndustry = normalizeIndustryKey(card.getAttribute('data-industry') || '');
			var cardTopics = (card.getAttribute('data-topics') || '').split(',').filter(Boolean);

			var matchesIndustry = !selectedCase.industry || selectedCase.industry === cardIndustry;
			var matchesTopic = !selectedCase.topic || cardTopics.indexOf(selectedCase.topic) !== -1;

			var isVisible = matchesIndustry && matchesTopic;
			card.style.display = isVisible ? '' : 'none';
			if (isVisible) {
				visibleCaseCards += 1;
			}
		});

		if (caseEmptyState) {
			caseEmptyState.style.display = visibleCaseCards === 0 ? 'block' : 'none';
		}

		if (casePagination) {
			casePagination.style.display = (selectedCase.industry || selectedCase.topic || visibleCaseCards === 0) ? 'none' : '';
		}
	}

	function applyWhiteFilters() {
		var visibleWhiteCards = 0;
		whitePaperCards.forEach(function (card) {
			var haystack = String(card.getAttribute('data-filter-text') || '').toLowerCase();
			var topicTerms = selectedWhite.topic ? (topicTermsByKey[selectedWhite.topic] || [selectedWhite.topic.replace(/-/g, ' ')]) : [];

			var matchesTopic = !selectedWhite.topic || topicTerms.some(function (term) {
				return haystack.indexOf(String(term).toLowerCase()) !== -1;
			});

			var isVisible = matchesTopic;
			card.style.display = isVisible ? '' : 'none';
			if (isVisible) {
				visibleWhiteCards += 1;
			}
		});

		if (whiteEmptyState) {
			whiteEmptyState.style.display = visibleWhiteCards === 0 ? 'block' : 'none';
		}

		if (whitePagination) {
			whitePagination.style.display = (selectedWhite.topic || visibleWhiteCards === 0) ? 'none' : '';
		}
	}

	function updateUrl() {
		if (!window.history || !window.history.replaceState) {
			return;
		}

		var url = new URL(window.location.href);
		url.searchParams.delete('industry');
		url.searchParams.delete('topic');

		if (selectedCase.industry) {
			url.searchParams.set('case_industry', selectedCase.industry);
		} else {
			url.searchParams.delete('case_industry');
		}

		if (selectedCase.topic) {
			url.searchParams.set('case_topic', selectedCase.topic);
		} else {
			url.searchParams.delete('case_topic');
		}

		if (selectedWhite.topic) {
			url.searchParams.set('white_topic', selectedWhite.topic);
		} else {
			url.searchParams.delete('white_topic');
		}

		window.history.replaceState({}, '', url.toString());
	}

	caseFilterChips.forEach(function (chip) {
		chip.addEventListener('click', function (event) {
			event.preventDefault();
			var group = chip.getAttribute('data-filter-group');
			var value = chip.getAttribute('data-filter-value') || '';

			selectedCase[group] = value;
			setActiveChip('case', group, value);
			updateActiveCount();
			applyCaseFilters();
			applyWhiteFilters();
			updateUrl();
		});
	});

	whiteFilterChips.forEach(function (chip) {
		chip.addEventListener('click', function (event) {
			event.preventDefault();
			var group = chip.getAttribute('data-filter-group');
			var value = chip.getAttribute('data-filter-value') || '';

			selectedWhite[group] = value;
			setActiveChip('white', group, value);
			applyWhiteFilters();
			updateUrl();
		});
	});

	if (resetLink) {
		resetLink.addEventListener('click', function (event) {
			event.preventDefault();
			selectedCase.industry = '';
			selectedCase.topic = '';
			setActiveChip('case', 'industry', '');
			setActiveChip('case', 'topic', '');
			updateActiveCount();
			applyCaseFilters();
			applyWhiteFilters();
			updateUrl();
		});
	}

	if (whiteResetLink) {
		whiteResetLink.addEventListener('click', function (event) {
			event.preventDefault();
			selectedWhite.topic = '';
			setActiveChip('white', 'topic', '');
			applyWhiteFilters();
			updateUrl();
		});
	}

	setActiveChip('case', 'industry', selectedCase.industry || '');
	setActiveChip('case', 'topic', selectedCase.topic || '');
	setActiveChip('white', 'topic', selectedWhite.topic || '');
	updateActiveCount();
	applyCaseFilters();
	applyWhiteFilters();
})();
</script>

@endsection

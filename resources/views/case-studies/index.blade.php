@extends('layouts.public')

@section('title', 'Case Studies & White Papers - Armely')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/case-studies-modern.css') }}">
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
						<p class="card-description">{{ Str::limit($caseStudy->body, 120) }}</p>
						<div class="card-footer">
							<a class="read-more-btn text-light" target="_blank" href="{{ $caseStudy->pdf_url ? (str_starts_with($caseStudy->pdf_url, 'http') ? $caseStudy->pdf_url : asset('case_docs/' . $caseStudy->pdf_url)) : '#' }}">
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
						<p class="card-description">{{ Str::limit($paper->body, 120) }}</p>
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

@endsection

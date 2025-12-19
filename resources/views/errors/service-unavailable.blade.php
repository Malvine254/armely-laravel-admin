@extends('layouts.public')

@section('title', 'Service Temporarily Unavailable - Armely')

@section('content')
<div class="breadcrumbs overlay">
	<div class="container">
		<div class="bread-inner">
			<div class="row">
				<div class="col-12">
					<h2>Service Unavailable</h2>
				</div>
			</div>
		</div>
	</div>
</div>

<section class="error-section pt-5 pb-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-8">
				<div class="card error-card p-5 text-center">
					<div class="error-icon mb-4">
						<i class="icofont-warning-alt" style="font-size: 4rem; color: #f39c12;"></i>
					</div>
					<h1 class="mb-3">Service Temporarily Unavailable</h1>
					<p class="lead mb-4 text-muted">
						We're experiencing a temporary issue accessing our database. Our team has been notified and is working to resolve this as quickly as possible.
					</p>
					<div class="error-details mb-4">
						<p><strong>Error Code:</strong> 503 Service Unavailable</p>
						<p class="small text-muted">Please try again in a few moments.</p>
					</div>
					<div class="action-buttons">
						<a href="{{ route('home') }}" class="btn default-button btn-lg me-2">
							<i class="icofont-home"></i> Back to Home
						</a>
						<a href="javascript:location.reload()" class="btn btn-outline-primary btn-lg">
							<i class="icofont-refresh"></i> Retry
						</a>
					</div>
					<hr class="my-4">
					<p class="small text-muted">
						If this issue persists, please <a href="{{ route('contact') }}" class="text-primary">contact us</a> for support.
					</p>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection

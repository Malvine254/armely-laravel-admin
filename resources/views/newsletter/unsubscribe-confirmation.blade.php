@extends('layouts.public')

@php($title = 'Newsletter Unsubscribed')
@section('title', 'Newsletter Unsubscribed | Armely')
@section('meta_description', 'Confirm your newsletter unsubscribe status or subscribe back to Armely updates if this was a mistake.')
@section('robots', 'noindex,nofollow')

@push('styles')
<style>
.unsubscribe-page {
    background: radial-gradient(circle at top, #f4f8ff 0%, #edf3fb 38%, #f7fafc 100%);
}

.unsubscribe-card {
    background: #ffffff;
    border: 1px solid #d9e5f3;
    border-radius: 22px;
    padding: 48px 36px;
    box-shadow: 0 24px 50px rgba(13, 31, 60, 0.12);
}

.unsubscribe-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 999px;
    background: #eff6ff;
    color: #1f4f91;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.02em;
    margin-bottom: 18px;
}

.unsubscribe-icon {
    width: 92px;
    height: 92px;
    margin: 0 auto 22px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    color: #ffffff;
}

.unsubscribe-title {
    color: #0d1f3c;
    font-weight: 800;
    margin-bottom: 14px;
}

.unsubscribe-copy {
    font-size: 17px;
    line-height: 1.65;
    color: #4b5b72;
    margin-bottom: 0;
}

.unsubscribe-panel {
    margin-top: 28px;
    padding: 26px;
    border-radius: 16px;
    background: #f7fbff;
    border: 1px solid #dce8f7;
    text-align: left;
}

.unsubscribe-panel h4 {
    color: #1f4f91;
    font-weight: 800;
    margin-bottom: 10px;
}

.unsubscribe-panel p {
    color: #51627c;
    margin-bottom: 16px;
    line-height: 1.6;
}

.unsubscribe-form .form-control {
    min-height: 48px;
    border-radius: 12px;
    border-color: #cfdcef;
}

.unsubscribe-form .btn {
    min-height: 48px;
    border-radius: 12px;
    font-weight: 800;
    color: #ffffff !important;
}

.unsubscribe-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    justify-content: center;
    margin-top: 28px;
}

.unsubscribe-actions .btn {
    min-width: 180px;
    color: #ffffff !important;
}

.unsubscribe-actions .btn-secondary-custom {
    background: #0d1f3c;
    border-color: #0d1f3c;
}

.unsubscribe-actions .btn-secondary-custom:hover {
    background: #1f4f91;
    border-color: #1f4f91;
}

.unsubscribe-form .form-row {
    margin-left: -6px;
    margin-right: -6px;
}

.unsubscribe-form .form-row > [class*="col-"] {
    padding-left: 6px;
    padding-right: 6px;
}

.unsubscribe-status {
    margin-bottom: 18px;
}

.unsubscribe-status .alert {
    margin-bottom: 0;
    border-radius: 14px;
}

@media (max-width: 576px) {
    .unsubscribe-card {
        padding: 32px 20px;
    }

    .unsubscribe-panel {
        padding: 20px;
    }

    .unsubscribe-actions .btn {
        min-width: 100%;
    }
}
</style>
@endpush

@section('content')
<section class="contact-us section unsubscribe-page">
    <div class="container col-12 col-lg-8 col-md-11 col-sm-12">
        <div class="inner">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="contact-us-form unsubscribe-card">
                        <div class="unsubscribe-badge">
                            <i class="icofont-email"></i>
                            Armely email preferences
                        </div>

                        <div class="unsubscribe-icon {{ $success ? 'default-background' : 'bg-danger' }}">
                            <i class="fa-solid {{ $success ? 'fa-circle-check' : 'fa-circle-exclamation' }}"></i>
                        </div>

                        <h1 class="unsubscribe-title">
                            {{ $success ? 'You have been unsubscribed' : 'We could not confirm that unsubscribe request' }}
                        </h1>

                        <p class="unsubscribe-copy">
                            @if($success)
                                {{ $source === 'admin' ? 'You will no longer receive Armely admin update emails.' : 'You will no longer receive Armely newsletter emails.' }}
                            @else
                                Please try the link again or contact us if you believe this was a mistake.
                            @endif
                        </p>

                        @if(session('status') || session('newsletter_error'))
                            <div class="unsubscribe-status">
                                <div class="alert {{ session('newsletter_error') ? 'alert-danger' : 'alert-success' }}" role="alert">
                                    {{ session('newsletter_error') ?? session('status') }}
                                </div>
                            </div>
                        @endif

                        <div class="unsubscribe-panel">
                            <h4>Changed your mind?</h4>
                            <p>Subscribe back below and we will start sending Armely blogs, events, and Microsoft platform updates again.</p>

                            <form class="unsubscribe-form" action="{{ route('newsletter.subscribe') }}" method="post">
                                @csrf
                                <div class="form-row align-items-center">
                                    <div class="col-md-8 mb-2 mb-md-0">
                                        <input
                                            type="email"
                                            name="email"
                                            class="form-control"
                                            value="{{ old('email', $email ?? '') }}"
                                            placeholder="Work email"
                                            aria-label="Newsletter email"
                                            required
                                        >
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn default-background w-100">Subscribe</button>
                                    </div>
                                </div>
                                <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off">
                            </form>
                        </div>

                        <div class="unsubscribe-actions">
                            <a href="{{ route('home') }}" class="btn btn-secondary-custom">Return Home</a>
                            <a href="{{ route('blog.index') }}" class="btn default-background">Browse Blog</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

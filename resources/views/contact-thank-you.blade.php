@extends('layouts.public')

@php($title = 'Thank You')

@push('styles')
<style>
.thankyou-section {
    background: #f8fbff;
}

.thankyou-card {
    background: #ffffff;
    border: 1px solid #dbe7f5;
    border-radius: 18px;
    padding: 48px 34px;
    box-shadow: 0 22px 45px rgba(13, 31, 60, 0.12);
}

.thankyou-icon {
    width: 92px;
    height: 92px;
    margin: 0 auto 22px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
}

.thankyou-title {
    color: #0d1f3c;
    font-weight: 800;
    margin-bottom: 14px;
}

.thankyou-subtitle {
    font-size: 18px;
    margin-bottom: 10px;
    color: #334155;
}

.thankyou-desc {
    font-size: 16px;
    color: #64748b;
    margin-bottom: 30px;
}

.thankyou-next {
    background: #f3f8ff;
    border: 1px solid #d8e6f8;
    border-radius: 14px;
    padding: 26px;
    margin-bottom: 30px;
    text-align: left;
}

.thankyou-next h4 {
    color: #1E62AD;
    font-weight: 700;
    margin-bottom: 14px;
}

.thankyou-next ul {
    margin-bottom: 0;
    padding-left: 18px;
    color: #334155;
}

.thankyou-next li {
    margin-bottom: 12px;
}

.thankyou-next li:last-child {
    margin-bottom: 0;
}

.thankyou-actions .btn {
    min-width: 200px;
    margin: 6px;
}

.thankyou-actions .btn-secondary-custom {
    background: #0d1f3c;
    color: #ffffff;
}

.thankyou-actions .btn-secondary-custom:hover {
    background: #1E62AD;
    color: #ffffff;
}

@media (max-width: 576px) {
    .thankyou-card {
        padding: 34px 20px;
    }

    .thankyou-actions .btn {
        min-width: 100%;
    }
}
</style>
@endpush

@section('content')

<!-- Start Thank You Section -->
<section class="contact-us section thankyou-section">
    <div class="container col-12 col-lg-8 col-md-11 col-sm-12">
        <div class="inner">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="contact-us-form thankyou-card">
                        <div class="thankyou-icon default-background text-light">
                            <i class="icofont-check-circled"></i>
                        </div>
                        <h2 class="thankyou-title">Thank You!</h2>
                        <p class="thankyou-subtitle">Your message has been sent successfully.</p>
                        <p class="thankyou-desc">We have received your inquiry and appreciate you reaching out to us. Our team will review your message and get back to you as soon as possible.</p>
                        
                        <div class="thankyou-next">
                            <h4>What happens next?</h4>
                            <ul>
                                <li><strong>Review:</strong> Our team will carefully review your message</li>
                                <li><strong>Response:</strong> We'll reach out within 24-48 business hours</li>
                                <li><strong>Follow-up:</strong> We'll discuss your needs and find the best solution</li>
                            </ul>
                        </div>

                        <div class="thankyou-actions">
                            <a href="/" class="btn default-background">Return to Home</a>
                            <a href="/contact" class="btn btn-secondary-custom">Send Another Message</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--/ End Thank You Section -->

@endsection

@extends('layouts.public')

@php($title = 'Contact')
@section('title', 'Contact | Armely')
@section('meta_description', 'Contact Armely to discuss your data, AI, cloud, and Microsoft platform transformation goals.')

@push('styles')
<style>
/* ===== Contact Page Redesign ===== */

/* --- Main section background --- */
.contact-main-section {
    background: #f4f7fc;
    padding: 64px 0 72px;
}

/* --- Form card --- */
.contact-form-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 32px rgba(47, 85, 151, 0.10);
    padding: 40px 40px 36px;
}
.contact-form-card .card-heading {
    margin-bottom: 28px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eef1f8;
}
.contact-form-card .card-heading h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e3a6b;
    margin-bottom: 6px;
}
.contact-form-card .card-heading p {
    color: #6c757d;
    font-size: 0.92rem;
    margin: 0;
}

/* --- Field groups --- */
.cf-field {
    margin-bottom: 18px;
}
.cf-field label {
    display: block;
    font-size: 0.78rem;
    font-weight: 700;
    color: #3a4966;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: 7px;
}
.cf-field label .req {
    color: #e53e3e;
    font-size: 0.85rem;
}
.cf-input-wrap {
    position: relative;
}
.cf-input-wrap .cf-icon {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #aab4cc;
    font-size: 1rem;
    pointer-events: none;
    transition: color 0.2s;
}
.cf-input-wrap.is-textarea .cf-icon {
    top: 14px;
    transform: none;
}
.cf-input-wrap input,
.cf-input-wrap textarea {
    width: 100%;
    padding: 11px 14px 11px 40px;
    border: 1.5px solid #dde3ef;
    border-radius: 8px;
    font-size: 0.94rem;
    color: #2d3748;
    background: #fafbfd;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
    font-family: inherit;
}
.cf-input-wrap input:focus,
.cf-input-wrap textarea:focus {
    border-color: #2f5597;
    box-shadow: 0 0 0 3px rgba(47, 85, 151, 0.10);
    background: #fff;
}
.cf-input-wrap:focus-within .cf-icon {
    color: #2f5597;
}
.cf-input-wrap textarea {
    min-height: 120px;
    resize: vertical;
    padding-top: 12px;
}

/* --- Submit button --- */
.contact-submit-btn {
    display: block;
    width: 100%;
    padding: 14px 24px;
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6b 100%);
    color: #fff;
    font-size: 1rem;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    letter-spacing: 0.03em;
    transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
    margin-top: 4px;
    box-shadow: 0 4px 16px rgba(47, 85, 151, 0.28);
}
.contact-submit-btn:hover {
    opacity: 0.90;
    transform: translateY(-1px);
    box-shadow: 0 6px 22px rgba(47, 85, 151, 0.36);
}
.contact-submit-btn:disabled {
    opacity: 0.60;
    cursor: not-allowed;
    transform: none;
}

/* --- Sidebar --- */
.contact-sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Branded reach card */
.contact-reach-card {
    background: linear-gradient(135deg, #2f5597 0%, #1e3a6b 100%);
    border-radius: 16px;
    padding: 32px 28px;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.contact-reach-card::before {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 160px; height: 160px;
    border-radius: 50%;
    background: rgba(255,255,255,0.07);
}
.contact-reach-card::after {
    content: '';
    position: absolute;
    bottom: -55px; left: -30px;
    width: 130px; height: 130px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
}
.reach-icon-wrap {
    width: 52px; height: 52px;
    background: rgba(255,255,255,0.15);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.45rem;
    margin-bottom: 18px;
    position: relative;
    z-index: 1;
}
.contact-reach-card h4 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #fff !important;
    margin-bottom: 10px;
    position: relative; z-index: 1;
}
.contact-reach-card p {
    font-size: 0.88rem;
    color: #fff !important;
    opacity: 0.82;
    margin: 0;
    line-height: 1.65;
    position: relative; z-index: 1;
}
.response-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 18px;
    font-size: 0.81rem;
    font-weight: 600;
    background: rgba(255,255,255,0.15);
    border-radius: 20px;
    padding: 6px 14px;
    position: relative; z-index: 1;
}

/* Info list card */
.contact-info-list-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 32px rgba(47, 85, 151, 0.08);
    padding: 8px 24px 4px;
}
.contact-info-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 18px 0;
    border-bottom: 1px solid #eef1f8;
}
.contact-info-item:last-child {
    border-bottom: none;
}
.info-icon-wrap {
    width: 44px; height: 44px;
    min-width: 44px;
    background: linear-gradient(135deg, #e8eef8 0%, #d5e0f2 100%);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.15rem;
    color: #2f5597;
}
.info-item-text h5 {
    font-size: 0.9rem;
    font-weight: 700;
    color: #1e3a6b;
    margin-bottom: 3px;
}
.info-item-text p {
    font-size: 0.82rem;
    color: #6c757d;
    margin: 0;
    line-height: 1.5;
}

/* --- Responsive --- */
@media (max-width: 991px) {
    .contact-form-card {
        padding: 28px 22px;
        margin-bottom: 8px;
    }
    .contact-main-section {
        padding: 44px 0 56px;
    }
}
@media (max-width: 575px) {
    .contact-form-card {
        padding: 22px 16px;
    }
    .contact-reach-card {
        padding: 24px 20px;
    }
}
</style>
@endpush

@section('content')

<!-- Breadcrumbs -->
<div class="breadcrumbs-contact overlay">
    <div class="container">
        <div class="bread-inner">
            <div class="row">
                <div class="col-12">
                    <h2>Contact Us</h2>
                    <ul class="bread-list">
                        <li><a href="/">Home</a></li>
                        <li><i class="icofont-simple-right"></i></li>
                        <li class="active">Contact Us</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumbs -->

<!-- Contact Main Section -->
<section class="contact-main-section">
    <div class="container">
        <div class="row align-items-start g-4">

            <!-- ===== Form Column ===== -->
            <div class="col-lg-7 col-12">
                <div class="contact-form-card">
                    <div class="card-heading">
                        <h3>Send Us a Message</h3>
                        <p>Fill out the form below and our team will get back to you shortly.</p>
                    </div>

                    <form id="contact-form" method="post" action="{{ route('contact.submit') }}">
                        @csrf
                        <p class="p-3 alert" id="SubmitMessage" style="display:none;"></p>
                        @if($errors->any())
                            <div class="alert alert-danger mb-3">
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="cf-field">
                                    <label>Name <span class="req">*</span></label>
                                    <div class="cf-input-wrap">
                                        <i class="cf-icon icofont-user"></i>
                                        <input type="text" name="name" placeholder="Your full name" required value="{{ old('name') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="cf-field">
                                    <label>Email <span class="req">*</span></label>
                                    <div class="cf-input-wrap">
                                        <i class="cf-icon icofont-email"></i>
                                        <input type="email" name="email" placeholder="you@company.com" required value="{{ old('email') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="cf-field">
                                    <label>Subject <span class="req">*</span></label>
                                    <div class="cf-input-wrap">
                                        <i class="cf-icon icofont-tag"></i>
                                        <input type="text" name="subject" placeholder="How can we help?" required value="{{ request('subject') ?? old('subject') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="cf-field">
                                    <label>Organization</label>
                                    <div class="cf-input-wrap">
                                        <i class="cf-icon icofont-building-alt"></i>
                                        <input type="text" name="organization" placeholder="Your company or organization" value="{{ old('organization') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="cf-field">
                                    <label>Message <span class="req">*</span></label>
                                    <div class="cf-input-wrap is-textarea">
                                        <i class="cf-icon icofont-speech-comments"></i>
                                        <textarea name="message" placeholder="Tell us about your needs or questions..." required>{{ old('message') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Honeypot field (hidden from humans) -->
                        <input style="display: none;" type="text" name="website" class="honeypot">

                        <!-- reCAPTCHA -->
                        <div class="mt-3 mb-3">
                            @if(!empty($recaptchaSiteKey ?? config('services.recaptcha.site_key')))
                                <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey ?? config('services.recaptcha.site_key') }}"></div>
                            @else
                                <div class="alert alert-warning">reCAPTCHA is not configured. Please set <strong>CAPTURE_SITE_KEY</strong>.</div>
                            @endif
                        </div>

                        <button name="submit_form" type="submit" class="contact-submit-btn">
                            Send Message &nbsp;<i class="icofont-long-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
            <!-- ===== End Form Column ===== -->

            <!-- ===== Sidebar Column ===== -->
            <div class="col-lg-5 col-12">
                <div class="contact-sidebar">

                    <!-- Branded reach card -->
                    <div class="contact-reach-card">
                        <div class="reach-icon-wrap">
                            <i class="icofont-headphone-alt"></i>
                        </div>
                        <h4>We'd Love to Hear From You</h4>
                        <p>Whether you have a question about our services, pricing, or anything else — our team is ready to answer all your questions.</p>
                        <div class="response-badge">
                            <i class="icofont-clock-time"></i> Typical response within 24 hours
                        </div>
                    </div>

                    <!-- Contact info list -->
                    <div class="contact-info-list-card">
                        <div class="contact-info-item">
                            <div class="info-icon-wrap">
                                <i class="icofont-ui-call"></i>
                            </div>
                            <div class="info-item-text">
                                <h5>+(1) 972 460 0643</h5>
                                <p>info@armely.com</p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <div class="info-icon-wrap">
                                <i class="icofont-google-map"></i>
                            </div>
                            <div class="info-item-text">
                                <h5>Dallas, Texas</h5>
                                <p>17400 Dallas Pkwy, Suite 111<br>Dallas, TX 75287</p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <div class="info-icon-wrap">
                                <i class="icofont-google-map"></i>
                            </div>
                            <div class="info-item-text">
                                <h5>Nairobi, Kenya</h5>
                                <p>Highpoint</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- ===== End Sidebar Column ===== -->

        </div>
    </div>
</section>
<!-- End Contact Section -->

<!-- Google reCAPTCHA Script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<!-- Contact Form Handler - Using Vanilla JS to avoid conflicts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contact-form');
    
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Prevent other handlers from firing
        
        const submitBtn = document.querySelector('button[name="submit_form"]');
        const messageDiv = document.getElementById('SubmitMessage');

        // Clear previous messages
        messageDiv.textContent = '';
        messageDiv.className = 'p-3 alert';
        messageDiv.style.display = 'none';

        // Get reCAPTCHA token
        const recaptchaResponse = grecaptcha.getResponse();
        if (!recaptchaResponse) {
            messageDiv.className = 'p-3 alert alert-danger alert-dismissible fade show';
            messageDiv.innerHTML = '<strong>Error:</strong> Please verify that you are not a robot.' +
                                  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            messageDiv.style.display = 'block';
            return;
        }

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';

        // Prepare form data
        const formData = new FormData(form);
        formData.append('g-recaptcha-response', recaptchaResponse);

        // Submit via fetch
        fetch('{{ route("contact.submit") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response:', data);
            
            messageDiv.className = 'p-3 alert';
            if (data.success) {
                messageDiv.classList.add('alert-success');
                messageDiv.textContent = '✅ ' + data.message;
                
                // Google Analytics Event Tracking (GA4)
                if (typeof gtag === 'function') {
                    gtag('event', 'contact_form_submit', {
                        'event_category': 'engagement',
                        'event_label': 'contact_page_form',
                        'form_name': 'Contact Us Form',
                        'organization': formData.get('organization') || 'Not specified',
                        'subject': formData.get('subject') || 'Not specified'
                    });
                    
                    // Google Ads Conversion Tracking
                    gtag('event', 'conversion', {
                        'send_to': '{{ env("GOOGLE_ADS_ID") }}/contact_form_submit',
                        'event_callback': function() {
                            console.log('Contact form conversion tracked');
                            // Redirect after tracking is confirmed
                            if (data.redirect_url) {
                                setTimeout(function() {
                                    window.location.href = data.redirect_url;
                                }, 500);
                            }
                        }
                    });
                }
                
                // Reset form and reCAPTCHA
                form.reset();
                grecaptcha.reset();
                
                // Fallback redirect (in case gtag didn't fire the event_callback)
                setTimeout(function() {
                    if (window.location.pathname === '/contact' && data.redirect_url) {
                        window.location.href = data.redirect_url;
                    }
                }, 2000);
            } else {
                messageDiv.classList.add('alert-danger');
                messageDiv.textContent = '❌ ' + (data.message || 'An error occurred. Please try again.');
            }
            messageDiv.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            messageDiv.className = 'p-3 alert alert-danger';
            messageDiv.textContent = '❌ An error occurred. Please try again.';
            messageDiv.style.display = 'block';
        })
        .finally(() => {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.textContent = 'Send';
        });
        
        return false; // Prevent any default behavior
    }, true); // Use capture phase to ensure our handler runs first
});
</script>

@endsection

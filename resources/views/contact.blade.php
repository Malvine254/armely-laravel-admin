@extends('layouts.public')

@php($title = 'Contact')

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

<!-- Start Contact Us -->
<section class="contact-us section ">
    <div class="container col-12 col-lg-8 col-md-11 col-sm-12">
        <div class="inner">
            <div class="row">
                <div class="col-lg-12">
                    <div class="contact-us-form">
                        <h2>Contact With Us</h2>
                        <p>If you have any questions please feel free to contact with us.</p>
                        <!-- Form -->
                        <form class="form" id="contact-form" method="post" action="{{ route('contact.submit') }}">
                            @csrf
                            <p class="p-3 alert " id="SubmitMessage"></p>
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    @foreach($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="text-start">Name *</label>
                                    <div class="form-group">
                                        <input type="text" name="name" placeholder="Name" required value="{{ old('name') }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="text-start">Email *</label>
                                    <div class="form-group">
                                        <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="text-start">Phone Number *</label>
                                    <div class="form-group">
                                        <input type="text" name="phone" placeholder="Phone" required value="{{ old('phone') }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="text-start">Subject *</label>
                                    <div class="form-group">
                                        <input type="text" name="subject" placeholder="Subject" required value="{{ request('subject') ?? old('subject') }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <label class="text-start">Organization Name *</label>
                                    <div class="form-group">
                                        <input type="text" name="organization" placeholder="Organization" required value="{{ old('organization') }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <label class="text-start">Message *</label>
                                    <div class="form-group">
                                        <textarea name="message" placeholder="Your Message" required>{{ old('message') }}</textarea>
                                    </div>
                                </div>
                                <!-- Honeypot field (hidden from humans) -->
                                <input style="display: none;" type="text" name="website" class="honeypot">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <div class="g-recaptcha" data-sitekey="{{ env('CAPTURE_SITE_KEY') }}" data-callback="onRecaptchaSuccess"></div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group login-btn">
                                        <button name="submit_form" class="btn default-background" type="submit">Send</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!--/ End Form -->
                    </div>
                </div>
            </div>
        </div>
        <div class="contact-info">
            <div class="row">
                <!-- single-info -->
                <div class="col-lg-4 col-12 ">
                    <div class="single-info">
                        <i class="icofont icofont-ui-call"></i>
                        <div class="content">
                            <h3>+(1)  972 460 0643</h3>
                            <p>info@armely.com</p>
                        </div>
                    </div>
                </div>
                <!--/End single-info -->
                <!-- single-info -->
                <div class="col-lg-4 col-12 ">
                    <div class="single-info">
                        <i class="icofont-google-map"></i>
                        <div class="content">
                            <h3>17400 Dallas Pkwy</h3>
                            <p>Suite 111 Dallas, TX 75287</p>
                        </div>
                    </div>
                </div>
                <!--/End single-info -->
                <!-- single-info -->
                <div class="col-lg-4 col-12 ">
                    <div class="single-info">
                        <i class="icofont icofont-google-map"></i>
                        <div class="content">
                            <h3>Nairobi, Kenya</h3>
                            <p>Highpoint</p>
                        </div>
                    </div>
                </div>
                <!--/End single-info -->
            </div>
        </div>
    </div>
</section>
<!--/ End Contact Us -->

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
                
                // Reset form and reCAPTCHA
                form.reset();
                grecaptcha.reset();
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

@extends('layouts.public')

@php($title = 'Thank You')

@section('content')

<!-- Breadcrumbs -->
<div class="breadcrumbs-contact overlay">
    <div class="container">
        <div class="bread-inner">
            <div class="row">
                <div class="col-12">
                    <h2>Thank You</h2>
                    <ul class="bread-list">
                        <li><a href="/">Home</a></li>
                        <li><i class="icofont-simple-right"></i></li>
                        <li class="active">Thank You</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumbs -->

<!-- Start Thank You Section -->
<section class="contact-us section">
    <div class="container col-12 col-lg-8 col-md-11 col-sm-12">
        <div class="inner">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="contact-us-form">
                        <div style="padding: 60px 0;">
                            <div style="font-size: 72px; margin-bottom: 30px;">✅</div>
                            <h2 style="color: #28a745; margin-bottom: 20px;">Thank You!</h2>
                            <p style="font-size: 18px; margin-bottom: 15px;">Your message has been sent successfully.</p>
                            <p style="font-size: 16px; color: #666; margin-bottom: 40px;">We have received your inquiry and appreciate you reaching out to us. Our team will review your message and get back to you as soon as possible.</p>
                            
                            <div style="background-color: #f8f9fa; padding: 30px; border-radius: 8px; margin-bottom: 30px;">
                                <h4 style="margin-top: 0;">What happens next?</h4>
                                <ul style="text-align: left; display: inline-block; margin-bottom: 0;">
                                    <li style="margin-bottom: 15px;"><strong>Review:</strong> Our team will carefully review your message</li>
                                    <li style="margin-bottom: 15px;"><strong>Response:</strong> We'll reach out within 24-48 business hours</li>
                                    <li style="margin-bottom: 0;"><strong>Follow-up:</strong> We'll discuss your needs and find the best solution</li>
                                </ul>
                            </div>

                            <div>
                                <a href="/" class="btn default-background" style="margin-right: 10px;">Return to Home</a>
                                <a href="/contact" class="btn" style="background-color: #6c757d; color: white;">Send Another Message</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--/ End Thank You Section -->

@endsection

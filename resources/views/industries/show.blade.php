@extends('layouts.public')

@section('title', ($industryPage['label'] ?? 'Industry') . ' | Armely')
@section('meta_description', $industryPage['description'] ?? 'Explore Armely industry solutions.')
@section('canonical_url', url('/industries/' . $industrySlug))

@section('content')
    @include($industryView)

    @push('styles')
        <style>
            .industry-shared-contact {
                padding: 64px 24px;
                background: #f5f8fc;
            }

            .industry-shared-contact__inner {
                width: min(920px, 100%);
                margin: 0 auto;
            }

            .industry-shared-contact__heading {
                margin: 0 auto 28px;
                max-width: 760px;
                text-align: center;
            }

            .industry-shared-contact__heading .section-eyebrow {
                margin-bottom: 12px;
                color: #2f5597;
                font-size: 0.72rem;
                font-weight: 700;
                letter-spacing: 0.14em;
                text-transform: uppercase;
            }

            .industry-shared-contact__heading h2 {
                margin: 0 0 12px;
                color: #162b49;
                font-size: clamp(1.8rem, 3.5vw, 2.8rem);
                line-height: 1.15;
            }

            .industry-shared-contact__heading p {
                margin: 0;
                color: #526581;
                line-height: 1.7;
            }

            .industry-shared-contact form.service-contact-form {
                width: 100%;
                max-width: 860px;
                margin: 0 auto;
                text-align: left;
            }

            .industry-shared-contact form.service-contact-form label,
            .industry-shared-contact form.service-contact-form input,
            .industry-shared-contact form.service-contact-form select,
            .industry-shared-contact form.service-contact-form textarea {
                text-align: left;
            }
        </style>
    @endpush

        <section class="industry-shared-contact" id="contact">
            <div class="industry-shared-contact__inner">
                <div class="industry-shared-contact__heading">
                    <div class="section-eyebrow">Get Started</div>
                    <h2>Ready to discuss your {{ strtolower($industryPage['label'] ?? 'industry') }} technology needs?</h2>
                    <p>Book a free 30-minute assessment. We will review your current systems and come back with practical recommendations.</p>
                </div>

                @include('partials.service-contact-form', [
                    'serviceContact' => [
                        'title' => 'Book Your Free Assessment',
                        'subtitle' => 'Tell us about your organization and what you are trying to improve.',
                        'button_label' => 'Request Free Assessment',
                        'options' => [
                            'Need a discovery call',
                            'Need pricing and scope',
                            'Need a demo',
                            'Need help with data, AI, or automation',
                            'Not sure, need a recommendation',
                        ],
                    ],
                ])
            </div>
        </section>
@endsection

@extends('layouts.public')

@section('title', 'Events')
@section('meta_description', 'Explore Armely events, sessions, and announcements focused on Microsoft data, AI, and digital transformation.')
@section('meta_keywords', 'Armely events, Microsoft events, data transformation events, AI events, Power Platform events, webinars, workshops')
@section('canonical_url', route('events.index'))

@push('head')
<meta name="robots" content="index,follow">
<meta property="og:type" content="website">
<meta property="og:title" content="Events | Armely">
<meta property="og:description" content="Explore Armely events, sessions, and announcements focused on Microsoft data, AI, and digital transformation.">
<meta property="og:url" content="{{ route('events.index') }}">
<meta property="og:site_name" content="Armely">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Events | Armely">
<meta name="twitter:description" content="Explore Armely events, sessions, and announcements focused on Microsoft data, AI, and digital transformation.">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/events-modern.css') }}">
@endpush

@section('content')

<section class="modern-hero-events">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="events-badge">Armely Events</div>
                <h1 class="hero-title-events">Discover what's next in data, AI, and digital transformation</h1>
                <p class="hero-subtitle-events  text-light">Join live sessions, webinars, and workshops designed to help teams learn faster and make better decisions.</p>
                <ul class="bread-list">
                    <li>Live sessions</li>
                    <li>Webinars</li>
                    <li>Workshops</li>
                </ul>
            </div>
            <div class="col-lg-5">
                <div class="events-hero-panel">
                   
                    <h3>Quick access to every event card</h3>
                    <p>See upcoming sessions, recordings, and event details in one place. Use the button below to jump straight to the cards.</p>
                    <div class="events-hero-stats">
                        <div class="events-hero-stat">
                            <strong>{{ count($events) }}</strong>
                            <span>Live event cards</span>
                        </div>
                        <div class="events-hero-stat">
                            <strong>Fresh</strong>
                            <span>Updated when new events are posted</span>
                        </div>
                    </div>
                    <ul class="events-hero-points">
                        <li>Live sessions, webinars, and workshops</li>
                        <li>Recording links for past events</li>
                        <li>Easy jump to the card grid below</li>
                    </ul>
                    <a href="#events-grid" class="btn events-hero-btn">
                        <span class="default-color">View Event Cards</span>
                        <svg class="icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12h14" />
                            <path d="m13 6 6 6-6 6" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Start service -->
<section id="events-grid" class="services events-section-modern">
    <div class="container">
        @if(!empty($dbErrorMessage))
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-warning text-center" role="alert">
                        <i class="icofont-warning-alt"></i> {{ $dbErrorMessage }}
                    </div>
                </div>
            </div>
        @endif
        <div class="row g-4">
            @forelse($events as $event)
                <!-- Start Single Service -->
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="modern-event-card">
                        <div class="event-card-header default-background">
                            <div class="countdown-wrapper">
                                <div class="countdown-label">Event Countdown</div>
                                <div class="countdown-timer" id="countdown-{{ $event->event_timestamp }}">
                                    <span class="time-block"><span class="time-value">00</span><span class="time-label">Days</span></span>
                                    <span class="time-separator">:</span>
                                    <span class="time-block"><span class="time-value">00</span><span class="time-label">Hrs</span></span>
                                    <span class="time-separator">:</span>
                                    <span class="time-block"><span class="time-value">00</span><span class="time-label">Min</span></span>
                                    <span class="time-separator">:</span>
                                    <span class="time-block"><span class="time-value">00</span><span class="time-label">Sec</span></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="event-card-body">
                            <div class="event-date-badge">
                                <i class="icofont-calendar"></i>
                                <span>{{ $event->formatted_date }}</span>
                            </div>
                            
                            <h5 class="event-title">{{ $event->truncated_title }}</h5>
                            <p class="event-description">{{ $event->truncated_body }}</p>
                        </div>
                        
                        <div class="event-card-footer">
                            @if($event->button_disabled)
                                <span style="{{ $event->button_style }}" class="btn-event-action {{ $event->button_class }}">
                                    <span class="btn-icon">{!! $event->button_icon !!}</span>
                                    <span class="btn-text">{{ $event->button_text }}</span>
                                </span>
                            @else
                                <a href="{{ $event->button_href }}" 
                                   @if($event->button_class === 'btn-recording') target="_blank" @endif
                                   style="{{ $event->button_style }}" 
                                   class="btn-event-action {{ $event->button_class }}">
                                    <span class="btn-icon">{!! $event->button_icon !!}</span>
                                    <span class="btn-text">{{ $event->button_text }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- End Single Service -->
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="icofont-info-circle"></i> No events found at this time. Please check back later!
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
<!--/ End service -->
@endsection

@push('scripts')
<script>
// Countdown Timer for Events
document.addEventListener("DOMContentLoaded", function () {
    const updateCountdown = () => {
        const countdownElements = document.querySelectorAll(".countdown-timer");
        const now = new Date();

        countdownElements.forEach(el => {
            const timestamp = parseInt(el.id.split('-')[1]) * 1000; // Convert to milliseconds
            const eventDate = new Date(timestamp);
            const diffTime = eventDate - now;

            if (diffTime > 0) {
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                const diffHours = Math.floor((diffTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const diffMinutes = Math.floor((diffTime % (1000 * 60 * 60)) / (1000 * 60));
                const diffSeconds = Math.floor((diffTime % (1000 * 60)) / 1000);

                // Update individual time blocks
                const timeBlocks = el.querySelectorAll('.time-value');
                if (timeBlocks.length >= 4) {
                    timeBlocks[0].textContent = String(diffDays).padStart(2, '0');
                    timeBlocks[1].textContent = String(diffHours).padStart(2, '0');
                    timeBlocks[2].textContent = String(diffMinutes).padStart(2, '0');
                    timeBlocks[3].textContent = String(diffSeconds).padStart(2, '0');
                }
            } else {
                // Event has passed
                const timeBlocks = el.querySelectorAll('.time-value');
                timeBlocks.forEach(block => {
                    block.textContent = '00';
                });
            }
        });
    };

    updateCountdown(); // Initial call
    setInterval(updateCountdown, 1000); // Update every second
});
</script>
@endpush


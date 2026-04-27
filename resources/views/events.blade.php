@extends('layouts.public')

@section('title', 'Events - Armely')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/events-modern.css') }}">
@endpush

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs overlay">
    <div class="container">
        <div class="bread-inner">
            <div class="row">
                <div class="col-12">
                    <h2>Events</h2>
                    <ul class="bread-list">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><i class="icofont-simple-right"></i></li>
                        <li class="active">Events</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
 
<!-- Start service -->
<section class="services events-section-modern">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title modern-section-title">
                    <h2 class="section-heading-modern">Discover Our Events</h2>
                    <div class="title-divider"></div>
                    <p class="section-description-modern">Stay updated with our latest events, webinars, and workshops designed to empower your business</p>
                </div>
            </div>
        </div>
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
                @php
                    // Parse the start date (d/m/Y format)
                    $eventDate = \DateTime::createFromFormat('d/m/Y', trim($event->start_date));
                    if ($eventDate === false) {
                        continue; // Skip invalid dates
                    }
                    
                    $eventTimestamp = $eventDate->getTimestamp();
                    $currentTimestamp = time();
                    
                    // Format date with suffix
                    $day = $eventDate->format('j');
                    $month = $eventDate->format('M');
                    $year = $eventDate->format('Y');
                    
                    if (!in_array(($day % 100), [11, 12, 13])) {
                        switch ($day % 10) {
                            case 1: $suffix = 'st'; break;
                            case 2: $suffix = 'nd'; break;
                            case 3: $suffix = 'rd'; break;
                            default: $suffix = 'th'; break;
                        }
                    } else {
                        $suffix = 'th';
                    }
                    
                    $formattedDate = $month . ' ' . $day . $suffix . ' ' . $year;
                    
                    // Prepare truncated fields
                    $truncatedTitle = \Illuminate\Support\Str::limit($event->title ?? '', 60);
                    $truncatedBody  = \Illuminate\Support\Str::limit(strip_tags($event->body ?? ''), 180);
                    
                    // Determine button state
                    if ($eventTimestamp > $currentTimestamp) {
                        $buttonText = "Register";
                        $buttonHref = $event->url;
                        $buttonClass = "btn-register";
                        $buttonIcon = '<i class="icofont-ui-calendar"></i>';
                        $buttonStyle = '';
                        $buttonDisabled = false;
                    } elseif (empty($event->recorded_url)) {
                        $buttonText = "No Recording Link";
                        $buttonHref = '#';
                        $buttonClass = "btn-no-recording";
                        $buttonIcon = '<i class="icofont-close-circled"></i>';
                        $buttonStyle = 'background: red !important;';
                        $buttonDisabled = true;
                    } else {
                        $buttonText = "View Recording";
                        $buttonHref = $event->recorded_url;
                        $buttonClass = "btn-recording";
                        $buttonIcon = '<i class="icofont-play-alt-2"></i>';
                        $buttonStyle = 'background: orange !important;';
                        $buttonDisabled = false;
                    }
                @endphp
                
                <!-- Start Single Service -->
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="modern-event-card">
                        <div class="event-card-header default-background">
                            <div class="countdown-wrapper">
                                <div class="countdown-label">Event Countdown</div>
                                <div class="countdown-timer" id="countdown-{{ $eventTimestamp }}">
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
                                <span>{{ $formattedDate }}</span>
                            </div>
                            
                            <h5 class="event-title">{{ $truncatedTitle }}</h5>
                            <p class="event-description">{{ $truncatedBody }}</p>
                        </div>
                        
                        <div class="event-card-footer">
                            @if($buttonDisabled)
                                <span style="{{ $buttonStyle }}" class="btn-event-action {{ $buttonClass }}">
                                    <span class="btn-icon">{!! $buttonIcon !!}</span>
                                    <span class="btn-text">{{ $buttonText }}</span>
                                </span>
                            @else
                                <a href="{{ $buttonHref }}" 
                                   @if($buttonClass === 'btn-recording') target="_blank" @endif
                                   style="{{ $buttonStyle }}" 
                                   class="btn-event-action {{ $buttonClass }}">
                                    <span class="btn-icon">{!! $buttonIcon !!}</span>
                                    <span class="btn-text">{{ $buttonText }}</span>
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

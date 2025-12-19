@extends('layouts.public')

@php($title = 'Home')

@section('content')
<section class="slider">
    <div class="hero-slider">
        <div class="single-slider" style="background-image:url('{{ asset('images/sliders/slider-1.webp') }}')" role="img" aria-label="Digital Excellence Banner">
            <div class="container"><div class="row"><div class="col-lg-7"><div class="text">
                <h1><span class="text-light">Your Trusted Source For Digital Excellence</span></h1>
                <p class="text-light">Beyond Imagination</p>
                <div class="button">
                    <a href="/services#consultation-form" class="btn">Get Appointment</a>
                    <a href="/company" class="btn primary">Learn More</a>
                </div>
            </div></div></div></div>
        </div>
        <div class="single-slider" style="background-image:url('{{ asset('images/sliders/slider-2.webp') }}')">
            <div class="container"><div class="row"><div class="col-lg-7"><div class="text">
                <h1><span class="text-light"> We Provide AI Services That You Can Trust!</span></h1>
                <p class="text-light">Beyond Imagination</p>
                <div class="button">
                    <a href="/services#consultation-form" class="btn">Get Appointment</a>
                    <a href="/company" class="btn primary">About Us</a>
                </div>
            </div></div></div></div>
        </div>
        <div class="single-slider" style="background-image:url('{{ asset('images/sliders/slider-3.webp') }}')">
            <div class="container"><div class="row"><div class="col-lg-7"><div class="text">
                <h1><span class="text-light">We Provide Data Services That You Can Trust!</span></h1>
                <p class="text-light">Beyond Imagination</p>
                <div class="button">
                    <a href="/services#consultation-form" class="btn">Get Appointment</a>
                    <a href="/contact" class="btn primary">Contact Us</a>
                </div>
            </div></div></div></div>
        </div>
    </div>
</section>

<main id="main-content" role="main">
<section class="schedule">
    <div class="container"><div class="schedule-inner"><div class="row">
        @foreach($offers as $offer)
            <div class="col-lg-4 col-md-6 col-12">
                <div class="single-schedule first" style="block-size: auto; min-block-size: 400px;">
                    <div class="inner">
                        <div class="icon"><i class="fa fa-data"></i></div>
                        <div class="single-content">
                            <img src="{{ $offer->image_path }}" class="img-fluid lazy-img" loading="lazy" decoding="async" alt="Offer Image">
                            <div><h4>{{ $offer->title }}</h4></div>
                            <span style="color: white !important;" class="shorten-content text-light"><b class="text-light">{{ $offer->body }}</b></span>
                            <a href="#" class="read-more-btn text-light">READ MORE <i class="fa fa-long-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div></div></div>
</section>

<section id="clients" class="wow fadeInUp blog">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><b>Brands that Trust Us</b><center><hr class="default-background hr"></center></h2>
            <p class="text-center">We’re proud to work alongside some truly trusted brands. Together, we’re focused on delivering real value, innovation, and quality. A huge thank you to our partners for their ongoing support and commitment to excellence.</p>
        </div>
        <div class="owl-carousel clients-carousel partner-carousel" style="content-visibility:auto; contain-intrinsic-size: 1px 320px;">
            <div class="m-4"><a href="{{ asset('images/brand-partners/university_of_nebrask1.png') }}" target="_blank" rel="noopener noreferrer"><img src="https://cdn.freelogovectors.net/wp-content/uploads/2023/07/university_of_nebraska_medical_center_logo-freelogovectors.net_.png" class="img-fluid lazy-img" loading="lazy" decoding="async" alt="University of Nebraska Medical Center logo" width="200" height="80"></a></div>
            <div class="m-4"><a href="{{ asset('images/brand-partners/swope_health.png') }}" target="_blank" rel="noopener noreferrer"><img src="https://swopehealth.org/wp-content/uploads/2024/03/Swope-Health-Services-244462845.png" alt="Swope Health Services logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a></div>
            <div class="m-4"><a href="{{ asset('images/brand-partners/esse_health.jpg') }}" target="_blank" rel="noopener noreferrer"><img src="https://assets.libsyn.com/content/17315963?height=250&width=250&overlay=true" alt="Esse Health logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a></div>
            <div class="m-4"><a href="{{ asset('images/brand-partners/sage_bute.webp') }}" target="_blank" rel="noopener noreferrer"><img src="https://www.sagebutte.com/wp-content/uploads/2024/01/Sage-Butte-Energy-logo.png" alt="Sage Butte Energy logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a></div>
            <div class="m-4"><a href="{{ asset('images/brand-partners/qb_energy.jpg') }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset('images/brand-partners/qb_energy.jpg') }}" alt="QB Energy logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a></div>
            <div class="m-4"><a href="{{ asset('images/brand-partners/frisco.jpeg') }}" target="_blank" rel="noopener noreferrer"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/78/Logo_of_Frisco%2C_Texas.svg/768px-Logo_of_Frisco%2C_Texas.svg.png?20180828011722" alt="City of Frisco Texas logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a></div>
            <div class="m-4"><a href="{{ asset('images/brand-partners/dallas_county.jpg') }}" target="_blank" rel="noopener noreferrer"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSrSzxH_f1lEuzqxEewHFlxbQr6jWb-ISY4eQ&s" alt="Dallas County logo" class="lazy-img" loading="lazy" decoding="async" width="200" height="80"></a></div>
            <div class="m-4"><a href="{{ asset('images/brand-partners/lambda.png') }}" target="_blank" rel="noopener noreferrer"><img src="https://lambdalegal.org/wp-content/uploads/2023/02/lambda-logo-300x84.png" alt="Lambda Legal logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a></div>
            <div class="m-4"><a href="{{ asset('images/brand-partners/homeward_bound.png') }}" target="_blank" rel="noopener noreferrer"><img src="https://nfg-sofun.s3.amazonaws.com/uploads/ui_configuration/main_logo/12617/welcome_logo_HBI_LOGO_FINAL_Long_Form.png" alt="Homeward Bound logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a></div>
            <div class="m-4"><a href="{{ asset('images/brand-partners/mhc.png') }}" target="_blank" rel="noopener noreferrer"><img src="https://www.moblicosolutions.com/wp-content/uploads/2024/09/Image-for-Home-Page-Hero-4-e1726688399270.png" alt="MHC logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a></div>
            <div class="m-4"><a href="{{ asset('images/brand-partners/bnsf.png') }}" target="_blank" rel="noopener noreferrer"><img src="https://logos-world.net/wp-content/uploads/2024/07/BNSF-Railway-Symbol.png" alt="BNSF Railway logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a></div>
        </div>
    </div>
</section>

<section class="portfolio">
    <div class="container"><div class="row"><div class="col-lg-12"><div class="section-title mt-5"><h2>Case Studies</h2><center><hr class="default-background hr"></center></div></div></div></div>
    <div class="container-fluid" style="content-visibility:auto; contain-intrinsic-size: 1px 800px;">
        <div class="row"><div class="col-lg-12 col-12"><div class="owl-carousel portfolio-slider">
            @foreach($industryListings as $listing)
                <div class="single-pf p-2" style="min-block-size: 360px;">
                    <div class="p-4 card-shadow shadow">
                        <img src="{{ $listing->image_path }}" alt="Industry Image" class="img-fluid lazy-img" loading="lazy" decoding="async">
                        <a href="{{ $listing->pdf_link }}" id="listing-{{ $listing->id }}" class="btn" target="_blank">View Details</a>
                        <h6 class="mt-2"><strong class="default-color">Industry: {{ $listing->category }}</strong></h6>
                        <p>{{ $listing->excerpt }}</p>
                    </div>
                </div>
            @endforeach
        </div></div></div>
        <div class="default-color h4 p-2"><a class="animated-text" href="/case-studies"><strong>Explore all Case Studies >>></strong></a></div>
    </div>
</section>

<section id="fun-facts" class="fun-facts default-background section col-md-12 mt-5" aria-labelledby="stats-heading">
    <div class="container">
        <h2 id="stats-heading" class="sr-only">Customer Statistics</h2>
        <div class="row" style="content-visibility:auto; contain-intrinsic-size: 1px 1200px;">
            <div class="col-lg-4 col-md-6 col-12"><div class="single-fun"><i class="icofont icofont-ui-user-group"></i><div class="content"><span class="counter plus">72</span><p>Customer Retention Rate</p></div></div></div>
            <div class="col-lg-4 col-md-6 col-12"><div class="single-fun"><i class="icofont icofont-users-social"></i><div class="content"><span class="counter percent">82</span><p>Customer Satisfaction</p></div></div></div>
            <div class="col-lg-4 col-md-6 col-12"><div class="single-fun"><i class="icofont-simple-smile"></i><div class="content"><span>Very Easy</span><p>Customer Effort Score</p></div></div></div>
            <div class="col-lg-4 col-md-6 col-12"></div>
        </div>
    </div>
</section>

<section class="blog section" id="blog">
    <div class="container">
        <div class="row"><div class="col-lg-12"><div class="section-title"><h2>Our Most Recent Blog Articles</h2><center><hr class="default-background hr"></center></div></div></div>
        <div class="row">
            @forelse($blogs as $blog)
                <div class="col-lg-4 col-md-6 col-12" data-aos="fade-in">
                    <div class="single-news" style="block-size: 430px; max-block-size: 430px;">
                        <div class="news-head"><img class="lazy-img" loading="lazy" decoding="async" style="block-size: 200px; max-block-size: 200px;" src="{{ $blog->image_path }}" alt="#"></div>
                        <div class="news-body"><div class="news-content">
                            <div class="date">{{ $blog->date }}.</div>
                            <h6 class="text-muted">{{ $blog->author }} |<span>{{ $blog->reading_time }} min read</span></h6>
                            <h2><a href="{{ route('blog.index', ['blogId' => $blog->blog_id]) }}">{{ $blog->title }}</a></h2>
                            <a class="default-color" href="{{ route('blog.index', ['blogId' => $blog->blog_id]) }}">READ MORE<i class="fa fa-long-arrow-right"></i></a>
                        </div></div>
                    </div>
                </div>
            @empty
                <p>No blog articles found.</p>
            @endforelse
        </div>
        <div class="default-color h4 p-2 mt-4"><a class="animated-text" href="/blog"><strong>Explore all Blogs >>></strong></a></div>
    </div>
</section>

<section class="">
    <div class="container">
        <div class="row"><div class="col-lg-12"><div class="section-title"><h2>See Our Most Recent Videos</h2><center><hr class="default-background hr"></center></div></div></div>
        <div class="row">
            @forelse($videos as $video)
                <div class="col-lg-4 col-md-12 col-12">
                    <div class="lazy-video" data-src="https://www.youtube.com/embed/{{ $video->video_id }}?autoplay=1">
                        <div class="play-overlay">
                            <img src="https://img.youtube.com/vi/{{ $video->video_id }}/hqdefault.jpg" class="lazy-thumb" alt="Video Thumbnail">
                            <div class="play-button"></div>
                        </div>
                    </div>
                </div>
            @empty
                <p>No video was found!</p>
            @endforelse
        </div>
        <div class="default-color h4 p-2"><a class="animated-text" href="https://www.youtube.com/@armelyarmely"><strong>Explore all Videos >>></strong></a></div>
    </div>
</section>

<section class="appointment">
    <div class="container">
        <div class="row"><div class="col-lg-12"><div class="section-title"><h2>Contact Us</h2><center><hr class="default-background hr"></center></div></div></div>
        <div class="row">
            <div class="col-lg-12 col-md-6 col-12 d-flex default-background mb-5">
                <form class="form p-5 w-100" id="contact-form" method="post" action="{{ route('contact.submit') }}">
                    @csrf
                    <p class="p-3 alert" id="SubmitMessage"></p>
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="text-start text-light">Name *</label>
                            <div class="form-group input-with-background"><input required class="remove-input-background" name="name" type="text" placeholder="Name" value="{{ old('name') }}"></div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="text-start text-light">Email *</label>
                            <div class="form-group"><input required class="remove-input-background" name="email" type="email" placeholder="Email" value="{{ old('email') }}"></div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="text-start text-light">Phone Number *</label>
                            <div class="form-group"><input required class="remove-input-background" name="phone" type="text" placeholder="Phone" value="{{ old('phone') }}"></div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="text-start text-light">Subject *</label>
                            <div class="form-group"><input required class="remove-input-background" name="subject" type="text" placeholder="Subject" value="{{ request('subject') ?? old('subject') }}"></div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-12">
                            <label class="text-start text-light">Organization Name *</label>
                            <div class="form-group"><input required class="remove-input-background" name="organization" type="text" placeholder="Organization Name" value="{{ old('organization') }}"></div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-12">
                            <label class="text-start text-light">Message *</label>
                            <div class="form-group"><textarea required class="remove-input-background" name="message" placeholder="Write Your Message Here.....">{{ old('message') }}</textarea></div>
                        </div>
                        <input style="display: none;" type="text" name="website" class="honeypot">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="text-start text-light">Confirm you are not a robot *</label>
                                <div class="g-recaptcha" data-sitekey="{{ env('CAPTURE_SITE_KEY') }}"></div>
                            </div>
                        </div>
                        <div class="form-group ml-3">
                            <div class="button"><button type="submit" class="btn send-message-btn" name="submit_form">Send Message</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Lazy-load Google reCAPTCHA when contact form enters viewport -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const recaptchaEl = document.querySelector('.g-recaptcha');
    if (!recaptchaEl) return;
    let scriptLoaded = false;
    const loadRecaptcha = () => {
        if (scriptLoaded) return; scriptLoaded = true;
        const s = document.createElement('script');
        s.src = 'https://www.google.com/recaptcha/api.js';
        s.async = true; s.defer = true; document.body.appendChild(s);
    };
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) { loadRecaptcha(); io.disconnect(); } });
        }, { rootMargin: '200px 0px' });
        io.observe(recaptchaEl);
    } else { loadRecaptcha(); }
});
</script>

<!-- Contact Form Handler - Using Vanilla JS to avoid conflicts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contact-form');
    
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const submitBtn = document.querySelector('button[name="submit_form"]');
        const messageDiv = document.getElementById('SubmitMessage');
        const originalBtnText = submitBtn.textContent;

        messageDiv.textContent = '';
        messageDiv.className = 'p-3 alert';
        messageDiv.style.display = 'none';

        const recaptchaResponse = grecaptcha.getResponse();
        if (!recaptchaResponse) {
            messageDiv.className = 'p-3 alert alert-danger alert-dismissible fade show';
            messageDiv.innerHTML = '<strong>Error:</strong> Please verify that you are not a robot.' +
                                  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            messageDiv.style.display = 'block';
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';

        const formData = new FormData(form);
        formData.append('g-recaptcha-response', recaptchaResponse);

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
            messageDiv.className = 'p-3 alert';
            if (data.success) {
                messageDiv.classList.add('alert-success');
                messageDiv.textContent = '✅ ' + data.message;
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
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
        });
        
        return false;
    }, true);
});
</script>
</main>
@endsection

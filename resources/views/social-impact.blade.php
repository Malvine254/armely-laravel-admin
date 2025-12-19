@extends('layouts.public')

@section('title', 'Social Impact - Armely')

@push('styles')
<link rel="stylesheet" href="{{ asset('more_style.css') }}">
<style>
:root {
    --default-color: #2f5597;
    --default-background: #2f5597;
}

.blog-carousel .item img {
  border-radius: 12px;
  object-fit: cover;
  height: 250px;
  width: 100%;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.blog-carousel .item:hover img {
  transform: scale(1.05);
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}
.blog-card img {
  object-fit: cover;
  height: 200px;
  border-radius: 10px;
}
.blog-card {
  border: none;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  transition: transform 0.3s ease;
}
.blog-card:hover {
  transform: translateY(-5px);
}

.section-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 2rem;
  position: relative;
}
.section-title::after {
  content: "";
  width: 60px;
  height: 4px;
  background: #0d6efd;
  display: block;
  margin: 10px auto 0;
  border-radius: 2px;
}
.owl-carousel .owl-nav {
  position: relative;
  margin-top: 15px;
  text-align: right;
}

.owl-carousel .owl-nav button.owl-prev,
.owl-carousel .owl-nav button.owl-next {
  background: #2f5597;
  color: #fff !important;
  border: none;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  font-size: 18px !important;
  margin-left: 8px;
  transition: all 0.3s ease;
}

.owl-carousel .owl-nav button.owl-prev:hover,
.owl-carousel .owl-nav button.owl-next:hover {
  background: #0b5ed7;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.blog-post {
  border-radius: 12px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  overflow: hidden;
}

.blog-post:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.blog-image {
  object-fit: cover;
  height: 100%;
  width: 100%;
}

.blog-title {
  color: #212529;
}

.blog-title:hover {
  color: #084196ff;
  text-decoration: underline;
}
.blog-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 8px;
}

.blog-card {
  border-radius: 12px;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.blog-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
.blog-card-image {
  height: 150px;
  object-fit: cover;
}
.card-title {
  font-size: 1rem;
  line-height: 1.3;
  margin-bottom: 0.5rem;
}
.card-text {
  font-size: 0.85rem;
  line-height: 1.4;
}

/* Hero Section */
.social-impact-hero {
    padding: 80px 0;
    background: linear-gradient(135deg, rgba(0,0,0,0.02) 0%, rgba(0,0,0,0) 100%);
    position: relative;
    overflow: hidden;
}

.social-impact-hero::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 50%;
    height: 100%;
    background: var(--default-background);
    opacity: 0.05;
    clip-path: polygon(30% 0, 100% 0, 100% 100%, 0% 100%);
}

.impact-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--default-background);
    color: #fff;
    padding: 8px 20px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 20px;
    animation: fadeInUp 0.6s ease;
}

.impact-badge i {
    font-size: 18px;
}

.hero-title {
    font-size: 3rem;
    font-weight: 800;
    color: #222;
    margin-bottom: 20px;
    line-height: 1.2;
    animation: fadeInUp 0.8s ease;
}

.hero-subtitle {
    font-size: 1.2rem;
    color: #666;
    line-height: 1.8;
    margin-bottom: 15px;
    animation: fadeInUp 1s ease;
}

.hero-image-wrapper {
    position: relative;
    animation: fadeInRight 1s ease;
}

.hero-impact-image {
    border-radius: 30px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    transform: rotate(2deg);
    transition: all 0.5s ease;
}

.hero-impact-image:hover {
    transform: rotate(0deg) scale(1.05);
    box-shadow: 0 30px 80px rgba(0,0,0,0.2);
}

.impact-stats {
    display: flex;
    gap: 30px;
    margin-top: 40px;
    flex-wrap: wrap;
}

.stat-item {
    flex: 1;
    min-width: 150px;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--default-color);
    display: block;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Gallery Section */
.gallery-section {
    padding: 80px 0;
    background: linear-gradient(to bottom, #f8f9fa 0%, #fff 100%);
    position: relative;
}

.section-header-modern {
    text-align: center;
    margin-bottom: 60px;
}

.section-tag {
    color: var(--default-color);
    font-weight: 600;
    text-transform: uppercase;
    font-size: 14px;
    letter-spacing: 2px;
    margin-bottom: 15px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.section-tag i {
    font-size: 18px;
}

.section-title-modern {
    font-size: 2.5rem;
    font-weight: 700;
    color: #222;
    margin-bottom: 15px;
}

.section-divider {
    width: 80px;
    height: 4px;
    background: var(--default-background);
    margin: 0 auto 20px;
    border-radius: 2px;
}

/* Photo Album Grid */
.photo-album-grid {
    margin-top: 50px;
}

.gallery-folder {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.gallery-folder:hover {
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.folder-header {
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
    display: flex;
    align-items: center;
    gap: 20px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.folder-header:hover {
    background: linear-gradient(135deg, #f0f1f3 0%, #f8f9fa 100%);
}

.folder-icon {
    position: relative;
    font-size: 40px;
    color: var(--default-color);
}

.image-count {
    position: absolute;
    bottom: -5px;
    right: -5px;
    background: var(--default-background);
    color: white;
    font-size: 12px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 12px;
}

.folder-info {
    flex: 1;
}

.folder-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #222;
    margin: 0 0 8px 0;
}

.folder-meta {
    display: flex;
    gap: 15px;
    font-size: 0.9rem;
    color: #666;
}

.folder-date {
    display: flex;
    align-items: center;
    gap: 5px;
}

.folder-badge {
    background: var(--default-color);
    color: white;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.folder-toggle {
    background: var(--default-color);
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.folder-toggle:hover {
    background: #1a3a5c;
    transform: scale(1.1);
}

.folder-content {
    padding: 30px;
    background: #f8f9fa;
}

.folder-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 20px;
}

.folder-image-item {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    display: block;
    height: 150px;
    background: linear-gradient(135deg, #2f5597 0%, #5a7fb8 100%);
}

.folder-image-item.no-image::after {
    content: '\e987';
    font-family: 'IcoFont';
    font-size: 40px;
    color: rgba(255,255,255,0.5);
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 2;
}

.folder-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.folder-image-item:hover .folder-img {
    transform: scale(1.1);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
    color: white;
}

.folder-image-item:hover .image-overlay {
    opacity: 1;
}

.image-number {
    font-weight: 600;
    font-size: 14px;
}

.image-overlay i {
    font-size: 24px;
}

/* Blog Section */
.featured-posts-section {
    padding: 80px 0;
    background: #fff;
}

.posts-grid {
    padding: 40px 0;
}

.modern-blog-post {
    display: block;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
    margin-bottom: 30px;
}

.modern-blog-post:hover {
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    transform: translateY(-8px);
}

.blog-image-container {
    position: relative;
    height: 250px;
    overflow: hidden;
    background: linear-gradient(135deg, #2f5597 0%, #5a7fb8 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.blog-image-container.no-image::after {
    content: '\e987';
    font-family: 'IcoFont';
    font-size: 60px;
    color: rgba(255,255,255,0.5);
    position: relative;
    z-index: 2;
}

.blog-post-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.modern-blog-post:hover .blog-post-image {
    transform: scale(1.05);
}

.blog-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.3);
    transition: background 0.3s ease;
}

.modern-blog-post:hover .blog-image-overlay {
    background: rgba(0, 0, 0, 0.5);
}

.blog-category-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: var(--default-background);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    z-index: 10;
}

.blog-content-wrapper {
    padding: 25px;
}

.blog-date {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.blog-post-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #222;
    margin: 10px 0;
    line-height: 1.4;
    transition: color 0.3s ease;
}

.modern-blog-post:hover .blog-post-title {
    color: var(--default-color);
}

.blog-excerpt {
    font-size: 0.95rem;
    color: #666;
    line-height: 1.6;
    margin: 15px 0;
}

.read-article-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--default-color);
    font-weight: 600;
    font-size: 0.95rem;
    transition: gap 0.3s ease;
}

.modern-blog-post:hover .read-article-btn {
    gap: 12px;
}

.modern-sidebar {
    background: linear-gradient(135deg, var(--default-background) 0%, #1a3a5c 100%);
    color: white;
    padding: 40px;
    border-radius: 12px;
    text-align: center;
    position: sticky;
    top: 20px;
}

.sidebar-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 20px;
}

.sidebar-heading {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 15px;
}

.sidebar-text {
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 20px;
    opacity: 0.95;
}

.sidebar-cta-btn {
    display: inline-block;
    background: #fff;
    color: var(--default-background);
    padding: 12px 30px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
}

.sidebar-cta-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }

    .section-title-modern {
        font-size: 1.8rem;
    }

    .folder-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .folder-images-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }

    .modern-sidebar {
        position: static;
        margin-top: 40px;
    }
}
</style>
@endpush

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs overlay">
    <div class="container">
        <div class="bread-inner">
            <div class="row">
                <div class="col-12">
                    <h2>Social Impact</h2>
                    <ul class="bread-list">
                        <li><a href="/">Home</a></li>
                        <li><i class="icofont-simple-right"></i></li>
                        <li class="active">Social Impact</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumbs -->

<!-- Hero Section -->
<section class="social-impact-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 col-md-12">
                <div class="impact-badge">
                    <i class="icofont-heart-alt"></i>
                    <span>Making a Difference</span>
                </div>
                <h1 class="hero-title">Driving Positive Change Through Innovation</h1>
                <p class="hero-subtitle">
                    Armely is committed to making a lasting and meaningful impact on the environment, the communities where we work and live, and the ethical standards that guide our business.
                </p>
                <p class="hero-subtitle">
                    Our dedication to environmental, social, and governance (ESG) principles reflects our responsibility to drive positive change while fostering innovation, sustainability, and inclusivity.
                </p>
                
                <div class="impact-stats">
                    <div class="stat-item">
                        <span class="stat-number">50+</span>
                        <span class="stat-label">Communities Impacted</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">100K+</span>
                        <span class="stat-label">Lives Touched</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">25+</span>
                        <span class="stat-label">ESG Initiatives</span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-5 col-md-12">
                <div class="hero-image-wrapper text-center mt-4 mt-lg-0">
                    <img src="{{ asset('images/gallery/gallery-10.jpeg') }}" class="img-fluid hero-impact-image" alt="Social Impact">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="gallery-section">
    <div class="container">
        <div class="section-header-modern">
            <div class="section-tag"><i class="icofont-camera"></i> Our Journey</div>
            <h2 class="section-title-modern">Impact Photo Album</h2>
            <div class="section-divider"></div>
            <p style="color: #666; max-width: 700px; margin: 0 auto; font-size: 1.1rem;">
                Capturing moments of transformation, hope, and positive change across communities.
            </p>
        </div>

        <div class="photo-album-grid">
            @forelse($gallery as $item)
                <div class="gallery-folder">
                    <div class="folder-header">
                        <div class="folder-icon">
                            <i class="icofont-folder"></i>
                            <span class="image-count">{{ count(array_filter(array_map('trim', explode(',', $item->image_url)))) }}</span>
                        </div>
                        <div class="folder-info">
                            <h3 class="folder-title">{{ $item->title }}</h3>
                            <div class="folder-meta">
                                <span class="folder-date"><i class="icofont-calendar"></i> {{ $item->posted_date }}</span>
                                <span class="folder-badge">{{ strtoupper($item->category) }}</span>
                            </div>
                        </div>
                        <button class="folder-toggle" onclick="toggleFolder(this)">
                            <i class="icofont-rounded-down"></i>
                        </button>
                    </div>
                    <div class="folder-content" style="display: none;">
                        <div class="folder-images-grid">
                            @php
                                $images = array_filter(array_map('trim', explode(',', $item->image_url)));
                                $photoCounter = 1;
                            @endphp
                            @foreach($images as $image)
                                @if(!empty($image))
                                    <a class="folder-image-item" href="{{ asset('images/social-impact/' . $image) }}" target="_blank">
                                        <img src="{{ asset('images/social-impact/' . $image) }}" alt="{{ $item->title }} - Photo {{ $photoCounter }}" class="folder-img" onerror="this.closest('.folder-image-item').classList.add('no-image'); this.style.display='none';">
                                        <div class="image-overlay">
                                            <span class="image-number">#{{ $photoCounter }}</span>
                                            <i class="icofont-eye"></i>
                                        </div>
                                    </a>
                                    @php $photoCounter++; @endphp
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info text-center">
                    <i class="icofont-info-circle"></i> No gallery items found.
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Blog Section -->
<section class="featured-posts-section">
    <div class="container">
        <div class="section-header-modern">
            <div class="section-tag">Latest Stories</div>
            <h2 class="section-title-modern">Featured Impact Stories</h2>
            <div class="section-divider"></div>
        </div>

        <div class="row">
            <!-- Blog Posts (Left Column) -->
            <div class="col-lg-8">
                @forelse($socialImpact as $impact)
                    <a href="{{ route('social-impact-details', $impact->secure_id) }}" class="modern-blog-post">
                        <div class="blog-image-container @if(!$impact->image_url || trim($impact->image_url) === '') no-image @endif">
                            @php
                                $images = explode(',', $impact->image_url);
                                $firstImage = trim($images[0]);
                            @endphp
                            @if(!empty($firstImage))
                                <img src="{{ asset('images/social-impact/' . $firstImage) }}" class="blog-post-image" alt="{{ $impact->title }}" onerror="this.parentElement.classList.add('no-image'); this.style.display='none';">
                            @endif
                            <div class="blog-image-overlay"></div>
                            <span class="blog-category-badge">{{ strtoupper($impact->category) }}</span>
                        </div>
                        <div class="blog-content-wrapper">
                            <div class="blog-date">
                                <i class="icofont-calendar"></i>
                                {{ $impact->posted_date }}
                            </div>
                            <h3 class="blog-post-title">{{ $impact->title }}</h3>
                            <p class="blog-excerpt">{{ Str::limit(strip_tags($impact->body), 150) }}...</p>
                            <span class="read-article-btn">
                                Read Full Story
                                <i class="icofont-long-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="alert alert-info">
                        <i class="icofont-info-circle"></i> No social impact stories found.
                    </div>
                @endforelse
            </div>

            <!-- Sidebar (Right Column) -->
            <div class="col-lg-4">
                <div class="modern-sidebar">
                    <img src="{{ asset('images/careers/software.png') }}" class="sidebar-image" alt="Get Insights">
                    <h4 class="sidebar-heading text-light">Got 5 minutes?</h4>
                    <p class="sidebar-text  text-light">
                        Get actionable insights on AI, cloud, cybersecurity, sustainability, and social impact delivered straight to your inbox.
                    </p>
                    <a href="{{ route('contact') }}" class="sidebar-cta-btn default-color">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function toggleFolder(button) {
    const folder = button.closest('.gallery-folder');
    const content = folder.querySelector('.folder-content');
    const icon = button.querySelector('i');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.classList.remove('icofont-rounded-down');
        icon.classList.add('icofont-rounded-up');
    } else {
        content.style.display = 'none';
        icon.classList.add('icofont-rounded-down');
        icon.classList.remove('icofont-rounded-up');
    }
}
</script>

@endsection

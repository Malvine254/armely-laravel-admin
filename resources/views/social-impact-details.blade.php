@extends('layouts.public')

@section('title', $initiative->title . ' - Social Impact - Armely')

@push('styles')
<style>
:root {
    --default-color: #2f5597;
    --default-background: #2f5597;
}

/* Article Hero Section */
.article-hero {
    position: relative;
    margin-bottom: 60px;
}

.article-hero-image {
    width: 100%;
    height: 500px;
    object-fit: cover;
    border-radius: 30px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    background: linear-gradient(135deg, #2f5597 0%, #5a7fb8 100%);
}

.article-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 100%);
    padding: 60px 40px 40px;
    border-radius: 0 0 30px 30px;
}

.article-category {
    display: inline-block;
    background: var(--default-background);
    color: #fff;
    padding: 8px 20px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 15px;
}

.article-title {
    font-size: 3rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 20px;
    line-height: 1.2;
    text-shadow: 0 2px 20px rgba(0,0,0,0.5);
}

.article-meta {
    display: flex;
    align-items: center;
    gap: 25px;
    flex-wrap: wrap;
}

.meta-item {
    color: rgba(255,255,255,0.9);
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.meta-item i {
    color: var(--default-color);
    font-size: 18px;
}

/* Author Section */
.author-section {
    background: linear-gradient(135deg, var(--default-background) 0%, var(--default-color) 100%);
    padding: 30px;
    border-radius: 20px;
    margin-bottom: 40px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 10px 30px rgba(47,85,151,0.2);
}

.author-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: 700;
    color: #fff;
    border: 4px solid rgba(255,255,255,0.3);
}

.author-info h4 {
    color: #fff;
    font-weight: 700;
    margin-bottom: 5px;
    font-size: 1.3rem;
}

.author-info p {
    color: rgba(255,255,255,0.9);
    margin: 0;
}

/* Article Content */
.article-content {
    background: #fff;
    padding: 50px;
    border-radius: 25px;
    box-shadow: 0 5px 30px rgba(0,0,0,0.08);
    margin-bottom: 40px;
}

.blog-body {
    font-size: 1.1rem;
    line-height: 1.9;
    color: #333;
}

.blog-body p {
    margin-bottom: 1.5rem;
}

.blog-body h2,
.blog-body h3 {
    color: #222;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.blog-body h2 {
    font-size: 2rem;
    border-left: 5px solid var(--default-background);
    padding-left: 20px;
}

.blog-body h3 {
    font-size: 1.5rem;
}

.blog-body img {
    border-radius: 15px;
    margin: 30px 0;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    max-width: 100%;
    height: auto;
}

.blog-body ul,
.blog-body ol {
    margin-bottom: 1.5rem;
    padding-left: 30px;
}

.blog-body li {
    margin-bottom: 10px;
}

.blog-body blockquote {
    border-left: 5px solid var(--default-background);
    padding-left: 20px;
    margin-left: 0;
    margin-bottom: 1.5rem;
    font-style: italic;
    color: #555;
}

/* Share Section */
.share-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 35px;
    border-radius: 20px;
    text-align: center;
    margin-bottom: 40px;
}

.share-section h5 {
    color: #222;
    font-weight: 700;
    font-size: 1.3rem;
    margin-bottom: 20px;
}

.share-buttons {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}

.share-btn {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 20px;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    cursor: pointer;
}

.share-btn:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.share-btn.facebook {
    background: #1877f2;
    color: #fff;
}

.share-btn.twitter {
    background: #1da1f2;
    color: #fff;
}

.share-btn.linkedin {
    background: #0077b5;
    color: #fff;
}

.share-btn.email {
    background: #ea4335;
    color: #fff;
}

.share-btn.whatsapp {
    background: #25d366;
    color: #fff;
}

.share-btn.telegram {
    background: #0088cc;
    color: #fff;
}

/* Related Section */
.related-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #f0f4ff 100%);
    padding: 100px 0;
    position: relative;
    overflow: hidden;
}

.related-section::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(47,85,151,0.05) 0%, transparent 70%);
    border-radius: 50%;
}

.related-section::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -5%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(47,85,151,0.03) 0%, transparent 70%);
    border-radius: 50%;
}

.section-header {
    text-align: center;
    margin-bottom: 60px;
    position: relative;
    z-index: 1;
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
    animation: fadeInDown 0.6s ease;
}

.section-tag i {
    font-size: 18px;
}

.section-title {
    font-size: 3rem;
    font-weight: 800;
    color: #222;
    margin-bottom: 20px;
    animation: fadeInUp 0.8s ease;
}

.section-divider {
    width: 100px;
    height: 5px;
    background: linear-gradient(90deg, var(--default-background), var(--default-color));
    margin: 0 auto 20px;
    border-radius: 3px;
    animation: expandWidth 1s ease;
}

.section-description {
    color: #666;
    font-size: 1.15rem;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
    animation: fadeInUp 1s ease;
}

/* Related Stories Grid */
.related-stories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 35px;
    position: relative;
    z-index: 1;
}

.modern-blog-post {
    background: #fff;
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    text-decoration: none;
    border: 2px solid transparent;
    position: relative;
}

.modern-blog-post::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, var(--default-background), var(--default-color));
    opacity: 0;
    transition: opacity 0.5s ease;
    z-index: -1;
}

.modern-blog-post:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 25px 50px rgba(47,85,151,0.25);
    border-color: var(--default-background);
    text-decoration: none;
}

.modern-blog-post:hover::before {
    opacity: 0.03;
}

.blog-image-container {
    position: relative;
    overflow: hidden;
    height: 250px;
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

.blog-category-badge {
    position: absolute;
    top: 20px;
    left: 20px;
    background: var(--default-background);
    color: #fff;
    padding: 8px 18px;
    border-radius: 25px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    z-index: 2;
}

.blog-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 50%);
    opacity: 0;
    transition: opacity 0.4s ease;
}

.modern-blog-post:hover .blog-image-overlay {
    opacity: 1;
}

.blog-content-wrapper {
    padding: 30px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.blog-date {
    color: #999;
    font-size: 13px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
}

.blog-date i {
    color: var(--default-color);
    font-size: 16px;
}

.blog-post-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #222;
    margin-bottom: 15px;
    line-height: 1.4;
    transition: color 0.3s ease;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.modern-blog-post:hover .blog-post-title {
    color: var(--default-color);
}

.blog-excerpt {
    color: #666;
    font-size: 15px;
    line-height: 1.7;
    margin-bottom: 20px;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.read-article-btn {
    color: var(--default-color);
    font-weight: 700;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 0;
    border-top: 2px solid #f0f0f0;
    transition: all 0.3s ease;
}

.read-article-btn i {
    font-size: 18px;
    transition: transform 0.3s ease;
}

.modern-blog-post:hover .read-article-btn {
    gap: 15px;
}

.modern-blog-post:hover .read-article-btn i {
    transform: translateX(5px);
}

/* View All Button */
.view-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: linear-gradient(135deg, var(--default-background), var(--default-color));
    color: #fff;
    padding: 18px 40px;
    border-radius: 50px;
    font-size: 16px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 10px 30px rgba(47,85,151,0.3);
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
}

.view-all-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
}

.view-all-btn:hover::before {
    width: 300px;
    height: 300px;
}

.view-all-btn:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(47,85,151,0.4);
    text-decoration: none;
    color: #fff;
}

.view-all-btn i {
    font-size: 20px;
    transition: transform 0.3s ease;
}

.view-all-btn:hover i {
    transform: translateX(5px);
}

/* Back Button */
.back-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--default-color);
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 30px;
    transition: all 0.3s ease;
}

.back-button:hover {
    gap: 12px;
    color: var(--default-background);
}

.back-button i {
    font-size: 18px;
}

/* Animations */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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

@keyframes expandWidth {
    from {
        width: 0;
    }
    to {
        width: 100px;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .article-hero-image {
        height: 300px;
        border-radius: 20px;
    }
    
    .article-title {
        font-size: 2rem;
    }
    
    .article-overlay {
        padding: 40px 20px 20px;
    }
    
    .article-content {
        padding: 30px 20px;
    }
    
    .author-section {
        flex-direction: column;
        text-align: center;
    }
    
    .blog-body {
        font-size: 1rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .related-stories-grid {
        grid-template-columns: 1fr;
        gap: 25px;
    }
    
    .blog-image-container {
        height: 200px;
    }
    
    .view-all-btn {
        padding: 15px 30px;
        font-size: 15px;
    }
}
</style>
@endpush

@section('content')

<section class="article-section" style="padding: 40px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Back Button -->
                <a href="{{ route('social-impact.index') }}" class="back-button">
                    <i class="icofont-rounded-left"></i>
                    Back to All Stories
                </a>

                <!-- Article Hero Section -->
                <article class="article-hero">
                    @php
                        $images = explode(',', $initiative->image_url);
                        $firstImage = trim($images[0]);
                    @endphp
                    @if(!empty($firstImage))
                        <img src="{{ asset('images/social-impact/' . $firstImage) }}" class="article-hero-image" alt="{{ $initiative->title }}" onerror="this.style.backgroundColor = '#2f5597'; this.style.display='none';">
                    @endif
                    <div class="article-overlay">
                        <span class="article-category">{{ strtoupper($initiative->category) }}</span>
                        <h1 class="article-title">{{ $initiative->title }}</h1>
                        <div class="article-meta">
                            <span class="meta-item">
                                <i class="icofont-calendar"></i>
                                {{ \Carbon\Carbon::parse($initiative->posted_date)->format('F d, Y') }}
                            </span>
                            <span class="meta-item">
                                <i class="icofont-book-alt"></i>
                                Social Impact
                            </span>
                        </div>
                    </div>
                </article>

                <!-- Author Section -->
                <div class="author-section">
                    <div class="author-avatar">
                        <i class="icofont-user"></i>
                    </div>
                    <div class="author-info">
                        <h4>Armely Impact Team</h4>
                        <p>Driving positive change and sustainable development across communities</p>
                    </div>
                </div>

                <!-- Article Content -->
                <div class="article-content">
                    <div class="blog-body">
                        {!! $initiative->body !!}
                    </div>
                </div>

                <!-- Share Section -->
                <div class="share-section">
                    <h5>Share This Story</h5>
                    <div class="share-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" class="share-btn facebook" target="_blank" title="Share on Facebook">
                            <i class="icofont-facebook"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($initiative->title) }}" class="share-btn twitter" target="_blank" title="Share on Twitter">
                            <i class="icofont-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" class="share-btn linkedin" target="_blank" title="Share on LinkedIn">
                            <i class="icofont-linkedin"></i>
                        </a>
                        <a href="mailto:?subject={{ urlencode($initiative->title) }}&body={{ urlencode(request()->url()) }}" class="share-btn email" title="Share via Email">
                            <i class="icofont-email"></i>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($initiative->title . ' ' . request()->url()) }}" class="share-btn whatsapp" target="_blank" title="Share on WhatsApp">
                            <i class="icofont-whatsapp"></i>
                        </a>
                        <a href="https://t.me/share/url?url={{ urlencode(request()->url()) }}&text={{ urlencode($initiative->title) }}" class="share-btn telegram" target="_blank" title="Share on Telegram">
                            <i class="icofont-paper-plane"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Stories Section -->
<section class="related-section">
    <div class="container">
        <div class="section-header">
            <div class="section-tag"><i class="icofont-link"></i> Continue Reading</div>
            <h2 class="section-title">Related Impact Stories</h2>
            <div class="section-divider"></div>
            <p class="section-description">Discover more inspiring stories of change and transformation</p>
        </div>
        
        <!-- Stories Grid -->
        <div class="related-stories-grid">
            @forelse($relatedStories ?? [] as $story)
                <a href="{{ route('social-impact-details', $story->secure_id) }}" class="modern-blog-post">
                    <div class="blog-image-container @if(!$story->image_url || trim($story->image_url) === '') no-image @endif">
                        @php
                            $storyImages = explode(',', $story->image_url);
                            $storyFirstImage = trim($storyImages[0]);
                        @endphp
                        @if(!empty($storyFirstImage))
                            <img src="{{ asset('images/social-impact/' . $storyFirstImage) }}" class="blog-post-image" alt="{{ $story->title }}" onerror="this.parentElement.classList.add('no-image'); this.style.display='none';">
                        @endif
                        <div class="blog-image-overlay"></div>
                        <span class="blog-category-badge">{{ strtoupper($story->category) }}</span>
                    </div>
                    <div class="blog-content-wrapper">
                        <div class="blog-date">
                            <i class="icofont-calendar"></i>
                            {{ \Carbon\Carbon::parse($story->posted_date)->format('M d, Y') }}
                        </div>
                        <h3 class="blog-post-title">{{ $story->title }}</h3>
                        <p class="blog-excerpt">{{ Str::limit(strip_tags($story->body), 150) }}...</p>
                        <span class="read-article-btn">
                            Read Full Story
                            <i class="icofont-long-arrow-right"></i>
                        </span>
                    </div>
                </a>
            @empty
                <div class="alert alert-info w-100" style="grid-column: 1 / -1;">
                    <i class="icofont-info-circle"></i> No related stories found.
                </div>
            @endforelse
        </div>
        
        <!-- View All Button -->
        <div class="text-center mt-5">
            <a href="{{ route('social-impact.index') }}" class="view-all-btn text-light">
                <span>Explore All Stories</span>
                <i class="icofont-rounded-double-right"></i>
            </a>
        </div>
    </div>
</section>

@endsection

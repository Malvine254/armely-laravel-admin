@extends('layouts.public')

@php
    use Illuminate\Support\Str;

    $name = trim((string) ($testimonial->name ?? 'Customer Story'));
    $role = trim((string) ($testimonial->position ?? 'Armely customer'));
    $companyName = trim((string) ($testimonial->company ?? ''));
    if ($companyName !== '') {
        $role = $role !== '' ? $role . ', ' . $companyName : $companyName;
    }
    $body = trim((string) ($testimonial->body_content ?? ''));
    $plainBody = trim(preg_replace('/\s+/', ' ', strip_tags($body)));
    $metaDescription = $plainBody !== ''
        ? Str::limit($plainBody, 155, '')
        : 'Read the full Armely customer story.';

    $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $initials = '';

    foreach ($parts as $part) {
        $initials .= strtoupper(substr($part, 0, 1));
        if (strlen($initials) >= 2) {
            break;
        }
    }

    $initials = $initials !== '' ? $initials : 'CS';

    $profile = trim((string) ($testimonial->profile ?? ''));
    $profileImage = null;

    if ($profile !== '') {
        $profileImage = Str::startsWith($profile, ['http://', 'https://', '/'])
            ? $profile
            : asset('images/customer-stories/' . ltrim($profile, '/'));
    }
@endphp

@section('title', $name . ' Customer Story | Armely')
@section('meta_description', $metaDescription)

@push('styles')
<style>
    .customer-story-page,
    .customer-story-page * {
        box-sizing: border-box;
    }

    .customer-story-page {
        --page-bg: #f7f9fc;
        --card-bg: #ffffff;
        --ink: #152b47;
        --text: #334155;
        --muted: #64748b;
        --brand: #2f5597;
        --brand-dark: #173b67;
        --line: rgba(47, 85, 151, 0.16);
        background: var(--page-bg);
        color: var(--text);
    }

    .customer-story-hero {
        padding: 72px 24px;
        background: linear-gradient(135deg, #173b67 0%, #284f84 100%);
        color: #ffffff;
    }

    .customer-story-inner {
        width: min(1040px, 100%);
        margin: 0 auto;
    }

    .back-link {
        display: inline-flex;
        margin-bottom: 24px;
        color: rgba(255, 255, 255, 0.82);
        font-size: 0.88rem;
        font-weight: 700;
        text-decoration: none;
    }

    .back-link:hover {
        color: #ffffff;
        text-decoration: none;
    }

    .story-hero-grid {
        display: grid;
        grid-template-columns: 96px minmax(0, 1fr);
        gap: 22px;
        align-items: center;
    }

    .story-avatar {
        display: inline-flex;
        width: 96px;
        height: 96px;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 3px solid rgba(255, 255, 255, 0.28);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        font-size: 1.5rem;
        font-weight: 800;
    }

    .story-avatar img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .story-kicker {
        margin-bottom: 10px;
        color: rgba(255, 255, 255, 0.72);
        font-size: 0.76rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .story-title {
        margin: 0 0 10px;
        color: #ffffff;
        font-size: clamp(2rem, 4.8vw, 4rem);
        font-weight: 800;
        line-height: 1.06;
        letter-spacing: 0;
    }

    .story-role {
        color: rgba(255, 255, 255, 0.82);
        font-size: 1rem;
        line-height: 1.6;
    }

    .story-content-section {
        padding: 70px 24px 88px;
    }

    .story-content-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 28px;
        align-items: start;
    }

    .story-article,
    .story-aside,
    .related-card {
        border: 1px solid var(--line);
        border-radius: 8px;
        background: var(--card-bg);
        box-shadow: 0 16px 38px rgba(18, 47, 82, 0.07);
    }

    .story-article {
        padding: clamp(24px, 4vw, 42px);
    }

    .story-article h2 {
        margin: 0 0 18px;
        color: var(--ink);
        font-size: clamp(1.45rem, 2.4vw, 2.15rem);
        font-weight: 800;
        line-height: 1.2;
    }

    .story-body {
        color: var(--text);
        font-size: 1rem;
        line-height: 1.85;
    }

    .story-body p {
        margin: 0 0 1.15em;
    }

    .story-body p:last-child {
        margin-bottom: 0;
    }

    .story-aside {
        padding: 22px;
        position: sticky;
        top: 24px;
    }

    .aside-label {
        margin-bottom: 8px;
        color: var(--brand);
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .aside-title {
        margin-bottom: 10px;
        color: var(--ink);
        font-size: 1.05rem;
        font-weight: 800;
        line-height: 1.35;
    }

    .aside-copy {
        margin: 0 0 18px;
        color: var(--muted);
        font-size: 0.9rem;
        line-height: 1.7;
    }

    .aside-link,
    .related-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 10px 14px;
        border-radius: 8px;
        background: var(--brand);
        color: #ffffff;
        font-size: 0.86rem;
        font-weight: 800;
        text-decoration: none;
    }

    .aside-link:hover,
    .related-link:hover {
        background: var(--brand-dark);
        color: #ffffff;
        text-decoration: none;
    }

    .related-section {
        margin-top: 34px;
    }

    .related-heading {
        margin: 0 0 16px;
        color: var(--ink);
        font-size: 1.3rem;
        font-weight: 800;
    }

    .related-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .related-card {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 20px;
    }

    .related-name {
        color: var(--ink);
        font-weight: 800;
        line-height: 1.35;
    }

    .related-role {
        color: var(--muted);
        font-size: 0.84rem;
        line-height: 1.55;
    }

    .related-link {
        align-self: flex-start;
        margin-top: auto;
        background: #f4f7fd;
        color: var(--brand);
        border: 1px solid rgba(47, 85, 151, 0.22);
    }

    .related-link:hover {
        background: var(--brand);
    }

    @media (max-width: 900px) {
        .story-content-grid,
        .related-grid {
            grid-template-columns: 1fr;
        }

        .story-aside {
            position: static;
        }
    }

    @media (max-width: 640px) {
        .customer-story-hero {
            padding: 60px 18px;
        }

        .story-content-section {
            padding: 56px 18px 72px;
        }

        .story-hero-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="customer-story-page">
    <section class="customer-story-hero">
        <div class="customer-story-inner">
            <a class="back-link" href="{{ route('customer-stories.index') }}">Back to customer stories</a>

            <div class="story-hero-grid">
                <div class="story-avatar">
                    @if($profileImage)
                        <img src="{{ $profileImage }}" alt="{{ $name }}" onerror="this.style.display='none'; this.parentElement.textContent='{{ $initials }}';">
                    @else
                        {{ $initials }}
                    @endif
                </div>
                <div>
                    <div class="story-kicker">Full customer story</div>
                    <h1 class="story-title">{{ $name }}</h1>
                    <div class="story-role">{{ $role }}</div>
                </div>
            </div>
        </div>
    </section>

    <section class="story-content-section">
        <div class="customer-story-inner">
            <div class="story-content-grid">
                <article class="story-article">
                    <h2>Full Review</h2>
                    <div class="story-body">
                        @if($body !== '')
                            {!! $body !!}
                        @else
                            <p>This customer story is being prepared.</p>
                        @endif
                    </div>
                </article>

                <aside class="story-aside">
                    <div class="aside-label">Customer voice</div>
                    <div class="aside-title">{{ $name }}</div>
                    <p class="aside-copy">{{ $role }}</p>
                    <a class="aside-link" href="{{ route('contact') }}">Talk with Armely</a>
                </aside>
            </div>

            @if(collect($relatedStories ?? [])->isNotEmpty())
                <div class="related-section">
                    <h2 class="related-heading">More Customer Stories</h2>
                    <div class="related-grid">
                        @foreach($relatedStories as $related)
                            <article class="related-card">
                                <div class="related-name">{{ $related->name ?? 'Customer Story' }}</div>
                                <div class="related-role">{{ trim(($related->position ?? '') . (!empty($related->company) ? ', ' . $related->company : '')) ?: 'Armely customer' }}</div>
                                <a class="related-link" href="{{ route('customer-stories.show', $related->id) }}">Read review</a>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

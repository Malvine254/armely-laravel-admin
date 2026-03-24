<style>
    .freemium-showcase {
        padding: 28px 0 12px;
    }

    .freemium-hero {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        padding: 44px 40px;
        background:
            radial-gradient(circle at top right, rgba(255, 255, 255, 0.14), transparent 32%),
            linear-gradient(135deg, #2f5597 0%, #1e3a6d 58%, #12304f 100%);
        box-shadow: 0 24px 60px rgba(24, 52, 94, 0.22);
        color: #fff;
        margin-bottom: 32px;
    }

    .freemium-hero::before {
        content: '';
        position: absolute;
        inset: auto -60px -60px auto;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        pointer-events: none;
    }

    .freemium-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.18);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 18px;
    }

    .freemium-hero h2 {
        color: #fff;
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1.12;
        margin-bottom: 16px;
        letter-spacing: -0.03em;
    }

    .freemium-hero p {
        max-width: 760px;
        color: rgba(255, 255, 255, 0.86);
        font-size: 1.03rem;
        line-height: 1.8;
        margin-bottom: 0;
    }

    .freemium-meta-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-top: 28px;
    }

    .freemium-meta-card {
        background: rgba(255, 255, 255, 0.10);
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 18px;
        padding: 18px 20px;
        backdrop-filter: blur(8px);
    }

    .freemium-meta-card strong {
        display: block;
        color: #fff;
        font-size: 1.05rem;
        margin-bottom: 4px;
    }

    .freemium-meta-card span {
        color: rgba(255, 255, 255, 0.78);
        font-size: 0.92rem;
        line-height: 1.5;
    }

    .freemium-section-heading {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 22px;
    }

    .freemium-section-heading h3 {
        font-size: 2rem;
        font-weight: 800;
        color: #17355c;
        margin: 0;
        letter-spacing: -0.03em;
    }

    .freemium-section-heading p {
        margin: 0;
        color: #64748b;
        max-width: 720px;
        line-height: 1.7;
    }

    .freemium-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
        margin-bottom: 34px;
        align-items: stretch;
    }

    .freemium-card {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border: 1px solid rgba(47, 85, 151, 0.10);
        border-radius: 24px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.07);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        min-height: 100%;
        height: 100%;
    }

    .freemium-card-badge {
        display: inline-flex;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 999px;
        background: #eef5ff;
        color: #1e3a6d;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        box-shadow: inset 0 0 0 1px rgba(47, 85, 151, 0.12);
    }

    .freemium-card-body {
        padding: 26px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        flex: 1;
        min-height: 420px;
    }

    .freemium-card-main {
        display: flex;
        flex-direction: column;
        gap: 16px;
        flex: 1;
    }

    .freemium-card-title {
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1.2;
        color: #17355c;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .freemium-card-summary {
        color: #526377;
        line-height: 1.8;
        font-size: 0.97rem;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .freemium-card-preview {
        color: #334155;
        line-height: 1.8;
        font-size: 0.96rem;
        display: -webkit-box;
        -webkit-line-clamp: 6;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 10.8em;
    }

    .freemium-card-content {
        color: #334155;
        line-height: 1.8;
        font-size: 0.96rem;
        display: none;
        padding-top: 6px;
        border-top: 1px solid rgba(47, 85, 151, 0.10);
    }

    .freemium-card.is-expanded .freemium-card-content {
        display: block;
    }

    .freemium-card.is-expanded .freemium-card-preview {
        display: none;
    }

    .freemium-card-content h1,
    .freemium-card-content h2,
    .freemium-card-content h3,
    .freemium-card-content h4,
    .freemium-card-content h5 {
        color: #1e3a6d;
        font-weight: 800;
        margin-top: 0;
        margin-bottom: 12px;
    }

    .freemium-card-content p:last-child {
        margin-bottom: 0;
    }

    .freemium-card-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: auto;
        padding-top: 6px;
    }

    .freemium-read-more {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: fit-content;
        padding: 0;
        border: none;
        background: transparent;
        color: #2f5597;
        font-weight: 800;
        font-size: 0.92rem;
        cursor: pointer;
    }

    .freemium-read-more:hover {
        color: #1e3a6d;
    }

    .freemium-btn-primary,
    .freemium-btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        border-radius: 999px;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 13px 20px;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .freemium-btn-primary {
        background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
        color: #fff !important;
        box-shadow: 0 12px 28px rgba(47, 85, 151, 0.24);
    }

    .freemium-btn-secondary {
        background: #eef5ff;
        color: #1e3a6d !important;
        border: 1px solid rgba(47, 85, 151, 0.16);
    }

    .freemium-btn-primary:hover,
    .freemium-btn-secondary:hover {
        transform: translateY(-2px);
        text-decoration: none;
    }

    .freemium-btn-primary:hover {
        color: #fff !important;
        background: linear-gradient(135deg, #24467f 0%, #173057 100%);
    }

    .freemium-btn-secondary:hover {
        color: #1e3a6d !important;
        background: #dfeeff;
        border-color: rgba(47, 85, 151, 0.24);
    }

    .freemium-insight-band {
        margin-top: 8px;
        background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%);
        border: 1px solid rgba(47, 85, 151, 0.10);
        border-radius: 24px;
        padding: 26px 28px;
    }

    .freemium-insight-band h4 {
        color: #17355c;
        font-size: 1.35rem;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .freemium-insight-band p {
        margin: 0;
        color: #526377;
        line-height: 1.8;
    }

    .freemium-empty {
        background: #f8fbff;
        border: 1px dashed rgba(47, 85, 151, 0.25);
        border-radius: 24px;
        padding: 40px 28px;
        text-align: center;
        color: #526377;
    }

    .freemium-empty h4 {
        color: #17355c;
        font-weight: 800;
        margin-bottom: 10px;
    }

    @media (max-width: 991px) {
        .freemium-grid {
            grid-template-columns: 1fr;
        }

        .freemium-meta-grid {
            grid-template-columns: 1fr;
        }

        .freemium-hero {
            padding: 34px 26px;
        }

        .freemium-hero h2 {
            font-size: 2rem;
        }
    }

    @media (max-width: 575px) {
        .freemium-hero {
            padding: 28px 20px;
            border-radius: 22px;
        }

        .freemium-hero h2 {
            font-size: 1.65rem;
        }

        .freemium-section-heading {
            flex-direction: column;
            align-items: flex-start;
        }

        .freemium-card-body {
            padding: 22px 18px;
            min-height: 0;
        }

        .freemium-card-actions {
            flex-direction: column;
        }
    }
</style>

@php
    $freemiumItems = collect($freemiums ?? []);
@endphp

<section class="freemium-showcase">
    <div class="container">
        <div class="freemium-hero">
            <span class="freemium-eyebrow">
                <i class="fa fa-gift"></i>
                Free Resources
            </span>
            <h2>Freemiums That Help Teams Move Faster</h2>
            <p>
                We pulled the freemium content into this page so visitors can browse your downloadable resources,
                starter assets, and decision-support materials in one polished experience before reaching out for
                implementation help.
            </p>

            <div class="freemium-meta-grid">
                <div class="freemium-meta-card">
                    <strong>{{ $freemiumItems->count() }}</strong>
                    <span>resource{{ $freemiumItems->count() === 1 ? '' : 's' }} currently published</span>
                </div>
                <div class="freemium-meta-card">
                    <strong>Built For Action</strong>
                    <span>Each item is positioned as a practical next step, not generic marketing copy.</span>
                </div>
                <div class="freemium-meta-card">
                    <strong>Consultation Ready</strong>
                    <span>Users can review a resource and immediately continue into the consultation form below.</span>
                </div>
            </div>
        </div>

        <div class="freemium-section-heading">
            <div>
                <h3>Available Freemium Resources</h3>
                <p>These cards are now driven from the freemium data source used in the legacy PHP page, with modern styling and clearer calls to action.</p>
            </div>
        </div>

        @if($freemiumItems->isNotEmpty())
            <div class="freemium-grid">
                @foreach($freemiumItems as $freemium)
                    <article class="freemium-card">
                        <div class="freemium-card-body">
                            @php
                                $summary = $freemium->snippet ?: \Illuminate\Support\Str::limit(trim(strip_tags($freemium->body ?? '')), 180);
                                $plainBody = trim(preg_replace('/\s+/', ' ', strip_tags($freemium->body ?? '')));
                                $preview = \Illuminate\Support\Str::limit($plainBody, 260);
                                $hasMore = \Illuminate\Support\Str::length($plainBody) > 260;
                            @endphp

                            <div class="freemium-card-main">
                                <span class="freemium-card-badge">
                                    <i class="fa fa-download"></i>
                                    Freemium
                                </span>

                                <h4 class="freemium-card-title">{{ $freemium->title }}</h4>

                                @if(!empty($summary))
                                    <p class="freemium-card-summary">{{ $summary }}</p>
                                @endif

                                @if(!empty($preview))
                                    <div class="freemium-card-preview">{{ $preview }}</div>
                                @endif

                                @if($hasMore)
                                    <button type="button" class="freemium-read-more" data-read-more>
                                        <span>Read more</span>
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                @endif

                                @if(!empty($freemium->body))
                                    <div class="freemium-card-content">
                                        {!! $freemium->body !!}
                                    </div>
                                @endif
                            </div>

                            <div class="freemium-card-actions">
                                <a href="#consultation" class="freemium-btn-primary">
                                    <i class="fa fa-arrow-right"></i>
                                    Request This Resource
                                </a>
                                <a href="#consultation" class="freemium-btn-secondary">
                                    <i class="fa fa-comments"></i>
                                    Talk To Our Team
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="freemium-insight-band">
                <h4>Need help operationalizing one of these offers?</h4>
                <p>Use the consultation section below to tell us which freemium caught your attention. We can help you turn a starter asset, assessment, or resource into an implementation roadmap.</p>
            </div>
        @else
            <div class="freemium-empty">
                <h4>No freemium resources are available yet</h4>
                <p>The page is wired to live freemium data, but no records were returned from the source table.</p>
            </div>
        @endif
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-read-more]').forEach(function (button) {
            button.addEventListener('click', function () {
                var card = button.closest('.freemium-card');
                if (!card) return;

                var expanded = card.classList.toggle('is-expanded');
                button.querySelector('span').textContent = expanded ? 'Read less' : 'Read more';

                var icon = button.querySelector('i');
                if (icon) {
                    icon.className = expanded ? 'fa fa-angle-up' : 'fa fa-angle-down';
                }
            });
        });
    });
</script>

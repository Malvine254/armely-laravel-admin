@extends('layouts.public')

@section('title', 'Client Reviews | What Clients Say About Armely')
@section('meta_description', 'Read client reviews from Armely customers, including full reviews, roles, organizations, and engagement outcomes.')

@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;

    $stories = collect($testimonials ?? []);

    $splitRoleCompany = function ($position) {
        $position = trim((string) $position);

        if ($position === '') {
            return ['Client', 'Armely customer'];
        }

        if (str_contains($position, ' - ')) {
            [$role, $company] = array_map('trim', explode(' - ', $position, 2));
            return [$role !== '' ? $role : 'Client', $company !== '' ? $company : 'Armely customer'];
        }

        if (str_contains($position, ',')) {
            [$role, $company] = array_map('trim', explode(',', $position, 2));
            return [$role !== '' ? $role : 'Client', $company !== '' ? $company : 'Armely customer'];
        }

        return [$position, 'Armely customer'];
    };

    $reviewedAt = function ($story) {
        try {
            $date = !empty($story->created_at) ? Carbon::parse($story->created_at) : null;
        } catch (Throwable $e) {
            $date = null;
        }

        return $date ? 'Reviewed ' . $date->format('F Y') : 'Reviewed by Armely client';
    };

    $accentColors = ['#1fa37a', '#c6561f', '#8fa0b8'];

    $cleanReviewText = function ($story) {
        $text = strip_tags((string) ($story->body_content ?? ''));
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/', ' ', $text));
    };

    // Use the shortest review as the base length for every collapsed preview.
    $reviewLengths = $stories
        ->map(fn ($story) => Str::length($cleanReviewText($story)))
        ->filter(fn ($len) => $len > 0);

    $baseReviewLength = $reviewLengths->isNotEmpty() ? $reviewLengths->min() : 240;
@endphp

@push('styles')
<style>
    .client-reviews-page,
    .client-reviews-page * {
        box-sizing: border-box;
    }

    .client-reviews-page {
        --page-bg: #fafbf9;
        --ink: #142846;
        --text: #182b4a;
        --muted: #5b667a;
        --quiet: #8c95aa;
        --brand: #1f4f9a;
        --line: #dbe3ee;
        background: var(--page-bg);
        color: var(--text);
        border-top: 1px solid #dfe7d8;
    }

    .reviews-hero {
        padding: 58px 24px 50px;
        background: #244f82;
        color: #ffffff;
        text-align: center;
    }

    .reviews-hero-inner {
        width: min(1120px, 100%);
        margin: 0 auto;
    }

    .reviews-hero-eyebrow {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 24px;
        margin-bottom: 18px;
        padding: 3px 8px;
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        font-size: 0.72rem;
        font-weight: 900;
        letter-spacing: 0.22em;
        line-height: 1;
        text-transform: uppercase;
    }

    .reviews-hero-title {
        max-width: 900px;
        margin: 0 auto 14px;
        color: #ffffff;
        font-size: clamp(1.8rem, 3vw, 2.65rem);
        font-weight: 900;
        line-height: 1.08;
        letter-spacing: 0;
    }

    .reviews-hero-copy {
        max-width: 650px;
        margin: 0 auto;
        color: rgba(255, 255, 255, 0.78);
        font-size: 1rem;
        line-height: 1.65;
    }

    .client-reviews-section {
        padding: 34px 24px 28px;
    }

    .client-reviews-inner {
        width: min(1120px, 100%);
        margin: 0 auto;
    }

    .client-reviews-eyebrow {
        margin-bottom: 14px;
        color: var(--brand);
        font-size: 0.72rem;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .client-reviews-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .review-card {
        position: relative;
        display: flex;
        flex-direction: column;
        padding: 22px 20px 20px;
        overflow: hidden;
        border: 1px solid var(--line);
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 12px 28px rgba(20, 40, 70, 0.04);
    }

    .review-card::before {
        content: "";
        position: absolute;
        top: 24px;
        left: 20px;
        right: 20px;
        height: 3px;
        border-radius: 999px;
        background: var(--accent);
    }

    .review-card::after {
        content: "OC";
        position: absolute;
        top: 10px;
        right: 18px;
        color: rgba(143, 160, 184, 0.12);
        font-size: 3.8rem;
        font-weight: 300;
        letter-spacing: -0.16em;
        line-height: 1;
        pointer-events: none;
    }

    .review-stars {
        margin-top: 24px;
        margin-bottom: 7px;
        color: #ff9c00;
        font-size: 0.82rem;
        line-height: 1;
    }

    .review-copy {
        margin: 0;
        color: var(--text);
        font-size: 0.86rem;
        font-style: italic;
        line-height: 1.58;
    }

    .review-toggle {
        display: block;
        margin: 8px 0 0;
        padding: 0;
        border: 0;
        background: none;
        color: var(--brand);
        font: inherit;
        font-weight: 900;
        font-style: normal;
        cursor: pointer;
        text-decoration: underline;
        text-underline-offset: 2px;
        white-space: nowrap;
    }

    .review-toggle:hover {
        color: #163a75;
    }

    .review-spacer {
        flex: 0 0 auto;
        min-height: 2px;
    }

    .review-divider {
        height: 1px;
        margin: 6px 0 6px;
        background: var(--line);
    }

    .review-meta-row {
        display: block;
        margin-bottom: 4px;
        text-align: left;
    }

    .review-name {
        margin: 0 0 6px;
        color: #061d3d;
        font-size: 0.94rem;
        font-weight: 900;
        line-height: 1.25;
    }

    .review-role {
        margin: 0 0 6px;
        color: var(--muted);
        font-size: 0.78rem;
        line-height: 1.45;
    }

    .review-company {
        display: block;
        margin: 0 0 6px;
        color: var(--brand);
        font-size: 0.78rem;
        font-weight: 900;
        line-height: 1.35;
    }

    .review-date {
        margin: 0;
        color: var(--quiet);
        font-size: 0.74rem;
        line-height: 1.45;
    }

    .empty-state {
        padding: 24px;
        border: 1px dashed #b9c9e1;
        border-radius: 12px;
        background: #ffffff;
        color: var(--muted);
    }

    .public-sector-section {
        padding: 34px 24px 32px;
        background: #f3f6fb;
    }

    .public-sector-inner {
        width: min(1120px, 100%);
        margin: 0 auto;
    }

    .public-sector-eyebrow {
        margin-bottom: 14px;
        color: var(--brand);
        font-size: 0.72rem;
        font-weight: 900;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    .public-sector-title {
        max-width: 860px;
        margin: 0 0 18px;
        color: var(--ink);
        font-size: clamp(1.5rem, 2.2vw, 1.95rem);
        font-weight: 900;
        line-height: 1.12;
        letter-spacing: 0;
    }

    .public-sector-copy {
        max-width: 570px;
        margin: 0 0 32px;
        color: var(--text);
        font-size: 1rem;
        line-height: 1.65;
    }

    .outcomes-panel {
        overflow: hidden;
        border: 1px solid var(--line);
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 14px 38px rgba(20, 40, 70, 0.05);
    }

    .outcomes-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 22px;
        padding: 22px 24px;
        border-bottom: 1px solid var(--line);
    }

    .outcomes-title {
        margin: 0 0 8px;
        color: #061d3d;
        font-size: 1rem;
        font-weight: 900;
        line-height: 1.3;
    }

    .outcomes-copy {
        max-width: 620px;
        margin: 0;
        color: var(--muted);
        font-size: 0.86rem;
        line-height: 1.6;
    }

    .outcomes-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        padding: 6px 14px;
        border: 1px solid #bdd0ea;
        border-radius: 999px;
        background: #eef5ff;
        color: var(--brand);
        font-size: 0.66rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .outcomes-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .outcome-card {
        min-height: 168px;
        padding: 20px;
        border-right: 1px solid var(--line);
        border-bottom: 1px solid var(--line);
    }

    .outcome-card:nth-child(3n) {
        border-right: 0;
    }

    .outcome-identity {
        display: grid;
        grid-template-columns: 34px minmax(0, 1fr);
        gap: 12px;
        align-items: start;
        margin-bottom: 14px;
    }

    .outcome-icon {
        display: inline-flex;
        width: 34px;
        height: 34px;
        align-items: center;
        justify-content: center;
        border: 1px solid #bdd0ea;
        border-radius: 10px;
        background: #eef5ff;
        color: var(--brand);
        font-size: 1rem;
        font-weight: 900;
        line-height: 1;
    }

    .outcome-name {
        margin: 0 0 3px;
        color: #061d3d;
        font-size: 0.94rem;
        font-weight: 900;
        line-height: 1.25;
    }

    .outcome-location {
        margin: 0;
        color: var(--muted);
        font-size: 0.76rem;
        line-height: 1.4;
    }

    .outcome-body {
        margin: 0 0 14px;
        color: var(--text);
        font-size: 0.88rem;
        line-height: 1.58;
    }

    .outcome-tag {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 4px;
        background: #edf3fb;
        color: var(--brand);
        font-size: 0.65rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        line-height: 1;
        text-transform: uppercase;
    }

    .agency-cta-card {
        display: flex;
        min-height: 100%;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: #eaf0f8;
        text-align: center;
    }

    .agency-cta-title {
        margin: 0 0 10px;
        color: var(--brand);
        font-size: 0.98rem;
        font-weight: 900;
    }

    .agency-cta-copy {
        max-width: 300px;
        margin: 0 0 18px;
        color: var(--muted);
        font-size: 0.84rem;
        line-height: 1.55;
    }

    .agency-cta-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 9px 18px;
        border-radius: 6px;
        background: #2f5597;
        color: #ffffff;
        font-size: 0.82rem;
        font-weight: 900;
        text-decoration: none;
    }

    .agency-cta-link:hover {
        background: #173b67;
        color: #ffffff;
        text-decoration: none;
    }

    .outcomes-note {
        padding: 12px 18px;
        color: var(--muted);
        font-size: 0.78rem;
        line-height: 1.5;
    }

    .outcomes-note strong {
        color: #061d3d;
    }

    .metrics-section {
        padding: 32px 24px 52px;
        background: #ffffff;
    }

    .metrics-inner {
        width: min(1120px, 100%);
        margin: 0 auto;
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }

    .metric-card {
        min-height: 92px;
        padding: 18px;
        border: 1px solid var(--line);
        border-radius: 10px;
        background: #ffffff;
        text-align: center;
    }

    .metric-number {
        margin-bottom: 7px;
        color: var(--brand);
        font-size: 1.55rem;
        font-weight: 900;
        line-height: 1;
    }

    .metric-label {
        color: var(--muted);
        font-size: 0.78rem;
        line-height: 1.4;
    }

    .case-study-cta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 22px;
        padding: 18px 22px;
        border: 1px solid var(--line);
        border-radius: 10px;
        background: #ffffff;
    }

    .case-study-copy {
        margin: 0;
        color: var(--text);
        font-size: 0.96rem;
        line-height: 1.55;
    }

    .case-study-copy strong {
        color: #061d3d;
        font-weight: 900;
    }

    .case-study-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 10px 22px;
        border-radius: 8px;
        background: #2f5597;
        color: #ffffff !important;
        font-size: 0.88rem;
        font-weight: 900;
        text-decoration: none;
        white-space: nowrap;
    }

    .case-study-link:hover {
        background: #173b67;
        color: #ffffff !important;
        text-decoration: none;
    }

    @media (max-width: 1100px) {
        .client-reviews-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .outcomes-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .outcome-card:nth-child(3n) {
            border-right: 1px solid var(--line);
        }

        .outcome-card:nth-child(2n) {
            border-right: 0;
        }

        .metrics-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .reviews-hero {
            padding: 48px 18px 42px;
        }

        .client-reviews-section {
            padding: 30px 18px 46px;
        }

        .client-reviews-grid {
            grid-template-columns: 1fr;
        }

        .review-card {
            min-height: auto;
        }

        .public-sector-section,
        .metrics-section {
            padding-left: 18px;
            padding-right: 18px;
        }

        .outcomes-head,
        .case-study-cta {
            align-items: flex-start;
            flex-direction: column;
        }

        .outcomes-grid,
        .metrics-grid {
            grid-template-columns: 1fr;
        }

        .outcome-card,
        .outcome-card:nth-child(2n),
        .outcome-card:nth-child(3n) {
            border-right: 0;
        }

        .case-study-link {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="client-reviews-page">
    <section class="reviews-hero">
        <div class="reviews-hero-inner">
            <div class="reviews-hero-eyebrow">Verified Customer Voices</div>
            <h1 class="reviews-hero-title">What clients say after working with Armely.</h1>
            <p class="reviews-hero-copy">Named reviewers. Real titles. Real companies. Review dates on every card so you know when the work was done.</p>
        </div>
    </section>

    <section class="client-reviews-section">
        <div class="client-reviews-inner">
            <div class="client-reviews-eyebrow">Client Reviews</div>

            @if($stories->isNotEmpty())
                <div class="client-reviews-grid">
                    @foreach($stories as $story)
                        @php
                            [$role, $company] = $splitRoleCompany($story->position ?? '');
                            $name = trim((string) ($story->name ?? 'Armely client'));
                            $fullText = $cleanReviewText($story);
                            $hasText = $fullText !== '';
                            $isTruncated = $hasText && Str::length($fullText) > $baseReviewLength;
                            $preview = $hasText ? Str::limit($fullText, $baseReviewLength, '...') : 'This client review is being prepared.';
                            $accent = $accentColors[$loop->index % count($accentColors)];
                        @endphp

                        <article class="review-card" style="--accent: {{ $accent }};">
                            <div class="review-stars" aria-label="Five star review">
                                <span aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                            </div>
                            <p class="review-copy">
                                <span class="review-copy-text" data-short="{{ $preview }}" data-full="{{ $fullText }}">{{ $preview }}</span>@if($isTruncated)
                                <button type="button" class="review-toggle" data-review-toggle aria-expanded="false">Read full review <span aria-hidden="true">&rarr;</span></button>@endif
                            </p>

                            <div class="review-spacer"></div>
                            <div class="review-divider"></div>

                            <div class="review-meta-row">
                                <h2 class="review-name">{{ $name }}</h2>
                                <p class="review-role">{{ $role }}</p>
                                <strong class="review-company">{{ $company }}</strong>
                                <p class="review-date">{{ $reviewedAt($story) }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="empty-state">Client reviews are coming soon.</div>
            @endif
        </div>
    </section>

    <section class="public-sector-section">
        <div class="public-sector-inner">
            <div class="public-sector-eyebrow">Government and Public Sector</div>
            <h2 class="public-sector-title">Trusted by government organizations across the United States.</h2>
            <p class="public-sector-copy">Government clients operate under procurement and communications policies that restrict public endorsements of vendors. We respect that. Here is what we delivered for each organization.</p>

            <div class="outcomes-panel">
                <div class="outcomes-head">
                    <div>
                        <h3 class="outcomes-title">Documented outcomes - no quotes by policy</h3>
                        <p class="outcomes-copy">These organizations cannot provide public testimonials due to procurement rules. The work is real, the outcomes are documented, and Armely understands the compliance requirements of the public sector better than most vendors.</p>
                    </div>
                    <span class="outcomes-badge">Outcomes Only</span>
                </div>

                <div class="outcomes-grid">
                    <article class="outcome-card">
                        <div class="outcome-identity">
                            <span class="outcome-icon" aria-hidden="true">B</span>
                            <div>
                                <h4 class="outcome-name">City of Frisco</h4>
                                <p class="outcome-location">Municipal Government - Frisco, TX</p>
                            </div>
                        </div>
                        <p class="outcome-body">Microsoft 365 governance and adoption program deployed across city departments. Compliance configuration, staff training, and change management completed.</p>
                        <span class="outcome-tag">M365 Governance</span>
                    </article>

                    <article class="outcome-card">
                        <div class="outcome-identity">
                            <span class="outcome-icon" aria-hidden="true">B</span>
                            <div>
                                <h4 class="outcome-name">Plano ISD</h4>
                                <p class="outcome-location">School District - Plano, TX</p>
                            </div>
                        </div>
                        <p class="outcome-body">Microsoft 365 EDU governance, SharePoint, and Power Platform implementations across district-wide operations serving 54,000 students.</p>
                        <span class="outcome-tag">M365 EDU</span>
                    </article>

                    <article class="outcome-card">
                        <div class="outcome-identity">
                            <span class="outcome-icon" aria-hidden="true">D</span>
                            <div>
                                <h4 class="outcome-name">Dallas County</h4>
                                <p class="outcome-location">County Government - Dallas, TX</p>
                            </div>
                        </div>
                        <p class="outcome-body">Government data modernization and compliance configuration supporting county-wide technology operations and public services delivery.</p>
                        <span class="outcome-tag">Data Modernization</span>
                    </article>

                    <article class="outcome-card">
                        <div class="outcome-identity">
                            <span class="outcome-icon" aria-hidden="true">S</span>
                            <div>
                                <h4 class="outcome-name">State Agency</h4>
                                <p class="outcome-location">State Government - Kansas</p>
                            </div>
                        </div>
                        <p class="outcome-body">Microsoft 365 Government deployment with FedRAMP compliance configuration across 400+ staff. Completed in 8 weeks with zero service disruption.</p>
                        <span class="outcome-tag">GCC Compliance</span>
                    </article>

                    <article class="outcome-card">
                        <div class="outcome-identity">
                            <span class="outcome-icon" aria-hidden="true">P</span>
                            <div>
                                <h4 class="outcome-name">City of San Diego</h4>
                                <p class="outcome-location">Municipal Government - San Diego, CA</p>
                            </div>
                        </div>
                        <p class="outcome-body">Real-time Power BI call center dashboard replacing manual reporting. Supervisors now see live queue and agent metrics without manual data pulls.</p>
                        <span class="outcome-tag">Power BI</span>
                    </article>

                    <div class="agency-cta-card">
                        <h4 class="agency-cta-title">Working with a government agency?</h4>
                        <p class="agency-cta-copy">Armely understands FedRAMP, CJIS, IRS 1075, and GCC compliance requirements. We can connect you with government client references under a reference arrangement.</p>
                        <a class="agency-cta-link" href="{{ route('contact') }}">Let's Talk</a>
                    </div>
                </div>

                <div class="outcomes-note"><strong>Why no quotes?</strong> Government clients operate under procurement and communications policies that restrict public endorsements of vendors. This is standard in the public sector. If you are a government buyer evaluating Armely, we are happy to arrange a direct client reference call.</div>
            </div>
        </div>
    </section>

    <section class="metrics-section">
        <div class="metrics-inner">
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-number">4.9/5</div>
                    <div class="metric-label">Average client rating</div>
                </div>
                <div class="metric-card">
                    <div class="metric-number">85%+</div>
                    <div class="metric-label">Client retention rate</div>
                </div>
                <div class="metric-card">
                    <div class="metric-number">90%+</div>
                    <div class="metric-label">Client satisfaction / NPS</div>
                </div>
                <div class="metric-card">
                    <div class="metric-number">{{ date('Y') - 2016 }} yrs</div>
                    <div class="metric-label">Delivery experience</div>
                </div>
            </div>

            <div class="case-study-cta">
                <p class="case-study-copy"><strong>Looking for documented project outcomes?</strong> See full case studies with problem statements, technologies used, and measured results.</p>
                @php $csCount = $caseStudyCount ?? 0; @endphp
                <a class="case-study-link" href="{{ route('case-studies.index') }}">View {{ $csCount > 0 ? $csCount . ' ' : '' }}{{ Str::plural('Case Study', $csCount) }}</a>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-review-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var copy = btn.closest('.review-copy');
            var text = copy ? copy.querySelector('.review-copy-text') : null;
            if (!text) {
                return;
            }

            var expanded = btn.getAttribute('aria-expanded') === 'true';

            if (expanded) {
                text.textContent = text.dataset.short;
                btn.setAttribute('aria-expanded', 'false');
                btn.innerHTML = 'Read full review <span aria-hidden="true">&rarr;</span>';
            } else {
                text.textContent = text.dataset.full;
                btn.setAttribute('aria-expanded', 'true');
                btn.innerHTML = 'Show less <span aria-hidden="true">&uarr;</span>';
            }
        });
    });
</script>
@endpush

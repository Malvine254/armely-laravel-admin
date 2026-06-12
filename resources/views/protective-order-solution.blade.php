@extends('layouts.public')

@section('title', 'Armely - Protective Order Digital Intake and Workflow Automation')
@section('meta_description', 'A secure digital intake, review, workflow, and reporting solution for county protective order programs, built with Microsoft Power Platform and governed data controls.')
@section('meta_keywords', 'protective order automation, county digital intake, Power Pages, Power Apps, Power Automate, Power BI, public sector workflow, Armely')
@section('canonical_url', url('/solutions/protective-order-digitization'))

@push('head')
<meta property="og:title" content="Armely - Protective Order Digital Intake and Workflow Automation">
<meta property="og:description" content="Modernize protective order intake, review, document generation, and reporting with a secure county-ready digital solution.">
<meta property="og:url" content="{{ url('/solutions/protective-order-digitization') }}">
<meta name="twitter:title" content="Armely - Protective Order Digital Intake and Workflow Automation">
<meta name="twitter:description" content="A secure digital workflow solution for county protective order programs.">
@endpush

@push('styles')
<style>
.protective-order-page {
    --po-blue: #2f5597;
    --po-blue-dark: #1e3a6d;
    --po-ink: #160a46;
    --po-text: #283650;
    --po-muted: #66758f;
    --po-line: #dfe7f5;
    --po-soft: #f5f8fd;
    --po-soft-2: #eef4ff;
    --po-accent: #f4a51c;
    --po-success: #2e7d62;
    color: var(--po-text);
    background: #ffffff;
}

.protective-order-page a {
    text-decoration: none;
}

.po-container {
    width: min(1180px, calc(100% - 40px));
    margin: 0 auto;
}

.po-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    color: var(--po-blue);
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin-bottom: 18px;
}

.po-eyebrow i {
    color: var(--po-accent);
}

.po-hero {
    background: var(--po-soft);
    overflow: hidden;
}

.po-hero-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(420px, 0.88fr);
    min-height: 560px;
    align-items: stretch;
}

.po-hero-copy {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 86px 64px 86px 0;
}

.po-hero h1 {
    color: var(--po-ink);
    font-size: clamp(2.4rem, 4.5vw, 4.3rem);
    line-height: 1.08;
    font-weight: 800;
    letter-spacing: -0.04em;
    margin: 0 0 22px;
}

.po-hero h1 span {
    color: var(--po-blue);
}

.po-hero-lede {
    color: var(--po-ink);
    font-size: clamp(1.15rem, 2vw, 1.55rem);
    line-height: 1.55;
    margin: 0 0 20px;
    max-width: 680px;
}

.po-hero-text {
    color: var(--po-muted);
    font-size: 1rem;
    line-height: 1.78;
    margin: 0 0 30px;
    max-width: 650px;
}

.po-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    align-items: center;
}

.po-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    min-height: 48px;
    padding: 12px 22px;
    border: 1px solid transparent;
    border-radius: 0;
    font-weight: 800;
    transition: transform .2s ease, box-shadow .2s ease, background .2s ease, color .2s ease;
}

.po-btn:hover {
    transform: translateY(-2px);
}

.po-btn-primary {
    color: #ffffff;
    background: var(--po-blue);
    box-shadow: 0 14px 28px rgba(47, 85, 151, 0.18);
}

.po-btn-primary:hover {
    color: #ffffff;
    background: var(--po-blue-dark);
}

.po-btn-secondary {
    color: var(--po-blue-dark);
    border-color: var(--po-blue-dark);
    background: transparent;
}

.po-btn-secondary:hover {
    color: #ffffff;
    background: var(--po-blue-dark);
}

.po-hero-visual {
    position: relative;
    min-height: 560px;
    background: linear-gradient(135deg, #11264c 0%, #2f5597 58%, #6e8cc4 100%);
    overflow: hidden;
}

.po-hero-visual::before,
.po-hero-visual::after {
    content: "";
    position: absolute;
    pointer-events: none;
}

.po-hero-visual::before {
    inset: 0;
    background:
        radial-gradient(circle at 72% 18%, rgba(255,255,255,0.24), transparent 28%),
        linear-gradient(90deg, rgba(255,255,255,0.09) 1px, transparent 1px),
        linear-gradient(0deg, rgba(255,255,255,0.09) 1px, transparent 1px);
    background-size: auto, 58px 58px, 58px 58px;
}

.po-hero-visual::after {
    left: -1px;
    bottom: -70px;
    width: 240px;
    height: 260px;
    background: linear-gradient(180deg, #f4a51c, #2f5597);
    clip-path: polygon(0 0, 70% 0, 70% 100%, 38% 100%, 38% 34%, 0 34%);
    opacity: .95;
}

.po-dashboard-card {
    position: absolute;
    left: 54px;
    right: 54px;
    top: 82px;
    background: #ffffff;
    border: 1px solid rgba(255,255,255,0.38);
    box-shadow: 0 30px 70px rgba(7, 22, 52, 0.28);
}

.po-dashboard-top {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    align-items: center;
    padding: 18px 20px;
    border-bottom: 1px solid var(--po-line);
}

.po-dashboard-title {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--po-ink);
    font-weight: 900;
}

.po-dashboard-title i {
    color: var(--po-blue);
}

.po-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 7px 11px;
    color: var(--po-success);
    background: #eaf7f2;
    font-size: .78rem;
    font-weight: 800;
}

.po-dashboard-body {
    padding: 22px;
}

.po-progress-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 22px;
}

.po-progress-step {
    min-height: 86px;
    padding: 14px;
    background: var(--po-soft);
    border: 1px solid var(--po-line);
}

.po-progress-step span {
    display: block;
    color: var(--po-blue);
    font-size: .72rem;
    font-weight: 900;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.po-progress-step strong {
    color: var(--po-ink);
    font-size: .9rem;
    line-height: 1.35;
}

.po-form-preview {
    display: grid;
    gap: 10px;
    padding: 16px;
    background: #fbfcff;
    border: 1px solid var(--po-line);
}

.po-form-line {
    height: 10px;
    background: #dbe6f7;
}

.po-form-line:nth-child(1) {
    width: 38%;
    background: var(--po-blue);
}

.po-form-line:nth-child(2) {
    width: 84%;
}

.po-form-line:nth-child(3) {
    width: 72%;
}

.po-form-line:nth-child(4) {
    width: 92%;
}

.po-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 10px;
}

.po-form-field {
    height: 38px;
    border: 1px solid var(--po-line);
    background: #ffffff;
}

.po-floating-card {
    position: absolute;
    right: 32px;
    bottom: 54px;
    width: 240px;
    padding: 20px;
    background: #ffffff;
    box-shadow: 0 20px 44px rgba(7, 22, 52, 0.24);
}

.po-floating-card i {
    color: var(--po-accent);
    font-size: 1.35rem;
    margin-bottom: 12px;
}

.po-floating-card strong {
    display: block;
    color: var(--po-ink);
    font-size: 1rem;
    line-height: 1.35;
    margin-bottom: 8px;
}

.po-floating-card span {
    color: var(--po-muted);
    font-size: .88rem;
    line-height: 1.55;
}

.po-proof {
    padding: 22px 0;
    background: #ffffff;
    border-bottom: 1px solid var(--po-line);
}

.po-proof-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
}

.po-proof-item {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    color: var(--po-muted);
    font-size: .9rem;
    line-height: 1.55;
}

.po-proof-item i {
    color: var(--po-blue);
    margin-top: 4px;
}

.po-proof-item strong {
    display: block;
    color: var(--po-ink);
    font-size: .96rem;
}

.po-section {
    padding: 84px 0;
}

.po-section-soft {
    background: var(--po-soft);
}

.po-section-dark {
    background: linear-gradient(135deg, #160a46, #1e3a6d 58%, #2f5597);
    color: #ffffff;
}

.po-section-header {
    margin-bottom: 40px;
}

.po-heading {
    color: var(--po-ink);
    font-size: clamp(1.9rem, 3vw, 3rem);
    line-height: 1.16;
    font-weight: 800;
    letter-spacing: -0.03em;
    margin: 0 0 16px;
    max-width: 820px;
}

.po-sub {
    color: var(--po-muted);
    font-size: 1rem;
    line-height: 1.78;
    max-width: 760px;
    margin: 0;
}

.po-section-dark .po-heading,
.po-section-dark h2,
.po-section-dark h3,
.po-section-dark h4 {
    color: #ffffff;
}

.po-section-dark .po-sub,
.po-section-dark p,
.po-section-dark li {
    color: rgba(255,255,255,0.78);
}

.po-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 54px;
    align-items: center;
}

.po-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 24px;
}

.po-card {
    height: 100%;
    background: #ffffff;
    border: 1px solid var(--po-line);
    padding: 28px;
    box-shadow: 0 10px 26px rgba(31, 64, 121, 0.06);
}

.po-card-icon {
    width: 52px;
    height: 52px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--po-soft-2);
    color: var(--po-blue);
    font-size: 1.25rem;
    margin-bottom: 18px;
}

.po-card h3,
.po-feature h3,
.po-step h3,
.po-persona h3 {
    color: var(--po-ink);
    font-size: 1.1rem;
    font-weight: 900;
    line-height: 1.35;
    margin: 0 0 10px;
}

.po-card p,
.po-feature p,
.po-step p,
.po-persona p {
    color: var(--po-muted);
    font-size: .95rem;
    line-height: 1.72;
    margin: 0;
}

.po-image-panel {
    min-height: 390px;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #e9f1ff, #ffffff);
    border: 1px solid var(--po-line);
}

.po-image-panel::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        linear-gradient(90deg, rgba(47,85,151,.08) 1px, transparent 1px),
        linear-gradient(0deg, rgba(47,85,151,.08) 1px, transparent 1px);
    background-size: 44px 44px;
}

.po-panel-content {
    position: relative;
    z-index: 1;
    padding: 34px;
}

.po-panel-window {
    background: #ffffff;
    border: 1px solid var(--po-line);
    box-shadow: 0 20px 44px rgba(31, 64, 121, 0.12);
    margin-top: 24px;
}

.po-panel-bar {
    display: flex;
    gap: 6px;
    padding: 13px;
    border-bottom: 1px solid var(--po-line);
}

.po-panel-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: #ccd7e8;
}

.po-panel-body {
    padding: 20px;
    display: grid;
    gap: 12px;
}

.po-panel-row {
    display: grid;
    grid-template-columns: 120px 1fr 80px;
    gap: 12px;
    align-items: center;
}

.po-panel-row span {
    height: 12px;
    background: #dce7f7;
}

.po-panel-row span:nth-child(3) {
    background: #eef4ff;
}

.po-feature-grid {
    display: grid;
    grid-template-columns: 1.1fr 1fr;
    gap: 28px;
    align-items: start;
}

.po-feature-large {
    grid-row: span 2;
}

.po-feature {
    background: #ffffff;
    border: 1px solid var(--po-line);
}

.po-feature-visual {
    min-height: 155px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #dce9ff, #ffffff);
    border-bottom: 1px solid var(--po-line);
    color: var(--po-blue);
    font-size: 2.1rem;
}

.po-feature-large .po-feature-visual {
    min-height: 330px;
    font-size: 3rem;
}

.po-feature-body {
    padding: 26px;
}

.po-list {
    list-style: none;
    padding: 0;
    margin: 22px 0 0;
    display: grid;
    gap: 12px;
}

.po-list li {
    position: relative;
    padding-left: 26px;
    color: var(--po-muted);
    line-height: 1.65;
}

.po-list li::before {
    content: "\f00c";
    position: absolute;
    left: 0;
    top: 2px;
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    color: var(--po-blue);
    font-size: .82rem;
}

.po-workflow {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 16px;
}

.po-step {
    position: relative;
    padding: 24px;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.16);
    min-height: 210px;
}

.po-step-number {
    color: var(--po-accent);
    font-size: .8rem;
    font-weight: 900;
    letter-spacing: .08em;
    text-transform: uppercase;
    margin-bottom: 12px;
}

.po-step i {
    color: #ffffff;
    font-size: 1.35rem;
    margin-bottom: 18px;
}

.po-step p {
    color: rgba(255,255,255,0.78);
}

.po-tech-grid {
    display: grid;
    grid-template-columns: 0.95fr 1.05fr;
    gap: 44px;
    align-items: start;
}

.po-stack-card {
    padding: 30px;
    background: #ffffff;
    border: 1px solid var(--po-line);
    box-shadow: 0 14px 34px rgba(31, 64, 121, 0.08);
}

.po-stack-row {
    display: grid;
    grid-template-columns: 52px 1fr;
    gap: 16px;
    align-items: flex-start;
    padding: 18px 0;
    border-bottom: 1px solid var(--po-line);
}

.po-stack-row:last-child {
    border-bottom: 0;
}

.po-stack-row i {
    width: 52px;
    height: 52px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--po-soft-2);
    color: var(--po-blue);
}

.po-stack-row h3 {
    margin: 0 0 5px;
    color: var(--po-ink);
    font-size: 1.02rem;
    font-weight: 900;
}

.po-stack-row p {
    margin: 0;
    color: var(--po-muted);
    line-height: 1.65;
    font-size: .93rem;
}

.po-report-card {
    background: var(--po-blue-dark);
    color: #ffffff;
    padding: 34px;
    min-height: 100%;
}

.po-report-card h3 {
    color: #ffffff;
    font-size: 1.55rem;
    line-height: 1.25;
    margin: 0 0 14px;
}

.po-report-card p {
    color: rgba(255,255,255,0.76);
    line-height: 1.72;
    margin: 0 0 24px;
}

.po-metric-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}

.po-metric {
    padding: 18px;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12);
}

.po-metric strong {
    display: block;
    color: #ffffff;
    font-size: 1.5rem;
    line-height: 1;
    margin-bottom: 8px;
}

.po-metric span {
    color: rgba(255,255,255,0.76);
    font-size: .9rem;
    line-height: 1.45;
}

.po-persona-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 16px;
}

.po-persona {
    padding: 24px;
    background: #ffffff;
    border: 1px solid var(--po-line);
}

.po-persona i {
    color: var(--po-blue);
    font-size: 1.3rem;
    margin-bottom: 15px;
}

.po-final-cta {
    text-align: center;
}

.po-final-cta .po-heading,
.po-final-cta .po-sub {
    margin-left: auto;
    margin-right: auto;
}

@media (max-width: 1180px) {
    .po-workflow {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .po-persona-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 991px) {
    .po-hero-grid,
    .po-grid-2,
    .po-feature-grid,
    .po-tech-grid {
        grid-template-columns: 1fr;
    }

    .po-hero-copy {
        padding: 70px 0 42px;
    }

    .po-hero-visual {
        min-height: 520px;
    }

    .po-proof-grid,
    .po-grid-3 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 700px) {
    .po-container {
        width: min(100% - 24px, 1180px);
    }

    .po-section {
        padding: 60px 0;
    }

    .po-proof-grid,
    .po-grid-3,
    .po-workflow,
    .po-persona-grid,
    .po-progress-row,
    .po-metric-grid {
        grid-template-columns: 1fr;
    }

    .po-dashboard-card {
        left: 18px;
        right: 18px;
        top: 40px;
    }

    .po-floating-card {
        left: 18px;
        right: 18px;
        width: auto;
        bottom: 28px;
    }

    .po-actions {
        align-items: stretch;
    }

    .po-btn {
        width: 100%;
    }
}
</style>
@endpush

@section('content')
<div class="protective-order-page">
    <section class="po-hero">
        <div class="po-container po-hero-grid">
            <div class="po-hero-copy">
                <div class="po-eyebrow"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Public sector solution</div>
                <h1>Protective Order <span>Digital Intake</span> and Workflow Automation</h1>
                <p class="po-hero-lede">A secure, guided solution that helps counties move protective order requests from paper-based intake to digital submission, review, routing, document generation, and reporting.</p>
                <p class="po-hero-text">Armely helps county justice teams modernize the protective order process while keeping sensitive information governed, role-based, auditable, and easier for authorized staff to manage.</p>
                <div class="po-actions">
                    <a href="{{ route('contact') }}" class="po-btn po-btn-primary">Discuss the solution <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="#solution" class="po-btn po-btn-secondary">See what is included</a>
                </div>
            </div>

            <div class="po-hero-visual" aria-label="Protective order workflow illustration">
                <div class="po-dashboard-card">
                    <div class="po-dashboard-top">
                        <div class="po-dashboard-title"><i class="fa-solid fa-gauge-high" aria-hidden="true"></i> Petition Review Dashboard</div>
                        <div class="po-status-pill"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Secure workflow</div>
                    </div>
                    <div class="po-dashboard-body">
                        <div class="po-progress-row">
                            <div class="po-progress-step"><span>Step 01</span><strong>Public intake</strong></div>
                            <div class="po-progress-step"><span>Step 02</span><strong>Eligibility triage</strong></div>
                            <div class="po-progress-step"><span>Step 03</span><strong>Internal review</strong></div>
                            <div class="po-progress-step"><span>Step 04</span><strong>Court update</strong></div>
                        </div>
                        <div class="po-form-preview" aria-hidden="true">
                            <div class="po-form-line"></div>
                            <div class="po-form-line"></div>
                            <div class="po-form-line"></div>
                            <div class="po-form-line"></div>
                            <div class="po-form-grid">
                                <div class="po-form-field"></div>
                                <div class="po-form-field"></div>
                                <div class="po-form-field"></div>
                                <div class="po-form-field"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="po-floating-card">
                    <i class="fa-solid fa-lock" aria-hidden="true"></i>
                    <strong>Sensitive data stays governed</strong>
                    <span>Role-based access, secure storage, audit-friendly workflows, and reporting for authorized teams.</span>
                </div>
            </div>
        </div>
    </section>

    <div class="po-proof">
        <div class="po-container po-proof-grid">
            <div class="po-proof-item"><i class="fa-solid fa-language" aria-hidden="true"></i><span><strong>Guided public intake</strong> Multi-language questionnaire options for residents.</span></div>
            <div class="po-proof-item"><i class="fa-solid fa-route" aria-hidden="true"></i><span><strong>Automated routing</strong> Next steps are determined by petition responses.</span></div>
            <div class="po-proof-item"><i class="fa-solid fa-user-shield" aria-hidden="true"></i><span><strong>Role-based review</strong> Separate experiences for advocates, attorneys, clerks, and leadership.</span></div>
            <div class="po-proof-item"><i class="fa-solid fa-chart-simple" aria-hidden="true"></i><span><strong>Operational reporting</strong> Power BI visibility into submissions, status, and outcomes.</span></div>
        </div>
    </div>

    <section class="po-section" id="solution">
        <div class="po-container po-grid-2">
            <div class="po-image-panel" aria-label="Digital form and review queue illustration">
                <div class="po-panel-content">
                    <div class="po-eyebrow"><i class="fa-solid fa-file-signature" aria-hidden="true"></i> Modern county service delivery</div>
                    <h2 class="po-heading">Make the protective order process easier to start, easier to review, and easier to manage.</h2>
                    <div class="po-panel-window">
                        <div class="po-panel-bar"><span class="po-panel-dot"></span><span class="po-panel-dot"></span><span class="po-panel-dot"></span></div>
                        <div class="po-panel-body">
                            <div class="po-panel-row"><span></span><span></span><span></span></div>
                            <div class="po-panel-row"><span></span><span></span><span></span></div>
                            <div class="po-panel-row"><span></span><span></span><span></span></div>
                            <div class="po-panel-row"><span></span><span></span><span></span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="po-eyebrow"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Why this matters</div>
                <h2 class="po-heading">Counties need a process that is accessible, consistent, and ready for sensitive case workflows.</h2>
                <p class="po-sub">Paper-based protective order intake can create friction for residents, staff, advocates, attorneys, court clerks, and leadership teams. A digital workflow helps counties standardize intake, improve internal coordination, and make case status easier to track without exposing private information unnecessarily.</p>
                <ul class="po-list">
                    <li>Public-facing intake forms that can be accessed from a county website.</li>
                    <li>Guided questions that help determine whether a petition should continue through the workflow.</li>
                    <li>Internal dashboards for authorized staff to view, update, review, and attach supporting documentation.</li>
                    <li>Document templates that support a consistent petition packet and court-ready handoff.</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="po-section po-section-soft">
        <div class="po-container">
            <div class="po-section-header">
                <div class="po-eyebrow"><i class="fa-solid fa-layer-group" aria-hidden="true"></i> Solution components</div>
                <h2 class="po-heading">A complete digital workflow from first question to operational reporting.</h2>
                <p class="po-sub">The solution is designed as a modular county platform, so each county can adapt the process to its policies, review paths, forms, roles, and reporting needs.</p>
            </div>

            <div class="po-feature-grid">
                <article class="po-feature po-feature-large">
                    <div class="po-feature-visual"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i></div>
                    <div class="po-feature-body">
                        <h3>Guided protective order questionnaire</h3>
                        <p>A public intake experience that collects petition information in a structured way, supports multiple language options, and guides residents through the questions needed to determine the correct next step.</p>
                    </div>
                </article>

                <article class="po-feature">
                    <div class="po-feature-visual"><i class="fa-solid fa-diagram-project" aria-hidden="true"></i></div>
                    <div class="po-feature-body">
                        <h3>Workflow automation</h3>
                        <p>Automated routing based on petition answers, with notifications and status changes for the right internal team members.</p>
                    </div>
                </article>

                <article class="po-feature">
                    <div class="po-feature-visual"><i class="fa-solid fa-table-columns" aria-hidden="true"></i></div>
                    <div class="po-feature-body">
                        <h3>Internal review dashboards</h3>
                        <p>Secure Power Apps dashboards for staff to review submissions, update status, add notes, attach files, and move the petition forward.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="po-section">
        <div class="po-container">
            <div class="po-section-header">
                <div class="po-eyebrow"><i class="fa-solid fa-star" aria-hidden="true"></i> Key features</div>
                <h2 class="po-heading">Built around the realities of county protective order operations.</h2>
            </div>

            <div class="po-grid-3">
                <article class="po-card">
                    <div class="po-card-icon"><i class="fa-solid fa-globe" aria-hidden="true"></i></div>
                    <h3>Accessible public portal</h3>
                    <p>Provide a public web entry point that residents can access from approved county channels without needing to begin with a paper packet.</p>
                </article>

                <article class="po-card">
                    <div class="po-card-icon"><i class="fa-solid fa-language" aria-hidden="true"></i></div>
                    <h3>Multi-language support</h3>
                    <p>Support English, Spanish, and additional language options to make the process more inclusive for residents who need assistance.</p>
                </article>

                <article class="po-card">
                    <div class="po-card-icon"><i class="fa-solid fa-database" aria-hidden="true"></i></div>
                    <h3>Secure data storage</h3>
                    <p>Store petition data in a secure database with encryption, role-based access, backups, and governance controls aligned to county requirements.</p>
                </article>

                <article class="po-card">
                    <div class="po-card-icon"><i class="fa-solid fa-users-gear" aria-hidden="true"></i></div>
                    <h3>Role-specific experiences</h3>
                    <p>Create focused review paths for advocates, attorneys, court clerks, supervisors, and administrative users.</p>
                </article>

                <article class="po-card">
                    <div class="po-card-icon"><i class="fa-solid fa-file-word" aria-hidden="true"></i></div>
                    <h3>Form templates</h3>
                    <p>Generate standardized templates such as temporary protective order packets and petitioner statements for review and court handoff.</p>
                </article>

                <article class="po-card">
                    <div class="po-card-icon"><i class="fa-solid fa-comments" aria-hidden="true"></i></div>
                    <h3>Feedback mechanism</h3>
                    <p>Collect feedback from users and staff to improve the workflow, reduce friction, and support continuous improvement.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="po-section po-section-dark" id="workflow">
        <div class="po-container">
            <div class="po-section-header">
                <div class="po-eyebrow"><i class="fa-solid fa-route" aria-hidden="true"></i> Workflow</div>
                <h2 class="po-heading">A governed process that routes each petition to the right review path.</h2>
                <p class="po-sub">The workflow starts after form submission and then moves through eligibility checks, internal review, advocate review, attorney preparation, court updates, and reporting.</p>
            </div>

            <div class="po-workflow">
                <article class="po-step">
                    <div class="po-step-number">01</div>
                    <i class="fa-solid fa-user-pen" aria-hidden="true"></i>
                    <h3>Resident intake</h3>
                    <p>The petitioner completes a guided questionnaire through the public portal.</p>
                </article>

                <article class="po-step">
                    <div class="po-step-number">02</div>
                    <i class="fa-solid fa-filter-circle-dollar" aria-hidden="true"></i>
                    <h3>Eligibility triage</h3>
                    <p>Responses help determine whether the request should continue, stop, or be redirected.</p>
                </article>

                <article class="po-step">
                    <div class="po-step-number">03</div>
                    <i class="fa-solid fa-user-group" aria-hidden="true"></i>
                    <h3>Advocate review</h3>
                    <p>Advocates receive notifications, review submissions, and request corrections when needed.</p>
                </article>

                <article class="po-step">
                    <div class="po-step-number">04</div>
                    <i class="fa-solid fa-scale-balanced" aria-hidden="true"></i>
                    <h3>Attorney review</h3>
                    <p>Assigned attorneys review approved petitions and prepare court-facing documents.</p>
                </article>

                <article class="po-step">
                    <div class="po-step-number">05</div>
                    <i class="fa-solid fa-building-columns" aria-hidden="true"></i>
                    <h3>Court update</h3>
                    <p>Court staff update petition status after court decisions and close the workflow when complete.</p>
                </article>

                <article class="po-step">
                    <div class="po-step-number">06</div>
                    <i class="fa-solid fa-chart-pie" aria-hidden="true"></i>
                    <h3>Reporting</h3>
                    <p>Leadership monitors volume, status, outcomes, and operational trends in Power BI.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="po-section po-section-soft">
        <div class="po-container po-tech-grid">
            <div>
                <div class="po-eyebrow"><i class="fa-solid fa-microchip" aria-hidden="true"></i> Technology foundation</div>
                <h2 class="po-heading">Designed for Microsoft environments and county governance needs.</h2>
                <p class="po-sub">Armely can implement the solution using Microsoft Power Platform components with secure data storage, controlled access, and reporting that fits each county's operational model.</p>
            </div>

            <div class="po-stack-card">
                <div class="po-stack-row">
                    <i class="fa-solid fa-window-maximize" aria-hidden="true"></i>
                    <div><h3>Power Pages public portal</h3><p>Secure public-facing questionnaire experience hosted through county-approved channels.</p></div>
                </div>
                <div class="po-stack-row">
                    <i class="fa-solid fa-mobile-screen-button" aria-hidden="true"></i>
                    <div><h3>Power Apps staff dashboards</h3><p>Internal dashboards for authorized personnel to review, update, attach, and manage petition records.</p></div>
                </div>
                <div class="po-stack-row">
                    <i class="fa-solid fa-arrows-spin" aria-hidden="true"></i>
                    <div><h3>Power Automate workflows</h3><p>Notifications, status transitions, review routing, and handoffs between internal roles.</p></div>
                </div>
                <div class="po-stack-row">
                    <i class="fa-solid fa-chart-column" aria-hidden="true"></i>
                    <div><h3>Power BI reporting</h3><p>Dashboards for submitted petitions, successful submissions, status counts, voided records, and demographic trends.</p></div>
                </div>
                <div class="po-stack-row">
                    <i class="fa-solid fa-server" aria-hidden="true"></i>
                    <div><h3>Secure county data layer</h3><p>Encrypted storage, backups, administrative access controls, and environment security review.</p></div>
                </div>
            </div>
        </div>
    </section>

    <section class="po-section">
        <div class="po-container po-grid-2">
            <div class="po-report-card">
                <h3>Reporting for decision makers</h3>
                <p>Move beyond one-off status checks. Give leadership visibility into process volume, queue status, form outcomes, and common trends without exposing data to unauthorized users.</p>
                <div class="po-metric-grid">
                    <div class="po-metric"><strong>Volume</strong><span>Petitions submitted by date range.</span></div>
                    <div class="po-metric"><strong>Status</strong><span>Open, processing, corrected, voided, and completed records.</span></div>
                    <div class="po-metric"><strong>Outcomes</strong><span>Successful submissions and petitions that did not qualify.</span></div>
                    <div class="po-metric"><strong>Trends</strong><span>Age groups, case types, locations, and other approved measures.</span></div>
                </div>
            </div>

            <div>
                <div class="po-eyebrow"><i class="fa-solid fa-people-arrows" aria-hidden="true"></i> County team alignment</div>
                <h2 class="po-heading">One solution, multiple stakeholder experiences.</h2>
                <p class="po-sub">The solution separates the petitioner experience from internal review work. Each role sees the information and actions needed for their part of the process.</p>
                <ul class="po-list">
                    <li>Petitioners get a guided digital questionnaire and clear instructions.</li>
                    <li>Advocates can review, approve, or request corrections.</li>
                    <li>Attorneys can prepare and print the appropriate court-facing documents.</li>
                    <li>Court clerks can update case status after court decisions.</li>
                    <li>Leadership can use dashboards to monitor process health and capacity.</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="po-section po-section-soft">
        <div class="po-container">
            <div class="po-section-header">
                <div class="po-eyebrow"><i class="fa-solid fa-users" aria-hidden="true"></i> Experiences</div>
                <h2 class="po-heading">Designed for the people involved in the process.</h2>
            </div>

            <div class="po-persona-grid">
                <article class="po-persona">
                    <i class="fa-solid fa-person" aria-hidden="true"></i>
                    <h3>Resident</h3>
                    <p>Starts a request through a guided, accessible public questionnaire.</p>
                </article>
                <article class="po-persona">
                    <i class="fa-solid fa-hands-holding-circle" aria-hidden="true"></i>
                    <h3>Advocate</h3>
                    <p>Reviews incoming submissions and helps identify corrections.</p>
                </article>
                <article class="po-persona">
                    <i class="fa-solid fa-scale-balanced" aria-hidden="true"></i>
                    <h3>Attorney</h3>
                    <p>Reviews approved petitions and prepares documents for court.</p>
                </article>
                <article class="po-persona">
                    <i class="fa-solid fa-building-columns" aria-hidden="true"></i>
                    <h3>Court clerk</h3>
                    <p>Updates petition status after court decisions and closes records.</p>
                </article>
                <article class="po-persona">
                    <i class="fa-solid fa-chart-line" aria-hidden="true"></i>
                    <h3>Leadership</h3>
                    <p>Uses reporting to understand volume, outcomes, and process trends.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="po-section po-section-dark po-final-cta" id="contact">
        <div class="po-container">
            <div class="po-eyebrow"><i class="fa-solid fa-handshake" aria-hidden="true"></i> Bring this to your county</div>
            <h2 class="po-heading">Modernize protective order intake with a secure digital workflow.</h2>
            <p class="po-sub">Armely can help your county assess current intake, map the review workflow, design the public portal, configure internal dashboards, automate routing, and deliver reporting for decision makers.</p>
            <div class="po-actions" style="justify-content: center; margin-top: 30px;">
                <a href="{{ route('contact') }}" class="po-btn po-btn-primary" style="background: #ffffff; color: var(--po-blue);">Schedule a consultation <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                <a href="mailto:info@armely.com" class="po-btn po-btn-secondary" style="border-color: rgba(255,255,255,.45); color: #ffffff;">Email Armely</a>
            </div>
        </div>
    </section>
</div>
@endsection

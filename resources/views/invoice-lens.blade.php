@extends('layouts.public')

@section('title', 'Invoice Lens | Armely')
@section('meta_description', 'Invoice Lens from Armely gives Enverus OpenInvoice operators real-time visibility into pending invoices before they reach AP and ERP systems.')
@section('meta_keywords', 'Invoice Lens, OpenInvoice, Enverus OpenInvoice, AP visibility, invoice search, oil and gas finance, Armely')
@section('canonical_url', url('/invoice-lens'))

@push('head')
<meta property="og:title" content="Invoice Lens | Armely">
<meta property="og:description" content="Real-time invoice visibility for Enverus OpenInvoice operators, deployed inside your environment by Armely.">
<meta property="og:url" content="{{ url('/invoice-lens') }}">
<meta name="twitter:title" content="Invoice Lens | Armely">
<meta name="twitter:description" content="See pending OpenInvoice spend before it reaches your ERP or AP system.">
@endpush

@push('styles')
<style>
.invoice-lens-page {
    --il-blue: #2f5597;
    --il-blue-dark: #1e3a6d;
    --il-ink: #18233f;
    --il-muted: #5f6f89;
    --il-line: #dfe8f6;
    --il-soft: #f4f8ff;
    --il-accent: #f4a51c;
    color: var(--il-ink);
    background: #ffffff;
}

.invoice-lens-page a {
    text-decoration: none;
}

.il-container {
    width: min(1120px, calc(100% - 32px));
    margin: 0 auto;
}

.il-hero {
    position: relative;
    overflow: hidden;
    padding: 92px 0 70px;
    background: linear-gradient(135deg, #1e3a6d 0%, #2f5597 56%, #5f83c3 100%);
}

.il-hero::before {
    content: "";
    position: absolute;
    inset: auto -12% -45% auto;
    width: 620px;
    height: 620px;
    background: radial-gradient(circle, rgba(255,255,255,0.13), transparent 68%);
    pointer-events: none;
}

.il-hero-grid {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: minmax(0, 1fr) 440px;
    gap: 52px;
    align-items: center;
}

.il-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 14px;
    border: 1px solid rgba(255,255,255,0.22);
    border-radius: 999px;
    background: rgba(255,255,255,0.12);
    color: rgba(255,255,255,0.9);
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0;
    margin-bottom: 22px;
}

.il-hero h1 {
    color: #ffffff;
    font-size: clamp(2.25rem, 4vw, 3.5rem);
    line-height: 1.12;
    font-weight: 800;
    margin: 0 0 20px;
    letter-spacing: 0;
}

.il-hero h1 span {
    color: #ffd166;
}

.il-hero p {
    color: rgba(255,255,255,0.88);
    font-size: 1.08rem;
    line-height: 1.8;
    margin: 0 0 28px;
    max-width: 610px;
}

.il-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    align-items: center;
}

.il-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 48px;
    padding: 12px 22px;
    border-radius: 8px;
    font-weight: 700;
    transition: transform .2s ease, box-shadow .2s ease, background .2s ease, color .2s ease;
}

.il-btn:hover {
    transform: translateY(-2px);
}

.il-btn-primary {
    background: #ffffff;
    color: var(--il-blue);
    box-shadow: 0 14px 28px rgba(17, 39, 83, 0.22);
}

.il-btn-primary:hover {
    color: var(--il-blue-dark);
}

.il-btn-secondary {
    color: #ffffff;
    border: 1px solid rgba(255,255,255,0.35);
}

.il-btn-secondary:hover {
    color: #ffffff;
    background: rgba(255,255,255,0.12);
}

.il-pipeline-card {
    background: #ffffff;
    border: 1px solid rgba(255,255,255,0.35);
    border-radius: 8px;
    padding: 28px;
    box-shadow: 0 24px 60px rgba(10, 24, 55, 0.28);
}

.il-card-label {
    color: var(--il-muted);
    font-size: 0.78rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0;
    margin-bottom: 18px;
}

.il-pipeline {
    display: grid;
    grid-template-columns: 1fr 28px 1fr 28px 1fr;
    align-items: center;
    margin-bottom: 20px;
}

.il-node {
    min-height: 72px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 10px;
    border-radius: 8px;
    background: var(--il-soft);
    color: var(--il-blue-dark);
    font-size: 0.86rem;
    font-weight: 700;
    line-height: 1.35;
}

.il-node-muted {
    background: #f2f5fa;
    color: #4c5a70;
}

.il-arrow {
    text-align: center;
    color: #98a8c2;
    font-weight: 800;
}

.il-warning,
.il-lens-note {
    border-radius: 8px;
    padding: 14px 16px;
    font-size: 0.88rem;
    line-height: 1.65;
}

.il-warning {
    border: 1px dashed #f4bd55;
    background: #fff8e8;
    color: #75500d;
    margin-bottom: 12px;
}

.il-warning strong {
    display: block;
    color: #a9442c;
    margin-bottom: 4px;
}

.il-lens-note {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    border: 1px solid #cddcf3;
    background: #eef4ff;
    color: var(--il-blue-dark);
    font-weight: 700;
}

.il-lens-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: var(--il-blue);
    margin-top: 8px;
    flex: 0 0 auto;
}

.il-proof {
    background: #172b52;
    color: rgba(255,255,255,0.74);
    padding: 18px 0;
    font-size: 0.92rem;
}

.il-proof .il-container {
    display: flex;
    justify-content: center;
    gap: 18px;
    flex-wrap: wrap;
    text-align: center;
}

.il-proof strong {
    color: #ffffff;
}

.il-section {
    padding: 76px 0;
}

.il-section-soft {
    background: var(--il-soft);
}

.il-section-blue {
    background: linear-gradient(135deg, #1e3a6d, #2f5597);
    color: #ffffff;
}

.il-label {
    color: var(--il-blue);
    font-size: 0.78rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0;
    margin-bottom: 10px;
}

.il-section-blue .il-label {
    color: #ffd166;
}

.il-heading {
    color: var(--il-ink);
    font-size: clamp(1.8rem, 3vw, 2.45rem);
    line-height: 1.22;
    font-weight: 800;
    margin: 0 0 14px;
    max-width: 760px;
    letter-spacing: 0;
}

.il-section-blue .il-heading,
.il-section-blue h3,
.il-section-blue h4 {
    color: #ffffff;
}

.il-sub {
    color: var(--il-muted);
    font-size: 1rem;
    line-height: 1.78;
    max-width: 690px;
    margin: 0 0 36px;
}

.il-section-blue .il-sub,
.il-section-blue p {
    color: rgba(255,255,255,0.78);
}

.il-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 22px;
}

.il-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 34px;
    align-items: center;
}

.il-card {
    height: 100%;
    background: #ffffff;
    border: 1px solid var(--il-line);
    border-radius: 8px;
    padding: 28px;
    box-shadow: 0 8px 24px rgba(31, 64, 121, 0.06);
}

.il-card-icon {
    width: 48px;
    height: 48px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: #eaf2ff;
    color: var(--il-blue);
    font-size: 1.2rem;
    margin-bottom: 18px;
}

.il-card h3,
.il-step h3,
.il-price-card h3 {
    color: var(--il-ink);
    font-size: 1.08rem;
    font-weight: 800;
    line-height: 1.4;
    margin: 0 0 10px;
}

.il-card p,
.il-step p,
.il-price-card p,
.il-faq p {
    color: var(--il-muted);
    font-size: 0.94rem;
    line-height: 1.72;
    margin: 0;
}

.il-step {
    border-left: 3px solid #cddcf3;
    padding-left: 22px;
}

.il-step-num {
    color: var(--il-blue);
    font-size: 0.76rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0;
    margin-bottom: 9px;
}

.il-case-list {
    display: grid;
    gap: 14px;
}

.il-case {
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 8px;
    background: rgba(255,255,255,0.08);
    padding: 20px;
}

.il-case-label {
    color: #ffd166;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0;
    margin-bottom: 7px;
}

.il-pricing {
    align-items: stretch;
}

.il-price-card {
    position: relative;
    border: 1px solid var(--il-line);
    border-radius: 8px;
    padding: 30px;
    background: #ffffff;
    height: 100%;
}

.il-price-card-featured {
    border: 2px solid var(--il-blue);
    box-shadow: 0 16px 38px rgba(47, 85, 151, 0.14);
}

.il-pill {
    position: absolute;
    top: -14px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--il-blue);
    color: #ffffff;
    border-radius: 999px;
    padding: 5px 14px;
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    white-space: nowrap;
}

.il-price {
    color: var(--il-blue);
    font-size: 2.15rem;
    font-weight: 800;
    line-height: 1;
    margin: 18px 0 8px;
}

.il-price span {
    color: var(--il-muted);
    font-size: 1rem;
    font-weight: 500;
}

.il-list {
    list-style: none;
    margin: 22px 0 26px;
    padding: 0;
    display: grid;
    gap: 10px;
}

.il-list li {
    color: #33435f;
    font-size: 0.92rem;
    line-height: 1.55;
    padding-left: 22px;
    position: relative;
}

.il-list li::before {
    content: "\f00c";
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    color: var(--il-blue);
    position: absolute;
    left: 0;
    top: 1px;
    font-size: 0.78rem;
}

.il-price-note {
    color: var(--il-muted);
    text-align: center;
    font-size: 0.9rem;
    margin: 22px 0 0;
}

.il-faq {
    max-width: 780px;
}

.il-faq-item {
    border-bottom: 1px solid var(--il-line);
    padding: 22px 0;
}

.il-faq h3 {
    color: var(--il-ink);
    font-size: 1rem;
    font-weight: 800;
    margin: 0 0 8px;
}

.il-final-cta {
    text-align: center;
}

.il-final-cta .il-heading,
.il-final-cta .il-sub {
    margin-left: auto;
    margin-right: auto;
}

@media (max-width: 991px) {
    .il-hero-grid,
    .il-grid-2 {
        grid-template-columns: 1fr;
    }

    .il-hero-grid {
        gap: 34px;
    }

    .il-grid-3 {
        grid-template-columns: 1fr;
    }

    .il-hero {
        padding: 70px 0 54px;
    }
}

@media (max-width: 575px) {
    .il-container {
        width: min(100% - 24px, 1120px);
    }

    .il-section {
        padding: 56px 0;
    }

    .il-pipeline-card,
    .il-card,
    .il-price-card {
        padding: 22px;
    }

    .il-pipeline {
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .il-arrow {
        transform: rotate(90deg);
    }

    .il-actions {
        align-items: stretch;
    }

    .il-btn {
        width: 100%;
    }
}
</style>
@endpush

@section('content')
<div class="invoice-lens-page">
    <section class="il-hero">
        <div class="il-container il-hero-grid">
            <div>
                <div class="il-eyebrow"><i class="fa-solid fa-file-invoice-dollar" aria-hidden="true"></i> Built for Enverus OpenInvoice operators</div>
                <h1>See every invoice <span>before</span> it hits your AP system</h1>
                <p>Invoice Lens bridges the gap between your OpenInvoice approval queue and your ERP, giving finance teams full visibility into pending invoices in real time without moving invoice data outside your network.</p>
                <div class="il-actions">
                    <a href="mailto:invoicelens@armely.com" class="il-btn il-btn-primary">Book a demo <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="#how" class="il-btn il-btn-secondary">How it works</a>
                </div>
            </div>

            <div class="il-pipeline-card" aria-label="Invoice Lens pipeline overview">
                <div class="il-card-label">Your invoice pipeline today</div>
                <div class="il-pipeline">
                    <div class="il-node il-node-muted">Vendor<br>submits</div>
                    <div class="il-arrow">></div>
                    <div class="il-node">OpenInvoice<br>queue</div>
                    <div class="il-arrow">></div>
                    <div class="il-node il-node-muted">ERP /<br>AP system</div>
                </div>
                <div class="il-warning">
                    <strong>Blind spot: days to weeks</strong>
                    Spend is already committed, but AP and finance teams often cannot see it until the invoice posts downstream.
                </div>
                <div class="il-lens-note">
                    <span class="il-lens-dot" aria-hidden="true"></span>
                    <span>Invoice Lens surfaces everything in this gap live, searchable, and inside your environment.</span>
                </div>
            </div>
        </div>
    </section>

    <div class="il-proof">
        <div class="il-container">
            <span>Purpose-built for <strong>E&P and midstream operators</strong></span>
            <span><strong>Invoice data stays in your network</strong></span>
            <span>Deployed and live in <strong>under one day</strong></span>
        </div>
    </div>

    <section class="il-section il-section-soft">
        <div class="il-container">
            <div class="il-label">The problem</div>
            <h2 class="il-heading">OpenInvoice is powerful, but the approval queue creates a finance visibility gap.</h2>
            <p class="il-sub">Invoices can sit in workflow for days or weeks before posting to the ERP. That leaves finance teams making close, cash, and accrual decisions without seeing committed spend.</p>
            <div class="il-grid-3">
                <article class="il-card">
                    <div class="il-card-icon"><i class="fa-solid fa-calendar-days" aria-hidden="true"></i></div>
                    <h3>Month-end surprises</h3>
                    <p>Invoices approved in OpenInvoice may not appear in your ERP until after books are closed and accrual assumptions are already set.</p>
                </article>
                <article class="il-card">
                    <div class="il-card-icon"><i class="fa-solid fa-chart-line" aria-hidden="true"></i></div>
                    <h3>Cash flow blind spots</h3>
                    <p>Large AFE-coded invoices can land without warning, making it harder to plan cash around spend that is already in motion.</p>
                </article>
                <article class="il-card">
                    <div class="il-card-icon"><i class="fa-solid fa-repeat" aria-hidden="true"></i></div>
                    <h3>Duplicate work and disputes</h3>
                    <p>Without queue visibility, teams lose time chasing invoice copies, confirming status, and resolving issues that should be visible instantly.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="il-section" id="how">
        <div class="il-container">
            <div class="il-label">How it works</div>
            <h2 class="il-heading">Deployed in your environment. Connected to OpenInvoice. Live in a day.</h2>
            <p class="il-sub">Invoice Lens runs inside your infrastructure and connects to Enverus OpenInvoice through the standard API. Your team gets a direct view into pending invoices without a separate external data store.</p>
            <div class="il-grid-3">
                <div class="il-step">
                    <div class="il-step-num">Step 01</div>
                    <h3>Deploy inside your environment</h3>
                    <p>Install on your on-premise or private cloud infrastructure so IT and security stay in control.</p>
                </div>
                <div class="il-step">
                    <div class="il-step-num">Step 02</div>
                    <h3>Connect to OpenInvoice via API</h3>
                    <p>Authenticate with your OpenInvoice instance and pull pending invoice metadata and document previews dynamically.</p>
                </div>
                <div class="il-step">
                    <div class="il-step-num">Step 03</div>
                    <h3>Give finance instant visibility</h3>
                    <p>Search, view, and filter invoices by vendor, AFE number, cost center, amount, date, or invoice identifier.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="il-section il-section-soft" id="features">
        <div class="il-container">
            <div class="il-label">Features</div>
            <h2 class="il-heading">Everything AP needs to see pending invoice exposure clearly.</h2>
            <p class="il-sub">Designed for oil and gas finance teams that need accurate, searchable OpenInvoice visibility before invoices reach downstream systems.</p>
            <div class="il-grid-3">
                <article class="il-card">
                    <div class="il-card-icon"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></div>
                    <h3>Full-text invoice search</h3>
                    <p>Find pending invoices by invoice number, vendor, AFE, company, cost center, or invoice ID.</p>
                </article>
                <article class="il-card">
                    <div class="il-card-icon"><i class="fa-solid fa-file-pdf" aria-hidden="true"></i></div>
                    <h3>Live document previews</h3>
                    <p>Preview invoice documents pulled directly from OpenInvoice so teams stop chasing copies.</p>
                </article>
                <article class="il-card">
                    <div class="il-card-icon"><i class="fa-solid fa-table-cells" aria-hidden="true"></i></div>
                    <h3>AFE and cost center visibility</h3>
                    <p>Filter committed spend by the coding structures finance teams already use for close and budget review.</p>
                </article>
                <article class="il-card">
                    <div class="il-card-icon"><i class="fa-solid fa-list-check" aria-hidden="true"></i></div>
                    <h3>Approval status tracking</h3>
                    <p>See where each invoice sits in workflow without logging into multiple systems.</p>
                </article>
                <article class="il-card">
                    <div class="il-card-icon"><i class="fa-solid fa-lock" aria-hidden="true"></i></div>
                    <h3>Data stays local</h3>
                    <p>Invoice Lens is deployed in your infrastructure, supporting strict data residency and security expectations.</p>
                </article>
                <article class="il-card">
                    <div class="il-card-icon"><i class="fa-solid fa-bolt" aria-hidden="true"></i></div>
                    <h3>Real-time data pull</h3>
                    <p>No stale snapshots or delayed batch exports. The view reflects what is in the OpenInvoice queue now.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="il-section il-section-blue">
        <div class="il-container il-grid-2">
            <div>
                <div class="il-label">Built by Armely</div>
                <h2 class="il-heading">Deep oil and gas experience, backed by modern data and AI delivery.</h2>
                <p class="il-sub">Armely helps energy organizations modernize financial operations, data platforms, and workflow visibility. Invoice Lens turns that field experience into a focused product for OpenInvoice operators.</p>
                <a href="{{ route('contact') }}" class="btn btn-primary">Talk with Armely</a>
            </div>
            <div class="il-case-list">
                <article class="il-case">
                    <div class="il-case-label">Energy operations</div>
                    <h4>Sage Butte OpenInvoice integration</h4>
                    <p>Improved visibility into the OpenInvoice pipeline and reduced month-end uncertainty.</p>
                </article>
                <article class="il-case">
                    <div class="il-case-label">Oil and gas</div>
                    <h4>Northwoods Energy platform modernization</h4>
                    <p>Replaced spreadsheet-based tracking with a live data solution for operational clarity.</p>
                </article>
                <article class="il-case">
                    <div class="il-case-label">Track record</div>
                    <h4>Data, AI, and digital transformation delivery</h4>
                    <p>Built with the same practical delivery discipline Armely brings to client transformation work.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="il-section" id="pricing">
        <div class="il-container">
            <h2 class="il-heading">Simple pricing for different operator sizes.</h2>
            <p class="il-sub">All plans include deployment support and onboarding. No per-invoice fees or surprise overages.</p>
            <div class="il-grid-3 il-pricing">
                <article class="il-price-card">
                    <h3>Starter</h3>
                    <p>Small operators, up to 3 AP users.</p>
                    <div class="il-price">$399 <span>/ mo</span></div>
                    <p>Billed annually.</p>
                    <ul class="il-list">
                        <li>Invoice search</li>
                        <li>Live PDF previews</li>
                        <li>Up to 3 users</li>
                        <li>Standard email support</li>
                    </ul>
                    <a href="mailto:invoicelens@armely.com" class="il-btn il-btn-secondary" style="color: var(--il-blue); border-color: var(--il-line);">Get started</a>
                </article>
                <article class="il-price-card il-price-card-featured">
                    <div class="il-pill">Most popular</div>
                    <h3>Professional</h3>
                    <p>Mid-size operators, up to 10 users.</p>
                    <div class="il-price">$1,199 <span>/ mo</span></div>
                    <p>Billed annually.</p>
                    <ul class="il-list">
                        <li>Everything in Starter</li>
                        <li>Approval workflow tracking</li>
                        <li>Multi-company support</li>
                        <li>Audit trail and reporting</li>
                    </ul>
                    <a href="mailto:invoicelens@armely.com" class="il-btn il-btn-primary" style="background: var(--il-blue); color: #fff;">Book a demo</a>
                </article>
                <article class="il-price-card">
                    <h3>Enterprise</h3>
                    <p>Large operators and MSP teams.</p>
                    <div class="il-price" style="font-size: 1.7rem;">Custom</div>
                    <p>Annual contract with volume pricing.</p>
                    <ul class="il-list">
                        <li>Everything in Professional</li>
                        <li>Unlimited users</li>
                        <li>Custom coding rules</li>
                        <li>ERP and BI connector options</li>
                    </ul>
                    <a href="mailto:invoicelens@armely.com" class="il-btn il-btn-secondary" style="color: var(--il-blue); border-color: var(--il-line);">Talk to sales</a>
                </article>
            </div>
            <p class="il-price-note">Onboarding and deployment fees vary by environment and integration scope.</p>
        </div>
    </section>

    <section class="il-section il-section-soft" id="faq">
        <div class="il-container">
            <div class="il-label">FAQ</div>
            <h2 class="il-heading">Common questions</h2>
            <div class="il-faq">
                <div class="il-faq-item">
                    <h3>Do we need to be an Enverus OpenInvoice customer?</h3>
                    <p>Yes. Invoice Lens is designed for organizations already using Enverus OpenInvoice.</p>
                </div>
                <div class="il-faq-item">
                    <h3>Where does invoice data go?</h3>
                    <p>Invoice Lens runs inside your environment. Invoice data is pulled from OpenInvoice and displayed within your infrastructure.</p>
                </div>
                <div class="il-faq-item">
                    <h3>Does this replace OpenInvoice?</h3>
                    <p>No. It is a companion visibility layer. Vendors and approvers continue using OpenInvoice as they do today.</p>
                </div>
                <div class="il-faq-item">
                    <h3>How long does deployment take?</h3>
                    <p>Most standard deployments can be completed within one business day with the right access and environment details ready.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="il-section il-section-blue il-final-cta" id="demo">
        <div class="il-container">
            <div class="il-label">Book a demo</div>
            <h2 class="il-heading">Stop closing the month with invoices you did not see coming.</h2>
            <p class="il-sub">Schedule a 30-minute walkthrough and see how Invoice Lens can surface the OpenInvoice queue before it reaches your AP system.</p>
            <a href="mailto:invoicelens@armely.com" class="il-btn il-btn-primary">Book a demo <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </section>
</div>
@endsection

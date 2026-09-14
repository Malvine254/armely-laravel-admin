@extends('layouts.public')

@section('title', 'Mela Security & Compliance Documentation | Armely')
@section('meta_description', 'Request Mela privacy policy, security documentation, and SOC 2 review materials for procurement and compliance review.')
@section('canonical_url', route('mela.security'))

@push('head')
<meta name="robots" content="index,follow">
<meta property="og:title" content="Mela Security & Compliance Documentation | Armely">
<meta property="og:description" content="Request Armely's Mela security, privacy, and SOC 2 documentation package for review.">
<meta property="og:url" content="{{ route('mela.security') }}">
@endpush

@push('styles')
@include('legal.partials.mela-policy-styles')
@endpush

@section('content')
<main class="mela-legal">
  <header class="mela-legal-hero">
    <div class="mela-legal-container">
      <span class="mela-legal-badge">Mela AI</span>
      <h1>Security &amp; Compliance Documentation</h1>
      <p>Request the Mela documentation package for procurement, privacy review, security diligence, and SOC 2 evaluation.</p>
      <div class="mela-legal-meta">
        <span>Privacy</span>
        <span>Security</span>
        <span>SOC 2 review</span>
      </div>
      <div class="mela-legal-links">
        <a href="{{ route('mela-meeting-assistant') }}">Mela Meeting Assistant</a>
        <a href="{{ route('mela.privacy') }}">Mela Privacy Policy</a>
        <a href="{{ route('mela.terms') }}">Mela Terms of Use</a>
      </div>
    </div>
  </header>

  <div class="mela-legal-body">
    <div class="mela-legal-container mela-legal-layout">
      <aside class="mela-legal-toc">
        <strong>On this page</strong>
        <div>
          <a href="#overview">1. Overview</a>
          <a href="#documentation">2. Documentation package</a>
          <a href="#process">3. How to request access</a>
          <a href="#contacts">4. Contact</a>
        </div>
      </aside>

      <article class="mela-legal-document">
        <section id="overview">
          <h2>1. Overview</h2>
          <p>Armely supports enterprise procurement and compliance review for Mela AI and Mela Meeting Assistant. If your organization is evaluating Mela for deployment, we can provide the documentation needed for privacy, security, and risk review.</p>
          <p>We maintain a privacy policy, product terms, and operational security practices designed to support customer diligence. For organizations with formal vendor review requirements, we can also provide a structured response package and discuss SOC 2 readiness and controls.</p>
        </section>

        <section id="documentation">
          <h2>2. Documentation package</h2>
          <p>Depending on the review scope and the customer’s requirements, the standard request package may include:</p>
          <ul>
            <li>Mela AI Privacy Policy and related legal terms</li>
            <li>Product and data-handling overview for Mela Meeting Assistant</li>
            <li>Security and operational controls summary</li>
            <li>Data retention and deletion information for customer data</li>
            <li>Support for procurement, IT security, and legal review questions</li>
            <li>SOC 2 readiness materials and discussion of current compliance posture</li>
          </ul>
          <div class="mela-legal-callout">
            <p>We tailor documentation based on the review process. Some organizations request a full policy packet, while others need a concise security overview and control response set.</p>
          </div>
        </section>

        <section id="process">
          <h2>3. How to request access</h2>
          <p>Use the contact form below and tell us which materials you need. Please include your organization name, your role, and any required review deadlines.</p>

          <div style="margin:24px 0; padding:20px 18px; border:1px solid #dce5f1; border-radius:12px; background:#f5f8fd;">
            <a href="{{ route('contact') }}?subject={{ urlencode('Mela Security & Compliance Documentation Request') }}" style="display:inline-flex; align-items:center; justify-content:center; min-height:46px; padding:0 18px; border-radius:8px; background:#2f5597; color:#fff; text-decoration:none; font-weight:700;">Request Mela documentation</a>
          </div>

          <p>Alternatively, email us directly at <a href="mailto:info@armely.com">info@armely.com</a> and include:</p>
          <ul>
            <li>Company name</li>
            <li>Your name and title</li>
            <li>Request type: privacy review, security review, or SOC 2 assessment</li>
            <li>Any required due date or vendor questionnaire information</li>
          </ul>
        </section>

        <section id="contacts">
          <h2>4. Contact</h2>
          <p>For Privacy, Security, and Compliance inquiries related to Mela:</p>
          <p><strong>Armely, LLC</strong><br>
            17400 Dallas Pkwy, Suite 111<br>
            Dallas, TX 75287<br>
            United States<br>
            <a href="mailto:info@armely.com">info@armely.com</a><br>
            <a href="tel:+19724600643">+1 972 460 0643</a>
          </p>
        </section>
      </article>
    </div>
  </div>
</main>
@endsection

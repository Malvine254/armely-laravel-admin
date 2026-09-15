@extends('layouts.public')

@section('title', 'Mela Meeting Assistant Security & Compliance Documentation | Armely')
@section('meta_description', 'Request the Mela Meeting Assistant user guide, privacy, security, and SOC 2 compliance documentation. A secure download link is emailed to you instantly.')
@section('canonical_url', route('mela.security'))

@push('head')
<meta name="robots" content="index,follow">
<meta property="og:title" content="Mela Meeting Assistant Security & Compliance Documentation | Armely">
<meta property="og:description" content="Request Armely's Mela Meeting Assistant user guide, security, privacy, and SOC 2 documentation package. Delivered by email instantly.">
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
      <h1>Mela Meeting Assistant Security &amp; Compliance Documentation</h1>
      <p>Request the Mela user guide, privacy, security, and SOC 2 documentation package. Submit the form below and we will instantly email you a secure download link.</p>
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
          <p>The Mela Meeting Assistant User Guide, Privacy, Security &amp; Compliance Overview is a single consolidated PDF. Depending on your review scope, it includes:</p>
          <ul>
            <li>Mela Meeting Assistant product and user guide, including Teams commands</li>
            <li>Privacy policy summary and data-handling overview</li>
            <li>Security, encryption, and operational controls summary</li>
            <li>AI governance and responsible-use guidance</li>
            <li>Data retention and deletion information for customer data</li>
            <li>SOC 2 readiness program status and current control scope</li>
            <li>Customer deployment security checklist and FAQ</li>
          </ul>
          <div class="mela-legal-callout">
            <p>Submitting the request form below will instantly email a secure, time-limited download link to the address you provide.</p>
          </div>
        </section>

        <section id="process">
          <h2>3. Request the documentation</h2>
          <p>Complete the form and we will immediately email you a secure download link for the full PDF. Your request is also logged so our team can follow up if you need additional materials.</p>

          <form id="mela-security-request-form" class="mela-legal-request-form" novalidate>
            @csrf
            <div class="mela-legal-request-message" role="status" aria-live="polite"></div>
            <div class="mela-legal-request-grid">
              <div class="mela-legal-request-row">
                <label for="mela-req-name">Full name *</label>
                <input type="text" id="mela-req-name" name="name" class="mela-legal-request-field" required maxlength="255">
              </div>
              <div class="mela-legal-request-row">
                <label for="mela-req-email">Work email *</label>
                <input type="email" id="mela-req-email" name="email" class="mela-legal-request-field" required maxlength="255">
              </div>
              <div class="mela-legal-request-row">
                <label for="mela-req-org">Organization</label>
                <input type="text" id="mela-req-org" name="organization" class="mela-legal-request-field" maxlength="255">
              </div>
              <div class="mela-legal-request-row">
                <label for="mela-req-title">Job title</label>
                <input type="text" id="mela-req-title" name="job_title" class="mela-legal-request-field" maxlength="255">
              </div>
              <div class="mela-legal-request-row">
                <label for="mela-req-phone">Phone</label>
                <input type="tel" id="mela-req-phone" name="phone" class="mela-legal-request-field" maxlength="50">
              </div>
              <div class="mela-legal-request-row mela-legal-request-row-full">
                <label for="mela-req-message">What are you evaluating? (optional)</label>
                <textarea id="mela-req-message" name="message" class="mela-legal-request-field mela-legal-request-textarea" maxlength="2000"></textarea>
              </div>
              <input type="text" name="website" class="mela-legal-request-honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">
              <div class="mela-legal-request-row mela-legal-request-row-full">
                @if(!empty($recaptchaSiteKey))
                  <div class="mela-security-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                @else
                  <p style="color:#991b1b;font-size:.85rem;">reCAPTCHA is not configured. Please contact support.</p>
                @endif
              </div>
              <div class="mela-legal-request-row mela-legal-request-row-full">
                <button type="submit" class="mela-legal-request-submit">Email me the documentation</button>
              </div>
            </div>
          </form>

          <p>Alternatively, email us directly at <a href="mailto:info@armely.com">info@armely.com</a> and include your organization name, role, and request type.</p>
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

@push('scripts')
<style>
.mela-legal-request-form { margin: 22px 0; padding: 22px; border: 1px solid #dce5f1; border-radius: 12px; background: #f5f8fd; }
.mela-legal-request-message { display: none; margin: 0 0 16px; padding: 12px 14px; border-radius: 10px; font-size: .9rem; line-height: 1.5; }
.mela-legal-request-message.is-success { display: block; background: rgba(34,197,94,.1); color: #166534; border: 1px solid rgba(34,197,94,.22); }
.mela-legal-request-message.is-error { display: block; background: rgba(239,68,68,.1); color: #991b1b; border: 1px solid rgba(239,68,68,.22); }
.mela-legal-request-grid { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 12px 14px; }
.mela-legal-request-row { display: flex; flex-direction: column; }
.mela-legal-request-row-full { grid-column: 1 / -1; }
.mela-legal-request-row label { font-size: .75rem; font-weight: 600; color: #6b7fa3; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 8px; }
.mela-legal-request-field { width: 100%; box-sizing: border-box; background: #fff; border: 1px solid rgba(41,78,139,.15); border-radius: 10px; padding: 12px 14px; font-family: 'Poppins', sans-serif; font-size: .875rem; color: #1a2540; min-height: 46px; }
.mela-legal-request-textarea { min-height: 110px; resize: vertical; }
.mela-legal-request-honeypot { position: absolute; left: -9999px; opacity: 0; }
.mela-legal-request-submit { display: inline-flex; align-items: center; justify-content: center; min-height: 48px; padding: 0 24px; border: 0; border-radius: 8px; background: #2f5597; color: #fff; font-weight: 700; font-size: .95rem; cursor: pointer; }
.mela-legal-request-submit:disabled { opacity: .65; cursor: not-allowed; }
</style>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
(function () {
    const form = document.getElementById('mela-security-request-form');
    if (!form) return;

    const submitUrl = @json(route('mela.security.request'));
    const csrfToken = @json(csrf_token());
    const siteKey = @json($recaptchaSiteKey ?? '');
    const messageEl = form.querySelector('.mela-legal-request-message');
    const submitBtn = form.querySelector('.mela-legal-request-submit');
    let widgetId = null;

    function renderWidget() {
        const el = form.querySelector('.mela-security-recaptcha');
        if (!el || typeof grecaptcha === 'undefined' || !grecaptcha.render || widgetId !== null) return false;
        widgetId = grecaptcha.render(el, { sitekey: siteKey });
        return true;
    }

    if (siteKey) {
        const timer = window.setInterval(function () {
            if (renderWidget()) window.clearInterval(timer);
        }, 300);
        window.setTimeout(function () { window.clearInterval(timer); }, 15000);
    }

    function showMessage(text, isError) {
        if (!messageEl) return;
        messageEl.className = 'mela-legal-request-message ' + (isError ? 'is-error' : 'is-success');
        messageEl.style.display = 'block';
        messageEl.textContent = text;
    }

    function firstError(errors) {
        if (!errors) return null;
        const key = Object.keys(errors)[0];
        return key ? (Array.isArray(errors[key]) ? errors[key][0] : errors[key]) : null;
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        if (messageEl) {
            messageEl.style.display = 'none';
            messageEl.textContent = '';
        }

        if (siteKey && (typeof grecaptcha === 'undefined' || !grecaptcha.getResponse(widgetId))) {
            showMessage('Please verify that you are not a robot.', true);
            return;
        }

        const formData = new FormData(form);
        if (siteKey) {
            formData.append('g-recaptcha-response', grecaptcha.getResponse(widgetId));
        }

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';
        }

        fetch(submitUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: formData,
        })
        .then(async function (response) {
            const data = await response.json().catch(function () { return {}; });
            if (!response.ok || data.ok === false) {
                throw new Error(data.message || firstError(data.errors) || 'An error occurred. Please try again.');
            }
            return data;
        })
        .then(function (data) {
            showMessage(data.message || 'Check your email for the secure download link.', false);
            form.reset();
            if (siteKey && widgetId !== null) grecaptcha.reset(widgetId);
        })
        .catch(function (error) {
            showMessage(error.message, true);
            if (siteKey && widgetId !== null) grecaptcha.reset(widgetId);
        })
        .finally(function () {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Email me the documentation';
            }
        });
    });
})();
</script>
@endpush
@endsection

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

          <form id="mela-security-request-form" class="service-contact-form service-contact-form--card" method="post" novalidate>
            @csrf
            <input type="hidden" name="website" value="">
            <p class="service-contact-message" role="alert" aria-live="polite"></p>

            <div class="service-contact-grid">
              <div class="service-contact-row">
                <label>Full Name *</label>
                <input class="service-contact-field" name="name" type="text" placeholder="Jane Smith" required maxlength="255">
              </div>
              <div class="service-contact-row">
                <label>Work Email *</label>
                <input class="service-contact-field" name="email" type="email" placeholder="jane@yourcompany.com" required maxlength="255">
              </div>
              <div class="service-contact-row">
                <label>Organization</label>
                <input class="service-contact-field" name="organization" type="text" placeholder="Acme Corp" maxlength="255">
              </div>
              <div class="service-contact-row">
                <label>Job Title</label>
                <input class="service-contact-field" name="job_title" type="text" placeholder="IT Security Manager" maxlength="255">
              </div>
              <div class="service-contact-row service-contact-row-full">
                <label>Phone</label>
                <input class="service-contact-field" name="phone" type="tel" inputmode="tel" pattern="^\+?[0-9][0-9\s().-]{6,19}$" placeholder="e.g. +1 (555) 123-4567" title="Enter a valid phone number" maxlength="50">
              </div>
              <div class="service-contact-row service-contact-row-full">
                <label>What are you evaluating? (optional)</label>
                <textarea class="service-contact-field service-contact-textarea" name="message" placeholder="Tell us about your review or deployment plans..." maxlength="2000"></textarea>
              </div>
              <div class="service-contact-row service-contact-row-full">
                <label>Confirm you are not a robot *</label>
                @if(!empty($recaptchaSiteKey))
                  <div class="service-contact-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                @else
                  <div class="service-contact-message is-error" style="display:block;">reCAPTCHA is not configured. Please contact support.</div>
                @endif
              </div>
              <div class="service-contact-row service-contact-row-full">
                <button type="submit" class="service-contact-submit">Email me the documentation</button>
                <p class="service-contact-note">Secure link delivered by email in seconds.</p>
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
/* Reuses the same visual design as partials/service-contact-form.blade.php so this page matches the rest of the site's lead forms. */
form.service-contact-form { display: block; width: 100%; }
form.service-contact-form.service-contact-form--card { background: #ffffff; border: 1px solid rgba(41, 78, 139, 0.12); border-radius: 16px; padding: 28px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06); margin: 22px 0; }
form.service-contact-form .service-contact-message { display: none; margin: 0 0 16px; padding: 12px 14px; border-radius: 10px; font-size: 0.9rem; line-height: 1.5; }
form.service-contact-form .service-contact-message.is-success { display: block; background: rgba(34, 197, 94, 0.1); color: #166534; border: 1px solid rgba(34, 197, 94, 0.22); }
form.service-contact-form .service-contact-message.is-error { display: block; background: rgba(239, 68, 68, 0.1); color: #991b1b; border: 1px solid rgba(239, 68, 68, 0.22); }
form.service-contact-form .service-contact-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px 14px; align-items: start; }
form.service-contact-form .service-contact-row { margin-bottom: 0; display: flex; flex-direction: column; }
form.service-contact-form .service-contact-row.service-contact-row-full { grid-column: 1 / -1; }
form.service-contact-form .service-contact-row label { display: block; font-size: 0.75rem; font-weight: 600; color: #6B7FA3; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px; }
form.service-contact-form .service-contact-field { width: 100%; display: block; background: #FFFFFF; border: 1px solid rgba(41, 78, 139, 0.15); border-radius: 10px; padding: 12px 14px; font-family: 'Poppins', sans-serif; font-size: 0.875rem; line-height: 1.45; color: #1A2540; outline: none; transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box; min-height: 48px; appearance: none; }
form.service-contact-form .service-contact-field:focus { border-color: rgba(41, 78, 139, 0.45); box-shadow: 0 0 0 3px rgba(41, 78, 139, 0.08); }
form.service-contact-form .service-contact-textarea { min-height: 132px; resize: vertical; }
form.service-contact-form .service-contact-recaptcha { min-height: 78px; }
form.service-contact-form .service-contact-submit { width: 100%; background: #294e8b; color: #fff; border: none; border-radius: 7px; padding: 14px 16px; margin-top: 10px; font-family: 'Poppins', sans-serif; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s, transform 0.15s; }
form.service-contact-form .service-contact-submit:hover { background: #3d6ab5; transform: translateY(-1px); }
form.service-contact-form .service-contact-submit:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
form.service-contact-form .service-contact-note { text-align: center; margin-top: 12px; font-size: 0.75rem; color: #6B7FA3; }
@media (max-width: 768px) {
    form.service-contact-form .service-contact-grid { grid-template-columns: 1fr; }
    form.service-contact-form .service-contact-row.service-contact-row-full { grid-column: auto; }
}
</style>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
(function () {
    const form = document.getElementById('mela-security-request-form');
    if (!form) return;

    const submitUrl = @json(route('mela.security.request'));
    const csrfToken = @json(csrf_token());
    const siteKey = @json($recaptchaSiteKey ?? '');
    const messageEl = form.querySelector('.service-contact-message');
    const submitBtn = form.querySelector('.service-contact-submit');
    let widgetId = null;

    function renderWidget() {
        const el = form.querySelector('.service-contact-recaptcha');
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
        messageEl.className = 'service-contact-message ' + (isError ? 'is-error' : 'is-success');
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

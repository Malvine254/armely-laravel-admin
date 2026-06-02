@extends('layouts.public')

@section('title', ($paper->title ?? 'White Paper') . ' | Microsoft Platform Guidance | Armely')
@section('meta_description', $metaDescription)

@push('head')
<meta property="og:type" content="article">
<meta property="og:title" content="{{ ($paper->title ?? 'White Paper') }} | Microsoft Platform Guidance | Armely">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ request()->url() }}">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ ($paper->title ?? 'White Paper') }} | Microsoft Platform Guidance | Armely">
<meta name="twitter:description" content="{{ $metaDescription }}">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/case-studies-modern.css') }}">
<style>
.white-paper-detail-page {
    --case-ink: #172033;
    --case-muted: #5f6f86;
    --case-line: #d9e4f2;
    --case-panel: #ffffff;
    --case-soft: #f5f8fc;
    --case-blue: #2f5597;
    --case-navy: #18345f;
    background: #fff;
    color: var(--case-ink);
}
.case-detail-hero {
    background:
        linear-gradient(90deg, rgba(47, 85, 151, .14) 0, rgba(47, 85, 151, 0) 36%),
        linear-gradient(180deg, #eef4fb 0%, #fff 100%);
    border-bottom: 1px solid var(--case-line);
    padding: 34px 0 46px;
}
.case-back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--case-navy);
    font-weight: 800;
    font-size: .92rem;
    margin-bottom: 24px;
}
.case-back-link:hover { color: var(--case-blue); text-decoration: none; }
.case-hero-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.24fr) minmax(300px, .76fr);
    gap: 34px;
    align-items: stretch;
}
.case-detail-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--case-navy);
    font-weight: 800;
    text-transform: uppercase;
    font-size: .78rem;
    letter-spacing: .08em;
    margin-bottom: 14px;
}
.case-detail-title {
    color: var(--case-ink);
    font-size: 3.05rem;
    line-height: 1.08;
    font-weight: 900;
    margin: 0 0 18px;
    max-width: 880px;
}
.case-detail-summary {
    color: var(--case-muted);
    font-size: 1.1rem;
    line-height: 1.8;
    max-width: 850px;
    margin-bottom: 22px;
}
.case-hero-actions { display: flex; flex-wrap: wrap; gap: 12px; }
.case-primary-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    min-height: 46px;
    padding: 12px 18px;
    font-weight: 800;
    text-decoration: none;
    border: 1px solid transparent;
    background: var(--case-blue);
    color: #fff;
}
.case-primary-btn:hover { color: #fff; background: var(--case-navy); text-decoration: none; }
.case-secondary-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    min-height: 46px;
    padding: 12px 18px;
    font-weight: 800;
    text-decoration: none;
    border: 1px solid var(--case-line);
    background: #fff;
    color: var(--case-navy);
}
.case-secondary-btn:hover { color: var(--case-blue); border-color: #b9cbe4; text-decoration: none; }
.case-hero-card {
    background: #fff;
    border: 1px solid var(--case-line);
    box-shadow: 0 20px 50px rgba(35, 62, 105, .12);
    display: flex;
    flex-direction: column;
    min-height: 100%;
}
.case-hero-visual {
    height: 214px;
    background: #e8eff8;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--case-blue);
    font-size: 3.6rem;
}
.case-hero-card-body { padding: 22px; }
.case-hero-card-title { color: var(--case-ink); font-weight: 900; font-size: 1.25rem; margin: 8px 0 10px; }
.case-hero-card-copy { color: var(--case-muted); line-height: 1.65; margin: 0; }
.case-detail-band { padding: 38px 0 72px; background: #fff; }
.case-detail-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.48fr) minmax(310px, .82fr);
    gap: 32px;
    align-items: start;
}
.case-content-card,
.case-lead-card,
.case-panel {
    border: 1px solid var(--case-line);
    background: var(--case-panel);
    box-shadow: 0 14px 34px rgba(28, 54, 93, .07);
}
.case-content-card { padding: 34px; }
.case-content-card h2,
.case-lead-card h3,
.case-panel h3 {
    color: var(--case-ink);
    font-weight: 900;
    line-height: 1.25;
    margin-bottom: 14px;
}
.case-detail-body { color: #3f4d63; line-height: 1.9; }
.case-detail-body h1,
.case-detail-body h2,
.case-detail-body h3 {
    color: var(--case-ink);
    font-weight: 900;
    margin-top: 1.2em;
    margin-bottom: .55em;
}
.case-detail-body p { margin-bottom: 1rem; }
.case-detail-body ul,
.case-detail-body ol { padding-left: 22px; margin-bottom: 1rem; }
.case-panel { padding: 24px; margin-top: 18px; }
.case-panel p { color: var(--case-muted); line-height: 1.8; margin: 0; }
.case-sidebar { position: sticky; top: 98px; }
.case-lead-card { padding: 24px; }
.case-lead-card p { color: var(--case-muted); }
.field-label {
    display: block;
    font-size: .88rem;
    font-weight: 800;
    color: var(--case-navy);
    margin-bottom: 8px;
}
.lead-field {
    width: 100%;
    border: 1px solid #c9d8eb;
    background: #f8fbff;
    min-height: 50px;
    padding: 0 14px;
    color: var(--case-ink);
    transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
}
.lead-field:focus {
    outline: none;
    border-color: var(--case-blue);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(47,85,151,.12);
}
.lead-form .form-group { margin-bottom: 15px; }
.lead-form .btn {
    width: 100%;
    min-height: 48px;
    border: 0;
    padding: 12px 18px;
    font-weight: 900;
}
.btn-content { display: inline-flex; align-items: center; justify-content: center; gap: 10px; }
.btn-loader {
    display: none;
    width: 14px;
    height: 14px;
    border: 2px solid rgba(255,255,255,.45);
    border-top-color: #fff;
    border-radius: 50%;
    animation: caseSpin .75s linear infinite;
}
.lead-form .btn.is-loading .btn-loader { display: inline-block; }
.case-form-status {
    display: none;
    margin-top: 12px;
    padding: 10px 12px;
    border: 1px solid transparent;
    border-radius: 10px;
    font-size: .9rem;
    font-weight: 600;
}
.case-form-status.is-success {
    display: block;
    color: #0f5132;
    background: #e8f9ef;
    border-color: #b7e7cc;
}
.case-form-status.is-error {
    display: block;
    color: #7a1f2a;
    background: #fdecef;
    border-color: #f2bec6;
}
.case-direct-download {
    display: none;
    margin-top: 12px;
    padding: 10px 12px;
    border-radius: 10px;
    border: 1px dashed #9eb5db;
    background: #f7fbff;
    color: #274879;
    font-size: .9rem;
}
.case-direct-download.is-visible { display: block; }
.case-direct-download a { font-weight: 700; color: #1f4d99; text-decoration: underline; }
@keyframes caseSpin { to { transform: rotate(360deg); } }
@media (max-width: 1199px) {
    .case-detail-title { font-size: 2.5rem; }
    .case-hero-layout,
    .case-detail-grid { grid-template-columns: 1fr; }
    .case-sidebar { position: static; }
}
@media (max-width: 767px) {
    .case-detail-title { font-size: 2rem; }
    .case-content-card,
    .case-lead-card,
    .case-panel,
    .case-hero-card-body { padding: 20px; }
}
</style>
@endpush

@section('content')
<div class="white-paper-detail-page">
    <section class="case-detail-hero">
        <div class="container">
            <a class="case-back-link" href="{{ route('case-studies.index') }}#white-papers">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                Back to White Papers
            </a>

            <div class="case-hero-layout">
                <div>
                    <div class="case-detail-eyebrow">
                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                        White Paper
                    </div>
                    <h1 class="case-detail-title">{{ $paper->title ?? 'White Paper' }}</h1>
                    <p class="case-detail-summary">{{ $paper->preview }}</p>

                    <div class="case-hero-actions">
                        <a class="case-primary-btn" href="#download">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                            Request Secure Download Link
                        </a>
                        <a class="case-secondary-btn" href="{{ route('resources.index') }}">
                            <i class="fa fa-book" aria-hidden="true"></i>
                            Browse Resources
                        </a>
                    </div>
                </div>

                <aside class="case-hero-card">
                    <div class="case-hero-visual" aria-hidden="true">
                        <i class="fa fa-file-pdf-o"></i>
                    </div>
                    <div class="case-hero-card-body">
                        <h2 class="case-hero-card-title">Full White Paper Access</h2>
                        <p class="case-hero-card-copy">Request a secure link to receive the complete white paper in your inbox with a time-limited download URL.</p>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="case-detail-band">
        <div class="container">
            <div class="case-detail-grid">
                <div>
                    <article class="case-content-card">
                        <h2>Overview</h2>
                        <div class="case-detail-body">
                            <p>{{ $paper->preview }}</p>
                            @if(!empty($paper->body))
                                <div>{!! $paper->body !!}</div>
                            @endif
                        </div>
                    </article>

                    <section class="case-panel" aria-labelledby="white-paper-note-heading">
                        <h3 id="white-paper-note-heading">What You Get In The Full Download</h3>
                        <p>The full white paper includes planning guidance, implementation recommendations, and governance checkpoints to help teams move from strategy to delivery with confidence.</p>
                    </section>
                </div>

                <aside class="case-sidebar" id="download">
                    <section class="case-lead-card">
                        <h3>Download Full White Paper</h3>
                        <p>Get the full document by email. No phone number required.</p>

                        <form class="lead-form" method="post" action="{{ route('case-studies.lead.submit') }}">
                            @csrf
                            <input type="hidden" name="interest" value="white-papers">
                            @if(!empty($paper->id))
                                <input type="hidden" name="white_paper_id" value="{{ $paper->id }}">
                            @endif
                            <input type="hidden" name="requested_resource" value="{{ $paper->title ?? 'White Paper' }}">
                            <input style="display:none;" type="text" name="website" class="honeypot">

                            <label class="field-label" for="white_paper_name">First name *</label>
                            <div class="form-group">
                                <input class="lead-field" id="white_paper_name" type="text" name="name" required>
                            </div>

                            <label class="field-label" for="white_paper_email">Work email *</label>
                            <div class="form-group">
                                <input class="lead-field" id="white_paper_email" type="email" name="email" required>
                            </div>

                            <label class="field-label" for="white_paper_company">Company</label>
                            <div class="form-group">
                                <input class="lead-field" id="white_paper_company" type="text" name="organization">
                            </div>

                            <label class="field-label" for="white_paper_job">Job title</label>
                            <div class="form-group">
                                <select class="lead-field" id="white_paper_job" name="job_title">
                                    <option value="">Select job title</option>
                                    <option>Executive leader</option>
                                    <option>Technology leader</option>
                                    <option>Data leader</option>
                                    <option>Operations leader</option>
                                    <option>Practitioner or analyst</option>
                                </select>
                            </div>

                            @if(!empty($recaptchaSiteKey))
                                <div class="form-group">
                                    <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                                </div>
                            @endif

                            <button class="btn default-background text-light" id="whitePaperLeadSubmitBtn" type="submit">
                                <span class="btn-content">
                                    <span class="btn-loader" aria-hidden="true"></span>
                                    <span id="whitePaperLeadSubmitText">Email Download Link</span>
                                </span>
                            </button>

                            <div class="case-form-status" id="whitePaperFormStatus" aria-live="polite"></div>
                            <div class="case-direct-download" id="whitePaperDirectDownload" aria-live="polite"></div>
                            <p class="mt-2 mb-0" style="font-size:.9rem; color: var(--case-muted);">We will send a secure download link to your work email. The link expires in 1 hour.</p>
                        </form>
                    </section>
                </aside>
            </div>
        </div>
    </section>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('.case-lead-card form.lead-form');
    if (!form) {
        return;
    }

    var submitBtn = document.getElementById('whitePaperLeadSubmitBtn');
    var submitText = document.getElementById('whitePaperLeadSubmitText');
    var formStatus = document.getElementById('whitePaperFormStatus');
    var directDownload = document.getElementById('whitePaperDirectDownload');

    var setSubmitting = function (isSubmitting) {
        if (!submitBtn) {
            return;
        }

        submitBtn.disabled = isSubmitting;
        submitBtn.classList.toggle('is-loading', isSubmitting);
        if (submitText) {
            submitText.textContent = isSubmitting ? 'Sending secure link...' : 'Email Download Link';
        }
    };

    var setFormStatus = function (message, type) {
        if (!formStatus) {
            return;
        }

        formStatus.className = 'case-form-status';
        formStatus.textContent = '';
        if (!message) {
            return;
        }

        formStatus.classList.add(type === 'success' ? 'is-success' : 'is-error');
        formStatus.textContent = message;
    };

    var setDirectDownload = function (url, expiresAt) {
        if (!directDownload) {
            return;
        }

        directDownload.className = 'case-direct-download';
        directDownload.innerHTML = '';

        if (!url) {
            return;
        }

        var expiresText = expiresAt ? (' Link expires at ' + expiresAt + '.') : '';
        directDownload.classList.add('is-visible');
        directDownload.innerHTML = 'Download now: <a href="' + url + '" target="_blank" rel="noopener noreferrer">Open secure file</a>.' + expiresText;
    };

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        setFormStatus('', '');
        setDirectDownload('', '');
        setSubmitting(true);

        var formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData,
            credentials: 'same-origin'
        }).then(function (response) {
            return response.json().catch(function () {
                return {};
            }).then(function (payload) {
                return { ok: response.ok, status: response.status, payload: payload };
            });
        }).then(function (result) {
            if (result.ok) {
                var emailSent = !(result.payload && result.payload.email_sent === false);
                var statusType = emailSent ? 'success' : 'error';
                setFormStatus(result.payload.message || 'Thanks! Your secure download link has been sent.', statusType);

                if (emailSent) {
                    setDirectDownload('', '');
                } else {
                    setDirectDownload(result.payload.download_url || '', result.payload.expires_at || '');
                }

                form.reset();
                if (typeof grecaptcha !== 'undefined' && grecaptcha.reset) {
                    grecaptcha.reset();
                }
                return;
            }

            var firstError = 'Unable to submit right now. Please try again.';
            if (result.payload && result.payload.errors) {
                for (var key in result.payload.errors) {
                    if (Object.prototype.hasOwnProperty.call(result.payload.errors, key)) {
                        firstError = result.payload.errors[key][0];
                        break;
                    }
                }
            } else if (result.payload && result.payload.message) {
                firstError = result.payload.message;
            }

            setFormStatus(firstError, 'error');
        }).catch(function () {
            setFormStatus('Unable to submit right now. Please check your connection and try again.', 'error');
        }).finally(function () {
            setSubmitting(false);
        });
    });
});
</script>
@endsection

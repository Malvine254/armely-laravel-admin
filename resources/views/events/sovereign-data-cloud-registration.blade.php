<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow,noarchive">
    <title>Request an Invitation | Sovereign Data Clouds with Snowflake</title>
    <style>
        :root{--navy:#0d233a;--blue:#00a1e0;--text:#2c3e50;--muted:#64748b;--line:#cbd5e1;--error:#be123c;--success:#166534}
        *{box-sizing:border-box}body{margin:0;min-height:100vh;padding:20px;display:grid;place-items:center;background:#f8fafc;color:var(--text);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif}
        .card{width:min(100%,760px);padding:30px 34px;background:#fff;border-top:6px solid var(--blue);border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,.08)}
        .badge{display:inline-block;margin-bottom:10px;padding:5px 11px;border-radius:20px;background:#e0f2fe;color:#0369a1;font-size:.75rem;font-weight:800;letter-spacing:.05em;text-transform:uppercase}
        h1{margin:0 0 7px;color:var(--navy);font-size:1.6rem;line-height:1.25}.subtitle{margin:0 0 9px;color:#475569;font-size:.92rem}.meta{display:flex;flex-wrap:wrap;gap:15px;margin-bottom:14px;color:var(--muted);font-size:.82rem}
        .note,.alert{margin-bottom:17px;padding:11px 15px;border-radius:6px;font-size:.81rem;line-height:1.42}.note{border-left:4px solid var(--navy);background:#f1f5f9;color:#475569}.error{border:1px solid #fecdd3;background:#ffe4e6;color:#9f1239}.success{border:1px solid #bbf7d0;background:#dcfce7;color:var(--success)}
        .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));column-gap:18px}.group{margin-bottom:15px}.full-row{grid-column:1/-1}label{display:block;margin-bottom:6px;color:var(--navy);font-size:.84rem;font-weight:650}.required{color:var(--error)}
        input,select{width:100%;padding:10px 13px;border:1px solid var(--line);border-radius:6px;background:#fff;font:inherit;font-size:.9rem;color:inherit}input:focus,select:focus{outline:0;border-color:var(--blue);box-shadow:0 0 0 3px rgba(0,161,224,.15)}input.is-invalid,select.is-invalid{border-color:var(--error);box-shadow:0 0 0 2px rgba(190,18,60,.1)}input.is-valid,select.is-valid{border-color:#16a34a}
        .hint{margin-top:5px;color:#718096;font-size:.76rem}.invalid{margin-top:5px;color:var(--error);font-size:.8rem}.submit{width:100%;margin-top:10px;padding:14px;border:0;border-radius:6px;background:var(--navy);color:#fff;font-size:1rem;font-weight:750;cursor:pointer}.submit:hover{background:#081626}.trap{position:absolute!important;left:-10000px!important;width:1px!important;height:1px!important;overflow:hidden!important}
        @media(max-width:640px){.card{padding:26px 20px}.form-grid{grid-template-columns:1fr}h1{font-size:1.45rem}}
    </style>
</head>
<body>
<main class="card">
    <span class="badge">Invite-Only Executive Briefing</span>
    <h1>{{ $eventName ?? 'Sovereign Data Clouds with Snowflake' }}</h1>
    <p class="subtitle">{{ $event ? \Illuminate\Support\Str::limit(strip_tags($event->body ?? ''), 180) : 'Ensuring Compliance, Security, and Isolation in Regulated Industries' }}</p>
    <div class="meta">
        <span><strong>Format:</strong> 45-Min Architecture Review</span>
        <span><strong>Platform:</strong> MS Teams Webinar</span>
        @if($eventDate)<span><strong>Date:</strong> {{ $eventDate }}</span>@endif
    </div>
    <div class="note"><strong>Access Note:</strong> Due to the interactive nature of our architecture briefings and dedicated workshop environments, attendance is strictly limited to verified Public Sector and Enterprise Technology Leaders. </div>

    @if(session('success'))
        <div class="alert success" role="status">{{ session('success') }}</div>
    @else
        @if($errors->any())
            <div class="alert error" role="alert">
                <strong>Please correct the following:</strong>
                <ul>
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form id="eventRegistrationForm" action="{{ $formAction ?? route('events.sovereign-data-cloud.register.store') }}" method="post" novalidate>
            @csrf
            <div class="trap" aria-hidden="true">
                <label for="website">Website</label>
                <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
            </div>
            <div class="form-grid">
                <div class="group">
                    <label for="full_name">First &amp; Last Name <span class="required">*</span></label>
                    <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" placeholder="e.g. Sarah Jenkins" maxlength="150" autocomplete="name" required>
                    @error('full_name')<div class="invalid">{{ $message }}</div>@enderror
                </div>
                <div class="group">
                    <label for="work_email">Organization Email Address <span class="required">*</span></label>
                    <input id="work_email" name="work_email" type="email" value="{{ old('work_email') }}" placeholder="sjenkins@organization.gov" maxlength="255" autocomplete="email" required>
                    <div class="hint">Organization or government email only.</div>
                    @error('work_email')<div class="invalid">{{ $message }}</div>@enderror
                </div>
                <div class="group">
                    <label for="organization">Organization / Agency <span class="required">*</span></label>
                    <input id="organization" name="organization" type="text" value="{{ old('organization') }}" placeholder="e.g. Company XYZ" maxlength="200" autocomplete="organization" required>
                    @error('organization')<div class="invalid">{{ $message }}</div>@enderror
                </div>
                <div class="group">
                    <label for="job_title">Job Title / Role <span class="required">*</span></label>
                    <select id="job_title" name="job_title" required>
                        <option value="" disabled @selected(!old('job_title'))>Select your current role...</option>
                        @foreach(['CIO / CTO','CISO / IT Security Director','Chief Data Officer / Director of Analytics','IT Director / Infrastructure Manager','Enterprise / Data Architect','Data Engineer / Technical Lead','Other Technology Leader'] as $role)
                            <option value="{{ $role }}" @selected(old('job_title') === $role)>{{ $role }}</option>
                        @endforeach
                    </select>
                    @error('job_title')<div class="invalid">{{ $message }}</div>@enderror
                </div>
                <div class="group {{ empty($showEventSelector) ? 'full-row' : '' }}">
                    <label for="compliance_focus">Regulatory / Governance Priority</label>
                    <select id="compliance_focus" name="compliance_focus">
                        <option value="">Select area of focus (optional)</option>
                        @foreach(['CJIS Compliance & Law Enforcement Data','FedRAMP / Sovereign Cloud Boundaries','HIPAA / PHI Data Isolation','Zero-Trust & Multi-Agency Data Sharing','General Cloud Modernization'] as $priority)
                            <option value="{{ $priority }}" @selected(old('compliance_focus') === $priority)>{{ $priority }}</option>
                        @endforeach
                    </select>
                </div>
                @if(!empty($showEventSelector))
                    <div class="group">
                        <label for="event_id">Upcoming Event <span class="required">*</span></label>
                        <select id="event_id" name="event_id" required>
                            @forelse($activeEvents ?? [] as $activeEvent)
                                <option value="{{ $activeEvent->id }}" @selected((string) old('event_id', $defaultEventId ?? '') === (string) $activeEvent->id)>
                                    {{ $activeEvent->title }} 
                            @empty
                                <option value="" selected disabled>No upcoming private events available</option>
                            @endforelse
                        </select>
                        <div class="hint">The nearest upcoming event is selected automatically.</div>
                        @error('event_id')<div class="invalid">{{ $message }}</div>@enderror
                    </div>
                @endif
                <div class="group full-row">
                    @if(config('services.recaptcha.site_key'))
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                    @else
                        <div class="invalid">reCAPTCHA is not configured. Please contact support.</div>
                    @endif
                    @error('captcha')<div class="invalid">{{ $message }}</div>@enderror
                    @error('g-recaptcha-response')<div class="invalid">{{ $message }}</div>@enderror
                </div>
            </div>
            <button class="submit" type="submit">Request Invitation</button>
        </form>
    @endif
</main>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
(() => {
    const form = document.getElementById('eventRegistrationForm');
    if (!form) return;

    const button = form.querySelector('.submit');
    const personalDomains = new Set([
        'aol.com','comcast.net','gmail.com','googlemail.com','hotmail.com','icloud.com',
        'live.com','mail.com','me.com','msn.com','outlook.com','proton.me',
        'protonmail.com','yahoo.com','ymail.com','zoho.com'
    ]);

    const messages = {
        full_name: 'Please enter your first and last name.',
        work_email: 'Please enter a valid company email address.',
        organization: 'Please enter your organization or agency.',
        job_title: 'Please select your job title or role.'
    };

    function errorElement(field) {
        let error = field.parentElement.querySelector('.invalid[data-live-error]');
        if (!error) {
            error = document.createElement('div');
            error.className = 'invalid';
            error.dataset.liveError = 'true';
            field.insertAdjacentElement('afterend', error);
        }
        return error;
    }

    function setFieldState(field, message = '') {
        const error = errorElement(field);
        field.classList.toggle('is-invalid', Boolean(message));
        field.classList.toggle('is-valid', !message && Boolean(field.value.trim()));
        field.setAttribute('aria-invalid', message ? 'true' : 'false');
        error.textContent = message;
        return !message;
    }

    function validateField(field) {
        const value = field.value.trim();
        let message = '';

        if (field.required && !value) {
            message = messages[field.name] || 'This field is required.';
        } else if (field.name === 'full_name' && value && value.split(/\s+/).length < 2) {
            message = 'Please enter both your first and last name.';
        } else if (field.name === 'work_email' && value) {
            const emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            const domain = value.includes('@') ? value.split('@').pop().toLowerCase() : '';
            if (!emailValid) message = messages.work_email;
            else if (personalDomains.has(domain)) message = 'Personal email addresses are not allowed. Please use your company email.';
        }

        return setFieldState(field, message);
    }

    form.querySelectorAll('.form-grid input, .form-grid select').forEach(field => {
        field.addEventListener(field.tagName === 'SELECT' ? 'change' : 'input', () => validateField(field));
        field.addEventListener('blur', () => validateField(field));
    });

    function showFormMessage(message, success) {
        let alert = form.parentElement.querySelector('#ajaxRegistrationMessage');
        if (!alert) {
            alert = document.createElement('div');
            alert.id = 'ajaxRegistrationMessage';
            form.insertAdjacentElement('beforebegin', alert);
        }
        alert.className = `alert ${success ? 'success' : 'error'}`;
        alert.textContent = message;
        alert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    form.addEventListener('submit', async event => {
        event.preventDefault();
        let valid = true;
        form.querySelectorAll('.form-grid input, .form-grid select').forEach(field => {
            if (!validateField(field)) valid = false;
        });

        const captchaToken = form.querySelector('[name="g-recaptcha-response"]')?.value.trim();
        if (!captchaToken) {
            showFormMessage('Please verify that you are not a robot.', false);
            valid = false;
        }
        if (!valid) return;

        button.disabled = true;
        button.textContent = 'Submitting...';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form)
            });
            const data = await response.json();

            if (!response.ok) {
                if (data.errors) {
                    Object.entries(data.errors).forEach(([name, errors]) => {
                        const field = form.elements.namedItem(name);
                        if (field && name !== 'g-recaptcha-response') setFieldState(field, errors[0]);
                    });
                }
                throw new Error(data.message === 'The given data was invalid.'
                    ? 'Please correct the highlighted fields and try again.'
                    : (data.message || 'Registration could not be submitted.'));
            }

            showFormMessage(data.message, true);
            form.reset();
            form.querySelectorAll('.is-valid,.is-invalid').forEach(field => field.classList.remove('is-valid', 'is-invalid'));
            form.querySelectorAll('[data-live-error]').forEach(error => error.textContent = '');
            form.hidden = true;
        } catch (error) {
            showFormMessage(error.message, false);
            if (window.grecaptcha) window.grecaptcha.reset();
        } finally {
            button.disabled = false;
            button.textContent = 'Request Invitation';
        }
    });
})();
</script>
</body>
</html>

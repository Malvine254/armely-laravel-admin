@php
    $serviceContact = array_merge([
        'title' => 'Book Your Free Assessment',
        'subtitle' => 'Tell us about your situation.',
        'button_label' => 'Request Free Assessment',
        'note' => 'No spam. No sales pressure. Just a useful conversation.',
        'options' => [
            'Need a discovery call',
            'Need pricing and scope',
            'Need a demo',
            'Not sure, need a recommendation',
        ],
    ], $serviceContact ?? []);

    $serviceContactOptions = array_values(array_filter(array_map('trim', (array) ($serviceContact['options'] ?? []))));
    $serviceContactSurface = $serviceContact['surface'] ?? 'card';
@endphp

@once
    @push('styles')
    <style>
        .service-contact-form {
            display: block;
            width: 100%;
        }

        .service-contact-form--card {
            background: #ffffff;
            border: 1px solid rgba(41, 78, 139, 0.12);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        }

        .service-contact-form--bare {
            background: transparent;
            border: 0;
            border-radius: 0;
            padding: 0;
            box-shadow: none;
        }

        .service-contact-message {
            display: none;
            margin: 0 0 16px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .service-contact-message.is-success {
            display: block;
            background: rgba(34, 197, 94, 0.1);
            color: #166534;
            border: 1px solid rgba(34, 197, 94, 0.22);
        }

        .service-contact-message.is-error {
            display: block;
            background: rgba(239, 68, 68, 0.1);
            color: #991b1b;
            border: 1px solid rgba(239, 68, 68, 0.22);
        }

        .service-contact-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 14px;
            align-items: start;
        }

        .service-contact-row {
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
        }

        .service-contact-row.service-contact-row-full {
            grid-column: 1 / -1;
        }

        .service-contact-row label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6B7FA3;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
        }

        .service-contact-field {
            width: 100%;
            display: block;
            background: #FFFFFF;
            border: 1px solid rgba(41, 78, 139, 0.15);
            border-radius: 10px;
            padding: 12px 14px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.875rem;
            line-height: 1.45;
            color: #1A2540;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
            min-height: 48px;
            appearance: none;
        }

        .service-contact-field:focus {
            border-color: rgba(41, 78, 139, 0.45);
            box-shadow: 0 0 0 3px rgba(41, 78, 139, 0.08);
        }

        .service-contact-textarea {
            min-height: 132px;
            resize: vertical;
        }

        .service-contact-field option {
            background: #fff;
            color: #1A2540;
        }

        .service-contact-recaptcha {
            min-height: 78px;
        }

        .service-contact-submit {
            width: 100%;
            background: #294e8b;
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 14px 16px;
            margin-top: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }

        .service-contact-submit:hover {
            background: #3d6ab5;
            transform: translateY(-1px);
        }

        .service-contact-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .service-contact-note {
            text-align: center;
            margin-top: 12px;
            font-size: 0.75rem;
            color: #6B7FA3;
        }

        @media (max-width: 768px) {
            .service-contact-grid {
                grid-template-columns: 1fr;
            }

            .service-contact-row.service-contact-row-full {
                grid-column: auto;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
    (function() {
        function firstErrorMessage(errors) {
            if (!errors || typeof errors !== 'object') {
                return '';
            }

            for (const key of Object.keys(errors)) {
                const value = errors[key];
                if (Array.isArray(value) && value.length) {
                    return value[0];
                }
                if (typeof value === 'string' && value) {
                    return value;
                }
            }

            return '';
        }

        function renderRecaptchaWidgets() {
            if (typeof grecaptcha === 'undefined' || !grecaptcha.render) {
                return false;
            }

            let renderedAny = false;

            document.querySelectorAll('.service-contact-recaptcha').forEach(function(widget) {
                if (widget.dataset.rendered === '1' || !widget.getAttribute('data-sitekey')) {
                    return;
                }

                try {
                    const widgetId = grecaptcha.render(widget, {
                        sitekey: widget.getAttribute('data-sitekey')
                    });
                    widget.dataset.rendered = '1';
                    widget.dataset.widgetId = String(widgetId);
                    renderedAny = true;
                } catch (error) {
                    // Ignore double-render attempts.
                }
            });

            return renderedAny;
        }

        function init() {
            const forms = Array.from(document.querySelectorAll('.service-contact-form'));
            if (!forms.length) {
                return;
            }

            const csrfToken = @json(csrf_token());
            const submitUrl = @json(route('submit-consultation'));
            const recaptchaSiteKey = @json(config('services.recaptcha.site_key', ''));

            const recaptchaTimer = window.setInterval(function() {
                if (renderRecaptchaWidgets()) {
                    window.clearInterval(recaptchaTimer);
                }
            }, 250);

            window.setTimeout(function() {
                window.clearInterval(recaptchaTimer);
            }, 10000);

            document.addEventListener('submit', function(event) {
                const form = event.target.closest ? event.target.closest('.service-contact-form') : null;
                if (!form) {
                    return;
                }

                event.preventDefault();

                const messageEl = form.querySelector('.service-contact-message');
                const submitBtn = form.querySelector('.service-contact-submit');
                const recaptchaEl = form.querySelector('.service-contact-recaptcha');
                const originalLabel = submitBtn ? submitBtn.textContent : 'Submit';

                if (messageEl) {
                    messageEl.className = 'service-contact-message';
                    messageEl.style.display = 'none';
                    messageEl.textContent = '';
                }

                if (!recaptchaSiteKey) {
                    if (messageEl) {
                        messageEl.className = 'service-contact-message is-error';
                        messageEl.style.display = 'block';
                        messageEl.textContent = 'reCAPTCHA is not configured. Please contact support.';
                    }
                    return;
                }

                const widgetId = recaptchaEl ? recaptchaEl.dataset.widgetId : null;
                const recaptchaResponse = widgetId && typeof grecaptcha !== 'undefined' && grecaptcha.getResponse
                    ? grecaptcha.getResponse(Number(widgetId))
                    : '';

                if (recaptchaEl && !recaptchaResponse) {
                    if (messageEl) {
                        messageEl.className = 'service-contact-message is-error';
                        messageEl.style.display = 'block';
                        messageEl.textContent = 'Please verify that you are not a robot.';
                    }
                    return;
                }

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Sending...';
                }

                const formData = new FormData(form);
                formData.append('g-recaptcha-response', recaptchaResponse);

                fetch(submitUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value || csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));

                    if (!response.ok || data.success === false) {
                        const message = data.message || firstErrorMessage(data.errors) || 'An error occurred. Please try again.';
                        throw new Error(message);
                    }

                    return data;
                })
                .then(data => {
                    if (messageEl) {
                        messageEl.className = 'service-contact-message is-success';
                        messageEl.style.display = 'block';
                        messageEl.textContent = data.message || 'Your consultation request has been submitted successfully!';
                    }
                    form.reset();

                    if (recaptchaEl && typeof grecaptcha !== 'undefined' && grecaptcha.reset && recaptchaEl.dataset.widgetId) {
                        grecaptcha.reset(Number(recaptchaEl.dataset.widgetId));
                    }
                })
                .catch(error => {
                    if (messageEl) {
                        messageEl.className = 'service-contact-message is-error';
                        messageEl.style.display = 'block';
                        messageEl.textContent = error && error.message ? error.message : 'An error occurred. Please try again.';
                    }

                    if (recaptchaEl && typeof grecaptcha !== 'undefined' && grecaptcha.reset && recaptchaEl.dataset.widgetId) {
                        grecaptcha.reset(Number(recaptchaEl.dataset.widgetId));
                    }
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalLabel;
                    }
                });
            }, true);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
    </script>
    @endpush
@endonce

<form class="service-contact-form service-contact-form--{{ $serviceContactSurface }}" method="post" action="{{ route('submit-consultation') }}" data-service-contact-form>
    @csrf
    <input type="hidden" name="website" value="">
    <p class="service-contact-message" role="alert" aria-live="polite"></p>

    <div class="form-title">{{ $serviceContact['title'] }}</div>
    <div class="form-sub">{{ $serviceContact['subtitle'] }}</div>

    <div class="service-contact-grid">
        <div class="service-contact-row">
            <label>Full Name *</label>
            <input class="service-contact-field" name="name" type="text" placeholder="Jane Smith" required value="">
        </div>
        <div class="service-contact-row">
            <label>Business Email *</label>
            <input class="service-contact-field" name="email" type="email" placeholder="jane@yourcompany.com" required value="">
        </div>
        <div class="service-contact-row">
            <label>Organization</label>
            <input class="service-contact-field" name="organization" type="text" placeholder="Acme Corp" value="">
        </div>
        <div class="service-contact-row">
            <label>Phone</label>
            <input class="service-contact-field" name="phone" type="tel" placeholder="Optional" value="">
        </div>
        <div class="service-contact-row service-contact-row-full">
            <label>Primary Need *</label>
            <select class="service-contact-field" name="service_type" required>
                <option value="" disabled selected>Select one</option>
                @foreach($serviceContactOptions as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        </div>
        <div class="service-contact-row service-contact-row-full">
            <label>Message *</label>
            <textarea class="service-contact-field service-contact-textarea" name="message" placeholder="Tell us what you need help with..." required></textarea>
        </div>
        <div class="service-contact-row service-contact-row-full">
            <label>Confirm you are not a robot *</label>
            @if(!empty(config('services.recaptcha.site_key')))
                <div class="service-contact-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
            @else
                <div class="service-contact-message is-error" style="display:block;">reCAPTCHA is not configured. Please set <strong>CAPTURE_SITE_KEY</strong>.</div>
            @endif
        </div>
    </div>

    <button type="submit" class="service-contact-submit">{{ $serviceContact['button_label'] }}</button>
    <div class="service-contact-note">{{ $serviceContact['note'] }}</div>
</form>

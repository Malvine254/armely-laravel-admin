@php($year = now()->year)

<div id="snackbar" class="snackbar container shadow bg-light">
    <button class="btn-close" aria-label="Close">&times;</button>
    <div class="text-start row">
        <div class="col-md-8">
            <div class="ml-4">
                <h4>We Value Your Privacy</h4>
                <p>We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies, <a class="default-color" href="/privacy-policy">see our privacy policy</a>. You can manage your preferences by clicking "customize".</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="modal-buttons mt-3">
                <button id="acceptAll" class="btn btn-light">Accept All</button>
                <button data-toggle="modal" data-target="#cookieModal" class="btn btn-outline-secondary bg-dark">Customize</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cookieModal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cookie Preferences</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="cookie-option"><label class="h5">Essential Cookies</label><label class="switch"><input type="checkbox" checked disabled><span class="slider-two round"></span></label></div>
                <p class="text-muted">These cookies are necessary for the website to function and cannot be switched off.</p>
                <div class="cookie-option"><label class="h5">Performance Cookies</label><label class="switch"><input type="checkbox"><span class="slider-two round"></span></label></div>
                <p class="text-muted">These cookies collect information about how you use the website to help improve its performance.</p>
                <div class="cookie-option"><label class="h5">Functionality Cookies</label><label class="switch"><input type="checkbox"><span class="slider-two round"></span></label></div>
                <p class="text-muted">These cookies remember your preferences and provide enhanced, personalized features.</p>
                <div class="cookie-option"><label class="h5">Targeting/Advertising Cookies</label><label class="switch"><input type="checkbox"><span class="slider-two round"></span></label></div>
                <p class="text-muted">These cookies are used to deliver ads more relevant to you and your interests.</p>
                <div class="cookie-option"><label class="h5">Analytics Cookies</label><label class="switch"><input type="checkbox"><span class="slider-two round"></span></label></div>
                <p class="text-muted">These cookies help website owners understand how visitors interact with the site.</p>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal" id="saveAllPreferences">Save Preferences</button></div>
        </div>
    </div>
</div>

<footer id="footer" class="footer">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-2 col-md-6 col-12">
                    <div class="single-footer">
                        <h2 class="footer-logo-font">armely</h2>
                        <div class="row"><div class="col-lg-12"><ul class="text-light">
                            <li><a href="/privacy-policy"><i class="fa fa-caret-right" aria-hidden="true"></i> Privacy Policy</a></li>
                            <li><a href="/customer-stories"><i class="fa fa-caret-right mt-2" aria-hidden="true"></i> Customer Stories</a></li>
                            <li><a href="/blog"><i class="fa fa-caret-right mt-2" aria-hidden="true"></i> Blog Articles</a></li>
                            <li><a href="/industries"><i class="fa fa-caret-right mt-2" aria-hidden="true"></i> Industries</a></li>
                        </ul></div></div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-12">
                    <div class="single-footer f-link"><h2>About</h2><div class="row"><div class="col-lg-12"><ul>
                        <li><a href="/case-studies"><i class="fa fa-caret-right" aria-hidden="true"></i>Case Studies</a></li>
                        <li><a href="/career"><i class="fa fa-caret-right" aria-hidden="true"></i>Job Board</a></li>
                        <li><a href="/company"><i class="fa fa-caret-right" aria-hidden="true"></i>Company Overview</a></li>
                        <li><a href="/blog"><i class="fa fa-caret-right" aria-hidden="true"></i>Blog Articles </a></li>
                    </ul></div></div></div>
                </div>
                <div class="col-lg-2 col-md-6 col-12">
                    <div class="single-footer f-link"><h2>Services</h2><div class="row"><div class="col-lg-12"><ul>
                        <li><a href="/services"><i class="fa fa-caret-right" aria-hidden="true"></i>Data Services</a></li>
                        <li><a href="/services"><i class="fa fa-caret-right" aria-hidden="true"></i>Advisory Services</a></li>
                        <li><a href="/services"><i class="fa fa-caret-right" aria-hidden="true"></i>Managed Services</a></li>
                        <li><a href="/services"><i class="fa fa-caret-right" aria-hidden="true"></i>Artificial intelligence</a></li>
                    </ul></div></div></div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="single-footer f-link"><h2>Contact Us</h2><ul>
                        <li><a href="tel:+19724600643" target="_blank"><i class="fa fa-phone" aria-hidden="true"></i> +1 972 460 0643</a></li>
                        <li><a href="https://maps.app.goo.gl/YaMkStLJ6eKwAQ2c7" target="_blank"><i class="fa fa-map-marker" aria-hidden="true"></i>17400 Dallas Pkwy, Suite 111 Dallas, TX 75287</a></li>
                        <li><a href="mailto:info@armely.com" target="_blank"><i class="fa fa-envelope" aria-hidden="true"></i><span class="lowercase">info@armely.com</span></a></li>
                    </ul></div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="single-footer"><h2>Follow Us</h2><ul class="social">
                        <li><a href="https://www.linkedin.com/company/armely/mycompany/" target="_blank"><i class="icofont-linkedin"></i></a></li>
                        <li><a href="https://github.com/armely" target="_blank"><i class="icofont-github"></i></a></li>
                        <li><a href="https://twitter.com/armelyData" target="_blank"><i class="icofont-twitter"></i></a></li>
                        <li><a href="https://www.youtube.com/@armelyarmely" target="_blank"><i class="icofont-youtube"></i></a></li>
                        <li><a href="https://www.instagram.com/armelyconsulting/" target="_blank"><i class="icofont-instagram"></i></a></li>
                    </ul>
                    <div class="footer-newsletter">
                        <h3>Newsletter</h3>
                        <p>Get new Armely blogs, events, and Microsoft platform updates.</p>
                        <form id="footerNewsletterForm" action="{{ route('newsletter.subscribe') }}" method="post">
                            @csrf
                            <input type="email" name="email" placeholder="Work email" aria-label="Newsletter email" required>
                            <input type="text" name="website" class="newsletter-honeypot" tabindex="-1" autocomplete="off">
                            <button type="submit">Subscribe</button>
                        </form>
                        <div id="footerNewsletterMessage" class="footer-newsletter-message" aria-live="polite"></div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright">
        <div class="container"><div class="row"><div class="col-lg-12">
            <div class="copyright-content"><p>&copy; {{ $year }} ARMELY LLC., ALL RIGHTS RESERVED</p></div>
        </div></div></div>
    </div>
</footer>

<style>
    .footer-newsletter { margin-top: 22px; }
    .footer-newsletter h3 { color: #fff; font-size: 18px; font-weight: 800; margin: 0 0 8px; }
    .footer-newsletter p { color: rgba(255,255,255,.78); font-size: 13px; line-height: 1.55; margin: 0 0 12px; }
    .footer-newsletter form { display: flex; align-items: stretch; gap: 0; width: 100%; }
    .footer-newsletter input[type="email"] { flex: 1 1 auto; min-width: 0; width: 100%; min-height: 44px; border: 1px solid rgba(255,255,255,.25); border-right: 0; background: rgba(255,255,255,.08); color: #fff; padding: 10px 12px; border-radius: 0; }
    .footer-newsletter input[type="email"]::placeholder { color: rgba(255,255,255,.65); }
    .footer-newsletter input[type="email"]:focus { border-color: rgba(255,255,255,.52); outline: 0; box-shadow: inset 0 0 0 1px rgba(255,255,255,.14); }
    .footer-newsletter button { flex: 0 0 auto; min-height: 44px; border: 1px solid #3b66ae; background: #3b66ae; color: #fff; font-weight: 800; padding: 10px 16px; cursor: pointer; border-radius: 0; white-space: nowrap; }
    .footer-newsletter button:hover { background: #4774bf; border-color: #4774bf; }
    .footer-newsletter button:disabled { opacity: .7; cursor: wait; }
    .newsletter-honeypot { display: none !important; }
    .footer-newsletter-message { margin-top: 8px; color: rgba(255,255,255,.82); font-size: 12px; line-height: 1.45; }
    .footer-newsletter-message.is-error { color: #ffd6d6; }
    .footer-newsletter-message.is-success { color: #d8ffe5; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('footerNewsletterForm');
    var message = document.getElementById('footerNewsletterMessage');
    if (!form || !message) {
        return;
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        var button = form.querySelector('button[type="submit"]');
        var originalText = button ? button.textContent : 'Subscribe';
        var formData = new FormData(form);

        message.textContent = '';
        message.className = 'footer-newsletter-message';
        if (button) {
            button.disabled = true;
            button.textContent = 'Subscribing...';
        }

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData,
            credentials: 'same-origin'
        })
            .then(function (response) {
                return response.json().catch(function () {
                    return {};
                }).then(function (json) {
                    return { ok: response.ok, json: json };
                });
            })
            .then(function (result) {
                message.className = 'footer-newsletter-message ' + (result.ok && result.json.success ? 'is-success' : 'is-error');
                message.textContent = result.json.message || (result.ok ? 'You are subscribed.' : 'Unable to subscribe right now.');
                if (result.ok && result.json.success) {
                    form.reset();
                }
            })
            .catch(function () {
                message.className = 'footer-newsletter-message is-error';
                message.textContent = 'Unable to subscribe right now.';
            })
            .finally(function () {
                if (button) {
                    button.disabled = false;
                    button.textContent = originalText;
                }
            });
    });
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS" crossorigin="anonymous"></script>
<script src="{{ asset('js/jquery-migrate-3.0.0.js') }}?v={{ file_exists(public_path('js/jquery-migrate-3.0.0.js')) ? filemtime(public_path('js/jquery-migrate-3.0.0.js')) : '' }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}?v={{ file_exists(public_path('js/jquery-ui.min.js')) ? filemtime(public_path('js/jquery-ui.min.js')) : '' }}"></script>
<script src="{{ asset('js/easing.js') }}?v={{ file_exists(public_path('js/easing.js')) ? filemtime(public_path('js/easing.js')) : '' }}"></script>
<script src="{{ asset('js/colors.js') }}?v={{ file_exists(public_path('js/colors.js')) ? filemtime(public_path('js/colors.js')) : '' }}"></script>
<script src="{{ asset('js/popper.min.js') }}?v={{ file_exists(public_path('js/popper.min.js')) ? filemtime(public_path('js/popper.min.js')) : '' }}"></script>
<script src="{{ asset('js/bootstrap-datepicker.js') }}?v={{ file_exists(public_path('js/bootstrap-datepicker.js')) ? filemtime(public_path('js/bootstrap-datepicker.js')) : '' }}"></script>
<script src="{{ asset('js/jquery.nav.js') }}?v={{ file_exists(public_path('js/jquery.nav.js')) ? filemtime(public_path('js/jquery.nav.js')) : '' }}"></script>
<script src="{{ asset('js/slicknav.min.js') }}?v={{ file_exists(public_path('js/slicknav.min.js')) ? filemtime(public_path('js/slicknav.min.js')) : '' }}"></script>
<script src="{{ asset('js/jquery.scrollUp.min.js') }}?v={{ file_exists(public_path('js/jquery.scrollUp.min.js')) ? filemtime(public_path('js/jquery.scrollUp.min.js')) : '' }}"></script>
<script src="{{ asset('js/niceselect.js') }}?v={{ file_exists(public_path('js/niceselect.js')) ? filemtime(public_path('js/niceselect.js')) : '' }}"></script>
<script src="{{ asset('js/tilt.jquery.min.js') }}?v={{ file_exists(public_path('js/tilt.jquery.min.js')) ? filemtime(public_path('js/tilt.jquery.min.js')) : '' }}"></script>
<script src="{{ asset('js/owl-carousel.js') }}?v={{ file_exists(public_path('js/owl-carousel.js')) ? filemtime(public_path('js/owl-carousel.js')) : '' }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js" integrity="sha384-hKAOvu4SRkR5UhOl+rvozhqPNh0VgjTz0sydNTzye3vkV6VzpbLACSgthC2bhXeV" crossorigin="anonymous"></script>
<script src="{{ asset('js/jquery.counterup.min.js') }}?v={{ file_exists(public_path('js/jquery.counterup.min.js')) ? filemtime(public_path('js/jquery.counterup.min.js')) : '' }}"></script>
<script src="{{ asset('js/steller.js') }}?v={{ file_exists(public_path('js/steller.js')) ? filemtime(public_path('js/steller.js')) : '' }}"></script>
<script src="{{ asset('js/wow.min.js') }}?v={{ file_exists(public_path('js/wow.min.js')) ? filemtime(public_path('js/wow.min.js')) : '' }}"></script>
<script src="{{ asset('js/jquery.magnific-popup.min.js') }}?v={{ file_exists(public_path('js/jquery.magnific-popup.min.js')) ? filemtime(public_path('js/jquery.magnific-popup.min.js')) : '' }}"></script>
<!-- Waypoints loaded earlier (HTTPS) to avoid mixed-content blocking -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.all.min.js" integrity="sha384-b6c4uUbNdZMyaIj7NBFb/VbMINhwXEsc58mJj3uSWsn3Wg9JV/cgBVQFaaZuRqnK" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4Twt/qOuYxE721u19sVFLVSA4hf/rRt6PrZTmiPltdZcI7q7PXQBYTKyf" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js" integrity="sha384-leGYpHE9Tc4N9OwRd98xg6YFpB9shlc/RkilpFi0ljr3QD4tFoFptZvgnnzzwG4Q" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/superfish/1.7.10/js/superfish.min.js" integrity="sha384-UyW9kmMvnnYg9IKH34gu1AamQdgJJngNQOtdCzMR/g/UQOwnibMh6DwTws8AIYfl" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.sticky/1.0.4/jquery.sticky.min.js" integrity="sha384-f6WCX7JLO6ay45iRtjFII0kYlM1G+BB9mxCkCLU5P3zR6lDo819vCP+ER+ORuOJj" crossorigin="anonymous"></script>
<!-- Google reCAPTCHA -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<!-- Enhanced Search & Bot JavaScript -->
<script src="{{ asset('js/search-enhanced.js') }}?v={{ file_exists(public_path('js/search-enhanced.js')) ? filemtime(public_path('js/search-enhanced.js')) : '' }}"></script>
<!-- More Settings (includes legacy features) -->
<script src="{{ asset('js/more-options10-v2.js') }}?v={{ file_exists(public_path('js/more-options10-v2.js')) ? filemtime(public_path('js/more-options10-v2.js')) : '' }}"></script>

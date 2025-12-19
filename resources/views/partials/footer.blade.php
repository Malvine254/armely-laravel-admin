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

<div class="linkedin-follow-float">
    <script src="https://platform.linkedin.com/in.js" type="text/javascript">lang: en_US</script>
    <script type="IN/FollowCompany" data-id="22310926" data-counter="bottom"></script>
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
                    </ul></div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="{{ asset('js/jquery-migrate-3.0.0.js') }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/easing.js') }}"></script>
<script src="{{ asset('js/colors.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('js/jquery.nav.js') }}"></script>
<script src="{{ asset('js/slicknav.min.js') }}"></script>
<script src="{{ asset('js/jquery.scrollUp.min.js') }}"></script>
<script src="{{ asset('js/niceselect.js') }}"></script>
<script src="{{ asset('js/tilt.jquery.min.js') }}"></script>
<script src="{{ asset('js/owl-carousel.js') }}"></script>
<script src="{{ asset('js/jquery.counterup.min.js') }}"></script>
<script src="{{ asset('js/steller.js') }}"></script>
<script src="{{ asset('js/wow.min.js') }}"></script>
<script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/superfish/1.7.10/js/hoverIntent.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/superfish/1.7.10/js/superfish.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.sticky/1.0.4/jquery.sticky.min.js"></script>
<!-- Google reCAPTCHA -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<!-- Enhanced Search & Bot JavaScript -->
<script src="{{ asset('js/search-enhanced.js') }}"></script>
<!-- More Settings (includes legacy features) -->
<script src="{{ asset('js/more-options10-v2.js') }}"></script>

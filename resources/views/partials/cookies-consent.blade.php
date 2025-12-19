{{-- Cookies Consent Snackbar --}}
<div id="snackbar" class="snackbar container shadow bg-light">
    <button class="btn-close" aria-label="Close">&times;</button>
    <div class="text-start row">
        <div class="col-md-8">
            <div class="ml-4">
                <h4>We Value Your Privacy</h4>
                <p>We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies. <a class="default-color" href="{{ route('home') }}">See our privacy policy</a>. You can manage your preferences by clicking "customize".</p>
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

{{-- Cookies Preferences Modal --}}
<div class="modal fade" id="cookieModal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            {{-- Modal Header --}}
            <div class="modal-header">
                <h4 class="modal-title">Cookie Preferences</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body">
                <div class="cookie-option">
                    <label class="h5">Essential Cookies</label>
                    <label class="switch">
                        <input type="checkbox" checked disabled>
                        <span class="slider-two round"></span>
                    </label>
                </div>
                <p class="text-muted">These cookies are necessary for the website to function and cannot be switched off.</p>
                
                <div class="cookie-option">
                    <label class="h5">Performance Cookies</label>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider-two round"></span>
                    </label>
                </div>
                <p class="text-muted">These cookies collect information about how you use the website to help improve its performance.</p>

                <div class="cookie-option">
                    <label class="h5">Functionality Cookies</label>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider-two round"></span>
                    </label>
                </div>
                <p class="text-muted">These cookies remember your preferences and provide enhanced, personalized features.</p>

                <div class="cookie-option">
                    <label class="h5">Targeting/Advertising Cookies</label>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider-two round"></span>
                    </label>
                </div>
                <p class="text-muted">These cookies are used to deliver ads more relevant to you and your interests.</p>

                <div class="cookie-option">
                    <label class="h5">Analytics Cookies</label>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider-two round"></span>
                    </label>
                </div>
                <p class="text-muted">These cookies help website owners understand how visitors interact with the site.</p>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="saveAllPreferences">Save Preferences</button>
            </div>
        </div>
    </div>
</div>

{{-- Cookie Styles --}}
<style>
    .snackbar {
        display: none;
        position: fixed;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        padding: 20px;
        border-radius: 5px 5px 0 0;
        width: 100vw;
        max-width: 100vw;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        z-index: 1050;
    }

    .snackbar.show {
        display: block;
        animation: slideUp 0.5s ease-in-out;
    }

    @keyframes slideUp {
        from {
            bottom: -200px;
        }
        to {
            bottom: 0;
        }
    }

    .cookie-option {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider-two {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.4s;
    }

    .slider-two:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.4s;
    }

    input:checked + .slider-two {
        background-color: #2f5597;
    }

    input:checked + .slider-two:before {
        transform: translateX(26px);
    }

    .slider-two.round {
        border-radius: 24px;
    }

    .slider-two.round:before {
        border-radius: 50%;
    }
</style>

{{-- Mela AI website assistant --}}
<div id="melaWidget" class="mela-widget" data-state="hidden"
     data-api="{{ url('/api/mela/conversations') }}"
     data-contact-email="{{ config('mela.contact_fallback.email') }}">
    <section class="mela-panel" id="melaPanel" role="dialog" aria-modal="false" aria-labelledby="melaTitle" hidden>
        <header class="mela-header">
            <img src="{{ asset('images/bot-image/bot.png') }}" alt="" class="mela-avatar">
            <div class="mela-title">
                <strong id="melaTitle">{{ config('mela.assistant_name', 'Mela AI') }}</strong>
                <span><i class="mela-online-dot" aria-hidden="true"></i> Armely's AI assistant &middot; Online</span>
            </div>
            <button type="button" class="mela-icon-btn" id="melaRestart" aria-label="Start a new conversation" title="New conversation">
                <i class="fa-solid fa-rotate-right" aria-hidden="true"></i>
            </button>
            <button type="button" class="mela-icon-btn mela-close" id="melaClose" aria-label="Minimize chat" title="Minimize">&times;</button>
        </header>

        <div class="mela-body" id="melaLog" role="log" aria-live="polite" aria-relevant="additions">
            @foreach ((array) config('mela.greeting', []) as $line)
                <div class="mela-msg mela-msg-bot" data-greeting hidden>{{ $line }}</div>
            @endforeach
        </div>

        <div class="mela-quick-replies" id="melaChips" hidden>
            @foreach ((array) config('mela.quick_replies', []) as $chip)
                <button type="button" class="mela-chip">{{ $chip }}</button>
            @endforeach
        </div>

        {{-- Not a <form>: analytics tags would count every chat message as a site form submission. --}}
        <div class="mela-composer" id="melaForm" role="group" aria-label="Send a message">
            <label for="melaInput" class="mela-sr-only">Message Mela AI</label>
            <textarea id="melaInput" rows="1" maxlength="{{ (int) config('mela.input.max_message_chars', 2000) }}" placeholder="Type your message&hellip;" autocomplete="off"></textarea>
            <button type="button" class="mela-send" id="melaSend" aria-label="Send message">
                <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
            </button>
        </div>
        <p class="mela-disclaimer">AI answers may be inaccurate. Please don't share sensitive information.</p>
    </section>

    <button type="button" class="mela-launcher" id="melaLauncher" aria-label="Chat with {{ config('mela.assistant_name', 'Mela AI') }}" hidden>
        <img src="{{ asset('images/bot-image/bot.png') }}" alt="" class="mela-launcher-avatar">
        <span>Questions? <br> Chat with {{ config('mela.assistant_name', 'Mela AI') }}</span>
        <span class="mela-badge" id="melaBadge" hidden>1</span>
    </button>
</div>

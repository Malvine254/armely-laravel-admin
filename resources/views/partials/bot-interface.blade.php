{{-- Enhanced Bot Interface Components --}}

{{-- Proactive greeting: the assistant opens the conversation --}}
<div id="helpPopup" class="help-popup mela-greeting" role="dialog" aria-label="Armely AI Assistant" aria-live="polite" style="display: none;">
    <div class="mela-greeting-header">
        <img src="{{ asset('images/bot-image/bot.png') }}" alt="" class="mela-greeting-avatar">
        <div class="mela-greeting-title">
            <strong>Armely AI Assistant</strong>
            <span><i class="mela-online-dot"></i> Online now</span>
        </div>
        <button id="noThanksBtn" type="button" class="mela-greeting-close" aria-label="Minimize assistant">&times;</button>
    </div>

    <div class="mela-greeting-body">
        <div class="mela-typing" id="melaTyping" aria-hidden="true"><span></span><span></span><span></span></div>
        <div class="mela-msg" id="melaMsg1" hidden>
            Hi there! I&rsquo;m the Armely AI Assistant. I can help explore our enterprise data and AI solutions, guide you through software and licensing options, or connect you directly with a technical specialist.
        </div>
        <div class="mela-msg" id="melaMsg2" hidden>
            What challenge are you looking to solve today?
        </div>
        <div class="mela-quick-replies" id="melaQuickReplies" hidden>
            <button type="button" class="mela-chip">Data &amp; AI solutions</button>
            <button type="button" class="mela-chip">Software &amp; licensing</button>
            <button type="button" class="mela-chip">Talk to a technical specialist</button>
        </div>
    </div>

    <button id="chatNowBtn" type="button" class="mela-greeting-input">
        <span>Type your message&hellip;</span>
        <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
    </button>
</div>

{{-- Small Floating Bubble --}}
<div id="chatBubble" class="chat-bubble" role="button" tabindex="0" aria-label="Chat with the Armely AI Assistant">
    <img src="{{ asset('images/bot-image/bot.png') }}" alt="" class="agent-img-small">
    <span>Questions? <br> Chat with us</span>
    <span class="chat-bubble-badge" id="chatBubbleBadge" hidden>1</span>
</div>

{{-- Chat Modal --}}
<div id="myModal" class="modal-chat">
    <div class="modal-content-chat col-lg-4">
        <span class="close" aria-label="Close chat">&times;</span>
        
        <iframe 
            id="armelyWebchatFrame"
            src="about:blank"
            data-src="https://copilotstudio.preview.microsoft.com/environments/Default-b783208a-8014-4829-9589-5324f76470c8/bots/cr44c_agent/webchat?__version__=2"
            frameborder="0"
            style="width: 100%; height: 100%;"
            title="Armely Chat Bot"
            allow="microphone">
        </iframe>
    </div>
</div>

{{-- Enhanced Bot Interface Components --}}

{{-- Help Popup ("Can we help you?") --}}
<div id="helpPopup" class="help-popup" style="display: none;">
    <div class="popup-inner">
        <h4>Ask AI About Armely</h4>
        <p><b>Mela AI</b> is available 24/7.</p>
        
        <button id="chatNowBtn" class="popup-btn main">Chat now</button>
        <button id="noThanksBtn" class="popup-btn secondary">No thanks</button>
        
        <img src="https://i.gifer.com/9Pa3.gif" alt="AI Agent" class="agent-img">
    </div>
</div>

{{-- Small Floating Bubble ("Need help? Let's chat") --}}
<div id="chatBubble" class="chat-bubble">
    <img src="https://i.gifer.com/9Pa3.gif" alt="AI Agent" class="agent-img-small">
    <span>Need help? <br> Let's chat</span>
</div>

{{-- Chat Modal --}}
<div id="myModal" class="modal-chat">
    <div class="modal-content-chat col-lg-4">
        <span class="close" aria-label="Close chat">&times;</span>
        
        <iframe 
            src="https://copilotstudio.preview.microsoft.com/environments/Default-b783208a-8014-4829-9589-5324f76470c8/bots/cr44c_agent/webchat?__version__=2"
            frameborder="0"
            style="width: 100%; height: 100%;"
            title="Armely Chat Bot"
            allow="microphone">
        </iframe>
    </div>
</div>

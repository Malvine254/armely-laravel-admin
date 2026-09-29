/* Mela AI website assistant client. All reasoning, memory and actions live on the server. */
(function () {
    'use strict';

    var root = document.getElementById('melaWidget');
    if (!root) {
        return;
    }

    var STORAGE_KEY = 'mela.conversation.v1';
    var STORAGE_TTL_MS = 7 * 24 * 60 * 60 * 1000;
    var SESSION = { engaged: 'mela.engaged' };
    var DISMISSED_KEY = 'mela.dismissedAt';
    var DISMISSED_TTL_MS = 30 * 24 * 60 * 60 * 1000;
    var GREETING_DELAY_MS = 3500;
    var CLOSE_ANIMATION_MS = 220;

    var api = root.getAttribute('data-api');
    var contactEmail = root.getAttribute('data-contact-email') || '';
    var panel = document.getElementById('melaPanel');
    var log = document.getElementById('melaLog');
    var chips = document.getElementById('melaChips');
    var input = document.getElementById('melaInput');
    var sendBtn = document.getElementById('melaSend');
    var launcher = document.getElementById('melaLauncher');
    var badge = document.getElementById('melaBadge');
    var closeBtn = document.getElementById('melaClose');
    var restartBtn = document.getElementById('melaRestart');
    var greetingNodes = Array.prototype.slice.call(log.querySelectorAll('[data-greeting]'));

    var conversation = loadConversation();
    var historyLoaded = false;
    var busy = false;
    var typingNode = null;

    // ---------- storage ----------
    function session(key, value) {
        try {
            if (value === undefined) { return sessionStorage.getItem(key); }
            sessionStorage.setItem(key, value);
        } catch (e) { return null; }
        return null;
    }

    function loadConversation() {
        try {
            var data = JSON.parse(localStorage.getItem(STORAGE_KEY) || 'null');
            if (data && data.id && data.token && Date.now() - (data.savedAt || 0) < STORAGE_TTL_MS) {
                return data;
            }
        } catch (e) { /* ignore */ }
        return null;
    }

    function saveConversation(data) {
        conversation = data;
        try {
            if (data) {
                data.savedAt = Date.now();
                localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
            } else {
                localStorage.removeItem(STORAGE_KEY);
            }
        } catch (e) { /* ignore */ }
    }

    // ---------- rendering ----------
    function escapeHtml(text) {
        return String(text)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function inline(text) {
        var html = escapeHtml(text);
        html = html.replace(/\[([^\]]+)\]\(((?:https?:\/\/|\/(?!\/))[^\s)]+)\)/g, function (m, label, href) {
            return '<a href="' + href + '" target="_blank" rel="noopener noreferrer">' + label + '</a>';
        });
        html = html.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/(^|[\s(])(https?:\/\/[^\s<)]+[^\s<).,;:!?])/g, function (m, pre, href) {
            return pre + '<a href="' + href + '" target="_blank" rel="noopener noreferrer">' + href + '</a>';
        });
        return html;
    }

    // Minimal, safe Markdown: text is escaped before any formatting is applied.
    function renderMarkdown(text) {
        var blocks = String(text).replace(/\r/g, '').split(/\n{2,}/);
        return blocks.map(function (block) {
            var lines = block.split('\n');
            var bullets = lines.every(function (l) { return /^\s*([-*•])\s+/.test(l); });
            var numbered = lines.every(function (l) { return /^\s*\d+[.)]\s+/.test(l); });
            if (bullets || numbered) {
                var tag = bullets ? 'ul' : 'ol';
                return '<' + tag + '>' + lines.map(function (l) {
                    return '<li>' + inline(l.replace(/^\s*([-*•]|\d+[.)])\s+/, '')) + '</li>';
                }).join('') + '</' + tag + '>';
            }
            return '<p>' + lines.map(function (l) {
                return /^\s*([-*•])\s+/.test(l) ? '&bull; ' + inline(l.replace(/^\s*[-*•]\s+/, '')) : inline(l);
            }).join('<br>') + '</p>';
        }).join('');
    }

    function addMessage(role, content, options) {
        options = options || {};
        var node = document.createElement('div');
        node.className = 'mela-msg ' + (role === 'user' ? 'mela-msg-user' : 'mela-msg-bot') + (options.error ? ' mela-msg-error' : '');
        if (role === 'user') {
            node.textContent = content;
        } else {
            node.innerHTML = renderMarkdown(content);
        }
        if (options.retry) {
            var retry = document.createElement('button');
            retry.type = 'button';
            retry.className = 'mela-retry';
            retry.textContent = 'Try again';
            retry.addEventListener('click', function () {
                node.remove();
                options.retry();
            });
            node.appendChild(retry);
        }
        log.appendChild(node);
        scrollToBottom();
        return node;
    }

    function showTyping() {
        hideTyping();
        typingNode = document.createElement('div');
        typingNode.className = 'mela-typing';
        typingNode.setAttribute('aria-label', 'Mela AI is typing');
        typingNode.innerHTML = '<span></span><span></span><span></span>';
        log.appendChild(typingNode);
        scrollToBottom();
    }

    function hideTyping() {
        if (typingNode) {
            typingNode.remove();
            typingNode = null;
        }
    }

    function scrollToBottom() {
        log.scrollTop = log.scrollHeight;
    }

    function resetLog() {
        while (log.firstChild) {
            log.removeChild(log.firstChild);
        }
        greetingNodes.forEach(function (node) {
            node.hidden = false;
            log.appendChild(node);
        });
        scrollToBottom();
    }

    function renderTranscript(messages) {
        Array.prototype.slice.call(log.children).forEach(function (child) { child.remove(); });
        (messages || []).forEach(function (m) {
            addMessage(m.role, m.content);
        });
        chips.hidden = (messages || []).some(function (m) { return m.role === 'user'; });
    }

    // ---------- state ----------
    var siteHeader = document.querySelector('header.header') || document.querySelector('header');
    // The nav pins itself (jQuery sticky) once the page scrolls, so measure it as well as the header.
    var headerParts = siteHeader
        ? [siteHeader].concat(Array.prototype.slice.call(siteHeader.querySelectorAll('.header-inner, .sticky-wrapper, .is-sticky, [class*="sticky"]')))
        : [];
    var cookieBar = document.getElementById('snackbar');
    var fitScheduled = false;
    var settleTimer = null;

    function headerBottom() {
        var bottom = 0;
        headerParts.forEach(function (el) {
            var r = el.getBoundingClientRect();
            if (r.height > 0 && r.bottom > 0 && window.getComputedStyle(el).visibility !== 'hidden') {
                bottom = Math.max(bottom, r.bottom);
            }
        });
        return bottom;
    }

    // Keep the panel between the visible site header/menu and the cookie bar.
    function fitBelowHeader() {
        fitScheduled = false;
        var gap = window.innerWidth <= 520 ? 0 : (window.innerWidth <= 768 ? 12 : 30);
        var cookieHeight = cookieBar && window.getComputedStyle(cookieBar).display !== 'none' ? cookieBar.getBoundingClientRect().height : 0;
        root.style.setProperty('--mela-vh', window.innerHeight + 'px');
        root.style.setProperty('--mela-top', (headerBottom() + (window.innerWidth <= 520 ? 0 : 12)) + 'px');
        root.style.setProperty('--mela-bottom', (cookieHeight > 0 ? cookieHeight + 12 : gap) + 'px');
    }

    function scheduleFit() {
        if (!fitScheduled) {
            fitScheduled = true;
            window.requestAnimationFrame(fitBelowHeader);
        }
        // Re-measure after the sticky menu finishes sliding in.
        clearTimeout(settleTimer);
        settleTimer = setTimeout(fitBelowHeader, 450);
    }

    window.addEventListener('resize', scheduleFit);
    window.addEventListener('scroll', scheduleFit, { passive: true });
    if (cookieBar && window.MutationObserver) {
        new MutationObserver(scheduleFit).observe(cookieBar, { attributes: true, attributeFilter: ['class', 'style'] });
    }

    function setState(state) {
        var wasShowingPanel = !panel.hidden;
        fitBelowHeader();
        root.setAttribute('data-state', state);
        panel.hidden = state !== 'teaser' && state !== 'open';
        launcher.hidden = state !== 'launcher';
        if (!panel.hidden && !wasShowingPanel) {
            playAnimation(panel, 'mela-opening');
        }
        if (!launcher.hidden) {
            launcher.classList.toggle('is-nudging', !isDismissed());
        }
        if (state === 'open') {
            document.documentElement.classList.add('mela-open');
        } else {
            document.documentElement.classList.remove('mela-open');
        }
        document.documentElement.classList.toggle('mela-panel-visible', !panel.hidden);
        document.documentElement.classList.toggle('mela-launcher-visible', !launcher.hidden);
    }

    function playAnimation(node, className) {
        node.classList.remove(className);
        void node.offsetWidth;
        node.classList.add(className);
        node.addEventListener('animationend', function done() {
            node.classList.remove(className);
            node.removeEventListener('animationend', done);
        });
    }

    // ---------- auto-open (stops once the visitor dismisses the chat) ----------
    function isDismissed() {
        try {
            var at = parseInt(localStorage.getItem(DISMISSED_KEY) || '0', 10);
            return at > 0 && Date.now() - at < DISMISSED_TTL_MS;
        } catch (e) { return false; }
    }

    function rememberDismissal() {
        try { localStorage.setItem(DISMISSED_KEY, String(Date.now())); } catch (e) { /* ignore */ }
        launcher.classList.remove('is-nudging');
    }

    function autoOpen() {
        if (isDismissed() || root.getAttribute('data-state') !== 'launcher') { return; }
        // Launcher "types" first, then the chat grows out of it.
        launcher.classList.add('is-typing');
        setTimeout(function () {
            launcher.classList.remove('is-typing');
            if (isDismissed() || root.getAttribute('data-state') !== 'launcher') { return; }
            if (conversation) {
                open();
            } else {
                greet(0);
            }
        }, 1500);
    }

    function showLauncher(withBadge) {
        badge.hidden = !withBadge;
        setState('launcher');
    }

    function greet(delay) {
        setTimeout(function () {
            if (root.getAttribute('data-state') === 'open') { return; }
            greetingNodes.forEach(function (n) { n.hidden = true; });
            chips.hidden = true;
            setState('teaser');
            showTyping();
            setTimeout(function () {
                hideTyping();
                if (greetingNodes[0]) { greetingNodes[0].hidden = false; }
                setTimeout(function () {
                    greetingNodes.forEach(function (n) { n.hidden = false; });
                    chips.hidden = false;
                    scrollToBottom();
                }, 700);
            }, 1200);
        }, delay);
    }

    function open() {
        session(SESSION.engaged, '1');
        badge.hidden = true;
        setState('open');
        if (conversation && !historyLoaded) {
            loadHistory();
        } else if (!conversation) {
            greetingNodes.forEach(function (n) { n.hidden = false; });
            if (!log.querySelector('.mela-msg-user')) { chips.hidden = false; }
        }
        setTimeout(function () { input.focus(); }, 50);
    }

    function minimize() {
        hideTypingIfIdle();
        rememberDismissal();
        var withBadge = !conversation && !session(SESSION.engaged);
        panel.classList.add('mela-closing');
        setTimeout(function () {
            panel.classList.remove('mela-closing');
            showLauncher(withBadge);
        }, CLOSE_ANIMATION_MS);
    }

    function hideTypingIfIdle() {
        if (!busy) { hideTyping(); }
    }

    // ---------- API ----------
    function request(method, url, body) {
        var headers = { 'Accept': 'application/json' };
        if (body) { headers['Content-Type'] = 'application/json'; }
        if (conversation && conversation.token) { headers['X-Mela-Token'] = conversation.token; }
        return fetch(url, {
            method: method,
            headers: headers,
            body: body ? JSON.stringify(body) : undefined,
            credentials: 'same-origin'
        }).then(function (response) {
            return response.json().catch(function () { return {}; }).then(function (data) {
                return { status: response.status, ok: response.ok, data: data };
            });
        });
    }

    function createConversation() {
        return request('POST', api, { page_url: window.location.href }).then(function (res) {
            if (!res.ok || !res.data.conversation_id) {
                throw res;
            }
            saveConversation({ id: res.data.conversation_id, token: res.data.token });
            historyLoaded = true;
            return conversation;
        });
    }

    function loadHistory() {
        historyLoaded = true;
        request('GET', api + '/' + encodeURIComponent(conversation.id)).then(function (res) {
            if (res.status === 404) {
                saveConversation(null);
                resetLog();
                chips.hidden = false;
                return;
            }
            if (res.ok && res.data.messages) {
                renderTranscript(res.data.messages);
            }
        }).catch(function () { historyLoaded = false; });
    }

    function send(text, isRetry) {
        text = (text || '').trim();
        if (!text || busy) { return; }
        if (root.getAttribute('data-state') !== 'open') { open(); }

        busy = true;
        sendBtn.disabled = true;
        chips.hidden = true;
        if (!isRetry) {
            addMessage('user', text);
        }
        showTyping();

        var ready = conversation ? Promise.resolve(conversation) : createConversation();

        ready.then(function () {
            return request('POST', api + '/' + encodeURIComponent(conversation.id) + '/messages', {
                message: text,
                page_url: window.location.href
            });
        }).then(function (res) {
            if (res.status === 404) {
                // The conversation expired server-side; start a new one and resend once.
                saveConversation(null);
                return createConversation().then(function () {
                    return request('POST', api + '/' + encodeURIComponent(conversation.id) + '/messages', {
                        message: text,
                        page_url: window.location.href
                    });
                });
            }
            return res;
        }).then(function (res) {
            hideTyping();
            if (res.ok && res.data.message) {
                addMessage('assistant', res.data.message.content);
                saveConversation(conversation);
                return;
            }
            var message = (res.data && res.data.message) || fallbackError();
            var retryable = res.status !== 429 || (res.data && res.data.error === 'rate_limited');
            addMessage('assistant', message, { error: true, retry: retryable ? function () { send(text, true); } : null });
        }).catch(function () {
            hideTyping();
            addMessage('assistant', fallbackError(), { error: true, retry: function () { send(text, true); } });
        }).then(function () {
            busy = false;
            sendBtn.disabled = false;
            input.focus();
        });
    }

    function fallbackError() {
        return "I couldn't reach the server just now. Please check your connection and try again" +
            (contactEmail ? ', or email the Armely team at ' + contactEmail + '.' : '.');
    }

    function restart() {
        if (busy) { return; }
        saveConversation(null);
        historyLoaded = false;
        resetLog();
        chips.hidden = false;
        input.value = '';
        autosize();
        input.focus();
    }

    function autosize() {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 120) + 'px';
    }

    // ---------- events ----------
    function submitDraft() {
        // Keep the draft in the box while a reply is still loading.
        if (busy || !input.value.trim()) { return; }
        var text = input.value;
        input.value = '';
        autosize();
        send(text);
    }

    sendBtn.addEventListener('click', submitDraft);

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
            e.preventDefault();
            submitDraft();
        }
    });
    input.addEventListener('input', autosize);
    input.addEventListener('focus', function () {
        if (root.getAttribute('data-state') === 'teaser') { open(); }
    });

    chips.addEventListener('click', function (e) {
        var chip = e.target.closest('.mela-chip');
        if (chip) { send(chip.textContent); }
    });

    launcher.addEventListener('click', open);
    closeBtn.addEventListener('click', minimize);
    restartBtn.addEventListener('click', restart);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && root.getAttribute('data-state') === 'open') { minimize(); }
    });

    // Re-engage when a desktop visitor moves to leave, unless they dismissed the chat.
    var exitShown = false;
    document.addEventListener('mouseout', function (e) {
        if (e.relatedTarget || e.clientY > 0) { return; }
        if (exitShown || conversation || isDismissed()) { return; }
        if (root.getAttribute('data-state') !== 'launcher') { return; }
        exitShown = true;
        greet(0);
    });

    // ---------- start ----------
    if (isDismissed()) {
        showLauncher(false);
    } else {
        showLauncher(!conversation);
        setTimeout(autoOpen, GREETING_DELAY_MS);
    }
})();

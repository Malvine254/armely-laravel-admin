<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UI Responsiveness Test</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #0f172a;
            --toolbar: #111827;
            --panel: #1f2937;
            --text: #f9fafb;
            --muted: #9ca3af;
            --accent: #38bdf8;
            --accent-2: #818cf8;
            --border: #374151;
        }

        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            margin: 0;
            font-family: Inter, "Segoe UI", Roboto, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow: hidden;
        }

        .browser-shell {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .toolbar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            background: var(--toolbar);
            border-bottom: 1px solid var(--border);
        }

        .toolbar .input-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 6px 10px;
        }

        input, select, button {
            font: inherit;
        }

        input {
            flex: 1;
            border: 0;
            outline: 0;
            background: transparent;
            color: var(--text);
            min-width: 0;
        }

        button, select {
            border: 1px solid var(--border);
            background: var(--panel);
            color: var(--text);
            border-radius: 999px;
            padding: 7px 10px;
        }

        button {
            cursor: pointer;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            color: white;
            font-weight: 600;
            border: 0;
        }

        .device-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            padding: 6px 8px;
            background: #0b1120;
            border-bottom: 1px solid var(--border);
        }

        .device-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #1f2937;
            color: var(--muted);
            cursor: pointer;
            font-size: 0.9rem;
            user-select: none;
        }

        .device-chip svg {
            width: 14px;
            height: 14px;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.8;
            flex: 0 0 auto;
        }

        .device-chip.active {
            color: white;
            background: rgba(56, 189, 248, 0.2);
        }

        .viewport {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 8px;
            background: #e5e7eb;
            overflow: auto;
        }

        .frame-wrap {
            width: 390px;
            max-width: 100%;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #d1d5db;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.16);
            transition: width 0.2s ease;
        }

        iframe {
            display: block;
            width: 100%;
            height: calc(100vh - 110px);
            min-height: 540px;
            border: 0;
            background: white;
        }

        .status {
            padding: 4px 8px;
            color: var(--muted);
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .status.loading { color: #0369a1; }
        .status.loaded { color: #047857; }
        .status.error { color: #b91c1c; }

        @media (max-width: 760px) {
            .toolbar { flex-wrap: wrap; }
            .toolbar .input-wrap { width: 100%; }
            .frame-wrap { width: 100%; }
            iframe { height: calc(100vh - 130px); }
        }
    </style>
</head>
<body>
    <div class="browser-shell">
        <div class="toolbar">
            <div class="input-wrap">
                <input id="urlInput" type="text" placeholder="Enter a URL" value="{{ $initialUrl ?? '' }}">
                <button id="loadBtn" type="button">Go</button>
            </div>
            <select id="deviceSelect">
                <option value="iphone-se">iPhone SE (375x667)</option>
                <option value="iphone-12">iPhone 12/13/14 (390x844)</option>
                <option value="iphone-14-plus">iPhone 14 Plus (428x926)</option>
                <option value="pixel-7">Pixel 7 (412x915)</option>
                <option value="galaxy-s20">Galaxy S20 (360x800)</option>
                <option value="ipad-mini">iPad Mini (768x1024)</option>
                <option value="ipad-air">iPad Air (820x1180)</option>
                <option value="ipad-pro-11">iPad Pro 11 (834x1194)</option>
                <option value="surface-pro-7">Surface Pro 7 (912x1368)</option>
                <option value="laptop-1366">Laptop 1366 (1366x768)</option>
                <option value="laptop-1440">Laptop 1440 (1440x900)</option>
                <option value="desktop-fhd">Desktop FHD (1920x1080)</option>
                <option value="desktop-qhd">Desktop QHD (2560x1440)</option>
            </select>
        </div>

        <div class="device-row" id="deviceRow">
            <div class="device-chip active" data-size="iphone-12">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="7" y="2" width="10" height="20" rx="2"></rect><line x1="11" y1="19" x2="13" y2="19"></line></svg>
                <span>iPhone 12</span>
            </div>
            <div class="device-chip" data-size="iphone-se">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="7" y="2" width="10" height="20" rx="2"></rect><line x1="11" y1="19" x2="13" y2="19"></line></svg>
                <span>iPhone SE</span>
            </div>
            <div class="device-chip" data-size="pixel-7">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="7" y="2" width="10" height="20" rx="2"></rect><line x1="11" y1="19" x2="13" y2="19"></line></svg>
                <span>Pixel 7</span>
            </div>
            <div class="device-chip" data-size="ipad-mini">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="2" width="14" height="20" rx="2"></rect><circle cx="12" cy="18" r="0.8"></circle></svg>
                <span>iPad Mini</span>
            </div>
            <div class="device-chip" data-size="ipad-pro-11">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="2" width="14" height="20" rx="2"></rect><circle cx="12" cy="18" r="0.8"></circle></svg>
                <span>iPad Pro 11</span>
            </div>
            <div class="device-chip" data-size="laptop-1366">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="5" width="16" height="11" rx="1.5"></rect><path d="M2 19h20"></path></svg>
                <span>Laptop 1366</span>
            </div>
            <div class="device-chip" data-size="laptop-1440">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="5" width="16" height="11" rx="1.5"></rect><path d="M2 19h20"></path></svg>
                <span>Laptop 1440</span>
            </div>
            <div class="device-chip" data-size="desktop-fhd">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="12" rx="1.5"></rect><path d="M9 20h6"></path><path d="M12 16v4"></path></svg>
                <span>Desktop FHD</span>
            </div>
        </div>

        <div class="viewport">
            <div class="frame-wrap" id="deviceStage">
                <div class="status" id="frameUrl">Enter a URL to begin testing.</div>
                <iframe id="previewFrame" srcdoc="<div style='font-family:Arial;padding:24px;color:#111'>Enter a URL to begin testing.</div>"></iframe>
            </div>
        </div>
    </div>

    <script>
        const presets = {
            'iphone-se': { label: 'iPhone SE', width: 375, height: 667 },
            'iphone-12': { label: 'iPhone 12/13/14', width: 390, height: 844 },
            'iphone-14-plus': { label: 'iPhone 14 Plus', width: 428, height: 926 },
            'pixel-7': { label: 'Pixel 7', width: 412, height: 915 },
            'galaxy-s20': { label: 'Galaxy S20', width: 360, height: 800 },
            'ipad-mini': { label: 'iPad Mini', width: 768, height: 1024 },
            'ipad-air': { label: 'iPad Air', width: 820, height: 1180 },
            'ipad-pro-11': { label: 'iPad Pro 11', width: 834, height: 1194 },
            'surface-pro-7': { label: 'Surface Pro 7', width: 912, height: 1368 },
            'laptop-1366': { label: 'Laptop 1366', width: 1366, height: 768 },
            'laptop-1440': { label: 'Laptop 1440', width: 1440, height: 900 },
            'desktop-fhd': { label: 'Desktop FHD', width: 1920, height: 1080 },
            'desktop-qhd': { label: 'Desktop QHD', width: 2560, height: 1440 }
        };

        const urlInput = document.getElementById('urlInput');
        const loadBtn = document.getElementById('loadBtn');
        const deviceSelect = document.getElementById('deviceSelect');
        const deviceStage = document.getElementById('deviceStage');
        const frameUrl = document.getElementById('frameUrl');
        const previewFrame = document.getElementById('previewFrame');
        const deviceRow = document.getElementById('deviceRow');
        let currentUrl = '';
        let loadingStartedAt = 0;
        let loadingTimer = null;
        let renderWatchTimer = null;
        let hasLoadedCurrentPage = false;

        function currentSizeLabel() {
            const selected = deviceSelect.value;
            const preset = presets[selected];
            return preset.width + 'x' + preset.height;
        }

        function setStatus(text, type = '') {
            frameUrl.classList.remove('loading', 'loaded', 'error');
            if (type) {
                frameUrl.classList.add(type);
            }
            frameUrl.textContent = text;
        }

        function stopLoadingTicker() {
            if (loadingTimer) {
                clearInterval(loadingTimer);
                loadingTimer = null;
            }

            if (renderWatchTimer) {
                clearInterval(renderWatchTimer);
                renderWatchTimer = null;
            }
        }

        function startLoadingTicker() {
            stopLoadingTicker();
            loadingStartedAt = Date.now();
            loadingTimer = setInterval(() => {
                const elapsed = ((Date.now() - loadingStartedAt) / 1000).toFixed(1);
                setStatus('Loading ' + currentUrl + ' (' + elapsed + 's) | ' + currentSizeLabel(), 'loading');
            }, 150);

            renderWatchTimer = setTimeout(() => {
                if (!currentUrl || hasLoadedCurrentPage) {
                    return;
                }

                hasLoadedCurrentPage = true;
                stopLoadingTicker();
                const elapsed = ((Date.now() - loadingStartedAt) / 1000).toFixed(1);
                setStatus('Rendered ' + currentUrl + ' in ' + elapsed + 's (background assets may still load) | ' + currentSizeLabel(), 'loaded');
            }, 3000);
        }

        function applyDevice(size) {
            const preset = presets[size];
            deviceStage.style.width = preset.width + 'px';
            previewFrame.style.height = Math.max(540, preset.height) + 'px';
            document.querySelectorAll('.device-chip').forEach(chip => {
                chip.classList.toggle('active', chip.dataset.size === size);
            });
            deviceSelect.value = size;

            if (!currentUrl) {
                setStatus('Enter a URL to begin testing.');
                return;
            }

            if (loadingTimer) {
                const elapsed = ((Date.now() - loadingStartedAt) / 1000).toFixed(1);
                setStatus('Loading ' + currentUrl + ' (' + elapsed + 's) | ' + currentSizeLabel(), 'loading');
                return;
            }

            if (hasLoadedCurrentPage) {
                setStatus('Loaded ' + currentUrl + ' | ' + currentSizeLabel(), 'loaded');
            } else {
                setStatus(currentUrl + ' | ' + currentSizeLabel());
            }
        }

        function normalizeUrl(raw) {
            const value = raw.trim();
            if (!value) return '';
            if (/^https?:\/\//i.test(value)) return value;
            if (/^www\./i.test(value)) return 'https://' + value;
            return 'https://' + value;
        }

        function showPlaceholder() {
            currentUrl = '';
            hasLoadedCurrentPage = false;
            stopLoadingTicker();
            setStatus('Enter a URL to begin testing.');
            previewFrame.removeAttribute('src');
            previewFrame.srcdoc = '<div style="font-family:Arial;padding:24px;color:#111">Enter a URL to begin testing.</div>';
        }

        function buildProxyUrl(url) {
            return '/ui-responsiveness/proxy?url=' + encodeURIComponent(url) + '&fast=1';
        }

        function loadUrl() {
            const raw = urlInput.value.trim();
            if (!raw) {
                showPlaceholder();
                return;
            }

            const url = normalizeUrl(raw);
            currentUrl = url;
            hasLoadedCurrentPage = false;
            startLoadingTicker();
            previewFrame.removeAttribute('srcdoc');
            previewFrame.src = buildProxyUrl(url);
        }

        previewFrame.addEventListener('load', () => {
            if (!currentUrl) {
                return;
            }

            stopLoadingTicker();
            hasLoadedCurrentPage = true;
            const elapsed = ((Date.now() - loadingStartedAt) / 1000).toFixed(1);
            setStatus('Loaded ' + currentUrl + ' in ' + elapsed + 's | ' + currentSizeLabel(), 'loaded');
        });

        window.addEventListener('message', (event) => {
            const data = event.data;
            if (!data || data.__uiResp !== true || !currentUrl) {
                return;
            }

            const elapsed = ((Date.now() - loadingStartedAt) / 1000).toFixed(1);
            if (data.type === 'domready') {
                hasLoadedCurrentPage = true;
                stopLoadingTicker();
                setStatus('Rendered ' + currentUrl + ' in ' + elapsed + 's | ' + currentSizeLabel(), 'loaded');
                return;
            }

            if (data.type === 'load') {
                hasLoadedCurrentPage = true;
                stopLoadingTicker();
                setStatus('Loaded ' + currentUrl + ' in ' + elapsed + 's | ' + currentSizeLabel(), 'loaded');
            }
        });

        previewFrame.addEventListener('error', () => {
            if (!currentUrl) {
                return;
            }

            stopLoadingTicker();
            setStatus('Failed to load ' + currentUrl + ' | ' + currentSizeLabel(), 'error');
        });

        loadBtn.addEventListener('click', loadUrl);
        urlInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') loadUrl();
        });
        deviceSelect.addEventListener('change', (event) => {
            applyDevice(event.target.value);
        });
        deviceRow.addEventListener('click', (event) => {
            const chip = event.target.closest('.device-chip');
            if (chip) {
                applyDevice(chip.dataset.size);
            }
        });

        if (urlInput.value) {
            loadUrl();
        } else {
            showPlaceholder();
        }

        applyDevice('iphone-12');
    </script>
</body>
</html>

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
            padding: 6px 10px;
            border-radius: 999px;
            background: #1f2937;
            color: var(--muted);
            cursor: pointer;
            font-size: 0.9rem;
            user-select: none;
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
                <option value="mobile">Mobile</option>
                <option value="tablet">Tablet</option>
                <option value="laptop">Laptop</option>
                <option value="desktop">Desktop</option>
            </select>
        </div>

        <div class="device-row" id="deviceRow">
            <div class="device-chip active" data-size="mobile">📱 Mobile</div>
            <div class="device-chip" data-size="tablet">📲 Tablet</div>
            <div class="device-chip" data-size="laptop">💻 Laptop</div>
            <div class="device-chip" data-size="desktop">🖥 Desktop</div>
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
            mobile: { label: 'Mobile', width: 390 },
            tablet: { label: 'Tablet', width: 768 },
            laptop: { label: 'Laptop', width: 1280 },
            desktop: { label: 'Desktop', width: 1440 }
        };

        const urlInput = document.getElementById('urlInput');
        const loadBtn = document.getElementById('loadBtn');
        const deviceSelect = document.getElementById('deviceSelect');
        const deviceStage = document.getElementById('deviceStage');
        const frameUrl = document.getElementById('frameUrl');
        const previewFrame = document.getElementById('previewFrame');
        const deviceRow = document.getElementById('deviceRow');

        function applyDevice(size) {
            const preset = presets[size];
            deviceStage.style.width = preset.width + 'px';
            document.querySelectorAll('.device-chip').forEach(chip => {
                chip.classList.toggle('active', chip.dataset.size === size);
            });
            deviceSelect.value = size;
        }

        function normalizeUrl(raw) {
            const value = raw.trim();
            if (!value) return '';
            if (/^https?:\/\//i.test(value)) return value;
            if (/^www\./i.test(value)) return 'https://' + value;
            return 'https://' + value;
        }

        function showPlaceholder() {
            frameUrl.textContent = 'Enter a URL to begin testing.';
            previewFrame.removeAttribute('src');
            previewFrame.srcdoc = '<div style="font-family:Arial;padding:24px;color:#111">Enter a URL to begin testing.</div>';
        }

        function buildProxyUrl(url) {
            return '/ui-responsiveness/proxy?url=' + encodeURIComponent(url);
        }

        function loadUrl() {
            const raw = urlInput.value.trim();
            if (!raw) {
                showPlaceholder();
                return;
            }

            const url = normalizeUrl(raw);
            frameUrl.textContent = url;
            previewFrame.removeAttribute('srcdoc');
            previewFrame.src = buildProxyUrl(url);
        }

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

        applyDevice('mobile');
    </script>
</body>
</html>

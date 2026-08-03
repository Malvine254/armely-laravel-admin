<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UI Responsiveness Test</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #07111f;
            --panel: rgba(15, 23, 42, 0.92);
            --panel-2: rgba(30, 41, 59, 0.9);
            --text: #f8fafc;
            --muted: #94a3b8;
            --accent: #38bdf8;
            --accent-2: #818cf8;
            --border: rgba(148, 163, 184, 0.2);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, "Segoe UI", Roboto, Arial, sans-serif;
            background: radial-gradient(circle at top left, rgba(56,189,248,0.18), transparent 24%), var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        .page {
            max-width: 1500px;
            margin: 0 auto;
            padding: 28px;
        }

        .hero {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            align-items: center;
            justify-content: space-between;
            padding: 24px;
            border: 1px solid var(--border);
            border-radius: 24px;
            background: linear-gradient(135deg, rgba(15,23,42,0.95), rgba(30,41,59,0.9));
            box-shadow: 0 20px 60px rgba(2, 6, 23, 0.35);
        }

        .hero h1 {
            margin: 0 0 8px;
            font-size: clamp(1.35rem, 2vw, 1.7rem);
            font-weight: 700;
        }

        .hero p {
            margin: 0;
            color: var(--muted);
            max-width: 700px;
            line-height: 1.6;
        }

        .controls {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 18px;
            align-items: center;
        }

        .control-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            padding: 10px 12px;
            border-radius: 999px;
            background: var(--panel-2);
            border: 1px solid var(--border);
        }

        input, select, button {
            font: inherit;
        }

        input {
            border: 1px solid var(--border);
            background: rgba(15, 23, 42, 0.9);
            color: var(--text);
            border-radius: 999px;
            padding: 10px 14px;
            min-width: 320px;
        }

        select {
            border: 1px solid var(--border);
            background: rgba(15, 23, 42, 0.9);
            color: var(--text);
            border-radius: 999px;
            padding: 10px 14px;
        }

        button {
            border: none;
            cursor: pointer;
            border-radius: 999px;
            padding: 10px 16px;
            color: white;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(56, 189, 248, 0.2);
        }

        .device-row {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .device-chip {
            padding: 9px 12px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: var(--panel-2);
            color: var(--muted);
            cursor: pointer;
            user-select: none;
            font-size: 0.95rem;
        }

        .device-chip.active {
            color: white;
            border-color: rgba(56,189,248,0.55);
            background: rgba(56, 189, 248, 0.16);
        }

        .frame-shell {
            margin-top: 22px;
            padding: 18px;
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(15,23,42,0.96), rgba(30,41,59,0.95));
            border: 1px solid var(--border);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.04), 0 20px 60px rgba(2, 6, 23, 0.25);
        }

        .frame-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
            padding: 10px 12px;
            border-radius: 16px;
            background: rgba(2, 6, 23, 0.45);
            border: 1px solid var(--border);
        }

        .frame-toolbar .label {
            color: var(--muted);
            font-size: 0.94rem;
        }

        .device-stage {
            border-radius: 22px;
            overflow: hidden;
            border: 1px solid rgba(148,163,184,0.22);
            background: white;
            margin: 0 auto;
            transition: width 0.25s ease, height 0.25s ease;
            max-width: 100%;
        }

        iframe {
            display: block;
            width: 100%;
            height: 820px;
            border: 0;
            background: white;
        }

        @media (max-width: 900px) {
            .page { padding: 16px; }
            .hero { padding: 18px; }
            input { min-width: 220px; }
            iframe { height: 700px; }
        }

        @media (max-width: 640px) {
            .controls { flex-direction: column; align-items: stretch; }
            .control-group { border-radius: 16px; }
            input { min-width: 0; width: 100%; }
            .frame-toolbar { flex-direction: column; align-items: flex-start; }
            iframe { height: 620px; }
        }
    </style>
</head>
<body>
    <div class="page">
        <section class="hero">
            <div>
                <h1>Responsive UI Testing Studio</h1>
                <p>Load any website and preview it across phone, tablet, and laptop screen sizes in a clean, distraction-free setup.</p>
            </div>
            <div class="controls">
                <div class="control-group">
                    <input id="urlInput" type="url" placeholder="https://your-site.com" value="{{ $initialUrl ?? '' }}">
                    <button id="loadBtn" type="button">Load Preview</button>
                </div>
                <div class="control-group">
                    <select id="deviceSelect">
                        <option value="mobile">Mobile</option>
                        <option value="tablet">Tablet</option>
                        <option value="laptop">Laptop</option>
                        <option value="desktop">Desktop</option>
                    </select>
                </div>
            </div>
        </section>

        <div class="device-row" id="deviceRow">
            <div class="device-chip active" data-size="mobile">📱 Mobile</div>
            <div class="device-chip" data-size="tablet">📲 Tablet</div>
            <div class="device-chip" data-size="laptop">💻 Laptop</div>
            <div class="device-chip" data-size="desktop">🖥 Desktop</div>
        </div>

        <section class="frame-shell">
            <div class="frame-toolbar">
                <div class="label" id="deviceLabel">Previewing at Mobile width</div>
                <div class="label" id="frameUrl">Waiting for a URL…</div>
            </div>
            <div class="device-stage" id="deviceStage">
                <iframe id="previewFrame" srcdoc="<h3 style='font-family:Arial;padding:24px;color:#111'>Enter a URL to begin testing.</h3>"></iframe>
            </div>
        </section>
    </div>

    <script>
        const presets = {
            mobile: { label: 'Mobile', width: 390, height: 844 },
            tablet: { label: 'Tablet', width: 768, height: 1024 },
            laptop: { label: 'Laptop', width: 1280, height: 900 },
            desktop: { label: 'Desktop', width: 1440, height: 960 }
        };

        const urlInput = document.getElementById('urlInput');
        const loadBtn = document.getElementById('loadBtn');
        const deviceSelect = document.getElementById('deviceSelect');
        const deviceStage = document.getElementById('deviceStage');
        const deviceLabel = document.getElementById('deviceLabel');
        const frameUrl = document.getElementById('frameUrl');
        const previewFrame = document.getElementById('previewFrame');
        const deviceRow = document.getElementById('deviceRow');

        let activeDevice = 'mobile';

        function applyDevice(size) {
            activeDevice = size;
            const preset = presets[size];
            deviceStage.style.width = preset.width + 'px';
            deviceStage.style.maxWidth = '100%';
            deviceLabel.textContent = 'Previewing at ' + preset.label + ' width';
            document.querySelectorAll('.device-chip').forEach(chip => {
                chip.classList.toggle('active', chip.dataset.size === size);
            });
            deviceSelect.value = size;
        }

        function loadUrl() {
            const url = urlInput.value.trim();
            if (!url) {
                frameUrl.textContent = 'Enter a URL to begin testing.';
                previewFrame.srcdoc = '<h3 style="font-family:Arial;padding:24px;color:#111">Enter a URL to begin testing.</h3>';
                return;
            }

            frameUrl.textContent = url;
            previewFrame.src = url;
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
        }

        applyDevice('mobile');
    </script>
</body>
</html>

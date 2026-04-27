<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Armely Store</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            background: #0f172a;
            color: #e2e8f0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .shell {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .notice {
            display: none;
            background: #7f1d1d;
            color: #fee2e2;
            padding: 12px 16px;
            font-size: 14px;
            line-height: 1.4;
        }

        .notice a {
            color: #fecaca;
        }

        iframe {
            border: 0;
            width: 100%;
            height: 100%;
            flex: 1;
            background: #fff;
        }
    </style>
</head>
<body>
<div class="shell">
    <div id="store-unreachable" class="notice">
        Store app is not reachable right now. Start it and retry: php artisan serve --host=127.0.0.1 --port=8001 inside the store folder.
        Direct link: <a id="store-link" href="{{ $targetUrl }}" target="_blank" rel="noopener noreferrer">open store</a>
    </div>
    <iframe
        id="store-frame"
        src="{{ $targetUrl }}"
        title="Armely Store"
        loading="eager"
        referrerpolicy="no-referrer"
    ></iframe>
</div>

<script>
    (function () {
        var frame = document.getElementById('store-frame');
        var notice = document.getElementById('store-unreachable');

        var timeout = setTimeout(function () {
            notice.style.display = 'block';
        }, 5000);

        frame.addEventListener('load', function () {
            clearTimeout(timeout);
        });
    })();
</script>
</body>
</html>

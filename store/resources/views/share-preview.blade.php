<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="noindex, nofollow">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $previewUrl }}">
    @if (!empty($imageUrl))
        <meta property="og:image" content="{{ $imageUrl }}">
        <meta property="og:image:secure_url" content="{{ $imageUrl }}">
    @endif

    <meta name="twitter:card" content="{{ !empty($imageUrl) ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    @if (!empty($imageUrl))
        <meta name="twitter:image" content="{{ $imageUrl }}">
    @endif

    <meta http-equiv="refresh" content="0;url={{ $redirectUrl }}">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            max-width: 720px;
            width: 100%;
            background: #fff;
            border: 1px solid #dbe3ef;
            border-radius: 16px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }
        .hero {
            min-height: 220px;
            background: linear-gradient(135deg, #d9e9fb, #f8fbff);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero img {
            max-width: 100%;
            max-height: 240px;
            object-fit: contain;
        }
        .content {
            padding: 24px;
        }
        h1 {
            margin: 0 0 12px;
            font-size: 28px;
            line-height: 1.2;
        }
        p {
            margin: 0 0 16px;
            color: #475569;
            line-height: 1.5;
        }
        a {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 10px;
            background: #1d4b8f;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
    <script>
        window.location.replace(@json($redirectUrl));
    </script>
</head>
<body>
    <div class="card">
        @if (!empty($imageUrl))
            <div class="hero">
                <img src="{{ $imageUrl }}" alt="{{ $title }}">
            </div>
        @endif
        <div class="content">
            <h1>{{ $title }}</h1>
            <p>{{ $description }}</p>
            <a href="{{ $redirectUrl }}">{{ $ctaLabel ?? 'Open link' }}</a>
        </div>
    </div>
</body>
</html>
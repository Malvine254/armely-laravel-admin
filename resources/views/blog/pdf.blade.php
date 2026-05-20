<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'DejaVu Sans', Arial, sans-serif;
        font-size: 10.5pt;
        color: #1f2937;
        background: #ffffff;
        line-height: 1.7;
    }

    /* ── Header ── */
    .page-header {
        background: #1e3a6d;
        padding: 16px 32px;
    }
    .page-header-inner { display: table; width: 100%; }
    .header-logo-cell  { display: table-cell; vertical-align: middle; width: 55%; }
    .header-label-cell {
        display: table-cell; vertical-align: middle; text-align: right;
        color: #9cc8ff; font-size: 8.5pt; letter-spacing: 1px;
        text-transform: uppercase; font-weight: 700;
    }
    .logo-wordmark {
        font-size: 22pt; font-weight: 700; color: #ffffff; letter-spacing: -0.5px;
    }
    .logo-wordmark span { color: #7ab3f4; }
    .logo-tagline {
        font-size: 7.5pt; color: #9cc8ff; letter-spacing: 1.8px;
        text-transform: uppercase; margin-top: 2px;
    }
    .accent-bar { height: 4px; background: #4a8fd4; }

    /* ── Cover band ── */
    .cover-band {
        background: #f5f8ff;
        border-bottom: 3px solid #2f5597;
        padding: 26px 32px 22px;
    }
    .article-eyebrow {
        font-size: 8pt; color: #2f5597; text-transform: uppercase;
        letter-spacing: 1.6px; font-weight: 700; margin-bottom: 10px;
    }
    .article-title {
        font-size: 20pt; font-weight: 700; color: #153462;
        line-height: 1.2; margin-bottom: 16px;
    }
    .article-meta-row {
        display: table; width: 100%; font-size: 8.5pt; color: #4b6388;
        border-top: 1px solid #dbe7ff; padding-top: 12px;
    }
    .meta-left  { display: table-cell; vertical-align: middle; }
    .meta-right { display: table-cell; vertical-align: middle; text-align: right; color: #2f5597; font-weight: 600; }
    .meta-pill {
        display: inline-block; background: #e8f0fd; border: 1px solid #c4d6f5;
        border-radius: 20px; padding: 2px 10px; margin-right: 8px;
        font-size: 8pt; color: #2f5597; font-weight: 600;
    }

    /* ── Featured image ── */
    .featured-image-wrap {
        margin: 22px 32px 0;
        text-align: center;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #dbe7ff;
    }
    .featured-image-wrap img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 0 auto;
    }

    /* ── Body ── */
    .article-body {
        padding: 22px 32px 32px;
        font-size: 10.5pt; color: #1f2937; line-height: 1.75;
    }
    .article-body p { margin-bottom: 11px; }
    .article-body h1, .article-body h2 {
        font-size: 15pt; color: #153462; font-weight: 700;
        margin: 24px 0 10px; padding-bottom: 6px; border-bottom: 2px solid #dbe7ff;
    }
    .article-body h3 {
        font-size: 12.5pt; color: #2f5597; font-weight: 700; margin: 20px 0 8px;
    }
    .article-body h4, .article-body h5, .article-body h6 {
        font-size: 11pt; color: #2f5597; font-weight: 700; margin: 16px 0 7px;
    }
    .article-body ul, .article-body ol { margin: 8px 0 12px 22px; }
    .article-body li { margin-bottom: 5px; }
    .article-body blockquote {
        margin: 16px 0; padding: 12px 18px;
        background: #f0f5ff; border-left: 4px solid #2f5597;
        color: #324a73; font-style: italic; border-radius: 0 6px 6px 0;
    }
    .article-body table {
        width: 100%; border-collapse: collapse; margin: 14px 0; font-size: 9.5pt;
    }
    .article-body th {
        background: #2f5597; color: #fff; padding: 8px 11px;
        text-align: left; font-weight: 700;
    }
    .article-body td {
        padding: 7px 11px; border: 1px solid #dbe7ff; vertical-align: top;
    }
    .article-body tr:nth-child(even) td { background: #f5f8ff; }
    .article-body img {
        max-width: 100%;
        width: auto;
        height: auto;
        display: block;
        margin: 14px auto;
        border-radius: 6px;
        border: 1px solid #dbe7ff;
    }
    .article-body a { color: #2f5597; text-decoration: underline; word-break: break-all; }
    .article-body strong, .article-body b { color: #153462; }
    .article-body pre, .article-body code {
        font-family: 'DejaVu Sans Mono', monospace; font-size: 9pt;
        background: #f1f5f9; border-radius: 4px; padding: 2px 6px;
    }
    .article-body pre {
        padding: 11px 15px; margin: 10px 0; white-space: pre-wrap;
        word-break: break-word; border-left: 3px solid #2f5597;
    }

    /* ── Footer ── */
    .section-divider { border: none; border-top: 1px solid #dbe7ff; margin: 20px 32px 0; }
    .pdf-footer {
        border-top: 2px solid #dbe7ff; padding: 14px 32px;
        background: #f7faff; display: table; width: 100%;
        font-size: 8pt; color: #7b8fad;
    }
    .footer-left  { display: table-cell; vertical-align: middle; }
    .footer-right { display: table-cell; vertical-align: middle; text-align: right; color: #2f5597; font-weight: 700; font-size: 9pt; }
</style>
</head>
<body>

{{-- Header --}}
<div class="page-header">
    <div class="page-header-inner">
        <div class="header-logo-cell">
            <div class="logo-wordmark">armely<span>&reg;</span></div>
            <div class="logo-tagline">Technology &amp; Business Solutions</div>
        </div>
        <div class="header-label-cell">Insights &nbsp;&bull;&nbsp; Blog Article</div>
    </div>
</div>
<div class="accent-bar"></div>

{{-- Cover --}}
<div class="cover-band">
    <div class="article-eyebrow">&#9632;&nbsp; Armely Blog</div>
    <div class="article-title">{{ $blog->title }}</div>
    <div class="article-meta-row">
        <div class="meta-left">
            <span class="meta-pill">By {{ $blog->author ?? 'Armely Team' }}</span>
            @if($blog->date)
                <span class="meta-pill">{{ \Carbon\Carbon::parse($blog->date)->format('F j, Y') }}</span>
            @endif
        </div>
        <div class="meta-right">armely.com/blog/{{ $blog->blog_id }}</div>
    </div>
</div>

{{-- Featured image --}}
@if($blog->image_path)
@php
    $imgPath = $blog->image_path;
    if (str_starts_with($imgPath, 'http://') || str_starts_with($imgPath, 'https://')) {
        $featuredSrc = $imgPath;
        $featuredExists = true;
    } else {
        $featuredSrc = public_path(ltrim($imgPath, '/'));
        $featuredExists = file_exists($featuredSrc);
    }
@endphp
@if($featuredExists)
<div class="featured-image-wrap">
    <img src="{{ $featuredSrc }}" alt="{{ $blog->title }}">
</div>
@endif
@endif

{{-- Article body --}}
<div class="article-body">
    {!! $bodyHtml !!}
</div>

<hr class="section-divider">

{{-- Footer --}}
<div class="pdf-footer">
    <div class="footer-left">
        &copy; {{ date('Y') }} Armely &middot; armely.com &middot; All rights reserved. &nbsp;
        Generated {{ now()->format('F j, Y') }}.
    </div>
    <div class="footer-right">armely.com</div>
</div>

</body>
</html>

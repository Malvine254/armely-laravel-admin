@extends('layouts.public')

@php
    $title = 'Home';
@endphp

@section('title', 'Armely | Microsoft Data, AI, and Digital Transformation Services')
@section('meta_description', 'Armely helps organizations modernize with Microsoft Fabric, Power BI, Copilot, Power Platform, and advisory services for measurable outcomes.')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/job-board-modern.css') }}?v={{ filemtime(public_path('css/job-board-modern.css')) }}">
<style>
    .portfolio .section-title h2,
    .blog .section-title h2,
    .video-section .section-title h2 {
        font-size: 2rem;
        font-weight: 800;
        color: #1a1a1a;
        margin-bottom: 0;
    }

    .clients-section .section-title h2 {
        font-size: 2.2rem;
        font-weight: 900;
        background: linear-gradient(135deg, #1E62AD 0%, #2f5597 50%, #1E62AD 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0;
        letter-spacing: -1px;
        text-shadow: 0 0 80px rgba(30, 98, 173, 0.1);
        position: relative;
        display: inline-block;
    }

    .clients-section .section-title p {
        color: #475569;
        font-size: 1.1rem;
        line-height: 1.7;
        font-weight: 400;
    }
    
    .portfolio .hr,
    .blog .hr,
    .video-section .hr {
        width: 100px;
        height: 6px;
        border: none;
        border-radius: 5px;
        margin-top: 15px;
        background: #2f5597 !important;
    }

    .clients-section .hr {
        width: 120px;
        height: 6px;
        border: none;
        border-radius: 10px;
        margin-top: 20px;
        background: linear-gradient(90deg, 
            transparent 0%, 
            rgba(30, 98, 173, 0.3) 20%, 
            #1E62AD 50%, 
            rgba(30, 98, 173, 0.3) 80%, 
            transparent 100%);
        box-shadow: 0 4px 20px rgba(30, 98, 173, 0.25);
    }

    .portfolio .single-pf {
        padding: 15px !important;
    }

    .portfolio .card-wrapper {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: box-shadow 0.25s ease, border-color 0.25s ease;
        height: 100%;
        border: 1px solid rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
    }

    .portfolio .card-wrapper:hover {
        transform: none;
        box-shadow: 0 14px 34px rgba(47, 85, 151, 0.14);
        border-color: rgba(47, 85, 151, 0.2);
    }

    .portfolio .image-container {
        position: relative;
        height: 180px;
        overflow: hidden;
        margin: 15px;
        border-radius: 15px;
    }

    .portfolio .image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: none;
    }

    .portfolio .case-study-default-image {
        width: 100%;
        height: 100%;
        min-height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #2f5597 0%, #1e3a6d 100%);
        color: rgba(255, 255, 255, 0.9);
    }

    .portfolio .case-study-default-image i {
        font-size: 3rem;
        line-height: 1;
    }

    .portfolio .card-wrapper:hover .image-container img {
        transform: none;
    }

    .portfolio .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(0,0,0,0.4) 0%, transparent 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .portfolio .card-wrapper:hover .image-overlay {
        opacity: 1;
    }

    .portfolio .card-body {
        padding: 0 25px 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .portfolio .label {
        font-size: 0.7rem;
        font-weight: 800;
        color: #2f5597;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        margin-bottom: 8px;
        display: block;
        opacity: 0.8;
    }

    .portfolio .card-body h4 {
        font-size: 1.3rem;
        font-weight: 800;
        color: #1a1a1a;
        line-height: 1.2;
        margin-bottom: 15px;
        transition: color 0.3s ease;
        min-height: 2.4em;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .portfolio .card-wrapper:hover .card-body h4 {
        color: #2f5597;
    }

    .portfolio .card-body p {
        font-size: 0.9rem;
        color: #555;
        line-height: 1.5;
        margin-bottom: 20px;
        min-height: 4.5em;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    .portfolio .card-btn {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        flex-wrap: nowrap;
        white-space: nowrap;
        width: 100%;
        min-height: 48px;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: background-color 0.2s ease, box-shadow 0.2s ease;
        border: none;
        position: relative;
        z-index: 10;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .portfolio .card-btn:hover {
        transform: none;
        box-shadow: 0 5px 12px rgba(0,0,0,0.16);
        color: #fff !important;
        text-decoration: none;
    }

    .portfolio .card-btn:active {
        transform: none;
    }

    .portfolio .card-wrapper:hover .card-btn {
        opacity: 1;
    }

    .portfolio .card-btn .card-btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 14px;
        height: 14px;
        flex-shrink: 0;
        transform: translateY(0.5px);
    }

    .portfolio .card-btn .card-btn-icon svg {
        width: 14px;
        height: 14px;
        display: block;
        stroke: currentColor;
        stroke-width: 2.4;
        stroke-linecap: round;
        stroke-linejoin: round;
        fill: none;
    }

    .portfolio .owl-nav,
    .portfolio .owl-dots {
        display: none !important;
    }

    .portfolio .explore-btn,
    .blog .explore-btn,
    .video-section .explore-btn {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        color: #55637c;
        font-size: 0.95rem;
        font-weight: 600;
        text-decoration: none;
        padding: 13px 26px;
        border-radius: 999px;
        border: 1px solid #d4dff0;
        background: #ffffff;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, color 0.2s ease;
        white-space: nowrap;
    }

    .portfolio .explore-btn:hover,
    .blog .explore-btn:hover,
    .video-section .explore-btn:hover {
        transform: translateY(-1px);
        gap: 9px;
        color: #2f5597;
        border-color: #bfd0ea;
        box-shadow: 0 12px 22px rgba(15, 23, 42, 0.07);
    }

    .blog .explore-btn i,
    .video-section .explore-btn i {
        font-size: 0.95rem;
    }

    .blog .explore-btn strong,
    .video-section .explore-btn strong {
        font-weight: 600;
    }

    /* Blog Section Enhancements */
    .blog.section {
        background: #f5f7fb;
        padding: 78px 0;
    }

    .blog.section .row {
        row-gap: 28px;
    }

    .blog.section .row > [class*='col-'] {
        display: flex;
        justify-content: center;
    }

    .blog.section .blog-card-wrapper {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        margin-bottom: 0;
        border: 1px solid rgba(47, 85, 151, 0.1);
        text-align: left;
        width: 100%;
        max-width: 320px;
    }

    .blog.section .blog-card-wrapper:hover {
        transform: translateY(-4px);
        border-color: rgba(47, 85, 151, 0.18);
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.09);
    }

    .blog.section .blog-image-box {
        position: relative;
        height: 180px;
        min-height: 180px;
        max-height: 180px;
        aspect-ratio: auto;
        overflow: hidden;
        background: linear-gradient(135deg, #2f5597 0%, #2a4b87 52%, #244177 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0;
        flex-shrink: 0;
        line-height: 0;
        padding: 0;
    }

    .blog.section .blog-image-box::before {
        content: '';
        position: absolute;
        inset: -20px;
        background-image: var(--blog-image);
        background-size: cover;
        background-position: center;
        filter: blur(28px) saturate(1.15);
        transform: scale(1.18);
        opacity: 0.28;
        pointer-events: none;
    }

    .blog.section .blog-image-box::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(19, 38, 74, 0.14) 0%, rgba(24, 46, 88, 0.08) 48%, rgba(17, 34, 66, 0.22) 100%);
        pointer-events: none;
    }

    .blog.section .blog-image-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.45s ease, filter 0.45s ease;
        transform: scale(1);
        padding: 0;
        box-sizing: border-box;
        display: block;
        margin: 0;
        position: relative;
        z-index: 2;
    }

    .blog.section .blog-card-wrapper:hover .blog-image-box img {
        transform: scale(1.03);
    }

    /* title overlay inside image */
    .blog-image-overlay {
        position: absolute;
        bottom: 20px;
        left: 20px;
        right: 20px;
        color: #fff;
        z-index: 3;
        text-shadow: 0 2px 8px rgba(0,0,0,0.6);
        font-size: 1.4rem;
        font-weight: 800;
        line-height: 1.2;
        overflow: hidden;
        max-height: 3em;
    }

    .blog-image-overlay a {
        color: #fff;
        text-decoration: none;
    }

    .blog.section .blog-content {
        padding: 11px 13px 13px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
        position: relative;
    }

    .blog.section .blog-author-info {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        min-width: 0;
    }

    .blog.section .author-avatar {
        width: 28px;
        height: 28px;
        border-radius: 11px;
        object-fit: cover;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
        background: #f4f7fb;
        flex: 0 0 auto;
    }

    .blog.section .author-details {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8rem;
        color: #333;
        font-weight: 600;
        min-width: 0;
        width: 100%;
    }

    .blog.section .author-details span:first-child {
        color: #1f2937;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .blog.section .author-details span:last-child {
        color: #64748b;
        font-size: 0.72rem;
        display: flex;
        align-items: center;
        gap: 4px;
        flex: 0 0 auto;
        white-space: nowrap;
    }

    .blog.section .blog-title {
        font-size: 0.88rem;
        font-weight: 600;
        color: #111827;
        text-align: left !important;
        line-height: 1.22;
        margin-bottom: 6px;
        transition: color 0.3s ease;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.1em;
        font-family: 'Poppins', sans-serif;
        letter-spacing: 0;
    }

    .blog.section .blog-title a,
    .blog.section .blog-snippet,
    .blog.section .blog-author-info,
    .blog.section .blog-footer {
        text-align: left !important;
        width: 100%;
    }

    .blog.section .blog-title a {
        font-weight: 600;
    }

    .blog.section .blog-snippet {
        font-size: 0.79rem;
        color: #5b6472;
        line-height: 1.38;
        margin-bottom: 7px;
        flex-grow: 1;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.8em;
    }

    .blog.section .blog-title[title],
    .blog.section .blog-snippet[title] {
        cursor: help;
    }

    .blog.section .blog-card-wrapper:hover .blog-title {
        color: #2f5597;
    }

    .blog.section .blog-footer {
        margin-top: auto;
        padding-top: 8px;
        border-top: 1px solid #edf1f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }

    .blog.section .blog-date {
        font-size: 0.72rem;
        font-weight: 600;
        color: #64748b;
        letter-spacing: -0.1px;
    }

    .blog.section .blog-btn-circle {
        background: #2f5597;
        color: #fff !important;
        width: 32px;
        height: 32px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 8px 18px rgba(47, 85, 151, 0.22);
        border: none;
        text-decoration: none;
    }

    .blog.section .blog-btn-circle i {
        font-weight: 900;
        font-size: 0.84rem;
        color: #fff !important;
    }

    .blog.section .blog-card-wrapper:hover .blog-btn-circle {
        background: #111827;
        color: #fff !important;
        transform: scale(1.08) rotate(-45deg);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    @media (max-width: 768px) {
        .blog.section { padding: 60px 0; }
        .blog.section .blog-card-wrapper { margin-bottom: 0; }
        .blog.section .blog-title { font-size: 0.88rem; font-weight: 600; min-height: auto; }
        .blog.section .blog-image-box {
            height: 168px;
            min-height: 168px;
            max-height: 168px;
            padding: 8px;
        }
        .blog.section .blog-content {
            padding: 11px 12px 13px;
        }
        .blog.section .blog-snippet {
            min-height: auto;
            -webkit-line-clamp: 2;
        }
        .blog.section .blog-btn-circle {
            width: 32px;
            height: 32px;
        }
        .blog.section .author-details {
            flex-wrap: wrap;
            row-gap: 2px;
        }
    }

    @media (max-width: 480px) {
        .blog.section { padding: 40px 0; }
        .blog.section .blog-image-box {
            height: 150px;
            min-height: 150px;
            max-height: 150px;
            padding: 6px;
        }
        .blog.section .blog-title { font-size: 0.88rem; min-height: auto; }
        .blog.section .blog-snippet { font-size: 0.8rem; -webkit-line-clamp: 2; min-height: auto; }
        .blog.section .blog-content { padding: 10px 11px 12px; }
    }

    @media (max-width: 576px) {
        .blog .explore-btn,
        .video-section .explore-btn {
            padding: 12px 20px;
            font-size: 0.9rem;
        }
    }

    /* Clients/Brands Section */
    .clients-section {
        padding: 110px 0;
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(820px 460px at 8% 8%, rgba(30, 98, 173, 0.16) 0%, rgba(30, 98, 173, 0) 62%),
            radial-gradient(760px 430px at 92% 88%, rgba(15, 38, 78, 0.12) 0%, rgba(15, 38, 78, 0) 65%),
            linear-gradient(145deg, #f4f8ff 0%, #eef4fd 42%, #f8fbff 100%);
    }

    .clients-section .container {
        position: relative;
        z-index: 2;
    }

    .clients-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            linear-gradient(120deg, rgba(30, 98, 173, 0.07) 0%, rgba(30, 98, 173, 0) 35%),
            linear-gradient(300deg, rgba(47, 85, 151, 0.06) 0%, rgba(47, 85, 151, 0) 40%);
        pointer-events: none;
    }

    .clients-section::after {
        content: '';
        position: absolute;
        inset: 0;
        background-image: linear-gradient(rgba(15, 38, 78, 0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(15, 38, 78, 0.04) 1px, transparent 1px);
        background-size: 64px 64px;
        opacity: 0.25;
        pointer-events: none;
        mask-image: radial-gradient(circle at center, #000 18%, transparent 85%);
    }

    .partner-carousel {
        min-height: 100%;
    }

    .partner-carousel .m-4 {
        background: #ffffff;
        border: 1px solid #dbe7fb;
        border-radius: 20px;
        padding: 24px 18px;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        height: 260px;
        min-height: 260px;
        max-height: 260px;
        flex-shrink: 0;
        box-shadow: 0 10px 24px rgba(16, 34, 66, 0.08);
        position: relative;
        margin: 12px !important;
    }

    .partner-carousel .m-4::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 20px;
        pointer-events: none;
        background: linear-gradient(160deg, rgba(30, 98, 173, 0.07) 0%, rgba(30, 98, 173, 0) 58%);
    }

    .partner-carousel .m-4:hover {
        transform: translateY(-4px);
        border-color: #aec7ef;
        box-shadow: 0 16px 32px rgba(16, 34, 66, 0.12);
    }

    .partner-carousel .partner-logo {
        width: 100%;
        height: 106px;
        min-height: 106px;
        max-height: 106px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        position: relative;
        padding: 8px;
        border-radius: 14px;
        background: #f7faff;
        border: 1px solid #e4eefc;
        transition: background-color 0.25s ease, border-color 0.25s ease;
        flex-shrink: 0;
    }

    .partner-carousel .m-4:hover .partner-logo {
        background: #ffffff;
        border-color: #bfd6f6;
    }

    .partner-carousel img {
        filter: grayscale(8%) contrast(1.05);
        opacity: 0.96;
        transition: filter 0.25s ease, opacity 0.25s ease;
        max-height: 100%;
        height: 100%;
        max-width: 100%;
        width: 100%;
        margin: 0 auto;
        object-fit: contain;
        position: relative;
        z-index: 1;
    }

    .partner-carousel .m-4:hover img {
        filter: grayscale(0%) contrast(1.08);
        opacity: 1;
    }

    .partner-carousel .partner-name {
        font-size: 12px;
        font-weight: 800;
        color: #1f3e73;
        text-align: center;
        line-height: 1.25;
        max-width: 172px;
        letter-spacing: 0.25px;
        text-transform: capitalize;
        position: relative;
    }

    @media (max-width: 768px) {
        .clients-section {
            padding: 72px 0;
        }

        .clients-section .section-title h2 {
            font-size: 1.9rem;
        }

        .partner-carousel .m-4 {
            height: 226px;
            min-height: 226px;
            max-height: 226px;
            padding: 18px 14px;
            margin: 8px !important;
        }

        .partner-carousel .partner-logo {
            height: 90px;
            min-height: 90px;
            max-height: 90px;
        }

        .partner-carousel .partner-name {
            font-size: 11px;
            max-width: 150px;
        }
    }

    @media (max-width: 480px) {
        .clients-section {
            padding: 54px 0;
        }

        .clients-section .section-title h2 {
            font-size: 1.55rem;
        }

        .clients-section .section-title p {
            font-size: 0.92rem;
            line-height: 1.55;
        }

        .partner-carousel .m-4 {
            height: 206px;
            min-height: 206px;
            max-height: 206px;
            padding: 14px 10px;
            margin: 6px !important;
        }
    }

    /* Video Section Enhancements */
    .video-section {
        padding: 72px 0 56px;
        background: #fcfcfc;
    }

    .video-card-wrapper {
        position: relative;
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.4s ease;
        margin-bottom: 30px;
        border: 1px solid rgba(0,0,0,0.02);
    }

    .video-card-wrapper:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.12);
    }

    /* Mobile adjustments for video section */
    @media (max-width: 768px) {
        .video-section {
            padding: 60px 0;
        }
        .video-section .section-title h2 {
            font-size: 1.6rem;
        }
        .video-info {
            padding: 15px 20px;
        }
        .video-title-text {
            font-size: 1rem !important;
        }
        .play-button-modern {
            width: 55px;
            height: 55px;
            font-size: 18px;
        }
        .video-card-wrapper {
            margin-bottom: 20px;
        }
    }

    @media (max-width: 480px) {
        .video-section { padding: 40px 0; }
        .video-section .section-title h2 { font-size: 1.3rem; }
        .play-button-modern {
            width: 45px;
            height: 45px;
            font-size: 16px;
        }
        .video-title-text { font-size: 0.9rem !important; }
    }

    /* Portfolio / Case Studies responsive */
    @media (max-width: 768px) {
        .portfolio .section-title h2,
        .blog .section-title h2,
        .video-section .section-title h2 {
            font-size: 1.6rem;
        }
        .portfolio .image-container {
            height: 150px;
        }
        .portfolio .card-body {
            padding: 0 20px 20px;
        }
        .portfolio .card-body h4 {
            font-size: 1.1rem;
            min-height: auto;
        }
        .portfolio .card-body p {
            font-size: 0.85rem;
            min-height: auto;
            -webkit-line-clamp: 2;
        }

    }

    @media (max-width: 480px) {
        .portfolio .section-title h2,
        .blog .section-title h2,
        .video-section .section-title h2 {
            font-size: 1.3rem;
        }
        .portfolio .image-container {
            height: 120px;
            margin: 10px;
            border-radius: 10px;
        }
        .portfolio .card-body {
            padding: 0 15px 15px;
        }
    }

    /* General mobile layout fixes */
    @media (max-width: 768px) {
        .section { padding: 50px 0; }
        .section-title { padding: 0 15px; margin-bottom: 25px; }
        .section-title h2 { font-size: 1.5rem; }
        .container { padding-left: 15px; padding-right: 15px; }
        .schedule .single-schedule { margin-bottom: 20px; }
        .appointment .form { padding: 25px !important; }
        .fun-facts .single-fun { margin: 15px 0; }
        .explore-btn { font-size: 1rem; }
    }

    @media (max-width: 480px) {
        .section { padding: 35px 0; }
        .section-title h2 { font-size: 1.3rem; }
        .schedule .single-schedule { min-block-size: auto !important; }
        .appointment .form { padding: 15px !important; }
        .appointment label { font-size: 0.85rem; }
        .appointment input, .appointment textarea { font-size: 0.9rem; }
    }

    /* Slider responsive */
    @media (max-width: 768px) {
        .slider .single-slider h1 { font-size: 1.5rem; line-height: 1.3; }
        .slider .single-slider p { font-size: 0.9rem; }
        .slider .single-slider .button .btn { padding: 8px 16px; font-size: 0.85rem; }
    }

    @media (max-width: 480px) {
        .slider .single-slider h1 { font-size: 1.2rem; }
        .slider .single-slider .button .btn { padding: 6px 12px; font-size: 0.8rem; }
    }

    /* Ensure stats icon ring stays fully visible and not clipped */
    .fun-facts .single-fun {
        position: relative;
        padding-left: 98px;
        min-height: 84px;
    }

    .fun-facts .single-fun i {
        width: 76px;
        height: 76px;
        margin-top: -38px;
        line-height: 1;
        padding: 14px;
        box-sizing: border-box;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .fun-facts .single-fun .content {
        padding-left: 0;
    }

    .fun-facts .single-fun .metric-value,
    .fun-facts .single-fun .years-metric {
        white-space: nowrap;
        display: inline-block;
    }

    .fun-facts .single-fun .metric-value .counter,
    .fun-facts .single-fun .years-metric .counter {
        display: inline !important;
    }

    .fun-facts.no-radius,
    #fun-facts.no-radius {
        border-radius: 0 !important;
        overflow: visible;
    }

    @media (max-width: 768px) {
        .fun-facts .single-fun {
            padding-left: 0;
            min-height: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .fun-facts .single-fun i {
            width: 74px;
            height: 74px;
            position: relative !important;
            left: auto !important;
            top: auto !important;
            transform: none !important;
            margin: 0 0 10px;
            padding: 13px;
        }

        .fun-facts .single-fun .content {
            padding-left: 0 !important;
            text-align: center;
        }

        .fun-facts .single-fun span {
            line-height: 1.1;
            margin-bottom: 8px;
        }
    }

    .lazy-video {
        position: relative;
        aspect-ratio: 16/9;
        cursor: pointer;
    }

    .play-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .lazy-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .video-card-wrapper:hover .lazy-thumb {
        transform: scale(1.05);
    }

    .play-button-modern {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 70px;
        height: 70px;
        background: var(--default-background);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 24px;
        box-shadow: 0 0 0 0 rgba(47, 85, 151, 0.4);
        animation: pulse-blue 2s infinite;
        z-index: 5;
        transition: all 0.3s ease;
    }

    .video-card-wrapper:hover .play-button-modern {
        background: #fff;
        color: var(--default-background);
        transform: translate(-50%, -50%) scale(1.1);
    }

    .video-info {
        padding: 20px 25px;
    }

        .video-tag {
        font-size: 0.7rem;
        font-weight: 800;
        color: var(--default-background);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        display: block;
    }

    .video-title-text {
        font-size: 1.1rem;
        font-weight: 800;
        color: #1a1a1a;
        line-height: 1.4;
        margin-bottom: 0;
    }

    @keyframes pulse-blue {
        0% { transform: translate(-50%, -50%) scale(0.95); box-shadow: 0 0 0 0 rgba(47, 85, 151, 0.7); }
        70% { transform: translate(-50%, -50%) scale(1); box-shadow: 0 0 0 15px rgba(47, 85, 151, 0); }
        100% { transform: translate(-50%, -50%) scale(0.95); box-shadow: 0 0 0 0 rgba(47, 85, 151, 0); }
    }

</style>
@endpush

@section('content')
<main id="main-content" role="main">
@include('partials.home-hero-1000')
{{-- Legacy homepage sections kept for rollback but disabled below. --}}
@if(false)
<section class="slider">
    <div class="hero-slider">
        <div class="single-slider" style="background-image:url('{{ asset('images/sliders/slider-1.webp') }}')" role="img" aria-label="Digital Excellence Banner">
            <div class="container"><div class="row"><div class="col-lg-7"><div class="text">
                <h1><span class="text-light">Your Trusted Source For Digital Excellence</span></h1>
                <p class="text-light">Beyond Imagination</p>
                <div class="button">
                    <a href="/services#consultation-form" class="btn">Get Appointment</a>
                </div>
            </div></div></div></div>
        </div>
        <div class="single-slider" style="background-image:url('{{ asset('images/sliders/slider-2.webp') }}')">
            <div class="container"><div class="row"><div class="col-lg-7"><div class="text">
                <h1><span class="text-light"> We Provide AI Services That You Can Trust!</span></h1>
                <p class="text-light">Beyond Imagination</p>
                <div class="button">
                    <a href="/services#consultation-form" class="btn">Get Appointment</a>
                    <a href="/company" class="btn primary">About Us</a>
                </div>
            </div></div></div></div>
        </div>
    </div>
</section>

<main id="main-content" role="main">
<section class="schedule">
    <div class="container"><div class="schedule-inner"><div class="row">
        @foreach($offers as $offer)
            <div class="col-lg-4 col-md-6 col-12">
                <div class="single-schedule first" style="block-size: auto; min-block-size: 400px;">
                    <div class="inner">
                        <div class="icon"><i class="fa fa-data"></i></div>
                        <div class="single-content">
                            <img src="{{ $offer->image_path }}" class="img-fluid lazy-img" loading="lazy" decoding="async" alt="Offer Image">
                            <div><h4>{{ $offer->title }}</h4></div>
                            <span style="color: white !important;" class="shorten-content text-light"><b class="text-light">{{ $offer->body }}</b></span>
                            <a href="#" class="read-more-btn text-light">READ MORE <i class="fa fa-long-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div></div></div>
</section>

<section id="clients" class="clients-section section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title text-center mb-5">
                    <h2>Brands that Trust Us</h2>
                    <center><hr class="default-background hr"></center>
                    <p class="text-center mt-3" style="max-width: 800px; margin: 0 auto; color: #666; font-size: 1.1rem; line-height: 1.6;">
                        We’re proud to work alongside some truly trusted brands. Together, we’re focused on delivering real value, innovation, and quality. A huge thank you to our partners for their ongoing support and commitment to excellence.
                    </p>
                </div>
            </div>
        </div>
        <div class="owl-carousel clients-carousel partner-carousel mt-4">
            <div class="m-4"><a class="partner-logo" href="{{ asset('images/brand-partners/university_of_nebrask1.png') }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset('images/brand-partners/university_of_nebrask1.png') }}" class="img-fluid lazy-img" loading="lazy" decoding="async" alt="University of Nebraska Medical Center logo" width="200" height="80"></a><div class="partner-name">University of Nebraska Medical Center</div></div>
            <div class="m-4"><a class="partner-logo" href="{{ asset('images/brand-partners/swope_health.png') }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset('images/brand-partners/swope_health.png') }}" alt="Swope Health Services logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a><div class="partner-name">Swope Health Services</div></div>
            <div class="m-4"><a class="partner-logo" href="{{ asset('images/brand-partners/esse_health.jpg') }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset('images/brand-partners/esse_health.jpg') }}" alt="Esse Health logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a><div class="partner-name">Esse Health</div></div>
            <div class="m-4"><a class="partner-logo" href="{{ asset('images/brand-partners/sage_bute.webp') }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset('images/brand-partners/sage_bute.webp') }}" alt="Sage Butte Energy logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a><div class="partner-name">Sage Butte Energy</div></div>
            <div class="m-4"><a class="partner-logo" href="{{ asset('images/brand-partners/qb_energy.jpg') }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset('images/brand-partners/qb_energy.jpg') }}" alt="QB Energy logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a><div class="partner-name">QB Energy</div></div>
            <div class="m-4"><a class="partner-logo" href="{{ asset('images/brand-partners/frisco.jpeg') }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset('images/brand-partners/frisco.jpeg') }}" alt="City of Frisco Texas logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a><div class="partner-name">City of Frisco</div></div>
            <div class="m-4"><a class="partner-logo" href="{{ asset('images/brand-partners/dallas_county.jpg') }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset('images/brand-partners/dallas_county.jpg') }}" alt="Dallas County logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a><div class="partner-name">Dallas County</div></div>
            <div class="m-4"><a class="partner-logo" href="{{ asset('images/brand-partners/lambda.png') }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset('images/brand-partners/lambda.png') }}" alt="Lambda Legal logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a><div class="partner-name">Lambda Legal</div></div>
            <div class="m-4"><a class="partner-logo" href="{{ asset('images/brand-partners/homeward_bound.png') }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset('images/brand-partners/homeward_bound.png') }}" alt="Homeward Bound logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a><div class="partner-name">Homeward Bound</div></div>
            <div class="m-4"><a class="partner-logo" href="{{ asset('images/brand-partners/mhc.png') }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset('images/brand-partners/mhc.png') }}" alt="MHC logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a><div class="partner-name">MHC</div></div>
            <div class="m-4"><a class="partner-logo" href="{{ asset('images/brand-partners/bnsf.png') }}" target="_blank" rel="noopener noreferrer"><img src="{{ asset('images/brand-partners/bnsf.png') }}" alt="BNSF Railway logo" class="img-fluid lazy-img" loading="lazy" decoding="async" width="200" height="80"></a><div class="partner-name">BNSF Railway</div></div>
        </div>
    </div>
</section>

<section class="portfolio py-5">
    <div class="container">
        @php
            $caseStudyCount = collect($industryListings ?? [])->count();
            $caseStudies = collect($industryListings ?? [])->take(6);
        @endphp
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title text-center mb-5">
                    <h2>{{ $caseStudyCount }} published outcomes. Real clients. Documented results.</h2>
                    <center><hr class="default-background hr"></center>
                </div>
            </div>
        </div>

        <div class="cases-grid">
            @forelse($caseStudies as $listing)
                @php
                    $caseStudyTitle = trim((string) ($listing->title ?? ''));
                    $caseStudyTitle = $caseStudyTitle !== '' ? $caseStudyTitle : trim((string) ($listing->category ?? 'Case Study'));
                    $caseStudyTitle = $caseStudyTitle !== '' ? $caseStudyTitle : 'Case Study';
                    $caseStudyTitle = \Illuminate\Support\Str::limit($caseStudyTitle, 52, '...');
                    $caseStudyTag = trim((string) ($listing->category ?? 'Case Study'));
                    $caseStudyTag = $caseStudyTag !== '' ? $caseStudyTag : 'Case Study';
                    $caseStudyDesc = \Illuminate\Support\Str::limit((string) ($listing->excerpt ?? ''), 140, '...');
                @endphp
                <article class="case-card">
                    <div class="case-body">
                        <div class="case-tag">{{ $caseStudyTag }}</div>
                        <h3 class="case-title">{{ $caseStudyTitle }}</h3>
                        <p class="case-desc">{{ $caseStudyDesc }}</p>
                        <a href="{{ route('case-studies.show', $listing->slug) }}" class="case-link">
                            <span>See Case Study</span>
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted mb-0">Case studies will appear here once they are published.</p>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-5">
            <a class="explore-btn" href="/case-studies">
                <strong>View {{ $caseStudyCount }} Case Studies</strong>
                <i class="fa fa-long-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<section id="fun-facts" class="fun-facts default-background section col-md-12 mt-5 no-radius" aria-labelledby="stats-heading">
    <div class="container">
        <h2 id="stats-heading" class="sr-only">Customer Statistics</h2>
        <div class="row" style="content-visibility:auto; contain-intrinsic-size: auto 300px;">
            <div class="col-lg-4 col-md-6 col-12"><div class="single-fun"><i class="icofont icofont-ui-user-group"></i><div class="content"><span class="metric-value"><span class="counter">85</span>%+</span><p>Client Retention</p></div></div></div>
            <div class="col-lg-4 col-md-6 col-12"><div class="single-fun"><i class="icofont icofont-users-social"></i><div class="content"><span class="metric-value"><span class="counter">90</span>%+</span><p>Client Satisfaction / NPS</p></div></div></div>
            <div class="col-lg-4 col-md-6 col-12"><div class="single-fun"><i class="icofont icofont-badge"></i><div class="content"><span class="years-metric"><span class="counter">9</span>&nbsp;Years</span><p>Years of Delivery</p></div></div></div>
        </div>
    </div>
</section>

@endif
<section class="blog section" id="blog">
    <div class="armely-home-shell">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title text-center mb-5">
                    <h2>Our Most Recent Blog Articles</h2>
                    <center><hr class="default-background hr"></center>
                </div>
            </div>
        </div>
        
        <div class="row">
            @forelse($blogs as $blog)
                @php($blogFullTitle = trim(strip_tags((string) ($blog->title ?? 'Blog Article'))))
                        @php($blogFullSnippet = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($blog->preview ?? '')))))
                        @php($blogFullDetails = trim($blogFullTitle . "\n" . ($blogFullSnippet !== '' ? $blogFullSnippet : '')))
                        @php($blogImageUrl = $blog->image_path ?: asset('images/blog/default.svg'))
                <div class="col-lg-4 col-md-6 col-12" data-aos="fade-up">
                    <article class="blog-card-wrapper">
                        <div class="blog-image-box" style="--blog-image: url('{{ $blogImageUrl }}');">
                            <img class="lazy-img" loading="lazy" 
                                 src="{{ $blogImageUrl }}" 
                                 alt="{{ $blogFullTitle }}"
                                 onerror="this.src='{{ asset('images/blog/default.svg') }}'">
                        </div>
                        <div class="blog-content" data-full-details="{{ $blogFullDetails }}">
                            <div class="blog-author-info">
                                <img src="{{ $blog->author_image ? asset('images/team/' . $blog->author_image) : asset('images/blog/profile.svg') }}" 
                                     class="author-avatar" alt="Author"
                                     onerror="this.src='{{ asset('images/blog/profile.svg') }}'">
                                <div class="author-details">
                                    <span>{{ $blog->author }}</span>
                                    <span><i class="fa fa-clock-o"></i> {{ $blog->reading_time }} min</span>
                                </div>
                            </div>
                            <h4 class="blog-title">
                                <a href="{{ route('blog.index', ['blogId' => $blog->blog_id]) }}" title="{{ $blogFullTitle }}">{{ $blogFullTitle }}</a>
                            </h4>
                            <p class="blog-snippet" title="{{ $blogFullSnippet }}">{{ $blog->preview ?? '' }}</p>
                            <div class="blog-footer">
                                <span class="blog-date">{{ \Carbon\Carbon::parse($blog->date)->format('M d, Y') }}</span>
                                <a href="{{ route('blog.index', ['blogId' => $blog->blog_id]) }}" class="blog-btn-circle">
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">No blog articles found at this time.</p>
                </div>
            @endforelse
        </div>
        
        <div class="text-center mt-3">
            <a class="explore-btn" href="/blog">
                <span>View All Insights</span>
                <i class="fa fa-long-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<section class="video-section">
    <div class="armely-home-shell">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title text-center mb-5">
                    <h2>Our Most Recent Videos</h2>
                    <center><hr class="default-background hr"></center>
                </div>
            </div>
        </div>
        
        <div class="row">
            @forelse($videos as $video)
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="video-card-wrapper">
                        <div class="lazy-video" data-src="https://www.youtube.com/embed/{{ $video->video_id }}?autoplay=1">
                            <div class="play-overlay">
                                <img src="https://img.youtube.com/vi/{{ $video->video_id }}/hqdefault.jpg" class="lazy-thumb" alt="Video Thumbnail">
                                <div class="play-button-modern">
                                    <i class="fa fa-play"></i>
                                </div>
                            </div>
                        </div>
                        <div class="video-info">
                            <span class="video-tag">YouTube Series</span>
                            <h4 class="video-title-text">{{ $video->video_title }}</h4>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">No videos found at this time.</p>
                </div>
            @endforelse
        </div>
        
        <div class="text-center mt-3">
            <a class="explore-btn" href="https://www.youtube.com/@armelyarmely" target="_blank">
                <span>Explore more on YouTube</span>
                <i class="fa fa-long-arrow-right"></i>
            </a>
        </div>
    </div>
</section>



</main>
@endsection

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush


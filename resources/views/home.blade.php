@extends('layouts.public')

@php
    $title = 'Home';
@endphp


@push('styles')
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
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        height: 100%;
        border: 1px solid rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
    }

    .portfolio .card-wrapper:hover {
        transform: translateY(-12px);
        box-shadow: 0 20px 50px rgba(47, 85, 151, 0.15);
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
        transition: transform 0.6s ease;
    }

    .portfolio .card-wrapper:hover .image-container img {
        transform: scale(1.1);
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
        justify-content: space-between;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        position: relative;
        z-index: 10;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .portfolio .card-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.2);
        color: #fff !important;
        text-decoration: none;
    }

    .portfolio .card-wrapper:hover .card-btn {
        opacity: 1;
    }

    .portfolio .card-btn i {
        transition: transform 0.3s ease;
        font-size: 1.1rem;
    }

    .portfolio .card-btn:hover i {
        transform: translateX(5px);
    }

    .portfolio .explore-btn, 
    .blog .explore-btn,
    .video-section .explore-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #2f5597;
        font-size: 1.1rem;
        font-weight: 800;
        text-decoration: none;
        padding: 15px 0;
        transition: all 0.3s ease;
    }

    .portfolio .explore-btn:hover,
    .blog .explore-btn:hover,
    .video-section .explore-btn:hover {
        gap: 15px;
        color: #1e3a6d;
    }

    /* Blog Section Enhancements */
    .blog.section {
        background: #f8faff;
        padding: 100px 0;
    }

    .blog-card-wrapper {
        background: #fff;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0,0,0,0.04);
        transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        margin-bottom: 15px;
        border: 1px solid rgba(0,0,0,0.03);
        text-align: left;
    }

    .blog-card-wrapper:hover {
        transform: translateY(-12px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.08);
    }

    .blog-image-box {
        position: relative;
        height: 260px;
        min-height: 260px;
        max-height: 260px;
        overflow: hidden;
        background: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0;
        flex-shrink: 0;
        line-height: 0;
    }

    .blog-image-box img {
        width: 100%;
        height: 100%;
        object-fit: fill;
        object-position: center;
        transition: transform 0.8s ease;
        transform: scale(1);
        padding: 0;
        box-sizing: border-box;
        display: block;
        margin: 0;
    }

    .blog-card-wrapper:hover .blog-image-box img {
        transform: scale(1);
    }

    .blog-category-tag {
        position: absolute;
        top: 14px;
        left: 14px;
        background: rgba(15, 38, 78, 0.9);
        color: #fff;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        z-index: 2;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        backdrop-filter: blur(2px);
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

    .blog-content {
        padding: 16px 24px 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }

    .blog-author-info {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .author-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px; /* Squircle style like screenshot */
        object-fit: cover;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .author-details {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.95rem;
        color: #333;
        font-weight: 700;
    }

    .author-details span:first-child {
        color: #1a1a1a;
    }

    .author-details span:last-child {
        color: #666;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .blog-title {
        font-size: 1.25rem;
        font-weight: 900;
        color: #1a1a1a;
        text-align: left !important;
        line-height: 1.35;
        margin-bottom: 10px;
        transition: color 0.3s ease;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.7em;
        font-family: 'Poppins', sans-serif; /* Assuming Poppins is available */
    }

    .blog-title a,
    .blog-snippet,
    .blog-author-info,
    .blog-footer {
        text-align: left !important;
        width: 100%;
    }

    .blog-snippet {
        font-size: 0.95rem;
        color: #444;
        line-height: 1.5;
        margin-bottom: 22px;
        flex-grow: 1;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 90px;
    }

    .blog-card-wrapper:hover .blog-title {
        color: #2f5597; /* Use brand blue explicitly */
    }

    .blog-footer {
        margin-top: auto;
        padding-top: 20px;
        border-top: 1px solid #efefef;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }

    .blog-date {
        font-size: 0.95rem;
        font-weight: 800;
        color: #777;
        letter-spacing: -0.1px;
    }

    .blog-btn-circle {
        background: #2f5597; /* Explicit brand blue */
        color: #fff !important; /* Force white arrow */
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 8px 20px rgba(47, 85, 151, 0.25);
        border: none;
        text-decoration: none;
    }

    .blog-btn-circle i {
        font-weight: 900;
        font-size: 1.2rem;
        color: #fff !important;
    }

    .blog-card-wrapper:hover .blog-btn-circle {
        background: #111;
        color: #fff !important;
        transform: scale(1.15) rotate(-45deg);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    @media (max-width: 768px) {
        .blog.section { padding: 60px 0; }
        .blog-card-wrapper { margin-bottom: 25px; }
        .blog-title { font-size: 1.15rem; }
        .blog-image-box {
            height: 210px;
            min-height: 210px;
            max-height: 210px;
        }
        .blog-content {
            padding: 14px 18px 18px;
        }
        .blog-btn-circle {
            width: 40px;
            height: 40px;
        }
    }

    @media (max-width: 480px) {
        .blog.section { padding: 40px 0; }
        .blog-image-box {
            height: 190px;
            min-height: 190px;
            max-height: 190px;
        }
        .blog-title { font-size: 1.05rem; min-height: auto; }
        .blog-snippet { font-size: 0.85rem; -webkit-line-clamp: 2; min-height: auto; }
        .blog-content { padding: 12px 14px 16px; }
    }

    /* Clients/Brands Section */
    .clients-section {
        padding: 110px 0;
        background: 
            radial-gradient(1200px 600px at 20% 10%, rgba(30, 98, 173, 0.08) 0%, transparent 50%),
            radial-gradient(1000px 500px at 80% 90%, rgba(255, 152, 0, 0.06) 0%, transparent 50%),
            linear-gradient(135deg, #f8fafc 0%, #ffffff 50%, #f1f5f9 100%);
        position: relative;
        overflow: hidden;
    }

    .clients-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background: 
            radial-gradient(circle at 30% 20%, rgba(30, 98, 173, 0.03), transparent 40%),
            radial-gradient(circle at 70% 80%, rgba(30, 98, 173, 0.03), transparent 40%);
        pointer-events: none;
    }

    .clients-section::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(30, 98, 173, 0.03) 1px, transparent 1px);
        background-size: 50px 50px;
        animation: gridMove 20s linear infinite;
        pointer-events: none;
        opacity: 0.4;
    }

    @keyframes gridMove {
        0% { transform: translate(0, 0); }
        100% { transform: translate(50px, 50px); }
    }

    .partner-carousel {
        min-height: 100%;
    }
    .partner-carousel .m-4 {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.98) 100%);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: 25px 20px;
        border-radius: 20px;
        transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        height: 280px;
        min-height: 280px;
        max-height: 280px;
        flex-shrink: 0;
        box-shadow: 
            0 8px 32px rgba(30, 98, 173, 0.08),
            0 2px 8px rgba(30, 98, 173, 0.04),
            inset 0 1px 0 rgba(255, 255, 255, 0.9);
        border: 2px solid transparent;
        background-clip: padding-box;
        position: relative;
        margin: 15px !important;
    }

    .partner-carousel .m-4::before {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: 28px;
        padding: 2px;
        background: linear-gradient(135deg, 
            rgba(30, 98, 173, 0.3) 0%, 
            rgba(30, 98, 173, 0.1) 25%,
            rgba(255, 152, 0, 0.1) 50%,
            rgba(30, 98, 173, 0.1) 75%,
            rgba(30, 98, 173, 0.3) 100%);
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
        opacity: 0.6;
        transition: all 0.5s ease;
    }

    .partner-carousel .m-4::after {
        content: '✓';
        position: absolute;
        top: 16px;
        right: 16px;
        width: 28px;
        height: 28px;
        background: linear-gradient(135deg, #1E62AD 0%, #2f5597 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
        opacity: 0;
        transform: scale(0) rotate(-180deg);
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        box-shadow: 0 4px 12px rgba(30, 98, 173, 0.3);
    }

    .partner-carousel .m-4:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 
            0 24px 48px rgba(30, 98, 173, 0.16),
            0 12px 24px rgba(30, 98, 173, 0.12),
            0 0 0 1px rgba(30, 98, 173, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 1);
        background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(248, 250, 252, 1) 100%);
    }

    .partner-carousel .m-4:hover::before {
        opacity: 1;
        background: linear-gradient(135deg, 
            rgba(30, 98, 173, 0.5) 0%, 
            rgba(255, 152, 0, 0.3) 50%,
            rgba(30, 98, 173, 0.5) 100%);
        animation: borderShine 3s ease-in-out infinite;
    }

    .partner-carousel .m-4:hover::after {
        opacity: 1;
        transform: scale(1) rotate(0deg);
    }

    @keyframes borderShine {
        0%, 100% {
            background: linear-gradient(135deg, 
                rgba(30, 98, 173, 0.5) 0%, 
                rgba(255, 152, 0, 0.3) 50%,
                rgba(30, 98, 173, 0.5) 100%);
        }
        50% {
            background: linear-gradient(135deg, 
                rgba(255, 152, 0, 0.5) 0%, 
                rgba(30, 98, 173, 0.3) 50%,
                rgba(255, 152, 0, 0.5) 100%);
        }
    }

    .partner-carousel .partner-logo {
        width: 100%;
        height: 110px;
        min-height: 110px;
        max-height: 110px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        position: relative;
        padding: 8px;
        border-radius: 14px;
        background: linear-gradient(135deg, rgba(30, 98, 173, 0.02) 0%, transparent 100%);
        transition: all 0.4s ease;
        flex-shrink: 0;
    }

    .partner-carousel .m-4:hover .partner-logo {
        background: linear-gradient(135deg, rgba(30, 98, 173, 0.06) 0%, rgba(255, 152, 0, 0.03) 100%);
    }

    .partner-carousel img {
        filter: grayscale(15%) contrast(1.1) brightness(0.98);
        opacity: 0.9;
        transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
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
        transform: scale(1.08) translateZ(0);
        filter: grayscale(0%) contrast(1.1) brightness(1.05);
        opacity: 1;
    }

    .partner-carousel .partner-name {
        font-size: 13px;
        font-weight: 700;
        background: linear-gradient(135deg, #1E62AD 0%, #2f5597 60%, #1E62AD 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-align: center;
        line-height: 1.2;
        max-width: 160px;
        letter-spacing: 0.2px;
        text-transform: capitalize;
        position: relative;
        transition: all 0.4s ease;
    }

    .partner-carousel .m-4:hover .partner-name {
        transform: translateY(-2px);
        letter-spacing: 0.5px;
    }

    /* Card entrance animation */
    .partner-carousel .m-4 {
        animation: cardFadeIn 0.6s ease-out backwards;
    }

    .partner-carousel .m-4:nth-child(1) { animation-delay: 0.1s; }
    .partner-carousel .m-4:nth-child(2) { animation-delay: 0.15s; }
    .partner-carousel .m-4:nth-child(3) { animation-delay: 0.2s; }
    .partner-carousel .m-4:nth-child(4) { animation-delay: 0.25s; }
    .partner-carousel .m-4:nth-child(5) { animation-delay: 0.3s; }
    .partner-carousel .m-4:nth-child(6) { animation-delay: 0.35s; }
    .partner-carousel .m-4:nth-child(7) { animation-delay: 0.4s; }
    .partner-carousel .m-4:nth-child(8) { animation-delay: 0.45s; }

    @keyframes cardFadeIn {
        from {
            opacity: 0;
            transform: translateY(30px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Subtle floating animation on idle */
    .partner-carousel .m-4 {
        animation: cardFadeIn 0.6s ease-out backwards, floatCard 6s ease-in-out infinite;
    }

    @keyframes floatCard {
        0%, 100% {
            transform: translateY(0) scale(1);
        }
        50% {
            transform: translateY(-5px) scale(1);
        }
    }

    @media (max-width: 768px) {
        .clients-section {
            padding: 60px 0;
        }
        .clients-section .section-title h2 {
            font-size: 1.8rem;
        }
        .partner-carousel .m-4 {
            height: 240px;
            min-height: 240px;
            max-height: 240px;
            padding: 20px 15px;
            margin: 8px !important;
        }
        .partner-carousel .partner-logo {
            height: 90px;
            min-height: 90px;
            max-height: 90px;
        }
        .partner-carousel .partner-name {
            font-size: 11px;
        }
    }

    @media (max-width: 480px) {
        .clients-section { padding: 40px 0; }
        .clients-section .section-title h2 { font-size: 1.5rem; }
        .clients-section .section-title p { font-size: 0.9rem; }
        .partner-carousel .m-4 {
            height: 200px;
            min-height: 200px;
            max-height: 200px;
            padding: 15px 10px;
            margin: 5px !important;
        }
    }

    /* Video Section Enhancements */
    .video-section {
        padding: 100px 0;
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
<section class="slider">
    <div class="hero-slider">
        <div class="single-slider" style="background-image:url('{{ asset('images/sliders/slider-1.webp') }}')" role="img" aria-label="Digital Excellence Banner">
            <div class="container"><div class="row"><div class="col-lg-7"><div class="text">
                <h1><span class="text-light">Your Trusted Source For Digital Excellence</span></h1>
                <p class="text-light">Beyond Imagination</p>
                <div class="button">
                    <a href="/services#consultation-form" class="btn">Get Appointment</a>
                    <a href="/company" class="btn primary">Learn More</a>
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
        <div class="single-slider" style="background-image:url('{{ asset('images/sliders/slider-3.webp') }}')">
            <div class="container"><div class="row"><div class="col-lg-7"><div class="text">
                <h1><span class="text-light">We Provide Data Services That You Can Trust!</span></h1>
                <p class="text-light">Beyond Imagination</p>
                <div class="button">
                    <a href="/services#consultation-form" class="btn">Get Appointment</a>
                    <a href="/contact" class="btn primary">Contact Us</a>
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
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title text-center mb-5">
                    <h2>Case Studies</h2>
                    <center><hr class="default-background hr"></center>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="owl-carousel portfolio-slider">
            @foreach($industryListings as $listing)
                <div class="single-pf">
                    <div class="card-wrapper">
                        <div class="image-container">
                            <img src="{{ $listing->image_path }}" alt="{{ $listing->category }}" class="img-fluid lazy-img" loading="lazy" decoding="async">
                            <div class="image-overlay"></div>
                        </div>
                        <div class="card-body">
                            <span class="label">Industry: {{ $listing->category }}</span>
                            <h4>{{ $listing->category }} Transformation Solution</h4>
                            <p>{{ \Illuminate\Support\Str::limit($listing->excerpt, 100) }}</p>
                            <a href="{{ $listing->pdf_link }}" class="card-btn default-background text-light" target="_blank">
                                <span>View Details</span>
                                <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-5">
            <a class="explore-btn" href="/case-studies">
                <strong>Explore all Case Studies</strong>
                <i class="fa fa-long-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<section id="fun-facts" class="fun-facts default-background section col-md-12 mt-5 no-radius" aria-labelledby="stats-heading">
    <div class="container">
        <h2 id="stats-heading" class="sr-only">Customer Statistics</h2>
        <div class="row" style="content-visibility:auto; contain-intrinsic-size: auto 300px;">
            <div class="col-lg-4 col-md-6 col-12"><div class="single-fun"><i class="icofont icofont-ui-user-group"></i><div class="content"><span class="counter plus">72</span><p>Customer Retention Rate</p></div></div></div>
            <div class="col-lg-4 col-md-6 col-12"><div class="single-fun"><i class="icofont icofont-users-social"></i><div class="content"><span class="counter percent">82</span><p>Customer Satisfaction</p></div></div></div>
            <div class="col-lg-4 col-md-6 col-12"><div class="single-fun"><i class="icofont-simple-smile"></i><div class="content"><span>Very Easy</span><p>Customer Effort Score</p></div></div></div>
            <div class="col-lg-4 col-md-6 col-12"></div>
        </div>
    </div>
</section>

<section class="blog section" id="blog">
    <div class="container">
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
                <div class="col-lg-4 col-md-6 col-12" data-aos="fade-up">
                    <article class="blog-card-wrapper">
                        <div class="blog-image-box">
                            <img class="lazy-img" loading="lazy" 
                                 src="{{ $blog->image_path ?: asset('images/blog/default.svg') }}" 
                                 alt="{{ $blog->title }}"
                                 onerror="this.src='{{ asset('images/blog/default.svg') }}'">
                            <div class="blog-category-tag">Insight</div>
                        </div>
                        <div class="blog-content">
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
                                <a href="{{ route('blog.index', ['blogId' => $blog->blog_id]) }}">{{ $blog->title }}</a>
                            </h4>
                            <p class="blog-snippet">{{ $blog->preview ?? '' }}</p>
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
        
        <div class="text-center mt-5">
            <a class="explore-btn" href="/blog">
                <strong>View All Insights</strong>
                <i class="fa fa-long-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<section class="video-section">
    <div class="container">
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
                            <h4 class="video-title-text">{{ $video->video_title ?? 'Expert Digital Insights' }}</h4>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">No videos found at this time.</p>
                </div>
            @endforelse
        </div>
        
        <div class="text-center mt-4">
            <a class="explore-btn" href="https://www.youtube.com/@armelyarmely" target="_blank">
                <strong>Explore more on YouTube</strong>
                <i class="fa fa-long-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<section class="appointment">
    <div class="container">
        <div class="row"><div class="col-lg-12"><div class="section-title"><h2>Contact Us</h2><center><hr class="default-background hr"></center></div></div></div>
        <div class="row">
            <div class="col-lg-12 col-md-6 col-12 d-flex default-background mb-5">
                <form class="form p-5 w-100" id="contact-form" method="post" action="{{ route('contact.submit') }}">
                    @csrf
                    <p class="p-3 alert" id="SubmitMessage"></p>
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="text-start text-light">Name *</label>
                            <div class="form-group input-with-background"><input required class="remove-input-background" name="name" type="text" placeholder="Name" value="{{ old('name') }}"></div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="text-start text-light">Email *</label>
                            <div class="form-group"><input required class="remove-input-background" name="email" type="email" placeholder="Email" value="{{ old('email') }}"></div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="text-start text-light">Phone Number *</label>
                            <div class="form-group"><input required class="remove-input-background" name="phone" type="text" placeholder="Phone" value="{{ old('phone') }}"></div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <label class="text-start text-light">Subject *</label>
                            <div class="form-group"><input required class="remove-input-background" name="subject" type="text" placeholder="Subject" value="{{ request('subject') ?? old('subject') }}"></div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-12">
                            <label class="text-start text-light">Organization Name *</label>
                            <div class="form-group"><input required class="remove-input-background" name="organization" type="text" placeholder="Organization Name" value="{{ old('organization') }}"></div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-12">
                            <label class="text-start text-light">Message *</label>
                            <div class="form-group"><textarea required class="remove-input-background" name="message" placeholder="Write Your Message Here.....">{{ old('message') }}</textarea></div>
                        </div>
                        <input style="display: none;" type="text" name="website" class="honeypot">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="text-start text-light">Confirm you are not a robot *</label>
                                @if(!empty($recaptchaSiteKey ?? config('services.recaptcha.site_key')))
                                    <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey ?? config('services.recaptcha.site_key') }}"></div>
                                @else
                                    <div class="alert alert-warning">reCAPTCHA is not configured. Please set <strong>CAPTURE_SITE_KEY</strong>.</div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group ml-3">
                            <div class="button"><button type="submit" class="btn send-message-btn" name="submit_form">Send Message</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Lazy-load Google reCAPTCHA when contact form enters viewport -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const recaptchaEl = document.querySelector('.g-recaptcha');
    if (!recaptchaEl) return;
    let scriptLoaded = false;
    const loadRecaptcha = () => {
        if (scriptLoaded) return; scriptLoaded = true;
        const s = document.createElement('script');
        s.src = 'https://www.google.com/recaptcha/api.js';
        s.async = true; s.defer = true; document.body.appendChild(s);
    };
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) { loadRecaptcha(); io.disconnect(); } });
        }, { rootMargin: '200px 0px' });
        io.observe(recaptchaEl);
    } else { loadRecaptcha(); }
});
</script>

<!-- Contact Form Handler - Using Vanilla JS to avoid conflicts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contact-form');
    
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const submitBtn = document.querySelector('button[name="submit_form"]');
        const messageDiv = document.getElementById('SubmitMessage');
        const originalBtnText = submitBtn.textContent;

        messageDiv.textContent = '';
        messageDiv.className = 'p-3 alert';
        messageDiv.style.display = 'none';

        const recaptchaResponse = grecaptcha.getResponse();
        if (!recaptchaResponse) {
            messageDiv.className = 'p-3 alert alert-danger alert-dismissible fade show';
            messageDiv.innerHTML = '<strong>Error:</strong> Please verify that you are not a robot.' +
                                  '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            messageDiv.style.display = 'block';
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';

        const formData = new FormData(form);
        formData.append('g-recaptcha-response', recaptchaResponse);

        fetch('{{ route("contact.submit") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            messageDiv.className = 'p-3 alert';
            if (data.success) {
                messageDiv.classList.add('alert-success');
                messageDiv.textContent = '✅ ' + data.message;
                
                // Google Analytics Event Tracking (GA4)
                if (typeof gtag === 'function') {
                    gtag('event', 'consultation_submit', {
                        'event_category': 'engagement',
                        'event_label': 'home_page_consultation',
                        'form_name': 'Request Consultation Form',
                        'service': form.querySelector('select[name="service"]')?.value || 'Not specified'
                    });
                    
                    // Google Ads Conversion Tracking
                    gtag('event', 'conversion', {
                        'send_to': '{{ env("GOOGLE_ADS_ID") }}/contact_form_submit',
                        'event_callback': function() {
                            console.log('Contact form conversion tracked');
                        }
                    });
                }
                
                form.reset();
                grecaptcha.reset();
            } else {
                messageDiv.classList.add('alert-danger');
                messageDiv.textContent = '❌ ' + (data.message || 'An error occurred. Please try again.');
            }
            messageDiv.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            messageDiv.className = 'p-3 alert alert-danger';
            messageDiv.textContent = '❌ An error occurred. Please try again.';
            messageDiv.style.display = 'block';
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
        });
        
        return false;
    }, true);
});
</script>
</main>
@endsection

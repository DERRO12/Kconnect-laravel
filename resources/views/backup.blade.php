<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>K-CONNECT - Intranet Portal</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --kiboko-dark: #0f172a;
            --glass: rgba(255, 255, 255, 0.7);
            --card-border: rgba(255, 255, 255, 0.8);
            --grad: linear-gradient(135deg, var(--kiboko-blue) 0%, var(--kiboko-green) 100%);
            --grad-light: linear-gradient(135deg, rgba(0, 154, 218, 0.1) 0%, rgba(118, 188, 33, 0.1) 100%);
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
            color: var(--kiboko-dark);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
            padding-top: 80px;
        }

        /* Background Animation */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
            overflow: hidden;
        }

        .orb {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.3;
            animation: float 20s infinite alternate ease-in-out;
        }

        .orb-1 {
            background: var(--kiboko-blue);
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .orb-2 {
            background: var(--kiboko-green);
            bottom: -100px;
            right: -100px;
            animation-delay: 5s;
        }

        .orb-3 {
            background: rgba(56, 189, 248, 0.5);
            top: 30%;
            right: 10%;
            animation-delay: 10s;
            width: 300px;
            height: 300px;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(50px, 30px) scale(1.1); }
            50% { transform: translate(-30px, 50px) scale(1.05); }
            75% { transform: translate(40px, -20px) scale(1.15); }
        }

        /* Navigation - Enhanced with scrolling and improved hover */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 10px 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            max-height: 100vh;
            overflow-y: auto;
        }

        /* Custom scrollbar for navigation */
        .navbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        .navbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .navbar::-webkit-scrollbar-thumb {
            background: var(--grad);
            border-radius: 10px;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
            white-space: nowrap;
        }

        .navbar-nav {
            max-height: calc(100vh - 100px);
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Custom scrollbar for navigation menu */
        .navbar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .navbar-nav::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
            border-radius: 10px;
        }

        .navbar-nav::-webkit-scrollbar-thumb {
            background: var(--grad);
            border-radius: 10px;
        }

        .nav-link {
            font-weight: 600;
            color: #475569 !important;
            margin: 0 4px;
            padding: 8px 12px !important;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            white-space: nowrap;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--grad-light);
            color: var(--kiboko-blue) !important;
            transform: translateY(-2px);
        }

        /* IMPROVED DROPDOWN STYLES - Better visibility and hover */
        .dropdown, .dropdown-submenu {
            position: relative;
        }

        /* Main dropdown menu */
        .dropdown-menu {
            border: none;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
            background: white;
            padding: 8px;
            min-width: 250px;
            display: block;
            visibility: hidden;
            opacity: 0;
            transform: translateY(10px);
            transition: visibility 0.2s, opacity 0.2s, transform 0.2s;
            border: 1px solid rgba(0,0,0,0.08);
            max-height: 400px;
            overflow-y: auto;
            overflow-x: hidden;
            position: absolute;
            z-index: 1000;
            pointer-events: none;
        }

        /* Show dropdown on hover */
        .dropdown:hover > .dropdown-menu,
        .dropdown-submenu:hover > .dropdown-menu {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        /* Submenu positioning */
        .dropdown-submenu .dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -8px;
            margin-left: 5px;
            transform: translateX(10px);
        }

        .dropdown-submenu:hover > .dropdown-menu {
            transform: translateX(0);
        }

        /* Submenu arrow indicator */
        .dropdown-submenu > .dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.5em;
            vertical-align: middle;
            content: "";
            border-top: 0.3em solid transparent;
            border-right: 0;
            border-bottom: 0.3em solid transparent;
            border-left: 0.3em solid #64748b;
            float: right;
            margin-top: 8px;
            transition: border-left-color 0.2s;
        }

        .dropdown-submenu:hover > .dropdown-toggle::after {
            border-left-color: var(--kiboko-blue);
        }

        /* Dropdown items */
        .dropdown-item {
            border-radius: 8px;
            padding: 10px 16px;
            font-weight: 500;
            color: #334155;
            transition: all 0.2s;
            position: relative;
            white-space: nowrap;
            font-size: 0.95rem;
        }

        .dropdown-item:hover {
            background: var(--grad-light);
            color: var(--kiboko-blue);
            transform: translateX(4px);
        }

        /* Dropdown headers (labels) */
        .dropdown-header {
            color: var(--kiboko-blue);
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 16px;
            background: rgba(0, 154, 218, 0.05);
            margin: 4px 0;
            border-radius: 6px;
            cursor: default;
            white-space: nowrap;
        }

        .dropdown-header:hover {
            background: rgba(0, 154, 218, 0.05);
            color: var(--kiboko-blue);
            transform: none;
        }

        /* Scrollbar for dropdowns */
        .dropdown-menu::-webkit-scrollbar {
            width: 4px;
        }

        .dropdown-menu::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
            border-radius: 10px;
        }

        .dropdown-menu::-webkit-scrollbar-thumb {
            background: var(--grad);
            border-radius: 10px;
        }

        /* Ensure dropdowns stay above content */
        .dropdown-menu {
            z-index: 1050;
        }

        .btn-login {
            background: var(--grad);
            color: white !important;
            border-radius: 10px;
            padding: 8px 20px !important;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            box-shadow: 0 6px 20px rgba(0, 154, 218, 0.2);
            white-space: nowrap;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 154, 218, 0.3);
        }

        /* Mobile navigation styles */
        @media (max-width: 991px) {
            .navbar {
                max-height: 100vh;
                overflow-y: auto;
            }

            .navbar-nav {
                max-height: none;
                overflow-y: visible;
                padding: 10px 0;
            }

            /* Reset dropdown styles for mobile */
            .dropdown-menu {
                position: static !important;
                visibility: visible !important;
                opacity: 1 !important;
                transform: none !important;
                width: 100%;
                margin-top: 5px;
                background: rgba(255,255,255,0.95);
                border: 1px solid rgba(0,0,0,0.05);
                box-shadow: none;
                padding-left: 15px;
                max-height: none;
                overflow-y: visible;
                display: none;
                pointer-events: auto;
            }

            .dropdown.show > .dropdown-menu,
            .dropdown-submenu.show > .dropdown-menu {
                display: block;
            }

            .dropdown-submenu .dropdown-menu {
                left: 0;
                margin-left: 15px;
                margin-top: 0;
                border-left: 2px solid var(--grad);
            }

            .dropdown-submenu > .dropdown-toggle::after {
                display: none;
            }

            .nav-link {
                white-space: normal;
                padding: 12px 15px !important;
            }

            .dropdown-item {
                white-space: normal;
                padding: 12px 15px;
            }

            .dropdown-header {
                white-space: normal;
            }

            .btn-login {
                margin: 15px 0 !important;
                text-align: center;
                display: block;
            }
        }

        /* Hero Section */
        .hero {
            padding: 30px 0 50px;
            position: relative;
            overflow: hidden;
        }

        .hero h1 {
            font-weight: 800;
            font-size: clamp(2rem, 5vw, 3.2rem);
            line-height: 1.2;
            margin-bottom: 1.5rem;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -1px;
        }

        .hero p {
            font-size: clamp(1rem, 2vw, 1.1rem);
            color: #64748b;
            margin-bottom: 2rem;
            max-width: 500px;
            line-height: 1.6;
        }

        /* Enhanced Carousel */
        .carousel-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .carousel-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .carousel-container {
            height: 450px;
            position: relative;
            overflow: hidden;
        }

        .carousel-item {
            height: 450px;
            position: relative;
        }

        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .carousel-item iframe {
            width: 100%;
            height: 100%;
            border: none;
            pointer-events: auto;
        }

        /* Video Description */
        .video-description-container {
            background: linear-gradient(135deg, var(--kiboko-blue), var(--kiboko-green));
            color: white;
            padding: 25px;
            border-radius: 0 0 24px 24px;
            margin-top: -5px;
            position: relative;
            z-index: 10;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .video-description-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .video-description-text {
            font-size: 1rem;
            line-height: 1.6;
            opacity: 0.95;
            margin-bottom: 0;
            color: white;
        }

        /* Image Caption */
        .carousel-caption {
            background: linear-gradient(135deg, rgba(0, 154, 218, 0.95), rgba(118, 188, 33, 0.95));
            border-radius: 16px;
            bottom: 30px;
            left: 30px;
            right: 30px;
            padding: 20px;
            text-align: left;
            backdrop-filter: blur(5px);
        }

        .carousel-caption h5 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .carousel-caption p {
            font-size: 1rem;
            margin-bottom: 0;
            opacity: 0.95;
        }

        .carousel-indicators {
            margin-bottom: 20px;
        }

        .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--kiboko-blue);
            opacity: 0.5;
            margin: 0 5px;
        }

        .carousel-indicators button.active {
            opacity: 1;
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            backdrop-filter: blur(5px);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .carousel-card:hover .carousel-control-prev,
        .carousel-card:hover .carousel-control-next {
            opacity: 1;
        }

        .carousel-control-prev {
            left: 20px;
        }

        .carousel-control-next {
            right: 20px;
        }

        /* Birthday Cards */
        .birthday-card {
            background: white;
            border-radius: 20px;
            padding: 25px 15px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
        }

        .birthday-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .birthday-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 80px;
            background: var(--grad);
            opacity: 0.1;
        }

        .birthday-card::after {
            content: '🎂';
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 50px;
            opacity: 0.1;
            transform: rotate(15deg);
        }

        .birthday-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 10px 30px rgba(0, 154, 218, 0.2);
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .birthday-name {
            font-weight: 800;
            font-size: 1.2rem;
            margin-bottom: 5px;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .birthday-dept {
            color: #64748b;
            font-weight: 500;
            margin-bottom: 12px;
            font-size: 0.85rem;
        }

        .birthday-date {
            display: inline-block;
            background: var(--grad-light);
            padding: 6px 16px;
            border-radius: 50px;
            font-weight: 700;
            color: var(--kiboko-blue);
            margin-bottom: 12px;
            font-size: 0.9rem;
        }

        .birthday-message {
            color: #475569;
            font-size: 0.85rem;
            line-height: 1.6;
            font-style: italic;
            position: relative;
            z-index: 1;
        }

        .confetti {
            position: absolute;
            width: 8px;
            height: 8px;
            background: var(--grad);
            opacity: 0.6;
            animation: confetti 3s ease-in-out infinite;
        }

        @keyframes confetti {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }

        /* Section Titles */
        .section-title {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .section-title h2 {
            font-weight: 800;
            font-size: clamp(1.8rem, 4vw, 2.2rem);
            margin-bottom: 10px;
            color: var(--kiboko-dark);
            letter-spacing: -0.5px;
        }

        .section-title .title-line {
            width: 60px;
            height: 4px;
            background: var(--grad);
            margin: 0 auto;
            border-radius: 2px;
            position: relative;
        }

        .section-title .title-line::after {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 100%;
            background: white;
            animation: lineGlow 2s infinite alternate;
        }

        @keyframes lineGlow {
            from { opacity: 0.3; }
            to { opacity: 0.8; }
        }

        /* Footer */
        .footer {
            background: var(--kiboko-dark);
            color: white;
            padding: 50px 0 20px;
            position: relative;
            margin-top: 50px;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--grad);
        }

        .footer h5 {
            color: white;
            font-weight: 800;
            margin-bottom: 15px;
            font-size: 1rem;
            letter-spacing: 0.5px;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .footer-links a {
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .footer-links a:hover {
            color: white;
            transform: translateX(4px);
        }

        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 45px;
            height: 45px;
            background: var(--grad);
            color: white;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 8px 25px rgba(0, 154, 218, 0.3);
            z-index: 1000;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .back-to-top:hover {
            transform: translateY(-4px) scale(1.1);
            box-shadow: 0 12px 30px rgba(0, 154, 218, 0.4);
        }

        /* Utility Classes */
        .text-gradient {
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .bg-gradient {
            background: var(--grad);
        }

        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 700;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .btn-gradient:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 154, 218, 0.3);
        }
    </style>
</head>
<body>
    <!-- Background Animation -->
    <div class="bg-animation">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- Navigation with Improved Hover -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="./">
                <i class="fas fa-bolt me-2"></i>K-CONNECT
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto" id="mainNav">
                    @php // Call the navigation function
                    if (function_exists('getMenu')) {
                        getMenu(); 
                    } else {
                        echo '<li class="nav-item"><a class="nav-link" href="#">Menu unavailable</a></li>';
                    } @endphp
                </ul>
                <a href="admin/login" class="btn-login ms-lg-3">
                    <i class="fas fa-user me-2"></i>Admin Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Carousel -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1>Welcome to <br>K-CONNECT</h1>
                    <p>Your central hub for communication, collaboration, and company celebrations. Stay connected with your team and celebrate together!</p>
                    <a href="#" class="btn-hero btn-gradient">
                        <i class="fas fa-rocket me-2"></i>Get Started
                    </a>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <!-- Dynamic Carousel -->
                    <div class="carousel-card">
                        <div id="mediaCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                            {{-- carousel items are fetched in the controller --}}
                            @if ($carouselItems && count($carouselItems) > 0)
                                @php $slideCount = count($carouselItems); @endphp
                            <div class="carousel-indicators">
                                @for ($i = 0; $i < $slideCount; $i++)
                                <button type="button" data-bs-target="#mediaCarousel" data-bs-slide-to="{!! $i !!}" {!! $i == 0 ? 'class="active"' : '' !!} aria-label="Slide {!! $i + 1 !!}"></button>
                                @endfor
                            </div>
                            
                            <div class="carousel-inner">
                                @php $active = true;
                                $videoDescriptions = array();
                                
                                @endphp
                                @foreach ($carouselItems as $slide)
                                    @php
                                    $mediaUrl = htmlspecialchars($slide['media_url'] ?? '');
                                    $title = htmlspecialchars($slide['title'] ?? '');
                                    $description = htmlspecialchars($slide['description'] ?? '');
                                    $mediaType = $slide['media_type'] ?? 'image'; @endphp
                                <div class="carousel-item {!! $active ? 'active' : '' !!}" data-media-type="{!! $mediaType !!}">
                                    @if ($mediaType == 'image')
                                        <img src="uploads/{!! $mediaUrl !!}" class="d-block w-100" alt="{!! $title !!}" loading="lazy">
                                        @if (!empty($description))
                                        <div class="carousel-caption d-none d-md-block">
                                            <h5>{!! $title !!}</h5>
                                            <p>{!! $description !!}</p>
                                        </div>
                                        @endif
                                        
                                    @elseif ($mediaType == 'youtube')
                                        <iframe src="https://www.youtube.com/embed/{!! $mediaUrl !!}?autoplay=0&mute=1&controls=1&rel=0" 
                                                title="{!! $title !!}"
                                                allowfullscreen 
                                                loading="lazy"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                                        </iframe>
                                        @php // Store video description for display below
                                        if(!empty($description)):
                                            $videoDescriptions[] = ['title' => $title, 'description' => $description];
                                        endif; @endphp
                                        
                                    @elseif ($mediaType == 'vimeo')
                                        <iframe src="https://player.vimeo.com/video/{!! $mediaUrl !!}?autoplay=0&muted=1&controls=1" 
                                                title="{!! $title !!}"
                                                allowfullscreen 
                                                loading="lazy"
                                                allow="autoplay; fullscreen; picture-in-picture">
                                        </iframe>
                                        @php // Store video description for display below
                                        if(!empty($description)):
                                            $videoDescriptions[] = ['title' => $title, 'description' => $description];
                                        endif; @endphp
                                    @endif
                                </div>
                                @php $active = false; @endphp
                                @endforeach
                            </div>
                            
                            <!-- Video Descriptions - Displayed BELOW the carousel -->
                            @if (!empty($videoDescriptions))
                                @foreach ($videoDescriptions as $index => $video)
                                <div class="video-description-container description-{!! $index === 0 ? 'active' : 'inactive' !!}" 
                                     data-slide="{!! $index !!}" 
                                     style="{!! $index === 0 ? 'display: block;' : 'display: none;' !!}">
                                    <div class="video-description-title">
                                        <i class="fas fa-play-circle"></i>
                                        <span>{!! $video['title'] !!}</span>
                                    </div>
                                    <p class="video-description-text">
                                        {!! $video['description'] !!}
                                    </p>
                                </div>
                                @endforeach
                            @endif
                            
                            <button class="carousel-control-prev" type="button" data-bs-target="#mediaCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#mediaCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                            
                            @else
                            <div class="text-center py-5">
                                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No carousel items available</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Birthday Celebrations Section -->
    <section class="container my-5">
        <div class="section-title" data-aos="fade-up">
            <h2><i class="fas fa-birthday-cake me-2"></i>Today's Birthday Celebrations</h2>
            <div class="title-line"></div>
        </div>

        {{-- today's birthdays are fetched in the controller --}}
        @if ($birthdays && count($birthdays) > 0)
            @php $counter = 0; @endphp
        <div class="row g-4">
            @foreach ($birthdays as $bday)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{!! $counter * 100 !!}">
                <div class="birthday-card">
                    <!-- Confetti effect -->
                    <div class="confetti" style="left: 10%; animation-delay: 0s;"></div>
                    <div class="confetti" style="left: 30%; animation-delay: 0.5s;"></div>
                    <div class="confetti" style="left: 50%; animation-delay: 1s;"></div>
                    <div class="confetti" style="left: 70%; animation-delay: 1.5s;"></div>
                    <div class="confetti" style="left: 90%; animation-delay: 2s;"></div>
                    
                    @php $photoPath = 'uploads/' . ($bday['photo'] ?? ''); @endphp
                    @if (!empty($bday['photo']) && file_exists(kc_public_file($photoPath)))
                        <img src="{!! $photoPath !!}" alt="{{ $bday['full_name'] ?? '' }}" class="birthday-avatar" loading="lazy">
                    @else
                        <div class="birthday-avatar bg-gradient d-flex align-items-center justify-content-center">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                    @endif
                    
                    <h3 class="birthday-name">{{ $bday['full_name'] ?? '' }}</h3>
                    <p class="birthday-dept">{!! !empty($bday['department']) ? htmlspecialchars($bday['department']) : 'Employee' !!}</p>
                    
                    <div class="birthday-date">
                        <i class="far fa-calendar-alt me-2"></i>
                        {!! !empty($bday['birth_date']) ? date('F j', strtotime($bday['birth_date'])) : '' !!}
                    </div>
                    
                    @if (!empty($bday['message']))
                        <p class="birthday-message">"{{ $bday['message'] }}"</p>
                    @endif
                    
                    <div class="mt-3">
                        <span class="badge bg-gradient text-white px-4 py-2">
                            <i class="fas fa-gift me-2"></i>Happy Birthday!
                        </span>
                    </div>
                </div>
            </div>
            @php $counter++; @endphp
            @endforeach
        </div>
        @else
        <div class="text-center py-5" data-aos="fade-up">
            <div class="display-1 mb-4">🎂</div>
            <h3 class="text-muted">No birthdays today</h3>
            <p class="text-muted">Check back tomorrow for more celebrations!</p>
        </div>
        @endif
    </section>

    <!-- Upcoming Birthdays Section -->
    <section class="container my-5">
        <div class="section-title" data-aos="fade-up">
            <h2><i class="far fa-calendar-alt me-2"></i>Upcoming Birthdays</h2>
            <div class="title-line"></div>
        </div>

        {{-- upcoming birthdays are fetched in the controller --}}
        @if ($upcoming && count($upcoming) > 0)
        <div class="row g-3">
            @foreach ($upcoming as $next)
            <div class="col-6 col-md-4 col-lg-2" data-aos="fade-up">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: var(--grad-light);">
                        @php $photoPath = 'uploads/' . ($next['photo'] ?? ''); @endphp
                        @if (!empty($next['photo']) && file_exists(kc_public_file($photoPath)))
                            <img src="{!! $photoPath !!}" class="w-100 h-100 rounded-circle" style="object-fit: cover;" alt="{{ $next['full_name'] ?? '' }}" loading="lazy">
                        @else
                            <i class="fas fa-user fa-2x text-gradient"></i>
                        @endif
                    </div>
                    <h6 class="fw-bold mb-1 small">{{ $next['full_name'] ?? '' }}</h6>
                    <small class="text-muted small">{!! !empty($next['birth_date']) ? date('M j', strtotime($next['birth_date'])) : '' !!}</small>
                    @php $days = isset($next['days_until']) ? (int)$next['days_until'] : 0;
                    if($days == 0):
                        echo '<span class="badge bg-success mt-2 small">Today!</span>';
                    elseif($days == 1):
                        echo '<span class="badge bg-warning mt-2 small">Tomorrow</span>';
                    else:
                        echo '<span class="badge bg-info mt-2 small">In '.$days.' days</span>';
                    endif; @endphp
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </section>

    <!-- Quick Links Section -->
    <section class="container my-5">
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="rounded-circle d-inline-flex p-3 mb-3" style="width: fit-content; background: var(--grad-light);">
                        <i class="fas fa-newspaper fa-2x text-gradient"></i>
                    </div>
                    <h5 class="fw-bold">Company News</h5>
                    <p class="text-muted small">Stay updated with the latest announcements and news from across the organization.</p>
                    <a href="#" class="text-decoration-none text-gradient fw-bold">Read More <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="rounded-circle d-inline-flex p-3 mb-3" style="width: fit-content; background: var(--grad-light);">
                        <i class="fas fa-calendar-alt fa-2x text-gradient"></i>
                    </div>
                    <h5 class="fw-bold">Events Calendar</h5>
                    <p class="text-muted small">Check out upcoming events, meetings, and company-wide activities.</p>
                    <a href="#" class="text-decoration-none text-gradient fw-bold">View Calendar <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="rounded-circle d-inline-flex p-3 mb-3" style="width: fit-content; background: var(--grad-light);">
                        <i class="fas fa-users fa-2x text-gradient"></i>
                    </div>
                    <h5 class="fw-bold">Employee Directory</h5>
                    <p class="text-muted small">Connect with colleagues across departments and locations.</p>
                    <a href="#" class="text-decoration-none text-gradient fw-bold">Search Directory <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5><i class="fas fa-bolt me-2"></i>K-CONNECT</h5>
                    <p class="text-white-50 small">Your central hub for communication, collaboration, and celebration.</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-2" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-white" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-2">
                    <h5>Quick Links</h5>
                    <div class="footer-links">
                        <a href="#"><i class="fas fa-chevron-right"></i> About Us</a>
                        <a href="#"><i class="fas fa-chevron-right"></i> Careers</a>
                        <a href="#"><i class="fas fa-chevron-right"></i> Contact</a>
                        <a href="#"><i class="fas fa-chevron-right"></i> Support</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <h5>Resources</h5>
                    <div class="footer-links">
                        <a href="#"><i class="fas fa-chevron-right"></i> Employee Handbook</a>
                        <a href="#"><i class="fas fa-chevron-right"></i> IT Support</a>
                        <a href="#"><i class="fas fa-chevron-right"></i> HR Portal</a>
                        <a href="#"><i class="fas fa-chevron-right"></i> Training</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <h5>Newsletter</h5>
                    <p class="text-white-50 small">Subscribe for updates</p>
                    <div class="input-group">
                        <input type="email" class="form-control form-control-sm bg-transparent text-white border-secondary" placeholder="Email">
                        <button class="btn btn-sm btn-gradient" type="button">Subscribe</button>
                    </div>
                </div>
            </div>
            <hr class="border-secondary mt-4">
            <div class="text-center text-white-50 small">
                &copy; {!! date('Y') !!} K-CONNECT. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1"></script>
    
    <script>
        // Initialize AOS
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                once: true,
                offset: 50,
                disable: window.innerWidth < 768
            });
        });

        // Back to top button
        window.onscroll = function() {
            const button = document.querySelector('.back-to-top');
            if (button) {
                if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                    button.style.display = 'flex';
                } else {
                    button.style.display = 'none';
                }
            }
        };

        // Birthday confetti effect
        document.addEventListener('DOMContentLoaded', function() {
            @if (isset($birthdays) && $birthdays && count($birthdays) > 0)
            // Launch confetti for today's birthdays
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#009ada', '#76bc21', '#ffffff']
            });
            
            // Additional confetti bursts
            setTimeout(() => {
                confetti({
                    particleCount: 50,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 }
                });
                confetti({
                    particleCount: 50,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 }
                });
            }, 250);
            @endif
        });

        // Carousel description synchronization
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.getElementById('mediaCarousel');
            if (carousel) {
                carousel.addEventListener('slid.bs.carousel', function(event) {
                    const descriptions = document.querySelectorAll('.video-description-container');
                    if (descriptions.length > 0) {
                        descriptions.forEach((desc, index) => {
                            desc.style.display = index === event.to ? 'block' : 'none';
                        });
                    }
                });
            }
        });

        // Mobile dropdown toggle
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth <= 991) {
                document.querySelectorAll('.dropdown-toggle').forEach(item => {
                    item.addEventListener('click', function(e) {
                        if (this.parentElement.classList.contains('dropdown-submenu') || 
                            this.parentElement.classList.contains('dropdown')) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            const dropdownMenu = this.nextElementSibling;
                            if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                                // Toggle current dropdown
                                if (dropdownMenu.style.display === 'block') {
                                    dropdownMenu.style.display = 'none';
                                    this.parentElement.classList.remove('show');
                                } else {
                                    // Close other dropdowns at same level
                                    const parent = this.parentElement.parentElement;
                                    parent.querySelectorAll(':scope > li > .dropdown-menu').forEach(menu => {
                                        menu.style.display = 'none';
                                    });
                                    parent.querySelectorAll(':scope > li').forEach(li => {
                                        li.classList.remove('show');
                                    });
                                    
                                    dropdownMenu.style.display = 'block';
                                    this.parentElement.classList.add('show');
                                }
                            }
                        }
                    });
                });
            }
        });

        // Active link highlighting
        document.addEventListener('DOMContentLoaded', function() {
            const currentLocation = window.location.pathname;
            document.querySelectorAll('.nav-link').forEach(link => {
                const href = link.getAttribute('href');
                if (href && href !== '#' && currentLocation.includes(href) && href !== './') {
                    link.classList.add('active');
                } else if (href === './' && (currentLocation.endsWith('/') || currentLocation.endsWith('./'))) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
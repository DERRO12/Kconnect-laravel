<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiboko Intranet Portal</title>
    
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

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 15px 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .nav-link {
            font-weight: 600;
            color: #475569 !important;
            margin: 0 8px;
            padding: 8px 16px !important;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--grad-light);
            color: var(--kiboko-blue) !important;
            transform: translateY(-2px);
        }

        /* Multi-level dropdown styles */
        .dropdown, .dropdown-submenu {
            position: relative;
        }

        .dropdown-menu {
            border: none;
            border-radius: 16px;
            box-shadow: var(--shadow);
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            padding: 8px;
            min-width: 240px;
            display: none;
            animation: dropdownFade 0.2s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .dropdown:hover > .dropdown-menu,
        .dropdown-submenu:hover > .dropdown-menu {
            display: block;
        }

        .dropdown-submenu .dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -8px;
            margin-left: 2px;
        }

        .dropdown-submenu .dropdown-menu-left {
            left: auto;
            right: 100%;
            margin-right: 2px;
        }

        @keyframes dropdownFade {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-submenu > .dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.5em;
            vertical-align: middle;
            content: "";
            border-top: 0.3em solid transparent;
            border-right: 0;
            border-bottom: 0.3em solid transparent;
            border-left: 0.3em solid;
            float: right;
            margin-top: 8px;
        }

        .dropdown-item {
            border-radius: 10px;
            padding: 10px 16px;
            font-weight: 500;
            color: #334155;
            transition: all 0.2s;
            position: relative;
        }

        .dropdown-item:hover {
            background: var(--grad-light);
            color: var(--kiboko-blue);
            transform: translateX(4px);
        }

        .dropdown-header {
            color: var(--kiboko-blue);
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 16px;
            background: rgba(0, 154, 218, 0.05);
            margin: 5px 0;
            border-radius: 6px;
            cursor: default;
        }

        .dropdown-header:hover {
            background: rgba(0, 154, 218, 0.05);
            color: var(--kiboko-blue);
            transform: none;
        }

        .nav-link.disabled,
        .dropdown-item.disabled {
            opacity: 0.6;
            cursor: default;
            pointer-events: none;
        }

        .btn-login {
            background: var(--grad);
            color: white !important;
            border-radius: 12px;
            padding: 10px 24px !important;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            box-shadow: 0 6px 20px rgba(0, 154, 218, 0.2);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 154, 218, 0.3);
        }

        /* Hero Section */
        .hero {
          
            padding: 40px 0 60px;
            position: relative;
            overflow: hidden;
        }

        .hero h1 {
            font-weight: 800;
            font-size: 3.2rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -1px;
        }

        .hero p {
            font-size: 1.1rem;
            color: #64748b;
            margin-bottom: 2rem;
            max-width: 500px;
            line-height: 1.6;
        }

        /* Enhanced Carousel */
        .carousel-section {
            margin-top: 20px;
            margin-bottom: 40px;
        }

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

        /* Video Description Overlay - Now positioned BELOW the video */
        .video-description-container {
            background: linear-gradient(135deg, rgba(0, 154, 218, 0.95), rgba(118, 188, 33, 0.95));
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
        }

        .video-description-text {
            font-size: 1rem;
            line-height: 1.6;
            opacity: 0.9;
            margin-bottom: 0;
            color: white;
        }

        /* Image Caption */
        .carousel-caption {
            background: linear-gradient(135deg, rgba(0, 154, 218, 0.9), rgba(118, 188, 33, 0.9));
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
            opacity: 0.9;
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
            border-radius: 24px;
            padding: 30px 20px;
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
            height: 100px;
            background: var(--grad);
            opacity: 0.1;
        }

        .birthday-card::after {
            content: '🎂';
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 60px;
            opacity: 0.1;
            transform: rotate(15deg);
        }

        .birthday-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 10px 30px rgba(0, 154, 218, 0.2);
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .birthday-name {
            font-weight: 800;
            font-size: 1.3rem;
            margin-bottom: 5px;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .birthday-dept {
            color: #64748b;
            font-weight: 500;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .birthday-date {
            display: inline-block;
            background: var(--grad-light);
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 700;
            color: var(--kiboko-blue);
            margin-bottom: 15px;
        }

        .birthday-message {
            color: #475569;
            font-size: 0.9rem;
            line-height: 1.6;
            font-style: italic;
            position: relative;
            z-index: 1;
        }

        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
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
            margin-bottom: 40px;
            position: relative;
        }

        .section-title h2 {
            font-weight: 800;
            font-size: 2.2rem;
            margin-bottom: 12px;
            color: var(--kiboko-dark);
            letter-spacing: -0.5px;
        }

        .section-title .title-line {
            width: 80px;
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
            width: 40px;
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
            padding: 60px 0 30px;
            position: relative;
            margin-top: 60px;
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
            margin-bottom: 20px;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-links a {
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .footer-links a:hover {
            color: white;
            transform: translateX(4px);
        }

        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 48px;
            height: 48px;
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
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s;
        }

        .btn-gradient:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 154, 218, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }
            
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .section-title h2 {
                font-size: 1.8rem;
            }
            
            .navbar-nav {
                padding: 10px 0;
            }
            
            .nav-link {
                margin: 5px 0;
            }
            
            .carousel-container {
                height: 300px;
            }
            
            .carousel-item {
                height: 300px;
            }
            
            .video-description-container {
                padding: 15px;
            }
            
            .video-description-title {
                font-size: 1.1rem;
            }
            
            .video-description-text {
                font-size: 0.9rem;
            }
            
            .dropdown-menu {
                position: static !important;
                float: none;
                width: auto;
                margin-top: 0;
                background-color: transparent;
                border: 0;
                box-shadow: none;
                padding-left: 20px;
            }
            
            .dropdown-submenu .dropdown-menu {
                left: 0;
                margin-left: 20px;
            }
            
            .dropdown-submenu > .dropdown-toggle::after {
                display: none;
            }
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

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-bolt me-2"></i>Kiboko Intranet
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php getMenu(); ?>
                </ul>
                <a href="admin/login.php" class="btn-login ms-3">
                    <i class="fas fa-user me-2"></i>Admin Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1>Welcome to Kiboko Intranet</h1>
                    <p>Your central hub for communication, collaboration, and company celebrations. Stay connected with your team and celebrate together!</p>
                    <a href="#" class="btn-hero btn-gradient">
                        <i class="fas fa-rocket me-2"></i>Get Started
                    </a>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <!-- Dynamic Carousel -->
                    <div class="carousel-card">
                        <div id="mediaCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                            <?php
                            $carouselItems = mysqli_query($conn, "SELECT * FROM homepage_carousel WHERE status = 1 ORDER BY sort_order ASC");
                            if ($carouselItems && mysqli_num_rows($carouselItems) > 0) {
                                $slideCount = mysqli_num_rows($carouselItems);
                            ?>
                            <div class="carousel-indicators">
                                <?php for($i = 0; $i < $slideCount; $i++): ?>
                                <button type="button" data-bs-target="#mediaCarousel" data-bs-slide-to="<?php echo $i; ?>" <?php echo $i == 0 ? 'class="active"' : ''; ?>></button>
                                <?php endfor; ?>
                            </div>
                            
                            <div class="carousel-inner">
                                <?php
                                $active = true;
                                while($slide = mysqli_fetch_assoc($carouselItems)):
                                    $mediaUrl = htmlspecialchars($slide['media_url']);
                                    $title = htmlspecialchars($slide['title']);
                                    $description = htmlspecialchars($slide['description']);
                                ?>
                                <div class="carousel-item <?php echo $active ? 'active' : ''; ?>">
                                    <?php if($slide['media_type'] == 'image'): ?>
                                        <img src="uploads/<?php echo $mediaUrl; ?>" class="d-block w-100" alt="<?php echo $title; ?>">
                                        <?php if(!empty($description)): ?>
                                        <div class="carousel-caption d-none d-md-block">
                                            <h5><?php echo $title; ?></h5>
                                            <p><?php echo $description; ?></p>
                                        </div>
                                        <?php endif; ?>
                                        
                                    <?php elseif($slide['media_type'] == 'youtube'): ?>
                                        <iframe src="https://www.youtube.com/embed/<?php echo $mediaUrl; ?>?autoplay=0&mute=1&controls=1&rel=0" 
                                                allowfullscreen 
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                                        </iframe>
                                        
                                    <?php elseif($slide['media_type'] == 'vimeo'): ?>
                                        <iframe src="https://player.vimeo.com/video/<?php echo $mediaUrl; ?>?autoplay=0&muted=1&controls=1" 
                                                allowfullscreen 
                                                allow="autoplay; fullscreen; picture-in-picture">
                                        </iframe>
                                    <?php endif; ?>
                                </div>
                                <?php 
                                    $active = false;
                                endwhile; 
                                ?>
                            </div>
                            
                            <!-- Video Descriptions - Displayed BELOW the video for better readability -->
                            <?php
                            // Reset pointer to show descriptions
                            mysqli_data_seek($carouselItems, 0);
                            $descActive = true;
                            while($slide = mysqli_fetch_assoc($carouselItems)):
                                if($slide['media_type'] != 'image' && !empty($slide['description'])):
                            ?>
                            <div class="video-description-container description-<?php echo $descActive ? 'active' : 'inactive'; ?>" 
                                 data-slide="<?php echo $descActive ? '0' : '1'; ?>">
                                <h4 class="video-description-title">
                                    <i class="fas fa-play-circle me-2"></i>
                                    <?php echo htmlspecialchars($slide['title']); ?>
                                </h4>
                                <p class="video-description-text">
                                    <?php echo htmlspecialchars($slide['description']); ?>
                                </p>
                            </div>
                            <?php 
                                endif;
                                $descActive = false;
                            endwhile; 
                            ?>
                            
                            <button class="carousel-control-prev" type="button" data-bs-target="#mediaCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#mediaCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                            <?php } else { ?>
                            <div class="text-center py-5">
                                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No carousel items available</p>
                            </div>
                            <?php } ?>
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

        <?php
        // Get today's birthdays
        $birthdayQuery = "SELECT * FROM birthdays 
                         WHERE MONTH(birth_date) = MONTH(CURDATE()) 
                         AND DAY(birth_date) = DAY(CURDATE())
                         AND status = 1
                         ORDER BY full_name ASC";
        $birthdays = mysqli_query($conn, $birthdayQuery);
        
        if ($birthdays && mysqli_num_rows($birthdays) > 0):
            $counter = 0;
        ?>
        <div class="row g-4">
            <?php while($bday = mysqli_fetch_assoc($birthdays)): ?>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo $counter * 100; ?>">
                <div class="birthday-card">
                    <!-- Confetti effect -->
                    <div class="confetti" style="left: 10%; animation-delay: 0s;"></div>
                    <div class="confetti" style="left: 30%; animation-delay: 0.5s;"></div>
                    <div class="confetti" style="left: 50%; animation-delay: 1s;"></div>
                    <div class="confetti" style="left: 70%; animation-delay: 1.5s;"></div>
                    <div class="confetti" style="left: 90%; animation-delay: 2s;"></div>
                    
                    <?php if(!empty($bday['photo']) && file_exists('uploads/' . $bday['photo'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($bday['photo']); ?>" alt="<?php echo htmlspecialchars($bday['full_name']); ?>" class="birthday-avatar">
                    <?php else: ?>
                        <div class="birthday-avatar bg-gradient d-flex align-items-center justify-content-center">
                            <i class="fas fa-user fa-4x text-white"></i>
                        </div>
                    <?php endif; ?>
                    
                    <h3 class="birthday-name"><?php echo htmlspecialchars($bday['full_name']); ?></h3>
                    <p class="birthday-dept"><?php echo !empty($bday['department']) ? htmlspecialchars($bday['department']) : 'Employee'; ?></p>
                    
                    <div class="birthday-date">
                        <i class="far fa-calendar-alt me-2"></i>
                        <?php echo date('F j', strtotime($bday['birth_date'])); ?>
                    </div>
                    
                    <?php if(!empty($bday['message'])): ?>
                        <p class="birthday-message">"<?php echo htmlspecialchars($bday['message']); ?>"</p>
                    <?php endif; ?>
                    
                    <div class="mt-3">
                        <span class="badge bg-gradient text-white px-4 py-2">
                            <i class="fas fa-gift me-2"></i>Happy Birthday!
                        </span>
                    </div>
                </div>
            </div>
            <?php 
                $counter++;
                endwhile; 
            ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5" data-aos="fade-up">
            <div class="display-1 mb-4">🎂</div>
            <h3 class="text-muted">No birthdays today</h3>
            <p class="text-muted">Check back tomorrow for more celebrations!</p>
        </div>
        <?php endif; ?>
    </section>

    <!-- Upcoming Birthdays Section -->
    <section class="container my-5">
        <div class="section-title" data-aos="fade-up">
            <h2><i class="far fa-calendar-alt me-2"></i>Upcoming Birthdays</h2>
            <div class="title-line"></div>
        </div>

        <?php
        // Get upcoming birthdays (next 7 days)
        $upcomingQuery = "SELECT *, 
                         DATE_FORMAT(birth_date, '%m-%d') as bday_md,
                         DATEDIFF(
                             DATE(CONCAT(YEAR(CURDATE()), '-', MONTH(birth_date), '-', DAY(birth_date))),
                             CURDATE()
                         ) as days_until
                         FROM birthdays 
                         WHERE status = 1
                         AND DATE(CONCAT(YEAR(CURDATE()), '-', MONTH(birth_date), '-', DAY(birth_date))) 
                             BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                         AND NOT (MONTH(birth_date) = MONTH(CURDATE()) AND DAY(birth_date) = DAY(CURDATE()))
                         ORDER BY bday_md ASC
                         LIMIT 6";
        $upcoming = mysqli_query($conn, $upcomingQuery);
        
        if ($upcoming && mysqli_num_rows($upcoming) > 0):
        ?>
        <div class="row g-3">
            <?php while($next = mysqli_fetch_assoc($upcoming)): ?>
            <div class="col-md-4 col-lg-2" data-aos="fade-up">
                <div class="card border-0 shadow-sm text-center p-3 h-100">
                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: var(--grad-light);">
                        <?php if(!empty($next['photo']) && file_exists('uploads/' . $next['photo'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($next['photo']); ?>" class="w-100 h-100 rounded-circle" style="object-fit: cover;">
                        <?php else: ?>
                            <i class="fas fa-user fa-2x text-gradient"></i>
                        <?php endif; ?>
                    </div>
                    <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($next['full_name']); ?></h6>
                    <small class="text-muted"><?php echo date('M j', strtotime($next['birth_date'])); ?></small>
                    <?php
                    $days = isset($next['days_until']) ? (int)$next['days_until'] : 0;
                    if($days == 0):
                        echo '<span class="badge bg-success mt-2">Today!</span>';
                    elseif($days == 1):
                        echo '<span class="badge bg-warning mt-2">Tomorrow</span>';
                    else:
                        echo '<span class="badge bg-info mt-2">In '.$days.' days</span>';
                    endif;
                    ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
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
                    <h5><i class="fas fa-bolt me-2"></i>Kiboko Intranet</h5>
                    <p class="text-white-50 small">Your central hub for communication, collaboration, and celebration.</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
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
                    
                </div>
            </div>
            <hr class="border-secondary mt-4">
            <div class="text-center text-white-50 small">
                &copy; 2024 Kiboko Intranet. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Back to top button
        window.onscroll = function() {
            const button = document.querySelector('.back-to-top');
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                button.style.display = 'flex';
            } else {
                button.style.display = 'none';
            }
        };

        // Birthday confetti effect
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(isset($birthdays) && mysqli_num_rows($birthdays) > 0): ?>
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
            <?php endif; ?>
        });

        // Carousel description synchronization
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.getElementById('mediaCarousel');
            if (carousel) {
                carousel.addEventListener('slid.bs.carousel', function(event) {
                    // Update video descriptions
                    const descriptions = document.querySelectorAll('[class^="video-description-container"]');
                    descriptions.forEach((desc, index) => {
                        if (index === event.to) {
                            desc.style.display = 'block';
                        } else {
                            desc.style.display = 'none';
                        }
                    });
                });

                // Initialize descriptions
                const descriptions = document.querySelectorAll('[class^="video-description-container"]');
                descriptions.forEach((desc, index) => {
                    if (index === 0) {
                        desc.style.display = 'block';
                    } else {
                        desc.style.display = 'none';
                    }
                });
            }
        });

        // Multi-level dropdown hover functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Handle all dropdowns with hover
            if (window.innerWidth > 768) {
                // For desktop - hover based
                document.querySelectorAll('.dropdown').forEach(dropdown => {
                    dropdown.addEventListener('mouseenter', function() {
                        const menu = this.querySelector('.dropdown-menu');
                        if (menu) {
                            menu.classList.add('show');
                        }
                    });
                    
                    dropdown.addEventListener('mouseleave', function() {
                        const menu = this.querySelector('.dropdown-menu');
                        if (menu) {
                            menu.classList.remove('show');
                        }
                        
                        // Also close all submenus
                        this.querySelectorAll('.dropdown-menu .dropdown-menu').forEach(submenu => {
                            submenu.classList.remove('show');
                        });
                    });
                });
                
                // Handle submenus
                document.querySelectorAll('.dropdown-submenu').forEach(submenu => {
                    submenu.addEventListener('mouseenter', function() {
                        const menu = this.querySelector('.dropdown-menu');
                        if (menu) {
                            menu.classList.add('show');
                        }
                    });
                    
                    submenu.addEventListener('mouseleave', function() {
                        const menu = this.querySelector('.dropdown-menu');
                        if (menu) {
                            menu.classList.remove('show');
                        }
                    });
                });
            } else {
                // For mobile - click based
                document.querySelectorAll('.dropdown-toggle').forEach(element => {
                    element.addEventListener('click', function(e) {
                        if (this.classList.contains('nav-link')) {
                            window.location = this.getAttribute('href');
                        } else {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            let submenu = this.nextElementSibling;
                            if (submenu && submenu.classList.contains('dropdown-menu')) {
                                submenu.classList.toggle('show');
                            }
                        }
                    });
                });
            }
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            });
        });

        // Active link highlighting
        const currentLocation = window.location.pathname;
        document.querySelectorAll('.nav-link').forEach(link => {
            if(link.getAttribute('href') === currentLocation) {
                link.classList.add('active');
            }
        });
    </script>
</body>
</html>
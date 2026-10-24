@verbatim

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIBOKO Academy - Learning & Development Portal</title>
      <link rel="stylesheet" href="assets/css/style.css">
   <link rel="icon" type="image/png" href="<?php echo asset('favi.png'); ?>">
  
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --kiboko-primary: #009ada;
            --kiboko-secondary: #76bc21;
            --kiboko-dark: #0f172a;
            --kiboko-light: #f8fafc;
            --kiboko-accent: #ff6b35;
            --kiboko-foundation: #4299e1;
            --kiboko-mastery: #48bb78;
            --kiboko-resource: #ed8936;
            --kiboko-compliance: #9f7aea;
        }

        /* Hero Section */
        .academy-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 4rem 0;
            position: relative;
            overflow: hidden;
        }

        .academy-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.05"><path d="M0 0 L100 100 M100 0 L0 100" stroke="white" stroke-width="1"/></svg>');
            background-size: 30px 30px;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: rgba(255,255,255,0.9);
            margin-bottom: 1.5rem;
        }

        .hero-text {
            color: rgba(255,255,255,0.7);
            font-size: 1.1rem;
            max-width: 500px;
        }

        .hero-video-container {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        /* Pathway Cards */
        .pathway-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            height: 100%;
        }

        .pathway-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,154,218,0.1);
            border-color: var(--kiboko-primary);
        }

        .pathway-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }

        .pathway-foundation .pathway-icon {
            background: linear-gradient(135deg, #4299e110, #4299e130);
            color: var(--kiboko-foundation);
        }

        .pathway-mastery .pathway-icon {
            background: linear-gradient(135deg, #48bb7810, #48bb7830);
            color: var(--kiboko-mastery);
        }

        .pathway-resource .pathway-icon {
            background: linear-gradient(135deg, #ed893610, #ed893630);
            color: var(--kiboko-resource);
        }

        .pathway-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .pathway-subtitle {
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .pathway-description {
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        .pathway-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .pathway-foundation .pathway-link {
            color: var(--kiboko-foundation);
        }

        .pathway-mastery .pathway-link {
            color: var(--kiboko-mastery);
        }

        .pathway-resource .pathway-link {
            color: var(--kiboko-resource);
        }

        .pathway-link:hover {
            gap: 1rem;
        }

        /* Section Headers */
        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .section-header .gradient-text {
            background: linear-gradient(135deg, var(--kiboko-primary), var(--kiboko-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-header p {
            color: #64748b;
            font-size: 1.1rem;
        }

        /* Course Cards */
        .course-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            height: 100%;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,154,218,0.1);
            border-color: var(--kiboko-primary);
        }

        .course-image {
            height: 210px;
            position: relative;
            background-color: #f8fafc;
        }

        .course-image img {
            width: 100%;
            height: 100%;
            object-fit: inherit;
        }

        .course-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 0.25rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 2;
        }

        .badge-foundation {
            background: var(--kiboko-foundation);
            color: white;
        }

        .badge-mastery {
            background: var(--kiboko-mastery);
            color: white;
        }

        .badge-compliance {
            background: var(--kiboko-compliance);
            color: white;
        }

        .badge-resource {
            background: var(--kiboko-resource);
            color: white;
        }

        .course-content {
            padding: 1.5rem;
        }

        .course-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .course-meta {
            display: flex;
            gap: 1rem;
            margin: 1rem 0;
            font-size: 0.9rem;
            color: #64748b;
        }

        .course-meta i {
            color: var(--kiboko-primary);
            margin-right: 0.3rem;
        }

        .btn-course {
            background: white;
            border: 2px solid var(--kiboko-primary);
            color: var(--kiboko-primary);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-course:hover {
            background: var(--kiboko-primary);
            color: white;
        }

        /* Compliance Section */
        .compliance-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 4rem 0;
        }

        .compliance-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
            border-left: 4px solid var(--kiboko-compliance);
            height: 100%;
        }

        .compliance-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .compliance-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #9f7aea10, #9f7aea30);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: var(--kiboko-compliance);
        }

        .compliance-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .compliance-meta {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 1rem;
        }

        /* Footer */
        .academy-footer {
            background: #0f172a;
            color: white;
            padding: 3rem 0 1.5rem;
        }

        .footer-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: white;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--kiboko-secondary);
        }

        .footer-bottom {
            border-top: 1px solid #334155;
            margin-top: 2rem;
            padding-top: 1.5rem;
            text-align: center;
            color: #64748b;
        }

        .support-box {
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .support-box i {
            color: var(--kiboko-secondary);
            margin-right: 0.5rem;
        }

        .support-box a {
            color: white;
            text-decoration: none;
        }

        .support-box a:hover {
            color: var(--kiboko-secondary);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .section-header h2 {
                font-size: 2rem;
            }
        }

        /* Loading states */
        .image-loading {
            position: relative;
            background: linear-gradient(110deg, #ececec 8%, #f5f5f5 18%, #ececec 33%);
            background-size: 200% 100%;
            animation: 1.5s shine linear infinite;
        }

        @keyframes shine {
            to {
                background-position-x: -200%;
            }
        }
     
        /* Landing menu: same look as the K-Academy bar on the other pages */
        .ka-land-nav { background: rgba(255, 255, 255, 0.92) !important; backdrop-filter: saturate(180%) blur(14px); border-bottom: 1px solid #e6ebf1; min-height: 68px; }
        .ka-land-nav::before { content: ""; position: absolute; left: 0; right: 0; top: 0; height: 3px; background: linear-gradient(90deg, #009ada, #76bc21); }
        .ka-land-mark { width: 38px; height: 38px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #009ada, #76bc21); color: #fff; font-size: 1.15rem; box-shadow: 0 6px 14px -6px rgba(0, 154, 218, 0.7); }
        .ka-land-nav .nav-link { font-weight: 600; color: #526077; border-radius: 10px; padding: 8px 14px !important; }
        .ka-land-nav .nav-link:hover { color: #0082b3; background: #f1f5f9; }
        .ka-land-actions .btn { border-radius: 999px; padding: 7px 16px; font-weight: 600; }
        .ka-land-home { border-color: #dbe3ec; color: #334155; }
        .ka-land-home:hover { border-color: #009ada; color: #0082b3; background: rgba(0, 154, 218, 0.06); }
        @media (max-width: 991.98px) { .ka-land-actions { padding: 10px 0 6px; } }
    </style>
</head>
<body>
    <!-- Navigation -->
     <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm ka-land-nav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index">
                <span class="ka-land-mark"><i class="bi bi-mortarboard-fill"></i></span>
                <span class="gradient-text fw-bold fs-3">K-Academy</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#pathways">Learning Paths</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#compliance">Compliance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="browse-course">All Courses</a>
                    </li>
                </ul>
                <div class="d-flex gap-2 ms-lg-3 ka-land-actions">
                    <a href="<?php echo url('/'); ?>" class="btn btn-outline-secondary btn-sm ka-land-home" title="Back to K-Connect">
                        <i class="bi bi-house-door"></i> K-Connect
                    </a>
                    <?php if ($user): ?>
                        <a href="dashboard" class="btn btn-primary btn-sm">
                            <i class="bi bi-speedometer2"></i> My Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?php echo url('login'); ?>" class="btn btn-primary btn-sm">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="academy-hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1 class="hero-title">Welcome to the Kiboko Academy</h1>
                    <p class="hero-subtitle">Master your role, sharpen your skills, and grow with the Kiboko family.</p>
                    <p class="hero-text">This is our centralized hub for company orientation, professional development, and the standard of excellence that defines us.</p>
                    
                    <div class="mt-4 d-flex gap-3">
                        <a href="#pathways" class="btn btn-primary btn-lg px-4">
                            Start Learning <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <a href="browse-course" class="btn btn-outline-light btn-lg px-4">
                            Browse Courses
                        </a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <?php if (!empty($hero_youtube_id)): ?>
                        <div class="hero-video-container ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/<?php echo $hero_youtube_id; ?>?autoplay=0&rel=0" 
                                    title="<?php echo htmlspecialchars($hero['video_title'] ?? 'Kiboko Academy'); ?>"
                                    allowfullscreen></iframe>
                        </div>
                        <?php if (!empty($hero['video_title'])): ?>
                            <p class="text-white-50 mt-2 text-center">
                                <small><?php echo htmlspecialchars($hero['video_title']); ?></small>
                            </p>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="hero-video-container ratio ratio-16x9 bg-dark d-flex align-items-center justify-content-center">
                            <div class="text-center text-white-50">
                                <i class="bi bi-camera-video display-1"></i>
                                <p class="mt-3">Welcome video coming soon</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Learning Pathways -->
    <section id="pathways" class="py-5">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2>Your <span class="gradient-text">Learning Pathways</span></h2>
                <p>Choose your journey: from foundation to mastery</p>
            </div>

            <div class="row g-4">
                <!-- K-Start: Foundation -->
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="pathway-card pathway-foundation">
                        <div class="pathway-icon">
                            <i class="bi bi-flag"></i>
                        </div>
                        <h3 class="pathway-title">K-Start: The Foundation</h3>
                        <p class="pathway-subtitle">Orientation & Culture</p>
                        <p class="pathway-description">Everything a new member needs to know about our history, values, and workplace policies.</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="badge bg-light text-dark"><?php echo count($foundation_courses); ?> courses</span>
                            <a href="browse-course?type=foundation" class="pathway-link pathway-foundation">
                                Begin Onboarding <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- K-Expert: Mastery -->
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="pathway-card pathway-mastery">
                        <div class="pathway-icon">
                            <i class="bi bi-stars"></i>
                        </div>
                        <h3 class="pathway-title">K-Expert: The Mastery</h3>
                        <p class="pathway-subtitle">Professional Mastery & Skills</p>
                        <p class="pathway-description">Deep Dive into comprehensive trainings from Skill development to Personality Development.</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="badge bg-light text-dark"><?php echo count($mastery_courses); ?> courses</span>
                            <a href="browse-course?type=mastery" class="pathway-link pathway-mastery">
                                Start Training <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- K-Library: Resource -->
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="pathway-card pathway-resource">
                        <div class="pathway-icon">
                            <i class="bi bi-journal-richtext"></i>
                        </div>
                        <h3 class="pathway-title">K-Library: The Resource</h3>
                        <p class="pathway-subtitle">Knowledge & SOPs</p>
                        <p class="pathway-description">A searchable archive of educational videos, "How-To" guides, and company-wide updates.</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="badge bg-light text-dark"><?php echo $resource_count; ?> resources</span>
                            <a href="browse-course?type=resource" class="pathway-link pathway-resource">
                                Browse Resources <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Courses by Pathway -->
    <?php if (!empty($foundation_courses) || !empty($mastery_courses)): ?>
    <section class="py-5 bg-light">
        <div class="container">
            <?php if (!empty($foundation_courses)): ?>
            <div class="mb-5" data-aos="fade-up">
                <h3 class="fw-bold mb-4">
                    <i class="bi bi-flag me-2" style="color: var(--kiboko-foundation);"></i>
                    Featured Foundation Courses
                </h3>
                <div class="row g-4">
                    <?php foreach ($foundation_courses as $course): ?>
                    <div class="col-md-4">
                        <div class="course-card">
                            <div class="course-image">
                                <?php 
                                $thumbnail_info = ka_course_thumbnail($course);
                                ?>
                                <?php if ($thumbnail_info['exists']): ?>
                                    <img src="<?php echo $thumbnail_info['url']; ?>" 
                                         alt="<?php echo htmlspecialchars($course['title']); ?>"
                                         loading="lazy"
                                         onerror="this.onerror=null; this.src='https://placehold.co/600x400/4299e1/white?text=Foundation+Course';">
                                <?php else: ?>
                                    <img src="<?php echo $thumbnail_info['url']; ?>" 
                                         alt="Foundation Course"
                                         loading="lazy">
                                <?php endif; ?>
                                <span class="course-badge badge-foundation">Foundation</span>
                            </div>
                            <div class="course-content">
                                <h5 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                                <p class="text-secondary small">
                                    <?php echo htmlspecialchars(substr($course['description'] ?? '', 0, 80)); ?>...
                                </p>
                                <div class="course-meta">
                                    <span><i class="bi bi-clock"></i> <?php echo $course['duration'] ?: 'Self-paced'; ?></span>
                                    <?php if (!empty($course['level'])): ?>
                                    <span><i class="bi bi-bar-chart"></i> <?php echo ucfirst($course['level']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="course-overview?id=<?php echo $course['id']; ?>" class="btn-course w-100 text-center">
                                    View Course
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($mastery_courses)): ?>
            <div class="mb-5" data-aos="fade-up">
                <h3 class="fw-bold mb-4">
                    <i class="bi bi-stars me-2" style="color: var(--kiboko-mastery);"></i>
                    Featured Mastery Courses
                </h3>
                <div class="row g-4">
                    <?php foreach ($mastery_courses as $course): ?>
                    <div class="col-md-4">
                        <div class="course-card">
                            <div class="course-image">
                                <?php 
                                $thumbnail_info = ka_course_thumbnail($course);
                                ?>
                                <?php if ($thumbnail_info['exists']): ?>
                                    <img src="<?php echo $thumbnail_info['url']; ?>" 
                                         alt="<?php echo htmlspecialchars($course['title']); ?>"
                                         loading="lazy"
                                         onerror="this.onerror=null; this.src='https://placehold.co/600x400/48bb78/white?text=Mastery+Course';">
                                <?php else: ?>
                                    <img src="<?php echo $thumbnail_info['url']; ?>" 
                                         alt="Mastery Course"
                                         loading="lazy">
                                <?php endif; ?>
                                <span class="course-badge badge-mastery">Mastery</span>
                            </div>
                            <div class="course-content">
                                <h5 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                                <p class="text-secondary small">
                                    <?php echo htmlspecialchars(substr($course['description'] ?? '', 0, 80)); ?>...
                                </p>
                                <div class="course-meta">
                                    <span><i class="bi bi-clock"></i> <?php echo $course['duration'] ?: 'Self-paced'; ?></span>
                                    <?php if (!empty($course['level'])): ?>
                                    <span><i class="bi bi-bar-chart"></i> <?php echo ucfirst($course['level']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="course-overview?id=<?php echo $course['id']; ?>" class="btn-course w-100 text-center">
                                    View Course
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Compliance Center -->
    <!-- Compliance Center -->
<section id="compliance" class="compliance-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2><span class="gradient-text">K-Compliance Center</span></h2>
            <p>Essential training for workplace compliance</p>
        </div>

        <div class="row g-4">
            <?php if (!empty($compliance_courses)): ?>
                <?php foreach ($compliance_courses as $index => $course): ?>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="course-card">
                        <div class="course-image">
                            <?php 
                            // Get thumbnail info using the helper function
                            $thumbnail_info = ka_course_thumbnail($course);
                            
                            // Determine badge color based on category
                            $badge_class = 'badge-compliance';
                            $badge_text = 'Compliance';
                            ?>
                            <?php if ($thumbnail_info['exists']): ?>
                                <img src="<?php echo $thumbnail_info['url']; ?>" 
                                     alt="<?php echo htmlspecialchars($course['title']); ?>"
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='https://placehold.co/600x400/9f7aea/white?text=Compliance+Course';">
                            <?php else: ?>
                                <img src="<?php echo $thumbnail_info['url']; ?>" 
                                     alt="Compliance Course"
                                     loading="lazy">
                            <?php endif; ?>
                            <span class="course-badge <?php echo $badge_class; ?>"><?php echo $badge_text; ?></span>
                        </div>
                        <div class="course-content">
                            <h5 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                            <p class="text-secondary small">
                                <?php echo htmlspecialchars(substr($course['description'] ?? '', 0, 80)); ?>...
                            </p>
                            <div class="course-meta">
                                <span><i class="bi bi-clock"></i> <?php echo $course['duration'] ?: 'Self-paced'; ?></span>
                                <?php if (!empty($course['level'])): ?>
                                <span><i class="bi bi-bar-chart"></i> <?php echo ucfirst($course['level']); ?></span>
                                <?php endif; ?>
                            </div>
                            <a href="course-overview?id=<?php echo $course['id']; ?>" class="btn-course w-100 text-center">
                                Start Training
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4">
                    <i class="bi bi-shield-check display-1 text-muted"></i>
                    <h4 class="mt-3">Compliance courses coming soon</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

    <!-- Footer -->
    <footer class="academy-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="footer-title">K-Academy</h5>
                    <p class="text-white-50">Empowering Kiboko employees through continuous learning and development.</p>
                    <div class="support-box">
                        <i class="bi bi-question-circle"></i>
                        <strong>Need Help?</strong><br>
                        <small>Can't find a video? Contact <a href="#">HR</a> or <a href="#">IT Support</a></small>
                    </div>
                </div>
                
                <div class="col-md-2 mb-4">
                    <h5 class="footer-title">Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="<?php echo url('/'); ?>">Back to K-Connect</a></li>
                        <li><a href="https://kibokoenterprises.com/" target="_blank">Company Website</a></li>
                        <li><a href="#" target="_blank">Employee Handbook</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h5 class="footer-title">Learning Paths</h5>
                    <ul class="footer-links">
                        <li><a href="browse-course?type=foundation">K-Start: Foundation</a></li>
                        <li><a href="browse-course?type=mastery">K-Expert: Mastery</a></li>
                        <li><a href="browse-course?type=resource">K-Library: Resources</a></li>
                        <li><a href="browse-course?type=compliance">K-Compliance Center</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h5 class="footer-title">Support</h5>
                    <ul class="footer-links">
                        <li>Extension <i class="bi bi-telephone me-2"> :</i> <a href="tel:801">801</a></li>
                        <li><i class="bi bi-headset me-2"></i> <a href="#">IT Help Desk</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <small>&copy; <?php echo date('Y'); ?> Kiboko Group. All rights reserved. | Version 2.0</small>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });

        // Add image error handling for all course images
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.course-image img');
            images.forEach(img => {
                img.addEventListener('error', function() {
                    const category = this.closest('.course-card')?.querySelector('.course-badge')?.textContent || 'Course';
                    this.src = 'https://placehold.co/600x400/009ada/white?text=' + encodeURIComponent(category);
                });
            });
        });
    </script>
</body>
</html>
@endverbatim

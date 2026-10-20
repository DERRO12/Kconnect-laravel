<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rewards & Recognition - K-Connect</title>
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 1rem 0;
        }

        .navbar-brand img {
            height: 25px;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 40px 0;
            border-bottom: 1px solid #eef2f6;
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0;
        }

        .page-description {
            color: #64748b;
            font-size: 1.1rem;
            margin-top: 10px;
        }

        .text-gradient {
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Rewards Grid */
        .rewards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .reward-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #eef2f6;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .reward-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,154,218,0.1);
            border-color: var(--kiboko-blue);
        }

        .card-media {
            position: relative;
            height: 200px;
            background: #000;
            overflow: hidden;
        }

        .thumbnail-wrapper {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .thumbnail-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.8;
        }

        .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            background: var(--grad);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }

        .reward-card:hover .play-button {
            transform: translate(-50%, -50%) scale(1.1);
        }

        .media-badge {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .media-badge i {
            margin-right: 5px;
        }

        .card-content {
            padding: 20px;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #1e293b;
            line-height: 1.4;
        }

        .card-description {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 0.85rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #64748b;
        }

        .meta-item i {
            color: var(--kiboko-blue);
            font-size: 0.9rem;
        }

        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 15px;
            border-top: 1px solid #eef2f6;
        }

        .view-btn {
            background: transparent;
            border: 2px solid #eef2f6;
            padding: 8px 20px;
            border-radius: 30px;
            color: #1e293b;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .view-btn:hover {
            background: var(--grad);
            border-color: transparent;
            color: white;
        }

        .view-btn:hover i {
            color: white;
        }

        .date-badge {
            color: #94a3b8;
            font-size: 0.85rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 24px;
            border: 1px solid #eef2f6;
        }

        .empty-state i {
            font-size: 5rem;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #64748b;
            font-size: 1.1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }
            
            .rewards-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Loading Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reward-card {
            animation: fadeInUp 0.5s ease forwards;
        }

        .reward-card:nth-child(1) { animation-delay: 0.1s; }
        .reward-card:nth-child(2) { animation-delay: 0.15s; }
        .reward-card:nth-child(3) { animation-delay: 0.2s; }
        .reward-card:nth-child(4) { animation-delay: 0.25s; }
        .reward-card:nth-child(5) { animation-delay: 0.3s; }
        .reward-card:nth-child(6) { animation-delay: 0.35s; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="./">
                <img src="K-CONNECT.png" alt="K-CONNECT">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @php
 getMenu(); 
@endphp
                </ul>
                @if (isLoggedIn())
                <div class="dropdown ms-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        @if (!empty(session('employee_photo')))
                            <img src="uploads/employees/{{ session('employee_photo') }}" 
                                 width="36" height="36" class="rounded-circle me-2">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" 
                                 style="width: 36px; height: 36px;">
                                <i class="fas fa-user text-gradient"></i>
                            </div>
                        @endif
                        <span class="d-none d-lg-inline">{{ session('employee_name') }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">{{ session('employee_code') }}</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>
                <span class="text-gradient">Rewards &</span> Recognition
            </h1>
            <p class="page-description">
                Celebrating excellence and achievements across Kiboko
            </p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container mb-5">
        @if ($rewards && count($rewards) > 0)
            <div class="rewards-grid">
                @foreach ($rewards as $reward)
@php
// Generate YouTube thumbnail
                    // (images/video-placeholder.jpg never existed: an inline placeholder instead)
                    $thumbnail = 'data:image/svg+xml,'.rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="640" height="360" viewBox="0 0 640 360"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#009ada"/><stop offset="1" stop-color="#76bc21"/></linearGradient></defs><rect width="640" height="360" fill="url(#g)"/></svg>');
                    if($reward['video_type'] == 'youtube' && !empty($reward['video_id'])) {
                        $thumbnail = "https://img.youtube.com/vi/{$reward['video_id']}/hqdefault.jpg";
                    }
@endphp
                <div class="reward-card" onclick="window.location.href='reward-detail?id={!! $reward['id'] !!}'">
                    <div class="card-media">
                        <div class="thumbnail-wrapper">
                            <img src="{{ $thumbnail }}" alt="{{ $reward['title'] }}" class="thumbnail-img">
                            <div class="play-button">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                        <div class="media-badge">
                            <i class="fas fa-video"></i> Video
                        </div>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">{{ $reward['title'] }}</h3>
                        @if (!empty($reward['description']))
                            <p class="card-description">{{ $reward['description'] }}</p>
                        @endif
                        
                        <div class="card-meta">
                            <span class="meta-item">
                                <i class="fas fa-eye"></i> {!! $reward['views_count'] !!} views
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-comments"></i> {!! $reward['comment_count'] !!} comments
                            </span>
                            @if ($reward['attachment_count'] > 0)
                            <span class="meta-item">
                                <i class="fas fa-paperclip"></i> {!! $reward['attachment_count'] !!} files
                            </span>
                            @endif
                        </div>
                        
                        <div class="card-footer">
                            <span class="date-badge">
                                <i class="far fa-calendar-alt me-1"></i>
                                {!! date('M j, Y', strtotime($reward['created_at'])) !!}
                            </span>
                            <span class="view-btn">
                                View Details <i class="fas fa-arrow-right"></i>
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <i class="fas fa-trophy"></i>
                <h3>No Recognition Posts Yet</h3>
                <p class="text-muted">Check back soon for new recognition and celebrations!</p>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
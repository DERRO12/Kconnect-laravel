{{-- news-details --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="theme-color" content="#009ada">
    <title>{{ $news['title'] }} - {{ SITE_NAME }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    
    <style>
        :root {
            --kiboko-blue: #009ada;
            --kiboko-green: #76bc21;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(145deg, #f8fafc 0%, #eef2f6 100%);
            padding-top: 70px;
        }
        
        .bg-animation {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            animation: float 20s infinite alternate ease-in-out;
        }
        
        .orb-1 { background: var(--kiboko-blue); width: 500px; height: 500px; top: -150px; left: -150px; }
        .orb-2 { background: var(--kiboko-green); width: 450px; height: 450px; bottom: -150px; right: -150px; animation-delay: 5s; }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -20px) scale(1.05); }
            66% { transform: translate(-20px, 30px) scale(0.98); }
        }
        
        .news-details-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .news-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .news-image {
            width: 100%;
            max-height: 500px;
            object-fit: contain;
            background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%);
            padding: 1rem;
        }
        
        .carousel-item img {
            width: 100%;
            max-height: 500px;
            object-fit: contain;
            background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%);
            padding: 1rem;
        }
        
        .carousel-control-prev, .carousel-control-next {
            width: 40px;
            height: 40px;
            background: rgba(0,0,0,0.5);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            opacity: 1;
        }
        
        .carousel-control-prev:hover, .carousel-control-next:hover {
            background: var(--kiboko-blue);
        }
        
        .news-content {
            padding: 2rem;
        }
        
        .category-badge-large {
            padding: 0.5rem 1.5rem;
            font-size: 0.85rem;
            border-radius: 50px;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .news-title {
            font-size: clamp(1.5rem, 5vw, 2.2rem);
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        
        .news-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #eef2f6;
            color: #64748b;
            font-size: 0.9rem;
        }
        
        .news-meta i {
            color: var(--kiboko-blue);
            margin-right: 0.5rem;
        }
        
        .news-body {
            font-size: 1rem;
            line-height: 1.8;
            color: #334155;
        }
        
        .news-body p {
            margin-bottom: 1.5rem;
        }
        
        .share-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eef2f6;
        }
        
        .share-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .share-btn:hover {
            transform: translateY(-3px);
        }
        
        .comment-item {
            background: #f8fafc;
            border-radius: 16px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        
        .comment-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--grad);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        
        .comment-form textarea, .comment-form input {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s;
        }
        
        .comment-form textarea:focus, .comment-form input:focus {
            outline: none;
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 3px rgba(0,154,218,0.1);
        }
        
        .sidebar-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
        }
        
        .sidebar-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .related-news-item {
            display: flex;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f5f9;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s;
        }
        
        .related-news-item:hover {
            transform: translateX(5px);
        }
        
        .related-news-img {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            object-fit: cover;
            background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%);
            flex-shrink: 0;
        }
        
        .related-news-title {
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,154,218,0.3);
            color: white;
        }
        
        .back-to-home {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: white;
            color: var(--kiboko-blue);
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            margin-bottom: 1rem;
            transition: all 0.3s;
            border: 1px solid #e2e8f0;
        }
        
        .back-to-home:hover {
            background: var(--grad);
            color: white;
            transform: translateX(-5px);
        }
        
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 45px;
            height: 45px;
            background: var(--grad);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99;
        }
        
        @media (max-width: 768px) {
            body {
                padding-top: 60px;
            }
            
            .news-content {
                padding: 1.25rem;
            }
            
            .news-meta {
                gap: 1rem;
                font-size: 0.75rem;
            }
            
            .sidebar-card {
                margin-top: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="bg-animation">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
    </div>

    @include('partials.navbar')

    <div class="container mb-5">
        <div class="news-details-container">
            <!-- Back Button -->
            <a href="{{ url('all-news') }}" class="back-to-home">
                <i class="fas fa-arrow-left"></i> Back to All News
            </a>
            
            <!-- Success Messages -->
            @if (request()->query->has('comment') && request()->query('comment') == 'success')
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i> Your comment has been posted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            @if (request()->query->has('liked'))
            <div class="alert alert-info alert-dismissible fade show">
                <i class="fas fa-heart me-2"></i> You liked this post!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            @if (isset($alreadyLiked) && $alreadyLiked)
            <div class="alert alert-warning alert-dismissible fade show">
                <i class="fas fa-info-circle me-2"></i> You have already liked this post.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="news-card" data-aos="fade-up">
                        <!-- Images Gallery -->
                        @if ($hasFeaturedImage || $hasAdditionalImages)
                            @if ($totalImages > 1)
                            <div id="newsCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    @if ($hasFeaturedImage)
                                    <div class="carousel-item active">
                                        <img src="{{ asset('uploads') }}/news/{{ $news['featured_image'] }}" 
                                             class="d-block w-100" alt="{{ $news['title'] }}">
                                    </div>
                                    @endif
                                    @php $isFirst = !$hasFeaturedImage; @endphp
                                    @if ($hasAdditionalImages)
                                        @foreach ($images as $img)
                                    <div class="carousel-item {!! $isFirst ? 'active' : '' !!}@php $isFirst = false; @endphp">
                                        <img src="{{ asset('uploads') }}/news/{{ $img['image_path'] }}" 
                                             class="d-block w-100" alt="{{ $img['caption'] ?: $news['title'] }}">
                                        @if (!empty($img['caption']))
                                        <div class="carousel-caption d-none d-md-block">
                                            <p class="bg-dark bg-opacity-50 rounded px-3 py-1 d-inline-block">
                                                {{ $img['caption'] }}
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach @endif
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                            @elseif ($hasFeaturedImage)
                            <img src="{{ asset('uploads') }}/news/{{ $news['featured_image'] }}" 
                                 class="news-image w-100" alt="{{ $news['title'] }}">
                            @endif
                        @endif
                        
                        <div class="news-content">
                            <span class="category-badge-large bg-{!! \App\Support\HomeData::categoryClass($news['category']) !!} text-white">
                                {{ $news['category'] }}
                            </span>
                            
                            <h1 class="news-title">{{ $news['title'] }}</h1>
                            
                            <div class="news-meta">
                                <span><i class="fas fa-user"></i> {{ $news['author_name'] }}</span>
                                <span><i class="far fa-calendar-alt"></i> {!! date('F j, Y', strtotime($news['created_at'])) !!}</span>
                                <span><i class="far fa-comment"></i> {!! (int)$news['comments_count'] !!} Comments</span>
                                <span>
                                    <a href="?id={!! $newsId !!}&like=1" class="text-decoration-none" style="color: inherit;">
                                        <i class="far fa-heart"></i> {!! (int)$news['likes_count'] !!} Likes
                                    </a>
                                </span>
                            </div>
                            
                            <div class="news-body">
                                {!! nl2br(htmlspecialchars($news['content'])) !!}
                            </div>
                            
                            <!-- Share Buttons -->
                            <div class="share-buttons">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={!! urlencode($shareUrl) !!}" 
                                   target="_blank" class="share-btn" style="background: #1877f2; color: white;">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={!! urlencode($shareUrl) !!}&text={!! urlencode($news['title']) !!}" 
                                   target="_blank" class="share-btn" style="background: #1da1f2; color: white;">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={!! urlencode($shareUrl) !!}" 
                                   target="_blank" class="share-btn" style="background: #0077b5; color: white;">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="mailto:?subject={!! urlencode($news['title']) !!}&body={!! urlencode('Check out this article: ' . $shareUrl) !!}" 
                                   class="share-btn" style="background: #ea4335; color: white;">
                                    <i class="far fa-envelope"></i>
                                </a>
                            </div>
                            
                            <!-- Comments Section -->
                            <div class="mt-4 pt-3">
                                <h5 class="fw-bold mb-4">
                                    <i class="fas fa-comments me-2" style="color: var(--kiboko-blue);"></i>
                                    Comments ({!! (int)$news['comments_count'] !!})
                                </h5>
                                
                                <div class="comment-form mb-4 p-4 bg-light rounded-3">
                                    <h6 class="fw-bold mb-3">Leave a Comment</h6>
                                    <form method="POST">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <input type="text" name="commenter_name" class="form-control" placeholder="Your Name" required>
                                            </div>
                                            <div class="col-12">
                                                <textarea name="comment_text" class="form-control" rows="3" placeholder="Write your comment..." required></textarea>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" name="submit_comment" class="btn-gradient">
                                                    <i class="fas fa-paper-plane me-2"></i>Post Comment
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                
                                <div class="comments-list">
                                    @if (count($comments) > 0)
                                        @foreach ($comments as $comment)
                                        <div class="comment-item d-flex gap-3">
                                            <div class="comment-avatar">
                                                {!! strtoupper(substr($comment['commenter_name'] ?: 'A', 0, 1)) !!}
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
                                                    <strong class="text-dark">{{ $comment['commenter_name'] ?: 'Anonymous' }}</strong>
                                                    <small class="text-muted">{!! date('M d, Y H:i', strtotime($comment['created_at'])) !!}</small>
                                                </div>
                                                <p class="mb-0 text-muted">{!! nl2br(htmlspecialchars($comment['comment_text'])) !!}</p>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <i class="far fa-comment-dots fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No comments yet. Be the first to comment!</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sidebar-card" data-aos="fade-left">
                        <h5 class="sidebar-title">
                            <i class="fas fa-newspaper text-gradient"></i>
                            More News
                        </h5>
                        
                        @if (count($otherNewsRows) > 0)
                            @foreach ($otherNewsRows as $otherNews)
                                <a href="news-details?id={!! $otherNews['id'] !!}" class="related-news-item">
                                    @if (!empty($otherNews['featured_image']) && file_exists(kc_public_file('uploads/news/' . $otherNews['featured_image'])))
                                        <img src="{{ asset('uploads') }}/news/{{ $otherNews['featured_image'] }}" 
                                             alt="{{ $otherNews['title'] }}"
                                             class="related-news-img">
                                    @else
                                        <div class="related-news-img d-flex align-items-center justify-content-center">
                                            <i class="fas fa-newspaper fa-2x text-muted" style="opacity: 0.3;"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="related-news-title">{{ $otherNews['title'] }}</h6>
                                        <small class="text-muted">
                                            <i class="far fa-calendar-alt"></i> {!! date('M d, Y', strtotime($otherNews['created_at'])) !!}
                                        </small>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <p class="text-muted text-center py-3">No other news available</p>
                        @endif
                        
                        <div class="text-center mt-3">
                            <a href="{{ url('all-news') }}" class="btn-gradient w-100">
                                <i class="fas fa-list me-2"></i>View All News
                            </a>
                        </div>
                    </div>
                    
                    <div class="sidebar-card" data-aos="fade-left" data-aos-delay="100">
                        <h5 class="sidebar-title">
                            <i class="fas fa-tags text-gradient"></i>
                            Categories
                        </h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ url('all-news') }}?category=Announcements" class="badge bg-primary text-white text-decoration-none px-3 py-2">Announcements</a>
                            <a href="{{ url('all-news') }}?category=Events" class="badge bg-success text-white text-decoration-none px-3 py-2">Events</a>
                            <a href="{{ url('all-news') }}?category=People" class="badge bg-warning text-white text-decoration-none px-3 py-2">People</a>
                            <a href="{{ url('all-news') }}?category=Milestones" class="badge bg-info text-white text-decoration-none px-3 py-2">Milestones</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')

    <button class="back-to-top" id="backToTop" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });
        
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', function() {
            backToTop.style.display = window.pageYOffset > 300 ? 'flex' : 'none';
        });
    </script>
</body>
</html>
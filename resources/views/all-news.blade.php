{{-- all-news --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="theme-color" content="#009ada">
    <title>All News - {{ SITE_NAME }}</title>
    
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
            --grad-light: linear-gradient(135deg, rgba(0,154,218,0.08) 0%, rgba(118,188,33,0.08) 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(145deg, #f8fafc 0%, #eef2f6 100%);
            min-height: 100vh;
            padding-top: 70px;
        }
        
        /* Background Animation */
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
        .orb-3 { background: #38bdf8; width: 350px; height: 350px; top: 40%; right: 20%; animation-delay: 10s; opacity: 0.2; }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -20px) scale(1.05); }
            66% { transform: translate(-20px, 30px) scale(0.98); }
        }
        
        /* Page Header */
        .page-header {
            background: white;
            padding: 2rem 0;
            border-bottom: 1px solid #eef2f6;
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            font-size: clamp(1.8rem, 5vw, 2.5rem);
            font-weight: 800;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }
        
        .text-gradient {
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
        }
        
        .category-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .category-badge {
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            background: #f1f5f9;
            color: #64748b;
        }
        
        .category-badge:hover, .category-badge.active {
            background: var(--grad);
            color: white;
            transform: translateY(-2px);
        }
        
        .search-box {
            position: relative;
            max-width: 400px;
        }
        
        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem;
            padding-right: 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 50px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 3px rgba(0,154,218,0.1);
        }
        
        .search-box button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--grad);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
        }
        
        /* News Card */
        .news-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
        }
        
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        
        .news-card-image {
            position: relative;
            height: 200px;
            overflow: hidden;
            background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%);
        }
        
        .news-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .news-card:hover .news-card-image img {
            transform: scale(1.05);
        }
        
        .news-card-category {
            position: absolute;
            top: 1rem;
            left: 1rem;
            padding: 0.25rem 1rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            z-index: 1;
        }
        
        .news-card-body {
            padding: 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .news-card-title {
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1.4;
            margin-bottom: 0.75rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            color: #1e293b;
        }
        
        .news-card-excerpt {
            font-size: 0.85rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .news-card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid #eef2f6;
            font-size: 0.75rem;
            color: #94a3b8;
        }
        
        .news-card-meta i {
            margin-right: 0.25rem;
        }
        
        .btn-read-more {
            background: var(--grad);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            justify-content: center;
            margin-top: 0.75rem;
        }
        
        .btn-read-more:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,154,218,0.3);
            color: white;
        }
        
        /* Pagination */
        .pagination {
            justify-content: center;
            margin-top: 2rem;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .page-link {
            border-radius: 50px !important;
            padding: 0.5rem 1rem;
            color: #64748b;
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
        }
        
        .page-item.active .page-link {
            background: var(--grad);
            border-color: transparent;
        }
        
        .page-link:hover {
            background: var(--grad-light);
            border-color: var(--kiboko-blue);
            color: var(--kiboko-blue);
        }
        
        /* Back to Top Button */
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
            box-shadow: 0 5px 15px rgba(0,154,218,0.3);
            z-index: 99;
            transition: all 0.3s;
        }
        
        .back-to-top:hover {
            transform: translateY(-3px);
        }
        
        /* No Results */
        .no-results {
            text-align: center;
            padding: 4rem;
            background: white;
            border-radius: 20px;
        }
        
        .no-results i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding-top: 60px;
            }
            
            .news-card-image {
                height: 180px;
            }
            
            .filter-section {
                padding: 1rem;
            }
            
            .category-filter {
                gap: 0.5rem;
            }
            
            .category-badge {
                padding: 0.35rem 1rem;
                font-size: 0.75rem;
            }
        }
        
        @media (max-width: 576px) {
            .news-card-image {
                height: 160px;
            }
            
            .news-card-title {
                font-size: 1rem;
            }
            
            .page-header {
                padding: 1.5rem 0;
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

    <!-- Navbar -->
    @include('partials.navbar')

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="text-center">
                <h1 data-aos="fade-down">
                    <span class="text-gradient">All</span> News
                </h1>
                <p class="text-muted mb-0" data-aos="fade-down" data-aos-delay="100">
                    Stay updated with the latest announcements, events, and milestones
                </p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container mb-5">
        <!-- Filter Section -->
        <div class="filter-section" data-aos="fade-up">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <div class="category-filter">
                        <a href="{{ url('all-news') }}" class="category-badge {!! empty($category) ? 'active' : '' !!}">
                            <i class="fas fa-newspaper me-1"></i> All
                        </a>
                        @if (count($categories) > 0)
                            @foreach ($categories as $cat)
                        <a href="?category={!! urlencode($cat['category']) !!}" 
                           class="category-badge {!! $category == $cat['category'] ? 'active' : '' !!}">
                            {{ $cat['category'] }} 
                            <span class="badge bg-white text-dark ms-1" style="font-size: 0.65rem;">{!! $cat['count'] !!}</span>
                        </a>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    <form method="GET" action="{{ url('all-news') }}" class="search-box">
                        @if (!empty($category))
                            <input type="hidden" name="category" value="{{ $category }}">
                        @endif
                        <input type="text" name="search" placeholder="Search news..." value="{{ $search }}">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </div>

        <!-- News Grid -->
        @if (count($newsRows) > 0)
        <div class="row g-4">
            @php $delay = 0; @endphp
            @foreach ($newsRows as $news)
            @php
                $hasImage = !empty($news['featured_image']) && file_exists(kc_public_file('uploads/news/' . $news['featured_image']));
                $categoryClass = \App\Support\HomeData::categoryClass($news['category']);
            @endphp
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{!! $delay !!}">
                <div class="news-card">
                    <div class="news-card-image">
                        @if ($hasImage)
                            <img src="{{ asset('uploads') }}/news/{{ $news['featured_image'] }}" 
                                 alt="{{ $news['title'] }}"
                                 onerror="this.src='https://via.placeholder.com/400x200?text=No+Image'">
                        @else
                            <div style="height: 100%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-newspaper fa-4x text-muted" style="opacity: 0.3;"></i>
                            </div>
                        @endif
                        <span class="news-card-category bg-{!! $categoryClass !!} text-white">
                            {{ $news['category'] }}
                        </span>
                    </div>
                    <div class="news-card-body">
                        <h5 class="news-card-title">{{ $news['title'] }}</h5>
                        <p class="news-card-excerpt">
                            {{ substr(strip_tags($news['content']), 0, 120) }}...
                        </p>
                        <div class="news-card-meta">
                            <span><i class="far fa-calendar-alt"></i> {!! date('M d, Y', strtotime($news['created_at'])) !!}</span>
                            <span><i class="far fa-comment"></i> {!! (int)$news['comments_count'] !!}</span>
                            <span><i class="far fa-heart"></i> {!! (int)$news['likes_count'] !!}</span>
                        </div>
                        <a href="news-details?id={!! $news['id'] !!}" class="btn-read-more">
                            Read More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @php $delay += 100; @endphp
            @endforeach
        </div>

        <!-- Pagination -->
        @if ($total_pages > 1)
        <nav aria-label="Page navigation">
            <ul class="pagination">
                @if ($page > 1)
                <li class="page-item">
                    <a class="page-link" href="?page={!! $page-1 !!}{!! !empty($category) ? '&category='.urlencode($category) : '' !!}{!! !empty($search) ? '&search='.urlencode($search) : '' !!}">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                </li>
                @endif

                @php
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                @endphp
                @if ($start > 1)
                <li class="page-item"><a class="page-link" href="?page=1{!! !empty($category) ? '&category='.urlencode($category) : '' !!}{!! !empty($search) ? '&search='.urlencode($search) : '' !!}">1</a></li>
                @if ($start > 2)
                <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif @endif

                @for ($i = $start; $i <= $end; $i++)
                <li class="page-item {!! $i == $page ? 'active' : '' !!}">
                    <a class="page-link" href="?page={!! $i !!}{!! !empty($category) ? '&category='.urlencode($category) : '' !!}{!! !empty($search) ? '&search='.urlencode($search) : '' !!}">
                        {!! $i !!}
                    </a>
                </li>
                @endfor

                @if ($end < $total_pages)
                @if ($end < $total_pages - 1)
                <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
                <li class="page-item"><a class="page-link" href="?page={!! $total_pages !!}{!! !empty($category) ? '&category='.urlencode($category) : '' !!}{!! !empty($search) ? '&search='.urlencode($search) : '' !!}">{!! $total_pages !!}</a></li>
                @endif

                @if ($page < $total_pages)
                <li class="page-item">
                    <a class="page-link" href="?page={!! $page+1 !!}{!! !empty($category) ? '&category='.urlencode($category) : '' !!}{!! !empty($search) ? '&search='.urlencode($search) : '' !!}">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                @endif
            </ul>
        </nav>
        @endif
        
        @else
        <div class="no-results">
            <i class="far fa-newspaper"></i>
            <h5>No News Found</h5>
            <p class="text-muted">{!! !empty($search) ? 'No news matching your search criteria.' : 'No news available at the moment.' !!}</p>
            <a href="{{ url('all-news') }}" class="btn btn-gradient mt-3">
                <i class="fas fa-sync-alt me-2"></i>View All News
            </a>
        </div>
        @endif
    </div>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
        
        // Back to top button
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTop.style.display = 'flex';
            } else {
                backToTop.style.display = 'none';
            }
        });
    </script>
</body>
</html>
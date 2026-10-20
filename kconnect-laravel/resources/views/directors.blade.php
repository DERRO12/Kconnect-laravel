<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leadership & Directors - K-Connect</title>
    
    <!-- Preconnect for faster resource loading -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://unpkg.com">
    
    <!-- Critical CSS - Inlined for instant render -->
    <style>
        /* Minimal critical CSS for above-the-fold content */
        :root {
            --blue: #009ada;
            --green: #76bc21;
            --dark: #1e293b;
            --gray: #64748b;
            --light: #f8fafc;
            --border: #eef2f6;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        *{margin:0;padding:0;box-sizing:border-box}
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f5f7fa;
            color: var(--dark);
            -webkit-font-smoothing: antialiased;
            padding-top: 76px;
        }
        /* Sticky Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            padding: 0.75rem 0;
            height: 76px;
        }
        .navbar-brand img{height:24px;width:auto}
        
        /* Page Header */
        .page-header {
            text-align: center;
            padding: 40px 0 25px;
        }
        .page-header h1 {
            font-size: clamp(1.8rem, 4vw, 2.4rem);
            font-weight: 700;
            color: var(--dark);
        }
        .text-gradient {
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .page-description {
            color: var(--gray);
            font-size: 1.05rem;
            margin-top: 8px;
        }
        
        /* Section Titles */
        .section-title {
            text-align: center;
            margin: 35px 0 25px;
        }
        .section-title h2 {
            font-size: clamp(1.4rem, 3vw, 1.7rem);
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 10px;
        }
        .title-line {
            width: 50px;
            height: 3px;
            background: var(--grad);
            margin: 0 auto;
            border-radius: 3px;
        }
        
        /* Cards Grid - CSS Grid for performance */
        .leadership-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
            padding: 10px 0;
            contain: layout style;
        }
        .single-card-grid {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
        }
        .single-card-grid .leadership-card {
            max-width: 300px;
            width: 100%;
        }
        
        /* Card Styles */
        .leadership-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border: 1px solid var(--border);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-align: center;
            padding: 22px 15px 20px;
            cursor: pointer;
            will-change: transform;
            contain: content;
        }
        .leadership-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0,154,218,0.12);
            border-color: var(--blue);
        }
        
        /* Avatar */
        .card-avatar {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 14px;
            border: 4px solid white;
            box-shadow: 0 8px 20px rgba(0,154,218,0.2);
            display: block;
            background: #f1f5f9;
        }
        .avatar-placeholder {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(0,154,218,0.06) 0%, rgba(118,188,33,0.06) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            border: 4px solid white;
            box-shadow: 0 8px 20px rgba(0,154,218,0.15);
        }
        .avatar-placeholder i {
            font-size: 2.8rem;
            color: var(--blue);
            opacity: 0.7;
        }
        
        /* Name & Position */
        .card-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
            margin: 0 0 8px;
            line-height: 1.35;
            padding: 0 5px;
            word-break: break-word;
        }
        .card-position {
            font-size: 0.82rem;
            color: var(--blue);
            font-weight: 600;
            background: rgba(0,154,218,0.07);
            display: inline-block;
            padding: 6px 14px;
            border-radius: 30px;
            margin-top: 5px;
            max-width: 100%;
            word-break: break-word;
        }
        
        /* Badges */
        .director-badge, .executive-badge, .ceo-badge {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 30px;
            margin-bottom: 14px;
            letter-spacing: 0.3px;
        }
        .director-badge {
            background: rgba(184,134,11,0.08);
            color: #b8860b;
            border: 1px solid rgba(184,134,11,0.2);
        }
        .executive-badge {
            background: rgba(0,154,218,0.08);
            color: var(--blue);
            border: 1px solid rgba(0,154,218,0.2);
        }
        .ceo-badge {
            background: rgba(118,188,33,0.08);
            color: var(--green);
            border: 1px solid rgba(118,188,33,0.2);
        }
        
        /* Modal */
        .modal-avatar {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 1.2rem;
            border: 5px solid white;
            box-shadow: 0 15px 35px rgba(0,0,0,0.12);
        }
        .modal-avatar-placeholder {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(0,154,218,0.06) 0%, rgba(118,188,33,0.06) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.2rem;
            border: 5px solid white;
        }
        .modal-avatar-placeholder i {
            font-size: 4rem;
            color: var(--blue);
            opacity: 0.7;
        }
        .modal-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        .modal-position {
            font-size: 1rem;
            color: var(--blue);
            font-weight: 500;
            margin-bottom: 0.5rem;
            padding: 0.3rem 1rem;
            background: rgba(0,154,218,0.07);
            display: inline-block;
            border-radius: 30px;
        }
        .modal-company {
            font-size: 0.9rem;
            color: var(--gray);
            background: #f1f4f8;
            padding: 0.3rem 1rem;
            border-radius: 30px;
            display: inline-block;
            margin-top: 0.5rem;
        }
        .modal-badge {
            display: inline-block;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.3rem 1rem;
            border-radius: 30px;
            margin-bottom: 1rem;
            border: 1px solid rgba(184,134,11,0.25);
            background: rgba(184,134,11,0.08);
            color: #b8860b;
        }
        
        /* Back to top */
        .back-to-top {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 46px;
            height: 46px;
            background: white;
            color: var(--blue);
            border: 1px solid var(--border);
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 100;
        }
        .back-to-top:hover {
            background: var(--blue);
            color: white;
            border-color: var(--blue);
        }
        
        /* Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
            background: 
                radial-gradient(circle at 15% 25%, rgba(0,154,218,0.025) 0%, transparent 35%),
                radial-gradient(circle at 85% 75%, rgba(118,188,33,0.025) 0%, transparent 35%),
                linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        }
        
        /* Responsive - use container queries where supported */
        @container (max-width: 500px) {
            .leadership-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 768px) {
            .leadership-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }
            .card-avatar, .avatar-placeholder {
                width: 110px;
                height: 110px;
            }
            .card-name {
                font-size: 1rem;
            }
        }
        @media (max-width: 480px) {
            .leadership-grid {
                grid-template-columns: 1fr;
                max-width: 280px;
                margin-left: auto;
                margin-right: auto;
            }
        }
        
        /* Print styles */
        @media print {
            .navbar, .back-to-top, .bg-animation { display: none !important; }
            body { padding-top: 0; }
            .leadership-card { break-inside: avoid; box-shadow: none; border: 1px solid #ddd; }
        }
    </style>
    
    <!-- Deferred CSS loading -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
          media="print" onload="this.media='all';this.onload=null;">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
          media="print" onload="this.media='all';this.onload=null;">
    <link rel="stylesheet" href="style.css" media="print" onload="this.media='all';this.onload=null;">
    
    <noscript>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="style.css">
    </noscript>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="bg-animation" aria-hidden="true"></div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="K-CONNECT.png" alt="K-CONNECT" width="130" height="26" loading="eager" fetchpriority="high">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-label="Toggle navigation" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @php getMenu(); @endphp
                </ul>
                @if ($isLoggedIn)
                <div class="dropdown ms-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        @if (!empty($employeePhoto))
                            <img src="uploads/employees/{{ $employeePhoto }}" 
                                 width="36" height="36" class="rounded-circle me-2" 
                                 loading="lazy" alt="" aria-hidden="true">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" 
                                 style="width: 36px; height: 36px;">
                                <i class="fas fa-user text-gradient"></i>
                            </div>
                        @endif
                        <span class="d-none d-lg-inline">{{ $employeeName }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">{{ $employeeCode }}</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <h1><span class="text-gradient">Leadership Team</span></h1>
        <p class="page-description">Board of Directors & Group Leadership</p>
    </div>

    <!-- Main Content -->
    <div class="container mb-5">
        
        <!-- Board of Directors -->
        @if ($hasBoardDirectors)
        <div class="section-title">
            <h2>Board of Directors</h2>
            <div class="title-line"></div>
        </div>
        <div class="leadership-grid">
            @foreach ($boardDirectors as $director)
            <div class="leadership-card" 
                 role="button"
                 tabindex="0"
                 aria-label="View details for {!! $director['full_name_safe'] !!}"
                 data-name="{!! $director['full_name_safe'] !!}"
                 data-position="{!! $director['position_safe'] !!}"
                 data-company="{!! $director['company_name_safe'] !!}"
                 data-photo="{!! $director['photo_url'] ?? '' !!}"
                 data-type="board"
                 onclick="openModal(this)">
                <div class="director-badge">
                    <i class="fas fa-user-tie me-1"></i> Board Director
                </div>
                @if ($director['photo_url'])
                    <img src="{!! $director['photo_url'] !!}" 
                         class="card-avatar" 
                         alt="{!! $director['full_name_safe'] !!}"
                         loading="lazy"
                         width="130"
                         height="130"
                         onerror="this.parentElement.innerHTML='<div class=\'avatar-placeholder\'><i class=\'fas fa-user-tie\'></i></div>'">
                @else
                    <div class="avatar-placeholder">
                        <i class="fas fa-user-tie"></i>
                    </div>
                @endif
                <h3 class="card-name">{!! $director['full_name_safe'] !!}</h3>
                @if ($director['position_safe'])
                    <div class="card-position">{!! $director['position_safe'] !!}</div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        <!-- Executive Director -->
        @if ($hasExecutiveDirector)
        <div class="section-title">
            <h2>Executive Director</h2>
            <div class="title-line"></div>
        </div>
        <div class="single-card-grid">
            @foreach ($executiveDirector as $exec)
            <div class="leadership-card"
                 role="button"
                 tabindex="0"
                 aria-label="View details for {!! $exec['full_name_safe'] !!}"
                 data-name="{!! $exec['full_name_safe'] !!}"
                 data-position="{!! $exec['position_safe'] !!}"
                 data-company="{!! $exec['company_name_safe'] !!}"
                 data-photo="{!! $exec['photo_url'] ?? '' !!}"
                 data-type="executive"
                 onclick="openModal(this)">
                <div class="executive-badge">
                    <i class="fas fa-chart-line me-1"></i> Executive Director
                </div>
                @if ($exec['photo_url'])
                    <img src="{!! $exec['photo_url'] !!}" 
                         class="card-avatar" 
                         alt="{!! $exec['full_name_safe'] !!}"
                         loading="lazy"
                         width="130"
                         height="130"
                         onerror="this.parentElement.innerHTML='<div class=\'avatar-placeholder\'><i class=\'fas fa-user-tie\'></i></div>'">
                @else
                    <div class="avatar-placeholder">
                        <i class="fas fa-user-tie"></i>
                    </div>
                @endif
                <h3 class="card-name">{!! $exec['full_name_safe'] !!}</h3>
                @if ($exec['position_safe'])
                    <div class="card-position">{!! $exec['position_safe'] !!}</div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        <!-- Group CEO -->
        @if ($hasGroupCEO)
        <div class="section-title">
            <h2>Group CEO</h2>
            <div class="title-line"></div>
        </div>
        <div class="single-card-grid">
            @foreach ($groupCEO as $ceo)
            <div class="leadership-card"
                 role="button"
                 tabindex="0"
                 aria-label="View details for {!! $ceo['full_name_safe'] !!}"
                 data-name="{!! $ceo['full_name_safe'] !!}"
                 data-position="{!! $ceo['position_safe'] !!}"
                 data-company="{!! $ceo['company_name_safe'] !!}"
                 data-photo="{!! $ceo['photo_url'] ?? '' !!}"
                 data-type="ceo"
                 onclick="openModal(this)">
                <div class="ceo-badge">
                    <i class="fas fa-crown me-1"></i> Group CEO
                </div>
                @if ($ceo['photo_url'])
                    <img src="{!! $ceo['photo_url'] !!}" 
                         class="card-avatar" 
                         alt="{!! $ceo['full_name_safe'] !!}"
                         loading="lazy"
                         width="130"
                         height="130"
                         onerror="this.parentElement.innerHTML='<div class=\'avatar-placeholder\'><i class=\'fas fa-user-tie\'></i></div>'">
                @else
                    <div class="avatar-placeholder">
                        <i class="fas fa-user-tie"></i>
                    </div>
                @endif
                <h3 class="card-name">{!! $ceo['full_name_safe'] !!}</h3>
                @if ($ceo['position_safe'])
                    <div class="card-position">{!! $ceo['position_safe'] !!}</div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        <!-- Group Leadership -->
        @if ($hasLeadership)
        <div class="section-title">
            <h2>Group Leadership</h2>
            <div class="title-line"></div>
        </div>
        <div class="leadership-grid">
            @foreach ($leadership as $leader)
            <div class="leadership-card"
                 role="button"
                 tabindex="0"
                 aria-label="View details for {!! $leader['full_name_safe'] !!}"
                 data-name="{!! $leader['full_name_safe'] !!}"
                 data-position="{!! $leader['position_safe'] !!}"
                 data-company="{!! $leader['company_name_safe'] !!}"
                 data-photo="{!! $leader['photo_url'] ?? '' !!}"
                 data-type="leadership"
                 onclick="openModal(this)">
                @if ($leader['photo_url'])
                    <img src="{!! $leader['photo_url'] !!}" 
                         class="card-avatar" 
                         alt="{!! $leader['full_name_safe'] !!}"
                         loading="lazy"
                         width="130"
                         height="130"
                         onerror="this.parentElement.innerHTML='<div class=\'avatar-placeholder\'><i class=\'fas fa-user-tie\'></i></div>'">
                @else
                    <div class="avatar-placeholder">
                        <i class="fas fa-user-tie"></i>
                    </div>
                @endif
                <h3 class="card-name">{!! $leader['full_name_safe'] !!}</h3>
                @if ($leader['position_safe'])
                    <div class="card-position">{!! $leader['position_safe'] !!}</div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true" aria-labelledby="modalTitle">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background:var(--grad);color:white;border:none;">
                    <h5 class="modal-title" id="modalTitle">Leadership Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div id="modalAvatarContainer"></div>
                    <div id="modalBadge" class="modal-badge" style="display:none"></div>
                    <h2 id="modalName" class="modal-name"></h2>
                    <div id="modalPosition" class="modal-position"></div>
                    <div id="modalCompany" class="modal-company" style="display:none"></div>
                </div>
            </div>
        </div>
    </div>
  @include('partials.footer')
    <!-- Back to Top -->
    <button class="back-to-top" id="backToTop" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
            aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Bootstrap JS - deferred -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    
    <script>
        // ============================================
        // Optimized JavaScript - Minimal & Efficient
        // ============================================
        
        // Lazy-load Bootstrap Modal instance
        let modalInstance = null;
        function getModal() {
            if (!modalInstance) {
                const el = document.getElementById('imageModal');
                modalInstance = new bootstrap.Modal(el);
            }
            return modalInstance;
        }
        
        /**
         * Open modal with member details
         * Uses data attributes from the clicked card for instant access
         */
        function openModal(card) {
            const name = card.dataset.name;
            const position = card.dataset.position;
            const company = card.dataset.company;
            const photo = card.dataset.photo;
            const type = card.dataset.type;
            
            // Update badge
            const badge = document.getElementById('modalBadge');
            const badgeConfig = {
                'board': '<i class="fas fa-user-tie me-1"></i> Board of Directors',
                'executive': '<i class="fas fa-chart-line me-1"></i> Executive Director',
                'ceo': '<i class="fas fa-crown me-1"></i> Group CEO'
            };
            
            if (badgeConfig[type]) {
                badge.innerHTML = badgeConfig[type];
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
            
            // Update avatar
            const avatarContainer = document.getElementById('modalAvatarContainer');
            if (photo && photo !== '') {
                avatarContainer.innerHTML = '<img src="' + photo + '" class="modal-avatar" alt="' + name + '" width="180" height="180">';
            } else {
                avatarContainer.innerHTML = '<div class="modal-avatar-placeholder"><i class="fas fa-user-tie"></i></div>';
            }
            
            // Update text
            document.getElementById('modalName').textContent = name;
            document.getElementById('modalPosition').textContent = position || '';
            
            const companyEl = document.getElementById('modalCompany');
            if (company && company !== '') {
                companyEl.textContent = company;
                companyEl.style.display = 'inline-block';
            } else {
                companyEl.style.display = 'none';
            }
            
            // Show modal
            getModal().show();
        }
        
        // Keyboard accessibility for cards
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                const card = e.target.closest('.leadership-card');
                if (card) {
                    e.preventDefault();
                    openModal(card);
                }
            }
        });
        
        // Back to top - throttled scroll handler
        const backToTopBtn = document.getElementById('backToTop');
        let scrollTicking = false;
        
        window.addEventListener('scroll', function() {
            if (!scrollTicking) {
                requestAnimationFrame(function() {
                    if (window.pageYOffset > 300) {
                        backToTopBtn.style.display = 'flex';
                    } else {
                        backToTopBtn.style.display = 'none';
                    }
                    scrollTicking = false;
                });
                scrollTicking = true;
            }
        }, { passive: true });
        
        // Lazy image loading with Intersection Observer
        if ('IntersectionObserver' in window) {
            const imgObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        imgObserver.unobserve(img);
                    }
                });
            }, { rootMargin: '100px' });
            
            // Observe all avatar images
            document.querySelectorAll('.card-avatar').forEach(function(img) {
                if (img.loading === 'lazy') {
                    imgObserver.observe(img);
                }
            });
        }
    </script>
</body>
</html>
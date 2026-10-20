{{-- gallery --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - K-Connect</title>
    <link rel="icon" type="image/png" href="{{ asset('favi.png') }}">
    
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    
    <style>
        :root {
            --blue: #009ada;
            --green: #76bc21;
            --dark: #1e293b;
            --gray: #64748b;
            --border: #eef2f6;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            background: #f5f7fa;
            color: var(--dark);
            padding-top: 76px;
            min-height: 100vh;
        }
        
        .navbar {
            position: fixed; top: 0; width: 100%; z-index: 1030;
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            padding: 0.75rem 0; height: 76px;
        }
        
        .page-header {
            text-align: center; padding: 25px 20px 10px;
        }
        .page-header h1 { font-size: clamp(1.6rem, 3vw, 2.2rem); font-weight: 700; }
        .text-gradient {
            background: var(--grad);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .page-description { color: var(--gray); font-size: 0.9rem; margin-top: 3px; }
        
        .filter-bar {
            background: white;
            border-radius: 16px;
            padding: 18px 22px;
            margin: 0 20px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
            display: flex; flex-wrap: wrap; gap: 12px; align-items: center;
            position: sticky; top: 86px; z-index: 50;
        }
        .search-wrapper { position: relative; flex: 1; min-width: 180px; }
        .search-wrapper i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem; }
        .search-wrapper input {
            width: 100%; padding: 10px 14px 10px 40px;
            border: 2px solid var(--border); border-radius: 10px;
            font-size: 0.85rem; font-family: inherit;
            transition: all 0.2s; background: white;
        }
        .search-wrapper input:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(0,154,218,0.06); }
        
        .category-filters { display: flex; gap: 6px; flex-wrap: wrap; }
        .cat-btn {
            padding: 8px 14px; border-radius: 25px;
            border: 2px solid var(--border); background: white;
            color: var(--gray); font-weight: 600; font-size: 0.78rem;
            cursor: pointer; transition: all 0.2s; white-space: nowrap;
        }
        .cat-btn:hover { border-color: var(--blue); color: var(--blue); }
        .cat-btn.active { background: var(--grad); color: white; border-color: transparent; }
        .cat-count { font-size: 0.65rem; opacity: 0.8; }
        
        .container-main { max-width: 1300px; margin: 0 auto; padding: 0 20px 40px; }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 22px;
        }
        
        .gallery-card {
            background: white; border-radius: 14px; overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            border: 1px solid var(--border); transition: all 0.3s;
            display: flex; flex-direction: column;
        }
        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,154,218,0.1);
            border-color: var(--blue);
        }
        
        .card-image {
            position: relative; height: 190px; overflow: hidden; background: #f1f5f9;
        }
        .card-image img {
            width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s;
        }
        .gallery-card:hover .card-image img { transform: scale(1.04); }
        
        .card-overlay {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.5) 100%);
            opacity: 0; transition: opacity 0.3s;
            display: flex; align-items: center; justify-content: center; z-index: 2;
        }
        .gallery-card:hover .card-overlay { opacity: 1; }
        
        .btn-view {
            background: white; color: var(--dark); padding: 9px 20px;
            border-radius: 25px; font-weight: 600; font-size: 0.82rem;
            text-decoration: none; display: inline-flex; align-items: center; gap: 7px;
            transition: all 0.3s;
        }
        .btn-view:hover { background: var(--grad); color: white; transform: scale(1.04); }
        
        .source-badge {
            position: absolute; top: 10px; right: 10px;
            background: rgba(255,255,255,0.9); padding: 4px 10px;
            border-radius: 15px; font-size: 0.65rem; font-weight: 600;
            color: var(--blue); z-index: 1;
        }
        .category-tag {
            position: absolute; bottom: 10px; left: 10px;
            background: rgba(255,255,255,0.9); padding: 3px 9px;
            border-radius: 15px; font-size: 0.6rem; font-weight: 600;
            color: var(--blue); z-index: 1;
        }
        
        .card-body { padding: 14px 16px; flex: 1; display: flex; flex-direction: column; }
        .card-title { font-size: 0.95rem; font-weight: 700; color: var(--dark); margin-bottom: 4px; line-height: 1.3; }
        .card-desc {
            color: var(--gray); font-size: 0.78rem; line-height: 1.4; flex: 1;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        
        .cat-header {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 16px; padding-bottom: 8px;
            border-bottom: 2px solid var(--blue);
        }
        .cat-header h2 { font-size: 1.3rem; font-weight: 700; margin: 0; }
        .cat-header i { font-size: 1.1rem; color: var(--blue); }
        .category-section { margin-bottom: 35px; }
        
        .empty-state {
            text-align: center; padding: 50px 20px;
            background: white; border-radius: 16px; border: 1px solid var(--border);
        }
        .empty-state i { font-size: 3.5rem; color: #cbd5e1; margin-bottom: 12px; display: block; }
        
        .scroll-top {
            position: fixed; bottom: 25px; right: 25px;
            width: 42px; height: 42px; background: var(--grad); color: white;
            border: none; border-radius: 50%; cursor: pointer; font-size: 1rem;
            box-shadow: 0 4px 15px rgba(0,154,218,0.25);
            z-index: 99; display: none; align-items: center; justify-content: center;
            transition: all 0.3s;
        }
        .scroll-top.show { display: flex; }
        .scroll-top:hover { transform: translateY(-3px); }
        
        .img-error {
            display: none; align-items: center; justify-content: center;
            width: 100%; height: 100%; background: #f1f5f9; color: #94a3b8; font-size: 2.5rem;
        }
        
        @media (max-width: 768px) {
            .gallery-grid { grid-template-columns: 1fr; gap: 14px; }
            .filter-bar { flex-direction: column; padding: 14px; top: 76px; }
            .card-image { height: 170px; }
        }
    </style>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
<body>
    @include('partials.navbar')

    <section class="page-header">
        <h1><span class="text-gradient">Photo & Video</span> Gallery</h1>
        <p class="page-description">Browse through our collection of memories, events, and company moments</p>
    </section>

    <div class="filter-bar">
        <div class="search-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search galleries..." autocomplete="off">
        </div>
        <div class="category-filters" id="categoryFilters">
            <button class="cat-btn active" data-category="all">All</button>
            @foreach ($categories as $cat)
            @if ($cat['item_count'] > 0)
            <button class="cat-btn" data-category="{{ $cat['name'] }}">
                {{ $cat['name'] }}
                <span class="cat-count">({!! (int)$cat['item_count'] !!})</span>
            </button>
            @endif
            @endforeach
        </div>
    </div>

    <div class="container-main">
        <div id="galleryContainer">
            @if (empty($itemsByCategory))
            <div class="empty-state">
                <i class="fas fa-images"></i>
                <h3>No galleries available</h3>
                <p style="color:var(--gray);">Check back soon for new photo and video galleries!</p>
            </div>
            @else
                @foreach ($itemsByCategory as $categoryName => $items)
                <div class="category-section" data-category="{{ $categoryName }}">
                    <div class="cat-header">
                        <i class="fas fa-folder"></i>
                        <h2>{{ $categoryName }}</h2>
                    </div>
                    <div class="gallery-grid">
                        @foreach ($items as $item)
                        @php
                            $icon = $sourceIcons[$item['source_type']] ?? 'fa-link';
                            $title = htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8');
                            $description = htmlspecialchars($item['description'] ?? '', ENT_QUOTES, 'UTF-8');
                            $thumbnail = kc_asset($item['thumbnail']);
                            // stored through sanitize() already: don't escape the & of a link twice
                            $externalUrl = htmlspecialchars($item['external_url'], ENT_QUOTES, 'UTF-8', false);
                            $sourceType = ucfirst($item['source_type']);
                            $itemCategory = htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8');
                            $searchData = strtolower($title . ' ' . $description);
                         @endphp
                        <div class="gallery-item" data-category="{!! $itemCategory !!}" data-search="{!! $searchData !!}">
                            <div class="gallery-card">
                                <div class="card-image">
                                    <img src="{{ $thumbnail }}" 
                                         alt="{!! $title !!}" loading="lazy"
                                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <div class="img-error"><i class="fas fa-image"></i></div>
                                    <div class="card-overlay">
                                        <a href="{!! $externalUrl !!}" target="_blank" rel="noopener noreferrer" class="btn-view">
                                            <i class="fas fa-external-link-alt"></i> View Gallery
                                        </a>
                                    </div>
                                    <span class="source-badge"><i class="fab {!! $icon !!} me-1"></i>{!! $sourceType !!}</span>
                                    <span class="category-tag"><i class="fas fa-folder me-1"></i>{!! $itemCategory !!}</span>
                                </div>
                                <div class="card-body">
                                    <h3 class="card-title">{!! $title !!}</h3>
                                    @if ($item['description'])
                                    <p class="card-desc">{!! $description !!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            @endif
        </div>
        <div class="empty-state" id="noResults" style="display:none;">
            <i class="fas fa-search"></i>
            <h3>No matching galleries</h3>
            <p style="color:var(--gray);">Try a different search term or category.</p>
        </div>
    </div>
  @include('partials.footer')
    <button class="scroll-top" id="scrollTop" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <script>
    (function() {
        const scrollTopBtn = document.getElementById('scrollTop');
        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    scrollTopBtn.classList.toggle('show', window.pageYOffset > 400);
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });

        const searchInput = document.getElementById('searchInput');
        const categoryBtns = document.querySelectorAll('.cat-btn');
        const galleryItems = document.querySelectorAll('.gallery-item');
        const categorySections = document.querySelectorAll('.category-section');
        const noResults = document.getElementById('noResults');
        let activeCategory = 'all';
        let searchTimeout;

        function filterGallery() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;
            galleryItems.forEach(function(item) {
                const category = item.dataset.category;
                const searchData = item.dataset.search || '';
                const match = (activeCategory === 'all' || category === activeCategory) && 
                              (searchTerm === '' || searchData.includes(searchTerm));
                item.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });
            categorySections.forEach(function(section) {
                const cat = section.dataset.category;
                const hasVisible = section.querySelectorAll('.gallery-item:not([style*="display: none"])').length > 0;
                section.style.display = (activeCategory === 'all' || activeCategory === cat) && hasVisible ? '' : 'none';
            });
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(filterGallery, 150);
        });

        document.getElementById('categoryFilters').addEventListener('click', function(e) {
            const btn = e.target.closest('.cat-btn');
            if (!btn) return;
            categoryBtns.forEach(b => { b.classList.remove('active'); b.setAttribute('aria-pressed', 'false'); });
            btn.classList.add('active');
            btn.setAttribute('aria-pressed', 'true');
            activeCategory = btn.dataset.category;
            filterGallery();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.activeElement === searchInput) {
                searchInput.value = '';
                filterGallery();
            }
        });
    })();
    </script>
</body>
</html>
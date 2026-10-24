{{-- brand-assets --}}
<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Assets - {{ SITE_NAME }}</title>
      <link rel="icon" type="image/png" href="{{ asset('favi.png') }}">
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('style.css') }}" media="print" onload="this.media='all'">
    
    <style>
        :root {
            --blue: #009ada;
            --green: #76bc21;
            --grad: linear-gradient(135deg, #009ada, #76bc21);
            --bg: #f8fafc;
            --text: #1e293b;
            --text-light: #64748b;
            --text-muted: #94a3b8;
            --border: #eef2f6;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

      body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    background: 
        radial-gradient(ellipse at 0% 0%, rgba(0, 154, 218, 0.06) 0%, transparent 50%),
        radial-gradient(ellipse at 100% 100%, rgba(118, 188, 33, 0.05) 0%, transparent 50%),
        radial-gradient(ellipse at 50% 50%, rgba(0, 154, 218, 0.02) 0%, transparent 70%),
        linear-gradient(180deg, #f8fafc 0%, #f1f5f9 30%, #f8fafc 60%, #f0f4f8 100%);
    color: var(--text);
    -webkit-font-smoothing: antialiased;
    min-height: 100vh;
        }

        /* Navigation
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 1rem 0;
        }

        .navbar-brand img {
            height: 25px;
        } */

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 40px 0;
            border-bottom: 1px solid #eef2f6;
            margin-bottom: 30px;
        }

        .avatar-circle {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: #eef2f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blue);
            font-size: 0.9rem;
        }
        .avatar-circle img {
            width: 34px; height: 34px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Page Header */
        .page-header-section {
            padding: 2.5rem 1rem 1.8rem;
            text-align: center;
            border-bottom: 1px solid var(--border);
            margin-bottom: 1.8rem;
            background: linear-gradient(180deg, #ffffff 0%, var(--bg) 100%);
        }
        .page-header-section h1 {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            margin-bottom: 0.3rem;
        }
        .text-gradient {
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .page-header-section .subtitle {
            color: var(--text-light);
            font-size: 0.88rem;
        }

        /* Toolbar */
        .toolbar-container {
            max-width: 1100px;
            margin: 0 auto 1.8rem;
            padding: 0 1rem;
        }
        .toolbar {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            background: #fff;
            padding: 0.9rem 1.2rem;
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 4px rgba(0,0,0,0.02);
        }
        .search-box {
            position: relative;
            flex: 1;
            min-width: 200px;
        }
        .search-box input {
            width: 100%;
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            border: 2px solid var(--border);
            border-radius: 40px;
            font-size: 0.85rem;
            background: #f8fafc;
            transition: all 0.25s;
        }
        .search-box input:focus {
            outline: none;
            border-color: var(--blue);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(0,154,218,0.05);
        }
        .search-box .s-icon {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            font-size: 0.85rem;
        }
        .search-box .s-clear {
            position: absolute;
            right: 0.6rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            display: none;
            padding: 0.25rem 0.4rem;
            border-radius: 50%;
            font-size: 0.7rem;
        }
        .stats-pills {
            display: flex;
            gap: 0.5rem;
        }
        .stat-pill {
            font-size: 0.76rem;
            font-weight: 600;
            color: var(--text-light);
            border: 1px solid var(--border);
            border-radius: 30px;
            padding: 0.35rem 0.85rem;
            white-space: nowrap;
            background: #f8fafc;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        .stat-pill i { color: var(--blue); font-size: 0.7rem; }

        /* Main Container - Single Column */
        .main-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 1rem 4rem;
        }

        /* Company Cards Stack */
        .companies-stack {
            display: flex;
            flex-direction: column;
            gap: 1.8rem;
        }

        .company-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--border);
            overflow: hidden;
            transition: box-shadow 0.25s;
            box-shadow: 0 1px 6px rgba(0,0,0,0.02);
        }
        .company-card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.05);
        }

        /* Card Header - properly aligned */
        .cc-header {
            padding: 1.3rem 1.75rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(135deg, #fafbfc, #fff);
        }
        .cc-icon {
            width: 42px; height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.15rem;
            flex-shrink: 0;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .cc-title-group {
            flex: 1;
            min-width: 0;
        }
        .cc-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.3;
        }
        .cc-subtitle {
            font-size: 0.73rem;
            color: var(--text-muted);
            margin-top: 0.15rem;
        }
        .cc-badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            background: #f1f5f9;
            color: var(--text-light);
            white-space: nowrap;
            margin-left: auto;
        }

        /* Card Body - 3 Column Grid */
        .cc-body {
            padding: 1.25rem 1.5rem 1.5rem;
        }
        .assets-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.7rem;
        }

        /* Compact Asset Row */
        .asset-row {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.6rem 0.7rem;
            border-radius: 10px;
            background: #f9fafb;
            border: 1px solid transparent;
            transition: all 0.18s ease;
            cursor: pointer;
            min-width: 0;
        }
        .asset-row:hover {
            background: #fff;
            border-color: #dfe6ef;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transform: translateY(-1px);
        }

        .asset-type-dot {
            width: 32px; height: 32px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .asset-info {
            flex: 1;
            min-width: 0;
        }
        .asset-info .a-title {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
        }
        .asset-info .a-meta {
            font-size: 0.66rem;
            color: var(--text-muted);
            display: flex;
            gap: 0.5rem;
            margin-top: 0.12rem;
        }
        .a-meta span {
            display: flex;
            align-items: center;
            gap: 0.18rem;
        }

        .asset-actions-inline {
            display: flex;
            gap: 0.25rem;
            flex-shrink: 0;
        }
        .btn-dot {
            width: 28px; height: 28px;
            border-radius: 7px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: var(--text-light);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            transition: all 0.18s;
            text-decoration: none;
        }
        .btn-dot:hover {
            background: var(--grad);
            color: #fff;
            border-color: transparent;
        }

        .file-tag {
            font-size: 0.56rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 0.14rem 0.42rem;
            border-radius: 4px;
            letter-spacing: 0.5px;
            flex-shrink: 0;
        }
        .tag-pdf { background: #fef2f2; color: #dc2626; }
        .tag-img { background: #f5f3ff; color: #7c3aed; }
        .tag-doc { background: #eff6ff; color: #2563eb; }
        .tag-ppt { background: #fff7ed; color: #ea580c; }
        .tag-xls { background: #f0fdf4; color: #16a34a; }
        .tag-vid { background: #f0fdfa; color: #0d9488; }
        .tag-arc { background: #fffdf0; color: #ca8a04; }
        .tag-oth { background: #f1f5f9; color: #64748b; }

        /* Empty States */
        .empty-state {
            text-align: center;
            padding: 3.5rem 2rem;
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--border);
        }
        .empty-state i {
            font-size: 2.5rem;
            color: #cbd5e1;
            margin-bottom: 0.8rem;
            display: block;
        }
        .empty-state h3 {
            color: var(--text);
            font-weight: 700;
            margin-bottom: 0.3rem;
            font-size: 1.1rem;
        }
        .empty-state p { color: var(--text-muted); font-size: 0.85rem; }

        .no-results { display: none; }
        .no-results.show { display: block; }

        /* Preview Modal */
        .preview-modal .modal-content { border: none; border-radius: 20px; overflow: hidden; }
        .preview-modal .modal-header { 
            border-bottom: 1px solid var(--border); 
            padding: 1rem 1.5rem;
            background: #fafbfc;
        }
        .preview-modal .modal-body { 
            padding: 0; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            min-height: 180px;
            max-height: 65vh;
            overflow: auto;
        }
        .preview-modal .modal-body img { max-width: 100%; max-height: 60vh; object-fit: contain; }
        .preview-modal .modal-body iframe { width: 100%; height: 60vh; border: none; }

        .btn-grad-sm {
            background: var(--grad);
            color: #fff;
            border: none;
            padding: 0.45rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: all 0.2s;
        }
        .btn-grad-sm:hover { box-shadow: 0 4px 14px rgba(0,154,218,0.3); color: #fff; }

        /* Back to top */
        .btt {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            width: 38px; height: 38px;
            background: var(--grad);
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: none;
            box-shadow: 0 4px 14px rgba(0,154,218,0.25);
            z-index: 99;
            font-size: 0.85rem;
        }
        .btt.show { display: flex; align-items: center; justify-content: center; }

        /* Responsive */
        @media (max-width: 992px) {
            .assets-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .page-header-section h1 { font-size: 1.7rem; }
            .cc-header { padding: 1.1rem 1.25rem; }
            .cc-body { padding: 1rem; }
            .asset-row { padding: 0.55rem 0.6rem; }
        }
        @media (max-width: 480px) {
            .assets-grid { grid-template-columns: 1fr 1fr; gap: 0.5rem; }
            .asset-row { padding: 0.5rem; gap: 0.45rem; }
            .asset-type-dot { width: 28px; height: 28px; font-size: 0.75rem; }
            .asset-info .a-title { font-size: 0.73rem; }
            .btn-dot { width: 26px; height: 26px; font-size: 0.65rem; }
            .file-tag { font-size: 0.52rem; }
            .toolbar { flex-direction: column; align-items: stretch; }
            .search-box { min-width: 0; }
            .stats-pills { justify-content: center; }
            .cc-header { gap: 0.75rem; }
            .cc-icon { width: 36px; height: 36px; font-size: 1rem; }
        }
    </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('K-CONNECT.png') }}" alt="K-CONNECT">
        </a>
        
        <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                @php getMenu(); @endphp

                @if (isLoggedIn())
                <div class="dropdown ms-3">
                    <a class="nav-link  d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        @if (!empty(session('employee_photo')))
                            <img src="{{ asset('uploads') }}/employees/{{ session('employee_photo') }}" 
                                 width="36" height="36" class="rounded-circle me-2" style="object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" 
                                 style="width: 36px; height: 36px;">
                                <i class="fas fa-user text-gradient"></i>
                            </div>
                        @endif
                        <span class="d-none d-lg-inline">{{ session('employee_name') }}</span>
                    </a>
                     <ul class="dropdown-menu dropdown-menu-end">
                       <li><h6 class="dropdown-header">ID: {!! session('employee_code') !!}</h6></li>
                         <li><a class="dropdown-item" href="{{ url('my-assets') }}"><i class="fas fa-laptop me-2"></i>My Assets</a></li>
                         <li><a class="dropdown-item" href="{{ url('academy') }}"><i class="fas fa-graduation-cap me-2"></i>My Learning (K-Academy)</a></li>
                        <li><a class="dropdown-item" href="{{ url('change-password') }}"><i class="fas fa-key me-2"></i>Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="{{ url('logout') }}"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
    <!-- Header -->
    <section class="page-header-section">
        <h1><span class="text-gradient">Brand Assets</span></h1>
        <p class="subtitle">Official brand materials &mdash; organized by company</p>
    </section>

    <!-- Toolbar -->
    <div class="toolbar-container">
        <div class="toolbar">
            <div class="search-box">
                <i class="fas fa-search s-icon"></i>
                <input type="text" id="searchInput" placeholder="Search assets by name, type or company..." autocomplete="off">
                <button class="s-clear" id="clearSearch" aria-label="Clear search"><i class="fas fa-times"></i></button>
            </div>
            <div class="stats-pills">
                <span class="stat-pill"><i class="fas fa-file-alt"></i> {!! $totalAssets !!} files</span>
                <span class="stat-pill"><i class="fas fa-arrow-down"></i> {!! number_format($totalDownloads) !!}</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-container">
        <div class="companies-stack" id="companiesStack">
            @php $hasAny = false; @endphp
            @foreach ($companies as $ckey => $company)
            @continue (!isset($companyAssets[$ckey]) || empty($companyAssets[$ckey]))
            @php
                $hasAny = true;
                $assetCount = count($companyAssets[$ckey]);
            @endphp
            <div class="company-card" data-company="{!! $ckey !!}">
                <!-- Card Header -->
                <div class="cc-header">
                    <div class="cc-icon" style="background:{{ $company['color'] }}">
                
                    </div>
                    <div class="cc-title-group">
                        <div class="cc-title">{{ $company['name'] }}</div>
                   
                    </div>
                    <span class="cc-badge">{!! $assetCount !!} {!! $assetCount === 1 ? 'file' : 'files' !!}</span>
                </div>
                
                <!-- Card Body - 3 Column Grid with Compact Rows -->
                <div class="cc-body">
                    <div class="assets-grid">
                        @foreach ($companyAssets[$ckey] as $asset)
                        @php
                            $ext = strtolower(pathinfo($asset['file_path'], PATHINFO_EXTENSION));
                            
                            $typeMap = [
                                'pdf'  => ['fa-file-pdf', '#dc3545', 'tag-pdf'],
                                'jpg'  => ['fa-image', '#7c3aed', 'tag-img'],
                                'jpeg' => ['fa-image', '#7c3aed', 'tag-img'],
                                'png'  => ['fa-image', '#7c3aed', 'tag-img'],
                                'gif'  => ['fa-image', '#7c3aed', 'tag-img'],
                                'svg'  => ['fa-image', '#7c3aed', 'tag-img'],
                                'webp' => ['fa-image', '#7c3aed', 'tag-img'],
                                'doc'  => ['fa-file-word', '#2563eb', 'tag-doc'],
                                'docx' => ['fa-file-word', '#2563eb', 'tag-doc'],
                                'ppt'  => ['fa-file-powerpoint', '#ea580c', 'tag-ppt'],
                                'pptx' => ['fa-file-powerpoint', '#ea580c', 'tag-ppt'],
                                'xls'  => ['fa-file-excel', '#16a34a', 'tag-xls'],
                                'xlsx' => ['fa-file-excel', '#16a34a', 'tag-xls'],
                                'mp4'  => ['fa-video', '#0d9488', 'tag-vid'],
                                'zip'  => ['fa-file-archive', '#ca8a04', 'tag-arc'],
                                'rar'  => ['fa-file-archive', '#ca8a04', 'tag-arc'],
                            ];
                            
                            $tIcon = $typeMap[$ext][0] ?? 'fa-file';
                            $tColor = $typeMap[$ext][1] ?? '#64748b';
                            $tTag = $typeMap[$ext][2] ?? 'tag-oth';
                            
                            $fsize = $asset['file_size'] ?? 0;
                            $fileSize = $fsize > 1048576 ? round($fsize/1048576, 1).' MB' : ($fsize > 0 ? round($fsize/1024).' KB' : '--');
                         @endphp
                        <div class="asset-row" 
                             data-title="{!! strtolower(htmlspecialchars($asset['title'])) !!}"
                             data-desc="{!! strtolower(htmlspecialchars($asset['description'] ?? '')) !!}"
                             data-type="{!! $ext !!}"
                             data-company="{!! $ckey !!}"
                             onclick="previewAsset({!! $asset['id'] !!})">
                            
                            <div class="asset-type-dot" style="background:{!! $tColor !!}">
                                <i class="fas {!! $tIcon !!}"></i>
                            </div>
                            
                            <div class="asset-info">
                                <div class="a-title" title="{{ $asset['title'] }}">
                                    {{ $asset['title'] }}
                                </div>
                                <div class="a-meta">
                                    <span><i class="far fa-file"></i>{!! $fileSize !!}</span>
                                    <span><i class="fas fa-download"></i>{!! $asset['download_count'] !!}</span>
                                </div>
                            </div>
                            
                            <span class="file-tag {!! $tTag !!}">{!! strtoupper($ext) !!}</span>
                            
                            <div class="asset-actions-inline">
                                <button class="btn-dot" onclick="event.stopPropagation(); previewAsset({!! $asset['id'] !!})" title="Preview">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="?download={!! $asset['id'] !!}" class="btn-dot" title="Download" onclick="event.stopPropagation()">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
            
            @if (!$hasAny)
            <div class="empty-state" id="emptyState">
                <i class="fas fa-folder-open"></i>
                <h3>No brand assets yet</h3>
                <p>Assets will appear here once uploaded by the admin.</p>
            </div>
            @endif
            
            <div class="empty-state no-results" id="noResults">
                <i class="fas fa-search"></i>
                <h3>No matching assets</h3>
                <p>Try adjusting your search terms.</p>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade preview-modal" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewTitle">
                        <i class="fas fa-eye text-gradient me-2"></i>Preview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="previewBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted small">Loading preview...</p>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <span id="previewMeta" class="text-muted small me-auto"></span>
                    <a href="#" id="previewDlBtn" class="btn-grad-sm">
                        <i class="fas fa-download me-1"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>
  @include('partials.footer')
    <!-- Back to Top -->
    <button class="btt" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Back to top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    
    <script>
    (function() {
        const searchInput = document.getElementById('searchInput');
        const clearSearch = document.getElementById('clearSearch');
        const assetRows = document.querySelectorAll('.asset-row');
        const companyCards = document.querySelectorAll('.company-card');
        const noResults = document.getElementById('noResults');
        const emptyState = document.getElementById('emptyState');
        const btt = document.querySelector('.btt');
        let searchTimeout;

        function filterAssets() {
            const term = searchInput.value.toLowerCase().trim();
            let totalVisible = 0;
            let visibleCards = 0;

            companyCards.forEach(card => {
                const rows = card.querySelectorAll('.asset-row');
                let cardVisible = 0;

                rows.forEach(row => {
                    const title = row.dataset.title || '';
                    const desc = row.dataset.desc || '';
                    const type = row.dataset.type || '';
                    
                    const match = term === '' || 
                        title.includes(term) || 
                        desc.includes(term) || 
                        type.includes(term);

                    row.style.display = match ? '' : 'none';
                    if (match) { cardVisible++; totalVisible++; }
                });

                card.style.display = cardVisible > 0 ? '' : 'none';
                if (cardVisible > 0) visibleCards++;

                const badge = card.querySelector('.cc-badge');
                if (badge) badge.textContent = cardVisible + (cardVisible === 1 ? ' file' : ' files');
            });

            noResults.classList.toggle('show', totalVisible === 0 && term !== '');
            if (emptyState) emptyState.style.display = visibleCards === 0 && term === '' ? '' : 'none';
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(filterAssets, 150);
            clearSearch.style.display = this.value ? 'inline-flex' : 'none';
        });

        clearSearch.addEventListener('click', function() {
            searchInput.value = '';
            filterAssets();
            this.style.display = 'none';
            searchInput.focus();
        });

        window.previewAsset = function(id) {
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            const body = document.getElementById('previewBody');
            const titleEl = document.getElementById('previewTitle');
            const meta = document.getElementById('previewMeta');
            const dlBtn = document.getElementById('previewDlBtn');

            body.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="mt-2 text-muted small">Loading...</p></div>';
            modal.show();

            fetch('get-asset?id=' + id)
                .then(r => r.json())
                .then(data => {
                    if (!data.success) throw new Error('Failed');
                    
                    titleEl.innerHTML = '<i class="fas fa-eye text-gradient me-2"></i>' + data.title;
                    meta.textContent = data.file_size + ' ¡¤ ' + data.download_count + ' downloads';
                    dlBtn.href = '?download=' + id;

                    const ext = data.file_type.toLowerCase();
                    let html;
                    
                    if (['jpg','jpeg','png','gif','svg','webp'].includes(ext)) {
                        html = '<div class="text-center p-3"><img src="' + data.file_path + '" alt="' + data.title + '" style="max-width:100%;max-height:55vh;object-fit:contain;"></div>';
                    } else if (ext === 'pdf') {
                        html = '<iframe src="' + data.file_path + '" style="width:100%;height:55vh;border:0;"></iframe>';
                    } else {
                        html = '<div class="text-center py-5"><i class="fas fa-file fa-4x text-muted mb-3"></i><p class="text-muted">Preview not available</p></div>';
                    }
                    
                    body.innerHTML = html;
                })
                .catch(() => {
                    body.innerHTML = '<div class="text-center py-5"><i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i><h5 class="text-danger">Preview failed</h5></div>';
                });
        };

        window.addEventListener('scroll', () => {
            btt.classList.toggle('show', window.pageYOffset > 400);
        }, { passive: true });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && document.activeElement === searchInput) {
                searchInput.value = '';
                filterAssets();
                clearSearch.style.display = 'none';
            }
        });

        filterAssets();
    })();
    </script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
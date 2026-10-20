{{-- Organization chart, staff view (orgchart_view). Data comes from App\Support\OrgChart::nodes(), embedded in the page. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Chart - K-Connect</title>
    <link rel="icon" type="image/png" href="{{ asset('favi.png') }}">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        :root {
            --blue: #009ada;
            --green: #76bc21;
            --dark: #1e293b;
            --gray: #64748b;
            --muted: #94a3b8;
            --border: #e6ebf1;
            --line: #a3b1c2;
            --bg: #f4f7fb;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--dark);
            padding-top: 76px;
            min-height: 100vh;
        }
        .navbar {
            position: fixed; top: 0; width: 100%; z-index: 1030;
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(12px);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            padding: 0.75rem 0; height: 76px;
        }
        .text-gradient {
            background: var(--grad);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ---------- Page header & toolbar ---------- */
        .oc-page { padding: 18px 20px 16px; max-width: 100%; }
        .oc-head { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
        .oc-head h1 { font-size: 1.6rem; font-weight: 800; margin: 0; letter-spacing: -0.02em; }
        .oc-head p { color: var(--gray); margin: 2px 0 0; font-size: 0.9rem; }
        .oc-stats { display: flex; gap: 8px; flex-wrap: wrap; }
        .oc-stat { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 6px 14px; min-width: 92px; }
        .oc-stat b { display: block; font-size: 1.15rem; font-weight: 800; color: var(--blue); line-height: 1.2; }
        .oc-stat span { font-size: 0.72rem; color: var(--gray); font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }

        .oc-toolbar {
            display: flex; flex-wrap: wrap; gap: 8px; align-items: center;
            background: #fff; border: 1px solid var(--border); border-radius: 14px;
            padding: 8px; margin-bottom: 10px;
        }
        .oc-search { position: relative; flex: 1 1 240px; min-width: 200px; }
        .oc-search i.fa-search { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: .85rem; }
        .oc-search input { padding-left: 34px; }
        .oc-toolbar .form-control, .oc-toolbar .form-select {
            border: 1.5px solid var(--border); border-radius: 10px; font-size: .88rem; height: 38px;
        }
        .oc-toolbar .form-control:focus, .oc-toolbar .form-select:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(0,154,218,.12); }
        .oc-toolbar .form-select { flex: 0 1 190px; }
        .oc-btn {
            height: 38px; min-width: 38px; padding: 0 12px; border-radius: 10px;
            border: 1.5px solid var(--border); background: #fff; color: var(--dark);
            font-size: .85rem; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            transition: all .15s;
        }
        .oc-btn:hover { border-color: var(--blue); color: var(--blue); }
        .oc-btn.active { background: var(--blue); border-color: var(--blue); color: #fff; }
        .oc-seg { display: inline-flex; border: 1.5px solid var(--border); border-radius: 10px; overflow: hidden; }
        .oc-seg .oc-btn { border: 0; border-radius: 0; }
        .oc-sep { width: 1px; height: 26px; background: var(--border); margin: 0 2px; }

        .oc-suggest {
            position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 50;
            background: #fff; border: 1px solid var(--border); border-radius: 12px;
            box-shadow: 0 12px 30px rgba(15,23,42,.12); max-height: 340px; overflow: auto; display: none;
        }
        .oc-suggest.show { display: block; }
        .oc-suggest button {
            display: flex; align-items: center; gap: 10px; width: 100%; border: 0; background: none;
            padding: 8px 12px; text-align: left; font-size: .85rem;
        }
        .oc-suggest button:hover, .oc-suggest button.kb { background: #f1f7fc; }
        .oc-suggest small { color: var(--gray); display: block; font-size: .75rem; }

        .oc-legend { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; margin-bottom: 10px; font-size: .78rem; }
        .oc-legend .lbl { color: var(--gray); font-weight: 600; margin-right: 2px; }
        .oc-lv {
            display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px 3px 6px; border-radius: 20px;
            background: #fff; border: 1px solid var(--border); cursor: pointer; font-weight: 600; color: var(--dark);
        }
        .oc-lv i { width: 10px; height: 10px; border-radius: 50%; background: var(--c); display: inline-block; }
        .oc-lv em { font-style: normal; color: var(--gray); font-weight: 500; }
        .oc-lv.on { background: var(--c); color: #fff; border-color: var(--c); }
        .oc-lv.on i { background: #fff; }
        .oc-lv.on em { color: rgba(255,255,255,.85); }
        .oc-filter-note { color: var(--blue); font-weight: 600; margin-left: auto; }
        .oc-filter-note a { color: var(--gray); margin-left: 6px; }

        .oc-crumbs { display: none; align-items: center; flex-wrap: wrap; gap: 4px; font-size: .82rem; margin-bottom: 8px; }
        .oc-crumbs.show { display: flex; }
        .oc-crumbs a { color: var(--blue); text-decoration: none; font-weight: 600; cursor: pointer; }
        .oc-crumbs .sep { color: var(--muted); }

        /* ---------- Chart viewport ---------- */
        .oc-viewport {
            position: relative; overflow: hidden; background: #fff;
            border: 1px solid var(--border); border-radius: 16px;
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 22px 22px;
            touch-action: none; cursor: grab; user-select: none;
            height: 600px;
        }
        .oc-viewport.dragging { cursor: grabbing; }
        .oc-stage { position: absolute; left: 0; top: 0; transform-origin: 0 0; }
        .oc-stage svg { position: absolute; left: 0; top: 0; overflow: visible; pointer-events: none; }
        .oc-edge { fill: none; stroke: var(--line); stroke-width: 1.6; }
        .oc-edge.hl { stroke: var(--blue); stroke-width: 2.4; }
        .oc-edge.dim { opacity: .25; }

        .oc-zoom {
            position: absolute; right: 12px; bottom: 12px; z-index: 5;
            display: flex; flex-direction: column; gap: 4px;
            background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 4px;
            box-shadow: 0 4px 14px rgba(15,23,42,.08);
        }
        .oc-zoom button { width: 34px; height: 34px; border: 0; background: none; border-radius: 8px; color: var(--dark); }
        .oc-zoom button:hover { background: #eef6fb; color: var(--blue); }
        .oc-zoom span { font-size: .68rem; text-align: center; color: var(--gray); font-weight: 600; }
        .oc-hint {
            position: absolute; left: 12px; top: 12px; z-index: 5; font-size: .72rem; color: var(--gray);
            background: rgba(255,255,255,.92); border: 1px solid var(--border); border-radius: 10px; padding: 5px 10px;
        }

        /* ---------- Person card ---------- */
        .oc-card {
            position: absolute; width: 216px; height: 104px;
            background: #fff; border: 1.5px solid var(--border); border-radius: 14px;
            box-shadow: 0 2px 6px rgba(15,23,42,.05);
            cursor: pointer; transition: box-shadow .15s, border-color .15s, opacity .2s, transform .15s;
            animation: ocIn .25s ease;
        }
        @keyframes ocIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: none; } }
        .oc-card::before {
            content: ''; position: absolute; left: -1.5px; right: -1.5px; top: -1.5px; height: 5px;
            background: var(--c); border-radius: 14px 14px 0 0;
        }
        .oc-card:hover { border-color: var(--c); box-shadow: 0 10px 24px rgba(15,23,42,.12); z-index: 3; }
        .oc-card.sel { border-color: var(--blue); box-shadow: 0 0 0 4px rgba(0,154,218,.18), 0 10px 24px rgba(15,23,42,.12); z-index: 4; }
        .oc-card.dim { opacity: .28; }
        .oc-card.pulse { animation: ocPulse 1.4s ease 2; }
        @keyframes ocPulse { 0%,100% { box-shadow: 0 0 0 0 rgba(0,154,218,.0); } 50% { box-shadow: 0 0 0 10px rgba(0,154,218,.25); } }
        .oc-card.top { border-width: 2px; }
        .oc-body { display: flex; gap: 10px; padding: 13px 12px 0; align-items: flex-start; }
        .oc-av {
            width: 44px; height: 44px; border-radius: 50%; flex: 0 0 44px; overflow: hidden;
            display: flex; align-items: center; justify-content: center;
            background: color-mix(in srgb, var(--c) 14%, #fff); color: var(--c);
            font-weight: 800; font-size: .9rem; border: 2px solid var(--c);
        }
        .oc-av img { width: 100%; height: 100%; object-fit: cover; }
        .oc-txt { min-width: 0; flex: 1; }
        .oc-name { font-weight: 700; font-size: .84rem; line-height: 1.2; color: var(--dark);
                   overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        .oc-pos { font-size: .74rem; color: var(--blue); font-weight: 600; margin-top: 2px;
                  white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .oc-meta { font-size: .68rem; color: var(--gray); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .oc-foot { position: absolute; left: 12px; right: 12px; bottom: 8px; display: flex; align-items: center; justify-content: space-between; gap: 6px; }
        .oc-chip {
            font-size: .62rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;
            color: var(--c); background: color-mix(in srgb, var(--c) 12%, #fff);
            padding: 2px 8px; border-radius: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 120px;
        }
        .oc-team { font-size: .68rem; color: var(--gray); font-weight: 600; white-space: nowrap; }
        .oc-toggle {
            position: absolute; left: 50%; bottom: -13px; transform: translateX(-50%); z-index: 2;
            min-width: 26px; height: 26px; padding: 0 7px; border-radius: 13px;
            border: 1.5px solid var(--line); background: #fff; color: var(--dark);
            font-size: .68rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; gap: 4px;
        }
        .oc-toggle:hover { border-color: var(--blue); color: var(--blue); }
        .oc-toggle.closed { background: var(--blue); border-color: var(--blue); color: #fff; }

        /* ---------- List view ---------- */
        .oc-list { background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 10px 8px; display: none; }
        .oc-list.show { display: block; }
        .oc-list ul { list-style: none; margin: 0; padding: 0; }
        .oc-list ul ul { margin-left: 21px; padding-left: 14px; border-left: 2px solid #e5ebf2; }
        .oc-row {
            display: flex; align-items: center; gap: 10px; padding: 7px 8px; border-radius: 10px; cursor: pointer; position: relative;
        }
        .oc-list ul ul > li > .oc-row::before {
            content: ''; position: absolute; left: -14px; top: 50%; width: 12px; border-top: 2px solid #e5ebf2;
        }
        .oc-row:hover { background: #f4f9fd; }
        .oc-row.sel { background: #e8f5fc; }
        .oc-row .oc-av { width: 38px; height: 38px; flex-basis: 38px; font-size: .8rem; }
        .oc-row .chev { width: 24px; height: 24px; border: 0; background: none; color: var(--gray); border-radius: 6px; flex: 0 0 24px; }
        .oc-row .chev:hover { background: #e8eef4; color: var(--blue); }
        .oc-row .chev.none { visibility: hidden; }
        .oc-row .oc-txt { min-width: 0; }
        .oc-row .oc-name { -webkit-line-clamp: 1; }
        .oc-row .oc-chip { margin-left: auto; flex-shrink: 0; }
        .oc-row .oc-team { flex-shrink: 0; min-width: 40px; text-align: right; }
        .oc-row.hit .oc-name { color: var(--blue); }

        .oc-empty { text-align: center; padding: 70px 20px; color: var(--gray); }
        .oc-empty i { font-size: 2.6rem; color: #cbd5e1; margin-bottom: 10px; }

        /* ---------- Details panel ---------- */
        .offcanvas.oc-detail { width: 380px; border-left: 0; box-shadow: -10px 0 40px rgba(15,23,42,.12); }
        .oc-d-head { background: var(--grad); color: #fff; padding: 22px 20px 54px; position: relative; }
        .oc-d-head .btn-close { position: absolute; right: 14px; top: 14px; }
        .oc-d-photo {
            width: 96px; height: 96px; border-radius: 50%; border: 4px solid #fff; margin: -48px auto 8px;
            background: #fff; overflow: hidden; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 22px rgba(15,23,42,.15); font-size: 1.8rem; font-weight: 800; position: relative;
        }
        .oc-d-photo img { width: 100%; height: 100%; object-fit: cover; }
        .oc-d-sec { padding: 0 20px 16px; }
        .oc-d-sec h6 { font-size: .7rem; text-transform: uppercase; letter-spacing: .06em; color: var(--gray); font-weight: 700; margin: 14px 0 8px; }
        .oc-d-info { background: #f8fafc; border-radius: 12px; padding: 4px 12px; }
        .oc-d-info div { display: flex; gap: 10px; padding: 7px 0; font-size: .85rem; border-bottom: 1px solid #eef2f6; }
        .oc-d-info div:last-child { border-bottom: 0; }
        .oc-d-info i { color: var(--blue); width: 16px; margin-top: 3px; }
        .oc-d-info span { color: var(--gray); min-width: 84px; }
        .oc-d-info b { font-weight: 600; word-break: break-word; }
        .oc-d-person { display: flex; align-items: center; gap: 10px; padding: 7px 8px; border-radius: 10px; cursor: pointer; }
        .oc-d-person:hover { background: #f1f7fc; }
        .oc-d-person .oc-av { width: 34px; height: 34px; flex-basis: 34px; font-size: .72rem; }
        .oc-d-person .oc-name { font-size: .82rem; -webkit-line-clamp: 1; }
        .oc-d-actions { display: flex; gap: 8px; padding: 4px 20px 20px; }
        .oc-d-actions .oc-btn { flex: 1; }

        .admin-fab {
            position: fixed; bottom: 20px; left: 20px; z-index: 100;
            background: var(--grad); color: #fff; border: none; padding: 10px 16px; border-radius: 25px;
            font-size: .8rem; font-weight: 600; box-shadow: 0 5px 15px rgba(0,154,218,.25);
            text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        }
        .admin-fab:hover { color: #fff; transform: translateY(-1px); }

        @media (max-width: 768px) {
            .oc-page { padding: 12px 10px; }
            .oc-head h1 { font-size: 1.3rem; }
            .oc-stat { min-width: 0; padding: 5px 10px; }
            .oc-toolbar .form-select { flex: 1 1 45%; }
            .oc-hide-sm { display: none !important; }
            .offcanvas.oc-detail { width: 100%; }
            .oc-row .oc-chip { display: none; }
            .admin-fab { bottom: 12px; left: 12px; }
        }
        @media print {
            body { padding-top: 0; background: #fff; }
            .navbar, .oc-toolbar, .oc-zoom, .oc-hint, .admin-fab, .oc-stats, .oc-crumbs { display: none !important; }
            .oc-viewport { border: 0; background: none; height: auto !important; overflow: visible; }
            .oc-stage { position: relative; }
            @page { size: landscape; margin: 10mm; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('K-CONNECT.png') }}" alt="K-CONNECT" height="26">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">@php getMenu(); @endphp</ul>
                <div class="dropdown ms-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                        @if (!empty($employeePhoto))
                            <img src="{{ kc_upload_url($employeePhoto, 'uploads/employees') }}" width="36" height="36" class="rounded-circle me-2" alt="">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width:36px;height:36px;">
                                <i class="fas fa-user text-gradient"></i>
                            </div>
                        @endif
                        <span class="d-none d-lg-inline">{{ $employeeName }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">{{ $employeeCode }}</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="{{ url('logout') }}">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="oc-page">
        <div class="oc-head">
            <div>
                <h1><span class="text-gradient">Organization</span> Structure</h1>
                <p>Who reports to whom across the group. Click anyone to see their details and team.</p>
            </div>
            <div class="oc-stats" id="ocStats"></div>
        </div>

        <div class="oc-toolbar">
            <div class="oc-search">
                <i class="fas fa-search"></i>
                <input type="search" class="form-control" id="ocSearch" placeholder="Find a person, position or department  ( / )" autocomplete="off">
                <div class="oc-suggest" id="ocSuggest"></div>
            </div>
            <select class="form-select" id="ocCompany" aria-label="Company"></select>
            <select class="form-select" id="ocDept" aria-label="Department"></select>
            <div class="oc-seg" role="group" aria-label="View">
                <button class="oc-btn" data-view="chart" title="Chart view"><i class="fas fa-sitemap"></i><span class="oc-hide-sm">Chart</span></button>
                <button class="oc-btn" data-view="list" title="List view"><i class="fas fa-list-ul"></i><span class="oc-hide-sm">List</span></button>
            </div>
            <div class="oc-sep oc-hide-sm"></div>
            <button class="oc-btn" id="ocExpand" title="Expand everyone"><i class="fas fa-up-right-and-down-left-from-center"></i></button>
            <button class="oc-btn" id="ocCollapse" title="Collapse to top levels"><i class="fas fa-down-left-and-up-right-to-center"></i></button>
            <div class="dropdown">
                <button class="oc-btn" data-bs-toggle="dropdown" data-bs-auto-close="outside" title="Layout options"><i class="fas fa-sliders"></i></button>
                <div class="dropdown-menu dropdown-menu-end p-3" style="min-width:260px;font-size:.85rem;">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="optAlign">
                        <label class="form-check-label" for="optAlign">Line up people of the same level in one row</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="optStack">
                        <label class="form-check-label" for="optStack">Stack teams of 3+ vertically (saves width)</label>
                    </div>
                </div>
            </div>
            <button class="oc-btn oc-hide-sm" id="ocPrint" title="Print chart"><i class="fas fa-print"></i></button>
        </div>

        <div class="oc-legend" id="ocLegend"></div>
        <div class="oc-crumbs" id="ocCrumbs"></div>

        <div class="oc-viewport" id="ocViewport">
            <div class="oc-stage" id="ocStage"></div>
            <div class="oc-hint oc-hide-sm"><i class="fas fa-hand me-1"></i>Drag to move &middot; Ctrl + scroll to zoom &middot; Arrows point to each person's reportees</div>
            <div class="oc-zoom">
                <button id="zIn" title="Zoom in"><i class="fas fa-plus"></i></button>
                <span id="zPct">100%</span>
                <button id="zOut" title="Zoom out"><i class="fas fa-minus"></i></button>
                <button id="zFit" title="Fit to screen"><i class="fas fa-expand"></i></button>
            </div>
        </div>
        <div class="oc-list" id="ocList"></div>
    </div>

    <div class="offcanvas offcanvas-end oc-detail" tabindex="-1" id="ocDetail">
        <div id="ocDetailBody" style="overflow-y:auto;height:100%;"></div>
    </div>

    @if ($isAdminUser)
    <a href="{{ url('admin/orgchart_manager') }}" class="admin-fab"><i class="fas fa-cog"></i> Manage Chart</a>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    window.ORGCHART = {
        nodes:  {!! json_encode($orgNodes, \App\Support\OrgChart::JSON_FLAGS) !!},
        levels: {!! json_encode($levels, \App\Support\OrgChart::JSON_FLAGS) !!},
        baseUrl: {!! json_encode(url('/') . '/') !!}
    };
    </script>
    @verbatim
    <script>
    (function () {
        'use strict';

        /* ================= Settings (safe to tweak) ================= */
        const CARD_W = 216, CARD_H = 104;   // must match .oc-card in CSS
        const H_GAP = 26;                   // space between neighbours
        const V_GAP = 70;                   // space between rows (arrows live here)
        const STACK_GAP = 14, STACK_INDENT = 30, STACK_MIN = 3;
        const ROOT_GAP = 60, PAD = 40;
        const AUTO_COLLAPSE_OVER = 40;      // above this many people, start collapsed below 2 levels

        const { nodes: DATA, levels: LEVELS, baseUrl } = window.ORGCHART;
        const $ = id => document.getElementById(id);
        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        const color = lv => (LEVELS[lv] || LEVELS[8]).color;
        const store = {
            get(k, d) { try { const v = localStorage.getItem('oc_' + k); return v === null ? d : JSON.parse(v); } catch (e) { return d; } },
            set(k, v) { try { localStorage.setItem('oc_' + k, JSON.stringify(v)); } catch (e) {} }
        };

        /* ================= Build the hierarchy ================= */
        const byId = new Map();
        DATA.forEach(n => { n.kids = []; byId.set(n.employee_id, n); });
        DATA.forEach(n => {
            if (n.parent_id && byId.has(n.parent_id) && n.parent_id !== n.employee_id) byId.get(n.parent_id).kids.push(n);
            else n.parent_id = null;
        });
        const sorter = (a, b) => a.sort_order - b.sort_order || a.level - b.level || a.full_name.localeCompare(b.full_name);
        DATA.forEach(n => n.kids.sort(sorter));
        let roots = DATA.filter(n => !n.parent_id).sort(sorter);

        // Guard against loops in the data (A reports to B, B reports to A): promote one of them to top level.
        (function breakCycles() {
            const seen = new Set();
            const walk = n => { seen.add(n.employee_id); n.kids.forEach(walk); };
            roots.forEach(walk);
            let rest = DATA.filter(n => !seen.has(n.employee_id));
            while (rest.length) {
                const n = rest.sort(sorter)[0];
                const p = byId.get(n.parent_id);
                if (p) p.kids = p.kids.filter(k => k !== n);
                n.parent_id = null; roots.push(n); walk(n);
                rest = rest.filter(x => !seen.has(x.employee_id));
            }
        })();

        DATA.forEach(n => { n.depth = 0; let p = n; while (p.parent_id) { p = byId.get(p.parent_id); n.depth++; } });
        const teamSize = (() => { const memo = new Map(); const f = n => { if (!memo.has(n.employee_id)) memo.set(n.employee_id, n.kids.reduce((s, k) => s + 1 + f(k), 0)); return memo.get(n.employee_id); }; return f; })();
        const ancestors = n => { const out = []; let p = n; while (p.parent_id) { p = byId.get(p.parent_id); out.unshift(p); } return out; };
        const initials = name => name.split(/\s+/).filter(Boolean).slice(0, 2).map(w => w[0]).join('').toUpperCase();
        const photoUrl = n => n.photo ? (/^(https?:)?\/\//.test(n.photo) ? n.photo : baseUrl + n.photo) : '';
        const avatar = n => {
            const url = photoUrl(n);
            return `<div class="oc-av" style="--c:${color(n.level)}">${url
                ? `<img src="${esc(url)}" alt="" loading="lazy" onerror="this.parentNode.textContent='${esc(initials(n.full_name))}'">`
                : esc(initials(n.full_name))}</div>`;
        };

        /* ================= State ================= */
        const smallScreen = window.matchMedia('(max-width: 768px)').matches;
        const state = {
            view: store.get('view', smallScreen ? 'list' : 'chart'),
            align: store.get('align', true),
            stack: store.get('stack', true),
            collapsed: new Set(),
            listClosed: new Set(),
            focus: null,
            selected: null,
            q: '', company: '', dept: '', level: 0,
            s: 1, tx: 0, ty: 0
        };
        function defaultCollapse() {
            state.collapsed.clear(); state.listClosed.clear();
            DATA.forEach(n => {
                if (!n.kids.length) return;
                if (DATA.length > AUTO_COLLAPSE_OVER && n.depth >= 1) state.collapsed.add(n.employee_id);
                if (n.depth >= 1) state.listClosed.add(n.employee_id);
            });
        }
        defaultCollapse();

        /* ================= Filtering ================= */
        const filterActive = () => !!(state.q || state.company || state.dept || state.level);
        function matches(n) {
            if (state.company && String(n.company_id) !== state.company) return false;
            if (state.dept && n.department !== state.dept) return false;
            if (state.level && n.level !== state.level) return false;
            if (state.q) {
                const hay = [n.full_name, n.position, n.department, n.employee_code, n.level_label, n.company_name].join(' ').toLowerCase();
                if (!state.q.toLowerCase().split(/\s+/).every(t => hay.includes(t))) return false;
            }
            return true;
        }
        function revealMatches() {
            if (!filterActive()) return;
            const hits = DATA.filter(matches);
            if (hits.length > 150) return;
            hits.forEach(n => ancestors(n).forEach(a => { state.collapsed.delete(a.employee_id); state.listClosed.delete(a.employee_id); }));
        }

        /* ================= Layout engine ================= */
        const visKids = n => state.collapsed.has(n.employee_id) ? [] : n.kids;
        // Only teams made purely of people with no reportees are stacked; collapsed managers stay side by side.
        const isStacked = n => { const k = visKids(n); return state.stack && k.length >= STACK_MIN && k.every(c => !c.kids.length); };

        function layout() {
            const tops = state.focus ? [byId.get(state.focus)] : roots;
            const visible = [];
            const collect = n => { visible.push(n); visKids(n).forEach(collect); };
            tops.forEach(collect);

            // Rows: by level (grade) so equals line up, or by reporting depth.
            const used = [...new Set(visible.map(n => n.level))].sort((a, b) => a - b);
            const rankRow = new Map(used.map((lv, i) => [lv, i]));
            const row = new Map();
            const setRow = (n, parentRow) => {
                let r = state.align ? rankRow.get(n.level) : parentRow + 1;
                if (parentRow >= 0) r = Math.max(r, parentRow + 1);
                row.set(n.employee_id, r);
                visKids(n).forEach(k => setRow(k, r));
            };
            tops.forEach(t => setRow(t, -1));
            // Compact away empty rows so the chart has no dead space.
            const rowsUsed = [...new Set(row.values())].sort((a, b) => a - b);
            const compact = new Map(rowsUsed.map((r, i) => [r, i]));
            row.forEach((r, id) => row.set(id, compact.get(r)));
            const rowY = r => PAD + r * (CARD_H + V_GAP);

            const widths = new Map();
            const width = n => {
                if (widths.has(n.employee_id)) return widths.get(n.employee_id);
                const k = visKids(n); let w = CARD_W;
                if (k.length && isStacked(n)) w = CARD_W + STACK_INDENT;
                else if (k.length) w = Math.max(CARD_W, k.reduce((s, c) => s + width(c), 0) + H_GAP * (k.length - 1));
                widths.set(n.employee_id, w); return w;
            };

            const pos = new Map(); const edges = [];
            const place = (n, left) => {
                const w = width(n), k = visKids(n), y = rowY(row.get(n.employee_id));
                if (!k.length) { pos.set(n.employee_id, { x: left + (w - CARD_W) / 2, y }); return; }
                if (isStacked(n)) {
                    const x = left + STACK_INDENT;
                    pos.set(n.employee_id, { x, y });
                    const startRow = Math.max(row.get(n.employee_id) + 1, Math.min(...k.map(c => row.get(c.employee_id))));
                    k.forEach((c, i) => pos.set(c.employee_id, { x, y: rowY(startRow) + i * (CARD_H + STACK_GAP) }));
                    return;
                }
                const total = k.reduce((s, c) => s + width(c), 0) + H_GAP * (k.length - 1);
                let cl = left + (w - total) / 2;
                k.forEach(c => { place(c, cl); cl += width(c) + H_GAP; });
                const a = pos.get(k[0].employee_id).x, b = pos.get(k[k.length - 1].employee_id).x;
                pos.set(n.employee_id, { x: (a + b) / 2, y });
            };
            let left = PAD;
            tops.forEach(t => { place(t, left); left += width(t) + ROOT_GAP; });

            // Connectors: parent bottom -> shared bar -> arrow down into each reportee.
            visible.forEach(n => {
                const k = visKids(n); if (!k.length) return;
                const p = pos.get(n.employee_id), px = p.x + CARD_W / 2, py = p.y + CARD_H;
                const busY = py + V_GAP / 2;
                if (isStacked(n)) {
                    const railX = p.x - STACK_INDENT / 2;
                    k.forEach(c => {
                        const q = pos.get(c.employee_id), cy = q.y + CARD_H / 2;
                        edges.push({ from: n.employee_id, to: c.employee_id,
                            d: `M${px},${py} V${busY} H${railX} V${cy} H${q.x - 2}` });
                    });
                } else {
                    k.forEach(c => {
                        const q = pos.get(c.employee_id), cx = q.x + CARD_W / 2;
                        edges.push({ from: n.employee_id, to: c.employee_id,
                            d: cx === px ? `M${px},${py} V${q.y - 2}` : `M${px},${py} V${busY} H${cx} V${q.y - 2}` });
                    });
                }
            });

            let W = 0, H = 0;
            pos.forEach(p => { W = Math.max(W, p.x + CARD_W); H = Math.max(H, p.y + CARD_H); });
            return { visible, pos, edges, W: W + PAD, H: H + PAD + 16 };
        }

        /* ================= Chart rendering ================= */
        const viewport = $('ocViewport'), stage = $('ocStage');
        let L = null;

        function cardHtml(n, p) {
            const k = n.kids.length, closed = state.collapsed.has(n.employee_id);
            const team = teamSize(n);
            const meta = [n.department, n.company_name].filter(Boolean).join(' · ');
            return `<div class="oc-card${n.level <= 2 ? ' top' : ''}" data-id="${n.employee_id}" style="left:${p.x}px;top:${p.y}px;--c:${color(n.level)}" title="${esc(n.full_name)}">
                <div class="oc-body">${avatar(n)}
                    <div class="oc-txt"><div class="oc-name">${esc(n.full_name)}</div>
                        <div class="oc-pos">${esc(n.position || '—')}</div>
                        <div class="oc-meta">${esc(meta)}</div></div></div>
                <div class="oc-foot"><span class="oc-chip">${esc(n.level_label)}</span>
                    ${team ? `<span class="oc-team" title="Total team size"><i class="fas fa-users me-1"></i>${team}</span>` : ''}</div>
                ${k ? `<button class="oc-toggle${closed ? ' closed' : ''}" data-toggle="${n.employee_id}" title="${closed ? 'Show' : 'Hide'} ${k} direct report${k > 1 ? 's' : ''}">
                    ${closed ? `<i class="fas fa-chevron-down"></i>${k}` : '<i class="fas fa-chevron-up"></i>'}</button>` : ''}
            </div>`;
        }

        function renderChart() {
            if (!DATA.length) {
                L = null; state.s = 1; state.tx = 0; state.ty = 0; applyTransform();
                stage.style.width = viewport.clientWidth + 'px';
                stage.innerHTML = emptyHtml(); return;
            }
            L = layout();
            const hl = highlightSet();
            const filt = filterActive();
            const svg = `<svg width="${L.W}" height="${L.H}" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <marker id="ocArrow" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0,0 L10,5 L0,10 z" fill="#a3b1c2"/></marker>
                    <marker id="ocArrowHl" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0,0 L10,5 L0,10 z" fill="#009ada"/></marker>
                </defs>
                ${L.edges.map(e => {
                    const on = hl.has(e.from) && hl.has(e.to);
                    const dim = filt && !(matches(byId.get(e.to)));
                    return `<path class="oc-edge${on ? ' hl' : ''}${dim ? ' dim' : ''}" d="${e.d}" marker-end="url(#${on ? 'ocArrowHl' : 'ocArrow'})"/>`;
                }).join('')}
            </svg>`;
            stage.style.width = L.W + 'px'; stage.style.height = L.H + 'px';
            stage.innerHTML = svg + L.visible.map(n => cardHtml(n, L.pos.get(n.employee_id))).join('');
            stage.querySelectorAll('.oc-card').forEach(el => {
                const n = byId.get(+el.dataset.id);
                if (filt && !matches(n)) el.classList.add('dim');
                if (state.selected === n.employee_id) el.classList.add('sel');
            });
            applyTransform();
        }

        // Selected person + their management line + direct reports get blue connectors.
        function highlightSet() {
            const s = new Set(); if (!state.selected || !byId.has(state.selected)) return s;
            const n = byId.get(state.selected);
            s.add(n.employee_id); ancestors(n).forEach(a => s.add(a.employee_id)); n.kids.forEach(k => s.add(k.employee_id));
            return s;
        }

        /* ================= Pan & zoom ================= */
        function applyTransform() {
            stage.style.transform = `translate(${state.tx}px,${state.ty}px) scale(${state.s})`;
            $('zPct').textContent = Math.round(state.s * 100) + '%';
        }
        function zoomAt(f, cx, cy) {
            const s = Math.min(2, Math.max(0.15, state.s * f));
            state.tx = cx - (cx - state.tx) * (s / state.s);
            state.ty = cy - (cy - state.ty) * (s / state.s);
            state.s = s; applyTransform();
        }
        function fit(maxScale = 1.1) {
            if (!L) return;
            const vw = viewport.clientWidth, vh = viewport.clientHeight;
            state.s = Math.max(0.15, Math.min((vw - 20) / L.W, (vh - 20) / L.H, maxScale));
            state.tx = (vw - L.W * state.s) / 2; state.ty = Math.max(10, (vh - L.H * state.s) / 2);
            applyTransform();
        }
        function initialView() {
            if (!L) return;
            const vw = viewport.clientWidth, vh = viewport.clientHeight;
            const s = Math.min((vw - 20) / L.W, (vh - 20) / L.H, 1);
            if (s >= 0.62) { fit(1); return; }
            // Too big to read when fitted: start readable, centred on the top of the chart.
            state.s = Math.max(0.62, Math.min(0.85, (vw - 20) / L.W * 1.6));
            const top = L.visible[0], p = L.pos.get(top.employee_id);
            state.tx = vw / 2 - (p.x + CARD_W / 2) * state.s; state.ty = 16;
            applyTransform();
        }
        function centerOn(id) {
            const p = L && L.pos.get(id); if (!p) return;
            state.s = Math.max(state.s, 0.8);
            state.tx = viewport.clientWidth / 2 - (p.x + CARD_W / 2) * state.s;
            state.ty = viewport.clientHeight / 2 - (p.y + CARD_H / 2) * state.s;
            applyTransform();
        }

        const pointers = new Map(); let drag = null, pinch = null, moved = false;
        viewport.addEventListener('pointerdown', e => {
            if (e.target.closest('.oc-zoom, .oc-toggle')) return;
            pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
            moved = false;
            if (pointers.size === 1) drag = { x: e.clientX, y: e.clientY, tx: state.tx, ty: state.ty };
            if (pointers.size === 2) {
                const [a, b] = [...pointers.values()];
                pinch = { d: Math.hypot(a.x - b.x, a.y - b.y), s: state.s }; drag = null;
            }
        });
        window.addEventListener('pointermove', e => {
            if (!pointers.has(e.pointerId)) return;
            pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
            if (pinch && pointers.size === 2) {
                const [a, b] = [...pointers.values()], r = viewport.getBoundingClientRect();
                const d = Math.hypot(a.x - b.x, a.y - b.y);
                zoomAt((pinch.s * d / pinch.d) / state.s, (a.x + b.x) / 2 - r.left, (a.y + b.y) / 2 - r.top);
                moved = true; return;
            }
            if (!drag) return;
            const dx = e.clientX - drag.x, dy = e.clientY - drag.y;
            if (Math.abs(dx) + Math.abs(dy) > 4) { moved = true; viewport.classList.add('dragging'); }
            state.tx = drag.tx + dx; state.ty = drag.ty + dy; applyTransform();
        });
        const endPointer = e => {
            pointers.delete(e.pointerId);
            if (pointers.size < 2) pinch = null;
            if (!pointers.size) { drag = null; viewport.classList.remove('dragging'); }
        };
        window.addEventListener('pointerup', endPointer);
        window.addEventListener('pointercancel', endPointer);
        viewport.addEventListener('wheel', e => {
            e.preventDefault();
            const r = viewport.getBoundingClientRect();
            if (e.ctrlKey || e.metaKey) zoomAt(Math.exp(-e.deltaY * 0.0022), e.clientX - r.left, e.clientY - r.top);
            else { state.tx -= e.deltaX; state.ty -= e.deltaY; applyTransform(); }
        }, { passive: false });
        $('zIn').onclick = () => zoomAt(1.2, viewport.clientWidth / 2, viewport.clientHeight / 2);
        $('zOut').onclick = () => zoomAt(1 / 1.2, viewport.clientWidth / 2, viewport.clientHeight / 2);
        $('zFit').onclick = () => fit();

        stage.addEventListener('click', e => {
            if (moved) return;
            const t = e.target.closest('[data-toggle]');
            if (t) { toggleNode(+t.dataset.toggle); return; }
            const c = e.target.closest('.oc-card');
            if (c) select(+c.dataset.id, true);
        });

        function toggleNode(id) {
            const before = L && L.pos.get(id);
            state.collapsed.has(id) ? state.collapsed.delete(id) : state.collapsed.add(id);
            renderChart();
            // Keep the clicked card where it was on screen, so the chart does not jump.
            const after = L.pos.get(id);
            if (before && after) { state.tx -= (after.x - before.x) * state.s; state.ty -= (after.y - before.y) * state.s; applyTransform(); }
        }

        /* ================= List view ================= */
        const list = $('ocList');
        function renderList() {
            if (!DATA.length) { list.innerHTML = emptyHtml(); return; }
            const filt = filterActive();
            const keep = new Set();
            if (filt) DATA.filter(matches).forEach(n => { keep.add(n.employee_id); ancestors(n).forEach(a => keep.add(a.employee_id)); });
            const tops = state.focus ? [byId.get(state.focus)] : roots;
            const li = n => {
                if (filt && !keep.has(n.employee_id)) return '';
                const kids = filt ? n.kids.filter(k => keep.has(k.employee_id)) : n.kids;
                const open = filt || !state.listClosed.has(n.employee_id);
                const team = teamSize(n);
                return `<li><div class="oc-row${state.selected === n.employee_id ? ' sel' : ''}${filt && matches(n) ? ' hit' : ''}" data-id="${n.employee_id}" style="--c:${color(n.level)}">
                    <button class="chev${kids.length ? '' : ' none'}" data-lt="${n.employee_id}" aria-label="Show or hide team"><i class="fas fa-chevron-${open ? 'down' : 'right'}"></i></button>
                    ${avatar(n)}
                    <div class="oc-txt"><div class="oc-name">${esc(n.full_name)}</div><div class="oc-pos">${esc(n.position || '—')}</div>
                        <div class="oc-meta">${esc([n.department, n.company_name].filter(Boolean).join(' · '))}</div></div>
                    <span class="oc-chip">${esc(n.level_label)}</span>
                    <span class="oc-team">${team ? `<i class="fas fa-users me-1"></i>${team}` : ''}</span>
                </div>${kids.length && open ? `<ul>${kids.map(li).join('')}</ul>` : ''}</li>`;
            };
            const html = tops.map(li).join('');
            list.innerHTML = html ? `<ul>${html}</ul>` : `<div class="oc-empty"><i class="fas fa-search d-block"></i>No one matches these filters.</div>`;
        }
        list.addEventListener('click', e => {
            const t = e.target.closest('[data-lt]');
            if (t) { const id = +t.dataset.lt; state.listClosed.has(id) ? state.listClosed.delete(id) : state.listClosed.add(id); renderList(); return; }
            const r = e.target.closest('.oc-row'); if (r) select(+r.dataset.id, true);
        });

        /* ================= Details panel ================= */
        const detail = new bootstrap.Offcanvas($('ocDetail'));
        function select(id, openPanel) {
            state.selected = id;
            if (state.view === 'chart') {
                stage.querySelectorAll('.oc-card.sel').forEach(c => c.classList.remove('sel'));
                renderChart();
            } else renderList();
            if (openPanel) showDetail(byId.get(id));
        }
        function personRow(n) {
            return `<div class="oc-d-person" data-go="${n.employee_id}">${avatar(n)}
                <div class="oc-txt"><div class="oc-name">${esc(n.full_name)}</div><div class="oc-pos">${esc(n.position || n.level_label)}</div></div>
                <i class="fas fa-chevron-right ms-auto text-muted small"></i></div>`;
        }
        function showDetail(n) {
            const mgr = n.parent_id ? byId.get(n.parent_id) : null;
            const url = photoUrl(n), c = color(n.level);
            const row = (icon, label, val) => val ? `<div><i class="fas fa-${icon}"></i><span>${label}</span><b>${val}</b></div>` : '';
            const phone = [n.phone, n.extension ? 'Ext. ' + esc(n.extension) : ''].filter(Boolean).map(v => v.startsWith('Ext') ? v : `<a href="tel:${esc(v)}">${esc(v)}</a>`).join(' &middot; ');
            $('ocDetailBody').innerHTML = `
                <div class="oc-d-head"><button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
                    <div class="small opacity-75">${esc(n.company_name || 'Kiboko')}</div></div>
                <div class="text-center px-3">
                    <div class="oc-d-photo" style="color:${c};border-color:#fff">${url ? `<img src="${esc(url)}" alt="" onerror="this.parentNode.textContent='${esc(initials(n.full_name))}'">` : esc(initials(n.full_name))}</div>
                    <h5 class="fw-bold mb-1">${esc(n.full_name)}</h5>
                    <div style="color:var(--blue);font-weight:600;font-size:.9rem;">${esc(n.position || 'No position set')}</div>
                    <span class="d-inline-block mt-2 px-3 py-1 rounded-pill text-white small fw-semibold" style="background:${c}">${esc(n.level_label)}</span>
                </div>
                <div class="oc-d-sec"><h6>Details</h6><div class="oc-d-info">
                    ${row('id-badge', 'Code', esc(n.employee_code))}
                    ${row('building', 'Company', esc(n.company_name))}
                    ${row('briefcase', 'Department', esc(n.department))}
                    ${row('envelope', 'Email', n.email ? `<a href="mailto:${esc(n.email)}">${esc(n.email)}</a>` : '')}
                    ${row('phone', 'Phone', phone)}
                    ${row('mobile-screen', 'Mobile', n.mobile ? `<a href="tel:${esc(n.mobile)}">${esc(n.mobile)}</a>` : '')}
                    ${row('users', 'Team size', teamSize(n) ? `${teamSize(n)} people (${n.kids.length} direct)` : '')}
                </div></div>
                <div class="oc-d-sec"><h6>Reports to</h6>${mgr ? personRow(mgr) : '<div class="text-muted small px-2">Top of the organization</div>'}</div>
                ${n.kids.length ? `<div class="oc-d-sec"><h6>Direct reports (${n.kids.length})</h6>${n.kids.map(personRow).join('')}</div>` : ''}
                <div class="oc-d-actions">
                    <button class="oc-btn" data-act="locate"><i class="fas fa-location-crosshairs"></i>Show in chart</button>
                    ${n.kids.length ? `<button class="oc-btn" data-act="focus"><i class="fas fa-people-group"></i>View this team</button>` : ''}
                </div>`;
            $('ocDetailBody').onclick = e => {
                const go = e.target.closest('[data-go]');
                if (go) { const t = byId.get(+go.dataset.go); locate(t.employee_id, false); showDetail(t); return; }
                const act = e.target.closest('[data-act]'); if (!act) return;
                if (act.dataset.act === 'locate') { detail.hide(); locate(n.employee_id, true); }
                if (act.dataset.act === 'focus') { detail.hide(); setFocus(n.employee_id); }
            };
            detail.show();
        }

        /* ================= Navigation helpers ================= */
        function locate(id, pulse) {
            const n = byId.get(id);
            if (state.focus && !ancestors(n).some(a => a.employee_id === state.focus) && state.focus !== id) setFocus(null, true);
            ancestors(n).forEach(a => { state.collapsed.delete(a.employee_id); state.listClosed.delete(a.employee_id); });
            state.selected = id;
            render();
            if (state.view === 'chart') {
                centerOn(id);
                if (pulse) { const el = stage.querySelector(`.oc-card[data-id="${id}"]`); el && el.classList.add('pulse'); }
            } else {
                const el = list.querySelector(`.oc-row[data-id="${id}"]`);
                el && el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        function setFocus(id, silent) {
            state.focus = id;
            if (id) { state.collapsed.delete(id); state.listClosed.delete(id); byId.get(id).kids.forEach(k => state.listClosed.delete(k.employee_id)); }
            renderCrumbs();
            if (!silent) { render(); if (state.view === 'chart') initialView(); }
        }
        function renderCrumbs() {
            const el = $('ocCrumbs');
            if (!state.focus) { el.classList.remove('show'); el.innerHTML = ''; return; }
            const n = byId.get(state.focus);
            el.innerHTML = `<i class="fas fa-people-group text-muted me-1"></i><a data-f="">Whole organization</a>` +
                ancestors(n).map(a => `<span class="sep">›</span><a data-f="${a.employee_id}">${esc(a.full_name)}</a>`).join('') +
                `<span class="sep">›</span><b>${esc(n.full_name)}'s team</b>`;
            el.classList.add('show');
        }
        $('ocCrumbs').addEventListener('click', e => { const a = e.target.closest('[data-f]'); if (a) setFocus(a.dataset.f ? +a.dataset.f : null); });

        /* ================= Toolbar ================= */
        function fillFilters() {
            const companies = [...new Map(DATA.filter(n => n.company_id).map(n => [n.company_id, n.company_name])).entries()].sort((a, b) => a[1].localeCompare(b[1]));
            $('ocCompany').innerHTML = `<option value="">All companies</option>` + companies.map(([id, nm]) => `<option value="${id}">${esc(nm)}</option>`).join('');
            $('ocCompany').style.display = companies.length > 1 ? '' : 'none';
            const depts = [...new Set(DATA.map(n => n.department).filter(Boolean))].sort();
            $('ocDept').innerHTML = `<option value="">All departments</option>` + depts.map(d => `<option>${esc(d)}</option>`).join('');
            $('ocDept').style.display = depts.length ? '' : 'none';

            $('ocStats').innerHTML = [
                [DATA.length, 'People'], [depts.length, 'Departments'], [companies.length, 'Companies'],
                [new Set(DATA.map(n => n.level)).size, 'Levels']
            ].filter(s => s[0]).map(([v, l]) => `<div class="oc-stat"><b>${v}</b><span>${l}</span></div>`).join('');
        }
        function renderLegend() {
            const counts = {}; DATA.forEach(n => counts[n.level] = (counts[n.level] || 0) + 1);
            const chips = Object.keys(LEVELS).filter(lv => counts[lv]).map(lv =>
                `<span class="oc-lv${state.level === +lv ? ' on' : ''}" data-lv="${lv}" style="--c:${LEVELS[lv].color}" title="Highlight ${esc(LEVELS[lv].label)}"><i></i>${esc(LEVELS[lv].label)} <em>${counts[lv]}</em></span>`).join('');
            const hits = filterActive() ? DATA.filter(matches).length : null;
            $('ocLegend').innerHTML = `<span class="lbl">Levels:</span>${chips}` +
                (hits !== null ? `<span class="oc-filter-note">${hits} match${hits === 1 ? '' : 'es'}<a href="#" id="ocClear">Clear</a></span>` : '');
        }
        $('ocLegend').addEventListener('click', e => {
            if (e.target.id === 'ocClear') {
                e.preventDefault(); state.q = state.company = state.dept = ''; state.level = 0;
                $('ocSearch').value = ''; $('ocCompany').value = ''; $('ocDept').value = ''; render(); return;
            }
            const c = e.target.closest('[data-lv]'); if (!c) return;
            state.level = state.level === +c.dataset.lv ? 0 : +c.dataset.lv; revealMatches(); render();
        });
        $('ocCompany').onchange = e => { state.company = e.target.value; revealMatches(); render(); };
        $('ocDept').onchange = e => { state.dept = e.target.value; revealMatches(); render(); };

        // Search with suggestions
        const search = $('ocSearch'), sug = $('ocSuggest'); let sugIdx = -1, sugItems = [], typing;
        search.addEventListener('input', () => {
            const q = search.value.trim();
            clearTimeout(typing);
            typing = setTimeout(() => { state.q = q; revealMatches(); render(); }, 200);
            if (!q) { sug.classList.remove('show'); return; }
            const save = state.q; state.q = q;
            sugItems = DATA.filter(matches).slice(0, 8); state.q = save; sugIdx = -1;
            sug.innerHTML = sugItems.length ? sugItems.map((n, i) => `<button data-i="${i}">${avatar(n)}<span><b>${esc(n.full_name)}</b><small>${esc([n.position, n.department].filter(Boolean).join(' · '))}</small></span></button>`).join('')
                : `<div class="p-3 small text-muted">No one found</div>`;
            sug.classList.add('show');
        });
        search.addEventListener('keydown', e => {
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault(); if (!sugItems.length) return;
                sugIdx = (sugIdx + (e.key === 'ArrowDown' ? 1 : -1) + sugItems.length) % sugItems.length;
                sug.querySelectorAll('button').forEach((b, i) => b.classList.toggle('kb', i === sugIdx));
            } else if (e.key === 'Enter') {
                e.preventDefault(); const n = sugItems[Math.max(0, sugIdx)]; if (n) pick(n);
            } else if (e.key === 'Escape') { sug.classList.remove('show'); search.blur(); }
        });
        sug.addEventListener('mousedown', e => { const b = e.target.closest('[data-i]'); if (b) { e.preventDefault(); pick(sugItems[+b.dataset.i]); } });
        search.addEventListener('blur', () => setTimeout(() => sug.classList.remove('show'), 150));
        function pick(n) {
            sug.classList.remove('show'); search.value = ''; state.q = '';
            locate(n.employee_id, true); showDetail(n);
        }
        document.addEventListener('keydown', e => {
            if (e.key === '/' && !/input|select|textarea/i.test(document.activeElement.tagName)) { e.preventDefault(); search.focus(); }
        });

        document.querySelectorAll('[data-view]').forEach(b => b.onclick = () => {
            state.view = b.dataset.view; store.set('view', state.view); render();
            if (state.view === 'chart') initialView();
        });
        $('ocExpand').onclick = () => { state.collapsed.clear(); state.listClosed.clear(); render(); if (state.view === 'chart') fit(); };
        $('ocCollapse').onclick = () => {
            DATA.forEach(n => { if (n.kids.length && n.depth >= 1) { state.collapsed.add(n.employee_id); state.listClosed.add(n.employee_id); } });
            render(); if (state.view === 'chart') initialView();
        };
        $('optAlign').checked = state.align; $('optStack').checked = state.stack;
        $('optAlign').onchange = e => { state.align = e.target.checked; store.set('align', state.align); render(); };
        $('optStack').onchange = e => { state.stack = e.target.checked; store.set('stack', state.stack); render(); };
        $('ocPrint').onclick = () => {
            if (state.view !== 'chart' || !L) { window.print(); return; }
            const saved = { s: state.s, tx: state.tx, ty: state.ty };
            state.s = Math.min(1, 1050 / L.W, 700 / L.H); state.tx = 0; state.ty = 0; applyTransform();
            viewport.style.height = (L.H * state.s + 20) + 'px';
            setTimeout(() => { window.print(); Object.assign(state, saved); sizeViewport(); applyTransform(); }, 50);
        };

        /* ================= Main render ================= */
        function emptyHtml() {
            return `<div class="oc-empty"><i class="fas fa-sitemap d-block"></i><h5>Organization chart not set up yet</h5>
                <p class="mb-0">An administrator can add people from <b>Admin › Organization Chart</b>.</p></div>`;
        }
        function render() {
            document.querySelectorAll('[data-view]').forEach(b => b.classList.toggle('active', b.dataset.view === state.view));
            viewport.style.display = state.view === 'chart' ? '' : 'none';
            list.classList.toggle('show', state.view === 'list');
            ['ocExpand', 'ocCollapse'].forEach(id => $(id).style.display = DATA.length ? '' : 'none');
            renderLegend();
            if (state.view === 'chart') { sizeViewport(); renderChart(); } else renderList();
        }
        function sizeViewport() {
            const top = viewport.getBoundingClientRect().top + window.scrollY;
            viewport.style.height = Math.max(420, window.innerHeight - top - 16) + 'px';
        }
        let rz; window.addEventListener('resize', () => { clearTimeout(rz); rz = setTimeout(() => { if (state.view === 'chart') { sizeViewport(); applyTransform(); } }, 120); });

        fillFilters();
        render();
        if (state.view === 'chart') initialView();
        // Deep link: orgchart_view?emp=<employee id>
        const deep = +new URLSearchParams(location.search).get('emp');
        if (deep && byId.has(deep)) { locate(deep, true); }
    })();
    </script>
    @endverbatim
</body>
</html>

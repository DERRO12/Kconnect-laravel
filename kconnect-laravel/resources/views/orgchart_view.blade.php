<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Chart - K-Connect</title>
    <link rel="icon" type="image/png" href="favi.png">
    
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <style>
        :root {
            --blue: #009ada;
            --green: #76bc21;
            --dark: #1e293b;
            --gray: #64748b;
            --border: #eef2f6;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
            --line-h: 18px;
            --card-gap: 4px;
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
            backdrop-filter: blur(12px);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            padding: 0.75rem 0; height: 76px;
        }
        
        .page-header { text-align: center; padding: 25px 20px 5px; }
        .page-header h1 { font-size: 1.8rem; font-weight: 700; }
        .text-gradient {
            background: var(--grad);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .tree-container {
            padding: 15px 20px 40px;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
        }
        .tree-container::-webkit-scrollbar { height: 6px; width: 6px; }
        .tree-container::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
        .tree-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        
        /* ================================================================
           MAIN TREE - Each level is a separate row (ul)
           Level 1 is a flex row with fixed tight spacing
           Level 2+ are nested inside level 1 li's but use CSS to not
           affect the parent li width
        ================================================================ */
        .org-tree {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: max-content;
            padding: 10px 0;
        }
        
        /* === LEVEL 1 ROW - FIXED TIGHT SPACING === */
        .org-tree > .tree-row-l1 {
            display: flex;
            justify-content: center;
            flex-wrap: nowrap;
            gap: 4px;
            position: relative;
        }
        
        .org-tree > .tree-row-l1 > .tree-li {
            list-style: none;
            text-align: center;
            position: relative;
            flex-shrink: 0;
            /* Level 1 li does NOT grow based on children */
        }
        
        /* === LEVEL 2+ ROWS (nested inside level 1 li's) === */
        .org-tree ul {
            padding-top: var(--line-h);
            position: relative;
            display: flex;
            justify-content: center;
            flex-wrap: nowrap;
        }
        
        .org-tree li, .tree-li-inner {
            list-style: none;
            position: relative;
            padding: var(--line-h) 10px 0 10px;
            text-align: center;
            float: left;
        }
        
        /* ─── CONNECTING LINES FOR LEVEL 2+ ─── */
        .org-tree li::before,
        .org-tree li::after,
        .tree-li-inner::before,
        .tree-li-inner::after {
            content: '';
            position: absolute;
            top: 0;
            right: 50%;
            border-top: 1.5px solid #cbd5e1;
            width: 50%;
            height: var(--line-h);
        }
        .org-tree li::after,
        .tree-li-inner::after {
            right: auto;
            left: 50%;
            border-left: 1.5px solid #cbd5e1;
        }
        .org-tree li:only-child::after,
        .org-tree li:only-child::before,
        .tree-li-inner:only-child::after,
        .tree-li-inner:only-child::before { display: none; }
        .org-tree li:only-child,
        .tree-li-inner:only-child { padding-top: 0; }
        .org-tree li:first-child::before,
        .org-tree li:last-child::after,
        .tree-li-inner:first-child::before,
        .tree-li-inner:last-child::after { border: 0 none; }
        .org-tree li:last-child::before,
        .tree-li-inner:last-child::before {
            border-right: 1.5px solid #cbd5e1;
            border-radius: 0 4px 0 0;
        }
        .org-tree li:first-child::after,
        .tree-li-inner:first-child::after {
            border-radius: 4px 0 0 0;
        }
        
        /* Vertical line from parent to children */
        .org-tree ul ul::before {
            content: '';
            position: absolute;
            top: 0; left: 50%;
            border-left: 1.5px solid #cbd5e1;
            width: 0;
            height: var(--line-h);
        }
        
        /* Arrow + line going down from any node with children */
        .has-children > .node-wrapper::after {
            content: '';
            position: absolute;
            bottom: calc(-1 * var(--line-h));
            left: 50%;
            transform: translateX(-50%);
            width: 1.5px;
            height: var(--line-h);
            background: #cbd5e1;
        }
        .has-children > .node-wrapper::before {
            content: '';
            position: absolute;
            bottom: calc(-1 * var(--line-h));
            left: 50%;
            transform: translateX(-50%);
            width: 0; height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 6px solid #cbd5e1;
            z-index: 2;
        }
        
        /* ========== NODE CARD ========== */
        .node-wrapper { display: inline-block; position: relative; }
        
        .tree-node {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border: 1.5px solid var(--border);
            padding: 10px 8px 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 125px;
            max-width: 170px;
            position: relative;
            z-index: 1;
        }
        .tree-node:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,154,218,0.12);
            border-color: var(--blue);
            z-index: 10;
        }
        
        .tree-node.lvl-1 {
            border-color: #76bc21;
            background: linear-gradient(135deg, #fff 0%, #f0fdf4 100%);
            min-width: 165px;
            padding: 14px 12px 12px;
        }
        .tree-node.lvl-2 {
            border-color: #f59e0b;
            background: linear-gradient(135deg, #fff 0%, #fffbeb 100%);
            min-width: 145px;
        }
        .tree-node.lvl-3 {
            border-color: #8b5cf6;
            background: linear-gradient(135deg, #fff 0%, #f5f3ff 100%);
        }
        .tree-node.lvl-4 { border-color: #3b82f6; }
        .tree-node.lvl-5, .tree-node.lvl-6, .tree-node.lvl-7, .tree-node.lvl-8 {
            border-color: #94a3b8;
            min-width: 110px;
            padding: 8px 6px 6px;
        }
        
        .rank-badge {
            position: absolute; top: -9px; left: 50%;
            transform: translateX(-50%);
            font-size: 0.5rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
            padding: 1px 7px; border-radius: 7px;
            white-space: nowrap; z-index: 2; color: white;
        }
        .rank-1 { background: #76bc21; }
        .rank-2 { background: #f59e0b; }
        .rank-3 { background: #8b5cf6; }
        .rank-4 { background: #3b82f6; }
        
        .tree-avatar {
            width: 42px; height: 42px;
            border-radius: 50%; object-fit: cover;
            border: 2px solid var(--blue);
            box-shadow: 0 3px 8px rgba(0,154,218,0.1);
            margin-bottom: 5px;
            background: #f1f5f9;
        }
        .lvl-1 .tree-avatar { width: 54px; height: 54px; border-color: #76bc21; border-width: 3px; }
        .lvl-2 .tree-avatar { width: 46px; height: 46px; border-color: #f59e0b; }
        
        .avatar-placeholder {
            width: 42px; height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(0,154,218,0.08), rgba(118,188,33,0.08));
            display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--blue);
            margin-bottom: 5px;
        }
        .lvl-1 .avatar-placeholder { width: 54px; height: 54px; border-color: #76bc21; border-width: 3px; }
        .lvl-2 .avatar-placeholder { width: 46px; height: 46px; border-color: #f59e0b; }
        .avatar-placeholder i { font-size: 1.1rem; color: var(--blue); opacity: 0.5; }
        
        .tree-name {
            font-size: 0.66rem; font-weight: 700;
            color: var(--dark); line-height: 1.2;
            word-break: break-word;
        }
        .lvl-1 .tree-name { font-size: 0.76rem; }
        .lvl-2 .tree-name { font-size: 0.7rem; }
        
        .tree-position {
            font-size: 0.54rem; color: var(--blue); font-weight: 600;
            background: rgba(0,154,218,0.07);
            padding: 1px 6px; border-radius: 8px;
            display: inline-block; max-width: 100%;
            word-break: break-word; margin-top: 2px;
        }
        .tree-dept { font-size: 0.5rem; color: #94a3b8; margin-top: 1px; }
        
        .scroll-hint { text-align: center; color: #94a3b8; font-size: 0.65rem; padding: 4px 0 6px; }
        
        .zoom-controls {
            position: fixed; bottom: 20px; right: 20px;
            display: flex; flex-direction: column; gap: 4px; z-index: 100;
        }
        .zoom-btn {
            width: 34px; height: 34px; border-radius: 50%;
            background: white; border: 1px solid var(--border);
            cursor: pointer; font-size: 0.85rem;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            transition: all 0.2s; color: var(--dark);
        }
        .zoom-btn:hover { background: var(--blue); color: white; border-color: var(--blue); }
        
        .loading-container { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 250px; }
        .spinner { width: 35px; height: 35px; border: 3px solid var(--border); border-top-color: var(--blue); border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        
        .empty-state { text-align: center; padding: 40px 20px; }
        .empty-state i { font-size: 2.5rem; color: #cbd5e1; margin-bottom: 8px; display: block; }
        
        .admin-fab {
            position: fixed; bottom: 20px; left: 20px;
            background: var(--grad); color: white;
            border: none; padding: 10px 16px; border-radius: 25px;
            font-size: 0.78rem; font-weight: 600;
            box-shadow: 0 5px 15px rgba(0,154,218,0.2);
            z-index: 100; text-decoration: none;
            display: inline-flex; align-items: center; gap: 5px;
        }
        
        .modal-header-gradient { background: var(--grad); color: white; border: none; }
        .detail-avatar {
            width: 110px; height: 110px;
            border-radius: 50%; object-fit: cover;
            margin: 0 auto 8px;
            border: 4px solid white;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        @media (max-width: 768px) {
            .tree-node { min-width: 100px; padding: 7px 5px 5px; }
            .tree-node.lvl-1 { min-width: 130px; }
            .tree-node.lvl-2 { min-width: 115px; }
            .tree-avatar, .avatar-placeholder { width: 32px; height: 32px; }
            .lvl-1 .tree-avatar, .lvl-1 .avatar-placeholder { width: 40px; height: 40px; }
            .tree-name { font-size: 0.58rem; }
        }
    </style>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="K-CONNECT.png" alt="K-CONNECT" height="26">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">@php getMenu(); @endphp</ul>
                @if (isLoggedIn())
                <div class="dropdown ms-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                        @if (!empty($employeePhoto))
                            <img src="uploads/employees/{{ $employeePhoto }}" width="36" height="36" class="rounded-circle me-2" alt="">
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
                        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </nav>

    <div class="page-header">
        <h1><span class="text-gradient">Organization</span> Chart</h1>
        <p style="color:var(--gray);margin-top:5px;">Reporting Structure & Hierarchy</p>
    </div>

    <div class="scroll-hint">
        <i class="fas fa-arrows-left-right me-1"></i> Scroll to view full chart &nbsp;|&nbsp;
        <i class="fas fa-mouse-pointer me-1"></i> Click card for details
    </div>

    <div class="tree-container" id="treeContainer">
        <div class="loading-container" id="loadingContainer">
            <div class="spinner"></div>
            <p style="margin-top:12px;color:var(--gray);">Loading organization chart...</p>
        </div>
        <div class="empty-state" id="emptyState" style="display:none;">
            <i class="fas fa-sitemap"></i>
            <h3>Organization Chart Not Set Up</h3>
            <p style="color:var(--gray);">The organizational structure hasn't been configured yet.</p>
        </div>
        <div class="org-tree" id="orgTree" style="display:none;"></div>
    </div>

    <div class="zoom-controls">
        <button class="zoom-btn" onclick="zoomIn()" title="Zoom In"><i class="fas fa-plus"></i></button>
        <button class="zoom-btn" onclick="zoomOut()" title="Zoom Out"><i class="fas fa-minus"></i></button>
        <button class="zoom-btn" onclick="zoomReset()" title="Reset Zoom"><i class="fas fa-expand"></i></button>
    </div>

    <div class="modal fade" id="employeeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header modal-header-gradient">
                    <h5 class="modal-title text-white">Employee Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4" id="modalBody"></div>
            </div>
        </div>
    </div>

    @if ($isAdminUser)
    <a href="admin/orgchart_manager.php" class="admin-fab">
        <i class="fas fa-cog"></i> Manage Chart
    </a>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function() {
        const API_URL = 'api/get_orgchart.php';
        const orgTree = document.getElementById('orgTree');
        const loadingContainer = document.getElementById('loadingContainer');
        const emptyState = document.getElementById('emptyState');
        let allData = [];
        let currentZoom = 1;

        async function fetchOrgChart() {
            try {
                const resp = await fetch(API_URL);
                if (!resp.ok) throw new Error('HTTP ' + resp.status);
                const result = await resp.json();
                if (!result.success) throw new Error(result.message);
                allData = result.data;
                if (allData.length === 0) { showEmptyState(); return; }
                renderTree();
            } catch(e) {
                console.error(e);
                showEmptyState();
            }
        }

        function renderTree() {
            loadingContainer.style.display = 'none';
            emptyState.style.display = 'none';
            orgTree.style.display = 'flex';
            orgTree.innerHTML = '';

            // Find roots (no parent) - these go in Level 1 row
            const roots = allData.filter(e => !e.parent_id);
            
            if (roots.length === 0) {
                // No roots - show all as flat
                const row = document.createElement('div');
                row.className = 'tree-row-l1';
                allData.forEach(emp => {
                    const div = document.createElement('div');
                    div.className = 'tree-li';
                    const card = createNodeCard(emp, true);
                    div.appendChild(card);
                    row.appendChild(div);
                });
                orgTree.appendChild(row);
            } else {
                // === LEVEL 1: Fixed row with tight spacing ===
                const level1Row = document.createElement('div');
                level1Row.className = 'tree-row-l1';
                
                roots.forEach(root => {
                    const l1Div = document.createElement('div');
                    l1Div.className = 'tree-li';
                    
                    const hasChildren = allData.some(e => e.parent_id === root.employee_id);
                    const card = createNodeCard(root, hasChildren);
                    l1Div.appendChild(card);
                    
                    // Build children tree (Level 2+) - nested inside l1Div
                    if (hasChildren) {
                        const children = allData.filter(e => e.parent_id === root.employee_id);
                        const childUl = document.createElement('ul');
                        children.forEach(child => {
                            const childLi = createNodeLi(child);
                            buildChildrenTree(childLi, child.employee_id);
                            childUl.appendChild(childLi);
                        });
                        l1Div.appendChild(childUl);
                    }
                    
                    level1Row.appendChild(l1Div);
                });
                
                orgTree.appendChild(level1Row);
            }
        }

        function buildChildrenTree(parentLi, parentId) {
            const children = allData.filter(e => e.parent_id === parentId);
            if (children.length === 0) return;
            const childUl = document.createElement('ul');
            children.forEach(child => {
                const childLi = createNodeLi(child);
                buildChildrenTree(childLi, child.employee_id);
                childUl.appendChild(childLi);
            });
            parentLi.appendChild(childUl);
        }

        function createNodeLi(employee) {
            const li = document.createElement('li');
            const hasChildren = allData.some(e => e.parent_id === employee.employee_id);
            if (hasChildren) li.classList.add('has-children');
            
            const card = createNodeCard(employee, hasChildren);
            li.appendChild(card);
            return li;
        }

        function createNodeCard(employee, hasChildren) {
            const wrapper = document.createElement('div');
            wrapper.className = 'node-wrapper';
            if (hasChildren) wrapper.parentElement?.classList?.add('has-children');

            const card = document.createElement('div');
            card.className = `tree-node lvl-${employee.level || 8}`;
            card.title = employee.full_name;
            card.addEventListener('click', () => showDetail(employee));

            if (employee.level <= 4) {
                const badge = document.createElement('span');
                badge.className = `rank-badge rank-${employee.level}`;
                badge.textContent = employee.level_label || `L${employee.level}`;
                card.appendChild(badge);
            }

            if (employee.photo) {
                const img = document.createElement('img');
                img.src = employee.photo;
                img.className = 'tree-avatar';
                img.alt = '';
                img.loading = 'lazy';
                img.onerror = function() {
                    this.style.display = 'none';
                    this.nextElementSibling.style.display = 'flex';
                };
                card.appendChild(img);
                const ph = document.createElement('div');
                ph.className = 'avatar-placeholder';
                ph.style.display = 'none';
                ph.innerHTML = '<i class="fas fa-user-tie"></i>';
                card.appendChild(ph);
            } else {
                const ph = document.createElement('div');
                ph.className = 'avatar-placeholder';
                ph.innerHTML = '<i class="fas fa-user-tie"></i>';
                card.appendChild(ph);
            }

            const nameEl = document.createElement('div');
            nameEl.className = 'tree-name';
            nameEl.textContent = employee.full_name;
            card.appendChild(nameEl);

            if (employee.position) {
                const posEl = document.createElement('div');
                posEl.className = 'tree-position';
                posEl.textContent = employee.position;
                card.appendChild(posEl);
            }

            if (employee.department) {
                const deptEl = document.createElement('div');
                deptEl.className = 'tree-dept';
                deptEl.textContent = employee.department;
                card.appendChild(deptEl);
            }

            // Mark wrapper with has-children for CSS arrow
            if (hasChildren) {
                wrapper.classList.add('has-children-marker');
            }

            wrapper.appendChild(card);
            return wrapper;
        }

        function showDetail(emp) {
            const body = document.getElementById('modalBody');
            const photoHtml = emp.photo
                ? `<img src="${emp.photo}" class="detail-avatar" alt="">`
                : `<div style="width:110px;height:110px;border-radius:50%;background:linear-gradient(135deg,rgba(0,154,218,.08),rgba(118,188,33,.08));display:flex;align-items:center;justify-content:center;margin:0 auto 10px;border:4px solid white;box-shadow:0 10px 25px rgba(0,0,0,.1);"><i class="fas fa-user-tie" style="font-size:2rem;color:var(--blue);opacity:.5;"></i></div>`;

            const managerName = emp.parent_id 
                ? (allData.find(e => e.employee_id === emp.parent_id)?.full_name || 'Unknown')
                : 'None (Top Level)';

            const levelColors = {1:'#76bc21',2:'#f59e0b',3:'#8b5cf6',4:'#3b82f6',5:'#06b6d4',6:'#14b8a6',7:'#6366f1',8:'#64748b'};

            body.innerHTML = `
                ${photoHtml}
                <h3 style="font-weight:700;margin-bottom:4px;color:#1e293b;">${emp.full_name}</h3>
                <p style="color:var(--blue);font-weight:600;margin-bottom:8px;font-size:0.85rem;">${emp.position || 'No Position'}</p>
                <span style="display:inline-block;padding:4px 12px;border-radius:20px;font-size:.75rem;font-weight:600;color:white;background:${levelColors[emp.level] || '#64748b'};">
                    ${emp.level_label || 'Level ' + emp.level}
                </span>
                <div style="background:#f8fafc;border-radius:14px;padding:16px;margin-top:16px;text-align:left;">
                    <div style="margin-bottom:7px;display:flex;gap:10px;"><i class="fas fa-id-badge" style="color:var(--blue);width:18px;"></i><strong style="min-width:75px;">Code:</strong> ${emp.employee_code}</div>
                    <div style="margin-bottom:7px;display:flex;gap:10px;"><i class="fas fa-building" style="color:var(--blue);width:18px;"></i><strong style="min-width:75px;">Company:</strong> ${emp.company_name || 'N/A'}</div>
                    <div style="margin-bottom:7px;display:flex;gap:10px;"><i class="fas fa-briefcase" style="color:var(--blue);width:18px;"></i><strong style="min-width:75px;">Dept:</strong> ${emp.department || 'N/A'}</div>
                    <div style="margin-bottom:7px;display:flex;gap:10px;"><i class="fas fa-user-tie" style="color:var(--blue);width:18px;"></i><strong style="min-width:75px;">Reports To:</strong> ${managerName}</div>
                    ${emp.email ? `<div style="display:flex;gap:10px;"><i class="fas fa-envelope" style="color:var(--blue);width:18px;"></i><strong style="min-width:75px;">Email:</strong> ${emp.email}</div>` : ''}
                </div>`;
            new bootstrap.Modal(document.getElementById('employeeModal')).show();
        }

        function showEmptyState() {
            loadingContainer.style.display = 'none';
            emptyState.style.display = 'block';
            orgTree.style.display = 'none';
        }

        window.zoomIn = () => { currentZoom = Math.min(currentZoom + 0.1, 1.5); orgTree.style.transform = `scale(${currentZoom})`; orgTree.style.transformOrigin = 'top center'; };
        window.zoomOut = () => { currentZoom = Math.max(currentZoom - 0.1, 0.5); orgTree.style.transform = `scale(${currentZoom})`; orgTree.style.transformOrigin = 'top center'; };
        window.zoomReset = () => { currentZoom = 1; orgTree.style.transform = 'scale(1)'; };

        document.addEventListener('DOMContentLoaded', fetchOrgChart);
    })();
    </script>
</body>
</html>
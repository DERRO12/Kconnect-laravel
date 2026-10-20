<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>{{ $project['title'] }} - K-Connect</title>
    
    <link rel="icon" type="image/png" href="favi.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
            --shadow-md: 0 8px 30px rgba(0,0,0,0.06);
            --shadow-lg: 0 15px 50px rgba(0,0,0,0.08);
        }
        
        * { box-sizing: border-box; }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f4f8;
            color: #1e293b;
            min-height: 100vh;
        }
        
        .project-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 24px 40px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
            background: white;
            padding: 20px 24px;
            border-radius: 20px;
            border: 1px solid #eef2f6;
            box-shadow: var(--shadow-sm);
        }
        
        .page-header .header-left { flex: 1; min-width: 200px; }
        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0 0 4px;
            color: #0f172a;
        }
        .page-header .code {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 500;
        }
        .page-header .badge-group {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 6px;
        }
        .page-header .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .text-gradient {
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0,154,218,0.25);
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,154,218,0.35);
            color: white;
        }
        .btn-gradient-sm {
            padding: 5px 12px;
            font-size: 0.75rem;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid #e2e8f0;
            color: #475569;
            padding: 7px 16px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-outline:hover {
            border-color: var(--kiboko-blue);
            color: var(--kiboko-blue);
            background: #f8faff;
        }
        
        .btn-back {
            background: transparent;
            border: 2px solid #e2e8f0;
            color: #475569;
            padding: 5px 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-back:hover {
            border-color: var(--kiboko-blue);
            color: var(--kiboko-blue);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .stat-mini {
            background: white;
            border-radius: 14px;
            padding: 14px 16px;
            border: 1px solid #eef2f6;
            text-align: center;
            transition: all 0.2s;
            box-shadow: var(--shadow-sm);
        }
        .stat-mini:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .stat-mini .number {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }
        .stat-mini .label {
            font-size: 0.65rem;
            color: #94a3b8;
            margin-top: 2px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .stat-mini .number.green { color: #16a34a; }
        .stat-mini .number.blue { color: var(--kiboko-blue); }
        .stat-mini .number.red { color: #dc2626; }
        .stat-mini .number.orange { color: #f59e0b; }
        .stat-mini .number.purple { color: #8b5cf6; }
        
        .dept-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .dept-stat-card {
            background: white;
            border-radius: 14px;
            padding: 14px 16px;
            border: 1px solid #eef2f6;
            transition: all 0.2s;
            box-shadow: var(--shadow-sm);
            cursor: pointer;
        }
        .dept-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--kiboko-blue);
        }
        .dept-stat-card.active {
            border-color: var(--kiboko-blue);
            background: #f0f9ff;
        }
        .dept-stat-card .dept-name {
            font-weight: 700;
            font-size: 0.85rem;
            color: #0f172a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .dept-stat-card .dept-name .count {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--kiboko-blue);
            background: #f0f9ff;
            padding: 2px 10px;
            border-radius: 20px;
            min-width: 50px;
            text-align: center;
        }
        .dept-stat-card .dept-name .count.done-all {
            color: #16a34a;
            background: #dcfce7;
        }
        .dept-stat-card .dept-name .count.zero {
            color: #94a3b8;
            background: #f1f5f9;
        }
        .dept-stat-card .dept-progress {
            height: 5px;
            background: #e2e8f0;
            border-radius: 3px;
            margin: 8px 0 6px;
            overflow: hidden;
        }
        .dept-stat-card .dept-progress .fill {
            height: 100%;
            border-radius: 3px;
            background: var(--grad);
            transition: width 0.6s ease;
        }
        .dept-stat-card .dept-stats {
            display: flex;
            gap: 10px;
            font-size: 0.65rem;
            color: #94a3b8;
            flex-wrap: wrap;
        }
        .dept-stat-card .dept-stats span {
            display: flex;
            align-items: center;
            gap: 3px;
        }
        .dept-stat-card .dept-stats .done { color: #16a34a; }
        .dept-stat-card .dept-stats .progress { color: var(--kiboko-blue); }
        .dept-stat-card .dept-stats .todo { color: #f59e0b; }
        .dept-stat-card .dept-stats .blocked { color: #dc2626; }
        .dept-stat-card .click-hint {
            font-size: 0.55rem;
            color: #94a3b8;
            margin-top: 4px;
            opacity: 0.5;
        }
        
        .card-custom {
            background: white;
            border-radius: 16px;
            border: 1px solid #eef2f6;
            padding: 18px 20px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s;
        }
        .card-custom:hover { box-shadow: var(--shadow-md); }
        .card-custom h4 {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #0f172a;
        }
        .card-custom h4 i { color: var(--kiboko-blue); }
        
        .filter-section {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
            padding: 8px 12px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #eef2f6;
            align-items: center;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .filter-group-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }
        .filter-group-label i { color: var(--kiboko-blue); }
        .filter-select {
            padding: 5px 28px 5px 12px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            background: white;
            font-size: 0.75rem;
            font-weight: 500;
            color: #1e293b;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            cursor: pointer;
            min-width: 110px;
            transition: all 0.2s;
        }
        .filter-select:focus {
            outline: none;
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 3px rgba(0,154,218,0.1);
        }
        .filter-select:hover { border-color: var(--kiboko-blue); }
        .filter-divider {
            width: 2px;
            height: 24px;
            background: #e2e8f0;
            border-radius: 2px;
            flex-shrink: 0;
        }
        .reset-filter-btn {
            padding: 4px 10px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            background: transparent;
            font-size: 0.65rem;
            font-weight: 600;
            color: #64748b;
            transition: all 0.2s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-left: auto;
        }
        .reset-filter-btn:hover {
            border-color: var(--kiboko-blue);
            color: var(--kiboko-blue);
            background: #f0f9ff;
        }
        .filter-badge {
            background: var(--grad);
            color: white;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 0.6rem;
            font-weight: 600;
            white-space: nowrap;
        }
        
        .task-list-wrapper {
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .task-item {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            border-bottom: 1px solid #f1f5f9;
            gap: 10px;
            transition: all 0.2s;
            background: white;
        }
        .task-item:last-child { border-bottom: none; }
        .task-item:hover { background: #f8fafc; }
        .task-item.hidden { display: none !important; }
        .task-item .task-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .task-item .task-info { flex: 1; min-width: 0; }
        .task-item .task-info .title {
            font-weight: 600;
            font-size: 0.85rem;
            color: #0f172a;
            word-break: break-word;
        }
        .task-item .task-info .meta {
            font-size: 0.7rem;
            color: #94a3b8;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            align-items: center;
            margin-top: 2px;
        }
        .task-item .task-info .meta .dept-badge {
            background: #f1f5f9;
            color: #475569;
            padding: 0 8px;
            border-radius: 12px;
            font-size: 0.6rem;
            font-weight: 600;
        }
        .task-item .task-due {
            font-size: 0.7rem;
            color: #64748b;
            flex-shrink: 0;
            white-space: nowrap;
        }
        .task-item .task-due.overdue { color: #dc2626; font-weight: 600; }
        .task-item .task-actions {
            display: flex;
            gap: 4px;
            flex-shrink: 0;
            align-items: center;
        }
        .task-item .task-actions .btn-sm {
            padding: 2px 6px;
            font-size: 0.65rem;
            border-radius: 6px;
        }
        
        .status-badge {
            font-size: 0.6rem;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-todo { background: #f1f5f9; color: #475569; }
        .status-progress { background: #dbeafe; color: #2563eb; }
        .status-review { background: #ede9fe; color: #7c3aed; }
        .status-done { background: #dcfce7; color: #16a34a; }
        .status-blocked { background: #fee2e2; color: #dc2626; }
        
        .priority-badge {
            font-size: 0.6rem;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 600;
            white-space: nowrap;
        }
        .priority-low { background: #f1f5f9; color: #475569; }
        .priority-medium { background: #dbeafe; color: #2563eb; }
        .priority-high { background: #fef3c7; color: #d97706; }
        .priority-urgent { background: #fee2e2; color: #dc2626; }
        
        .member-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.65rem;
            color: #475569;
            flex-shrink: 0;
            overflow: hidden;
        }
        .member-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .member-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .member-item:last-child { border-bottom: none; }
        .member-item .role-badge {
            font-size: 0.55rem;
            padding: 1px 8px;
            border-radius: 20px;
            font-weight: 600;
            background: #dbeafe;
            color: #1e40af;
            flex-shrink: 0;
        }
        .member-item .member-name {
            font-weight: 600;
            font-size: 0.8rem;
        }
        .member-item .member-dept {
            font-size: 0.65rem;
            color: #94a3b8;
        }
        
        .empty-state-small {
            text-align: center;
            padding: 25px 16px;
        }
        .empty-state-small i {
            font-size: 2rem;
            color: #cbd5e1;
            margin-bottom: 8px;
        }
        .empty-state-small p { color: #94a3b8; font-size: 0.85rem; margin: 0; }
        
        .alert-custom {
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 18px;
            border: none;
            font-weight: 500;
            font-size: 0.85rem;
        }
        .alert-success { background: #dcfce7; color: #166534; border-left: 4px solid #16a34a; }
        .alert-danger { background: #fee2e2; color: #991b1b; border-left: 4px solid #dc2626; }
        
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: var(--shadow-lg);
        }
        .modal-header {
            border-bottom: 1px solid #eef2f6;
            padding: 16px 20px;
        }
        .modal-header .modal-title {
            font-weight: 700;
            font-size: 1rem;
        }
        .modal-body { padding: 18px 20px; }
        .modal-footer {
            border-top: 1px solid #eef2f6;
            padding: 12px 20px;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 8px 12px;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 4px rgba(0,154,218,0.08);
        }
        .form-label {
            font-weight: 600;
            font-size: 0.75rem;
            color: #475569;
            margin-bottom: 4px;
        }
        
        @media (max-width: 992px) {
            .project-container { padding: 16px; }
            .page-header { padding: 16px 18px; flex-direction: column; }
            .page-header .header-actions { width: 100%; justify-content: flex-start; }
            .dept-stats-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
        }
        
        @media (max-width: 768px) {
            .project-container { padding: 12px; }
            .page-header h1 { font-size: 1.2rem; }
            .page-header { padding: 12px 14px; }
            .stats-grid { grid-template-columns: repeat(3, 1fr); }
            .stat-mini { padding: 10px 8px; }
            .stat-mini .number { font-size: 1.1rem; }
            .dept-stats-grid { grid-template-columns: repeat(2, 1fr); }
            .dept-stat-card { padding: 10px 12px; }
            .dept-stat-card .dept-name { font-size: 0.75rem; }
            .dept-stat-card .dept-name .count { font-size: 0.7rem; padding: 1px 8px; min-width: 35px; }
            .filter-section { flex-direction: column; align-items: stretch; gap: 6px; }
            .filter-group { width: 100%; }
            .filter-select { flex: 1; min-width: 60px; }
            .filter-divider { display: none; }
            .reset-filter-btn { margin-left: 0; }
            .task-item { flex-wrap: wrap; padding: 8px 12px; gap: 6px; }
            .task-item .task-actions { width: 100%; justify-content: flex-end; }
            .card-custom { padding: 12px 14px; }
            .btn-gradient, .btn-outline { font-size: 0.75rem; padding: 5px 12px; }
        }
        
        @media (max-width: 480px) {
            .project-container { padding: 8px; }
            .page-header { padding: 10px 12px; }
            .page-header h1 { font-size: 1rem; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 6px; }
            .stat-mini .number { font-size: 0.9rem; }
            .stat-mini .label { font-size: 0.55rem; }
            .dept-stats-grid { grid-template-columns: 1fr; }
            .task-item .task-info .title { font-size: 0.75rem; }
            .filter-select { font-size: 0.65rem; padding: 4px 24px 4px 8px; min-width: 50px; }
            .filter-badge { font-size: 0.5rem; padding: 1px 6px; }
            .task-item .task-actions .btn-sm { padding: 1px 4px; font-size: 0.55rem; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    @include('partials.navbar')

    <!-- Project Content -->
    <div class="project-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-left">
                <a href="projects-dashboard.php" class="btn-back mb-2">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
                <h1>{{ $project['title'] }}</h1>
                <div class="code">{{ $project['project_code'] }}</div>
                <div class="badge-group">
                    <span class="status-badge status-{!! strtolower(str_replace(' ', '', $project['status'])) !!}">
                        {!! $project['status'] !!}
                    </span>
                    <span class="priority-badge priority-{!! strtolower($project['priority']) !!}">
                        {!! $project['priority'] !!} Priority
                    </span>
                </div>
            </div>
            <div class="header-actions">
                <button class="btn-outline" data-bs-toggle="modal" data-bs-target="#membersModal">
                    <i class="fas fa-users"></i> <span class="d-none d-sm-inline">Team</span>
                </button>
                <button class="btn-gradient" data-bs-toggle="modal" data-bs-target="#taskModal">
                    <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add Task</span>
                </button>
            </div>
        </div>

        <!-- Flash Message -->
        @if (!empty($message))
            <div class="alert-custom alert-{!! $messageType !!}">
                <i class="fas {!! $messageType == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' !!} me-2"></i>
                {!! $message !!}
            </div>
        @endif

        <!-- Main Stats -->
        <div class="stats-grid">
            <div class="stat-mini">
                <div class="number">{!! $stats['total_tasks'] !!}</div>
                <div class="label">Total Tasks</div>
            </div>
            <div class="stat-mini">
                <div class="number green">{!! $stats['completed_tasks'] !!}</div>
                <div class="label">Completed</div>
            </div>
            <div class="stat-mini">
                <div class="number blue">{!! $stats['in_progress_tasks'] !!}</div>
                <div class="label">In Progress</div>
            </div>
            <div class="stat-mini">
                <div class="number red">{!! $stats['overdue_tasks'] !!}</div>
                <div class="label">Overdue</div>
            </div>
            <div class="stat-mini">
                <div class="number purple">{!! $stats['completion_percentage'] !!}%</div>
                <div class="label">Completion</div>
            </div>
        </div>

        <!-- Department Stats Cards -->
        @if (!empty($departmentList))
            <div class="dept-stats-grid" id="deptStatsGrid">
                @foreach ($departmentList as $dept)
@php
$stats = $deptTaskStats[$dept] ?? ['total' => 0, 'completed' => 0, 'in_progress' => 0, 'todo' => 0, 'blocked' => 0, 'completion' => 0];
                    $countClass = $stats['total'] > 0 ? ($stats['completed'] == $stats['total'] ? 'done-all' : '') : 'zero';
@endphp
                    <div class="dept-stat-card" onclick="filterByDepartment('{{ $dept }}')" 
                         data-dept="{{ $dept }}">
                        <div class="dept-name">
                            <span>{{ $dept }}</span>
                            <span class="count {!! $countClass !!}">
                                {!! $stats['completed'] !!}/{!! $stats['total'] !!}
                            </span>
                        </div>
                        <div class="dept-progress">
                            <div class="fill" style="width: {!! $stats['completion'] !!}%;"></div>
                        </div>
                        <div class="dept-stats">
                            <span class="done"><i class="fas fa-check-circle"></i> {!! $stats['completed'] !!}</span>
                            <span class="progress"><i class="fas fa-spinner"></i> {!! $stats['in_progress'] !!}</span>
                            <span class="todo"><i class="fas fa-circle"></i> {!! $stats['todo'] !!}</span>
                            @if ($stats['blocked'] > 0)
                                <span class="blocked"><i class="fas fa-ban"></i> {!! $stats['blocked'] !!}</span>
                            @endif
                        </div>
                        <div class="click-hint"><i class="fas fa-mouse-pointer"></i> Click to filter</div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="row g-4">
            <!-- Main Content - Tasks -->
            <div class="col-lg-8">
                <div class="card-custom">
                    <h4><i class="fas fa-tasks" style="color: var(--kiboko-blue);"></i> Tasks</h4>
                    
                    <!-- Filter Section -->
                    <div class="filter-section" id="filterSection">
                        <div class="filter-group">
                            <span class="filter-group-label"><i class="fas fa-filter"></i> Status:</span>
                            <select class="filter-select" id="statusFilter" onchange="applyFilters()">
                                <option value="all">All Statuses</option>
                                <option value="To Do">To Do</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Review">Review</option>
                                <option value="Done">Done</option>
                                <option value="Blocked">Blocked</option>
                            </select>
                        </div>
                        
                        <div class="filter-divider"></div>
                        
                        <div class="filter-group">
                            <span class="filter-group-label"><i class="fas fa-building"></i> Dept:</span>
                            <select class="filter-select" id="departmentFilter" onchange="applyFilters()">
                                <option value="all">All Departments</option>
                                @foreach ($departmentList as $deptName)
                                    <option value="{{ $deptName }}">
                                        {{ $deptName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button class="reset-filter-btn" onclick="resetFilters()">
                            <i class="fas fa-times"></i> Reset
                        </button>
                        
                        <span class="filter-badge" id="filterCount">Showing all tasks</span>
                    </div>

                    <!-- Task List -->
                    <div class="task-list-wrapper">
                        @if (!empty($taskData) && count($taskData) > 0)
                            @foreach ($taskData as $task)
@php
$statusClass = 'status-' . str_replace(' ', '', strtolower($task['status']));
                                $isOverdue = $task['status'] != 'Done' && !empty($task['due_date']) && strtotime($task['due_date']) < time();
                                $canUpdateStatus = ($task['assigned_to'] == $employee_id || $isAdmin);
                                $statusColor = function_exists('getStatusColor') ? getStatusColor($task['status']) : '#64748b';
@endphp
                                <div class="task-item" 
                                     data-status="{{ $task['status'] }}"
                                     data-department="{{ $task['department'] ?? '' }}">
                                    <div class="task-status-dot" style="background: {!! $statusColor !!};"></div>
                                    <div class="task-info">
                                        <div class="title">{{ $task['title'] }}</div>
                                        <div class="meta">
                                            <span>👤 {{ $task['assigned_to_name'] ?? 'Unassigned' }}</span>
                                            @if (!empty($task['department']))
                                                <span class="dept-badge">🏢 {{ $task['department'] }}</span>
                                            @endif
                                            <span class="priority-badge priority-{!! strtolower($task['priority']) !!}">
                                                {!! $task['priority'] !!}
                                            </span>
                                        </div>
                                    </div>
                                    @if ($task['due_date'])
                                        <div class="task-due {!! $isOverdue ? 'overdue' : '' !!}">
                                            <i class="fas fa-calendar-alt"></i> 
                                            {!! date('M d', strtotime($task['due_date'])) !!}
                                            @if ($isOverdue)
                                                <span class="d-none d-sm-inline" style="color:#dc2626;">(Overdue)</span>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="task-actions">
                                        <span class="status-badge {!! $statusClass !!} d-none d-sm-inline-block">
                                            {!! $task['status'] !!}
                                        </span>
                                        
                                        @if ($canUpdateStatus)
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="openStatusModal({!! $task['id'] !!}, '{!! $task['status'] !!}', '{{ addslashes($task['title']) }}')"
                                                    title="Update Status">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @else
                                            <span class="btn btn-sm btn-secondary disabled" title="Only the assigned user or admin can update this task">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        @endif
                                        
                                        @if ($isAdmin)
                                            <a href="?delete_task={!! $task['id'] !!}&id={!! $project_id !!}" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Delete this task?')"
                                               title="Delete Task">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state-small">
                                <i class="fas fa-check-circle"></i>
                                <p class="text-muted">No tasks created yet.</p>
                                <button class="btn-gradient btn-gradient-sm mt-2" data-bs-toggle="modal" data-bs-target="#taskModal">
                                    <i class="fas fa-plus"></i> Add First Task
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-2 text-muted" style="font-size:0.75rem;" id="taskCounter">
                        Showing <span id="visibleCount">{!! !empty($taskData) ? count($taskData) : 0 !!}</span> of <span id="totalCount">{!! !empty($taskData) ? count($taskData) : 0 !!}</span> tasks
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                @if (!empty($project['description']))
                    <div class="card-custom">
                        <h4><i class="fas fa-info-circle" style="color: #f59e0b;"></i> Description</h4>
                        <p style="font-size:0.85rem; color:#475569; line-height:1.6;">{!! nl2br(htmlspecialchars($project['description'])) !!}</p>
                    </div>
                @endif

                <div class="card-custom">
                    <h4><i class="fas fa-calendar-alt" style="color: #76bc21;"></i> Details</h4>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted" style="font-size:0.8rem;">Start Date</span>
                        <span style="font-weight:600; font-size:0.8rem;">{!! date('M d, Y', strtotime($project['start_date'])) !!}</span>
                    </div>
                    @if ($project['end_date'])
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted" style="font-size:0.8rem;">End Date</span>
                        <span style="font-weight:600; font-size:0.8rem;">{!! date('M d, Y', strtotime($project['end_date'])) !!}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted" style="font-size:0.8rem;">Created By</span>
                        <span style="font-weight:600; font-size:0.8rem;">{{ $project['created_by_name'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted" style="font-size:0.8rem;">Departments</span>
                        <div style="display:flex; flex-wrap:wrap; gap:4px; justify-content:flex-end;">
                            @foreach ($departmentList as $deptName)
                                <span class="badge" style="background:#f1f5f9; color:#475569; font-weight:600;">{{ $deptName }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card-custom">
                    <h4><i class="fas fa-users" style="color: #8b5cf6;"></i> Team Members</h4>
                    @if ($members && count($members) > 0)
                        @foreach ($members as $member)
                            <div class="member-item">
                                <div class="member-avatar">
                                    @if (!empty($member['photo']))
                                        <img src="uploads/employees/{{ $member['photo'] }}" alt="{{ $member['full_name'] }}">
                                    @else
                                        {!! strtoupper(substr($member['full_name'], 0, 2)) !!}
                                    @endif
                                </div>
                                <div style="flex:1; min-width:0;">
                                    <div class="member-name">{{ $member['full_name'] }}</div>
                                    <div class="member-dept">{{ $member['department'] ?? '' }}</div>
                                </div>
                                <span class="role-badge">{!! $member['role'] !!}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center" style="font-size:0.8rem;">No members added yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="taskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus-circle" style="color: var(--kiboko-blue);"></i> Add New Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_task">
                        
                        <div class="mb-3">
                            <label class="form-label">Task Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" required placeholder="Enter task title">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Task description"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Assign To <span class="text-danger">*</span></label>
                                <select class="form-select" name="assigned_to" required>
                                    <option value="">Select...</option>
                                    @foreach ($members as $member)
                                        <option value="{!! $member['employee_id'] !!}">
                                            {{ $member['full_name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department</label>
                                <select class="form-select" name="department">
                                    <option value="">None</option>
                                    @foreach ($departmentList as $deptName)
                                        <option value="{{ $deptName }}">
                                            {{ $deptName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Priority <span class="text-danger">*</span></label>
                                <select class="form-select" name="priority" required>
                                    <option value="Low">Low</option>
                                    <option value="Medium" selected>Medium</option>
                                    <option value="High">High</option>
                                    <option value="Urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="To Do">To Do</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Review">Review</option>
                                    <option value="Done">Done</option>
                                    <option value="Blocked">Blocked</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" class="form-control" name="due_date">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-gradient"><i class="fas fa-plus"></i> Add Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit" style="color: #f59e0b;"></i> Update Task Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_task_status">
                        <input type="hidden" name="task_id" id="statusTaskId">
                        
                        <p id="statusTaskTitle" class="fw-bold" style="font-size:0.9rem;"></p>
                        
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="status" id="statusSelect" required>
                                <option value="To Do">To Do</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Review">Review</option>
                                <option value="Done">Done</option>
                                <option value="Blocked">Blocked</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="Add notes about this status update..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-gradient"><i class="fas fa-save"></i> Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Members Modal -->
    <div class="modal fade" id="membersModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-users" style="color: #8b5cf6;"></i> Team Members</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if ($members && count($members) > 0)
                        @foreach ($members as $member)
                            <div class="member-item">
                                <div class="member-avatar">
                                    @if (!empty($member['photo']))
                                        <img src="uploads/employees/{{ $member['photo'] }}" alt="{{ $member['full_name'] }}">
                                    @else
                                        {!! strtoupper(substr($member['full_name'], 0, 2)) !!}
                                    @endif
                                </div>
                                <div style="flex:1; min-width:0;">
                                    <div class="member-name">{{ $member['full_name'] }}</div>
                                    <div class="member-dept">{{ $member['department'] ?? '' }}</div>
                                    <div class="member-email" style="font-size:0.65rem; color:#94a3b8;">{{ $member['email'] ?? '' }}</div>
                                </div>
                                <span class="role-badge">{!! $member['role'] !!}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center" style="font-size:0.85rem;">No members added to this project yet.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterByDepartment(dept) {
            const deptSelect = document.getElementById('departmentFilter');
            deptSelect.value = dept;
            
            document.querySelectorAll('.dept-stat-card').forEach(card => {
                card.classList.remove('active');
                if (card.dataset.dept === dept) {
                    card.classList.add('active');
                }
            });
            
            applyFilters();
        }
        
        function applyFilters() {
            const statusFilter = document.getElementById('statusFilter').value;
            const departmentFilter = document.getElementById('departmentFilter').value;
            
            const items = document.querySelectorAll('.task-item');
            let visibleCount = 0;
            
            document.querySelectorAll('.dept-stat-card').forEach(card => {
                card.classList.remove('active');
                if (departmentFilter !== 'all' && card.dataset.dept === departmentFilter) {
                    card.classList.add('active');
                }
            });
            
            items.forEach(item => {
                const status = item.dataset.status || '';
                const department = item.dataset.department || '';
                
                const statusMatch = statusFilter === 'all' || status === statusFilter;
                const deptMatch = departmentFilter === 'all' || department === departmentFilter;
                
                if (statusMatch && deptMatch) {
                    item.classList.remove('hidden');
                    item.style.display = 'flex';
                    visibleCount++;
                } else {
                    item.classList.add('hidden');
                    item.style.display = 'none';
                }
            });
            
            const total = items.length;
            document.getElementById('visibleCount').textContent = visibleCount;
            document.getElementById('totalCount').textContent = total;
            
            const badge = document.getElementById('filterCount');
            if (statusFilter === 'all' && departmentFilter === 'all') {
                badge.textContent = 'Showing all tasks';
            } else {
                let text = '';
                if (statusFilter !== 'all') text += statusFilter;
                if (departmentFilter !== 'all') {
                    if (text) text += ' in ';
                    text += departmentFilter;
                }
                if (!text) text = 'Filtered';
                badge.textContent = 'Showing: ' + text;
            }
        }
        
        function resetFilters() {
            document.getElementById('statusFilter').value = 'all';
            document.getElementById('departmentFilter').value = 'all';
            
            document.querySelectorAll('.dept-stat-card').forEach(card => {
                card.classList.remove('active');
            });
            
            applyFilters();
        }
        
        function openStatusModal(taskId, currentStatus, taskTitle) {
            document.getElementById('statusTaskId').value = taskId;
            document.getElementById('statusTaskTitle').textContent = '📌 ' + taskTitle;
            document.getElementById('statusSelect').value = currentStatus;
            new bootstrap.Modal(document.getElementById('statusModal')).show();
        }
        
        document.getElementById('departmentFilter').addEventListener('change', function() {
            const val = this.value;
            document.querySelectorAll('.dept-stat-card').forEach(card => {
                card.classList.remove('active');
                if (val !== 'all' && card.dataset.dept === val) {
                    card.classList.add('active');
                }
            });
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            applyFilters();
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Projects Dashboard - K-Connect</title>
    
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
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f4f8;
            color: #1e293b;
            min-height: 100vh;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 24px 40px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 28px;
            background: white;
            padding: 20px 24px;
            border-radius: 20px;
            border: 1px solid #eef2f6;
            box-shadow: var(--shadow-sm);
        }
        
        .page-header h1 {
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0;
        }
        
        .text-gradient {
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .welcome-text {
            color: #64748b;
            font-size: 0.95rem;
            margin: 2px 0 0;
        }
        
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 10px 22px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s;
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
            padding: 7px 16px;
            font-size: 0.8rem;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid #e2e8f0;
            color: #475569;
            padding: 8px 18px;
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }
        
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 18px 22px;
            border: 1px solid #eef2f6;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }
        .stat-card .icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 10px;
        }
        .stat-card .icon.blue { background: #dbeafe; color: #2563eb; }
        .stat-card .icon.green { background: #dcfce7; color: #16a34a; }
        .stat-card .icon.orange { background: #fef3c7; color: #d97706; }
        .stat-card .icon.red { background: #fee2e2; color: #dc2626; }
        .stat-card .icon.purple { background: #ede9fe; color: #7c3aed; }
        .stat-card .number {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }
        .stat-card .label {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-top: 2px;
            font-weight: 500;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #0f172a;
        }
        .section-title .badge {
            font-size: 0.7rem;
            padding: 3px 10px;
            border-radius: 20px;
            background: #e2e8f0;
            color: #475569;
            font-weight: 600;
        }
        
        .project-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 36px;
        }
        
        .project-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #eef2f6;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: var(--shadow-sm);
        }
        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--kiboko-blue);
        }
        .project-card-header {
            padding: 16px 18px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }
        .project-card-header .title {
            font-weight: 700;
            font-size: 1rem;
            color: #0f172a;
            margin: 0;
        }
        .project-card-header .code {
            font-size: 0.65rem;
            color: #94a3b8;
            font-weight: 600;
        }
        .project-card-body {
            padding: 14px 18px;
        }
        .project-card-body .description {
            color: #64748b;
            font-size: 0.85rem;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 8px;
        }
        .project-card-footer {
            padding: 12px 18px;
            background: #f8fafc;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.75rem;
            color: #64748b;
            flex-wrap: wrap;
            gap: 8px;
        }
        .project-card-footer .stats {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .project-card-footer .stats span {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .project-card-footer .status-text {
            font-weight: 600;
            font-size: 0.75rem;
        }
        
        .progress-bar-custom {
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            margin: 8px 0 4px;
            overflow: hidden;
        }
        .progress-bar-custom .progress-fill {
            height: 100%;
            background: var(--grad);
            border-radius: 2px;
            transition: width 0.6s ease;
        }
        
        .priority-badge {
            font-size: 0.65rem;
            padding: 2px 10px;
            border-radius: 20px;
            font-weight: 600;
            white-space: nowrap;
        }
        .priority-low { background: #f1f5f9; color: #475569; }
        .priority-medium { background: #dbeafe; color: #2563eb; }
        .priority-high { background: #fef3c7; color: #d97706; }
        .priority-urgent { background: #fee2e2; color: #dc2626; }
        
        .task-list {
            background: white;
            border-radius: 16px;
            border: 1px solid #eef2f6;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        
        .task-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s;
            gap: 12px;
        }
        .task-item:last-child { border-bottom: none; }
        .task-item:hover { background: #f8fafc; }
        .task-item .task-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .task-item .task-info { flex: 1; min-width: 0; }
        .task-item .task-info .title {
            font-weight: 600;
            font-size: 0.9rem;
            color: #0f172a;
            word-break: break-word;
        }
        .task-item .task-info .meta {
            font-size: 0.75rem;
            color: #94a3b8;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
            margin-top: 2px;
        }
        .task-item .task-info .meta .project-tag {
            background: #f1f5f9;
            color: #475569;
            padding: 0 10px;
            border-radius: 12px;
            font-size: 0.65rem;
        }
        .task-item .task-due {
            font-size: 0.75rem;
            color: #64748b;
            flex-shrink: 0;
            white-space: nowrap;
        }
        .task-item .task-due.overdue { color: #dc2626; font-weight: 600; }
        
        .task-status-badge {
            font-size: 0.65rem;
            padding: 2px 10px;
            border-radius: 20px;
            font-weight: 600;
            flex-shrink: 0;
            white-space: nowrap;
        }
        .status-todo { background: #f1f5f9; color: #475569; }
        .status-progress { background: #dbeafe; color: #2563eb; }
        .status-review { background: #ede9fe; color: #7c3aed; }
        .status-done { background: #dcfce7; color: #16a34a; }
        .status-blocked { background: #fee2e2; color: #dc2626; }
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 16px;
            border: 1px solid #eef2f6;
            box-shadow: var(--shadow-sm);
        }
        .empty-state i {
            font-size: 3.5rem;
            color: #cbd5e1;
            margin-bottom: 16px;
        }
        .empty-state h3 {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
            font-size: 1.3rem;
        }
        .empty-state p {
            color: #94a3b8;
            max-width: 400px;
            margin: 0 auto 16px;
            font-size: 0.9rem;
        }
        
        @media (max-width: 992px) {
            .dashboard-container { padding: 16px; }
            .page-header { padding: 16px 20px; }
            .page-header h1 { font-size: 1.5rem; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (max-width: 768px) {
            .dashboard-container { padding: 12px; }
            .page-header { flex-direction: column; align-items: stretch; gap: 12px; }
            .page-header h1 { font-size: 1.3rem; }
            .page-header .header-actions { width: 100%; }
            .page-header .header-actions .btn-gradient { width: 100%; justify-content: center; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .stat-card { padding: 14px 16px; }
            .stat-card .number { font-size: 1.4rem; }
            .stat-card .icon { width: 36px; height: 36px; font-size: 1rem; }
            .project-grid { grid-template-columns: 1fr; gap: 14px; }
            .task-item { flex-wrap: wrap; gap: 8px; padding: 10px 14px; }
            .task-item .task-due { width: auto; }
            .section-title { font-size: 1rem; }
        }
        
        @media (max-width: 480px) {
            .dashboard-container { padding: 8px; }
            .page-header { padding: 12px 14px; }
            .page-header h1 { font-size: 1.1rem; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
            .stat-card { padding: 10px 12px; }
            .stat-card .number { font-size: 1.2rem; }
            .stat-card .label { font-size: 0.65rem; }
            .stat-card .icon { width: 30px; height: 30px; font-size: 0.85rem; margin-bottom: 6px; }
            .project-card-header .title { font-size: 0.9rem; }
            .project-card-body .description { font-size: 0.8rem; }
            .project-card-footer { flex-direction: column; align-items: stretch; gap: 6px; }
            .task-item .task-info .title { font-size: 0.8rem; }
            .task-status-badge { font-size: 0.55rem; padding: 1px 8px; }
            .btn-gradient { font-size: 0.8rem; padding: 8px 16px; }
            .empty-state i { font-size: 2.5rem; }
            .empty-state h3 { font-size: 1.1rem; }
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .project-card, .stat-card, .task-item {
            animation: fadeInUp 0.4s ease forwards;
        }
        .project-card:nth-child(1) { animation-delay: 0.05s; }
        .project-card:nth-child(2) { animation-delay: 0.1s; }
        .project-card:nth-child(3) { animation-delay: 0.15s; }
        .project-card:nth-child(4) { animation-delay: 0.2s; }
    </style>
</head>
<body>
    <!-- Navigation -->
    @include('partials.navbar')

    <!-- Dashboard -->
    <div class="dashboard-container">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1><span class="text-gradient">Projects</span> Dashboard</h1>
                <p class="welcome-text">👋 Welcome back, {{ $employee_name }}!</p>
            </div>
            <div class="header-actions d-flex gap-2 flex-wrap">
                @if ($canCreateProjects)
                    <a href="projects-admin" class="btn-gradient">
                        <i class="fas fa-plus-circle"></i> New Project
                    </a>
                @endif
            </div>
        </div>

        @if (request()->query('msg') === 'project_created')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Project created successfully! Added members have been notified by e-mail.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon blue"><i class="fas fa-project-diagram"></i></div>
                <div class="number">{!! $totalProjects !!}</div>
                <div class="label">Active Projects</div>
            </div>
            <div class="stat-card">
                <div class="icon orange"><i class="fas fa-tasks"></i></div>
                <div class="number">{!! $pendingTasks !!}</div>
                <div class="label">Pending Tasks</div>
            </div>
            <div class="stat-card">
                <div class="icon green"><i class="fas fa-check-circle"></i></div>
                <div class="number">{!! $completedTasks !!}</div>
                <div class="label">Completed Tasks</div>
            </div>
            <div class="stat-card">
                <div class="icon red"><i class="fas fa-clock"></i></div>
                <div class="number">{!! $overdueTasks !!}</div>
                <div class="label">Overdue Tasks</div>
            </div>
        </div>

        <!-- Active Projects -->
        <h3 class="section-title">
            <i class="fas fa-rocket" style="color: #009ada;"></i> Your Active Projects
            <span class="badge">{!! $totalProjects !!}</span>
        </h3>

        @if ($totalProjects > 0)
            <div class="project-grid">
                @foreach ($allProjects as $project)
@php
$total = $project['task_count'] ?? 0;
                    $completed = $project['completed_count'] ?? 0;
                    $progress = $total > 0 ? round(($completed / $total) * 100) : 0;
                    $statusColor = getStatusColor($project['status']);
                    $priorityClass = 'priority-' . strtolower($project['priority']);
@endphp
                    <div class="project-card" onclick="window.location.href='project-detail?id={!! $project['id'] !!}'">
                        <div class="project-card-header">
                            <div>
                                <div class="title">{{ $project['title'] }}</div>
                                <div class="code">{{ $project['project_code'] }}</div>
                            </div>
                            <span class="priority-badge {!! $priorityClass !!}">
                                {!! ucfirst($project['priority']) !!}
                            </span>
                        </div>
                        <div class="project-card-body">
                            @if (!empty($project['description']))
                                <div class="description">{{ $project['description'] }}</div>
                            @endif
                            <div class="progress-bar-custom">
                                <div class="progress-fill" style="width: {!! $progress !!}%;"></div>
                            </div>
                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px;">
                                {!! $completed !!} / {!! $total !!} tasks completed ({!! $progress !!}%)
                            </div>
                        </div>
                        <div class="project-card-footer">
                            <div class="stats">
                                <span><i class="fas fa-calendar-alt"></i> {!! date('M d', strtotime($project['start_date'])) !!}</span>
                                @if ($project['end_date'])
                                    <span><i class="fas fa-flag-checkered"></i> {!! date('M d', strtotime($project['end_date'])) !!}</span>
                                @endif
                                <span><i class="fas fa-users"></i> {!! $project['member_count'] ?? 0 !!}</span>
                            </div>
                            <span class="status-text" style="color: {!! $statusColor !!};">
                                {!! $project['status'] !!}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>No Active Projects</h3>
                <p>You don't have any active projects assigned to you yet.</p>
                @if ($canCreateProjects)
                    <a href="projects-admin" class="btn-gradient btn-gradient-sm">
                        <i class="fas fa-plus"></i> Create Your First Project
                    </a>
                @endif
            </div>
        @endif

        <!-- My Tasks -->
        <h3 class="section-title">
            <i class="fas fa-clipboard-list" style="color: #76bc21;"></i> My Tasks
            <span class="badge">{!! $totalTasks !!}</span>
        </h3>

        @if ($totalTasks > 0)
            <div class="task-list">
                @foreach ($myTasks as $task)
@php
                    $statusClass = 'status-' . str_replace(' ', '', strtolower($task['status']));
                    $isOverdue = $task['status'] != 'Done' && !empty($task['due_date']) && strtotime($task['due_date']) < time();
                    $statusColor = getStatusColor($task['status']);
                
@endphp
                    <div class="task-item">
                        <div class="task-status-dot" style="background: {!! $statusColor !!};"></div>
                        <div class="task-info">
                            <div class="title">{{ $task['title'] }}</div>
                            <div class="meta">
                                @if ($task['project_title'])
                                    <span class="project-tag">📁 {{ $task['project_title'] }}</span>
                                @endif
                                @if ($task['assigned_by_name'])
                                    <span>👤 {{ $task['assigned_by_name'] }}</span>
                                @endif
                                @if (!empty($task['department']))
                                    <span>🏢 {{ $task['department'] }}</span>
                                @endif
                            </div>
                        </div>
                        @if ($task['due_date'])
                            <div class="task-due {!! $isOverdue ? 'overdue' : '' !!}">
                                <i class="fas fa-calendar-alt"></i> 
                                {!! date('M d', strtotime($task['due_date'])) !!}
                            </div>
                        @endif
                        <span class="task-status-badge {!! $statusClass !!}">
                            {!! $task['status'] !!}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state" style="padding: 30px 20px;">
                <i class="fas fa-check-double" style="font-size: 2.5rem;"></i>
                <p style="margin: 0; font-size: 0.9rem;">You have no tasks assigned to you. 🎉</p>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide any flash messages after 5 seconds
        document.querySelectorAll('.alert').forEach(el => {
            setTimeout(() => {
                if (el) el.style.display = 'none';
            }, 5000);
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Logs - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .main-content { margin-left: 280px; padding: 24px 32px; min-height: 100vh; }
        
        .page-header {
            background: white;
            border-radius: 16px;
            padding: 20px 25px;
            margin-bottom: 24px;
            border: 1px solid #eef2f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .page-header h2 { font-weight: 700; color: #1e293b; margin: 0; font-size: 1.5rem; }
        
        .stat-mini {
            background: white;
            border-radius: 14px;
            padding: 16px;
            text-align: center;
            border: 1px solid #eef2f6;
            transition: all 0.3s;
        }
        .stat-mini:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.05); transform: translateY(-2px); }
        .stat-mini h3 { font-size: 1.5rem; font-weight: 800; margin-bottom: 4px; }
        .stat-mini p { font-size: 0.78rem; color: #64748b; margin: 0; }
        
        .card-custom {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #eef2f6;
            margin-bottom: 20px;
        }
        .card-custom h5 {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 2px solid #eef2f6;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-custom h5 i { color: #009ada; }
        
        .filter-bar {
            background: white;
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 20px;
            border: 1px solid #eef2f6;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 10px 14px;
            font-size: 0.85rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #009ada;
            box-shadow: 0 0 0 3px rgba(0,154,218,0.1);
        }
        
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,154,218,0.2); color: white; }
        .btn-outline { background: white; border: 1px solid #e2e8f0; color: #475569; padding: 10px 20px; border-radius: 10px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 0.85rem; transition: all 0.3s; }
        .btn-outline:hover { border-color: #009ada; color: #009ada; }
        .btn-danger-outline { background: white; border: 1px solid #fecaca; color: #dc2626; padding: 8px 16px; border-radius: 10px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 0.8rem; cursor: pointer; transition: all 0.3s; }
        .btn-danger-outline:hover { background: #dc2626; color: white; border-color: #dc2626; }
        
        .log-table { width: 100%; border-collapse: separate; border-spacing: 0 4px; }
        .log-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 14px;
            border: none;
            white-space: nowrap;
        }
        .log-table tbody tr {
            background: white;
            transition: all 0.2s;
        }
        .log-table tbody tr:hover { background: #f8fafc; transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .log-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.8rem;
            vertical-align: middle;
        }
        
        .badge-log { padding: 3px 8px; border-radius: 15px; font-size: 0.68rem; font-weight: 600; }
        .badge-login { background: #dcfce7; color: #16a34a; }
        .badge-logout { background: #fee2e2; color: #dc2626; }
        .badge-page_view { background: #dbeafe; color: #2563eb; }
        .badge-create { background: #fef3c7; color: #d97706; }
        .badge-update { background: #e0e7ff; color: #4f46e5; }
        .badge-delete { background: #fee2e2; color: #991b1b; }
        .badge-registration { background: #d1fae5; color: #065f46; }
        .badge-admin { background: #e0e7ff; color: #3730a3; }
        .badge-employee { background: #dcfce7; color: #166534; }
        
        .pagination { gap: 4px; }
        .pagination .page-link {
            border-radius: 8px; border: 1px solid #e2e8f0;
            color: #475569; font-weight: 500; font-size: 0.85rem; padding: 8px 14px;
        }
        .pagination .page-link:hover { background: var(--grad); color: white; border-color: transparent; }
        .pagination .active .page-link { background: var(--grad); color: white; border-color: transparent; }
        
        .ip-badge {
            background: #f1f5f9; padding: 2px 8px; border-radius: 4px;
            font-family: 'Courier New', monospace; font-size: 0.7rem; color: #475569;
        }
        
        .activity-bar {
            display: flex; align-items: flex-end; gap: 2px; height: 40px;
        }
        .activity-bar-item {
            flex: 1; background: var(--grad); border-radius: 2px 2px 0 0;
            min-height: 2px; transition: all 0.3s; cursor: pointer;
        }
        .activity-bar-item:hover { opacity: 0.8; }
        
        .message-alert {
            padding: 12px 18px; border-radius: 10px; margin-bottom: 20px;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .message-alert.success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        
        .user-activity-card {
            display: flex; align-items: center; gap: 10px; padding: 10px;
            background: #f8fafc; border-radius: 10px; margin-bottom: 8px; transition: all 0.2s;
        }
        .user-activity-card:hover { background: #eef2ff; }
        
        .recent-login-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            background: #f8fafc;
            border-radius: 10px;
            margin-bottom: 8px;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .recent-login-item:hover {
            background: #eef2ff;
            border-left-color: #009ada;
        }
        .recent-login-item .avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            object-fit: cover;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #64748b;
            font-size: 1rem;
        }
        .recent-login-item .info { flex: 1; min-width: 0; }
        .recent-login-item .info .name { font-weight: 600; font-size: 0.85rem; color: #1e293b; }
        .recent-login-item .info .detail { font-size: 0.7rem; color: #64748b; }
        .recent-login-item .time { font-size: 0.7rem; color: #94a3b8; white-space: nowrap; }
        .online-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }
        .online-indicator.online { background: #22c55e; }
        .online-indicator.offline { background: #94a3b8; }
        
        .new-employee-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            background: #f8fafc;
            border-radius: 10px;
            margin-bottom: 8px;
            transition: all 0.2s;
            border-left: 3px solid #76bc21;
        }
        .new-employee-card:hover { background: #f0fdf4; }
        .new-employee-card .avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            object-fit: cover;
            background: #d1fae5;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #065f46;
            font-size: 1rem;
        }
        .new-employee-card .info { flex: 1; min-width: 0; }
        .new-employee-card .info .name { font-weight: 600; font-size: 0.85rem; color: #1e293b; }
        .new-employee-card .info .detail { font-size: 0.7rem; color: #64748b; }
        .new-employee-card .badge-new {
            background: #dcfce7;
            color: #166534;
            font-size: 0.6rem;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 600;
        }
        
        .activity-feed-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .activity-feed-item:last-child { border-bottom: none; }
        .activity-feed-item .activity-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.8rem;
        }
        .activity-feed-item .activity-content { flex: 1; min-width: 0; }
        .activity-feed-item .activity-content .text { font-size: 0.8rem; color: #1e293b; }
        .activity-feed-item .activity-content .text strong { color: #0f172a; }
        .activity-feed-item .activity-content .meta { font-size: 0.7rem; color: #94a3b8; }
        
        .badge-role {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.6rem;
            font-weight: 600;
        }
        .badge-role.admin { background: #fee2e2; color: #991b1b; }
        .badge-role.employee { background: #dcfce7; color: #166534; }
        .badge-role.hr { background: #dbeafe; color: #1e40af; }
        .badge-role.manager { background: #fef3c7; color: #92400e; }
        .badge-role.director { background: #ede9fe; color: #5b21b6; }
        
        .status-badge-sm {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.6rem;
            font-weight: 600;
        }
        .status-badge-sm.active { background: #dcfce7; color: #166534; }
        .status-badge-sm.inactive { background: #fee2e2; color: #991b1b; }
        
        .scrollable-list {
            max-height: 400px;
            overflow-y: auto;
        }
        .scrollable-list::-webkit-scrollbar { width: 4px; }
        .scrollable-list::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .scrollable-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        
        .activity-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 8px;
            margin-bottom: 12px;
        }
        .activity-stat-grid .stat-box {
            background: #f8fafc;
            padding: 10px 12px;
            border-radius: 8px;
            text-align: center;
        }
        .activity-stat-grid .stat-box .num {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
        }
        .activity-stat-grid .stat-box .label {
            font-size: 0.65rem;
            color: #64748b;
        }
        
        @media (max-width: 992px) {
            .main-content { margin-left: 0; padding: 16px; }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="page-header">
            <div>
                <h2><i class="fas fa-history me-2" style="color: #009ada;"></i>System Logs</h2>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Track all login activity, employee registrations, and system actions</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn-outline" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>

        @if (isset($session_message))
        <div class="message-alert {!! $session_message_type !!}">
            <i class="fas fa-check-circle me-2"></i>{!! $session_message !!}
            <button class="btn-close float-end" onclick="this.parentElement.remove()"></button>
        </div>
        @endif

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-mini">
                    <h3 class="text-primary">{!! $totalLogEntries !!}</h3>
                    <p>Total Log Entries</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-mini">
                    <h3 class="text-success">{!! $todayActivity !!}</h3>
                    <p>Today's Activity</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-mini">
                    <h3 class="text-info">{!! $todayData['today_logins'] ?? 0 !!}</h3>
                    <p>Logins Today</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-mini">
                    <h3 class="text-warning">{!! $todayData['today_registrations'] ?? 0 !!}</h3>
                    <p>New Registrations Today</p>
                </div>
            </div>
        </div>

        <!-- Today's Activity Summary -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card-custom">
                    <h5><i class="fas fa-chart-simple"></i> Today's Activity Summary</h5>
                    <div class="activity-stat-grid">
                        <div class="stat-box">
                            <div class="num">{!! $todayData['today_logins'] ?? 0 !!}</div>
                            <div class="label"><i class="fas fa-sign-in-alt me-1"></i>Logins</div>
                        </div>
                        <div class="stat-box">
                            <div class="num">{!! $todayData['today_registrations'] ?? 0 !!}</div>
                            <div class="label"><i class="fas fa-user-plus me-1"></i>Registrations</div>
                        </div>
                        <div class="stat-box">
                            <div class="num">{!! $todayData['today_page_views'] ?? 0 !!}</div>
                            <div class="label"><i class="fas fa-eye me-1"></i>Page Views</div>
                        </div>
                        <div class="stat-box">
                            <div class="num">{!! $todayData['unique_active_users'] ?? 0 !!}</div>
                            <div class="label"><i class="fas fa-users me-1"></i>Active Users</div>
                        </div>
                        <div class="stat-box">
                            <div class="num">{!! $todayData['total_today_activity'] ?? 0 !!}</div>
                            <div class="label"><i class="fas fa-bolt me-1"></i>Total Actions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Main Logs -->
            <div class="col-lg-8">
                <!-- Filter Bar -->
                <div class="filter-bar">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold mb-1">Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold mb-1">Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold mb-1">User Type</label>
                            <select name="user_type" class="form-select">
                                <option value="">All Users</option>
                                <option value="admin" {!! $userType === 'admin' ? 'selected' : '' !!}>Admin Users</option>
                                <option value="employee" {!! $userType === 'employee' ? 'selected' : '' !!}>Employees</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold mb-1">Action</label>
                            <select name="action" class="form-select">
                                <option value="">All Actions</option>
                                <option value="login" {!! $actionFilter === 'login' ? 'selected' : '' !!}>Login</option>
                                <option value="logout" {!! $actionFilter === 'logout' ? 'selected' : '' !!}>Logout</option>
                                <option value="page_view" {!! $actionFilter === 'page_view' ? 'selected' : '' !!}>Page View</option>
                                <option value="create" {!! $actionFilter === 'create' ? 'selected' : '' !!}>Create</option>
                                <option value="update" {!! $actionFilter === 'update' ? 'selected' : '' !!}>Update</option>
                                <option value="delete" {!! $actionFilter === 'delete' ? 'selected' : '' !!}>Delete</option>
                                <option value="registration" {!! $actionFilter === 'registration' ? 'selected' : '' !!}>Registration</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold mb-1">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Name, username..." value="{{ $searchTerm }}">
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn-gradient flex-fill">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="system-logs.php" class="btn-outline flex-fill" style="justify-content:center;">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Main Logs Table -->
                <div class="card-custom">
                    <h5>
                        <i class="fas fa-list-alt"></i> Activity Logs
                        <span class="badge bg-secondary ms-2">{!! $totalLogs !!} entries</span>
                        <span class="ms-auto small text-muted">Page {!! $page !!} of {!! max(1, $totalPages) !!}</span>
                    </h5>
                    <div class="table-responsive">
                        <table class="log-table">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                    <th>Page / Details</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($logsResult && count($logsResult) > 0)
                                    @foreach ($logsResult as $log)
@php
$actionBadgeClass = 'badge-' . $log['action'];
                                        $typeBadgeClass = $log['user_type'] === 'admin' ? 'badge-admin' : 'badge-employee';
                                        $actionIcon = [
                                            'login' => 'fa-sign-in-alt',
                                            'logout' => 'fa-sign-out-alt',
                                            'page_view' => 'fa-eye',
                                            'create' => 'fa-plus-circle',
                                            'update' => 'fa-edit',
                                            'delete' => 'fa-trash',
                                            'registration' => 'fa-user-plus'
                                        ][$log['action']] ?? 'fa-circle';
@endphp
                                        <tr>
                                            <td>
                                                <span class="fw-bold">{!! date('H:i:s', strtotime($log['created_at'])) !!}</span>
                                                <br><small class="text-muted">{!! date('M d, Y', strtotime($log['created_at'])) !!}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $log['full_name'] ?? $log['username'] ?? 'Unknown' }}</strong>
                                                @if ($log['username'])
                                                    <br><small class="text-muted">{!! '@' !!}{{ $log['username'] }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge-log {!! $typeBadgeClass !!}">
                                                    {!! ucfirst($log['user_type']) !!}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-log {!! $actionBadgeClass !!}">
                                                    <i class="fas {!! $actionIcon !!} me-1"></i>
                                                    {!! ucfirst(str_replace('_', ' ', $log['action'])) !!}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($log['page_accessed'])
                                                    <code style="font-size: 0.75rem;">{{ $log['page_accessed'] }}</code>
                                                @endif
                                                @if ($log['details'])
                                                    <br><small class="text-muted">{{ substr($log['details'], 0, 100) }}{!! strlen($log['details']) > 100 ? '...' : '' !!}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="ip-badge">{{ $log['ip_address'] ?? 'N/A' }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>No log entries found matching your criteria.</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if ($totalPages > 1)
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            @if ($page > 1)
                                <li class="page-item">
                                    <a class="page-link" href="?page={!! $page - 1 !!}&date_from={!! urlencode($dateFrom) !!}&date_to={!! urlencode($dateTo) !!}&user_type={!! urlencode($userType) !!}&action={!! urlencode($actionFilter) !!}&search={!! urlencode($searchTerm) !!}">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            @endif
                            @for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++)
                                <li class="page-item {!! $i === $page ? 'active' : '' !!}">
                                    <a class="page-link" href="?page={!! $i !!}&date_from={!! urlencode($dateFrom) !!}&date_to={!! urlencode($dateTo) !!}&user_type={!! urlencode($userType) !!}&action={!! urlencode($actionFilter) !!}&search={!! urlencode($searchTerm) !!}">{!! $i !!}</a>
                                </li>
                            @endfor
                            @if ($page < $totalPages)
                                <li class="page-item">
                                    <a class="page-link" href="?page={!! $page + 1 !!}&date_from={!! urlencode($dateFrom) !!}&date_to={!! urlencode($dateTo) !!}&user_type={!! urlencode($userType) !!}&action={!! urlencode($actionFilter) !!}&search={!! urlencode($searchTerm) !!}">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </nav>
                    @endif
                </div>
            </div>

            <!-- Right Column: Recent Logins, New Employees, Activity Feed -->
            <div class="col-lg-4">
                <!-- Recently Logged In -->
                <div class="card-custom">
                    <h5>
                        <i class="fas fa-clock"></i> Recently Logged In
                        <span class="badge bg-primary ms-2">{!! count($recentLogins) !!}</span>
                    </h5>
                    <div class="scrollable-list">
                        @if ($recentLogins && count($recentLogins) > 0)
                            @php
 $isFirst = true; 
@endphp
                            @foreach ($recentLogins as $user)
                                <div class="recent-login-item" style="{!! $isFirst ? 'border-left-color: #22c55e;' : '' !!}">
                                    @if ($user['photo'])
                                        <img src="../uploads/employees/{{ $user['photo'] }}" class="avatar" alt="{{ $user['full_name'] }}">
                                    @else
                                        <div class="avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                    <div class="info">
                                        <div class="name">
                                            {{ $user['full_name'] ?? $user['username'] ?? 'Unknown' }}
                                            @if ($isFirst)
                                                <span class="badge-new" style="background:#dcfce7;color:#166534;font-size:0.6rem;padding:1px 6px;border-radius:10px;font-weight:600;margin-left:4px;">
                                                    <i class="fas fa-circle" style="font-size:0.4rem;color:#22c55e;"></i> Active
                                                </span>
                                            @endif
                                        </div>
                                        <div class="detail">
                                            {{ $user['employee_code'] ?? '' }}
                                            @if ($user['department'])
                                                • {{ $user['department'] }}
                                            @endif
                                            @if ($user['role'])
                                                <span class="badge-role {!! $user['role'] !!}">{!! ucfirst($user['role']) !!}</span>
                                            @endif
                                            <br>
                                            <span class="ip-badge">{{ $user['ip_address'] ?? 'N/A' }}</span>
                                            @if ($user['today_logins'] > 1)
                                                <span class="text-muted" style="font-size:0.6rem;">{!! $user['today_logins'] !!}x today</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="time">
                                        {!! date('H:i', strtotime($user['login_time'])) !!}
                                        <br>
                                        <small>{!! \App\Support\ProjectsTimeAgo::timeAgo($user['login_time']) !!}</small>
                                    </div>
                                </div>
                            @php $isFirst = false; @endphp
                            @endforeach
                        @else
                            <div class="text-center py-3 text-muted">No recent logins</div>
                        @endif
                    </div>
                </div>

                <!-- Newly Joined Employees -->
                <div class="card-custom">
                    <h5>
                        <i class="fas fa-user-plus"></i> Newly Joined Employees
                        <span class="badge bg-success ms-2">{!! count($newEmployees) !!}</span>
                    </h5>
                    <div class="scrollable-list">
                        @if ($newEmployees && count($newEmployees) > 0)
                            @foreach ($newEmployees as $emp)
                                <div class="new-employee-card">
                                    @if ($emp['photo'])
                                        <img src="../uploads/employees/{{ $emp['photo'] }}" class="avatar" alt="{{ $emp['full_name'] }}">
                                    @else
                                        <div class="avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                    <div class="info">
                                        <div class="name">
                                            {{ $emp['full_name'] }}
                                            @if (strtotime($emp['registration_date']) > strtotime('-7 days'))
                                                <span class="badge-new">New</span>
                                            @endif
                                        </div>
                                        <div class="detail">
                                            {{ $emp['employee_code'] }}
                                            @if ($emp['department'])
                                                • {{ $emp['department'] }}
                                            @endif
                                            @if ($emp['position'])
                                                • {{ $emp['position'] }}
                                            @endif
                                            <br>
                                            <span class="status-badge-sm {!! $emp['status'] ? 'active' : 'inactive' !!}">
                                                {!! $emp['status'] ? 'Active' : 'Inactive' !!}
                                            </span>
                                            @if ($emp['role'])
                                                <span class="badge-role {!! $emp['role'] !!}">{!! ucfirst($emp['role']) !!}</span>
                                            @endif
                                            <span class="text-muted" style="font-size:0.6rem;">
                                                Joined: {!! date('M d, Y', strtotime($emp['registration_date'])) !!}
                                                @if ($emp['last_action'])
                                                    • Last: {!! ucfirst(str_replace('_', ' ', $emp['last_action'])) !!}
                                                    <span class="text-muted">{!! \App\Support\ProjectsTimeAgo::timeAgo($emp['last_action_time']) !!}</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span style="font-size:0.7rem;color:#64748b;">
                                            {!! $emp['total_activity'] !!} actions
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3 text-muted">No new employees</div>
                        @endif
                    </div>
                </div>

                <!-- Recent Activity Feed -->
                <div class="card-custom">
                    <h5>
                        <i class="fas fa-stream"></i> Recent Activity Feed
                        <span class="badge bg-secondary ms-2">{!! count($recentActivity) !!}</span>
                    </h5>
                    <div class="scrollable-list" style="max-height:300px;">
                        @if ($recentActivity && count($recentActivity) > 0)
                            @php
 
                            $actionColors = [
                                'login' => '#dcfce7',
                                'logout' => '#fee2e2',
                                'page_view' => '#dbeafe',
                                'registration' => '#d1fae5'
                            ];
                            $actionIcons = [
                                'login' => 'fa-sign-in-alt',
                                'logout' => 'fa-sign-out-alt',
                                'page_view' => 'fa-eye',
                                'registration' => 'fa-user-plus'
                            ];
                            
@endphp
                            @foreach ($recentActivity as $activity)
@php
$color = $actionColors[$activity['action']] ?? '#f1f5f9';
                                $icon = $actionIcons[$activity['action']] ?? 'fa-circle';
@endphp
                                <div class="activity-feed-item">
                                    <div class="activity-icon" style="background:{!! $color !!};color:{!! $activity['action'] === 'login' ? '#16a34a' : ($activity['action'] === 'logout' ? '#dc2626' : '#2563eb') !!}">
                                        <i class="fas {!! $icon !!}"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="text">
                                            <strong>{{ $activity['full_name'] ?? $activity['username'] ?? 'Unknown' }}</strong>
                                            @php
 
                                            $actionDisplay = [
                                                'login' => 'logged in',
                                                'logout' => 'logged out',
                                                'page_view' => 'viewed a page',
                                                'registration' => 'registered'
                                            ];
                                            echo $actionDisplay[$activity['action']] ?? $activity['action'];
                                            
@endphp
                                            @if ($activity['page_accessed'])
                                                <code style="font-size:0.65rem;">{{ $activity['page_accessed'] }}</code>
                                            @endif
                                        </div>
                                        <div class="meta">
                                            {!! date('M d, Y H:i', strtotime($activity['created_at'])) !!}
                                            <span class="text-muted">• {!! \App\Support\ProjectsTimeAgo::timeAgo($activity['created_at']) !!}</span>
                                            @if ($activity['ip_address'])
                                                <span class="ip-badge">{{ $activity['ip_address'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3 text-muted">No recent activity</div>
                        @endif
                    </div>
                </div>

                <!-- Maintenance -->
                <div class="card-custom">
                    <h5><i class="fas fa-broom"></i> Maintenance</h5>
                    <p class="text-muted small">Clear log entries older than a specified number of days.</p>
                    <form method="POST" class="d-flex gap-2" onsubmit="return confirm('Delete old log entries? This cannot be undone.');">
                        @csrf
                        <select name="days" class="form-select" style="width:auto;">
                            <option value="7">7 days</option>
                            <option value="30" selected>30 days</option>
                            <option value="60">60 days</option>
                            <option value="90">90 days</option>
                            <option value="180">180 days</option>
                            <option value="365">1 year</option>
                        </select>
                        <button type="submit" name="clear_logs" class="btn-danger-outline">
                            <i class="fas fa-trash-alt"></i> Clear
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh every 60 seconds
        setTimeout(function() {
            location.reload();
        }, 60000);
    </script>
</body>
</html>

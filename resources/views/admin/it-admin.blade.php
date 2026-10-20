<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Analytics Dashboard – K-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root { --kiboko-green:#76bc21;--kiboko-blue:#009ada;--grad:linear-gradient(135deg,#009ada 0%,#76bc21 100%);--dark:#0f172a;--bg:#f1f5f9;--card:#ffffff;--border:#e2e8f0;--text:#334155;--muted:#94a3b8; }
        * { margin:0;padding:0;box-sizing:border-box; }
        body { font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text); }
        .main-content { margin-left:280px;padding:20px 28px;min-height:100vh; }
        .page-header { background:var(--card);border-radius:20px;padding:20px 28px;margin-bottom:20px;border:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px; }
        .kpi-row { display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px;margin-bottom:20px; }
        .kpi-card { background:var(--card);border-radius:16px;padding:18px 20px;border:1px solid var(--border);transition:all .25s;position:relative;overflow:hidden; }
        .kpi-card:hover { transform:translateY(-3px);box-shadow:0 8px 25px rgba(0,0,0,.07); }
        .kpi-card .kpi-icon { font-size:22px;margin-bottom:8px; }
        .kpi-card .kpi-value { font-size:32px;font-weight:800;color:var(--dark);line-height:1.1; }
        .kpi-card .kpi-label { font-size:12px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px; }
        .kpi-card .kpi-sub { font-size:11px;color:var(--muted);margin-top:4px; }
        .kpi-accent { position:absolute;top:0;left:0;width:4px;height:100%;border-radius:4px 0 0 4px; }
        .kpi-accent.blue { background:var(--kiboko-blue); }.kpi-accent.green { background:#16a34a; }.kpi-accent.red { background:#dc2626; }.kpi-accent.orange { background:#f59e0b; }.kpi-accent.purple { background:#8b5cf6; }
        .chart-card { background:var(--card);border-radius:16px;padding:20px;border:1px solid var(--border);margin-bottom:18px; }
        .chart-title { font-weight:700;font-size:15px;display:flex;align-items:center;gap:8px;margin-bottom:16px; }
        .grid-2 { display:grid;grid-template-columns:1fr 1fr;gap:18px; }
        .grid-3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px; }
        .grid-2-1 { display:grid;grid-template-columns:2fr 1fr;gap:18px; }
        .admin-card { background:var(--card);border-radius:14px;padding:16px;border:1px solid var(--border);transition:all .25s;display:flex;align-items:center;gap:14px;cursor:pointer; }
        .admin-card:hover { box-shadow:0 4px 18px rgba(0,0,0,.06);border-color:var(--kiboko-blue); }
        .admin-card.selected { border:2px solid var(--kiboko-blue);background:#f0f9ff; }
        .admin-avatar { width:44px;height:44px;border-radius:12px;flex-shrink:0;background:linear-gradient(135deg,rgba(0,154,218,.15),rgba(118,188,33,.15));display:flex;align-items:center;justify-content:center;overflow:hidden; }
        .admin-avatar img { width:100%;height:100%;object-fit:cover; }
        .admin-avatar i { color:var(--kiboko-blue);font-size:18px; }
        .leaderboard-table { width:100%;border-collapse:collapse; }
        .leaderboard-table th { background:#f8fafc;color:var(--muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;padding:10px 12px;text-align:center; }
        .leaderboard-table td { padding:10px 12px;text-align:center;border-bottom:1px solid #f1f5f9;font-size:13px; }
        .leaderboard-table tr:hover td { background:#f8fafc; }
        .rank-badge { width:30px;height:30px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:13px; }
        .rank-1{background:linear-gradient(135deg,#fbbf24,#f59e0b);color:white;}.rank-2{background:linear-gradient(135deg,#94a3b8,#64748b);color:white;}.rank-3{background:linear-gradient(135deg,#d97706,#b45309);color:white;}.rank-n{background:#f1f5f9;color:#64748b;}
        .mini-bar{height:6px;border-radius:3px;background:#f1f5f9;overflow:hidden;width:80px;display:inline-block;vertical-align:middle;}
        .mini-bar-fill{height:100%;border-radius:3px;}.mini-bar-fill.green{background:linear-gradient(90deg,#22c55e,#16a34a);}.mini-bar-fill.blue{background:linear-gradient(90deg,#3b82f6,#2563eb);}.mini-bar-fill.orange{background:linear-gradient(90deg,#f59e0b,#d97706);}.mini-bar-fill.red{background:linear-gradient(90deg,#ef4444,#dc2626);}
        .perf-badge{padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;}
        .perf-excellent{background:#dcfce7;color:#16a34a;}.perf-good{background:#dbeafe;color:#2563eb;}.perf-average{background:#fef3c7;color:#d97706;}.perf-poor{background:#fee2e2;color:#dc2626;}
        .period-btn{padding:7px 14px;border-radius:8px;border:1px solid var(--border);background:white;color:var(--muted);font-weight:600;font-size:12px;text-decoration:none;transition:all .2s;}
        .period-btn.active,.period-btn:hover{background:var(--grad);color:white;border-color:transparent;}
        .filter-select{padding:8px 14px;border-radius:10px;border:1px solid var(--border);background:white;font-size:13px;font-weight:600;color:var(--text);}
        .sla-row{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f1f5f9;}
        .sla-row:last-child{border-bottom:none;}
        .sla-bar{flex:1;height:8px;border-radius:4px;background:#f1f5f9;overflow:hidden;}
        .sla-fill{height:100%;border-radius:4px;}
        .stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(100px,1fr));gap:10px;}
        .stat-item{text-align:center;padding:8px;}.stat-item .val{font-size:20px;font-weight:700;}.stat-item .lbl{font-size:10px;color:var(--muted);text-transform:uppercase;}
        .status-badge{padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
        .status-New{background:#fee2e2;color:#dc2626;}.status-In-Progress{background:#dbeafe;color:#2563eb;}.status-Resolved{background:#dcfce7;color:#16a34a;}.status-Closed{background:#f3f4f6;color:#6b7280;}
        .priority-badge{padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
        .priority-Urgent{background:#fee2e2;color:#dc2626;}.priority-High{background:#fef3c7;color:#d97706;}.priority-Medium{background:#dbeafe;color:#2563eb;}.priority-Low{background:#f3f4f6;color:#6b7280;}
        .btn-gradient{background:var(--grad);color:white;border:none;padding:8px 18px;border-radius:30px;font-weight:600;font-size:12px;cursor:pointer;transition:all .3s;text-decoration:none;display:inline-block;}
        .btn-gradient:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,154,218,.3);color:white;}
        .btn-outline{background:transparent;border:2px solid var(--kiboko-blue);color:var(--kiboko-blue);border-radius:30px;padding:7px 16px;font-weight:600;font-size:12px;transition:all .3s;text-decoration:none;}
        .btn-outline:hover{background:var(--kiboko-blue);color:white;}
        .btn-success-sm{background:#16a34a;color:white;border:none;padding:4px 10px;border-radius:20px;font-size:10px;cursor:pointer;transition:all .2s;}
        .btn-success-sm:hover{background:#15803d;}
        .btn-danger-sm{background:#dc2626;color:white;border:none;padding:4px 10px;border-radius:20px;font-size:10px;cursor:pointer;transition:all .2s;}
        .btn-danger-sm:hover{background:#b91c1c;}
        .btn-warning-sm{background:#f59e0b;color:white;border:none;padding:4px 10px;border-radius:20px;font-size:10px;cursor:pointer;transition:all .2s;}
        .btn-warning-sm:hover{background:#d97706;}
        .tickets-table{width:100%;border-collapse:collapse;font-size:12px;}
        .tickets-table th{background:#f8fafc;color:var(--muted);font-size:10px;font-weight:700;text-transform:uppercase;padding:8px 10px;text-align:left;}
        .tickets-table td{padding:8px 10px;border-bottom:1px solid #f1f5f9;}
        .tickets-table tr:hover td{background:#f8fafc;}
        .search-box{position:relative;}
        .search-box input{padding:8px 14px 8px 36px;border-radius:30px;border:1px solid var(--border);font-size:13px;width:250px;}
        .search-box i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);}
        canvas{max-height:300px;}
        .modal-close-notes textarea{resize:vertical;}
        @media(max-width:992px){.grid-2,.grid-3,.grid-2-1{grid-template-columns:1fr;}.main-content{margin-left:0;padding:14px;}.kpi-row{grid-template-columns:repeat(2,1fr);}}
        @media(max-width:576px){.kpi-row{grid-template-columns:1fr;}}
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="alert alert-{!! session('message_type') ?? 'info' !!} alert-dismissible fade show">
                {!! session()->pull('message') !!}@php session()->forget('message_type'); @endphp
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        <!-- Header -->
        <div class="page-header">
            <div>
                <h3 class="fw-bold mb-1"><i class="fas fa-chart-pie me-2" style="color:var(--kiboko-blue);"></i>IT Analytics Dashboard</h3>
                <small class="text-muted">Ticket management, reports & admin performance</small>
            </div>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <!-- Search -->
                <form method="GET" class="search-box me-2">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search tickets..." value="{{ $search }}">
                    <input type="hidden" name="period" value="{!! $period !!}">
                    <input type="hidden" name="admin" value="{!! $selected_admin !!}">
                </form>
                <!-- Admin Filter -->
                <select class="filter-select" onchange="location.href='?period={!! $period !!}&search={!! urlencode($search) !!}&admin='+this.value">
                    <option value="0">All Admins</option>
                    @foreach ($admins as $a)
                        <option value="{!! $a['id'] !!}" {!! $selected_admin==$a['id']?'selected':'' !!}>{{ $a['full_name'] }}</option>
                    @endforeach
                </select>
                <!-- Export -->
                <a href="?export=1&period={!! $period !!}&admin={!! $selected_admin !!}&search={!! urlencode($search) !!}" class="btn-outline">
                    <i class="fas fa-file-excel me-1"></i> Export
                </a>
                <!-- Print -->
                <a href="?print=1&period={!! $period !!}&admin={!! $selected_admin !!}&search={!! urlencode($search) !!}" class="btn-outline" target="_blank">
                    <i class="fas fa-print me-1"></i> Print
                </a>
            </div>
        </div>
        
        <!-- Period Buttons -->
        <div class="d-flex gap-2 mb-3 flex-wrap">
            @foreach (['today'=>'Today','yesterday'=>'Yesterday','week'=>'7 Days','month'=>'30 Days','quarter'=>'90 Days','year'=>'Year','all'=>'All Time'] as $k=>$l)
                <a href="?period={!! $k !!}&admin={!! $selected_admin !!}&search={!! urlencode($search) !!}" class="period-btn {!! $period==$k?'active':'' !!}">{!! $l !!}</a>
            @endforeach
            @if ($search)
                <a href="?period={!! $period !!}&admin={!! $selected_admin !!}" class="period-btn" style="background:#fee2e2;color:#dc2626;border-color:#fecaca;">
                    <i class="fas fa-times me-1"></i> Clear Search
                </a>
            @endif
        </div>
        
        <!-- KPI Row -->
        <div class="kpi-row">
            <div class="kpi-card"><div class="kpi-accent blue"></div><div class="kpi-icon" style="color:var(--kiboko-blue);"><i class="fas fa-ticket-alt"></i></div><div class="kpi-value">{!! number_format($total_tickets) !!}</div><div class="kpi-label">Total</div></div>
            <div class="kpi-card"><div class="kpi-accent green"></div><div class="kpi-icon" style="color:#16a34a;"><i class="fas fa-check-circle"></i></div><div class="kpi-value">{!! $resolution_rate !!}%</div><div class="kpi-label">Resolution Rate</div></div>
            <div class="kpi-card"><div class="kpi-accent orange"></div><div class="kpi-icon" style="color:#f59e0b;"><i class="fas fa-clock"></i></div><div class="kpi-value">{!! $avg_res_hours>0?$avg_res_hours.'h':'N/A' !!}</div><div class="kpi-label">Avg Resolution</div></div>
            <div class="kpi-card"><div class="kpi-accent red"></div><div class="kpi-icon" style="color:#dc2626;"><i class="fas fa-exclamation-circle"></i></div><div class="kpi-value">{!! ($overall['new_count']??0)+($overall['in_progress']??0) !!}</div><div class="kpi-label">Open</div><div class="kpi-sub">{!! $backlog['backlog']??0 !!} backlog</div></div>
            <div class="kpi-card"><div class="kpi-accent purple"></div><div class="kpi-icon" style="color:#8b5cf6;"><i class="fas fa-fire"></i></div><div class="kpi-value">{!! $overall['urgent']??0 !!}</div><div class="kpi-label">Urgent</div></div>
        </div>
        
        <!-- Charts Row 1 -->
        <div class="grid-2-1 mb-3">
            <div class="chart-card"><h5 class="chart-title"><i class="fas fa-chart-line" style="color:var(--kiboko-blue);"></i>Monthly Trend</h5><canvas id="trendChart" height="260"></canvas></div>
            <div class="chart-card"><h5 class="chart-title"><i class="fas fa-shield-alt" style="color:#16a34a;"></i>SLA Compliance</h5>
                @foreach ($sla_data as $sla) @php $c=$sla['compliance']>=90?'#16a34a':($sla['compliance']>=70?'#2563eb':($sla['compliance']>=50?'#f59e0b':'#dc2626')); @endphp
                    <div class="sla-row"><span style="font-weight:700;width:70px;">{!! $sla['priority'] !!}</span><span style="font-size:11px;color:var(--muted);width:40px;">{!! $sla['threshold'] !!}</span><div class="sla-bar"><div class="sla-fill" style="width:{!! $sla['compliance'] !!}%;background:{!! $c !!};"></div></div><span style="font-weight:700;font-size:13px;width:45px;text-align:right;">{!! $sla['compliance'] !!}%</span></div>
                @endforeach
            </div>
        </div>
        
        <!-- Charts Row 2 -->
        <div class="grid-2 mb-3">
            <div class="chart-card"><h5 class="chart-title"><i class="fas fa-calendar-week" style="color:#8b5cf6;"></i>Weekly Pattern</h5><canvas id="weeklyChart" height="260"></canvas></div>
            <div class="chart-card"><h5 class="chart-title"><i class="fas fa-hourglass-half" style="color:#f59e0b;"></i>Resolution Time</h5><canvas id="responseChart" height="260"></canvas></div>
        </div>
        
        <!-- Charts Row 3 -->
        <div class="grid-2 mb-3">
            <div class="chart-card"><h5 class="chart-title"><i class="fas fa-tags" style="color:#14b8a6;"></i>Categories</h5><canvas id="categoryChart" height="280"></canvas></div>
            <div class="chart-card"><h5 class="chart-title"><i class="fas fa-flag" style="color:#ef4444;"></i>Priority Breakdown</h5><canvas id="priorityChart" height="280"></canvas></div>
        </div>
        
        <!-- Charts Row 4 -->
        <div class="grid-2 mb-3">
            <div class="chart-card"><h5 class="chart-title"><i class="fas fa-building" style="color:var(--kiboko-blue);"></i>Top Departments</h5><canvas id="deptChart" height="300"></canvas></div>
            <div class="chart-card"><h5 class="chart-title"><i class="fas fa-calendar-day" style="color:#16a34a;"></i>Daily Volume</h5><canvas id="dailyChart" height="300"></canvas></div>
        </div>
        
        <!-- Workload -->
        <div class="chart-card mb-3"><h5 class="chart-title"><i class="fas fa-tasks" style="color:#f59e0b;"></i>Admin Workload</h5><canvas id="workloadChart" height="250"></canvas></div>
        
        <!-- Tickets Table with Close/Delete -->
        <div class="chart-card mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="chart-title mb-0"><i class="fas fa-list me-2" style="color:var(--kiboko-blue);"></i>Ticket List ({!! count($tickets) !!})</h5>
            </div>
            <div class="table-responsive">
                <table class="tickets-table">
                    <thead><tr>
                        <th>Ticket #</th><th>Employee</th><th>Subject</th><th>Category</th>
                        <th>Priority</th><th>Status</th><th>Assigned</th><th>Created</th>
                        <th style="width:120px;">Actions</th>
                    </tr></thead>
                    <tbody>
                        @if (count($tickets)>0) @foreach ($tickets as $t)
                            <tr>
                                <td><strong style="color:var(--kiboko-blue);">#{{ $t['ticket_number'] }}</strong></td>
                                <td>{{ $t['employee_name'] }}<br><small class="text-muted">{!! $t['employee_code'] !!}</small></td>
                                <td>{{ substr($t['subject'],0,40) }}</td>
                                <td>{!! $t['category'] !!}</td>
                                <td><span class="priority-badge priority-{!! $t['priority'] !!}">{!! $t['priority'] !!}</span></td>
                                <td><span class="status-badge status-{!! str_replace(' ','-',$t['status']) !!}">{!! $t['status'] !!}</span></td>
                                <td>{{ $t['assigned_to_name']??'N/A' }}</td>
                                <td><small>{!! date('M d, Y',strtotime($t['created_at'])) !!}</small></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="it-support?search={{ urlencode($t['ticket_number']) }}" class="btn-outline" style="padding:4px 10px;font-size:10px;" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if ($t['status'] != 'Closed')
                                            <button class="btn-success-sm" title="Close Ticket" onclick="openCloseModal({!! (int) $t['id'] !!}, {{ json_encode($t['ticket_number']) }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        <form method="POST" onsubmit="return confirm({{ json_encode('Delete ticket #'.$t['ticket_number'].'? This cannot be undone.') }});" style="display:inline;">
@csrf
                                            <input type="hidden" name="ticket_id" value="{!! $t['id'] !!}">
                                            <input type="hidden" name="ticket_number" value="{{ $t['ticket_number'] }}">
                                            <button type="submit" name="delete_ticket" class="btn-danger-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach @else
                            <tr><td colspan="9" class="text-center py-4 text-muted">No tickets found</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Admin Leaderboard -->
        <div class="chart-card mb-3"><h5 class="chart-title"><i class="fas fa-trophy" style="color:#f59e0b;"></i>Admin Leaderboard</h5>
            <div class="table-responsive"><table class="leaderboard-table"><thead><tr><th>#</th><th style="text-align:left;">Admin</th><th>Assigned</th><th>Resolved</th><th>Pending</th><th>Rate</th><th>Avg Time</th><th>Urgent</th><th>Status</th></tr></thead><tbody>
                @foreach ($top_resolvers as $i=>$ap) @php $r=$i+1;$rc=$r==1?'rank-1':($r==2?'rank-2':($r==3?'rank-3':'rank-n'));$rate=$ap['resolution_rate'];$bc=$rate>=80?'green':($rate>=60?'blue':($rate>=40?'orange':'red'));$avgH=$ap['avg_resolution_hours'];$p=$ap['new_count']+$ap['in_progress'];
                if($rate>=90&&$avgH<=24){$pb='perf-excellent';$pt='Excellent';}elseif($rate>=70){$pb='perf-good';$pt='Good';}elseif($rate>=50){$pb='perf-average';$pt='Average';}else{$pb='perf-poor';$pt='Low';} @endphp
                    <tr style="cursor:pointer;" onclick="location.href='?period={!! $period !!}&admin={!! $ap['id'] !!}&search={!! urlencode($search) !!}'">
                        <td><span class="rank-badge {!! $rc !!}">{!! $r !!}</span></td>
                        <td style="text-align:left;"><div class="d-flex align-items-center gap-2"><div class="admin-avatar" style="width:32px;height:32px;border-radius:8px;">@if ($ap['photo'])<img src="../uploads/employees/{!! $ap['photo'] !!}">@else<i class="fas fa-user" style="font-size:14px;"></i>@endif</div><div><div class="fw-bold" style="font-size:13px;">{{ $ap['name'] }}</div><small style="color:var(--muted);font-size:10px;">{!! $ap['code'] !!}</small></div></div></td>
                        <td class="fw-bold">{!! $ap['total_assigned'] !!}</td>
                        <td class="fw-bold" style="color:#16a34a;">{!! $ap['total_resolved'] !!}</td>
                        <td>{!! $p>0?"<span class='badge bg-warning text-dark'>$p</span>":'-' !!}</td>
                        <td>{!! $rate !!}%<span class="mini-bar"><span class="mini-bar-fill {!! $bc !!}" style="width:{!! $rate !!}%;"></span></span></td>
                        <td>{!! $avgH>0?$avgH.'h':'N/A' !!}</td>
                        <td>{!! $ap['urgent_resolved'] !!}</td>
                        <td><span class="perf-badge {!! $pb !!}">{!! $pt !!}</span></td>
                    </tr>
                @endforeach
            </tbody></table></div>
        </div>
        
        <!-- Admin Cards -->
        <h5 class="fw-bold mb-3 mt-4"><i class="fas fa-user-check me-2" style="color:var(--kiboko-blue);"></i>All Admins</h5>
        <div class="grid-3 mb-3">
            @foreach ($admin_performance as $ap) @php $isSel=$selected_admin==$ap['id'];$p=$ap['new_count']+$ap['in_progress']; @endphp
                <div class="admin-card {!! $isSel?'selected':'' !!}" onclick="location.href='?period={!! $period !!}&admin={!! $ap['id'] !!}&search={!! urlencode($search) !!}'">
                    <div class="admin-avatar">@if ($ap['photo'])<img src="../uploads/employees/{!! $ap['photo'] !!}">@else<i class="fas fa-user"></i>@endif</div>
                    <div style="flex:1;min-width:0;"><div class="fw-bold" style="font-size:14px;">{{ $ap['name'] }}</div><small style="color:var(--muted);">{!! $ap['code'] !!}·{!! $ap['department'] !!}</small>
                        <div class="stat-grid mt-2"><div class="stat-item"><div class="val" style="color:var(--kiboko-blue);">{!! $ap['total_assigned'] !!}</div><div class="lbl">Assigned</div></div><div class="stat-item"><div class="val" style="color:#16a34a;">{!! $ap['total_resolved'] !!}</div><div class="lbl">Resolved</div></div><div class="stat-item"><div class="val" style="color:{!! $p>0?'#dc2626':'var(--muted)' !!};">{!! $p !!}</div><div class="lbl">Pending</div></div><div class="stat-item"><div class="val">{!! $ap['resolution_rate'] !!}%</div><div class="lbl">Rate</div></div></div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if ($selected_admin>0&&!empty($perf_trend)) @php $sn='';foreach($admins as $a)if($a['id']==$selected_admin){$sn=$a['full_name'];break;} @endphp
            <div class="chart-card mb-3"><h5 class="chart-title"><i class="fas fa-user-chart" style="color:var(--kiboko-blue);"></i>{{ $sn }}'s Trend</h5><canvas id="perfTrendChart" height="220"></canvas></div>
        @endif
    </div>
    
    <!-- Close Ticket Modal -->
    <div class="modal fade" id="closeTicketModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-check-circle me-2" style="color:#16a34a;"></i>Close Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
@csrf
                    <div class="modal-body">
                        <input type="hidden" name="ticket_id" id="close_ticket_id">
                        <input type="hidden" name="ticket_number" id="close_ticket_number">
                        <p>Are you sure you want to close ticket <strong>#<span id="close_ticket_display"></span></strong>?</p>
                        <div class="mb-3">
                            <label class="form-label">Closing Notes</label>
                            <textarea name="close_notes" class="form-control modal-close-notes" rows="3" placeholder="Reason for closing this ticket..."></textarea>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Once closed, the ticket will be marked as completed and the user will be notified.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="close_ticket" class="btn btn-success">
                            <i class="fas fa-check me-1"></i> Close Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
    Chart.defaults.font.family="'Plus Jakarta Sans',sans-serif";Chart.defaults.font.size=11;Chart.defaults.color='#64748b';
    new Chart(document.getElementById('trendChart'),{type:'line',data:{labels:{!! json_encode(array_column($monthly_trend,'label')) !!},datasets:[{label:'Total',data:{!! json_encode(array_column($monthly_trend,'total')) !!},borderColor:'#009ada',backgroundColor:'rgba(0,154,218,.08)',fill:true,tension:.35,pointRadius:3},{label:'Resolved',data:{!! json_encode(array_column($monthly_trend,'resolved')) !!},borderColor:'#16a34a',backgroundColor:'rgba(22,163,74,.06)',fill:true,tension:.35,pointRadius:3},{label:'Pending',data:{!! json_encode(array_column($monthly_trend,'pending')) !!},borderColor:'#dc2626',backgroundColor:'rgba(220,38,38,.05)',fill:true,tension:.35,pointRadius:3}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true}}}});
    new Chart(document.getElementById('weeklyChart'),{type:'bar',data:{labels:{!! json_encode(array_column($weekly_pattern,'day_name')) !!},datasets:[{label:'Tickets',data:{!! json_encode(array_column($weekly_pattern,'count')) !!},backgroundColor:['rgba(0,154,218,.7)','rgba(0,154,218,.6)','rgba(0,154,218,.5)','rgba(0,154,218,.5)','rgba(0,154,218,.5)','rgba(0,154,218,.4)','rgba(0,154,218,.3)'],borderRadius:8}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
    new Chart(document.getElementById('responseChart'),{type:'bar',data:{labels:{!! json_encode(array_column($response_dist,'bucket')) !!},datasets:[{label:'Resolved',data:{!! json_encode(array_column($response_dist,'count')) !!},backgroundColor:['#16a34a','#22c55e','#84cc16','#fbbf24','#f59e0b','#f97316','#ef4444'],borderRadius:6}]},options:{responsive:true,maintainAspectRatio:false,indexAxis:'y',plugins:{legend:{display:false}},scales:{x:{beginAtZero:true}}}});
    new Chart(document.getElementById('categoryChart'),{type:'doughnut',data:{labels:{!! json_encode(array_column($categories,'category')) !!},datasets:[{data:{!! json_encode(array_column($categories,'count')) !!},backgroundColor:['#009ada','#76bc21','#f59e0b','#8b5cf6','#ef4444','#14b8a6','#ec4899','#64748b'],borderWidth:0}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}}}});
    new Chart(document.getElementById('priorityChart'),{type:'bar',data:{labels:{!! json_encode(array_column($priorities,'priority')) !!},datasets:[{label:'Resolved',data:{!! json_encode(array_column($priorities,'resolved')) !!},backgroundColor:'rgba(22,163,74,.7)',borderRadius:6},{label:'Pending',data:{!! json_encode(array_column($priorities,'pending')) !!},backgroundColor:'rgba(239,68,68,.6)',borderRadius:6}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},scales:{x:{stacked:true},y:{stacked:true,beginAtZero:true}}}});
    new Chart(document.getElementById('deptChart'),{type:'bar',data:{labels:{!! json_encode(array_column($departments,'department')) !!},datasets:[{label:'Total',data:{!! json_encode(array_column($departments,'total')) !!},backgroundColor:'rgba(0,154,218,.7)',borderRadius:5},{label:'Pending',data:{!! json_encode(array_column($departments,'pending')) !!},backgroundColor:'rgba(239,68,68,.6)',borderRadius:5}]},options:{responsive:true,maintainAspectRatio:false,indexAxis:'y',plugins:{legend:{position:'bottom'}},scales:{x:{beginAtZero:true}}}});
    new Chart(document.getElementById('dailyChart'),{type:'bar',data:{labels:{!! json_encode(array_column($daily_volume,'label')) !!},datasets:[{label:'Created',data:{!! json_encode(array_column($daily_volume,'created')) !!},backgroundColor:'rgba(0,154,218,.5)',borderRadius:3},{label:'Same-Day',data:{!! json_encode(array_column($daily_volume,'same_day')) !!},backgroundColor:'rgba(22,163,74,.5)',borderRadius:3}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},scales:{x:{grid:{display:false}},y:{beginAtZero:true}}}});
    new Chart(document.getElementById('workloadChart'),{type:'bar',data:{labels:{!! json_encode(array_column($workload_data,'name')) !!},datasets:[{label:'Assigned',data:{!! json_encode(array_column($workload_data,'assigned')) !!},backgroundColor:'rgba(0,154,218,.7)',borderRadius:6},{label:'Resolved',data:{!! json_encode(array_column($workload_data,'resolved')) !!},backgroundColor:'rgba(22,163,74,.7)',borderRadius:6},{label:'Pending',data:{!! json_encode(array_column($workload_data,'pending')) !!},backgroundColor:'rgba(239,68,68,.6)',borderRadius:6}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true}}}});
    @if ($selected_admin>0&&!empty($perf_trend))
    new Chart(document.getElementById('perfTrendChart'),{type:'line',data:{labels:{!! json_encode(array_column($perf_trend,'month')) !!},datasets:[{label:'Total',data:{!! json_encode(array_column($perf_trend,'total')) !!},borderColor:'#009ada',fill:false,tension:.3,pointRadius:4},{label:'Resolved',data:{!! json_encode(array_column($perf_trend,'resolved')) !!},borderColor:'#16a34a',fill:false,tension:.3,pointRadius:4}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true}}}});
    @endif
    
    // Close Ticket Modal
    function openCloseModal(ticketId, ticketNumber) {
        document.getElementById('close_ticket_id').value = ticketId;
        document.getElementById('close_ticket_number').value = ticketNumber;
        document.getElementById('close_ticket_display').textContent = ticketNumber;
        new bootstrap.Modal(document.getElementById('closeTicketModal')).show();
    }
    
    setTimeout(()=>document.querySelectorAll('.alert').forEach(e=>e.remove()),5000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
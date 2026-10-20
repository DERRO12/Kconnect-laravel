<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Analytics Dashboard – K-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root{--kiboko-green:#76bc21;--kiboko-blue:#009ada;--grad:linear-gradient(135deg,#009ada,#76bc21);--dark:#0f172a;--bg:#f1f5f9;--card:#fff;--border:#e2e8f0;--muted:#94a3b8}
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg)}
        .main-content{margin-left:280px;padding:20px 28px;min-height:100vh}
        .page-header{background:var(--card);border-radius:20px;padding:20px 28px;margin-bottom:20px;border:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
        .kpi-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:14px;margin-bottom:20px}
        .kpi-card{background:var(--card);border-radius:16px;padding:16px 18px;border:1px solid var(--border);transition:.25s;position:relative;overflow:hidden}
        .kpi-card:hover{transform:translateY(-3px);box-shadow:0 8px 25px rgba(0,0,0,.07)}
        .kpi-value{font-size:30px;font-weight:800;color:var(--dark)}.kpi-label{font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase}
        .kpi-accent{position:absolute;top:0;left:0;width:4px;height:100%}
        .kpi-accent.blue{background:var(--kiboko-blue)}.kpi-accent.green{background:#16a34a}.kpi-accent.red{background:#dc2626}.kpi-accent.orange{background:#f59e0b}
        .chart-card{background:var(--card);border-radius:16px;padding:20px;border:1px solid var(--border);margin-bottom:18px}
        .chart-title{font-weight:700;font-size:14px;display:flex;align-items:center;gap:8px;margin-bottom:16px}
        .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:18px}.grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px}.grid-2-1{display:grid;grid-template-columns:2fr 1fr;gap:18px}
        .period-btn{padding:6px 12px;border-radius:8px;border:1px solid var(--border);background:#fff;color:var(--muted);font-weight:600;font-size:11px;text-decoration:none;transition:.2s}
        .period-btn.active,.period-btn:hover{background:var(--grad);color:#fff;border-color:transparent}
        .filter-select{padding:7px 12px;border-radius:8px;border:1px solid var(--border);background:#fff;font-size:12px;font-weight:600}
        .btn-gradient{background:var(--grad);color:#fff;border:none;padding:8px 16px;border-radius:30px;font-weight:600;font-size:12px;cursor:pointer;text-decoration:none;transition:.2s}
        .btn-gradient:hover{transform:translateY(-2px);box-shadow:0 6px 15px rgba(0,154,218,.3);color:#fff}
        .btn-outline{background:transparent;border:2px solid var(--kiboko-blue);color:var(--kiboko-blue);border-radius:30px;padding:7px 14px;font-weight:600;font-size:12px;text-decoration:none;transition:.2s}
        .btn-outline:hover{background:var(--kiboko-blue);color:#fff}
        .btn-backup-green{background:linear-gradient(135deg,#76bc21,#4d8a0a);color:#fff;border:none;padding:8px 16px;border-radius:30px;font-weight:600;font-size:12px;cursor:pointer;text-decoration:none;transition:.2s}
        .btn-backup-green:hover{transform:translateY(-2px);box-shadow:0 6px 15px rgba(118,188,33,.4);color:#fff}
        .status-badge{padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;display:inline-block}
        .st-Completed{background:#dcfce7;color:#16a34a}.st-Partial{background:#fef3c7;color:#d97706}.st-Failed{background:#fee2e2;color:#dc2626}
        .calendar-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:3px}
        .cal-header{text-align:center;font-size:10px;font-weight:700;color:var(--muted);padding:6px 2px}
        .cal-day{border-radius:8px;padding:6px 4px;text-align:center;font-size:12px;font-weight:600;min-height:40px;display:flex;flex-direction:column;align-items:center;justify-content:center;border:1px solid transparent}
        .cal-day.weekend{background:#f8fafc;color:var(--muted)}.cal-day.today{border:2px solid var(--kiboko-blue)}
        .cal-day.has-backup{background:#dcfce7;border-color:#bbf7d0}.cal-day.missing{background:#fef2f2;border-color:#fecaca}
        .cal-day .dot{width:6px;height:6px;border-radius:50%;margin-top:2px}.cal-day .dot.green{background:#16a34a}.cal-day .dot.red{background:#dc2626}
        .screenshot-thumb{max-width:60px;max-height:45px;border-radius:6px;cursor:pointer;border:2px solid #e2e8f0;transition:all .2s;object-fit:cover}
        .screenshot-thumb:hover{border-color:var(--kiboko-blue);transform:scale(1.1);box-shadow:0 4px 12px rgba(0,0,0,.15)}
        .tickets-table{width:100%;border-collapse:collapse;font-size:11px}
        .tickets-table th{background:#f8fafc;color:var(--muted);font-size:9px;font-weight:700;text-transform:uppercase;padding:8px;text-align:left}
        .tickets-table td{padding:8px;border-bottom:1px solid #f1f5f9}.tickets-table tr:hover td{background:#f8fafc}
        canvas{max-height:280px}
        @media(max-width:992px){.grid-2,.grid-3,.grid-2-1{grid-template-columns:1fr}.main-content{margin-left:0;padding:14px}}
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    <div class="main-content">
        @if (session()->has('message'))<div class="alert alert-{!! session('message_type')??'info' !!} alert-dismissible fade show py-2 small">{!! session()->pull('message') !!}@php session()->forget('message_type'); @endphp<button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button></div>@endif
        
        <!-- Header -->
        <div class="page-header">
            <div><h3 class="fw-bold mb-1"><i class="fas fa-cloud-upload-alt me-2" style="color:var(--kiboko-blue);"></i>Backup Analytics Dashboard</h3><small class="text-muted">Audit trail, reports & compliance tracking</small></div>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <a href="../backup-log.php" class="btn-backup-green"><i class="fas fa-plus-circle me-1"></i> Record Backup</a>
                <a href="?export=1&period={!! $period !!}&company={!! $company_filter !!}&status_filter={!! $status_filter !!}&admin_filter={!! $admin_filter !!}" class="btn-outline"><i class="fas fa-file-excel me-1"></i> Export</a>
                <a href="?print=1&period={!! $period !!}&company={!! $company_filter !!}&status_filter={!! $status_filter !!}&admin_filter={!! $admin_filter !!}" class="btn-outline" target="_blank"><i class="fas fa-print me-1"></i> Print</a>
            </div>
        </div>
        
        <!-- Period -->
        <div class="d-flex gap-2 mb-3 flex-wrap">
            @foreach (['today'=>'Today','yesterday'=>'Yesterday','week'=>'7 Days','month'=>'30 Days','quarter'=>'90 Days','year'=>'Year','all'=>'All'] as $k=>$l)
                <a href="?period={!! $k !!}&company={!! $company_filter !!}&status_filter={!! $status_filter !!}&admin_filter={!! $admin_filter !!}" class="period-btn {!! $period==$k?'active':'' !!}">{!! $l !!}</a>
            @endforeach
        </div>
        
        <!-- Filters -->
        <div class="d-flex gap-2 mb-3 flex-wrap">
            <select class="filter-select" onchange="location.href='?period={!! $period !!}&status_filter={!! $status_filter !!}&admin_filter={!! $admin_filter !!}&company='+this.value">
                <option value="">All Companies</option>
                @foreach ($companies as $n=>$i)<option value="{!! $n !!}" {!! $company_filter==$n?'selected':'' !!}>{!! $n !!}</option>@endforeach
            </select>
            <select class="filter-select" onchange="location.href='?period={!! $period !!}&company={!! $company_filter !!}&admin_filter={!! $admin_filter !!}&status_filter='+this.value">
                <option value="">All Status</option>
                <option value="Completed" {!! $status_filter=='Completed'?'selected':'' !!}>Completed</option>
                <option value="Partial" {!! $status_filter=='Partial'?'selected':'' !!}>Partial</option>
                <option value="Failed" {!! $status_filter=='Failed'?'selected':'' !!}>Failed</option>
            </select>
            <select class="filter-select" onchange="location.href='?period={!! $period !!}&company={!! $company_filter !!}&status_filter={!! $status_filter !!}&admin_filter='+this.value">
                <option value="">All Admins</option>
                @if ($admin_list) @foreach ($admin_list as $al)<option value="{!! $al['id'] !!}" {!! $admin_filter==$al['id']?'selected':'' !!}>{{ $al['full_name'] }}</option>@endforeach @endif
            </select>
            @if ($period!='month'||$company_filter||$status_filter||$admin_filter)<a href="backup-dashboard.php" class="btn btn-sm btn-light rounded-pill"><i class="fas fa-times me-1"></i>Clear</a>@endif
        </div>
        
        <!-- KPI -->
        <div class="kpi-row">
            <div class="kpi-card"><div class="kpi-accent blue"></div><div class="kpi-value">{!! $kpi['total']??0 !!}</div><div class="kpi-label">Total Records</div></div>
            <div class="kpi-card"><div class="kpi-accent green"></div><div class="kpi-value" style="color:#16a34a;">{!! $kpi['completed']??0 !!}</div><div class="kpi-label">Completed</div></div>
            <div class="kpi-card"><div class="kpi-accent orange"></div><div class="kpi-value" style="color:#d97706;">{!! $kpi['partial']??0 !!}</div><div class="kpi-label">Partial</div></div>
            <div class="kpi-card"><div class="kpi-accent red"></div><div class="kpi-value" style="color:#dc2626;">{!! $kpi['failed']??0 !!}</div><div class="kpi-label">Failed</div></div>
            <div class="kpi-card"><div class="kpi-accent blue"></div><div class="kpi-value">{!! $kpi['days_covered']??0 !!}</div><div class="kpi-label">Days Covered</div></div>
            <div class="kpi-card"><div class="kpi-accent red"></div><div class="kpi-value" style="color:#dc2626;">{!! count($missing_days) !!}</div><div class="kpi-label">Missing {!! date('M') !!}</div></div>
        </div>
        
        <!-- Calendar + Company -->
        <div class="grid-2-1 mb-3">
            <div class="chart-card">
                <h5 class="chart-title"><i class="fas fa-calendar-alt" style="color:var(--kiboko-blue);"></i>{!! date('F Y') !!} Backup Calendar</h5>
                <div class="calendar-grid">
                    @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dh)<div class="cal-header">{!! $dh !!}</div>@endforeach
                    @for ($i=0;$i<$firstDow;$i++)<div class="cal-day" style="visibility:hidden;"></div>@endfor
                    @foreach ($calendar as $cd) @php $cls='cal-day';if($cd['isWeekend'])$cls.=' weekend';if($cd['isToday'])$cls.=' today';if($cd['hasBackup'])$cls.=' has-backup';elseif(!$cd['isWeekend']&&$cd['date']<=$today)$cls.=' missing'; @endphp
                        <div class="{!! $cls !!}" title="{!! $cd['date'] !!}">{!! $cd['day'] !!}@if ($cd['hasBackup'])<span class="dot green"></span>@elseif (!$cd['isWeekend']&&$cd['date']<=$today)<span class="dot red"></span>@endif</div>
                    @endforeach
                </div>
                <div class="d-flex gap-3 mt-2 small"><span><span class="dot green d-inline-block me-1"></span> Done</span><span><span class="dot red d-inline-block me-1"></span> Missing</span></div>
            </div>
            <div class="chart-card">
                <h5 class="chart-title"><i class="fas fa-building" style="color:#16a34a;"></i>Company Coverage</h5>
                @php $maxDays=max(($kpi['days_covered']??1),1); @endphp @foreach ($company_stats as $name=>$cs) @php $rate=round(($cs['count']/$maxDays)*100); @endphp
                    <div class="mb-2"><div class="d-flex justify-content-between small mb-1"><span class="fw-bold">{!! $name !!}</span><span>{!! $cs['count'] !!} backups</span></div><div class="progress" style="height:8px;"><div class="progress-bar" style="width:{!! min($rate,100) !!}%;background:{!! $companies[$name]['color'] !!};"></div></div></div>
                @endforeach
            </div>
        </div>
        
        <!-- Charts -->
        <div class="grid-2 mb-3">
            <div class="chart-card"><h5 class="chart-title"><i class="fas fa-chart-line" style="color:var(--kiboko-blue);"></i>Monthly Backup Trend</h5><canvas id="trendChart" height="260"></canvas></div>
            <div class="chart-card"><h5 class="chart-title"><i class="fas fa-calendar-week" style="color:#8b5cf6;"></i>Weekly Pattern</h5><canvas id="weeklyChart" height="260"></canvas></div>
        </div>
        
        <!-- Admin Performance -->
        <div class="chart-card mb-3"><h5 class="chart-title"><i class="fas fa-users" style="color:#f59e0b;"></i>Admin Backup Performance</h5>
            <div class="table-responsive"><table class="tickets-table"><thead><tr><th>Admin</th><th class="text-center">Total</th><th class="text-center">Completed</th><th class="text-center">Failed</th><th class="text-center">Days Active</th><th>Reliability</th></tr></thead><tbody>
                @foreach ($admin_perf as $ap) @php $rel=$ap['total']>0?round(($ap['completed']/$ap['total'])*100):0; $bc=$rel>=90?'green':($rel>=70?'blue':($rel>=50?'orange':'red')); @endphp
                    <tr><td class="fw-bold">{{ $ap['full_name'] }}</td><td class="text-center">{!! $ap['total'] !!}</td><td class="text-center text-success">{!! $ap['completed'] !!}</td><td class="text-center text-danger">{!! $ap['failed'] !!}</td><td class="text-center">{!! $ap['days'] !!}</td><td>{!! $rel !!}%<span class="mini-bar"><span class="mini-bar-fill {!! $bc !!}" style="width:{!! $rel !!}%;"></span></span></td></tr>
                @endforeach
            </tbody></table></div>
        </div>
        
        <!-- Logs Table -->
        <div class="chart-card mb-3"><h5 class="chart-title"><i class="fas fa-list" style="color:var(--kiboko-blue);"></i>Backup Records ({!! $logs?count($logs):0 !!})</h5>
            <div class="table-responsive"><table class="tickets-table"><thead><tr><th>Date</th><th>Time</th><th>Companies</th><th>Status</th><th>Recorded By</th><th>Screenshot</th><th>Ticket</th><th>Notes</th></tr></thead><tbody>
                @if ($logs&&count($logs)>0) @foreach ($logs as $log) @php $comps=json_decode($log['companies']??'[]',true)?:[]; @endphp
                    <tr>
                        <td><strong>{!! date('M d, Y',strtotime($log['backup_date'])) !!}</strong><br><small class="text-muted">{!! date('D',strtotime($log['backup_date'])) !!}</small></td>
                        <td>{!! date('H:i',strtotime($log['created_at'])) !!}</td>
                        <td>@foreach ($comps as $c)<span class="badge bg-light text-dark me-1">{{ $c }}</span>@endforeach</td>
                        <td><span class="status-badge st-{!! $log['status'] !!}">{!! $log['status'] !!}</span></td>
                        <td>{{ $log['full_name'] }}</td>
                        <td>@if ($log['screenshot'])<img src="../{!! $log['screenshot'] !!}" class="screenshot-thumb" onclick="previewImage('../{!! $log['screenshot'] !!}')" title="Click to view full size">@else-@endif</td>
                        <td>@if ($log['ticket_number'])<a href="../it-support.php?search={!! $log['ticket_number'] !!}" class="small">#{!! $log['ticket_number'] !!}</a>@endif</td>
                        <td><small>{{ substr($log['notes']??'',0,30) }}</small></td>
                    </tr>
                @endforeach @else<tr><td colspan="8" class="text-center py-4 text-muted">No records found</td></tr>@endif
            </tbody></table></div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-dark border-0 rounded-4">
                <div class="modal-header border-0 py-2">
                    <span class="text-white small fw-bold" id="imageModalTitle"><i class="fas fa-image me-1"></i> Backup Screenshot</span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img src="" id="imagePreviewFull" style="max-width:100%;max-height:78vh;border-radius:8px;" alt="Backup Screenshot">
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .mini-bar{height:6px;border-radius:3px;background:#e2e8f0;overflow:hidden;width:70px;display:inline-block;vertical-align:middle;margin-left:6px}
        .mini-bar-fill{height:100%;border-radius:3px}
        .mini-bar-fill.green{background:linear-gradient(90deg,#22c55e,#16a34a)}
        .mini-bar-fill.blue{background:linear-gradient(90deg,#3b82f6,#2563eb)}
        .mini-bar-fill.orange{background:linear-gradient(90deg,#f59e0b,#d97706)}
        .mini-bar-fill.red{background:linear-gradient(90deg,#ef4444,#dc2626)}
    </style>
    <script>
    function previewImage(url) {
        document.getElementById('imagePreviewFull').src = url;
        var modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
        modal.show();
    }
    Chart.defaults.font.family="'Plus Jakarta Sans',sans-serif";Chart.defaults.font.size=11;Chart.defaults.color='#64748b';
    new Chart(document.getElementById('trendChart'),{type:'line',data:{labels:{!! json_encode(array_column($monthly,'l')) !!},datasets:[{label:'Total',data:{!! json_encode(array_column($monthly,'total')) !!},borderColor:'#009ada',backgroundColor:'rgba(0,154,218,.08)',fill:true,tension:.35,pointRadius:3},{label:'Completed',data:{!! json_encode(array_column($monthly,'completed')) !!},borderColor:'#16a34a',fill:true,tension:.35,pointRadius:3},{label:'Failed',data:{!! json_encode(array_column($monthly,'failed')) !!},borderColor:'#dc2626',fill:true,tension:.35,pointRadius:3}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true}}}});
    new Chart(document.getElementById('weeklyChart'),{type:'bar',data:{labels:{!! json_encode(array_column($weekly,'day')) !!},datasets:[{label:'Backups',data:{!! json_encode(array_column($weekly,'cnt')) !!},backgroundColor:'rgba(0,154,218,.7)',borderRadius:8}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
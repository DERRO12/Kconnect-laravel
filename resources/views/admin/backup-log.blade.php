<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Logger - K-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
        .status-badge{padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700;}
        .status-Completed{background:#dcfce7;color:#16a34a;}.status-Partial{background:#fef3c7;color:#d97706;}.status-Failed{background:#fee2e2;color:#dc2626;}
        .btn-gradient{background:var(--grad);color:white;border:none;padding:8px 18px;border-radius:30px;font-weight:600;font-size:12px;cursor:pointer;transition:all .3s;text-decoration:none;display:inline-block;}
        .btn-gradient:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,154,218,.3);color:white;}
        .btn-outline{background:transparent;border:2px solid var(--kiboko-blue);color:var(--kiboko-blue);border-radius:30px;padding:7px 16px;font-weight:600;font-size:12px;transition:all .3s;text-decoration:none;}
        .btn-outline:hover{background:var(--kiboko-blue);color:white;}
        .filters-bar { background:var(--card);border-radius:14px;padding:16px;margin-bottom:20px;border:1px solid var(--border); }
        .form-control,.form-select{border-radius:8px;border:1px solid var(--border);padding:8px 12px;font-size:13px;}
        .form-control:focus,.form-select:focus{border-color:var(--kiboko-blue);box-shadow:0 0 0 3px rgba(0,154,218,.1);}
        .tickets-table{width:100%;border-collapse:collapse;font-size:12px;}
        .tickets-table th{background:#f8fafc;color:var(--muted);font-size:10px;font-weight:700;text-transform:uppercase;padding:8px 10px;text-align:left;}
        .tickets-table td{padding:8px 10px;border-bottom:1px solid #f1f5f9;}
        .tickets-table tr:hover td{background:#f8fafc;}
        .screenshot-thumb{max-width:80px;max-height:60px;cursor:pointer;border-radius:6px;}
        .screenshot-preview{max-width:200px;max-height:200px;margin-top:10px;border-radius:8px;display:none;}
        .company-checkbox{padding:10px;border:1px solid var(--border);border-radius:8px;margin-bottom:8px;transition:all .3s;cursor:pointer;}
        .company-checkbox:hover{background:rgba(0,154,218,.05);border-color:var(--kiboko-blue);}
        @media(max-width:992px){.main-content{margin-left:0;padding:14px;}.kpi-row{grid-template-columns:repeat(2,1fr);}}
        @media(max-width:576px){.kpi-row{grid-template-columns:1fr;}}
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        @if ($success_message)
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{!! $success_message !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if ($error_message)
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>{!! $error_message !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        <!-- Header -->
        <div class="page-header">
            <div>
                <h3 class="fw-bold mb-1"><i class="fas fa-database me-2" style="color:var(--kiboko-blue);"></i>IT Backup Logger</h3>
                <small class="text-muted">Record and track daily backup activities</small>
            </div>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <a href="?export=1&date_from={{ urlencode($date_from) }}&date_to={{ urlencode($date_to) }}&status_filter={{ urlencode($status_filter) }}" class="btn-outline">
                    <i class="fas fa-file-excel me-1"></i> Export
                </a>
                <a href="?print=1&date_from={{ urlencode($date_from) }}&date_to={{ urlencode($date_to) }}&status_filter={{ urlencode($status_filter) }}" class="btn-outline" target="_blank">
                    <i class="fas fa-print me-1"></i> Print
                </a>
                <button class="btn-gradient" data-bs-toggle="modal" data-bs-target="#newBackupModal">
                    <i class="fas fa-plus-circle me-1"></i> New Backup
                </button>
            </div>
        </div>
        
        <!-- Date Range Filter -->
        <div class="filters-bar">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{!! $date_from !!}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{!! $date_to !!}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select name="status_filter" class="form-select">
                        <option value="">All Status</option>
                        <option value="Completed" {!! $status_filter=='Completed'?'selected':'' !!}>Completed</option>
                        <option value="Partial" {!! $status_filter=='Partial'?'selected':'' !!}>Partial</option>
                        <option value="Failed" {!! $status_filter=='Failed'?'selected':'' !!}>Failed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Apply Filter
                    </button>
                </div>
            </form>
        </div>
        
        <!-- KPI Row -->
        <div class="kpi-row">
            <div class="kpi-card"><div class="kpi-accent blue"></div><div class="kpi-icon" style="color:var(--kiboko-blue);"><i class="fas fa-database"></i></div><div class="kpi-value">{!! $stats['total'] ?? 0 !!}</div><div class="kpi-label">Total</div></div>
            <div class="kpi-card"><div class="kpi-accent green"></div><div class="kpi-icon" style="color:#16a34a;"><i class="fas fa-check-circle"></i></div><div class="kpi-value">{!! $stats['completed'] ?? 0 !!}</div><div class="kpi-label">Completed</div></div>
            <div class="kpi-card"><div class="kpi-accent orange"></div><div class="kpi-icon" style="color:#f59e0b;"><i class="fas fa-exclamation-triangle"></i></div><div class="kpi-value">{!! $stats['partial'] ?? 0 !!}</div><div class="kpi-label">Partial</div></div>
            <div class="kpi-card"><div class="kpi-accent red"></div><div class="kpi-icon" style="color:#dc2626;"><i class="fas fa-times-circle"></i></div><div class="kpi-value">{!! $stats['failed'] ?? 0 !!}</div><div class="kpi-label">Failed</div></div>
            <div class="kpi-card"><div class="kpi-accent purple"></div><div class="kpi-icon" style="color:#8b5cf6;"><i class="fas fa-calendar-check"></i></div><div class="kpi-value">{!! $compliance_rate !!}%</div><div class="kpi-label">Compliance</div><div class="kpi-sub">{!! $backup_days_count !!}/{!! $total_days !!} days</div></div>
            <div class="kpi-card"><div class="kpi-accent red"></div><div class="kpi-icon" style="color:#dc2626;"><i class="fas fa-calendar-times"></i></div><div class="kpi-value">{!! $missed_days_count !!}</div><div class="kpi-label">Missed Days</div></div>
        </div>
        
        <!-- Backup Logs Table -->
        <div class="chart-card">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="chart-title mb-0"><i class="fas fa-list me-2" style="color:var(--kiboko-blue);"></i>Backup Records ({!! $backup_logs ? count($backup_logs) : 0 !!})</h5>
            </div>
            <div class="table-responsive">
                <table class="tickets-table">
                    <thead><tr>
                        <th>Date</th><th>Staff</th><th>Companies</th><th>Status</th>
                        <th>Evidence</th><th>Ticket</th><th>Notes</th>
                    </tr></thead>
                    <tbody>
                        @if ($backup_logs && count($backup_logs)>0) @foreach ($backup_logs as $backup)
                            <tr>
                                <td><strong>{!! date('M d, Y', strtotime($backup['backup_date'])) !!}</strong><br><small class="text-muted">{!! date('H:i', strtotime($backup['created_at'])) !!}</small></td>
                                <td>{{ $backup['employee_name'] ?? 'Unknown' }}@if (isset($backup['employee_code']))<br><small class="text-muted">{!! $backup['employee_code'] !!}</small>@endif</td>
                                <td><small>{{ $backup['companies'] }}</small></td>
                                <td><span class="status-badge status-{!! $backup['status'] !!}">{!! $backup['status'] !!}</span></td>
                                <td>
                                    @if ($backup['screenshot'])
                                        @php $shot = \App\Support\ItHelpers::backupScreenshotUrl($backup['screenshot']); @endphp
                                        <a href="../{{ $shot }}" target="_blank">
                                            <img src="../{{ $shot }}" class="screenshot-thumb" alt="Screenshot">
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($backup['ticket_id'])
                                        <a href="it-admin" class="badge bg-info text-decoration-none">#{!! $backup['ticket_id'] !!}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td><small>{{ substr($backup['notes'] ?? '', 0, 50) }}</small></td>
                            </tr>
                        @endforeach @else
                            <tr><td colspan="7" class="text-center py-4 text-muted">No backup records found for selected period</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- New Backup Modal -->
    <div class="modal fade" id="newBackupModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
@csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Record New Backup</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Backup Date *</label>
                                <input type="date" name="backup_date" class="form-control" value="{!! date('Y-m-d') !!}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Backup Status *</label>
                                <select name="status" class="form-select" required>
                                    <option value="Completed">✓ Completed</option>
                                    <option value="Partial">⚠ Partial</option>
                                    <option value="Failed">✗ Failed</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Select Companies/Servers *</label>
                                <div class="row">
                                    @if ($companies_result && count($companies_result) > 0)
                                        @foreach ($companies_result as $company)
                                    <div class="col-md-4 mb-2">
                                        <div class="company-checkbox">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="companies[]" 
                                                       value="{{ $company['name'] }}" 
                                                       id="modal_company_{!! $company['id'] !!}">
                                                <label class="form-check-label" for="modal_company_{!! $company['id'] !!}">
                                                    <i class="fas fa-server me-1" style="color:#009ada;"></i>
                                                    {{ $company['name'] }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Screenshot Evidence</label>
                                <input type="file" name="screenshot" class="form-control" accept="image/*" id="screenshotInput">
                                <img id="screenshotPreview" class="screenshot-preview" alt="Screenshot preview">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reference Ticket (Optional)</label>
                                <select name="ticket_id" class="form-select">
                                    <option value="">No ticket reference</option>
                                    @if ($tickets_result && count($tickets_result) > 0)
                                        @foreach ($tickets_result as $ticket)
                                    <option value="{!! $ticket['id'] !!}">
                                        {{ $ticket['ticket_number'] . ' - ' . $ticket['subject'] }}
                                    </option>
                                    @endforeach @endif
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes (Optional)</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Any issues or additional information..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="submit_backup" class="btn btn-gradient">
                            <i class="fas fa-save me-2"></i>Save Backup Log
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Screenshot preview
        document.getElementById('screenshotInput')?.addEventListener('change', function(e) {
            const preview = document.getElementById('screenshotPreview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
        
        // Auto-dismiss alerts
        setTimeout(()=>document.querySelectorAll('.alert').forEach(e=>e.remove()),5000);
    </script>
</body>
</html>
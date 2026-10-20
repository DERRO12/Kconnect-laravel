<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Logger - K-Connect</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
    <style>
        :root {
            --kiboko-blue: #009ada;
            --kiboko-green: #76bc21;
            --grad: linear-gradient(135deg, #009ada, #76bc21);
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
        }
        
        .page-header {
            background: linear-gradient(135deg, #fff, #f8fafc);
            padding: 20px 0;
            border-bottom: 1px solid #eef2f6;
            margin-bottom: 24px;
        }
        
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #eef2f6;
            text-align: center;
            transition: .2s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,.06);
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 800;
        }
        
        .stat-label {
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
        }
        
        .btn-gradient {
            background: var(--grad);
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
        }
        
        .btn-gradient:hover {
            opacity: .9;
            color: #fff;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-Completed {
            background: #dcfce7;
            color: #16a34a;
        }
        
        .status-Partial {
            background: #fef3c7;
            color: #d97706;
        }
        
        .status-Failed {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .card-header {
            background: #fff;
            border-bottom: 1px solid #eef2f6;
            border-radius: 16px 16px 0 0 !important;
            padding: 15px 20px;
            font-weight: 700;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 3px rgba(0, 154, 218, 0.1);
        }
        
        .screenshot-thumb {
            max-width: 80px;
            max-height: 60px;
            cursor: pointer;
            border-radius: 6px;
        }
        
        .screenshot-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 8px;
            display: none;
        }
        
        .table th {
            font-weight: 700;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .company-checkbox {
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .company-checkbox:hover {
            background: rgba(0, 154, 218, 0.05);
            border-color: var(--kiboko-blue);
        }
        
        .filters-bar {
            background: #fff;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 20px;
            border: 1px solid #eef2f6;
        }
        
        .btn-export {
            background: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-export:hover {
            opacity: .9;
            color: #fff;
        }
    </style>
</head>
<body>
    @include('partials.navbar')
    
    <div class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-database me-2" style="color: #009ada;"></i>IT Backup Logger
                    </h2>
                    <p class="text-muted mb-0 small">Record and track daily backup activities</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="?export=csv{!! !empty($status_filter) ? '&status_filter='.urlencode($status_filter) : '' !!}{!! !empty($date_from) ? '&date_from='.urlencode($date_from) : '' !!}{!! !empty($date_to) ? '&date_to='.urlencode($date_to) : '' !!}" class="btn-export">
                        <i class="fas fa-file-export me-1"></i> Export CSV
                    </a>
                    <button class="btn-gradient" data-bs-toggle="modal" data-bs-target="#newBackupModal">
                        <i class="fas fa-plus-circle me-1"></i> New Backup
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container mb-5">
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
        
        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-2">
                <div class="stat-card">
                    <div class="stat-value">{!! $stats['total'] ?? 0 !!}</div>
                    <div class="stat-label">Total Backups</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-card">
                    <div class="stat-value" style="color: #16a34a;">{!! $stats['completed'] ?? 0 !!}</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-card">
                    <div class="stat-value" style="color: #d97706;">{!! $stats['partial'] ?? 0 !!}</div>
                    <div class="stat-label">Partial</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-card">
                    <div class="stat-value" style="color: #dc2626;">{!! $stats['failed'] ?? 0 !!}</div>
                    <div class="stat-label">Failed</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-card">
                    <div class="stat-value" style="color: #009ada;">{!! $stats['today_count'] ?? 0 !!}</div>
                    <div class="stat-label">Today</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-card">
                    <div class="stat-value" style="color: #76bc21;">{!! $stats['with_evidence'] ?? 0 !!}</div>
                    <div class="stat-label">With Evidence</div>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters-bar">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select name="status_filter" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="Completed" {!! $status_filter == 'Completed' ? 'selected' : '' !!}>Completed</option>
                        <option value="Partial" {!! $status_filter == 'Partial' ? 'selected' : '' !!}>Partial</option>
                        <option value="Failed" {!! $status_filter == 'Failed' ? 'selected' : '' !!}>Failed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{!! $date_from !!}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{!! $date_to !!}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                    <a href="backup-log" class="btn btn-secondary btn-sm w-100 mt-1">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Backup Logs Table -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list me-2"></i>Backup Records
                <span class="badge bg-primary ms-2">{!! $backup_logs ? count($backup_logs) : 0 !!}</span>
            </div>
            <div class="card-body">
                @if ($backup_logs && count($backup_logs) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Staff</th>
                                    <th>Companies</th>
                                    <th>Status</th>
                                    <th>Evidence</th>
                                    <th>Ticket</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($backup_logs as $backup)
                                <tr>
                                    <td>
                                        <strong>{!! date('M d, Y', strtotime($backup['backup_date'])) !!}</strong><br>
                                        <small class="text-muted">{!! date('H:i', strtotime($backup['created_at'])) !!}</small>
                                    </td>
                                    <td>{{ $backup['employee_name'] ?? 'Unknown' }}</td>
                                    <td>
                                        <small>{{ $backup['companies'] }}</small>
                                    </td>
                                    <td>
                                        <span class="status-badge status-{!! $backup['status'] !!}">
                                            {!! $backup['status'] !!}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($backup['screenshot'])
                                            @php $shot = \App\Support\ItHelpers::backupScreenshotUrl($backup['screenshot']); @endphp
                                            <a href="{{ $shot }}" target="_blank">
                                                <img src="{{ $shot }}" 
                                                     class="screenshot-thumb" alt="Screenshot">
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($backup['ticket_id'])
                                            <a href="it-support" class="badge bg-info text-decoration-none">
                                                #{!! $backup['ticket_id'] !!}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ substr($backup['notes'] ?? '', 0, 50) }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-database fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No backup records found</p>
                    </div>
                @endif
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
                                <input type="date" name="backup_date" class="form-control" 
                                       value="{!! date('Y-m-d') !!}" required>
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
                                                    <i class="fas fa-server me-1" style="color: #009ada;"></i>
                                                    {{ $company['name'] }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @else
                                    <div class="col-12">
                                        <div class="alert alert-warning">No companies found.</div>
                                    </div>
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
                                <textarea name="notes" class="form-control" rows="3" 
                                          placeholder="Any issues or additional information..."></textarea>
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
    
    @include('partials.footer')
    
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
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Normalize Birthdays - Kiboko Intranet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
        }
        body { 
            background: #f8fafc; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px;
        }
        .container {
            max-width: 1400px;
        }
        .card {
            background: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            padding: 30px;
        }
        .btn-gradient {
            background: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.2);
            color: white;
        }
        .btn-secondary {
            background: #64748b;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        .btn-secondary:hover {
            background: #475569;
            color: white;
        }
        .preview-table {
            font-size: 0.9rem;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .preview-table th {
            background: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
            color: white;
            font-weight: 600;
            padding: 12px;
        }
        .preview-table td {
            padding: 12px;
            vertical-align: middle;
        }
        .old-value { 
            color: #dc3545; 
            font-weight: 500;
        }
        .new-value { 
            color: #28a745; 
            font-weight: 500;
        }
        .stats-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .warning-card {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .badge-count {
            background: var(--kiboko-blue);
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">
                    <i class="fas fa-magic me-2" style="color: var(--kiboko-blue);"></i>
                    Normalize Birthday Names & Departments
                </h2>
                <div>
                    <a href="manage-birthdays.php" class="btn-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
            
            @if($message)
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($error)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ $error }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <div class="stats-card">
                <div class="row">
                    <div class="col-md-4">
                        <strong>Total Records:</strong> 
                        <span class="badge-count">{!! $total_count !!}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Showing Preview:</strong> 
                        <span class="badge-count">{!! min($preview_limit, $total_count) !!} records</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Admin User:</strong> 
                        <span class="badge-count">{{ $username }}</span>
                    </div>
                </div>
            </div>
            
            <div class="warning-card">
                <div class="d-flex">
                    <div class="me-3">
                        <i class="fas fa-info-circle fa-2x text-warning"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-2">What this tool will do:</h5>
                        <ul class="mb-0">
                            <li>Convert names to proper case: <span class="old-value">"MWAKA JOHNSON"</span> → <span class="new-value">"Mwaka Johnson"</span></li>
                            <li>Convert departments to proper case: <span class="old-value">"WAREHOUSE"</span> → <span class="new-value">"Warehouse"</span></li>
                            <li>Trim any extra spaces from beginning and end</li>
                            <li>Handle empty or NULL values safely</li>
                        </ul>
                        <p class="text-muted mt-2 mb-0"><i class="fas fa-clock me-1"></i> This action cannot be undone. Please review the preview below before proceeding.</p>
                    </div>
                </div>
            </div>
            
            <h5 class="mt-4 mb-3 fw-bold">
                <i class="fas fa-eye me-2" style="color: var(--kiboko-blue);"></i>
                Preview (First {!! min($preview_limit, $total_count) !!} of {!! $total_count !!} records):
            </h5>
            
            <div class="table-responsive">
                <table class="table table-bordered preview-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Birth Date</th>
                            <th>Current Name</th>
                            <th>Will Become</th>
                            <th>Current Department</th>
                            <th>Will Become</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($birthdays && count($birthdays) > 0)
                            @foreach ($birthdays as $row)
                            @php
                                $current_name = $row['full_name'] ?? '';
                                $current_dept = $row['department'] ?? '';
                                $new_name = !empty($current_name) ? ucwords(strtolower(trim($current_name))) : '';
                                $new_dept = !empty($current_dept) ? ucwords(strtolower(trim($current_dept))) : '';
                            @endphp
                        <tr>
                            <td><span class="badge bg-secondary">{!! $row['id'] !!}</span></td>
                            <td>{!! date('M j, Y', strtotime($row['birth_date'])) !!}</td>
                            <td class="old-value">
                                {{ $current_name ?: '<em class="text-muted">Empty</em>' }}
                            </td>
                            <td class="new-value">
                                {{ $new_name ?: '<em class="text-muted">Empty</em>' }}
                                @if($current_name !== $new_name && !empty($current_name))
                                    <i class="fas fa-arrow-right ms-2 text-muted"></i>
                                @endif
                            </td>
                            <td class="old-value">
                                {{ $current_dept ?: '<em class="text-muted">Empty</em>' }}
                            </td>
                            <td class="new-value">
                                {{ $new_dept ?: '<em class="text-muted">Empty</em>' }}
                                @if($current_dept !== $new_dept && !empty($current_dept))
                                    <i class="fas fa-arrow-right ms-2 text-muted"></i>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-birthday-cake fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">No birthday records found</h6>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            @if($total_count > $preview_limit)
            <p class="text-muted text-center mt-2">
                <i class="fas fa-info-circle me-1"></i>
                Showing only first {!! $preview_limit !!} records. All {!! $total_count !!} records will be processed.
            </p>
            @endif
            
            <form method="POST" class="mt-4 text-center" onsubmit="return confirm('Are you sure you want to normalize all {!! $total_count !!} birthday records?\n\nThis action cannot be undone. Please make sure you have reviewed the preview above.');">
@csrf
                <button type="submit" name="normalize" class="btn-gradient me-2" {!! ($total_count == 0) ? 'disabled' : '' !!}>
                    <i class="fas fa-sync-alt me-2"></i>Normalize All {!! $total_count !!} Records
                </button>
                <a href="manage-birthdays.php" class="btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </form>
            
            @if($total_count > 0)
            <p class="text-muted text-center mt-3 small">
                <i class="fas fa-database me-1"></i>
                This will update {!! $total_count !!} records in the database.
            </p>
            @endif
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{!! $page_title !!}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
        }

        .main-content {
            margin-left: 280px;
            padding: 24px 32px;
            min-height: 100vh;
        }

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

        .page-header h2 {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0;
        }

        .report-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #eef2f6;
            margin-bottom: 24px;
        }

        .report-card h5 {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eef2f6;
        }

        .report-card h5 i {
            color: #009ada;
        }

        .stat-mini {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            border: 1px solid #eef2f6;
        }

        .stat-mini h3 {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .stat-mini p {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0;
        }

        .btn-export {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
        }

        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16,185,129,0.3);
            color: white;
        }

        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.2);
            color: white;
        }

        .nav-pills .nav-link {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            color: #64748b;
            transition: all 0.3s;
            font-size: 0.85rem;
        }

        .nav-pills .nav-link.active {
            background: var(--grad);
            color: white;
        }

        .nav-pills .nav-link:hover:not(.active) {
            background: #f1f5f9;
            color: #1e293b;
        }

        .table th {
            background: #f8fafc;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }

        .table td {
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .badge-status {
            font-size: 0.7rem;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .filter-bar {
            background: #f8fafc;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #eef2f6;
        }

        .progress-custom {
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--grad);
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .export-dropdown .dropdown-menu {
            border-radius: 14px;
            border: 1px solid #eef2f6;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 10px;
        }

        .export-dropdown .dropdown-item {
            border-radius: 10px;
            padding: 10px 16px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .export-dropdown .dropdown-item:hover {
            background: rgba(0,154,218,0.1);
            color: #009ada;
        }

        .export-dropdown .dropdown-item i {
            color: #10b981;
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
            }
            
            .page-header {
                padding: 15px 20px;
            }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h2><i class="fas fa-chart-bar me-2" style="color: #009ada;"></i>Asset Reports & Trails</h2>
                <p class="text-muted mb-0">Comprehensive reports, analytics, and assignment trails</p>
            </div>
            <div class="d-flex gap-2">
                <a href="manage-assets.php" class="btn btn-outline-secondary">
                    <i class="fas fa-boxes me-2"></i>Asset Register
                </a>
                <div class="export-dropdown dropdown">
                    <button class="btn-export dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-file-excel"></i> Export Reports
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Export Options</h6></li>
                        <li><a class="dropdown-item" href="?export=csv&type=all_assets">
                            <i class="fas fa-file-csv me-2"></i>All Assets Register
                        </a></li>
                        <li><a class="dropdown-item" href="?export=csv&type=active_assignments">
                            <i class="fas fa-file-csv me-2"></i>Active Assignments
                        </a></li>
                        <li><a class="dropdown-item" href="?export=csv&type=assignment_trail">
                            <i class="fas fa-file-csv me-2"></i>Assignment Trail (Full)
                        </a></li>
                        <li><a class="dropdown-item" href="?export=csv&type=employee_assets">
                            <i class="fas fa-file-csv me-2"></i>Employee Asset Summary
                        </a></li>
                        <li><a class="dropdown-item" href="?export=csv&type=category_summary">
                            <i class="fas fa-file-csv me-2"></i>Category Summary
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-mini">
                    <h3 class="text-primary">{!! $totalAssets !!}</h3>
                    <p>Total Assets</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-mini">
                    <h3 class="text-success">{!! $activeAssignments !!}</h3>
                    <p>Active Assignments</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-mini">
                    <h3 class="text-info">{!! $availableAssets !!}</h3>
                    <p>Available</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-mini">
                    <h3 class="text-warning">{!! $totalAssignmentsHistory !!}</h3>
                    <p>Total Assignments</p>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <ul class="nav nav-pills mb-4" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button">
                    <i class="fas fa-chart-pie me-2"></i>Overview
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="trail-tab" data-bs-toggle="pill" data-bs-target="#trail" type="button">
                    <i class="fas fa-history me-2"></i>Assignment Trail
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="employee-tab" data-bs-toggle="pill" data-bs-target="#employee" type="button">
                    <i class="fas fa-users me-2"></i>Employee Summary
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="recent-tab" data-bs-toggle="pill" data-bs-target="#recent" type="button">
                    <i class="fas fa-clock me-2"></i>Recent Activity
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="reportTabContent">
            
            <!-- Overview Tab -->
          
            <!-- Assignment Trail Tab -->
            <div class="tab-pane fade" id="trail" role="tabpanel">
                <div class="report-card">
                    <h5>
                        <i class="fas fa-history"></i> Full Assignment Trail
                        <a href="?export=csv&type=assignment_trail" class="btn-export ms-auto" style="padding: 6px 12px; font-size: 0.75rem;">
                            <i class="fas fa-download"></i> Export
                        </a>
                    </h5>
                    
                    <!-- Trail Filters -->
                    <div class="filter-bar">
                        <form method="GET" class="row g-2 align-items-end">
                            <input type="hidden" name="tab" value="trail">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Search Asset</label>
                                <input type="text" name="trail_asset" class="form-control" placeholder="Asset name or code..." value="{!! $get['trail_asset'] ?? '' !!}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Search Employee</label>
                                <input type="text" name="trail_employee" class="form-control" placeholder="Employee name or code..." value="{!! $get['trail_employee'] ?? '' !!}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Status</label>
                                <select name="trail_status" class="form-select">
                                    <option value="">All Statuses</option>
                                    @foreach ($assignmentStatuses as $status)
                                        <option value="{!! $status !!}" {!! (isset($get['trail_status']) && $get['trail_status'] === $status) ? 'selected' : '' !!}>{!! $status !!}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="asset-reports.php" class="btn btn-outline-secondary btn-sm flex-fill">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Asset Code</th>
                                    <th>Asset Name</th>
                                    <th>Category</th>
                                    <th>Serial Number</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Assigned Date</th>
                                    <th>Return Date</th>
                                    <th>Condition</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($assetTrail) > 0)
                                    @foreach ($assetTrail as $trail)
                                        <tr>
                                            <td><strong>{{ $trail['asset_code'] }}</strong></td>
                                            <td>{{ $trail['asset_name'] }}</td>
                                            <td><span class="badge bg-info">{{ $trail['category'] }}</span></td>
                                            <td><code>{{ $trail['serial_number'] ?? 'N/A' }}</code></td>
                                            <td>
                                                <strong>{{ $trail['full_name'] }}</strong>
                                                <br><small class="text-muted">{{ $trail['employee_code'] }}</small>
                                            </td>
                                            <td>{{ $trail['department'] }}</td>
                                            <td>{!! date('M d, Y', strtotime($trail['assigned_date'])) !!}</td>
                                            <td>
                                                @if ($trail['actual_return_date'])
                                                    {!! date('M d, Y', strtotime($trail['actual_return_date'])) !!}
                                                @elseif ($trail['expected_return_date'])
                                                    <span class="text-warning">Expected: {!! date('M d, Y', strtotime($trail['expected_return_date'])) !!}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-status bg-light text-dark">{!! $trail['condition_on_assignment'] !!}</span>
                                                @if ($trail['condition_on_return'])
                                                    <br><small class="text-muted">Return: {!! $trail['condition_on_return'] !!}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @php $statusColors = [
                                                    'Active' => 'success',
                                                    'Returned' => 'secondary',
                                                    'Lost' => 'danger',
                                                    'Damaged' => 'danger',
                                                    'Transferred' => 'warning'
                                                ];
                                                $color = $statusColors[$trail['assignment_status']] ?? 'secondary'; @endphp
                                                <span class="badge bg-{!! $color !!} badge-status">{!! $trail['assignment_status'] !!}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center py-5 text-muted">
                                            <i class="fas fa-search fa-3x mb-3"></i>
                                            <p>No records found. Try adjusting your filters.</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Employee Summary Tab -->
            <div class="tab-pane fade" id="employee" role="tabpanel">
                <div class="report-card">
                    <h5>
                        <i class="fas fa-users"></i> Employee Asset Summary
                        <a href="?export=csv&type=employee_assets" class="btn-export ms-auto" style="padding: 6px 12px; font-size: 0.75rem;">
                            <i class="fas fa-download"></i> Export
                        </a>
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Code</th>
                                    <th>Department</th>
                                    <th class="text-center">Total Assignments</th>
                                    <th class="text-center">Active</th>
                                    <th class="text-center">Returned</th>
                                    <th class="text-center">Lost/Damaged</th>
                                    <th>Status Distribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employeeSummary as $emp)
                                    @php
                                    $activePct = $emp['total_assignments'] > 0 ? ($emp['active_count'] / $emp['total_assignments']) * 100 : 0; @endphp
                                    <tr>
                                        <td><strong>{{ $emp['full_name'] }}</strong></td>
                                        <td><code>{{ $emp['employee_code'] }}</code></td>
                                        <td>{{ $emp['department'] }}</td>
                                        <td class="text-center">{!! $emp['total_assignments'] !!}</td>
                                        <td class="text-center">
                                            @if ($emp['active_count'] > 0)
                                                <span class="badge bg-success">{!! $emp['active_count'] !!}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{!! $emp['returned_count'] !!}</td>
                                        <td class="text-center">
                                            @if ($emp['lost_damaged'] > 0)
                                                <span class="badge bg-danger">{!! $emp['lost_damaged'] !!}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td>
                                        <td style="width: 150px;">
                                            <div class="progress-custom">
                                                <div class="progress-fill" style="width: {!! $activePct !!}%; background: {!! $emp['active_count'] > 0 ? 'var(--grad)' : '#e2e8f0' !!};"></div>
                                            </div>
                                            <small class="text-muted">{!! round($activePct) !!}% active</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Tab -->
            <div class="tab-pane fade" id="recent" role="tabpanel">
                <div class="report-card">
                    <h5>
                        <i class="fas fa-clock"></i> Recent Assignment Activity
                        <a href="?export=csv&type=active_assignments" class="btn-export ms-auto" style="padding: 6px 12px; font-size: 0.75rem;">
                            <i class="fas fa-download"></i> Export
                        </a>
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Asset Code</th>
                                    <th>Asset Name</th>
                                    <th>Category</th>
                                    <th>Serial</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Assigned Date</th>
                                    <th>Condition</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentAssignments as $recent)
                                    <tr>
                                        <td><strong>{{ $recent['asset_code'] }}</strong></td>
                                        <td>{{ $recent['asset_name'] }}</td>
                                        <td><span class="badge bg-info">{{ $recent['category'] }}</span></td>
                                        <td><code>{{ $recent['serial_number'] ?? '—' }}</code></td>
                                        <td>
                                            <strong>{{ $recent['full_name'] }}</strong>
                                            <br><small class="text-muted">{{ $recent['employee_code'] }}</small>
                                        </td>
                                        <td>{{ $recent['department'] }}</td>
                                        <td>{!! date('M d, Y H:i', strtotime($recent['assigned_date'])) !!}</td>
                                        <td><span class="badge bg-light text-dark">{!! $recent['condition_on_assignment'] !!}</span></td>
                                        <td>
                                            @php $color = $recent['assignment_status'] === 'Active' ? 'success' : 'secondary'; @endphp
                                            <span class="badge bg-{!! $color !!} badge-status">{!! $recent['assignment_status'] !!}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Category Distribution Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryData = {!! $categoryJson !!};
        
        new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: categoryData.labels,
                datasets: [
                    {
                        label: 'Assigned',
                        data: categoryData.assigned,
                        backgroundColor: '#009ada',
                        borderRadius: 6,
                        borderSkipped: false
                    },
                    {
                        label: 'Available',
                        data: categoryData.available,
                        backgroundColor: '#76bc21',
                        borderRadius: 6,
                        borderSkipped: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { size: 12, family: 'Plus Jakarta Sans' },
                            usePointStyle: true,
                            padding: 15
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: { 
                            font: { size: 11 },
                            stepSize: 1
                        },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });

        // Condition Overview Chart
        const conditionCtx = document.getElementById('conditionChart').getContext('2d');
        const conditionData = {!! $conditionJson !!};
        
        new Chart(conditionCtx, {
            type: 'doughnut',
            data: {
                labels: conditionData.labels,
                datasets: [{
                    data: conditionData.data,
                    backgroundColor: ['#10b981', '#34d399', '#009ada', '#f59e0b', '#ef4444', '#dc2626', '#6b7280'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 11, family: 'Plus Jakarta Sans' },
                            usePointStyle: true,
                            padding: 12
                        }
                    }
                }
            }
        });

        // Handle URL params to activate correct tab
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if (tab) {
                const tabBtn = document.querySelector(`[data-bs-target="#${tab}"]`);
                if (tabBtn) {
                    const bsTab = new bootstrap.Tab(tabBtn);
                    bsTab.show();
                }
            }
        });
    </script>
</body>
</html>
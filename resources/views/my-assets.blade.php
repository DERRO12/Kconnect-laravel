<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{!! $page_title !!}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad-primary: linear-gradient(135deg, #009ada, #76bc21);
        }

        .assets-container {
            padding: 2rem 0;
        }

        .page-header {
            background: white;
            border-radius: 20px;
            padding: 25px 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
        }

        .page-header h2 {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .page-header p {
            color: #64748b;
            margin-bottom: 0;
        }

        .asset-card {
            background: white;
            border-radius: 16px;
            padding: 0;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #eef2f6;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .asset-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        }

        .asset-card-header {
            background: linear-gradient(135deg, #f0f9ff 0%, #f4faf0 100%);
            padding: 20px;
            border-bottom: 1px solid #eef2f6;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .asset-icon {
            width: 55px;
            height: 55px;
            background: var(--grad-primary);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .asset-title {
            flex: 1;
        }

        .asset-title h5 {
            font-weight: 700;
            margin-bottom: 3px;
            color: #1e293b;
        }

        .asset-title .badge {
            font-size: 0.7rem;
            font-weight: 600;
        }

        .asset-card-body {
            padding: 20px;
            flex: 1;
        }

        .asset-detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
        }

        .asset-detail-row:last-child {
            border-bottom: none;
        }

        .asset-detail-label {
            color: #64748b;
            font-weight: 500;
        }

        .asset-detail-value {
            color: #1e293b;
            font-weight: 600;
            text-align: right;
        }

        .asset-card-footer {
            padding: 15px 20px;
            background: #f8fafc;
            border-top: 1px solid #eef2f6;
        }

        .condition-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .condition-New, .condition-Excellent { background: #dcfce7; color: #166534; }
        .condition-Good { background: #dbeafe; color: #1e40af; }
        .condition-Fair { background: #fef3c7; color: #d97706; }
        .condition-Poor, .condition-Damaged { background: #fee2e2; color: #dc2626; }

        .btn-gradient {
            background: var(--grad-primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
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
        }

        .nav-pills .nav-link.active {
            background: var(--grad-primary);
            color: white;
        }

        .nav-pills .nav-link:hover:not(.active) {
            background: #f1f5f9;
            color: #1e293b;
        }

        .history-timeline {
            position: relative;
            padding-left: 30px;
        }

        .history-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(180deg, #009ada, #76bc21);
        }

        .timeline-item {
            position: relative;
            padding: 15px 20px;
            background: white;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 1px solid #eef2f6;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -21px;
            top: 20px;
            width: 12px;
            height: 12px;
            background: #009ada;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 3px #009ada;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 4rem;
            background: var(--grad-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
        }

        .empty-state h4 {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #64748b;
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
     <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('K-CONNECT.png') }}" alt="K-CONNECT">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @php getMenu(); @endphp
                </ul>
                @if (isLoggedIn())
                <div class="dropdown ms-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        @if (!empty(session('employee_photo')))
                            <img src="{{ asset('uploads') }}/employees/{{ session('employee_photo') }}" 
                                 width="36" height="36" class="rounded-circle me-2">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" 
                                 style="width: 36px; height: 36px;">
                                <i class="fas fa-user text-gradient"></i>
                            </div>
                        @endif
                        <span class="d-none d-lg-inline">{{ session('employee_name') }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                         <li><a class="dropdown-item" href="{{ url('my-assets') }}"><i class="fas fa-laptop me-2"></i>My Assets</a></li>
                         <li><a class="dropdown-item" href="{{ url('academy') }}"><i class="fas fa-graduation-cap me-2"></i>My Learning (K-Academy)</a></li>
                        <li><h6 class="dropdown-header">{{ session('employee_code') }}</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="{{ url('logout') }}"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </nav>
    <!-- Main Content -->
    <div class="assets-container">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header" data-aos="fade-up">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h2><i class="fas fa-laptop me-2" style="color: #009ada;"></i>My Assets</h2>
                        <p>View your currently assigned IT assets and equipment</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark p-2">
                            <i class="fas fa-user me-1"></i> {{ $employee['employee_code'] ?? null }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <ul class="nav nav-pills mb-4" id="assetTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="active-tab" data-bs-toggle="pill" data-bs-target="#active-assets" type="button">
                        <i class="fas fa-check-circle me-2"></i>Current Assets
                        <span class="badge bg-white text-dark ms-1">{!! count($active_assets) !!}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="history-tab" data-bs-toggle="pill" data-bs-target="#history-assets" type="button">
                        <i class="fas fa-history me-2"></i>Asset History
                        <span class="badge bg-white text-dark ms-1">{!! count($asset_history) !!}</span>
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="assetTabContent">
                
                <!-- Active Assets -->
                <div class="tab-pane fade show active" id="active-assets" role="tabpanel">
                    @if (count($active_assets) > 0)
                        <div class="row g-4">
                            @foreach ($active_assets as $asset)
                                <div class="col-lg-4 col-md-6" data-aos="fade-up">
                                    <div class="asset-card">
                                        <div class="asset-card-header">
                                            <div class="asset-icon">
                                                @php
$category_icons = [
                                                    'Laptop' => 'fa-laptop',
                                                    'Desktop' => 'fa-desktop',
                                                    'Monitor' => 'fa-tv',
                                                    'Printer' => 'fa-print',
                                                    'Phone' => 'fa-mobile-alt',
                                                    'Tablet' => 'fa-tablet-alt',
                                                    'Networking' => 'fa-network-wired',
                                                    'Server' => 'fa-server',
                                                    'Other IT' => 'fa-microchip',
                                                    'Furniture' => 'fa-chair',
                                                    'Vehicle' => 'fa-car',
                                                    'Other' => 'fa-box'
                                                ];
                                                $icon = $category_icons[$asset['category']] ?? 'fa-laptop';
@endphp
                                                <i class="fas {!! $icon !!}"></i>
                                            </div>
                                            <div class="asset-title">
                                                <h5>{{ $asset['asset_name'] }}</h5>
                                                <span class="badge bg-info">{{ $asset['category'] }}</span>
                                            </div>
                                        </div>
                                        <div class="asset-card-body">
                                            <div class="asset-detail-row">
                                                <span class="asset-detail-label">Asset Code</span>
                                                <span class="asset-detail-value">{{ $asset['asset_code'] }}</span>
                                            </div>
                                            @if ($asset['brand'])
                                            <div class="asset-detail-row">
                                                <span class="asset-detail-label">Brand/Model</span>
                                                <span class="asset-detail-value">{{ $asset['brand'] . ' ' . $asset['model'] }}</span>
                                            </div>
                                            @endif
                                            @if ($asset['serial_number'])
                                            <div class="asset-detail-row">
                                                <span class="asset-detail-label">Serial Number</span>
                                                <span class="asset-detail-value" style="font-family: monospace;">{{ $asset['serial_number'] }}</span>
                                            </div>
                                            @endif
                                            @if ($asset['ram'])
                                            <div class="asset-detail-row">
                                                <span class="asset-detail-label">RAM</span>
                                                <span class="asset-detail-value">{{ $asset['ram'] }}</span>
                                            </div>
                                            @endif
                                            @if ($asset['storage'])
                                            <div class="asset-detail-row">
                                                <span class="asset-detail-label">Storage</span>
                                                <span class="asset-detail-value">{{ $asset['storage'] }}</span>
                                            </div>
                                            @endif
                                            @if ($asset['processor'])
                                            <div class="asset-detail-row">
                                                <span class="asset-detail-label">Processor</span>
                                                <span class="asset-detail-value">{{ $asset['processor'] }}</span>
                                            </div>
                                            @endif
                                            <div class="asset-detail-row">
                                                <span class="asset-detail-label">Assigned Date</span>
                                                <span class="asset-detail-value">{!! date('M d, Y', strtotime($asset['assigned_date'])) !!}</span>
                                            </div>
                                            @if ($asset['expected_return_date'])
                                            <div class="asset-detail-row">
                                                <span class="asset-detail-label">Expected Return</span>
                                                <span class="asset-detail-value">{!! date('M d, Y', strtotime($asset['expected_return_date'])) !!}</span>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="asset-card-footer">
                                            <span class="condition-badge condition-{!! $asset['condition_on_assignment'] !!}">
                                                <i class="fas fa-circle me-1" style="font-size: 6px;"></i>
                                                {!! $asset['condition_on_assignment'] !!}
                                            </span>
                                            @if ($asset['assignment_notes'])
                                                <small class="text-muted ms-2" title="{{ $asset['assignment_notes'] }}">
                                                    <i class="fas fa-sticky-note"></i>
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-laptop"></i>
                            <h4>No Active Assets</h4>
                            <p>You currently don't have any IT assets assigned to you.</p>
                        </div>
                    @endif
                </div>

                <!-- Asset History -->
                <div class="tab-pane fade" id="history-assets" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-10 mx-auto">
                            @if (count($asset_history) > 0)
                                <div class="history-timeline">
                                    @foreach ($asset_history as $history)
                                        <div class="timeline-item" data-aos="fade-up">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-1">{{ $history['asset_name'] }}</h6>
                                                    <small class="text-muted">{{ $history['asset_code'] }} | {{ $history['category'] }}</small>
                                                </div>
                                                <span class="condition-badge condition-{!! $history['condition_on_assignment'] !!}">
                                                    {!! $history['condition_on_assignment'] !!}
                                                </span>
                                            </div>
                                            <div class="row g-2 small">
                                                <div class="col-md-6">
                                                    <span class="text-muted">Assigned:</span> 
                                                    {!! date('M d, Y', strtotime($history['assigned_date'])) !!}
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="text-muted">Returned:</span> 
                                                    {!! $history['actual_return_date'] ? date('M d, Y', strtotime($history['actual_return_date'])) : 'N/A' !!}
                                                </div>
                                                @if ($history['condition_on_return'])
                                                <div class="col-md-6">
                                                    <span class="text-muted">Return Condition:</span>
                                                    <span class="condition-badge condition-{!! $history['condition_on_return'] !!}">
                                                        {!! $history['condition_on_return'] !!}
                                                    </span>
                                                </div>
                                                @endif
                                            </div>
                                            @if ($history['return_notes'])
                                                <div class="mt-2 p-2 bg-light rounded">
                                                    <small class="text-muted">Return Notes: {{ $history['return_notes'] }}</small>
                                                </div>
                                            @endif
                                            <small class="text-muted mt-2 d-block">
                                                Status: <span class="badge bg-secondary">{!! $history['assignment_status'] !!}</span>
                                            </small>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <i class="fas fa-archive"></i>
                                    <h4>No Asset History</h4>
                                    <p>You don't have any previous asset assignments.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>
</html>
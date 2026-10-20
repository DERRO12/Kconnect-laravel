<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Trail - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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

        .trail-container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            border: 1px solid #eef2f6;
        }

        .trail-timeline {
            position: relative;
            padding-left: 40px;
        }

        .trail-timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, #009ada, #76bc21, #009ada);
        }

        .trail-item {
            position: relative;
            margin-bottom: 25px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 14px;
            border: 1px solid #eef2f6;
        }

        .trail-item::before {
            content: '';
            position: absolute;
            left: -28px;
            top: 25px;
            width: 16px;
            height: 16px;
            background: #009ada;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 0 0 3px #009ada;
        }

        .trail-item.returned::before {
            background: #76bc21;
            box-shadow: 0 0 0 3px #76bc21;
        }

        .trail-item.transferred::before {
            background: #f59e0b;
            box-shadow: 0 0 0 3px #f59e0b;
        }

        .badge-assignment {
            font-size: 0.75rem;
            padding: 5px 12px;
            border-radius: 20px;
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

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold"><i class="fas fa-history me-2" style="color: #009ada;"></i>Asset Trail</h2>
                @if ($asset)
                    <p class="text-muted mb-0">{{ $asset['asset_name'] }} ({{ $asset['asset_code'] }})</p>
                @elseif ($employee)
                    <p class="text-muted mb-0">{{ $employee['full_name'] }} ({{ $employee['employee_code'] }})</p>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="manage-assets" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Assets
                </a>
                <a href="asset-reports" class="btn-gradient">
                    <i class="fas fa-file-excel me-2"></i>Export Report
                </a>
            </div>
        </div>

        @if ($asset)
            <div class="trail-container">
                <h5 class="mb-4">Assignment History</h5>
                
                @if (count($history) > 0)
                    <div class="trail-timeline">
                        @foreach ($history as $h)
                            @php
                            $trailClass = '';
                            if ($h['status'] === 'Returned') $trailClass = 'returned';
                            elseif ($h['status'] === 'Transferred') $trailClass = 'transferred'; @endphp
                            <div class="trail-item {!! $trailClass !!}">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="mb-1">
                                            {{ $h['employee_name'] }}
                                            <small class="text-muted">({{ $h['employee_code'] }})</small>
                                        </h6>
                                        <small class="text-muted">{{ $h['department'] }}</small>
                                    </div>
                                    <span class="badge badge-assignment bg-{!! $h['status'] === 'Active' ? 'success' : ($h['status'] === 'Returned' ? 'secondary' : 'warning') !!}">
                                        {!! $h['status'] !!}
                                    </span>
                                </div>
                                
                                <div class="row g-2 small">
                                    <div class="col-md-6">
                                        <strong>Assigned:</strong> {!! date('M d, Y H:i', strtotime($h['assigned_date'])) !!}
                                        @if ($h['assigned_by_name'])
                                            <br><small class="text-muted">By: {{ $h['assigned_by_name'] }}</small>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        @if ($h['actual_return_date'])
                                            <strong>Returned:</strong> {!! date('M d, Y H:i', strtotime($h['actual_return_date'])) !!}
                                            @if ($h['received_by_name'])
                                                <br><small class="text-muted">By: {{ $h['received_by_name'] }}</small>
                                            @endif
                                        @elseif ($h['expected_return_date'])
                                            <strong>Expected Return:</strong> {!! date('M d, Y', strtotime($h['expected_return_date'])) !!}
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="row g-2 small mt-2">
                                    <div class="col-md-6">
                                        <strong>Condition (Assigned):</strong> {!! $h['condition_on_assignment'] !!}
                                    </div>
                                    @if ($h['condition_on_return'])
                                        <div class="col-md-6">
                                            <strong>Condition (Returned):</strong> {!! $h['condition_on_return'] !!}
                                        </div>
                                    @endif
                                </div>
                                
                                @if ($h['assignment_notes'])
                                    <div class="mt-2 p-2 bg-white rounded">
                                        <small class="text-muted">Notes: {{ $h['assignment_notes'] }}</small>
                                    </div>
                                @endif
                                @if ($h['return_notes'])
                                    <div class="mt-2 p-2 bg-white rounded">
                                        <small class="text-muted">Return Notes: {{ $h['return_notes'] }}</small>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-history fa-3x mb-3"></i>
                        <p>No assignment history for this asset</p>
                    </div>
                @endif
            </div>
        @endif

        @if ($employee)
            <div class="trail-container mt-4">
                <h5 class="mb-4">Assets Assigned to {{ $employee['full_name'] }}</h5>
                @if (count($emp_history) > 0)
                    <div class="trail-timeline">
                        @foreach ($emp_history as $h)
                            <div class="trail-item {{ $h['status'] === 'Returned' ? 'returned' : ($h['status'] === 'Transferred' ? 'transferred' : '') }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1"><a href="asset-trail?id={{ (int) $h['asset_id'] }}" class="text-decoration-none">{{ $h['asset_name'] }}</a> <small class="text-muted">({{ $h['asset_code'] }})</small></h6>
                                        <small class="text-muted">{{ $h['category'] }}@if ($h['serial_number']) · S/N {{ $h['serial_number'] }}@endif</small>
                                    </div>
                                    <span class="badge badge-assignment bg-{{ $h['status'] === 'Active' ? 'success' : ($h['status'] === 'Returned' ? 'secondary' : 'warning') }}">{{ $h['status'] }}</span>
                                </div>
                                <div class="row g-2 small">
                                    <div class="col-md-6"><strong>Assigned:</strong> {{ date('M d, Y H:i', strtotime($h['assigned_date'])) }}@if ($h['assigned_by_name'])<br><small class="text-muted">By: {{ $h['assigned_by_name'] }}</small>@endif</div>
                                    <div class="col-md-6">@if ($h['actual_return_date'])<strong>Returned:</strong> {{ date('M d, Y H:i', strtotime($h['actual_return_date'])) }}@if ($h['received_by_name'])<br><small class="text-muted">By: {{ $h['received_by_name'] }}</small>@endif @endif</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted"><i class="fas fa-laptop fa-3x mb-3"></i><p>No assets have been assigned to this employee</p></div>
                @endif
            </div>
        @endif

        @if (! $asset && ! $employee)
            <div class="trail-container text-center py-5 text-muted">
                <i class="fas fa-search fa-3x mb-3"></i>
                <p class="mb-3">Asset not found. Open an asset from the register to see its assignment trail.</p>
                <a href="manage-assets" class="btn btn-outline-secondary">Go to Asset Register</a>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
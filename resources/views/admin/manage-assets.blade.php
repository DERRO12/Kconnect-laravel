<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{!! $page_title !!}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8fafc; margin: 0; padding: 0; }
        .main-content { margin-left: 280px; padding: 24px; }
        .card { background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid #eef2f6; }
        .stat-row { display: flex; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .stat-box { background: white; border-radius: 12px; padding: 20px; flex: 1; min-width: 180px; border: 1px solid #eef2f6; display: flex; align-items: center; gap: 12px; }
        .stat-box h3 { margin: 0; font-size: 1.8rem; }
        .stat-box p { margin: 0; color: #64748b; font-size: 0.85rem; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { background: #f8fafc; padding: 10px; text-align: left; font-size: 0.75rem; text-transform: uppercase; color: #64748b; border-bottom: 2px solid #e2e8f0; }
        .table td { padding: 10px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; }
        .btn { padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer; }
        .btn-primary { background: linear-gradient(135deg, #009ada, #76bc21); color: white; }
        .btn-outline { background: white; border: 1px solid #e2e8f0; color: #475569; }
        .btn-sm { padding: 4px 8px; font-size: 0.75rem; }
        .badge { padding: 3px 8px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-danger { background: #fee2e2; color: #991b1b; }
        input, select { padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.85rem; }
        @media (max-width: 992px) { .main-content { margin-left: 0; padding: 16px; } }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <div class="main-content">
        @if ($success)
            <div class="alert alert-success">✅ {!! $success !!}</div>
        @endif
        @if ($error)
            <div class="alert alert-danger">❌ {!! $error !!}</div>
        @endif

        <div class="card">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                <div>
                    <h2 style="margin:0;">🏢 Asset Register</h2>
                    <p style="margin:5px 0 0;color:#64748b;">Manage all company IT assets</p>
                </div>
                <div style="display:flex;gap:8px;">
                    <a href="asset-form" class="btn btn-primary">➕ Add New Asset</a>
                    <a href="asset-reports" class="btn btn-outline">📊 Reports</a>
                </div>
            </div>
        </div>

        <div class="stat-row">
            <div class="stat-box">
                <span style="font-size:2rem;">📦</span>
                <div><h3>{!! $totalAssets !!}</h3><p>Total Assets</p></div>
            </div>
            <div class="stat-box">
                <span style="font-size:2rem;">👤</span>
                <div><h3>{!! $activeAssignments !!}</h3><p>Assigned</p></div>
            </div>
            <div class="stat-box">
                <span style="font-size:2rem;">✅</span>
                <div><h3>{!! $availableAssets !!}</h3><p>Available</p></div>
            </div>
        </div>

        <div class="card">
            <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
                <div>
                    <label style="font-size:0.8rem;font-weight:600;">Search</label><br>
                    <input type="text" name="search" placeholder="Name, code, serial..." value="{{ $get['search'] ?? '' }}" style="width:200px;">
                </div>
                <div>
                    <label style="font-size:0.8rem;font-weight:600;">Category</label><br>
                    <select name="category">
                        <option value="">All</option>
                        @foreach ($categories as $c)
                            <option {!! ($cat_filter === $c) ? 'selected' : '' !!}>{!! $c !!}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:0.8rem;font-weight:600;">Condition</label><br>
                    <select name="condition">
                        <option value="">All</option>
                        @foreach ($conditions as $c)
                            <option {!! ($cond_filter === $c) ? 'selected' : '' !!}>{!! $c !!}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">🔍 Filter</button>
                    <a href="manage-assets" class="btn btn-outline">Clear</a>
                </div>
            </form>
        </div>

        <div class="card" style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Asset Code</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Serial #</th>
                        <th>Specs</th>
                        <th>Condition</th>
                        <th>Assigned To</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($result) > 0)
                        @foreach ($result as $row)
                            <tr>
                                <td><strong>{{ $row['asset_code'] }}</strong></td>
                                <td>{{ $row['asset_name'] }}</td>
                                <td><span class="badge" style="background:#e0f2fe;color:#0369a1;">{{ $row['category'] }}</span></td>
                                <td><code>{{ $row['serial_number'] ?? '—' }}</code></td>
                                <td style="font-size:0.75rem;">
                                    @if ($row['ram'])RAM: {{ $row['ram'] }}<br>@endif
                                    @if ($row['storage'])Storage: {{ $row['storage'] }}<br>@endif
                                    @if ($row['processor'])CPU: {{ $row['processor'] }}@endif
                                </td>
                                <td>
                                    @php $colors = ['New'=>'#16a34a','Excellent'=>'#16a34a','Good'=>'#2563eb','Fair'=>'#d97706','Poor'=>'#dc2626','Damaged'=>'#dc2626','Retired'=>'#6b7280'];
                                    $c = $colors[$row['condition_status']] ?? '#6b7280'; @endphp
                                    <span class="badge" style="background:{!! $c !!};color:white;">{!! $row['condition_status'] !!}</span>
                                </td>
                                <td>
                                    @if ($row['assigned_to_name'])
                                        <strong>{{ $row['assigned_to_name'] }}</strong><br>
                                        <small>{{ $row['assigned_to_code'] }}</small>
                                    @else
                                        <span class="badge" style="background:#dcfce7;color:#166534;">Available</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="asset-detail?id={!! $row['id'] !!}" class="btn btn-sm btn-outline" title="View">👁️</a>
                                    <a href="asset-form?id={!! $row['id'] !!}" class="btn btn-sm btn-outline" title="Edit">✏️</a>
                                    @if (!$row['assigned_to_name'])
                                        <a href="asset-assign?asset_id={!! $row['id'] !!}" class="btn btn-sm btn-outline" title="Assign">👤</a>
                                        <a href="?delete=1&id={!! $row['id'] !!}" class="btn btn-sm btn-outline" title="Delete" onclick="return confirm('Delete this asset?')" style="color:#dc2626;">🗑️</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" style="text-align:center;padding:40px;color:#64748b;">
                                📦 No assets found<br>
                                <a href="asset-form" class="btn btn-primary" style="margin-top:10px;">➕ Add First Asset</a>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
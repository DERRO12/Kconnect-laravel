<!DOCTYPE html>
<html>
<head>
    <title>Sync All Employees to K-Academy</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f4f8; padding: 20px; max-width: 1200px; margin: 0 auto; }
        .container { background: white; padding: 30px; border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        h1 { color: #009ada; margin-top: 0; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-box { background: #f8fafc; padding: 15px; border-radius: 10px; text-align: center; border-left: 4px solid #009ada; }
        .stat-number { font-size: 28px; font-weight: 700; color: #1e293b; }
        .stat-label { color: #64748b; font-size: 12px; text-transform: uppercase; }
        .success { border-left-color: #16a34a; }
        .warning { border-left-color: #f59e0b; }
        .danger { border-left-color: #dc3545; }
        .log { background: #1e293b; color: #e2e8f0; padding: 15px; border-radius: 10px; font-family: monospace; font-size: 13px; max-height: 500px; overflow-y: auto; white-space: pre-wrap; word-break: break-all; margin: 20px 0; }
        .progress-bar { width: 100%; background: #e2e8f0; border-radius: 10px; overflow: hidden; height: 25px; margin: 10px 0; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #009ada, #76bc21); transition: width 0.5s; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 600; width: 0%; }
        .btn { display: inline-block; padding: 10px 25px; background: linear-gradient(135deg, #009ada, #76bc21); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; border: none; cursor: pointer; margin-right: 10px; }
        .btn-secondary { background: #64748b; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { background: #f8fafc; padding: 8px; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; }
        code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 12px; }
        .badge { padding: 2px 8px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }
        hr { border: none; border-top: 2px solid #eef2f6; margin: 20px 0; }
        .scroll-box { max-height: 200px; overflow-y: auto; margin-bottom: 20px; border: 1px solid #eef2f6; border-radius: 8px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔄 Sync All Employees to K-Academy</h1>
    <p>This script will sync <strong>ALL</strong> {!! number_format($total) !!} employees from K-Connect to K-Academy.</p>
    <p><strong>Note:</strong> All synced users will be set to <strong>ACTIVE</strong> status with email verification.</p>
    <hr>

<h2>📊 Step 1: Fetching employees from K-Connect...</h2>
<div class='stats'><div class='stat-box success'><div class='stat-number'>{!! $total_kconnect !!}</div><div class='stat-label'>Employees in K-Connect</div></div></div>
<h2>📊 Step 2: Fetching users from K-Academy...</h2>
<div class='stats'><div class='stat-box success'><div class='stat-number'>{!! $total_kacademy !!}</div><div class='stat-label'>Users in K-Academy</div></div></div>
<div class='stats'><div class='stat-box success'><div class='stat-number'>{!! $existing_count !!}</div><div class='stat-label'>Already Synced</div></div><div class='stat-box warning'><div class='stat-number'>{!! $missing_count !!}</div><div class='stat-label'>Missing in K-Academy</div></div></div>
@if ($missing_count > 0 || $existing_count > 0)
<h2>⚡ Step 3: Syncing employees to K-Academy...</h2><div class='progress-bar'><div class='progress-fill' id='progressFill'>0%</div></div><div class='log' id='syncLog'>Starting sync process...
</div>
@foreach ($sync_log as $entry)
<script>document.getElementById('progressFill').style.width='{!! $entry['progress'] !!}%';document.getElementById('progressFill').textContent='{!! $entry['progress'] !!}%';</script><script>document.getElementById('syncLog').innerHTML += '{!! $entry['line'] !!}\n';</script>
@endforeach
<h2>📊 Sync Complete!</h2><div class='stats'><div class='stat-box success'><div class='stat-number'>{!! $synced_count !!}</div><div class='stat-label'>New Users Synced</div></div><div class='stat-box'><div class='stat-number'>{!! $updated_count !!}</div><div class='stat-label'>Existing Users Updated</div></div><div class='stat-box danger'><div class='stat-number'>{!! $failed_count !!}</div><div class='stat-label'>Failed</div></div></div>
@if (!empty($error_log))
<h3>⚠️ Errors:</h3><div class='log' style='background: #fee2e2; color: #991b1b;'>@foreach ($error_log as $error){{ $error }}
@endforeach</div>
@endif
@else
<div class='stats'><div class='stat-box success'><div class='stat-number'>✅</div><div class='stat-label'>All users are already synced!</div></div></div>
@endif
<h2>🔍 Step 4: Verification...</h2>
@if ($verify_data)
<div class='stats'><div class='stat-box success'><div class='stat-number'>{!! $verify_data['total'] !!}</div><div class='stat-label'>Total Users in K-Academy</div></div><div class='stat-box success'><div class='stat-number'>{!! $verify_data['active_count'] !!}</div><div class='stat-label'>Active Users</div></div><div class='stat-box success'><div class='stat-number'>{!! $verify_data['verified_count'] !!}</div><div class='stat-label'>Email Verified</div></div></div>
@else
<div class='stat-box danger'>Error verifying: {!! $verify_error !!}</div>
@endif
<h2>📋 Recent Users in K-Academy</h2>
@if (count($recent_users) > 0)
<div class='scroll-box'><table><tr><th>ID</th><th>Employee Code</th><th>Name</th><th>Email</th><th>Status</th><th>Verified</th></tr>
@foreach ($recent_users as $row)
<tr><td>{!! $row['id'] !!}</td><td><code>{!! $row['employee_code'] !!}</code></td><td>{{ $row['full_name'] }}</td><td>{!! $row['email'] !!}</td><td><span class='badge badge-{!! $row['status'] == 'active' ? 'active' : 'inactive' !!}'>{!! $row['status'] == 'active' ? 'Active' : 'Inactive' !!}</span></td><td>{!! $row['email_verified'] ? '✅ Yes' : '❌ No' !!}</td></tr>
@endforeach
</table></div>
@endif
    <hr>
    <p style="text-align: center;">
        <a href="admin-sync-users.php" class="btn">🔄 Re-run Sync</a>
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </p>
    <p style="text-align: center; color: #94a3b8; font-size: 12px; margin-top: 20px;">
        Sync completed at: {!! date('Y-m-d H:i:s') !!}
    </p>
</div>
</body>
</html>
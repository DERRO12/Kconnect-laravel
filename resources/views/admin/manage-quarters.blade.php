<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Quarters - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #eef2f6;
            padding: 20px 25px;
            font-weight: 700;
            border-radius: 20px 20px 0 0 !important;
        }
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 40px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.2);
            color: white;
        }
        .quarter-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
        }
        .quarter-Q1 { background: #dbeafe; color: #1e40af; }
        .quarter-Q2 { background: #dcfce7; color: #166534; }
        .quarter-Q3 { background: #fef3c7; color: #92400e; }
        .quarter-Q4 { background: #fce7f3; color: #9d174d; }
        .setting-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
        }
        .setting-enabled { background: #dcfce7; color: #166534; }
        .setting-disabled { background: #f1f5f9; color: #64748b; }
        .info-box {
            background: linear-gradient(135deg, rgba(0,154,218,0.05) 0%, rgba(118,188,33,0.05) 100%);
            border-radius: 16px;
            padding: 16px 20px;
            border-left: 4px solid var(--kiboko-blue);
        }
        .winner-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #eef2f6;
            transition: all 0.3s;
        }
        .winner-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        .winner-rank {
            position: absolute;
            top: -10px;
            left: 15px;
            padding: 4px 15px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            z-index: 1;
        }
        .winner-rank-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #fff; }
        .winner-rank-2 { background: linear-gradient(135deg, #94a3b8, #64748b); color: #fff; }
        .profile-pic {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            object-fit: cover;
            background: linear-gradient(135deg, #f8fafc, #e9ecef);
        }
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 16px; }
        }
        .winner-group {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .preview-card {
            background: #f0f9ff;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1"><i class="fas fa-calendar-alt me-2 text-gradient"></i>Quarter Management</h2>
                <p class="text-muted">Manage nomination quarters, voting rules, and announce winners (up to 2 per category)</p>
            </div>
        </div>
        
        @if (session()->has('message'))
        <div class="alert alert-{!! session('message_type') !!} alert-dismissible fade show" role="alert">
            <i class="fas fa-{!! session('message_type') == 'success' ? 'check-circle' : 'info-circle' !!} me-2"></i>
            {!! session('message') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @php session()->forget('message'); session()->forget('message_type'); @endphp @endif
        
        <!-- Current Quarter Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-sliders-h me-2 text-gradient"></i>Current Quarter Settings
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
@csrf
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Current Quarter</label>
                        <select name="quarter" class="form-select">
                            <option value="1" {!! $settings['current_quarter'] == 1 ? 'selected' : '' !!}>Q1 (Jan-Mar)</option>
                            <option value="2" {!! $settings['current_quarter'] == 2 ? 'selected' : '' !!}>Q2 (Apr-Jun)</option>
                            <option value="3" {!! $settings['current_quarter'] == 3 ? 'selected' : '' !!}>Q3 (Jul-Sep)</option>
                            <option value="4" {!! $settings['current_quarter'] == 4 ? 'selected' : '' !!}>Q4 (Oct-Dec)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Current Year</label>
                        <input type="number" name="year" class="form-control" value="{!! $settings['current_year'] !!}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Cooldown Days</label>
                        <input type="number" name="cooldown_days" class="form-control" value="{!! $settings['cooldown_days'] !!}" required min="0">
                        <small class="text-muted">Days between nominations</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Max Nominations/Quarter</label>
                        <input type="number" name="max_nominations" class="form-control" value="{!! $settings['max_nominations_per_quarter'] ?? '' !!}" placeholder="Unlimited">
                        <small class="text-muted">Leave empty for unlimited</small>
                    </div>
                    <div class="col-md-12">
                        <div class="info-box mt-2">
                            <div class="d-flex align-items-start gap-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="unique_votes" id="uniqueVotesSwitch" 
                                           {!! isset($settings['unique_votes_per_quarter']) && $settings['unique_votes_per_quarter'] == 1 ? 'checked' : '' !!}>
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-check-label fw-bold" for="uniqueVotesSwitch">
                                        <i class="fas fa-lock me-1"></i> Unique Votes Per Quarter
                                    </label>
                                    <p class="text-muted mb-0 small">When enabled, each user can only submit ONE nomination per category per quarter.</p>
                                    <span class="setting-badge mt-2 {!! isset($settings['unique_votes_per_quarter']) && $settings['unique_votes_per_quarter'] == 1 ? 'setting-enabled' : 'setting-disabled' !!}" id="uniqueVotesStatus">
                                        <i class="fas fa-{!! isset($settings['unique_votes_per_quarter']) && $settings['unique_votes_per_quarter'] == 1 ? 'check-circle' : 'ban' !!} me-1"></i>
                                        {!! isset($settings['unique_votes_per_quarter']) && $settings['unique_votes_per_quarter'] == 1 ? 'Unique votes ENABLED' : 'Multiple votes ALLOWED' !!}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="update_quarter" class="btn-gradient"><i class="fas fa-save me-2"></i>Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
      <!-- Email Action Buttons -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-4">
                <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #009ada20, #76bc2120); border-radius: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-envelope-open-text fa-2x" style="color: var(--kiboko-blue);"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1"><i class="fas fa-trophy me-1"></i> Send Nomination Reminders</h6>
                    <p class="text-muted small mb-2">Send email reminders to employees who haven't submitted nominations for {!! isset($settings['current_quarter']) ? 'Q' . $settings['current_quarter'] : 'current' !!} {!! isset($settings['current_year']) ? $settings['current_year'] : '' !!}.</p>
                    <a href="send-emails#nominationForm" class="btn btn-sm btn-gradient">
                        <i class="fas fa-paper-plane me-1"></i> Go to Email Center
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-4">
                <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #fbbf2420, #f59e0b20); border-radius: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-birthday-cake fa-2x" style="color: #f59e0b;"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1"><i class="fas fa-birthday-cake me-1"></i> Send Birthday Notifications</h6>
                    <p class="text-muted small mb-2">Send birthday wishes to all employees with birthdays this month ({!! date('F') !!}).</p>
                    <a href="send-emails#birthdayForm" class="btn btn-sm" style="background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; border: none; border-radius: 40px; padding: 6px 16px; font-weight: 600;">
                        <i class="fas fa-paper-plane me-1"></i> Go to Email Center
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
        <!-- Reset Quarter Tracking -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-undo-alt me-2 text-gradient"></i>Reset Quarter Tracking
            </div>
            <div class="card-body">
                <div class="info-box mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Reset unique vote tracking for a quarter. This allows users who have already voted to vote again.
                </div>
                <form method="POST" class="row g-3">
@csrf
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Quarter</label>
                        <select name="reset_quarter" class="form-select" required>
                            <option value="1">Q1 (Jan-Mar)</option>
                            <option value="2">Q2 (Apr-Jun)</option>
                            <option value="3">Q3 (Jul-Sep)</option>
                            <option value="4">Q4 (Oct-Dec)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Year</label>
                        <input type="number" name="reset_year" class="form-control" value="{!! date('Y') !!}" required>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" name="reset_quarter_tracking" class="btn btn-warning" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-redo-alt me-2"></i>Reset Tracking
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Announce Winners Section -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-trophy me-2 text-gradient"></i>Announce Quarter Winners
                <small class="text-muted ms-2">(Up to 2 winners per category: Winner + Runner Up)</small>
            </div>
            <div class="card-body">
                <!-- Selection Form -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Category</label>
                        <select name="category" class="form-select" onchange="this.form.submit()">
                            <option value="0">Select Category</option>
                            @foreach ($categories_with_nominations as $cat)
                            <option value="{!! $cat['id'] !!}" {!! $selected_category == $cat['id'] ? 'selected' : '' !!}>
                                {{ $cat['name'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Quarter</label>
                        <select name="quarter" class="form-select" onchange="this.form.submit()">
                            <option value="0">Select Quarter</option>
                            <option value="1" {!! $selected_quarter == 1 ? 'selected' : '' !!}>Q1 (Jan-Mar)</option>
                            <option value="2" {!! $selected_quarter == 2 ? 'selected' : '' !!}>Q2 (Apr-Jun)</option>
                            <option value="3" {!! $selected_quarter == 3 ? 'selected' : '' !!}>Q3 (Jul-Sep)</option>
                            <option value="4" {!! $selected_quarter == 4 ? 'selected' : '' !!}>Q4 (Oct-Dec)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Year</label>
                        <input type="number" name="year" class="form-control" value="{!! $selected_year !!}" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="manage-quarters" class="btn btn-secondary w-100">Clear</a>
                    </div>
                </form>
                
                <!-- Announce Winners Form -->
                @if ($selected_category > 0 && $selected_quarter > 0 && $selected_year > 0)
                    @if (count($available_nominations) > 0)
                        <form method="POST" class="row g-3">
@csrf
                            <input type="hidden" name="category_id" value="{!! $selected_category !!}">
                            <input type="hidden" name="quarter" value="{!! $selected_quarter !!}">
                            <input type="hidden" name="year" value="{!! $selected_year !!}">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 h-100">
                                        <h6 class="fw-bold mb-3">
                                            <i class="fas fa-crown text-warning me-2"></i>Winner (Rank 1) <span class="text-danger">*</span>
                                        </h6>
                                        <div class="mb-3">
                                            <select name="winner_id" class="form-select" required>
                                                <option value="">Select Winner</option>
                                                @foreach ($available_nominations as $nom)
                                                <option value="{!! $nom['id'] !!}">
                                                    {{ $nom['nominee_name'] . ' (' . $nom['employee_code'] . ')' }}
                                                    {!! !empty($nom['department']) ? ' - ' . htmlspecialchars($nom['department']) : '' !!}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Winner Notes</label>
                                            <textarea name="winner_notes" class="form-control" rows="2" placeholder="Additional notes about the winner..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 h-100">
                                        <h6 class="fw-bold mb-3">
                                            <i class="fas fa-medal text-secondary me-2"></i>Runner Up (Rank 2)
                                        </h6>
                                        <div class="mb-3">
                                            <select name="runner_up_id" class="form-select">
                                                <option value="">Select Runner Up (Optional)</option>
                                                @foreach ($available_nominations as $nom)
                                                <option value="{!! $nom['id'] !!}">
                                                    {{ $nom['nominee_name'] . ' (' . $nom['employee_code'] . ')' }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Runner Up Notes</label>
                                            <textarea name="runner_up_notes" class="form-control" rows="2" placeholder="Additional notes about the runner up..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12 mt-3">
                                <button type="submit" name="announce_winners" class="btn-gradient" onclick="return confirm('Are you sure you want to announce these winners? This will overwrite any existing winners for this category/quarter.')">
                                    <i class="fas fa-crown me-2"></i>Announce Winners
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            No approved nominations found for the selected category, quarter, and year.
                            Please approve nominations in <a href="all-nominations" class="alert-link">All Nominations</a> first.
                        </div>
                    @endif
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Select a category, quarter, and year to see available nominees.
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Quarterly Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2 text-gradient"></i>Quarterly Statistics
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr><th>Year</th><th>Quarter</th><th>Total Nominations</th><th>Unique Nominators</th><th>Unique Nominees</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @if (count($stats) > 0)
                                @foreach ($stats as $stat)
                                <tr>
                                    <td>{!! $stat['year'] !!}</td>
                                    <td><span class="quarter-badge quarter-Q{!! $stat['quarter'] !!}">Q{!! $stat['quarter'] !!}</span></td>
                                    <td>{!! number_format($stat['total']) !!}</td>
                                    <td>{!! number_format($stat['nominators']) !!}</td>
                                    <td>{!! number_format($stat['nominees']) !!}</td>
                                    <td>
                                        <a href="nomination-dashboard?quarter={!! $stat['quarter'] !!}&year={!! $stat['year'] !!}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                  </tr>
                                @endforeach
                            @else
                                <tr><td colspan="6" class="text-center text-muted">No nomination data available</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Previous Winners -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-medal me-2 text-gradient"></i>Previous Quarter Winners
            </div>
            <div class="card-body">
                @if (count($grouped_winners) > 0)
                    @foreach ($grouped_winners as $group)
                        <div class="winner-group">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="fw-bold mb-0">
                                        <span class="quarter-badge quarter-Q{!! $group['quarter'] !!} me-2">Q{!! $group['quarter'] !!}</span>
                                        {{ $group['category_name'] }}
                                    </h5>
                                    <small class="text-muted">Year: {!! $group['year'] !!}</small>
                                </div>
                            </div>
                            <div class="row g-3">
                                @foreach ($group['winners'] as $winner)
                                    <div class="col-md-6">
                                        <div class="winner-card position-relative">
                                            <div class="winner-rank winner-rank-{!! $winner['winner_rank'] !!}">
                                                @if ($winner['winner_rank'] == 1)
                                                    <i class="fas fa-crown me-1"></i> WINNER
                                                @else
                                                    <i class="fas fa-medal me-1"></i> RUNNER UP
                                                @endif
                                            </div>
                                            <div class="p-3 pt-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    @if (!empty($winner['photo']))
                                                        <img src="../uploads/employees/{!! $winner['photo'] !!}" class="profile-pic">
                                                    @else
                                                        <div class="profile-pic d-flex align-items-center justify-content-center bg-gradient-light">
                                                            <i class="fas fa-user fa-2x text-gradient"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="fw-bold mb-0">{{ $winner['nominee_name'] }}</h6>
                                                        <small class="text-muted">{!! $winner['employee_code'] !!}</small>
                                                        <div class="mt-2">
                                                            <small class="text-success">
                                                                <i class="fas fa-calendar-check me-1"></i>
                                                                Announced: {!! date('M d, Y', strtotime($winner['announced_at'])) !!}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-3 text-end">
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Remove this winner?');">
@csrf
                                                        <input type="hidden" name="winner_id" value="{!! $winner['winner_record_id'] !!}">
                                                        <button type="submit" name="remove_winner" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash me-1"></i> Remove
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No winners announced yet</h4>
                        <p class="text-muted">Use the form above to announce quarter winners</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update status text when switch is toggled
        const uniqueSwitch = document.getElementById('uniqueVotesSwitch');
        if (uniqueSwitch) {
            uniqueSwitch.addEventListener('change', function() {
                const statusEl = document.getElementById('uniqueVotesStatus');
                if (this.checked) {
                    statusEl.className = 'setting-badge mt-2 setting-enabled';
                    statusEl.innerHTML = '<i class="fas fa-check-circle me-1"></i>Unique votes ENABLED - One vote per category per quarter';
                } else {
                    statusEl.className = 'setting-badge mt-2 setting-disabled';
                    statusEl.innerHTML = '<i class="fas fa-ban me-1"></i>Multiple votes ALLOWED - Users can vote multiple times';
                }
            });
        }
    </script>
</body>
</html>
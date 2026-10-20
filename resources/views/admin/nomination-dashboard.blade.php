<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nomination Dashboard | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --kiboko-dark: #0f172a;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
            --grad-light: linear-gradient(135deg, rgba(0,154,218,0.1) 0%, rgba(118,188,33,0.1) 100%);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            overflow-x: hidden;
        }

        .main-content {
            margin-left: 280px;
            padding: 24px 32px;
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            transition: all 0.3s;
            border: 1px solid #eef2f6;
            height: 100%;
        }

        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: var(--grad-light);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--kiboko-blue);
            margin-bottom: 15px;
        }

        .stat-value { font-size: 32px; font-weight: 800; color: var(--kiboko-dark); margin-bottom: 5px; }
        .stat-label { color: #64748b; font-size: 14px; font-weight: 500; }

        /* Filters */
        .filters-bar {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-select {
            padding: 10px 16px;
            border: 2px solid #eef2f6;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            background: white;
        }

        .quarter-filter {
            padding: 10px 16px;
            border: 2px solid #eef2f6;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            background: var(--grad-light);
            color: var(--kiboko-blue);
        }

        /* Nomination Cards */
        .nomination-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
            border: 1px solid #eef2f6;
            position: relative;
        }

        .nomination-card.unread { background: linear-gradient(135deg, #fff, #f0f9ff); border-left: 4px solid var(--kiboko-blue); }

        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
        }

        .category-sanyu { background: #fef3c7; color: #d97706; }
        .category-catalyst { background: #dbeafe; color: #2563eb; }
        .category-innovator { background: #dcfce7; color: #16a34a; }
        .category-performer { background: #ede9fe; color: #7c3aed; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending { background: #fef3c7; color: #d97706; }
        .status-reviewed { background: #dcfce7; color: #16a34a; }
        .status-approved { background: #dbeafe; color: #2563eb; }
        .status-rejected { background: #fee2e2; color: #dc2626; }

        /* Enhanced Leaderboard */
        .leaderboard-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid #eef2f6;
        }

        .category-leaderboard {
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eef2f6;
        }
        
        .category-leaderboard:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .category-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--grad-light);
        }

        .category-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .category-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }

        .category-total {
            margin-left: auto;
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }

        .leaderboard-table {
            width: 100%;
        }

        .leaderboard-row {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s;
        }

        .leaderboard-row:hover {
            background: var(--grad-light);
            margin: 0 -12px;
            padding: 12px;
            border-radius: 12px;
        }

        .rank-badge {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .rank-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; }
        .rank-2 { background: linear-gradient(135deg, #94a3b8, #64748b); color: white; }
        .rank-3 { background: linear-gradient(135deg, #d97706, #b45309); color: white; }
        .rank-4 { background: #e2e8f0; color: #475569; }

        .nominee-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--grad-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--kiboko-blue);
            margin-right: 12px;
            flex-shrink: 0;
            overflow: hidden;
        }

        .nominee-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .nominee-details {
            flex: 1;
        }

        .nominee-name {
            font-weight: 700;
            margin-bottom: 2px;
            font-size: 14px;
        }

        .nominee-meta {
            font-size: 11px;
            color: #64748b;
        }

        .vote-count-badge {
            background: var(--grad-light);
            padding: 4px 12px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 14px;
            color: var(--kiboko-blue);
            flex-shrink: 0;
        }

        /* Progress Bar */
        .progress-container {
            margin-top: 8px;
            width: 100%;
        }

        .progress-bar-custom {
            height: 6px;
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

        /* Insight Cards */
        .insight-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 20px;
            color: white;
            margin-bottom: 24px;
        }

        .insight-card.green {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .insight-card.blue {
            background: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }

        .insight-card.orange {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .insight-value {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .insight-label {
            font-size: 13px;
            opacity: 0.9;
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

        .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,154,218,0.3); color: white; }

        .btn-outline {
            background: white;
            border: 2px solid var(--kiboko-blue);
            color: var(--kiboko-blue);
            padding: 8px 20px;
            border-radius: 40px;
            font-weight: 600;
        }

        .btn-outline:hover { background: var(--grad); color: white; border-color: transparent; }

        .btn-success-custom {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 40px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-success-custom:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(16,185,129,0.3); color: white; }

        .activity-timeline {
            max-height: 300px;
            overflow-y: auto;
        }

        .timeline-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #eef2f6;
        }

        .timeline-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--kiboko-blue);
        }

        .quarter-badge {
            background: var(--grad-light);
            border-radius: 40px;
            padding: 8px 20px;
            font-weight: 600;
            color: var(--kiboko-blue);
        }

        .quarter-selector {
            background: white;
            border-radius: 16px;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: 1px solid #eef2f6;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .quarter-selector label {
            font-weight: 600;
            color: #0f172a;
            margin: 0;
        }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 16px; }
            .leaderboard-row { flex-wrap: wrap; }
        }
    </style>
</head>
<body>
    <!-- Include Sidebar -->
    @include('admin.partials.sidebar')
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-1">Rewards & Recognition Dashboard</h2>
                <p class="text-muted">Comprehensive insights, leaderboards, and nomination management</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <span class="quarter-badge">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Viewing: {!! ($quarter_names[$selected_quarter] ?? null) . ' ' . $selected_year !!}
                </span>
                <a href="?export_excel=1&export_quarter={!! $selected_quarter !!}&export_year={!! $selected_year !!}" class="btn-success-custom" target="_blank">
                    <i class="fas fa-file-excel me-2"></i>Export Report
                </a>
                <a href="manage-categories" class="btn-gradient">
                    <i class="fas fa-plus-circle me-2"></i>Manage Categories
                </a>
                <a href="../rewards/nomination" target="_blank" class="btn-outline">
                    <i class="fas fa-external-link-alt me-2"></i>View Form
                </a>
            </div>
        </div>

        <!-- Quarter Selector -->
        <div class="quarter-selector">
            <label><i class="fas fa-filter me-2"></i>Filter by Quarter:</label>
            <form method="GET" class="d-flex gap-3 flex-wrap align-items-center">
                <select name="quarter" class="filter-select" onchange="this.form.year.value = this.options[this.selectedIndex].dataset.year; this.form.submit()">
                    @foreach ($available_quarters as $q)
                        <option value="{!! $q['quarter'] !!}" data-year="{!! $q['year'] !!}" {!! ($selected_quarter == $q['quarter'] && $selected_year == $q['year']) ? 'selected' : '' !!}>
                            {!! ($quarter_names[$q['quarter']] ?? null) . ' ' . $q['year'] !!}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="year" value="{!! $selected_year !!}">
                
                <!-- Preserve other filters -->
                @if ($category_filter > 0)
                    <input type="hidden" name="category" value="{!! $category_filter !!}">
                @endif
                @if ($status_filter)
                    <input type="hidden" name="status" value="{!! $status_filter !!}">
                @endif
                @if ($read_filter)
                    <input type="hidden" name="read_status" value="{!! $read_filter !!}">
                @endif
                
                @if ($category_filter > 0 || $status_filter || $read_filter)
                    <a href="nomination-dashboard?quarter={!! $selected_quarter !!}&year={!! $selected_year !!}" class="btn-outline btn-sm">
                        <i class="fas fa-times me-1"></i>Clear Filters
                    </a>
                @endif
            </form>
        </div>

        <!-- Stats Cards Row 1 -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                    <div class="stat-value">{!! number_format($stats['total'] ?? 0) !!}</div>
                    <div class="stat-label">Total Nominations</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fee2e2; color: #dc2626;"><i class="fas fa-envelope"></i></div>
                    <div class="stat-value">{!! number_format($stats['unread'] ?? 0) !!}</div>
                    <div class="stat-label">Unread Nominations</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fef3c7; color: #d97706;"><i class="fas fa-clock"></i></div>
                    <div class="stat-value">{!! number_format($stats['pending'] ?? 0) !!}</div>
                    <div class="stat-label">Pending Review</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #dcfce7; color: #16a34a;"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-value">{!! number_format($stats['approved'] ?? 0) !!}</div>
                    <div class="stat-label">Approved</div>
                </div>
            </div>
        </div>

        <!-- Stats Cards Row 2 - Participation Metrics -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="insight-card blue">
                    <div class="insight-value">{!! number_format($participation['unique_nominators'] ?? 0) !!}</div>
                    <div class="insight-label">Unique Nominators</div>
                    <small>{!! round(($participation['unique_nominators'] / max($participation['total_employees'], 1)) * 100, 1) !!}% participation</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="insight-card green">
                    <div class="insight-value">{!! number_format($participation['unique_nominees'] ?? 0) !!}</div>
                    <div class="insight-label">Unique Nominees</div>
                    <small>Recognized colleagues</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="insight-card orange">
                    <div class="insight-value">{!! round(($stats['total'] ?? 0) / max($participation['unique_nominators'] ?? 1, 1), 1) !!}</div>
                    <div class="insight-label">Avg Nominations per Person</div>
                    <small>Nomination rate</small>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-bar">
            <form method="GET" class="d-flex gap-3 flex-wrap align-items-center w-100">
                <input type="hidden" name="quarter" value="{!! $selected_quarter !!}">
                <input type="hidden" name="year" value="{!! $selected_year !!}">
                
                <select name="category" class="filter-select" onchange="this.form.submit()">
                    <option value="0">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{!! $cat['id'] !!}" {!! $category_filter == $cat['id'] ? 'selected' : '' !!}>
                            {{ $cat['name'] ?? '' }}
                        </option>
                    @endforeach
                </select>
                
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" {!! $status_filter == 'pending' ? 'selected' : '' !!}>Pending</option>
                    <option value="reviewed" {!! $status_filter == 'reviewed' ? 'selected' : '' !!}>Reviewed</option>
                    <option value="approved" {!! $status_filter == 'approved' ? 'selected' : '' !!}>Approved</option>
                    <option value="rejected" {!! $status_filter == 'rejected' ? 'selected' : '' !!}>Rejected</option>
                </select>
                
                <select name="read_status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Read Status</option>
                    <option value="unread" {!! $read_filter == 'unread' ? 'selected' : '' !!}>Unread Only</option>
                    <option value="read" {!! $read_filter == 'read' ? 'selected' : '' !!}>Read Only</option>
                </select>
                
                @if ($category_filter > 0 || $status_filter || $read_filter)
                    <a href="nomination-dashboard?quarter={!! $selected_quarter !!}&year={!! $selected_year !!}" class="btn-outline btn-sm">
                        <i class="fas fa-times me-1"></i>Clear Filters
                    </a>
                @endif
            </form>
        </div>

        <!-- Main Content Area -->
        <div class="row">
            <!-- Nominations List Column -->
            <div class="col-lg-7">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-list me-2"></i>Recent Nominations</h5>
                    <small class="text-muted">Showing for {!! ($quarter_names[$selected_quarter] ?? null) . ' ' . $selected_year !!}</small>
                </div>
                
                @if (count($nominations) > 0)
                    @foreach ($nominations as $nom)
                        <div class="nomination-card {!! ($nom['is_read'] ?? 0) ? '' : 'unread' !!}">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="category-badge category-{!! strtolower(str_replace(' ', '-', $nom['category_name'] ?? '')) !!}">
                                        <i class="fas fa-{!! $nom['category_name'] == 'Sanyu Spotlight' ? 'star' : ($nom['category_name'] == 'Business Catalyst' ? 'chart-line' : ($nom['category_name'] == 'Innovator of the Year' ? 'lightbulb' : 'medal')) !!} me-1"></i>
                                        {{ $nom['category_name'] ?? '' }}
                                    </span>
                                    <span class="status-badge status-{!! $nom['status'] ?? 'pending' !!} ms-2">
                                        <i class="fas fa-{!! ($nom['status'] ?? 'pending') == 'pending' ? 'clock' : (($nom['status'] ?? '') == 'reviewed' ? 'check' : (($nom['status'] ?? '') == 'approved' ? 'check-circle' : 'times')) !!}"></i>
                                        {!! ucfirst($nom['status'] ?? 'Pending') !!}
                                    </span>
                                </div>
                                <small class="text-muted">{!! date('M d, Y H:i', strtotime($nom['created_at'] ?? '')) !!}</small>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="text-center">
                                            <div class="small text-muted mb-1">Nominator</div>
                                            <div class="fw-bold">{{ $nom['nominator_name'] ?? '' }}</div>
                                            <div class="small text-muted">{{ $nom['nominator_code'] ?? '' }}</div>
                                        </div>
                                        <i class="fas fa-arrow-right text-muted"></i>
                                        <div class="text-center">
                                            <div class="small text-muted mb-1">Nominee</div>
                                            <div class="fw-bold">{{ $nom['nominee_name'] ?? '' }}</div>
                                            <div class="small text-muted">{{ $nom['nominee_code'] ?? '' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    @if (!($nom['is_read'] ?? 0))
                                        <form method="POST" style="display: inline;">
@csrf
                                            <input type="hidden" name="nomination_id" value="{!! $nom['id'] !!}">
                                            <button type="submit" name="mark_read" class="btn-outline btn-sm">
                                                <i class="fas fa-check-circle me-1"></i>Mark as Read
                                            </button>
                                        </form>
                                    @endif
                                    <a href="view-nomination?id={!! $nom['id'] !!}" class="btn-gradient btn-sm ms-2">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5 bg-white rounded-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No nominations found for {!! ($quarter_names[$selected_quarter] ?? null) . ' ' . $selected_year !!}</p>
                    </div>
                @endif
            </div>
            
            <!-- Leaderboard & Insights Column -->
            <div class="col-lg-5">
                <!-- Export Info Card -->
                <div class="leaderboard-card bg-light">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 50px; height: 50px; background: var(--grad-light); border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-download fa-2x" style="color: var(--kiboko-blue);"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Quarterly Report Export</h6>
                            <p class="small text-muted mb-0">Download complete nomination data for {!! ($quarter_names[$selected_quarter] ?? null) . ' ' . $selected_year !!}</p>
                        </div>
                        <div class="ms-auto">
                            <a href="?export_excel=1&export_quarter={!! $selected_quarter !!}&export_year={!! $selected_year !!}" class="btn-success-custom btn-sm" target="_blank">
                                <i class="fas fa-file-excel me-1"></i> Export Now
                            </a>
                        </div>
                    </div>
                    <div class="small text-muted mt-2">
                        <i class="fas fa-info-circle"></i> Report includes: Nominator info, Nominee info, Category details, Question answers, Committee decisions, Status, and Winner information
                    </div>
                </div>
                
                <!-- Detailed Leaderboard by Category -->
                <div class="leaderboard-card">
                    <h5 class="fw-bold mb-3"><i class="fas fa-chart-simple me-2"></i>🏆 Category Leaderboards</h5>
                    
                    @if (count($leaderboard_by_category) > 0)
                        @foreach ($leaderboard_by_category as $cat_id => $category)
                            <div class="category-leaderboard">
                                <div class="category-header">
                                    <div class="category-icon" style="background: {!! $category['color'] ?? '#009ada' !!}20; color: {!! $category['color'] ?? '#009ada' !!};">
                                        <i class="fas fa-{!! $category['icon'] ?? 'star' !!}"></i>
                                    </div>
                                    <div class="category-title">{{ $category['name'] ?? '' }}</div>
                                    <div class="category-total">
                                        <i class="fas fa-users"></i> {!! count($category['nominees'] ?? []) !!} nominees
                                    </div>
                                </div>
                                
                                <div class="leaderboard-table">
                                    @if (count($category['nominees'] ?? []) > 0)
                                        @foreach ($category['nominees'] as $index => $nominee)
                                            @php $rank = $index + 1;
                                            $max_votes = $category['nominees'][0]['vote_count'] ?? 1;
                                            $percentage = ($nominee['vote_count'] / $max_votes) * 100; @endphp
                                            <div class="leaderboard-row">
                                                <div class="rank-badge rank-{!! $rank !!}">
                                                    #{!! $rank !!}
                                                </div>
                                                <div class="nominee-avatar">
                                                    @if (!empty($nominee['nominee_photo']) && file_exists(kc_public_file('../uploads/employees/' . $nominee['nominee_photo'], 'admin')))
                                                        <img src="../uploads/employees/{{ $nominee['nominee_photo'] ?? '' }}" alt="">
                                                    @else
                                                        {!! strtoupper(substr($nominee['nominee_name'] ?? '', 0, 1)) !!}
                                                    @endif
                                                </div>
                                                <div class="nominee-details">
                                                    <div class="nominee-name">{{ $nominee['nominee_name'] ?? '' }}</div>
                                                    <div class="nominee-meta">
                                                        {{ $nominee['nominee_code'] ?? '' }} • 
                                                        {{ $nominee['nominee_department'] ?? '—' }}
                                                    </div>
                                                    <div class="progress-container">
                                                        <div class="progress-bar-custom">
                                                            <div class="progress-fill" style="width: {!! $percentage !!}%;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="vote-count-badge">
                                                    <i class="fas fa-vote-yea"></i> {!! $nominee['vote_count'] ?? 0 !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-3 text-muted">
                                            <small>No nominations yet for this category</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-trophy fa-3x text-muted mb-2"></i>
                            <p class="text-muted">No nominations submitted yet.<br>Be the first to recognize excellence!</p>
                        </div>
                    @endif
                </div>
                
                <!-- Recent Activity Timeline -->
                <div class="leaderboard-card">
                    <h5 class="fw-bold mb-3"><i class="fas fa-history me-2"></i>Recent Activity</h5>
                    <div class="activity-timeline">
                        @if (count($recent_activity) > 0)
                            @foreach ($recent_activity as $activity)
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <div class="flex-grow-1">
                                        <strong>{{ $activity['nominator_name'] ?? '' }}</strong> 
                                        nominated 
                                        <strong>{{ $activity['nominee_name'] ?? '' }}</strong>
                                        <div class="small text-muted">
                                            <i class="fas fa-tag me-1"></i>{{ $activity['category_name'] ?? '' }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-clock me-1"></i>{!! date('M d, H:i', strtotime($activity['created_at'] ?? '')) !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <p>No activity yet</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="leaderboard-card">
                    <h5 class="fw-bold mb-3"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    <div class="d-grid gap-2">
                        <a href="manage-categories" class="btn-outline text-center">
                            <i class="fas fa-plus-circle me-2"></i>Add New Category
                        </a>
                        <a href="manage-questions" class="btn-outline text-center">
                            <i class="fas fa-question-circle me-2"></i>Manage Questions
                        </a>
                        <a href="nominee-votes" class="btn-outline text-center">
                            <i class="fas fa-trophy me-2"></i>View Vote Leaderboard
                        </a>
                        <a href="?export_excel=1&export_quarter={!! $selected_quarter !!}&export_year={!! $selected_year !!}" class="btn-success-custom text-center" target="_blank">
                            <i class="fas fa-file-excel me-2"></i>Export Quarter Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nominee Votes | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --kiboko-dark: #0f172a;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
            --grad-light: linear-gradient(135deg, rgba(0,154,218,0.08) 0%, rgba(118,188,33,0.08) 100%);
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

        /* Header */
        .page-header {
            background: white;
            border-radius: 24px;
            padding: 24px 32px;
            margin-bottom: 24px;
            border: 1px solid #eef2f6;
        }

        .quarter-badge-large {
            display: inline-block;
            padding: 10px 24px;
            background: var(--grad);
            color: white;
            border-radius: 40px;
            font-weight: 700;
            font-size: 18px;
        }

        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid #eef2f6;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

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

        .stat-value {
            font-size: 36px;
            font-weight: 800;
            color: var(--kiboko-dark);
            margin-bottom: 5px;
        }

        .stat-label {
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
        }

        /* Filter Bar */
        .filter-bar {
            background: white;
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 24px;
            border: 1px solid #eef2f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .filter-select {
            padding: 10px 16px;
            border: 2px solid #eef2f6;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            background: white;
            min-width: 250px;
        }

        /* Category Pills */
        .category-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .category-pill {
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            color: white;
        }

        /* Table Container */
        .table-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #eef2f6;
        }

        .votes-table {
            width: 100%;
            margin-bottom: 0;
        }

        .votes-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px;
            border-bottom: 1px solid #eef2f6;
        }

        .votes-table tbody td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .votes-table tbody tr:hover {
            background: #f8fafc;
        }

        /* Rank Badge */
        .rank-badge {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
        }

        .rank-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; }
        .rank-2 { background: linear-gradient(135deg, #94a3b8, #64748b); color: white; }
        .rank-3 { background: linear-gradient(135deg, #d97706, #b45309); color: white; }
        .rank-other { background: var(--grad-light); color: var(--kiboko-blue); }

        /* Profile Avatar */
        .profile-avatar {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: var(--grad-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            color: var(--kiboko-blue);
            overflow: hidden;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Votes Display */
        .votes-count {
            font-size: 28px;
            font-weight: 800;
            color: var(--kiboko-dark);
            line-height: 1;
        }

        .votes-label {
            font-size: 12px;
            color: #64748b;
        }

        .progress-bar-container {
            width: 100px;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: var(--grad);
            border-radius: 4px;
        }

        /* Winner Badge */
        .winner-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 60px;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .stats-row { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 16px; }
            .stats-row { grid-template-columns: 1fr; }
            .filter-bar { flex-direction: column; align-items: stretch; }
            .filter-select { width: 100%; }
            .votes-table { min-width: 700px; }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold mb-2">
                        <i class="fas fa-trophy me-2" style="color: var(--kiboko-blue);"></i>
                        Nominee Votes Leaderboard
                    </h2>
                    <p class="text-muted mb-0">
                        Track votes for each nominee during the current quarter
                    </p>
                </div>
                <div class="quarter-badge-large">
                    <i class="fas fa-calendar-alt me-2"></i>
                    {!! ($quarter_names[$current_quarter] ?? null) . ' ' . $current_year !!}
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-value">{!! number_format($stats['total_nominees']) !!}</div>
                <div class="stat-label">Total Nominees</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-user-plus"></i></div>
                <div class="stat-value">{!! number_format($stats['total_nominators']) !!}</div>
                <div class="stat-label">Total Nominators</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-star"></i></div>
                <div class="stat-value">{!! number_format($stats['total_nominations']) !!}</div>
                <div class="stat-label">Total Nominations</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-tags"></i></div>
                <div class="stat-value">{!! number_format($stats['categories_with_nominations']) !!}</div>
                <div class="stat-label">Active Categories</div>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="filter-bar">
            <div class="category-pills">
                @foreach ($cat_breakdown as $cat)
                    @if ($cat['nomination_count'] > 0)
                    <span class="category-pill" style="background: {!! $cat['color'] !!};">
                        <i class="fas fa-tag me-1"></i>
                        {{ $cat['name'] }}: {!! $cat['nomination_count'] !!}
                    </span>
                @endif
                @endforeach
            </div>
            <form method="GET" class="d-flex gap-2">
                <select name="category" class="filter-select" onchange="this.form.submit()">
                    <option value="0">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{!! $cat['id'] !!}" {!! $category_filter == $cat['id'] ? 'selected' : '' !!}>
                            {{ $cat['name'] }}
                        </option>
                    @endforeach
                </select>
                @if ($category_filter)
                    <a href="nominee-votes.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Votes Table -->
        <div class="table-container">
            @if (count($votes_result) > 0)
                <table class="votes-table">
                    <thead>
                        <tr>
                            <th width="80">Rank</th>
                            <th>Nominee</th>
                            <th width="120">Department</th>
                            <th width="150">Categories</th>
                            <th width="120" class="text-center">Votes</th>
                            <th width="100" class="text-center">Nominators</th>
                            <th width="150">Last Nominated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rank = 1;
                        $max_votes = 0;
                        // First pass to get max votes
                        $votes_data = [];
                        foreach($votes_result as $row) {
                            $votes_data[] = $row;
                            if($row['total_votes'] > $max_votes) {
                                $max_votes = $row['total_votes'];
                            }
                        }
                        
                        @endphp
                        @foreach ($votes_data as $nominee)
                            @php $rank_class = $rank == 1 ? 'rank-1' : ($rank == 2 ? 'rank-2' : ($rank == 3 ? 'rank-3' : 'rank-other'));
                            $percentage = $max_votes > 0 ? ($nominee['total_votes'] / $max_votes) * 100 : 0; @endphp
                        <tr>
                            <td>
                                <div class="rank-badge {!! $rank_class !!}">
                                    #{!! $rank !!}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="profile-avatar">
                                        @if (!empty($nominee['photo']))
                                            <img src="../uploads/employees/{{ $nominee['photo'] }}" 
                                                 alt="{{ $nominee['full_name'] }}">
                                        @else
                                            {!! strtoupper(substr($nominee['full_name'], 0, 1)) !!}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $nominee['full_name'] }}</div>
                                        <div class="small text-muted">{{ $nominee['employee_code'] }}</div>
                                        @if (!empty($nominee['position']))
                                            <div class="small text-muted">{{ $nominee['position'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $nominee['department'] ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <div class="small">
                                    @php $categories = explode(', ', $nominee['categories_nominated']); @endphp
                                    @foreach ($categories as $cat)
                                        <span class="badge bg-light text-dark mb-1">{{ $cat }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="votes-count">{!! $nominee['total_votes'] !!}</div>
                                <div class="votes-label">votes</div>
                                <div class="progress-bar-container mx-auto mt-2">
                                    <div class="progress-bar-fill" style="width: {!! $percentage !!}%"></div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="fw-bold">{!! $nominee['unique_nominators'] !!}</div>
                                <div class="votes-label">unique</div>
                            </td>
                            <td>
                                <div class="small">
                                    {!! date('M d, Y', strtotime($nominee['last_nominated'])) !!}
                                </div>
                                <div class="small text-muted">
                                    {!! date('h:i A', strtotime($nominee['last_nominated'])) !!}
                                </div>
                            </td>
                        </tr>
                        @php $rank++; @endphp
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <i class="fas fa-trophy"></i>
                    <h5>No nominations yet</h5>
                    <p class="text-muted">No votes have been cast for {!! ($quarter_names[$current_quarter] ?? null) . ' ' . $current_year !!}.</p>
                    @if ($category_filter)
                        <p class="text-muted">Try selecting a different category.</p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Export and Actions -->
        <div class="d-flex justify-content-end gap-2 mt-4">
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Print
            </button>
            <button class="btn btn-gradient" onclick="exportToCSV()">
                <i class="fas fa-download me-2"></i>Export CSV
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Export to CSV
        function exportToCSV() {
            const rows = [];
            const table = document.querySelector('.votes-table');
            const headers = [];
            
            // Get headers
            table.querySelectorAll('thead th').forEach(th => {
                headers.push(th.textContent.trim());
            });
            rows.push(headers.join(','));
            
            // Get data
            table.querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach((td, index) => {
                    let text = td.textContent.trim();
                    // Clean up text
                    text = text.replace(/,/g, ';').replace(/\n/g, ' ').replace(/\s+/g, ' ');
                    row.push(`"${text}"`);
                });
                rows.push(row.join(','));
            });
            
            // Create and download file
            const csvContent = rows.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `nominee_votes_{!! $current_quarter !!}_{!! $current_year !!}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>
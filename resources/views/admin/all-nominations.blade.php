<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Nominations - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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

        /* Main Content */
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
            border: 1px solid #eef2f6;
        }

        .filter-select, .filter-input {
            padding: 10px 16px;
            border: 2px solid #eef2f6;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            background: white;
        }

        /* Export Button */
        .btn-export {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 40px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16,185,129,0.3);
            color: white;
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 20px;
            padding: 0;
            overflow-x: auto;
            border: 1px solid #eef2f6;
        }

        .nominations-table {
            width: 100%;
            margin-bottom: 0;
            min-width: 800px;
        }

        .nominations-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px;
            border-bottom: 1px solid #eef2f6;
        }

        .nominations-table tbody td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .nominations-table tbody tr:hover {
            background: #f8fafc;
        }

        /* Badges */
        .category-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
        }

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
        .status-reviewed { background: #dbeafe; color: #2563eb; }
        .status-approved { background: #dcfce7; color: #16a34a; }
        .status-rejected { background: #fee2e2; color: #dc2626; }

        .read-badge { padding: 4px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .read-yes { background: #dcfce7; color: #166534; }
        .read-no { background: #fee2e2; color: #991b1b; }

        /* Profile Avatar */
        .profile-avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--grad-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--kiboko-blue);
            overflow: hidden;
        }
        .profile-avatar-sm img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
        }
        .action-btn:hover {
            background: var(--grad);
            color: white;
            border-color: transparent;
            transform: translateY(-2px);
        }

        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.2);
            color: white;
        }
        .btn-outline {
            background: white;
            border: 2px solid var(--kiboko-blue);
            color: var(--kiboko-blue);
            padding: 8px 20px;
            border-radius: 40px;
            font-weight: 600;
        }

        /* Modal */
        .modal-content {
            border: none;
            border-radius: 24px;
        }
        .modal-header {
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border-bottom: 1px solid #eef2f6;
            padding: 20px 25px;
        }
        .modal-body {
            padding: 25px;
            max-height: 70vh;
            overflow-y: auto;
        }

        /* Checkbox */
        .select-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--kiboko-blue);
        }

        .message-alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            animation: slideDown 0.3s ease;
        }
        .message-alert.success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .message-alert.danger { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .filter-info {
            background: var(--grad-light);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 16px; }
            .filters-bar .row > div { margin-bottom: 10px; }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="fw-bold mb-1"><i class="fas fa-list me-2 text-gradient"></i>All Nominations</h2>
                <p class="text-muted">View and manage all nominations across all categories</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-light text-dark p-2">
                    <i class="fas fa-calendar-alt me-1"></i> {!! ($quarter_names[$current_quarter] ?? null) . ' ' . $current_year !!}
                </span>
                <button class="btn-outline" onclick="window.location.href='nomination-dashboard'">
                    <i class="fas fa-chart-line me-2"></i>Dashboard
                </button>
                <a href="../rewards/nomination" target="_blank" class="btn-gradient">
                    <i class="fas fa-plus-circle me-2"></i>New Nomination
                </a>
            </div>
        </div>

        <!-- Message Alert -->
        @if (session()->has('message'))
        <div class="message-alert {!! session('message_type') !!}">
            <span><i class="fas fa-info-circle me-2"></i>{!! session('message') !!}</span>
            <button class="btn-close float-end" onclick="this.parentElement.remove()"></button>
        </div>
        @php session()->forget('message'); session()->forget('message_type'); @endphp
        @endif

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-2 col-6">
                <div class="stat-card text-center">
                    <div class="stat-value">{!! number_format($stats['total']) !!}</div>
                    <div class="stat-label">Total</div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card text-center">
                    <div class="stat-value" style="color: #d97706;">{!! number_format($stats['pending']) !!}</div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card text-center">
                    <div class="stat-value" style="color: #2563eb;">{!! number_format($stats['reviewed']) !!}</div>
                    <div class="stat-label">Reviewed</div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card text-center">
                    <div class="stat-value" style="color: #16a34a;">{!! number_format($stats['approved']) !!}</div>
                    <div class="stat-label">Approved</div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card text-center">
                    <div class="stat-value" style="color: #dc2626;">{!! number_format($stats['rejected']) !!}</div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card text-center">
                    <div class="stat-value">{!! number_format($stats['unread']) !!}</div>
                    <div class="stat-label">Unread</div>
                </div>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="d-flex gap-3 flex-wrap">
                    @foreach ($category_stats as $cat_stat)
                        <span class="category-badge" style="background: #f1f5f9; color: #475569;">
                            <i class="fas fa-tag"></i>
                            {{ $cat_stat['name'] }}: {!! $cat_stat['count'] !!}
                        </span>
                    @endforeach
                    <span class="category-badge" style="background: #f1f5f9; color: #475569;">
                        <i class="fas fa-users"></i>
                        Nominators: {!! $stats['unique_nominators'] !!}
                    </span>
                    <span class="category-badge" style="background: #f1f5f9; color: #475569;">
                        <i class="fas fa-user-check"></i>
                        Nominees: {!! $stats['unique_nominees'] !!}
                    </span>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-bar">
            <form method="GET" class="row g-3 align-items-end" id="filterForm">
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Search</label>
                    <input type="text" name="search" class="form-control filter-input" 
                           placeholder="Name or employee code..." 
                           value="{{ $search }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">Category</label>
                    <select name="category" class="form-select filter-select">
                        <option value="0">All Categories</option>
                        @foreach ($categories as $cat)
                        <option value="{!! $cat['id'] !!}" {!! $category_filter == $cat['id'] ? 'selected' : '' !!}>
                            {{ $cat['name'] }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">Status</label>
                    <select name="status" class="form-select filter-select">
                        <option value="">All Status</option>
                        <option value="pending" {!! $status_filter == 'pending' ? 'selected' : '' !!}>Pending</option>
                        <option value="reviewed" {!! $status_filter == 'reviewed' ? 'selected' : '' !!}>Reviewed</option>
                        <option value="approved" {!! $status_filter == 'approved' ? 'selected' : '' !!}>Approved</option>
                        <option value="rejected" {!! $status_filter == 'rejected' ? 'selected' : '' !!}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">Read Status</label>
                    <select name="read_status" class="form-select filter-select">
                        <option value="">All</option>
                        <option value="unread" {!! $read_filter == 'unread' ? 'selected' : '' !!}>Unread</option>
                        <option value="read" {!! $read_filter == 'read' ? 'selected' : '' !!}>Read</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-gradient w-100">
                            <i class="fas fa-filter me-2"></i>Apply Filters
                        </button>
                        @if ($category_filter || $status_filter || $read_filter || $search)
                            <a href="all-nominations" class="btn-outline">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Filter Info and Export Bar -->
        <div class="filter-info">
            <div>
                <i class="fas fa-chart-simple me-2" style="color: var(--kiboko-blue);"></i>
                <strong>Current View:</strong> 
                @php $filter_parts = [];
                if ($category_filter > 0) {
                    $cat_name = $filter_category_row;
                    $filter_parts[] = "Category: " . ($cat_name['name'] ?? 'Selected');
                }
                if ($status_filter) $filter_parts[] = "Status: " . ucfirst($status_filter);
                if ($read_filter) $filter_parts[] = "Read: " . ucfirst($read_filter);
                if ($search) $filter_parts[] = "Search: " . htmlspecialchars($search);
                echo !empty($filter_parts) ? implode(' • ', $filter_parts) : 'All Nominations'; @endphp
                <span class="badge bg-light ms-2">{!! count($nominations) !!} records</span>
            </div>
            <div>
                <button type="button" class="btn-export" onclick="exportFilteredData()">
                    <i class="fas fa-file-excel me-2"></i>Export Filtered Data
                </button>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="d-flex gap-2 align-items-center" id="bulkActions" style="display: none;">
                <span class="text-muted me-2" id="selectedCount">0 selected</span>
                <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('approve')">
                    <i class="fas fa-check-circle"></i> Approve
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('reject')">
                    <i class="fas fa-times-circle"></i> Reject
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="bulkAction('mark_read')">
                    <i class="fas fa-eye"></i> Mark Read
                </button>
                <button type="button" class="btn btn-sm btn-warning" onclick="bulkAction('mark_unread')">
                    <i class="fas fa-eye-slash"></i> Mark Unread
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleSelectAll()">
                    <i class="fas fa-check-double"></i> Select All
                </button>
            </div>
        </div>

        <!-- Bulk Action Form -->
        <form id="bulkForm" method="POST">
@csrf
            <input type="hidden" name="bulk_action" id="bulk_action">
            <div id="selectedIdsContainer"></div>
        </form>

        <!-- Nominations Table -->
        <div class="table-container">
            <table class="nominations-table table">
                <thead>
                    <tr>
                        <th width="30"><input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()"></th>
                        <th>Nominator → Nominee</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Read</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($nominations) > 0)
                        @foreach ($nominations as $nom)
                        <tr>
                            <td>
                                <input type="checkbox" class="select-checkbox nomination-checkbox" 
                                       value="{!! $nom['id'] !!}" onchange="updateBulkActions()">
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="text-center">
                                        <div class="profile-avatar-sm">
                                            @if ($nom['nominator_photo'])
                                                <img src="../uploads/employees/{!! $nom['nominator_photo'] !!}">
                                            @else
                                                <i class="fas fa-user"></i>
                                            @endif
                                        </div>
                                        <div class="small fw-bold mt-1">{{ $nom['nominator_name'] }}</div>
                                        <div class="small text-muted">{{ $nom['nominator_code'] }}</div>
                                    </div>
                                    <i class="fas fa-arrow-right text-muted"></i>
                                    <div class="text-center">
                                        <div class="profile-avatar-sm">
                                            @if ($nom['nominee_photo'])
                                                <img src="../uploads/employees/{!! $nom['nominee_photo'] !!}">
                                            @else
                                                <i class="fas fa-user"></i>
                                            @endif
                                        </div>
                                        <div class="small fw-bold mt-1">{{ $nom['nominee_name'] }}</div>
                                        <div class="small text-muted">{{ $nom['nominee_code'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="category-badge" style="background: {!! $nom['category_color'] !!}20; color: {!! $nom['category_color'] !!};">
                                    <i class="fas fa-{!! $nom['category_icon'] !!}"></i>
                                    {{ $nom['category_name'] }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{!! $nom['status'] !!}">
                                    <i class="fas fa-{!! $nom['status'] == 'pending' ? 'clock' : ($nom['status'] == 'reviewed' ? 'check' : ($nom['status'] == 'approved' ? 'check-circle' : 'times')) !!}"></i>
                                    {!! ucfirst($nom['status']) !!}
                                </span>
                            </td>
                            <td>
                                <span class="read-badge {!! $nom['is_read'] ? 'read-yes' : 'read-no' !!}">
                                    <i class="fas fa-{!! $nom['is_read'] ? 'eye' : 'eye-slash' !!}"></i>
                                    {!! $nom['is_read'] ? 'Read' : 'Unread' !!}
                                </span>
                            </td>
                            <td>
                                <div class="small fw-bold">{!! date('M d, Y', strtotime($nom['created_at'])) !!}</div>
                                <div class="small text-muted">{!! date('h:i A', strtotime($nom['created_at'])) !!}</div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="view-nomination?id={!! $nom['id'] !!}" class="action-btn" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="action-btn" title="Quick Edit" 
                                            onclick="quickEditNomination({{ json_encode($nom) }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if (!$nom['is_read'])
                                        <button type="button" class="action-btn" title="Mark as Read" 
                                                onclick="markAsRead({!! $nom['id'] !!})">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No nominations found</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Edit Modal -->
    <div class="modal fade" id="quickEditModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
@csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-edit me-2 text-gradient"></i>Quick Edit Nomination
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="nomination_id" id="quick_edit_id">
                        <input type="hidden" name="update_nomination" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nominator</label>
                            <p class="form-control-static" id="quick_edit_nominator"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nominee</label>
                            <p class="form-control-static" id="quick_edit_nominee"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <p class="form-control-static" id="quick_edit_category"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select" id="quick_edit_status">
                                <option value="pending">Pending</option>
                                <option value="reviewed">Reviewed</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Admin Notes</label>
                            <textarea name="admin_notes" class="form-control" rows="3" id="quick_edit_notes" placeholder="Add notes about this nomination..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let quickEditModal;
        
        document.addEventListener('DOMContentLoaded', function() {
            quickEditModal = new bootstrap.Modal(document.getElementById('quickEditModal'));
            
            // Auto-hide messages
            setTimeout(() => {
                document.querySelectorAll('.message-alert').forEach(el => {
                    if (el) el.remove();
                });
            }, 5000);
        });
        
        function exportFilteredData() {
            // Get current form data and append export parameter
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            let url = '?' + new URLSearchParams(formData).toString();
            url += '&export_excel=1';
            window.open(url, '_blank');
        }
        
        function quickEditNomination(nom) {
            document.getElementById('quick_edit_id').value = nom.id;
            document.getElementById('quick_edit_nominator').innerHTML = `<strong>${escapeHtml(nom.nominator_name)}</strong> (${escapeHtml(nom.nominator_code)})`;
            document.getElementById('quick_edit_nominee').innerHTML = `<strong>${escapeHtml(nom.nominee_name)}</strong> (${escapeHtml(nom.nominee_code)})`;
            document.getElementById('quick_edit_category').innerHTML = escapeHtml(nom.category_name);
            document.getElementById('quick_edit_status').value = nom.status;
            document.getElementById('quick_edit_notes').value = nom.admin_notes || '';
            quickEditModal.show();
        }
        
        function markAsRead(id) {
            if(confirm('Mark this nomination as read?')) {
                fetch('api/mark_as_read', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                }).then(response => response.json()).then(data => {
                    if(data.success) location.reload();
                    else alert('Error marking as read');
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Error marking as read');
                });
            }
        }
        
        // Bulk Actions
        let selectedNominations = new Set();
        
        function toggleSelectAll() {
            const checkboxes = document.querySelectorAll('.nomination-checkbox');
            const selectAll = document.getElementById('selectAllCheckbox');
            const isChecked = selectAll.checked;
            
            checkboxes.forEach(cb => {
                cb.checked = isChecked;
                if(isChecked) selectedNominations.add(cb.value);
                else selectedNominations.delete(cb.value);
            });
            updateBulkActions();
        }
        
        function updateBulkActions() {
            const checkboxes = document.querySelectorAll('.nomination-checkbox:checked');
            selectedNominations.clear();
            checkboxes.forEach(cb => selectedNominations.add(cb.value));
            
            const count = selectedNominations.size;
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');
            
            if(count > 0) {
                bulkActions.style.display = 'flex';
                selectedCount.textContent = `${count} selected`;
            } else {
                bulkActions.style.display = 'none';
            }
            
            // Update select all checkbox
            const allCheckboxes = document.querySelectorAll('.nomination-checkbox');
            const selectAll = document.getElementById('selectAllCheckbox');
            if(selectAll) {
                selectAll.checked = allCheckboxes.length > 0 && allCheckboxes.length === checkboxes.length;
            }
        }
        
        function bulkAction(action) {
            if(selectedNominations.size === 0) {
                alert('Please select at least one nomination');
                return;
            }
            
            let message = '';
            switch(action) {
                case 'delete': message = `Are you sure you want to delete ${selectedNominations.size} nomination(s)? This cannot be undone.`; break;
                case 'approve': message = `Approve ${selectedNominations.size} nomination(s)?`; break;
                case 'reject': message = `Reject ${selectedNominations.size} nomination(s)?`; break;
                case 'mark_read': message = `Mark ${selectedNominations.size} nomination(s) as read?`; break;
                case 'mark_unread': message = `Mark ${selectedNominations.size} nomination(s) as unread?`; break;
            }
            
            if(confirm(message)) {
                const container = document.getElementById('selectedIdsContainer');
                container.innerHTML = '';
                selectedNominations.forEach(id => {
                    container.innerHTML += `<input type="hidden" name="selected_ids[]" value="${id}">`;
                });
                document.getElementById('bulk_action').value = action;
                document.getElementById('bulkForm').submit();
            }
        }
        
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }
        
        // Initialize checkboxes
        document.querySelectorAll('.nomination-checkbox').forEach(cb => {
            cb.addEventListener('change', updateBulkActions);
        });
    </script>
</body>
</html>
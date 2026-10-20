<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: #f8fafc;
            font-family: 'Plus Jakarta Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .page-header h2 {
            font-size: 1.8rem;
            font-weight: 800;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
        }
        
        .page-header p {
            color: #64748b;
            margin: 0;
            font-size: 0.9rem;
        }
        
        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-gradient {
            background: var(--grad);
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
            font-size: 0.9rem;
            cursor: pointer;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.2);
            color: white;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        
        .stat-icon.blue {
            background: #e0f2fe;
            color: #009ada;
        }
        
        .stat-icon.green {
            background: #dcfce7;
            color: #16a34a;
        }
        
        .stat-icon.red {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .stat-info .stat-number {
            font-size: 1.6rem;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.1;
        }
        
        .stat-info .stat-label {
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .filter-bar {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
        }
        
        .search-box {
            position: relative;
            flex: 1;
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        
        .search-box input {
            padding: 10px 15px 10px 42px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            width: 100%;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: #009ada;
            box-shadow: 0 0 0 3px rgba(0,154,218,0.1);
        }
        
        .filter-btn {
            padding: 8px 16px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            background: white;
            color: #475569;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .filter-btn:hover {
            border-color: #009ada;
            color: #009ada;
        }
        
        .filter-btn.active {
            background: var(--grad);
            color: white;
            border-color: transparent;
        }
        
        .user-table {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
            overflow-x: auto;
        }
        
        .user-table table {
            margin: 0;
        }
        
        .user-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px;
            border: none;
            white-space: nowrap;
        }
        
        .user-table tbody td {
            padding: 12px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
        }
        
        .user-table tbody tr:hover {
            background: #f8fafc;
        }
        
        .status-badge {
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }
        
        .status-active {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .role-badge {
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }
        
        .role-admin {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .role-hr {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .role-manager {
            background: #fef3c7;
            color: #92400e;
        }
        
        .role-employee {
            background: #f1f5f9;
            color: #334155;
        }
        
        .role-director {
            background: #ede9fe;
            color: #5b21b6;
        }
        
        .action-buttons {
            display: flex;
            gap: 4px;
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
            transition: all 0.2s;
            font-size: 0.8rem;
        }
        
        .action-btn:hover {
            color: white;
            transform: translateY(-2px);
        }
        
        .action-btn.edit:hover { background: #009ada; border-color: #009ada; }
        .action-btn.activate:hover { background: #16a34a; border-color: #16a34a; }
        .action-btn.deactivate:hover { background: #f59e0b; border-color: #f59e0b; }
        .action-btn.delete:hover { background: #dc2626; border-color: #dc2626; }
        
        .export-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .export-dropdown-btn {
            background: #16a34a;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .export-dropdown-btn:hover {
            background: #15803d;
        }
        
        .export-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 5px;
            background: white;
            min-width: 220px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            z-index: 1000;
            overflow: hidden;
            border: 1px solid #eef2f6;
        }
        
        .export-dropdown:hover .export-dropdown-content,
        .export-dropdown-content.show {
            display: block;
        }
        
        .export-dropdown-content a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            color: #334155;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s;
            border-bottom: 1px solid #f8fafc;
        }
        
        .export-dropdown-content a:last-child {
            border-bottom: none;
        }
        
        .export-dropdown-content a:hover {
            background: #f8fafc;
            color: #009ada;
        }
        
        .export-dropdown-content a i {
            width: 20px;
            text-align: center;
        }
        
        .export-dropdown-content .divider {
            height: 1px;
            background: #e2e8f0;
            margin: 5px 0;
        }
        
        .export-dropdown-content .dropdown-header {
            padding: 8px 16px 4px;
            font-size: 0.7rem;
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .header-actions {
                width: 100%;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    @include('admin.partials.sidebar')
    
    <!-- Main Content -->
    <div class="main-content">
        @php
            $msgTexts = [
                'added' => 'User added successfully!',
                'updated' => 'User updated successfully!',
                'deleted' => 'User deleted successfully!',
                'activated' => 'User activated successfully!',
                'deactivated' => 'User deactivated successfully!',
            ];
            $msgKey = (string) request()->query('msg', '');
        @endphp
        @if (isset($msgTexts[$msgKey]))
            <div class="alert alert-success alert-dismissible fade show" style="border-radius: 12px; border: none; background: #d1fae5; color: #065f46;">
                <i class="fas fa-check-circle me-2"></i>
                {{ $msgTexts[$msgKey] }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @elseif ($msgKey === 'delete_failed')
            <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 12px; border: none;">
                <i class="fas fa-exclamation-triangle me-2"></i>
                This user could not be deleted because other records (tickets, nominations, assets...) still refer to them. Deactivate the user instead.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h2><i class="fas fa-users me-2"></i>User Management</h2>
                <p>Manage employee accounts and permissions</p>
            </div>
            <div class="header-actions">
                <!-- Export Dropdown -->
                <div class="export-dropdown">
                    <button class="export-dropdown-btn" onclick="toggleExportDropdown(event)">
                        <i class="fas fa-file-excel"></i> Export to Excel <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
                    </button>
                    <div class="export-dropdown-content" id="exportDropdown">
                        <div class="dropdown-header">Export Options</div>
                        <a href="?export=all{!! $search ? '&search=' . urlencode($search) : '' !!}">
                            <i class="fas fa-users" style="color: #009ada;"></i>
                            <div>
                                <div style="font-weight: 600;">All Users</div>
                                <small style="color: #94a3b8; font-size: 0.7rem;">{!! $total_users !!} records</small>
                            </div>
                        </a>
                        <a href="?export=active{!! $search ? '&search=' . urlencode($search) : '' !!}">
                            <i class="fas fa-check-circle" style="color: #16a34a;"></i>
                            <div>
                                <div style="font-weight: 600;">Active Users Only</div>
                                <small style="color: #94a3b8; font-size: 0.7rem;">{!! $active_users !!} records</small>
                            </div>
                        </a>
                        <a href="?export=inactive{!! $search ? '&search=' . urlencode($search) : '' !!}">
                            <i class="fas fa-times-circle" style="color: #dc2626;"></i>
                            <div>
                                <div style="font-weight: 600;">Inactive Users Only</div>
                                <small style="color: #94a3b8; font-size: 0.7rem;">{!! $inactive_users !!} records</small>
                            </div>
                        </a>
                        @if ($search)
                            <div class="divider"></div>
                            <div class="dropdown-header">With Current Search</div>
                            <small style="padding: 0 16px 8px; display: block; color: #64748b; font-size: 0.7rem;">
                                Search: "{{ $search }}"
                            </small>
                        @endif
                    </div>
                </div>
                
                <a href="user-form" class="btn-gradient">
                    <i class="fas fa-plus-circle"></i> Add New User
                </a>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number">{!! $total_users !!}</div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number" style="color: #16a34a;">{!! $active_users !!}</div>
                    <div class="stat-label">Active Users</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number" style="color: #dc2626;">{!! $inactive_users !!}</div>
                    <div class="stat-label">Inactive Users</div>
                </div>
            </div>
        </div>
        
        <!-- Filter Bar -->
        <div class="filter-bar">
            <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search by code, name or email..." 
                           value="{{ $search }}">
                </div>
                
                <div class="d-flex gap-2 flex-wrap">
                    <a href="?{!! $search ? 'search=' . urlencode($search) . '&' : '' !!}" 
                       class="filter-btn {!! $status_filter === '' ? 'active' : '' !!}">
                        <i class="fas fa-list"></i> All
                    </a>
                    <a href="?status=active{!! $search ? '&search=' . urlencode($search) : '' !!}" 
                       class="filter-btn {!! $status_filter === 'active' ? 'active' : '' !!}">
                        <i class="fas fa-check-circle"></i> Active
                    </a>
                    <a href="?status=inactive{!! $search ? '&search=' . urlencode($search) : '' !!}" 
                       class="filter-btn {!! $status_filter === 'inactive' ? 'active' : '' !!}">
                        <i class="fas fa-times-circle"></i> Inactive
                    </a>
                </div>
                
                <button type="submit" class="btn-gradient" style="padding: 8px 16px;">
                    <i class="fas fa-search"></i> Search
                </button>
                
                @if ($search || $status_filter)
                    <a href="users" class="filter-btn" style="border-color: #dc2626; color: #dc2626;">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
                @endif
            </form>
        </div>
        
        <!-- Users Table -->
        <div class="user-table">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee Code</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($users) > 0)
                        @foreach ($users as $user)
                            <tr>
                                <td>#{!! $user['id'] !!}</td>
                                <td><strong>{{ $user['employee_code'] ?? '' }}</strong></td>
                                <td>{{ $user['full_name'] ?? '' }}</td>
                                <td>{{ $user['email'] ?? '-' }}</td>
                                <td>{{ $user['department'] ?? '-' }}</td>
                                <td>
                                    @php 
                                    $role = $user['role'] ?? 'employee';
                                    $role_class = 'role-' . $role;
@endphp
                                    <span class="role-badge {!! $role_class !!}">
                                        {!! ucfirst($role) !!}
                                    </span>
                                </td>
                                <td>
                                    @if ($user['status'])
                                        <span class="status-badge status-active">
                                            <i class="fas fa-circle me-1" style="font-size: 7px;"></i> Active
                                        </span>
                                    @else
                                        <span class="status-badge status-inactive">
                                            <i class="fas fa-circle me-1" style="font-size: 7px;"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    {!! $user['last_login'] ? date('M d, Y', strtotime($user['last_login'])) : 'Never' !!}
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="user-form?id={!! $user['id'] !!}" 
                                           class="action-btn edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        @if ($user['id'] != $current_admin_id)
                                            @if ($user['status'])
                                                <form method="POST" action="users?action=deactivate&id={!! $user['id'] !!}" class="d-inline"
                                                      onsubmit="return confirm('Deactivate this user?')">@csrf
                                                    <button type="submit" class="action-btn deactivate" title="Deactivate">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="users?action=activate&id={!! $user['id'] !!}" class="d-inline"
                                                      onsubmit="return confirm('Activate this user?')">@csrf
                                                    <button type="submit" class="action-btn activate" title="Activate">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <form method="POST" action="users?action=delete&id={!! $user['id'] !!}" class="d-inline"
                                                  onsubmit="return confirm('Are you sure? This cannot be undone.')">@csrf
                                                <button type="submit" class="action-btn delete" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="action-btn" style="opacity: 0.3; cursor: not-allowed;" title="You cannot modify yourself">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3" style="opacity: 0.3;"></i>
                                <p class="text-muted">No users found</p>
                                @if ($search || $status_filter)
                                    <a href="users" class="btn-gradient" style="margin-top: 10px; display: inline-flex;">
                                        <i class="fas fa-times"></i> Clear Filters
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle export dropdown
        function toggleExportDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('exportDropdown');
            dropdown.classList.toggle('show');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('exportDropdown');
            const button = document.querySelector('.export-dropdown-btn');
            if (dropdown && !dropdown.contains(event.target) && !button.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
        
        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(el => {
                if (el) el.remove();
            });
        }, 5000);
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Directory - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        
        body {
            background: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .page-header h2 {
            font-size: 2rem;
            font-weight: 800;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
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
        
        .btn-outline-gradient {
            background: white;
            border: 2px solid transparent;
            border-image: var(--grad);
            border-image-slice: 1;
            color: #009ada;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-outline-gradient:hover {
            background: var(--grad);
            color: white;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--kiboko-blue);
            line-height: 1.2;
        }
        
        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .stat-number.green { color: #16a34a; }
        .stat-number.red { color: #dc2626; }
        .stat-number.orange { color: #f59e0b; }
        
        .table-container {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
        }
        
        .employee-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }
        
        .employee-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 15px;
            border: none;
        }
        
        .employee-table tbody tr {
            background: white;
            border-radius: 12px;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        
        .employee-table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,154,218,0.1);
        }
        
        .employee-table td {
            padding: 15px;
            vertical-align: middle;
            border: none;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .employee-avatar {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            object-fit: cover;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--kiboko-blue);
            font-size: 1.2rem;
        }
        
        .employee-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .employee-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 3px;
        }
        
        .employee-code {
            font-size: 0.75rem;
            color: #64748b;
        }
        
        .company-badge {
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            color: #475569;
        }
        
        .role-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .role-admin { background: #fee2e2; color: #991b1b; }
        .role-hr { background: #dbeafe; color: #1e40af; }
        .role-manager { background: #fef3c7; color: #92400e; }
        .role-employee { background: #dcfce7; color: #166534; }
        .role-director { background: #ede9fe; color: #5b21b6; }
        
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .status-active { background: #dcfce7; color: #166534; }
        .status-inactive { background: #fee2e2; color: #991b1b; }
        
        .badge-synced {
            background: #dcfce7;
            color: #166534;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .badge-not-synced {
            background: #fee2e2;
            color: #991b1b;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }
        
        .action-btn {
            width: 35px;
            height: 35px;
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
        
        .action-btn.delete:hover { background: #dc3545; }
        .action-btn.password:hover { background: #f59e0b; }
        .action-btn.sync:hover { background: #16a34a; }
        
        .modal-content {
            border: none;
            border-radius: 20px;
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
        
        .modal-footer {
            padding: 15px 25px;
            background: #f8fafc;
            border-top: 1px solid #eef2f6;
        }
        
        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.85rem;
            margin-bottom: 5px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 3px rgba(0,154,218,0.1);
        }
        
        .photo-upload {
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #f8fafc;
        }
        
        .photo-upload:hover {
            border-color: var(--kiboko-blue);
            background: #f0f9ff;
        }
        
        .photo-upload i {
            font-size: 2rem;
            color: var(--kiboko-blue);
            margin-bottom: 10px;
        }
        
        .photo-preview {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            object-fit: cover;
            margin-top: 10px;
        }
        
        .message-alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .message-alert.success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .message-alert.danger { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .message-alert.warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #eef2f6;
        }
        
        .company-filter {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 8px 16px;
            border-radius: 30px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #475569;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: var(--grad);
            color: white;
            border-color: transparent;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="page-header">
            <h2><i class="fas fa-users me-2"></i>Employee Directory</h2>
            <div class="d-flex gap-2 flex-wrap">
                <form method="POST" style="display:inline-block;">@csrf
                    <button type="submit" name="sync_all" class="btn-gradient" onclick="return confirm('Sync ALL employees to K-Academy? This will sync all {!! $total_with_data !!} employees with valid email and employee code.')">
                        <i class="fas fa-sync me-2"></i>Sync All ({!! $total_with_data !!})
                    </button>
                </form>
                <button class="btn-gradient" onclick="openAddModal()">
                    <i class="fas fa-plus me-2"></i>Add New Employee
                </button>
            </div>
        </div>
        
        <!-- Message Alert -->
        @if ($message !== null)
        <div class="message-alert {!! $message_type ?? 'info' !!}">
            <span><i class="fas fa-info-circle me-2"></i>{!! $message !!}</span>
            <button class="btn-close float-end" onclick="this.parentElement.remove()"></button>
        </div>
        @endif
        
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{!! $total_employees !!}</div>
                <div class="stat-label">Total Employees</div>
            </div>
            <div class="stat-card">
                <div class="stat-number green">{!! $active_employees !!}</div>
                <div class="stat-label">Active Employees</div>
            </div>
            <div class="stat-card">
                <div class="stat-number green">{!! $synced_count !!}</div>
                <div class="stat-label"><i class="fas fa-check-circle me-1"></i>Synced to K-Academy</div>
            </div>
            <div class="stat-card">
                <div class="stat-number red">{!! $not_synced_count !!}</div>
                <div class="stat-label"><i class="fas fa-exclamation-circle me-1"></i>Not Synced</div>
            </div>
            <div class="stat-card">
                <div class="stat-number orange">{!! $total_with_data !!}</div>
                <div class="stat-label">Can be Synced (have email + code)</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{!! $total_companies_count !!}</div>
                <div class="stat-label">Companies with Employees</div>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="tableSearch" placeholder="Search employees...">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="company-filter" id="companyFilter">
                        <button class="filter-btn active" data-company="all">All Companies</button>
                        @if (count($companies) > 0)
                            @foreach ($companies as $company)
                        <button class="filter-btn" data-company="{!! $company['id'] !!}">
                            {{ $company['name'] ?? '' }}
                        </button>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Employees Table -->
        <div class="table-container">
            <table class="employee-table" id="employeeTable">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Contact</th>
                        <th>Company</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>K-Academy</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($employees) > 0)
                        @foreach ($employees as $emp)
                        @php
                            $is_synced = $emp_synced[$emp['id']];
@endphp
                        <tr data-company="{!! $emp['company_id'] !!}">
                            <td>
                                <div class="employee-info">
                                    @if ($emp['photo'])
                                        <img src="../uploads/employees/{{ $emp['photo'] ?? '' }}" 
                                             class="employee-avatar" alt="{{ $emp['full_name'] ?? '' }}">
                                    @else
                                        <div class="employee-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="employee-name">{{ $emp['full_name'] ?? '' }}</div>
                                        <div class="employee-code">{{ $emp['employee_code'] ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><i class="fas fa-envelope fa-sm text-muted me-1"></i>{{ $emp['email'] ?? '' }}</div>
                                @if (!empty($emp['phone']))
                                    <div class="mt-1"><i class="fas fa-phone fa-sm text-muted me-1"></i>{{ $emp['phone'] ?? '' }}</div>
                                @endif
                                @if (!empty($emp['extension']))
                                    <div class="mt-1"><i class="fas fa-phone-alt fa-sm text-muted me-1"></i>Ext: {{ $emp['extension'] ?? '' }}</div>
                                @endif
                            </td>
                            <td>
                                @if (!empty($emp['company_name']))
                                    <span class="company-badge">
                                        {{ $emp['company_name'] ?? '' }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $emp['department'] ?? '—' }}</td>
                            <td>{{ $emp['position'] ?? '—' }}</td>
                            <td>
                                @php
                                $role_class = 'role-employee';
                                $role = $emp['role'] ?? 'employee';
                                if($role == 'admin') $role_class = 'role-admin';
                                elseif($role == 'hr') $role_class = 'role-hr';
                                elseif($role == 'manager') $role_class = 'role-manager';
                                elseif($role == 'director') $role_class = 'role-director';
@endphp
                                <span class="role-badge {!! $role_class !!}">
                                    {!! ucfirst($role) !!}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge {!! ($emp['status'] ?? 0) ? 'status-active' : 'status-inactive' !!}">
                                    {!! ($emp['status'] ?? 0) ? 'Active' : 'Inactive' !!}
                                </span>
                            </td>
                            <td>
                                @if ($is_synced)
                                    <span class="badge-synced"><i class="fas fa-check-circle me-1"></i>Synced</span>
                                @else
                                    <span class="badge-not-synced"><i class="fas fa-exclamation-circle me-1"></i>Not Synced</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit={!! $emp['id'] !!}" class="action-btn" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if ($emp['id'] != session('admin_id'))
                                        <a href="#" class="action-btn password" title="Reset Password" 
                                           onclick="openPasswordModal({!! $emp['id'] !!}, '{{ addslashes($emp['full_name'] ?? '') }}')">
                                            <i class="fas fa-key"></i>
                                        </a>
                                        <form method="POST" action="manage-employees?toggle={!! $emp['id'] !!}" style="display:inline-block;"
                                              onsubmit="return confirm('Toggle status for {{ addslashes($emp['full_name'] ?? '') }}?')">@csrf
                                            <button type="submit" class="action-btn" title="Toggle Status">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="manage-employees?delete={!! $emp['id'] !!}" style="display:inline-block;"
                                              onsubmit="return confirm('Are you sure you want to delete {{ addslashes($emp['full_name'] ?? '') }}?')">@csrf
                                            <button type="submit" class="action-btn delete" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @if (!$is_synced && !empty($emp['email']) && !empty($emp['employee_code']))
                                            <form method="POST" style="display:inline-block;">@csrf
                                                <input type="hidden" name="employee_id" value="{!! $emp['id'] !!}">
                                                <button type="submit" name="sync_single" class="action-btn sync" title="Sync to K-Academy" 
                                                        onclick="return confirm('Sync {{ addslashes($emp['full_name'] ?? '') }} to K-Academy?')">
                                                    <i class="fas fa-sync"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>No employees found. Click "Add New Employee" to create one.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Add/Edit Employee Modal -->
    <div class="modal fade" id="employeeModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-user-plus me-2 text-gradient"></i>Add New Employee
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data" id="employeeForm">@csrf
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="edit_id" value="{!! $edit_employee ? $edit_employee['id'] : 0 !!}">
                        <input type="hidden" name="save_employee" value="1">
                        
                        <!-- Basic Information -->
                        <h6 class="fw-bold text-gradient mb-3">Basic Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Employee Code <span class="text-danger">*</span></label>
                                <input type="text" name="employee_code" id="employee_code" class="form-control" 
                                       value="{!! $edit_employee ? htmlspecialchars($edit_employee['employee_code'] ?? '') : '' !!}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" id="full_name" class="form-control" 
                                       value="{!! $edit_employee ? htmlspecialchars($edit_employee['full_name'] ?? '') : '' !!}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" 
                                       value="{!! $edit_employee ? htmlspecialchars($edit_employee['email'] ?? '') : '' !!}" required>
                            </div>
                        </div>
                        
                        <!-- Password (for new employees) -->
                        @if (!$edit_employee)
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" class="form-control" required>
                                <div class="form-text">Minimum 6 characters</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Contact Information -->
                        <h6 class="fw-bold text-gradient mb-3 mt-4">Contact Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control" 
                                       value="{!! $edit_employee ? htmlspecialchars($edit_employee['phone'] ?? '') : '' !!}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Extension</label>
                                <input type="text" name="extension" id="extension" class="form-control" 
                                       value="{!! $edit_employee ? htmlspecialchars($edit_employee['extension'] ?? '') : '' !!}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mobile</label>
                                <input type="text" name="mobile" id="mobile" class="form-control" 
                                       value="{!! $edit_employee ? htmlspecialchars($edit_employee['mobile'] ?? '') : '' !!}">
                            </div>
                        </div>
                        
                        <!-- Employment Information -->
                        <h6 class="fw-bold text-gradient mb-3 mt-4">Employment Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Company</label>
                                <select name="company_id" id="company_id" class="form-select">
                                    <option value="">-- Select Company --</option>
                                    @if (count($companies) > 0)
                                        @foreach ($companies as $company)
                                    <option value="{!! $company['id'] !!}" 
                                        {!! ($edit_employee && isset($edit_employee['company_id']) && $edit_employee['company_id'] == $company['id']) ? 'selected' : '' !!}>
                                        {{ $company['name'] ?? '' }}
                                    </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Department</label>
                                <input type="text" name="department" id="department" class="form-control" 
                                       value="{!! $edit_employee ? htmlspecialchars($edit_employee['department'] ?? '') : '' !!}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Position</label>
                                <input type="text" name="position" id="position" class="form-control" 
                                       value="{!! $edit_employee ? htmlspecialchars($edit_employee['position'] ?? '') : '' !!}">
                            </div>
                        </div>
                        
                        <!-- Role and Dates -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Role</label>
                                <select name="role" id="role" class="form-select">
                                    <option value="employee" {!! ($edit_employee && isset($edit_employee['role']) && $edit_employee['role'] == 'employee') ? 'selected' : '' !!}>Employee</option>
                                    <option value="manager" {!! ($edit_employee && isset($edit_employee['role']) && $edit_employee['role'] == 'manager') ? 'selected' : '' !!}>Manager</option>
                                    <option value="hr" {!! ($edit_employee && isset($edit_employee['role']) && $edit_employee['role'] == 'hr') ? 'selected' : '' !!}>HR</option>
                                    <option value="director" {!! ($edit_employee && isset($edit_employee['role']) && $edit_employee['role'] == 'director') ? 'selected' : '' !!}>Director</option>
                                    <option value="admin" {!! ($edit_employee && isset($edit_employee['role']) && $edit_employee['role'] == 'admin') ? 'selected' : '' !!}>Admin</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Birth Date</label>
                                <input type="date" name="birth_date" id="birth_date" class="form-control" 
                                       value="{!! $edit_employee ? $edit_employee['birth_date'] ?? '' : '' !!}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Join Date</label>
                                <input type="date" name="join_date" id="join_date" class="form-control" 
                                       value="{!! $edit_employee ? $edit_employee['join_date'] ?? '' : '' !!}">
                            </div>
                        </div>
                        
                        <!-- Emergency Contact -->
                        <h6 class="fw-bold text-gradient mb-3 mt-4">Emergency Contact</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Contact Name</label>
                                <input type="text" name="emergency_contact" id="emergency_contact" class="form-control" 
                                       value="{!! $edit_employee ? htmlspecialchars($edit_employee['emergency_contact'] ?? '') : '' !!}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Emergency Phone</label>
                                <input type="text" name="emergency_phone" id="emergency_phone" class="form-control" 
                                       value="{!! $edit_employee ? htmlspecialchars($edit_employee['emergency_phone'] ?? '') : '' !!}">
                            </div>
                        </div>
                        
                        <!-- Address and Notes -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Address</label>
                                <textarea name="address" id="address" class="form-control" rows="3">{!! $edit_employee ? htmlspecialchars($edit_employee['address'] ?? '') : '' !!}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3">{!! $edit_employee ? htmlspecialchars($edit_employee['notes'] ?? '') : '' !!}</textarea>
                            </div>
                        </div>
                        
                        <!-- Photo Upload -->
                        <h6 class="fw-bold text-gradient mb-3 mt-4">Profile Photo</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="photo-upload" id="photoUpload" onclick="document.getElementById('photo').click()">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p class="mb-1">Click to upload employee photo</p>
                                    <small class="text-muted">JPG, PNG, GIF (Max 2MB)</small>
                                    <input type="file" name="photo" id="photo" class="d-none" accept="image/*">
                                </div>
                                <div id="photoPreview" class="text-center mt-3" style="display: none;">
                                    <img src="" alt="Preview" class="photo-preview" id="previewImg">
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="clearPhoto()">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </div>
                                @if ($edit_employee && !empty($edit_employee['photo']))
                                <div class="text-center mt-3" id="currentPhoto">
                                    <img src="../uploads/employees/{{ $edit_employee['photo'] ?? '' }}" class="photo-preview">
                                    <p class="small text-muted mt-1">Current Photo</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-save me-2"></i>Save Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Password Reset Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-key me-2 text-gradient"></i>Reset Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="passwordForm">@csrf
                    <div class="modal-body">
                        <input type="hidden" name="employee_id" id="reset_employee_id">
                        
                        <p class="mb-3">Reset password for: <strong id="reset_employee_name"></strong></p>
                        
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" required minlength="6">
                            <div class="form-text">Minimum 6 characters</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" id="confirm_new_password" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="reset_password" class="btn btn-gradient">
                            <i class="fas fa-save me-2"></i>Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        let employeeModal;
        let passwordModal;
        
        document.addEventListener('DOMContentLoaded', function() {
            employeeModal = new bootstrap.Modal(document.getElementById('employeeModal'));
            passwordModal = new bootstrap.Modal(document.getElementById('passwordModal'));
            
            @if ((request()->query('edit') !== null) && $edit_employee)
            employeeModal.show();
            @endif
            
            // Initialize DataTable
            @if (count($employees) > 0)
            const table = $('#employeeTable').DataTable({
                pageLength: 25,
                language: {
                    search: "",
                    searchPlaceholder: "Search employees...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ employees",
                    infoEmpty: "Showing 0 to 0 of 0 employees",
                    infoFiltered: "(filtered from _MAX_ total employees)"
                },
                dom: 'rtip',
                initComplete: function() {
                    $('#employeeTable_filter').hide();
                }
            });
            
            // Connect custom search
            $('#tableSearch').on('keyup', function() {
                table.search(this.value).draw();
            });
            
            // Company filter
            $('#companyFilter .filter-btn').on('click', function() {
                $('#companyFilter .filter-btn').removeClass('active');
                $(this).addClass('active');
                
                const companyId = $(this).data('company');
                if (companyId === 'all') {
                    table.column(2).search('').draw();
                } else {
                    table.column(2).search('^' + companyId + '$', true, false).draw();
                }
            });
            @endif
            
            // Photo upload preview
            $('#photo').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#previewImg').attr('src', e.target.result);
                        $('#photoPreview').show();
                        $('#currentPhoto').hide();
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            // Password validation
            $('#passwordForm').on('submit', function(e) {
                const newPass = $('#new_password').val();
                const confirmPass = $('#confirm_new_password').val();
                
                if (newPass !== confirmPass) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                } else if (newPass.length < 6) {
                    e.preventDefault();
                    alert('Password must be at least 6 characters long!');
                }
            });
        });
        
        function openAddModal() {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-plus me-2 text-gradient"></i>Add New Employee';
            document.getElementById('edit_id').value = '0';
            document.getElementById('employee_code').value = '';
            document.getElementById('full_name').value = '';
            document.getElementById('email').value = '';
            document.getElementById('phone').value = '';
            document.getElementById('extension').value = '';
            document.getElementById('mobile').value = '';
            document.getElementById('company_id').value = '';
            document.getElementById('department').value = '';
            document.getElementById('position').value = '';
            document.getElementById('role').value = 'employee';
            document.getElementById('birth_date').value = '';
            document.getElementById('join_date').value = '';
            document.getElementById('emergency_contact').value = '';
            document.getElementById('emergency_phone').value = '';
            document.getElementById('address').value = '';
            document.getElementById('notes').value = '';
            
            const passwordField = document.getElementById('password');
            const confirmField = document.getElementById('confirm_password');
            if (passwordField) {
                passwordField.required = true;
            }
            if (confirmField) {
                confirmField.required = true;
            }
            
            $('#currentPhoto').hide();
            $('#photoPreview').hide();
            employeeModal.show();
        }
        
        function openPasswordModal(id, name) {
            document.getElementById('reset_employee_id').value = id;
            document.getElementById('reset_employee_name').textContent = name || 'Employee';
            document.getElementById('new_password').value = '';
            document.getElementById('confirm_new_password').value = '';
            passwordModal.show();
        }
        
        function clearPhoto() {
            $('#photo').val('');
            $('#photoPreview').hide();
            $('#currentPhoto').show();
        }
        
        // Auto-hide messages
        setTimeout(() => {
            document.querySelectorAll('.message-alert').forEach(el => {
                if (el) el.remove();
            });
        }, 5000);
    </script>
</body>
</html>
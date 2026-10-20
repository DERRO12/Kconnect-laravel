<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments - Kiboko Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --kiboko-purple: #8b5cf6;
            --kiboko-orange: #f59e0b;
            --grad-primary: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
            --grad-sales: linear-gradient(135deg, #f59e0b, #d97706);
            --grad-support: linear-gradient(135deg, #10b981, #059669);
            --grad-leadership: linear-gradient(135deg, #8b5cf6, #6d28d9);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        .wrapper {
            display: flex;
        }

        .sidebar {
            width: 280px;
            background: white;
            height: 100vh;
            position: fixed;
            box-shadow: 2px 0 20px rgba(0,0,0,0.05);
            overflow-y: auto;
            z-index: 100;
        }

        .sidebar-header {
            padding: 25px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }

        .sidebar-header h3 {
            font-weight: 800;
            background: var(--grad-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        .sidebar-menu {
            padding: 20px;
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: block;
            padding: 12px 15px;
            color: #475569;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: var(--grad-primary);
            color: white;
        }

        .sidebar-menu i {
            width: 25px;
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .content-header h2 {
            font-weight: 800;
            font-size: 2rem;
        }

        .btn-gradient {
            background: var(--grad-primary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,154,218,0.2);
            color: white;
        }

        .btn-outline-gradient {
            background: transparent;
            border: 2px solid var(--kiboko-blue);
            color: var(--kiboko-blue);
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-outline-gradient:hover {
            background: var(--kiboko-blue);
            color: white;
        }

        .company-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .company-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .company-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .company-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .company-name {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
        }

        .company-code {
            color: var(--kiboko-blue);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .department-tree {
            margin-top: 20px;
            padding-left: 20px;
            border-left: 2px dashed #e2e8f0;
        }

        .department-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid #e2e8f0;
        }

        .department-item:hover {
            background: white;
            border-color: var(--kiboko-blue);
        }

        .department-header {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .department-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }

        .icon-sales { background: var(--grad-sales); }
        .icon-support { background: var(--grad-support); }
        .icon-leadership { background: var(--grad-leadership); }

        .department-name {
            font-weight: 600;
            flex: 1;
        }

        .department-badge {
            padding: 3px 10px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
            color: white;
        }

        .badge-sales { background: var(--grad-sales); }
        .badge-support { background: var(--grad-support); }
        .badge-leadership { background: var(--grad-leadership); }

        .sub-departments {
            margin-left: 45px;
            margin-top: 10px;
            padding-left: 15px;
            border-left: 2px solid #e2e8f0;
        }

        .sub-dept-item {
            padding: 8px 12px;
            background: white;
            border-radius: 8px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.2s;
        }

        .sub-dept-item:hover {
            border-color: var(--kiboko-blue);
            transform: translateX(5px);
        }

        .sub-dept-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--kiboko-blue);
        }

        .head-info {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 5px;
        }

        .head-info i {
            color: var(--kiboko-blue);
            width: 18px;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            margin-left: auto;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            border: none;
            transition: all 0.2s;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .btn-edit { background: var(--kiboko-blue); }
        .btn-delete { background: #ef4444; }
        .btn-employees { background: var(--kiboko-green); }

        .stats-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
            border: 1px solid #e2e8f0;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--kiboko-blue);
        }

        .modal-content {
            border-radius: 20px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, #f8fafc, white);
            border-bottom: 1px solid #e2e8f0;
            padding: 20px;
        }

        .modal-body {
            padding: 25px;
        }

        .nav-tabs .nav-link {
            color: #475569;
            font-weight: 600;
            border: none;
            padding: 12px 25px;
        }

        .nav-tabs .nav-link.active {
            color: var(--kiboko-blue);
            border-bottom: 3px solid var(--kiboko-blue);
            background: transparent;
        }

        .badge-uganda { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .badge-tanzania { background: linear-gradient(135deg, #10b981, #059669); }
        .badge-kfs { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
        .badge-group { background: var(--grad-primary); }
    </style>
</head>
<body>
    <div class="wrapper">
            @include('admin.partials.sidebar')

        <!-- Main Content -->
        <div class="main-content">
            <!-- Alerts -->
            @if ($flash_success !== null)
                <div class="alert alert-success alert-dismissible fade show">
                    {!! $flash_success !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($flash_error !== null)
                <div class="alert alert-danger alert-dismissible fade show">
                    {!! $flash_error !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="content-header">
                <h2>
                    <i class="fas fa-sitemap me-2" style="color: var(--kiboko-blue);"></i>
                    Department Management
                </h2>
                <div>
                    <a href="manage-orgchart.php" class="btn btn-outline-gradient me-2">
                        <i class="fas fa-users me-2"></i>Manage Employees
                    </a>
                    <button class="btn-gradient" onclick="openCompanyModal()">
                        <i class="fas fa-plus me-2"></i>Add Company
                    </button>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stat-value">{!! $totalCompanies !!}</div>
                        <div class="text-muted">Companies</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stat-value">{!! $totalDepartments !!}</div>
                        <div class="text-muted">Total Departments</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stat-value">{!! $salesDepartments !!}</div>
                        <div class="text-muted">Sales Divisions</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stat-value">{!! $supportDepartments !!}</div>
                        <div class="text-muted">Support Depts</div>
                    </div>
                </div>
            </div>

            <!-- Companies and Departments -->
            <div class="row">
                @foreach ($companies as $company)
                <div class="col-12">
                    <div class="company-card">
                        <div class="company-header">
                            @if ($company['logo'])
                                <img src="../uploads/{!! $company['logo'] !!}" class="company-logo">
                            @else
                                <div class="company-logo">
                                    <i class="fas fa-building" style="color: var(--kiboko-blue);"></i>
                                </div>
                            @endif
                            <div>
                                <h3 class="company-name">{{ $company['name'] }}</h3>
                                @if ($company['code'])
                                    <span class="company-code">{!! $company['code'] !!}</span>
                                @endif
                            </div>
                            <div class="action-buttons ms-auto">
                                <button class="action-btn btn-edit" onclick="openCompanyModal({!! $company['id'] !!})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn btn-employees" onclick="window.location.href='manage-orgchart.php?filter_company_id={!! $company['id'] !!}'">
                                    <i class="fas fa-users"></i>
                                </button>
                                <button class="action-btn btn-delete" onclick="deleteCompany({!! $company['id'] !!})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        @if ($company['description'])
                            <p class="text-muted mb-3">{{ $company['description'] }}</p>
                        @endif

                        <!-- Departments for this company -->
                        <div class="department-tree">
                            @php
                            $topDepts = $topDeptsByCompany[$company['id']];
@endphp
                            @foreach ($topDepts as $dept)
                            @php
                                $typeClass = strtolower($dept['organization_type']);
@endphp
                            <div class="department-item" onclick="viewDepartment({!! $dept['id'] !!})">
                                <div class="department-header">
                                    <div class="department-icon icon-{!! $typeClass !!}">
                                        <i class="fas {!! $typeClass == 'sales' ? 'fa-chart-line' : 
                                                ($typeClass == 'support' ? 'fa-tools' : 'fa-crown') !!}"></i>
                                    </div>
                                    <div class="department-name">
                                        <strong>{{ $dept['name'] }}</strong>
                                        @if ($dept['code'])
                                            <span class="badge bg-light text-dark ms-2">{!! $dept['code'] !!}</span>
                                        @endif
                                        
                                        @if ($dept['head_name'])
                                            <div class="head-info">
                                                <i class="fas fa-user-tie"></i> Head: {!! $dept['head_name'] !!}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="department-badge badge-{!! $typeClass !!}">
                                        @php 
                                        if ($dept['organization_type'] == 'Sales') echo $dept['sales_division'];
                                        elseif ($dept['organization_type'] == 'Support') echo $dept['support_department'];
                                        else echo 'Leadership';
@endphp
                                    </span>
                                    <div class="action-buttons">
                                        <button class="action-btn btn-edit" onclick="event.stopPropagation(); openDepartmentModal({!! $dept['id'] !!})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn btn-employees" onclick="event.stopPropagation(); window.location.href='manage-orgchart.php?filter_department_id={!! $dept['id'] !!}'">
                                            <i class="fas fa-users"></i>
                                        </button>
                                        <button class="action-btn btn-delete" onclick="event.stopPropagation(); deleteDepartment({!! $dept['id'] !!})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                @include('admin.manage-departments-subtree', ['parent_id' => $dept['id']])
                            </div>
                            @endforeach

                            @if (count($topDepts) == 0)
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-folder-open fa-2x mb-2"></i>
                                    <p>No departments yet. Click "Add Department" to create one.</p>
                                    <button class="btn btn-sm btn-outline-primary" onclick="openDepartmentModal(0, {!! $company['id'] !!})">
                                        <i class="fas fa-plus me-2"></i>Add Department
                                    </button>
                                </div>
                            @else
                                <div class="text-center mt-3">
                                    <button class="btn btn-sm btn-outline-primary" onclick="openDepartmentModal(0, {!! $company['id'] !!})">
                                        <i class="fas fa-plus me-2"></i>Add Department
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Company Modal -->
    <div class="modal fade" id="companyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">@csrf
                    <input type="hidden" name="action" value="save_company">
                    <input type="hidden" name="id" id="companyId" value="">
                    <input type="hidden" name="existing_logo" id="existingLogo" value="">
                    
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-building me-2" style="color: var(--kiboko-blue);"></i>
                            <span id="companyModalTitle">Add Company</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="companyName" class="form-control" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Company Code</label>
                                    <input type="text" name="code" id="companyCode" class="form-control" placeholder="e.g., KELUG">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" id="companyDescription" class="form-control" rows="3"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" id="companyAddress" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" id="companyPhone" class="form-control">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" id="companyEmail" class="form-control">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Website</label>
                                    <input type="url" name="website" id="companyWebsite" class="form-control">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" name="sort_order" id="companySortOrder" class="form-control" value="0">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Logo</label>
                                    <input type="file" name="logo" class="form-control" accept="image/*">
                                    <div id="currentLogoContainer" style="display: none;" class="mt-2">
                                        <small>Current: </small>
                                        <img id="currentLogo" src="" width="50" height="50" class="rounded-circle">
                                    </div>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" name="status" class="form-check-input" id="companyStatus" checked>
                                    <label class="form-check-label" for="companyStatus">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-save me-2"></i>Save Company
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Department Modal -->
    <div class="modal fade" id="departmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">@csrf
                    <input type="hidden" name="action" value="save_department">
                    <input type="hidden" name="id" id="deptId" value="">
                    <input type="hidden" name="company_id" id="deptCompanyId" value="">
                    
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-folder me-2" style="color: var(--kiboko-blue);"></i>
                            <span id="deptModalTitle">Add Department</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Department Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="deptName" class="form-control" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Department Code</label>
                                    <input type="text" name="code" id="deptCode" class="form-control" placeholder="e.g., SALES-PG">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Company <span class="text-danger">*</span></label>
                                    <select name="company_id" id="deptCompanySelect" class="form-control" required onchange="filterParentDepartments()">
                                        <option value="">Select Company</option>
                                        @foreach ($companies as $comp)
                                            <option value="{!! $comp['id'] !!}">{{ $comp['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Organization Type <span class="text-danger">*</span></label>
                                    <select name="organization_type" id="deptOrgType" class="form-control" required onchange="toggleDeptFields()">
                                        <option value="">Select Type</option>
                                        <option value="Leadership">Leadership</option>
                                        <option value="Sales">Sales</option>
                                        <option value="Support">Support</option>
                                    </select>
                                </div>
                                
                                <!-- Sales Division Field -->
                                <div class="mb-3 dynamic-field" id="deptSalesField" style="display: none;">
                                    <label class="form-label">Sales Division <span class="text-danger">*</span></label>
                                    <select name="sales_division" id="deptSalesDivision" class="form-control">
                                        <option value="None">Select Division</option>
                                        <option value="P&G">P&G</option>
                                        <option value="Spark">Spark</option>
                                        <option value="Star">Star</option>
                                        <option value="CD">CD</option>
                                    </select>
                                </div>
                                
                                <!-- Support Department Field -->
                                <div class="mb-3 dynamic-field" id="deptSupportField" style="display: none;">
                                    <label class="form-label">Support Department <span class="text-danger">*</span></label>
                                    <select name="support_department" id="deptSupportDept" class="form-control">
                                        <option value="">Select Department</option>
                                        <option value="IT">IT</option>
                                        <option value="HR">HR</option>
                                        <option value="Internal Audit">Internal Audit</option>
                                        <option value="Legal">Legal</option>
                                        <option value="Warehouse">Warehouse</option>
                                        <option value="Procurement">Procurement</option>
                                        <option value="Logistics">Logistics</option>
                                        <option value="Administration">Administration</option>
                                        <option value="Finance">Finance</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Parent Department</label>
                                    <select name="parent_id" id="deptParent" class="form-control">
                                        <option value="">-- Top Level --</option>
                                        @foreach ($allDepts as $dept)
                                            <option value="{!! $dept['id'] !!}" data-company="{!! $dept['company_id'] !!}">
                                                {{ $dept['company_name'] . ' - ' . $dept['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Level <span class="text-danger">*</span></label>
                                    <input type="number" name="level" id="deptLevel" class="form-control" min="1" max="10" value="1" required>
                                    <small class="text-muted">1=Top Level, 2=Division, 3=Department, 4+=Sub-Department</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Department Head</label>
                                    <select name="head_employee_id" id="deptHead" class="form-control">
                                        <option value="">-- Select Head --</option>
                                        @foreach ($employees as $emp)
                                            <option value="{!! $emp['id'] !!}">
                                                {{ $emp['employee_name'] . ' - ' . $emp['position'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" id="deptDescription" class="form-control" rows="3"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Icon Class</label>
                                    <input type="text" name="icon" id="deptIcon" class="form-control" placeholder="e.g., fa-chart-line">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Color</label>
                                    <input type="color" name="color" id="deptColor" class="form-control form-control-color" value="#009ada">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" name="sort_order" id="deptSortOrder" class="form-control" value="0">
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" name="status" class="form-check-input" id="deptStatus" checked>
                                    <label class="form-check-label" for="deptStatus">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-save me-2"></i>Save Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Department View Modal -->
    <div class="modal fade" id="viewDepartmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-sitemap me-2" style="color: var(--kiboko-blue);"></i>
                        Department Structure
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="departmentViewContent">
                    <!-- Loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Store data
        let companies = {!! json_encode($companies) !!};
        
        let departments = {!! json_encode($departments) !!};

        // Toggle department fields based on organization type
        function toggleDeptFields() {
            let type = document.getElementById('deptOrgType').value;
            let salesField = document.getElementById('deptSalesField');
            let supportField = document.getElementById('deptSupportField');
            
            salesField.style.display = 'none';
            supportField.style.display = 'none';
            
            if (type === 'Sales') {
                salesField.style.display = 'block';
                document.getElementById('deptSalesDivision').setAttribute('required', 'required');
                document.getElementById('deptSupportDept').removeAttribute('required');
            } else if (type === 'Support') {
                supportField.style.display = 'block';
                document.getElementById('deptSupportDept').setAttribute('required', 'required');
                document.getElementById('deptSalesDivision').removeAttribute('required');
            } else {
                document.getElementById('deptSalesDivision').removeAttribute('required');
                document.getElementById('deptSupportDept').removeAttribute('required');
            }
        }

        // Filter parent departments based on selected company
        function filterParentDepartments() {
            let companyId = document.getElementById('deptCompanySelect').value;
            $('#deptParent option').each(function() {
                let optionCompany = $(this).data('company');
                if (optionCompany && optionCompany != companyId) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        }

        // Company Modal Functions
        function openCompanyModal(id = null) {
            let modal = new bootstrap.Modal(document.getElementById('companyModal'));
            
            if (id) {
                let company = companies.find(c => c.id == id);
                if (company) {
                    document.getElementById('companyModalTitle').textContent = 'Edit Company';
                    document.getElementById('companyId').value = company.id;
                    document.getElementById('companyName').value = company.name;
                    document.getElementById('companyCode').value = company.code || '';
                    document.getElementById('companyDescription').value = company.description || '';
                    document.getElementById('companyAddress').value = company.address || '';
                    document.getElementById('companyPhone').value = company.phone || '';
                    document.getElementById('companyEmail').value = company.email || '';
                    document.getElementById('companyWebsite').value = company.website || '';
                    document.getElementById('companySortOrder').value = company.sort_order || 0;
                    document.getElementById('companyStatus').checked = company.status == 1;
                    
                    if (company.logo) {
                        document.getElementById('existingLogo').value = company.logo;
                        document.getElementById('currentLogo').src = '../uploads/' + company.logo;
                        document.getElementById('currentLogoContainer').style.display = 'block';
                    } else {
                        document.getElementById('currentLogoContainer').style.display = 'none';
                    }
                }
            } else {
                document.getElementById('companyModalTitle').textContent = 'Add Company';
                document.getElementById('companyId').value = '';
                document.getElementById('companyName').value = '';
                document.getElementById('companyCode').value = '';
                document.getElementById('companyDescription').value = '';
                document.getElementById('companyAddress').value = '';
                document.getElementById('companyPhone').value = '';
                document.getElementById('companyEmail').value = '';
                document.getElementById('companyWebsite').value = '';
                document.getElementById('companySortOrder').value = '0';
                document.getElementById('companyStatus').checked = true;
                document.getElementById('existingLogo').value = '';
                document.getElementById('currentLogoContainer').style.display = 'none';
            }
            
            modal.show();
        }

        // Department Modal Functions
        function openDepartmentModal(id = null, companyId = null) {
            let modal = new bootstrap.Modal(document.getElementById('departmentModal'));
            
            // Reset form
            document.getElementById('deptId').value = '';
            document.getElementById('deptName').value = '';
            document.getElementById('deptCode').value = '';
            document.getElementById('deptCompanySelect').value = companyId || '';
            document.getElementById('deptCompanyId').value = companyId || '';
            document.getElementById('deptOrgType').value = '';
            document.getElementById('deptSalesDivision').value = 'None';
            document.getElementById('deptSupportDept').value = '';
            document.getElementById('deptParent').value = '';
            document.getElementById('deptLevel').value = '1';
            document.getElementById('deptHead').value = '';
            document.getElementById('deptDescription').value = '';
            document.getElementById('deptIcon').value = '';
            document.getElementById('deptColor').value = '#009ada';
            document.getElementById('deptSortOrder').value = '0';
            document.getElementById('deptStatus').checked = true;
            
            // Hide dynamic fields
            document.getElementById('deptSalesField').style.display = 'none';
            document.getElementById('deptSupportField').style.display = 'none';
            
            // Filter parent departments
            filterParentDepartments();
            
            if (id) {
                let dept = departments.find(d => d.id == id);
                if (dept) {
                    document.getElementById('deptModalTitle').textContent = 'Edit Department';
                    document.getElementById('deptId').value = dept.id;
                    document.getElementById('deptName').value = dept.name;
                    document.getElementById('deptCode').value = dept.code || '';
                    document.getElementById('deptCompanySelect').value = dept.company_id;
                    document.getElementById('deptCompanyId').value = dept.company_id;
                    document.getElementById('deptOrgType').value = dept.organization_type || '';
                    document.getElementById('deptSalesDivision').value = dept.sales_division || 'None';
                    document.getElementById('deptSupportDept').value = dept.support_department || '';
                    document.getElementById('deptParent').value = dept.parent_id || '';
                    document.getElementById('deptLevel').value = dept.level || 1;
                    document.getElementById('deptHead').value = dept.head_employee_id || '';
                    document.getElementById('deptDescription').value = dept.description || '';
                    document.getElementById('deptIcon').value = dept.icon || '';
                    document.getElementById('deptColor').value = dept.color || '#009ada';
                    document.getElementById('deptSortOrder').value = dept.sort_order || 0;
                    document.getElementById('deptStatus').checked = dept.status == 1;
                    
                    // Show relevant fields
                    if (dept.organization_type === 'Sales') {
                        document.getElementById('deptSalesField').style.display = 'block';
                    } else if (dept.organization_type === 'Support') {
                        document.getElementById('deptSupportField').style.display = 'block';
                    }
                    
                    // Filter parent departments
                    filterParentDepartments();
                }
            } else {
                document.getElementById('deptModalTitle').textContent = 'Add Department';
            }
            
            modal.show();
        }

        // View Department Structure
        function viewDepartment(id) {
            $.ajax({
                url: 'ajax-department-view.php',
                type: 'POST',
                data: { department_id: id },
                success: function(response) {
                    document.getElementById('departmentViewContent').innerHTML = response;
                    new bootstrap.Modal(document.getElementById('viewDepartmentModal')).show();
                }
            });
        }

        // Delete functions
        function deleteCompany(id) {
            if (confirm('Are you sure you want to delete this company? This action cannot be undone.')) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_company">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteDepartment(id) {
            if (confirm('Are you sure you want to delete this department? This action cannot be undone.')) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_department">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            let alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
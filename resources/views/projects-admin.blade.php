<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management - K-Connect</title>
    
    <link rel="icon" type="image/png" href="../favi.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }
        
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
        }
        .page-header h1 {
            font-size: 2rem;
            font-weight: 800;
            margin: 0;
        }
        
        .text-gradient {
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
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
        
        .btn-outline {
            background: transparent;
            border: 2px solid #e2e8f0;
            color: #475569;
            padding: 8px 20px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
        }
        .btn-outline:hover {
            border-color: var(--kiboko-blue);
            color: var(--kiboko-blue);
        }
        
        .btn-outline-danger-custom {
            background: transparent;
            border: 2px solid #fee2e2;
            color: #dc2626;
            padding: 6px 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-outline-danger-custom:hover {
            background: #dc2626;
            color: white;
            border-color: #dc2626;
        }
        .btn-outline-danger-custom.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        .card-custom {
            background: white;
            border-radius: 16px;
            border: 1px solid #eef2f6;
            padding: 24px;
            margin-bottom: 24px;
        }
        .card-custom h4 {
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 10px 14px;
            font-size: 0.9rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 4px rgba(0,154,218,0.1);
        }
        
        .project-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 4px;
        }
        .project-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            border: none;
        }
        .project-table tbody tr {
            background: white;
            transition: all 0.2s;
        }
        .project-table tbody tr:hover {
            background: #f8fafc;
        }
        .project-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
            vertical-align: middle;
        }
        
        .priority-badge {
            font-size: 0.7rem;
            padding: 2px 10px;
            border-radius: 20px;
            font-weight: 600;
        }
        .priority-low { background: #f1f5f9; color: #475569; }
        .priority-medium { background: #dbeafe; color: #2563eb; }
        .priority-high { background: #fef3c7; color: #d97706; }
        .priority-urgent { background: #fee2e2; color: #dc2626; }
        
        .status-badge {
            font-size: 0.7rem;
            padding: 2px 10px;
            border-radius: 20px;
            font-weight: 600;
        }
        .status-planning { background: #fef3c7; color: #92400e; }
        .status-active { background: #dbeafe; color: #1e40af; }
        .status-onhold { background: #fef3c7; color: #d97706; }
        .status-completed { background: #dcfce7; color: #16a34a; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        
        .alert-custom {
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: none;
        }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-danger { background: #fee2e2; color: #991b1b; }
        
        .badge-dept {
            background: #f1f5f9;
            color: #475569;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            margin: 2px;
            display: inline-block;
        }
        
        .modal-content {
            border-radius: 16px;
            border: none;
        }
        .modal-header {
            border-bottom: 1px solid #eef2f6;
            padding: 20px 24px;
        }
        .modal-body {
            padding: 24px;
            max-height: 70vh;
            overflow-y: auto;
        }
        .modal-footer {
            border-top: 1px solid #eef2f6;
            padding: 16px 24px;
        }
        
        .creator-badge {
            font-size: 0.6rem;
            background: #dbeafe;
            color: #1e40af;
            padding: 1px 8px;
            border-radius: 12px;
            font-weight: 600;
        }
        
        .member-select-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.2s;
            cursor: pointer;
        }
        .member-select-item:hover {
            background: #f8fafc;
        }
        .member-select-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--kiboko-blue);
            flex-shrink: 0;
        }
        .member-select-item .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.7rem;
            color: #475569;
            flex-shrink: 0;
        }
        .member-select-item .avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .member-select-item .info { flex: 1; }
        .member-select-item .info .name {
            font-weight: 600;
            font-size: 0.9rem;
        }
        .member-select-item .info .detail {
            font-size: 0.75rem;
            color: #94a3b8;
        }
        .member-select-item .info .dept-badge {
            font-size: 0.6rem;
            background: #f1f5f9;
            padding: 1px 8px;
            border-radius: 12px;
            color: #475569;
        }
        
        .select-all-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            background: #f8fafc;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 1px solid #eef2f6;
        }
        .select-all-header input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--kiboko-blue);
        }
        .select-all-header label {
            font-weight: 600;
            font-size: 0.85rem;
            margin: 0;
            cursor: pointer;
        }
        .selected-count {
            font-size: 0.75rem;
            color: #64748b;
            margin-left: auto;
        }
        
        .member-search {
            position: relative;
            margin-bottom: 12px;
        }
        .member-search input {
            padding: 8px 12px 8px 35px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            width: 100%;
            font-size: 0.85rem;
        }
        .member-search input:focus {
            border-color: var(--kiboko-blue);
            outline: none;
        }
        .member-search i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        
        .member-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #eef2f6;
            border-radius: 8px;
            padding: 4px;
        }
        .member-list::-webkit-scrollbar {
            width: 6px;
        }
        .member-list::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        .member-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        @media (max-width: 768px) {
            .page-header h1 { font-size: 1.5rem; }
            .project-table { font-size: 0.8rem; }
            .project-table td, .project-table th { padding: 8px 10px; }
            .modal-body { max-height: 60vh; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    @include('partials.navbar')

    <!-- Admin Content -->
    <div class="admin-container">
        <div class="page-header">
            <div>
                <h1><span class="text-gradient">Project</span> Management</h1>
                <p class="text-muted">
                    @if ($isAdmin)
                        <i class="fas fa-shield-alt text-warning"></i> Admin Mode - Viewing all projects
                    @else
                        <i class="fas fa-user text-info"></i> Viewing projects you created or are a member of
                    @endif
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="projects-dashboard" class="btn-outline">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
                <a href="?create" class="btn-gradient" data-bs-toggle="modal" data-bs-target="#projectModal">
                    <i class="fas fa-plus"></i> New Project
                </a>
            </div>
        </div>

        @if ($message)
            <div class="alert-custom alert-{!! $messageType !!}">
                <i class="fas {!! $messageType == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' !!} me-2"></i>
                {!! $message !!}
            </div>
        @endif

        <!-- Projects List -->
        <div class="card-custom">
            <h4><i class="fas fa-list-alt" style="color: #009ada;"></i> All Projects</h4>
            
            <div class="table-responsive">
                <table class="project-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Title</th>
                            <th>Departments</th>
                            <th>Members</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Tasks</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($projects && count($projects) > 0)
                            @foreach ($projects as $project)
@php
$priorityClass = 'priority-' . strtolower($project['priority']);
                                $statusClass = 'status-' . strtolower(str_replace(' ', '', $project['status']));
                                $isCreator = ($project['created_by'] == $employee_id);
                                $canEdit = ($isCreator || $isAdmin);
                                $canDelete = ($isCreator || $isAdmin);
                                
                                $creatorName = $creatorNames[$project['id']];
@endphp
                                <tr>
                                    <td><strong>{{ $project['project_code'] }}</strong></td>
                                    <td>{{ $project['title'] }}</td>
                                    <td>
                                        @php
 
                                        if (!empty($project['departments'])) {
                                            $depts = explode(',', $project['departments']);
                                            foreach ($depts as $dept) {
                                                echo '<span class="badge-dept">' . htmlspecialchars($dept) . '</span> ';
                                            }
                                        } else {
                                            echo '<span class="text-muted">None</span>';
                                        }
                                        
@endphp
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{!! $project['member_count'] ?? 0 !!}</span>
                                    </td>
                                    <td><span class="priority-badge {!! $priorityClass !!}">{!! $project['priority'] !!}</span></td>
                                    <td><span class="status-badge {!! $statusClass !!}">{!! $project['status'] !!}</span></td>
                                    <td>
                                        <span class="badge bg-secondary">{!! $project['task_count'] ?? 0 !!}</span>
                                    </td>
                                    <td>
                                        <span style="font-size:0.8rem;">{{ $creatorName }}</span>
                                        @if ($isCreator)
                                            <span class="creator-badge">You</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="../project-detail?id={!! $project['id'] !!}" 
                                               class="btn btn-outline-primary" title="View Project">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($canEdit)
                                                <a href="?edit={!! $project['id'] !!}" 
                                                   class="btn btn-outline-warning" title="Edit Project">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            @if ($canDelete)
                                                <a href="?delete={!! $project['id'] !!}" 
                                                   class="btn btn-outline-danger-custom" title="Delete Project"
                                                   onclick="return confirm('⚠️ Delete project \'{!! addslashes($project['title']) !!}\' and all associated data? This cannot be undone.')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            @else
                                                <span class="btn btn-outline-secondary btn-sm disabled" 
                                                      title="Only the project creator can delete this project">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            @endif
                                        </div>
                                        @if (!$canDelete)
                                            <small class="text-muted d-block" style="font-size:0.6rem;">
                                                <i class="fas fa-lock"></i> Creator only
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">
                                        @if ($isAdmin)
                                            No projects found in the system.
                                        @else
                                            You haven't created any projects and aren't a member of any.
                                        @endif
                                    </p>
                                    <button class="btn-gradient btn-sm" data-bs-toggle="modal" data-bs-target="#projectModal">
                                        <i class="fas fa-plus"></i> Create Your First Project
                                    </button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Project Modal -->
    <div class="modal fade" id="projectModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form method="POST" action="" id="projectForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-project-diagram" style="color: #009ada;"></i>
                            {!! $editProject ? 'Edit Project' : 'Create New Project' !!}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="{!! $editProject ? 'update' : 'create' !!}">
                        @if ($editProject)
                            <input type="hidden" name="project_id" value="{!! $editProject['id'] !!}">
                        @endif
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <h6 class="fw-bold mb-3"><i class="fas fa-info-circle" style="color: #009ada;"></i> Project Details</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label">Project Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title" required
                                           value="{!! $editProject ? htmlspecialchars($editProject['title']) : '' !!}"
                                           placeholder="Enter project title">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="3" placeholder="Project description">{!! $editProject ? htmlspecialchars($editProject['description']) : '' !!}</textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="start_date" required
                                               value="{!! $editProject ? $editProject['start_date'] : date('Y-m-d') !!}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">End Date</label>
                                        <input type="date" class="form-control" name="end_date"
                                               value="{!! $editProject && $editProject['end_date'] ? $editProject['end_date'] : '' !!}">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Priority <span class="text-danger">*</span></label>
                                        <select class="form-select" name="priority" required>
                                            <option value="Low" {!! ($editProject && $editProject['priority'] == 'Low') ? 'selected' : '' !!}>Low</option>
                                            <option value="Medium" {!! ($editProject && $editProject['priority'] == 'Medium') ? 'selected' : '' !!}>Medium</option>
                                            <option value="High" {!! ($editProject && $editProject['priority'] == 'High') ? 'selected' : '' !!}>High</option>
                                            <option value="Urgent" {!! ($editProject && $editProject['priority'] == 'Urgent') ? 'selected' : '' !!}>Urgent</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select" name="status" required>
                                            <option value="Planning" {!! ($editProject && $editProject['status'] == 'Planning') ? 'selected' : '' !!}>Planning</option>
                                            <option value="Active" {!! ($editProject && $editProject['status'] == 'Active') ? 'selected' : '' !!}>Active</option>
                                            <option value="On Hold" {!! ($editProject && $editProject['status'] == 'On Hold') ? 'selected' : '' !!}>On Hold</option>
                                            <option value="Completed" {!! ($editProject && $editProject['status'] == 'Completed') ? 'selected' : '' !!}>Completed</option>
                                            <option value="Cancelled" {!! ($editProject && $editProject['status'] == 'Cancelled') ? 'selected' : '' !!}>Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Departments <span class="text-danger">*</span></label>
                                    <select class="form-select" name="departments[]" multiple required style="height: 80px;">
                                        @php
 
                                        $selectedDepts = [];
                                        if ($editProject && !empty($editProject['departments_list'])) {
                                            $selectedDepts = explode(',', $editProject['departments_list']);
                                        }
@endphp
                                        @foreach ($departmentList as $dept)
                                            <option value="{{ $dept }}"
                                                {!! in_array($dept, $selectedDepts) ? 'selected' : '' !!}>
                                                {{ $dept }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Hold Ctrl/Cmd to select multiple departments</small>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <h6 class="fw-bold mb-3"><i class="fas fa-users" style="color: #76bc21;"></i> Select Team Members</h6>
                                <p class="text-muted small">Select employees to add to this project. They will receive an email notification.</p>
                                
                                <div class="member-search">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="memberSearch" placeholder="Search employees by name, code or department..." onkeyup="filterMembers()">
                                </div>
                                
                                <div class="select-all-header">
                                    <input type="checkbox" id="selectAll" onchange="toggleAllMembers(this)">
                                    <label for="selectAll">Select All Employees</label>
                                    <span class="selected-count" id="selectedCount">0 selected</span>
                                </div>
                                
                                <div class="member-list" id="memberList">
                                    @php
 
                                    $selectedMembers = $editProjectMembers ?? [];
@endphp
                                    @foreach ($allEmployees as $emp)
@php
                                        $isSelected = in_array($emp['id'], $selectedMembers) || ($editProject && $emp['id'] == $employee_id);
                                        $avatarHtml = '';
                                        if (!empty($emp['photo'])) {
                                            $avatarHtml = '<img src="../uploads/employees/' . htmlspecialchars($emp['photo']) . '" alt="' . htmlspecialchars($emp['full_name']) . '">';
                                        } else {
                                            $avatarHtml = strtoupper(substr($emp['full_name'], 0, 2));
                                        }
                                    
@endphp
                                        <div class="member-select-item" data-name="{!! strtolower($emp['full_name']) !!}" data-code="{!! strtolower($emp['employee_code']) !!}" data-dept="{!! strtolower($emp['department'] ?? '') !!}">
                                            <input type="checkbox" name="members[]" value="{!! $emp['id'] !!}" 
                                                   {!! $isSelected ? 'checked' : '' !!}
                                                   onchange="updateSelectedCount()">
                                            <div class="avatar">{!! $avatarHtml !!}</div>
                                            <div class="info">
                                                <div class="name">{{ $emp['full_name'] }}</div>
                                                <div class="detail">
                                                    {{ $emp['employee_code'] }}
                                                    @if ($emp['department'])
                                                        <span class="dept-badge">{{ $emp['department'] }}</span>
                                                    @endif
                                                    @if ($emp['id'] == $employee_id)
                                                        <span class="dept-badge" style="background:#dbeafe; color:#1e40af;">You</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-2">
                                    <small class="text-muted">The project creator (you) is automatically added as an Admin.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-gradient" id="submitBtn">
                            <i class="fas fa-save"></i> {!! $editProject ? 'Update' : 'Create' !!} Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterMembers() {
            const search = document.getElementById('memberSearch').value.toLowerCase();
            const items = document.querySelectorAll('.member-select-item');
            
            items.forEach(item => {
                const name = item.dataset.name || '';
                const code = item.dataset.code || '';
                const dept = item.dataset.dept || '';
                const match = name.includes(search) || code.includes(search) || dept.includes(search);
                item.style.display = match ? 'flex' : 'none';
            });
        }
        
        function toggleAllMembers(checkbox) {
            const checkboxes = document.querySelectorAll('.member-select-item input[type="checkbox"]:not(:disabled)');
            checkboxes.forEach(cb => {
                cb.checked = checkbox.checked;
            });
            updateSelectedCount();
        }
        
        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('.member-select-item input[type="checkbox"]:checked');
            document.getElementById('selectedCount').textContent = checkboxes.length + ' selected';
            
            const allCheckboxes = document.querySelectorAll('.member-select-item input[type="checkbox"]:not(:disabled)');
            const checkedCheckboxes = document.querySelectorAll('.member-select-item input[type="checkbox"]:checked:not(:disabled)');
            const selectAll = document.getElementById('selectAll');
            
            if (allCheckboxes.length === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            } else if (checkedCheckboxes.length === allCheckboxes.length) {
                selectAll.checked = true;
                selectAll.indeterminate = false;
            } else if (checkedCheckboxes.length > 0) {
                selectAll.checked = false;
                selectAll.indeterminate = true;
            } else {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            }
        }
        
        @if ($editProject)
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('projectModal'));
                modal.show();
                updateSelectedCount();
            });
        @endif
        
        document.getElementById('projectForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            updateSelectedCount();
        });
    </script>
</body>
</html>
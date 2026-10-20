<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Chart Manager - Admin</title>
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
        
        .table-container {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
        }
        
        .org-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }
        
        .org-table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 15px;
            border: none;
        }
        
        .org-table tbody tr {
            background: white;
            border-radius: 12px;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        
        .org-table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,154,218,0.1);
        }
        
        .org-table td {
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
        
        .level-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .level-1 { background: #76bc2120; color: #166534; border: 1px solid #76bc2140; }
        .level-2 { background: #f59e0b20; color: #92400e; border: 1px solid #f59e0b40; }
        .level-3 { background: #ef444420; color: #991b1b; border: 1px solid #ef444440; }
        .level-4 { background: #8b5cf620; color: #5b21b6; border: 1px solid #8b5cf640; }
        .level-5 { background: #3b82f620; color: #1e40af; border: 1px solid #3b82f640; }
        .level-6 { background: #06b6d420; color: #155e75; border: 1px solid #06b6d440; }
        .level-7 { background: #14b8a620; color: #115e59; border: 1px solid #14b8a640; }
        .level-8 { background: #64748b20; color: #334155; border: 1px solid #64748b40; }
        
        .parent-cell {
            color: #64748b;
            font-size: 0.85rem;
        }
        
        .top-level {
            color: #94a3b8;
            font-style: italic;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: flex-end;
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
            cursor: pointer;
        }
        
        .action-btn:hover {
            background: var(--grad);
            color: white;
            border-color: transparent;
            transform: translateY(-2px);
        }
        
        .action-btn.delete:hover {
            background: #dc3545;
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
        
        .message-alert.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .message-alert.danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
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
        
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #eef2f6;
        }
        
        .edit-notice {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: #1e40af;
            display: none;
        }
        
        .edit-notice.show {
            display: block;
        }
        
        .form-select:disabled {
            background-color: #f1f5f9;
            color: #475569;
            cursor: not-allowed;
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
            <h2><i class="fas fa-sitemap me-2"></i>Organization Chart</h2>
            <div class="d-flex gap-2">
                <a href="../orgchart_view.php" target="_blank" class="btn btn-outline-secondary">
                    <i class="fas fa-eye me-2"></i>View Chart
                </a>
                <button class="btn-gradient" onclick="openAddModal()">
                    <i class="fas fa-plus me-2"></i>Add to Org Chart
                </button>
            </div>
        </div>
        
        <!-- Message Alert -->
        @if ($message !== null)
        <div class="message-alert {!! $message_type !!}">
            <span><i class="fas fa-info-circle me-2"></i>{!! $message !!}</span>
            <button class="btn-close float-end" onclick="this.parentElement.remove()"></button>
        </div>
        @endif
        
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{!! $totalInChart !!}</div>
                <div class="stat-label">Total in Chart</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{!! $topLevelCount !!}</div>
                <div class="stat-label">Top Level (No Manager)</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{!! $levelsUsed !!}</div>
                <div class="stat-label">Levels Used</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{!! $availableCount !!}</div>
                <div class="stat-label">Available Employees</div>
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
                        <input type="text" class="form-control border-start-0" id="tableSearch" placeholder="Search org chart...">
                    </div>
                </div>
                <div class="col-md-6">
                    <select class="form-select" id="levelFilter">
                        <option value="all">All Levels</option>
                        @foreach ($levelLabels as $lv => $label)
                        <option value="{!! $lv !!}">Level {!! $lv !!} - {!! $label !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Table -->
        <div class="table-container">
            <table class="org-table" id="orgTable">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Level</th>
                        <th>Reports To</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($orgData))
                        @foreach ($orgData as $entry)
                        @php
                            $parentName = '';
                            if ($entry['parent_id']) {
                                foreach ($orgData as $p) {
                                    if ($p['employee_id'] === $entry['parent_id']) {
                                        $parentName = $p['full_name'];
                                        break;
                                    }
                                }
                            }
@endphp
                        <tr data-level="{!! $entry['level'] !!}" data-employee-id="{!! (int)$entry['employee_id'] !!}">
                            <td>
                                <div class="employee-info">
                                    @if ($entry['photo'])
                                        <img src="../uploads/employees/{{ $entry['photo'] }}" 
                                             class="employee-avatar" alt="">
                                    @else
                                        <div class="employee-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="employee-name">{{ $entry['full_name'] }}</div>
                                        <div class="employee-code">{{ $entry['employee_code'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="level-badge level-{!! $entry['level'] !!}">
                                    {{ $entry['level_label'] ?? 'Level ' . $entry['level'] }}
                                </span>
                            </td>
                            <td class="parent-cell">
                                @if ($parentName)
                                    {{ $parentName }}
                                @else
                                    <span class="top-level"><i class="fas fa-star me-1"></i>Top Level</span>
                                @endif
                            </td>
                            <td>{{ $entry['position'] ?? '—' }}</td>
                            <td>{{ $entry['department'] ?? '—' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn" title="Edit" onclick="openEditModal({!! (int)$entry['employee_id'] !!})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn delete" title="Remove" 
                                            onclick="confirmDelete({!! (int)$entry['employee_id'] !!}, '{{ addslashes($entry['full_name'] ?? 'Employee') }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-sitemap fa-3x mb-3"></i>
                                    <p>No employees in organization chart yet.</p>
                                    <button class="btn btn-gradient btn-sm" onclick="openAddModal()">
                                        <i class="fas fa-plus me-1"></i> Add First Employee
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Add/Edit Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-sitemap me-2" style="color:var(--kiboko-blue);"></i>Add to Organization Chart
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="resetModal()"></button>
                </div>
                <form method="POST" id="orgForm">@csrf
                    <div class="modal-body">
                        <input type="hidden" name="action" value="save">
                        
                        <!-- Edit Notice -->
                        <div class="edit-notice" id="editNotice">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Editing entry for:</strong> <span id="editingEmployeeName"></span>
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" id="employeeSelect" class="form-select" required>
                                    <option value="">-- Choose Employee --</option>
                                    @foreach ($allEmployees as $emp)
                                    @php
                                        $alreadyInChart = in_array($emp['id'], $orgEmployeeIds);
@endphp
                                        <option value="{!! $emp['id'] !!}" {!! $alreadyInChart ? 'disabled' : '' !!}>
                                            {{ $emp['full_name'] . ' (' . $emp['employee_code'] . ') - ' . ($emp['position'] ?? 'No Position') }}
                                            {!! $alreadyInChart ? ' (Already in chart)' : '' !!}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reports To</label>
                                <select name="parent_id" id="parentSelect" class="form-select">
                                    <option value="">-- Nobody (Top Level / CEO) --</option>
                                    @foreach ($allChartEmployees as $chartEmp)
                                        <option value="{!! $chartEmp['id'] !!}">
                                            {{ $chartEmp['full_name'] . ' - ' . ($chartEmp['position'] ?? 'No Position') . ' (' . ($chartEmp['level_label'] ?? 'Lvl ' . $chartEmp['level']) . ')' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Level <span class="text-danger">*</span></label>
                                <select name="level" id="levelSelect" class="form-select" required onchange="autoSetLabel()">
                                    @foreach ($levelLabels as $lv => $label)
                                        <option value="{!! $lv !!}">Level {!! $lv !!} - {!! $label !!}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Level Label</label>
                                <input type="text" name="level_label" id="levelLabel" class="form-control" 
                                       placeholder="e.g., CEO, Director, Head" maxlength="50">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" id="sortOrder" class="form-control" 
                                       value="0" min="0" max="999">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetModal()">Cancel</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-save me-2" id="saveIcon"></i>
                            <span id="saveText">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Remove from Org Chart
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-user-minus fa-3x text-danger mb-3"></i>
                    <p id="deleteMessage">Are you sure you want to remove this employee?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" id="deleteForm">@csrf
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="employee_id" id="deleteEmployeeId">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Remove
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Pass PHP data to JavaScript -->
    <script>
        const orgData = {!! json_encode($orgDataJSON) !!};
        const allEmployees = {!! json_encode($allEmployeesJSON) !!};
        const allChartEmployees = {!! json_encode($allChartEmployeesJSON) !!};
        const levelLabels = {!! json_encode($levelLabels) !!};
    </script>
    
   <script>
    let currentEditId = null;
    let addModal;
    
    $(document).ready(function() {
        addModal = new bootstrap.Modal(document.getElementById('addModal'));
        
        const table = $('#orgTable').DataTable({
            pageLength: 25,
            language: {
                search: "",
                searchPlaceholder: "Search org chart...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No entries",
                infoFiltered: "(filtered from _MAX_ total)"
            },
            dom: 'rtip',
            initComplete: function() {
                $('#orgTable_filter').hide();
            }
        });
        
        $('#tableSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
        
        $('#levelFilter').on('change', function() {
            const val = $(this).val();
            if (val === 'all') {
                table.column(1).search('').draw();
            } else {
                table.column(1).search('^' + val + '$', true, false).draw();
            }
        });
        
        // Form submit handler - ensures employee_id is always sent
        $('#orgForm').on('submit', function(e) {
            // If we're editing, ensure the correct employee ID is sent
            if (currentEditId) {
                // Find or create the employee_id field
                let empIdField = $(this).find('input[name="employee_id"]').not('#employeeSelect');
                if (empIdField.length === 0) {
                    // Create hidden input
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'employee_id',
                        value: currentEditId
                    }).appendTo(this);
                } else {
                    empIdField.val(currentEditId);
                }
                // Remove required from disabled select
                $('#employeeSelect').prop('required', false);
            }
        });
    });
    
    function openAddModal() {
        currentEditId = null;
        
        $('#modalTitle').html('<i class="fas fa-plus-circle me-2" style="color:var(--kiboko-blue);"></i>Add to Organization Chart');
        $('#editNotice').removeClass('show');
        $('#saveIcon').attr('class', 'fas fa-save me-2');
        $('#saveText').text('Save');
        
        // Remove any hidden employee_id inputs
        $('#orgForm input[name="employee_id"][type="hidden"]').remove();
        
        // Reset and enable employee select
        const empSelect = document.getElementById('employeeSelect');
        empSelect.disabled = false;
        empSelect.required = true;
        empSelect.value = '';
        
        // Remove edit options
        empSelect.querySelectorAll('option[data-edit="true"]').forEach(opt => opt.remove());
        
        // Reset all options
        for (let i = 0; i < empSelect.options.length; i++) {
            const opt = empSelect.options[i];
            opt.hidden = false;
            if (opt.value === '') {
                opt.disabled = false;
            } else {
                const empId = parseInt(opt.value);
                opt.disabled = orgData.some(e => e.employee_id === empId);
            }
        }
        
        // Reset parent
        const parentSelect = document.getElementById('parentSelect');
        parentSelect.value = '';
        for (let i = 0; i < parentSelect.options.length; i++) {
            parentSelect.options[i].disabled = false;
        }
        
        // Reset levels
        document.getElementById('levelSelect').value = '8';
        document.getElementById('levelLabel').value = '';
        document.getElementById('sortOrder').value = '0';
        
        addModal.show();
    }
    
    function openEditModal(employeeId) {
        const empId = parseInt(employeeId);
        const entry = orgData.find(e => e.employee_id === empId);
        
        if (!entry) {
            alert('Employee not found in chart. ID: ' + empId);
            return;
        }
        
        currentEditId = empId;
        
        // Update UI
        $('#modalTitle').html('<i class="fas fa-edit me-2" style="color:#f59e0b;"></i>Edit Org Chart Entry');
        $('#editNotice').addClass('show');
        $('#editingEmployeeName').text(entry.full_name + ' (' + entry.employee_code + ')');
        $('#saveIcon').attr('class', 'fas fa-save me-2');
        $('#saveText').text('Update');
        
        // Remove any old hidden employee_id inputs
        $('#orgForm input[name="employee_id"][type="hidden"]').remove();
        
        // Create hidden input with correct employee ID
        $('<input>').attr({
            type: 'hidden',
            name: 'employee_id',
            value: empId,
            id: 'hiddenEmpId'
        }).appendTo('#orgForm');
        
        // Handle visible select - just show the employee name
        const empSelect = document.getElementById('employeeSelect');
        empSelect.querySelectorAll('option[data-edit="true"]').forEach(opt => opt.remove());
        
        // Hide all options, add edit option
        for (let i = 0; i < empSelect.options.length; i++) {
            empSelect.options[i].hidden = true;
            empSelect.options[i].disabled = true;
        }
        
        // Add and show only the edit option
        const editOption = document.createElement('option');
        editOption.value = empId;
        editOption.textContent = entry.full_name + ' (' + entry.employee_code + ')';
        editOption.selected = true;
        editOption.setAttribute('data-edit', 'true');
        editOption.hidden = false;
        editOption.disabled = false;
        empSelect.appendChild(editOption);
        
        // Disable select (visual only, hidden input handles submission)
        empSelect.disabled = true;
        empSelect.required = false;
        
        // Parent select
        const parentSelect = document.getElementById('parentSelect');
        parentSelect.value = entry.parent_id || '';
        for (let i = 0; i < parentSelect.options.length; i++) {
            parentSelect.options[i].disabled = (parseInt(parentSelect.options[i].value) === empId);
        }
        
        // Set values
        document.getElementById('levelSelect').value = entry.level || 8;
        document.getElementById('levelLabel').value = entry.level_label || '';
        document.getElementById('sortOrder').value = entry.sort_order || 0;
        
        addModal.show();
    }
    
    function autoSetLabel() {
        const val = document.getElementById('levelSelect').value;
        const label = document.getElementById('levelLabel');
        if (label.value === '') {
            label.value = levelLabels[val] || '';
        }
    }
    
    function confirmDelete(employeeId, name) {
        document.getElementById('deleteEmployeeId').value = employeeId;
        document.getElementById('deleteMessage').innerHTML = 
            '<strong>' + name + '</strong> will be removed from the organization chart.<br>' +
            '<small class="text-muted">Subordinates will be reassigned to the next manager.</small>';
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
    
    function resetModal() {
        currentEditId = null;
        $('#orgForm input[name="employee_id"][type="hidden"]').remove();
        
        const empSelect = document.getElementById('employeeSelect');
        empSelect.querySelectorAll('option[data-edit="true"]').forEach(opt => opt.remove());
        empSelect.disabled = false;
        empSelect.required = true;
        empSelect.value = '';
        
        for (let i = 0; i < empSelect.options.length; i++) {
            empSelect.options[i].hidden = false;
            if (empSelect.options[i].value === '') {
                empSelect.options[i].disabled = false;
            } else {
                const empId = parseInt(empSelect.options[i].value);
                empSelect.options[i].disabled = orgData.some(e => e.employee_id === empId);
            }
        }
        
        const parentSelect = document.getElementById('parentSelect');
        for (let i = 0; i < parentSelect.options.length; i++) {
            parentSelect.options[i].disabled = false;
        }
    }
    
    document.getElementById('addModal').addEventListener('hidden.bs.modal', resetModal);
    
    setTimeout(() => {
        document.querySelectorAll('.message-alert').forEach(el => el.remove());
    }, 5000);
</script>
</body>
</html>
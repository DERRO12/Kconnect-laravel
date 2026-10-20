<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Nomination Committee | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .sidebar { width: 280px; background: white; height: 100vh; position: fixed; left: 0; top: 0; box-shadow: 2px 0 20px rgba(0,0,0,0.05); overflow-y: auto; z-index: 100; }
        .sidebar-logo { padding: 25px 20px; text-align: center; border-bottom: 1px solid #eef2f6; }
        .sidebar-logo h2 { font-size: 1.5rem; font-weight: 800; background: var(--grad); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu a { display: flex; align-items: center; gap: 12px; padding: 12px 24px; color: #475569; text-decoration: none; transition: all 0.3s; margin: 4px 12px; border-radius: 12px; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: linear-gradient(135deg, rgba(0,154,218,0.1) 0%, rgba(118,188,33,0.1) 100%); color: var(--kiboko-blue); }
        .sidebar-menu a i { width: 24px; }
        .main-content { margin-left: 280px; padding: 24px 32px; }
        .page-header { background: white; border-radius: 24px; padding: 24px 32px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; }
        .level-section { background: white; border-radius: 24px; padding: 28px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; }
        .level-header { display: flex; align-items: center; gap: 15px; margin-bottom: 24px; }
        .level-badge { width: 60px; height: 60px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 800; }
        .level-badge.level-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; }
        .level-badge.level-2 { background: linear-gradient(135deg, #009ada, #06b6d4); color: white; }
        .level-badge.level-3 { background: linear-gradient(135deg, #8b5cf6, #6366f1); color: white; }
        .committee-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .member-card { background: #fafbfc; border-radius: 20px; padding: 20px; border: 1px solid #eef2f6; transition: all 0.3s; position: relative; }
        .member-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,0.08); background: white; }
        .member-avatar { width: 70px; height: 70px; border-radius: 20px; object-fit: cover; margin-bottom: 15px; border: 3px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .member-avatar-placeholder { width: 70px; height: 70px; border-radius: 20px; background: var(--grad); display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; font-weight: 700; margin-bottom: 15px; }
        .member-role-badge { display: inline-block; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; margin-top: 8px; }
        .member-role-badge.chair { background: #fef3c7; color: #92400e; }
        .member-role-badge.secretary { background: #dbeafe; color: #1e40af; }
        .member-role-badge.member { background: #dcfce7; color: #166534; }
        .member-actions { position: absolute; top: 16px; right: 16px; display: flex; gap: 6px; opacity: 0; transition: opacity 0.2s; }
        .member-card:hover .member-actions { opacity: 1; }
        .btn-icon { width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: all 0.2s; }
        .btn-icon.edit { background: rgba(0,154,218,0.1); color: var(--kiboko-blue); }
        .btn-icon.delete { background: rgba(220,53,69,0.1); color: #dc3545; }
        .btn-icon:hover { transform: scale(1.1); }
        .btn-gradient { background: var(--grad); color: white; border: none; padding: 12px 28px; border-radius: 40px; font-weight: 600; transition: all 0.3s; }
        .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,154,218,0.3); color: white; }
        .modal-content { border-radius: 24px; border: none; }
        .form-control, .form-select { border-radius: 14px; border: 2px solid #eef2f6; padding: 12px 16px; }
        .form-control:focus, .form-select:focus { border-color: var(--kiboko-blue); box-shadow: 0 0 0 3px rgba(0,154,218,0.1); }
        .empty-state { text-align: center; padding: 40px; color: #94a3b8; }
        .add-member-btn { margin-top: 20px; }
        @media (max-width: 768px) { 
            .sidebar { transform: translateX(-100%); } 
            .main-content { margin-left: 0; padding: 16px; }
            .committee-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-2"><i class="fas fa-users-cog me-2" style="color: var(--kiboko-blue);"></i>Nomination Committee</h2>
                    <p class="text-muted mb-0">Manage committee members for the Rewards & Recognition program review process</p>
                </div>
                <button class="btn-gradient" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="fas fa-plus-circle me-2"></i>Add Member
                </button>
            </div>
        </div>

        @if (request()->has('msg'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Operation completed successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Process Flow Info -->
        <div class="alert alert-info border-0" style="background: linear-gradient(135deg, rgba(0,154,218,0.08) 0%, rgba(118,188,33,0.08) 100%); border-radius: 16px;">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-info-circle fa-2x" style="color: var(--kiboko-blue);"></i>
                <div>
                    <strong class="mb-1">Committee Review Process</strong>
                    <p class="mb-0 text-muted">Level 1 → Level 2 → Level 3. Each level can shortlist nominations to move forward. Level 3 makes final approval decisions.</p>
                </div>
            </div>
        </div>

        <!-- Level 1 Committee -->
        <div class="level-section">
            <div class="level-header">
                <div class="level-badge level-1">L1</div>
                <div>
                    <h4 class="fw-bold mb-1">Level 1 - Initial Review</h4>
                    <p class="text-muted mb-0">First-level reviewers who screen all nominations</p>
                </div>
            </div>
            
            <div class="committee-grid">
                @if (count($level1) > 0)
                    @foreach ($level1 as $member)
                        @include('admin.partials.nominations.committee-member-card', ['member' => $member])
                    @endforeach
                @else
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <i class="fas fa-user-plus fa-3x mb-3" style="opacity: 0.3;"></i>
                        <p>No Level 1 committee members yet</p>
                        <button class="btn btn-outline-primary mt-2" onclick="openAddModal(1)">Add Level 1 Member</button>
                    </div>
                @endif
            </div>
            
            <div class="add-member-btn">
                <button class="btn btn-outline-secondary" onclick="openAddModal(1)">
                    <i class="fas fa-plus me-2"></i>Add Level 1 Member
                </button>
            </div>
        </div>

        <!-- Level 2 Committee -->
        <div class="level-section">
            <div class="level-header">
                <div class="level-badge level-2">L2</div>
                <div>
                    <h4 class="fw-bold mb-1">Level 2 - Secondary Review</h4>
                    <p class="text-muted mb-0">Review shortlisted nominations from Level 1</p>
                </div>
            </div>
            
            <div class="committee-grid">
                @if (count($level2) > 0)
                    @foreach ($level2 as $member)
                        @include('admin.partials.nominations.committee-member-card', ['member' => $member])
                    @endforeach
                @else
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <i class="fas fa-user-plus fa-3x mb-3" style="opacity: 0.3;"></i>
                        <p>No Level 2 committee members yet</p>
                        <button class="btn btn-outline-primary mt-2" onclick="openAddModal(2)">Add Level 2 Member</button>
                    </div>
                @endif
            </div>
            
            <div class="add-member-btn">
                <button class="btn btn-outline-secondary" onclick="openAddModal(2)">
                    <i class="fas fa-plus me-2"></i>Add Level 2 Member
                </button>
            </div>
        </div>

        <!-- Level 3 Committee -->
        <div class="level-section">
            <div class="level-header">
                <div class="level-badge level-3">L3</div>
                <div>
                    <h4 class="fw-bold mb-1">Level 3 - Final Approval</h4>
                    <p class="text-muted mb-0">Final decision makers who approve winners</p>
                </div>
            </div>
            
            <div class="committee-grid">
                @if (count($level3) > 0)
                    @foreach ($level3 as $member)
                        @include('admin.partials.nominations.committee-member-card', ['member' => $member])
                    @endforeach
                @else
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <i class="fas fa-user-plus fa-3x mb-3" style="opacity: 0.3;"></i>
                        <p>No Level 3 committee members yet</p>
                        <button class="btn btn-outline-primary mt-2" onclick="openAddModal(3)">Add Level 3 Member</button>
                    </div>
                @endif
            </div>
            
            <div class="add-member-btn">
                <button class="btn btn-outline-secondary" onclick="openAddModal(3)">
                    <i class="fas fa-plus me-2"></i>Add Level 3 Member
                </button>
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
@csrf
                    <div class="modal-header border-0">
                        <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add Committee Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Committee Level</label>
                            <select name="committee_level" id="add_level" class="form-select" required>
                                <option value="">Select Level</option>
                                <option value="1">Level 1 - Initial Review</option>
                                <option value="2">Level 2 - Secondary Review</option>
                                <option value="3">Level 3 - Final Approval</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">Search and select employee...</option>
                                                                @foreach ($employees as $emp)
                                    <option value="{!! $emp['id'] !!}">
                                        {{ $emp['full_name'] . ' (' . $emp['employee_code'] . ') - ' . $emp['department'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Committee Role</label>
                            <select name="role" class="form-select">
                                <option value="Member">Member</option>
                                <option value="Chair">Chair</option>
                                <option value="Secretary">Secretary</option>
                                <option value="Co-Chair">Co-Chair</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-gradient">Add Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Member Modal -->
    <div class="modal fade" id="editMemberModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
@csrf
                    <div class="modal-header border-0">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Committee Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Committee Level</label>
                            <select name="committee_level" id="edit_level" class="form-select" required>
                                <option value="1">Level 1 - Initial Review</option>
                                <option value="2">Level 2 - Secondary Review</option>
                                <option value="3">Level 3 - Final Approval</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Employee</label>
                            <select name="employee_id" id="edit_employee_id" class="form-select" required>
                                <option value="">Search and select employee...</option>
                                                                @foreach ($employees as $emp)
                                    <option value="{!! $emp['id'] !!}">
                                        {{ $emp['full_name'] . ' (' . $emp['employee_code'] . ') - ' . $emp['department'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Committee Role</label>
                            <select name="role" id="edit_role" class="form-select">
                                <option value="Member">Member</option>
                                <option value="Chair">Chair</option>
                                <option value="Secretary">Secretary</option>
                                <option value="Co-Chair">Co-Chair</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" id="edit_sort_order" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-gradient">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
@csrf
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="delete_id">
    </form>

    <!-- Toggle Status Form -->
    <form id="toggleForm" method="POST" style="display: none;">
@csrf
        <input type="hidden" name="action" value="toggle_status">
        <input type="hidden" name="id" id="toggle_id">
        <input type="hidden" name="status" id="toggle_status">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openAddModal(level) {
            document.getElementById('add_level').value = level;
            new bootstrap.Modal(document.getElementById('addMemberModal')).show();
        }
        
        function editMember(member) {
            document.getElementById('edit_id').value = member.id;
            document.getElementById('edit_level').value = member.committee_level;
            document.getElementById('edit_employee_id').value = member.employee_id;
            document.getElementById('edit_role').value = member.role || 'Member';
            document.getElementById('edit_sort_order').value = member.sort_order || 0;
            new bootstrap.Modal(document.getElementById('editMemberModal')).show();
        }
        
        function deleteMember(id) {
            if(confirm('Are you sure you want to remove this committee member?')) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        function toggleStatus(id, status) {
            document.getElementById('toggle_id').value = id;
            document.getElementById('toggle_status').value = status;
            document.getElementById('toggleForm').submit();
        }
    </script>
</body>
</html>
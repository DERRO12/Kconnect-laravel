<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories | Admin</title>
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
        .sidebar { width: 280px; background: white; height: 100vh; position: fixed; left: 0; top: 0; box-shadow: 2px 0 20px rgba(0,0,0,0.05); overflow-y: auto; }
        .sidebar-logo { padding: 25px 20px; text-align: center; border-bottom: 1px solid #eef2f6; }
        .sidebar-logo h2 { font-size: 1.5rem; font-weight: 800; background: var(--grad); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu a { display: flex; align-items: center; gap: 12px; padding: 12px 24px; color: #475569; text-decoration: none; transition: all 0.3s; margin: 4px 12px; border-radius: 12px; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: linear-gradient(135deg, rgba(0,154,218,0.1) 0%, rgba(118,188,33,0.1) 100%); color: var(--kiboko-blue); }
        .sidebar-menu a i { width: 24px; }
        .main-content { margin-left: 280px; padding: 24px 32px; }
        .category-card { background: white; border-radius: 20px; padding: 20px; margin-bottom: 20px; transition: all 0.3s; border: 1px solid #eef2f6; }
        .category-icon { width: 60px; height: 60px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 15px; }
        .btn-gradient { background: var(--grad); color: white; border: none; padding: 10px 24px; border-radius: 40px; font-weight: 600; transition: all 0.3s; }
        .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,154,218,0.3); color: white; }
        .modal-content { border-radius: 24px; }
        .form-control, .form-select { border-radius: 12px; border: 2px solid #eef2f6; padding: 12px 16px; }
        .form-control:focus { border-color: var(--kiboko-blue); box-shadow: 0 0 0 3px rgba(0,154,218,0.1); }
        .form-check-input:checked { background-color: var(--kiboko-blue); border-color: var(--kiboko-blue); }
        .self-badge { 
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%); 
            color: white; 
            font-size: 11px; 
            padding: 2px 8px; 
            border-radius: 20px; 
            margin-left: 8px;
        }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; padding: 16px; } }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div><h2 class="fw-bold mb-1">Nomination Categories</h2><p class="text-muted">Manage award categories for the Rewards & Recognition program</p></div>
            <button class="btn-gradient" data-bs-toggle="modal" data-bs-target="#addCategoryModal"><i class="fas fa-plus-circle me-2"></i>Add Category</button>
        </div>

        @if (request()->has('msg'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">Operation completed successfully!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row">
            @foreach ($categories as $cat)
                <div class="col-md-6 col-lg-4">
                    <div class="category-card">
                        <div class="category-icon" style="background: {!! $cat['color'] !!}20; color: {!! $cat['color'] !!};">
                            <i class="fas fa-{!! $cat['icon'] !!}"></i>
                        </div>
                        <h5 class="fw-bold mb-1">
                            {{ $cat['name'] }}
                            @if ($cat['is_self_nomination'])
                                <span class="self-badge"><i class="fas fa-user-check me-1"></i>Self-Nomination</span>
                            @endif
                        </h5>
                        <p class="text-muted small mb-3">{{ $cat['description'] }}</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-sm btn-outline-primary" onclick="editCategory({{ json_encode($cat) }})"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory({!! $cat['id'] !!})"><i class="fas fa-trash"></i> Delete</button>
                            <button class="btn btn-sm {!! $cat['status'] ? 'btn-outline-warning' : 'btn-outline-success' !!}" onclick="toggleStatus({!! $cat['id'] !!}, {!! $cat['status'] ? 0 : 1 !!})">
                                <i class="fas fa-{!! $cat['status'] ? 'ban' : 'check-circle' !!}"></i> {!! $cat['status'] ? 'Deactivate' : 'Activate' !!}
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
@csrf
                    <div class="modal-header"><h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Category</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3"><label class="form-label">Category Name</label><input type="text" name="name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"></textarea></div>
                        <div class="row">
                            <div class="col-md-6"><label class="form-label">Icon (Font Awesome)</label><input type="text" name="icon" class="form-control" value="award" placeholder="e.g., star, medal"></div>
                            <div class="col-md-6"><label class="form-label">Color</label><input type="color" name="color" class="form-control" value="#009ada"></div>
                        </div>
                        <div class="mt-3"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" value="0"></div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="is_self_nomination" id="add_self_nomination" value="1">
                            <label class="form-check-label" for="add_self_nomination">
                                <i class="fas fa-user-check me-1" style="color: #8b5cf6;"></i>
                                <strong>Self-Nomination Category</strong>
                                <br><small class="text-muted">Users nominate themselves (no need to select a nominee)</small>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn-gradient">Add Category</button></div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
@csrf
                    <div class="modal-header"><h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Category</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3"><label class="form-label">Category Name</label><input type="text" name="name" id="edit_name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Description</label><textarea name="description" id="edit_description" class="form-control" rows="3"></textarea></div>
                        <div class="row">
                            <div class="col-md-6"><label class="form-label">Icon</label><input type="text" name="icon" id="edit_icon" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label">Color</label><input type="color" name="color" id="edit_color" class="form-control"></div>
                        </div>
                        <div class="mt-3"><label class="form-label">Sort Order</label><input type="number" name="sort_order" id="edit_sort_order" class="form-control"></div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="is_self_nomination" id="edit_self_nomination" value="1">
                            <label class="form-check-label" for="edit_self_nomination">
                                <i class="fas fa-user-check me-1" style="color: #8b5cf6;"></i>
                                <strong>Self-Nomination Category</strong>
                                <br><small class="text-muted">Users nominate themselves (no need to select a nominee)</small>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn-gradient">Save Changes</button></div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
@csrf<input type="hidden" name="action" value="delete"><input type="hidden" name="id" id="delete_id"></form>
    <form id="toggleForm" method="POST" style="display: none;">
@csrf<input type="hidden" name="action" value="toggle_status"><input type="hidden" name="id" id="toggle_id"><input type="hidden" name="status" id="toggle_status"></form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editCategory(cat) {
            document.getElementById('edit_id').value = cat.id;
            document.getElementById('edit_name').value = cat.name;
            document.getElementById('edit_description').value = cat.description || '';
            document.getElementById('edit_icon').value = cat.icon || 'award';
            document.getElementById('edit_color').value = cat.color || '#009ada';
            document.getElementById('edit_sort_order').value = cat.sort_order || 0;
            document.getElementById('edit_self_nomination').checked = cat.is_self_nomination == 1;
            new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
        }
        function deleteCategory(id) {
            if(confirm('Are you sure you want to delete this category? All associated questions and nominations will also be deleted.')) {
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
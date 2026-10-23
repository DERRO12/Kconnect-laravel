@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim


<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manage Users</h1>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show"><?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Filter by Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All Users</option>
                        <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Search by name, email, or employee code...">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Employee Code</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Courses</th>
                            <th>Certificates</th>
                            <th>Joined</th>
                            <th>Last Activity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><code><?php echo $user['employee_code']; ?></code></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary text-white me-2" style="width: 32px; height: 32px;">
                                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                    </div>
                                    <?php echo $user['full_name']; ?>
                                </div>
                            </td>
                            <td><?php echo $user['email']; ?></td>
                            <td>
                                <?php
                                $status_class = [
                                    'active' => 'success',
                                    'pending' => 'warning',
                                    'inactive' => 'danger'
                                ][$user['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $status_class; ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </td>
                            <td class="text-center"><?php echo $user['course_count']; ?></td>
                            <td class="text-center"><?php echo $user['certificate_count']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php echo $user['last_activity'] ? ka_time_ago($user['last_activity']) : 'Never'; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-info" 
                                            onclick="viewUser(<?php echo $user['id']; ?>)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    
                                    <?php if ($user['status'] === 'pending'): ?>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Approve this user?')">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn btn-outline-success">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($user['status'] === 'active'): ?>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Deactivate this user?')">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                            <input type="hidden" name="action" value="deactivate">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn btn-outline-warning">
                                                <i class="bi bi-pause-circle"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($user['status'] === 'inactive'): ?>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Activate this user?')">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                            <input type="hidden" name="action" value="activate">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn btn-outline-success">
                                                <i class="bi bi-play-circle"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete this user? This action cannot be undone!')">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- User Details Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userModalBody">
                Loading...
            </div>
        </div>
    </div>
</div>

<script>
function viewUser(userId) {
    $('#userModalBody').html('Loading...');
    $('#userModal').modal('show');
    
    fetch('get-user-details?id=' + userId)
        .then(response => response.text())
        .then(html => {
            $('#userModalBody').html(html);
        });
}

$(document).ready(function() {
    $('#usersTable').DataTable({
        pageLength: 25,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: 9 }
        ]
    });
});
</script>

@endverbatim

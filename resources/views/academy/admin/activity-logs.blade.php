@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim


<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Activity Logs</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button class="btn btn-sm btn-outline-primary" onclick="exportLogs()">
                <i class="bi bi-download"></i> Export CSV
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="user_id" class="form-label">User</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">All Users</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?php echo $u['id']; ?>" <?php echo $user_id == $u['id'] ? 'selected' : ''; ?>>
                                <?php echo $u['full_name']; ?> (<?php echo $u['employee_code']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="action" class="form-label">Action</label>
                    <select class="form-select" id="action" name="action">
                        <option value="">All Actions</option>
                        <?php foreach ($actions as $a): ?>
                            <option value="<?php echo $a; ?>" <?php echo $action == $a ? 'selected' : ''; ?>>
                                <?php echo $a; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="<?php echo $date_from; ?>">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="<?php echo $date_to; ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable" id="logsTable">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                                <td>
                                    <?php if ($log['full_name']): ?>
                                        <div>
                                            <strong><?php echo $log['full_name']; ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo $log['employee_code']; ?></small>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">System</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo $log['action']; ?></span>
                                </td>
                                <td>
                                    <?php if ($log['details']): ?>
                                        <?php $details = json_decode($log['details'], true); ?>
                                        <pre class="mb-0 small"><?php print_r($details); ?></pre>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><code><?php echo $log['ip_address']; ?></code></td>
                                <td>
                                    <small class="text-muted" title="<?php echo htmlspecialchars($log['user_agent']); ?>">
                                        <?php echo substr($log['user_agent'], 0, 50); ?>...
                                    </small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
function exportLogs() {
    // Build export URL with current filters
    const params = new URLSearchParams(window.location.search);
    window.location.href = 'export-logs?' + params.toString();
}

$(document).ready(function() {
    $('#logsTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 50,
        columnDefs: [
            { orderable: false, targets: [3, 4, 5] }
        ]
    });
});
</script>


@endverbatim

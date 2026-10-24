@php $page_title = $page_title ?? 'Certificate Management'; @endphp
@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim




            
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Certificate Management</h1>
                </div>
                
                <!-- Flash Messages -->
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show">
                        <?php 
                        echo $_SESSION['flash_message'];
                        unset($_SESSION['flash_message']);
                        unset($_SESSION['flash_type']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card gradient-orange">
                            <div class="stat-icon">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $pending_count; ?></h3>
                                <p>Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card gradient-green">
                            <div class="stat-icon">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $approved_count; ?></h3>
                                <p>Approved</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card gradient-red">
                            <div class="stat-icon">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $rejected_count; ?></h3>
                                <p>Rejected</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card gradient-blue">
                            <div class="stat-icon">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $approved_this_month; ?></h3>
                                <p>This Month</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filters and Search -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Filter by Status</label>
                                <select name="status" class="form-select">
                                    <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending Only</option>
                                    <option value="approved" <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>Approved Only</option>
                                    <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>Rejected Only</option>
                                    <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Certificates</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($search); ?>" 
                                       placeholder="Search by name, employee code, course, or certificate number...">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Certificates Table -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Certificate List</h5>
                        <div>
                            <button class="btn btn-sm btn-success" onclick="bulkApprove()" id="bulkApproveBtn" style="display: none;">
                                <i class="bi bi-check-all"></i> Approve Selected
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="bulkDelete()" id="bulkDeleteBtn" style="display: none;">
                                <i class="bi bi-trash"></i> Delete Selected
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($certificates)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-award display-1 text-muted mb-3"></i>
                                <h4>No Certificates Found</h4>
                                <p class="text-muted">No certificates match your current filters.</p>
                            </div>
                        <?php else: ?>
                            <form method="POST" id="bulkForm">
                                <input type="hidden" name="token" value="<?php echo $csrf_token; ?>">
                                <input type="hidden" name="bulk_action" id="bulkAction" value="">
                                
                                <div class="table-responsive">
                                    <table class="table table-hover datatable" id="certificatesTable">
                                        <thead>
                                            <tr>
                                                <th width="40">
                                                    <input type="checkbox" id="selectAll">
                                                </th>
                                                <th>ID</th>
                                                <th>User</th>
                                                <th>Course</th>
                                                <th>Certificate #</th>
                                                <th>Requested</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($certificates as $cert): 
                                                $status_class = '';
                                                $status_text = '';
                                                
                                                switch($cert['status']) {
                                                    case 'pending':
                                                        $status_class = 'warning';
                                                        $status_text = 'Pending';
                                                        break;
                                                    case 'approved':
                                                        $status_class = 'success';
                                                        $status_text = 'Approved';
                                                        break;
                                                    case 'rejected':
                                                        $status_class = 'danger';
                                                        $status_text = 'Rejected';
                                                        break;
                                                    default:
                                                        $status_class = 'secondary';
                                                        $status_text = $cert['status'];
                                                }
                                            ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="selected_ids[]" value="<?php echo $cert['id']; ?>" class="select-item">
                                                </td>
                                                <td><?php echo $cert['id']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($cert['user_name']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($cert['employee_code']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($cert['course_title']); ?></td>
                                                <td>
                                                    <code><?php echo htmlspecialchars($cert['certificate_number']); ?></code>
                                                </td>
                                                <td>
                                                    <?php echo date('M d, Y', strtotime($cert['created_at'])); ?><br>
                                                    <small class="text-muted"><?php echo date('H:i', strtotime($cert['created_at'])); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                                    <?php if ($cert['status'] == 'approved' && $cert['approved_by_name']): ?>
                                                        <br><small class="text-muted">by <?php echo $cert['approved_by_name']; ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <?php if ($cert['status'] == 'pending'): ?>
                                                            <a href="approve-certificate?id=<?php echo $cert['id']; ?>" 
                                                               class="btn btn-outline-success" title="Review">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($cert['status'] == 'approved' && !empty($cert['pdf_path'])): ?>
                                                            <a href="../<?php echo $cert['pdf_path']; ?>" target="_blank" 
                                                               class="btn btn-outline-primary" title="View PDF">
                                                                <i class="bi bi-file-pdf"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        
                                                        <a href="?delete=<?php echo $cert['id']; ?>&token=<?php echo $csrf_token; ?>" 
                                                           class="btn btn-outline-danger" title="Delete"
                                                           onclick="return confirm('Are you sure you want to delete this certificate?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#certificatesTable').DataTable({
                pageLength: 25,
                order: [[1, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [0, 7] }
                ]
            });
            
            // Select all checkbox functionality
            $('#selectAll').change(function() {
                $('.select-item').prop('checked', $(this).prop('checked'));
                toggleBulkButtons();
            });
            
            // Individual checkbox change
            $('.select-item').change(function() {
                toggleBulkButtons();
                // Update select all checkbox
                if ($('.select-item:checked').length === $('.select-item').length) {
                    $('#selectAll').prop('checked', true);
                } else {
                    $('#selectAll').prop('checked', false);
                }
            });
        });
        
        function toggleBulkButtons() {
            const selectedCount = $('.select-item:checked').length;
            if (selectedCount > 0) {
                $('#bulkApproveBtn, #bulkDeleteBtn').show();
            } else {
                $('#bulkApproveBtn, #bulkDeleteBtn').hide();
            }
        }
        
        function bulkApprove() {
            const selectedCount = $('.select-item:checked').length;
            if (selectedCount === 0) {
                alert('Please select at least one certificate.');
                return;
            }
            
            Swal.fire({
                title: 'Approve Selected?',
                text: `You are about to approve ${selectedCount} certificate(s).`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Yes, approve them'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#bulkAction').val('approve_selected');
                    $('#bulkForm').submit();
                }
            });
        }
        
        function bulkDelete() {
            const selectedCount = $('.select-item:checked').length;
            if (selectedCount === 0) {
                alert('Please select at least one certificate.');
                return;
            }
            
            Swal.fire({
                title: 'Delete Selected?',
                text: `You are about to delete ${selectedCount} certificate(s). This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete them'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#bulkAction').val('delete_selected');
                    $('#bulkForm').submit();
                }
            });
        }
    </script>

    <style>
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }
    
    .stat-details h3 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
    }
    
    .stat-details p {
        margin: 0;
        color: #64748b;
        font-size: 0.9rem;
    }
    
    .gradient-orange {
        background: linear-gradient(135deg, #f97316 0%, #f59e0b 100%);
    }
    
    .gradient-orange .stat-icon {
        background: rgba(255,255,255,0.2);
        color: white;
    }
    
    .gradient-orange .stat-details h3,
    .gradient-orange .stat-details p {
        color: white;
    }
    
    .gradient-green {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    }
    
    .gradient-green .stat-icon {
        background: rgba(255,255,255,0.2);
        color: white;
    }
    
    .gradient-green .stat-details h3,
    .gradient-green .stat-details p {
        color: white;
    }
    
    .gradient-red {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }
    
    .gradient-red .stat-icon {
        background: rgba(255,255,255,0.2);
        color: white;
    }
    
    .gradient-red .stat-details h3,
    .gradient-red .stat-details p {
        color: white;
    }
    
    .gradient-blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }
    
    .gradient-blue .stat-icon {
        background: rgba(255,255,255,0.2);
        color: white;
    }
    
    .gradient-blue .stat-details h3,
    .gradient-blue .stat-details p {
        color: white;
    }
    </style>
</body>
</html>
@endverbatim

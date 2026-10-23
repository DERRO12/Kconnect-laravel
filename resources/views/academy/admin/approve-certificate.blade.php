@php $page_title = $page_title ?? 'Approve Certificate'; @endphp
@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim




            
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Approve Certificate</h1>
                    <a href="pending-certificates" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Pending
                    </a>
                </div>
                
                <!-- Display flash messages if any -->
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
                
                <div class="row">
                    <!-- Certificate Preview -->
                    <div class="col-lg-7 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Certificate Preview</h5>
                            </div>
                            <div class="card-body">
                                <div class="certificate-preview" style="background: #f8fafc; padding: 2rem; border-radius: 16px;">
                                    <div style="background: white; padding: 2rem; border: 3px solid transparent; border-image: linear-gradient(135deg, #009ada, #76bc21); border-image-slice: 1;">
                                        <h1 style="text-align: center; font-size: 32px; background: linear-gradient(135deg, #009ada, #76bc21); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">CERTIFICATE OF COMPLETION</h1>
                                        
                                        <p style="text-align: center; margin: 20px 0;">This is to certify that</p>
                                        
                                        <h2 style="text-align: center; color: #009ada; font-size: 28px;"><?php echo htmlspecialchars($certificate['user_name']); ?></h2>
                                        
                                        <p style="text-align: center;">Employee Code: <?php echo htmlspecialchars($certificate['employee_code']); ?></p>
                                        
                                        <p style="text-align: center;">has successfully completed the course</p>
                                        
                                        <h3 style="text-align: center; color: #76bc21; font-size: 24px;"><?php echo htmlspecialchars($certificate['course_title']); ?></h3>
                                        
                                        <p style="text-align: center;">on <?php echo date('F j, Y', strtotime($certificate['issued_date'])); ?></p>
                                        
                                        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 2px solid #e2e8f0;">
                                            <strong>Administrator</strong><br>
                                            Kiboko LMS
                                        </div>
                                        
                                        <p style="text-align: center; font-size: 12px; color: #94a3b8; margin-top: 20px;">
                                            Certificate ID: <?php echo htmlspecialchars($certificate['certificate_number']); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Approval Form -->
                    <div class="col-lg-5 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Review Certificate</h5>
                            </div>
                            <div class="card-body">
                                <div class="user-info mb-4">
                                    <h6>User Information</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <th>Name:</th>
                                            <td><?php echo htmlspecialchars($certificate['user_name']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td><?php echo htmlspecialchars($certificate['email']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Employee Code:</th>
                                            <td><?php echo htmlspecialchars($certificate['employee_code']); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div class="course-info mb-4">
                                    <h6>Course Information</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <th>Course:</th>
                                            <td><?php echo htmlspecialchars($certificate['course_title']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Quiz Score:</th>
                                            <td>
                                                <span class="badge bg-<?php echo $certificate['quiz_passed'] ? 'success' : 'danger'; ?>">
                                                    <?php echo round($certificate['quiz_score']); ?>%
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Requested:</th>
                                            <td><?php echo date('M d, Y H:i', strtotime($certificate['created_at'])); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Admin Notes (Optional)</label>
                                        <textarea name="admin_notes" class="form-control" rows="2" placeholder="Add any notes about this certificate..."></textarea>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" name="action" value="approve" class="btn btn-success flex-grow-1">
                                            <i class="bi bi-check-circle"></i> Approve Certificate
                                        </button>
                                        
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Certificate</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to reject this certificate?</p>
                        
                        <div class="mb-3">
                            <label class="form-label">Reason for Rejection</label>
                            <textarea name="rejection_reason" class="form-control" rows="3" required 
                                      placeholder="Please provide a reason for rejection..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Admin Notes</label>
                            <textarea name="admin_notes" class="form-control" rows="2" placeholder="Internal notes (optional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger">Reject Certificate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
@endverbatim

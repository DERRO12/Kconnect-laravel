@include('academy.partials.header')
@verbatim


<style>
.pending-container {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.pending-card {
    background: white;
    border-radius: 32px;
    padding: 3rem;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    max-width: 600px;
    width: 100%;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.pending-card.pending {
    border-top: 8px solid #f59e0b;
}

.pending-card.rejected {
    border-top: 8px solid #dc2626;
}

.pending-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 8px;
    background: linear-gradient(90deg, #f59e0b, #fbbf24);
}

.pending-card.rejected::before {
    background: linear-gradient(90deg, #dc2626, #ef4444);
}

.pending-icon {
    width: 100px;
    height: 100px;
    background: #fef3c7;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    color: #f59e0b;
    font-size: 3rem;
    animation: pulse 2s infinite;
}

.pending-card.rejected .pending-icon {
    background: #fee2e2;
    color: #dc2626;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.pending-title {
    font-size: 2rem;
    font-weight: 700;
    color: #f59e0b;
    margin-bottom: 1rem;
}

.pending-card.rejected .pending-title {
    color: #dc2626;
}

.pending-message {
    color: #64748b;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.pending-details {
    background: #f8fafc;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    text-align: left;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    color: #64748b;
    font-weight: 500;
}

.detail-value {
    font-weight: 600;
    color: #1e293b;
}

.rejection-reason {
    background: #fee2e2;
    border-radius: 12px;
    padding: 1rem;
    color: #991b1b;
    margin-bottom: 2rem;
    text-align: left;
}

.rejection-reason strong {
    display: block;
    margin-bottom: 0.5rem;
    color: #7f1d1d;
}

.estimated-time {
    background: #e0f2fe;
    border-radius: 50px;
    padding: 0.75rem;
    color: #0369a1;
    font-weight: 500;
    margin-bottom: 2rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn-outline {
    border: 2px solid #e2e8f0;
    background: white;
    color: #1e293b;
    padding: 0.75rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.btn-outline:hover {
    border-color: #009ada;
    color: #009ada;
    transform: translateY(-2px);
}

.refresh-btn {
    background: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.refresh-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0,154,218,0.4);
}

@media (max-width: 768px) {
    .pending-card {
        padding: 2rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<div class="pending-container">
    <div class="pending-card <?php echo isset($show_rejection) ? 'rejected' : 'pending'; ?>">
        <div class="pending-icon">
            <?php if (isset($show_rejection)): ?>
                <i class="bi bi-x-circle"></i>
            <?php else: ?>
                <i class="bi bi-hourglass-split"></i>
            <?php endif; ?>
        </div>
        
        <?php if (isset($show_rejection)): ?>
            <h1 class="pending-title">Certificate Rejected</h1>
            
            <p class="pending-message">
                Your certificate request for <strong>"<?php echo htmlspecialchars($certificate['course_title']); ?>"</strong> 
                has been reviewed and was not approved.
            </p>
            
            <?php if (!empty($certificate['rejection_reason'])): ?>
                <div class="rejection-reason">
                    <strong>Reason for rejection:</strong>
                    <?php echo htmlspecialchars($certificate['rejection_reason']); ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <h1 class="pending-title">Certificate Pending Approval</h1>
            
            <p class="pending-message">
                Your certificate for <strong>"<?php echo htmlspecialchars($certificate['course_title']); ?>"</strong> 
                has been submitted and is waiting for administrator approval.
            </p>
            
            <div class="pending-details">
                <div class="detail-row">
                    <span class="detail-label">Certificate Number:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($certificate['certificate_number'] ?? 'Pending'); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Submitted On:</span>
                    <span class="detail-value"><?php echo date('F j, Y, g:i a', strtotime($certificate['created_at'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value"><span class="badge bg-warning">Pending Review</span></span>
                </div>
            </div>
            
            <div class="estimated-time">
                <i class="bi bi-clock-history me-2"></i>
                Estimated review time: 24-48 hours
            </div>
        <?php endif; ?>
        
        <div class="action-buttons">
            <?php if (!isset($show_rejection)): ?>
                <button class="refresh-btn" onclick="checkStatus()">
                    <i class="bi bi-arrow-repeat"></i>
                    Check Status
                </button>
            <?php endif; ?>
            
            <a href="my-courses" class="btn-outline">
                <i class="bi bi-arrow-left"></i>
                Back to My Courses
            </a>
            
            <?php if (isset($show_rejection)): ?>
                <a href="contact-admin" class="btn-outline">
                    <i class="bi bi-envelope"></i>
                    Contact Admin
                </a>
            <?php endif; ?>
        </div>
        
        <?php if (!isset($show_rejection)): ?>
            <div class="mt-4 small text-muted">
                <i class="bi bi-info-circle me-1"></i>
                You will receive a notification once your certificate is approved.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add SweetAlert if not already included -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function checkStatus() {
    // Show loading
    Swal.fire({
        title: 'Checking Status',
        text: 'Please wait...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Make AJAX call to check status
    fetch(`api/check-certificate-status?course_id=<?php echo $course_id; ?>`)
        .then(response => response.json())
        .then(data => {
            Swal.close();
            
            if (data.status === 'approved') {
                Swal.fire({
                    icon: 'success',
                    title: 'Certificate Approved!',
                    text: 'Your certificate has been approved. Redirecting...',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = `view-certificate?course_id=<?php echo $course_id; ?>`;
                });
            } else if (data.status === 'rejected') {
                Swal.fire({
                    icon: 'error',
                    title: 'Certificate Rejected',
                    text: data.rejection_reason || 'Your certificate was rejected. Please contact administrator.'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Still Pending',
                    text: 'Your certificate is still pending approval. Please check again later.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        })
        .catch(error => {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to check status. Please try again.'
            });
        });
}

// Auto-refresh every 30 seconds to check status
const interval = setInterval(checkStatus, 30000);

// Clear interval when leaving the page
window.addEventListener('beforeunload', function() {
    clearInterval(interval);
});
</script>


@endverbatim

@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim


<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">My Certificates</h1>
        <?php if (isset($_GET['course_id'])): ?>
            <a href="certificates" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to All Certificates
            </a>
        <?php endif; ?>
    </div>

    <!-- Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10">
                    <i class="bi bi-award text-warning"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $total_certificates; ?></h3>
                    <p>Total Certificates</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10">
                    <i class="bi bi-check-circle text-success"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $approved_certificates; ?></h3>
                    <p>Approved</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-info bg-opacity-10">
                    <i class="bi bi-calendar-check text-info"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $latest_date ? date('M Y', strtotime($latest_date)) : 'N/A'; ?></h3>
                    <p>Latest</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificates Grid -->
    <?php if (empty($certificates)): ?>
        <div class="text-center py-5">
            <i class="bi bi-award text-secondary d-block mb-3" style="font-size: 5rem; opacity: .4"></i>
            <h4>No Certificates Yet</h4>
            <p class="text-secondary mb-4">Complete courses and pass quizzes to earn certificates.</p>
            <a href="browse-courses" class="btn btn-primary">
                <i class="bi bi-search"></i> Browse Courses
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($certificates as $cert):
                $cert_status = $cert['status'] ?? 'pending';
            ?>
                <div class="col-lg-6">
                    <div class="certificate-card card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="certificate-badge">
                                    <i class="bi bi-patch-check-fill text-warning" style="font-size: 3rem;"></i>
                                </div>
                                <?php if ($cert_status === 'approved'): ?>
                                    <span class="badge bg-success px-3 py-2">Approved</span>
                                <?php elseif ($cert_status === 'rejected'): ?>
                                    <span class="badge bg-danger px-3 py-2">Rejected</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark px-3 py-2">Pending approval</span>
                                <?php endif; ?>
                            </div>
                            
                            <h4 class="mb-2"><?php echo htmlspecialchars($cert['course_title']); ?></h4>
                            
                            <div class="certificate-details mb-3">
                                <p class="mb-1">
                                    <strong>Certificate ID:</strong> 
                                    <span class="text-muted"><?php echo htmlspecialchars($cert['certificate_number']); ?></span>
                                </p>
                                <p class="mb-1">
                                    <strong>Issued To:</strong> 
                                    <span class="text-muted"><?php echo htmlspecialchars($cert['full_name']); ?> (<?php echo htmlspecialchars($cert['employee_code']); ?>)</span>
                                </p>
                                <p class="mb-1">
                                    <strong>Issue Date:</strong> 
                                    <span class="text-muted"><?php echo date('F d, Y', strtotime($cert['issued_date'] ?? $cert['created_at'])); ?></span>
                                </p>
                                <?php if (!empty($cert['quiz_score'])): ?>
                                <p class="mb-1">
                                    <strong>Quiz Score:</strong> 
                                    <span class="badge bg-info"><?php echo round($cert['quiz_score']); ?>%</span>
                                </p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="certificate-actions d-flex flex-wrap gap-2">
                                <?php if ($cert_status === 'approved'): ?>
                                    <a href="download-certificate?id=<?php echo $cert['id']; ?>" class="btn btn-primary">
                                        <i class="bi bi-file-earmark-pdf"></i> Download PDF
                                    </a>
                                    <a href="verify-certificate?code=<?php echo urlencode($cert['certificate_number']); ?>" target="_blank" class="btn btn-outline-success">
                                        <i class="bi bi-shield-check"></i> Verify
                                    </a>
                                <?php elseif ($cert_status === 'rejected'): ?>
                                    <a href="certificate-pending?course_id=<?php echo $cert['course_id']; ?>" class="btn btn-outline-danger">
                                        <i class="bi bi-x-circle"></i> See reason
                                    </a>
                                <?php else: ?>
                                    <a href="certificate-pending?course_id=<?php echo $cert['course_id']; ?>" class="btn btn-warning">
                                        <i class="bi bi-hourglass-split"></i> Awaiting approval
                                    </a>
                                <?php endif; ?>
                                
                                <a href="course-player?id=<?php echo $cert['course_id']; ?>" class="btn btn-outline-primary">
                                    <i class="bi bi-play-circle"></i> View Course
                                </a>
                                
                                <button class="btn btn-outline-secondary" onclick="shareCertificate('<?php echo $cert['certificate_number']; ?>', '<?php echo addslashes($cert['course_title']); ?>')">
                                    <i class="bi bi-share"></i> Share
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<!-- Add SweetAlert if not already included -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function shareCertificate(certNumber, courseTitle) {
    // Create shareable link
    const shareUrl = window.location.origin + '/verify-certificate.php?code=' + certNumber;
    
    // Check if Web Share API is available
    if (navigator.share) {
        navigator.share({
            title: 'My Certificate - ' + courseTitle,
            text: 'I earned a certificate for completing the course: ' + courseTitle,
            url: shareUrl
        }).catch(console.error);
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(shareUrl).then(() => {
            Swal.fire({
                title: 'Link Copied!',
                text: 'Certificate verification link has been copied to clipboard.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }).catch(() => {
            Swal.fire({
                title: 'Error',
                text: 'Could not copy link. Please try again.',
                icon: 'error',
                timer: 2000,
                showConfirmButton: false
            });
        });
    }
}

// Add animation on load
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.certificate-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

<style>
.certificate-card {
    transition: all 0.3s ease;
}

.certificate-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

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

.badge {
    font-weight: 500;
    letter-spacing: 0.3px;
}
</style>


@endverbatim

@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim

  <link rel="stylesheet" href="assets/css/style.css">
<!-- Add SweetAlert2 CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>

</style>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2 mb-1">My Courses</h1>
            <p class="text-muted mb-0">Track your learning progress and continue where you left off</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="browse-courses" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Browse More Courses
            </a>
        </div>
    </div>

    <!-- Overall Progress Bar -->
    <div class="continue-learning mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-2">Your Learning Journey</h5>
                <div class="d-flex align-items-center gap-3">
                    <div class="flex-grow-1">
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar" style="width: <?php echo $overall_progress; ?>%"></div>
                        </div>
                    </div>
                    <span class="fw-bold text-primary"><?php echo $overall_progress; ?>% Complete</span>
                </div>
                <small class="text-muted">
                    <i class="bi bi-check-circle"></i> 
                    <?php echo $stats['total_lessons_completed']; ?>/<?php echo $stats['total_lessons']; ?> lessons completed
                </small>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="d-flex gap-2 justify-content-md-end">
                    <div class="text-center">
                        <h4 class="mb-0 text-primary"><?php echo $stats['completed']; ?></h4>
                        <small class="text-muted">Completed</small>
                    </div>
                    <div class="vr"></div>
                    <div class="text-center">
                        <h4 class="mb-0 text-warning"><?php echo $stats['in_progress']; ?></h4>
                        <small class="text-muted">In Progress</small>
                    </div>
                    <div class="vr"></div>
                    <div class="text-center">
                        <h4 class="mb-0 text-success"><?php echo $stats['certificates']; ?></h4>
                        <small class="text-muted">Certificates</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(0,154,218,0.1);">
                    <i class="bi bi-book text-primary"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $stats['total']; ?></h3>
                    <p>Total Enrolled</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(40,167,69,0.1);">
                    <i class="bi bi-check-circle text-success"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $stats['completed']; ?></h3>
                    <p>Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(23,162,184,0.1);">
                    <i class="bi bi-arrow-repeat text-info"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $stats['in_progress']; ?></h3>
                    <p>In Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(255,193,7,0.1);">
                    <i class="bi bi-award text-warning"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $stats['certificates']; ?></h3>
                    <p>Certificates</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" class="row g-3">
            <div class="col-md-5">
                <label for="search" class="form-label fw-bold">Search Courses</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" id="search" name="search" 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Search by course title...">
                </div>
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label fw-bold">Filter by Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All Courses</option>
                    <option value="in_progress" <?php echo $status === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 me-2">
                    <i class="bi bi-funnel"></i> Apply Filters
                </button>
                <a href="my-courses" class="btn btn-outline-secondary" title="Reset filters">
                    <i class="bi bi-arrow-repeat"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Active Filters Display -->
    <?php if (!empty($search) || $status !== 'all'): ?>
    <div class="alert alert-info d-flex align-items-center mb-4 py-2">
        <i class="bi bi-funnel-fill me-2"></i>
        <strong class="me-2">Active Filters:</strong>
        <?php if (!empty($search)): ?>
            <span class="badge bg-primary me-2">Search: "<?php echo htmlspecialchars($search); ?>"</span>
        <?php endif; ?>
        <?php if ($status !== 'all'): ?>
            <span class="badge bg-success me-2">Status: <?php echo ucfirst(str_replace('_', ' ', $status)); ?></span>
        <?php endif; ?>
        <a href="my-courses" class="ms-auto text-decoration-none">
            <i class="bi bi-x-circle"></i> Clear all
        </a>
    </div>
    <?php endif; ?>

    <!-- Continue Learning Section (First In Progress Course) -->
    <?php 
    $continue_course = array_filter($enrollments, fn($e) => 
        $e['enrollment_status'] === 'in_progress' && $e['progress_percentage'] < 100
    );
    $continue_course = reset($continue_course);
    ?>
    <?php if ($continue_course): ?>
    <div class="continue-learning mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                    <i class="bi bi-play-circle-fill text-primary fs-3"></i>
                </div>
            </div>
            <div class="col">
                <h5 class="mb-1">Continue Learning: <?php echo htmlspecialchars($continue_course['title']); ?></h5>
                <div class="d-flex align-items-center gap-3">
                    <small class="text-muted">
                        <i class="bi bi-journal-check"></i> 
                        <?php echo $continue_course['completed_lessons']; ?>/<?php echo $continue_course['total_lessons']; ?> lessons
                    </small>
                    <small class="text-muted">
                        <i class="bi bi-clock"></i> 
                        Last accessed: <?php echo $continue_course['last_accessed'] ? ka_time_ago($continue_course['last_accessed']) : 'Not started'; ?>
                    </small>
                </div>
                <div class="progress mt-2" style="height: 6px; width: 100%;">
                    <div class="progress-bar" style="width: <?php echo $continue_course['progress_percentage']; ?>%"></div>
                </div>
            </div>
            <div class="col-auto">
                <a href="course-player?id=<?php echo $continue_course['id']; ?>" class="btn btn-primary">
                    <i class="bi bi-play-circle"></i> Continue
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Courses List -->
    <?php if (empty($enrollments)): ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-journal-x display-1 text-muted"></i>
            </div>
            <h4 class="text-muted mb-3">No Courses Found</h4>
            <p class="text-secondary mb-4">You haven't enrolled in any courses yet.</p>
            <a href="browse-courses" class="btn btn-primary btn-lg">
                <i class="bi bi-search"></i> Browse Courses
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($enrollments as $enrollment): ?>
                <div class="col-lg-6">
                    <div class="course-enrollment-card">
                        <div class="row g-0 h-100">
                            <div class="col-md-4">
                                <?php 
                                $thumbnail_path = 'assets/uploads/courses/' . ($enrollment['thumbnail'] ?? '');
                                $thumbnail_exists = !empty($enrollment['thumbnail']) && file_exists($thumbnail_path);
                                ?>
                                <?php if ($thumbnail_exists): ?>
                                    <img src="<?php echo $thumbnail_path; ?>" 
                                         class="img-fluid rounded-start h-100" 
                                         style="object-fit: cover;"
                                         alt="<?php echo htmlspecialchars($enrollment['title']); ?>"
                                         loading="lazy"
                                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'default-thumbnail\'><i class=\'bi bi-journal-bookmark-fill\'></i></div>';">
                                <?php else: ?>
                                    <div class="default-thumbnail">
                                        <i class="bi bi-journal-bookmark-fill"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Progress circle overlay -->
                                <div class="position-absolute top-0 end-0 m-2">
                                    <div class="progress-circle bg-white shadow-sm" 
                                         style="width: 45px; height: 45px;"
                                         data-progress="<?php echo round($enrollment['progress_percentage']); ?>">
                                        <span style="font-size: 0.8rem;"><?php echo round($enrollment['progress_percentage']); ?>%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card-body d-flex flex-column h-100">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($enrollment['title']); ?></h5>
                                        <span class="badge bg-<?php 
                                            echo $enrollment['enrollment_status'] === 'completed' ? 'success' : 'primary'; 
                                        ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $enrollment['enrollment_status'])); ?>
                                        </span>
                                    </div>
                                    
                                    <p class="card-text text-secondary small mb-2">
                                        <?php echo htmlspecialchars(substr($enrollment['description'] ?? 'No description available', 0, 100)); ?>...
                                    </p>
                                    
                                    <!-- Lesson Progress -->
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-secondary">Lesson Progress</small>
                                            <small class="text-secondary fw-bold">
                                                <?php echo $enrollment['completed_lessons']; ?>/<?php echo $enrollment['total_lessons']; ?>
                                            </small>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <?php 
                                            $lesson_progress = $enrollment['total_lessons'] > 0 
                                                ? ($enrollment['completed_lessons'] / $enrollment['total_lessons']) * 100 
                                                : 0;
                                            ?>
                                            <div class="progress-bar" style="width: <?php echo $lesson_progress; ?>%"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Course Meta -->
                                    <div class="d-flex gap-3 mb-3">
                                        <small class="text-secondary" title="Enrolled on">
                                            <i class="bi bi-calendar-check"></i> 
                                            <?php echo date('M d, Y', strtotime($enrollment['enrolled_at'])); ?>
                                        </small>
                                        <?php if ($enrollment['quiz_passed']): ?>
                                            <small class="text-success" title="Quiz passed">
                                                <i class="bi bi-check-circle-fill"></i> 
                                                Quiz: <?php echo round($enrollment['quiz_score']); ?>%
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2 mt-auto">
                                        <?php if ($enrollment['enrollment_status'] === 'completed'): ?>
                                            <?php if (!empty($enrollment['certificate_id'])): ?>
                                                <a href="certificate?id=<?php echo $enrollment['certificate_id']; ?>" 
                                                   class="btn btn-sm btn-success flex-grow-1">
                                                    <i class="bi bi-award"></i> View Certificate
                                                </a>
                                            <?php elseif ($enrollment['quiz_passed']): ?>
                                                <a href="generate-certificate?course_id=<?php echo $enrollment['id']; ?>" 
                                                   class="btn btn-sm btn-success flex-grow-1">
                                                    <i class="bi bi-award"></i> Get Certificate
                                                </a>
                                            <?php else: ?>
                                                <a href="quiz?course_id=<?php echo $enrollment['id']; ?>" 
                                                   class="btn btn-sm btn-warning flex-grow-1">
                                                    <i class="bi bi-patch-question"></i> Take Final Quiz
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <a href="course-player?id=<?php echo $enrollment['id']; ?>" 
                                               class="btn btn-sm btn-primary flex-grow-1">
                                                <i class="bi bi-play-circle"></i> Continue Learning
                                            </a>
                                        <?php endif; ?>
                                        
                                        <div class="btn-group">
                                            <a href="course-overview?id=<?php echo $enrollment['id']; ?>" 
                                               class="btn btn-sm btn-outline-secondary" 
                                               title="Course Overview">
                                                <i class="bi bi-info-circle"></i>
                                            </a>
                                            <?php if ($enrollment['quiz_id']): ?>
                                                <a href="quiz-results?course_id=<?php echo $enrollment['id']; ?>" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="Quiz Results">
                                                    <i class="bi bi-trophy"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Last accessed info -->
                                    <small class="text-secondary d-block mt-2">
                                        <i class="bi bi-clock-history"></i> 
                                        Last activity: <?php echo $enrollment['last_accessed'] ? ka_time_ago($enrollment['last_accessed']) : 'Not started'; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Results count -->
        <div class="text-center mt-4">
            <small class="text-muted">
                Showing <?php echo count($enrollments); ?> course<?php echo count($enrollments) > 1 ? 's' : ''; ?>
            </small>
        </div>
    <?php endif; ?>
    
    <!-- Recent Activity Section -->
    <?php if (!empty($recent_activity)): ?>
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Activity</h6>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        <?php foreach ($recent_activity as $activity): ?>
                            <?php
                            // Get lesson details
                            $stmt = $pdo->prepare("SELECT title, course_id FROM lessons WHERE id = ?");
                            $stmt->execute([$activity['lesson_id']]);
                            $lesson = $stmt->fetch();
                            ?>
                            <div class="activity-item">
                                <div class="activity-icon <?php echo $activity['completed'] ? 'bg-success' : 'bg-info'; ?> bg-opacity-10">
                                    <i class="bi bi-<?php echo $activity['completed'] ? 'check-circle-fill' : 'play-circle-fill'; ?> 
                                       text-<?php echo $activity['completed'] ? 'success' : 'info'; ?>"></i>
                                </div>
                                <div class="activity-details flex-grow-1">
                                    <p class="mb-1">
                                        <strong><?php echo $lesson['title'] ?? 'Unknown lesson'; ?></strong>
                                        <?php echo $activity['completed'] ? 'completed' : 'accessed'; ?>
                                    </p>
                                    <small class="text-secondary">
                                        <?php echo ka_time_ago($activity['last_accessed']); ?>
                                    </small>
                                </div>
                                <?php if (!$activity['completed'] && $lesson): ?>
                                    <a href="course-player?id=<?php echo $lesson['course_id']; ?>&lesson=<?php echo $activity['lesson_id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        Continue <i class="bi bi-arrow-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</main>

<script>
// Update progress circles
document.querySelectorAll('.progress-circle').forEach(circle => {
    const progress = parseInt(circle.dataset.progress || 0);
    const angle = (progress / 100) * 360;
    circle.style.background = `conic-gradient(var(--kiboko-blue) ${angle}deg, #e9ecef ${angle}deg)`;
    
    // Add white inner circle
    const innerCircle = document.createElement('div');
    innerCircle.style.position = 'absolute';
    innerCircle.style.width = '35px';
    innerCircle.style.height = '35px';
    innerCircle.style.background = 'white';
    innerCircle.style.borderRadius = '50%';
    innerCircle.style.top = '5px';
    innerCircle.style.left = '5px';
    circle.appendChild(innerCircle);
    
    // Move the percentage text to be on top
    const span = circle.querySelector('span');
    if (span) {
        span.style.position = 'relative';
        span.style.zIndex = '2';
    }
});

// Auto-submit on filter change
document.getElementById('status').addEventListener('change', function() {
    document.getElementById('filterForm').submit();
});

// Live search with debounce
let searchTimeout;
document.getElementById('search').addEventListener('keyup', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.getElementById('filterForm').submit();
    }, 500);
});

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Confirm before leaving course
function confirmLeaveCourse(courseId, courseTitle) {
    Swal.fire({
        title: 'Leave Course?',
        text: `Are you sure you want to unenroll from "${courseTitle}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, leave course'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'api/unenroll-course?id=' + courseId + '&token=<?php echo csrf_token(); ?>';
        }
    });
}
</script>

@endverbatim

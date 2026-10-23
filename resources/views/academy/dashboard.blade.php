@php $page_title = $page_title ?? 'Dashboard'; @endphp
@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim



      <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Welcome Banner with Continue Learning -->
                <div class="welcome-banner mb-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-2">Welcome back, <?php echo $user['full_name']; ?>! 👋</h2>
                            <p class="text-secondary mb-0">
                                <i class="bi bi-calendar-check me-2"></i>
                                Last login: <?php echo $user['last_login'] ? date('F j, Y, g:i a', strtotime($user['last_login'])) : 'First time login'; ?>
                            </p>
                        </div>
                        <div class="col-auto">
                            <?php if ($continue_course): ?>
                                <a href="course-player?id=<?php echo $continue_course['id']; ?>" class="btn btn-success">
                                    <i class="bi bi-play-circle"></i> Continue Learning: <?php echo substr($continue_course['title'], 0, 30); ?>...
                                </a>
                            <?php else: ?>
                                <a href="browse-courses" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Enroll in New Course
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: linear-gradient(135deg, #009ada20, #76bc2120);">
                                <i class="bi bi-book-half" style="color: var(--kiboko-blue);"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $total_courses; ?></h3>
                                <p>Enrolled Courses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: linear-gradient(135deg, #76bc2120, #009ada20);">
                                <i class="bi bi-check-circle-fill" style="color: var(--kiboko-green);"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $completed_courses; ?></h3>
                                <p>Completed Courses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: linear-gradient(135deg, #ffc10720, #fd7e1420);">
                                <i class="bi bi-trophy-fill" style="color: #ffc107;"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $total_certificates; ?></h3>
                                <p>Certificates Earned</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: linear-gradient(135deg, #dc354520, #6f42c120);">
                                <i class="bi bi-clock-history" style="color: #6f42c1;"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $in_progress_courses; ?></h3>
                                <p>In Progress</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Continue Learning Section -->
                <?php 
                $inProgressCourses = array_filter($enrollments, function($e) {
                    return $e['enrollment_status'] === 'in_progress' && $e['progress_percentage'] < 100;
                });
                
                if (!empty($inProgressCourses)): 
                ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-play-circle me-2 text-primary"></i>Continue Learning</h5>
                                <a href="my-courses?status=in_progress" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <?php foreach (array_slice($inProgressCourses, 0, 3) as $course): 
                                        $thumbnail_path = 'assets/uploads/courses/' . ($course['thumbnail'] ?? '');
                                        $thumbnail_exists = !empty($course['thumbnail']) && file_exists($thumbnail_path);
                                    ?>
                                    <div class="col-md-4">
                                        <div class="course-progress-card h-100">
                                            <div class="d-flex align-items-center mb-3">
                                                <?php if ($thumbnail_exists): ?>
                                                    <img src="<?php echo $thumbnail_path; ?>" 
                                                         alt="<?php echo htmlspecialchars($course['title']); ?>"
                                                         class="rounded-circle me-3" width="50" height="50">
                                                <?php else: ?>
                                                    <div class="course-icon me-3">
                                                        <i class="bi bi-journal-bookmark-fill fs-3" style="color: var(--kiboko-blue);"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($course['title']); ?></h6>
                                                    <small class="text-secondary">
                                                        <?php echo $course['completed_lessons']; ?>/<?php echo $course['total_lessons']; ?> lessons
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="progress mb-2" style="height: 6px;">
                                                <div class="progress-bar bg-gradient" 
                                                     style="width: <?php echo $course['progress_percentage']; ?>%"></div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-secondary"><?php echo round($course['progress_percentage']); ?>% complete</small>
                                                <small class="text-secondary">
                                                    <i class="bi bi-clock"></i> 
                                                    <?php echo $course['last_accessed'] ? ka_time_ago($course['last_accessed']) : 'Not started'; ?>
                                                </small>
                                            </div>
                                            <a href="course-player?id=<?php echo $course['id']; ?>" 
                                               class="btn btn-outline-primary w-100">
                                                <i class="bi bi-arrow-right-circle"></i> Continue
                                            </a>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- My Courses Table -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-grid me-2 text-primary"></i>My Courses</h5>
                                <div>
                                    <a href="my-courses?status=completed" class="btn btn-sm btn-outline-success me-2">Completed</a>
                                    <a href="my-courses" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Course</th>
                                                <th>Progress</th>
                                                <th>Status</th>
                                                <th>Last Accessed</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $display_enrollments = array_slice($enrollments, 0, 5);
                                            foreach ($display_enrollments as $enrollment): 
                                                $thumbnail_path = 'assets/uploads/courses/' . ($enrollment['thumbnail'] ?? '');
                                                $thumbnail_exists = !empty($enrollment['thumbnail']) && file_exists($thumbnail_path);
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if ($thumbnail_exists): ?>
                                                            <img src="<?php echo $thumbnail_path; ?>" 
                                                                 alt="<?php echo htmlspecialchars($enrollment['title']); ?>"
                                                                 class="rounded me-3" width="50" height="50">
                                                        <?php else: ?>
                                                            <div class="bg-light rounded me-3 p-2">
                                                                <i class="bi bi-journal-bookmark-fill fs-4 text-primary"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <h6 class="mb-0"><?php echo htmlspecialchars($enrollment['title']); ?></h6>
                                                            <small class="text-secondary">
                                                                <?php echo $enrollment['completed_lessons']; ?>/<?php echo $enrollment['total_lessons']; ?> lessons
                                                                <?php if ($enrollment['quiz_passed']): ?>
                                                                    <span class="badge bg-success ms-2">Quiz Passed</span>
                                                                <?php endif; ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="min-width: 150px;">
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                            <div class="progress-bar bg-gradient" 
                                                                 style="width: <?php echo $enrollment['progress_percentage']; ?>%"></div>
                                                        </div>
                                                        <small class="text-secondary">
                                                            <?php echo round($enrollment['progress_percentage']); ?>%
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($enrollment['enrollment_status'] === 'completed'): ?>
                                                        <span class="badge bg-success">Completed</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-primary">In Progress</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo $enrollment['last_accessed'] ? ka_time_ago($enrollment['last_accessed']) : 'Not started'; ?>
                                                </td>
                                                <td>
                                                    <?php if ($enrollment['enrollment_status'] === 'completed'): ?>
                                                        <?php if ($enrollment['quiz_passed']): ?>
                                                            <a href="generate-certificate?course_id=<?php echo $enrollment['id']; ?>" 
                                                               class="btn btn-sm btn-success">
                                                                <i class="bi bi-award"></i> Certificate
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="quiz?course_id=<?php echo $enrollment['id']; ?>" 
                                                               class="btn btn-sm btn-warning">
                                                                <i class="bi bi-patch-question"></i> Take Quiz
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <a href="course-player?id=<?php echo $enrollment['id']; ?>" 
                                                           class="btn btn-sm btn-primary">
                                                            <i class="bi bi-play-circle"></i> Continue
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            
                                            <?php if (empty($enrollments)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <i class="bi bi-journal-x display-4 text-muted"></i>
                                                    <p class="mt-2">You haven't enrolled in any courses yet.</p>
                                                    <a href="browse-courses" class="btn btn-primary">
                                                        Browse Courses
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity & Certificates -->
                <div class="row g-4">
                    <!-- Recent Activity -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Activity</h5>
                                <a href="progress" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <?php foreach ($activities as $activity): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <p class="mb-1"><?php echo ucfirst(str_replace('_', ' ', $activity['action'])); ?></p>
                                            <small class="text-secondary">
                                                <i class="bi bi-clock"></i> <?php echo ka_time_ago($activity['created_at']); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    
                                    <?php if (empty($activities)): ?>
                                    <div class="text-center py-4">
                                        <i class="bi bi-activity display-4 text-muted"></i>
                                        <p class="text-muted mt-2">No recent activities</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Certificates -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-award me-2 text-primary"></i>Recent Certificates</h5>
                                <a href="certificates" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php 
                                $recent_certificates = array_slice($certificates, 0, 5);
                                foreach ($recent_certificates as $certificate): 
                                ?>
                                <div class="d-flex align-items-center justify-content-between mb-3 p-2 border-bottom">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($certificate['course_title']); ?></h6>
                                        <small class="text-secondary">
                                            <i class="bi bi-calendar-check"></i> 
                                            Issued: <?php echo date('M d, Y', strtotime($certificate['issued_date'])); ?>
                                        </small>
                                    </div>
                                    <div>
                                        <a href="download-certificate?id=<?php echo $certificate['id']; ?>" 
                                           class="btn btn-sm btn-outline-success me-1" title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <a href="verify-certificate?code=<?php echo $certificate['certificate_number']; ?>" 
                                           class="btn btn-sm btn-outline-info" title="Verify" target="_blank">
                                            <i class="bi bi-shield-check"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                
                                <?php if (empty($certificates)): ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-award display-4 text-muted"></i>
                                    <p class="text-muted mt-2">
                                        No certificates earned yet. Complete courses to earn certificates.
                                    </p>
                                    <a href="browse-courses" class="btn btn-sm btn-primary">Browse Courses</a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Dark Mode Toggle -->
    <div class="dark-mode-toggle">
        <button class="btn btn-light rounded-circle shadow" id="darkModeToggle">
            <i class="bi bi-moon-stars"></i>
        </button>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile Sidebar Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('mainSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleBtn = document.getElementById('sidebarToggle');
            const closeBtn = document.getElementById('sidebarClose');
            
            if (!sidebar || !overlay || !toggleBtn || !closeBtn) {
                console.log('Sidebar elements not found');
                return;
            }
            
            function openSidebar() {
                sidebar.classList.add('active');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            
            function closeSidebar() {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            toggleBtn.addEventListener('click', openSidebar);
            closeBtn.addEventListener('click', closeSidebar);
            overlay.addEventListener('click', closeSidebar);
            
            // Close sidebar on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                    closeSidebar();
                }
            });
            
            // Handle window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (window.innerWidth >= 992) {
                        closeSidebar();
                    }
                }, 250);
            });
        });

        // Dark Mode Toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        if (darkModeToggle) {
            darkModeToggle.addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                const icon = this.querySelector('i');
                if (document.body.classList.contains('dark-mode')) {
                    icon.classList.remove('bi-moon-stars');
                    icon.classList.add('bi-brightness-high-fill');
                    localStorage.setItem('darkMode', 'enabled');
                } else {
                    icon.classList.remove('bi-brightness-high-fill');
                    icon.classList.add('bi-moon-stars');
                    localStorage.setItem('darkMode', 'disabled');
                }
            });
        }

        // Check for saved dark mode preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
            const icon = document.querySelector('#darkModeToggle i');
            if (icon) {
                icon.classList.remove('bi-moon-stars');
                icon.classList.add('bi-brightness-high-fill');
            }
        }

        // Global search
        const searchInput = document.getElementById('globalSearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    window.location.href = 'browse-courses?search=' + encodeURIComponent(this.value);
                }
            });
        }

        // Active link highlighting
        const currentPath = window.location.pathname.split('/').pop();
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPath) {
                link.classList.add('active');
            }
        });
    </script>
</body>
</html>
@endverbatim

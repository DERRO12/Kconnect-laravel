@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim


<!-- Add AOS Animation -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Add SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
:root {
    --kiboko-primary: #009ada;
    --kiboko-secondary: #76bc21;
    --kiboko-foundation: #4299e1;
    --kiboko-mastery: #48bb78;
    --kiboko-resource: #ed8936;
    --kiboko-compliance: #9f7aea;
}

.course-header {
    background: linear-gradient(135deg, var(--kiboko-primary) 0%, var(--kiboko-secondary) 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 0 0 20px 20px;
}

.course-title {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.category-badge {
    display: inline-block;
    padding: 0.4rem 1.2rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
    background: rgba(255,255,255,0.15);
    color: white;
    backdrop-filter: blur(5px);
}

.course-stats {
    display: flex;
    gap: 2rem;
    margin: 1.5rem 0;
}

.course-stat {
    text-align: center;
}

.course-stat-value {
    font-size: 1.6rem;
    font-weight: 700;
    line-height: 1;
}

.course-stat-label {
    font-size: 0.85rem;
    opacity: 0.9;
}

.course-image-large {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    height: 280px;
    background-size: cover;
    background-position: center;
}

.course-image-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.enrollment-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
    position: sticky;
    top: 100px;
}

.course-features {
    list-style: none;
    padding: 0;
    margin: 1rem 0;
}

.course-features li {
    padding: 0.6rem 0;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-size: 0.95rem;
}

.course-features li:last-child {
    border-bottom: none;
}

.course-features i {
    color: var(--kiboko-primary);
    width: 20px;
}

.curriculum-section {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.03);
    border: 1px solid #e2e8f0;
    margin-bottom: 1.5rem;
}

.lesson-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0;
    transition: background 0.3s ease;
}

.lesson-item:hover {
    background: #f8fafc;
}

.lesson-item:last-child {
    border-bottom: none;
}

.lesson-icon {
    width: 36px;
    height: 36px;
    background: #f1f5f9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: var(--kiboko-primary);
}

.lesson-details {
    flex: 1;
}

.lesson-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
    font-size: 1rem;
}

.lesson-meta {
    font-size: 0.8rem;
    color: #64748b;
    display: flex;
    gap: 1rem;
}

.lesson-completed {
    color: var(--kiboko-secondary);
    font-weight: 600;
    font-size: 0.85rem;
}

.instructor-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 12px;
}

.instructor-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--kiboko-primary), var(--kiboko-secondary));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    font-weight: 600;
}

.instructor-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.tab-navigation {
    display: flex;
    gap: 1rem;
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 1.5rem;
}

.tab-link {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    color: #64748b;
    border: none;
    background: none;
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
}

.tab-link.active {
    color: var(--kiboko-primary);
}

.tab-link.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--kiboko-primary);
}

.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
}

.btn-enroll {
    background: linear-gradient(135deg, var(--kiboko-primary), var(--kiboko-secondary));
    color: white;
    border: none;
    padding: 0.75rem;
    border-radius: 8px;
    font-weight: 600;
    width: 100%;
    transition: all 0.3s ease;
}

.btn-enroll:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,154,218,0.3);
}

.btn-continue {
    background: var(--kiboko-primary);
    color: white;
    border: none;
    padding: 0.75rem;
    border-radius: 8px;
    font-weight: 600;
    width: 100%;
    text-decoration: none;
    display: block;
    text-align: center;
    transition: all 0.3s ease;
}

.btn-continue:hover {
    background: #0085b3;
    color: white;
}

.category-badge-foundation {
    background: var(--kiboko-foundation);
    color: white;
}

.category-badge-mastery {
    background: var(--kiboko-mastery);
    color: white;
}

.category-badge-resource {
    background: var(--kiboko-resource);
    color: white;
}

.category-badge-compliance {
    background: var(--kiboko-compliance);
    color: white;
}

@media (max-width: 768px) {
    .course-title {
        font-size: 1.8rem;
    }
    
    .course-stats {
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .enrollment-card {
        position: static;
        margin-top: 1.5rem;
    }
    
    .tab-navigation {
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 0.5rem;
    }
}
</style>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Course Header -->
    <div class="course-header" data-aos="fade-down">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="mb-3">
                        <?php 
                        $category_colors = [
                            'foundation' => 'badge-foundation',
                            'mastery' => 'badge-mastery',
                            'resource' => 'badge-resource',
                            'compliance' => 'badge-compliance'
                        ];
                        $cat_type = $course['category_type'] ?? '';
                        $cat_class = $category_colors[$cat_type] ?? '';
                        ?>
                        <span class="category-badge <?php echo $cat_class; ?>">
                            <?php 
                            $category_names = [
                                'foundation' => 'K-Start: Foundation',
                                'mastery' => 'K-Expert: Mastery',
                                'resource' => 'K-Library: Resource',
                                'compliance' => 'K-Compliance Center'
                            ];
                            echo $category_names[$cat_type] ?? ($course['category'] ?? 'Course');
                            ?>
                        </span>
                        
                        <?php if ($course['level']): ?>
                        <span class="category-badge">
                            <i class="bi bi-bar-chart"></i> <?php echo ucfirst($course['level']); ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($course['duration']): ?>
                        <span class="category-badge">
                            <i class="bi bi-clock"></i> <?php echo $course['duration']; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <h1 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h1>
                    
                    
                    
                    <div class="course-stats">
                        <div class="course-stat">
                            <div class="course-stat-value"><?php echo $course['total_lessons']; ?></div>
                            <div class="course-stat-label">Lessons</div>
                        </div>
                        <div class="course-stat">
                            <div class="course-stat-value"><?php echo number_format($course['total_enrollments']); ?></div>
                            <div class="course-stat-label">Enrolled</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-5" data-aos="fade-left">
                    <div class="course-image-large">
                        <?php 
                        $thumbnail_path = 'assets/uploads/courses/' . ($course['thumbnail'] ?? '');
                        $thumbnail_exists = !empty($course['thumbnail']) && file_exists($thumbnail_path);
                        ?>
                        <?php if ($thumbnail_exists): ?>
                            <img src="<?php echo $thumbnail_path; ?>" 
                                 alt="<?php echo htmlspecialchars($course['title']); ?>">
                        <?php else: ?>
                            <img src="https://placehold.co/600x400/009ada/white?text=K-Academy" 
                                 alt="<?php echo htmlspecialchars($course['title']); ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Tab Navigation -->
                <div class="tab-navigation" data-aos="fade-up">
                    <button class="tab-link active" onclick="showTab('overview')">Overview</button>
                    <button class="tab-link" onclick="showTab('curriculum')">Curriculum (<?php echo count($lessons); ?>)</button>
                    <?php if ($instructor): ?>
                    <button class="tab-link" onclick="showTab('instructor')">Instructor</button>
                    <?php endif; ?>
                </div>

                <!-- Overview Tab -->
                <div id="overview" class="tab-pane active" data-aos="fade-up">
                    <div class="curriculum-section">
                        <h5 class="mb-3">About This Course</h5>
                        <?php if ($course['description']): ?>
                            <div class="mb-4">
                                <?php echo nl2br(htmlspecialchars($course['description'])); ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-4">No description available.</p>
                        <?php endif; ?>
                        
                        
                        
                        </ul>
                    </div>
                </div>

                <!-- Curriculum Tab -->
                <div id="curriculum" class="tab-pane" data-aos="fade-up">
                    <div class="curriculum-section">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Course Content</h5>
                            <span class="badge bg-light text-dark"><?php echo count($lessons); ?> lessons</span>
                        </div>
                        
                        <?php if (empty($lessons)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-journal-x fs-1 text-muted"></i>
                                <p class="mt-2 text-muted">No lessons available yet.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($lessons as $index => $lesson): ?>
                            <div class="lesson-item">
                                <div class="lesson-icon">
                                    <?php if ($enrollment && $lesson['completed']): ?>
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    <?php else: ?>
                                        <i class="bi bi-play-circle-fill"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="lesson-details">
                                    <div class="lesson-title">
                                        <?php echo htmlspecialchars($lesson['title']); ?>
                                        <?php if ($enrollment && $lesson['completed']): ?>
                                            <span class="lesson-completed ms-2">
                                                <i class="bi bi-check-circle"></i> Completed
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="lesson-meta">
                                        <?php if ($lesson['duration']): ?>
                                            <span><i class="bi bi-clock"></i> <?php echo $lesson['duration']; ?> min</span>
                                        <?php endif; ?>
                                        <span>Lesson <?php echo $index + 1; ?></span>
                                    </div>
                                </div>
                                <?php if ($enrollment): ?>
                                    <a href="course-player?id=<?php echo $course_id; ?>&lesson=<?php echo $lesson['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <?php echo $lesson['completed'] ? 'Review' : 'Start'; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Instructor Tab -->
                <?php if ($instructor): ?>
                <div id="instructor" class="tab-pane" data-aos="fade-up">
                    <div class="curriculum-section">
                        <h5 class="mb-3">About the Instructor</h5>
                        
                        <div class="instructor-card">
                            <div class="instructor-avatar">
                                <?php if (!empty($instructor['profile_image'])): ?>
                                    <img src="assets/uploads/profiles/<?php echo $instructor['profile_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($instructor['full_name']); ?>">
                                <?php else: ?>
                                    <?php echo strtoupper(substr($instructor['full_name'], 0, 1)); ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h6 class="mb-1"><?php echo htmlspecialchars($instructor['full_name']); ?></h6>
                                <p class="text-muted small mb-1"><?php echo htmlspecialchars($instructor['employee_code']); ?></p>
                                <?php if ($instructor['email']): ?>
                                <p class="small mb-0">
                                    <i class="bi bi-envelope"></i> 
                                    <?php echo $instructor['email']; ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <p class="mt-3 text-muted small">
                            Expert instructor with extensive experience, dedicated to helping KIBOKO employees grow their skills.
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar - Enrollment Card -->
            <div class="col-lg-4" data-aos="fade-left">
                <div class="enrollment-card">
                    <?php if ($enrollment): ?>
                        <?php 
                        $completed_lessons = count(array_filter($lessons, fn($l) => $l['completed']));
                        $progress = $course['total_lessons'] > 0 ? round(($completed_lessons / $course['total_lessons']) * 100) : 0;
                        ?>
                        
                        <h5 class="mb-3">Your Progress</h5>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Progress</span>
                                <span class="small fw-bold"><?php echo $progress; ?>%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-gradient" style="width: <?php echo $progress; ?>%"></div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <?php echo $completed_lessons; ?>/<?php echo $course['total_lessons']; ?> lessons completed
                            </small>
                        </div>
                        
                        <a href="course-player?id=<?php echo $course_id; ?>" class="btn-continue mb-2">
                            <i class="bi bi-play-circle"></i> Continue Learning
                        </a>
                        
                    <?php else: ?>
                        <h5 class="mb-3">Enroll in this Course</h5>
                        
                        <ul class="course-features">
                            <li>
                                <i class="bi bi-play-circle"></i>
                                <span><?php echo $course['total_lessons']; ?> lessons</span>
                            </li>
                            <li>
                                <i class="bi bi-clock-history"></i>
                                <span><?php echo $course['duration'] ?: 'Self-paced'; ?></span>
                            </li>
                            <li>
                                <i class="bi bi-trophy"></i>
                                <span>Certificate upon completion</span>
                            </li>
                            <li>
                                <i class="bi bi-phone"></i>
                                <span>Access on any device</span>
                            </li>
                        </ul>
                        
                        <button class="btn-enroll" onclick="enrollCourse()">
                            <i class="bi bi-plus-circle"></i> Enroll Now
                        </button>
                    <?php endif; ?>
                    
                    <hr class="my-3">
                    
                    <div class="text-center">
                        <p class="small text-muted mb-1">
                            <i class="bi bi-people"></i> 
                            <?php echo number_format($course['total_enrollments']); ?> enrolled
                        </p>
                        <p class="small text-muted">
                            <i class="bi bi-calendar"></i> 
                            Added: <?php echo date('M d, Y', strtotime($course['created_at'])); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Initialize AOS
AOS.init({
    duration: 800,
    once: true,
    offset: 50
});

// Tab switching
function showTab(tabId) {
    // Hide all tabs
    document.querySelectorAll('.tab-pane').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all tab links
    document.querySelectorAll('.tab-link').forEach(link => {
        link.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabId).classList.add('active');
    
    // Add active class to clicked link
    event.target.classList.add('active');
}

// Enroll course function
function enrollCourse() {
    Swal.fire({
        title: 'Enroll in Course?',
        text: 'Are you sure you want to enroll in this course?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#009ada',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, enroll me!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('api/enroll-course', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    course_id: <?php echo $course_id; ?>,
                    csrf_token: '<?php echo $csrf_token; ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Enrolled Successfully!',
                        text: 'You can now start learning.',
                        icon: 'success',
                        confirmButtonColor: '#009ada'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to enroll',
                        icon: 'error',
                        confirmButtonColor: '#009ada'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#009ada'
                });
            });
        }
    });
}
</script>


@endverbatim

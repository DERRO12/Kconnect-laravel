@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim


<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Add AOS Animation -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<style>
/* Main container */
.progress-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* Header Section */
.profile-header {
    background: linear-gradient(135deg, var(--kiboko-primary) 0%, var(--kiboko-secondary) 100%);
    border-radius: 24px;
    padding: 2rem;
    margin-bottom: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(0,154,218,0.3);
}

.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 600;
    color: var(--kiboko-primary);
    border: 3px solid rgba(255,255,255,0.3);
}

.profile-stats {
    display: flex;
    gap: 2rem;
    margin-top: 1rem;
}

.profile-stat-item {
    text-align: center;
    background: rgba(255,255,255,0.1);
    padding: 1rem 1.5rem;
    border-radius: 16px;
    backdrop-filter: blur(10px);
}

.profile-stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    line-height: 1;
}

.profile-stat-label {
    font-size: 0.85rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Period Selector */
.period-selector {
    display: flex;
    gap: 0.5rem;
    background: white;
    padding: 0.25rem;
    border-radius: 50px;
    border: 1px solid var(--kiboko-border);
    box-shadow: var(--shadow-sm);
}

.period-btn {
    padding: 0.5rem 1.25rem;
    border: none;
    background: transparent;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.period-btn.active {
    background: var(--kiboko-primary);
    color: white;
}

/* Stat Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.03);
    border: 1px solid var(--kiboko-border);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,154,218,0.1);
}

.stat-icon-wrapper {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--kiboko-primary) 0%, var(--kiboko-secondary) 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
}

.stat-content {
    flex: 1;
}

.stat-main {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--kiboko-dark);
    line-height: 1;
}

.stat-label {
    color: var(--kiboko-gray);
    font-size: 0.9rem;
}

.stat-progress {
    height: 4px;
    background: var(--kiboko-border);
    border-radius: 2px;
    overflow: hidden;
}

.stat-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--kiboko-primary), var(--kiboko-secondary));
    border-radius: 2px;
}

/* Chart Container */
.chart-card {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.03);
    border: 1px solid var(--kiboko-border);
    margin-bottom: 2rem;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--kiboko-border);
}

.chart-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--kiboko-dark);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.chart-title i {
    color: var(--kiboko-primary);
}

/* Course Progress Cards */
.courses-section {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.03);
    border: 1px solid var(--kiboko-border);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--kiboko-border);
}

.section-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--kiboko-dark);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-title i {
    color: var(--kiboko-primary);
}

.course-progress-card {
    background: white;
    border: 1px solid var(--kiboko-border);
    border-radius: 16px;
    margin-bottom: 1rem;
    overflow: hidden;
    transition: all 0.3s ease;
}

.course-progress-card:hover {
    border-color: var(--kiboko-primary);
    box-shadow: 0 10px 30px rgba(0,154,218,0.1);
}

.course-progress-header {
    padding: 1.5rem;
    cursor: pointer;
    background: white;
    transition: background 0.3s ease;
}

.course-progress-header:hover {
    background: var(--kiboko-light);
}

.course-main-info {
    display: flex;
    gap: 1rem;
}

.course-thumbnail {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    object-fit: cover;
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
}

.course-thumbnail-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--kiboko-primary), var(--kiboko-secondary));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
}

.course-details {
    flex: 1;
}

.course-title-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.course-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--kiboko-dark);
    margin: 0;
}

.course-badges {
    display: flex;
    gap: 0.5rem;
}

.course-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    background: var(--kiboko-light);
    color: var(--kiboko-gray);
}

.course-badge.primary { background: #e3f2fd; color: #1976d2; }
.course-badge.success { background: #e8f5e9; color: #388e3c; }
.course-badge.warning { background: #fff3e0; color: #f57c00; }

.course-meta {
    display: flex;
    gap: 1.5rem;
    margin: 0.5rem 0;
    font-size: 0.85rem;
    color: var(--kiboko-gray);
}

.course-meta-item {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.course-meta-item i {
    color: var(--kiboko-primary);
}

.progress-section {
    margin-top: 1rem;
}

.progress-row {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.progress-bar-container {
    flex: 1;
    height: 8px;
    background: var(--kiboko-border);
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--kiboko-primary), var(--kiboko-secondary));
    border-radius: 4px;
    transition: width 0.3s ease;
}

.progress-percentage {
    font-weight: 600;
    color: var(--kiboko-primary);
    min-width: 45px;
}

.lesson-stats {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
    font-size: 0.85rem;
}

.lesson-stat {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.75rem;
    background: var(--kiboko-light);
    border-radius: 50px;
}

.lesson-stat i {
    color: var(--kiboko-primary);
}

/* Lessons Dropdown */
.lessons-dropdown {
    background: var(--kiboko-light);
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--kiboko-border);
}

.lessons-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 0.75rem;
    margin-top: 0.5rem;
}

.lesson-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: white;
    border-radius: 10px;
    border: 1px solid var(--kiboko-border);
    transition: all 0.2s ease;
}

.lesson-item:hover {
    border-color: var(--kiboko-primary);
    transform: translateX(3px);
}

.lesson-status {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
}

.lesson-status.completed {
    background: #e8f5e9;
    color: #388e3c;
}

.lesson-status.pending {
    background: #fff3e0;
    color: #f57c00;
}

.lesson-info {
    flex: 1;
}

.lesson-name {
    font-weight: 500;
    font-size: 0.9rem;
    color: var(--kiboko-dark);
}

.lesson-meta {
    font-size: 0.75rem;
    color: var(--kiboko-gray);
    display: flex;
    gap: 0.5rem;
    margin-top: 0.2rem;
}

/* Achievements Sidebar */
.achievements-card {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.03);
    border: 1px solid var(--kiboko-border);
    height: fit-content;
    position: sticky;
    top: 100px;
}

.achievement-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--kiboko-light);
    border-radius: 12px;
    margin-bottom: 0.75rem;
    transition: all 0.3s ease;
}

.achievement-item:hover {
    transform: translateX(5px);
    background: white;
    box-shadow: var(--shadow-md);
}

.achievement-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
}

.achievement-icon.course { background: #e3f2fd; color: #1976d2; }
.achievement-icon.certificate { background: #fff3e0; color: #f57c00; }
.achievement-icon.quiz { background: #e8f5e9; color: #388e3c; }

.achievement-content {
    flex: 1;
}

.achievement-title {
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--kiboko-dark);
    margin-bottom: 0.2rem;
}

.achievement-date {
    font-size: 0.8rem;
    color: var(--kiboko-gray);
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

/* Quiz Stats */
.quiz-stats-card {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.03);
    border: 1px solid var(--kiboko-border);
    margin-top: 1.5rem;
}

.quiz-stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-top: 1rem;
}

.quiz-stat-item {
    text-align: center;
    padding: 1rem;
    background: var(--kiboko-light);
    border-radius: 12px;
}

.quiz-stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--kiboko-primary);
    line-height: 1;
}

.quiz-stat-label {
    font-size: 0.8rem;
    color: var(--kiboko-gray);
    margin-top: 0.3rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem;
    background: var(--kiboko-light);
    border-radius: 20px;
}

.empty-state i {
    font-size: 4rem;
    color: var(--kiboko-gray-light);
    margin-bottom: 1rem;
}

.empty-state h5 {
    color: var(--kiboko-dark);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--kiboko-gray);
    margin-bottom: 1.5rem;
}

/* Responsive */
@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .profile-header .row {
        flex-direction: column;
        gap: 1rem;
    }
    
    .profile-stats {
        flex-wrap: wrap;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .course-main-info {
        flex-direction: column;
    }
    
    .course-thumbnail,
    .course-thumbnail-placeholder {
        width: 100%;
        height: 120px;
    }
    
    .course-title-row {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .lessons-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <div class="progress-container">
        <!-- Profile Header -->
        <div class="profile-header" data-aos="fade-down">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-4">
                        <?php if (!empty($user['profile_image']) && file_exists('assets/uploads/profiles/' . $user['profile_image'])): ?>
                            <img src="assets/uploads/profiles/<?php echo $user['profile_image']; ?>" 
                                 alt="<?php echo htmlspecialchars($user['full_name']); ?>"
                                 class="profile-avatar" style="object-fit: cover;">
                        <?php else: ?>
                            <div class="profile-avatar">
                                <?php echo strtoupper(substr($user['full_name'] ?? 'U', 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <h2 class="h3 mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h2>
                            <p class="mb-2 opacity-75">
                                <i class="bi bi-briefcase me-2"></i>
                                Employee Code: <?php echo htmlspecialchars($user['employee_code']); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="profile-stats justify-content-end">
                        <div class="profile-stat-item">
                            <div class="profile-stat-value"><?php echo $stats['avg_progress'] ?? 0; ?>%</div>
                            <div class="profile-stat-label">Avg Progress</div>
                        </div>
                        <div class="profile-stat-item">
                            <div class="profile-stat-value"><?php echo $certificates_count; ?></div>
                            <div class="profile-stat-label">Certificates</div>
                        </div>
                        <div class="profile-stat-item">
                            <div class="profile-stat-value"><?php echo $total_hours; ?>h</div>
                            <div class="profile-stat-label">Learning</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Selector -->
        <div class="d-flex justify-content-end mb-4" data-aos="fade-down">
            <div class="period-selector">
                <button class="period-btn <?php echo $period == 'week' ? 'active' : ''; ?>" onclick="changePeriod('week')">Week</button>
                <button class="period-btn <?php echo $period == 'month' ? 'active' : ''; ?>" onclick="changePeriod('month')">Month</button>
                <button class="period-btn <?php echo $period == 'year' ? 'active' : ''; ?>" onclick="changePeriod('year')">Year</button>
                <button class="period-btn <?php echo $period == 'all' ? 'active' : ''; ?>" onclick="changePeriod('all')">All Time</button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid mb-4">
            <div class="stat-card" data-aos="fade-up" data-aos-delay="0">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-book"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-main">
                        <span class="stat-value"><?php echo $stats['total_courses'] ?? 0; ?></span>
                        <span class="stat-label">Total</span>
                    </div>
                    <div class="stat-progress">
                        <div class="stat-progress-fill" style="width: <?php echo $stats['completed_courses'] > 0 ? ($stats['completed_courses'] / $stats['total_courses'] * 100) : 0; ?>%"></div>
                    </div>
                    <div style="font-size: 0.8rem; color: var(--kiboko-gray); margin-top: 0.3rem;">
                        <?php echo $stats['completed_courses'] ?? 0; ?> completed • 
                        <?php echo $stats['in_progress_courses'] ?? 0; ?> in progress
                    </div>
                </div>
            </div>

            <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-main">
                        <span class="stat-value"><?php echo $lesson_stats['completed_lessons'] ?? 0; ?></span>
                        <span class="stat-label">of <?php echo $lesson_stats['total_lessons'] ?? 0; ?></span>
                    </div>
                    <div class="stat-progress">
                        <div class="stat-progress-fill" style="width: <?php echo $lesson_stats['total_lessons'] > 0 ? ($lesson_stats['completed_lessons'] / $lesson_stats['total_lessons'] * 100) : 0; ?>%"></div>
                    </div>
                    <div style="font-size: 0.8rem; color: var(--kiboko-gray); margin-top: 0.3rem;">
                        <i class="bi bi-check-circle text-success"></i> Lessons completed
                    </div>
                </div>
            </div>

            <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-main">
                        <span class="stat-value"><?php echo $total_hours; ?>h <?php echo $total_minutes; ?>m</span>
                    </div>
                    <div style="font-size: 0.8rem; color: var(--kiboko-gray); margin-top: 0.5rem;">
                        <i class="bi bi-lightning text-warning"></i> Total learning time
                    </div>
                </div>
            </div>

            <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-icon-wrapper">
                    <i class="bi bi-award"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-main">
                        <span class="stat-value"><?php echo $certificates_count; ?></span>
                    </div>
                    <div style="font-size: 0.8rem; color: var(--kiboko-gray); margin-top: 0.5rem;">
                        <i class="bi bi-trophy text-warning"></i> Certificates earned
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row">
            <!-- Left Column - Course Progress -->
            <div class="col-lg-8">
                <div class="courses-section" data-aos="fade-right">
                    <div class="section-header">
                        <h5 class="section-title">
                            <i class="bi bi-bar-chart"></i>
                            Course Progress
                        </h5>
                        <span class="badge bg-primary"><?php echo count($course_progress); ?> Courses</span>
                    </div>
                    
                    <?php if (empty($course_progress)): ?>
                        <div class="empty-state">
                            <i class="bi bi-journal-x"></i>
                            <h5>No courses yet</h5>
                            <p>Start learning to see your progress here.</p>
                            <a href="browse-courses" class="btn btn-primary">Browse Courses</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($course_progress as $index => $course): ?>
                        <div class="course-progress-card" data-aos="fade-up" data-aos-delay="<?php echo $index * 50; ?>">
                            <div class="course-progress-header" onclick="toggleLessons(<?php echo $course['id']; ?>)">
                                <div class="course-main-info">
                                    <?php 
                                    $thumb_path = 'assets/uploads/courses/' . ($course['thumbnail'] ?? '');
                                    $thumb_exists = !empty($course['thumbnail']) && file_exists($thumb_path);
                                    ?>
                                    <?php if ($thumb_exists): ?>
                                        <img src="<?php echo $thumb_path; ?>" 
                                             alt="<?php echo htmlspecialchars($course['title']); ?>"
                                             class="course-thumbnail">
                                    <?php else: ?>
                                        <div class="course-thumbnail-placeholder">
                                            <i class="bi bi-journal-bookmark-fill"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="course-details">
                                        <div class="course-title-row">
                                            <h6 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h6>
                                            <div class="course-badges">
                                                <span class="course-badge primary"><?php echo $course['category'] ?? 'General'; ?></span>
                                                <span class="course-badge <?php echo $course['status'] == 'completed' ? 'success' : 'warning'; ?>">
                                                    <?php echo ucfirst($course['status']); ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="course-meta">
                                            <span class="course-meta-item">
                                                <i class="bi bi-calendar"></i>
                                                Enrolled: <?php echo date('M d, Y', strtotime($course['enrolled_at'])); ?>
                                            </span>
                                            <span class="course-meta-item">
                                                <i class="bi bi-clock"></i>
                                                Last: <?php echo $course['last_accessed'] ? ka_time_ago($course['last_accessed']) : 'Not started'; ?>
                                            </span>
                                            <span class="course-meta-item">
                                                <i class="bi bi-bar-chart"></i>
                                                Level: <?php echo ucfirst($course['level'] ?? 'Beginner'); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="progress-section">
                                            <div class="progress-row">
                                                <div class="progress-bar-container">
                                                    <div class="progress-bar-fill" style="width: <?php echo $course['progress_percentage']; ?>%"></div>
                                                </div>
                                                <span class="progress-percentage"><?php echo round($course['progress_percentage']); ?>%</span>
                                            </div>
                                            
                                            <div class="lesson-stats">
                                                <span class="lesson-stat">
                                                    <i class="bi bi-journal-check"></i>
                                                    <?php echo $course['completed_lessons']; ?>/<?php echo $course['total_lessons']; ?> lessons
                                                </span>
                                                <?php if ($course['quiz_score']): ?>
                                                    <span class="lesson-stat text-<?php echo $course['quiz_passed'] ? 'success' : 'warning'; ?>">
                                                        <i class="bi bi-patch-question"></i>
                                                        Quiz: <?php echo round($course['quiz_score']); ?>%
                                                    </span>
                                                <?php endif; ?>
                                                <?php if ($course['certificate_id']): ?>
                                                    <span class="lesson-stat text-success">
                                                        <i class="bi bi-award-fill"></i>
                                                        Certified
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="dropdown-toggle-icon">
                                        <i class="bi bi-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Lessons Dropdown (Hidden by default) -->
                            <div id="lessons-<?php echo $course['id']; ?>" class="lessons-dropdown" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Course Lessons</h6>
                                    <a href="course-player?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-play-circle"></i> Continue Course
                                    </a>
                                </div>
                                <div class="lessons-grid" id="lessons-grid-<?php echo $course['id']; ?>">
                                    <!-- Lessons will be loaded via AJAX -->
                                    <div class="text-center py-3">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span class="ms-2">Loading lessons...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Right Column - Achievements & Quiz Stats -->
            <div class="col-lg-4">
                <!-- Recent Achievements -->
                <div class="achievements-card" data-aos="fade-left">
                    <h5 class="mb-3">
                        <i class="bi bi-trophy me-2 text-warning"></i>
                        Recent Achievements
                    </h5>
                    
                    <?php if (empty($recent_achievements)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-award display-4 text-muted"></i>
                            <p class="text-muted mt-2">No achievements yet</p>
                            <small>Complete courses and quizzes to earn achievements!</small>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recent_achievements as $achievement): ?>
                        <div class="achievement-item">
                            <div class="achievement-icon <?php echo $achievement['type']; ?>">
                                <i class="bi bi-<?php 
                                    echo $achievement['type'] == 'course_completed' ? 'check-circle' : 
                                        ($achievement['type'] == 'certificate' ? 'award' : 'patch-check'); 
                                ?>"></i>
                            </div>
                            <div class="achievement-content">
                                <div class="achievement-title">
                                    <?php echo htmlspecialchars($achievement['title']); ?>
                                </div>
                                <div class="achievement-date">
                                    <i class="bi bi-calendar"></i> 
                                    <?php echo date('M d, Y', strtotime($achievement['achieved_date'])); ?>
                                </div>
                            </div>
                            <?php if ($achievement['type'] == 'certificate'): ?>
                                <a href="download-certificate?id=<?php echo $achievement['reference_id']; ?>" 
                                   class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-download"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Quiz Performance -->
                <div class="quiz-stats-card" data-aos="fade-left" data-aos-delay="100">
                    <h5 class="mb-3">
                        <i class="bi bi-patch-question me-2 text-info"></i>
                        Quiz Performance
                    </h5>
                    
                    <?php if ($quiz_stats['total_quizzes'] > 0): ?>
                        <div class="text-center mb-3">
                            <div style="font-size: 3rem; font-weight: 700; color: var(--kiboko-primary);">
                                <?php echo round($quiz_stats['avg_score'] ?? 0); ?>%
                            </div>
                            <small class="text-muted">Average Score</small>
                        </div>
                        
                        <div class="quiz-stats-grid">
                            <div class="quiz-stat-item">
                                <div class="quiz-stat-value"><?php echo $quiz_stats['total_quizzes']; ?></div>
                                <div class="quiz-stat-label">Total Quizzes</div>
                            </div>
                            <div class="quiz-stat-item">
                                <div class="quiz-stat-value"><?php echo $quiz_stats['passed_quizzes']; ?></div>
                                <div class="quiz-stat-label">Passed</div>
                            </div>
                            <div class="quiz-stat-item">
                                <div class="quiz-stat-value"><?php echo round($quiz_stats['best_score'] ?? 0); ?>%</div>
                                <div class="quiz-stat-label">Best Score</div>
                            </div>
                            <div class="quiz-stat-item">
                                <div class="quiz-stat-value">
                                    <?php echo $quiz_stats['total_quizzes'] > 0 ? round(($quiz_stats['passed_quizzes'] / $quiz_stats['total_quizzes']) * 100) : 0; ?>%
                                </div>
                                <div class="quiz-stat-label">Pass Rate</div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-patch-question display-4 text-muted"></i>
                            <p class="text-muted mt-2">No quizzes taken yet</p>
                        </div>
                    <?php endif; ?>
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

// Toggle lessons dropdown
function toggleLessons(courseId) {
    const lessonsDiv = document.getElementById(`lessons-${courseId}`);
    const icon = event.currentTarget.querySelector('.dropdown-toggle-icon i');
    
    if (lessonsDiv.style.display === 'none') {
        lessonsDiv.style.display = 'block';
        icon.classList.remove('bi-chevron-down');
        icon.classList.add('bi-chevron-up');
        
        // Load lessons if not already loaded
        if (!lessonsDiv.dataset.loaded) {
            loadLessons(courseId);
            lessonsDiv.dataset.loaded = 'true';
        }
    } else {
        lessonsDiv.style.display = 'none';
        icon.classList.remove('bi-chevron-up');
        icon.classList.add('bi-chevron-down');
    }
}

// Load lessons via AJAX
function loadLessons(courseId) {
    fetch(`api/get-course-lessons?course_id=${courseId}`)
        .then(response => response.json())
        .then(data => {
            const grid = document.getElementById(`lessons-grid-${courseId}`);
            
            if (data.success && data.lessons.length > 0) {
                let html = '';
                data.lessons.forEach(lesson => {
                    html += `
                        <div class="lesson-item">
                            <div class="lesson-status ${lesson.completed ? 'completed' : 'pending'}">
                                <i class="bi bi-${lesson.completed ? 'check-circle-fill' : 'circle'}"></i>
                            </div>
                            <div class="lesson-info">
                                <div class="lesson-name">${lesson.title}</div>
                                <div class="lesson-meta">
                                    ${lesson.duration ? `<span><i class="bi bi-clock"></i> ${lesson.duration} min</span>` : ''}
                                    ${lesson.video_type ? `<span><i class="bi bi-${lesson.video_type == 'youtube' ? 'youtube' : 'camera-reels'}"></i> ${lesson.video_type}</span>` : ''}
                                </div>
                            </div>
                            <a href="course-player?id=${courseId}&lesson=${lesson.id}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-play-circle"></i>
                            </a>
                        </div>
                    `;
                });
                grid.innerHTML = html;
            } else {
                grid.innerHTML = '<div class="text-center py-3 text-muted">No lessons available</div>';
            }
        })
        .catch(error => {
            console.error('Error loading lessons:', error);
            document.getElementById(`lessons-grid-${courseId}`).innerHTML = 
                '<div class="text-center py-3 text-danger">Failed to load lessons</div>';
        });
}

// Change period function
function changePeriod(period) {
    window.location.href = 'progress?period=' + period;
}

// Export progress
function exportProgress() {
    Swal.fire({
        title: 'Export Progress',
        text: 'This will generate a PDF report of your learning progress.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#009ada',
        confirmButtonText: 'Export'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'export-progress';
        }
    });
}

// Share achievement
function shareAchievement(title, date) {
    const text = `I just earned a new achievement on KIBOKO L&D: ${title} on ${date}! 🎓`;
    
    if (navigator.share) {
        navigator.share({
            title: 'My Learning Achievement',
            text: text,
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(text);
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Achievement text copied to clipboard',
            timer: 2000,
            showConfirmButton: false
        });
    }
}
</script>


@endverbatim

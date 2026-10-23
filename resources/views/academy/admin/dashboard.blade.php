@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim


            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

                <!-- Welcome Banner -->
                <div class="welcome-banner-admin mb-4">
                    <h2>Admin Dashboard</h2>
                    <p class="text-secondary">Welcome back! Here's what's happening with your LMS today.</p>
                </div>

                <!-- Statistics Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card-admin gradient-blue">
                            <div class="stat-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $stats['total_users']; ?></h3>
                                <p>Learners</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card-admin gradient-green">
                            <div class="stat-icon">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div class="stat-details">
                                <h3><a href="pending-certificates" class="text-reset text-decoration-none"><?php echo (int) $pdo->query("SELECT COUNT(*) FROM certificates WHERE status = 'pending'")->fetchColumn(); ?></a></h3>
                                <p>To Approve</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card-admin gradient-orange">
                            <div class="stat-icon">
                                <i class="bi bi-book"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $stats['total_courses']; ?></h3>
                                <p>Courses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card-admin gradient-purple">
                            <div class="stat-icon">
                                <i class="bi bi-journal-check"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $stats['total_enrollments']; ?></h3>
                                <p>Enrollments</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card-admin gradient-pink">
                            <div class="stat-icon">
                                <i class="bi bi-trophy"></i>
                            </div>
                            <div class="stat-details">
                                <h3><?php echo $stats['completion_rate']; ?>%</h3>
                                <p>Completion</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card-admin gradient-teal">
                            <div class="stat-icon">
                                <i class="bi bi-award"></i>
                            </div>
                            <div class="stat-details">
                                <h3>
                                    <?php
                                    $stmt = $pdo->query("SELECT COUNT(*) FROM certificates");
                                    echo $stmt->fetchColumn();
                                    ?>
                                </h3>
                                <p>Certificates</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0">Enrollment Trends</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="enrollmentChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0">Course Popularity</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="courseChart" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities & Course Stats -->
                <div class="row g-4">
                    <!-- Recent Activities -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Activities</h5>
                                <a href="activity-logs" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="activity-timeline">
                                    <?php foreach ($recent_activities as $activity): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon bg-<?php 
                                            echo strpos($activity['action'], 'login') !== false ? 'success' : 
                                                (strpos($activity['action'], 'course') !== false ? 'info' : 'primary');
                                        ?> bg-opacity-10">
                                            <i class="bi bi-<?php 
                                                echo strpos($activity['action'], 'login') !== false ? 'box-arrow-in-right' : 
                                                    (strpos($activity['action'], 'course') !== false ? 'book' : 'person');
                                            ?> text-<?php 
                                                echo strpos($activity['action'], 'login') !== false ? 'success' : 
                                                    (strpos($activity['action'], 'course') !== false ? 'info' : 'primary');
                                            ?>"></i>
                                        </div>
                                        <div class="activity-details">
                                            <p class="mb-1">
                                                <strong><?php echo $activity['full_name'] ?? 'System'; ?></strong>
                                                <?php echo $activity['action']; ?>
                                            </p>
                                            <small class="text-secondary">
                                                <?php echo date('M d, Y H:i', strtotime($activity['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Statistics -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0">Top Courses by Enrollment</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Course</th>
                                                <th>Enrollments</th>
                                                <th>Completed</th>
                                                <th>Rate</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($course_stats as $course): ?>
                                            <tr>
                                                <td><?php echo $course['title']; ?></td>
                                                <td><?php echo $course['enrollments']; ?></td>
                                                <td><?php echo $course['completed']; ?></td>
                                                <td>
                                                    <?php 
                                                    $rate = $course['enrollments'] > 0 ? 
                                                        round(($course['completed'] / $course['enrollments']) * 100) : 0;
                                                    ?>
                                                    <div class="progress" style="height: 5px;">
                                                        <div class="progress-bar bg-success" style="width: <?php echo $rate; ?>%"></div>
                                                    </div>
                                                    <small class="text-secondary"><?php echo $rate; ?>%</small>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enrollment Chart
        const ctx1 = document.getElementById('enrollmentChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Enrollments',
                    data: [65, 59, 80, 81, 56, 55, 40, 70, 85, 90, 95, 100],
                    borderColor: '#009ada',
                    backgroundColor: 'rgba(0, 154, 218, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Course Chart
        const ctx2 = document.getElementById('courseChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($course_stats, 'title')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($course_stats, 'enrollments')); ?>,
                    backgroundColor: [
                        '#009ada',
                        '#76bc21',
                        '#ffc107',
                        '#dc3545',
                        '#6f42c1'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
</body>
</html>
@endverbatim

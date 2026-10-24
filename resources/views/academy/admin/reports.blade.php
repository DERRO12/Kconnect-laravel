@php $page_title = $page_title ?? 'Reports'; @endphp
@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Analytics & Reports</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button class="btn btn-sm btn-outline-primary" onclick="exportReport()">
                <i class="bi bi-download"></i> Export Report
            </button>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="<?php echo $start_date; ?>">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="<?php echo $end_date; ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Apply Date Range</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10">
                    <i class="bi bi-people text-primary"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $engagement['active_users'] ?? 0; ?></h3>
                    <p>Active Users (7 days)</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10">
                    <i class="bi bi-check-circle text-success"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo round($engagement['avg_progress'] ?? 0); ?>%</h3>
                    <p>Avg Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info bg-opacity-10">
                    <i class="bi bi-trophy text-info"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $engagement['completed_courses'] ?? 0; ?></h3>
                    <p>Completed (7 days)</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10">
                    <i class="bi bi-patch-check text-warning"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo round($quiz_stats['avg_score'] ?? 0); ?>%</h3>
                    <p>Avg Quiz Score</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">User Growth</h6>
                </div>
                <div class="card-body">
                    <canvas id="userGrowthChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Enrollments Over Time</h6>
                </div>
                <div class="card-body">
                    <canvas id="enrollmentsChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Completion Table -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Course Completion Rates</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Enrollments</th>
                                    <th>Completions</th>
                                    <th>Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($course_completion as $course): ?>
                                <tr>
                                    <td><?php echo $course['title']; ?></td>
                                    <td><?php echo $course['total_enrollments']; ?></td>
                                    <td><?php echo $course['completions']; ?></td>
                                    <td>
                                        <?php 
                                        $rate = $course['total_enrollments'] > 0 
                                            ? round(($course['completions'] / $course['total_enrollments']) * 100) 
                                            : 0;
                                        ?>
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar bg-success" style="width: <?php echo $rate; ?>%"></div>
                                        </div>
                                        <small><?php echo $rate; ?>%</small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Students -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Top Performing Students</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Certificates</th>
                                    <th>Avg Quiz Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_students as $student): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?php echo $student['full_name']; ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo $student['employee_code']; ?></small>
                                        </div>
                                    </td>
                                    <td class="text-center"><?php echo $student['certificates']; ?></td>
                                    <td>
                                        <?php echo round($student['avg_quiz_score'] ?? 0); ?>%
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

    <!-- Quiz Performance -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Quiz Performance Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <h3><?php echo $quiz_stats['total_attempts'] ?? 0; ?></h3>
                            <p class="text-muted">Total Attempts (30 days)</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <h3><?php echo $quiz_stats['passed_count'] ?? 0; ?></h3>
                            <p class="text-muted">Passed</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <h3><?php echo round($quiz_stats['avg_score'] ?? 0); ?>%</h3>
                            <p class="text-muted">Average Score</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// User Growth Chart
const userCtx = document.getElementById('userGrowthChart').getContext('2d');
new Chart(userCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($user_growth, 'date')); ?>,
        datasets: [{
            label: 'New Users',
            data: <?php echo json_encode(array_column($user_growth, 'count')); ?>,
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

// Enrollments Chart
const enrollCtx = document.getElementById('enrollmentsChart').getContext('2d');
new Chart(enrollCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($enrollments_over_time, 'date')); ?>,
        datasets: [{
            label: 'Enrollments',
            data: <?php echo json_encode(array_column($enrollments_over_time, 'count')); ?>,
            backgroundColor: '#76bc21',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

function exportReport() {
    window.location.href = 'export-report?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>';
}
</script>
</body>


@endverbatim

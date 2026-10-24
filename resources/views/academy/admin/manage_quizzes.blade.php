@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim

   <link rel="stylesheet" href="../assets/css/style.css">
<!-- Add DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<!-- Add SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.quiz-card {
    transition: all 0.3s ease;
    border: none;
    border-radius: 16px;
    overflow: hidden;
    background: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    height: 100%;
}

.quiz-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,154,218,0.15);
}

.quiz-header {
    padding: 1.5rem;
    background: linear-gradient(135deg, var(--kiboko-blue) 0%, var(--kiboko-green) 100%);
    color: white;
    position: relative;
}

.quiz-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: white;
}

.quiz-header .course-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(255,255,255,0.2);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
}

.quiz-body {
    padding: 1.5rem;
}

.quiz-stats {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 12px;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--kiboko-dark);
    display: block;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--kiboko-gray);
}

.quiz-meta {
    margin-bottom: 1rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    color: var(--kiboko-gray);
    font-size: 0.9rem;
}

.meta-item i {
    width: 20px;
    color: var(--kiboko-blue);
}

.quiz-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.quiz-actions .btn {
    flex: 1;
    padding: 0.5rem;
    font-size: 0.85rem;
}

.status-badge {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

/* Table styles */
.table-quiz-title {
    font-weight: 600;
    color: var(--kiboko-dark);
}

.table-course-title {
    font-size: 0.85rem;
    color: var(--kiboko-gray);
}

.table-stats {
    display: flex;
    gap: 1rem;
    font-size: 0.85rem;
}

.table-stats span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Filter section */
.filter-section {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

/* Responsive */
@media (max-width: 768px) {
    .quiz-header h3 {
        font-size: 1rem;
    }
    
    .quiz-stats {
        flex-wrap: wrap;
    }
    
    .stat-item {
        flex: 1 1 50%;
        margin-bottom: 0.5rem;
    }
}
</style>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2 mb-1">Manage Quizzes</h1>
            <p class="text-muted mb-0">Create and manage quizzes for your courses</p>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="add_quiz" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create New Quiz
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(0,154,218,0.1);">
                    <i class="bi bi-patch-question text-primary"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $stats['total_quizzes']; ?></h3>
                    <p>Total Quizzes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(40,167,69,0.1);">
                    <i class="bi bi-question-circle text-success"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $stats['total_questions']; ?></h3>
                    <p>Total Questions</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(255,193,7,0.1);">
                    <i class="bi bi-people text-warning"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $stats['total_attempts']; ?></h3>
                    <p>Total Attempts</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(23,162,184,0.1);">
                    <i class="bi bi-trophy text-info"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo $stats['avg_score']; ?>%</h3>
                    <p>Average Score</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" class="row g-3">
            <div class="col-md-5">
                <label for="search" class="form-label fw-bold">Search Quizzes</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" id="search" name="search" 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Search by quiz or course title...">
                </div>
            </div>
            <div class="col-md-4">
                <label for="course_id" class="form-label fw-bold">Filter by Course</label>
                <select class="form-select" id="course_id" name="course_id">
                    <option value="">All Courses</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['id']; ?>" <?php echo $course_id == $course['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 me-2">
                    <i class="bi bi-funnel"></i> Apply Filters
                </button>
                <a href="manage_quizzes" class="btn btn-outline-secondary" title="Reset filters">
                    <i class="bi bi-arrow-repeat"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Active Filters Display -->
    <?php if (!empty($search) || $course_id > 0): ?>
    <div class="alert alert-info d-flex align-items-center mb-4 py-2">
        <i class="bi bi-funnel-fill me-2"></i>
        <strong class="me-2">Active Filters:</strong>
        <?php if (!empty($search)): ?>
            <span class="badge bg-primary me-2">Search: "<?php echo htmlspecialchars($search); ?>"</span>
        <?php endif; ?>
        <?php if ($course_id > 0): 
            $course_name = '';
            foreach ($courses as $course) {
                if ($course['id'] == $course_id) {
                    $course_name = $course['title'];
                    break;
                }
            }
        ?>
            <span class="badge bg-success me-2">Course: <?php echo htmlspecialchars($course_name); ?></span>
        <?php endif; ?>
        <a href="manage_quizzes" class="ms-auto text-decoration-none">
            <i class="bi bi-x-circle"></i> Clear all
        </a>
    </div>
    <?php endif; ?>

    <!-- Quizzes Grid/Table View Toggle -->
    <div class="d-flex justify-content-end mb-3">
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-primary active" id="gridViewBtn" onclick="toggleView('grid')">
                <i class="bi bi-grid-3x3-gap-fill"></i> Grid
            </button>
            <button type="button" class="btn btn-outline-primary" id="tableViewBtn" onclick="toggleView('table')">
                <i class="bi bi-table"></i> Table
            </button>
        </div>
    </div>

    <!-- Grid View -->
    <div id="gridView">
        <?php if (empty($quizzes)): ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-patch-question display-1 text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">No Quizzes Found</h4>
                <p class="text-secondary mb-4">Get started by creating your first quiz.</p>
                <a href="add_quiz" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle"></i> Create New Quiz
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($quizzes as $quiz): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="quiz-card">
                            <div class="quiz-header">
                                <h3><?php echo htmlspecialchars($quiz['title']); ?></h3>
                                <span class="course-badge">
                                    <i class="bi bi-book"></i> 
                                    <?php echo htmlspecialchars(substr($quiz['course_title'], 0, 30)) . (strlen($quiz['course_title']) > 30 ? '...' : ''); ?>
                                </span>
                            </div>
                            <div class="quiz-body">
                                <!-- Quick Stats -->
                                <div class="quiz-stats">
                                    <div class="stat-item">
                                        <span class="stat-value"><?php echo $quiz['question_count']; ?></span>
                                        <span class="stat-label">Questions</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value"><?php echo $quiz['attempt_count']; ?></span>
                                        <span class="stat-label">Attempts</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value"><?php echo $quiz['passing_score']; ?>%</span>
                                        <span class="stat-label">Passing</span>
                                    </div>
                                </div>
                                
                                <!-- Quiz Details -->
                                <div class="quiz-meta">
                                    <div class="meta-item">
                                        <i class="bi bi-clock"></i>
                                        <span>Time Limit: <?php echo $quiz['time_limit'] ? $quiz['time_limit'] . ' minutes' : 'No limit'; ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="bi bi-arrow-repeat"></i>
                                        <span>Attempts Allowed: <?php echo $quiz['attempts_allowed'] ?: 'Unlimited'; ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="bi bi-trophy"></i>
                                        <span>Avg. Score: <?php echo round($quiz['avg_score'] ?? 0); ?>%</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="bi bi-calendar"></i>
                                        <span>Created: <?php echo date('M d, Y', strtotime($quiz['created_at'])); ?></span>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="quiz-actions">
                                    <a href="edit_quiz?id=<?php echo $quiz['id']; ?>" 
                                       class="btn btn-outline-primary" title="Edit Quiz">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="manage_quiz_questions?quiz_id=<?php echo $quiz['id']; ?>" 
                                       class="btn btn-outline-success" title="Manage Questions">
                                        <i class="bi bi-list-check"></i> Questions
                                    </a>
                                    <button type="button" 
                                            class="btn btn-outline-danger" 
                                            onclick="deleteQuiz(<?php echo $quiz['id']; ?>, '<?php echo addslashes($quiz['title']); ?>')"
                                            title="Delete Quiz">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- View Results Link -->
                                <div class="text-center mt-2">
                                    <a href="quiz_results?quiz_id=<?php echo $quiz['id']; ?>" class="small text-decoration-none">
                                        <i class="bi bi-bar-chart"></i> View Results
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Table View (Hidden by default) -->
    <div id="tableView" style="display: none;">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover datatable" id="quizzesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Quiz Title</th>
                                <th>Course</th>
                                <th>Questions</th>
                                <th>Attempts</th>
                                <th>Passing Score</th>
                                <th>Time Limit</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($quizzes as $quiz): ?>
                                <tr>
                                    <td>#<?php echo $quiz['id']; ?></td>
                                    <td>
                                        <div class="table-quiz-title">
                                            <?php echo htmlspecialchars($quiz['title']); ?>
                                        </div>
                                        <small class="text-muted">Avg: <?php echo round($quiz['avg_score'] ?? 0); ?>%</small>
                                    </td>
                                    <td>
                                        <div class="table-course-title">
                                            <?php echo htmlspecialchars($quiz['course_title']); ?>
                                        </div>
                                    </td>
                                    <td class="text-center"><?php echo $quiz['question_count']; ?></td>
                                    <td class="text-center"><?php echo $quiz['attempt_count']; ?></td>
                                    <td class="text-center"><?php echo $quiz['passing_score']; ?>%</td>
                                    <td class="text-center"><?php echo $quiz['time_limit'] ? $quiz['time_limit'] . ' min' : '∞'; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($quiz['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="edit_quiz?id=<?php echo $quiz['id']; ?>" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="manage_quiz_questions?quiz_id=<?php echo $quiz['id']; ?>" 
                                               class="btn btn-outline-success" title="Manage Questions">
                                                <i class="bi bi-list-check"></i>
                                            </a>
                                            <a href="quiz_results?quiz_id=<?php echo $quiz['id']; ?>" 
                                               class="btn btn-outline-info" title="View Results">
                                                <i class="bi bi-bar-chart"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    onclick="deleteQuiz(<?php echo $quiz['id']; ?>, '<?php echo addslashes($quiz['title']); ?>')"
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination/Info -->
    <?php if (!empty($quizzes)): ?>
        <div class="text-center mt-4">
            <small class="text-muted">
                Showing <?php echo count($quizzes); ?> quiz<?php echo count($quizzes) > 1 ? 'zes' : ''; ?>
            </small>
        </div>
    <?php endif; ?>
</main>

<!-- Include necessary scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
// Toggle between grid and table view
function toggleView(view) {
    const gridView = document.getElementById('gridView');
    const tableView = document.getElementById('tableView');
    const gridBtn = document.getElementById('gridViewBtn');
    const tableBtn = document.getElementById('tableViewBtn');
    
    if (view === 'grid') {
        gridView.style.display = 'block';
        tableView.style.display = 'none';
        gridBtn.classList.add('active');
        tableBtn.classList.remove('active');
    } else {
        gridView.style.display = 'none';
        tableView.style.display = 'block';
        tableBtn.classList.add('active');
        gridBtn.classList.remove('active');
        
        // Initialize DataTable if not already initialized
        if (!$.fn.DataTable.isDataTable('#quizzesTable')) {
            $('#quizzesTable').DataTable({
                pageLength: 25,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: 8 }
                ]
            });
        }
    }
}

// Delete quiz function
function deleteQuiz(quizId, quizTitle) {
    Swal.fire({
        title: 'Delete Quiz?',
        html: `Are you sure you want to delete "<strong>${quizTitle}</strong>"?<br><br>
               This will also delete all questions and results associated with this quiz.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to delete script
            window.location.href = `delete_quiz?id=${quizId}&token=<?php echo $csrf_token; ?>`;
        }
    });
}

// Auto-submit on filter change
document.getElementById('course_id').addEventListener('change', function() {
    document.querySelector('.filter-section form').submit();
});

// Live search with debounce
let searchTimeout;
document.getElementById('search').addEventListener('keyup', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.querySelector('.filter-section form').submit();
    }, 500);
});

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Show grid view by default
document.addEventListener('DOMContentLoaded', function() {
    if (window.innerWidth < 768) {
        // On mobile, show grid view by default
        toggleView('grid');
    }
});
</script>


@endverbatim

@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim


<!-- Add SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* Additional styles that complement style.css */
.quiz-edit-header {
    background: linear-gradient(135deg, var(--kiboko-blue) 0%, var(--kiboko-green) 100%);
    color: white;
    padding: 2rem;
    border-radius: var(--radius-lg);
    margin-bottom: 2rem;
    box-shadow: var(--shadow-lg);
}

.quiz-edit-header h2 {
    color: white;
    margin-bottom: 0.5rem;
}

.quiz-edit-header p {
    color: rgba(255,255,255,0.9);
    margin-bottom: 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-box {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-md);
    text-align: center;
    transition: var(--transition-base);
}

.stat-box:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.stat-box .stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--kiboko-blue);
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-box .stat-label {
    color: var(--kiboko-gray);
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.stat-box .stat-desc {
    font-size: 0.8rem;
    color: var(--kiboko-gray-light);
}

.settings-section {
    background: white;
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-md);
}

.settings-section h5 {
    color: var(--kiboko-dark);
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--kiboko-border);
}

.settings-section h5 i {
    color: var(--kiboko-blue);
    margin-right: 0.5rem;
}

.form-label {
    font-weight: 500;
    color: var(--kiboko-dark);
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 2px solid var(--kiboko-border);
    border-radius: var(--radius-sm);
    padding: 0.6rem 1rem;
    transition: var(--transition-fast);
}

.form-control:focus, .form-select:focus {
    border-color: var(--kiboko-blue);
    box-shadow: 0 0 0 3px rgba(0,154,218,0.1);
}

.input-group-text {
    background: var(--kiboko-light);
    border: 2px solid var(--kiboko-border);
    border-right: none;
    color: var(--kiboko-gray);
}

.input-group .form-control {
    border-left: none;
}

.input-group .form-control:focus {
    border-left: none;
}

.btn-save {
    background: var(--grad-primary);
    border: none;
    color: white;
    padding: 0.75rem 2rem;
    border-radius: var(--radius-sm);
    font-weight: 500;
    transition: var(--transition-base);
}

.btn-save:hover {
    background: var(--grad-hover);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-preview {
    background: white;
    border: 2px solid var(--kiboko-blue);
    color: var(--kiboko-blue);
    padding: 0.75rem 2rem;
    border-radius: var(--radius-sm);
    font-weight: 500;
    transition: var(--transition-base);
}

.btn-preview:hover {
    background: var(--grad-primary);
    border-color: transparent;
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.quick-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}

.quick-action-btn {
    background: var(--kiboko-light);
    border: 1px solid var(--kiboko-border);
    color: var(--kiboko-dark);
    padding: 0.5rem 1rem;
    border-radius: var(--radius-sm);
    font-size: 0.9rem;
    transition: var(--transition-fast);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.quick-action-btn:hover {
    background: var(--grad-light);
    border-color: var(--kiboko-blue);
    color: var(--kiboko-blue);
    transform: translateY(-2px);
}

.quick-action-btn i {
    font-size: 1rem;
}

.status-badge {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    border-radius: 100px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

/* Toggle switch styling */
.form-switch .form-check-input {
    width: 3em;
    height: 1.5em;
    margin-right: 0.5rem;
    cursor: pointer;
}

.form-switch .form-check-input:checked {
    background-color: var(--kiboko-blue);
    border-color: var(--kiboko-blue);
}

.form-switch .form-check-label {
    cursor: pointer;
    color: var(--kiboko-dark);
}

/* Responsive */
@media (max-width: 768px) {
    .quiz-edit-header {
        padding: 1.5rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    .stat-box {
        padding: 1rem;
    }
    
    .stat-box .stat-value {
        font-size: 1.5rem;
    }
    
    .quick-actions {
        justify-content: center;
    }
}
</style>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Header -->
    <div class="quiz-edit-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-2"><?php echo htmlspecialchars($quiz['title']); ?></h2>
                <p class="mb-0">
                    <i class="bi bi-book me-2"></i>Course: <?php echo htmlspecialchars($quiz['course_title']); ?>
                </p>
            </div>
            <div>
                <a href="manage_quizzes" class="btn btn-light me-2">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <a href="manage_quiz_questions?quiz_id=<?php echo $quiz_id; ?>" class="btn btn-light">
                    <i class="bi bi-list-check"></i> Manage Questions
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="manage_quiz_questions?quiz_id=<?php echo $quiz_id; ?>" class="quick-action-btn">
            <i class="bi bi-plus-circle"></i> Add Questions
        </a>
        <button class="quick-action-btn" onclick="previewQuiz()">
            <i class="bi bi-eye"></i> Preview Quiz
        </button>
        <button class="quick-action-btn" onclick="duplicateQuiz()">
            <i class="bi bi-files"></i> Duplicate
        </button>
        <a href="quiz_results?quiz_id=<?php echo $quiz_id; ?>" class="quick-action-btn">
            <i class="bi bi-bar-chart"></i> View Results
        </a>
        <button class="quick-action-btn text-danger" onclick="deleteQuiz()">
            <i class="bi bi-trash"></i> Delete Quiz
        </button>
    </div>

    <!-- Statistics Grid -->
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-value"><?php echo $question_stats['total_questions'] ?? 0; ?></div>
            <div class="stat-label">Total Questions</div>
            <div class="stat-desc"><?php echo $question_stats['total_points'] ?? 0; ?> total points</div>
        </div>
        <div class="stat-box">
            <div class="stat-value"><?php echo $attempt_stats['total_attempts'] ?? 0; ?></div>
            <div class="stat-label">Total Attempts</div>
            <div class="stat-desc"><?php echo $attempt_stats['unique_users'] ?? 0; ?> unique students</div>
        </div>
        <div class="stat-box">
            <div class="stat-value"><?php echo round($attempt_stats['avg_score'] ?? 0); ?>%</div>
            <div class="stat-label">Average Score</div>
            <div class="stat-desc">Highest: <?php echo round($attempt_stats['highest_score'] ?? 0); ?>%</div>
        </div>
        <div class="stat-box">
            <div class="stat-value"><?php echo $attempt_stats['passed_count'] ?? 0; ?></div>
            <div class="stat-label">Passed</div>
            <div class="stat-desc"><?php echo $attempt_stats['total_attempts'] > 0 ? round(($attempt_stats['passed_count'] / $attempt_stats['total_attempts']) * 100) : 0; ?>% pass rate</div>
        </div>
    </div>

    <!-- Edit Form -->
    <form method="POST" id="quizForm">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <!-- Basic Settings -->
        <div class="settings-section">
            <h5><i class="bi bi-gear"></i> Basic Settings</h5>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="title" class="form-label">Quiz Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo htmlspecialchars($quiz['title']); ?>" required>
                        <small class="text-muted">A descriptive title for the quiz</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                        <select class="form-select" id="course_id" name="course_id" required>
                            <option value="">Select Course</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['id']; ?>" 
                                    <?php echo $course['id'] == $quiz['course_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($quiz['description'] ?? ''); ?></textarea>
                <small class="text-muted">Brief description or instructions for the quiz</small>
            </div>
        </div>
        
        <!-- Scoring Settings -->
        <div class="settings-section">
            <h5><i class="bi bi-trophy"></i> Scoring Settings</h5>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="passing_score" class="form-label">Passing Score (%) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="passing_score" name="passing_score" 
                                   value="<?php echo $quiz['passing_score']; ?>" min="0" max="100" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted">Minimum percentage required to pass</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="attempts_allowed" class="form-label">Attempts Allowed</label>
                        <input type="number" class="form-control" id="attempts_allowed" name="attempts_allowed" 
                               value="<?php echo $quiz['attempts_allowed']; ?>" min="0">
                        <small class="text-muted">0 = unlimited attempts</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                        <input type="number" class="form-control" id="time_limit" name="time_limit" 
                               value="<?php echo $quiz['time_limit']; ?>" min="0">
                        <small class="text-muted">Leave empty for no time limit</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Advanced Settings - Only show if columns exist -->
        <?php if ($has_shuffle || $has_show_answers || $has_status): ?>
        <div class="settings-section">
            <h5><i class="bi bi-sliders"></i> Advanced Settings</h5>
            
            <div class="row">
                <?php if ($has_shuffle): ?>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="shuffle_questions" name="shuffle_questions"
                                   <?php echo ($quiz['shuffle_questions'] ?? 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="shuffle_questions">
                                <strong>Shuffle Questions</strong>
                                <br>
                                <small class="text-muted">Display questions in random order for each attempt</small>
                            </label>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($has_show_answers): ?>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="show_answers" name="show_answers"
                                   <?php echo ($quiz['show_answers'] ?? 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="show_answers">
                                <strong>Show Correct Answers</strong>
                                <br>
                                <small class="text-muted">Display correct answers after quiz completion</small>
                            </label>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if ($has_status): ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Quiz Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?php echo ($quiz['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($quiz['status'] ?? 'active') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                        <small class="text-muted">Inactive quizzes are not accessible to students</small>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Danger Zone -->
        <div class="settings-section border-danger">
            <h5 class="text-danger"><i class="bi bi-exclamation-triangle"></i> Danger Zone</h5>
            
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2"><strong>Reset Quiz Statistics</strong></p>
                    <p class="text-muted small mb-3">Clear all attempts and results for this quiz</p>
                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="resetStats()">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset Statistics
                    </button>
                </div>
                <div class="col-md-6">
                    <p class="mb-2"><strong>Delete Quiz</strong></p>
                    <p class="text-muted small mb-3">Permanently delete this quiz and all its data</p>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteQuiz()">
                        <i class="bi bi-trash"></i> Delete Quiz
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="manage_quizzes" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
            <button type="button" class="btn btn-preview" onclick="previewQuiz()">
                <i class="bi bi-eye"></i> Preview
            </button>
            <button type="submit" class="btn btn-save">
                <i class="bi bi-save"></i> Save Changes
            </button>
        </div>
    </form>
</main>

<script>
// Preview quiz function
function previewQuiz() {
    window.open('preview_quiz?id=<?php echo $quiz_id; ?>', '_blank');
}

// Delete quiz function
function deleteQuiz() {
    Swal.fire({
        title: 'Delete Quiz?',
        html: `Are you sure you want to delete "<strong><?php echo addslashes($quiz['title']); ?></strong>"?<br><br>
               This will permanently delete:<br>
               - <?php echo $question_stats['total_questions'] ?? 0; ?> questions<br>
               - <?php echo $attempt_stats['total_attempts'] ?? 0; ?> attempts<br>
               - All associated results`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `delete_quiz?id=<?php echo $quiz_id; ?>&token=<?php echo $csrf_token; ?>`;
        }
    });
}

// Reset statistics function
function resetStats() {
    Swal.fire({
        title: 'Reset Statistics?',
        text: 'This will delete all attempts and results for this quiz. This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, reset',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('reset_quiz_stats', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    quiz_id: <?php echo $quiz_id; ?>,
                    csrf_token: '<?php echo $csrf_token; ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Reset Complete',
                        text: 'Quiz statistics have been reset',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }
    });
}

// Duplicate quiz function
function duplicateQuiz() {
    Swal.fire({
        title: 'Duplicate Quiz?',
        text: 'This will create a copy of this quiz including all questions.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#009ada',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, duplicate',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `duplicate_quiz?id=<?php echo $quiz_id; ?>&token=<?php echo $csrf_token; ?>`;
        }
    });
}

// Warn before leaving if form is dirty
let formChanged = false;
document.querySelectorAll('#quizForm input, #quizForm textarea, #quizForm select').forEach(element => {
    element.addEventListener('change', () => {
        formChanged = true;
    });
    element.addEventListener('input', () => {
        formChanged = true;
    });
});

window.addEventListener('beforeunload', (e) => {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
    }
});

document.getElementById('quizForm').addEventListener('submit', () => {
    formChanged = false;
});

// Validation
document.getElementById('quizForm').addEventListener('submit', (e) => {
    const passingScore = document.getElementById('passing_score').value;
    if (passingScore < 0 || passingScore > 100) {
        e.preventDefault();
        Swal.fire('Error', 'Passing score must be between 0 and 100', 'error');
    }
});
</script>


@endverbatim

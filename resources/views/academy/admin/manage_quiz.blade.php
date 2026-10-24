@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim

                <!-- ... sidebar content ... -->
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <nav class="navbar navbar-expand navbar-light bg-white mb-4 rounded-bottom shadow-sm">
                    <div class="container-fluid">
                        <div>
                            <h5 class="mb-0">Manage Quiz: <?php echo $course['title']; ?></h5>
                            <small class="text-secondary"><?php echo count($questions); ?> questions</small>
                        </div>
                        <a href="manage_courses" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Courses
                        </a>
                    </div>
                </nav>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Quiz Settings -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Quiz Settings</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <input type="hidden" name="action" value="update_settings">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="quiz_title" class="form-label">Quiz Title</label>
                                        <input type="text" class="form-control" id="quiz_title" name="title" 
                                               value="<?php echo $quiz['title']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="passing_score" class="form-label">Passing Score (%)</label>
                                        <input type="number" class="form-control" id="passing_score" name="passing_score" 
                                               value="<?php echo $quiz['passing_score']; ?>" min="0" max="100" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="time_limit" class="form-label">Time Limit (minutes, optional)</label>
                                        <input type="number" class="form-control" id="time_limit" name="time_limit" 
                                               value="<?php echo $quiz['time_limit']; ?>" min="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="attempts_allowed" class="form-label">Attempts Allowed</label>
                                        <input type="number" class="form-control" id="attempts_allowed" name="attempts_allowed" 
                                               value="<?php echo $quiz['attempts_allowed']; ?>" min="1" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="quiz_description" class="form-label">Description</label>
                                        <textarea class="form-control" id="quiz_description" name="description" rows="3"><?php echo $quiz['description']; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Settings
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Add Question Form -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Add New Question</h6>
                        <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#addQuestionForm">
                            <i class="bi bi-plus-circle"></i> Show/Hide
                        </button>
                    </div>
                    <div class="collapse" id="addQuestionForm">
                        <div class="card-body">
                            <form method="POST" action="">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                <input type="hidden" name="action" value="add_question">
                                
                                <div class="mb-3">
                                    <label for="question" class="form-label">Question</label>
                                    <textarea class="form-control" id="question" name="question" rows="2" required></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="option_a" class="form-label">Option A</label>
                                            <div class="input-group">
                                                <span class="input-group-text">A</span>
                                                <input type="text" class="form-control" id="option_a" name="option_a" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="option_b" class="form-label">Option B</label>
                                            <div class="input-group">
                                                <span class="input-group-text">B</span>
                                                <input type="text" class="form-control" id="option_b" name="option_b" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="option_c" class="form-label">Option C</label>
                                            <div class="input-group">
                                                <span class="input-group-text">C</span>
                                                <input type="text" class="form-control" id="option_c" name="option_c" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="option_d" class="form-label">Option D</label>
                                            <div class="input-group">
                                                <span class="input-group-text">D</span>
                                                <input type="text" class="form-control" id="option_d" name="option_d" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="correct_answer" class="form-label">Correct Answer</label>
                                            <select class="form-select" id="correct_answer" name="correct_answer" required>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="C">C</option>
                                                <option value="D">D</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="points" class="form-label">Points</label>
                                            <input type="number" class="form-control" id="points" name="points" value="1" min="1" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-plus-circle"></i> Add Question
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Questions List -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Questions (<?php echo count($questions); ?>)</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($questions)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-patch-question display-4 text-muted"></i>
                                <p class="mt-2">No questions added yet. Add your first question above.</p>
                            </div>
                        <?php else: ?>
                            <div class="questions-list" id="questionsList">
                                <?php foreach ($questions as $index => $q): ?>
                                <div class="question-item card mb-3" data-id="<?php echo $q['id']; ?>">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="bi bi-grip-vertical drag-handle me-2 text-secondary" style="cursor: move;"></i>
                                                    <span class="badge bg-primary me-2">Q<?php echo $index + 1; ?></span>
                                                    <h6 class="mb-0"><?php echo $q['question']; ?></h6>
                                                </div>
                                                
                                                <div class="row mt-2">
                                                    <div class="col-md-6">
                                                        <div class="mb-1">
                                                            <span class="badge <?php echo $q['correct_answer'] == 'A' ? 'bg-success' : 'bg-light text-dark'; ?> me-1">A</span>
                                                            <?php echo $q['option_a']; ?>
                                                        </div>
                                                        <div class="mb-1">
                                                            <span class="badge <?php echo $q['correct_answer'] == 'B' ? 'bg-success' : 'bg-light text-dark'; ?> me-1">B</span>
                                                            <?php echo $q['option_b']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-1">
                                                            <span class="badge <?php echo $q['correct_answer'] == 'C' ? 'bg-success' : 'bg-light text-dark'; ?> me-1">C</span>
                                                            <?php echo $q['option_c']; ?>
                                                        </div>
                                                        <div class="mb-1">
                                                            <span class="badge <?php echo $q['correct_answer'] == 'D' ? 'bg-success' : 'bg-light text-dark'; ?> me-1">D</span>
                                                            <?php echo $q['option_d']; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-2">
                                                    <small class="text-secondary">
                                                        <i class="bi bi-star-fill text-warning"></i> Points: <?php echo $q['points']; ?>
                                                    </small>
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <form method="POST" action="" class="d-inline" onsubmit="return confirm('Delete this question?')">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                    <input type="hidden" name="action" value="delete_question">
                                                    <input type="hidden" name="question_id" value="<?php echo $q['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        // Enable drag & drop reordering of questions
        const questionsList = document.getElementById('questionsList');
        if (questionsList) {
            new Sortable(questionsList, {
                animation: 150,
                handle: '.drag-handle',
                onEnd: function(evt) {
                    const items = [];
                    document.querySelectorAll('.question-item').forEach(item => {
                        items.push(item.dataset.id);
                    });
                    
                    fetch('reorder_questions', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            items: items,
                            quiz_id: <?php echo $quiz_id; ?>,
                            csrf_token: '<?php echo $csrf_token; ?>'
                        })
                    });
                }
            });
        }
    </script>
</body>
</html>
@endverbatim

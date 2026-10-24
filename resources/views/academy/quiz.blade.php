@include('academy.partials.header')
@verbatim


<!-- Add required CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.quiz-container {
    max-width: 900px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.quiz-header {
    background: linear-gradient(135deg, var(--kiboko-blue) 0%, var(--kiboko-green) 100%);
    color: white;
    padding: 2rem;
    border-radius: var(--radius-lg);
    margin-bottom: 2rem;
    box-shadow: var(--shadow-lg);
}

.quiz-info {
    background: white;
    border-radius: var(--radius-md);
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-md);
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.info-icon {
    width: 40px;
    height: 40px;
    background: var(--kiboko-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--kiboko-blue);
    font-size: 1.2rem;
}

.info-text {
    font-size: 0.95rem;
}

.info-text strong {
    display: block;
    color: var(--kiboko-dark);
    font-size: 1.1rem;
}

.info-text small {
    color: var(--kiboko-gray);
}

.timer-warning {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    color: #856404;
    padding: 1rem;
    border-radius: var(--radius-sm);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.timer-warning i {
    font-size: 1.5rem;
}

.question-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-md);
    border: 2px solid transparent;
    transition: var(--transition-base);
}

.question-card:hover {
    border-color: var(--kiboko-blue);
    box-shadow: var(--shadow-lg);
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--kiboko-border);
}

.question-number {
    font-weight: 600;
    color: var(--kiboko-blue);
}

.question-points {
    background: var(--kiboko-light);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    color: var(--kiboko-gray);
}

.question-text {
    font-size: 1.1rem;
    font-weight: 500;
    margin-bottom: 1.5rem;
    color: var(--kiboko-dark);
}

.options-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.option-item {
    position: relative;
    padding: 1rem 1rem 1rem 3rem;
    background: var(--kiboko-light);
    border: 2px solid transparent;
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: var(--transition-fast);
}

.option-item:hover {
    background: white;
    border-color: var(--kiboko-blue);
    transform: translateX(5px);
    box-shadow: var(--shadow-sm);
}

.option-item.selected {
    background: linear-gradient(135deg, rgba(0,154,218,0.1), rgba(118,188,33,0.1));
    border-color: var(--kiboko-blue);
}

.option-letter {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    width: 24px;
    height: 24px;
    background: white;
    border: 2px solid var(--kiboko-blue);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.85rem;
    color: var(--kiboko-blue);
}

.option-item.selected .option-letter {
    background: var(--kiboko-blue);
    color: white;
}

.option-text {
    margin-left: 0.5rem;
}

.quiz-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 2px solid var(--kiboko-border);
}

.btn-prev,
.btn-next {
    min-width: 120px;
}

.btn-submit {
    min-width: 200px;
    padding: 0.75rem;
    font-weight: 600;
}

.question-navigation {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: white;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
}

.question-dot {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid var(--kiboko-border);
    background: white;
    color: var(--kiboko-gray);
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition-fast);
    display: flex;
    align-items: center;
    justify-content: center;
}

.question-dot:hover {
    border-color: var(--kiboko-blue);
    color: var(--kiboko-blue);
    transform: scale(1.1);
}

.question-dot.active {
    background: var(--kiboko-blue);
    border-color: var(--kiboko-blue);
    color: white;
}

.question-dot.answered {
    background: var(--kiboko-green);
    border-color: var(--kiboko-green);
    color: white;
}

.progress-bar-container {
    background: white;
    border-radius: var(--radius-md);
    padding: 1rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-sm);
}

.progress-text {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: var(--kiboko-gray);
}

.progress {
    height: 8px;
    border-radius: 4px;
    background: var(--kiboko-border);
}

.progress-bar {
    background: linear-gradient(90deg, var(--kiboko-blue), var(--kiboko-green));
    border-radius: 4px;
    transition: width 0.3s ease;
}

.attempts-info {
    background: var(--kiboko-light);
    border-radius: var(--radius-md);
    padding: 1rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.attempts-badge {
    background: var(--kiboko-blue);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
}

/* Disabled state */
.quiz-disabled {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
}

.quiz-disabled i {
    font-size: 4rem;
    color: var(--kiboko-gray-light);
    margin-bottom: 1rem;
}

.quiz-disabled h3 {
    color: var(--kiboko-dark);
    margin-bottom: 1rem;
}

.quiz-disabled p {
    color: var(--kiboko-gray);
    margin-bottom: 2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .quiz-header {
        padding: 1.5rem;
    }
    
    .quiz-info {
        flex-direction: column;
        gap: 1rem;
    }
    
    .question-navigation {
        justify-content: center;
    }
    
    .quiz-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-prev,
    .btn-next,
    .btn-submit {
        width: 100%;
    }
}
</style>

<div class="container quiz-container">
    <?php if (!$can_take_quiz): ?>
        <!-- Quiz Disabled Message -->
        <div class="quiz-disabled">
            <i class="bi bi-exclamation-triangle-fill text-warning"></i>
            <h3>Quiz Not Available</h3>
            <p><?php echo $message; ?></p>
            <div class="d-flex gap-2 justify-content-center">
                <a href="course-player?id=<?php echo $course_id; ?>" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Back to Course
                </a>
                <?php if (!empty($previous_attempts)): ?>
                    <a href="quiz-results?course_id=<?php echo $course_id; ?>" class="btn btn-outline-info">
                        <i class="bi bi-bar-chart"></i> View Previous Results
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Quiz Header -->
        <div class="quiz-header">
            <h2 class="mb-2"><?php echo htmlspecialchars($quiz['title']); ?></h2>
            <p class="mb-0 opacity-75">
                <i class="bi bi-book me-2"></i>Course: <?php echo htmlspecialchars($quiz['course_title']); ?>
            </p>
        </div>

        <!-- Quiz Info -->
        <div class="quiz-info">
            <div class="info-item">
                <div class="info-icon">
                    <i class="bi bi-question-circle"></i>
                </div>
                <div class="info-text">
                    <strong><?php echo count($questions); ?> Questions</strong>
                    <small>Total questions in this quiz</small>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i class="bi bi-trophy"></i>
                </div>
                <div class="info-text">
                    <strong><?php echo $quiz['passing_score']; ?>% Required</strong>
                    <small>Minimum score to pass</small>
                </div>
            </div>
            <?php if ($quiz['time_limit']): ?>
            <div class="info-item">
                <div class="info-icon">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="info-text">
                    <strong id="timer-display"><?php echo $quiz['time_limit']; ?>:00</strong>
                    <small>Time remaining</small>
                </div>
            </div>
            <?php endif; ?>
            <div class="info-item">
                <div class="info-icon">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div class="info-text">
                    <strong>Attempt <?php echo $attempts_count + 1; ?> 
                        <?php if ($quiz['attempts_allowed'] > 0): ?>
                            / <?php echo $quiz['attempts_allowed']; ?>
                        <?php endif; ?>
                    </strong>
                    <small>Current attempt</small>
                </div>
            </div>
        </div>

        <?php if (!empty($previous_attempts)): ?>
        <!-- Previous Attempts Info -->
        <div class="attempts-info">
            <i class="bi bi-info-circle-fill text-info fs-4"></i>
            <div class="flex-grow-1">
                <strong>Previous Attempts:</strong>
                <?php foreach (array_slice($previous_attempts, 0, 3) as $attempt): ?>
                    <span class="attempts-badge ms-2">
                        <?php echo round($attempt['percentage']); ?>% 
                        (<?php echo $attempt['passed'] ? 'Passed' : 'Failed'; ?>)
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($quiz['time_limit']): ?>
        <!-- Timer Warning -->
        <div class="timer-warning" id="timer-warning" style="display: none;">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                <strong>Time is running out!</strong>
                <span id="warning-message" class="ms-2">You have less than 5 minutes remaining.</span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Progress Bar -->
        <div class="progress-bar-container">
            <div class="progress-text">
                <span>Progress</span>
                <span id="progress-percent">0%</span>
            </div>
            <div class="progress">
                <div class="progress-bar" id="progress-bar" style="width: 0%"></div>
            </div>
        </div>

        <!-- Question Navigation Dots -->
        <div class="question-navigation" id="question-nav">
            <?php foreach ($questions as $index => $question): ?>
                <button type="button" class="question-dot" data-index="<?php echo $index; ?>" 
                        onclick="goToQuestion(<?php echo $index; ?>)">
                    <?php echo $index + 1; ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Quiz Form -->
        <form method="POST" id="quizForm">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="submit_quiz" value="1">
            
            <!-- Questions Container -->
            <div id="questions-container">
                <?php foreach ($questions as $index => $question): ?>
                    <div class="question-card" id="question-<?php echo $index; ?>" 
                         style="<?php echo $index > 0 ? 'display: none;' : ''; ?>">
                        <div class="question-header">
                            <span class="question-number">Question <?php echo $index + 1; ?></span>
                            <span class="question-points"><?php echo $question['points']; ?> point<?php echo $question['points'] > 1 ? 's' : ''; ?></span>
                        </div>
                        
                        <div class="question-text">
                            <?php echo nl2br(htmlspecialchars($question['question'])); ?>
                        </div>
                        
                        <div class="options-list">
                            <?php
                            $options = [
                                'A' => $question['option_a'],
                                'B' => $question['option_b'],
                                'C' => $question['option_c'],
                                'D' => $question['option_d']
                            ];
                            
                            foreach ($options as $letter => $text):
                                if (empty($text)) continue;
                            ?>
                                <label class="option-item" onclick="selectOption(<?php echo $index; ?>, '<?php echo $letter; ?>')">
                                    <input type="radio" name="answers[<?php echo $question['id']; ?>]" 
                                           value="<?php echo $letter; ?>" style="display: none;" 
                                           onchange="markAnswered(<?php echo $index; ?>)">
                                    <span class="option-letter"><?php echo $letter; ?></span>
                                    <span class="option-text"><?php echo htmlspecialchars($text); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Navigation Buttons -->
            <div class="quiz-actions">
                <button type="button" class="btn btn-outline-primary btn-prev" id="prevBtn" onclick="prevQuestion()" style="display: none;">
                    <i class="bi bi-arrow-left"></i> Previous
                </button>
                <button type="button" class="btn btn-primary btn-next" id="nextBtn" onclick="nextQuestion()">
                    Next <i class="bi bi-arrow-right"></i>
                </button>
                <button type="submit" class="btn btn-success btn-submit" id="submitBtn" style="display: none;" onclick="return confirmSubmit()">
                    <i class="bi bi-check-circle"></i> Submit Quiz
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
let currentQuestion = 0;
const totalQuestions = <?php echo count($questions); ?>;
let answers = {};
let timerInterval;
let timeLeft = <?php echo $quiz['time_limit'] ? $quiz['time_limit'] * 60 : 0; ?>;

// Initialize the quiz
document.addEventListener('DOMContentLoaded', function() {
    updateNavigation();
    loadSavedAnswers();
    
    <?php if ($quiz['time_limit']): ?>
    startTimer();
    <?php endif; ?>
});

// Timer function
<?php if ($quiz['time_limit']): ?>
function startTimer() {
    const timerDisplay = document.getElementById('timer-display');
    const timerWarning = document.getElementById('timer-warning');
    
    timerInterval = setInterval(function() {
        timeLeft--;
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            document.getElementById('quizForm').submit();
            return;
        }
        
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        // Show warning when less than 5 minutes left
        if (timeLeft <= 300) {
            timerWarning.style.display = 'flex';
            if (timeLeft <= 60) {
                document.getElementById('warning-message').textContent = 'Less than 1 minute remaining!';
            }
        }
        
        // Change color when less than 2 minutes left
        if (timeLeft <= 120) {
            timerDisplay.style.color = '#dc3545';
        }
    }, 1000);
}
<?php endif; ?>

// Select option
function selectOption(questionIndex, letter) {
    const radio = document.querySelector(`#question-${questionIndex} input[value="${letter}"]`);
    if (radio) {
        radio.checked = true;
        markAnswered(questionIndex);
        
        // Update visual
        document.querySelectorAll(`#question-${questionIndex} .option-item`).forEach(opt => {
            opt.classList.remove('selected');
        });
        event.currentTarget.classList.add('selected');
    }
}

// Mark question as answered
function markAnswered(questionIndex) {
    const dot = document.querySelector(`.question-dot[data-index="${questionIndex}"]`);
    if (dot) {
        dot.classList.add('answered');
    }
    
    answers[questionIndex] = true;
    updateProgress();
}

// Go to specific question
function goToQuestion(index) {
    document.querySelectorAll('.question-card').forEach(q => {
        q.style.display = 'none';
    });
    document.getElementById(`question-${index}`).style.display = 'block';
    
    // Update active dot
    document.querySelectorAll('.question-dot').forEach(dot => {
        dot.classList.remove('active');
    });
    document.querySelector(`.question-dot[data-index="${index}"]`).classList.add('active');
    
    currentQuestion = index;
    updateNavButtons();
}

// Next question
function nextQuestion() {
    if (currentQuestion < totalQuestions - 1) {
        goToQuestion(currentQuestion + 1);
    } else {
        // Show submit button instead
        document.getElementById('nextBtn').style.display = 'none';
        document.getElementById('submitBtn').style.display = 'block';
    }
}

// Previous question
function prevQuestion() {
    if (currentQuestion > 0) {
        goToQuestion(currentQuestion - 1);
    }
    
    if (currentQuestion < totalQuestions - 1) {
        document.getElementById('nextBtn').style.display = 'block';
        document.getElementById('submitBtn').style.display = 'none';
    }
}

// Update navigation buttons
function updateNavButtons() {
    document.getElementById('prevBtn').style.display = currentQuestion > 0 ? 'block' : 'none';
    
    if (currentQuestion === totalQuestions - 1) {
        document.getElementById('nextBtn').style.display = 'none';
        document.getElementById('submitBtn').style.display = 'block';
    } else {
        document.getElementById('nextBtn').style.display = 'block';
        document.getElementById('submitBtn').style.display = 'none';
    }
}

// Update progress bar
function updateProgress() {
    const answeredCount = Object.keys(answers).length;
    const progress = (answeredCount / totalQuestions) * 100;
    
    document.getElementById('progress-bar').style.width = progress + '%';
    document.getElementById('progress-percent').textContent = Math.round(progress) + '%';
}

// Load saved answers from localStorage (in case of page refresh)
function loadSavedAnswers() {
    // Check for any pre-selected answers (from browser's autocomplete)
    document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
        const questionCard = radio.closest('.question-card');
        if (questionCard) {
            const index = questionCard.id.split('-')[1];
            markAnswered(parseInt(index));
            
            // Highlight the option
            const optionItem = radio.closest('.option-item');
            if (optionItem) {
                optionItem.classList.add('selected');
            }
        }
    });
}

// Confirm before submitting
function confirmSubmit() {
    const answeredCount = Object.keys(answers).length;
    
    if (answeredCount < totalQuestions) {
        return Swal.fire({
            title: 'Not All Questions Answered',
            text: `You have answered ${answeredCount} out of ${totalQuestions} questions. Are you sure you want to submit?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#009ada',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, submit',
            cancelButtonText: 'Review Answers'
        }).then((result) => {
            if (result.isConfirmed) {
                return true;
            }
            return false;
        });
    }
    
    return Swal.fire({
        title: 'Submit Quiz?',
        text: 'Are you ready to submit your answers?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, submit',
        cancelButtonText: 'Review'
    }).then((result) => {
        return result.isConfirmed;
    });
}

// Prevent accidental navigation
window.addEventListener('beforeunload', function(e) {
    const answeredCount = Object.keys(answers).length;
    if (answeredCount > 0 && answeredCount < totalQuestions) {
        e.preventDefault();
        e.returnValue = 'You have unanswered questions. Are you sure you want to leave?';
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Left arrow for previous
    if (e.key === 'ArrowLeft' && !e.ctrlKey && !e.altKey && !e.shiftKey) {
        if (currentQuestion > 0) {
            prevQuestion();
        }
    }
    
    // Right arrow for next
    if (e.key === 'ArrowRight' && !e.ctrlKey && !e.altKey && !e.shiftKey) {
        if (currentQuestion < totalQuestions - 1) {
            nextQuestion();
        }
    }
    
    // Number keys 1-4 for options (on current question)
    if (e.key >= '1' && e.key <= '4') {
        const index = parseInt(e.key) - 1;
        const letters = ['A', 'B', 'C', 'D'];
        if (index < 4) {
            const option = document.querySelector(`#question-${currentQuestion} input[value="${letters[index]}"]`);
            if (option) {
                option.checked = true;
                markAnswered(currentQuestion);
                
                // Update visual
                document.querySelectorAll(`#question-${currentQuestion} .option-item`).forEach(opt => {
                    opt.classList.remove('selected');
                });
                option.closest('.option-item').classList.add('selected');
            }
        }
    }
});

// Clean up timer on page unload
window.addEventListener('unload', function() {
    if (timerInterval) {
        clearInterval(timerInterval);
    }
});
</script>


@endverbatim

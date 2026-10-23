@include('academy.partials.header')
@verbatim


<!-- Add required CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.results-container {
    max-width: 900px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.results-header {
    background: linear-gradient(135deg, var(--kiboko-blue) 0%, var(--kiboko-green) 100%);
    color: white;
    padding: 2rem;
    border-radius: var(--radius-lg);
    margin-bottom: 2rem;
    box-shadow: var(--shadow-lg);
}

.score-circle {
    width: 150px;
    height: 150px;
    margin: 0 auto 1.5rem;
    position: relative;
}

.score-circle svg {
    width: 150px;
    height: 150px;
    transform: rotate(-90deg);
}

.score-circle-bg {
    fill: none;
    stroke: #e9ecef;
    stroke-width: 8;
}

.score-circle-progress {
    fill: none;
    stroke: white;
    stroke-width: 8;
    stroke-linecap: round;
    stroke-dasharray: 440;
    stroke-dashoffset: <?php echo 440 - (440 * $result['percentage'] / 100); ?>;
    transition: stroke-dashoffset 1s ease;
}

.score-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.score-percentage {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1;
}

.score-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.result-badge {
    display: inline-block;
    padding: 0.5rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    margin-top: 1rem;
}

.result-pass {
    background: rgba(40, 167, 69, 0.2);
    color: #28a745;
    border: 2px solid #28a745;
}

.result-fail {
    background: rgba(220, 53, 69, 0.2);
    color: #dc3545;
    border: 2px solid #dc3545;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-md);
    text-align: center;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--kiboko-blue);
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: var(--kiboko-gray);
    font-size: 0.9rem;
}

.question-review {
    background: white;
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: var(--shadow-md);
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--kiboko-border);
}

.review-question {
    font-weight: 600;
    margin-bottom: 1rem;
}

.review-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.review-option {
    padding: 0.75rem;
    border-radius: var(--radius-sm);
    background: var(--kiboko-light);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.review-option.correct {
    background: #d4edda;
    border: 2px solid #28a745;
}

.review-option.incorrect {
    background: #f8d7da;
    border: 2px solid #dc3545;
}

.review-option.user-selected {
    border-width: 3px;
}

.option-letter-small {
    width: 24px;
    height: 24px;
    background: white;
    border: 2px solid var(--kiboko-blue);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.8rem;
    color: var(--kiboko-blue);
}

.review-option.correct .option-letter-small {
    background: #28a745;
    border-color: #28a745;
    color: white;
}

.review-option.incorrect .option-letter-small {
    background: #dc3545;
    border-color: #dc3545;
    color: white;
}

.explanation-box {
    background: var(--kiboko-light);
    border-left: 4px solid var(--kiboko-blue);
    padding: 1rem;
    border-radius: var(--radius-sm);
    margin-top: 1rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
}

@media print {
    .no-print {
        display: none;
    }
}
</style>

<div class="container results-container">
    <!-- Results Header -->
    <div class="results-header text-center">
        <h2 class="mb-3"><?php echo htmlspecialchars($result['quiz_title']); ?></h2>
        <p class="mb-4"><?php echo htmlspecialchars($result['course_title']); ?></p>
        
        <!-- Score Circle -->
        <div class="score-circle">
            <svg viewBox="0 0 150 150">
                <circle class="score-circle-bg" cx="75" cy="75" r="70"></circle>
                <circle class="score-circle-progress" cx="75" cy="75" r="70"></circle>
            </svg>
            <div class="score-text">
                <div class="score-percentage"><?php echo round($result['percentage']); ?>%</div>
                <div class="score-label">Score</div>
            </div>
        </div>
        
        <!-- Pass/Fail Badge -->
        <div class="result-badge <?php echo $result['passed'] ? 'result-pass' : 'result-fail'; ?>">
            <i class="bi bi-<?php echo $result['passed'] ? 'check-circle-fill' : 'x-circle-fill'; ?> me-2"></i>
            <?php echo $result['passed'] ? 'PASSED' : 'FAILED'; ?>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo $correct_count; ?>/<?php echo $total_questions; ?></div>
            <div class="stat-label">Correct Answers</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo round($result['percentage'], 1); ?>%</div>
            <div class="stat-label">Your Score</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $result['passing_score']; ?>%</div>
            <div class="stat-label">Passing Score</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $result['attempt_number']; ?></div>
            <div class="stat-label">Attempt #</div>
        </div>
    </div>

    <!-- Question Review -->
    <h4 class="mb-3">Question Review</h4>
    
    <?php foreach ($answers as $index => $answer): ?>
        <div class="question-review">
            <div class="review-header">
                <span class="fw-bold">Question <?php echo $index + 1; ?></span>
                <span class="badge bg-<?php echo $answer['is_correct'] ? 'success' : 'danger'; ?>">
                    <?php echo $answer['is_correct'] ? 'Correct' : 'Incorrect'; ?> 
                    (<?php echo $answer['points']; ?> pts)
                </span>
            </div>
            
            <div class="review-question">
                <?php echo nl2br(htmlspecialchars($answer['question'])); ?>
            </div>
            
            <div class="review-options">
                <?php
                $options = [
                    'A' => $answer['option_a'] ?? '',
                    'B' => $answer['option_b'] ?? '',
                    'C' => $answer['option_c'] ?? '',
                    'D' => $answer['option_d'] ?? ''
                ];
                
                foreach ($options as $letter => $text):
                    if (empty($text)) continue;
                    
                    $is_correct = ($letter == $answer['correct_answer']);
                    $is_user = ($letter == $answer['user_answer']);
                    $class = '';
                    
                    if ($is_correct && $is_user) {
                        $class = 'correct user-selected';
                    } elseif ($is_correct) {
                        $class = 'correct';
                    } elseif ($is_user && !$is_correct) {
                        $class = 'incorrect user-selected';
                    }
                ?>
                    <div class="review-option <?php echo $class; ?>">
                        <span class="option-letter-small"><?php echo $letter; ?></span>
                        <span class="flex-grow-1"><?php echo htmlspecialchars($text); ?></span>
                        <?php if ($is_correct): ?>
                            <i class="bi bi-check-circle-fill text-success"></i>
                        <?php elseif ($is_user && !$is_correct): ?>
                            <i class="bi bi-x-circle-fill text-danger"></i>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (!empty($answer['explanation']) && $result['show_answers']): ?>
                <div class="explanation-box">
                    <strong><i class="bi bi-info-circle me-2"></i>Explanation:</strong>
                    <p class="mb-0 mt-1"><?php echo nl2br(htmlspecialchars($answer['explanation'])); ?></p>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <!-- Action Buttons -->
    <div class="action-buttons no-print">
        <?php if (!$result['passed']): ?>
            <a href="quiz?course_id=<?php echo $result['course_id']; ?>" class="btn btn-warning">
                <i class="bi bi-arrow-repeat"></i> Try Again
            </a>
        <?php endif; ?>
        
        <a href="course-player?id=<?php echo $result['course_id']; ?>" class="btn btn-primary">
            <i class="bi bi-arrow-left"></i> Back to Course
        </a>
        
        <button class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer"></i> Print Results
        </button>
    </div>
</div>

<script>
// Animate the score circle on load
document.addEventListener('DOMContentLoaded', function() {
    const circle = document.querySelector('.score-circle-progress');
    if (circle) {
        setTimeout(() => {
            circle.style.strokeDashoffset = <?php echo 440 - (440 * $result['percentage'] / 100); ?>;
        }, 100);
    }
});

// Share results function
function shareResults() {
    const text = `I scored <?php echo round($result['percentage']); ?>% on the "<?php echo addslashes($result['quiz_title']); ?>" quiz!`;
    
    if (navigator.share) {
        navigator.share({
            title: 'Quiz Results',
            text: text,
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(text + ' ' + window.location.href);
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Results link copied to clipboard',
            timer: 1500,
            showConfirmButton: false
        });
    }
}
</script>


@endverbatim

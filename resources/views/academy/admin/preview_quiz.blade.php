@verbatim

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Quiz - <?php echo htmlspecialchars($quiz['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: var(--kiboko-light);
            padding: 2rem;
        }
        .preview-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .question-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-md);
        }
        .option-item {
            padding: 0.75rem;
            border: 2px solid var(--kiboko-border);
            border-radius: var(--radius-sm);
            margin-bottom: 0.5rem;
            cursor: default;
        }
        .option-item.correct {
            background: #d4edda;
            border-color: #28a745;
        }
        .badge-points {
            background: var(--kiboko-blue);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>
            <button class="btn btn-outline-secondary" onclick="window.close()">
                <i class="bi bi-x-lg"></i> Close Preview
            </button>
        </div>
        
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> This is a preview of how students will see the quiz.
        </div>
        
        <div class="quiz-info mb-4">
            <span class="badge bg-primary me-2"><?php echo count($questions); ?> Questions</span>
            <span class="badge bg-success me-2">Passing Score: <?php echo $quiz['passing_score']; ?>%</span>
            <?php if ($quiz['time_limit']): ?>
                <span class="badge bg-warning">Time Limit: <?php echo $quiz['time_limit']; ?> minutes</span>
            <?php endif; ?>
        </div>
        
        <?php foreach ($questions as $index => $q): ?>
            <div class="question-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5>Question <?php echo $index + 1; ?>: <?php echo htmlspecialchars($q['question']); ?></h5>
                    <span class="badge-points"><?php echo $q['points']; ?> pts</span>
                </div>
                
                <div class="options">
                    <?php
                    $options = [
                        'A' => $q['option_a'],
                        'B' => $q['option_b'],
                        'C' => $q['option_c'],
                        'D' => $q['option_d']
                    ];
                    foreach ($options as $letter => $text):
                        if (empty($text)) continue;
                    ?>
                        <div class="option-item <?php echo $q['correct_answer'] == $letter ? 'correct' : ''; ?>">
                            <strong><?php echo $letter; ?>.</strong> <?php echo htmlspecialchars($text); ?>
                            <?php if ($q['correct_answer'] == $letter): ?>
                                <i class="bi bi-check-circle-fill text-success ms-2"></i>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (!empty($q['explanation'])): ?>
                    <div class="mt-3 p-3 bg-light rounded">
                        <small><strong>Explanation:</strong> <?php echo htmlspecialchars($q['explanation']); ?></small>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
@endverbatim

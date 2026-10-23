@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim


<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Questions for: <?php echo htmlspecialchars($quiz['title']); ?></h2>
        <button class="btn btn-primary" onclick="showAddQuestionModal()">+ Add Question</button>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div id="questionsList">
        <?php foreach ($questions as $q): ?>
            <div class="card mb-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><strong>Q:</strong> <?php echo htmlspecialchars($q['question']); ?></div>
                    <div class="btn-group">
                        <button onclick="editQuestion(<?php echo $q['id']; ?>)" class="btn btn-sm btn-outline-primary">Edit</button>
                        <button onclick="duplicateQuestion(<?php echo $q['id']; ?>)" class="btn btn-sm btn-outline-info">Copy</button>
                        <a href="?quiz_id=<?php echo $quiz_id; ?>&delete=<?php echo $q['id']; ?>&token=<?php echo $csrf_token; ?>" 
                           class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this question?')">Del</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<div class="modal fade" id="questionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="questionForm" method="POST" action="manage_quiz_questions?quiz_id=<?php echo $quiz_id; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" id="formAction" value="add_question">
            <input type="hidden" name="question_id" id="questionId" value="">
            <div class="modal-content">
                <div class="modal-header"><h5 id="modalTitle">Add Question</h5></div>
                <div class="modal-body">
                    <textarea name="question" id="question" class="form-control mb-3" placeholder="Question Text" required></textarea>
                    <div class="row">
                        <div class="col-6 mb-2"><input type="text" name="option_a" id="option_a" class="form-control" placeholder="Option A" required></div>
                        <div class="col-6 mb-2"><input type="text" name="option_b" id="option_b" class="form-control" placeholder="Option B" required></div>
                        <div class="col-6 mb-2"><input type="text" name="option_c" id="option_c" class="form-control" placeholder="Option C"></div>
                        <div class="col-6 mb-2"><input type="text" name="option_d" id="option_d" class="form-control" placeholder="Option D"></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label>Correct Answer</label>
                            <select name="correct_answer" id="correct_answer" class="form-select">
                                <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label>Points</label>
                            <input type="number" name="points" id="points" class="form-control" value="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Question</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let qModal = new bootstrap.Modal(document.getElementById('questionModal'));

function showAddQuestionModal() {
    document.getElementById('questionForm').reset();
    document.getElementById('formAction').value = 'add_question';
    document.getElementById('modalTitle').innerText = 'Add New Question';
    qModal.show();
}

function editQuestion(id) {
    // Calling the SAME page with ajax_get_question parameter
    fetch(`manage_quiz_questions?quiz_id=<?php echo $quiz_id; ?>&ajax_get_question=${id}`)
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('formAction').value = 'edit_question';
                document.getElementById('questionId').value = id;
                document.getElementById('question').value = data.question.question;
                document.getElementById('option_a').value = data.question.option_a;
                document.getElementById('option_b').value = data.question.option_b;
                document.getElementById('option_c').value = data.question.option_c;
                document.getElementById('option_d').value = data.question.option_d;
                document.getElementById('correct_answer').value = data.question.correct_answer;
                document.getElementById('points').value = data.question.points;
                qModal.show();
            }
        });
}

function duplicateQuestion(id) {
    if(!confirm('Duplicate this question?')) return;
    fetch('manage_quiz_questions?quiz_id=<?php echo $quiz_id; ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            action: 'duplicate',
            question_id: id,
            csrf_token: '<?php echo $csrf_token; ?>'
        })
    }).then(() => location.reload());
}
</script>
@endverbatim

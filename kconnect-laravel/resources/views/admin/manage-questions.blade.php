<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SortableJS for drag and drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <style>
        :root { --kiboko-green: #76bc21; --kiboko-blue: #009ada; --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%); }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .sidebar { width: 280px; background: white; height: 100vh; position: fixed; left: 0; top: 0; box-shadow: 2px 0 20px rgba(0,0,0,0.05); overflow-y: auto; z-index: 100; }
        .sidebar-logo { padding: 25px 20px; text-align: center; border-bottom: 1px solid #eef2f6; }
        .sidebar-logo h2 { font-size: 1.5rem; font-weight: 800; background: var(--grad); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu a { display: flex; align-items: center; gap: 12px; padding: 12px 24px; color: #475569; text-decoration: none; transition: all 0.3s; margin: 4px 12px; border-radius: 12px; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: linear-gradient(135deg, rgba(0,154,218,0.1) 0%, rgba(118,188,33,0.1) 100%); color: var(--kiboko-blue); }
        .sidebar-menu a i { width: 24px; }
        .main-content { margin-left: 280px; padding: 24px 32px; }
        
        /* Drag and Drop Styles */
        .questions-list { list-style: none; padding: 0; }
        .question-item-drag {
            background: white;
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 12px;
            border: 1px solid #eef2f6;
            transition: all 0.3s;
            cursor: grab;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .question-item-drag:hover {
            box-shadow: 0 4px 15px rgba(0,154,218,0.1);
            border-color: var(--kiboko-blue);
        }
        .question-item-drag.sortable-ghost {
            opacity: 0.4;
            background: var(--grad-light);
            border: 2px dashed var(--kiboko-blue);
        }
        .question-item-drag.sortable-drag {
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            transform: rotate(1deg);
        }
        .drag-handle {
            color: #94a3b8;
            font-size: 20px;
            cursor: grab;
            padding: 8px;
        }
        .drag-handle:active { cursor: grabbing; }
        .question-content { flex: 1; }
        .question-text-display { 
            font-weight: 600; 
            margin-bottom: 8px; 
            font-size: 15px;
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .category-tag { display: inline-block; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 600; margin-right: 8px; }
        .type-badge { 
            display: inline-block; 
            padding: 3px 10px; 
            border-radius: 20px; 
            font-size: 11px; 
            font-weight: 500;
            background: #f1f5f9;
            color: #475569;
        }
        .required-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            background: #fee2e2;
            color: #dc2626;
        }
        .btn-gradient { background: var(--grad); color: white; border: none; padding: 10px 24px; border-radius: 40px; font-weight: 600; transition: all 0.3s; }
        .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,154,218,0.3); color: white; }
        .form-control, .form-select { border-radius: 12px; border: 2px solid #eef2f6; padding: 12px 16px; }
        textarea.form-control { resize: vertical; min-height: 100px; }
        .save-order-btn { margin-left: 12px; }
        .order-hint {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 13px;
            margin-bottom: 16px;
        }
        @media (max-width: 768px) { 
            .sidebar { transform: translateX(-100%); } 
            .main-content { margin-left: 0; padding: 16px; }
            .question-item-drag { flex-wrap: wrap; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
       @include('admin.partials.sidebar')
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Nomination Questions</h2>
                <p class="text-muted">Add custom questions and drag to reorder</p>
            </div>
            <button class="btn-gradient" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                <i class="fas fa-plus-circle me-2"></i>Add Question
            </button>
        </div>

        @if (request()->has('msg'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Operation completed successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter by Category -->
        <div class="mb-4">
            <form method="GET" class="d-flex gap-2 align-items-center">
                <select name="category" class="form-select w-auto" onchange="this.form.submit()">
                    <option value="0">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{!! $cat['id'] !!}" {!! $category_filter == $cat['id'] ? 'selected' : '' !!}>
                            {{ $cat['name'] }}
                        </option>
                    @endforeach
                </select>
                @if ($category_filter)
                    <a href="manage-questions.php" class="btn btn-outline-secondary">Clear</a>
                @endif
                <span class="ms-auto order-hint">
                    <i class="fas fa-grip-vertical"></i> Drag questions to reorder
                    <button class="btn btn-sm btn-outline-primary save-order-btn" id="saveOrderBtn" onclick="saveOrder()" style="display: none;">
                        <i class="fas fa-save me-1"></i>Save Order
                    </button>
                </span>
            </form>
        </div>

        <!-- Questions List with Drag and Drop -->
        @if (count($questions) > 0)
            @php $current_category = '';
            $questions_data = [];
            foreach ($questions as $q) {
                $questions_data[] = $q;
            }
            
            // Group by category
            $grouped_questions = [];
            foreach($questions_data as $q) {
                $grouped_questions[$q['category_name']][] = $q;
            }
            @endphp
            @foreach ($grouped_questions as $category_name => $category_questions)
                <h5 class="fw-bold mt-4 mb-3">
                    <i class="fas fa-folder-open me-2" style="color: var(--kiboko-blue);"></i>
                    {{ $category_name }}
                </h5>
                <ul class="questions-list" id="questions-list-{!! md5($category_name) !!}" data-category="{{ $category_name }}">
                    @foreach ($category_questions as $q)
                        <li class="question-item-drag" data-id="{!! $q['id'] !!}">
                            <span class="drag-handle"><i class="fas fa-grip-vertical"></i></span>
                            <div class="question-content">
                                <div class="mb-2">
                                    <span class="type-badge">
                                        <i class="fas fa-{!! \App\Support\NominationsLegacy::getTypeIcon($q['question_type']) !!} me-1"></i>
                                        {!! ucfirst(str_replace('_', ' ', $q['question_type'])) !!}
                                    </span>
                                    @if ($q['is_required'])
                                        <span class="required-badge">
                                            <i class="fas fa-asterisk me-1"></i>Required
                                        </span>
                                    @endif
                                    @if ($q['options'])
                                        <span class="type-badge ms-1">
                                            <i class="fas fa-list me-1"></i>{{ $q['options'] }}
                                        </span>
                                    @endif
                                </div>
                                <div class="question-text-display">
                                    @php $text = $q['question_text'];
                                    $text = str_replace("\r\n", "\n", $text);
                                    $text = str_replace("\r", "\n", $text);
                                    echo nl2br(htmlspecialchars($text)); @endphp
                                </div>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary" onclick='editQuestion({!! json_encode($q) !!})'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteQuestion({!! $q['id'] !!})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endforeach
        @else
            <div class="text-center py-5 bg-white rounded-4">
                <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                <p class="text-muted">No questions found. Add your first question!</p>
            </div>
        @endif
    </div>

    <!-- Add Question Modal -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form method="POST">
@csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Question</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                @foreach ($categories as $cat)
                                    <option value="{!! $cat['id'] !!}">{{ $cat['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Question Text</label>
                            <textarea name="question_text" class="form-control" rows="4" required placeholder="e.g., How has this person demonstrated leadership?&#10;Please provide specific examples..."></textarea>
                            <small class="text-muted">Press Enter for new lines. Line breaks will be preserved.</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Question Type</label>
                                <select name="question_type" id="question_type" class="form-select" required onchange="toggleOptionsField()">
                                    <option value="text">Short Text</option>
                                    <option value="long_text">Long Text / Paragraph</option>
                                    <option value="number">Number</option>
                                    <option value="date">Date</option>
                                    <option value="rating">Rating (1-5)</option>
                                    <option value="radio">Radio (Single Choice)</option>
                                    <option value="checkbox">Checkbox (Multiple Choice)</option>
                                    <option value="attachment">File Attachment</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="mb-3" id="options_field" style="display: none;">
                            <label class="form-label">Options (comma separated)</label>
                            <input type="text" name="options" class="form-control" placeholder="Option 1, Option 2, Option 3">
                        </div>
                        <div class="mb-3">
                            <label class="form-check-label">
                                <input type="checkbox" name="is_required" value="1" class="form-check-input"> 
                                <span class="ms-2">Required question</span>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-gradient">Add Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Question Modal -->
    <div class="modal fade" id="editQuestionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form method="POST">
@csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Question</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" id="edit_category_id" class="form-select" required>
                                @foreach ($categories as $cat)
                                    <option value="{!! $cat['id'] !!}">{{ $cat['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Question Text</label>
                            <textarea name="question_text" id="edit_question_text" class="form-control" rows="4" required></textarea>
                            <small class="text-muted">Press Enter for new lines. Line breaks will be preserved.</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Question Type</label>
                                <select name="question_type" id="edit_question_type" class="form-select" required onchange="toggleEditOptionsField()">
                                    <option value="text">Short Text</option>
                                    <option value="long_text">Long Text / Paragraph</option>
                                    <option value="number">Number</option>
                                    <option value="date">Date</option>
                                    <option value="rating">Rating (1-5)</option>
                                    <option value="radio">Radio (Single Choice)</option>
                                    <option value="checkbox">Checkbox (Multiple Choice)</option>
                                    <option value="attachment">File Attachment</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" id="edit_sort_order" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3" id="edit_options_field" style="display: none;">
                            <label class="form-label">Options (comma separated)</label>
                            <input type="text" name="options" id="edit_options" class="form-control" placeholder="Option 1, Option 2, Option 3">
                        </div>
                        <div class="mb-3">
                            <label class="form-check-label">
                                <input type="checkbox" name="is_required" id="edit_is_required" value="1" class="form-check-input"> 
                                <span class="ms-2">Required question</span>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-gradient">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="POST" style="display: none;">
@csrf
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="delete_id">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Store original order for change detection
        let originalOrders = {};
        let sortableInstances = [];
        let orderChanged = false;

        // Initialize drag and drop
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.questions-list').forEach(list => {
                const sortable = new Sortable(list, {
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                    onEnd: function() {
                        orderChanged = true;
                        document.getElementById('saveOrderBtn').style.display = 'inline-block';
                    }
                });
                sortableInstances.push(sortable);
                
                // Store original order
                const category = list.dataset.category;
                originalOrders[category] = [];
                list.querySelectorAll('.question-item-drag').forEach(item => {
                    originalOrders[category].push(item.dataset.id);
                });
            });
        });

        function saveOrder() {
            const saveBtn = document.getElementById('saveOrderBtn');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';
            saveBtn.disabled = true;
            
            const allOrders = [];
            document.querySelectorAll('.questions-list').forEach(list => {
                list.querySelectorAll('.question-item-drag').forEach((item, index) => {
                    allOrders.push({
                        id: item.dataset.id,
                        sort_order: index + 1
                    });
                });
            });
            
            const formData = new FormData();
            formData.append('action', 'reorder');
            formData.append('order', JSON.stringify(allOrders.map(o => o.id)));
            
            fetch('manage-questions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    orderChanged = false;
                    document.getElementById('saveOrderBtn').style.display = 'none';
                    
                    // Show success toast
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show position-fixed';
                    alert.style.top = '20px';
                    alert.style.right = '20px';
                    alert.style.zIndex = '9999';
                    alert.innerHTML = 'Order saved successfully! <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    document.body.appendChild(alert);
                    setTimeout(() => alert.remove(), 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving order. Please try again.');
            })
            .finally(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            });
        }

        function toggleOptionsField() {
            const type = document.getElementById('question_type').value;
            const optionsField = document.getElementById('options_field');
            optionsField.style.display = (type === 'radio' || type === 'checkbox') ? 'block' : 'none';
        }
        
        function toggleEditOptionsField() {
            const type = document.getElementById('edit_question_type').value;
            const optionsField = document.getElementById('edit_options_field');
            optionsField.style.display = (type === 'radio' || type === 'checkbox') ? 'block' : 'none';
        }
        
        function editQuestion(q) {
            document.getElementById('edit_id').value = q.id;
            document.getElementById('edit_category_id').value = q.category_id;
            document.getElementById('edit_question_text').value = q.question_text;
            document.getElementById('edit_question_type').value = q.question_type;
            document.getElementById('edit_sort_order').value = q.sort_order;
            document.getElementById('edit_options').value = q.options || '';
            document.getElementById('edit_is_required').checked = q.is_required == 1;
            toggleEditOptionsField();
            new bootstrap.Modal(document.getElementById('editQuestionModal')).show();
        }
        
        function deleteQuestion(id) {
            if(confirm('Are you sure you want to delete this question?')) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>

@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
:root {
    --kiboko-blue: #009ada;
    --kiboko-green: #76bc21;
}

.notes-header {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.03);
    margin-bottom: 1.5rem;
}

.lesson-tabs {
    background: white;
    border-radius: 12px;
    padding: 0.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    margin-bottom: 1.5rem;
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
}

.lesson-tab {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.3s ease;
    color: #64748b;
    font-weight: 500;
}

.lesson-tab:hover {
    background: #f1f5f9;
}

.lesson-tab.active {
    background: linear-gradient(135deg, var(--kiboko-blue) 0%, var(--kiboko-green) 100%);
    color: white;
}

.lesson-tab.has-notes::after {
    content: '';
    display: inline-block;
    width: 8px;
    height: 8px;
    background: #28a745;
    border-radius: 50%;
    margin-left: 8px;
}

.notes-editor {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.03);
}

.notes-textarea {
    font-size: 15px;
    line-height: 1.8;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    resize: vertical;
    transition: all 0.3s ease;
}

.notes-textarea:focus {
    border-color: var(--kiboko-blue);
    box-shadow: 0 0 0 3px rgba(0,154,218,0.1);
    outline: none;
}

.word-count {
    font-size: 0.85rem;
    color: #64748b;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.quick-action-btn {
    border: 2px solid #e2e8f0;
    background: white;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
    text-align: center;
}

.quick-action-btn:hover {
    border-color: var(--kiboko-blue);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Header -->
    <div class="notes-header">
        <div class="row align-items-center">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="my-courses" class="text-decoration-none">My Courses</a></li>
                        <li class="breadcrumb-item"><a href="course-player?id=<?php echo $course_id; ?>" class="text-decoration-none"><?php echo htmlspecialchars($course['title']); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Notes</li>
                    </ol>
                </nav>
                <h2 class="mb-1">My Course Notes</h2>
                <p class="text-muted mb-0">Take and manage notes for <?php echo htmlspecialchars($course['title']); ?></p>
            </div>
            <div class="col-auto">
                <span class="badge bg-primary p-2">
                    <i class="bi bi-journal-text"></i> 
                    <?php echo count(array_filter($lessons, fn($l) => !empty($l['notes']))); ?>/<?php echo count($lessons); ?> lessons with notes
                </span>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10">
                    <i class="bi bi-journal-text text-primary"></i>
                </div>
                <div>
                    <h4 class="mb-0"><?php echo count($lessons); ?></h4>
                    <small class="text-muted">Total Lessons</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10">
                    <i class="bi bi-check-circle text-success"></i>
                </div>
                <div>
                    <h4 class="mb-0"><?php echo count(array_filter($lessons, fn($l) => !empty($l['notes']))); ?></h4>
                    <small class="text-muted">With Notes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info bg-opacity-10">
                    <i class="bi bi-file-text text-info"></i>
                </div>
                <div>
                    <h4 class="mb-0"><?php 
                        $total_words = 0;
                        foreach ($lessons as $lesson) {
                            if (!empty($lesson['notes'])) {
                                $total_words += str_word_count($lesson['notes']);
                            }
                        }
                        echo number_format($total_words);
                    ?></h4>
                    <small class="text-muted">Total Words</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10">
                    <i class="bi bi-clock-history text-warning"></i>
                </div>
                <div>
                    <h4 class="mb-0"><?php 
                        $last_updated = array_reduce($lessons, function($carry, $lesson) {
                            if (!empty($lesson['updated_at']) && (!$carry || strtotime($lesson['updated_at']) > strtotime($carry))) {
                                return $lesson['updated_at'];
                            }
                            return $carry;
                        });
                        echo $last_updated ? date('M d, Y', strtotime($last_updated)) : 'Never';
                    ?></h4>
                    <small class="text-muted">Last Updated</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Lesson Tabs -->
    <div class="lesson-tabs" id="lessonTabs">
        <?php foreach ($lessons as $index => $lesson): ?>
            <div class="lesson-tab <?php echo $index === 0 ? 'active' : ''; ?> <?php echo !empty($lesson['notes']) ? 'has-notes' : ''; ?>" 
                 data-lesson-id="<?php echo $lesson['id']; ?>"
                 data-lesson-index="<?php echo $index; ?>">
                Lesson <?php echo $lesson['order_number']; ?>: <?php echo htmlspecialchars($lesson['title']); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Notes Editor -->
    <div class="notes-editor">
        <input type="hidden" id="currentLessonId" value="<?php echo $lessons[0]['id'] ?? ''; ?>">
        <input type="hidden" id="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 id="currentLessonTitle"><?php echo $lessons[0]['title'] ?? ''; ?></h5>
            <div>
                <span class="badge bg-success me-2" id="notesStatus" style="display: none;">
                    <i class="bi bi-check-circle"></i> Saved
                </span>
                <span class="word-count" id="wordCount">0 words</span>
            </div>
        </div>
        
        <textarea class="notes-textarea w-100" id="lessonNotes" rows="20" 
                  placeholder="✏️ Write your notes for this lesson here...

Tips:
- Use bullet points for key concepts
- Note down questions you have
- Mark important timestamps
- Summarize main takeaways
- Jot down additional resources to explore

Your notes are automatically saved every 30 seconds."><?php echo htmlspecialchars($lessons[0]['notes'] ?? ''); ?></textarea>
        
        <div class="d-flex justify-content-between mt-3">
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary" onclick="insertTimestamp()">
                    <i class="bi bi-clock"></i> Insert Timestamp
                </button>
                <button class="btn btn-sm btn-outline-primary" onclick="insertBullet()">
                    <i class="bi bi-list-ul"></i> Bullet Point
                </button>
                <button class="btn btn-sm btn-outline-primary" onclick="insertQuote()">
                    <i class="bi bi-quote"></i> Quote
                </button>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" onclick="saveNotes(true)">
                    <i class="bi bi-save"></i> Save Now
                </button>
                <button class="btn btn-sm btn-outline-success" onclick="exportNotes()">
                    <i class="bi bi-download"></i> Export
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="clearNotes()">
                    <i class="bi bi-eraser"></i> Clear
                </button>
            </div>
        </div>
        
        <div class="mt-2 text-end" id="lastSaved">
            <?php if (!empty($lessons[0]['updated_at'])): ?>
                <small class="text-muted">
                    <i class="bi bi-clock-history"></i> Last saved: <?php echo date('M d, Y H:i', strtotime($lessons[0]['updated_at'])); ?>
                </small>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mt-4">
        <div class="col-md-3">
            <div class="quick-action-btn" onclick="exportAllNotes()">
                <i class="bi bi-file-earmark-text fs-4 d-block mb-2"></i>
                <small>Export All Notes</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="quick-action-btn" onclick="printNotes()">
                <i class="bi bi-printer fs-4 d-block mb-2"></i>
                <small>Print Notes</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="quick-action-btn" onclick="searchNotes()">
                <i class="bi bi-search fs-4 d-block mb-2"></i>
                <small>Search Notes</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="quick-action-btn" onclick="backupNotes()">
                <i class="bi bi-cloud-upload fs-4 d-block mb-2"></i>
                <small>Backup Notes</small>
            </div>
        </div>
    </div>
</main>

<script>
let currentLessonId = <?php echo $lessons[0]['id'] ?? 0; ?>;
let notesTimeout;
let isSaving = false;

// Initialize lesson tabs
document.querySelectorAll('.lesson-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        // Update active tab
        document.querySelectorAll('.lesson-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        // Get lesson data
        const lessonId = this.dataset.lessonId;
        const lessonIndex = this.dataset.lessonIndex;
        const lessonTitle = this.textContent.replace(/^(Lesson \d+: )/, '');
        
        // Save current notes before switching
        saveNotes(false).then(() => {
            // Load new lesson notes
            loadLessonNotes(lessonId, lessonIndex, lessonTitle);
        });
    });
});

function loadLessonNotes(lessonId, lessonIndex, lessonTitle) {
    currentLessonId = lessonId;
    document.getElementById('currentLessonId').value = lessonId;
    document.getElementById('currentLessonTitle').textContent = lessonTitle;
    
    // Get notes for this lesson from the server
    fetch(`api/get-notes?lesson_id=${lessonId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('lessonNotes').value = data.notes || '';
                updateWordCount();
                
                if (data.updated_at) {
                    document.getElementById('lastSaved').innerHTML = 
                        `<small class="text-muted"><i class="bi bi-clock-history"></i> Last saved: ${data.updated_at}</small>`;
                } else {
                    document.getElementById('lastSaved').innerHTML = '';
                }
            }
        });
}

function saveNotes(showNotification = false) {
    const notes = document.getElementById('lessonNotes').value;
    const saveBtn = document.querySelector('.btn-outline-secondary i.bi-save');
    const notesStatus = document.getElementById('notesStatus');
    
    if (isSaving) return Promise.resolve();
    isSaving = true;
    
    if (saveBtn) {
        saveBtn.classList.remove('bi-save');
        saveBtn.classList.add('bi-arrow-repeat', 'spinner');
    }
    
    return fetch('api/save-notes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            lesson_id: currentLessonId,
            notes: notes,
            csrf_token: document.getElementById('csrf_token').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('lastSaved').innerHTML = 
                '<small class="text-muted"><i class="bi bi-check-circle text-success"></i> Saved just now</small>';
            
            if (notesStatus) {
                notesStatus.style.display = 'inline-block';
                setTimeout(() => {
                    notesStatus.style.display = 'none';
                }, 2000);
            }
            
            // Mark tab as having notes
            const activeTab = document.querySelector('.lesson-tab.active');
            if (activeTab && notes.trim() !== '') {
                activeTab.classList.add('has-notes');
            } else if (activeTab && notes.trim() === '') {
                activeTab.classList.remove('has-notes');
            }
            
            if (showNotification) {
                Swal.fire({
                    icon: 'success',
                    title: 'Notes Saved!',
                    text: 'Your notes have been saved successfully.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        }
    })
    .finally(() => {
        isSaving = false;
        if (saveBtn) {
            saveBtn.classList.remove('bi-arrow-repeat', 'spinner');
            saveBtn.classList.add('bi-save');
        }
    });
}

function updateWordCount() {
    const notes = document.getElementById('lessonNotes').value;
    const words = notes.trim() === '' ? 0 : notes.trim().split(/\s+/).length;
    document.getElementById('wordCount').textContent = words + ' word' + (words !== 1 ? 's' : '');
}

// Auto-save every 30 seconds
setInterval(() => {
    saveNotes(false);
}, 30000);

// Update word count on input
document.getElementById('lessonNotes').addEventListener('input', updateWordCount);
updateWordCount();

function insertTimestamp() {
    const textarea = document.getElementById('lessonNotes');
    const now = new Date();
    const timestamp = `\n[${now.toLocaleTimeString()}] `;
    
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    
    textarea.value = text.substring(0, start) + timestamp + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + timestamp.length;
    textarea.focus();
    
    saveNotes(false);
    updateWordCount();
}

function insertBullet() {
    const textarea = document.getElementById('lessonNotes');
    const bullet = '\n• ';
    
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    
    textarea.value = text.substring(0, start) + bullet + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + bullet.length;
    textarea.focus();
    
    saveNotes(false);
    updateWordCount();
}

function insertQuote() {
    const textarea = document.getElementById('lessonNotes');
    const selectedText = textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);
    const quote = selectedText ? `\n> ${selectedText}\n` : '\n> ';
    
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    
    textarea.value = text.substring(0, start) + quote + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + quote.length;
    textarea.focus();
    
    saveNotes(false);
    updateWordCount();
}

function exportNotes() {
    const notes = document.getElementById('lessonNotes').value;
    const lessonTitle = document.getElementById('currentLessonTitle').textContent;
    const blob = new Blob([notes], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `lesson-${currentLessonId}-notes.txt`;
    a.click();
    window.URL.revokeObjectURL(url);
}

function exportAllNotes() {
    window.location.href = `api/export-all-notes?course_id=<?php echo $course_id; ?>`;
}

function clearNotes() {
    Swal.fire({
        title: 'Clear Notes?',
        text: 'Are you sure you want to clear all notes for this lesson?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, clear'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('lessonNotes').value = '';
            saveNotes(true);
            updateWordCount();
        }
    });
}

function printNotes() {
    const notes = document.getElementById('lessonNotes').value;
    const lessonTitle = document.getElementById('currentLessonTitle').textContent;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Notes - ${lessonTitle}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 2rem; line-height: 1.6; }
                h1 { color: #009ada; }
                pre { white-space: pre-wrap; font-family: inherit; }
            </style>
        </head>
        <body>
            <h1>${lessonTitle}</h1>
            <pre>${notes}</pre>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function searchNotes() {
    Swal.fire({
        title: 'Search Notes',
        input: 'text',
        inputLabel: 'Enter search term',
        inputPlaceholder: 'Search in your notes...',
        showCancelButton: true,
        confirmButtonText: 'Search'
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const searchTerm = result.value.toLowerCase();
            const notes = document.getElementById('lessonNotes').value.toLowerCase();
            
            if (notes.includes(searchTerm)) {
                Swal.fire({
                    icon: 'success',
                    title: 'Found!',
                    text: 'Search term found in current lesson notes.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Not Found',
                    text: 'Search term not found in current lesson notes.'
                });
            }
        }
    });
}

function backupNotes() {
    Swal.fire({
        title: 'Backup Notes?',
        text: 'This will create a backup of all your notes for this course.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Backup'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implement backup functionality
            window.location.href = `api/backup-notes?course_id=<?php echo $course_id; ?>`;
        }
    });
}
</script>


@endverbatim

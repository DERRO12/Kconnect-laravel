@include('academy.partials.header')
@verbatim
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.css" rel="stylesheet">
<style>
        .lesson-item {
            transition: all 0.3s ease;
        }
        .lesson-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        .drag-handle {
            cursor: move;
            color: #6c757d;
        }
        .drag-handle:hover {
            color: #0d6efd;
        }
    </style>
@endverbatim
@include('academy.partials.sidebar')
@verbatim


            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <nav class="navbar navbar-expand navbar-light bg-white mb-4 rounded-bottom shadow-sm">
                    <div class="container-fluid">
                        <div>
                            <h5 class="mb-0">Manage Lessons: <?php echo $course['title']; ?></h5>
                            <small class="text-secondary"><?php echo count($lessons); ?> lessons</small>
                        </div>
                        <div>
                            <a href="manage_courses" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                            <button type="button" class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#bulkLessonModal">
                                <i class="bi bi-collection-play"></i> Upload Videos
                            </button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLessonModal">
                                <i class="bi bi-plus-circle"></i> Add Lesson
                            </button>
                        </div>
                    </div>
                </nav>

                <?php echo ka_flash_html(); ?>

                <!-- Lessons List -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Drag and drop lessons to reorder them.
                        </div>
                        
                        <div id="lessonList" class="lesson-list">
                            <?php foreach ($lessons as $index => $lesson): ?>
                            <div class="lesson-item card mb-2" data-id="<?php echo $lesson['id']; ?>">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <i class="bi bi-grip-vertical drag-handle" style="cursor: move;"></i>
                                            <span class="badge bg-secondary me-2"><?php echo $index + 1; ?></span>
                                        </div>
                                        <div class="col">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($lesson['title']); ?></h6>
                                            <small class="text-secondary">
                                                <i class="bi bi-clock"></i> <?php echo $lesson['duration'] ?: '0'; ?> min |
                                                <i class="bi bi-<?php echo $lesson['video_type'] == 'youtube' ? 'youtube' : 'camera-reels'; ?>"></i> 
                                                <?php echo ucfirst($lesson['video_type']); ?> |
                                                <i class="bi bi-people"></i> <?php echo $lesson['completions']; ?> completions
                                                <?php if ($lesson['attachments']): ?>
                                                    | <i class="bi bi-paperclip"></i> <?php echo count(json_decode($lesson['attachments'], true)); ?> files
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <div class="col-auto">
                                            <button class="btn btn-sm btn-outline-primary edit-lesson-btn" data-lesson-id="<?php echo $lesson['id']; ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <a href="?course_id=<?php echo $course_id; ?>&delete=<?php echo $lesson['id']; ?>&token=<?php echo $csrf_token; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Are you sure you want to delete this lesson? This will also delete all associated files.')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <?php if (empty($lessons)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-journal-x display-1 text-muted"></i>
                                <h5 class="mt-3">No Lessons Yet</h5>
                                <p class="text-secondary">Click the "Add Lesson" button to create your first lesson.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Lesson Modal -->
    <div class="modal fade" id="addLessonModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Lesson</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="add_lesson" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                        
                        <div class="mb-3">
                            <label for="lesson_title" class="form-label">Lesson Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="lesson_title" name="title" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="video_type" class="form-label">Video Type</label>
                                    <select class="form-select" id="video_type" name="video_type">
                                        <option value="youtube">YouTube or online link</option>
                                        <option value="upload">Upload a video file</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration (minutes)</label>
                                    <input type="number" class="form-control" id="duration" name="duration" min="1">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3" id="youtube_url_field">
                            <label for="video_url" class="form-label">Video link</label>
                            <input type="url" class="form-control" id="video_url" name="video_url" 
                                   placeholder="https://www.youtube.com/watch?v=...">
                            <div class="form-text">YouTube, Vimeo, Google Drive (shared link) or a direct .mp4 address.</div>
                        </div>
                        
                        <div class="mb-3 d-none" id="video_upload_field">
                            <label for="video_file" class="form-label">Upload Video</label>
                            <input type="file" class="form-control" id="video_file" name="video_file" 
                                   accept="video/mp4,video/webm,video/ogg,video/quicktime,.m4v">
                            <div class="form-text">Max size: <?php echo (int) config('academy.max_video_mb'); ?>MB. Formats: MP4, WebM, OGG, M4V, MOV (MP4 plays everywhere)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="lesson_content" class="form-label">Lesson Content</label>
                            <textarea class="form-control" id="lesson_content" name="content" rows="6"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="attachments" class="form-label">Attachments (PDF, Images, Documents)</label>
                            <input type="file" class="form-control" id="attachments" name="attachments[]" multiple 
                                   accept=".pdf,image/*,.doc,.docx,.ppt,.pptx,.txt,.zip">
                            <div class="form-text">You can select multiple files. Max size per file: 10MB</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Lesson</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Video Upload Modal: one lesson per video -->
    <div class="modal fade" id="bulkLessonModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Several Videos at Once</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="bulk_add_lessons" method="POST" enctype="multipart/form-data" id="bulkLessonForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                        <div class="mb-3">
                            <label for="video_files" class="form-label">Video files <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="video_files" name="video_files[]" multiple required
                                   accept="video/mp4,video/webm,video/ogg,video/quicktime,.m4v">
                            <div class="form-text">Each video becomes a lesson named after its file, added in file-name order (name them 01 Intro, 02 Safety...). Edit each lesson afterwards to add content, duration and attachments. Max <?php echo (int) config('academy.max_video_mb'); ?>MB per video, and the whole upload must fit the server's upload limit.</div>
                        </div>
                        <div class="small text-secondary" id="bulkLessonSummary"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> Upload and Create Lessons</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('video_files');
        var summary = document.getElementById('bulkLessonSummary');
        if (!input) return;
        input.addEventListener('change', function () {
            var total = 0;
            for (var i = 0; i < input.files.length; i++) total += input.files[i].size;
            summary.textContent = input.files.length ? input.files.length + ' video(s), ' + (total / 1048576).toFixed(1) + ' MB in total' : '';
        });
        document.getElementById('bulkLessonForm').addEventListener('submit', function () {
            var b = this.querySelector('button[type=submit]');
            b.disabled = true;
            b.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Uploading...';
        });
    });
    </script>

    <!-- Edit Lesson Modal -->
    <div class="modal fade" id="editLessonModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Lesson</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="edit_lesson" method="POST" enctype="multipart/form-data" id="editLessonForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="lesson_id" id="edit_lesson_id">
                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                        
                        <div class="mb-3">
                            <label for="edit_lesson_title" class="form-label">Lesson Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_lesson_title" name="title" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_video_type" class="form-label">Video Type</label>
                                    <select class="form-select" id="edit_video_type" name="video_type">
                                        <option value="youtube">YouTube or online link</option>
                                        <option value="upload">Upload a video file</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_duration" class="form-label">Duration (minutes)</label>
                                    <input type="number" class="form-control" id="edit_duration" name="duration" min="1">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3" id="edit_youtube_url_field">
                            <label for="edit_video_url" class="form-label">Video link</label>
                            <input type="url" class="form-control" id="edit_video_url" name="video_url" 
                                   placeholder="https://www.youtube.com/watch?v=...">
                            <div id="current_youtube_video" class="mt-2 small text-muted"></div>
                        </div>
                        
                        <div class="mb-3 d-none" id="edit_video_upload_field">
                            <label for="edit_video_file" class="form-label">Upload New Video</label>
                            <input type="file" class="form-control" id="edit_video_file" name="video_file" 
                                   accept="video/mp4,video/webm,video/ogg,video/quicktime,.m4v">
                            <div class="form-text">Max size: <?php echo (int) config('academy.max_video_mb'); ?>MB. Leave empty to keep current video.</div>
                            <div id="current_uploaded_video" class="mt-2 small text-muted"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Current Attachments</label>
                            <div id="current_attachments" class="mb-2"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_attachments" class="form-label">Add New Attachments</label>
                            <input type="file" class="form-control" id="edit_attachments" name="attachments[]" multiple 
                                   accept=".pdf,image/*,.doc,.docx,.ppt,.pptx,.txt,.zip">
                            <div class="form-text">You can select multiple files. Max size per file: 10MB</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_lesson_content" class="form-label">Lesson Content</label>
                            <textarea class="form-control" id="edit_lesson_content" name="content" rows="6"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Lesson</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Wait for DOM to be fully loaded
        $(document).ready(function() {
            console.log('Document ready - Summernote available:', typeof $.fn.summernote !== 'undefined');
            
            // Initialize Sortable for drag & drop reordering
            const lessonList = document.getElementById('lessonList');
            if (lessonList) {
                new Sortable(lessonList, {
                    animation: 150,
                    handle: '.drag-handle',
                    onEnd: function(evt) {
                        const items = [];
                        document.querySelectorAll('.lesson-item').forEach(item => {
                            items.push(item.dataset.id);
                        });
                        
                        fetch('reorder_lessons', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                items: items,
                                course_id: <?php echo $course_id; ?>,
                                csrf_token: '<?php echo $csrf_token; ?>'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update badge numbers
                                document.querySelectorAll('.lesson-item .badge').forEach((badge, index) => {
                                    badge.textContent = index + 1;
                                });
                            }
                        });
                    }
                });
            }
            
            // Toggle video fields for Add Modal
            $('#video_type').change(function() {
                if ($(this).val() === 'youtube') {
                    $('#youtube_url_field').removeClass('d-none');
                    $('#video_upload_field').addClass('d-none');
                    $('#video_url').prop('required', true);
                    $('#video_file').prop('required', false);
                } else {
                    $('#youtube_url_field').addClass('d-none');
                    $('#video_upload_field').removeClass('d-none');
                    $('#video_url').prop('required', false);
                    $('#video_file').prop('required', true);
                }
            });
            
            // Toggle video fields for Edit Modal
            $('#edit_video_type').change(function() {
                if ($(this).val() === 'youtube') {
                    $('#edit_youtube_url_field').removeClass('d-none');
                    $('#edit_video_upload_field').addClass('d-none');
                    $('#edit_video_url').prop('required', false);
                    $('#edit_video_file').prop('required', false);
                } else {
                    $('#edit_youtube_url_field').addClass('d-none');
                    $('#edit_video_upload_field').removeClass('d-none');
                    $('#edit_video_url').prop('required', false);
                    $('#edit_video_file').prop('required', false);
                }
            });
            
            // Edit lesson function - using event delegation
            $(document).on('click', '.edit-lesson-btn', function() {
                const lessonId = $(this).data('lesson-id');
                console.log('Edit button clicked for lesson:', lessonId);
                
                // Fetch lesson data
                fetch('get_lesson?id=' + lessonId)
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Lesson data received:', data);
                        
                        if (data.error) {
                            alert('Error loading lesson: ' + data.error);
                            return;
                        }
                        
                        // Populate form fields
                        $('#edit_lesson_id').val(data.id);
                        $('#edit_lesson_title').val(data.title);
                        $('#edit_video_type').val(data.video_type || 'youtube').trigger('change');
                        $('#edit_duration').val(data.duration || '');
                        
                        // Set video URL/content based on type
                        if (data.video_type === 'youtube') {
                            $('#edit_video_url').val(data.video_url || '');
                            $('#current_youtube_video').html('Current video: <a href="' + (data.video_url || '#') + '" target="_blank">' + (data.video_url || 'None') + '</a>');
                            $('#current_uploaded_video').html('');
                        } else if (data.video_type === 'upload') {
                            $('#edit_video_url').val('');
                            $('#current_uploaded_video').html('Current video: ' + (data.video_url || 'None'));
                            $('#current_youtube_video').html('');
                        } else {
                            $('#edit_video_url').val('');
                            $('#current_youtube_video').html('');
                            $('#current_uploaded_video').html('');
                        }
                        
                        // Set content
                        $('#edit_lesson_content').val(data.content || '');
                        
                        // Display attachments
                        const attachmentsDiv = $('#current_attachments');
                        attachmentsDiv.empty();
                        
                        if (data.attachments) {
                            let attachments = data.attachments;
                            if (typeof attachments === 'string') {
                                try {
                                    attachments = JSON.parse(attachments);
                                } catch(e) {
                                    attachments = [];
                                }
                            }
                            
                            if (attachments && attachments.length > 0) {
                                const list = $('<div class="list-group"></div>');
                                attachments.forEach(file => {
                                    list.append(`
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="bi bi-file-earmark"></i> 
                                                ${file}
                                            </span>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-attachment-btn" data-filename="${file}" data-lesson-id="${data.id}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    `);
                                });
                                attachmentsDiv.append(list);
                            } else {
                                attachmentsDiv.html('<p class="text-muted">No attachments</p>');
                            }
                        } else {
                            attachmentsDiv.html('<p class="text-muted">No attachments</p>');
                        }
                        
                        // Show modal
                        const editModal = new bootstrap.Modal(document.getElementById('editLessonModal'));
                        editModal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error loading lesson: ' + error.message);
                    });
            });
            
            // Remove attachment
            $(document).on('click', '.remove-attachment-btn', function() {
                if (!confirm('Delete this attachment?')) return;
                
                const filename = $(this).data('filename');
                const lessonId = $(this).data('lesson-id');
                
                fetch('delete_attachment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        lesson_id: lessonId,
                        filename: filename,
                        csrf_token: '<?php echo $csrf_token; ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Refresh the edit modal
                        $('.edit-lesson-btn[data-lesson-id="' + lessonId + '"]').click();
                    } else {
                        alert('Error deleting attachment: ' + data.error);
                    }
                });
            });
            
            // Reset form when modal is closed
            $('#addLessonModal, #editLessonModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
            });
        });
    </script>
</body>
</html>
@endverbatim

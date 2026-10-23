@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim


<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Edit Course</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="manage_courses" class="btn btn-sm btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Back to Courses
            </a>
            <a href="manage_lessons?course_id=<?php echo $course_id; ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-list-check"></i> Manage Lessons
            </a>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <!-- Main Course Form -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Course Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="courseForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="title" 
                                   name="title" 
                                   value="<?php echo htmlspecialchars($course['title']); ?>" 
                                   required>
                            <small class="text-muted">The title should be descriptive and engaging</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="slug" class="form-label">URL Slug</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><?php echo ka_app_url(); ?>/course/</span>
                                <input type="text" 
                                       class="form-control" 
                                       id="slug" 
                                       name="slug" 
                                       value="<?php echo htmlspecialchars($course['slug']); ?>"
                                       placeholder="auto-generated-if-empty">
                            </div>
                            <small class="text-muted">Leave empty to auto-generate from title</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Course Description</label>
                            <textarea class="form-control summernote" 
                                      id="description" 
                                      name="description" 
                                      rows="10"><?php echo htmlspecialchars($course['description']); ?></textarea>
                            <small class="text-muted">Provide a detailed description of the course content</small>
                        </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Course Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Course Settings</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" 
                               class="form-control" 
                               id="category" 
                               name="category" 
                               value="<?php echo htmlspecialchars($course['category']); ?>"
                               placeholder="e.g., Web Development, Design, Marketing">
                        <small class="text-muted">Helps users find your course</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="level" class="form-label">Difficulty Level</label>
                        <select class="form-select" id="level" name="level">
                            <option value="beginner" <?php echo $course['level'] === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                            <option value="intermediate" <?php echo $course['level'] === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                            <option value="advanced" <?php echo $course['level'] === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration</label>
                        <input type="text" 
                               class="form-control" 
                               id="duration" 
                               name="duration" 
                               value="<?php echo htmlspecialchars($course['duration']); ?>"
                               placeholder="e.g., 10 hours, 4 weeks">
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="draft" <?php echo $course['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?php echo $course['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                        </select>
                        <small class="text-muted">Draft courses are hidden from users</small>
                    </div>
                </div>
            </div>
            
            <!-- Thumbnail Upload -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Course Thumbnail</h6>
                </div>
                <div class="card-body">
                    <div class="current-thumbnail mb-3 text-center">
                        <?php if ($course['thumbnail']): ?>
                            <img src="../assets/uploads/courses/<?php echo $course['thumbnail']; ?>" 
                                 alt="Current thumbnail" 
                                 class="img-fluid rounded" 
                                 style="max-height: 200px;">
                            <div class="mt-2">
                                <small class="text-muted">Current thumbnail</small>
                            </div>
                        <?php else: ?>
                            <div class="bg-light rounded p-4 text-center">
                                <i class="bi bi-image display-1 text-muted"></i>
                                <p class="mt-2 text-muted">No thumbnail uploaded</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Upload New Thumbnail</label>
                        <input type="file" 
                               class="form-control" 
                               id="thumbnail" 
                               name="thumbnail" 
                               accept="image/jpeg,image/png,image/gif">
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> 
                            Max size: 2MB. Recommended: 800x600px. Allowed: JPG, PNG, GIF
                        </div>
                    </div>
                    
                    <div id="thumbnailPreview" class="text-center d-none">
                        <img src="#" alt="Preview" class="img-fluid rounded" style="max-height: 150px;">
                    </div>
                </div>
            </div>
            
            <!-- Course Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Course Statistics</h6>
                </div>
                <div class="card-body">
                    <?php
                    // Get lesson count
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lessons WHERE course_id = ?");
                    $stmt->execute([$course_id]);
                    $lesson_count = $stmt->fetchColumn();
                    
                    // Get enrollment count
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE course_id = ?");
                    $stmt->execute([$course_id]);
                    $enrollment_count = $stmt->fetchColumn();
                    
                    // Get completion count
                    $stmt = $pdo->prepare("
                        SELECT COUNT(*) FROM enrollments 
                        WHERE course_id = ? AND status = 'completed'
                    ");
                    $stmt->execute([$course_id]);
                    $completion_count = $stmt->fetchColumn();
                    ?>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="bi bi-journal-check me-2 text-primary"></i>Lessons:</span>
                        <strong><?php echo $lesson_count; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="bi bi-people me-2 text-success"></i>Enrollments:</span>
                        <strong><?php echo $enrollment_count; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="bi bi-trophy me-2 text-warning"></i>Completions:</span>
                        <strong><?php echo $completion_count; ?></strong>
                    </div>
                    <?php if ($enrollment_count > 0): ?>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar bg-success" 
                                 style="width: <?php echo ($completion_count / $enrollment_count) * 100; ?>%"></div>
                        </div>
                        <small class="text-muted">
                            <?php echo round(($completion_count / $enrollment_count) * 100); ?>% completion rate
                        </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Form Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                <i class="bi bi-clock-history me-1"></i>
                                Created: <?php echo date('M d, Y H:i', strtotime($course['created_at'])); ?>
                                <?php if ($course['updated_at'] != $course['created_at']): ?>
                                    <br>
                                    <i class="bi bi-pencil-square me-1"></i>
                                    Last updated: <?php echo date('M d, Y H:i', strtotime($course['updated_at'])); ?>
                                <?php endif; ?>
                            </small>
                        </div>
                        <div>
                            <a href="manage_courses" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" form="courseForm" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Include necessary scripts -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Summernote
    $('.summernote').summernote({
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'italic', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onImageUpload: function(files) {
                // Handle image upload
                for(let i = 0; i < files.length; i++) {
                    uploadImage(files[i]);
                }
            }
        }
    });
    
    // Auto-generate slug from title
    $('#title').on('blur', function() {
        if ($('#slug').val().trim() === '') {
            let slug = $(this).val()
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            $('#slug').val(slug);
        }
    });
    
    // Thumbnail preview
    $('#thumbnail').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#thumbnailPreview').removeClass('d-none');
                $('#thumbnailPreview img').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });
});

// Image upload function for Summernote
function uploadImage(file) {
    let formData = new FormData();
    formData.append("file", file);
    
    $.ajax({
        url: '../api/upload-editor-image',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(url) {
            $('.summernote').summernote('insertImage', url);
        },
        error: function() {
            alert('Image upload failed');
        }
    });
}

// Confirm before leaving with unsaved changes
let formChanged = false;
$('#courseForm input, #courseForm textarea, #courseForm select').on('change', function() {
    formChanged = true;
});

$('.summernote').on('summernote.change', function() {
    formChanged = true;
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
    }
});

$('#courseForm').on('submit', function() {
    formChanged = false;
});
</script>

<style>
.note-editor.note-frame {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}
.note-toolbar {
    background: #f8fafc;
    border-radius: 12px 12px 0 0;
}
.current-thumbnail {
    border: 2px dashed #e2e8f0;
    padding: 10px;
    border-radius: 8px;
}
</style>


@endverbatim

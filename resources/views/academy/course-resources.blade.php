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

.resources-header {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.03);
    margin-bottom: 1.5rem;
}

.resource-card {
    transition: all 0.3s ease;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    background: white;
    overflow: hidden;
}

.resource-card:hover {
    border-color: var(--kiboko-blue);
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.resource-preview {
    cursor: pointer;
    transition: all 0.3s ease;
}

.resource-preview:hover {
    background: #f8fafc;
}

.lesson-badge {
    background: linear-gradient(135deg, var(--kiboko-blue) 0%, var(--kiboko-green) 100%);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
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

.filter-badge {
    cursor: pointer;
    transition: all 0.2s ease;
}

.filter-badge:hover {
    transform: scale(1.05);
}

.filter-badge.active {
    background: var(--kiboko-blue) !important;
    color: white !important;
}
</style>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Header -->
    <div class="resources-header">
        <div class="row align-items-center">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="my-courses" class="text-decoration-none">My Courses</a></li>
                        <li class="breadcrumb-item"><a href="course-player?id=<?php echo $course_id; ?>" class="text-decoration-none"><?php echo htmlspecialchars($course['title']); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Resources</li>
                    </ol>
                </nav>
                <h2 class="mb-1">Course Resources</h2>
                <p class="text-muted mb-0">All downloadable materials for <?php echo htmlspecialchars($course['title']); ?></p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" onclick="downloadAllResources()" <?php echo empty($all_resources) ? 'disabled' : ''; ?>>
                    <i class="bi bi-cloud-download"></i> Download All
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10">
                    <i class="bi bi-files text-primary"></i>
                </div>
                <div>
                    <h4 class="mb-0"><?php echo count($all_resources); ?></h4>
                    <small class="text-muted">Total Files</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info bg-opacity-10">
                    <i class="bi bi-hdd-stack text-info"></i>
                </div>
                <div>
                    <h4 class="mb-0"><?php echo ka_format_file_size($total_size); ?></h4>
                    <small class="text-muted">Total Size</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10">
                    <i class="bi bi-file-earmark-pdf text-success"></i>
                </div>
                <div>
                    <h4 class="mb-0"><?php echo count(array_filter($all_resources, fn($r) => $r['file_ext'] === 'pdf')); ?></h4>
                    <small class="text-muted">PDF Files</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10">
                    <i class="bi bi-file-earmark-image text-warning"></i>
                </div>
                <div>
                    <h4 class="mb-0"><?php echo count(array_filter($all_resources, fn($r) => in_array($r['file_ext'], ['jpg','jpeg','png','gif']))); ?></h4>
                    <small class="text-muted">Images</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter by Type</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge bg-light text-dark p-2 filter-badge active" data-filter="all">All</span>
                        <span class="badge bg-light text-dark p-2 filter-badge" data-filter="pdf">PDF</span>
                        <span class="badge bg-light text-dark p-2 filter-badge" data-filter="image">Images</span>
                        <span class="badge bg-light text-dark p-2 filter-badge" data-filter="doc">Documents</span>
                        <span class="badge bg-light text-dark p-2 filter-badge" data-filter="video">Videos</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sort by</label>
                    <select class="form-select" id="sortSelect">
                        <option value="lesson">Lesson Order</option>
                        <option value="name">File Name</option>
                        <option value="type">File Type</option>
                        <option value="size">File Size</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search files...">
                </div>
            </div>
        </div>
    </div>

    <!-- Resources by Lesson -->
    <div class="resources-container">
        <?php if (empty($all_resources)): ?>
            <div class="text-center py-5">
                <i class="bi bi-paperclip display-1 text-muted"></i>
                <h4 class="mt-3">No Resources Available</h4>
                <p class="text-muted">This course doesn't have any downloadable resources yet.</p>
                <a href="course-player?id=<?php echo $course_id; ?>" class="btn btn-primary mt-2">
                    <i class="bi bi-arrow-left"></i> Back to Course
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($lessons_with_resources as $lesson): 
                $attachments = json_decode($lesson['attachments'], true);
            ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <span class="lesson-badge me-2">Lesson <?php echo $lesson['order_number']; ?></span>
                            <?php echo htmlspecialchars($lesson['lesson_title']); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3" data-lesson="<?php echo $lesson['order_number']; ?>">
                            <?php foreach ($attachments as $file): 
                                $file_path = 'assets/uploads/attachments/' . $file;
                                $file_size = file_exists($file_path) ? filesize($file_path) : 0;
                                $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                $file_icon = ka_file_icon($file_ext);
                                $file_date = file_exists($file_path) ? date("M d, Y", filemtime($file_path)) : 'Unknown';
                            ?>
                                <div class="col-md-6 resource-item" 
                                     data-filename="<?php echo strtolower($file); ?>"
                                     data-filetype="<?php echo $file_ext; ?>"
                                     data-filesize="<?php echo $file_size; ?>"
                                     data-lesson="<?php echo $lesson['order_number']; ?>">
                                    <div class="resource-card">
                                        <div class="resource-preview p-3" onclick="previewResource('<?php echo $file_path; ?>', '<?php echo $file_ext; ?>')">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="bi bi-<?php echo $file_icon; ?> fs-1 
                                                        <?php 
                                                        echo $file_ext === 'pdf' ? 'text-danger' : 
                                                            (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif']) ? 'text-success' : 
                                                            (in_array($file_ext, ['mp4', 'webm']) ? 'text-info' : 'text-primary')); 
                                                        ?>">
                                                    </i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($file); ?></h6>
                                                    <div class="d-flex gap-3 small text-muted">
                                                        <span><i class="bi bi-file-earmark"></i> <?php echo strtoupper($file_ext); ?></span>
                                                        <span><i class="bi bi-hdd"></i> <?php echo ka_format_file_size($file_size); ?></span>
                                                        <span><i class="bi bi-calendar"></i> <?php echo $file_date; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white border-top p-2">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="<?php echo $file_path; ?>" class="btn btn-sm btn-outline-primary" download>
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                                <?php if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'pdf'])): ?>
                                                    <button class="btn btn-sm btn-outline-secondary" 
                                                            onclick="previewResource('<?php echo $file_path; ?>', '<?php echo $file_ext; ?>')">
                                                        <i class="bi bi-eye"></i> Preview
                                                    </button>
                                                <?php endif; ?>
                                                <button class="btn btn-sm btn-outline-info" 
                                                        onclick="shareResource('<?php echo addslashes($file); ?>')">
                                                    <i class="bi bi-share"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<!-- Resource Preview Modal -->
<div class="modal fade" id="resourcePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resource Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="resourcePreviewContent">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="downloadFromPreview" class="btn btn-primary" download>Download</a>
            </div>
        </div>
    </div>
</div>

<!-- Share Resource Modal -->
<div class="modal fade" id="shareResourceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Share Resource</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Share this resource with others:</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="shareLink" readonly>
                    <button class="btn btn-outline-primary" onclick="copyShareLink()">Copy</button>
                </div>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-outline-primary" onclick="shareVia('email')">
                        <i class="bi bi-envelope"></i> Email
                    </button>
                    <button class="btn btn-outline-info" onclick="shareVia('whatsapp')">
                        <i class="bi bi-whatsapp"></i> WhatsApp
                    </button>
                    <button class="btn btn-outline-secondary" onclick="shareVia('telegram')">
                        <i class="bi bi-telegram"></i> Telegram
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentFilter = 'all';
let currentSort = 'lesson';
let resources = <?php echo json_encode($all_resources); ?>;

// Filter functionality
document.querySelectorAll('.filter-badge').forEach(badge => {
    badge.addEventListener('click', function() {
        document.querySelectorAll('.filter-badge').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.dataset.filter;
        filterResources();
    });
});

// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    filterResources();
});

// Sort functionality
document.getElementById('sortSelect').addEventListener('change', function() {
    currentSort = this.value;
    filterResources();
});

function filterResources() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const items = document.querySelectorAll('.resource-item');
    
    items.forEach(item => {
        const filename = item.dataset.filename;
        const filetype = item.dataset.filetype;
        let showByFilter = true;
        
        if (currentFilter !== 'all') {
            if (currentFilter === 'pdf') {
                showByFilter = filetype === 'pdf';
            } else if (currentFilter === 'image') {
                showByFilter = ['jpg', 'jpeg', 'png', 'gif'].includes(filetype);
            } else if (currentFilter === 'doc') {
                showByFilter = ['doc', 'docx', 'txt', 'pdf'].includes(filetype);
            } else if (currentFilter === 'video') {
                showByFilter = ['mp4', 'webm'].includes(filetype);
            }
        }
        
        const showBySearch = filename.includes(searchTerm);
        
        if (showByFilter && showBySearch) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
    
    // Sort after filtering
    sortResources();
}

function sortResources() {
    const container = document.querySelector('.resources-container');
    const lessons = Array.from(document.querySelectorAll('.card.border-0.shadow-sm.mb-4'));
    
    if (currentSort === 'lesson') {
        lessons.sort((a, b) => {
            const aLesson = parseInt(a.querySelector('.card-header .lesson-badge').textContent.replace('Lesson ', ''));
            const bLesson = parseInt(b.querySelector('.card-header .lesson-badge').textContent.replace('Lesson ', ''));
            return aLesson - bLesson;
        });
    } else {
        // For other sorts, we need to reorganize across lessons
        // This is more complex - for now just refresh the page with sort parameter
        // You could implement AJAX reload here
    }
    
    // Reorder lessons
    container.innerHTML = '';
    lessons.forEach(lesson => container.appendChild(lesson));
}

function previewResource(filePath, fileExt) {
    const modal = new bootstrap.Modal(document.getElementById('resourcePreviewModal'));
    const content = document.getElementById('resourcePreviewContent');
    const downloadBtn = document.getElementById('downloadFromPreview');
    
    downloadBtn.href = filePath;
    
    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
        content.innerHTML = `<img src="${filePath}" class="img-fluid" alt="Preview" style="max-height: 70vh;">`;
    } else if (fileExt === 'pdf') {
        content.innerHTML = `<iframe src="${filePath}" style="width:100%; height:70vh;" frameborder="0"></iframe>`;
    } else {
        content.innerHTML = `
            <div class="py-4">
                <i class="bi bi-file-earmark display-1 text-muted"></i>
                <p class="mt-3">Preview not available for this file type.</p>
            </div>
        `;
    }
    
    modal.show();
}

function shareResource(fileName) {
    const modal = new bootstrap.Modal(document.getElementById('shareResourceModal'));
    const linkInput = document.getElementById('shareLink');
    linkInput.value = window.location.href + '&resource=' + encodeURIComponent(fileName);
    modal.show();
}

function copyShareLink() {
    const linkInput = document.getElementById('shareLink');
    linkInput.select();
    document.execCommand('copy');
    
    Swal.fire({
        icon: 'success',
        title: 'Copied!',
        text: 'Link copied to clipboard',
        timer: 1500,
        showConfirmButton: false
    });
}

function shareVia(platform) {
    const link = document.getElementById('shareLink').value;
    let url = '';
    
    switch(platform) {
        case 'email':
            url = `mailto:?subject=Check out this resource&body=I found this resource: ${link}`;
            break;
        case 'whatsapp':
            url = `https://wa.me/?text=${encodeURIComponent('Check out this resource: ' + link)}`;
            break;
        case 'telegram':
            url = `https://t.me/share/url?url=${encodeURIComponent(link)}`;
            break;
    }
    
    if (url) window.open(url, '_blank');
}

function downloadAllResources() {
    if (resources.length === 0) return;
    
    Swal.fire({
        title: 'Download All Resources?',
        html: `This will download <strong>${resources.length}</strong> files individually.<br>
               Total size: <?php echo ka_format_file_size($total_size); ?>`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Download All',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            resources.forEach(resource => {
                window.open(resource.file_path, '_blank');
            });
        }
    });
}
</script>


@endverbatim

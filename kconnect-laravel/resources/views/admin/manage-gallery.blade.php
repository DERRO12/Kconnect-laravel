<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Manager - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        
        body {
            background: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-header h2 {
            font-size: 2rem;
            font-weight: 800;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }
        
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.2);
            color: white;
        }
        
        .btn-outline-gradient {
            background: white;
            border: 2px solid transparent;
            border-image: var(--grad);
            border-image-slice: 1;
            color: #009ada;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-outline-gradient:hover {
            background: var(--grad);
            color: white;
        }
        
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .category-pills {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .category-pill {
            padding: 8px 16px;
            border-radius: 30px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .category-pill:hover,
        .category-pill.active {
            background: var(--grad);
            color: white;
            border-color: transparent;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .gallery-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s;
            position: relative;
        }
        
        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,154,218,0.1);
            border-color: #009ada;
        }
        
        .card-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        
        .gallery-card:hover .card-image img {
            transform: scale(1.05);
        }
        
        .source-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            background: rgba(255,255,255,0.9);
            color: #009ada;
            z-index: 1;
        }
        
        .status-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            z-index: 1;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .card-content {
            padding: 15px;
        }
        
        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: #1e293b;
        }
        
        .card-category {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 10px;
        }
        
        .card-category i {
            margin-right: 5px;
            color: #009ada;
        }
        
        .card-description {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 15px;
            line-height: 1.5;
            max-height: 60px;
            overflow: hidden;
        }
        
        .card-actions {
            display: flex;
            gap: 5px;
            border-top: 1px solid #eef2f6;
            padding-top: 15px;
        }
        
        .action-btn {
            flex: 1;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #475569;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        
        .action-btn:hover {
            background: var(--grad);
            color: white;
            border-color: transparent;
        }
        
        .action-btn.delete:hover {
            background: #dc3545;
        }
        
        .action-btn.view:hover {
            background: #009ada;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .modal-content {
            border: none;
            border-radius: 15px;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border-bottom: 1px solid #eef2f6;
        }
        
        .form-control, .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #009ada;
            box-shadow: 0 0 0 3px rgba(0,154,218,0.1);
        }
        
        .drop-zone {
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #f8fafc;
        }
        
        .drop-zone:hover,
        .drop-zone.dragover {
            border-color: #009ada;
            background: #f0f9ff;
        }
        
        .drop-zone i {
            font-size: 3rem;
            color: #009ada;
            margin-bottom: 15px;
        }
        
        .thumbnail-preview {
            width: 100%;
            max-height: 200px;
            object-fit: contain;
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid #eef2f6;
        }
        
        .message-alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .message-alert.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .message-alert.danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .gallery-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="page-header">
            <h2><i class="fas fa-images me-2"></i>Gallery Manager</h2>
            <button class="btn-gradient" onclick="openAddModal()">
                <i class="fas fa-plus me-2"></i>Add Gallery Item
            </button>
        </div>
        
        <!-- Message Alert -->
        @if(isset($flashMessage))
        <div class="message-alert {!! $flashType !!}">
            <span><i class="fas fa-info-circle me-2"></i>{!! $flashMessage !!}</span>
            <button class="btn-close float-end" onclick="this.parentElement.remove()"></button>
        </div>
        @endif
        
        <!-- Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stat-number">{!! $total_items !!}</div>
                    <div class="text-muted">Total Items</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stat-number">{!! $active_items !!}</div>
                    <div class="text-muted">Active Items</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stat-number">{!! $total_categories !!}</div>
                    <div class="text-muted">Categories</div>
                </div>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-3"><i class="fas fa-filter me-2 text-gradient"></i>Filter by Category</h5>
                    <div class="category-pills">
                        <span class="category-pill active" data-category="all">All</span>
                        @foreach($categories as $cat)
                        <span class="category-pill" data-category="{{ $cat['name'] }}">
                            {{ $cat['name'] }}
                        </span>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search gallery items...">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gallery Grid -->
        <div class="gallery-grid" id="galleryGrid">
            @if(count($gallery_items) > 0)
                @foreach($gallery_items as $item)
                @php
                    $source_icons = [
                        'onedrive' => 'fa-cloud',
                        'googledrive' => 'fa-google-drive',
                        'dropbox' => 'fa-dropbox',
                        'youtube' => 'fa-youtube',
                        'vimeo' => 'fa-vimeo',
                        'other' => 'fa-link'
                    ];
                    $icon = $source_icons[$item['source_type']] ?? 'fa-link';
                @endphp
                <div class="gallery-item" data-category="{{ $item['category'] }}" 
                     data-title="{!! strtolower(htmlspecialchars($item['title'])) !!}" 
                     data-description="{!! strtolower(htmlspecialchars($item['description'] ?? '')) !!}">
                    <div class="gallery-card">
                        <div class="card-image">
                            <img src="../{{ $item['thumbnail'] }}" alt="{{ $item['title'] }}">
                            <span class="source-badge">
                                <i class="fab {!! $icon !!} me-1"></i>
                                {!! ucfirst($item['source_type']) !!}
                            </span>
                            <span class="status-badge {!! $item['status'] ? 'status-active' : 'status-inactive' !!}">
                                {!! $item['status'] ? 'Active' : 'Inactive' !!}
                            </span>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title">{{ $item['title'] }}</h3>
                            <div class="card-category">
                                <i class="fas fa-folder"></i>
                                {{ $item['category'] }}
                            </div>
                            @if($item['description'])
                            <p class="card-description">{{ $item['description'] }}</p>
                            @endif
                            <div class="card-actions">
                                <a href="../{{ $item['external_url'] }}" target="_blank" class="action-btn view" title="View Gallery">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <a href="?edit={!! $item['id'] !!}" class="action-btn" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?toggle={!! $item['id'] !!}" class="action-btn" title="Toggle Status" onclick="return confirm('Toggle status of this item?')">
                                    <i class="fas fa-power-off"></i>
                                </a>
                                <a href="?delete={!! $item['id'] !!}" class="action-btn delete" title="Delete" onclick="return confirm('Delete this gallery item?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="display-1 mb-4 text-muted">🖼️</div>
                    <h3 class="text-muted">No gallery items yet</h3>
                    <p class="text-muted">Click "Add Gallery Item" to create your first gallery entry.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Add/Edit Modal -->
    <div class="modal fade" id="galleryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-plus-circle me-2 text-gradient"></i>Add Gallery Item
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data" id="galleryForm">
@csrf
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="edit_id" value="{!! $edit_item ? $edit_item['id'] : 0 !!}">
                        
                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" value="{!! $edit_item ? htmlspecialchars($edit_item['title']) : '' !!}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{!! $edit_item ? htmlspecialchars($edit_item['description'] ?? '') : '' !!}</textarea>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">External URL <span class="text-danger">*</span></label>
                                <input type="url" name="external_url" id="external_url" class="form-control" 
                                       value="{!! $edit_item ? htmlspecialchars($edit_item['external_url']) : '' !!}" 
                                       placeholder="https://onedrive.com/your-gallery-link" required>
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Paste the full OneDrive, Google Drive, or external gallery link
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Source Type</label>
                                <select name="source_type" id="source_type" class="form-select">
                                    <option value="onedrive" {!! ($edit_item && $edit_item['source_type'] == 'onedrive') ? 'selected' : '' !!}>OneDrive</option>
                                    <option value="googledrive" {!! ($edit_item && $edit_item['source_type'] == 'googledrive') ? 'selected' : '' !!}>Google Drive</option>
                                    <option value="dropbox" {!! ($edit_item && $edit_item['source_type'] == 'dropbox') ? 'selected' : '' !!}>Dropbox</option>
                                    <option value="youtube" {!! ($edit_item && $edit_item['source_type'] == 'youtube') ? 'selected' : '' !!}>YouTube</option>
                                    <option value="vimeo" {!! ($edit_item && $edit_item['source_type'] == 'vimeo') ? 'selected' : '' !!}>Vimeo</option>
                                    <option value="other" {!! ($edit_item && $edit_item['source_type'] == 'other') ? 'selected' : '' !!}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Category</label>
                                <select name="category" id="category" class="form-select">
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat['name'] }}" 
                                        {!! ($edit_item && $edit_item['category'] == $cat['name']) ? 'selected' : '' !!}>
                                        {{ $cat['name'] }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Thumbnail Image {!! !$edit_item ? '<span class="text-danger">*</span>' : '' !!}</label>
                            @if($edit_item && $edit_item['thumbnail'])
                            <div class="mb-2">
                                <img src="../{!! $edit_item['thumbnail'] !!}" class="thumbnail-preview" id="currentThumbnail">
                            </div>
                            @endif
                            
                            <div class="drop-zone" id="dropZone">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <h5>Drag & drop thumbnail image here</h5>
                                <p class="text-muted mb-2">or click to browse</p>
                                <input type="file" name="thumbnail" id="thumbnail" class="d-none" accept="image/*" {!! !$edit_item ? 'required' : '' !!}>
                                <button type="button" class="btn btn-outline-gradient btn-sm" onclick="document.getElementById('thumbnail').click()">
                                    Browse Images
                                </button>
                            </div>
                            <div class="file-info mt-2" id="fileInfo" style="display: none;">
                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                    <i class="fas fa-image text-gradient me-2"></i>
                                    <span id="fileName"></span>
                                    <button type="button" class="btn-close ms-auto" onclick="clearFile()"></button>
                                </div>
                                <img id="imagePreview" class="thumbnail-preview mt-2" style="display: none;">
                            </div>
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Recommended size: 800x600px. Max size: 2MB. Formats: JPG, PNG, GIF, WEBP
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-save me-2"></i>{!! $edit_item ? 'Update' : 'Save' !!} Gallery Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let galleryModal;
        
        document.addEventListener('DOMContentLoaded', function() {
            galleryModal = new bootstrap.Modal(document.getElementById('galleryModal'));
            
            @if(isset($get['edit']) && $edit_item)
            galleryModal.show();
            @endif
            
            // Initialize drag & drop
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('thumbnail');
            const fileInfo = document.getElementById('fileInfo');
            const fileName = document.getElementById('fileName');
            const imagePreview = document.getElementById('imagePreview');
            
            if (dropZone) {
                dropZone.addEventListener('click', function() {
                    fileInput.click();
                });
                
                dropZone.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.classList.add('dragover');
                });
                
                dropZone.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    this.classList.remove('dragover');
                });
                
                dropZone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.classList.remove('dragover');
                    
                    const files = e.dataTransfer.files;
                    if (files.length > 0 && files[0].type.startsWith('image/')) {
                        fileInput.files = files;
                        updateFileInfo(files[0]);
                    } else {
                        alert('Please drop an image file');
                    }
                });
            }
            
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        updateFileInfo(this.files[0]);
                    }
                });
            }
            
            // Category filter
            document.querySelectorAll('.category-pill').forEach(pill => {
                pill.addEventListener('click', function() {
                    document.querySelectorAll('.category-pill').forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    filterGallery();
                });
            });
            
            // Search input
            document.getElementById('searchInput').addEventListener('keyup', filterGallery);
        });
        
        function updateFileInfo(file) {
            const fileInfo = document.getElementById('fileInfo');
            const fileName = document.getElementById('fileName');
            const imagePreview = document.getElementById('imagePreview');
            const dropZone = document.getElementById('dropZone');
            
            fileName.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            
            // Show image preview
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
            
            dropZone.style.display = 'none';
            fileInfo.style.display = 'block';
        }
        
        function clearFile() {
            const fileInput = document.getElementById('thumbnail');
            const fileInfo = document.getElementById('fileInfo');
            const dropZone = document.getElementById('dropZone');
            const imagePreview = document.getElementById('imagePreview');
            
            fileInput.value = '';
            fileInfo.style.display = 'none';
            dropZone.style.display = 'block';
            imagePreview.style.display = 'none';
        }
        
        function filterGallery() {
            const selectedCategory = document.querySelector('.category-pill.active').dataset.category;
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const items = document.querySelectorAll('.gallery-item');
            
            items.forEach(item => {
                const category = item.dataset.category;
                const title = item.dataset.title || '';
                const description = item.dataset.description || '';
                
                const matchesCategory = selectedCategory === 'all' || category === selectedCategory;
                const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
                
                item.style.display = matchesCategory && matchesSearch ? 'block' : 'none';
            });
        }
        
        function openAddModal() {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle me-2 text-gradient"></i>Add Gallery Item';
            document.getElementById('edit_id').value = '0';
            document.getElementById('title').value = '';
            document.getElementById('description').value = '';
            document.getElementById('external_url').value = '';
            document.getElementById('source_type').value = 'onedrive';
            document.getElementById('category').value = 'General';
            document.getElementById('thumbnail').required = true;
            clearFile();
            galleryModal.show();
        }
        
        // Auto-hide messages
        setTimeout(() => {
            document.querySelectorAll('.message-alert').forEach(el => {
                if (el) el.remove();
            });
        }, 3000);
    </script>
</body>
</html>
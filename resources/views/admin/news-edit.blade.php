<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit News - Kiboko Admin</title>
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
        
        .sidebar {
            background: white;
            height: 100vh;
            position: fixed;
            width: 260px;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            overflow-y: auto;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 20px 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        }
        
        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 3px rgba(0,154,218,0.1);
        }
        
        textarea.form-control {
            min-height: 200px;
        }
        
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.2);
            color: white;
        }
        
        .btn-outline-secondary {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-outline-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        
        .btn-outline-danger {
            border: 1px solid #dc3545;
            color: #dc3545;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            background: transparent;
            cursor: pointer;
        }
        
        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
        }
        
        .image-preview {
            max-width: 200px;
            max-height: 150px;
            border-radius: 10px;
            border: 2px dashed #e2e8f0;
            padding: 5px;
            object-fit: cover;
        }
        
        .featured-image-container {
            position: relative;
            display: inline-block;
        }
        
        .existing-images-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .existing-image-card {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px;
            background: #f8fafc;
            position: relative;
            width: 200px;
            transition: all 0.3s;
        }
        
        .existing-image-card:hover {
            border-color: var(--kiboko-blue);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .existing-image-card.marked-delete {
            border-color: #dc3545;
            background: #fff5f5;
            opacity: 0.6;
        }
        
        .existing-image-card img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 8px;
        }
        
        .existing-image-card .delete-checkbox {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #dc3545;
        }
        
        .existing-image-card .caption-input {
            width: 100%;
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 0.8rem;
        }
        
        .additional-images-section {
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            background: #f8fafc;
        }
        
        .new-image-item {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            position: relative;
        }
        
        .new-image-item .remove-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .section-divider {
            border-top: 2px solid #f1f5f9;
            margin: 30px 0;
            padding-top: 20px;
        }
        
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .section-title i {
            color: var(--kiboko-blue);
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="page-header">
            <div>
                <h4 class="fw-bold mb-1">Edit News Post</h4>
                <p class="text-muted mb-0">Update existing news article</p>
            </div>
            <a href="{{ url('admin/news-events') }}" class="btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        
        @if(isset($error))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i> {!! $error !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        @if(isset($success))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i> {!! $success !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        <div class="form-container">
            <form method="POST" action="" enctype="multipart/form-data" id="editNewsForm">
@csrf
                
                <!-- Basic Information -->
                <div class="mb-4">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" required 
                           value="{{ $newsData['title'] }}"
                           placeholder="e.g., Company Announcement">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Announcements" {!! $newsData['category'] == 'Announcements' ? 'selected' : '' !!}>Announcements</option>
                            <option value="Events" {!! $newsData['category'] == 'Events' ? 'selected' : '' !!}>Events</option>
                            <option value="People" {!! $newsData['category'] == 'People' ? 'selected' : '' !!}>People</option>
                            <option value="Milestones" {!! $newsData['category'] == 'Milestones' ? 'selected' : '' !!}>Milestones</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="1" {!! $newsData['status'] ? 'selected' : '' !!}>Active (visible)</option>
                            <option value="0" {!! !$newsData['status'] ? 'selected' : '' !!}>Inactive (hidden)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Featured Image Section -->
                <div class="mb-4">
                    <label class="form-label">
                        <i class="fas fa-star me-1" style="color: #f59e0b;"></i> Featured Image
                    </label>
                    
                    @if(!empty($newsData['featured_image']) && file_exists(kc_public_file('../uploads/news/' . $newsData['featured_image'], 'admin')))
                    <div class="featured-image-container mb-3">
                        <img src="{{ asset('uploads') }}/news/{{ $newsData['featured_image'] }}" 
                             class="image-preview" alt="Current featured image" id="currentFeaturedImg">
                        <div class="mt-2">
                            <label class="text-danger small" style="cursor: pointer;">
                                <input type="checkbox" name="remove_featured" value="1" id="removeFeatured" 
                                       onchange="toggleRemoveFeatured()"> 
                                Remove current featured image
                            </label>
                        </div>
                    </div>
                    @endif
                    
                    <div id="newFeaturedUpload" {!! (!empty($newsData['featured_image'])) ? 'style="display:none;"' : '' !!}>
                        <input type="file" class="form-control" name="featured_image" id="featured_image" 
                               accept="image/*" onchange="previewImage(this)">
                        <small class="text-muted d-block mt-1">
                            Upload a new featured image (JPG, PNG, GIF, WebP)
                        </small>
                        <div id="imagePreviewContainer" style="display: none; margin-top: 10px;">
                            <img id="imagePreview" class="image-preview" src="#" alt="Preview">
                        </div>
                    </div>
                </div>
                
                <div class="section-divider"></div>
                
                <!-- Existing Additional Images -->
                @if(!empty($existingImages))
                <div class="mb-4">
                    <div class="section-title">
                        <i class="fas fa-images"></i>
                        Existing Gallery Images 
                        <span class="badge bg-secondary ms-2">{!! count($existingImages) !!}</span>
                    </div>
                    <small class="text-muted d-block mb-3">
                        Check the box (top-right corner) to mark an image for deletion. Edit captions below each image.
                    </small>
                    
                    <div class="existing-images-grid">
                        @foreach($existingImages as $img)
                        <div class="existing-image-card" id="imgCard_{!! $img['id'] !!}">
                            <input type="checkbox" name="delete_images[]" value="{!! $img['id'] !!}" 
                                   class="delete-checkbox" 
                                   onchange="toggleDeleteMark(this, {!! $img['id'] !!})"
                                   title="Mark for deletion">
                            <img src="{{ asset('uploads') }}/news/{{ $img['image_path'] }}" 
                                 alt="Gallery image">
                            <input type="text" name="existing_captions[{!! $img['id'] !!}]" 
                                   class="caption-input" 
                                   placeholder="Image caption..." 
                                   value="{{ $img['caption'] ?? '' }}">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Add New Additional Images -->
                <div class="mb-4">
                    <div class="section-title">
                        <i class="fas fa-plus-circle"></i>
                        Add New Gallery Images
                    </div>
                    
                    <div class="additional-images-section">
                        <div id="newImagesContainer"></div>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addNewImageField()">
                                <i class="fas fa-plus me-2"></i>Add Image
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">
                            You can add multiple images to the news gallery. Each image can have an optional caption.
                        </small>
                    </div>
                </div>
                
                <div class="section-divider"></div>
                
                <!-- Author Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Author Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="author_name" required 
                               value="{{ $newsData['author_name'] }}"
                               placeholder="e.g., John Doe">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Author Role</label>
                        <input type="text" class="form-control" name="author_role" 
                               value="{{ $newsData['author_role'] ?? '' }}"
                               placeholder="e.g., Admin, HR Manager">
                    </div>
                </div>
                
                <!-- Content -->
                <div class="mb-4">
                    <label class="form-label">Content <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="content" required 
                              placeholder="Write your news content here...">{{ $newsData['content'] }}</textarea>
                </div>
                
                <!-- Submit Buttons -->
                <div class="d-flex gap-3">
                    <button type="submit" class="btn-gradient">
                        <i class="fas fa-save"></i> Update News
                    </button>
                    <a href="{{ url('admin/news-events') }}" class="btn-outline-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let newImageCount = 0;
        
        function previewImage(input) {
            const previewContainer = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function toggleRemoveFeatured() {
            const checkbox = document.getElementById('removeFeatured');
            const uploadDiv = document.getElementById('newFeaturedUpload');
            const currentImg = document.getElementById('currentFeaturedImg');
            
            if (checkbox.checked) {
                uploadDiv.style.display = 'block';
                if (currentImg) {
                    currentImg.style.opacity = '0.4';
                }
            } else {
                uploadDiv.style.display = 'none';
                if (currentImg) {
                    currentImg.style.opacity = '1';
                }
            }
        }
        
        function toggleDeleteMark(checkbox, imgId) {
            const card = document.getElementById('imgCard_' + imgId);
            if (checkbox.checked) {
                card.classList.add('marked-delete');
            } else {
                card.classList.remove('marked-delete');
            }
        }
        
        function addNewImageField() {
            newImageCount++;
            const container = document.getElementById('newImagesContainer');
            
            const imageItem = document.createElement('div');
            imageItem.className = 'new-image-item';
            imageItem.id = 'newImageItem_' + newImageCount;
            imageItem.innerHTML = `
                <button type="button" class="remove-btn" onclick="removeNewImageField('newImageItem_${newImageCount}')" title="Remove this image">
                    <i class="fas fa-times"></i>
                </button>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Image File</label>
                        <input type="file" class="form-control" name="additional_images[]" 
                               accept="image/*" onchange="previewNewImage(this, 'newPreview_${newImageCount}')">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Caption (optional)</label>
                        <input type="text" class="form-control" name="new_captions[]" 
                               placeholder="Image caption...">
                    </div>
                </div>
                <div class="mt-2">
                    <img id="newPreview_${newImageCount}" 
                         style="display: none; max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid #e2e8f0; object-fit: cover;">
                </div>
            `;
            
            container.appendChild(imageItem);
        }
        
        function removeNewImageField(itemId) {
            const item = document.getElementById(itemId);
            if (item) {
                item.style.transition = 'all 0.3s';
                item.style.opacity = '0';
                item.style.transform = 'scale(0.9)';
                setTimeout(() => item.remove(), 300);
            }
        }
        
        function previewNewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add News - Kiboko Admin</title>
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
            min-height: 150px;
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
        
        .image-preview {
            margin-top: 15px;
            max-width: 200px;
            max-height: 150px;
            border-radius: 10px;
            border: 2px dashed #e2e8f0;
            padding: 5px;
        }
        
        .additional-images-container {
            border: 2px dashed #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            background: #f8fafc;
            margin-bottom: 15px;
        }
        
        .image-preview-item {
            display: inline-block;
            position: relative;
            margin: 5px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #e2e8f0;
        }
        
        .image-preview-item img {
            width: 120px;
            height: 90px;
            object-fit: cover;
        }
        
        .image-preview-item .remove-btn {
            position: absolute;
            top: 2px;
            right: 2px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .image-caption-input {
            width: 100%;
            margin-top: 5px;
            padding: 5px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="page-header">
            <div>
                <h4 class="fw-bold mb-1">Add News Post</h4>
                <p class="text-muted mb-0">Create a new news article or announcement</p>
            </div>
            <a href="{{ url('admin/news-events') }}" class="btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        
        @if(isset($error))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i> {!! $error !!}
        </div>
        @endif
        
        @if(isset($success))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i> {!! $success !!}
        </div>
        @endif
        
        <div class="form-container">
            <form method="POST" action="" enctype="multipart/form-data">
@csrf
                <div class="mb-4">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" value="{{ is_string(request()->input('title')) ? request()->input('title') : '' }}" required placeholder="e.g., Company Announcement">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Announcements" @selected(request()->input('category') === 'Announcements')>Announcements</option>
                            <option value="Events" @selected(request()->input('category') === 'Events')>Events</option>
                            <option value="People" @selected(request()->input('category') === 'People')>People</option>
                            <option value="Milestones" @selected(request()->input('category') === 'Milestones')>Milestones</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Featured Image (Main)</label>
                        <input type="file" class="form-control" name="featured_image" id="featured_image" accept="image/*" onchange="previewImage(this)">
                        <small class="text-muted d-block mt-1">Main image for the news card thumbnail</small>
                        <div id="imagePreviewContainer" style="display: none;">
                            <img id="imagePreview" class="image-preview" src="#" alt="Preview">
                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearImage()">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Multiple Images Section -->
                <div class="mb-4">
                    <label class="form-label">
                        <i class="fas fa-images me-2"></i>Additional Images
                    </label>
                    <div class="additional-images-container">
                        <div id="additionalImagesList"></div>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addImageField()">
                                <i class="fas fa-plus me-2"></i>Add Image
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">You can add multiple images to the news post gallery</small>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Author Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="author_name" value="{{ is_string(request()->input('author_name')) ? request()->input('author_name') : '' }}" required placeholder="e.g., John Doe">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Author Role</label>
                        <input type="text" class="form-control" name="author_role" value="{{ is_string(request()->input('author_role')) ? request()->input('author_role') : '' }}" placeholder="e.g., Admin, HR Manager">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Content <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="content" required placeholder="Write your news content here...">{{ is_string(request()->input('content')) ? request()->input('content') : '' }}</textarea>
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="status" id="status" checked>
                        <label class="form-check-label" for="status">
                            Active (visible on homepage)
                        </label>
                    </div>
                </div>
                
                <div class="d-flex gap-3">
                    <button type="submit" class="btn-gradient">
                        <i class="fas fa-save"></i> Save News
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
        let imageFieldCount = 0;
        
        function previewImage(input) {
            const previewContainer = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function clearImage() {
            const fileInput = document.getElementById('featured_image');
            const previewContainer = document.getElementById('imagePreviewContainer');
            
            fileInput.value = '';
            previewContainer.style.display = 'none';
        }
        
        function addImageField() {
            imageFieldCount++;
            const container = document.getElementById('additionalImagesList');
            
            const imageItem = document.createElement('div');
            imageItem.className = 'additional-image-item mb-3 p-3 bg-white rounded border';
            imageItem.id = 'imageItem_' + imageFieldCount;
            imageItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <strong class="small">Image #${imageFieldCount}</strong>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeImageField('imageItem_${imageFieldCount}')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <input type="file" class="form-control" name="additional_images[]" accept="image/*" 
                               onchange="previewAdditionalImage(this, 'preview_${imageFieldCount}')">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control image-caption-input" name="image_captions[]" 
                               placeholder="Image caption (optional)">
                    </div>
                </div>
                <div class="mt-2">
                    <img id="preview_${imageFieldCount}" style="display: none; max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid #e2e8f0; object-fit: cover;">
                </div>
            `;
            
            container.appendChild(imageItem);
        }
        
        function removeImageField(itemId) {
            const item = document.getElementById(itemId);
            if (item) {
                item.remove();
            }
        }
        
        function previewAdditionalImage(input, previewId) {
            const preview = document.getElementById(previewId);
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
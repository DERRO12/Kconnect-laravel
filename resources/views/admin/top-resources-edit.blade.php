
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Resource - Kiboko Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
        
        .icon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            gap: 10px;
            max-height: 300px;
            overflow-y: auto;
            padding: 15px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        
        .icon-item {
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            padding: 8px;
        }
        
        .icon-item i {
            font-size: 1.5rem;
            color: #64748b;
            margin-bottom: 4px;
        }
        
        .icon-item span {
            font-size: 0.6rem;
            color: #94a3b8;
            text-align: center;
            word-break: break-word;
        }
        
        .icon-item:hover {
            border-color: var(--kiboko-blue);
            background: var(--grad-light);
            transform: translateY(-2px);
        }
        
        .icon-item.selected {
            border: 2px solid var(--kiboko-blue);
            background: var(--grad-light);
        }
        
        .icon-item.selected i {
            color: var(--kiboko-blue);
        }
        
        .color-preview {
            width: 100%;
            height: 40px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            margin-top: 8px;
        }
        
        .preview-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            position: sticky;
            top: 100px;
        }
        
        .resource-preview {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .resource-preview i {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="page-header">
            <div>
                <h4 class="fw-bold mb-1">Edit Resource</h4>
                <p class="text-muted mb-0">Update quick access resource</p>
            </div>
            <a href="top-resources" class="btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        
        @if (isset($error))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i> {!! $error !!}
        </div>
        @endif
        
        <div class="row">
            <div class="col-lg-8">
                <div class="form-container">
                    <form method="POST" action="" id="resourceForm">
                @csrf
                        <div class="mb-4">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" required placeholder="e.g., Budget Change" id="titleInput" value="{{ $resource['title'] }}">
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <input type="text" class="form-control" name="category" value="{{ $resource['category'] }}" placeholder="e.g., Forms & Templates">
                                <small class="text-muted">Group resources by category</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Sort Order</label>
                                <input type="number" class="form-control" name="sort_order" value="{!! $resource['sort_order'] !!}" min="0">
                                <small class="text-muted">Lower numbers appear first</small>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">URL/Link</label>
                            <input type="url" class="form-control" name="url" placeholder="https://example.com or # for placeholder" value="{{ $resource['url'] }}">
                            <small class="text-muted">Leave as # if not sure</small>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Description (Optional)</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Brief description of this resource">{{ $resource['description'] }}</textarea>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Open in</label>
                                <select class="form-select" name="target">
                                    <option value="_self" {!! $resource['target'] == '_self' ? 'selected' : '' !!}>Same Window</option>
                                    <option value="_blank" {!! $resource['target'] == '_blank' ? 'selected' : '' !!}>New Tab</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="status" id="status" {!! $resource['status'] ? 'checked' : '' !!}>
                                    <label class="form-check-label" for="status">Active (visible on homepage)</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Text Color</label>
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <input type="color" class="form-control form-control-color" name="color" id="colorInput" value="{!! $resource['color'] !!}" style="height: 46px;">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" value="{!! $resource['color'] !!}" id="colorText" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Background Color</label>
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <input type="color" class="form-control form-control-color" name="bg_color" id="bgColorInput" value="{!! $resource['bg_color'] !!}" style="height: 46px;">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" value="{!! $resource['bg_color'] !!}" id="bgColorText" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn-gradient">
                                <i class="fas fa-save"></i> Update Resource
                            </button>
                            <a href="top-resources" class="btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="preview-card" data-aos="fade-left">
                    <h6 class="fw-bold mb-3">Live Preview</h6>
                    <div class="text-center mb-4">
                        <div id="livePreview" class="resource-preview" style="background: {!! $resource['bg_color'] !!}; color: {!! $resource['color'] !!};">
                            <i class="fas {!! $resource['icon'] !!}"></i>
                            <span>{{ $resource['title'] }}</span>
                        </div>
                    </div>
                    <p class="small text-muted mb-2">This is how your resource will appear:</p>
                    <ul class="small text-muted">
                        <li>In the Top Resources section</li>
                        <li>As a pill-shaped button</li>
                        <li>With your chosen colors</li>
                        <li>With your selected icon</li>
                    </ul>
                    
                    <hr>
                    
                    <h6 class="fw-bold mb-3">Select Icon</h6>
                    <div class="icon-grid" id="iconGrid">
                        @foreach ($commonIcons as $icon => $label)
                        <div class="icon-item {!! $icon == $resource['icon'] ? 'selected' : '' !!}" data-icon="{!! $icon !!}">
                            <i class="fas {!! $icon !!}"></i>
                            <span>{!! $label !!}</span>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="icon" id="selectedIcon" value="{!! $resource['icon'] !!}">
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
        
        // Icon selection
        const iconItems = document.querySelectorAll('.icon-item');
        const selectedIconInput = document.getElementById('selectedIcon');
        const previewIcon = document.querySelector('#livePreview i');
        
        iconItems.forEach(item => {
            item.addEventListener('click', function() {
                iconItems.forEach(i => i.classList.remove('selected'));
                this.classList.add('selected');
                const icon = this.dataset.icon;
                selectedIconInput.value = icon;
                
                // Update preview
                previewIcon.className = 'fas ' + icon;
            });
        });
        
        // Color pickers
        const colorInput = document.getElementById('colorInput');
        const colorText = document.getElementById('colorText');
        const bgColorInput = document.getElementById('bgColorInput');
        const bgColorText = document.getElementById('bgColorText');
        const preview = document.getElementById('livePreview');
        
        colorInput.addEventListener('input', function() {
            colorText.value = this.value;
            preview.style.color = this.value;
        });
        
        bgColorInput.addEventListener('input', function() {
            bgColorText.value = this.value;
            preview.style.backgroundColor = this.value;
        });
        
        // Title input
        const titleInput = document.getElementById('titleInput');
        const previewTitle = document.querySelector('#livePreview span');
        
        titleInput.addEventListener('input', function() {
            previewTitle.textContent = this.value || 'Resource Title';
        });
        
        // Form submission with icon
        document.getElementById('resourceForm').addEventListener('submit', function(e) {
            const iconHidden = document.createElement('input');
            iconHidden.type = 'hidden';
            iconHidden.name = 'icon';
            iconHidden.value = selectedIconInput.value;
            this.appendChild(iconHidden);
        });
    </script>
</body>
</html>
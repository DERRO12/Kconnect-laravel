<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Carousel - Kiboko Intranet</title>
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
        }
        
        .sidebar-logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            padding: 20px 0;
            text-align: center;
            border-bottom: 2px solid #f1f5f9;
            margin-bottom: 20px;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 15px;
            color: #475569;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: var(--grad);
            color: white;
        }
        
        .sidebar-menu i {
            width: 25px;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.05);
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
        }
        
        /* Formatting Toolbar Styles */
        .format-toolbar {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            padding: 8px 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .format-btn {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 6px 12px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .format-btn:hover {
            background: var(--grad);
            color: white;
            border-color: transparent;
        }
        
        .format-btn i {
            font-size: 14px;
        }
        
        .font-size-select {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 5px;
            font-size: 14px;
            cursor: pointer;
        }
        
        .rich-textarea {
            width: 100%;
            min-height: 200px;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 0 0 8px 8px;
            font-family: inherit;
            line-height: 1.6;
            resize: vertical;
        }
        
        .rich-textarea:focus {
            outline: none;
            border-color: #009ada;
            box-shadow: 0 0 0 3px rgba(0,154,218,0.1);
        }
        
        /* Preview Styles */
        .description-preview {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            max-height: 200px;
            overflow-y: auto;
        }
        
        .description-preview h1 { font-size: 1.8rem; margin-bottom: 0.5rem; }
        .description-preview h2 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .description-preview h3 { font-size: 1.3rem; margin-bottom: 0.5rem; }
        .description-preview h4 { font-size: 1.2rem; margin-bottom: 0.5rem; }
        .description-preview h5 { font-size: 1.1rem; margin-bottom: 0.5rem; }
        .description-preview h6 { font-size: 1rem; margin-bottom: 0.5rem; }
        .description-preview p { margin-bottom: 0.5rem; }
        .description-preview ul, .description-preview ol { padding-left: 1.5rem; margin-bottom: 0.5rem; }
        .description-preview li { margin-bottom: 0.25rem; }
        .description-preview b, .description-preview strong { font-weight: 700; color: #009ada; }
        .description-preview i, .description-preview em { font-style: italic; }
        .description-preview u { text-decoration: underline; }
        
        .media-preview {
            width: 100px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
            background: #f1f5f9;
        }
        
        .media-preview img, .media-preview iframe {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .status-badge {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        
        .status-active {
            background: var(--kiboko-green);
        }
        
        .status-inactive {
            background: #ef4444;
        }
        
        .type-badge {
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .type-image { background: #3b82f6; color: white; }
        .type-youtube { background: #ef4444; color: white; }
        .type-vimeo { background: #1ab7ea; color: white; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    @include('admin.partials.sidebar')
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Carousel Manager</h2>
            <button class="btn-gradient" onclick="openAddModal()">
                <i class="fas fa-plus me-2"></i>Add Media
            </button>
        </div>
        
        @if(isset($error))
        <div class="alert alert-danger">{!! $error !!}</div>
        @endif
        
        <div class="content-card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($carousel) > 0)
                        @foreach($carousel as $item)
                        <tr>
                            <td>
                                <div class="media-preview">
                                    @if($item['media_type'] == 'image')
                                        <img src="../uploads/{!! $item['media_url'] !!}" alt="{!! $item['title'] !!}">
                                    @elseif($item['media_type'] == 'youtube')
                                        <img src="https://img.youtube.com/vi/{!! $item['media_url'] !!}/mqdefault.jpg" alt="YouTube">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                            <i class="fas fa-video text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $item['title'] }}</td>
                            <td>
                                <span class="type-badge type-{!! $item['media_type'] !!}">
                                    {!! ucfirst($item['media_type']) !!}
                                </span>
                            </td>
                            <td>
                                @php
                                // Strip tags for preview but show a snippet
                                $plainText = strip_tags($item['description'] ?? '');
                                @endphp
{!! substr($plainText, 0, 50) . (strlen($plainText) > 50 ? '...' : '') !!}
                            </td>
                            <td>{!! $item['sort_order'] !!}</td>
                            <td>
                                <span class="status-badge {!! $item['status'] ? 'status-active' : 'status-inactive' !!}"></span>
                                {!! $item['status'] ? 'Active' : 'Inactive' !!}
                            </td>
                            <td>
                                <button onclick='openEditModal({!! json_encode($item) !!})' class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?toggle={!! $item['id'] !!}" class="btn btn-sm btn-outline-warning" onclick="return confirm('Toggle status?')">
                                    <i class="fas fa-power-off"></i>
                                </a>
                                <a href="?delete={!! $item['id'] !!}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No carousel items yet. Click "Add Media" to create your first item.</p>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Carousel Modal -->
    <div class="modal fade" id="carouselModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-images me-2 text-gradient"></i>
                        <span id="modalTitle">Add Carousel Media</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data" id="carouselForm">
@csrf
                    <input type="hidden" name="id" id="carouselId" value="">
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" id="title" class="form-control" value="">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Media Type</label>
                                    <select name="media_type" id="mediaType" class="form-control" onchange="toggleMediaInput()">
                                        <option value="image">Image (Upload)</option>
                                        <option value="youtube">YouTube Video</option>
                                        <option value="vimeo">Vimeo Video</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3" id="imageUploadDiv">
                                    <label class="form-label">Upload Image</label>
                                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                                    <div id="currentImageContainer" style="display: none;" class="mt-2">
                                        <small>Current: </small>
                                        <img id="currentImage" src="" width="100" height="60" style="object-fit: cover; border-radius: 4px;">
                                    </div>
                                </div>
                                
                                <div class="mb-3" id="mediaUrlDiv" style="display: none;">
                                    <label class="form-label">Video URL</label>
                                    <input type="url" name="media_url" id="mediaUrl" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                                    <small class="text-muted">Paste the full YouTube or Vimeo URL</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" name="sort_order" id="sortOrder" class="form-control" value="0" min="0">
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" name="status" class="form-check-input" id="status" checked>
                                    <label class="form-check-label" for="status">Active</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Description with Formatting Tools -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <label class="form-label">Description (with formatting)</label>
                                
                                <!-- Formatting Toolbar -->
                                <div class="format-toolbar">
                                    <button type="button" class="format-btn" onclick="formatText('bold')" title="Bold">
                                        <i class="fas fa-bold"></i>
                                    </button>
                                    <button type="button" class="format-btn" onclick="formatText('italic')" title="Italic">
                                        <i class="fas fa-italic"></i>
                                    </button>
                                    <button type="button" class="format-btn" onclick="formatText('underline')" title="Underline">
                                        <i class="fas fa-underline"></i>
                                    </button>
                                    <div class="vr mx-1"></div>
                                    <button type="button" class="format-btn" onclick="formatText('h1')" title="Heading 1">
                                        H1
                                    </button>
                                    <button type="button" class="format-btn" onclick="formatText('h2')" title="Heading 2">
                                        H2
                                    </button>
                                    <button type="button" class="format-btn" onclick="formatText('h3')" title="Heading 3">
                                        H3
                                    </button>
                                    <div class="vr mx-1"></div>
                                    <button type="button" class="format-btn" onclick="formatText('ul')" title="Bullet List">
                                        <i class="fas fa-list-ul"></i>
                                    </button>
                                    <button type="button" class="format-btn" onclick="formatText('ol')" title="Numbered List">
                                        <i class="fas fa-list-ol"></i>
                                    </button>
                                    <div class="vr mx-1"></div>
                                    <select class="font-size-select" onchange="formatText('fontsize', this.value)" title="Font Size">
                                        <option value="">Font Size</option>
                                        <option value="1">Small</option>
                                        <option value="3">Normal</option>
                                        <option value="5">Large</option>
                                        <option value="7">Extra Large</option>
                                    </select>
                                    <button type="button" class="format-btn" onclick="formatText('p')" title="Paragraph">
                                        <i class="fas fa-paragraph"></i>
                                    </button>
                                    <button type="button" class="format-btn" onclick="formatText('clear')" title="Clear Format">
                                        <i class="fas fa-eraser"></i>
                                    </button>
                                </div>
                                
                                <!-- Textarea for rich content -->
                                <textarea name="description" id="description" class="rich-textarea" placeholder="Enter description with formatting..."></textarea>
                                
                                <!-- Live Preview -->
                                <div class="mt-2">
                                    <small class="text-muted">Preview:</small>
                                    <div id="descriptionPreview" class="description-preview"></div>
                                </div>
                                
                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    You can use HTML tags: &lt;b&gt;, &lt;i&gt;, &lt;u&gt;, &lt;h1&gt;-&lt;h6&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt;, &lt;p&gt;
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-save me-2"></i>Save Media
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Formatting functions
        function formatText(command, value = null) {
            const textarea = document.getElementById('description');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selectedText = textarea.value.substring(start, end);
            let formattedText = '';
            
            switch(command) {
                case 'bold':
                    formattedText = '<b>' + selectedText + '</b>';
                    break;
                case 'italic':
                    formattedText = '<i>' + selectedText + '</i>';
                    break;
                case 'underline':
                    formattedText = '<u>' + selectedText + '</u>';
                    break;
                case 'h1':
                    formattedText = '<h1>' + selectedText + '</h1>';
                    break;
                case 'h2':
                    formattedText = '<h2>' + selectedText + '</h2>';
                    break;
                case 'h3':
                    formattedText = '<h3>' + selectedText + '</h3>';
                    break;
                case 'h4':
                    formattedText = '<h4>' + selectedText + '</h4>';
                    break;
                case 'h5':
                    formattedText = '<h5>' + selectedText + '</h5>';
                    break;
                case 'h6':
                    formattedText = '<h6>' + selectedText + '</h6>';
                    break;
                case 'p':
                    formattedText = '<p>' + selectedText + '</p>';
                    break;
                case 'ul':
                    if (selectedText.includes('\n')) {
                        const items = selectedText.split('\n').filter(item => item.trim());
                        formattedText = '<ul>\n' + items.map(item => '    <li>' + item + '</li>').join('\n') + '\n</ul>';
                    } else {
                        formattedText = '<ul>\n    <li>' + selectedText + '</li>\n</ul>';
                    }
                    break;
                case 'ol':
                    if (selectedText.includes('\n')) {
                        const items = selectedText.split('\n').filter(item => item.trim());
                        formattedText = '<ol>\n' + items.map(item => '    <li>' + item + '</li>').join('\n') + '\n</ol>';
                    } else {
                        formattedText = '<ol>\n    <li>' + selectedText + '</li>\n</ol>';
                    }
                    break;
                case 'fontsize':
                    formattedText = '<span style="font-size: ' + getFontSize(value) + ';">' + selectedText + '</span>';
                    break;
                case 'clear':
                    formattedText = selectedText;
                    break;
                default:
                    return;
            }
            
            // Insert formatted text
            const newValue = textarea.value.substring(0, start) + formattedText + textarea.value.substring(end);
            textarea.value = newValue;
            
            // Update preview
            updatePreview();
            
            // Restore cursor position
            textarea.focus();
            textarea.setSelectionRange(start + formattedText.length, start + formattedText.length);
        }
        
        function getFontSize(size) {
            const sizes = {
                '1': '0.75rem',
                '2': '0.875rem',
                '3': '1rem',
                '4': '1.25rem',
                '5': '1.5rem',
                '6': '1.75rem',
                '7': '2rem'
            };
            return sizes[size] || '1rem';
        }
        
        function updatePreview() {
            const description = document.getElementById('description').value;
            const preview = document.getElementById('descriptionPreview');
            preview.innerHTML = description || '<em class="text-muted">No description entered yet...</em>';
        }
        
        // Toggle media input based on type
        function toggleMediaInput() {
            var type = document.getElementById('mediaType').value;
            var imageDiv = document.getElementById('imageUploadDiv');
            var urlDiv = document.getElementById('mediaUrlDiv');
            var urlInput = document.getElementById('mediaUrl');
            
            if(type == 'image') {
                imageDiv.style.display = 'block';
                urlDiv.style.display = 'none';
                urlInput.required = false;
            } else {
                imageDiv.style.display = 'none';
                urlDiv.style.display = 'block';
                urlInput.required = true;
            }
        }
        
        // Open add modal
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Carousel Media';
            document.getElementById('carouselId').value = '';
            document.getElementById('title').value = '';
            document.getElementById('mediaType').value = 'image';
            document.getElementById('description').value = '';
            document.getElementById('sortOrder').value = '0';
            document.getElementById('status').checked = true;
            document.getElementById('mediaUrl').value = '';
            document.getElementById('image').value = '';
            document.getElementById('currentImageContainer').style.display = 'none';
            toggleMediaInput();
            updatePreview();
            
            new bootstrap.Modal(document.getElementById('carouselModal')).show();
        }
        
        // Open edit modal
        function openEditModal(item) {
            document.getElementById('modalTitle').textContent = 'Edit Carousel Media';
            document.getElementById('carouselId').value = item.id;
            document.getElementById('title').value = item.title || '';
            document.getElementById('mediaType').value = item.media_type;
            document.getElementById('description').value = item.description || '';
            document.getElementById('sortOrder').value = item.sort_order || 0;
            document.getElementById('status').checked = item.status == 1;
            
            if (item.media_type == 'image') {
                document.getElementById('mediaUrl').value = '';
                if (item.media_url) {
                    document.getElementById('currentImage').src = '../uploads/' + item.media_url;
                    document.getElementById('currentImageContainer').style.display = 'block';
                }
            } else {
                document.getElementById('mediaUrl').value = item.media_url;
                document.getElementById('currentImageContainer').style.display = 'none';
            }
            
            document.getElementById('image').value = '';
            toggleMediaInput();
            updatePreview();
            
            new bootstrap.Modal(document.getElementById('carouselModal')).show();
        }
        
        // Update preview on input
        document.getElementById('description').addEventListener('input', updatePreview);
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleMediaInput();
            updatePreview();
        });
        
        @if(isset($get['edit']) && $editData)
        window.onload = function() {
            setTimeout(function() {
                openEditModal({!! json_encode($editData) !!});
            }, 100);
        };
        @endif
    </script>
</body>
</html>
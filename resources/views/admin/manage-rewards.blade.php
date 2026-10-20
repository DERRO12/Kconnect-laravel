<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rewards & Recognition - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ... (keep all the existing styles from previous admin page) ... */
        /* I'll keep the styles the same as before */
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
            flex-wrap: wrap;
            gap: 15px;
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--kiboko-blue);
            line-height: 1.2;
        }
        
        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .table-container {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
        }
        
        .rewards-table {
            width: 100%;
        }
        
        .video-thumb {
            width: 120px;
            height: 68px;
            border-radius: 8px;
            overflow: hidden;
            background: #f1f5f9;
        }
        
        .video-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-active {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .action-btn:hover {
            background: var(--grad);
            color: white;
            border-color: transparent;
            transform: translateY(-2px);
        }
        
        .action-btn.delete:hover {
            background: #dc3545;
        }
        
        .modal-content {
            border: none;
            border-radius: 20px;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border-bottom: 1px solid #eef2f6;
            padding: 20px 25px;
        }
        
        .modal-body {
            padding: 25px;
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .modal-footer {
            padding: 15px 25px;
            background: #f8fafc;
            border-top: 1px solid #eef2f6;
        }
        
        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.85rem;
            margin-bottom: 5px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 3px rgba(0,154,218,0.1);
        }
        
        .attachment-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .attachment-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .attachment-icon {
            width: 40px;
            height: 40px;
            background: var(--grad);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .attachment-details {
            flex: 1;
        }
        
        .attachment-name {
            font-weight: 600;
            color: #1e293b;
        }
        
        .attachment-meta {
            font-size: 0.75rem;
            color: #64748b;
        }
        
        .file-input-container {
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #f8fafc;
            margin-bottom: 15px;
        }
        
        .file-input-container:hover {
            border-color: var(--kiboko-blue);
            background: #f0f9ff;
        }
        
        .file-input-container i {
            font-size: 2rem;
            color: var(--kiboko-blue);
            margin-bottom: 10px;
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
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="page-header">
            <h2><i class="fas fa-trophy me-2"></i>Rewards & Recognition</h2>
            <button class="btn-gradient" onclick="openAddModal()">
                <i class="fas fa-plus me-2"></i>Add New Video
            </button>
        </div>
        
        <!-- Message Alert -->
        @if (isset($session_message))
        <div class="message-alert {!! $session_message_type !!}">
            <span><i class="fas fa-info-circle me-2"></i>{!! $session_message !!}</span>
            <button class="btn-close float-end" onclick="this.parentElement.remove()"></button>
        </div>
        @endif
        
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{!! $total_videos !!}</div>
                <div class="stat-label">Total Videos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{!! $active_videos !!}</div>
                <div class="stat-label">Active Videos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{!! $total_attachments !!}</div>
                <div class="stat-label">Attachments</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{!! number_format($total_views) !!}</div>
                <div class="stat-label">Total Views</div>
            </div>
        </div>
        
        <!-- Rewards Table -->
        <div class="table-container">
            <table class="rewards-table table">
                <thead>
                    <tr>
                        <th>Video</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Attachments</th>
                        <th>Views</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($rewards && count($rewards) > 0)
                        @foreach ($rewards as $item)
@php
$att_count = $att_counts[$item['id']];
@endphp
                        <tr>
                            <td>
                                <div class="video-thumb">
                                    @if ($item['video_type'] == 'youtube')
                                        <img src="https://img.youtube.com/vi/{!! $item['video_id'] !!}/mqdefault.jpg" alt="YouTube">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                            <i class="fas fa-video text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $item['title'] }}</td>
                            <td>
                                @php
 
                                $desc = strip_tags($item['description']);
                                echo substr($desc, 0, 50) . (strlen($desc) > 50 ? '...' : ''); 
                                
@endphp
                            </td>
                            <td>
                                <span class="badge bg-info">{!! $att_count !!} files</span>
                            </td>
                            <td>{!! number_format($item['views_count']) !!}</td>
                            <td>
                                <span class="status-badge {!! $item['status'] ? 'status-active' : 'status-inactive' !!}">
                                    {!! $item['status'] ? 'Active' : 'Inactive' !!}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?edit={!! $item['id'] !!}" class="action-btn" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?toggle={!! $item['id'] !!}" class="action-btn" title="Toggle Status" 
                                       onclick="return confirm('Toggle status for this item?')">
                                        <i class="fas fa-power-off"></i>
                                    </a>
                                    <a href="?delete={!! $item['id'] !!}" class="action-btn delete" title="Delete" 
                                       onclick="return confirm('Delete this item and all its attachments?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No rewards videos yet. Click "Add New Video" to create one.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Edit Section -->
        @if ($edit_item)
        <div class="table-container mt-4">
            <h4 class="mb-4"><i class="fas fa-edit me-2 text-gradient"></i>Editing: {{ $edit_item['title'] }}</h4>
            
            <form method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                <input type="hidden" name="edit_id" value="{!! $edit_item['id'] !!}">
                <input type="hidden" name="save_reward" value="1">
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $edit_item['title'] }}" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Video Type</label>
                                <select name="video_type" class="form-select">
                                    <option value="youtube" {!! $edit_item['video_type'] == 'youtube' ? 'selected' : '' !!}>YouTube</option>
                                    <option value="vimeo" {!! $edit_item['video_type'] == 'vimeo' ? 'selected' : '' !!}>Vimeo</option>
                                    <option value="other" {!! $edit_item['video_type'] == 'other' ? 'selected' : '' !!}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Video URL</label>
                                <input type="url" name="video_url" class="form-control" value="{{ $edit_item['video_url'] }}" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="5">{{ $edit_item['description'] }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light p-3 rounded">
                            <h6 class="fw-bold mb-3">Current Video</h6>
                            @if ($edit_item['video_type'] == 'youtube')
                                <div class="ratio ratio-16x9 mb-3">
                                    <iframe src="https://www.youtube.com/embed/{!! $edit_item['video_id'] !!}" allowfullscreen></iframe>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="text-end">
                    <a href="manage-rewards" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-gradient">
                        <i class="fas fa-save me-2"></i>Update Video
                    </button>
                </div>
            </form>
            
            <!-- Attachments Section -->
            <h5 class="fw-bold mb-3"><i class="fas fa-paperclip me-2"></i>Attachments</h5>
            
            @if ($edit_attachments && count($edit_attachments) > 0)
                @foreach ($edit_attachments as $att)
                <div class="attachment-item">
                    <div class="attachment-info">
                        <div class="attachment-icon">
                            @php

                            $icon = 'fa-file';
                            if($att['file_type'] == 'pdf') $icon = 'fa-file-pdf';
                            elseif(in_array($att['file_type'], ['doc', 'docx'])) $icon = 'fa-file-word';
                            elseif(in_array($att['file_type'], ['xls', 'xlsx', 'csv'])) $icon = 'fa-file-excel';
                            elseif(in_array($att['file_type'], ['ppt', 'pptx'])) $icon = 'fa-file-powerpoint';
                            elseif(in_array($att['file_type'], ['jpg', 'jpeg', 'png', 'gif'])) $icon = 'fa-file-image';
                            elseif(in_array($att['file_type'], ['zip', 'rar', '7z'])) $icon = 'fa-file-archive';
                            
@endphp
                            <i class="fas {!! $icon !!}"></i>
                        </div>
                        <div class="attachment-details">
                            <div class="attachment-name">{{ $att['file_name'] }}</div>
                            <div class="attachment-meta">
                                {!! strtoupper($att['file_type']) !!} • 
                                {!! round($att['file_size'] / 1024, 1) !!} KB • 
                                Downloaded {!! $att['download_count'] !!} times
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="../{!! $att['file_path'] !!}" class="action-btn" target="_blank" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="?delete_attachment={!! $att['id'] !!}&item_id={!! $edit_item['id'] !!}" 
                           class="action-btn delete" title="Delete" onclick="return confirm('Delete this attachment?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            @else
                <p class="text-muted">No attachments yet.</p>
            @endif
            
            <!-- Upload New Attachments -->
            <form method="POST" enctype="multipart/form-data" class="mt-4">
                @csrf
                <input type="hidden" name="item_id" value="{!! $edit_item['id'] !!}">
                <input type="hidden" name="upload_attachments" value="1">
                
                <div class="file-input-container" onclick="document.getElementById('new_attachments').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p class="mb-1">Click to upload additional files</p>
                    <small class="text-muted">PDF, Word, Excel, PowerPoint, Images, Archives (Max 10MB per file)</small>
                    <input type="file" name="new_attachments[]" id="new_attachments" class="d-none" multiple>
                </div>
                <div id="fileList" class="mt-2"></div>
                
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-gradient">
                        <i class="fas fa-upload me-2"></i>Upload Files
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
    
    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2 text-gradient"></i>Add New Rewards Video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="save_reward" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Video Type</label>
                                <select name="video_type" class="form-select">
                                    <option value="youtube">YouTube</option>
                                    <option value="vimeo">Vimeo</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Video URL</label>
                                <input type="url" name="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=..." required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="5"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Attachments (Optional)</label>
                            <div class="file-input-container" onclick="document.getElementById('attachments').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p class="mb-1">Click to upload files</p>
                                <small class="text-muted">PDF, Word, Excel, PowerPoint, Images (Max 10MB per file)</small>
                                <input type="file" name="attachments[]" id="attachments" class="d-none" multiple>
                            </div>
                            <div id="newFileList" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-save me-2"></i>Save Video
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openAddModal() {
            new bootstrap.Modal(document.getElementById('addModal')).show();
        }
        
        // File list display for new attachments
        document.getElementById('attachments')?.addEventListener('change', function(e) {
            const fileList = document.getElementById('newFileList');
            fileList.innerHTML = '';
            for(let i = 0; i < this.files.length; i++) {
                const file = this.files[i];
                fileList.innerHTML += `
                    <div class="alert alert-info py-1 px-2 mb-1">
                        <i class="fas fa-file me-2"></i>${file.name} (${(file.size/1024).toFixed(1)} KB)
                    </div>
                `;
            }
        });
        
        // File list display for additional attachments in edit mode
        document.getElementById('new_attachments')?.addEventListener('change', function(e) {
            const fileList = document.getElementById('fileList');
            fileList.innerHTML = '';
            for(let i = 0; i < this.files.length; i++) {
                const file = this.files[i];
                fileList.innerHTML += `
                    <div class="alert alert-info py-1 px-2 mb-1">
                        <i class="fas fa-file me-2"></i>${file.name} (${(file.size/1024).toFixed(1)} KB)
                    </div>
                `;
            }
        });
        
        // Auto-hide messages
        setTimeout(() => {
            document.querySelectorAll('.message-alert').forEach(el => {
                if (el) el.remove();
            });
        }, 5000);
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Resources - {{ $course['title'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #009ada;
            --secondary: #76bc21;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
        }
        
        .app-wrapper {
            display: flex;
        }
        
        .sidebar {
            width: 260px;
            background: white;
            height: 100vh;
            position: fixed;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            padding: 20px;
            overflow-y: auto;
        }
        
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 30px;
        }
        
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 20px 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            margin-bottom: 30px;
        }
        
        .resource-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid #eef2f6;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .resource-card:hover {
            border-color: var(--primary);
            box-shadow: 0 5px 15px rgba(0,154,218,0.1);
            transform: translateX(5px);
        }
        
        .resource-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .resource-icon.youtube { background: #fee2e2; color: #dc2626; }
        .resource-icon.pdf { background: #fee2e2; color: #dc2626; }
        .resource-icon.article { background: #dbeafe; color: #2563eb; }
        .resource-icon.link { background: #d1fae5; color: #059669; }
        .resource-icon.image { background: #fef3c7; color: #d97706; }
        
        .resource-details {
            flex: 1;
        }
        
        .resource-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .resource-meta {
            font-size: 0.85rem;
            color: #64748b;
            display: flex;
            gap: 15px;
        }
        
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.2);
            color: white;
        }
        
        .resource-type-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .type-option {
            flex: 1;
            cursor: pointer;
            padding: 20px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .type-option:hover {
            border-color: var(--primary);
            background: #f0f9ff;
        }
        
        .type-option.selected {
            border-color: var(--primary);
            background: #f0f9ff;
        }
        
        .type-option i {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .type-option.youtube i { color: #dc2626; }
        .type-option.pdf i { color: #dc2626; }
        .type-option.article i { color: #2563eb; }
        .type-option.link i { color: #059669; }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <h3 class="fw-bold mb-4" style="background: var(--grad); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                <i class="fas fa-bolt me-2"></i>Kiboko Admin
            </h3>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="dashboard" class="nav-link text-dark"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="course-resources?id={!! $course_id !!}" class="nav-link active text-primary"><i class="fas fa-graduation-cap me-2"></i>Courses</a>
                </li>
                <li class="nav-item">
                    <a href="logout" class="nav-link text-dark"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold mb-1">Course Resources</h4>
                        <p class="text-muted mb-0">Course: {{ $course['title'] }}</p>
                    </div>
                    <a href="dashboard" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Courses
                    </a>
                </div>
            </div>
            
            @if (session()->has('message'))
            <div class="alert alert-{!! session('message_type') !!} alert-dismissible fade show">
                {!! session('message') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @php session()->forget('message');
                session()->forget('message_type'); @endphp
            @endif
            
            <!-- Add Resource Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2" style="color: var(--primary);"></i>Add New Resource</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                @csrf
                        <div class="mb-3">
                            <label class="form-label">Resource Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Resource Type</label>
                            <select name="resource_type" id="resourceType" class="form-select" required>
                                <option value="youtube">YouTube Video</option>
                                <option value="pdf">PDF Document</option>
                                <option value="article">Article</option>
                                <option value="link">External Link</option>
                                <option value="image">Image</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="urlField">
                            <label class="form-label">URL</label>
                            <input type="url" name="resource_url" class="form-control" placeholder="https://...">
                        </div>
                        
                        <div class="mb-3" id="fileField" style="display: none;">
                            <label class="form-label">Upload PDF</label>
                            <input type="file" name="pdf_file" class="form-control" accept=".pdf">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                        
                        <button type="submit" name="add_resource" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Resource
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Resources List -->
            <h5 class="mb-3">Course Resources ({!! count($resources) !!})</h5>
            
            @if ($resources && count($resources) > 0)
                @foreach ($resources as $resource)
                <div class="resource-card">
                    <div class="resource-icon {!! $resource['resource_type'] !!}">
                        <i class="fas fa-{!! $resource['resource_type'] == 'youtube' ? 'play' : 
                                ($resource['resource_type'] == 'pdf' ? 'file-pdf' : 
                                ($resource['resource_type'] == 'article' ? 'newspaper' : 
                                ($resource['resource_type'] == 'image' ? 'image' : 'link'))) !!}"></i>
                    </div>
                    <div class="resource-details">
                        <div class="resource-title">{{ $resource['title'] }}</div>
                        <div class="resource-meta">
                            <span><i class="fas fa-tag me-1"></i>{!! ucfirst($resource['resource_type']) !!}</span>
                            <span><i class="fas fa-sort me-1"></i>Order: {!! $resource['sort_order'] !!}</span>
                        </div>
                        @if ($resource['description'])
                            <small class="text-muted">{{ $resource['description'] }}</small>
                        @endif
                    </div>
                    <div>
                        <a href="{{ $resource['resource_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="?id={!! $course_id !!}&delete={!! $resource['id'] !!}" 
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Delete this resource?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No resources added yet</h5>
                    <p class="text-muted">Add your first resource using the form above</p>
                </div>
            @endif
        </div>
    </div>
    
    <script>
        // Toggle between URL and file upload based on resource type
        document.getElementById('resourceType').addEventListener('change', function() {
            const urlField = document.getElementById('urlField');
            const fileField = document.getElementById('fileField');
            
            if (this.value === 'pdf') {
                urlField.style.display = 'none';
                fileField.style.display = 'block';
            } else {
                urlField.style.display = 'block';
                fileField.style.display = 'none';
            }
        });
    </script>
</body>
</html>
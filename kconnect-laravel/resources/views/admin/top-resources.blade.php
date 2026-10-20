
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Resources - Kiboko Admin</title>
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
        
        .table-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        }
        
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 10px 24px;
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
        
        .btn-outline-gradient {
            background: transparent;
            border: 1px solid var(--kiboko-blue);
            color: var(--kiboko-blue);
            padding: 5px 15px;
            border-radius: 8px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }
        
        .btn-outline-gradient:hover {
            background: var(--grad);
            border-color: transparent;
            color: white;
        }
        
        .resource-preview {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.9rem;
        }
        
        .resource-preview i {
            font-size: 1rem;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            border-bottom: 2px solid #f1f5f9;
            color: #64748b;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table tbody td {
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            padding: 15px 10px;
        }
        
        .table tbody tr:hover {
            background: #f8fafc;
        }
        
        .color-dot {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: inline-block;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="page-header">
            <div>
                <h4 class="fw-bold mb-1">Top Resources</h4>
                <p class="text-muted mb-0">Manage quick access resources and forms</p>
            </div>
            <a href="top-resources-add.php" class="btn-gradient">
                <i class="fas fa-plus"></i> Add New Resource
            </a>
        </div>
        
        @if (isset($success))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {!! $success !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        @if (isset($error))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {!! $error !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        <div class="table-card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Icon</th>
                            <th>Colors</th>
                            <th>URL</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($result && count($result) > 0)
                            @foreach ($result as $resource)
                            <tr>
                                <td>
                                    <span class="resource-preview" style="background: {!! $resource['bg_color'] !!}; color: {!! $resource['color'] !!};">
                                        <i class="fas {!! $resource['icon'] !!}"></i>
                                        <span>{{ $resource['title'] }}</span>
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $resource['title'] }}</strong>
                                    @if (!empty($resource['description']))
                                    <br><small class="text-muted">{!! substr(htmlspecialchars($resource['description']), 0, 30) !!}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $resource['category'] }}</span>
                                </td>
                                <td>
                                    <code>{!! $resource['icon'] !!}</code>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="color-dot" style="background: {!! $resource['color'] !!};" title="Text Color"></span>
                                        <span class="color-dot" style="background: {!! $resource['bg_color'] !!};" title="Background Color"></span>
                                    </div>
                                </td>
                                <td>
                                    @if (!empty($resource['url']) && $resource['url'] != '#')
                                    <a href="{{ $resource['url'] }}" target="_blank" class="text-muted">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    @else
                                    <span class="text-muted">#</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{!! $resource['sort_order'] !!}</span>
                                </td>
                                <td>
                                    @if ($resource['status'])
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="top-resources-edit.php?id={!! $resource['id'] !!}" class="btn-outline-gradient me-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?toggle={!! $resource['id'] !!}" class="btn-outline-gradient me-2">
                                        <i class="fas fa-toggle-{!! $resource['status'] ? 'on' : 'off' !!}"></i>
                                    </a>
                                    <a href="?delete={!! $resource['id'] !!}" class="btn-outline-gradient" onclick="return confirm('Are you sure you want to delete this resource?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-link fa-4x text-muted mb-3"></i>
                                    <h6 class="text-muted">No resources found</h6>
                                    <a href="top-resources-add.php" class="btn-gradient btn-sm mt-3">
                                        <i class="fas fa-plus me-2"></i>Add Your First Resource
                                    </a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
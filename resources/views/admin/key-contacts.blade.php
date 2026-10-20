
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Key Contacts - Kiboko Admin</title>
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
        
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
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
            padding: 5px 12px;
            border-radius: 8px;
            transition: all 0.3s;
            text-decoration: none;
            font-size: 0.85rem;
        }
        
        .btn-outline-gradient:hover {
            background: var(--grad);
            border-color: transparent;
            color: white;
        }
        
        .contact-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .contact-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--grad-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--kiboko-blue);
            font-size: 1.5rem;
        }
        
        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            background: var(--grad-light);
            color: var(--kiboko-blue);
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
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
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="page-header">
            <div>
                <h4 class="fw-bold mb-1">Key Contacts</h4>
                <p class="text-muted mb-0">Manage important contacts and their details</p>
            </div>
            <a href="key-contacts-add" class="btn-gradient">
                <i class="fas fa-plus"></i> Add Contact
            </a>
        </div>
        
        @if (isset($get['success']))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> Operation completed successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        <div class="content-card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Contact</th>
                            <th>Category</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($contacts && count($contacts) > 0)
                            @foreach ($contacts as $contact)
                            <tr>
                                <td>
                                    @if ($hasPhoto && !empty($contact['photo']) && file_exists(kc_public_file('../uploads/contacts/' . $contact['photo'], 'admin')))
                                        <img src="../uploads/contacts/{!! $contact['photo'] !!}" class="contact-photo">
                                    @else
                                        <div class="contact-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $contact['name'] }}</strong>
                                </td>
                                <td>{{ $contact['position'] }}</td>
                                <td>{{ $contact['department'] }}</td>
                                <td>
                                    @if (!empty($contact['email']))
                                        <small><i class="fas fa-envelope me-1"></i>{{ $contact['email'] }}</small><br>
                                    @endif
                                    @if (!empty($contact['extension']))
                                        <small><i class="fas fa-phone me-1"></i>Ext: {{ $contact['extension'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="category-badge">{{ $contact['category'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{!! $contact['sort_order'] !!}</span>
                                </td>
                                <td>
                                    <span class="status-badge {!! $contact['status'] ? 'status-active' : 'status-inactive' !!}"></span>
                                    {!! $contact['status'] ? 'Active' : 'Inactive' !!}
                                </td>
                                <td>
                                    <a href="key-contacts-edit?id={!! $contact['id'] !!}" class="btn-outline-gradient me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?toggle={!! $contact['id'] !!}" class="btn-outline-gradient me-1" onclick="return confirm('Toggle status?')">
                                        <i class="fas fa-power-off"></i>
                                    </a>
                                    <a href="?delete={!! $contact['id'] !!}" class="btn-outline-gradient" onclick="return confirm('Delete this contact?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-address-book fa-4x text-muted mb-3"></i>
                                    <h6 class="text-muted">No contacts found</h6>
                                    <a href="key-contacts-add" class="btn-gradient btn-sm mt-3">
                                        <i class="fas fa-plus me-2"></i>Add Your First Contact
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
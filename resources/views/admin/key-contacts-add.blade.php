
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Contact - Kiboko Admin</title>
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
        }
        
        .emergency-badge {
            background: #d32f2f;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 8px;
        }
        
        .preview-card {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e9edf2;
            position: sticky;
            top: 100px;
        }
        
        .preview-emergency-details {
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
        }
        
        .preview-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e9edf2;
        }
        
        .preview-item:last-child {
            border-bottom: none;
        }
        
        .preview-item i {
            width: 30px;
            height: 30px;
            background: var(--kiboko-blue);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="page-header">
            <div>
                <h4 class="fw-bold mb-1">Add New Contact</h4>
                <p class="text-muted mb-0">Add an emergency contact or key person</p>
            </div>
            <a href="key-contacts" class="btn-outline-secondary">
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
                    <form method="POST">
                @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Position <span class="text-danger">*</span></label>
                                    <input type="text" name="position" class="form-control" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Department</label>
                                    <input type="text" name="department" class="form-control">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" placeholder="+256 XXX XXX XXX">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Extension <span class="emergency-badge">Important</span></label>
                                    <input type="text" name="extension" class="form-control" placeholder="e.g., 1001">
                                    <small class="text-muted">Internal extension for quick dialing</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Mobile</label>
                                    <input type="text" name="mobile" class="form-control" placeholder="+256 XXX XXX XXX">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Office Location</label>
                                    <input type="text" name="office_location" class="form-control" placeholder="e.g., 5th Floor, Room 501">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Office Hours</label>
                                    <input type="text" name="office_hours" class="form-control" placeholder="e.g., 24/7, Mon-Fri 9am-5pm">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-select">
                                        @foreach ($categories as $cat)
                                        <option value="{{ $cat }}" {!! $cat == 'Emergency' ? 'selected' : '' !!}>
                                            {{ $cat }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" name="sort_order" class="form-control" value="0" min="0">
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Additional Notes / Bio</label>
                                    <textarea name="bio" class="form-control" rows="2" placeholder="e.g., Available after hours for emergencies only"></textarea>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" name="status" class="form-check-input" id="status" checked>
                                    <label class="form-check-label" for="status">Active</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-3 mt-3">
                            <button type="submit" class="btn-gradient">
                                <i class="fas fa-save"></i> Save Contact
                            </button>
                            <a href="key-contacts" class="btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="preview-card">
                    <h6 class="fw-bold mb-3">Contact Preview</h6>
                    <div class="text-center mb-3">
                        <span class="badge bg-danger mb-2" id="previewCategory">Emergency</span>
                        <h5 class="fw-bold" id="previewName">John Doe</h5>
                        <p class="text-muted" id="previewPosition">IT Support</p>
                    </div>
                    <div class="preview-emergency-details">
                        <div class="preview-item">
                            <i class="fas fa-phone-alt"></i>
                            <div>
                                <small class="text-muted d-block">Phone</small>
                                <span id="previewPhone">+256 700 000000</span>
                            </div>
                        </div>
                        <div class="preview-item">
                            <i class="fas fa-phone-square"></i>
                            <div>
                                <small class="text-muted d-block">Extension</small>
                                <strong id="previewExtension" class="text-primary">1001</strong>
                            </div>
                        </div>
                        <div class="preview-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <small class="text-muted d-block">Email</small>
                                <span id="previewEmail">support@kiboko.com</span>
                            </div>
                        </div>
                        <div class="preview-item">
                            <i class="fas fa-building"></i>
                            <div>
                                <small class="text-muted d-block">Department</small>
                                <span id="previewDept">IT Department</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Live preview
        document.querySelector('input[name="name"]').addEventListener('input', function(e) {
            document.getElementById('previewName').textContent = e.target.value || 'John Doe';
        });
        
        document.querySelector('input[name="position"]').addEventListener('input', function(e) {
            document.getElementById('previewPosition').textContent = e.target.value || 'IT Support';
        });
        
        document.querySelector('input[name="department"]').addEventListener('input', function(e) {
            document.getElementById('previewDept').textContent = e.target.value || 'IT Department';
        });
        
        document.querySelector('input[name="email"]').addEventListener('input', function(e) {
            document.getElementById('previewEmail').textContent = e.target.value || 'support@kiboko.com';
        });
        
        document.querySelector('input[name="phone"]').addEventListener('input', function(e) {
            document.getElementById('previewPhone').textContent = e.target.value || '+256 700 000000';
        });
        
        document.querySelector('input[name="extension"]').addEventListener('input', function(e) {
            document.getElementById('previewExtension').textContent = e.target.value || '1001';
        });
        
        document.querySelector('select[name="category"]').addEventListener('change', function(e) {
            const badge = document.getElementById('previewCategory');
            badge.textContent = e.target.value;
            if (e.target.value === 'Emergency') {
                badge.className = 'badge bg-danger mb-2';
            } else {
                badge.className = 'badge bg-secondary mb-2';
            }
        });
    </script>
</body>
</html>
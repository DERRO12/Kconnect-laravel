
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Contact - Kiboko Admin</title>
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
        
        .current-photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .photo-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-top: 10px;
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="page-header">
            <div>
                <h4 class="fw-bold mb-1">Edit Contact</h4>
                <p class="text-muted mb-0">Update contact information</p>
            </div>
            <a href="key-contacts.php" class="btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        
        @if (isset($error))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i> {!! $error !!}
        </div>
        @endif
        
        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="{{ $contact['name'] }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Position <span class="text-danger">*</span></label>
                            <input type="text" name="position" class="form-control" required value="{{ $contact['position'] }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" class="form-control" value="{{ $contact['department'] }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $contact['email'] }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ $contact['phone'] }}">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Extension</label>
                            <input type="text" name="extension" class="form-control" value="{{ $contact['extension'] }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mobile</label>
                            <input type="text" name="mobile" class="form-control" value="{{ $contact['mobile'] }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Office Location</label>
                            <input type="text" name="office_location" class="form-control" value="{{ $contact['office_location'] }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Office Hours</label>
                            <input type="text" name="office_hours" class="form-control" value="{{ $contact['office_hours'] }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                @foreach ($categories as $cat)
                                <option value="{!! $cat !!}" {!! $contact['category'] == $cat ? 'selected' : '' !!}>{!! $cat !!}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{!! $contact['sort_order'] !!}" min="0">
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Bio / Description</label>
                            <textarea name="bio" class="form-control" rows="3">{{ $contact['bio'] }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Photo</label>
                            @if (!empty($contact['photo']) && file_exists(kc_public_file('../uploads/contacts/' . $contact['photo'], 'admin')))
                                <div class="mb-2">
                                    <img src="../uploads/contacts/{!! $contact['photo'] !!}" class="current-photo">
                                    <p class="small text-muted mt-1">Current photo</p>
                                </div>
                            @endif
                            <input type="file" name="photo" class="form-control" accept="image/*" id="photoInput">
                            <small class="text-muted">Leave empty to keep current photo. Allowed: JPG, PNG, GIF, WEBP</small>
                            <div id="photoPreview" style="display: none;" class="mt-2">
                                <img src="#" alt="Preview" class="photo-preview" id="preview">
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="status" class="form-check-input" id="status" {!! $contact['status'] ? 'checked' : '' !!}>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-3 mt-3">
                    <button type="submit" class="btn-gradient">
                        <i class="fas fa-save"></i> Update Contact
                    </button>
                    <a href="key-contacts.php" class="btn-outline-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('photoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                    document.getElementById('photoPreview').style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
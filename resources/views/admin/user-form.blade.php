<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{!! $id ? 'Edit' : 'Add' !!} User - Admin</title>
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
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            max-width: 800px;
        }
        
        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            padding: 12px 15px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #009ada;
            box-shadow: 0 0 0 3px rgba(0, 154, 218, 0.1);
            outline: none;
        }
        
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.2);
            color: white;
        }
        
        .current-photo {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
            border: 3px solid #e2e8f0;
        }
    </style>
</head>
<body>
   @include('admin.partials.sidebar')
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">{!! $id ? 'Edit' : 'Add New' !!} User</h2>
            <a href="users" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Users
            </a>
        </div>
        
        @if ($error)
            <div class="alert alert-danger">{!! $error !!}</div>
        @endif
        
        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">@csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Employee Code *</label>
                        <input type="text" name="employee_code" class="form-control" 
                               value="{!! $user['employee_code'] ?? '' !!}" required>
                        <small class="text-muted">Format: KEL-0001, EXP-001, ADMIN-001</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" 
                               value="{!! $user['full_name'] ?? '' !!}" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="{!! $user['email'] ?? '' !!}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" 
                               placeholder="{!! $id ? 'Leave blank to keep current' : 'Default: Kiboko@2024' !!}">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Department</label>
                        <select name="department" class="form-select">
                            <option value="">Select Department</option>
                            @foreach ($departments as $dept)
                                <option value="{!! $dept !!}" 
                                    {!! (isset($user['department']) && $user['department'] == $dept) ? 'selected' : '' !!}>
                                    {!! $dept !!}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Position</label>
                        <input type="text" name="position" class="form-control" 
                               value="{!! $user['position'] ?? '' !!}">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="employee" {!! (isset($user['role']) && $user['role'] == 'employee') ? 'selected' : '' !!}>Employee</option>
                            <option value="hr" {!! (isset($user['role']) && $user['role'] == 'hr') ? 'selected' : '' !!}>HR</option>
                            <option value="manager" {!! (isset($user['role']) && $user['role'] == 'manager') ? 'selected' : '' !!}>Manager</option>
                            <option value="director" {!! (isset($user['role']) && $user['role'] == 'director') ? 'selected' : '' !!}>Director</option>
                            <option value="admin" {!! (isset($user['role']) && $user['role'] == 'admin') ? 'selected' : '' !!}>Admin</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        @if ($id && $user['photo'])
                            <div class="mt-2">
                                <img src="../uploads/employees/{!! $user['photo'] !!}" class="current-photo">
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" name="status" class="form-check-input" id="status" 
                               {!! (!isset($user['status']) || $user['status']) ? 'checked' : '' !!}>
                        <label class="form-check-label" for="status">Active</label>
                    </div>
                </div>
                
                <button type="submit" class="btn-gradient">
                    <i class="fas fa-save me-2"></i>
                    {!! $id ? 'Update User' : 'Create User' !!}
                </button>
            </form>
        </div>
    </div>
</body>
</html>
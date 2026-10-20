<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - K-Connect</title>
       <link rel="icon" type="image/png" href="favi.png">
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad-primary: linear-gradient(135deg, #009ada, #76bc21);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .profile-container {
            flex: 1;
            padding: 40px 20px;
        }

        .profile-card {
            background: white;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            border: 1px solid #eef2f6;
            position: relative;
        }

        .profile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--grad-primary);
            z-index: 1;
        }

        .profile-header {
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            padding: 30px 30px 20px;
            border-bottom: 1px solid #eef2f6;
        }

        .profile-header h2 {
            font-size: 1.8rem;
            font-weight: 800;
            background: var(--grad-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
        }

        .profile-header p {
            color: #64748b;
            font-size: 0.9rem;
        }

        .profile-content {
            padding: 30px;
        }

        /* Photo Section */
        .photo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-photo-wrapper {
            position: relative;
            display: inline-block;
        }

        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            background: #f1f5f9;
        }

        .photo-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            background: var(--grad-primary);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid white;
        }

        .photo-overlay:hover {
            transform: scale(1.1);
        }

        .photo-overlay i {
            color: white;
            font-size: 1rem;
        }

        .photo-actions {
            margin-top: 10px;
        }

        .photo-actions .btn-sm {
            font-size: 0.75rem;
            padding: 4px 10px;
        }

        /* Form Sections */
        .form-section {
            background: #f8fafc;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .form-section-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-section-title i {
            color: var(--kiboko-blue);
            font-size: 1.2rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            color: #94a3b8;
            font-size: 1rem;
            transition: all 0.3s;
            z-index: 2;
        }

        .input-wrapper .toggle-password {
            left: auto;
            right: 15px;
            cursor: pointer;
            z-index: 2;
        }

        .input-wrapper .toggle-password:hover {
            color: var(--kiboko-blue);
            transform: scale(1.1);
        }

        .form-control, .form-select {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e9edf2;
            border-radius: 12px;
            font-size: 0.9rem;
            transition: all 0.3s;
            background: white;
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 4px rgba(0,154,218,0.1);
        }

        .form-control:focus + i {
            color: var(--kiboko-blue);
        }

        textarea.form-control {
            padding-left: 15px;
            resize: vertical;
        }

        .btn-gradient {
            padding: 12px 24px;
            background: var(--grad-primary);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.3);
        }

        .btn-outline-gradient {
            background: white;
            border: 2px solid var(--kiboko-blue);
            border-radius: 12px;
            padding: 10px 20px;
            color: var(--kiboko-blue);
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-outline-gradient:hover {
            background: var(--grad-primary);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease;
        }

        .alert-danger {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-success {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .info-badge {
            background: #f1f5f9;
            padding: 8px 15px;
            border-radius: 30px;
            font-size: 0.8rem;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .info-badge i {
            color: var(--kiboko-blue);
        }

        .password-rules {
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px 15px;
            margin-top: 15px;
        }

        .password-rules p {
            font-size: 0.8rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }

        .password-rules ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .password-rules li {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .password-rules li i {
            font-size: 0.7rem;
            color: #76bc21;
        }

        .nav-tabs-custom {
            border-bottom: 2px solid #e9edf2;
            margin-bottom: 25px;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            font-weight: 600;
            color: #64748b;
            padding: 12px 24px;
            position: relative;
            transition: all 0.3s;
        }

        .nav-tabs-custom .nav-link:hover {
            color: var(--kiboko-blue);
        }

        .nav-tabs-custom .nav-link.active {
            color: var(--kiboko-blue);
            background: transparent;
        }

        .nav-tabs-custom .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--grad-primary);
        }

        .tab-pane {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .readonly-field {
            background: #f8fafc;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .profile-content {
                padding: 20px;
            }
            
            .nav-tabs-custom .nav-link {
                padding: 10px 16px;
                font-size: 0.85rem;
            }
            
            .btn-gradient, .btn-outline-gradient {
                width: 100%;
            }
        }
        
    </style>
</head>
<body>
    <!-- Navigation -->
   <nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="K-CONNECT.png" alt="K-CONNECT">
        </a>
        
        <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                @php getMenu(); @endphp

                @if (isLoggedIn())
                <div class="dropdown ms-3">
                    <a class="nav-link  d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        @if (!empty(session('employee_photo')))
                            <img src="uploads/employees/{{ session('employee_photo') }}" 
                                 width="36" height="36" class="rounded-circle me-2" style="object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" 
                                 style="width: 36px; height: 36px;">
                                <i class="fas fa-user text-gradient"></i>
                            </div>
                        @endif
                        <span class="d-none d-lg-inline">{{ session('employee_name') }}</span>
                    </a>
                     <ul class="dropdown-menu dropdown-menu-end">
                       <li><h6 class="dropdown-header">ID: {!! session('employee_code') !!}</h6></li>
                         <li><a class="dropdown-item" href="my-assets.php"><i class="fas fa-laptop me-2"></i>My Assets</a></li>
                        <li><a class="dropdown-item" href="change-password.php"><i class="fas fa-key me-2"></i>Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

    <!-- Profile Container -->
    <div class="profile-container">
        <div class="container">
            <div class="profile-card" data-aos="fade-up">
                <div class="profile-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h2><i class="fas fa-user-circle me-2"></i>My Profile</h2>
                            <p>Manage your personal information and account settings</p>
                        </div>
                        <div class="info-badge">
                            <i class="fas fa-id-card"></i>
                            <span>Employee Code: <strong>{{ $employee['employee_code'] }}</strong></span>
                        </div>
                    </div>
                </div>

                <div class="profile-content">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs-custom" id="profileTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                                <i class="fas fa-user me-2"></i>Profile Information
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Profile Information Tab -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            @if ($profile_error)
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>{!! $profile_error !!}</span>
                                </div>
                            @endif

                            @if ($profile_success)
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <span>{!! $profile_success !!}</span>
                                </div>
                            @endif

                            <!-- Profile Photo -->
                            <div class="photo-section">
                                <div class="profile-photo-wrapper">
                                    @if ($employee['photo'])
                                        <img src="uploads/employees/{{ $employee['photo'] }}" 
                                             class="profile-photo" alt="Profile Photo" id="profilePhoto">
                                    @else
                                        <div class="profile-photo d-flex align-items-center justify-content-center" 
                                             style="background: linear-gradient(135deg, #009ada20, #76bc2120);">
                                            <i class="fas fa-user fa-4x" style="color: #009ada;"></i>
                                        </div>
                                    @endif
                                    <div class="photo-overlay" onclick="document.getElementById('photoInput').click()">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                </div>
                                <div class="photo-actions">
                                    <form method="POST" enctype="multipart/form-data" id="photoForm" style="display: inline;">
@csrf
                                        <input type="file" name="profile_photo" id="photoInput" class="d-none" accept="image/*">
                                        <input type="hidden" name="upload_photo" value="1">
                                    </form>
                                    @if ($employee['photo'])
                                    <form method="POST" id="removePhotoForm" style="display: inline;">
@csrf
                                        <input type="hidden" name="remove_photo" value="1">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove your profile photo?')">
                                            <i class="fas fa-trash-alt me-1"></i>Remove Photo
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Profile Update Form -->
                            <form method="POST" action="" id="profileForm">
@csrf
                                <input type="hidden" name="update_profile" value="1">
                                
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-id-card"></i>
                                        Basic Information
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Employee Code</label>
                                                <div class="input-wrapper">
                                                    <i class="fas fa-barcode"></i>
                                                    <input type="text" class="form-control readonly-field" 
                                                           value="{{ $employee['employee_code'] }}" 
                                                           readonly disabled>
                                                </div>
                                                <small class="text-muted">Employee code cannot be changed</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Full Name <span class="text-danger">*</span></label>
                                                <div class="input-wrapper">
                                                    <i class="fas fa-user"></i>
                                                    <input type="text" class="form-control" name="full_name" 
                                                           value="{{ $employee['full_name'] }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Email Address <span class="text-danger">*</span></label>
                                                <div class="input-wrapper">
                                                    <i class="fas fa-envelope"></i>
                                                    <input type="email" class="form-control" name="email" 
                                                           value="{{ $employee['email'] }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Company</label>
                                                <div class="input-wrapper">
                                                    <i class="fas fa-building"></i>
                                                    <input type="text" class="form-control readonly-field" 
                                                           value="{{ $employee['company_name'] ?? 'Not Assigned' }}" 
                                                           readonly disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Department</label>
                                                <div class="input-wrapper">
                                                    <i class="fas fa-layer-group"></i>
                                                    <input type="text" class="form-control readonly-field" 
                                                           value="{{ $employee['department'] ?? '—' }}" 
                                                           readonly disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Position</label>
                                                <div class="input-wrapper">
                                                    <i class="fas fa-briefcase"></i>
                                                    <input type="text" class="form-control readonly-field" 
                                                           value="{{ $employee['position'] ?? '—' }}" 
                                                           readonly disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Role</label>
                                                <div class="input-wrapper">
                                                    <i class="fas fa-user-tag"></i>
                                                    <input type="text" class="form-control readonly-field" 
                                                           value="{!! ucfirst($employee['role'] ?? 'Employee') !!}" 
                                                           readonly disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-phone-alt"></i>
                                        Contact Information
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <div class="input-wrapper">
                                                    <i class="fas fa-phone"></i>
                                                    <input type="text" class="form-control" name="phone" 
                                                           value="{{ $employee['phone'] ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Extension</label>
                                                <div class="input-wrapper">
                                                    <i class="fas fa-phone-alt"></i>
                                                    <input type="text" class="form-control" name="extension" 
                                                           value="{{ $employee['extension'] ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Mobile</label>
                                                <div class="input-wrapper">
                                                    <i class="fas fa-mobile-alt"></i>
                                                    <input type="text" class="form-control" name="mobile" 
                                                           value="{{ $employee['mobile'] ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-ambulance"></i>
                                        Emergency Contact
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Contact Person</label>
                                                <div class="input-wrapper">
                                                    <i class="fas fa-user-friends"></i>
                                                    <input type="text" class="form-control" name="emergency_contact" 
                                                           value="{{ $employee['emergency_contact'] ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Emergency Phone</label>
                                                <div class="input-wrapper">
                                                    <i class="fas fa-phone-alt"></i>
                                                    <input type="text" class="form-control" name="emergency_phone" 
                                                           value="{{ $employee['emergency_phone'] ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Additional Information
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Address</label>
                                                <textarea class="form-control" name="address" rows="3">{{ $employee['address'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Notes</label>
                                                <textarea class="form-control" name="notes" rows="3">{{ $employee['notes'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-3">
                                    <button type="submit" class="btn-gradient">
                                        <i class="fas fa-save me-2"></i>Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Change Password Tab -->
                        <div class="tab-pane fade" id="password" role="tabpanel">
                            @if ($error)
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>{!! $error !!}</span>
                                </div>
                            @endif

                            @if ($success)
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <span>{!! $success !!}</span>
                                </div>
                            @endif

                            <div class="form-section">
                                <div class="form-section-title">
                                    <i class="fas fa-lock"></i>
                                    Change Your Password
                                </div>
                                
                                <form method="POST" action="" id="passwordForm">
@csrf
                                    <input type="hidden" name="change_password" value="1">
                                    
                                    <div class="form-group">
                                        <label>Current Password</label>
                                        <div class="input-wrapper">
                                            <i class="fas fa-lock"></i>
                                            <input type="password" class="form-control" name="current_password" id="current_password" required>
                                            <i class="fas fa-eye toggle-password" data-target="current_password"></i>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>New Password</label>
                                                <div class="input-wrapper">
                                                    <i class="fas fa-key"></i>
                                                    <input type="password" class="form-control" name="new_password" id="new_password" required minlength="6">
                                                    <i class="fas fa-eye toggle-password" data-target="new_password"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Confirm New Password</label>
                                                <div class="input-wrapper">
                                                    <i class="fas fa-check-circle"></i>
                                                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                                                    <i class="fas fa-eye toggle-password" data-target="confirm_password"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="password-rules">
                                        <p><i class="fas fa-shield-alt me-2"></i>Password Requirements:</p>
                                        <ul>
                                            <li><i class="fas fa-check-circle"></i> At least 6 characters long</li>
                                            <li><i class="fas fa-check-circle"></i> Should be different from current password</li>
                                            <li><i class="fas fa-check-circle"></i> Use a combination of letters and numbers for strength</li>
                                        </ul>
                                    </div>

                                    <div class="d-flex justify-content-end gap-3 mt-4">
                                        <button type="submit" class="btn-gradient">
                                            <i class="fas fa-save me-2"></i>Change Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 @include('partials.footer')
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        AOS.init({
            duration: 800,
            once: true
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(el => {
                if (el) el.style.display = 'none';
            });
        }, 5000);

        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            });
        });

        // Password form validation
        document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
            const newPass = document.getElementById('new_password').value;
            const confirmPass = document.getElementById('confirm_password').value;
            
            if (newPass !== confirmPass) {
                e.preventDefault();
                alert('New passwords do not match!');
            } else if (newPass.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
            }
        });

        // Photo upload
        document.getElementById('photoInput')?.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Only JPG, PNG, GIF, WEBP allowed.');
                    return;
                }
                
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB.');
                    return;
                }
                
                document.getElementById('photoForm').submit();
            }
        });

        // Keep the active tab after form submission
        document.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash;
            if (hash === '#password') {
                const passwordTab = document.querySelector('[data-bs-target="#password"]');
                if (passwordTab) {
                    const tab = new bootstrap.Tab(passwordTab);
                    tab.show();
                }
            }
        });
    </script>
</body>
</html>
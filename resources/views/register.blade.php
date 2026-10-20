{!! $metaRefresh !!}<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {!! SITE_NAME !!}</title>
    <link rel="icon" type="image/png" href="{{ asset('favi.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad-primary: linear-gradient(135deg, #009ada, #76bc21);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0f4f8 0%, #e8eef5 100%);
            padding: 20px;
        }

        .bg-shape {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            overflow: hidden;
        }

        .bg-shape::before,
        .bg-shape::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
        }

        .bg-shape::before {
            background: var(--kiboko-blue);
            top: -200px;
            left: -200px;
        }

        .bg-shape::after {
            background: var(--kiboko-green);
            bottom: -200px;
            right: -200px;
        }

        .register-container {
            width: 100%;
            max-width: 700px;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 32px;
            padding: 45px 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border: 1px solid rgba(255,255,255,0.5);
            position: relative;
        }

        .register-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--grad-primary);
            border-radius: 32px 32px 0 0;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-section img {
            height: 50px;
            width: auto;
            margin-bottom: 15px;
        }

        .logo-section h1 {
            font-size: 1.8rem;
            font-weight: 700;
            background: var(--grad-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-text {
            text-align: center;
            margin-bottom: 25px;
        }

        .welcome-text h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .welcome-text p {
            color: #64748b;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 5px;
        }

        .form-group .required {
            color: #dc3545;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 1rem;
            transition: all 0.3s;
            z-index: 2;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 12px 14px 12px 44px;
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

        .btn-primary {
            width: 100%;
            padding: 14px;
            background: var(--grad-primary);
            border: none;
            border-radius: 16px;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,154,218,0.3);
        }

        .btn-secondary {
            width: 100%;
            padding: 14px;
            background: transparent;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            color: #64748b;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            margin-top: 12px;
        }

        .btn-secondary:hover {
            border-color: var(--kiboko-blue);
            color: var(--kiboko-blue);
            background: rgba(0,154,218,0.05);
        }

        .alert-message {
            padding: 14px 18px;
            border-radius: 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            animation: slideIn 0.3s ease;
        }

        .alert-success {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .alert-danger {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-info {
            background: #dbeafe;
            border: 1px solid #bfdbfe;
            color: #1e40af;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--kiboko-blue);
        }

        .photo-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e2e8f0;
            margin: 10px auto;
            display: block;
        }

        .photo-upload-box {
            border: 2px dashed #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .photo-upload-box:hover {
            border-color: var(--kiboko-blue);
            background: rgba(0,154,218,0.05);
        }

        .photo-upload-box i {
            font-size: 2rem;
            color: #94a3b8;
        }

        .photo-upload-box p {
            margin: 5px 0 0;
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .footer-note {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .success-redirect {
            text-align: center;
            padding: 20px;
        }

        .success-redirect .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e2e8f0;
            border-top-color: var(--kiboko-blue);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .auto-password-note {
            background: #f0f9ff;
            border: 1px solid #b6d4fe;
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .auto-password-note i {
            color: var(--kiboko-blue);
            font-size: 1.2rem;
        }

        .auto-password-note p {
            margin: 0;
            color: #1e293b;
            font-size: 0.9rem;
        }

        @media (max-width: 480px) {
            .register-card {
                padding: 35px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-shape"></div>

    <div class="register-container">
        <div class="register-card position-relative">
            <div class="logo-section">
                <img src="{{ asset('K-CONNECT.png') }}" onerror="this.onerror=null;this.src='{{ asset('K CONNECT PNG.png') }}'" alt="{!! SITE_NAME !!}">
                <h1>{!! SITE_NAME !!}</h1>
            </div>

            <div class="welcome-text">
                <h2>Create Your Account</h2>
                <p>Fill in all the details below to register</p>
            </div>

            @if ($error)
                <div class="alert-message alert-danger">
                    <i class="fas fa-exclamation-circle fa-lg"></i>
                    <span>{{ $error }}</span>
                </div>
            @endif

            @if ($success)
                <div class="alert-message alert-success">
                    <i class="fas fa-check-circle fa-lg"></i>
                    <span>{{ $success }}</span>
                </div>
                <div class="success-redirect">
                    <div class="spinner"></div>
                    <p>Redirecting to dashboard...</p>
                    <a href="{{ url('login') }}" class="btn-primary" style="width:auto; padding:12px 30px; display:inline-block; margin-top:10px;">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Login Now
                    </a>
                </div>
            @endif

            @if (!$success)
                <form method="POST" action="" id="registerForm" enctype="multipart/form-data">
@csrf
                    <input type="hidden" name="register" value="1">
                    
                    <!-- Auto Password Notice -->
                    <div class="auto-password-note">
                        <i class="fas fa-info-circle"></i>
                        <p><strong>No password needed!</strong> A secure password will be automatically generated and sent to your email.</p>
                    </div>
                    
                    <!-- Section 1: Personal Information -->
                    <div class="section-title">
                        <i class="fas fa-user-circle"></i>
                        Personal Information
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Employee Code <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-id-card"></i>
                                    <input type="text" class="form-control" name="employee_code" 
                                           placeholder="e.g., KEL-XXXX" 
                                           value="{!! isset($post['employee_code']) ? htmlspecialchars($post['employee_code']) : '' !!}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Full Name <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user"></i>
                                    <input type="text" class="form-control" name="full_name" 
                                           placeholder="Enter your full name"
                                           value="{!! isset($post['full_name']) ? htmlspecialchars($post['full_name']) : '' !!}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email Address <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" class="form-control" name="email" 
                                           placeholder="your.email@domain.com"
                                           value="{!! isset($post['email']) ? htmlspecialchars($post['email']) : '' !!}" required>
                                </div>
                                <small class="text-muted">Your login credentials will be sent to this email</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Birth Date <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-calendar-alt"></i>
                                    <input type="date" class="form-control" name="birth_date" 
                                           value="{!! isset($post['birth_date']) ? htmlspecialchars($post['birth_date']) : '' !!}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Join Date <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-calendar-check"></i>
                                    <input type="date" class="form-control" name="join_date" 
                                           value="{!! isset($post['join_date']) ? htmlspecialchars($post['join_date']) : '' !!}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Role</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user-tag"></i>
                                    <select class="form-select" name="role">
                                        <option value="employee" {!! (isset($post['role']) && $post['role'] == 'employee') ? 'selected' : '' !!}>Employee</option>
                                        <option value="manager" {!! (isset($post['role']) && $post['role'] == 'manager') ? 'selected' : '' !!}>Manager</option>
                                        <option value="hr" {!! (isset($post['role']) && $post['role'] == 'hr') ? 'selected' : '' !!}>HR</option>
                                        <option value="director" {!! (isset($post['role']) && $post['role'] == 'director') ? 'selected' : '' !!}>Director</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Contact Information -->
                    <div class="section-title" style="margin-top:20px;">
                        <i class="fas fa-address-card"></i>
                        Contact Information
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone Number <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-phone"></i>
                                    <input type="text" class="form-control" name="phone" 
                                           placeholder="e.g., 0755 933 629"
                                           value="{!! isset($post['phone']) ? htmlspecialchars($post['phone']) : '' !!}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Extension</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-phone-alt"></i>
                                    <input type="text" class="form-control" name="extension" 
                                           placeholder="e.g., 838"
                                           value="{!! isset($post['extension']) ? htmlspecialchars($post['extension']) : '' !!}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mobile Number</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-mobile-alt"></i>
                                    <input type="text" class="form-control" name="mobile" 
                                           placeholder="e.g., 0755 933 629"
                                           value="{!! isset($post['mobile']) ? htmlspecialchars($post['mobile']) : '' !!}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Address</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <input type="text" class="form-control" name="address" 
                                           placeholder="Physical address"
                                           value="{!! isset($post['address']) ? htmlspecialchars($post['address']) : '' !!}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Employment Information -->
                    <div class="section-title" style="margin-top:20px;">
                        <i class="fas fa-briefcase"></i>
                        Employment Information
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Department <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-layer-group"></i>
                                    <input type="text" class="form-control" name="department" 
                                           placeholder="e.g., Information Technology"
                                           value="{!! isset($post['department']) ? htmlspecialchars($post['department']) : '' !!}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Position <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-briefcase"></i>
                                    <input type="text" class="form-control" name="position" 
                                           placeholder="e.g., IT Officer"
                                           value="{!! isset($post['position']) ? htmlspecialchars($post['position']) : '' !!}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Company <span class="required">*</span></label>
                                <div class="input-wrapper">
                                    <i class="fas fa-building"></i>
                                    <select class="form-select" name="company_id" required>
                                        <option value="">Select Company</option>
                                        @foreach ($companies as $company)
                                            <option value="{!! $company['id'] !!}" 
                                                {!! (isset($post['company_id']) && $post['company_id'] == $company['id']) ? 'selected' : '' !!}>
                                                {{ $company['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Notes</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-sticky-note"></i>
                                    <input type="text" class="form-control" name="notes" 
                                           placeholder="Additional notes"
                                           value="{!! isset($post['notes']) ? htmlspecialchars($post['notes']) : '' !!}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Emergency Contact -->
                    <div class="section-title" style="margin-top:20px;">
                        <i class="fas fa-ambulance"></i>
                        Emergency Contact
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Emergency Contact Person</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user-friends"></i>
                                    <input type="text" class="form-control" name="emergency_contact" 
                                           placeholder="Full name"
                                           value="{!! isset($post['emergency_contact']) ? htmlspecialchars($post['emergency_contact']) : '' !!}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Emergency Phone</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-phone-alt"></i>
                                    <input type="text" class="form-control" name="emergency_phone" 
                                           placeholder="Phone number"
                                           value="{!! isset($post['emergency_phone']) ? htmlspecialchars($post['emergency_phone']) : '' !!}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: Profile Photo -->
                    <div class="section-title" style="margin-top:20px;">
                        <i class="fas fa-camera"></i>
                        Profile Photo
                    </div>

                    <div class="form-group">
                        <div class="photo-upload-box" onclick="document.getElementById('photoInput').click()">
                            <i class="fas fa-camera"></i>
                            <p>Click to upload your profile photo</p>
                            <small class="text-muted">JPG, PNG, GIF, WEBP (Max 2MB)</small>
                        </div>
                        <input type="file" class="d-none" name="photo" id="photoInput" accept="image/*">
                        <div id="photoPreview" style="display:none; text-align:center; margin-top:10px;">
                            <img id="previewImage" class="photo-preview" src="#" alt="Preview">
                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removePhoto()">
                                <i class="fas fa-trash"></i> Remove Photo
                            </button>
                        </div>
                    </div>

                    <div class="alert-message alert-info" style="margin-top:15px;">
                        <i class="fas fa-info-circle fa-lg"></i>
                        <span>All fields marked with <span class="required">*</span> are required. A <strong>password will be auto-generated</strong> and sent to your email.</span>
                    </div>

                    <button type="submit" class="btn-primary" id="submitBtn">
                        <i class="fas fa-user-plus"></i>
                        Create Account
                    </button>

                    <div style="text-align:center; margin-top:15px;">
                        <span style="color:#94a3b8;">Already have an account?</span>
                        <a href="{{ url('login') }}" style="color:var(--kiboko-blue); font-weight:600; text-decoration:none; margin-left:5px;">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </div>
                </form>
            @endif

            <div class="footer-note">
                <i class="fas fa-shield-alt me-1"></i> 
                Secure registration • {!! SITE_NAME !!} Employee Portal
            </div>
        </div>
    </div>

    <script>
        // Photo upload preview
        document.getElementById('photoInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('previewImage').src = event.target.result;
                    document.getElementById('photoPreview').style.display = 'block';
                    document.querySelector('.photo-upload-box').style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });

        function removePhoto() {
            document.getElementById('photoInput').value = '';
            document.getElementById('photoPreview').style.display = 'none';
            document.querySelector('.photo-upload-box').style.display = 'block';
        }
    </script>
</body>
</html>
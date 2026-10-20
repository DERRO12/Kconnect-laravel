<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login - {{ SITE_NAME }}</title>
     <link rel="icon" type="image/png" href="{{ asset('favi.png') }}">
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --kiboko-light-green: rgba(118, 188, 33, 0.1);
            --kiboko-light-blue: rgba(0, 154, 218, 0.1);
            --grad-primary: linear-gradient(135deg, #009ada, #76bc21);
            --grad-soft: linear-gradient(135deg, rgba(0,154,218,0.05), rgba(118,188,33,0.05));
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background: #f0f4f8;
        }

        /* ===== LIGHT THEME ANIMATED BACKGROUND ===== */
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        /* Soft Gradient Base */
        .gradient-base {
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(145deg, #f8faff 0%, #f0f5ff 50%, #f5f9f0 100%);
            opacity: 0.8;
            animation: softPulse 15s ease-in-out infinite alternate;
        }

        @keyframes softPulse {
            0% { opacity: 0.6; transform: scale(1); }
            100% { opacity: 1; transform: scale(1.1); }
        }

        /* Floating Kiboko Orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(50px);
            opacity: 0.15;
            animation: floatOrb 20s infinite ease-in-out;
        }

        .orb-1 {
            background: var(--kiboko-blue);
            width: 500px;
            height: 500px;
            top: -150px;
            left: -150px;
            animation-delay: 0s;
        }

        .orb-2 {
            background: var(--kiboko-green);
            width: 450px;
            height: 450px;
            bottom: -150px;
            right: -150px;
            animation-delay: -5s;
        }

        .orb-3 {
            background: #7c3aed;
            width: 300px;
            height: 300px;
            top: 40%;
            right: 20%;
            animation-delay: -10s;
            opacity: 0.1;
        }

        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -20px) scale(1.1); }
            66% { transform: translate(-20px, 30px) scale(0.9); }
            100% { transform: translate(20px, 10px) scale(1); }
        }

        /* Floating Particles */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: linear-gradient(135deg, var(--kiboko-blue), var(--kiboko-green));
            border-radius: 50%;
            opacity: 0.2;
            animation: particleFloat 15s infinite linear;
        }

        @keyframes particleFloat {
            from {
                transform: translateY(100vh) translateX(0) rotate(0deg);
                opacity: 0;
            }
            to {
                transform: translateY(-100vh) translateX(100px) rotate(360deg);
                opacity: 0.4;
            }
        }

        /* Soft Wave Lines */
        .wave-lines {
            position: absolute;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 20px,
                rgba(0, 154, 218, 0.02) 20px,
                rgba(118, 188, 33, 0.02) 40px
            );
            animation: waveMove 30s linear infinite;
        }

        @keyframes waveMove {
            from { background-position: 0 0; }
            to { background-position: 100px 100px; }
        }

        /* Dotted Pattern */
        .dots {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(circle at 30px 30px, rgba(0,154,218,0.1) 2px, transparent 2px);
            background-size: 60px 60px;
            animation: dotsFloat 40s linear infinite;
            opacity: 0.5;
        }

        @keyframes dotsFloat {
            from { transform: translateY(0) rotate(0deg); }
            to { transform: translateY(-50px) rotate(1deg); }
        }

        /* Light Streaks */
        .streak {
            position: absolute;
            width: 200%;
            height: 2px;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(0,154,218,0.1), 
                rgba(118,188,33,0.1), 
                transparent
            );
            animation: streakMove 8s infinite linear;
        }

        .streak-1 {
            top: 20%;
            animation-delay: 0s;
        }

        .streak-2 {
            top: 60%;
            animation-delay: -3s;
            opacity: 0.7;
        }

        @keyframes streakMove {
            from { transform: translateX(-50%) rotate(5deg); }
            to { transform: translateX(0%) rotate(5deg); }
        }

        /* ===== MAIN CONTAINER ===== */
        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 20px;
            position: relative;
            z-index: 10;
        }

        /* Clean White Card */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 32px;
            padding: 45px 40px;
            box-shadow: 
                0 20px 40px -15px rgba(0,0,0,0.1),
                0 0 0 1px rgba(255,255,255,0.8) inset,
                0 0 30px rgba(0,154,218,0.1);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.5);
        }

        /* Subtle Gradient Border */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--kiboko-blue), var(--kiboko-green), var(--kiboko-blue));
            background-size: 200% 100%;
            animation: borderGradient 6s ease infinite;
        }

        @keyframes borderGradient {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Logo Section */
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-section img {
            height: 45px;
            width: auto;
            margin-bottom: 15px;
            filter: drop-shadow(0 5px 10px rgba(0,154,218,0.2));
        }

        .logo-section h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .logo-section .badge {
            background: linear-gradient(135deg, var(--kiboko-light-blue), var(--kiboko-light-green));
            color: #475569;
            font-weight: 600;
            padding: 6px 15px;
            border-radius: 30px;
            font-size: 0.8rem;
            border: 1px solid rgba(0,154,218,0.2);
            display: inline-block;
        }

        /* Welcome Text */
        .welcome-text {
            text-align: center;
            margin-bottom: 30px;
        }

        .welcome-text h2 {
            font-size: 1.4rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .welcome-text p {
            color: #64748b;
            font-size: 0.9rem;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
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
            left: 18px;
            color: #94a3b8;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .input-wrapper .toggle-password {
            left: auto;
            right: 18px;
            cursor: pointer;
            z-index: 2;
        }

        .input-wrapper .toggle-password:hover {
            color: var(--kiboko-blue);
            transform: scale(1.1);
        }

        .form-control {
            width: 100%;
            padding: 16px 18px 16px 52px;
            border: 2px solid #e9edf2;
            border-radius: 18px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #ffffff;
            color: #1e293b;
            font-weight: 500;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 4px rgba(0,154,218,0.1);
            background: #ffffff;
        }

        .form-control:focus + i {
            color: var(--kiboko-blue);
        }

        /* Login Button */
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #1e293b, #334155);
            border: none;
            border-radius: 18px;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        /* Alert Message */
        .alert-message {
            background: #fee2e2;
            border: 1px solid #fecaca;
            border-radius: 16px;
            padding: 14px 18px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #991b1b;
            font-size: 0.9rem;
            font-weight: 500;
            animation: slideIn 0.3s ease;
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

        /* Code Hint - Light Style */
        .code-hint {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px dashed #e2e8f0;
        }

        .code-hint p {
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 15px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            text-align: center;
        }

        .code-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
        }

        .code-item {
            background: #f8fafc;
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #475569;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            cursor: default;
        }

        .code-item i {
            font-size: 0.7rem;
            margin-right: 5px;
            color: var(--kiboko-green);
        }

        .code-item:hover {
            background: linear-gradient(135deg, var(--kiboko-light-blue), var(--kiboko-light-green));
            border-color: var(--kiboko-blue);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,154,218,0.1);
        }

        /* Demo Note */
        .demo-note {
            margin-top: 15px;
            background: #f8fafc;
            border-radius: 14px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid #e2e8f0;
        }

        .demo-note i {
            color: var(--kiboko-blue);
            font-size: 1.1rem;
        }

        .demo-note span {
            font-size: 0.9rem;
            color: #475569;
            font-weight: 600;
        }

        .demo-note small {
            font-size: 0.75rem;
            color: #94a3b8;
            display: block;
            margin-top: 2px;
        }

        /* ===== SECRET ADMIN BUTTON ===== */
        .secret-admin {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .secret-admin:hover {
            opacity: 1;
        }

        .secret-admin a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: rgba(0, 0, 0, 0.03);
            border-radius: 40px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            pointer-events: auto;
        }

        .secret-admin a:hover {
            background: rgba(0, 154, 218, 0.1);
            color: var(--kiboko-blue);
            transform: scale(1.05);
            border-color: var(--kiboko-blue);
        }

        /* Show on hover over bottom right corner */
        .secret-area {
            position: fixed;
            bottom: 0;
            right: 0;
            width: 100px;
            height: 100px;
            z-index: 999;
            cursor: default;
        }

        .secret-area:hover + .secret-admin,
        .secret-admin:hover {
            opacity: 1;
        }

        /* Loading Spinner */
        .loading-spinner {
            display: none;
            width: 22px;
            height: 22px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-login.loading {
            pointer-events: none;
            opacity: 0.9;
        }

        .btn-login.loading .btn-text {
            display: none;
        }

        .btn-login.loading .loading-spinner {
            display: inline-block;
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 25px;
            color: #94a3b8;
            font-size: 0.8rem;
        }

        .login-footer i {
            color: var(--kiboko-blue);
            margin: 0 3px;
            animation: gentlePulse 2s infinite;
        }

        @keyframes gentlePulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 35px 25px;
            }
            
            .logo-section h1 {
                font-size: 1.8rem;
            }
        }

        /* Additional Animations */
        @keyframes gentleFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }

        .logo-section img {
            animation: gentleFloat 4s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <!-- Light Theme Animated Background -->
    <div class="background">
        <div class="gradient-base"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="wave-lines"></div>
        <div class="dots"></div>
        <div class="streak streak-1"></div>
        <div class="streak streak-2"></div>
        <div class="particles" id="particles"></div>
    </div>

    <!-- Secret Admin Access Area -->
    <div class="secret-area"></div>
    <div class="secret-admin">
        <a href="{{ url('admin/login') }}">
            <i class="fas fa-shield-alt"></i>
            <span>Admin Access</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <!-- Main Container -->
    <div class="login-container animate__animated animate__fadeInUp">
        <div class="login-card">
            <!-- Logo -->
            <div class="logo-section">
                <img src="{{ asset('K-CONNECT.png') }}" onerror="this.onerror=null;this.src='{{ asset('K CONNECT PNG.png') }}'" alt="K-Connect">
             
                <span class="badge">Employee Portal</span>
            </div>

            <!-- Welcome Message -->
            <div class="welcome-text">
                <h2>Welcome Back</h2>
                <p>Sign in with your employee credentials</p>
            </div>

            <!-- Error Message -->
            @if ($error)
                <div class="alert-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{!! $error !!}</span>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="" id="loginForm">
                @csrf
                <div class="form-group">
                    <label>Employee Code</label>
                    <div class="input-wrapper">
                        <i class="fas fa-id-card"></i>
                        <input type="text" class="form-control" name="employee_code" 
                                required
                               value="{{ is_string(request()->input('employee_code')) ? request()->input('employee_code') : '' }}"
                               id="employeeCode">
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" name="password" 
                               id="password"  required>
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <span class="btn-text">Sign In</span>
                    <div class="loading-spinner"></div>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <!-- Employee Code Reference -->
           <div class="text-center mt-3">
    <span style="color: #94a3b8; font-size: 0.85rem;">New employee?</span>
    <a href="{{ url('register') }}" style="color: var(--kiboko-blue); font-weight: 600; text-decoration: none; font-size: 0.85rem;">
        Register Here
    </a>
</div>

            <!-- Footer -->
            <div class="login-footer">
               <!-- Add this after the password input group in login -->
<div class="text-end mt-2 mb-3">
    <a href="{{ url('forgot-password') }}" style="font-size: 0.8rem; margin:20px; color: #009ada; text-decoration: none;">
        <i class="fas fa-key me-1"></i>Forgot Password?
    </a>
          


</div>
<!-- Add this after the login button in login, before the login-footer div -->

            </div>
 
        </div>
        
    </div>

    <script>
        // Generate Particles
        function createParticles() {
            const particles = document.getElementById('particles');
            for (let i = 0; i < 30; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.animationDuration = (10 + Math.random() * 20) + 's';
                particle.style.opacity = Math.random() * 0.3;
                particles.appendChild(particle);
            }
        }
        createParticles();

        // Password Toggle
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Form Submission
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');

        loginForm.addEventListener('submit', function(e) {
            const code = document.getElementById('employeeCode');
            const password = document.getElementById('password');
            
            if (!code.value || !password.value) {
                e.preventDefault();
                return;
            }
            
            loginBtn.classList.add('loading');
        });

        // Auto-fill demo credentials
        

       
        // Input focus effects
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('i:first-child').style.color = '#009ada';
            });
            input.addEventListener('blur', function() {
                this.parentElement.querySelector('i:first-child').style.color = '#94a3b8';
            });
        });

        // Secret admin button - double click anywhere in bottom right to reveal
        let clickCount = 0;
        let clickTimer;

        document.addEventListener('click', function(e) {
            const rect = document.body.getBoundingClientRect();
            const isBottomRight = e.clientX > rect.width - 150 && e.clientY > rect.height - 150;
            
            if (isBottomRight) {
                clickCount++;
                
                clearTimeout(clickTimer);
                clickTimer = setTimeout(() => {
                    clickCount = 0;
                }, 1000);
                
                if (clickCount === 3) {
                    document.querySelector('.secret-admin').style.opacity = '1';
                    setTimeout(() => {
                        document.querySelector('.secret-admin').style.opacity = '0';
                    }, 5000);
                    clickCount = 0;
                }
            }
        });
    </script>
</body>
</html>
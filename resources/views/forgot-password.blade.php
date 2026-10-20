<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - K-Connect</title>
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
            background: #f0f4f8;
        }
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            background: linear-gradient(145deg, #f8faff 0%, #f0f5ff 50%, #f5f9f0 100%);
        }
        .reset-container {
            width: 100%;
            max-width: 480px;
            padding: 20px;
            position: relative;
            z-index: 10;
        }
        .reset-card {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 32px;
            padding: 45px 40px;
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.5);
        }
        .reset-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--grad-primary);
            border-radius: 32px 32px 0 0;
        }
        .logo-section { text-align: center; margin-bottom: 30px; }
        .logo-section img { height: 45px; margin-bottom: 15px; }
        .logo-section h1 { font-size: 1.8rem; font-weight: 700; color: #1e293b; }
        .reset-header { text-align: center; margin-bottom: 30px; }
        .reset-header h2 { font-size: 1.5rem; font-weight: 700; color: #1e293b; }
        .reset-header p { color: #64748b; font-size: 0.9rem; }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 8px; }
        .input-wrapper { position: relative; display: flex; align-items: center; }
        .input-wrapper i { position: absolute; left: 18px; color: #94a3b8; }
        .form-control {
            width: 100%;
            padding: 16px 18px 16px 52px;
            border: 2px solid #e9edf2;
            border-radius: 18px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 4px rgba(0,154,218,0.1);
        }
        .btn-reset {
            width: 100%;
            padding: 16px;
            background: var(--grad-primary);
            border: none;
            border-radius: 18px;
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
        .btn-reset:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0,154,218,0.3); }
        .btn-back {
            width: 100%;
            padding: 14px;
            background: transparent;
            border: 2px solid #e2e8f0;
            border-radius: 18px;
            color: #64748b;
            font-weight: 600;
            text-align: center;
            display: inline-block;
            text-decoration: none;
            margin-top: 12px;
        }
        .btn-back:hover { border-color: var(--kiboko-blue); color: var(--kiboko-blue); background: rgba(0,154,218,0.05); }
        .alert-message {
            border-radius: 16px;
            padding: 14px 18px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }
        .alert-message.error { background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-message.success { background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .info-box {
            background: #f8fafc;
            border-radius: 16px;
            padding: 15px 20px;
            margin-top: 25px;
            display: flex;
            gap: 15px;
            border: 1px solid #e2e8f0;
        }
        .info-box i { color: var(--kiboko-blue); font-size: 1.2rem; }
        .info-box p { font-size: 0.8rem; color: #64748b; margin: 0; }
        .login-footer { text-align: center; margin-top: 25px; color: #94a3b8; font-size: 0.75rem; }
        .loading-spinner { display: none; width: 20px; height: 20px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (max-width: 480px) { .reset-card { padding: 35px 25px; } }
    </style>
</head>
<body>
    <div class="background"></div>
    <div class="reset-container">
        <div class="reset-card">
            <div class="logo-section">
                <img src="{{ asset('K-CONNECT.png') }}" onerror="this.onerror=null;this.src='{{ asset('K CONNECT PNG.png') }}'" alt="K-Connect">
             
            </div>
            <div class="reset-header">
                <h2>Forgot Password?</h2>
                <p>Enter your employee code to reset your password</p>
            </div>
            @if ($error)
                <div class="alert-message error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $error }}</span>
                </div>
            @endif
            @if ($success)
                <div class="alert-message success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ $success }}</span>
                </div>
            @endif
            <form method="POST" action="" id="resetForm">
@csrf
                <div class="form-group">
                    <label>Employee Code</label>
                    <div class="input-wrapper">
                        <i class="fas fa-id-card"></i>
                        <input type="text" class="form-control" name="employee_code" id="employeeCode" required autocomplete="off" placeholder="e.g., KEL-xxx">
                    </div>
                </div>
                <button type="submit" class="btn-reset" id="resetBtn">
                    <i class="fas fa-paper-plane"></i>
                    <span>Send Reset Password</span>
                    <div class="loading-spinner"></div>
                </button>
                <a href="{{ url('login') }}" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i>Back to Login
                </a>
            </form>
            <div class="info-box">
                <i class="fas fa-envelope"></i>
                <p>A new password will be sent to your registered email address.</p>
            </div>
            <div class="login-footer">
                <i class="fas fa-shield-alt"></i> Secure Password Reset • K-Connect Portal
            </div>
        </div>
    </div>
    <script>
        const resetForm = document.getElementById('resetForm');
        const resetBtn = document.getElementById('resetBtn');
        const btnIcon = resetBtn.querySelector('i:first-child');
        const btnText = resetBtn.querySelector('span');
        const spinner = resetBtn.querySelector('.loading-spinner');
        resetForm.addEventListener('submit', function(e) {
            const code = document.getElementById('employeeCode');
            if (!code.value.trim()) {
                e.preventDefault();
                return;
            }
            btnIcon.style.display = 'none';
            btnText.style.display = 'none';
            spinner.style.display = 'inline-block';
            resetBtn.disabled = true;
        });
    </script>
</body>
</html>
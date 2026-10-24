@verbatim


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Certificate - Kiboko LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .verify-container {
            max-width: 800px;
            width: 100%;
        }
        
        .verify-card {
            background: white;
            border-radius: 32px;
            padding: 3rem;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
        }
        
        .verify-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .verify-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--kiboko-blue) 0%, var(--kiboko-green) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }
        
        .search-box {
            background: var(--kiboko-light);
            border-radius: 60px;
            padding: 0.5rem;
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }
        
        .search-box input {
            flex: 1;
            border: none;
            background: transparent;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            outline: none;
        }
        
        .search-box button {
            background: var(--grad-primary);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-base);
        }
        
        .search-box button:hover {
            background: var(--grad-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .certificate-info {
            text-align: center;
            padding: 2rem;
            background: var(--kiboko-light);
            border-radius: 24px;
        }
        
        .valid-badge {
            display: inline-block;
            padding: 0.5rem 2rem;
            background: #d4edda;
            color: #155724;
            border-radius: 50px;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .invalid-badge {
            display: inline-block;
            padding: 0.5rem 2rem;
            background: #f8d7da;
            color: #721c24;
            border-radius: 50px;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .info-item {
            text-align: left;
        }
        
        .info-label {
            color: var(--kiboko-gray);
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--kiboko-dark);
        }
        
        .back-link {
            display: inline-block;
            margin-top: 1.5rem;
            color: white;
            text-decoration: none;
            opacity: 0.9;
            transition: opacity 0.3s;
        }
        
        .back-link:hover {
            opacity: 1;
            color: white;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-card">
            <div class="verify-header">
                <h1>Certificate Verification</h1>
                <p class="text-muted">Enter the certificate number to verify its authenticity</p>
            </div>
            
            <form method="GET" class="search-box">
                <input type="text" name="code" placeholder="Enter certificate number (e.g., CERT-2024-12345)" 
                       value="<?php echo htmlspecialchars($certificate_code); ?>" required>
                <button type="submit">
                    <i class="bi bi-search"></i> Verify
                </button>
            </form>
            
            <?php if (!empty($certificate_code)): ?>
                <?php if ($valid): ?>
                    <div class="certificate-info">
                        <div class="valid-badge">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            VALID CERTIFICATE
                        </div>
                        
                        <h2 class="mb-4"><?php echo htmlspecialchars($certificate['full_name']); ?></h2>
                        
                        <p class="lead mb-4">
                            successfully completed the course
                        </p>
                        
                        <h3 class="text-primary mb-4"><?php echo htmlspecialchars($certificate['course_title']); ?></h3>
                        
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Certificate Number</div>
                                <div class="info-value"><?php echo htmlspecialchars($certificate['certificate_number']); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Employee Code</div>
                                <div class="info-value"><?php echo htmlspecialchars($certificate['employee_code']); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label">Issue Date</div>
                                <div class="info-value"><?php echo date('F j, Y', strtotime($certificate['issued_date'])); ?></div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="generate-certificate?course_id=<?php echo $certificate['course_id']; ?>" class="btn btn-outline-primary" target="_blank">
                                <i class="bi bi-eye"></i> View Certificate
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="certificate-info">
                        <div class="invalid-badge">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            INVALID CERTIFICATE
                        </div>
                        
                        <i class="bi bi-shield-x display-1 text-muted mb-3"></i>
                        <h4 class="text-muted mb-3">Certificate Not Found</h4>
                        <p>The certificate number you entered could not be found in our database.</p>
                        <p class="text-muted small mt-3">
                            Please check the certificate number and try again.<br>
                            Certificate numbers are in the format: CERT-YYYY-XXXXX
                        </p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <div class="text-center">
            <a href="index" class="back-link">
                <i class="bi bi-arrow-left me-2"></i>
                Back to Home
            </a>
        </div>
    </div>
</body>
</html>
@endverbatim

{{-- Access denied page printed by die() in admin/normalize-birthdays --}}
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Access Denied</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background: #f8fafc; display: flex; align-items: center; justify-content: center; height: 100vh; }
            .error-card { background: white; border-radius: 15px; padding: 40px; text-align: center; max-width: 500px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
            .error-icon { font-size: 4rem; color: #dc3545; margin-bottom: 20px; }
            h2 { color: #1e293b; margin-bottom: 15px; }
            p { color: #64748b; margin-bottom: 25px; }
            .btn-back { background: linear-gradient(135deg, #009ada 0%, #76bc21 100%); color: white; border: none; padding: 10px 30px; border-radius: 10px; text-decoration: none; display: inline-block; }
        </style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body>
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2>Access Denied</h2>
            <p>You need administrator privileges to access this page. Please contact your system administrator if you believe this is an error.</p>
            <a href="{{ url('admin/manage-birthdays') }}" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i>Back to Management
            </a>
        </div>
    </body>
    </html>
    
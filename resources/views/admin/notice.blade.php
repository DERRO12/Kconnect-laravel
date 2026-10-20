{{-- Simple admin message page: database errors and features whose tables are not installed. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - K-Connect Admin</title>
    <link rel="icon" type="image/png" href="../favi.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .main-content { margin-left: 280px; padding: 48px 28px; min-height: 100vh; }
        .notice-card { max-width: 640px; margin: 40px auto; background: #fff; border: 1px solid #eef2f6; border-radius: 20px; padding: 40px; text-align: center; box-shadow: 0 10px 30px rgba(15, 23, 42, .06); }
        .notice-icon { width: 72px; height: 72px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 30px; margin-bottom: 18px; }
        .notice-icon.warning { background: #fef3c7; color: #d97706; }
        .notice-icon.danger { background: #fee2e2; color: #dc2626; }
        .btn-gradient { background: linear-gradient(135deg, #009ada, #76bc21); color: #fff; border: none; padding: 10px 24px; border-radius: 30px; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-gradient:hover { color: #fff; opacity: .9; }
        @media (max-width: 992px) { .main-content { margin-left: 0; padding: 20px 14px; } }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    <div class="main-content">
        <div class="notice-card">
            <div class="notice-icon {{ $tone ?? 'warning' }}"><i class="fas {{ $icon ?? 'fa-exclamation-triangle' }}"></i></div>
            <h3 class="fw-bold mb-2">{{ $heading }}</h3>
            <p class="text-muted mb-4">{{ $text }}</p>
            <a href="{{ $backUrl ?? 'dashboard' }}" class="btn-gradient"><i class="fas fa-arrow-left me-2"></i>{{ $backLabel ?? 'Back to Dashboard' }}</a>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Nomination | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --kiboko-green: #76bc21; --kiboko-blue: #009ada; --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%); }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .sidebar { width: 280px; background: white; height: 100vh; position: fixed; left: 0; top: 0; box-shadow: 2px 0 20px rgba(0,0,0,0.05); overflow-y: auto; }
        .sidebar-logo { padding: 25px 20px; text-align: center; border-bottom: 1px solid #eef2f6; }
        .sidebar-logo h2 { font-size: 1.5rem; font-weight: 800; background: var(--grad); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .sidebar-menu { padding: 20px 0; }
        .sidebar-menu a { display: flex; align-items: center; gap: 12px; padding: 12px 24px; color: #475569; text-decoration: none; transition: all 0.3s; margin: 4px 12px; border-radius: 12px; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: linear-gradient(135deg, rgba(0,154,218,0.1) 0%, rgba(118,188,33,0.1) 100%); color: var(--kiboko-blue); }
        .sidebar-menu a i { width: 24px; }
        .main-content { margin-left: 280px; padding: 24px 32px; }
        .detail-card { background: white; border-radius: 24px; padding: 28px; margin-bottom: 24px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid #eef2f6; }
        .profile-avatar { width: 80px; height: 80px; border-radius: 50%; background: var(--grad); display: flex; align-items: center; justify-content: center; color: white; font-size: 32px; font-weight: 700; overflow: hidden; flex-shrink: 0; }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .answer-box { background: #f8fafc; border-radius: 16px; padding: 16px; margin-top: 8px; }
        .btn-gradient { background: var(--grad); color: white; border: none; padding: 10px 24px; border-radius: 40px; font-weight: 600; transition: all 0.3s; }
        .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,154,218,0.3); color: white; }
        .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 40px; font-weight: 600; font-size: 14px; }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; padding: 16px; } }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <div class="main-content">
        @if (session()->has('nomination_error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('nomination_error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (request()->has('msg') && request()->query('msg') == 'updated')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Status updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Nomination Details</h2>
                <p class="text-muted">Review nomination and update status</p>
            </div>
            <a href="nomination-dashboard" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>

        <!-- Category Info -->
        <div class="detail-card">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width: 50px; height: 50px; background: {!! $nom['color'] ?? '#009ada' !!}20; border-radius: 15px; display: flex; align-items: center; justify-content: center; color: {!! $nom['color'] ?? '#009ada' !!}; font-size: 24px;">
                    <i class="fas fa-{!! $nom['icon'] ?? 'star' !!}"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">{{ $nom['category_name'] ?? '' }}</h4>
                    <p class="text-muted mb-0">{{ $nom['category_desc'] ?? '' }}</p>
                </div>
                <div class="ms-auto">
                    <span class="status-badge {!! ($nom['status'] ?? 'pending') == 'pending' ? 'bg-warning text-dark' : (($nom['status'] ?? '') == 'reviewed' ? 'bg-success text-white' : (($nom['status'] ?? '') == 'approved' ? 'bg-primary text-white' : 'bg-danger text-white')) !!}">
                        <i class="fas fa-{!! ($nom['status'] ?? 'pending') == 'pending' ? 'clock' : (($nom['status'] ?? '') == 'reviewed' ? 'check' : (($nom['status'] ?? '') == 'approved' ? 'check-circle' : 'times')) !!}"></i>
                        {!! ucfirst($nom['status'] ?? 'Pending') !!}
                    </span>
                </div>
            </div>
            <small class="text-muted">Submitted on {!! date('F j, Y \a\t g:i A', strtotime($nom['created_at'] ?? '')) !!}</small>
        </div>

        <!-- Nominator & Nominee -->
        <div class="row">
            <div class="col-md-6">
                <div class="detail-card">
                    <h5 class="fw-bold mb-3"><i class="fas fa-user-check me-2"></i>Nominator</h5>
                    <div class="d-flex gap-3">
                        <div class="profile-avatar">
                            @if (!empty($nom['nominator_photo']) && file_exists(kc_upload_path('employees/' . $nom['nominator_photo'])))
                                <img src="../uploads/employees/{{ $nom['nominator_photo'] }}" alt="{{ $nom['nominator_name'] ?? '' }}">
                            @else
                                {!! strtoupper(substr($nom['nominator_name'] ?? 'U', 0, 1)) !!}
                            @endif
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">{{ $nom['nominator_name'] ?? '' }}</h6>
                            <p class="text-muted mb-1">{{ $nom['nominator_code'] ?? '' }}</p>
                            <p class="text-muted mb-0">{{ $nom['nominator_dept'] ?? '' }} • {{ $nom['nominator_position'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="detail-card">
                    <h5 class="fw-bold mb-3"><i class="fas fa-user-star me-2"></i>Nominee</h5>
                    <div class="d-flex gap-3">
                        <div class="profile-avatar">
                            @if (!empty($nom['nominee_photo']) && file_exists(kc_upload_path('employees/' . $nom['nominee_photo'])))
                                <img src="../uploads/employees/{{ $nom['nominee_photo'] }}" alt="{{ $nom['nominee_name'] ?? '' }}">
                            @else
                                {!! strtoupper(substr($nom['nominee_name'] ?? 'U', 0, 1)) !!}
                            @endif
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">{{ $nom['nominee_name'] ?? '' }}</h6>
                            <p class="text-muted mb-1">{{ $nom['nominee_code'] ?? '' }}</p>
                            <p class="text-muted mb-0">{{ $nom['nominee_dept'] ?? '' }} • {{ $nom['nominee_position'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Answers -->
        <div class="detail-card">
            <h5 class="fw-bold mb-3"><i class="fas fa-clipboard-list me-2"></i>Nomination Responses</h5>
            @if (count($answers) > 0)
                @foreach ($answers as $ans)
                    <div class="mb-4">
                        <div class="fw-bold mb-2">{{ $ans['question_text'] ?? '' }}</div>
                        <div class="answer-box">
                            @if (($ans['question_type'] ?? '') == 'rating')
                                <div class="d-flex gap-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {!! $i <= (int)($ans['answer'] ?? 0) ? 'text-warning' : 'text-muted' !!}" style="font-size: 20px;"></i>
                                    @endfor
                                    <span class="ms-2">({!! (int)($ans['answer'] ?? 0) !!}/5)</span>
                                </div>
                            @elseif (($ans['question_type'] ?? '') == 'attachment' && str_starts_with((string) ($ans['answer'] ?? ''), 'uploads/nomination_attachments/'))
                                <a href="../{{ $ans['answer'] }}" target="_blank" rel="noopener"><i class="fas fa-paperclip me-1"></i>{{ basename($ans['answer']) }}</a>
                            @elseif (($ans['question_type'] ?? '') == 'checkbox')
                                <div>{!! nl2br(htmlspecialchars($ans['answer'] ?? '')) !!}</div>
                            @else
                                <div>{!! nl2br(htmlspecialchars($ans['answer'] ?? '')) !!}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted">No responses found.</p>
            @endif
        </div>

        <!-- Update Status -->
        <div class="detail-card">
            <h5 class="fw-bold mb-3"><i class="fas fa-cog me-2"></i>Update Status</h5>
            <form method="POST">
@csrf
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="pending" {!! ($nom['status'] ?? '') == 'pending' ? 'selected' : '' !!}>Pending</option>
                            <option value="reviewed" {!! ($nom['status'] ?? '') == 'reviewed' ? 'selected' : '' !!}>Reviewed</option>
                            <option value="approved" {!! ($nom['status'] ?? '') == 'approved' ? 'selected' : '' !!}>Approved</option>
                            <option value="rejected" {!! ($nom['status'] ?? '') == 'rejected' ? 'selected' : '' !!}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Admin Notes</label>
                        <input type="text" name="admin_notes" class="form-control" value="{{ $nom['admin_notes'] ?? '' }}" placeholder="Add any notes...">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" name="update_status" class="btn-gradient w-100">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
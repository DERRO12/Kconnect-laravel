<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Emails - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
        }
        .main-content {
            margin-left: 280px;
            padding: 24px 32px;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
            transition: all 0.3s;
        }
        .card:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #eef2f6;
            padding: 20px 25px;
            font-weight: 700;
            border-radius: 20px 20px 0 0 !important;
        }
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 40px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.2);
            color: white;
        }
        .btn-gradient:disabled {
            opacity: 0.6;
            transform: none;
        }
        .stat-card {
            padding: 24px;
            border-radius: 16px;
            background: white;
            border: 1px solid #eef2f6;
            text-align: center;
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 4px;
        }
        .stat-label {
            color: #64748b;
            font-size: 14px;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 24px;
        }
        .stat-blue .stat-icon { background: #e6f0ff; color: var(--kiboko-blue); }
        .stat-green .stat-icon { background: #dcfce7; color: var(--kiboko-green); }
        .stat-orange .stat-icon { background: #fef3c7; color: #f59e0b; }
        .stat-purple .stat-icon { background: #ede9fe; color: #8b5cf6; }
        .stat-blue .stat-number { color: var(--kiboko-blue); }
        .stat-green .stat-number { color: var(--kiboko-green); }
        .stat-orange .stat-number { color: #f59e0b; }
        .stat-purple .stat-number { color: #8b5cf6; }
        
        .result-alert {
            border-radius: 16px;
            padding: 16px 20px;
        }
        .preview-frame {
            width: 100%;
            min-height: 400px;
            border: 1px solid #eef2f6;
            border-radius: 12px;
            background: white;
        }
        .preview-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 400px;
            background: #f8fafc;
            border-radius: 12px;
        }
        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--grad);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: none;
            box-shadow: 0 5px 20px rgba(0,154,218,0.3);
            z-index: 99;
            transition: all 0.3s;
        }
        .scroll-top:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,154,218,0.4);
        }
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 16px; }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1"><i class="fas fa-envelope me-2 text-gradient"></i>Email Management</h2>
                <p class="text-muted">Send notifications and reminders to employees</p>
            </div>
            <div>
                <a href="manage-quarters.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Quarters
                </a>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-blue">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-number">{!! number_format($totalEmployees) !!}</div>
                    <div class="stat-label">Total Active Employees</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-green">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-number">{!! number_format($submitted) !!}</div>
                    <div class="stat-label">Submitted for {!! $currentQuarterName !!}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-orange">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-number">{!! number_format($pending) !!}</div>
                    <div class="stat-label">Pending Submissions</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-purple">
                    <div class="stat-icon"><i class="fas fa-birthday-cake"></i></div>
                    <div class="stat-number">{!! number_format($birthdayCount) !!}</div>
                    <div class="stat-label">Birthdays in {!! $currentMonthName !!}</div>
                </div>
            </div>
        </div>
        
        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="alert alert-success result-alert alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @php session()->forget('success'); @endphp
        @endif
        
        @if (session()->has('error'))
            <div class="alert alert-danger result-alert alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @php session()->forget('error'); @endphp
        @endif
        
        @if ($emailResult && isset($emailResult['errors']) && !empty($emailResult['errors']))
            <div class="alert alert-warning result-alert">
                <div class="d-flex align-items-start gap-3">
                    <i class="fas fa-info-circle fa-2x mt-1"></i>
                    <div>
                        <h6 class="fw-bold">⚠️ Some emails failed to send</h6>
                        <div class="mt-2 small">
                            <button class="btn btn-sm btn-outline-danger" onclick="document.getElementById('errorList').style.display = document.getElementById('errorList').style.display === 'none' ? 'block' : 'none'">
                                <i class="fas fa-chevron-down me-1"></i> Show Failed
                            </button>
                            <div id="errorList" style="display: none;" class="mt-2">
                                <ul class="list-unstyled">
                                    @foreach ($emailResult['errors'] as $error)
                                        <li><i class="fas fa-times-circle text-danger me-2"></i>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Nomination Reminder Card -->
        <div class="card mb-4" id="nominationForm">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-trophy me-2 text-gradient"></i>
                        <span>Nomination Reminder</span>
                    </div>
                    <span class="badge" style="background: var(--grad); padding: 6px 14px;">{!! $currentQuarterName !!}</span>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    Send reminder emails to employees who haven't submitted nominations for the current quarter.
                    <br><strong>Pending: {!! number_format($pending) !!}</strong> employees haven't submitted yet.
                </p>
                
                <form method="POST" class="row g-3">
                @csrf
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Quarter</label>
                        <select name="quarter" class="form-select">
                            <option value="1" {!! $currentQuarter == 1 ? 'selected' : '' !!}>Q1 (Jan-Mar)</option>
                            <option value="2" {!! $currentQuarter == 2 ? 'selected' : '' !!}>Q2 (Apr-Jun)</option>
                            <option value="3" {!! $currentQuarter == 3 ? 'selected' : '' !!}>Q3 (Jul-Sep)</option>
                            <option value="4" {!! $currentQuarter == 4 ? 'selected' : '' !!}>Q4 (Oct-Dec)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Year</label>
                        <input type="number" name="year" class="form-control" value="{!! $currentYear !!}" required>
                    </div>
                    <div class="col-md-5 d-flex align-items-end gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="previewNominationEmail()">
                            <i class="fas fa-eye me-2"></i>Preview
                        </button>
                        <button type="submit" name="send_nomination_reminder" class="btn-gradient" onclick="return confirm('Send nomination reminder emails to {!! number_format($pending) !!} employees?')">
                            <i class="fas fa-paper-plane me-2"></i>Send Reminders
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Birthday Notification Card -->
        <div class="card mb-4" id="birthdayForm">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-birthday-cake me-2 text-gradient"></i>
                        <span>Birthday Notifications</span>
                    </div>
                    <span class="badge" style="background: linear-gradient(135deg, #fbbf24, #f59e0b); padding: 6px 14px;">
                        {!! $birthdayCount !!} in {!! $currentMonthName !!}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    Send birthday wishes to all employees, featuring colleagues celebrating birthdays this month.
                    <br><strong>{!! $birthdayCount !!}</strong> birthday(s) in {!! $currentMonthName !!}.
                </p>
                
                <form method="POST" class="row g-3">
                @csrf
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Month</label>
                        <select name="month" class="form-select">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{!! $m !!}" {!! $m == date('m') ? 'selected' : '' !!}>
                                    {!! date('F', mktime(0, 0, 0, $m, 1)) !!}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-8 d-flex align-items-end gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="previewBirthdayEmail()">
                            <i class="fas fa-eye me-2"></i>Preview
                        </button>
                        <button type="submit" name="send_birthday_emails" class="btn-gradient" style="background: linear-gradient(135deg, #fbbf24, #f59e0b);" onclick="return confirm('Send birthday notifications to all employees?')">
                            <i class="fas fa-paper-plane me-2"></i>Send Birthday Wishes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-2 text-gradient"></i>Email Preview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="previewContent">
                        <div class="preview-loading">
                            <div>
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-3 text-muted">Loading preview...</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll to Top -->
    <button class="scroll-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Scroll to top button
        window.addEventListener('scroll', function() {
            const btn = document.querySelector('.scroll-top');
            if (btn) {
                btn.style.display = window.pageYOffset > 300 ? 'flex' : 'none';
            }
        });
        
        // Preview functions
        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
        
        function previewNominationEmail() {
            const form = document.querySelector('#nominationForm form');
            const quarter = form.querySelector('[name="quarter"]').value;
            const year = form.querySelector('[name="year"]').value;
            
            document.getElementById('previewContent').innerHTML = `
                <div class="preview-loading">
                    <div>
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 text-muted">Loading preview...</p>
                    </div>
                </div>
            `;
            previewModal.show();
            
            fetch(`send-emails.php?action=preview_nomination&quarter=${quarter}&year=${year}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('previewContent').innerHTML = `
                            <div class="mb-2 text-muted small">Preview for: ${data.employee}</div>
                            <div class="preview-frame">${data.preview}</div>
                        `;
                    } else {
                        document.getElementById('previewContent').innerHTML = `
                            <div class="alert alert-danger">${data.error || 'Error loading preview'}</div>
                        `;
                    }
                })
                .catch(error => {
                    document.getElementById('previewContent').innerHTML = `
                        <div class="alert alert-danger">Error: ${error.message}</div>
                    `;
                });
        }
        
        function previewBirthdayEmail() {
            const form = document.querySelector('#birthdayForm form');
            const month = form.querySelector('[name="month"]').value;
            
            document.getElementById('previewContent').innerHTML = `
                <div class="preview-loading">
                    <div>
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 text-muted">Loading preview...</p>
                    </div>
                </div>
            `;
            previewModal.show();
            
            fetch(`send-emails.php?action=preview_birthday&month=${month}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('previewContent').innerHTML = `
                            <div class="mb-2 text-muted small">${data.birthday_count} birthday(s) this month</div>
                            <div class="preview-frame">${data.preview}</div>
                        `;
                    } else {
                        document.getElementById('previewContent').innerHTML = `
                            <div class="alert alert-danger">${data.error || 'Error loading preview'}</div>
                        `;
                    }
                })
                .catch(error => {
                    document.getElementById('previewContent').innerHTML = `
                        <div class="alert alert-danger">Error: ${error.message}</div>
                    `;
                });
        }
        
        // Auto-refresh stats every 30 seconds
        function refreshStats() {
            fetch('send-emails.php?action=get_stats')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector('.stat-blue .stat-number').textContent = data.total_employees.toLocaleString();
                        document.querySelector('.stat-green .stat-number').textContent = data.submitted.toLocaleString();
                        document.querySelector('.stat-orange .stat-number').textContent = data.pending.toLocaleString();
                        document.querySelector('.stat-purple .stat-number').textContent = data.birthday_count.toLocaleString();
                    }
                })
                .catch(error => console.log('Stats refresh failed:', error));
        }
        setInterval(refreshStats, 30000);
    </script>
</body>
</html>
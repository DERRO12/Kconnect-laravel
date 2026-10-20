<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Support - K-Connect Admin</title>
    <link rel="icon" type="image/png" href="../favi.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --kiboko-dark: #0f172a;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
        }
        
        .page-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 30px 0;
            border-bottom: 1px solid #eef2f6;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            transition: all 0.3s;
            border: 1px solid #eef2f6;
            text-align: center;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: var(--kiboko-dark);
        }
        
        .stat-label {
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
        }
        
        .ticket-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #eef2f6;
            transition: all 0.3s;
        }
        
        .ticket-card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-New { background: #fee2e2; color: #dc2626; }
        .status-In-Progress { background: #dbeafe; color: #2563eb; }
        .status-Resolved { background: #dcfce7; color: #16a34a; }
        .status-Closed { background: #f3f4f6; color: #6b7280; }
        
        .priority-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .priority-Low { background: #f3f4f6; color: #6b7280; }
        .priority-Medium { background: #dbeafe; color: #2563eb; }
        .priority-High { background: #fef3c7; color: #d97706; }
        .priority-Urgent { background: #fee2e2; color: #dc2626; }
        
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 40px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.3);
            color: white;
        }
        
        .btn-outline-gradient {
            background: transparent;
            border: 2px solid var(--kiboko-blue);
            color: var(--kiboko-blue);
            border-radius: 40px;
            padding: 10px 24px;
            font-weight: 600;
        }
        
        .modal-content {
            border: none;
            border-radius: 24px;
        }
        
        .ticket-detail-section {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .comment-item {
            background: white;
            border-radius: 16px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #eef2f6;
        }
        
        .admin-comment {
            border-left: 4px solid var(--kiboko-blue);
            background: #f0f9ff;
        }
        
        .filters-bar {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid #eef2f6;
        }
        
        .profile-avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(0,154,218,0.1) 0%, rgba(118,188,33,0.1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .profile-avatar-sm img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .action-btn:hover {
            background: var(--grad);
            color: white;
            border-color: transparent;
        }
        
        .admin-badge {
            background: var(--grad);
            color: white;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            margin-left: 8px;
        }
        .main-content { margin-left: 280px; padding: 0 0 30px; min-height: 100vh; }
        .main-content .page-header { padding: 24px 0; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <div class="main-content">
    <div class="page-header">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="fw-bold mb-1"><i class="fas fa-headset me-2" style="color: #009ada;"></i>IT Support</h1>
                    <p class="text-muted mb-0">Review, reply to and update all IT support tickets</p>
                    <span class="admin-badge mt-2 d-inline-block"><i class="fas fa-shield-alt me-1"></i>Admin Access - Viewing All Tickets</span>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="it-admin" class="btn-outline-gradient text-decoration-none"><i class="fas fa-chart-pie me-2"></i>Analytics</a>
                    @if ($canWrite)
                    <button class="btn-gradient" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                        <i class="fas fa-plus-circle me-2"></i>New Ticket
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4 mb-5">
        @if ($message)
            <div class="alert alert-{{ $message_type }} alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @unless ($canWrite)
            <div class="alert alert-warning small">
                <i class="fas fa-info-circle me-1"></i>Your admin account is not linked to an employee record (same e-mail address), so you can update ticket status here but not raise tickets or post replies.
            </div>
        @endunless

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            @foreach ([['total', 'Total Tickets', ''], ['new_count', 'New', '#dc2626'], ['in_progress', 'In Progress', '#2563eb'], ['urgent', 'Urgent', '#d97706'], ['resolved', 'Resolved', '#16a34a'], ['closed', 'Closed', '#6b7280']] as [$key, $label, $color])
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <div class="stat-value" @if ($color) style="color: {{ $color }};" @endif>{{ (int) ($stats[$key] ?? 0) }}</div>
                    <div class="stat-label">{{ $label }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Filters -->
        <div class="filters-bar">
            <form method="GET" action="it-support" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold small">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Ticket #, subject, employee..." value="{{ $search }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        @foreach ($statuses as $s)
                            <option value="{{ $s }}" @selected($status_filter == $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="">All</option>
                        @foreach ($priorities as $p)
                            <option value="{{ $p }}" @selected($priority_filter == $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-gradient w-100">
                            <i class="fas fa-filter me-2"></i>Apply Filters
                        </button>
                        <a class="btn-outline-gradient text-decoration-none" href="?{{ http_build_query(array_filter(['export_excel' => 1, 'status' => $status_filter, 'priority' => $priority_filter, 'search' => $search], 'strlen')) }}">
                            <i class="fas fa-file-excel me-2"></i>Export
                        </a>
                        @if ($status_filter || $priority_filter || $search)
                            <a href="it-support" class="btn-outline-gradient text-decoration-none">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Tickets List -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="fas fa-ticket-alt me-2 text-gradient"></i>All Tickets <small class="text-muted fs-6">({{ count($tickets) }})</small></h3>
        </div>

        @forelse ($tickets as $ticket)
            <div class="ticket-card">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="profile-avatar-sm">
                                @if (!empty($ticket['photo']))
                                    <img src="../uploads/employees/{{ $ticket['photo'] }}" alt="">
                                @else
                                    <i class="fas fa-user"></i>
                                @endif
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">#{{ $ticket['ticket_number'] }}</h6>
                                <small class="text-muted">{{ $ticket['employee_name'] }} ({{ $ticket['employee_code'] }})</small>
                                <div class="small text-muted text-truncate" style="max-width: 260px;">{{ $ticket['subject'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <span class="badge bg-light text-dark me-2">
                            <i class="fas fa-folder me-1"></i>{{ $ticket['category'] }}
                        </span>
                    </div>
                    <div class="col-md-2">
                        <span class="priority-badge priority-{{ $ticket['priority'] }}">{{ $ticket['priority'] }}</span>
                    </div>
                    <div class="col-md-2">
                        <span class="status-badge status-{{ str_replace(' ', '-', $ticket['status']) }}">{{ $ticket['status'] }}</span>
                    </div>
                    <div class="col-md-3 text-md-end">
                        <small class="text-muted d-block">{{ date('M d, Y', strtotime($ticket['created_at'])) }}</small>
                        <small class="text-muted"><i class="far fa-comment me-1"></i>{{ $ticket['comment_count'] }} {{ $ticket['comment_count'] == 1 ? 'reply' : 'replies' }}</small>
                    </div>
                </div>
                <hr class="my-3">
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-outline-gradient" onclick="viewTicket({{ (int) $ticket['id'] }})">
                        <i class="fas fa-eye me-1"></i>View Details
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-gradient" onclick="editTicket({{ (int) $ticket['id'] }})">
                        <i class="fas fa-edit me-1"></i>Update Status
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-white rounded-4 border">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No tickets found</h4>
                <p class="text-muted">{{ ($status_filter || $priority_filter || $search) ? 'No tickets match your filters.' : 'No IT support tickets have been raised yet.' }}</p>
            </div>
        @endforelse
    </div>
    </div>

    <!-- New Ticket Modal -->
    @if ($canWrite)
    <div class="modal fade" id="newTicketModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="it-support" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus-circle me-2 text-gradient"></i>Submit New Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Priority <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select" required>
                                    <option value="Low">Low - Minor issue, no impact on work</option>
                                    <option value="Medium" selected>Medium - Affects work but workaround exists</option>
                                    <option value="High">High - Major work disruption</option>
                                    <option value="Urgent">Urgent - Complete work stoppage</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject" class="form-control" required maxlength="255" placeholder="Brief summary of the issue">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control" rows="4" required placeholder="Please provide detailed information about the issue..."></textarea>
                            </div>

                            @if (count($questions) > 0)
                                <div class="col-12">
                                    <hr>
                                    <h6 class="fw-bold mb-3">Additional Information</h6>
                                </div>
                                @foreach ($questions as $q)
                                    @php $qn = 'q_'.$q['id']; $req = $q['is_required'] ? 'required' : ''; @endphp
                                    <div class="col-12">
                                        <label class="form-label fw-bold">
                                            {{ $q['question_text'] }}
                                            @if ($q['is_required'])<span class="text-danger">*</span>@endif
                                        </label>
                                        @if ($q['question_type'] == 'text')
                                            <input type="text" name="{{ $qn }}" class="form-control" {{ $req }}>
                                        @elseif ($q['question_type'] == 'textarea')
                                            <textarea name="{{ $qn }}" class="form-control" rows="3" {{ $req }}></textarea>
                                        @elseif ($q['question_type'] == 'radio')
                                            <div class="d-flex flex-wrap gap-3">
                                                @foreach (array_map('trim', explode(',', (string) $q['options'])) as $oi => $opt)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="{{ $qn }}" value="{{ $opt }}" id="{{ $qn }}_{{ $oi }}" {{ $req }}>
                                                        <label class="form-check-label" for="{{ $qn }}_{{ $oi }}">{{ $opt }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif ($q['question_type'] == 'checkbox')
                                            @foreach (array_map('trim', explode(',', (string) $q['options'])) as $oi => $opt)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="{{ $qn }}[]" value="{{ $opt }}" id="{{ $qn }}_{{ $oi }}">
                                                    <label class="form-check-label" for="{{ $qn }}_{{ $oi }}">{{ $opt }}</label>
                                                </div>
                                            @endforeach
                                        @elseif ($q['question_type'] == 'number')
                                            <input type="number" name="{{ $qn }}" class="form-control" {{ $req }}>
                                        @elseif ($q['question_type'] == 'file')
                                            <input type="file" name="{{ $qn }}" class="form-control" accept="image/*,.pdf,.doc,.docx,.txt">
                                            <small class="text-muted">Max file size: 5MB. Supported: JPG, PNG, PDF, DOC</small>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="submit_ticket" value="1" class="btn-gradient">Submit Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Ticket Detail Modal -->
    <div class="modal fade" id="ticketDetailModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-ticket-alt me-2 text-gradient"></i>Ticket Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="ticketDetailContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-3">Loading ticket details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="editTicketModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="it-support">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit me-2 text-gradient"></i>Update Ticket Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="ticket_id" id="edit_ticket_id">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ticket #</label>
                            <p class="form-control-static" id="edit_ticket_number"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Employee</label>
                            <p class="form-control-static" id="edit_employee_name"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Subject</label>
                            <p class="form-control-static" id="edit_subject"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select" id="edit_status">
                                @foreach ($statuses as $s)
                                    <option value="{{ $s }}">{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Notes</label>
                            <textarea name="admin_notes" class="form-control" rows="4" id="edit_notes" placeholder="Add notes about the resolution (sent to the employee and added to the conversation)..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_ticket_status" value="1" class="btn-gradient">Update Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewTicket(ticketId) {
            var box = document.getElementById('ticketDetailContent');
            box.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3">Loading ticket details...</p></div>';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('ticketDetailModal')).show();
            fetch('it-ticket-detail?id=' + ticketId + '&is_admin=1')
                .then(function (r) { if (!r.ok) throw new Error(r.status); return r.text(); })
                .then(function (html) { box.innerHTML = html; })
                .catch(function () { box.innerHTML = '<div class="text-center py-5 text-danger">Error loading ticket details</div>'; });
        }

        function editTicket(id) {
            fetch('it-ticket-data?id=' + id)
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.error) { alert(data.error); return; }
                    document.getElementById('edit_ticket_id').value = data.id;
                    document.getElementById('edit_ticket_number').textContent = '#' + data.ticket_number;
                    document.getElementById('edit_employee_name').textContent = data.employee_name + ' (' + data.employee_code + ')';
                    document.getElementById('edit_subject').textContent = data.subject;
                    document.getElementById('edit_status').value = data.status;
                    document.getElementById('edit_notes').value = '';
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('editTicketModal')).show();
                })
                .catch(function () { alert('Could not load the ticket.'); });
        }

        // Auto-hide messages
        setTimeout(function () {
            document.querySelectorAll('.alert-dismissible').forEach(function (el) { el.remove(); });
        }, 5000);
    </script>
</body>
</html>

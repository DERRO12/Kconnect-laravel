<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Support - K-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
    <style>
        :root{--kiboko-blue:#009ada;--grad:linear-gradient(135deg,#009ada,#76bc21)}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:#f8fafc}
        .page-header{background:linear-gradient(135deg,#fff,#f8fafc);padding:20px 0;border-bottom:1px solid #eef2f6;margin-bottom:24px}
        .stat-card{background:#fff;border-radius:16px;padding:20px;border:1px solid #eef2f6;text-align:center;transition:.2s}
        .stat-card:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.06)}
        .stat-value{font-size:28px;font-weight:800}
        .stat-label{color:#64748b;font-size:13px;font-weight:500}
        .stat-card.today{border-top:3px solid #009ada}
        .stat-card.unsolved{border-top:3px solid #dc3545}
        .ticket-card{background:#fff;border-radius:16px;padding:20px;margin-bottom:16px;border:1px solid #eef2f6;cursor:pointer;transition:.2s}
        .ticket-card:hover{box-shadow:0 8px 20px rgba(0,0,0,.06)}
        .ticket-card.status-new{border-left:4px solid #dc2626}
        .ticket-card.status-in-progress{border-left:4px solid #2563eb}
        .ticket-card.status-resolved{border-left:4px solid #16a34a}
        .ticket-card.status-closed{border-left:4px solid #6b7280}
        .btn-gradient{background:var(--grad);color:#fff;border:none;padding:10px 24px;border-radius:30px;font-weight:600;cursor:pointer}
        .btn-gradient:hover{opacity:.9;color:#fff}
        .btn-outline-gradient{background:transparent;border:2px solid var(--kiboko-blue);color:var(--kiboko-blue);border-radius:30px;padding:6px 16px;font-weight:600;cursor:pointer;font-size:13px}
        .btn-outline-gradient:hover{background:var(--kiboko-blue);color:#fff}
        .btn-quick-status{padding:2px 10px;font-size:10px;border-radius:20px;background:#f8fafc;border:1px solid #e2e8f0;cursor:pointer;font-weight:600}
        .btn-quick-status:hover{background:#e2e8f0}
        .modal-content{border:none;border-radius:20px}
        .filters-bar{background:#fff;border-radius:14px;padding:16px;margin-bottom:20px;border:1px solid #eef2f6}
        .profile-avatar-sm{width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,rgba(0,154,218,.1),rgba(118,188,33,.1));display:flex;align-items:center;justify-content:center;overflow:hidden}
        .profile-avatar-sm img{width:100%;height:100%;object-fit:cover}
        .admin-badge{background:var(--grad);color:#fff;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:600}
        .paste-preview-container{display:flex;flex-wrap:wrap;gap:8px;margin:8px 0}
        .paste-preview-item{position:relative;border:2px solid #eef2f6;border-radius:10px;overflow:hidden}
        .paste-preview-item img{max-width:80px;max-height:80px;object-fit:cover;display:block}
        .paste-preview-item .remove-image{position:absolute;top:3px;right:3px;background:rgba(0,0,0,.7);color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:10px;cursor:pointer;display:flex;align-items:center;justify-content:center}
        .image-modal-overlay{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.9);z-index:9999;justify-content:center;align-items:center}
        .image-modal-overlay.active{display:flex}.image-modal-overlay img{max-width:90%;max-height:90%}
        .image-modal-close{position:absolute;top:20px;right:30px;color:#fff;font-size:40px;cursor:pointer}
        .live-dot{display:inline-block;width:7px;height:7px;border-radius:50%;background:#16a34a;animation:pulse 2s infinite;margin-right:6px;vertical-align:middle}
        @keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}
        .btn-export{background:#28a745;color:#fff;border:none;padding:10px 20px;border-radius:30px;font-weight:600;cursor:pointer}
        .btn-export:hover{opacity:.9;color:#fff}
        .action-buttons{display:flex;gap:5px;flex-wrap:wrap}
        .btn-close-ticket{background:#dc3545;color:#fff;border:none;padding:2px 10px;border-radius:20px;font-size:10px;font-weight:600;cursor:pointer}
        .btn-close-ticket:hover{opacity:.9;color:#fff}
        .close-only-notice{font-size:10px;color:#6c757d;font-style:italic}
        .ticket-owner-badge{background:#e9ecef;color:#495057;padding:2px 8px;border-radius:12px;font-size:10px}
        .admin-created-badge{background:#ffc107;color:#000;padding:2px 8px;border-radius:12px;font-size:10px}
        .select2-container .select2-selection--single{height:38px;border-radius:8px;border:1px solid #ced4da}
        .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:38px}
        .select2-container--default .select2-selection--single .select2-selection__arrow{height:36px}
    </style>
</head>
<body>
    @include('partials.navbar')
    <div class="image-modal-overlay" id="imageModal" onclick="closeImageModal()"><span class="image-modal-close">&times;</span><img src="" id="imageModalImg"></div>
    
    <div class="page-header"><div class="container"><div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div><h2 class="fw-bold mb-1"><i class="fas fa-headset me-2" style="color:#009ada;"></i>IT Support <span class="live-dot"></span></h2><p class="text-muted mb-0 small">Submit and track tickets@php if ($is_admin): echo ' <span class="admin-badge ms-2">Admin</span>'; endif; @endphp</p>
<br>
@if ($is_admin)
<a href="backup-log" class="btn" style="background: linear-gradient(135deg, #76bc21, #5a9a15); color: white; border: none; padding: 10px 22px; border-radius: 30px; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; box-shadow: 0 4px 12px rgba(118, 188, 33, 0.3);" 
   onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 18px rgba(118, 188, 33, 0.4)';" 
   onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(118, 188, 33, 0.3)';">
    <i class="fas fa-cloud-upload-alt"></i> Daily Backup Log
</a>
@endif
</div>
        <div class="d-flex gap-2">
            <a href="?export=csv{!! !empty($status_filter) ? '&status='.urlencode($status_filter) : '' !!}{!! !empty($priority_filter) ? '&priority='.urlencode($priority_filter) : '' !!}{!! !empty($search) ? '&search='.urlencode($search) : '' !!}{!! !empty($date_from) ? '&date_from='.urlencode($date_from) : '' !!}{!! !empty($date_to) ? '&date_to='.urlencode($date_to) : '' !!}{!! !empty($date_range) ? '&date_range='.urlencode($date_range) : '' !!}" class="btn-export"><i class="fas fa-file-export me-1"></i> Export</a>
            <button class="btn-gradient" data-bs-toggle="modal" data-bs-target="#newTicketModal"><i class="fas fa-plus-circle me-1"></i> New Ticket</button>
        </div>
    </div></div></div>
    
    
    <div class="container mb-5">
        @if (session()->has('message'))<div class="alert alert-{!! session('message_type')??'info' !!} alert-dismissible fade show py-2 small">{!! session()->pull('message') !!}@php session()->forget('message_type'); @endphp<button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button></div>@endif
        
        <div class="row g-3 mb-4">
            <div class="col-3 col-md-2"><div class="stat-card"><div class="stat-value">{!! $stats['total'] !!}</div><div class="stat-label">Total</div></div></div>
            <div class="col-3 col-md-2"><div class="stat-card"><div class="stat-value" style="color:#dc2626;">{!! $stats['new_count'] !!}</div><div class="stat-label">New</div></div></div>
            <div class="col-3 col-md-2"><div class="stat-card"><div class="stat-value" style="color:#2563eb;">{!! $stats['in_progress'] !!}</div><div class="stat-label">In Progress</div></div></div>
            @if ($is_admin)<div class="col-3 col-md-2"><div class="stat-card"><div class="stat-value" style="color:#d97706;">{!! $stats['urgent'] !!}</div><div class="stat-label">Urgent</div></div></div>@endif
            <div class="col-3 col-md-2"><div class="stat-card"><div class="stat-value" style="color:#16a34a;">{!! $stats['resolved'] !!}</div><div class="stat-label">Resolved</div></div></div>
            <div class="col-3 col-md-2"><div class="stat-card"><div class="stat-value" style="color:#6b7280;">{!! $stats['closed'] !!}</div><div class="stat-label">Closed</div></div></div>
        </div>

        <!-- Additional Stats: Today & Unsolved -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card today">
                    <div class="stat-value" style="color:#009ada;">{!! $stats['today_count'] ?? 0 !!}</div>
                    <div class="stat-label"><i class="fas fa-calendar-day me-1"></i>Today's Tickets</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card unsolved">
                    <div class="stat-value" style="color:#dc3545;">{!! $stats['unsolved_count'] ?? 0 !!}</div>
                    <div class="stat-label"><i class="fas fa-exclamation-triangle me-1"></i>Unsolved Tickets</div>
                </div>
            </div>
        </div>
        
        <div class="filters-bar">
            <form method="GET" class="row g-2 align-items-end" id="filterForm">
                <div class="col-md-2">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ $search }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        @foreach (['New','In Progress','Resolved','Closed'] as $s)
                            <option value="{!! $s !!}" {!! $status_filter==$s?'selected':'' !!}>{!! $s !!}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="priority" class="form-select form-select-sm">
                        <option value="">All Priority</option>
                        @foreach (['Low','Medium','High','Urgent'] as $p)
                            <option value="{!! $p !!}" {!! $priority_filter==$p?'selected':'' !!}>{!! $p !!}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="date_range" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Time</option>
                        <option value="today" {!! $date_range=='today'?'selected':'' !!}>Today</option>
                        <option value="week" {!! $date_range=='week'?'selected':'' !!}>This Week</option>
                        <option value="month" {!! $date_range=='month'?'selected':'' !!}>This Month</option>
                        <option value="unsolved" {!! $date_range=='unsolved'?'selected':'' !!}>Unsolved</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{!! $date_from !!}" placeholder="From">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{!! $date_to !!}" placeholder="To">
                </div>
                <div class="col-md-12 mt-2">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Filter</button>
                    <a href="it-support" class="btn btn-secondary btn-sm"><i class="fas fa-undo"></i> Reset</a>
                    <a href="?export=csv{!! !empty($status_filter) ? '&status='.urlencode($status_filter) : '' !!}{!! !empty($priority_filter) ? '&priority='.urlencode($priority_filter) : '' !!}{!! !empty($search) ? '&search='.urlencode($search) : '' !!}{!! !empty($date_from) ? '&date_from='.urlencode($date_from) : '' !!}{!! !empty($date_to) ? '&date_to='.urlencode($date_to) : '' !!}{!! !empty($date_range) ? '&date_range='.urlencode($date_range) : '' !!}" class="btn btn-success btn-sm"><i class="fas fa-file-export"></i> Export CSV</a>
                </div>
            </form>
        </div>
        
        <h5 class="fw-bold mb-3">{!! $is_admin?'All Tickets':'My Tickets' !!} <small class="text-muted">({!! $my_tickets ? count($my_tickets) : 0 !!})</small>
        @if (!$is_admin)
        <small class="close-only-notice ms-2"><i class="fas fa-info-circle"></i> You can only close tickets you raised</small>
        @else
        <small class="close-only-notice ms-2"><i class="fas fa-info-circle"></i> Admins: Can set to New, In Progress, Resolved (Closed only by ticket creator)</small>
        @endif
        </h5>
        
        
        @if ($my_tickets && count($my_tickets)>0)@foreach ($my_tickets as $ticket)@php $sc=strtolower(str_replace(' ','-',$ticket['status'])); @endphp
            <div class="ticket-card status-{!! $sc !!}">
                <div class="row align-items-center" onclick="viewTicket({!! $ticket['id'] !!},{!! $is_admin?1:0 !!})">
                    <div class="col-md-2"><div class="d-flex align-items-center gap-2">@if ($is_admin)<div class="profile-avatar-sm">@if (!empty($ticket['photo']))<img src="uploads/employees/{!! $ticket['photo'] !!}">@else<i class="fas fa-user"></i>@endif</div>@endif<div><strong>#{{ $ticket['ticket_number'] }}</strong>@if ($is_admin)<br><small class="text-muted">{{ $ticket['employee_name']??'' }}</small>@endif</div></div></div>
                    <div class="col-md-3"><small>{{ substr($ticket['subject'],0,40) }}</small></div>
                    <div class="col-md-1"><span class="badge bg-light text-dark">{!! $ticket['category'] !!}</span></div>
                    <div class="col-md-1"><span class="badge bg-{!! $ticket['priority']=='Urgent'?'danger':($ticket['priority']=='High'?'warning':'info') !!}">{!! $ticket['priority'] !!}</span></div>
                    <div class="col-md-1"><span class="badge bg-{!! $ticket['status']=='New'?'danger':($ticket['status']=='In Progress'?'primary':($ticket['status']=='Resolved'?'success':'secondary')) !!}">{!! $ticket['status'] !!}</span></div>
                    <div class="col-md-1"><small>{{ $ticket['assigned_to_name']??'N/A' }}</small></div>
                    <div class="col-md-2 text-end"><small class="text-muted">{!! date('M d',strtotime($ticket['created_at'])) !!}</small></div>
                </div>
                @if ($is_admin && isset($ticket['created_by_admin']) && $ticket['created_by_admin'] == 1)
                    <div class="mt-1"><span class="admin-created-badge"><i class="fas fa-user-cog"></i> Created by Admin</span></div>
                @endif
                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2" onclick="event.stopPropagation()">
                    @if ($is_admin)
                        <div class="action-buttons">
                            @php
                            // Admin can change to New, In Progress, Resolved
                            $admin_statuses = ['New'=>'🔴','In Progress'=>'🔵','Resolved'=>'🟢'];
                            @endphp
                            @foreach ($admin_statuses as $sk=>$sv)
                                @if ($sk != $ticket['status'])
                                <form method="POST" style="display:inline;">
@csrf
                                    <input type="hidden" name="form_token" value="{!! $form_token !!}">
                                    <input type="hidden" name="ticket_id" value="{!! $ticket['id'] !!}">
                                    <input type="hidden" name="status" value="{!! $sk !!}">
                                    <input type="hidden" name="admin_notes" value="Status changed to {!! $sk !!} by admin">
                                    <button type="submit" name="update_ticket_status" class="btn-quick-status">{!! $sv !!}</button>
                                </form>
                            @endif
                            @endforeach
                            <span class="badge bg-secondary text-white" style="font-size:9px;">Cannot Close</span>
                        </div>
                    @else
                        @if ($ticket['employee_id'] == $employee_id && $ticket['status'] != 'Closed')
                            <form method="POST" style="display:inline;">
@csrf
                                <input type="hidden" name="form_token" value="{!! $form_token !!}">
                                <input type="hidden" name="ticket_id" value="{!! $ticket['id'] !!}">
                                <input type="hidden" name="status" value="Closed">
                                <input type="hidden" name="admin_notes" value="Ticket closed by requester">
                                <button type="submit" name="update_ticket_status" class="btn-close-ticket"><i class="fas fa-check me-1"></i>Close Ticket</button>
                            </form>
                        @elseif ($ticket['status'] == 'Closed')
                            <span class="badge bg-secondary">Closed</span>
                        @else
                            <span class="badge bg-secondary">View Only</span>
                        @endif
                    @endif
                    
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-gradient" onclick="viewTicket({!! $ticket['id'] !!},{!! $is_admin?1:0 !!})"><i class="fas fa-eye"></i></button>
                        @if ($is_admin)
                            <button class="btn btn-sm btn-outline-gradient" onclick="editTicket({!! $ticket['id'] !!})"><i class="fas fa-edit"></i></button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach @else<div class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-2"></i><p>No tickets found</p></div>@endif
    </div>

    <!-- New Ticket Modal -->
    <div class="modal fade" id="newTicketModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
        <form method="POST" action="it-support" enctype="multipart/form-data">
@csrf
            <input type="hidden" name="form_token" value="{!! $form_token !!}">
            <div class="modal-header"><h5 class="modal-title">Submit New Ticket</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><div class="row g-2">
                @if ($is_admin)
                <div class="col-12">
                    <label class="form-label small">Ticket Owner (Who is raising this ticket?) *</label>
                    <select name="ticket_owner" id="ticket_owner_select" class="form-select form-select-sm" required style="width:100%;">
                        <option value="{!! $employee_id !!}" selected>Myself ({{ $employee_name }})</option>
                        <option value="">-- Search for employee --</option>
                    </select>
                    <small class="text-muted">Start typing to search for an employee. The selected person will receive email notifications.</small>
                </div>
                @endif
                <div class="col-md-6"><label class="form-label small">Category *</label><select name="category" class="form-select form-select-sm" required><option value="">-- Select --</option>@foreach ($categories as $cat)<option value="{{ $cat }}">{{ $cat }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label small">Priority *</label><select name="priority" class="form-select form-select-sm" required><option value="">-- Select --</option><option value="Low">Low</option><option value="Medium" selected>Medium</option><option value="High">High</option><option value="Urgent">Urgent</option></select></div>
                <div class="col-12"><label class="form-label small">Assign To *</label><select name="assigned_to" class="form-select form-select-sm" required><option value="">-- Select --</option>@foreach ($admin_employees as $a)<option value="{!! $a['id'] !!}">{{ $a['full_name'] }}</option>@endforeach</select></div>
                <div class="col-12"><label class="form-label small">Subject *</label><input type="text" name="subject" class="form-control form-control-sm" required></div>
                <div class="col-12"><label class="form-label small">Description *</label><textarea name="description" class="form-control form-control-sm" rows="3" required id="ticketDescription" placeholder="Describe issue... Ctrl+V to paste images"></textarea><div class="paste-preview-container" id="ticketPastePreview" style="display:none;"></div><input type="hidden" name="pasted_images" id="ticketPastedImages" value="[]"></div>
                <div class="col-12"><label class="form-label small">Attachments</label><input type="file" name="attachments[]" class="form-control form-control-sm" multiple></div>
            </div></div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button type="submit" name="submit_ticket" class="btn btn-primary btn-sm">Submit Ticket</button></div>
        </form>
    </div></div></div>

    <!-- Detail Modal -->
    <div class="modal fade" id="ticketDetailModal" tabindex="-1"><div class="modal-dialog modal-xl"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Ticket Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" id="ticketDetailContent"><div class="text-center py-4"><div class="spinner-border spinner-border-sm"></div></div></div></div></div></div>

    <!-- Edit Modal -->
    @if ($is_admin)
    <div class="modal fade" id="editTicketModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><form method="POST">
@csrf<input type="hidden" name="form_token" value="{!! $form_token !!}"><div class="modal-header bg-primary text-white"><h5 class="modal-title">Update Ticket</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" name="ticket_id" id="edit_ticket_id"><div class="row g-2">
        <div class="col-6"><label class="small">Ticket #</label><p class="fw-bold" id="edit_ticket_number"></p></div><div class="col-6"><label class="small">Employee</label><p id="edit_employee_name"></p></div>
        <div class="col-12"><label class="small">Subject</label><p id="edit_subject"></p></div>
        <div class="col-md-6"><label class="small">Status *</label><select name="status" class="form-select form-select-sm" id="edit_status" required>
            @foreach (['New','In Progress','Resolved'] as $k)<option value="{!! $k !!}">{!! $k !!}</option>@endforeach
            <option value="Closed" disabled>Closed (Only ticket creator can close)</option>
        </select><small class="text-muted">Admins cannot close tickets</small></div>
        <div class="col-md-6"><label class="small">Reassign</label><select name="assigned_to" class="form-select form-select-sm" id="edit_assigned_to"><option value="">Keep</option>@foreach ($admin_employees as $a)<option value="{!! $a['id'] !!}">{{ $a['full_name'] }}</option>@endforeach</select></div>
        <div class="col-12"><label class="small">Notes</label><textarea name="admin_notes" class="form-control form-control-sm" rows="2" id="edit_notes" placeholder="Optional notes about status change"></textarea></div>
    </div></div><div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button type="submit" name="update_ticket_status" class="btn btn-primary btn-sm">Update</button></div></form></div></div></div>
    @endif

    @include('partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        // Initialize Select2 for employee search
        $('#ticket_owner_select').select2({
            placeholder: 'Search for employee...',
            allowClear: true,
            dropdownParent: $('#newTicketModal'),
            ajax: {
                url: 'it-support?ajax=search_employees',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.full_name + ' (' + (item.employee_code || '') + ') - ' + (item.department || '')
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        });
    });

    function openImageModal(u){document.getElementById('imageModalImg').src=u;document.getElementById('imageModal').classList.add('active');}
    function closeImageModal(){document.getElementById('imageModal').classList.remove('active');}
    document.addEventListener('keydown',function(e){if(e.key==='Escape')closeImageModal();});
    function viewTicket(id,ia){var c=document.getElementById('ticketDetailContent');c.innerHTML='<div class="text-center py-4"><div class="spinner-border spinner-border-sm"></div></div>';new bootstrap.Modal(document.getElementById('ticketDetailModal')).show();fetch('it-support?ajax=ticket_detail&id='+id+'&is_admin='+(ia?1:0)).then(function(r){return r.text();}).then(function(d){c.innerHTML=d;setTimeout(function(){document.querySelectorAll('textarea[name="comment"]').forEach(function(t){var m=t.id.match(/commentTextarea_(\d+)/);if(m)initReplyPasteSupport(m[1]);});},200);}).catch(function(){c.innerHTML='<div class="text-center text-danger">Error</div>';});}
    @if ($is_admin)
    function editTicket(id){fetch('it-ticket-data?id='+id).then(function(r){return r.json();}).then(function(d){document.getElementById('edit_ticket_id').value=d.id;document.getElementById('edit_ticket_number').innerHTML='#'+d.ticket_number;document.getElementById('edit_employee_name').innerHTML=d.employee_name;document.getElementById('edit_subject').innerHTML=d.subject;document.getElementById('edit_status').value=d.status;document.getElementById('edit_assigned_to').value=d.assigned_to||'';document.getElementById('edit_notes').value=d.admin_notes||'';new bootstrap.Modal(document.getElementById('editTicketModal')).show();});}
    @endif
    (function(){var ta=document.getElementById('ticketDescription');if(!ta)return;var pv=document.getElementById('ticketPastePreview');var hd=document.getElementById('ticketPastedImages');var imgs=[];ta.addEventListener('paste',function(e){var items=e.clipboardData.items;var h=false;for(var i=0;i<items.length;i++){if(items[i].type.indexOf('image')!==-1){h=true;var r=new FileReader();r.onload=function(ev){imgs.push(ev.target.result);up();uh();};r.readAsDataURL(items[i].getAsFile());}}if(h)e.preventDefault();});function up(){pv.innerHTML='';if(!imgs.length){pv.style.display='none';return;}pv.style.display='flex';imgs.forEach(function(u,i){var d=document.createElement('div');d.className='paste-preview-item';var img=document.createElement('img');img.src=u;d.appendChild(img);var b=document.createElement('button');b.className='remove-image';b.innerHTML='×';b.onclick=function(e){e.stopPropagation();imgs.splice(i,1);up();uh();};d.appendChild(b);pv.appendChild(d);});}function uh(){hd.value=JSON.stringify(imgs);}})();
    function initReplyPasteSupport(tid){var ta=document.getElementById('commentTextarea_'+tid);if(!ta||ta.dataset.init)return;ta.dataset.init='1';var pv=document.getElementById('paste_preview_'+tid);var hd=document.getElementById('pasted_images_'+tid);if(!pv||!hd)return;var imgs=[];ta.addEventListener('paste',function(e){var items=e.clipboardData.items;var h=false;for(var i=0;i<items.length;i++){if(items[i].type.indexOf('image')!==-1){h=true;var r=new FileReader();r.onload=function(ev){imgs.push(ev.target.result);up();uh();};r.readAsDataURL(items[i].getAsFile());}}if(h)e.preventDefault();});function up(){pv.innerHTML='';if(!imgs.length){pv.style.display='none';return;}pv.style.display='flex';imgs.forEach(function(u,i){var d=document.createElement('div');d.className='paste-preview-item';var img=document.createElement('img');img.src=u;d.appendChild(img);var b=document.createElement('button');b.className='remove-image';b.innerHTML='×';b.onclick=function(e){e.stopPropagation();imgs.splice(i,1);up();uh();};d.appendChild(b);pv.appendChild(d);});}function uh(){hd.value=JSON.stringify(imgs);}}
    var lastId={!! $max_id !!};
    setInterval(function(){fetch('it-support?ajax=refresh_tickets&last_id='+lastId).then(function(r){return r.json();}).then(function(d){if(d.tickets&&d.tickets.length){d.tickets.forEach(function(t){if(t.id>lastId)lastId=t.id;});var n=document.createElement('div');n.className='alert alert-info position-fixed top-0 end-0 m-2 py-1 px-2 small';n.style.zIndex='9999';n.innerHTML='<i class="fas fa-bell me-1"></i>'+d.tickets.length+' new <a href="javascript:location.reload()">Refresh</a>';document.body.appendChild(n);setTimeout(function(){n.remove();},6000);}}).catch(function(){});},15000);
    setTimeout(function(){document.querySelectorAll('.alert').forEach(function(e){e.remove();});},5000);
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Whistleblowing Reports - Admin</title>
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

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .main-content { margin-left: 280px; padding: 24px 32px; min-height: 100vh; }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            border: 1px solid #eef2f6;
            text-align: center;
            transition: all 0.3s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .stat-card .number { font-size: 32px; font-weight: 800; color: var(--kiboko-dark); }
        .stat-card .label { color: #64748b; font-size: 13px; font-weight: 500; }
        .stat-card.urgent .number { color: #dc2626; }
        .stat-card.new .number { color: var(--kiboko-blue); }
        .stat-card.resolved .number { color: #16a34a; }

        .filters-bar {
            background: white;
            border-radius: 16px;
            padding: 18px 20px;
            margin-bottom: 20px;
            border: 1px solid #eef2f6;
        }

        .table-container {
            background: white;
            border-radius: 20px;
            overflow: auto;
            border: 1px solid #eef2f6;
        }
        .table-container table {
            width: 100%;
            margin-bottom: 0;
            min-width: 1000px;
        }
        .table-container thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 14px;
            border-bottom: 2px solid #eef2f6;
            white-space: nowrap;
            position: sticky;
            top: 0;
        }
        .table-container tbody td {
            padding: 10px 14px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }
        .table-container tbody tr:hover { background: #f8fafc; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }
        .status-new { background: #fee2e2; color: #dc2626; }
        .status-under_review { background: #fef3c7; color: #d97706; }
        .status-investigating { background: #dbeafe; color: #2563eb; }
        .status-resolved { background: #dcfce7; color: #16a34a; }
        .status-closed { background: #f3f4f6; color: #6b7280; }
        .status-dismissed { background: #f3e8ff; color: #7c3aed; }

        .submission-type {
            font-size: 11px;
            padding: 2px 10px;
            border-radius: 20px;
        }
        .submission-type.anonymous { background: #f1f5f9; color: #475569; }
        .submission-type.confidential { background: #fef3c7; color: #92400e; }
        .submission-type.open { background: #dbeafe; color: #1d4ed8; }

        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 40px;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 13px;
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
            padding: 6px 18px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s;
        }
        .btn-outline-gradient:hover {
            background: var(--kiboko-blue);
            color: white;
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
            cursor: pointer;
        }
        .action-btn:hover {
            background: var(--grad);
            color: white;
            border-color: transparent;
        }

        .modal-content { border: none; border-radius: 20px; }
        .modal-header { border-radius: 20px 20px 0 0; }
        .modal .detail-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .modal .detail-label {
            font-weight: 600;
            min-width: 160px;
            color: #475569;
            flex-shrink: 0;
        }
        .modal .detail-value { color: #0f172a; word-break: break-word; }
        .modal .detail-value pre {
            background: #f8fafc;
            padding: 10px 14px;
            border-radius: 6px;
            white-space: pre-wrap;
            font-family: inherit;
            font-size: 14px;
            margin: 4px 0;
        }

        @media (max-width: 992px) {
            .main-content { margin-left: 0; padding: 16px; }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h2 class="fw-bold"><i class="fas fa-shield-alt me-2" style="color: var(--kiboko-blue);"></i>Whistleblowing Reports</h2>
                <p class="text-muted mb-0">Manage and review all submitted reports</p>
            </div>
            <div>
                <span class="badge" style="background:var(--grad); color:white; padding:8px 16px;">
                    <i class="fas fa-lock me-1"></i> Confidential
                </span>
            </div>
        </div>

        @if (isset($success))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{!! $success !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (isset($error))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>{!! $error !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <div class="number">{!! $stats['total'] !!}</div>
                    <div class="label">Total Reports</div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card new">
                    <div class="number">{!! $stats['new'] !!}</div>
                    <div class="label">New</div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <div class="number">{!! $stats['under_review'] !!}</div>
                    <div class="label">Under Review</div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <div class="number">{!! $stats['investigating'] !!}</div>
                    <div class="label">Investigating</div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card resolved">
                    <div class="number">{!! $stats['resolved'] !!}</div>
                    <div class="label">Resolved</div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card urgent">
                    <div class="number">{!! $stats['urgent'] !!}</div>
                    <div class="label">🚨 Urgent</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-bar">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="🔍 Search reference, name..." value="{{ $search }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="new" {!! $status_filter == 'new' ? 'selected' : '' !!}>New</option>
                        <option value="under_review" {!! $status_filter == 'under_review' ? 'selected' : '' !!}>Under Review</option>
                        <option value="investigating" {!! $status_filter == 'investigating' ? 'selected' : '' !!}>Investigating</option>
                        <option value="resolved" {!! $status_filter == 'resolved' ? 'selected' : '' !!}>Resolved</option>
                        <option value="closed" {!! $status_filter == 'closed' ? 'selected' : '' !!}>Closed</option>
                        <option value="dismissed" {!! $status_filter == 'dismissed' ? 'selected' : '' !!}>Dismissed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="anonymous" {!! $type_filter == 'anonymous' ? 'selected' : '' !!}>Anonymous</option>
                        <option value="confidential" {!! $type_filter == 'confidential' ? 'selected' : '' !!}>Confidential</option>
                        <option value="open" {!! $type_filter == 'open' ? 'selected' : '' !!}>Open</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="entity" class="form-select form-select-sm">
                        <option value="">All Entities</option>
                        @foreach ($entities as $e)
                            <option value="{!! $e['entity'] !!}" {!! $entity_filter == $e['entity'] ? 'selected' : '' !!}>
                                {!! $e['entity'] !!}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="urgent" class="form-select form-select-sm">
                        <option value="">Urgent</option>
                        <option value="1" {!! $urgent_filter == '1' ? 'selected' : '' !!}>Yes</option>
                        <option value="0" {!! $urgent_filter == '0' ? 'selected' : '' !!}>No</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <input type="date" name="date_from" class="form-control form-control-sm" placeholder="From" value="{{ $date_from }}">
                        <input type="date" name="date_to" class="form-control form-control-sm" placeholder="To" value="{{ $date_to }}">
                    </div>
                </div>
                <div class="col-md-12 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn-gradient"><i class="fas fa-filter me-1"></i> Apply Filters</button>
                    <a href="whistleblowing-admin.php" class="btn-outline-gradient"><i class="fas fa-times me-1"></i> Clear</a>
                    <a href="?export_excel=1&{!! http_build_query(request()->query()) !!}" class="btn-outline-gradient" style="border-color: #16a34a; color: #16a34a;">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </a>
                </div>
            </form>
        </div>

        <!-- Reports Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Ref #</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Entity</th>
                        <th>Department</th>
                        <th>Submitted</th>
                        <th>Urgent</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($reports) > 0)
                        @foreach ($reports as $row)
                            <tr>
                                <td><strong>{{ $row['reference_number'] }}</strong></td>
                                <td>
                                    <span class="submission-type {!! $row['submission_type'] !!}">
                                        {!! ucfirst($row['submission_type']) !!}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-{!! $row['status'] !!}">
                                        {!! str_replace('_', ' ', ucfirst($row['status'])) !!}
                                    </span>
                                </td>
                                <td>{{ $row['entity'] ?: '—' }}</td>
                                <td>{{ $row['department'] ?: '—' }}</td>
                                <td>{!! date('d M Y, H:i', strtotime($row['created_at'])) !!}</td>
                                <td>{!! $row['urgent'] ? '🚨 Yes' : '—' !!}</td>
                                <td>
                                    <button class="action-btn" title="View Details" onclick="openModal({!! $row['id'] !!})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x d-block mb-3"></i>
                                No reports found matching your filters.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Report Detail Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background:var(--grad);">
                    <h5 class="modal-title text-white fw-bold"><i class="fas fa-shield-alt me-2"></i>Report Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" id="modalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border" style="color:var(--kiboko-blue);"></div>
                        <p class="mt-3 text-muted">Loading report details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openModal(reportId) {
            var modal = document.getElementById('reportModal');
            var body = document.getElementById('modalBody');
            
            body.innerHTML = '<div class="text-center py-5"><div class="spinner-border" style="color:var(--kiboko-blue);"></div><p class="mt-3 text-muted">Loading...</p></div>';
            var bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            
            fetch('whistleblowing-api.php?id=' + reportId)
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        body.innerHTML = '<div class="text-center py-5 text-danger">Error: ' + data.error + '</div>';
                        return;
                    }
                    displayReport(data.report);
                })
                .catch(error => {
                    body.innerHTML = '<div class="text-center py-5 text-danger">Error loading report: ' + error.message + '</div>';
                });
        }
        
        function displayReport(r) {
            var statusOptions = ['new', 'under_review', 'investigating', 'resolved', 'closed', 'dismissed'];
            var statusOptionsHtml = statusOptions.map(function(s) {
                return '<option value="' + s + '" ' + (s === r.status ? 'selected' : '') + '>' + 
                    s.replace('_', ' ').toUpperCase() + 
                    '</option>';
            }).join('');
            
            document.getElementById('modalBody').innerHTML = `
                <div style="padding: 25px 30px;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>Reference:</strong> ${r.reference_number}
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong> 
                            <span class="status-badge status-${r.status}">${r.status.replace('_', ' ').toUpperCase()}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Type:</strong> 
                            <span class="submission-type ${r.submission_type}">${r.submission_type.toUpperCase()}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Submitted:</strong> ${r.created_at_formatted}
                        </div>
                        ${r.full_name ? `<div class="col-md-6"><strong>Full Name:</strong> ${r.full_name}</div>` : ''}
                        ${r.entity ? `<div class="col-md-6"><strong>Entity:</strong> ${r.entity}</div>` : ''}
                        ${r.department ? `<div class="col-md-6"><strong>Department:</strong> ${r.department}</div>` : ''}
                        ${r.contact_method ? `<div class="col-md-6"><strong>Contact:</strong> ${r.contact_method}</div>` : ''}
                        <div class="col-md-6"><strong>Relationship:</strong> ${r.relationship_display} ${r.relationship_other ? ' (' + r.relationship_other + ')' : ''}</div>
                        ${r.nature_concerns ? `<div class="col-12"><strong>Concerns:</strong> ${r.nature_concerns}</div>` : ''}
                        <div class="col-12">
                            <strong>What Happened:</strong>
                            <div class="bg-light p-3 rounded-3 mt-1">${r.what_happened || 'N/A'}</div>
                        </div>
                        ${r.who_involved ? `<div class="col-12"><strong>Who Involved:</strong><div class="bg-light p-3 rounded-3 mt-1">${r.who_involved}</div></div>` : ''}
                        <div class="col-md-6"><strong>Incident Type:</strong> ${r.incident_type}</div>
                        <div class="col-md-6"><strong>Incident Date:</strong> ${r.incident_date || r.incident_from_date || 'N/A'}</div>
                        ${r.incident_location ? `<div class="col-md-6"><strong>Location:</strong> ${r.incident_location}</div>` : ''}
                        ${r.awareness_method ? `<div class="col-md-6"><strong>Awareness:</strong> ${r.awareness_method}</div>` : ''}
                        ${r.estimated_impact ? `<div class="col-md-6"><strong>Impact:</strong> ${r.estimated_impact}</div>` : ''}
                        ${r.evidence_description ? `<div class="col-12"><strong>Evidence:</strong><div class="bg-light p-3 rounded-3 mt-1">${r.evidence_description}</div></div>` : ''}
                        ${r.witnesses ? `<div class="col-12"><strong>Witnesses:</strong><div class="bg-light p-3 rounded-3 mt-1">${r.witnesses}</div></div>` : ''}
                        <div class="col-md-6"><strong>Urgent:</strong> ${r.urgent ? '🚨 Yes' : 'No'}</div>
                        <div class="col-md-6"><strong>Involves Handler:</strong> ${r.involves_handler ? 'Yes' : 'No'}</div>
                        ${r.prior_reported ? `<div class="col-12"><strong>Prior Reported:</strong> Yes</div>` : ''}
                        ${r.prior_report_details ? `<div class="col-12"><strong>Prior Details:</strong><div class="bg-light p-3 rounded-3 mt-1">${r.prior_report_details}</div></div>` : ''}
                    </div>

                    <hr style="margin: 20px 0; border-color: #e2e8f0;">

                    <h6 class="fw-bold mb-3"><i class="fas fa-edit me-2" style="color:var(--kiboko-blue);"></i>Update Status</h6>
                    <form method="POST">@csrf
                        <input type="hidden" name="report_id" value="${r.id}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <select name="status" class="form-select">
                                    ${statusOptionsHtml}
                                </select>
                            </div>
                            <div class="col-md-8">
                                <button type="submit" name="update_status" class="btn-gradient"><i class="fas fa-save me-1"></i> Update Status</button>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Admin Notes</label>
                                <textarea name="admin_notes" class="form-control" rows="2" placeholder="Add notes...">${r.admin_notes || ''}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Resolution Notes</label>
                                <textarea name="resolution_notes" class="form-control" rows="2" placeholder="Add resolution details...">${r.resolution_notes || ''}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
            `;
        }
    </script>
</body>
</html>
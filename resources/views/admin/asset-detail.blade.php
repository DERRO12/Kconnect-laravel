<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{!! $page_title !!}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
            --grad-light: linear-gradient(135deg, rgba(0,154,218,0.08) 0%, rgba(118,188,33,0.08) 100%);
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .main-content { margin-left: 280px; padding: 24px 32px; min-height: 100vh; }
        
        .detail-header {
            background: white; border-radius: 20px; padding: 30px; margin-bottom: 24px;
            border: 1px solid #eef2f6; position: relative; overflow: hidden;
        }
        .detail-header::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--grad);
        }
        .asset-icon-large {
            width: 100px; height: 100px; background: var(--grad-light); border-radius: 20px;
            display: flex; align-items: center; justify-content: center; font-size: 2.5rem;
            color: #009ada; border: 2px solid rgba(0,154,218,0.2);
        }
        .detail-card {
            background: white; border-radius: 16px; padding: 24px; border: 1px solid #eef2f6;
            margin-bottom: 24px; height: 100%;
        }
        .detail-card h5 {
            font-weight: 700; color: #1e293b; margin-bottom: 20px; padding-bottom: 12px;
            border-bottom: 2px solid #eef2f6; display: flex; align-items: center; gap: 10px;
        }
        .detail-card h5 i { color: #009ada; }
        .spec-row {
            display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9;
        }
        .spec-row:last-child { border-bottom: none; }
        .spec-label { color: #64748b; font-size: 0.85rem; font-weight: 500; }
        .spec-value { color: #1e293b; font-weight: 600; font-size: 0.85rem; text-align: right; }
        .spec-value code { background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; }

        .timeline { position: relative; padding-left: 30px; }
        .timeline::before {
            content: ''; position: absolute; left: 12px; top: 0; bottom: 0; width: 2px;
            background: linear-gradient(180deg, #009ada, #76bc21);
        }
        .timeline-item {
            position: relative; margin-bottom: 20px; padding: 16px 20px; background: #f8fafc;
            border-radius: 12px; border: 1px solid #eef2f6;
        }
        .timeline-item::before {
            content: ''; position: absolute; left: -24px; top: 20px; width: 12px; height: 12px;
            border-radius: 50%; border: 3px solid white;
        }
        .timeline-item.active-timeline::before { background: #10b981; box-shadow: 0 0 0 3px #10b981; }
        .timeline-item.returned-timeline::before { background: #6b7280; box-shadow: 0 0 0 3px #6b7280; }
        .timeline-item.transferred-timeline::before { background: #f59e0b; box-shadow: 0 0 0 3px #f59e0b; }

        .badge-status { font-size: 0.7rem; padding: 5px 12px; border-radius: 20px; font-weight: 600; }
        .stat-badge {
            display: inline-flex; align-items: center; gap: 5px; background: #f8fafc;
            padding: 8px 14px; border-radius: 10px; border: 1px solid #eef2f6; font-size: 0.8rem; font-weight: 600;
        }
        .btn-gradient {
            background: var(--grad); color: white; border: none; padding: 10px 20px;
            border-radius: 10px; font-weight: 600; font-size: 0.85rem; cursor: pointer;
            transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
        }
        .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,154,218,0.3); color: white; }
        .btn-outline-gradient {
            background: transparent; border: 2px solid #009ada; color: #009ada; padding: 10px 20px;
            border-radius: 10px; font-weight: 600; font-size: 0.85rem; text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-outline-gradient:hover { background: var(--grad); color: white; border-color: transparent; }
        .employee-card {
            display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8fafc;
            border-radius: 12px; border: 1px solid #eef2f6;
        }
        .employee-avatar {
            width: 45px; height: 45px; background: var(--grad); border-radius: 12px;
            display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.1rem;
        }
        .empty-state { text-align: center; padding: 40px 20px; }
        .empty-state i { font-size: 3rem; color: #cbd5e1; margin-bottom: 15px; }
        
        /* Search Suggestions */
        .search-suggestion-item {
            padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; gap: 10px; transition: background 0.15s;
        }
        .search-suggestion-item:hover { background: #f0f9ff; }
        .search-suggestion-item:last-child { border-bottom: none; }
        .search-suggestion-item .s-avatar {
            width: 34px; height: 34px; background: var(--grad); border-radius: 8px;
            display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.8rem; flex-shrink: 0;
        }
        .no-results { padding: 20px; text-align: center; color: #94a3b8; }

        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 992px) { .main-content { margin-left: 0; padding: 16px; } .detail-header { padding: 20px; } }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <div class="main-content">
        <!-- Breadcrumb -->
        <nav style="margin-bottom: 20px;">
            <a href="dashboard" style="color: #64748b; text-decoration: none;">Dashboard</a>
            <span style="color: #94a3b8;"> / </span>
            <a href="manage-assets" style="color: #64748b; text-decoration: none;">Assets</a>
            <span style="color: #94a3b8;"> / </span>
            <span style="color: #1e293b; font-weight: 600;">{{ $asset['asset_name'] }}</span>
        </nav>

        <!-- Alerts -->
        @if ($success)
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px;">
                <i class="fas fa-check-circle me-2"></i>{!! $success !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($error)
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px;">
                <i class="fas fa-exclamation-triangle me-2"></i>{!! $error !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Asset Header -->
        <div class="detail-header">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="asset-icon-large">
                        @php $icons = ['Laptop'=>'fa-laptop','Desktop'=>'fa-desktop','Monitor'=>'fa-tv','Printer'=>'fa-print','Phone'=>'fa-mobile-alt','Tablet'=>'fa-tablet-alt','Networking'=>'fa-network-wired','Server'=>'fa-server','Other IT'=>'fa-microchip','Furniture'=>'fa-chair','Vehicle'=>'fa-car','Other'=>'fa-box'];
                        $icon = $icons[$asset['category']] ?? 'fa-laptop'; @endphp
                        <i class="fas {!! $icon !!}"></i>
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <h2 class="fw-bold mb-1">{{ $asset['asset_name'] }}</h2>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-info">{{ $asset['category'] }}</span>
                                <code style="background:#f1f5f9;padding:3px 10px;border-radius:6px;">{{ $asset['asset_code'] }}</code>
                                @php $cCol = ['New'=>'success','Excellent'=>'success','Good'=>'primary','Fair'=>'warning','Poor'=>'danger','Damaged'=>'danger','Retired'=>'secondary']; @endphp
                                <span class="badge bg-{!! $cCol[$asset['condition_status']]??'secondary' !!}">{!! $asset['condition_status'] !!}</span>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="asset-form?id={!! $asset['id'] !!}" class="btn-outline-gradient"><i class="fas fa-edit"></i> Edit</a>
                            <a href="asset-trail?id={!! $asset['id'] !!}" class="btn btn-outline-secondary"><i class="fas fa-history"></i> Trail</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Specifications -->
                <div class="detail-card">
                    <h5><i class="fas fa-microchip"></i> Specifications</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="spec-row"><span class="spec-label">Brand</span><span class="spec-value">{{ $asset['brand']??'—' }}</span></div>
                            <div class="spec-row"><span class="spec-label">Model</span><span class="spec-value">{{ $asset['model']??'—' }}</span></div>
                            <div class="spec-row"><span class="spec-label">Serial Number</span><span class="spec-value"><code>{{ $asset['serial_number']??'N/A' }}</code></span></div>
                            <div class="spec-row"><span class="spec-label">Category</span><span class="spec-value">{{ $asset['category'] }}</span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="spec-row"><span class="spec-label">RAM</span><span class="spec-value">{{ $asset['ram']??'—' }}</span></div>
                            <div class="spec-row"><span class="spec-label">Storage</span><span class="spec-value">{{ $asset['storage']??'—' }}</span></div>
                            <div class="spec-row"><span class="spec-label">Processor</span><span class="spec-value">{{ $asset['processor']??'—' }}</span></div>
                            <div class="spec-row"><span class="spec-label">Additional</span><span class="spec-value">{!! nl2br(htmlspecialchars($asset['specifications']??'—')) !!}</span></div>
                        </div>
                    </div>
                </div>

                <!-- Purchase Info -->
                <div class="detail-card">
                    <h5><i class="fas fa-shopping-cart"></i> Purchase & Warranty</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="spec-row"><span class="spec-label">Purchase Date</span><span class="spec-value">{!! $asset['purchase_date']?date('M d, Y',strtotime($asset['purchase_date'])):'—' !!}</span></div>
                            <div class="spec-row"><span class="spec-label">Cost</span><span class="spec-value">{!! $asset['purchase_cost']?'UGX '.number_format($asset['purchase_cost'],2):'—' !!}</span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="spec-row"><span class="spec-label">Warranty</span><span class="spec-value">{!! $asset['warranty_expiry']?date('M d, Y',strtotime($asset['warranty_expiry'])):'—' !!}</span></div>
                            <div class="spec-row"><span class="spec-label">Condition</span><span class="spec-value"><span class="badge bg-{!! $cCol[$asset['condition_status']]??'secondary' !!}">{!! $asset['condition_status'] !!}</span></span></div>
                        </div>
                    </div>
                    @if ($asset['notes'])
                        <div class="mt-3 p-3 bg-light rounded-3"><strong class="small">Notes:</strong><p class="mb-0 mt-1 small">{!! nl2br(htmlspecialchars($asset['notes'])) !!}</p></div>
                    @endif
                </div>

                <!-- Assignment History -->
                <div class="detail-card">
                    <h5><i class="fas fa-history"></i> Assignment History <span class="badge bg-secondary ms-2">{!! $totalAssignments !!}</span></h5>
                    @if ($totalAssignments > 0)
                        <div class="timeline">
                            @foreach ($assignmentHistory as $h)
                            @php
                                $tc=''; $sc=['Active'=>'success','Returned'=>'secondary','Lost'=>'danger','Damaged'=>'danger','Transferred'=>'warning'];
                                if($h['status']==='Active')$tc='active-timeline';
                                elseif($h['status']==='Returned')$tc='returned-timeline';
                                elseif($h['status']==='Transferred')$tc='transferred-timeline'; @endphp
                                <div class="timeline-item {!! $tc !!}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div><h6 class="mb-1">{{ $h['employee_name'] }} <small class="text-muted">({{ $h['employee_code'] }})</small></h6>
                                        <small class="text-muted">{{ $h['department'] }}</small></div>
                                        <span class="badge bg-{!! $sc[$h['status']]??'secondary' !!} badge-status">{!! $h['status'] !!}</span>
                                    </div>
                                    <div class="row g-2 small mt-2">
                                        <div class="col-md-6"><strong>Assigned:</strong> {!! date('M d, Y',strtotime($h['assigned_date'])) !!}</div>
                                        <div class="col-md-6">@if ($h['actual_return_date'])<strong>Returned:</strong> {!! date('M d, Y',strtotime($h['actual_return_date'])) !!}@endif</div>
                                    </div>
                                    <div class="row g-2 small mt-2">
                                        <div class="col-md-6"><strong>Condition Out:</strong> {!! $h['condition_on_assignment'] !!}</div>
                                        @if ($h['condition_on_return'])<div class="col-md-6"><strong>Condition In:</strong> {!! $h['condition_on_return'] !!}</div>@endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state"><i class="fas fa-box-open"></i><p>No assignment history</p></div>
                    @endif
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Current Status -->
                <div class="detail-card">
                    <h5><i class="fas fa-info-circle"></i> Current Status</h5>
                    @if ($asset['assignment_id'] && $asset['assignment_status']==='Active')
                        <div class="employee-card mb-3">
                            <div class="employee-avatar">{!! strtoupper(substr($asset['assigned_to_name'],0,1)) !!}</div>
                            <div><strong>{{ $asset['assigned_to_name'] }}</strong><br>
                            <small class="text-muted">{{ $asset['assigned_to_code'] }}</small><br>
                            <small class="text-muted">{{ $asset['assigned_department'] }}</small></div>
                        </div>
                        <div class="spec-row"><span class="spec-label">Assigned</span><span class="spec-value">{!! date('M d, Y',strtotime($asset['assigned_date'])) !!}</span></div>
                        <div class="spec-row"><span class="spec-label">Days</span><span class="spec-value">{!! $totalDaysAssigned !!} days</span></div>
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-outline-warning flex-fill" data-bs-toggle="modal" data-bs-target="#returnModal"><i class="fas fa-undo me-1"></i> Return</button>
                            <button class="btn btn-outline-info flex-fill" data-bs-toggle="modal" data-bs-target="#transferModal"><i class="fas fa-exchange-alt me-1"></i> Transfer</button>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-box-open fa-3x text-success mb-2"></i>
                            <h6 class="text-success">Available</h6>
                            <a href="asset-assign?asset_id={!! $asset['id'] !!}" class="btn-gradient mt-2"><i class="fas fa-user-plus"></i> Assign Now</a>
                        </div>
                    @endif
                </div>

                <!-- Quick Stats -->
                <div class="detail-card">
                    <h5><i class="fas fa-chart-bar"></i> Quick Stats</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <div class="stat-badge"><i class="fas fa-exchange-alt text-primary"></i> {!! $totalAssignments !!} Assignments</div>
                        <div class="stat-badge"><i class="fas fa-tools text-warning"></i> {!! $totalMaintenance !!} Maintenance</div>
                    </div>
                </div>

                <!-- Maintenance Records -->
                <div class="detail-card">
                    <h5><i class="fas fa-tools"></i> Maintenance <button class="btn btn-sm btn-outline-primary ms-auto" data-bs-toggle="modal" data-bs-target="#maintenanceModal"><i class="fas fa-plus"></i> Add</button></h5>
                    @if ($totalMaintenance>0)
                        @foreach ($maintenanceRecords as $m)
                            @php $mc=['Scheduled'=>'warning','In Progress'=>'info','Completed'=>'success','Cancelled'=>'secondary']; @endphp
                            <div class="p-3 mb-2 bg-light rounded-3">
                                <div class="d-flex justify-content-between"><div><strong class="small">{!! date('M d, Y',strtotime($m['maintenance_date'])) !!}</strong> <span class="badge bg-light text-dark">{!! $m['maintenance_type'] !!}</span></div>
                                <span class="badge bg-{!! $mc[$m['status']]??'secondary' !!}">{!! $m['status'] !!}</span></div>
                                <p class="small mb-1 mt-2">{{ $m['description'] }}</p>
                                @if ($m['cost'])<small>Cost: UGX {!! number_format($m['cost']) !!}</small>@endif
                            </div>
                    @endforeach @else<div class="empty-state py-3"><i class="fas fa-tools" style="font-size:2rem;"></i><p class="small">No records</p></div>@endif
                </div>
            </div>
        </div>
    </div>

    <!-- Return Modal -->
    <div class="modal fade" id="returnModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px;">
                <form method="POST">
                @csrf
                    <div class="modal-header"><h5 class="modal-title"><i class="fas fa-undo me-2" style="color:#f59e0b;"></i>Return Asset</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="alert alert-info small mb-3">Returning from <strong>{{ $asset['assigned_to_name'] }}</strong></div>
                        <div class="mb-3"><label class="form-label fw-bold">Return Condition *</label>
                            <select name="return_condition" class="form-select" required>
                                @foreach ($conditions as $c)<option value="{!! $c !!}" {!! $c===$asset['condition_on_assignment']?'selected':'' !!}>{!! $c !!}</option>@endforeach
                            </select></div>
                        <div class="mb-3"><label class="form-label fw-bold">Return Notes</label><textarea name="return_notes" class="form-control" rows="3"></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" name="return_asset" class="btn btn-warning"><i class="fas fa-undo me-1"></i> Confirm Return</button></div>
                </form>
            </div>
        </div>
    </div>

    <!-- Transfer Modal - WITH LIVE EMPLOYEE SEARCH -->
    <div class="modal fade" id="transferModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px;">
                <form method="POST" id="transferForm">
                @csrf
                    <div class="modal-header"><h5 class="modal-title"><i class="fas fa-exchange-alt me-2" style="color:#3b82f6;"></i>Transfer Asset</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="alert alert-info small mb-3">Transferring from <strong>{{ $asset['assigned_to_name'] }}</strong></div>
                        
                        <!-- Employee Search -->
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fas fa-search me-1"></i> Search New Employee *</label>
                            <div style="position:relative;">
                                <i class="fas fa-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;z-index:2;"></i>
                                <input type="text" id="transferEmployeeSearch" class="form-control" style="padding:10px 15px 10px 40px;border-radius:10px;border:2px solid #e2e8f0;" placeholder="Type name, code, or department..." autocomplete="off">
                                <i class="fas fa-spinner fa-spin" id="transferSpinner" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#009ada;display:none;z-index:2;"></i>
                                <div id="transferSuggestions" style="position:absolute;top:calc(100%+4px);left:0;right:0;background:white;border:2px solid #009ada;border-radius:12px;max-height:220px;overflow-y:auto;z-index:1060;display:none;box-shadow:0 15px 35px rgba(0,0,0,0.2);"></div>
                            </div>
                            <input type="hidden" name="new_employee_id" id="transferEmployeeId" value="">
                            
                            <!-- Selected Employee -->
                            <div id="transferSelectedCard" style="display:none;margin-top:12px;padding:14px;background:#f0fdf4;border:2px solid #76bc21;border-radius:12px;animation:slideDown 0.3s ease;">
                                <div class="d-flex align-items-center gap-3">
                                    <div id="transferAvatar" style="width:44px;height:44px;background:var(--grad);border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:1.1rem;">?</div>
                                    <div class="flex-grow-1"><strong id="transferName" style="font-size:0.95rem;"></strong><br><small class="text-muted" id="transferCode"></small><span class="mx-2 text-muted">|</span><small class="text-muted" id="transferDept"></small></div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearTransferSelection()"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3"><label class="form-label fw-bold">Transfer Condition *</label>
                            <select name="transfer_condition" class="form-select" required>
                                @foreach ($conditions as $c)<option value="{!! $c !!}" {!! $c===$asset['condition_on_assignment']?'selected':'' !!}>{!! $c !!}</option>@endforeach
                            </select></div>
                        <div class="mb-3"><label class="form-label fw-bold">Transfer Notes</label><textarea name="transfer_notes" class="form-control" rows="3"></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" name="transfer_asset" class="btn btn-primary" id="transferSubmitBtn" disabled><i class="fas fa-exchange-alt me-1"></i> Confirm Transfer</button></div>
                </form>
            </div>
        </div>
    </div>

    <!-- Maintenance Modal -->
    <div class="modal fade" id="maintenanceModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:16px;">
                <form method="POST">
                @csrf
                    <div class="modal-header"><h5 class="modal-title"><i class="fas fa-tools me-2" style="color:#f59e0b;"></i>Add Maintenance</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label fw-bold">Date *</label><input type="date" name="maintenance_date" class="form-control" value="{!! date('Y-m-d') !!}" required></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Type *</label><select name="maintenance_type" class="form-select" required>@foreach ($maintenanceTypes as $t)<option value="{!! $t !!}">{!! $t !!}</option>@endforeach</select></div>
                            <div class="col-12"><label class="form-label fw-bold">Description *</label><textarea name="maintenance_description" class="form-control" rows="2" required></textarea></div>
                            <div class="col-md-4"><label class="form-label fw-bold">Cost (UGX)</label><input type="number" step="0.01" name="maintenance_cost" class="form-control" placeholder="0.00"></div>
                            <div class="col-md-4"><label class="form-label fw-bold">Performed By</label><input type="text" name="performed_by" class="form-control"></div>
                            <div class="col-md-4"><label class="form-label fw-bold">Status</label><select name="maintenance_status" class="form-select">@foreach ($maintenanceStatuses as $s)<option value="{!! $s !!}">{!! $s !!}</option>@endforeach</select></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Next Date</label><input type="date" name="next_maintenance_date" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Notes</label><input type="text" name="maintenance_notes" class="form-control"></div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" name="add_maintenance" class="btn-gradient"><i class="fas fa-save me-1"></i> Save</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // ============================================
    // TRANSFER MODAL - EMPLOYEE SEARCH
    // ============================================
    const transferSearch = document.getElementById('transferEmployeeSearch');
    const transferSuggestions = document.getElementById('transferSuggestions');
    const transferSpinner = document.getElementById('transferSpinner');
    const transferSelectedCard = document.getElementById('transferSelectedCard');
    const transferEmployeeId = document.getElementById('transferEmployeeId');
    const transferSubmitBtn = document.getElementById('transferSubmitBtn');
    const currentEmployeeId = {!! $asset['employee_id'] ?? 0 !!};
    let transferTimeout;

    transferSearch.addEventListener('input', function() {
        clearTimeout(transferTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            transferSuggestions.style.display = 'none';
            transferSuggestions.innerHTML = '';
            transferSpinner.style.display = 'none';
            return;
        }
        
        transferSpinner.style.display = 'block';
        
        transferTimeout = setTimeout(() => {
            fetch('asset-detail?ajax_search_employee=' + encodeURIComponent(query) + '&exclude_id=' + currentEmployeeId)
                .then(r => r.json())
                .then(data => {
                    transferSpinner.style.display = 'none';
                    renderTransferSuggestions(data);
                })
                .catch(() => {
                    transferSpinner.style.display = 'none';
                    transferSuggestions.innerHTML = '<div class="no-results">Error loading results</div>';
                    transferSuggestions.style.display = 'block';
                });
        }, 300);
    });

    function renderTransferSuggestions(employees) {
        if (!Array.isArray(employees) || employees.length === 0) {
            transferSuggestions.innerHTML = '<div class="no-results"><i class="fas fa-user-slash"></i><p>No employees found</p></div>';
            transferSuggestions.style.display = 'block';
            return;
        }
        
        let html = '';
        employees.forEach(emp => {
            const initial = (emp.full_name || '?').charAt(0).toUpperCase();
            html += `
                <div class="search-suggestion-item" onclick="selectTransferEmployee(${emp.id},'${escHtml(emp.full_name)}','${escHtml(emp.employee_code||'')}','${escHtml(emp.department||'')}')">
                    <div class="s-avatar">${initial}</div>
                    <div style="flex:1;"><strong style="font-size:0.9rem;">${escHtml(emp.full_name)}</strong><br><small style="color:#64748b;">${escHtml(emp.department||'—')}</small></div>
                    <span style="font-size:0.75rem;background:#f1f5f9;padding:3px 8px;border-radius:6px;">${escHtml(emp.employee_code||'')}</span>
                </div>`;
        });
        transferSuggestions.innerHTML = html;
        transferSuggestions.style.display = 'block';
    }

    function selectTransferEmployee(id, name, code, dept) {
        transferEmployeeId.value = id;
        document.getElementById('transferName').textContent = name;
        document.getElementById('transferCode').textContent = code;
        document.getElementById('transferDept').textContent = dept;
        document.getElementById('transferAvatar').textContent = name.charAt(0).toUpperCase();
        transferSelectedCard.style.display = 'block';
        transferSubmitBtn.disabled = false;
        transferSearch.value = '';
        transferSuggestions.style.display = 'none';
        transferSpinner.style.display = 'none';
    }

    function clearTransferSelection() {
        transferEmployeeId.value = '';
        transferSelectedCard.style.display = 'none';
        transferSubmitBtn.disabled = true;
        transferSearch.value = '';
        transferSuggestions.style.display = 'none';
    }

    // Close suggestions on outside click
    document.addEventListener('click', function(e) {
        if (!transferSearch.contains(e.target) && !transferSuggestions.contains(e.target)) {
            transferSuggestions.style.display = 'none';
        }
    });

    transferSearch.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { transferSuggestions.style.display = 'none'; }
    });

    function escHtml(s) { if(!s) return ''; const d=document.createElement('div'); d.textContent=s; return d.innerHTML; }

    // Prevent form submit without employee
    document.getElementById('transferForm').addEventListener('submit', function(e) {
        if (!transferEmployeeId.value) {
            e.preventDefault();
            alert('Please search and select an employee first!');
            transferSearch.focus();
        }
    });
    </script>
</body>
</html>
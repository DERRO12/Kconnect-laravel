{{-- Organization chart manager (admin/orgchart_manager). Level names and colours: App\Support\OrgChart::LEVELS. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Chart Manager - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root {
            --kiboko-green: #76bc21; --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
            --dark: #1e293b; --gray: #64748b; --border: #e6ebf1;
        }
        body { background: #f5f7fb; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: var(--dark); }
        .main-content { margin-left: 280px; padding: 28px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; flex-wrap: wrap; gap: 12px; }
        .page-header h2 { font-size: 1.8rem; font-weight: 800; background: var(--grad); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin: 0; }
        .page-header p { color: var(--gray); margin: 2px 0 0; font-size: .9rem; }
        .btn-gradient { background: var(--grad); color: #fff; border: none; padding: 9px 18px; border-radius: 10px; font-weight: 600; }
        .btn-gradient:hover { color: #fff; box-shadow: 0 8px 20px rgba(0,154,218,.2); }
        .btn-soft { background: #fff; border: 1.5px solid var(--border); color: var(--dark); padding: 8px 16px; border-radius: 10px; font-weight: 600; }
        .btn-soft:hover { border-color: var(--kiboko-blue); color: var(--kiboko-blue); }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 18px; }
        .stat-card { background: #fff; border-radius: 14px; padding: 16px 18px; border: 1px solid var(--border); display: flex; align-items: center; gap: 14px; }
        .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
        .stat-number { font-size: 1.6rem; font-weight: 800; line-height: 1.1; }
        .stat-label { color: var(--gray); font-size: .8rem; font-weight: 600; }

        .health { background: #fffbeb; border: 1px solid #fde68a; border-radius: 14px; padding: 12px 16px; margin-bottom: 18px; font-size: .88rem; }
        .health summary { cursor: pointer; font-weight: 700; color: #92400e; }
        .health ul { margin: 8px 0 0; padding-left: 18px; }
        .health li { margin-bottom: 4px; }
        .health li a { font-size: .8rem; margin-left: 6px; }

        .panel { background: #fff; border-radius: 16px; border: 1px solid var(--border); }
        .panel-tabs { display: flex; gap: 4px; padding: 8px 8px 0; border-bottom: 1px solid var(--border); flex-wrap: wrap; }
        .panel-tabs button { border: 0; background: none; padding: 10px 16px; font-weight: 600; color: var(--gray); border-bottom: 3px solid transparent; font-size: .9rem; }
        .panel-tabs button.active { color: var(--kiboko-blue); border-bottom-color: var(--kiboko-blue); }
        .panel-tabs .badge { background: #eef2f6; color: var(--dark); font-weight: 700; margin-left: 4px; }
        .panel-body { padding: 16px; }
        .tab-pane-oc { display: none; }
        .tab-pane-oc.active { display: block; }

        .toolbar { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 12px; }
        .toolbar .input-group { flex: 1 1 260px; max-width: 420px; }
        .form-control, .form-select { border: 1.5px solid var(--border); border-radius: 10px; }
        .form-control:focus, .form-select:focus { border-color: var(--kiboko-blue); box-shadow: 0 0 0 3px rgba(0,154,218,.1); }
        .input-group-text { border: 1.5px solid var(--border); background: #fff; border-radius: 10px 0 0 10px; }

        /* Tree editor */
        .tree, .tree ul { list-style: none; margin: 0; padding: 0; }
        .tree ul { margin-left: 22px; padding-left: 16px; border-left: 2px solid #e5ebf2; }
        .tree li { position: relative; }
        .tree ul > li > .node::before { content: ''; position: absolute; left: -16px; top: 50%; width: 13px; border-top: 2px solid #e5ebf2; }
        .node { display: flex; align-items: center; gap: 10px; padding: 7px 8px; border-radius: 10px; position: relative; }
        .node:hover { background: #f4f9fd; }
        .node.hit { background: #e8f5fc; }
        .chev { width: 24px; height: 24px; border: 0; background: none; color: var(--gray); border-radius: 6px; flex: 0 0 24px; }
        .chev:hover { background: #e8eef4; }
        .chev.none { visibility: hidden; }
        .av { width: 38px; height: 38px; border-radius: 50%; flex: 0 0 38px; overflow: hidden; display: flex; align-items: center; justify-content: center;
              font-weight: 800; font-size: .78rem; border: 2px solid var(--c); color: var(--c); background: color-mix(in srgb, var(--c) 12%, #fff); }
        .av img { width: 100%; height: 100%; object-fit: cover; }
        .who { min-width: 0; flex: 1; }
        .who b { display: block; font-size: .9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .who small { color: var(--gray); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .lvl { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; padding: 3px 9px; border-radius: 10px;
               color: var(--c); background: color-mix(in srgb, var(--c) 12%, #fff); white-space: nowrap; }
        .cnt { font-size: .75rem; color: var(--gray); min-width: 46px; text-align: right; white-space: nowrap; }
        .acts { display: flex; gap: 4px; opacity: .55; transition: opacity .15s; }
        .node:hover .acts, .node:focus-within .acts { opacity: 1; }
        .act { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: #fff; color: var(--gray);
               display: inline-flex; align-items: center; justify-content: center; font-size: .8rem; }
        .act:hover { background: var(--kiboko-blue); border-color: var(--kiboko-blue); color: #fff; }
        .act.del:hover { background: #dc3545; border-color: #dc3545; }
        .act:disabled { opacity: .3; pointer-events: none; }

        /* Table */
        table.dataTable { border-collapse: collapse !important; }
        #orgTable td { vertical-align: middle; font-size: .88rem; }
        #orgTable thead th { font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; color: var(--gray); }

        .message-alert { padding: 12px 18px; border-radius: 12px; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; animation: slideDown .3s ease; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: none; } }
        .message-alert.success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
        .message-alert.danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

        .modal-content { border: none; border-radius: 18px; }
        .modal-header { border-bottom: 1px solid var(--border); padding: 18px 22px; }
        .modal-body { padding: 22px; }
        .modal-footer { background: #f8fafc; border-top: 1px solid var(--border); border-radius: 0 0 18px 18px; }
        .form-label { font-weight: 600; color: #475569; font-size: .84rem; margin-bottom: 5px; }
        .form-text { font-size: .76rem; }
        .emp-preview { display: none; align-items: center; gap: 12px; background: #f8fafc; border: 1px solid var(--border); border-radius: 12px; padding: 10px 12px; margin-top: 10px; }
        .emp-preview.show { display: flex; }
        .ts-wrapper.form-select .ts-control, .ts-control { border: 1.5px solid var(--border) !important; border-radius: 10px !important; min-height: 40px; }
        .ts-dropdown .opt-sub { color: var(--gray); font-size: .78rem; }
        .ts-dropdown .option.disabled { opacity: .45; }
        .delete-choice { border: 1.5px solid var(--border); border-radius: 12px; padding: 10px 12px; display: flex; gap: 10px; cursor: pointer; margin-bottom: 8px; }
        .delete-choice:has(input:checked) { border-color: var(--kiboko-blue); background: #f0f9ff; }
        .bulk-bar { display: none; position: sticky; bottom: 0; background: #fff; border-top: 1px solid var(--border); padding: 12px 0 2px; gap: 8px; align-items: center; flex-wrap: wrap; }
        .bulk-bar.show { display: flex; }

        @media (max-width: 991px) { .main-content { margin-left: 0; padding: 16px; } }
        @media (max-width: 640px) {
            .lvl, .cnt { display: none; }
            .acts { opacity: 1; }
            .act { width: 30px; height: 30px; }
            .tree ul { margin-left: 10px; padding-left: 10px; }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <div class="main-content">
        <div class="page-header">
            <div>
                <h2><i class="fas fa-sitemap me-2"></i>Organization Chart</h2>
                <p>Set who reports to whom. Changes show on the staff chart straight away.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ url('orgchart_view') }}" target="_blank" class="btn btn-soft"><i class="fas fa-eye me-2"></i>View chart</a>
                <button class="btn btn-gradient" onclick="OC.openAdd()"><i class="fas fa-user-plus me-2"></i>Add person</button>
            </div>
        </div>

        @if ($message !== null)
        <div class="message-alert {{ $messageType === 'danger' ? 'danger' : 'success' }}">
            <i class="fas fa-{{ $messageType === 'danger' ? 'circle-exclamation' : 'circle-check' }}"></i>
            <span class="flex-grow-1">{{ $message }}</span>
            <button class="btn-close" onclick="this.parentElement.remove()"></button>
        </div>
        @endif

        <div class="stats-grid">
            <div class="stat-card"><div class="stat-icon" style="background:#e0f2fe;color:#0284c7"><i class="fas fa-users"></i></div>
                <div><div class="stat-number">{{ $totalInChart }}</div><div class="stat-label">People in chart</div></div></div>
            <div class="stat-card"><div class="stat-icon" style="background:#ecfccb;color:#4d7c0f"><i class="fas fa-crown"></i></div>
                <div><div class="stat-number">{{ $topLevelCount }}</div><div class="stat-label">Top level (no manager)</div></div></div>
            <div class="stat-card"><div class="stat-icon" style="background:#f3e8ff;color:#7c3aed"><i class="fas fa-layer-group"></i></div>
                <div><div class="stat-number">{{ $levelsUsed }}</div><div class="stat-label">Levels used</div></div></div>
            <div class="stat-card" role="button" onclick="OC.tab('pending')"><div class="stat-icon" style="background:#fef3c7;color:#b45309"><i class="fas fa-user-clock"></i></div>
                <div><div class="stat-number">{{ count($pending) }}</div><div class="stat-label">Active staff not in chart</div></div></div>
        </div>

        @if ($issues)
        <details class="health" {{ count($issues) <= 3 ? 'open' : '' }}>
            <summary><i class="fas fa-triangle-exclamation me-1"></i>{{ count($issues) }} thing{{ count($issues) === 1 ? '' : 's' }} to check in the structure</summary>
            <ul>
                @foreach ($issues as $is)
                <li>
                    @if ($is['type'] === 'inactive')
                        {{ $is['name'] }} is inactive but still has an entry in the chart (hidden from staff).
                        <a href="#" onclick="OC.confirmDelete({{ $is['id'] }}, true);return false;">Remove entry</a>
                    @elseif ($is['type'] === 'orphan')
                        <b>{{ $is['name'] }}</b> reports to someone who is no longer in the chart, so they show as top level.
                        <a href="#" onclick="OC.openEdit({{ $is['id'] }});return false;">Fix</a>
                    @else
                        <b>{{ $is['name'] }}</b> ({{ $is['label'] }}) is at the same or a higher level than their manager {{ $is['manager'] }} ({{ $is['manager_label'] }}).
                        <a href="#" onclick="OC.openEdit({{ $is['id'] }});return false;">Fix</a>
                    @endif
                </li>
                @endforeach
            </ul>
        </details>
        @endif

        <div class="panel">
            <div class="panel-tabs">
                <button data-tab="structure"><i class="fas fa-sitemap me-1"></i>Structure</button>
                <button data-tab="table"><i class="fas fa-table me-1"></i>Table</button>
                <button data-tab="pending"><i class="fas fa-user-clock me-1"></i>Not in chart <span class="badge rounded-pill">{{ count($pending) }}</span></button>
            </div>
            <div class="panel-body">
                <!-- STRUCTURE (tree editor) -->
                <div class="tab-pane-oc" id="tab-structure">
                    <div class="toolbar">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                            <input type="search" class="form-control border-start-0" id="treeSearch" placeholder="Find someone in the structure...">
                        </div>
                        <button class="btn btn-soft btn-sm" onclick="OC.expandAll(true)"><i class="fas fa-angles-down me-1"></i>Expand all</button>
                        <button class="btn btn-soft btn-sm" onclick="OC.expandAll(false)"><i class="fas fa-angles-up me-1"></i>Collapse all</button>
                    </div>
                    <div id="treeRoot"></div>
                </div>

                <!-- TABLE -->
                <div class="tab-pane-oc" id="tab-table">
                    <div class="toolbar">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                            <input type="search" class="form-control border-start-0" id="tableSearch" placeholder="Search name, code, position, department...">
                        </div>
                        <select class="form-select" id="levelFilter" style="max-width:240px">
                            <option value="">All levels</option>
                            @foreach ($levels as $lv => $L)
                            <option value="{{ $lv }}">Level {{ $lv }} - {{ $L['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover w-100" id="orgTable">
                            <thead><tr><th>Employee</th><th>Level</th><th>Reports to</th><th>Position</th><th>Department</th><th>Team</th><th class="text-end">Actions</th><th>Level no.</th></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- NOT IN CHART -->
                <div class="tab-pane-oc" id="tab-pending">
                    @if (!$pending)
                        <div class="text-center text-muted py-5"><i class="fas fa-circle-check fa-2x mb-2" style="color:var(--kiboko-green)"></i><p class="mb-0">Every active employee is in the chart.</p></div>
                    @else
                    <form method="POST" id="bulkForm" action="{{ url('admin/orgchart_manager') }}">
                        @csrf
                        <input type="hidden" name="action" value="bulk_add">
                        <input type="hidden" name="tab" value="pending">
                        <div class="toolbar">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                                <input type="search" class="form-control border-start-0" id="pendingSearch" placeholder="Filter employees...">
                            </div>
                            <span class="text-muted small">Tick people to add several at once under the same manager.</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="pendingTable">
                                <thead><tr><th style="width:36px"><input type="checkbox" class="form-check-input" id="checkAll"></th><th>Employee</th><th>Position</th><th>Department</th><th>Company</th><th class="text-end">Add</th></tr></thead>
                                <tbody>
                                @foreach ($pending as $p)
                                    <tr data-search="{{ mb_strtolower($p['full_name'] . ' ' . $p['employee_code'] . ' ' . $p['position'] . ' ' . $p['department']) }}">
                                        <td><input type="checkbox" class="form-check-input pick" name="employee_ids[]" value="{{ $p['id'] }}"></td>
                                        <td><b>{{ $p['full_name'] }}</b><br><small class="text-muted">{{ $p['employee_code'] }}</small></td>
                                        <td>{{ $p['position'] ?: '—' }}</td>
                                        <td>{{ $p['department'] ?: '—' }}</td>
                                        <td>{{ $p['company_name'] ?: '—' }}</td>
                                        <td class="text-end"><button type="button" class="act" title="Add to chart" onclick="OC.openAdd(null, {{ $p['id'] }})"><i class="fas fa-plus"></i></button></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="bulk-bar" id="bulkBar">
                            <b id="bulkCount">0 selected</b>
                            <span class="text-muted">Reports to</span>
                            <div style="min-width:260px;flex:1;max-width:380px"><select name="parent_id" id="bulkParent"></select></div>
                            <select name="level" class="form-select" style="max-width:200px">
                                @foreach ($levels as $lv => $L)
                                <option value="{{ $lv }}" {{ $lv === 8 ? 'selected' : '' }}>{{ $L['label'] }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-gradient"><i class="fas fa-user-plus me-1"></i>Add selected</button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add / Edit modal -->
    <div class="modal fade" id="editModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="orgForm" action="{{ url('admin/orgchart_manager') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="tab" id="formTab" value="">
                        <input type="hidden" name="employee_id" id="employeeId">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Employee <span class="text-danger">*</span></label>
                                <select id="employeeSelect" placeholder="Type a name or code..."></select>
                                <input type="text" class="form-control" id="employeeFixed" readonly hidden>
                                <div class="emp-preview" id="empPreview"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reports to</label>
                                <select name="parent_id" id="parentSelect" placeholder="Nobody (top level)"></select>
                                <div class="form-text" id="parentHint">Leave empty for the CEO / top of the organization.</div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Level <span class="text-danger">*</span></label>
                                <select name="level" id="levelSelect" class="form-select" required>
                                    @foreach ($levels as $lv => $L)
                                    <option value="{{ $lv }}">Level {{ $lv }} - {{ $L['label'] }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text" id="levelHint"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Title shown on badge</label>
                                <input type="text" name="level_label" id="levelLabel" class="form-control" maxlength="50">
                                <div class="form-text">Optional, e.g. "Managing Director".</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Order</label>
                                <input type="number" name="sort_order" id="sortOrder" class="form-control" value="0" min="0" max="9999">
                                <div class="form-text">Lower shows first.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient"><i class="fas fa-save me-2"></i><span id="saveText">Save</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Remove modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ url('admin/orgchart_manager') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title text-danger"><i class="fas fa-user-minus me-2"></i>Remove from chart</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="tab" class="js-tab" value="">
                        <input type="hidden" name="employee_id" id="deleteEmployeeId">
                        <p id="deleteMessage" class="mb-3"></p>
                        <div id="deleteChoices">
                            <label class="delete-choice"><input type="radio" class="form-check-input mt-1" name="mode" value="reassign" checked>
                                <span><b>Keep their team</b><br><small class="text-muted" id="reassignText"></small></span></label>
                            <label class="delete-choice"><input type="radio" class="form-check-input mt-1" name="mode" value="branch">
                                <span><b>Remove the whole branch</b><br><small class="text-muted" id="branchText"></small></span></label>
                        </div>
                        <p class="small text-muted mb-0">This only changes the org chart. The employee record is not deleted.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i>Remove</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden form for move up / down -->
    <form method="POST" id="moveForm" class="d-none" action="{{ url('admin/orgchart_manager') }}">
        @csrf
        <input type="hidden" name="action" value="move">
        <input type="hidden" name="tab" class="js-tab" value="">
        <input type="hidden" name="employee_id" id="moveId">
        <input type="hidden" name="dir" id="moveDir">
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
    window.OC_DATA = {
        nodes: {!! json_encode($orgData, \App\Support\OrgChart::JSON_FLAGS) !!},
        employees: {!! json_encode($employees, \App\Support\OrgChart::JSON_FLAGS) !!},
        levels: {!! json_encode($levels, \App\Support\OrgChart::JSON_FLAGS) !!},
        activeTab: {!! json_encode($activeTab) !!}
    };
    </script>
    @verbatim
    <script>
    const OC = (function () {
        'use strict';
        const { nodes, employees, levels } = window.OC_DATA;
        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        const color = lv => (levels[lv] || levels[8]).color;
        const initials = n => n.split(/\s+/).filter(Boolean).slice(0, 2).map(w => w[0]).join('').toUpperCase();
        const photo = p => p || ''; // photo URLs come complete from the server (asset())
        const av = (name, p, lv) => `<div class="av" style="--c:${color(lv)}">${p ? `<img src="${esc(photo(p))}" alt="" onerror="this.parentNode.textContent='${esc(initials(name))}'">` : esc(initials(name))}</div>`;

        const byId = new Map(nodes.map(n => [n.employee_id, Object.assign(n, { kids: [] })]));
        nodes.forEach(n => { if (n.parent_id && byId.has(n.parent_id)) byId.get(n.parent_id).kids.push(n); });
        const sorter = (a, b) => a.sort_order - b.sort_order || a.level - b.level || a.full_name.localeCompare(b.full_name);
        nodes.forEach(n => n.kids.sort(sorter));
        const roots = nodes.filter(n => !n.parent_id || !byId.has(n.parent_id)).sort(sorter);
        const empById = new Map(employees.map(e => [e.id, e]));
        const descendants = n => n.kids.flatMap(k => [k.employee_id, ...descendants(k)]);
        const ancestors = n => { const a = []; let p = n, guard = 0; while (p.parent_id && byId.has(p.parent_id) && guard++ < 50) { p = byId.get(p.parent_id); a.push(p); } return a; };
        const levelText = n => n.level_label || levels[n.level].label;
        let currentTab = window.OC_DATA.activeTab;

        /* ---------- Tabs ---------- */
        function tab(name) {
            currentTab = name;
            document.querySelectorAll('.panel-tabs button').forEach(b => b.classList.toggle('active', b.dataset.tab === name));
            document.querySelectorAll('.tab-pane-oc').forEach(p => p.classList.toggle('active', p.id === 'tab-' + name));
            document.querySelectorAll('.js-tab').forEach(i => i.value = name);
            history.replaceState(null, '', '?tab=' + name);
            if (name === 'table') initTable();
        }
        document.querySelectorAll('.panel-tabs button').forEach(b => b.onclick = () => tab(b.dataset.tab));

        /* ---------- Structure tree ---------- */
        const closed = new Set(nodes.filter(n => n.kids.length && ancestors(n).length >= 2).map(n => n.employee_id));
        let treeQuery = '';
        function actionsHtml(n, siblings, i) {
            return `<div class="acts">
                <button class="act" title="Add someone who reports to ${esc(n.full_name)}" onclick="OC.openAdd(${n.employee_id})"><i class="fas fa-user-plus"></i></button>
                <button class="act" title="Edit" onclick="OC.openEdit(${n.employee_id})"><i class="fas fa-pen"></i></button>
                <button class="act" title="Move up" ${i === 0 ? 'disabled' : ''} onclick="OC.move(${n.employee_id},'up')"><i class="fas fa-arrow-up"></i></button>
                <button class="act" title="Move down" ${i === siblings.length - 1 ? 'disabled' : ''} onclick="OC.move(${n.employee_id},'down')"><i class="fas fa-arrow-down"></i></button>
                <button class="act del" title="Remove from chart" onclick="OC.confirmDelete(${n.employee_id})"><i class="fas fa-trash"></i></button>
            </div>`;
        }
        function renderTree() {
            const el = document.getElementById('treeRoot');
            if (!nodes.length) {
                el.innerHTML = `<div class="text-center text-muted py-5"><i class="fas fa-sitemap fa-3x mb-3" style="color:#cbd5e1"></i>
                    <p>No one is in the organization chart yet. Start with the CEO, then add their direct reports.</p>
                    <button class="btn btn-gradient" onclick="OC.openAdd()"><i class="fas fa-user-plus me-1"></i>Add the first person</button></div>`;
                return;
            }
            const q = treeQuery.toLowerCase();
            const hit = n => q && [n.full_name, n.employee_code, n.position, n.department].join(' ').toLowerCase().includes(q);
            const keep = new Set();
            if (q) nodes.filter(hit).forEach(n => { keep.add(n.employee_id); ancestors(n).forEach(a => keep.add(a.employee_id)); });
            const li = (n, sibs, i) => {
                if (q && !keep.has(n.employee_id)) return '';
                const team = descendants(n).length, open = q || !closed.has(n.employee_id);
                const meta = [n.position, n.department].filter(Boolean).join(' · ') || n.employee_code;
                return `<li><div class="node${hit(n) ? ' hit' : ''}" style="--c:${color(n.level)}">
                    <button class="chev${n.kids.length ? '' : ' none'}" onclick="OC.toggle(${n.employee_id})"><i class="fas fa-chevron-${open ? 'down' : 'right'}"></i></button>
                    ${av(n.full_name, n.photo, n.level)}
                    <div class="who"><b>${esc(n.full_name)}</b><small>${esc(meta)}</small></div>
                    <span class="lvl">${esc(levelText(n))}</span>
                    <span class="cnt">${team ? `<i class="fas fa-users me-1"></i>${team}` : ''}</span>
                    ${actionsHtml(n, sibs, i)}
                </div>${n.kids.length && open ? `<ul>${n.kids.map((k, j) => li(k, n.kids, j)).join('')}</ul>` : ''}</li>`;
            };
            const html = roots.map((r, i) => li(r, roots, i)).join('');
            el.innerHTML = html ? `<ul class="tree">${html}</ul>` : '<p class="text-muted text-center py-4">No match.</p>';
        }
        function toggle(id) { closed.has(id) ? closed.delete(id) : closed.add(id); renderTree(); }
        function expandAll(open) { closed.clear(); if (!open) nodes.forEach(n => { if (n.kids.length && n.parent_id) closed.add(n.employee_id); }); renderTree(); }
        let tq; document.getElementById('treeSearch').addEventListener('input', e => { clearTimeout(tq); tq = setTimeout(() => { treeQuery = e.target.value.trim(); renderTree(); }, 150); });

        /* ---------- Table ---------- */
        let table = null;
        function initTable() {
            if (table) { table.columns.adjust(); return; }
            const rows = nodes.map(n => {
                const p = n.parent_id ? byId.get(n.parent_id) : null;
                return [
                    `<div class="d-flex align-items-center gap-2">${av(n.full_name, n.photo, n.level)}<div><b>${esc(n.full_name)}</b><br><small class="text-muted">${esc(n.employee_code)}</small></div></div>`,
                    `<span class="lvl" style="--c:${color(n.level)}" data-level="${n.level}">${esc(levelText(n))}</span>`,
                    p ? esc(p.full_name) : '<span class="text-muted fst-italic"><i class="fas fa-star me-1"></i>Top level</span>',
                    esc(n.position || '—'), esc(n.department || '—'), descendants(n).length || '',
                    `<div class="d-flex gap-1 justify-content-end">
                        <button class="act" title="Edit" onclick="OC.openEdit(${n.employee_id})"><i class="fas fa-pen"></i></button>
                        <button class="act" title="Add reportee" onclick="OC.openAdd(${n.employee_id})"><i class="fas fa-user-plus"></i></button>
                        <button class="act del" title="Remove" onclick="OC.confirmDelete(${n.employee_id})"><i class="fas fa-trash"></i></button></div>`,
                    n.level
                ];
            });
            table = $('#orgTable').DataTable({
                data: rows, pageLength: 25, dom: 'rtip', order: [[7, 'asc']],
                columnDefs: [{ targets: 7, visible: false }, { targets: [6], orderable: false }, { targets: 1, orderData: [7] }],
                language: { info: 'Showing _START_ to _END_ of _TOTAL_ people', infoEmpty: 'No entries', emptyTable: 'No one in the chart yet' }
            });
            $('#tableSearch').on('input', function () { table.search(this.value).draw(); });
            $('#levelFilter').on('change', function () { table.column(7).search(this.value ? '^' + this.value + '$' : '', true, false).draw(); });
        }

        /* ---------- Not in chart (bulk) ---------- */
        const bulkForm = document.getElementById('bulkForm');
        let bulkParentTs = null;
        if (bulkForm) {
            const update = () => {
                const n = bulkForm.querySelectorAll('.pick:checked').length;
                document.getElementById('bulkBar').classList.toggle('show', n > 0);
                document.getElementById('bulkCount').textContent = n + ' selected';
            };
            bulkForm.addEventListener('change', e => {
                if (e.target.id === 'checkAll') bulkForm.querySelectorAll('tbody tr:not([hidden]) .pick').forEach(c => c.checked = e.target.checked);
                update();
            });
            document.getElementById('pendingSearch').addEventListener('input', e => {
                const q = e.target.value.toLowerCase();
                bulkForm.querySelectorAll('tbody tr').forEach(tr => tr.hidden = q && !tr.dataset.search.includes(q));
            });
            bulkParentTs = new TomSelect('#bulkParent', managerOptions([]));
        }

        /* ---------- Add / Edit modal ---------- */
        const modal = new bootstrap.Modal(document.getElementById('editModal'));
        const empRender = {
            option: d => `<div><div>${esc(d.text)}</div><div class="opt-sub">${esc(d.sub || '')}</div></div>`,
            item: d => `<div>${esc(d.text)}</div>`
        };
        function managerOptions(exclude) {
            const ex = new Set(exclude);
            const opts = [];
            const walk = (n, depth) => {
                if (!ex.has(n.employee_id)) opts.push({ value: String(n.employee_id), text: n.full_name, depth,
                    sub: [n.position, levelText(n)].filter(Boolean).join(' · '), level: n.level });
                n.kids.forEach(k => walk(k, depth + 1));
            };
            roots.forEach(r => walk(r, 0));
            return {
                options: opts, maxItems: 1, allowEmptyOption: true, placeholder: 'Nobody (top level)',
                searchField: ['text', 'sub'], sortField: [{ field: '$order' }, { field: '$score' }],
                render: {
                    option: d => `<div style="padding-left:${Math.min(d.depth, 6) * 12}px"><div>${d.depth ? '<span class="text-muted">└ </span>' : ''}${esc(d.text)}</div><div class="opt-sub" style="padding-left:${d.depth ? 14 : 0}px">${esc(d.sub)}</div></div>`,
                    item: d => `<div>${esc(d.text)}</div>`
                }
            };
        }
        const empTs = new TomSelect('#employeeSelect', {
            options: employees.map(e => ({ value: String(e.id), text: `${e.full_name} (${e.employee_code})`, sub: [e.position, e.department].filter(Boolean).join(' · '), disabled: e.in_chart })),
            maxItems: 1, searchField: ['text', 'sub'], render: Object.assign({}, empRender, {
                option: d => `<div><div>${esc(d.text)}${d.disabled ? ' <small class="text-muted">(already in chart)</small>' : ''}</div><div class="opt-sub">${esc(d.sub)}</div></div>`
            }),
            onChange: v => { document.getElementById('employeeId').value = v; preview(+v); }
        });
        let parentTs = new TomSelect('#parentSelect', managerOptions([]));
        function resetParent(exclude, value) {
            parentTs.destroy();
            parentTs = new TomSelect('#parentSelect', Object.assign(managerOptions(exclude), {
                onChange: v => suggestLevel(v ? +v : null)
            }));
            if (value) parentTs.setValue(String(value), true);
        }
        function preview(id) {
            const e = empById.get(id), box = document.getElementById('empPreview');
            if (!e) { box.classList.remove('show'); return; }
            box.innerHTML = `${av(e.full_name, e.photo, +document.getElementById('levelSelect').value)}<div class="who"><b>${esc(e.full_name)}</b><small>${esc([e.position, e.department, e.company_name].filter(Boolean).join(' · ') || 'No position on record')}</small></div>`;
            box.classList.add('show');
        }
        let labelTouched = false;
        const levelSel = document.getElementById('levelSelect'), labelIn = document.getElementById('levelLabel');
        levelSel.addEventListener('change', () => { if (!labelTouched) labelIn.placeholder = levels[levelSel.value].label; updateLevelHint(); });
        labelIn.addEventListener('input', () => labelTouched = labelIn.value.trim() !== '');
        function suggestLevel(parentId) {
            const p = parentId ? byId.get(parentId) : null;
            if (!editing) levelSel.value = p ? Math.min(8, p.level + 1) : 1;
            labelIn.placeholder = levels[levelSel.value].label;
            updateLevelHint();
        }
        function updateLevelHint() {
            const pid = +parentTs.getValue(); const p = pid ? byId.get(pid) : null;
            const hint = document.getElementById('levelHint');
            if (p && +levelSel.value <= p.level) hint.innerHTML = `<span class="text-warning"><i class="fas fa-triangle-exclamation me-1"></i>Same or higher level than their manager (${esc(levelText(p))}).</span>`;
            else hint.textContent = 'Controls the colour and which row the card sits on.';
        }
        let editing = null;
        function openAdd(parentId = null, employeeId = null) {
            editing = null; labelTouched = false;
            document.getElementById('modalTitle').innerHTML = parentId
                ? `<i class="fas fa-user-plus me-2" style="color:var(--kiboko-blue)"></i>Add a reportee to ${esc(byId.get(parentId).full_name)}`
                : '<i class="fas fa-user-plus me-2" style="color:var(--kiboko-blue)"></i>Add person to the chart';
            document.getElementById('saveText').textContent = 'Add to chart';
            document.getElementById('formTab').value = currentTab;
            empTs.wrapper.hidden = false; document.getElementById('employeeFixed').hidden = true;
            empTs.clear(true); document.getElementById('employeeId').value = '';
            preview(0);
            resetParent([], parentId);
            labelIn.value = ''; document.getElementById('sortOrder').value = parentId ? (byId.get(parentId).kids.length + 1) * 10 : 0;
            suggestLevel(parentId);
            if (employeeId) { empTs.setValue(String(employeeId)); }
            modal.show();
            setTimeout(() => employeeId ? parentTs.focus() : empTs.focus(), 350);
        }
        function openEdit(id) {
            const n = byId.get(id);
            if (!n) { alert('This person is not active in the chart.'); return; }
            editing = id; labelTouched = !!n.level_label && n.level_label !== levels[n.level].label;
            document.getElementById('modalTitle').innerHTML = `<i class="fas fa-pen me-2" style="color:#f59e0b"></i>Edit ${esc(n.full_name)}`;
            document.getElementById('saveText').textContent = 'Save changes';
            document.getElementById('formTab').value = currentTab;
            empTs.wrapper.hidden = true;
            const fixed = document.getElementById('employeeFixed');
            fixed.hidden = false; fixed.value = `${n.full_name} (${n.employee_code})`;
            document.getElementById('employeeId').value = id;
            resetParent([id, ...descendants(n)], n.parent_id);
            levelSel.value = n.level; preview(id);
            labelIn.value = labelTouched ? n.level_label : ''; labelIn.placeholder = levels[n.level].label;
            document.getElementById('sortOrder').value = n.sort_order;
            document.getElementById('parentHint').textContent = n.kids.length ? `${n.full_name} and their ${descendants(n).length} team member(s) are hidden from this list to prevent loops.` : 'Leave empty for the CEO / top of the organization.';
            updateLevelHint();
            modal.show();
        }
        document.getElementById('orgForm').addEventListener('submit', e => {
            if (!document.getElementById('employeeId').value) { e.preventDefault(); empTs.focus(); alert('Please choose an employee.'); }
        });
        document.getElementById('editModal').addEventListener('hidden.bs.modal', () => {
            document.getElementById('parentHint').textContent = 'Leave empty for the CEO / top of the organization.';
        });

        /* ---------- Remove ---------- */
        const delModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        function confirmDelete(id, inactive) {
            const n = byId.get(id);
            document.getElementById('deleteEmployeeId').value = id;
            const name = n ? n.full_name : (empById.get(id)?.full_name || 'This inactive employee');
            const team = n ? descendants(n).length : 0;
            const mgr = n && n.parent_id ? byId.get(n.parent_id) : null;
            document.getElementById('deleteMessage').innerHTML = `<b>${esc(name)}</b> will be taken out of the organization chart.`;
            document.getElementById('deleteChoices').style.display = n && n.kids.length ? '' : 'none';
            if (n && n.kids.length) {
                document.getElementById('reassignText').textContent = `Their ${n.kids.length} direct report(s) will report to ${mgr ? mgr.full_name : 'nobody (become top level)'}.`;
                document.getElementById('branchText').textContent = `Also removes all ${team} people below them from the chart.`;
            }
            document.querySelector('#deleteModal input[value=reassign]').checked = true;
            delModal.show();
        }

        function move(id, dir) {
            document.getElementById('moveId').value = id;
            document.getElementById('moveDir').value = dir;
            document.getElementById('moveForm').submit();
        }

        tab(currentTab);
        renderTree();
        setTimeout(() => document.querySelectorAll('.message-alert.success').forEach(el => el.remove()), 6000);

        return { openAdd, openEdit, confirmDelete, move, toggle, expandAll, tab };
    })();
    </script>
    @endverbatim
</body>
</html>

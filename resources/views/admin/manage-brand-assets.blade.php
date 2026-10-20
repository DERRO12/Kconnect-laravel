<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Assets Manager - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
            --bg: #f8fafc;
            --text: #1e293b;
            --text-light: #64748b;
            --text-muted: #94a3b8;
            --border: #eef2f6;
        }
        
        body {
            background: var(--bg);
            font-family: 'Plus Jakarta Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .page-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }
        
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.2);
            color: white;
        }
        
        .btn-outline-gradient {
            background: white;
            border: 2px solid #009ada;
            color: #009ada;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 16px;
            transition: all 0.3s;
            cursor: pointer;
        }
        .btn-outline-gradient:hover {
            background: var(--grad);
            color: white;
            border-color: transparent;
        }
        
        /* Stats */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: white;
            border-radius: 14px;
            padding: 18px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
        }
        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.1;
        }
        .stat-label {
            font-size: 0.78rem;
            color: var(--text-light);
            margin-top: 2px;
            font-weight: 500;
        }
        
        /* Company quick-stats */
        .company-stats-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }
        .company-stat-pill {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
            background: white;
            border: 1px solid var(--border);
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .company-stat-pill .dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            display: inline-block;
        }
        
        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
        }
        
        .filter-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .filter-pill {
            padding: 7px 15px;
            border-radius: 30px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            text-decoration: none;
            transition: all 0.25s;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
        }
        .filter-pill:hover,
        .filter-pill.active {
            background: var(--grad);
            color: white;
            border-color: transparent;
        }
        
        .search-box {
            position: relative;
        }
        .search-box input {
            width: 100%;
            padding: 9px 14px 9px 36px;
            border: 2px solid var(--border);
            border-radius: 30px;
            font-size: 0.85rem;
            background: #f8fafc;
            transition: all 0.25s;
        }
        .search-box input:focus {
            outline: none;
            border-color: #009ada;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(0,154,218,0.06);
        }
        .search-box .s-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }
        
        /* Assets Table */
        .table-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
        }
        .table-card table {
            margin: 0;
        }
        .table-card thead th {
            background: #fafbfc;
            border-bottom: 1px solid var(--border);
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--text-light);
            padding: 14px 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table-card tbody td {
            padding: 12px 16px;
            vertical-align: middle;
            font-size: 0.88rem;
            border-bottom: 1px solid var(--border);
        }
        .table-card tbody tr:hover {
            background: #fafcfd;
        }
        .table-card tbody tr:last-child td {
            border-bottom: none;
        }
        
        .file-type-icon {
            width: 36px; height: 36px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.85rem;
            margin-right: 10px;
        }
        
        .company-badge-mini {
            font-size: 0.68rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 12px;
            white-space: nowrap;
        }
        .badge-kiboko_enterprises { background: #e6f4fa; color: #007aad; }
        .badge-kibocell { background: #eaf5db; color: #5a9418; }
        .badge-kfs { background: #f0ebf9; color: #5b3ea8; }
        .badge-kibs { background: #fff8e8; color: #c48508; }
        .badge-general { background: #f1f5f9; color: #64748b; }
        
        .status-dot {
            display: inline-block;
            width: 8px; height: 8px;
            border-radius: 50%;
            margin-right: 6px;
        }
        .status-active-dot { background: #22c55e; }
        .status-inactive-dot { background: #ef4444; }
        
        .action-btn-group {
            display: flex;
            gap: 4px;
        }
        .action-btn-sm {
            width: 32px; height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 7px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #64748b;
            transition: all 0.2s;
            text-decoration: none;
            font-size: 0.75rem;
            cursor: pointer;
        }
        .action-btn-sm:hover {
            background: var(--grad);
            color: white;
            border-color: transparent;
        }
        .action-btn-sm.delete-sm:hover {
            background: #dc3545;
        }
        
        /* Modal */
        .modal-content {
            border: none;
            border-radius: 18px;
        }
        .modal-header {
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            border-bottom: 1px solid #eef2f6;
            border-radius: 18px 18px 0 0;
            padding: 1.2rem 1.5rem;
        }
        .form-control, .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.9rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #009ada;
            box-shadow: 0 0 0 3px rgba(0,154,218,0.08);
        }
        .drop-zone {
            border: 2px dashed #e2e8f0;
            border-radius: 14px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #f8fafc;
        }
        .drop-zone:hover,
        .drop-zone.dragover {
            border-color: #009ada;
            background: #f0f9ff;
        }
        .drop-zone i {
            font-size: 2.5rem;
            color: #009ada;
            margin-bottom: 12px;
        }
        .file-info {
            display: none;
            padding: 12px;
            background: #f0fdf4;
            border-radius: 10px;
            margin-top: 12px;
            border: 1px solid #bbf7d0;
        }
        
        .message-alert {
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .message-alert.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .message-alert.danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        @media (max-width: 768px) {
            .main-content { margin-left: 0; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .table-card { overflow-x: auto; }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="page-header">
            <h2><i class="fas fa-paint-brush me-2"></i>Brand Assets Manager</h2>
            <button class="btn-gradient" onclick="openAddModal()">
                <i class="fas fa-plus me-2"></i>Upload New Asset
            </button>
        </div>
        
        <!-- Message Alert -->
        @if(isset($flashMessage))
        <div class="message-alert {!! $flashType !!}">
            <span><i class="fas fa-{!! $flashType === 'success' ? 'check-circle' : 'exclamation-circle' !!} me-2"></i>{!! $flashMessage !!}</span>
            <button class="btn-close float-end" onclick="this.parentElement.remove()"></button>
        </div>
        @endif
        
        <!-- Stats Row -->
        <div class="stats-row">
            @php
            $totalAssets = count($allAssets);
            $totalSize = 0;
            $totalDownloads = 0;
            $activeCount = 0;
            foreach ($allAssets as $a) {
                $totalSize += ($a['file_size'] ?? 0);
                $totalDownloads += ($a['download_count'] ?? 0);
                if ($a['status']) $activeCount++;
            }
            $totalSizeFormatted = $totalSize > 0 ? round($totalSize / 1048576, 2) . ' MB' : '0 MB';
            @endphp
            <div class="stat-card">
                <div class="stat-number">{!! $totalAssets !!}</div>
                <div class="stat-label"><i class="fas fa-file-alt me-1"></i> Total Assets</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{!! $totalSizeFormatted !!}</div>
                <div class="stat-label"><i class="fas fa-database me-1"></i> Storage Used</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{!! number_format($totalDownloads) !!}</div>
                <div class="stat-label"><i class="fas fa-download me-1"></i> Downloads</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{!! $activeCount !!}</div>
                <div class="stat-label"><i class="fas fa-check-circle me-1"></i> Active</div>
            </div>
        </div>
        
        <!-- Company Distribution -->
        <div class="company-stats-row">
            {{-- $companies / $companyColors: from the companies table (App\Support\HomeData::brandCompanies) --}}
            @foreach ($companies as $ckey => $cname)
            <span class="company-stat-pill">
                <span class="dot" style="background:{{ $companyColors[$ckey] ?? '#64748b' }}"></span>
                {{ $cname }}: <strong>{!! $companyStats[$ckey] !!}</strong>
            </span>
            @endforeach
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row g-3 align-items-center">
                <div class="col-lg-5">
                    <h6 class="mb-2 small text-muted text-uppercase fw-bold">Filter by Company</h6>
                    <div class="filter-pills">
                        <span class="filter-pill active" data-filter="all">All Companies</span>
                        @foreach($companies as $ckey => $cname)
                        <span class="filter-pill" data-filter="{!! $ckey !!}">
                            {{ $cname }}
                        </span>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-4">
                    <h6 class="mb-2 small text-muted text-uppercase fw-bold">Filter by Category</h6>
                    <div class="filter-pills" id="categoryFilters">
                        <span class="filter-pill active" data-category="all">All Types</span>
                        @foreach($categories as $cat)
                        <span class="filter-pill" data-category="{!! $cat['name'] !!}">
                            <i class="fas {!! $cat['icon'] !!} me-1"></i>{!! $cat['name'] !!}
                        </span>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-3">
                    <h6 class="mb-2 small text-muted text-uppercase fw-bold">Search</h6>
                    <div class="search-box">
                        <i class="fas fa-search s-icon"></i>
                        <input type="text" id="searchInput" placeholder="Search assets...">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Assets Table -->
        <div class="table-card">
            <table class="table" id="assetsTable">
                <thead>
                    <tr>
                        <th>Asset</th>
                        <th>Company</th>
                        <th>Category</th>
                        <th>Size</th>
                        <th>Downloads</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($allAssets) > 0)
                        @foreach ($allAssets as $asset)
                        @php
                            $ext = strtolower(pathinfo($asset['file_path'], PATHINFO_EXTENSION));
                            
                            $iconMap = [
                                'pdf' => ['fa-file-pdf', '#dc3545'],
                                'jpg' => ['fa-image', '#7c3aed'],
                                'jpeg' => ['fa-image', '#7c3aed'],
                                'png' => ['fa-image', '#7c3aed'],
                                'gif' => ['fa-image', '#7c3aed'],
                                'svg' => ['fa-image', '#7c3aed'],
                                'doc' => ['fa-file-word', '#2563eb'],
                                'docx' => ['fa-file-word', '#2563eb'],
                                'ppt' => ['fa-file-powerpoint', '#ea580c'],
                                'pptx' => ['fa-file-powerpoint', '#ea580c'],
                                'xls' => ['fa-file-excel', '#16a34a'],
                                'xlsx' => ['fa-file-excel', '#16a34a'],
                                'mp4' => ['fa-video', '#0d9488'],
                                'zip' => ['fa-file-archive', '#ca8a04'],
                            ];
                            
                            $fIcon = $iconMap[$ext][0] ?? 'fa-file';
                            $fColor = $iconMap[$ext][1] ?? '#64748b';
                            
                            $fileSize = $asset['file_size'] > 1048576 ? round($asset['file_size']/1048576, 1).' MB' : ($asset['file_size'] > 0 ? round($asset['file_size']/1024).' KB' : '--');
                            $company = $asset['computed_company'];
                        @endphp
                        <tr class="asset-row-data" 
                            data-company="{!! $company !!}"
                            data-category="{!! strtolower($asset['category']) !!}"
                            data-title="{!! strtolower($asset['title']) !!}"
                            data-desc="{!! strtolower($asset['description'] ?? '') !!}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="file-type-icon" style="background:{!! $fColor !!}">
                                        <i class="fas {!! $fIcon !!}"></i>
                                    </span>
                                    <div>
                                        <div class="fw-semibold">{{ $asset['title'] }}</div>
                                        @if($asset['description'])
                                        <small class="text-muted">{!! substr(htmlspecialchars($asset['description']), 0, 40) !!}...</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="company-badge-mini badge-{!! $company !!}"@if (!in_array($company, ['kiboko_enterprises', 'kibocell', 'kfs', 'kibs', 'general'])) style="background: {{ $companyColors[$company] ?? '#64748b' }}1a; color: {{ $companyColors[$company] ?? '#64748b' }};"@endif>
                                    {{ $companies[$company] ?? '' }}
                                </span>
                            </td>
                            <td><span class="text-muted small">{!! $asset['category'] !!}</span></td>
                            <td><small class="text-muted">{!! $fileSize !!}</small></td>
                            <td><small class="text-muted">{!! number_format($asset['download_count']) !!}</small></td>
                            <td>
                                <span class="status-dot {!! $asset['status'] ? 'status-active-dot' : 'status-inactive-dot' !!}"></span>
                                <small class="text-muted">{!! $asset['status'] ? 'Active' : 'Inactive' !!}</small>
                            </td>
                            <td><small class="text-muted">{!! date('M d, Y', strtotime($asset['created_at'])) !!}</small></td>
                            <td>
                                <div class="action-btn-group">
                                    <a href="{{ kc_asset($asset['file_path']) }}" class="action-btn-sm" target="_blank" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="{{ kc_asset($asset['file_path']) }}" class="action-btn-sm" download title="Download"><i class="fas fa-download"></i></a>
                                    <a href="?edit={!! $asset['id'] !!}" class="action-btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="?toggle={!! $asset['id'] !!}" class="action-btn-sm" title="Toggle" onclick="return confirm('Toggle status?')"><i class="fas fa-power-off"></i></a>
                                    <a href="?delete={!! $asset['id'] !!}" class="action-btn-sm delete-sm" title="Delete" onclick="return confirm('Delete this asset?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 d-block" style="color:#cbd5e1;"></i>
                                <strong>No brand assets yet</strong><br>
                                <small>Click "Upload New Asset" to get started.</small>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Add/Edit Modal -->
    <div class="modal fade" id="assetModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-upload me-2" style="color:#009ada;"></i>Upload New Asset
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data" id="assetForm">
@csrf
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="edit_id" value="{!! $edit_item ? $edit_item['id'] : 0 !!}">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" 
                                   value="{!! $edit_item ? htmlspecialchars($edit_item['title']) : '' !!}" 
                                   placeholder="e.g., Kiboko Enterprises Logo PNG" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="2" 
                                      placeholder="Brief description of this asset...">{!! $edit_item ? htmlspecialchars($edit_item['description'] ?? '') : '' !!}</textarea>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">File Category</label>
                                <select name="category" id="category" class="form-select">
                                    @foreach($cats as $cat)
                                    <option value="{!! $cat['name'] !!}" {!! ($edit_item && $edit_item['category'] == $cat['name']) ? 'selected' : '' !!}>
                                        {!! $cat['name'] !!}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Company Destination
                                    <small class="text-muted fw-normal">(where it appears on frontend)</small>
                                </label>
                                <select name="company" id="company" class="form-select">
                                    <option value="auto">🔍 Auto-detect from title</option>
                                    @foreach($companies as $ckey => $cname)
                                    <option value="{!! $ckey !!}" {!! ($edit_item && $edit_item['computed_company'] == $ckey) ? 'selected' : '' !!}>
                                        {{ $cname }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="form-text small text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Include company name in the title (e.g., "KFS Logo") for auto-detection.
                                </div>
                            </div>
                        </div>
                        
                        @if(!$edit_item)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">File <span class="text-danger">*</span></label>
                            <div class="drop-zone" id="dropZone">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <h6>Drag & drop file here</h6>
                                <p class="text-muted small mb-2">or click to browse</p>
                                <input type="file" name="asset_file" id="asset_file" class="d-none" required>
                                <button type="button" class="btn-outline-gradient" onclick="document.getElementById('asset_file').click()">
                                    <i class="fas fa-folder-open me-1"></i> Browse Files
                                </button>
                            </div>
                            <div class="file-info" id="fileInfo">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span id="fileName"></span>
                                <span class="text-muted ms-2" id="fileSizeDisplay"></span>
                                <button type="button" class="btn-close float-end" onclick="clearFile()"></button>
                            </div>
                            <div class="form-text text-muted small mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Supported: Images, PDF, Word, Excel, PowerPoint, Videos, Archives (Max: 50MB)
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-gradient">
                            <i class="fas fa-save me-2"></i>{!! $edit_item ? 'Update Asset' : 'Upload Asset' !!}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let assetModal;
        
        document.addEventListener('DOMContentLoaded', function() {
            assetModal = new bootstrap.Modal(document.getElementById('assetModal'));
            
            @if(isset($get['edit']) && $edit_item)
            assetModal.show();
            @endif
            
            // Drag & drop
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('asset_file');
            const fileInfo = document.getElementById('fileInfo');
            
            if (dropZone) {
                dropZone.addEventListener('click', () => fileInput.click());
                dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('dragover'); });
                dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('dragover');
                    if (e.dataTransfer.files.length > 0) {
                        fileInput.files = e.dataTransfer.files;
                        updateFileInfo(e.dataTransfer.files[0]);
                    }
                });
            }
            
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files.length > 0) updateFileInfo(this.files[0]);
                });
            }
            
            // Filtering
            const searchInput = document.getElementById('searchInput');
            let activeCompanyFilter = 'all';
            let activeCategoryFilter = 'all';
            
            // Company filter
            document.querySelectorAll('.filter-pills')[0].querySelectorAll('.filter-pill').forEach(pill => {
                pill.addEventListener('click', function() {
                    this.parentElement.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    activeCompanyFilter = this.dataset.filter;
                    filterTable();
                });
            });
            
            // Category filter
            document.querySelectorAll('#categoryFilters .filter-pill').forEach(pill => {
                pill.addEventListener('click', function() {
                    document.querySelectorAll('#categoryFilters .filter-pill').forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    activeCategoryFilter = this.dataset.category;
                    filterTable();
                });
            });
            
            // Search
            if (searchInput) {
                searchInput.addEventListener('keyup', filterTable);
            }
            
            function filterTable() {
                const term = (searchInput?.value || '').toLowerCase();
                const rows = document.querySelectorAll('.asset-row-data');
                
                rows.forEach(row => {
                    const company = row.dataset.company;
                    const category = row.dataset.category;
                    const title = row.dataset.title || '';
                    const desc = row.dataset.desc || '';
                    
                    const matchCompany = activeCompanyFilter === 'all' || company === activeCompanyFilter;
                    const matchCategory = activeCategoryFilter === 'all' || category === activeCategoryFilter;
                    const matchSearch = term === '' || title.includes(term) || desc.includes(term);
                    
                    row.style.display = (matchCompany && matchCategory && matchSearch) ? '' : 'none';
                });
            }
        });
        
        function updateFileInfo(file) {
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSizeDisplay').textContent = '(' + (file.size / 1024).toFixed(1) + ' KB)';
            document.getElementById('dropZone').style.display = 'none';
            document.getElementById('fileInfo').style.display = 'block';
        }
        
        function clearFile() {
            document.getElementById('asset_file').value = '';
            document.getElementById('fileInfo').style.display = 'none';
            document.getElementById('dropZone').style.display = 'block';
        }
        
        function openAddModal() {
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-upload me-2" style="color:#009ada;"></i>Upload New Asset';
            document.getElementById('edit_id').value = '0';
            document.getElementById('title').value = '';
            document.getElementById('description').value = '';
            document.getElementById('category').value = 'Logos';
            document.getElementById('company').value = 'auto';
            clearFile();
            assetModal.show();
        }
        
        // Auto-hide messages
        setTimeout(() => {
            document.querySelectorAll('.message-alert').forEach(el => el.remove());
        }, 4000);
    </script>
</body>
</html>
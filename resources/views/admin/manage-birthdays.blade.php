<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Birthdays & Social - {{ SITE_NAME }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
            --grad-light: linear-gradient(135deg, rgba(0, 154, 218, 0.08) 0%, rgba(118, 188, 33, 0.08) 100%);
        }
        
        body {
            background: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background: white;
            height: 100vh;
            position: fixed;
            width: 260px;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            overflow-y: auto;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.2);
            color: white;
        }
        
        .btn-outline-gradient {
            background: transparent;
            border: 1px solid var(--kiboko-blue);
            color: var(--kiboko-blue);
            padding: 5px 12px;
            border-radius: 8px;
            transition: all 0.3s;
            text-decoration: none;
            font-size: 0.85rem;
            display: inline-block;
        }
        
        .btn-outline-gradient:hover {
            background: var(--grad);
            border-color: transparent;
            color: white;
        }
        
        .btn-outline-danger {
            border: 1px solid #dc3545;
            color: #dc3545;
            padding: 5px 12px;
            border-radius: 8px;
            transition: all 0.3s;
            text-decoration: none;
            font-size: 0.85rem;
            display: inline-block;
        }
        
        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
        }
        
        .avatar-preview {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .avatar-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--grad-light);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .status-badge {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        
        .status-active {
            background: var(--kiboko-green);
        }
        
        .status-inactive {
            background: #ef4444;
        }
        
        .today-badge {
            background: #f59e0b;
            color: white;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .nav-tabs {
            border: none;
            margin-bottom: 20px;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #64748b;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 10px;
            margin-right: 10px;
        }
        
        .nav-tabs .nav-link.active {
            background: var(--grad);
            color: white;
        }
        
        .social-icon-preview {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .color-dot {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: inline-block;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .platform-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .icon-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 8px;
            max-height: 200px;
            overflow-y: auto;
            padding: 10px;
            background: #f8fafc;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }
        
        .icon-item {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1.2rem;
            color: #64748b;
        }
        
        .icon-item:hover {
            border-color: var(--kiboko-blue);
            background: var(--grad-light);
            transform: scale(1.05);
        }
        
        .icon-item.selected {
            border: 2px solid var(--kiboko-blue);
            background: var(--grad-light);
            color: var(--kiboko-blue);
        }
        
        .action-group {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .table > :not(caption) > * > * {
            vertical-align: middle;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    @include('admin.partials.sidebar')
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">
                <i class="fas fa-birthday-cake me-2" style="color: var(--kiboko-blue);"></i>
                Birthdays & Social Links
            </h2>
            <div>
                <a href="{{ url('admin/normalize-birthdays') }}" class="btn btn-outline-gradient me-2">
                    <i class="fas fa-magic me-2"></i>Normalize Names
                </a>
                <button class="btn-gradient me-2" data-bs-toggle="modal" data-bs-target="#birthdayModal" 
                        onclick="document.getElementById('birthdayModalLabel').innerText='Add Birthday Record'; document.getElementById('birthdayForm').reset(); document.getElementById('birthday_id').value='';">
                    <i class="fas fa-plus me-2"></i>Add Birthday
                </button>
                <button class="btn-gradient" data-bs-toggle="modal" data-bs-target="#socialModal"
                        onclick="document.getElementById('socialModalLabel').innerText='Add Social Link'; document.getElementById('socialForm').reset(); document.getElementById('social_id').value='';">
                    <i class="fas fa-plus me-2"></i>Add Social Link
                </button>
            </div>
        </div>
        
        @if(isset($error))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {!! $error !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        @if(isset($get['success']) && isset($successMessages[$get['success']]))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {!! $successMessages[$get['success']] !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="?tab=birthdays" class="nav-link {!! $activeTab == 'birthdays' ? 'active' : '' !!}" 
                   id="birthdays-tab" role="tab">
                    <i class="fas fa-birthday-cake me-2"></i>Birthdays
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="?tab=social" class="nav-link {!! $activeTab == 'social' ? 'active' : '' !!}" 
                   id="social-tab" role="tab">
                    <i class="fas fa-share-alt me-2"></i>Social Links
                </a>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Birthdays Tab -->
            <div class="tab-pane fade show {!! $activeTab == 'birthdays' ? 'active' : '' !!}" id="birthdays" role="tabpanel">
                <div class="content-card">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Birth Date</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $today = date('m-d');
                                @endphp
                                @if(count($birthdays) > 0)
                                    @foreach($birthdays as $item)
                                    @php
                                        $bdayMD = date('m-d', strtotime($item['birth_date']));
                                    @endphp
                                <tr>
                                    <td>
                                        @if($item['photo'] && file_exists(kc_public_file('uploads/' . $item['photo'], 'admin')))
                                            <img src="{{ asset('admin/uploads') }}/{!! $item['photo'] !!}" class="avatar-preview" alt="{{ $item['full_name'] }}">
                                        @else
                                            <div class="avatar-placeholder">
                                                <i class="fas fa-user" style="color: var(--kiboko-blue);"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $item['full_name'] }}</strong>
                                        @if($bdayMD == $today)
                                            <span class="today-badge ms-2">Today!</span>
                                        @endif
                                    </td>
                                    <td>{{ $item['department'] ?: '-' }}</td>
                                    <td>
                                        {!! date('F j, Y', strtotime($item['birth_date'])) !!}
                                        <small class="text-muted d-block">({!! date('D', strtotime($item['birth_date'])) !!})</small>
                                    </td>
                                    <td>
                                        @if($item['message'])
{!! substr(htmlspecialchars($item['message']), 0, 30) . (strlen($item['message']) > 30 ? '...' : '') !!}
                                        @else
-
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge {!! $item['status'] ? 'status-active' : 'status-inactive' !!}"></span>
                                        {!! $item['status'] ? 'Active' : 'Inactive' !!}
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <a href="?edit_birthday={!! $item['id'] !!}&tab=birthdays" class="btn-outline-gradient" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?toggle_birthday={!! $item['id'] !!}&tab=birthdays" class="btn-outline-gradient" title="{!! $item['status'] ? 'Deactivate' : 'Activate' !!}" onclick="return confirm('Are you sure you want to toggle status?')">
                                                <i class="fas fa-power-off"></i>
                                            </a>
                                            <a href="?delete_birthday={!! $item['id'] !!}&tab=birthdays" class="btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this birthday record? This action cannot be undone.')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-birthday-cake fa-4x text-muted mb-3"></i>
                                        <h6 class="text-muted">No birthday records found</h6>
                                        <button class="btn-gradient mt-3" data-bs-toggle="modal" data-bs-target="#birthdayModal">
                                            <i class="fas fa-plus me-2"></i>Add Your First Birthday
                                        </button>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Social Links Tab -->
            <div class="tab-pane fade show {!! $activeTab == 'social' ? 'active' : '' !!}" id="social" role="tabpanel">
                <div class="content-card">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Preview</th>
                                    <th>Platform</th>
                                    <th>Username</th>
                                    <th>Followers</th>
                                    <th>Colors</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($socialLinks) > 0)
                                    @foreach($socialLinks as $link)
                                <tr>
                                    <td>
                                        <div class="platform-badge" style="background: {!! $link['bg_color'] !!}; color: {!! $link['color'] !!};">
                                            <i class="fab {!! $link['icon'] !!}"></i>
                                            <span>{{ $link['platform'] }}</span>
                                        </div>
                                    </td>
                                    <td><strong>{{ $link['platform'] }}</strong></td>
                                    <td>
                                        {{ $link['username'] ?: '-' }}
                                        @if($link['username'])
                                            <a href="{!! $link['url'] !!}" target="_blank" class="text-muted ms-1">
                                                <i class="fas fa-external-link-alt fa-xs"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td>{{ $link['followers_count'] ?: '-' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="color-dot" style="background: {!! $link['color'] !!};" title="Text Color"></span>
                                            <span class="color-dot" style="background: {!! $link['bg_color'] !!};" title="Background Color"></span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary">{!! $link['sort_order'] !!}</span></td>
                                    <td>
                                        <span class="status-badge {!! $link['status'] ? 'status-active' : 'status-inactive' !!}"></span>
                                        {!! $link['status'] ? 'Active' : 'Inactive' !!}
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <a href="?edit_social={!! $link['id'] !!}&tab=social" class="btn-outline-gradient" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?toggle_social={!! $link['id'] !!}&tab=social" class="btn-outline-gradient" title="{!! $link['status'] ? 'Deactivate' : 'Activate' !!}" onclick="return confirm('Are you sure you want to toggle status?')">
                                                <i class="fas fa-power-off"></i>
                                            </a>
                                            <a href="?delete_social={!! $link['id'] !!}&tab=social" class="btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this social link?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-share-alt fa-4x text-muted mb-3"></i>
                                        <h6 class="text-muted">No social links found</h6>
                                        <button class="btn-gradient mt-3" data-bs-toggle="modal" data-bs-target="#socialModal">
                                            <i class="fas fa-plus me-2"></i>Add Your First Social Link
                                        </button>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Birthday Modal -->
    <div class="modal fade" id="birthdayModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="birthdayModalLabel">
                        <i class="fas fa-birthday-cake me-2" style="color: var(--kiboko-blue);"></i>
                        {!! $editBirthday ? 'Edit' : 'Add' !!} Birthday Record
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data" id="birthdayForm">
@csrf
                    <div class="modal-body">
                        @if($editBirthday)
                            <input type="hidden" name="birthday_id" id="birthday_id" value="{!! $editBirthday['id'] !!}">
                            <input type="hidden" name="existing_photo" value="{!! $editBirthday['photo'] !!}">
                        @else
                            <input type="hidden" name="birthday_id" id="birthday_id" value="">
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control" value="{!! $editBirthday['full_name'] ?? '' !!}" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Department</label>
                                    <input type="text" name="department" class="form-control" value="{!! $editBirthday['department'] ?? '' !!}" placeholder="e.g., Sales, IT, HR">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Birth Date <span class="text-danger">*</span></label>
                                    <input type="date" name="birth_date" class="form-control" value="{!! $editBirthday['birth_date'] ?? '' !!}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Photo</label>
                                    <input type="file" name="photo" class="form-control" accept="image/*">
                                    @if($editBirthday && $editBirthday['photo'] && file_exists(kc_public_file('uploads/' . $editBirthday['photo'], 'admin')))
                                        <div class="mt-2 d-flex align-items-center gap-2">
                                            <small class="text-muted">Current:</small>
                                            <img src="{{ asset('admin/uploads') }}/{!! $editBirthday['photo'] !!}" width="50" height="50" class="rounded-circle" style="object-fit: cover;">
                                            <small class="text-muted">{!! $editBirthday['photo'] !!}</small>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Birthday Message</label>
                                    <textarea name="message" class="form-control" rows="3" placeholder="Optional message for this person">{!! $editBirthday['message'] ?? '' !!}</textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="status" class="form-check-input" id="birthday_status" value="1" {!! (!isset($editBirthday) || (isset($editBirthday) && $editBirthday['status'])) ? 'checked' : '' !!}>
                                        <label class="form-check-label" for="birthday_status">Active (visible on frontend)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="birthday_submit" value="1">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-save me-2"></i>Save Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Social Modal -->
    <div class="modal fade" id="socialModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="socialModalLabel">
                        <i class="fas fa-share-alt me-2" style="color: var(--kiboko-blue);"></i>
                        {!! $editSocial ? 'Edit' : 'Add' !!} Social Link
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="socialForm">
@csrf
                    <div class="modal-body">
                        @if($editSocial)
                            <input type="hidden" name="social_id" id="social_id" value="{!! $editSocial['id'] !!}">
                        @else
                            <input type="hidden" name="social_id" id="social_id" value="">
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Platform Name <span class="text-danger">*</span></label>
                                    <input type="text" name="platform" class="form-control" value="{!! $editSocial['platform'] ?? '' !!}" required placeholder="e.g., LinkedIn, Twitter">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Username/Handle</label>
                                    <input type="text" name="username" class="form-control" value="{!! $editSocial['username'] ?? '' !!}" placeholder="@username">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Followers Count</label>
                                    <input type="text" name="followers_count" class="form-control" value="{!! $editSocial['followers_count'] ?? '' !!}" placeholder="e.g., 5.2K">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">URL <span class="text-danger">*</span></label>
                                    <input type="url" name="url" class="form-control" value="{!! $editSocial['url'] ?? '' !!}" required placeholder="https://...">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="2" placeholder="Brief description">{!! $editSocial['description'] ?? '' !!}</textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" name="sort_order" class="form-control" value="{!! $editSocial['sort_order'] ?? 0 !!}" min="0">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Text Color</label>
                                    <input type="color" name="color" class="form-control form-control-color" value="{!! $editSocial['color'] ?? '#009ada' !!}" style="height: 46px; width: 100%;">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Background Color</label>
                                    <input type="color" name="bg_color" class="form-control form-control-color" value="{!! $editSocial['bg_color'] ?? '#f0f9ff' !!}" style="height: 46px; width: 100%;">
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="status" class="form-check-input" id="social_status" value="1" {!! (!isset($editSocial) || (isset($editSocial) && $editSocial['status'])) ? 'checked' : '' !!}>
                                        <label class="form-check-label" for="social_status">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Select Icon</label>
                            <div class="icon-grid" id="iconGrid">
                                @foreach($socialIcons as $icon => $label)
                                <div class="icon-item {!! ($editSocial && $editSocial['icon'] == $icon) ? 'selected' : '' !!}" 
                                     data-icon="{!! $icon !!}" 
                                     onclick="selectIcon(this)"
                                     title="{!! $label !!}">
                                    <i class="fab {!! $icon !!}"></i>
                                </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="icon" id="selectedIcon" value="{!! $editSocial['icon'] ?? 'fa-linkedin-in' !!}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="social_submit" value="1">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-save me-2"></i>Save Link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle modal auto-open for editing
        @if(isset($get['edit_birthday']) && $editBirthday)
        document.addEventListener('DOMContentLoaded', function() {
            var birthdayModal = new bootstrap.Modal(document.getElementById('birthdayModal'));
            birthdayModal.show();
        });
        @endif
        
        @if(isset($get['edit_social']) && $editSocial)
        document.addEventListener('DOMContentLoaded', function() {
            var socialModal = new bootstrap.Modal(document.getElementById('socialModal'));
            socialModal.show();
        });
        @endif
        
        // Icon selection
        function selectIcon(element) {
            document.querySelectorAll('.icon-item').forEach(item => {
                item.classList.remove('selected');
            });
            element.classList.add('selected');
            const icon = element.dataset.icon;
            document.getElementById('selectedIcon').value = icon;
        }
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
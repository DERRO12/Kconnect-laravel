<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Spotlights - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        
        body {
            background: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        
        .avatar-preview {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            object-fit: cover;
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
        
        .rating-stars {
            color: #fbbf24;
        }
        
        .spotlight-type-badge {
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .type-employee {
            background: #e0f2fe;
            color: #0369a1;
        }
        
        .type-team {
            background: #dcfce7;
            color: #166534;
        }

        .action-btn-group {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .photo-preview {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid var(--kiboko-blue);
            padding: 2px;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-1"><i class="fas fa-star me-2" style="color: var(--kiboko-blue);"></i>Employee & Team Spotlights</h2>
                <p class="text-muted">Manage featured employees and teams on the homepage</p>
            </div>
            <button class="btn-gradient" onclick="openAddModal()">
                <i class="fas fa-plus me-2"></i>Add Spotlight
            </button>
        </div>
        
        @if(isset($flashMessage))
            <div class="alert alert-{!! $flashType !!} alert-dismissible fade show" role="alert">
                <i class="fas fa-{!! $flashType == 'success' ? 'check-circle' : 'info-circle' !!} me-2"></i>
                {!! $flashMessage !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
                    @endif
        
        @if($error)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{!! $error !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(count($spotlights) > 0)
        <div class="content-card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name/Team</th>
                            <th>Type</th>
                            <th>Achievement</th>
                            <th>Rating</th>
                            <th>Department</th>
                            <th>Badge</th>
                            <th>Status</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($spotlights as $item)
                        <tr>
                            <td>
                                @if(!empty($item['employee_photo']) && file_exists(kc_public_file('../uploads/' . $item['employee_photo'], 'admin')))
                                    <img src="{{ asset('uploads') }}/{{ $item['employee_photo'] }}" class="avatar-preview" style="object-fit: cover;">
                                @else
                                    <div class="avatar-preview bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-{!! $item['spotlight_type'] == 'team' ? 'users' : 'user' !!} fa-2x text-secondary"></i>
                                    </div>
                                @endif
                              </td>
                              <td>
                                  <strong>{{ $item['employee_name'] }}</strong>
                              </td>
                              <td>
                                  <span class="spotlight-type-badge type-{!! $item['spotlight_type'] !!}">
                                      <i class="fas fa-{!! $item['spotlight_type'] == 'team' ? 'users' : 'user' !!} me-1"></i>
                                      {!! ucfirst($item['spotlight_type']) !!}
                                  </span>
                              </td>
                              <td>
                                  <strong>{{ $item['achievement_title'] }}</strong>
                                  <br>
                                  <small class="text-muted">{{ substr($item['achievement_description'] ?? '', 0, 50) }}...</small>
                              </td>
                              <td>
                                  @if(!empty($item['rating']) && $item['rating'] > 0)
                                      <div class="rating-stars" style="white-space: nowrap;">
                                          @php
                                          $rating = floatval($item['rating']);
                                          $fullStars = floor($rating);
                                          $halfStar = ($rating - $fullStars) >= 0.5;
                                          for ($i = 1; $i <= 5; $i++) {
                                              if ($i <= $fullStars) {
                                                  echo '<i class="fas fa-star"></i>';
                                              } elseif ($halfStar && $i == $fullStars + 1) {
                                                  echo '<i class="fas fa-star-half-alt"></i>';
                                              } else {
                                                  echo '<i class="far fa-star"></i>';
                                              }
                                          }
                                          @endphp
                                          <span class="ms-1 small">({!! $rating !!})</span>
                                      </div>
                                  @else
                                      <span class="text-muted">—</span>
                                  @endif
                              </td>
                              <td>{{ $item['department'] ?? '' }}</td>
                              <td>
                                  @if(!empty($item['badge_text']))
                                      <span class="badge bg-{!! $item['badge_color'] ?: 'primary' !!}">
                                          {{ $item['badge_text'] }}
                                      </span>
                                  @else
                                      <span class="text-muted">—</span>
                                  @endif
                              </td>
                              <td>
                                  <span class="status-badge {!! $item['status'] ? 'status-active' : 'status-inactive' !!}"></span>
                                  {!! $item['status'] ? 'Active' : 'Inactive' !!}
                              </td>
                              <td>
                                  <div class="action-btn-group">
                                      <button onclick='openEditModal({!! json_encode($item) !!});' class="btn btn-sm btn-outline-primary" title="Edit">
                                          <i class="fas fa-edit"></i>
                                      </button>
                                      <a href="?toggle={!! $item['id'] !!}" class="btn btn-sm btn-outline-warning" title="Toggle Status" onclick="return confirm('Toggle status?')">
                                          <i class="fas fa-power-off"></i>
                                      </a>
                                      <a href="?delete={!! $item['id'] !!}" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this spotlight? This action cannot be undone.')">
                                          <i class="fas fa-trash"></i>
                                      </a>
                                  </div>
                              </td>
                          </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="content-card text-center py-5">
            <i class="fas fa-star fa-4x text-muted mb-3"></i>
            <h4>No Spotlights Yet</h4>
            <p class="text-muted">Click the "Add Spotlight" button to create your first spotlight.</p>
            <button class="btn-gradient mt-3" onclick="openAddModal()">
                <i class="fas fa-plus me-2"></i>Add Your First Spotlight
            </button>
        </div>
        @endif
    </div>
    
    <!-- Spotlight Modal -->
    <div class="modal fade" id="spotlightModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-star me-2" style="color: var(--kiboko-blue);"></i>
                        <span id="modalTitle">Add Spotlight</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data" id="spotlightForm" action="">
@csrf
                    <input type="hidden" name="id" id="spotlightId" value="">
                    <input type="hidden" name="existing_photo" id="existingPhoto" value="">
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name / Team Name <span class="text-danger">*</span></label>
                                    <input type="text" name="employee_name" id="employeeName" class="form-control" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Spotlight Type <span class="text-danger">*</span></label>
                                    <select name="spotlight_type" id="spotlightType" class="form-select" required>
                                        <option value="employee">Employee</option>
                                        <option value="team">Team</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Achievement Title <span class="text-danger">*</span></label>
                                    <input type="text" name="achievement_title" id="achievementTitle" class="form-control" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Achievement Description</label>
                                    <textarea name="achievement_description" id="achievementDescription" class="form-control" rows="3"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Rating (out of 5)</label>
                                    <input type="number" name="rating" id="rating" class="form-control" step="0.1" min="0" max="5">
                                    <small class="text-muted">Leave empty for no rating</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Department</label>
                                    <input type="text" name="department" id="department" class="form-control">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Featured Quote</label>
                                    <textarea name="featured_quote" id="featuredQuote" class="form-control" rows="2" placeholder="Inspiring quote from this person/team..."></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Badge Text</label>
                                    <input type="text" name="badge_text" id="badgeText" class="form-control" placeholder="e.g., Top Performer, Team of the Month">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Badge Color</label>
                                    <select name="badge_color" id="badgeColor" class="form-select">
                                        <option value="">None</option>
                                        @foreach($badgeColors as $value => $label)
                                            <option value="{!! $value !!}">{!! $label !!}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Photo</label>
                                    <input type="file" name="employee_photo" id="employeePhoto" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                                    <div id="currentPhotoContainer" style="display: none;" class="mt-2">
                                        <small>Current photo:</small><br>
                                        <img id="currentPhoto" src="" class="photo-preview mt-1">
                                    </div>
                                    <small class="text-muted">Allowed: JPG, PNG, GIF, WEBP. Max size: 5MB</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" name="sort_order" id="sortOrder" class="form-control" min="0" value="0">
                                    <small class="text-muted">Lower numbers appear first</small>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" name="status" class="form-check-input" id="status" value="1" checked>
                                    <label class="form-check-label" for="status">Active (visible on homepage)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-save me-2"></i>Save Spotlight
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let spotlightModal;
        
        document.addEventListener('DOMContentLoaded', function() {
            spotlightModal = new bootstrap.Modal(document.getElementById('spotlightModal'));
            
            @if($editData)
            setTimeout(function() {
                openEditModal({!! json_encode($editData) !!});
            }, 100);
            @endif
        });
        
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Spotlight';
            document.getElementById('spotlightId').value = '';
            document.getElementById('employeeName').value = '';
            document.getElementById('spotlightType').value = 'employee';
            document.getElementById('achievementTitle').value = '';
            document.getElementById('achievementDescription').value = '';
            document.getElementById('rating').value = '';
            document.getElementById('department').value = '';
            document.getElementById('featuredQuote').value = '';
            document.getElementById('badgeText').value = '';
            document.getElementById('badgeColor').value = '';
            document.getElementById('sortOrder').value = '0';
            document.getElementById('status').checked = true;
            document.getElementById('existingPhoto').value = '';
            document.getElementById('currentPhotoContainer').style.display = 'none';
            document.getElementById('employeePhoto').value = '';
            
            spotlightModal.show();
        }
        
        function openEditModal(spotlight) {
            document.getElementById('modalTitle').textContent = 'Edit Spotlight';
            document.getElementById('spotlightId').value = spotlight.id;
            document.getElementById('employeeName').value = spotlight.employee_name;
            document.getElementById('spotlightType').value = spotlight.spotlight_type;
            document.getElementById('achievementTitle').value = spotlight.achievement_title;
            document.getElementById('achievementDescription').value = spotlight.achievement_description || '';
            document.getElementById('rating').value = spotlight.rating || '';
            document.getElementById('department').value = spotlight.department || '';
            document.getElementById('featuredQuote').value = spotlight.featured_quote || '';
            document.getElementById('badgeText').value = spotlight.badge_text || '';
            document.getElementById('badgeColor').value = spotlight.badge_color || '';
            document.getElementById('sortOrder').value = spotlight.sort_order || 0;
            document.getElementById('status').checked = spotlight.status == 1;
            document.getElementById('existingPhoto').value = spotlight.employee_photo || '';
            
            if (spotlight.employee_photo) {
                var imgSrc = '{{ asset('uploads') }}/' + spotlight.employee_photo;
                document.getElementById('currentPhoto').src = imgSrc;
                document.getElementById('currentPhotoContainer').style.display = 'block';
            } else {
                document.getElementById('currentPhotoContainer').style.display = 'none';
            }
            
            document.getElementById('employeePhoto').value = '';
            spotlightModal.show();
        }
    </script>
</body>
</html>
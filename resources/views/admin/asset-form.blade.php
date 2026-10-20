<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{!! $page_title !!}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .main-content { margin-left: 280px; padding: 24px 32px; min-height: 100vh; }
        .form-container { background: white; border-radius: 20px; padding: 30px; border: 1px solid #eef2f6; max-width: 900px; }
        .form-section { margin-bottom: 25px; padding-bottom: 25px; border-bottom: 1px solid #eef2f6; }
        .form-section:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .form-section h5 { font-weight: 700; color: #1e293b; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .form-section h5 i { color: #009ada; }
        .form-label { font-weight: 600; font-size: 0.8rem; color: #475569; margin-bottom: 6px; }
        .form-control, .form-select { border-radius: 10px; border: 2px solid #e2e8f0; padding: 10px 15px; font-size: 0.9rem; transition: all 0.3s; width: 100%; }
        .form-control:focus, .form-select:focus { border-color: #009ada; box-shadow: 0 0 0 3px rgba(0,154,218,0.1); outline: none; }
        
        /* Search Suggestions Dropdown */
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #009ada;
            border-top: none;
            border-radius: 0 0 10px 10px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .search-suggestions.active { display: block; }
        .suggestion-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .suggestion-item:hover { background: #f0f9ff; }
        .suggestion-item:last-child { border-bottom: none; }
        .suggestion-item strong { color: #1e293b; font-size: 0.9rem; }
        .suggestion-item small { color: #64748b; font-size: 0.75rem; }
        .suggestion-item .badge { font-size: 0.7rem; }
        .no-results { padding: 15px; text-align: center; color: #94a3b8; font-size: 0.85rem; }
        
        .search-wrapper { position: relative; }
        .search-wrapper i.fa-search { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; cursor: pointer; }
        .search-wrapper i.fa-spinner { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #009ada; display: none; }
        
        .btn-gradient { background: var(--grad); color: white; border: none; padding: 12px 25px; border-radius: 10px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,154,218,0.3); color: white; }
        .btn-outline-secondary { background: white; border: 1px solid #e2e8f0; color: #475569; padding: 10px 20px; border-radius: 10px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .required-star { color: #dc2626; }
        
        @media (max-width: 992px) { .main-content { margin-left: 0; padding: 16px; } .form-container { padding: 20px; } }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">
                <i class="fas fa-{!! $id > 0 ? 'edit' : 'plus-circle' !!} me-2" style="color: #009ada;"></i>
                {!! $id > 0 ? 'Edit Asset' : 'Add New Asset' !!}
            </h2>
            <a href="manage-assets" class="btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Assets
            </a>
        </div>

        @if ($error)
            <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>{!! $error !!}</div>
        @endif

        <!-- Quick Search Existing Assets -->
        <div class="form-container mb-4">
            <h5><i class="fas fa-search"></i> Quick Find Existing Asset (Optional)</h5>
            <p class="text-muted small">Search for an existing asset to view or reference its details</p>
            <div class="search-wrapper">
                <input type="text" id="assetLookup" class="form-control" placeholder="Type asset name, code, serial number, or brand to search...">
                <i class="fas fa-spinner fa-spin" id="assetSearchSpinner"></i>
                <div class="search-suggestions" id="assetSuggestions"></div>
            </div>
            <div class="mt-2" id="selectedAssetInfo" style="display:none;">
                <div class="p-3 bg-light rounded-3">
                    <strong id="selectedAssetName"></strong> 
                    <span class="badge bg-info ms-2" id="selectedAssetCode"></span>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-3" onclick="clearAssetSelection()">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="form-container">
            <form method="POST" enctype="multipart/form-data" id="assetForm">
                @csrf
                
                <!-- Basic Information -->
                <div class="form-section">
                    <h5><i class="fas fa-info-circle"></i> Basic Information</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Asset Code <span class="required-star">*</span></label>
                            <input type="text" name="asset_code" id="asset_code" class="form-control" 
                                   value="{{ $asset['asset_code'] ?? '' }}" required
                                   placeholder="e.g., IT-LAP-001">
                            <small class="text-muted">Must be unique</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Asset Name <span class="required-star">*</span></label>
                            <input type="text" name="asset_name" id="asset_name" class="form-control" 
                                   value="{{ $asset['asset_name'] ?? '' }}" required
                                   placeholder="e.g., Dell Latitude 5520">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category <span class="required-star">*</span></label>
                            <select name="category" id="category" class="form-select" required>
                                <option value="">-- Select Category --</option>
                                @foreach ($categories as $cat)
                                    <option value="{!! $cat !!}" {!! (isset($asset['category']) && $asset['category'] === $cat) ? 'selected' : '' !!}>{!! $cat !!}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Specifications -->
                <div class="form-section">
                    <h5><i class="fas fa-microchip"></i> Technical Specifications</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Brand</label>
                            <input type="text" name="brand" id="brand" class="form-control" 
                                   value="{{ $asset['brand'] ?? '' }}" placeholder="e.g., Dell, HP, Lenovo">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Model</label>
                            <input type="text" name="model" id="model" class="form-control" 
                                   value="{{ $asset['model'] ?? '' }}" placeholder="e.g., Latitude 5520">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Serial Number</label>
                            <input type="text" name="serial_number" id="serial_number" class="form-control" 
                                   value="{{ $asset['serial_number'] ?? '' }}" placeholder="e.g., SN-ABC123456">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">RAM</label>
                            <input type="text" name="ram" class="form-control" 
                                   value="{{ $asset['ram'] ?? '' }}" placeholder="e.g., 16GB DDR4">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Storage</label>
                            <input type="text" name="storage" class="form-control" 
                                   value="{{ $asset['storage'] ?? '' }}" placeholder="e.g., 512GB SSD">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Processor</label>
                            <input type="text" name="processor" class="form-control" 
                                   value="{{ $asset['processor'] ?? '' }}" placeholder="e.g., Intel i7-11th Gen">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Additional Specifications</label>
                            <textarea name="specifications" class="form-control" rows="3" placeholder="Any other technical details...">{{ $asset['specifications'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Purchase & Warranty -->
                <div class="form-section">
                    <h5><i class="fas fa-shopping-cart"></i> Purchase & Status</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control" value="{{ $asset['purchase_date'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Purchase Cost (UGX)</label>
                            <input type="number" step="0.01" name="purchase_cost" class="form-control" value="{{ $asset['purchase_cost'] ?? '' }}" placeholder="0.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Warranty Expiry</label>
                            <input type="date" name="warranty_expiry" class="form-control" value="{{ $asset['warranty_expiry'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Condition <span class="required-star">*</span></label>
                            <select name="condition_status" class="form-select" required>
                                <option value="">-- Select Condition --</option>
                                @foreach ($conditions as $cond)
                                    <option value="{!! $cond !!}" {!! (isset($asset['condition_status']) && $asset['condition_status'] === $cond) ? 'selected' : '' !!}>{!! $cond !!}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Asset Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            @if ($id > 0 && !empty($asset['image']))
                                <small class="text-success mt-1 d-block"><i class="fas fa-image me-1"></i>Current: {{ $asset['image'] }}</small>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="form-section">
                    <h5><i class="fas fa-sticky-note"></i> Notes</h5>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Any additional notes...">{{ $asset['notes'] ?? '' }}</textarea>
                </div>

                <!-- Submit -->
                <div class="d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input type="checkbox" name="status" class="form-check-input" id="status" {!! (!isset($asset['status']) || $asset['status'] == 1) ? 'checked' : '' !!}>
                        <label class="form-check-label" for="status"><strong>Active</strong> - Asset is available</label>
                    </div>
                    <button type="submit" class="btn-gradient">
                        <i class="fas fa-save"></i> {!! $id > 0 ? 'Update Asset' : 'Add Asset' !!}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // ============================================
    // LIVE SEARCH FUNCTIONALITY
    // ============================================
    const assetLookup = document.getElementById('assetLookup');
    const assetSuggestions = document.getElementById('assetSuggestions');
    const assetSearchSpinner = document.getElementById('assetSearchSpinner');
    let searchTimeout;

    assetLookup.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            assetSuggestions.classList.remove('active');
            assetSuggestions.innerHTML = '';
            return;
        }
        
        // Show spinner
        assetSearchSpinner.style.display = 'block';
        
        searchTimeout = setTimeout(() => {
            fetch(`asset-form?ajax_search=${encodeURIComponent(query)}&type=asset`)
                .then(response => response.json())
                .then(data => {
                    assetSearchSpinner.style.display = 'none';
                    renderSuggestions(data);
                })
                .catch(error => {
                    assetSearchSpinner.style.display = 'none';
                    console.error('Search error:', error);
                });
        }, 300);
    });

    function renderSuggestions(items) {
        if (items.length === 0) {
            assetSuggestions.innerHTML = '<div class="no-results"><i class="fas fa-search me-2"></i>No assets found</div>';
            assetSuggestions.classList.add('active');
            return;
        }
        
        let html = '';
        items.forEach(item => {
            html += `
                <div class="suggestion-item" onclick="selectAsset(${item.id}, '${escapeHtml(item.asset_name)}', '${escapeHtml(item.asset_code)}', '${escapeHtml(item.category)}', '${escapeHtml(item.brand || '')}', '${escapeHtml(item.model || '')}', '${escapeHtml(item.serial_number || '')}')">
                    <div>
                        <strong>${escapeHtml(item.asset_name)}</strong><br>
                        <small>${escapeHtml(item.brand || '')} ${escapeHtml(item.model || '')} | SN: ${escapeHtml(item.serial_number || 'N/A')}</small>
                    </div>
                    <span class="badge bg-info">${escapeHtml(item.asset_code)}</span>
                </div>
            `;
        });
        assetSuggestions.innerHTML = html;
        assetSuggestions.classList.add('active');
    }

    function selectAsset(id, name, code, category, brand, model, serial) {
        // Show selected info
        document.getElementById('selectedAssetInfo').style.display = 'block';
        document.getElementById('selectedAssetName').textContent = name + ' (' + brand + ' ' + model + ')';
        document.getElementById('selectedAssetCode').textContent = code;
        
        // Clear search
        assetLookup.value = '';
        assetSuggestions.classList.remove('active');
        assetSuggestions.innerHTML = '';
        
        // Optionally navigate to edit that asset
        if (confirm('Would you like to edit this asset?\n\n' + name + '\n' + code)) {
            window.location.href = 'asset-form?id=' + id;
        }
    }

    function clearAssetSelection() {
        document.getElementById('selectedAssetInfo').style.display = 'none';
    }

    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!assetLookup.contains(e.target) && !assetSuggestions.contains(e.target)) {
            assetSuggestions.classList.remove('active');
        }
    });

    // Helper function to escape HTML
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    // Form validation
    document.getElementById('assetForm').addEventListener('submit', function(e) {
        const assetCode = document.querySelector('[name="asset_code"]').value.trim();
        const assetName = document.querySelector('[name="asset_name"]').value.trim();
        const category = document.querySelector('[name="category"]').value;
        
        if (!assetCode || !assetName || !category) {
            e.preventDefault();
            alert('Please fill in all required fields (marked with *)');
        }
    });
    </script>
</body>
</html>
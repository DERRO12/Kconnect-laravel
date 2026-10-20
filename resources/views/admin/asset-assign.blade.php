<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Asset - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            min-height: 100vh;
        }

        .form-container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            border: 1px solid #eef2f6;
            max-width: 750px;
        }

        .asset-info-card {
            background: linear-gradient(135deg, #f0f9ff, #f4faf0);
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
            position: relative;
        }

        .asset-info-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--grad);
            border-radius: 4px 0 0 4px;
        }

        /* Search Styles */
        .search-wrapper {
            position: relative;
        }

        .search-input {
            padding: 14px 45px 14px 45px !important;
            border-radius: 12px !important;
            border: 2px solid #e2e8f0 !important;
            font-size: 1rem !important;
            width: 100% !important;
            transition: all 0.3s !important;
            background: white !important;
        }

        .search-input:focus {
            border-color: #009ada !important;
            box-shadow: 0 0 0 4px rgba(0,154,218,0.1) !important;
            outline: none !important;
        }

        .search-input::placeholder {
            color: #94a3b8;
        }

        .search-icon-left {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            z-index: 2;
            pointer-events: none;
        }

        .search-spinner {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #009ada;
            display: none;
            z-index: 2;
        }

        .search-suggestions {
            position: absolute;
            top: calc(100% + 5px);
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #009ada;
            border-radius: 14px;
            max-height: 350px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .search-suggestions.show {
            display: block;
        }

        .suggestion-item {
            padding: 14px 18px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .suggestion-item:hover {
            background: #f0f9ff;
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

        .suggestion-avatar {
            width: 42px;
            height: 42px;
            background: var(--grad);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .suggestion-info {
            flex: 1;
            min-width: 0;
        }

        .suggestion-info .name {
            display: block;
            color: #1e293b;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .suggestion-info .dept {
            color: #64748b;
            font-size: 0.8rem;
        }

        .suggestion-code {
            font-size: 0.75rem;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 8px;
            color: #475569;
            font-weight: 600;
            flex-shrink: 0;
        }

        .no-results {
            padding: 25px;
            text-align: center;
            color: #94a3b8;
        }

        .no-results i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 10px;
        }

        /* Selected Employee Card */
        .selected-employee-card {
            background: #f0fdf4;
            border: 2px solid #76bc21;
            border-radius: 14px;
            padding: 18px;
            margin-top: 15px;
            display: none;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .selected-employee-card .emp-avatar {
            width: 52px;
            height: 52px;
            background: var(--grad);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.3rem;
        }

        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .btn-gradient:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,154,218,0.3);
        }

        .btn-gradient:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-outline-secondary {
            background: white;
            border: 1px solid #e2e8f0;
            color: #475569;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #475569;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 10px 15px;
            font-size: 0.9rem;
            width: 100%;
        }

        .form-control:focus, .form-select:focus {
            border-color: #009ada;
            box-shadow: 0 0 0 3px rgba(0,154,218,0.1);
            outline: none;
        }

        .loading-dots {
            display: inline-block;
        }

        .loading-dots::after {
            content: '...';
            animation: dots 1.5s steps(4, end) infinite;
        }

        @keyframes dots {
            0%, 20% { content: ''; }
            40% { content: '.'; }
            60% { content: '..'; }
            80%, 100% { content: '...'; }
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
            }
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><i class="fas fa-user-plus me-2" style="color: #009ada;"></i>Assign Asset</h2>
            <a href="manage-assets" class="btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>

        @if ($error)
            <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>{!! $error !!}</div>
        @endif

        <!-- Asset Details Card -->
        <div class="asset-info-card">
            <h5 class="mb-3"><i class="fas fa-laptop me-2" style="color: #009ada;"></i>Asset Being Assigned</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">Asset Code</small>
                    <strong>{{ $asset['asset_code'] }}</strong>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Asset Name</small>
                    <strong>{{ $asset['asset_name'] }}</strong>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Category</small>
                    <span class="badge bg-info">{{ $asset['category'] }}</span>
                </div>
                <div class="col-md-6">
                    <small class="text-muted d-block">Serial Number</small>
                    <code>{{ $asset['serial_number'] ?? 'N/A' }}</code>
                </div>
                @if ($asset['brand'] || $asset['model'])
                <div class="col-md-6">
                    <small class="text-muted d-block">Brand / Model</small>
                    <span>{{ trim(($asset['brand'] ?? '') . ' ' . ($asset['model'] ?? '')) }}</span>
                </div>
                @endif
                <div class="col-md-6">
                    <small class="text-muted d-block">Current Condition</small>
                    <span class="badge bg-warning text-dark">{{ $asset['condition_status'] }}</span>
                </div>
            </div>
        </div>

        <!-- Assignment Form -->
        <div class="form-container">
            <form method="POST" id="assignForm">
                @csrf
                
                <!-- EMPLOYEE SEARCH -->
                <div class="mb-4">
                    <label class="form-label">
                        <i class="fas fa-search me-1"></i> Search Employee <span style="color:#dc2626;">*</span>
                    </label>
                    
                    <div class="search-wrapper">
                        <i class="fas fa-search search-icon-left"></i>
                        <input type="text" 
                               id="employeeSearch" 
                               class="search-input" 
                               placeholder="Type employee name, code, email, or department to search..."
                               autocomplete="off">
                        <i class="fas fa-spinner fa-spin search-spinner" id="searchSpinner"></i>
                        <div class="search-suggestions" id="searchSuggestions"></div>
                    </div>
                    
                    <!-- Hidden employee ID field -->
                    <input type="hidden" name="employee_id" id="selectedEmployeeId" value="">
                    
                    <!-- Selected Employee Display -->
                    <div class="selected-employee-card" id="selectedEmployeeCard">
                        <div class="d-flex align-items-center gap-3">
                            <div class="emp-avatar" id="selectedAvatar">?</div>
                            <div class="flex-grow-1">
                                <strong style="font-size:1.05rem;" id="selectedName"></strong>
                                <br>
                                <span class="badge bg-secondary me-1" id="selectedCode"></span>
                                <small class="text-muted" id="selectedDept"></small>
                                <br>
                                <small class="text-muted" id="selectedPosition"></small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearSelection()" title="Remove selection">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>
                    </div>
                    
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle me-1"></i>Start typing at least 2 characters to search
                    </small>
                </div>

                <!-- Assignment Dates -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-calendar-plus me-1"></i> Assignment Date *</label>
                        <input type="datetime-local" name="assigned_date" class="form-control" 
                               value="{!! date('Y-m-d\TH:i') !!}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="fas fa-calendar-check me-1"></i> Expected Return Date</label>
                        <input type="date" name="expected_return_date" class="form-control">
                    </div>
                </div>

                <!-- Condition -->
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-clipboard-check me-1"></i> Condition on Assignment *</label>
                    <select name="condition_on_assignment" class="form-select" required>
                        <option value="">-- Select Condition --</option>
                        @foreach ($conditions as $cond)
                            <option value="{!! $cond !!}" {!! ($cond === $asset['condition_status']) ? 'selected' : '' !!}>
                                {!! $cond !!}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Notes -->
                <div class="mb-4">
                    <label class="form-label"><i class="fas fa-sticky-note me-1"></i> Assignment Notes</label>
                    <textarea name="assignment_notes" class="form-control" rows="3" 
                              placeholder="Any notes about this assignment (optional)..."></textarea>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-gradient" id="submitBtn" disabled>
                    <i class="fas fa-check-circle"></i> Confirm Assignment
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    const searchInput = document.getElementById('employeeSearch');
    const suggestionsDiv = document.getElementById('searchSuggestions');
    const searchSpinner = document.getElementById('searchSpinner');
    const selectedCard = document.getElementById('selectedEmployeeCard');
    const selectedId = document.getElementById('selectedEmployeeId');
    const submitBtn = document.getElementById('submitBtn');
    let searchTimeout;
    let selectedEmployee = null;

    // Search on input
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            suggestionsDiv.classList.remove('show');
            suggestionsDiv.innerHTML = '';
            searchSpinner.style.display = 'none';
            return;
        }
        
        // Show spinner
        searchSpinner.style.display = 'block';
        
        searchTimeout = setTimeout(() => {
            // Use the same file with ajax parameter
            fetch('asset-assign?ajax_search_employee=' + encodeURIComponent(query))
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    searchSpinner.style.display = 'none';
                    renderSuggestions(data);
                })
                .catch(error => {
                    searchSpinner.style.display = 'none';
                    suggestionsDiv.innerHTML = '<div class="no-results"><i class="fas fa-exclamation-circle"></i><p>Error loading results</p></div>';
                    suggestionsDiv.classList.add('show');
                    console.error('Error:', error);
                });
        }, 350);
    });

    function renderSuggestions(employees) {
        if (!Array.isArray(employees) || employees.length === 0) {
            suggestionsDiv.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-user-slash"></i>
                    <p><strong>No employees found</strong></p>
                    <small>Try a different name, code, or department</small>
                </div>`;
            suggestionsDiv.classList.add('show');
            return;
        }
        
        let html = '';
        employees.forEach(emp => {
            const initial = (emp.full_name || '?').charAt(0).toUpperCase();
            html += `
                <div class="suggestion-item" onclick="selectEmployee(${emp.id}, '${escapeHtml(emp.full_name)}', '${escapeHtml(emp.employee_code || '')}', '${escapeHtml(emp.department || '')}', '${escapeHtml(emp.position || '')}')">
                    <div class="suggestion-avatar">${initial}</div>
                    <div class="suggestion-info">
                        <span class="name">${escapeHtml(emp.full_name)}</span>
                        <span class="dept">${escapeHtml(emp.department || 'No Department')}${emp.position ? ' • ' + escapeHtml(emp.position) : ''}</span>
                    </div>
                    <span class="suggestion-code">${escapeHtml(emp.employee_code || 'N/A')}</span>
                </div>
            `;
        });
        suggestionsDiv.innerHTML = html;
        suggestionsDiv.classList.add('show');
    }

    function selectEmployee(id, name, code, department, position) {
        selectedEmployee = { id, name, code, department, position };
        selectedId.value = id;
        
        document.getElementById('selectedName').textContent = name;
        document.getElementById('selectedCode').textContent = code || 'No Code';
        document.getElementById('selectedDept').textContent = department || 'No Department';
        document.getElementById('selectedPosition').textContent = position || '';
        document.getElementById('selectedAvatar').textContent = name.charAt(0).toUpperCase();
        
        selectedCard.style.display = 'block';
        submitBtn.disabled = false;
        
        // Clear search
        searchInput.value = '';
        suggestionsDiv.classList.remove('show');
        suggestionsDiv.innerHTML = '';
        searchSpinner.style.display = 'none';
    }

    function clearSelection() {
        selectedEmployee = null;
        selectedId.value = '';
        selectedCard.style.display = 'none';
        submitBtn.disabled = true;
        searchInput.value = '';
        suggestionsDiv.classList.remove('show');
        searchInput.focus();
    }

    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.classList.remove('show');
        }
    });

    // Escape key closes suggestions
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            suggestionsDiv.classList.remove('show');
            searchSpinner.style.display = 'none';
        }
    });

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Prevent form submission without employee
    document.getElementById('assignForm').addEventListener('submit', function(e) {
        if (!selectedId.value) {
            e.preventDefault();
            alert('Please search and select an employee first!');
            searchInput.focus();
        }
    });
    </script>
</body>
</html>
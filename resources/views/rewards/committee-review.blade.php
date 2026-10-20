<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Committee Review | K-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; padding-top: 80px; }
        .navbar { background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
        .page-header { background: white; border-radius: 24px; padding: 24px 32px; margin-bottom: 30px; border: 1px solid #eef2f6; }
        .filter-bar { background: white; border-radius: 20px; padding: 20px; margin-bottom: 24px; border: 1px solid #eef2f6; }
        .level-tabs { display: flex; gap: 10px; flex-wrap: wrap; }
        .level-tab { padding: 12px 24px; border-radius: 40px; border: none; background: #f1f5f9; color: #475569; font-weight: 600; transition: all 0.3s; }
        .level-tab.active { background: var(--grad); color: white; }
        .level-tab.level-1.active { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
        .level-tab.level-2.active { background: linear-gradient(135deg, #009ada, #06b6d4); }
        .level-tab.level-3.active { background: linear-gradient(135deg, #8b5cf6, #6366f1); }
        .nomination-card { background: white; border-radius: 20px; padding: 24px; margin-bottom: 20px; border: 1px solid #eef2f6; transition: all 0.3s; }
        .nomination-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
        .nomination-card.shortlisted { border-left: 4px solid #10b981; }
        .nomination-card.rejected { border-left: 4px solid #ef4444; opacity: 0.7; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 30px; font-size: 12px; font-weight: 600; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-shortlisted { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-approved { background: #dbeafe; color: #1e40af; }
        .answer-section { background: #f8fafc; border-radius: 16px; padding: 16px; margin-top: 16px; }
        .answer-item { padding: 12px 0; border-bottom: 1px solid #e2e8f0; }
        .answer-item:last-child { border-bottom: none; }
        .action-buttons { display: flex; gap: 12px; margin-top: 20px; }
        .btn-shortlist { background: #10b981; color: white; border: none; padding: 10px 24px; border-radius: 40px; font-weight: 600; }
        .btn-reject { background: #ef4444; color: white; border: none; padding: 10px 24px; border-radius: 40px; font-weight: 600; }
        .btn-approve { background: linear-gradient(135deg, #009ada, #06b6d4); color: white; border: none; padding: 10px 24px; border-radius: 40px; font-weight: 600; }
        .btn-shortlist:hover, .btn-reject:hover, .btn-approve:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
        .review-notes { margin-top: 16px; }
        .review-notes textarea { border-radius: 14px; border: 2px solid #eef2f6; padding: 12px; width: 100%; }
        .stats-card { background: white; border-radius: 20px; padding: 20px; text-align: center; border: 1px solid #eef2f6; }
        .stats-number { font-size: 36px; font-weight: 800; }
        .category-filter { display: flex; gap: 8px; flex-wrap: wrap; }
        .category-btn { padding: 8px 16px; border-radius: 40px; border: 1px solid #e2e8f0; background: white; font-size: 13px; font-weight: 500; transition: all 0.2s; }
        .category-btn.active { background: var(--kiboko-blue); color: white; border-color: var(--kiboko-blue); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="../">
                <img src="../K-CONNECT.png" alt="K-CONNECT" style="height: 25px;">
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="text-muted">
                    <i class="fas fa-gavel me-2"></i>Committee Member - Level {!! $selected_level !!}
                    @if (($committee_memberships[$selected_level] ?? null) != 'Member')
                        <span class="badge ms-2" style="background: var(--grad);">{!! ($committee_memberships[$selected_level] ?? null) !!}</span>
                    @endif
                </span>
                <div class="dropdown">
                    <button class="btn dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        @if (!empty(($currentEmployee['photo'] ?? null)))
                            <img src="../uploads/employees/{{ ($currentEmployee['photo'] ?? null) }}" width="36" height="36" class="rounded-circle">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                        <span>{{ ($currentEmployee['full_name'] ?? null) }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2 class="fw-bold mb-2"><i class="fas fa-clipboard-check me-2" style="color: var(--kiboko-blue);"></i>Committee Review Dashboard</h2>
            <p class="text-muted mb-0">Review and evaluate nominations for Q{!! $current_quarter !!} {!! $current_year !!}</p>
        </div>

        <!-- Level Tabs -->
        <div class="filter-bar">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="level-tabs">
                    @foreach ($committee_memberships as $level => $role)
                        <button class="level-tab level-{!! $level !!} {!! $selected_level == $level ? 'active' : '' !!}" 
                                onclick="window.location.href='?level={!! $level !!}&category={!! $selected_category !!}'">
                            <i class="fas fa-layer-group me-2"></i>Level {!! $level !!}
                            @if ($role != 'Member')
                                <span class="badge bg-light text-dark ms-2">{!! $role !!}</span>
                            @endif
                        </button>
                    @endforeach
                </div>
                <button class="btn btn-outline-primary" onclick="location.reload()">
                    <i class="fas fa-sync-alt me-2"></i>Refresh
                </button>
            </div>
            
            <!-- Category Filter -->
            <div class="category-filter mt-3" id="categoryFilter">
                <!-- Populated via JavaScript -->
            </div>
        </div>

        <!-- Stats Row -->
        <div class="row g-3 mb-4" id="statsRow">
            <!-- Populated via JavaScript -->
        </div>

        <!-- Nominations List -->
        <div id="nominationsList">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-3 text-muted">Loading nominations...</p>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="reviewModalTitle">Review Nomination</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="reviewModalBody">
                    <!-- Populated dynamically -->
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-shortlist" id="shortlistBtn" onclick="submitDecision('shortlisted')">
                        <i class="fas fa-star me-2"></i>Shortlist
                    </button>
                    <button type="button" class="btn btn-reject" id="rejectBtn" onclick="submitDecision('rejected')">
                        <i class="fas fa-times me-2"></i>Reject
                    </button>
                    <button type="button" class="btn btn-approve d-none" id="approveBtn" onclick="submitDecision('approved')">
                        <i class="fas fa-check me-2"></i>Approve as Winner
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const currentLevel = {!! $selected_level !!};
        const currentCategory = {!! $selected_category !!};
        let categories = [];
        let currentNominationId = null;
        let reviewModal;

        document.addEventListener('DOMContentLoaded', function() {
            reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));
            loadCategories();
            loadStats();
            loadNominations();
        });

        async function loadCategories() {
            try {
                const response = await fetch('api/get_categories');
                const data = await response.json();
                if (data.success) {
                    categories = data.data;
                    displayCategoryFilter(data.data);
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        function displayCategoryFilter(cats) {
            const container = document.getElementById('categoryFilter');
            let html = `<button class="category-btn ${currentCategory == 0 ? 'active' : ''}" onclick="filterByCategory(0)">All Categories</button>`;
            cats.forEach(cat => {
                html += `<button class="category-btn ${currentCategory == cat.id ? 'active' : ''}" onclick="filterByCategory(${cat.id})">${escapeHtml(cat.name)}</button>`;
            });
            container.innerHTML = html;
        }

        function filterByCategory(categoryId) {
            window.location.href = `?level=${currentLevel}&category=${categoryId}`;
        }

        async function loadStats() {
            try {
                const response = await fetch(`api/get_committee_stats?level=${currentLevel}&category=${currentCategory}`);
                const data = await response.json();
                if (data.success) {
                    displayStats(data.data);
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        function displayStats(stats) {
            const container = document.getElementById('statsRow');
            container.innerHTML = `
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number" style="color: #f59e0b;">${stats.pending || 0}</div>
                        <div class="text-muted">Pending Review</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number" style="color: #10b981;">${stats.shortlisted || 0}</div>
                        <div class="text-muted">Shortlisted</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number" style="color: #ef4444;">${stats.rejected || 0}</div>
                        <div class="text-muted">Rejected</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="stats-number" style="color: #009ada;">${stats.total || 0}</div>
                        <div class="text-muted">Total Nominations</div>
                    </div>
                </div>
            `;
        }

        async function loadNominations() {
            try {
                const response = await fetch(`api/get_committee_nominations?level=${currentLevel}&category=${currentCategory}`);
                const data = await response.json();
                if (data.success) {
                    displayNominations(data.data);
                }
            } catch (error) {
                console.error('Error loading nominations:', error);
            }
        }

        function displayNominations(nominations) {
            const container = document.getElementById('nominationsList');
            
            if (!nominations || nominations.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3" style="opacity: 0.3;"></i>
                        <h5>No nominations to review</h5>
                        <p class="text-muted">All caught up! Check back later.</p>
                    </div>
                `;
                return;
            }

            let html = '';
            nominations.forEach(nom => {
                const statusClass = nom.status === 'pending' ? 'status-pending' : 
                                   (nom.status === 'shortlisted' ? 'status-shortlisted' : 'status-rejected');
                const cardClass = nom.status === 'shortlisted' ? 'shortlisted' : (nom.status === 'rejected' ? 'rejected' : '');
                const isSelfNom = nom.nominator_id == nom.nominee_id;
                
                html += `
                    <div class="nomination-card ${cardClass}">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 50px; height: 50px; background: ${nom.category_color}20; border-radius: 16px; display: flex; align-items: center; justify-content: center; color: ${nom.category_color};">
                                    <i class="fas fa-${nom.category_icon} fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">${escapeHtml(nom.category_name)}</h6>
                                    <div class="text-muted small">
                                        Nominated by: ${escapeHtml(nom.nominator_name)} (${escapeHtml(nom.nominator_code)})
                                    </div>
                                </div>
                            </div>
                            <span class="status-badge ${statusClass}">
                                <i class="fas fa-${nom.status === 'pending' ? 'clock' : (nom.status === 'shortlisted' ? 'star' : 'times')} me-1"></i>
                                ${nom.status}
                            </span>
                        </div>
                        
                        <div class="mt-3 p-3" style="background: #f8fafc; border-radius: 14px;">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <i class="fas fa-user-circle fa-2x" style="color: var(--kiboko-blue); opacity: 0.5;"></i>
                                <div>
                                    <strong>${escapeHtml(nom.nominee_name)}</strong> (${escapeHtml(nom.nominee_code)})
                                    ${isSelfNom ? '<span class="badge ms-2" style="background: linear-gradient(135deg, #8b5cf6, #6366f1);"><i class="fas fa-user-check me-1"></i>Self</span>' : ''}
                                    <div class="text-muted small">${escapeHtml(nom.nominee_department || '—')} • ${escapeHtml(nom.nominee_position || '—')}</div>
                                </div>
                            </div>
                        </div>
                        
                        ${nom.previous_review ? `
                            <div class="mt-2 small text-muted">
                                <i class="fas fa-history me-1"></i>
                                Previous review: ${nom.previous_review}
                            </div>
                        ` : ''}
                        
                        <div class="action-buttons">
                            <button class="btn btn-outline-primary" onclick='openReviewModal(${JSON.stringify(nom).replace(/'/g, "&apos;")})'>
                                <i class="fas fa-eye me-2"></i>Review Nomination
                            </button>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        function openReviewModal(nomination) {
            currentNominationId = nomination.id;
            
            document.getElementById('reviewModalTitle').innerHTML = `
                <i class="fas fa-${nomination.category_icon} me-2"></i>Review: ${escapeHtml(nomination.category_name)}
            `;
            
            // Show/hide approve button based on level
            const approveBtn = document.getElementById('approveBtn');
            if (currentLevel == 3) {
                approveBtn.classList.remove('d-none');
            } else {
                approveBtn.classList.add('d-none');
            }
            
            let body = `
                <div class="mb-3">
                    <label class="form-label fw-bold">Nominee Information</label>
                    <div class="p-3 bg-light rounded-3">
                        <strong>${escapeHtml(nomination.nominee_name)}</strong> (${escapeHtml(nomination.nominee_code)})<br>
                        ${escapeHtml(nomination.nominee_department || '—')} • ${escapeHtml(nomination.nominee_position || '—')}
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Answers</label>
                    <div class="answer-section">
            `;
            
            if (nomination.answers && nomination.answers.length > 0) {
                nomination.answers.forEach(ans => {
                    body += `
                        <div class="answer-item">
                            <div class="fw-semibold mb-1">${escapeHtml(ans.question_text)}</div>
                            <div class="text-muted">${escapeHtml(ans.answer) || '<em>No answer provided</em>'}</div>
                        </div>
                    `;
                });
            } else {
                body += '<p class="text-muted">No answers provided.</p>';
            }
            
            body += `
                    </div>
                </div>
                
                <div class="review-notes">
                    <label class="form-label fw-bold">Review Notes (Optional)</label>
                    <textarea id="reviewNotes" rows="3" placeholder="Add any notes about this nomination..."></textarea>
                </div>
            `;
            
            document.getElementById('reviewModalBody').innerHTML = body;
            reviewModal.show();
        }

        async function submitDecision(decision) {
            const notes = document.getElementById('reviewNotes').value;
            
            const submitBtn = decision === 'shortlisted' ? document.getElementById('shortlistBtn') : 
                            (decision === 'rejected' ? document.getElementById('rejectBtn') : 
                            document.getElementById('approveBtn'));
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            submitBtn.disabled = true;
            
            try {
                const formData = new FormData();
                formData.append('nomination_id', currentNominationId);
                formData.append('level', currentLevel);
                formData.append('decision', decision);
                formData.append('notes', notes);
                
                const response = await fetch('api/submit_committee_decision', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    reviewModal.hide();
                    loadStats();
                    loadNominations();
                    
                    // Show success message
                    alert(`Nomination ${decision}!`);
                } else {
                    alert(data.message || 'Error processing decision');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }
    </script>
</body>
</html>
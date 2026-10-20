/**
 * Organization Chart - Frontend
 * Fetches from api/get_orgchart.php and renders tree
 */
(function() {
    'use strict';
    
    const API_URL = 'api/get_orgchart.php';
    let orgData = [];
    
    const orgTree = document.getElementById('orgTree');
    const loadingContainer = document.getElementById('loadingContainer');
    const emptyState = document.getElementById('emptyState');
    
    const levelLabels = {
        1: 'CEO',
        2: 'Director',
        3: 'Head',
        4: 'Manager',
        5: 'Staff'
    };
    
    /**
     * Fetch org chart data
     */
    async function fetchOrgChart() {
        try {
            const response = await fetch(API_URL);
            
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Failed to load');
            }
            
            orgData = result.data;
            
            if (orgData.length === 0) {
                showEmptyState();
                return;
            }
            
            renderOrgChart(orgData);
            
        } catch (error) {
            console.error('Org chart error:', error);
            showEmptyState();
        }
    }
    
    /**
     * Render the organization chart
     */
    function renderOrgChart(data) {
        loadingContainer.style.display = 'none';
        emptyState.style.display = 'none';
        orgTree.style.display = 'flex';
        orgTree.innerHTML = '';
        
        // Group employees by level
        const levelGroups = {};
        data.forEach(emp => {
            const level = emp.level || 5;
            if (!levelGroups[level]) {
                levelGroups[level] = [];
            }
            levelGroups[level].push(emp);
        });
        
        // Sort levels
        const sortedLevels = Object.keys(levelGroups)
            .map(Number)
            .sort((a, b) => a - b);
        
        // Create level containers
        sortedLevels.forEach(levelNum => {
            const employees = levelGroups[levelNum];
            const levelContainer = document.createElement('div');
            levelContainer.className = 'org-level';
            levelContainer.setAttribute('data-level', levelNum);
            
            employees.forEach(employee => {
                const hasChildren = data.some(e => e.parent_id === employee.employee_id);
                const node = createOrgNode(employee, hasChildren);
                levelContainer.appendChild(node);
            });
            
            orgTree.appendChild(levelContainer);
        });
    }
    
    /**
     * Create a single org chart node
     */
    function createOrgNode(employee, hasChildren) {
        const node = document.createElement('div');
        node.className = `org-node level-${employee.level}`;
        if (hasChildren) {
            node.classList.add('has-children');
        }
        node.setAttribute('data-employee-id', employee.employee_id);
        node.setAttribute('role', 'button');
        node.setAttribute('tabindex', '0');
        node.setAttribute('aria-label', `View details for ${employee.full_name}`);
        node.title = `Click for details - ${employee.full_name}`;
        
        node.addEventListener('click', () => showEmployeeDetail(employee));
        node.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                showEmployeeDetail(employee);
            }
        });
        
        // Level badge
        const badge = document.createElement('span');
        badge.className = `level-badge level-badge-${employee.level}`;
        badge.textContent = employee.level_label || levelLabels[employee.level] || 'Staff';
        node.appendChild(badge);
        
        // Avatar
        if (employee.photo) {
            const img = document.createElement('img');
            img.src = employee.photo;
            img.className = 'org-avatar';
            img.alt = employee.full_name;
            img.loading = 'lazy';
            img.onerror = function() {
                this.style.display = 'none';
                this.parentElement.insertBefore(createPlaceholder(), this);
            };
            node.appendChild(img);
        } else {
            node.appendChild(createPlaceholder());
        }
        
        // Name
        const nameEl = document.createElement('div');
        nameEl.className = 'org-name';
        nameEl.textContent = employee.full_name;
        node.appendChild(nameEl);
        
        // Position
        if (employee.position) {
            const posEl = document.createElement('div');
            posEl.className = 'org-position';
            posEl.textContent = employee.position;
            node.appendChild(posEl);
        }
        
        // Department
        if (employee.department) {
            const deptEl = document.createElement('div');
            deptEl.className = 'org-department';
            deptEl.innerHTML = `<i class="fas fa-building me-1"></i>${employee.department}`;
            node.appendChild(deptEl);
        }
        
        return node;
    }
    
    /**
     * Create avatar placeholder
     */
    function createPlaceholder() {
        const div = document.createElement('div');
        div.className = 'avatar-placeholder';
        div.innerHTML = '<i class="fas fa-user-tie"></i>';
        return div;
    }
    
    /**
     * Show employee detail modal
     */
    function showEmployeeDetail(employee) {
        const modalBody = document.getElementById('modalBody');
        
        let photoHtml;
        if (employee.photo) {
            photoHtml = `<img src="${employee.photo}" class="detail-avatar" alt="${employee.full_name}" 
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">`;
        } else {
            photoHtml = '';
        }
        
        const placeholderHtml = `
            <div class="avatar-placeholder" style="width:150px;height:150px;margin:0 auto 15px;${employee.photo ? 'display:none;' : ''}">
                <i class="fas fa-user-tie" style="font-size:3rem;"></i>
            </div>`;
        
        modalBody.innerHTML = `
            ${photoHtml}
            ${placeholderHtml}
            <h3 style="font-weight:700;margin-bottom:8px;color:var(--dark);">${employee.full_name}</h3>
            <p style="color:var(--blue);font-weight:600;margin-bottom:4px;">${employee.position || 'No Position'}</p>
            <span class="badge" style="background:${getLevelColor(employee.level)};color:white;font-size:0.8rem;padding:5px 12px;">
                ${employee.level_label || levelLabels[employee.level] || 'Staff'}
            </span>
            
            <div style="background:#f8fafc;border-radius:16px;padding:20px;margin-top:20px;text-align:left;">
                <div style="margin-bottom:10px;display:flex;align-items:center;gap:10px;">
                    <i class="fas fa-id-badge" style="color:var(--blue);width:20px;"></i>
                    <strong style="min-width:70px;">Code:</strong>
                    <span>${employee.employee_code}</span>
                </div>
                <div style="margin-bottom:10px;display:flex;align-items:center;gap:10px;">
                    <i class="fas fa-building" style="color:var(--blue);width:20px;"></i>
                    <strong style="min-width:70px;">Company:</strong>
                    <span>${employee.company_name || 'N/A'}</span>
                </div>
                <div style="margin-bottom:10px;display:flex;align-items:center;gap:10px;">
                    <i class="fas fa-briefcase" style="color:var(--blue);width:20px;"></i>
                    <strong style="min-width:70px;">Dept:</strong>
                    <span>${employee.department || 'N/A'}</span>
                </div>
                ${employee.email ? `
                <div style="display:flex;align-items:center;gap:10px;">
                    <i class="fas fa-envelope" style="color:var(--blue);width:20px;"></i>
                    <strong style="min-width:70px;">Email:</strong>
                    <span>${employee.email}</span>
                </div>` : ''}
            </div>
        `;
        
        const modal = new bootstrap.Modal(document.getElementById('employeeModal'));
        modal.show();
    }
    
    function getLevelColor(level) {
        const colors = {
            1: '#76bc21',
            2: '#f59e0b',
            3: '#8b5cf6',
            4: '#3b82f6',
            5: '#64748b'
        };
        return colors[level] || '#64748b';
    }
    
    function showEmptyState() {
        loadingContainer.style.display = 'none';
        emptyState.style.display = 'block';
        orgTree.style.display = 'none';
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', fetchOrgChart);
    
})();
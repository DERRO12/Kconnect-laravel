/**
 * Organization Chart Manager - Admin Panel
 * Handles CRUD operations for org chart structure
 */
(function() {
    'use strict';
    
    // API Endpoints (relative to admin folder)
    const API = {
        orgchart: '../api/get_orgchart.php',
        employees: '../api/get_employees.php',
        save: '../api/save_orgchart.php',
        delete: '../api/delete_node.php'
    };
    
    // State
    let orgData = [];
    let allEmployees = [];
    let deleteTargetId = null;
    
    // DOM Elements
    const tableBody = document.getElementById('tableBody');
    const editModal = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteModal');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    
    // Form elements
    const editId = document.getElementById('editId');
    const employeeSelect = document.getElementById('employeeSelect');
    const parentSelect = document.getElementById('parentSelect');
    const levelSelect = document.getElementById('levelSelect');
    const levelLabel = document.getElementById('levelLabel');
    const sortOrder = document.getElementById('sortOrder');
    const modalTitle = document.getElementById('modalTitle');
    const orgForm = document.getElementById('orgForm');
    
    // Stats
    const statTotal = document.getElementById('statTotal');
    const statTopLevel = document.getElementById('statTopLevel');
    const statLevels = document.getElementById('statLevels');
    const statAvailable = document.getElementById('statAvailable');
    
    /**
     * Initialize - Load all data
     */
    async function init() {
        showLoading(true);
        try {
            await Promise.all([loadOrgChart(), loadEmployees()]);
            updateStats();
            renderTable();
        } catch (error) {
            console.error('Init error:', error);
            showToast('Failed to load data. Please refresh.', 'error');
        }
        showLoading(false);
    }
    
    /**
     * Load org chart data from API
     */
    async function loadOrgChart() {
        const response = await fetch(API.orgchart);
        if (!response.ok) throw new Error('HTTP ' + response.status);
        const result = await response.json();
        if (!result.success) throw new Error(result.message);
        orgData = result.data;
    }
    
    /**
     * Load all active employees
     */
    async function loadEmployees() {
        const response = await fetch(API.employees);
        if (!response.ok) throw new Error('HTTP ' + response.status);
        const result = await response.json();
        if (!result.success) throw new Error(result.message);
        allEmployees = result.data;
    }
    
    /**
     * Update statistics
     */
    function updateStats() {
        statTotal.textContent = orgData.length;
        statTopLevel.textContent = orgData.filter(e => !e.parent_id).length;
        
        const uniqueLevels = new Set(orgData.map(e => e.level));
        statLevels.textContent = uniqueLevels.size;
        
        const orgIds = new Set(orgData.map(e => e.employee_id));
        const available = allEmployees.filter(e => !orgIds.has(e.id));
        statAvailable.textContent = available.length;
    }
    
    /**
     * Render the table
     */
    function renderTable(filterTerm = '') {
        const term = filterTerm.toLowerCase().trim();
        
        let filtered = orgData;
        if (term) {
            filtered = orgData.filter(emp => {
                return (
                    emp.full_name.toLowerCase().includes(term) ||
                    emp.employee_code.toLowerCase().includes(term) ||
                    (emp.position && emp.position.toLowerCase().includes(term)) ||
                    (emp.department && emp.department.toLowerCase().includes(term)) ||
                    (emp.level_label && emp.level_label.toLowerCase().includes(term))
                );
            });
        }
        
        if (filtered.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6">
                        <div class="empty-table">
                            <i class="fas fa-${term ? 'search' : 'sitemap'}"></i>
                            <p>${term ? 'No matching entries found.' : 'No employees in organization chart yet.'}</p>
                            ${!term ? '<button class="btn btn-primary btn-sm" onclick="window._openAddModal()" style="margin-top:10px;"><i class="fas fa-plus-circle"></i> Add First Employee</button>' : ''}
                        </div>
                    </td>
                </tr>`;
            return;
        }
        
        tableBody.innerHTML = filtered.map(emp => `
            <tr>
                <td>
                    <div class="employee-cell">
                        ${emp.photo 
                            ? `<img src="../${emp.photo}" alt="${emp.full_name}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">`
                            : ''
                        }
                        <div class="employee-placeholder" style="${emp.photo ? 'display:none;' : 'display:flex;'}">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="employee-name">${emp.full_name}</div>
                            <div class="employee-code">${emp.employee_code}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge badge-${emp.level}">
                        ${emp.level_label || 'Level ' + emp.level}
                    </span>
                </td>
                <td>${getParentDisplay(emp)}</td>
                <td>${emp.position || '<span style="color:#94a3b8;">-</span>'}</td>
                <td>${emp.department || '<span style="color:#94a3b8;">-</span>'}</td>
                <td>
                    <div class="action-btns">
                        <button class="btn btn-warning btn-xs" onclick="window._editEntry(${emp.employee_id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-xs" onclick="window._confirmDelete(${emp.employee_id}, '${escapeHtml(emp.full_name)}')" title="Remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
    
    /**
     * Get parent display text
     */
    function getParentDisplay(employee) {
        if (!employee.parent_id) {
            return '<span class="top-level"><i class="fas fa-star me-1"></i>Top Level</span>';
        }
        const parent = orgData.find(e => e.employee_id === employee.parent_id);
        if (parent) {
            return `<span class="parent-name">${parent.full_name}</span>`;
        }
        return '<span class="top-level">Unknown</span>';
    }
    
    /**
     * Open Add Modal
     */
    function openAddModal() {
        modalTitle.textContent = 'Add Employee to Org Chart';
        editId.value = '';
        levelSelect.value = '5';
        levelLabel.value = '';
        sortOrder.value = '0';
        
        populateEmployeeDropdown();
        populateParentDropdown();
        
        // Reset form
        orgForm.reset();
        editId.value = '';
        levelSelect.value = '5';
        
        editModal.classList.add('active');
        employeeSelect.focus();
    }
    
    /**
     * Edit existing entry
     */
    function editEntry(employeeId) {
        const emp = orgData.find(e => e.employee_id === employeeId);
        if (!emp) {
            showToast('Employee not found in org chart', 'error');
            return;
        }
        
        modalTitle.textContent = 'Edit Org Chart Entry';
        editId.value = employeeId;
        levelSelect.value = emp.level;
        levelLabel.value = emp.level_label || '';
        sortOrder.value = emp.sort_order || 0;
        
        populateEmployeeDropdown(employeeId);
        populateParentDropdown(emp.parent_id);
        
        editModal.classList.add('active');
    }
    
    /**
     * Populate employee select dropdown
     */
    function populateEmployeeDropdown(selectedId = null) {
        const orgEmployeeIds = new Set(orgData.map(e => e.employee_id));
        
        // Filter: show all, but mark already-added
        const options = allEmployees.map(emp => {
            const alreadyUsed = orgEmployeeIds.has(emp.id);
            const isSelected = selectedId === emp.id;
            const disabled = alreadyUsed && !isSelected;
            
            return `<option value="${emp.id}" ${isSelected ? 'selected' : ''} ${disabled ? 'disabled' : ''}>
                ${emp.full_name} (${emp.employee_code}) ${disabled ? '- Already in chart' : ''}
            </option>`;
        });
        
        employeeSelect.innerHTML = '<option value="">-- Choose Employee --</option>' + options.join('');
    }
    
    /**
     * Populate parent select dropdown
     */
    function populateParentDropdown(selectedParentId = null) {
        const options = orgData.map(emp => {
            const selected = selectedParentId === emp.employee_id;
            return `<option value="${emp.employee_id}" ${selected ? 'selected' : ''}>
                ${emp.full_name} - ${emp.position || 'No Position'} (${emp.level_label || 'Lvl ' + emp.level})
            </option>`;
        });
        
        parentSelect.innerHTML = '<option value="">-- Nobody (Top Level / CEO) --</option>' + options.join('');
    }
    
    /**
     * Auto-set level label when level changes
     */
    function autoSetLevelLabel() {
        if (levelLabel.value === '') {
            const labels = { '1': 'CEO', '2': 'Director', '3': 'Head', '4': 'Manager', '5': 'Staff' };
            levelLabel.value = labels[levelSelect.value] || '';
        }
    }
    
    /**
     * When employee is selected, auto-fill some data
     */
    function onEmployeeSelect() {
        const selectedId = parseInt(employeeSelect.value);
        if (!selectedId) return;
        
        const emp = allEmployees.find(e => e.id === selectedId);
        if (!emp) return;
        
        // Auto-set level label based on position keywords
        if (emp.position && levelLabel.value === '') {
            const pos = emp.position.toLowerCase();
            if (pos.includes('ceo') || pos.includes('chief')) {
                levelSelect.value = '1';
                levelLabel.value = 'CEO';
            } else if (pos.includes('director') || pos.includes('executive')) {
                levelSelect.value = '2';
                levelLabel.value = 'Director';
            } else if (pos.includes('head') || pos.includes('lead')) {
                levelSelect.value = '3';
                levelLabel.value = 'Head';
            } else if (pos.includes('manager')) {
                levelSelect.value = '4';
                levelLabel.value = 'Manager';
            }
        }
    }
    
    /**
     * Save org chart entry
     */
    async function saveOrgChart(event) {
        event.preventDefault();
        
        const selectedEmployeeId = parseInt(employeeSelect.value);
        const employeeId = editId.value ? parseInt(editId.value) : selectedEmployeeId;
        
        if (!employeeId) {
            showToast('Please select an employee', 'error');
            employeeSelect.focus();
            return;
        }
        
        const parentId = parentSelect.value ? parseInt(parentSelect.value) : null;
        
        // Prevent self-referencing
        if (parentId === employeeId) {
            showToast('An employee cannot report to themselves', 'error');
            return;
        }
        
        const data = {
            employee_id: employeeId,
            parent_id: parentId,
            level: parseInt(levelSelect.value),
            level_label: levelLabel.value.trim() || null,
            sort_order: parseInt(sortOrder.value) || 0
        };
        
        showLoading(true);
        
        try {
            const response = await fetch(API.save, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast(result.message, 'success');
                closeEditModal();
                await loadOrgChart();
                await loadEmployees();
                updateStats();
                renderTable();
            } else {
                showToast(result.message || 'Save failed', 'error');
            }
        } catch (error) {
            console.error('Save error:', error);
            showToast('Network error. Please try again.', 'error');
        }
        
        showLoading(false);
    }
    
    /**
     * Confirm delete
     */
    function confirmDelete(employeeId, name) {
        deleteTargetId = employeeId;
        
        // Check if has subordinates
        const hasChildren = orgData.some(e => e.parent_id === employeeId);
        const childrenCount = orgData.filter(e => e.parent_id === employeeId).length;
        
        let message = `<strong>${name}</strong> will be removed from the organization chart.`;
        if (hasChildren) {
            message += `<br><br><i class="fas fa-info-circle" style="color:var(--warning);"></i> 
                ${childrenCount} subordinate(s) will be reassigned to this person's manager.`;
        }
        
        document.getElementById('deleteMessage').innerHTML = message;
        deleteModal.classList.add('active');
        
        document.getElementById('confirmDeleteBtn').onclick = executeDelete;
    }
    
    /**
     * Execute delete
     */
    async function executeDelete() {
        if (!deleteTargetId) return;
        
        showLoading(true);
        deleteModal.classList.remove('active');
        
        try {
            const response = await fetch(API.delete, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ employee_id: deleteTargetId })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast(result.message, 'success');
                deleteTargetId = null;
                await loadOrgChart();
                await loadEmployees();
                updateStats();
                renderTable();
            } else {
                showToast(result.message || 'Delete failed', 'error');
            }
        } catch (error) {
            console.error('Delete error:', error);
            showToast('Network error. Please try again.', 'error');
        }
        
        showLoading(false);
    }
    
    /**
     * Refresh all data
     */
    async function refreshData() {
        showLoading(true);
        try {
            await Promise.all([loadOrgChart(), loadEmployees()]);
            updateStats();
            renderTable();
            showToast('Data refreshed successfully', 'success');
        } catch (error) {
            showToast('Refresh failed', 'error');
        }
        showLoading(false);
    }
    
    // Modal controls
    function closeEditModal() {
        editModal.classList.remove('active');
    }
    
    function closeDeleteModal() {
        deleteModal.classList.remove('active');
        deleteTargetId = null;
    }
    
    // Close modals on overlay click
    editModal.addEventListener('click', function(e) {
        if (e.target === editModal) closeEditModal();
    });
    
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) closeDeleteModal();
    });
    
    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (editModal.classList.contains('active')) closeEditModal();
            if (deleteModal.classList.contains('active')) closeDeleteModal();
        }
    });
    
    // Search / Filter
    function filterTable() {
        const term = document.getElementById('searchInput').value;
        renderTable(term);
    }
    
    // Toast notification
    function showToast(message, type = 'success') {
        toastMessage.textContent = message;
        toast.className = 'toast toast-' + type + ' show';
        
        if (type === 'success') {
            toastIcon.className = 'fas fa-check-circle';
        } else {
            toastIcon.className = 'fas fa-exclamation-circle';
        }
        
        clearTimeout(window._toastTimeout);
        window._toastTimeout = setTimeout(() => {
            toast.classList.remove('show');
        }, 3500);
    }
    
    // Loading state
    function showLoading(show) {
        loadingOverlay.classList.toggle('active', show);
    }
    
    // Utility: escape HTML
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML.replace(/'/g, "\\'");
    }
    
    // Expose functions to global scope for onclick handlers
    window._openAddModal = openAddModal;
    window._editEntry = editEntry;
    window._confirmDelete = confirmDelete;
    window._refreshData = refreshData;
    
    // Expose for inline onclick
    window.openAddModal = openAddModal;
    window.editEntry = editEntry;
    window.confirmDelete = confirmDelete;
    window.saveOrgChart = saveOrgChart;
    window.onEmployeeSelect = onEmployeeSelect;
    window.autoSetLevelLabel = autoSetLevelLabel;
    window.filterTable = filterTable;
    window.closeEditModal = closeEditModal;
    window.closeDeleteModal = closeDeleteModal;
    window.refreshData = refreshData;
    
    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', init);
    
})();
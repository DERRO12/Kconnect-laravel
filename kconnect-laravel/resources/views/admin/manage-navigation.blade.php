<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Manager - Kiboko Intranet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.css" rel="stylesheet">
    <style>
        /* All existing styles remain the same */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f0f2f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif; }
        .app-wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: white; box-shadow: 2px 0 10px rgba(0,0,0,0.05); position: fixed; height: 100vh; overflow-y: auto; z-index: 100; }
        .sidebar-header { padding: 20px; border-bottom: 1px solid #eef2f6; }
        .sidebar-header h3 { background: linear-gradient(135deg, #009ada, #76bc21); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 700; font-size: 1.3rem; margin: 0; }
        .sidebar-menu { list-style: none; padding: 15px 0; }
        .sidebar-menu li a { display: block; padding: 12px 20px; color: #4a5568; text-decoration: none; transition: all 0.3s; border-left: 3px solid transparent; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: #f7f9fc; border-left-color: #009ada; color: #009ada; }
        .sidebar-menu li a i { width: 25px; font-size: 1.1rem; }
        .main-content { flex: 1; margin-left: 250px; padding: 30px; }
        .content-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .content-header h2 { font-size: 1.8rem; font-weight: 600; color: #2d3748; margin: 0; }
        .content-header h2 i { background: linear-gradient(135deg, #009ada, #76bc21); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-right: 10px; }
        .btn-primary-custom { background: linear-gradient(135deg, #009ada, #76bc21); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; }
        .btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,154,218,0.3); color: white; }
        .btn-outline-custom { background: white; border: 1px solid #e2e8f0; color: #4a5568; padding: 8px 12px; border-radius: 6px; font-size: 0.9rem; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
        .btn-outline-custom:hover { border-color: #009ada; color: #009ada; background: #f0f9ff; }
        .message-alert { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; animation: slideDown 0.3s ease; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .message-alert.success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .message-alert.danger { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .message-alert .close-btn { background: none; border: none; font-size: 1.2rem; cursor: pointer; color: inherit; opacity: 0.7; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid #eef2f6; }
        .stat-number { font-size: 2rem; font-weight: 700; color: #009ada; line-height: 1; margin-bottom: 5px; }
        .stat-label { color: #718096; font-size: 0.9rem; }
        .nav-tree-container { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid #eef2f6; }
        .sortable-container { min-height: 50px; }
        .tree-root { list-style: none; padding: 0; min-height: 50px; }
        .tree-item { margin: 4px 0; list-style: none; }
        .tree-item-content { display: flex; align-items: center; justify-content: space-between; padding: 12px 15px; background: white; border-radius: 8px; border: 2px solid #eef2f6; transition: all 0.2s; margin: 2px 0; cursor: grab; user-select: none; }
        .tree-item-content.dragging { opacity: 0.5; border: 2px dashed #009ada; background: #f0f9ff; cursor: grabbing; }
        .tree-item-content.sortable-ghost { opacity: 0.4; background: #c8e6ff; border: 2px dashed #009ada; }
        .tree-item-content.sortable-drag { opacity: 0.8; transform: rotate(2deg); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .tree-item-content:hover { border-color: #009ada; box-shadow: 0 2px 8px rgba(0,154,218,0.1); }
        .item-info { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }
        .item-indent { display: flex; gap: 5px; min-width: 30px; }
        .indent-line { width: 20px; height: 2px; background: linear-gradient(90deg, #cbd5e0, transparent); }
        .drag-handle { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 8px; color: #94a3b8; cursor: grab; margin-right: 5px; border: 1px solid #e2e8f0; }
        .drag-handle:hover { background: #e2e8f0; color: #009ada; }
        .drag-handle:active { cursor: grabbing; }
        .item-icon { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background: #f0f9ff; border-radius: 8px; color: #009ada; flex-shrink: 0; }
        .item-icon.label { background: #fff3cd; color: #f59e0b; }
        .item-details { display: flex; align-items: center; gap: 12px; min-width: 0; flex: 1; flex-wrap: wrap; }
        .item-title { font-weight: 600; color: #2d3748; white-space: nowrap; }
        .item-badge { padding: 3px 8px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; flex-shrink: 0; }
        .badge-link { background: #009ada; color: white; }
        .badge-label { background: #f59e0b; color: white; }
        .badge-active { background: #76bc21; color: white; }
        .badge-inactive { background: #cbd5e0; color: #4a5568; }
        .item-url-container { display: flex; align-items: center; gap: 5px; background: #f8fafc; padding: 2px 8px; border-radius: 30px; max-width: 300px; border: 1px solid #e2e8f0; }
        .item-url { color: #009ada; font-size: 0.8rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 200px; }
        .copy-url-btn { background: none; border: none; color: #94a3b8; cursor: pointer; padding: 2px 5px; border-radius: 4px; transition: all 0.2s; }
        .copy-url-btn:hover { color: #009ada; background: white; }
        .item-actions { display: flex; gap: 5px; opacity: 0.7; transition: opacity 0.2s; flex-shrink: 0; }
        .tree-item-content:hover .item-actions { opacity: 1; }
        .action-btn { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; border: 1px solid #e2e8f0; background: white; color: #4a5568; cursor: pointer; transition: all 0.2s; text-decoration: none; }
        .action-btn:hover { border-color: #009ada; color: #009ada; transform: translateY(-2px); }
        .action-btn.delete:hover { border-color: #dc3545; color: #dc3545; }
        .action-btn.toggle:hover { border-color: #f59e0b; color: #f59e0b; }
        .tree-children { margin-left: 60px; padding-left: 15px; border-left: 2px dashed #e2e8f0; min-height: 30px; }
        .sortable-placeholder { height: 50px; border: 2px dashed #009ada; border-radius: 8px; background: #f0f9ff; margin: 4px 0; }
        .url-preview { position: fixed; bottom: 20px; right: 20px; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 5px 20px rgba(0,0,0,0.15); border-left: 4px solid #009ada; display: none; z-index: 1060; max-width: 400px; word-break: break-all; }
        .url-preview.show { display: block; animation: slideIn 0.3s; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .drag-info { background: linear-gradient(135deg, #009ada, #76bc21); color: white; padding: 10px 15px; border-radius: 8px; margin-bottom: 15px; display: none; align-items: center; justify-content: space-between; }
        .drag-info.show { display: flex; animation: slideDown 0.3s ease; }
        .drag-info button { background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white; padding: 5px 15px; border-radius: 20px; cursor: pointer; }
        .drag-info button:hover { background: rgba(255,255,255,0.3); }
        .modal-content { border: none; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
        .modal-header { background: linear-gradient(135deg, #f8fafc, #ffffff); border-bottom: 1px solid #eef2f6; padding: 20px 25px; }
        .modal-title { font-weight: 600; color: #2d3748; }
        .modal-body { padding: 25px; }
        .modal-footer { padding: 15px 25px; background: #f8fafc; border-top: 1px solid #eef2f6; }
        .form-label { font-weight: 500; color: #4a5568; margin-bottom: 5px; font-size: 0.9rem; }
        .form-control, .form-select { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; font-size: 0.95rem; }
        .form-control:focus, .form-select:focus { border-color: #009ada; box-shadow: 0 0 0 3px rgba(0,154,218,0.1); outline: none; }
        .form-check-input:checked { background-color: #009ada; border-color: #009ada; }
        @media (max-width: 768px) { .sidebar { width: 0; display: none; } .main-content { margin-left: 0; } .stats-grid { grid-template-columns: repeat(2, 1fr); } .item-url-container { max-width: 150px; } .tree-children { margin-left: 30px; } }
    </style>
</head>
<body>
    <!-- URL Preview Toast -->
    <div class="url-preview" id="urlPreview">
        <div class="d-flex align-items-center mb-2">
            <i class="fas fa-link text-gradient me-2"></i>
            <strong class="flex-grow-1">Full URL</strong>
            <button class="btn btn-sm btn-outline-custom" onclick="copyFullUrl()">
                <i class="fas fa-copy"></i>
            </button>
            <button class="btn btn-sm btn-outline-custom ms-1" onclick="closeUrlPreview()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <p class="mb-0 small" id="fullUrlText" style="word-break: break-all;"></p>
    </div>

    <!-- Drag Info Bar -->
    <div class="drag-info" id="dragInfo">
        <span><i class="fas fa-arrows-alt me-2"></i> <span id="dragInfoText">Drag items to reorder. Changes are saved automatically.</span></span>
        <button onclick="cancelDragMode()">Cancel</button>
    </div>

    @include('admin.partials.sidebar')

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <h2><i class="fas fa-sitemap"></i> Navigation Manager</h2>
            <div>
                <button class="btn-outline-custom me-2" onclick="enableDragMode()" id="dragModeBtn">
                    <i class="fas fa-arrows-alt"></i> Drag Mode
                </button>
                <button class="btn-primary-custom" onclick="openAddModal()">
                    <i class="fas fa-plus me-2"></i>Add New Item
                </button>
            </div>
        </div>

        <!-- Message Alert -->
        @if ($message !== null)
        <div class="message-alert {!! $message_type !!}">
            <span><i class="fas fa-info-circle me-2"></i>{!! $message !!}</span>
            <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
        @endif

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{!! $total !!}</div>
                <div class="stat-label">Total Items</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{!! $active !!}</div>
                <div class="stat-label">Active</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{!! $labels !!}</div>
                <div class="stat-label">Labels</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{!! $links !!}</div>
                <div class="stat-label">Links</div>
            </div>
        </div>

        <!-- Navigation Tree -->
        <div class="nav-tree-container">
            <div class="sortable-container" id="treeContainer">
                @if (empty($tree))
<div class="text-center py-5 text-muted"><i class="fas fa-folder-open fa-3x mb-3"></i><p>No navigation items yet. Click "Add New Item" to create your first menu item.</p></div>
                @else
                @include('admin.manage-navigation-tree', ['items' => $tree, 'level' => 0, 'parentId' => null])
                @endif
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="navModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Navigation Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="navForm">@csrf
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="edit_id" value="0">
                        
                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">URL <small class="text-muted">(leave empty for labels)</small></label>
                            <input type="text" name="url" id="url" class="form-control" placeholder="https://example.com or /page.php">
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Long URLs are automatically truncated in display but stored fully
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Parent Item</label>
                            <select name="parent_id" id="parent_id" class="form-select">
                                <option value="">-- Main Menu (Root) --</option>
                                {!! $parent_options_html !!}
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_label" class="form-check-input" id="is_label">
                                        <label class="form-check-label" for="is_label">This is a label (not clickable)</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="status" class="form-check-input" id="status" checked>
                                        <label class="form-check-label" for="status">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="fas fa-save me-2"></i>Save Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        // Modal instance
        let navModal;
        let currentFullUrl = '';
        let sortableInstances = [];
        let dragModeEnabled = false;
        let saveTimeout = null;
        let isSaving = false;
        
        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            navModal = new bootstrap.Modal(document.getElementById('navModal'));
            
            // Handle label checkbox
            document.getElementById('is_label').addEventListener('change', function() {
                const urlField = document.getElementById('url');
                if(this.checked) {
                    urlField.disabled = true;
                    urlField.placeholder = 'Labels do not use URLs';
                } else {
                    urlField.disabled = false;
                    urlField.placeholder = 'https://example.com or /page.php';
                }
            });
            
            // Auto-hide messages after 5 seconds
            setTimeout(() => {
                document.querySelectorAll('.message-alert').forEach(el => {
                    if (el) {
                        el.style.transition = 'opacity 0.5s';
                        el.style.opacity = '0';
                        setTimeout(() => el.remove(), 500);
                    }
                });
            }, 5000);
            
            // Close URL preview when clicking outside
            document.addEventListener('click', function(event) {
                const preview = document.getElementById('urlPreview');
                if (preview && !event.target.closest('.copy-url-btn') && !event.target.closest('#urlPreview')) {
                    preview.classList.remove('show');
                }
            });
            
            // Initialize sortable
            setTimeout(initSortable, 100);
        });
        
        // Initialize Sortable instances
        function initSortable() {
            // Destroy existing instances
            sortableInstances.forEach(instance => {
                if (instance) instance.destroy();
            });
            sortableInstances = [];
            
            // Find all sortable containers
            const containers = document.querySelectorAll('.tree-root');
            
            if (!containers.length) {
                console.warn('No sortable containers found');
                return;
            }
            
            containers.forEach(container => {
                const sortable = new Sortable(container, {
                    group: 'nested',
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                    handle: '.drag-handle',
                    disabled: !dragModeEnabled,
                    onStart: function(evt) {
                        document.getElementById('dragInfo').classList.add('show');
                        const title = evt.item.querySelector('.item-title');
                        document.getElementById('dragInfoText').textContent = 'Dragging: ' + (title ? title.textContent : 'item');
                    },
                    onEnd: function(evt) {
                        // Save the new order after drag ends
                        if (dragModeEnabled) {
                            saveNewOrder();
                        }
                    },
                    onSort: function(evt) {
                        // Visual feedback during sorting
                    }
                });
                
                sortableInstances.push(sortable);
            });
            
            console.log('Sortable initialized with ' + sortableInstances.length + ' instances');
        }
        
        // Enable drag mode
        function enableDragMode() {
            dragModeEnabled = true;
            const btn = document.getElementById('dragModeBtn');
            btn.innerHTML = '<i class="fas fa-check"></i> Drag Mode Active';
            btn.className = 'btn-primary-custom me-2';
            
            // Enable all sortable instances
            sortableInstances.forEach(instance => {
                instance.option('disabled', false);
            });
            
            // Show drag info
            document.getElementById('dragInfo').classList.add('show');
            document.getElementById('dragInfoText').textContent = 'Drag items to reorder. Changes are saved automatically.';
            
            // Add visual indicator to items
            document.querySelectorAll('.tree-item-content').forEach(el => {
                el.style.border = '2px solid #009ada';
                el.style.backgroundColor = '#f0f9ff';
            });
        }
        
        // Cancel drag mode
        function cancelDragMode() {
            dragModeEnabled = false;
            const btn = document.getElementById('dragModeBtn');
            btn.innerHTML = '<i class="fas fa-arrows-alt"></i> Drag Mode';
            btn.className = 'btn-outline-custom me-2';
            
            // Disable all sortable instances
            sortableInstances.forEach(instance => {
                instance.option('disabled', true);
            });
            
            // Hide drag info
            document.getElementById('dragInfo').classList.remove('show');
            
            // Remove visual indicators
            document.querySelectorAll('.tree-item-content').forEach(el => {
                el.style.border = '2px solid #eef2f6';
                el.style.backgroundColor = 'white';
            });
        }
        
        // Save new order after drag and drop
        function saveNewOrder() {
            if (isSaving) return;
            isSaving = true;
            
            // Clear any existing timeout
            if (saveTimeout) {
                clearTimeout(saveTimeout);
            }
            
            // Collect order data
            const orderData = [];
            
            // Process all items recursively
            function processItems(container, parentId = null) {
                if (!container) return;
                const items = container.children;
                
                for (let i = 0; i < items.length; i++) {
                    const item = items[i];
                    if (!item.classList.contains('tree-item')) continue;
                    
                    const itemId = item.dataset.id;
                    if (!itemId) continue;
                    
                    const childrenContainer = item.querySelector('.tree-children > ul');
                    
                    // Add current item
                    orderData.push({
                        id: parseInt(itemId),
                        sort_order: i,
                        parent_id: parentId ? parseInt(parentId) : null
                    });
                    
                    // Process children if they exist
                    if (childrenContainer && childrenContainer.children.length > 0) {
                        processItems(childrenContainer, itemId);
                    }
                }
            }
            
            // Start from root
            const rootContainer = document.getElementById('sortable-root');
            if (rootContainer) {
                processItems(rootContainer, null);
            } else {
                // Fallback: Find all root containers
                document.querySelectorAll('.tree-root').forEach(container => {
                    if (!container.id || container.id === 'sortable-root') {
                        processItems(container, null);
                    }
                });
            }
            
            // If no items, don't save
            if (orderData.length === 0) {
                isSaving = false;
                return;
            }
            
            // Send to server
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=reorder&order_data=' + encodeURIComponent(JSON.stringify(orderData))
            })
            .then(response => response.json())
            .then(data => {
                isSaving = false;
                if (data.success) {
                    showTemporaryMessage('Order updated successfully! Refreshing...', 'success');
                    // Reload the page after a short delay to show updated order
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showTemporaryMessage('Error updating order: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                isSaving = false;
                console.error('Error:', error);
                showTemporaryMessage('Error saving order. Please refresh and try again.', 'danger');
            });
        }
        
        // Show temporary message
        function showTemporaryMessage(message, type) {
            // Remove existing temp messages
            document.querySelectorAll('.message-alert.temp-message').forEach(el => el.remove());
            
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message-alert ' + type + ' temp-message';
            messageDiv.innerHTML = `
                <span><i class="fas fa-info-circle me-2"></i>${message}</span>
                <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
            `;
            
            const statsGrid = document.querySelector('.stats-grid');
            if (statsGrid && statsGrid.parentNode) {
                statsGrid.parentNode.insertBefore(messageDiv, statsGrid.nextSibling);
            }
            
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.style.transition = 'opacity 0.5s';
                    messageDiv.style.opacity = '0';
                    setTimeout(() => messageDiv.remove(), 500);
                }
            }, 3000);
        }
        
        // Open modal for adding
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Navigation Item';
            document.getElementById('edit_id').value = '0';
            document.getElementById('title').value = '';
            document.getElementById('url').value = '';
            document.getElementById('parent_id').value = '';
            document.getElementById('is_label').checked = false;
            document.getElementById('status').checked = true;
            document.getElementById('url').disabled = false;
            document.getElementById('url').placeholder = 'https://example.com or /page.php';
            navModal.show();
        }
        
        // Open modal for editing
        function openEditModal(item) {
            document.getElementById('modalTitle').textContent = 'Edit Navigation Item';
            document.getElementById('edit_id').value = item.id;
            document.getElementById('title').value = item.title || '';
            document.getElementById('url').value = item.url || '';
            document.getElementById('parent_id').value = item.parent_id || '';
            document.getElementById('is_label').checked = item.is_label == 1;
            document.getElementById('status').checked = item.status == 1;
            
            // Trigger label change
            const event = new Event('change');
            document.getElementById('is_label').dispatchEvent(event);
            
            navModal.show();
        }
        
        // Show full URL in preview
        function showFullUrl(url, event) {
            if (event) event.stopPropagation();
            
            const preview = document.getElementById('urlPreview');
            const fullUrlText = document.getElementById('fullUrlText');
            currentFullUrl = url;
            
            fullUrlText.textContent = url;
            preview.classList.add('show');
            
            // Auto-hide after 10 seconds
            if (window.urlPreviewTimeout) {
                clearTimeout(window.urlPreviewTimeout);
            }
            window.urlPreviewTimeout = setTimeout(() => {
                preview.classList.remove('show');
            }, 10000);
        }
        
        // Copy full URL to clipboard
        function copyFullUrl() {
            if (currentFullUrl) {
                navigator.clipboard.writeText(currentFullUrl).then(function() {
                    showTemporaryMessage('URL copied to clipboard!', 'success');
                }).catch(function() {
                    // Fallback
                    const textarea = document.createElement('textarea');
                    textarea.value = currentFullUrl;
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                    showTemporaryMessage('URL copied to clipboard!', 'success');
                });
                closeUrlPreview();
            }
        }
        
        // Close URL preview
        function closeUrlPreview() {
            document.getElementById('urlPreview').classList.remove('show');
            if (window.urlPreviewTimeout) {
                clearTimeout(window.urlPreviewTimeout);
            }
        }
        
        // Form validation
        document.getElementById('navForm').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            if(!title) {
                e.preventDefault();
                alert('Title is required!');
                return false;
            }
            
            const isLabel = document.getElementById('is_label').checked;
            const url = document.getElementById('url').value;
            
            if(!isLabel && url) {
                if(url.length > 500) {
                    if(!confirm('URL is very long. Do you want to continue?')) {
                        e.preventDefault();
                        return false;
                    }
                }
            }
            return true;
        });
        
        // Handle page unload - cancel drag mode
        window.addEventListener('beforeunload', function() {
            if (dragModeEnabled) {
                cancelDragMode();
            }
        });
        
        console.log('Navigation Manager loaded successfully');
    </script>
</body>
</html>
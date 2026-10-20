{{-- Admin sidebar (was admin/sidebar.php). Included on every admin page with @include('admin.partials.sidebar') --}}
@php
// Get current page filename for active state
$current_page = basename(kc_self());
@endphp

<!-- Sidebar -->
<div class="admin-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="logo-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="logo-text">
                K-Connect <span>Admin</span>
            </div>
        </div>
        <button class="sidebar-toggle-btn" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <div class="sidebar-user">
        <div class="user-avatar">
            {!! session()->has('admin_username') ? strtoupper(substr(session('admin_username'), 0, 1)) : 'A' !!}
        </div>
        <div class="user-info">
            <div class="user-name">{{ session()->has('admin_username') ? session('admin_username') : 'Administrator' }}</div>
            <div class="user-role">Super Admin</div>
        </div>
    </div>
    
    <div class="sidebar-search">
        <i class="fas fa-search"></i>
        <input type="text" id="sidebarSearch" placeholder="Search menu...">
    </div>
    
    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <!-- DASHBOARD SECTION -->
            <li class="menu-section">
                <span class="section-title">Main</span>
            </li>
            
            <li class="menu-item">
                <a href="dashboard.php" class="{{ $current_page == 'dashboard.php' ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- CONTENT MANAGEMENT SECTION -->
            <li class="menu-section">
                <span class="section-title">Content Management</span>
            </li>
            
            <li class="menu-item dropdown">
                <a href="#" class="dropdown-toggle">
                    <i class="fas fa-newspaper"></i>
                    <span>News & Events</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="news-events.php" class="{{ $current_page == 'news-events.php' ? 'active' : '' }}"><i class="fas fa-plus-circle"></i> Manage News</a></li>
                    <li><a href="event-items.php" class="{{ $current_page == 'event-items.php' ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> Manage Events</a></li>
                    
                </ul>
            </li>
            
            <li class="menu-item dropdown">
                <a href="#" class="dropdown-toggle">
                    <i class="fas fa-images"></i>
                    <span>Media Gallery</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="manage-gallery.php" class="{{ $current_page == 'manage-gallery.php' ? 'active' : '' }}"><i class="fas fa-images"></i> Gallery Items</a></li>
                    
                    <li><a href="manage-carousel.php" class="{{ $current_page == 'manage-carousel.php' ? 'active' : '' }}"><i class="fas fa-sliders-h"></i> Homepage Carousel</a></li>
                </ul>
            </li>
            
            <li class="menu-item dropdown">
                <a href="#" class="dropdown-toggle">
                    <i class="fas fa-paint-brush"></i>
                    <span>Brand Assets</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="manage-brand-assets.php" class="{{ $current_page == 'manage-brand-assets.php' ? 'active' : '' }}"><i class="fas fa-folder-open"></i> All Assets</a></li>
                                   </ul>
            </li>
            
            <!-- ORGANIZATION SECTION -->
            <li class="menu-section">
                <span class="section-title">Organization</span>
            </li>
            
            <li class="menu-item dropdown">
                <!-- Organization Chart -->

    <a class="nav-link d-flex align-items-center gap-2 {{ (basename(kc_self()) == 'orgchart_manager.php' || basename(kc_self()) == 'orgchart_view.php') ? 'active' : '' }}" 
       href="orgchart_manager.php">
        <i class="fas fa-sitemap"></i>
        <span>Organization Chart</span>
    </a>

                <a href="#" class="dropdown-toggle">
                    <i class="fas fa-users"></i>
                    <span>Employee Management</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="manage-employees.php" class="{{ $current_page == 'manage-employees.php' ? 'active' : '' }}"><i class="fas fa-user-plus"></i> All Employees</a></li>
                    <li><a href="users.php" class="{{ $current_page == 'users.php' ? 'active' : '' }}"><i class="fas fa-user-shield"></i> System Users</a></li>
                   
                </ul>
            </li>
            
            <li class="menu-item dropdown">
                <a href="#" class="dropdown-toggle">
                    <i class="fas fa-sitemap"></i>
                    <span>Structure</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="manage-orgchart.php" class="{{ $current_page == 'manage-orgchart.php' ? 'active' : '' }}"><i class="fas fa-project-diagram"></i> Organization Chart</a></li>
                    <li><a href="manage-departments.php" class="{{ $current_page == 'manage-departments.php' ? 'active' : '' }}"><i class="fas fa-building"></i> Departments</a></li>
                                    </ul>
            </li>
            
            <li class="menu-item">
                <a href="key-contacts.php" class="{{ $current_page == 'key-contacts.php' ? 'active' : '' }}">
                    <i class="fas fa-address-book"></i>
                    <span>Key Contacts</span>
                </a>
            </li>
            
            <li class="menu-item">
                <a href="manage-birthdays.php" class="{{ $current_page == 'manage-birthdays.php' ? 'active' : '' }}">
                    <i class="fas fa-birthday-cake"></i>
                    <span>Birthdays</span>
                </a>
            </li>
            
            <!-- REWARDS & RECOGNITION SECTION -->
            <li class="menu-section">
                <span class="section-title">Rewards & Recognition</span>
            </li>
            
            <li class="menu-item dropdown">
                <a href="#" class="dropdown-toggle">
                    <i class="fas fa-trophy"></i>
                    <span>Nominations</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <a href="manage-committee.php">
    <i class="fas fa-users-cog"></i>
    <span>Nomination Committee</span>
</a>
                <ul class="submenu">
                    <!-- Quarter Management -->
<li>
    <a href="manage-quarters.php" class="{{ $current_page == 'manage-quarters.php' ? 'active' : '' }}">
        <i class="fas fa-calendar-alt"></i> Quarter Management
    </a>
</li>
<li><a href="nominee-votes.php">
    <i class="fas fa-trophy"></i>
    <span>Nominee Votes</span>
</a></li>
                    <li><a href="nomination-dashboard.php" class="{{ $current_page == 'nomination-dashboard.php' ? 'active' : '' }}"><i class="fas fa-chart-line"></i> Nomination Dashboard</a></li>
                    <li><a href="manage-categories.php" class="{{ $current_page == 'manage-categories.php' ? 'active' : '' }}"><i class="fas fa-tags"></i> Award Categories</a></li>
                    <li><a href="manage-questions.php" class="{{ $current_page == 'manage-questions.php' ? 'active' : '' }}"><i class="fas fa-question-circle"></i> Nomination Questions</a></li>
                    <li><a href="all-nominations.php" class="{{ $current_page == 'all-nominations.php' ? 'active' : '' }}"><i class="fas fa-list"></i> All Nominations</a></li>
                             <li class="nav-item">
    <a class="nav-link" href="manage-spotlights.php">
        <i class="fas fa-star me-2"></i>
        <span>Employee Spotlights</span>
    </a>
</li>
                </ul>
                
            </li>
   
          
<li class="menu-item dropdown">
    <a href="#" class="dropdown-toggle">
        <i class="fas fa-laptop"></i>
        <span>Asset Management</span>
        <i class="fas fa-chevron-down dropdown-arrow"></i>
    </a>
    <ul class="submenu">
        <li><a href="manage-assets.php"><i class="fas fa-boxes"></i> Asset Register</a></li>
        <li><a href="asset-reports.php"><i class="fas fa-chart-bar"></i> Reports & Trail</a></li>
    </ul>
</li>
            <li class="menu-item dropdown">
                <a href="#" class="dropdown-toggle">
                    <i class="fas fa-video"></i>
                    <span>Rewards Media</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="manage-rewards.php" class="{{ $current_page == 'manage-rewards.php' ? 'active' : '' }}"><i class="fas fa-play-circle"></i> Programes & Videos</a></li>
                    
                </ul>
            </li>
            
            <!-- NAVIGATION SECTION -->
            <li class="menu-section">
                <span class="section-title">Navigation</span>
            </li>
            
            <li class="menu-item">
                <a href="manage-navigation.php" class="{{ $current_page == 'manage-navigation.php' ? 'active' : '' }}">
                    <i class="fas fa-sitemap"></i>
                    <span>Menu Manager</span>
                </a>
            </li>
            
            <li class="menu-item">
                <a href="top-resources.php" class="{{ $current_page == 'top-resources.php' ? 'active' : '' }}">
                    <i class="fas fa-link"></i>
                    <span>Resources Links</span>
                </a>
            </li>
            
          <li class="menu-item dropdown">
    <a href="#" class="dropdown-toggle">
        <i class="fas fa-laptop"></i>
        <span>Asset Management</span>
        <i class="fas fa-chevron-down dropdown-arrow"></i>
    </a>
    <ul class="submenu">
        <li><a href="manage-assets.php"><i class="fas fa-boxes"></i> Asset Register</a></li>
        <li><a href="asset-reports.php"><i class="fas fa-chart-bar"></i> Reports & Trail</a></li>
    </ul>
</li>
            
            <!-- SYSTEM SECTION -->
            <li class="menu-section">
                <span class="section-title">System</span>
            </li>
            
            <li class="menu-item dropdown">
                <a class="nav-link" href="it-admin.php">
        <i class="fas fa-headset"></i>
        <span>IT Support</span>
    </a>
                <a href="#" class="dropdown-toggle">
                    <i class="fas fa-database"></i>
                    <span>Database</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="backup.php"><i class="fas fa-download"></i> Database Backup</a></li>
                    <li><a href="#Soptimize.php#"><i class="fas fa-tachometer-alt"></i> Optimize Tables</a></li>
                </ul>
            </li>
            
            <li class="menu-item">
                <a href="system-logs.php#" class="{{ $current_page == 'system-logs.php' ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    <span>System Logs</span>
                </a>
            </li>
            
            <!-- QUICK LINKS -->
            <li class="menu-section">
                <span class="section-title">Quick Links</span>
            </li>
            
            <li class="menu-item">
                <a href="../index.php" target="_blank">
                    <i class="fas fa-home"></i>
                    <span>View Site</span>
                    <i class="fas fa-external-link-alt external-icon"></i>
                </a>
            </li>
            
            <li class="menu-item">
                <a href="../rewards/nomination.php" target="_blank">
                    <i class="fas fa-trophy"></i>
                    <span>Nomination Form</span>
                    <i class="fas fa-external-link-alt external-icon"></i>
                </a>
            </li>
            <li class="nav-item">
    <li class="menu-item">
                <a href="admin-whistleblowing.php" class="{{ $current_page == 'dashboard.php' ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Whistleblowing</span>
                </a>
            </li>
</li>
            <!-- DIVIDER -->
            <li class="divider"></li>
            
            <!-- LOGOUT -->
            <li class="menu-item logout-item">
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <div class="sidebar-version">
            <i class="fas fa-code-branch"></i> v2.0.0
        </div>
    </div>
</div>

<style>
:root {
    --kiboko-green: #76bc21;
    --kiboko-blue: #009ada;
    --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
    --sidebar-bg: #0f172a;
    --sidebar-dark: #1e293b;
    --sidebar-hover: #334155;
    --sidebar-active: #009ada;
    --sidebar-text: #94a3b8;
    --sidebar-text-light: #cbd5e1;
    --sidebar-border: #1e293b;
}

/* Sidebar Container */
.admin-sidebar {
    width: 280px;
    background: var(--sidebar-bg);
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
    overflow-y: auto;
    overflow-x: hidden;
}

/* Custom Scrollbar */
.admin-sidebar::-webkit-scrollbar {
    width: 5px;
}

.admin-sidebar::-webkit-scrollbar-track {
    background: var(--sidebar-dark);
}

.admin-sidebar::-webkit-scrollbar-thumb {
    background: var(--sidebar-active);
    border-radius: 10px;
}

/* Sidebar Header */
.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid var(--sidebar-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sidebar-logo {
    display: flex;
    align-items: center;
    gap: 12px;
}

.logo-icon {
    width: 36px;
    height: 36px;
    background: var(--grad);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.logo-text {
    font-size: 18px;
    font-weight: 700;
    color: white;
}

.logo-text span {
    color: var(--kiboko-blue);
    font-weight: 600;
}

.sidebar-toggle-btn {
    background: transparent;
    border: none;
    color: var(--sidebar-text);
    cursor: pointer;
    display: none;
    font-size: 18px;
}

/* User Profile */
.sidebar-user {
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid var(--sidebar-border);
}

.user-avatar {
    width: 45px;
    height: 45px;
    background: var(--grad);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 18px;
}

.user-info {
    flex: 1;
}

.user-name {
    font-size: 14px;
    font-weight: 600;
    color: white;
    margin-bottom: 4px;
}

.user-role {
    font-size: 11px;
    color: var(--sidebar-text);
}

/* Search Bar */
.sidebar-search {
    margin: 15px 20px;
    position: relative;
}

.sidebar-search i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--sidebar-text);
    font-size: 12px;
}

.sidebar-search input {
    width: 100%;
    padding: 8px 12px 8px 35px;
    background: var(--sidebar-dark);
    border: 1px solid var(--sidebar-border);
    border-radius: 10px;
    color: white;
    font-size: 13px;
    transition: all 0.3s;
}

.sidebar-search input:focus {
    outline: none;
    border-color: var(--kiboko-blue);
    background: var(--sidebar-hover);
}

.sidebar-search input::placeholder {
    color: var(--sidebar-text);
    font-size: 12px;
}

/* Navigation */
.sidebar-nav {
    flex: 1;
    padding: 10px 0;
}

.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

/* Menu Section Title */
.menu-section {
    padding: 15px 20px 8px;
}

.section-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--sidebar-text);
    display: block;
}

/* Menu Items */
.menu-item {
    margin: 2px 0;
}

.menu-item > a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 20px;
    color: var(--sidebar-text);
    text-decoration: none;
    transition: all 0.3s;
    font-size: 14px;
    font-weight: 500;
    position: relative;
}

.menu-item > a i:first-child {
    width: 20px;
    font-size: 16px;
}

.menu-item > a span {
    flex: 1;
}

.dropdown-arrow {
    font-size: 12px;
    transition: transform 0.3s;
}

.menu-item.dropdown.open > .dropdown-toggle .dropdown-arrow {
    transform: rotate(180deg);
}

.menu-item > a:hover {
    background: var(--sidebar-hover);
    color: white;
}

.menu-item > a.active {
    background: linear-gradient(90deg, rgba(0, 154, 218, 0.2) 0%, transparent 100%);
    color: var(--kiboko-blue);
    border-left: 3px solid var(--kiboko-blue);
}

/* Submenu */
.submenu {
    list-style: none;
    padding: 0;
    margin: 0;
    background: var(--sidebar-dark);
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.menu-item.dropdown.open .submenu {
    max-height: 500px;
}

.submenu li a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 20px 10px 52px;
    color: var(--sidebar-text);
    text-decoration: none;
    font-size: 13px;
    transition: all 0.3s;
}

.submenu li a i {
    width: 18px;
    font-size: 12px;
}

.submenu li a:hover {
    background: var(--sidebar-hover);
    color: white;
}

.submenu li a.active {
    color: var(--kiboko-blue);
    background: linear-gradient(90deg, rgba(0, 154, 218, 0.15) 0%, transparent 100%);
}

/* Divider */
.divider {
    height: 1px;
    background: var(--sidebar-border);
    margin: 15px 20px;
}

/* Logout Item */
.logout-item {
    margin-top: 10px;
}

.logout-item a {
    color: #ef4444 !important;
}

.logout-item a:hover {
    background: rgba(239, 68, 68, 0.1) !important;
    color: #ef4444 !important;
}

/* External Icon */
.external-icon {
    font-size: 10px;
    opacity: 0.6;
}

/* Sidebar Footer */
.sidebar-footer {
    padding: 15px 20px;
    border-top: 1px solid var(--sidebar-border);
}

.sidebar-version {
    font-size: 11px;
    color: var(--sidebar-text);
    display: flex;
    align-items: center;
    gap: 6px;
}

.sidebar-version i {
    font-size: 10px;
}

/* Main Content Adjustment */
.main-content {
    margin-left: 280px;
    padding: 20px 30px;
    transition: all 0.3s ease;
    min-height: 100vh;
    background: #f8fafc;
}

/* Mobile Responsive */
@media (max-width: 992px) {
    .admin-sidebar {
        transform: translateX(-100%);
    }
    
    .admin-sidebar.show {
        transform: translateX(0);
    }
    
    .sidebar-toggle-btn {
        display: block;
    }
    
    .main-content {
        margin-left: 0;
        padding: 15px;
    }
    
    /* Overlay when sidebar is open on mobile */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        display: none;
    }
    
    .sidebar-overlay.show {
        display: block;
    }
}

/* Collapsed Sidebar Option (Optional) */
.admin-sidebar.collapsed {
    width: 80px;
}

.admin-sidebar.collapsed .logo-text,
.admin-sidebar.collapsed .sidebar-user,
.admin-sidebar.collapsed .sidebar-search,
.admin-sidebar.collapsed .menu-item > a span,
.admin-sidebar.collapsed .dropdown-arrow,
.admin-sidebar.collapsed .section-title,
.admin-sidebar.collapsed .sidebar-footer,
.admin-sidebar.collapsed .submenu {
    display: none;
}

.admin-sidebar.collapsed .menu-item > a {
    justify-content: center;
    padding: 12px;
}

.admin-sidebar.collapsed .menu-item > a i:first-child {
    margin: 0;
}

.admin-sidebar.collapsed .menu-item > a:hover .tooltip {
    display: block;
}

/* Tooltip for collapsed mode */
.tooltip {
    display: none;
    position: fixed;
    left: 70px;
    background: var(--sidebar-dark);
    color: white;
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1001;
}

/* Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.menu-item.dropdown.open .submenu li {
    animation: fadeIn 0.2s ease forwards;
}
</style>

<script>
// Sidebar Toggle Function
function toggleSidebar() {
    const sidebar = document.querySelector('.admin-sidebar');
    sidebar.classList.toggle('show');
    
    // Add/remove overlay on mobile
    let overlay = document.querySelector('.sidebar-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }
    overlay.classList.toggle('show');
}

// Dropdown Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dropdowns
    const dropdownItems = document.querySelectorAll('.menu-item.dropdown');
    
    dropdownItems.forEach(item => {
        const toggle = item.querySelector('.dropdown-toggle');
        
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Close other dropdowns
            dropdownItems.forEach(other => {
                if (other !== item && other.classList.contains('open')) {
                    other.classList.remove('open');
                }
            });
            
            // Toggle current dropdown
            item.classList.toggle('open');
        });
    });
    
    // Keep dropdown open if active submenu item exists
    dropdownItems.forEach(item => {
        const hasActiveSubmenu = item.querySelector('.submenu a.active');
        if (hasActiveSubmenu) {
            item.classList.add('open');
        }
    });
    
    // Search functionality
    const searchInput = document.getElementById('sidebarSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const menuItems = document.querySelectorAll('.menu-item');
            const menuSections = document.querySelectorAll('.menu-section');
            
            if (searchTerm === '') {
                // Show all
                menuItems.forEach(item => item.style.display = '');
                menuSections.forEach(section => section.style.display = '');
                return;
            }
            
            menuItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = '';
                    // Open parent dropdown if needed
                    const parentDropdown = item.closest('.dropdown');
                    if (parentDropdown) {
                        parentDropdown.classList.add('open');
                    }
                } else {
                    item.style.display = 'none';
                }
            });
            
            menuSections.forEach(section => {
                const hasVisibleItems = Array.from(section.nextElementSibling?.querySelectorAll('.menu-item') || [])
                    .some(item => item.style.display !== 'none');
                section.style.display = hasVisibleItems ? '' : 'none';
            });
        });
    }
    
    // Close sidebar on window resize (if becomes desktop)
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            const sidebar = document.querySelector('.admin-sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            if (sidebar) sidebar.classList.remove('show');
            if (overlay) overlay.classList.remove('show');
        }
    });
});

// Active link highlighting
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const currentFile = currentPath.split('/').pop();
    
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        const href = link.getAttribute('href');
        if (href && (href === currentFile || (href.includes('.php') && currentFile === href))) {
            link.classList.add('active');
            // Open parent dropdown
            const parentDropdown = link.closest('.dropdown');
            if (parentDropdown) {
                parentDropdown.classList.add('open');
            }
        }
    });
});

// Optional: Collapse sidebar on double click (for power users)
let clickCount = 0;
let clickTimer;
document.querySelector('.sidebar-header')?.addEventListener('dblclick', function() {
    const sidebar = document.querySelector('.admin-sidebar');
    sidebar.classList.toggle('collapsed');
});
</script>

<!-- Add this overlay div to your main pages -->
<div class="sidebar-overlay" onclick="toggleSidebar()"></div>
{{-- K-Academy side menu (was kacademy/includes/sidebar.php). Variables come from App\Support\Academy::pageVars(). --}}
@if (!$is_admin_area)
<!-- Mobile Sidebar Toggle Button -->
<div class="mobile-sidebar-toggle d-md-none">
    <button class="btn btn-primary" type="button" id="sidebarToggle">
        <i class="bi bi-list"></i> Menu
    </button>
</div>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Main Sidebar -->
<nav class="col-md-3 col-lg-2 d-md-block bg-white sidebar shadow-sm" id="mainSidebar">
    <div class="position-sticky pt-3">
        <!-- Mobile Close Button -->
        <div class="sidebar-close d-md-none text-end p-2">
            <button class="btn btn-sm btn-outline-secondary" id="sidebarClose">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Sidebar Header -->
        <div class="sidebar-header text-center mb-4 px-3">
            <a href="{{ $is_admin_area ? url('academy/admin/dashboard') : url('academy/dashboard') }}" class="text-decoration-none">
                <h4 class="gradient-text fw-bold">K-Academy</h4>
            </a>
        </div>

        <!-- User Info -->
        @if (!empty($user))
        <div class="user-info text-center mb-4 px-3">
            <div class="avatar-circle bg-gradient text-white mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px;">
                {{ strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) }}
            </div>
            <h6 class="mb-0">{{ $user['full_name'] ?? '' }}</h6>
            <small class="text-secondary">{{ $user['employee_code'] ?? '' }}</small>
        </div>
        @endif

        <!-- Sidebar Menu -->
        <ul class="nav flex-column">
                @foreach ([
                    ['dashboard', ['dashboard.php'], 'bi-speedometer2', 'Dashboard'],
                    ['my-courses', ['my-courses.php'], 'bi-book', 'My Courses'],
                    ['browse-courses', ['browse-courses.php', 'browse-course.php', 'course-overview.php'], 'bi-search', 'Browse Courses'],
                    ['certificates', ['certificates.php', 'certificate-pending.php'], 'bi-award', 'My Certificates'],
                    ['quiz-results', ['quiz-results.php'], 'bi-patch-check', 'Quiz Results'],
                    ['progress', ['progress.php'], 'bi-graph-up', 'My Progress'],
                    ['notifications', ['notifications.php'], 'bi-bell', 'Notifications'],
                    ['profile', ['profile.php'], 'bi-person', 'Profile'],
                ] as [$page, $pages, $icon, $label])
                <li class="nav-item">
                    <a class="nav-link {{ in_array($current_page, $pages) ? 'active' : '' }}" href="{{ url('academy/'.$page) }}">
                        <i class="bi {{ $icon }}"></i>
                        <span>{{ $label }}</span>
                        @if ($page === 'certificates' && $cert_count > 0)
                            <span class="badge bg-success rounded-pill float-end">{{ $cert_count }}</span>
                        @elseif ($page === 'notifications' && ($unread_count ?? 0) > 0)
                            <span class="badge bg-danger rounded-pill float-end">{{ $unread_count }}</span>
                        @endif
                    </a>
                </li>
                @endforeach
                <li class="nav-item mt-4">
                    <a class="nav-link" href="{{ url('/') }}">
                        <i class="bi bi-house"></i>
                        <span>Back to K-Connect</span>
                    </a>
                </li>
                @if (isAdminLoggedIn())
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('academy/admin/dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>K-Academy Admin</span>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link text-danger" href="{{ isLoggedIn() ? url('logout') : url('admin/logout') }}">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>
                </li>
        </ul>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer mt-5 px-3">
            <div class="dark-mode-toggle-sidebar">
                <button class="btn btn-sm btn-outline-secondary w-100" id="sidebarDarkModeToggle">
                    <i class="bi bi-moon-stars me-2"></i>
                    <span>Dark Mode</span>
                </button>
            </div>
            <div class="text-center mt-3">
                <small class="text-secondary">
                    &copy; {{ date('Y') }} K-Academy<br>
                    v1.0.0
                </small>
            </div>
        </div>
    </div>
</nav>

@verbatim
<style>
/* ===== SIDEBAR RESPONSIVE STYLES ===== */

/* Mobile Sidebar Toggle Button */
.mobile-sidebar-toggle {
    position: fixed;
    bottom: 20px;
    left: 20px;
    z-index: 1050;
    display: none;
}

.mobile-sidebar-toggle .btn {
    border-radius: 50px;
    padding: 0.75rem 1.5rem;
    box-shadow: 0 5px 20px rgba(0,154,218,0.3);
    border: none;
    background: linear-gradient(135deg, #009ada, #76bc21);
    color: white;
    font-weight: 600;
}

/* Sidebar Overlay */
.sidebar-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1020;
    backdrop-filter: blur(3px);
    transition: opacity 0.3s ease;
}

.sidebar-overlay.active {
    display: block;
}

/* Main Sidebar */
.sidebar {
    transition: transform 0.3s ease;
    height: 100vh;
    overflow-y: auto;
    z-index: 1030;
}

/* Sidebar Close Button (Mobile) */
.sidebar-close {
    display: none;
}

/* ===== DESKTOP STYLES (lg and up) ===== */
@media (min-width: 992px) {
    .sidebar {
        transform: translateX(0) !important;
    }
}

/* ===== TABLET STYLES (md) ===== */
@media (max-width: 991.98px) {
    .sidebar {
        position: fixed !important;
        left: 0;
        top: 0;
        width: 280px !important;
        transform: translateX(-100%);
        z-index: 1050;
        height: 100vh;
        box-shadow: 2px 0 20px rgba(0,0,0,0.1);
    }
    
    .sidebar.active {
        transform: translateX(0);
    }
    
    .sidebar-close {
        display: block !important;
    }
    
    .mobile-sidebar-toggle {
        display: block;
    }
}

/* ===== MOBILE STYLES (sm and below) ===== */
@media (max-width: 767.98px) {
    .sidebar {
        width: 260px !important;
    }
    
    .mobile-sidebar-toggle {
        bottom: 15px;
        left: 15px;
    }
    
    .mobile-sidebar-toggle .btn {
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
    }
    
    .sidebar-header h4 {
        font-size: 1.3rem;
    }
    
    .user-info h6 {
        font-size: 0.95rem;
    }
    
    .user-info small {
        font-size: 0.8rem;
    }
    
    .nav-link {
        padding: 10px 12px !important;
        font-size: 0.9rem;
    }
    
    .nav-link i {
        font-size: 1.1rem;
    }
    
    .badge.float-end {
        float: none !important;
        margin-left: 8px;
    }
}

/* ===== SMALL MOBILE STYLES (xs) ===== */
@media (max-width: 575.98px) {
    .sidebar {
        width: 85% !important;
        max-width: 280px;
    }
    
    .mobile-sidebar-toggle {
        bottom: 10px;
        left: 10px;
    }
    
    .mobile-sidebar-toggle .btn {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }
}

/* Scrollbar Styling */
.sidebar::-webkit-scrollbar {
    width: 4px;
}

.sidebar::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Animation for sidebar items */
.nav-link {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.nav-link::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(135deg, #009ada, #76bc21);
    transform: scaleY(0);
    transition: transform 0.2s ease;
}

.nav-link:hover::before,
.nav-link.active::before {
    transform: scaleY(1);
}

.nav-link.active {
    background: linear-gradient(135deg, rgba(0,154,218,0.1) 0%, rgba(118,188,33,0.1) 100%);
    color: #009ada !important;
}

/* Badge animations */
.badge {
    transition: transform 0.2s ease;
}

.nav-link:hover .badge {
    transform: scale(1.1);
}

/* Dark mode styles */
.dark-mode .sidebar {
    background: #1e293b !important;
    border-color: #334155 !important;
}

.dark-mode .nav-link {
    color: #cbd5e1 !important;
}

.dark-mode .nav-link:hover,
.dark-mode .nav-link.active {
    background: rgba(255,255,255,0.05) !important;
}

.dark-mode .avatar-circle {
    border: 2px solid #475569;
}

.dark-mode .text-secondary {
    color: #94a3b8 !important;
}

.dark-mode .sidebar-footer small {
    color: #64748b !important;
}
</style>

<script>
// Dark mode toggle for sidebar
document.addEventListener('DOMContentLoaded', function() {
    // Dark mode toggle
    const toggle = document.getElementById('sidebarDarkModeToggle');
    if (toggle) {
        toggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const icon = this.querySelector('i');
            const text = this.querySelector('span');
            
            if (document.body.classList.contains('dark-mode')) {
                icon.classList.remove('bi-moon-stars');
                icon.classList.add('bi-brightness-high-fill');
                text.textContent = 'Light Mode';
                localStorage.setItem('darkMode', 'enabled');
            } else {
                icon.classList.remove('bi-brightness-high-fill');
                icon.classList.add('bi-moon-stars');
                text.textContent = 'Dark Mode';
                localStorage.setItem('darkMode', 'disabled');
            }
        });
        
        // Check for saved dark mode preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
            const icon = toggle.querySelector('i');
            const text = toggle.querySelector('span');
            icon.classList.remove('bi-moon-stars');
            icon.classList.add('bi-brightness-high-fill');
            text.textContent = 'Light Mode';
        }
    }
    
    // Mobile sidebar toggle
    const sidebar = document.getElementById('mainSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('sidebarClose');
    
    function openSidebar() {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    if (toggleBtn) {
        toggleBtn.addEventListener('click', openSidebar);
    }
    
    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidebar);
    }
    
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }
    
    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('active')) {
            closeSidebar();
        }
    });
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth >= 992) {
                closeSidebar();
            }
        }, 250);
    });
    
    // Active link highlighting
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(link => {
        const href = link.getAttribute('href');
        if (href && currentPath.includes(href) && href !== '#') {
            link.classList.add('active');
        }
    });
});
</script>@endverbatim
@endif

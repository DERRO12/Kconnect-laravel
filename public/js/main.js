// ========================================
// KIBOKO INTRANET - OPTIMIZED MAIN.JS
// Fixed: Passive event listeners, storage tracking, performance issues
// Enhanced: Mobile responsiveness, touch events
// ========================================

// Initialize AOS with performance optimizations
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS with reduced motion preference
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true,
            offset: 100,
            disable: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
            startEvent: 'DOMContentLoaded',
            throttleDelay: 99,
            debounceDelay: 50
        });
    }
});

// ========================================
// PERFORMANCE: Optimized scroll handler with passive event
// ========================================

// Back to top button - optimized with passive event
(function() {
    const backToTopButton = document.querySelector('.back-to-top');
    if (backToTopButton) {
        // Use passive event listener to avoid scroll-blocking warnings
        window.addEventListener('scroll', function() {
            if (window.scrollY > 20) {
                backToTopButton.style.display = 'flex';
            } else {
                backToTopButton.style.display = 'none';
            }
        }, { passive: true });
        
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
})();

// ========================================
// OPTIMIZED: Multi-level dropdown with better touch support
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    const isTouchDevice = window.matchMedia('(hover: none) and (pointer: coarse)').matches;
    const isMobile = window.innerWidth <= 991.98;
    
    // Desktop hover dropdowns
    if (!isTouchDevice && !isMobile) {
        // Desktop hover - passive approach
        document.querySelectorAll('.dropdown').forEach(dropdown => {
            dropdown.addEventListener('mouseenter', function() {
                const menu = this.querySelector('.dropdown-menu');
                if (menu) menu.classList.add('show');
            }, { passive: true });
            
            dropdown.addEventListener('mouseleave', function() {
                const menu = this.querySelector('.dropdown-menu');
                if (menu) menu.classList.remove('show');
                this.querySelectorAll('.dropdown-menu .dropdown-menu').forEach(submenu => {
                    submenu.classList.remove('show');
                });
            }, { passive: true });
        });
        
        document.querySelectorAll('.dropdown-submenu').forEach(submenu => {
            submenu.addEventListener('mouseenter', function() {
                const menu = this.querySelector('.dropdown-menu');
                if (menu) menu.classList.add('show');
            }, { passive: true });
            
            submenu.addEventListener('mouseleave', function() {
                const menu = this.querySelector('.dropdown-menu');
                if (menu) menu.classList.remove('show');
            }, { passive: true });
        });
    } else {
        // Mobile touch - click handling
        document.querySelectorAll('.dropdown-toggle').forEach(element => {
            element.addEventListener('click', function(e) {
                // For parent links that have URL
                if (this.classList.contains('nav-link') && this.getAttribute('href') && this.getAttribute('href') !== '#') {
                    // Check if dropdown is open, if not, prevent navigation
                    const parentDropdown = this.closest('.dropdown');
                    const menu = parentDropdown ? parentDropdown.querySelector('.dropdown-menu') : null;
                    
                    if (menu && menu.classList.contains('show')) {
                        // Dropdown is open, allow navigation
                        return;
                    } else if (menu) {
                        // Dropdown is closed, open it and prevent navigation
                        e.preventDefault();
                        e.stopPropagation();
                        menu.classList.toggle('show');
                    }
                } else {
                    e.preventDefault();
                    e.stopPropagation();
                    let submenu = this.nextElementSibling;
                    if (submenu && submenu.classList.contains('dropdown-menu')) {
                        submenu.classList.toggle('show');
                    }
                }
            }, { passive: false });
        });
        
        // Handle submenu toggles
        document.querySelectorAll('.dropdown-submenu > .dropdown-toggle').forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const parent = this.closest('.dropdown-submenu');
                const menu = parent.querySelector('.dropdown-menu');
                if (menu) {
                    menu.classList.toggle('show');
                    parent.classList.toggle('show');
                }
            }, { passive: false });
        });
    }
    
    // Close dropdowns when clicking outside - optimized
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
            document.querySelectorAll('.dropdown-submenu.show').forEach(submenu => {
                submenu.classList.remove('show');
            });
        }
    }, { passive: true });
    
    // Active link highlighting
    const currentLocation = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') === currentLocation) {
            link.classList.add('active');
        }
    });
});

// ========================================
// OPTIMIZED: jQuery AJAX Functions with error handling
// ========================================

if (typeof $ !== 'undefined') {
    $(document).ready(function() {
        
        // ========================================
        // BIRTHDAY AJAX FUNCTIONALITY - Optimized
        // ========================================
        
        window.loadMonthlyBirthdays = function(month, monthName) {
            $('#monthlyBirthdaysModal').modal('show');
            $('#monthlyBirthdaysBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading ${monthName} birthdays...</p>
                </div>
            `);
            
            $.ajax({
                url: 'ajax/birthday-handler?action=get_monthly_birthdays&month=' + month,
                method: 'GET',
                dataType: 'json',
                timeout: 15000,
                success: function(response) {
                    if (response.success) {
                        $('#monthlyBirthdaysBody').html(response.html);
                        $('#modalMonthTitle').text(monthName + ' Birthdays');
                        
                        $('.change-month').off('click').on('click', function() {
                            var newMonth = $(this).data('month');
                            var newMonthName = $(this).data('month-name') || window.getMonthName(newMonth);
                            window.loadMonthlyBirthdays(newMonth, newMonthName);
                        });
                    } else {
                        $('#monthlyBirthdaysBody').html(`
                            <div class="alert alert-danger text-center py-4">
                                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                                <h5 class="alert-heading mb-3">Error Loading Birthdays</h5>
                                <p class="mb-0">${response.message || 'Please try again later.'}</p>
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    $('#monthlyBirthdaysBody').html(`
                        <div class="alert alert-danger text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <h5 class="alert-heading mb-3">Connection Error</h5>
                            <p class="mb-2">Please check your connection and try again.</p>
                            <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                                <i class="fas fa-sync-alt me-2"></i>Refresh Page
                            </button>
                        </div>
                    `);
                }
            });
        };
        
        window.getMonthName = function(monthNumber) {
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
            return months[monthNumber - 1];
        };
        
        // View all birthdays button
        $(document).on('click', '#viewMonthBirthdays, .view-month-btn', function(e) {
            e.preventDefault();
            var month = $(this).data('month');
            var monthName = $(this).data('month-name') || window.getMonthName(month);
            window.loadMonthlyBirthdays(month, monthName);
        });
        
        // Send wishes function
        window.sendWishes = function(birthdayId) {
            var toastElement = document.getElementById('wishesToast');
            if (toastElement) {
                var toast = new bootstrap.Toast(toastElement);
                toast.show();
            }
            
            // Optional confetti effect (only if confetti is loaded)
            if (typeof confetti === 'function') {
                confetti({
                    particleCount: 80,
                    spread: 60,
                    origin: { y: 0.6 },
                    colors: ['#009ada', '#76bc21', '#ffd700'],
                    disableForReducedMotion: true
                });
            }
        };
        
        // ========================================
        // NEWS DETAIL VIEW - Optimized
        // ========================================
        
        $(document).on('click', '.news-card, .news-card-all', function() {
            var newsId = $(this).data('news-id');
            if (!newsId) return;
            
            $('#newsDetailModal').modal('show');
            $('#newsDetailModalBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading news...</p>
                </div>
            `);
            
            $.ajax({
                url: 'ajax/news-handler?action=get_news&id=' + newsId,
                method: 'GET',
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    if (response.success) {
                        $('#newsDetailModalBody').html(response.html);
                    } else {
                        $('#newsDetailModalBody').html(`
                            <div class="alert alert-danger text-center py-4">
                                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                                <p>${response.message || 'Error loading news'}</p>
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#newsDetailModalBody').html(`
                        <div class="alert alert-danger text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <p>Error loading news. Please try again.</p>
                        </div>
                    `);
                }
            });
        });
        
        // ========================================
        // EVENT DETAIL VIEW - Optimized
        // ========================================
        
        $(document).on('click', '.event-item', function() {
            var eventId = $(this).data('event-id');
            if (!eventId) return;
            
            $('#eventDetailModal').modal('show');
            $('#eventDetailModalBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading event details...</p>
                </div>
            `);
            
            $.ajax({
                url: 'ajax/news-handler?action=get_event&id=' + eventId,
                method: 'GET',
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    if (response.success) {
                        $('#eventDetailModalBody').html(response.html);
                    } else {
                        $('#eventDetailModalBody').html(`
                            <div class="alert alert-danger text-center py-4">
                                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                                <p>${response.message || 'Error loading event'}</p>
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#eventDetailModalBody').html(`
                        <div class="alert alert-danger text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <p>Error loading event. Please try again.</p>
                        </div>
                    `);
                }
            });
        });
        
        // ========================================
        // LIKE FUNCTIONALITY - Optimized with anti-spam
        // ========================================
        
        $(document).on('click', '.like-button, .like-button-detail', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var newsId = $(this).data('news-id');
            var button = $(this);
            var likeCount = button.find('.like-count');
            
            // Disable button temporarily to prevent spam
            if (button.hasClass('disabled')) return;
            button.addClass('disabled');
            
            $.ajax({
                url: 'ajax/news-handler?action=like_news',
                method: 'POST',
                data: { news_id: newsId },
                dataType: 'json',
                timeout: 8000,
                success: function(response) {
                    if (response.success) {
                        likeCount.text(response.likes);
                        button.find('i').removeClass('far').addClass('fas text-danger');
                    } else {
                        button.removeClass('disabled');
                        if (response.message === 'Already liked') {
                            // Show subtle feedback
                            button.find('i').addClass('text-danger');
                        }
                    }
                },
                error: function() {
                    button.removeClass('disabled');
                    console.error('Error liking news');
                }
            });
        });
        
        // ========================================
        // SHARE FUNCTIONALITY - Optimized
        // ========================================
        
        $(document).on('click', '.share', function(e) {
            e.preventDefault();
            
            var shareUrl = window.location.href;
            
            // Use modern clipboard API if available
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(shareUrl).then(function() {
                    showToast('Link copied to clipboard!');
                }).catch(function() {
                    fallbackCopy(shareUrl);
                });
            } else {
                fallbackCopy(shareUrl);
            }
        });
        
        function fallbackCopy(text) {
            var tempInput = $('<input>');
            $('body').append(tempInput);
            tempInput.val(text).select();
            document.execCommand('copy');
            tempInput.remove();
            showToast('Link copied to clipboard!');
        }
        
        function showToast(message) {
            var toast = document.getElementById('wishesToast');
            if (toast) {
                var toastBody = toast.querySelector('.toast-body');
                if (toastBody) toastBody.textContent = message;
                var bsToast = new bootstrap.Toast(toast);
                bsToast.show();
            } else {
                alert(message);
            }
        }
        
        // ========================================
        // CONTACT MODAL - Optimized
        // ========================================
        
        $('.contact-card').on('click', function() {
            var contactId = $(this).data('contact-id');
            if (!contactId) return;
            
            $('#contactModalBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading contact details...</p>
                </div>
            `);
            
            $.ajax({
                url: 'ajax/contact-handler?action=get_contact&id=' + contactId,
                method: 'GET',
                dataType: 'json',
                timeout: 8000,
                success: function(response) {
                    if (response.success) {
                        $('#contactModalBody').html(response.html);
                    } else {
                        $('#contactModalBody').html(`
                            <div class="alert alert-danger text-center py-4">
                                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                                <p>${response.message || 'Error loading contact details'}</p>
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#contactModalBody').html(`
                        <div class="alert alert-danger text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <p>Error loading contact details. Please try again.</p>
                        </div>
                    `);
                }
            });
        });
        
        // ========================================
        // ALL SPOTLIGHTS MODAL - Optimized
        // ========================================
        
        const allSpotlightsModal = document.getElementById('allSpotlightsModal');
        if (allSpotlightsModal) {
            allSpotlightsModal.addEventListener('show.bs.modal', function() {
                const modalBody = document.getElementById('allSpotlightsModalBody');
                
                if (modalBody) {
                    modalBody.innerHTML = `
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Loading spotlights...</p>
                        </div>
                    `;
                    
                    fetch('ajax/get-all-spotlights')
                        .then(response => response.text())
                        .then(data => {
                            modalBody.innerHTML = data;
                        })
                        .catch(error => {
                            modalBody.innerHTML = `
                                <div class="alert alert-danger text-center py-5">
                                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                    <p>Error loading spotlights. Please try again.</p>
                                </div>
                            `;
                            console.error('Error:', error);
                        });
                }
            });
        }
    });
}

// ========================================
// OPTIMIZED: Mobile touch scroll for contacts
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    const scrollContainer = document.querySelector('.contacts-horizontal-scroll');
    if (scrollContainer) {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        // Use passive: false only for drag scrolling
        scrollContainer.addEventListener('mousedown', (e) => {
            isDown = true;
            scrollContainer.style.cursor = 'grabbing';
            startX = e.pageX - scrollContainer.offsetLeft;
            scrollLeft = scrollContainer.scrollLeft;
            e.preventDefault();
        }, { passive: false });
        
        scrollContainer.addEventListener('mouseleave', () => {
            isDown = false;
            scrollContainer.style.cursor = 'grab';
        }, { passive: true });
        
        scrollContainer.addEventListener('mouseup', () => {
            isDown = false;
            scrollContainer.style.cursor = 'grab';
        }, { passive: true });
        
        scrollContainer.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - scrollContainer.offsetLeft;
            const walk = (x - startX) * 1.5;
            scrollContainer.scrollLeft = scrollLeft - walk;
        }, { passive: false });
        
        // Touch events for mobile
        scrollContainer.addEventListener('touchstart', (e) => {
            isDown = true;
            startX = e.touches[0].pageX - scrollContainer.offsetLeft;
            scrollLeft = scrollContainer.scrollLeft;
        }, { passive: true });
        
        scrollContainer.addEventListener('touchmove', (e) => {
            if (!isDown) return;
            const x = e.touches[0].pageX - scrollContainer.offsetLeft;
            const walk = (x - startX) * 1.5;
            scrollContainer.scrollLeft = scrollLeft - walk;
        }, { passive: true });
        
        scrollContainer.addEventListener('touchend', () => {
            isDown = false;
        }, { passive: true });
    }
});

// ========================================
// OPTIMIZED: Lazy loading images
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.add('loaded');
                    }
                    observer.unobserve(img);
                }
            });
        }, { rootMargin: '50px' });
        
        document.querySelectorAll('img[loading="lazy"]').forEach(img => {
            if (img.dataset.src) {
                imageObserver.observe(img);
            }
        });
    }
});

// ========================================
// OPTIMIZED: Reduce motion for accessibility
// ========================================

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
if (prefersReducedMotion.matches) {
    document.documentElement.style.setProperty('--transition-base', '0s');
    document.documentElement.style.setProperty('--transition-fast', '0s');
    document.querySelectorAll('.aos-init').forEach(el => {
        el.removeAttribute('data-aos');
    });
}

// ========================================
// OPTIMIZED: View All News/Events buttons
// ========================================

if (typeof $ !== 'undefined') {
    // View all news
    $('#viewAllNewsBtn').click(function(e) {
        e.preventDefault();
        $('#allNewsModal').modal('show');
        $('#allNewsModalBody').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-3 text-muted">Loading news...</p>
            </div>
        `);
        
        $.ajax({
            url: 'ajax/news-handler?action=get_all_news',
            method: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                if (response.success) {
                    $('#allNewsModalBody').html(response.html);
                } else {
                    $('#allNewsModalBody').html(`
                        <div class="alert alert-danger text-center py-4">
                            <p>${response.message || 'Error loading news'}</p>
                        </div>
                    `);
                }
            },
            error: function() {
                $('#allNewsModalBody').html(`
                    <div class="alert alert-danger text-center py-4">
                        <p>Error loading news. Please try again.</p>
                    </div>
                `);
            }
        });
    });
    
    // View all events
    $('[data-bs-target="#allEventsModal"]').click(function(e) {
        e.preventDefault();
        $('#allEventsModal').modal('show');
        $('#allEventsModalBody').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-3 text-muted">Loading events...</p>
            </div>
        `);
        
        $.ajax({
            url: 'ajax/news-handler?action=get_all_events',
            method: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                if (response.success) {
                    $('#allEventsModalBody').html(response.html);
                } else {
                    $('#allEventsModalBody').html(`
                        <div class="alert alert-danger text-center py-4">
                            <p>${response.message || 'Error loading events'}</p>
                        </div>
                    `);
                }
            },
            error: function() {
                $('#allEventsModalBody').html(`
                    <div class="alert alert-danger text-center py-4">
                        <p>Error loading events. Please try again.</p>
                    </div>
                `);
            }
        });
    });
}

// ========================================
// IMPROVED MOBILE SIDEBAR NAVIGATION
// Better touch handling, click functionality, and navigation
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    // Only initialize mobile menu on screens 991.98px and below
    if (window.innerWidth <= 991.98) {
        initMobileSidebar();
    }
    
    // Handle resize - reinitialize if needed
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            const existingSidebar = document.querySelector('.sidebar-nav');
            const existingOverlay = document.querySelector('.sidebar-overlay');
            const existingToggle = document.querySelector('.mobile-nav-toggle');
            
            if (window.innerWidth <= 991.98) {
                if (!document.querySelector('.sidebar-nav')) {
                    initMobileSidebar();
                }
            } else {
                // Clean up mobile elements on desktop
                if (existingSidebar) existingSidebar.remove();
                if (existingOverlay) existingOverlay.remove();
                if (existingToggle) existingToggle.remove();
                
                // Restore original navbar structure
                const navbarNav = document.querySelector('#navbarNav');
                const navbarToggler = document.querySelector('.navbar-toggler');
                if (navbarNav) navbarNav.classList.add('collapse');
                if (navbarToggler) navbarToggler.style.display = '';
            }
        }, 250);
    });
});

// ========================================
// ENHANCED MOBILE SIDEBAR NAVIGATION
// Full screen, clear submenu alignment, instant toggle
// ========================================

function initMobileSidebar() {
    console.log('Initializing mobile sidebar...');
    
    const navbar = document.querySelector('.navbar .container');
    const originalNavMenu = document.querySelector('#navbarNav');
    
    // Don't reinitialize if already exists
    if (document.querySelector('.sidebar-nav')) {
        console.log('Sidebar already exists');
        return;
    }
    
    // Hide the default navbar collapse
    if (originalNavMenu) {
        originalNavMenu.classList.remove('show');
        originalNavMenu.classList.add('d-none');
    }
    
    // Hide the default navbar toggler
    const navbarToggler = document.querySelector('.navbar-toggler');
    if (navbarToggler) {
        navbarToggler.style.display = 'none';
    }
    
    // Create Sidebar Container
    const sidebar = document.createElement('div');
    sidebar.className = 'sidebar-nav';
    sidebar.innerHTML = `
        <div class="sidebar-header">
            <img src="K-CONNECT.png" alt="Logo">
            <i class="fas fa-times close-sidebar"></i>
        </div>
        <div class="sidebar-content"></div>
    `;
    
    // Create Overlay
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    
    // Create Toggle Button - Make sure it's always visible
    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'mobile-nav-toggle';
    toggleBtn.setAttribute('aria-label', 'Menu');
    toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
    
    // Add to DOM
    document.body.appendChild(sidebar);
    document.body.appendChild(overlay);
    
    // IMPORTANT: Add toggle button to navbar container immediately
    if (navbar) {
        // Remove any existing toggle button first
        const existingToggle = document.querySelector('.mobile-nav-toggle');
        if (existingToggle) existingToggle.remove();
        
        navbar.appendChild(toggleBtn);
        console.log('Toggle button added to navbar');
    } else {
        console.error('Navbar container not found');
    }
    
    // Get the navbar-nav content
    let navContent = document.querySelector('.navbar-nav');
    if (!navContent) {
        navContent = document.querySelector('#navbarNav .navbar-nav');
    }
    
    if (navContent) {
        const sidebarContent = sidebar.querySelector('.sidebar-content');
        // Clone the navigation
        const clonedNav = navContent.cloneNode(true);
        sidebarContent.appendChild(clonedNav);
        
        // Add class for styling
        clonedNav.classList.add('sidebar-nav-list');
        
        // Remove any existing show classes
        clonedNav.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            menu.classList.remove('show');
        });
        
        // Process all dropdown toggles
        clonedNav.querySelectorAll('.dropdown-toggle').forEach(toggle => {
            // Store the dropdown menu
            let menu = toggle.nextElementSibling;
            if (!menu || !menu.classList.contains('dropdown-menu')) {
                menu = toggle.closest('.dropdown')?.querySelector('.dropdown-menu');
            }
            
            if (menu) {
                // Initially hide menu
                menu.classList.remove('show');
                menu.style.display = 'none';
                
                // Remove existing click handlers and add new one
                const newToggle = toggle.cloneNode(true);
                toggle.parentNode.replaceChild(newToggle, toggle);
                
                newToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const currentMenu = this.nextElementSibling && this.nextElementSibling.classList.contains('dropdown-menu') 
                        ? this.nextElementSibling 
                        : this.closest('.dropdown')?.querySelector('.dropdown-menu');
                    
                    if (currentMenu) {
                        // Check if this dropdown is already open
                        const isOpen = currentMenu.style.display === 'block';
                        
                        // Close all dropdowns in sidebar first
                        clonedNav.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                            otherMenu.style.display = 'none';
                            otherMenu.classList.remove('show');
                        });
                        clonedNav.querySelectorAll('.dropdown-toggle.show').forEach(otherToggle => {
                            otherToggle.classList.remove('show');
                        });
                        
                        // If it wasn't open, open this one
                        if (!isOpen) {
                            currentMenu.style.display = 'block';
                            currentMenu.classList.add('show');
                            this.classList.add('show');
                        }
                    }
                });
            }
        });
        
        // Handle submenu toggles separately
        clonedNav.querySelectorAll('.dropdown-submenu > .dropdown-toggle').forEach(toggle => {
            const parentSubmenu = toggle.closest('.dropdown-submenu');
            const submenu = parentSubmenu?.querySelector('.dropdown-menu');
            
            if (submenu) {
                submenu.style.display = 'none';
                
                const newToggle = toggle.cloneNode(true);
                toggle.parentNode.replaceChild(newToggle, toggle);
                
                newToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const isOpen = submenu.style.display === 'block';
                    
                    // Close other submenus at same level
                    parentSubmenu.closest('.dropdown-menu')?.querySelectorAll('.dropdown-submenu .dropdown-menu').forEach(otherMenu => {
                        if (otherMenu !== submenu) {
                            otherMenu.style.display = 'none';
                        }
                    });
                    
                    // Toggle this submenu
                    submenu.style.display = isOpen ? 'none' : 'block';
                    this.classList.toggle('show', !isOpen);
                });
            }
        });
        
        // Handle regular navigation links
        clonedNav.querySelectorAll('.nav-link:not(.dropdown-toggle)').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href && href !== '#' && href !== 'javascript:void(0)') {
                    closeSidebar();
                    setTimeout(() => {
                        window.location.href = href;
                    }, 150);
                    e.preventDefault();
                    return false;
                }
            });
        });
        
        // Admin login button removed from sidebar as requested
        // No admin login button added
    }
    
    // Sidebar control functions
    const openSidebar = () => {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.width = '100%';
        document.body.style.top = `-${window.scrollY}px`;
    };
    
    const closeSidebar = () => {
        const scrollY = document.body.style.top;
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.width = '';
        document.body.style.top = '';
        if (scrollY) {
            window.scrollTo(0, parseInt(scrollY || '0') * -1);
        }
        
        // Close all dropdowns
        sidebar.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
            menu.classList.remove('show');
        });
        sidebar.querySelectorAll('.dropdown-toggle.show').forEach(toggle => {
            toggle.classList.remove('show');
        });
    };
    
    // Event listeners
    toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        openSidebar();
    });
    
    overlay.addEventListener('click', closeSidebar);
    
    const closeBtn = sidebar.querySelector('.close-sidebar');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidebar);
    }
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('active')) {
            closeSidebar();
        }
    });
    
    console.log('Mobile sidebar initialized successfully');
}

// Force initialization on page load and after any dynamic content changes
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on mobile
    if (window.innerWidth <= 991.98) {
        // Small delay to ensure DOM is fully ready
        setTimeout(function() {
            initMobileSidebar();
        }, 100);
    }
});

// Also watch for resize events to reinitialize if needed
let resizeTimeout;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(function() {
        const isMobile = window.innerWidth <= 991.98;
        const hasSidebar = document.querySelector('.sidebar-nav');
        const hasToggle = document.querySelector('.mobile-nav-toggle');
        
        if (isMobile && !hasSidebar) {
            // On mobile without sidebar, initialize
            initMobileSidebar();
        } else if (!isMobile && hasSidebar) {
            // On desktop with sidebar, remove it
            const sidebar = document.querySelector('.sidebar-nav');
            const overlay = document.querySelector('.sidebar-overlay');
            const toggle = document.querySelector('.mobile-nav-toggle');
            if (sidebar) sidebar.remove();
            if (overlay) overlay.remove();
            if (toggle) toggle.remove();
            
            // Restore original navbar
            const originalNavMenu = document.querySelector('#navbarNav');
            if (originalNavMenu) {
                originalNavMenu.classList.remove('d-none');
            }
            const navbarToggler = document.querySelector('.navbar-toggler');
            if (navbarToggler) {
                navbarToggler.style.display = '';
            }
            
            // Reset body styles
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.width = '';
            document.body.style.top = '';
        }
    }, 250);
});

// Also check immediately in case DOMContentLoaded already fired
if (document.readyState === 'loading') {
    // Still loading, wait for DOMContentLoaded
} else {
    // DOM already loaded, initialize if mobile
    if (window.innerWidth <= 991.98) {
        setTimeout(function() {
            initMobileSidebar();
        }, 100);
    }
}
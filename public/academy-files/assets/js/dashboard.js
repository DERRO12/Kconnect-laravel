// Dashboard specific JavaScript

// Charts
function initializeDashboardCharts() {
    // Enrollment trend chart
    const enrollmentCtx = document.getElementById('enrollmentChart')?.getContext('2d');
    if (enrollmentCtx) {
        new Chart(enrollmentCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Course Enrollments',
                    data: [12, 19, 15, 17, 14, 13],
                    borderColor: '#009ada',
                    backgroundColor: 'rgba(0, 154, 218, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    
    // Course completion chart
    const completionCtx = document.getElementById('completionChart')?.getContext('2d');
    if (completionCtx) {
        new Chart(completionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Not Started'],
                datasets: [{
                    data: [5, 8, 3],
                    backgroundColor: ['#76bc21', '#009ada', '#e2e8f0'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

// Progress circles
function updateProgressCircles() {
    document.querySelectorAll('.progress-circle').forEach(circle => {
        const progress = parseInt(circle.dataset.progress || 0);
        const angle = (progress / 100) * 360;
        circle.style.background = `conic-gradient(var(--kiboko-blue) ${angle}deg, var(--kiboko-border) ${angle}deg)`;
    });
}

// Notification system
class NotificationManager {
    constructor() {
        this.notifications = [];
        this.checkInterval = 30000; // Check every 30 seconds
    }
    
    start() {
        this.fetchNotifications();
        setInterval(() => this.fetchNotifications(), this.checkInterval);
    }
    
    fetchNotifications() {
        fetch('api/get-notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.notifications && data.notifications.length > 0) {
                    this.showNotification(data.notifications[0]);
                    this.updateBadge(data.unread_count);
                }
            });
    }
    
    showNotification(notification) {
        // Show browser notification if supported
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(notification.title, {
                body: notification.message,
                icon: '/assets/images/logo.png'
            });
        }
        
        // Show toast notification
        this.showToast(notification);
    }
    
    showToast(notification) {
        const toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) return;
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${notification.type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${notification.title}</strong><br>
                    <small>${notification.message}</small>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
    
    updateBadge(count) {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }
    }
}

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboardCharts();
    updateProgressCircles();
    
    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
    
    // Initialize notification manager
    const notificationManager = new NotificationManager();
    notificationManager.start();
    
    // Refresh data periodically
    setInterval(() => {
        fetch('api/get-dashboard-stats.php')
            .then(response => response.json())
            .then(data => {
                // Update statistics
                Object.keys(data).forEach(key => {
                    const element = document.getElementById(`stat-${key}`);
                    if (element) {
                        element.textContent = data[key];
                    }
                });
            });
    }, 60000); // Refresh every minute
});
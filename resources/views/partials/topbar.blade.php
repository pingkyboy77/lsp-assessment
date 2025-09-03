<!-- Top Navbar -->
<nav class="top-navbar" id="topNavbar">
    <div class="d-flex align-items-center">
        <button class="sidebar-toggle me-3" id="sidebarToggle" title="Toggle Sidebar">
            <i class="bi bi-layout-sidebar"></i>
        </button>
        <h5 class="mb-0 fw-semibold">LSP Assessment Application</h5>
    </div>

    <div class="d-flex align-items-center gap-3">
        <!-- Notification Bell -->
        <div class="dropdown">
            <button class="btn btn-light position-relative" type="button" id="notificationDropdown" 
                    data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                <i class="bi bi-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    3
                    <span class="visually-hidden">unread notifications</span>
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown">
                <li class="dropdown-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Notifications</span>
                    <small class="text-muted">3 new</small>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item notification-item" href="#">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="notification-icon bg-primary">
                                    <i class="bi bi-person-plus"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="notification-title mb-1">New User Registration</h6>
                                <p class="notification-text mb-1">John Doe has registered as a new assessor</p>
                                <small class="text-muted">2 minutes ago</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item notification-item" href="#">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="notification-icon bg-success">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="notification-title mb-1">Assessment Completed</h6>
                                <p class="notification-text mb-1">Assessment for TUK-001 has been completed</p>
                                <small class="text-muted">1 hour ago</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item notification-item" href="#">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="notification-icon bg-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="notification-title mb-1">System Maintenance</h6>
                                <p class="notification-text mb-1">Scheduled maintenance at 2:00 AM tomorrow</p>
                                <small class="text-muted">3 hours ago</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-center" href="#">
                        <small>View all notifications</small>
                    </a>
                </li>
            </ul>
        </div>

        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <button class="btn d-flex align-items-center gap-2 dropdown-toggle user-dropdown" type="button"
                id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Super Admin') }}&background=6c757d&color=fff"
                    alt="User Avatar" class="user-avatar rounded-circle">
                <div class="user-info d-none d-sm-block text-start">
                    <div class="user-name">{{ Auth::user()->name ?? 'Super Admin' }}</div>
                    <small class="user-role text-muted">{{ Auth::user()->roles->first()->name ?? 'Administrator' }}</small>
                </div>
            </button>

            <ul class="dropdown-menu dropdown-menu-end user-menu" aria-labelledby="userDropdown">
                <li class="dropdown-header">
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Super Admin') }}&background=6c757d&color=fff"
                            alt="User Avatar" class="user-avatar-large rounded-circle me-2">
                        <div>
                            <div class="fw-semibold">{{ Auth::user()->name ?? 'Super Admin' }}</div>
                            <small class="text-muted">{{ Auth::user()->email ?? 'admin@example.com' }}</small>
                        </div>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('asesi.data-pribadi.index') }}">
                        <i class="bi bi-person me-2"></i> My Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="bi bi-gear me-2"></i>Account Settings
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="bi bi-question-circle me-2"></i> Help & Support
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger" type="submit">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
/* Topbar specific styles */
.user-dropdown {
    background: none;
    border: none;
    color: var(--text-primary);
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    transition: all 0.2s ease-in-out;
}

.user-dropdown:hover {
    background: rgba(108, 117, 125, 0.1);
}

.user-avatar {
    width: 36px;
    height: 36px;
}

.user-avatar-large {
    width: 40px;
    height: 40px;
}

.user-info {
    line-height: 1.2;
}

.user-name {
    font-weight: 500;
    font-size: 0.875rem;
    color: var(--text-primary);
}

.user-role {
    font-size: 0.75rem;
    color: var(--text-muted);
}

.user-menu {
    min-width: 250px;
    border: none;
    box-shadow: var(--shadow);
    border-radius: 0.75rem;
    padding: 0.5rem 0;
}

.user-menu .dropdown-header {
    padding: 1rem;
    background: var(--sidebar-bg);
    border-radius: 0.75rem 0.75rem 0 0;
    margin: -0.5rem -0rem 0 -0rem;
    border: none;
}

.user-menu .dropdown-item {
    padding: 0.75rem 1rem;
    transition: all 0.2s ease-in-out;
}

.user-menu .dropdown-item:hover {
    background: rgba(108, 117, 125, 0.1);
}

.user-menu .dropdown-item.text-danger:hover {
    background: rgba(220, 53, 69, 0.1);
}

.user-menu button.dropdown-item {
    width: 100%;
    text-align: left;
    border: none;
    background: none;
}

/* Notification styles */
.notification-dropdown {
    min-width: 320px;
    max-height: 400px;
    overflow-y: auto;
    border: none;
    box-shadow: var(--shadow);
    border-radius: 0.75rem;
    padding: 0;
}

.notification-item {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--border-color);
    transition: all 0.2s ease-in-out;
}

.notification-item:hover {
    background: rgba(108, 117, 125, 0.05);
}

.notification-item:last-of-type {
    border-bottom: none;
}

.notification-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
}

.notification-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
}

.notification-text {
    font-size: 0.8125rem;
    color: var(--text-secondary);
    line-height: 1.3;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .user-info {
        display: none !important;
    }
    
    .notification-dropdown {
        min-width: 280px;
    }
    
    .user-menu {
        min-width: 200px;
    }
}

/* Loading animation for notification badge */
@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

.badge {
    /* animation: pulse 2s infinite; */
}

/* Custom scrollbar for notification dropdown */
.notification-dropdown::-webkit-scrollbar {
    width: 6px;
}

.notification-dropdown::-webkit-scrollbar-track {
    background: var(--border-color);
    border-radius: 3px;
}

.notification-dropdown::-webkit-scrollbar-thumb {
    background: var(--text-muted);
    border-radius: 3px;
}

.notification-dropdown::-webkit-scrollbar-thumb:hover {
    background: var(--text-secondary);
}
</style>

<script>
// Topbar specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Auto-close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const dropdowns = document.querySelectorAll('.dropdown-menu.show');
        dropdowns.forEach(dropdown => {
            const dropdownButton = dropdown.previousElementSibling;
            if (!dropdown.contains(event.target) && !dropdownButton.contains(event.target)) {
                const bsDropdown = bootstrap.Dropdown.getInstance(dropdownButton);
                if (bsDropdown) {
                    bsDropdown.hide();
                }
            }
        });
    });

    // Mark notifications as read when clicked
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function() {
            this.classList.add('read');
            // You can add AJAX call here to mark notification as read in backend
        });
    });

    // Update notification count
    function updateNotificationCount() {
        // This function can be called via AJAX to update notification count
        const badge = document.querySelector('#notificationDropdown .badge');
        // Update badge count from server response
    }

    // Real-time notifications (if using WebSocket/Pusher)
    // if (window.Echo) {
    //     Echo.private(`user.${userId}`)
    //         .notification((notification) => {
    //             addNotificationToDropdown(notification);
    //             updateNotificationCount();
    //         });
    // }
});

// Function to add new notification to dropdown
function addNotificationToDropdown(notification) {
    const notificationList = document.querySelector('.notification-dropdown');
    const newNotification = document.createElement('li');
    newNotification.innerHTML = `
        <a class="dropdown-item notification-item" href="#">
            <div class="d-flex">
                <div class="flex-shrink-0">
                    <div class="notification-icon bg-primary">
                        <i class="bi bi-bell"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-2">
                    <h6 class="notification-title mb-1">${notification.title}</h6>
                    <p class="notification-text mb-1">${notification.message}</p>
                    <small class="text-muted">Just now</small>
                </div>
            </div>
        </a>
    `;
    
    // Insert after the divider
    const firstDivider = notificationList.querySelector('.dropdown-divider');
    firstDivider.parentNode.insertBefore(newNotification, firstDivider.nextSibling);
}
</script>
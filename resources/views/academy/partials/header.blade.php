{{-- K-Academy page top (was kacademy/includes/header.php). Variables come from App\Support\Academy::pageVars(). --}}
@php
    $page_title = $page_title ?? 'K-Academy';
    $is_admin_area = $is_admin_area ?? false;
    $recent_notifications = $recent_notifications ?? [];
    $unread_count = $unread_count ?? 0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ strip_tags(html_entity_decode((string) $page_title)) }} - K-Academy</title>
    <link rel="icon" type="image/png" href="{{ asset('favi.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ url('academy/assets/css/style.css') }}">
    @foreach ($extra_css ?? [] as $css)
        <link rel="stylesheet" href="{{ $css }}">
    @endforeach
    <style>
        .ka-brand img { height: 34px; width: auto; }
        .ka-brand .ka-divider { width: 1px; height: 26px; background: #e2e8f0; margin: 0 12px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container-fluid px-4">
            <button class="btn btn-outline-secondary d-md-none me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                <i class="bi bi-list"></i>
            </button>

            <a class="navbar-brand ka-brand d-flex align-items-center" href="{{ $is_admin_area ? url('academy/admin/dashboard') : url('academy/dashboard') }}">
                <img src="{{ asset('K-CONNECT.png') }}" alt="K-Connect" onerror="this.style.display='none'">
                <span class="ka-divider"></span>
                <span class="gradient-text fw-bold">K-Academy</span>
            </a>

            @if (!empty($show_search))
            <div class="search-bar d-none d-md-block ms-4">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" placeholder="Search courses..." id="globalSearch">
            </div>
            @endif

            <div class="navbar-nav ms-auto flex-row align-items-center">
                @if (!empty($user_id) && !$is_admin_area)
                    <div class="nav-item dropdown me-3">
                        <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell fs-5"></i>
                            @if ($unread_count > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger ka-unread-badge">{{ $unread_count }}</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown">
                            <li>
                                <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                                    Notifications
                                    @if ($unread_count > 0)
                                        <a href="#" class="text-primary small" id="markAllRead">Mark all as read</a>
                                    @endif
                                </h6>
                            </li>
                            @if (empty($recent_notifications))
                                <li>
                                    <span class="dropdown-item-text text-muted text-center py-3">
                                        <i class="bi bi-bell-slash d-block fs-3 mb-2"></i>
                                        No new notifications
                                    </span>
                                </li>
                            @else
                                @foreach ($recent_notifications as $notification)
                                    <li>
                                        <a class="dropdown-item {{ !$notification['is_read'] ? 'unread' : '' }}" href="{{ ka_link($notification['link']) }}">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    <i class="bi bi-{{ $notification['type'] == 'success' ? 'check-circle-fill text-success' : ($notification['type'] == 'warning' ? 'exclamation-triangle-fill text-warning' : ($notification['type'] == 'danger' ? 'x-circle-fill text-danger' : 'info-circle-fill text-info')) }}"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <small class="fw-bold d-block">{{ $notification['title'] }}</small>
                                                    <small class="text-muted d-block">{{ $notification['message'] }}</small>
                                                    <small class="text-muted">{{ ka_time_ago($notification['created_at']) }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-center small" href="{{ url('academy/notifications') }}">View all notifications</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif

                @if (!empty($user_id) || $is_admin_area)
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="avatar-circle bg-gradient text-white me-2">{{ strtoupper(substr($user_name ?? 'U', 0, 1)) }}</div>
                            <span class="d-none d-lg-inline">{{ $user_name ?? 'User' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if ($is_admin_area)
                                <li><a class="dropdown-item" href="{{ url('admin/dashboard') }}"><i class="bi bi-grid"></i> K-Connect Admin</a></li>
                                @if (isLoggedIn())
                                    <li><a class="dropdown-item" href="{{ url('academy/dashboard') }}"><i class="bi bi-person"></i> Switch to User</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="{{ url('admin/logout') }}"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                            @else
                                <li><a class="dropdown-item" href="{{ url('academy/profile') }}"><i class="bi bi-person"></i> Profile</a></li>
                                <li><a class="dropdown-item" href="{{ url('/') }}"><i class="bi bi-house"></i> Back to K-Connect</a></li>
                                @if (isAdminLoggedIn())
                                    <li><a class="dropdown-item" href="{{ url('academy/admin/dashboard') }}"><i class="bi bi-speedometer2"></i> K-Academy Admin</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="{{ url('logout') }}"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                            @endif
                        </ul>
                    </div>
                @else
                    <a href="{{ url('login') }}" class="btn btn-primary">Login with K-Connect</a>
                @endif
            </div>
        </div>
    </nav>
    @if (!empty($user_id) && !$is_admin_area)
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var link = document.getElementById('markAllRead');
        if (!link) return;
        link.addEventListener('click', function (e) {
            e.preventDefault();
            fetch('{{ url('academy/api/mark-notifications-read') }}', { method: 'POST', headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function () {
                    document.querySelectorAll('.ka-unread-badge').forEach(function (b) { b.remove(); });
                    document.querySelectorAll('.notification-dropdown .dropdown-item.unread').forEach(function (i) { i.classList.remove('unread'); });
                    link.remove();
                });
        });
    });
    </script>
    @endif

    <div class="container-fluid">
        <div class="row">

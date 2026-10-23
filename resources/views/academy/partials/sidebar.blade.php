{{-- K-Academy menu (was the kacademy/includes/sidebar.php side menu). On learner pages it is a bar under the
     K-Connect top menu, so the two menus do not compete; admin pages use the K-Connect admin sidebar instead. --}}
@if (!$is_admin_area)
@php
    $ka_menu = [
        ['dashboard', ['dashboard.php'], 'bi-speedometer2', 'Dashboard'],
        ['my-courses', ['my-courses.php'], 'bi-journal-bookmark', 'My Courses'],
        ['browse-courses', ['browse-courses.php', 'browse-course.php', 'course-overview.php', 'course-player.php', 'course-notes.php', 'course-discussions.php', 'course-resources.php', 'quiz.php'], 'bi-compass', 'Browse Courses'],
        ['certificates', ['certificates.php', 'certificate-pending.php'], 'bi-award', 'Certificates'],
        ['quiz-results', ['quiz-results.php'], 'bi-patch-check', 'Quiz Results'],
        ['progress', ['progress.php'], 'bi-graph-up-arrow', 'My Progress'],
        ['notifications', ['notifications.php'], 'bi-bell', 'Notifications'],
    ];
@endphp
<div class="ka-subnav">
    <div class="ka-subnav-inner">
        <a class="ka-subnav-brand" href="{{ url('academy/dashboard') }}">
            <span class="ka-subnav-logo"><i class="bi bi-mortarboard-fill"></i></span>
            <span>K-Academy</span>
        </a>
        <nav class="ka-subnav-links" aria-label="K-Academy">
            @foreach ($ka_menu as [$page, $pages, $icon, $label])
                <a class="ka-subnav-link {{ in_array($current_page, $pages) ? 'active' : '' }}" href="{{ url('academy/'.$page) }}">
                    <i class="bi {{ $icon }}"></i>
                    <span>{{ $label }}</span>
                    @if ($page === 'certificates' && ($cert_count ?? 0) > 0)
                        <span class="ka-count ka-count-green">{{ $cert_count }}</span>
                    @elseif ($page === 'notifications' && ($unread_count ?? 0) > 0)
                        <span class="ka-count">{{ $unread_count }}</span>
                    @endif
                </a>
            @endforeach
        </nav>
        <div class="ka-subnav-end">
            @if (isAdminLoggedIn())
                <a class="ka-subnav-admin" href="{{ url('academy/admin/dashboard') }}" title="K-Academy admin">
                    <i class="bi bi-gear"></i><span>Manage</span>
                </a>
            @endif
            <a class="ka-subnav-user {{ in_array($current_page, ['profile.php']) ? 'active' : '' }}" href="{{ url('academy/profile') }}" title="My K-Academy profile">
                <span class="ka-avatar">{{ strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) }}</span>
                <span class="ka-subnav-user-name">{{ $user['full_name'] ?? '' }}</span>
            </a>
        </div>
    </div>
</div>
@endif

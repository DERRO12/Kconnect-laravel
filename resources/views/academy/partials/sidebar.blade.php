{{-- K-Academy menu (was the kacademy/includes/sidebar.php side menu). On learner pages it is a bar under the
     K-Connect top menu, so the two menus do not compete; admin pages use the K-Connect admin sidebar instead. --}}
@if (!$is_admin_area)
@php
    // page, its pages, icon, label, short label (narrow screens)
    $ka_menu = [
        ['dashboard', ['dashboard.php'], 'bi-speedometer2', 'Dashboard', 'Dashboard'],
        ['my-courses', ['my-courses.php'], 'bi-journal-bookmark', 'My Courses', 'Courses'],
        ['browse-courses', ['browse-courses.php', 'browse-course.php', 'course-overview.php', 'course-player.php', 'course-notes.php', 'course-discussions.php', 'course-resources.php', 'quiz.php'], 'bi-compass', 'Browse Courses', 'Browse'],
        ['certificates', ['certificates.php', 'certificate-pending.php'], 'bi-award', 'Certificates', 'Certificates'],
        ['quiz-results', ['quiz-results.php'], 'bi-patch-check', 'Quiz Results', 'Results'],
        ['progress', ['progress.php'], 'bi-graph-up-arrow', 'My Progress', 'Progress'],
        ['notifications', ['notifications.php'], 'bi-bell', 'Notifications', 'Alerts'],
    ];
@endphp
<div class="ka-subnav{{ isAdminLoggedIn() ? ' ka-has-admin' : '' }}">
    <div class="ka-subnav-inner">
        <a class="ka-subnav-brand" href="{{ url('academy/dashboard') }}">
            <span class="ka-subnav-logo"><i class="bi bi-mortarboard-fill"></i></span>
            <span>K-Academy</span>
        </a>
        <nav class="ka-subnav-links" aria-label="K-Academy">
            @foreach ($ka_menu as [$page, $pages, $icon, $label, $short])
                <a class="ka-subnav-link {{ in_array($current_page, $pages) ? 'active' : '' }}" href="{{ url('academy/'.$page) }}" title="{{ $label }}">
                    <span class="ka-subnav-icon"><i class="bi {{ $icon }}"></i></span>
                    <span class="ka-label">{{ $label }}</span>
                    <span class="ka-label-short">{{ $short }}</span>
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
<script>
// Keep the K-Academy bar right under the K-Connect menu whatever that menu's height, and the
// current page's link in view when the links scroll sideways on narrow screens
(function () {
    function fit() {
        var nav = document.querySelector('.kc-topnav .navbar');
        // Measured with the phone menu closed, so opening it does not push the page down
        if (nav && !nav.querySelector('.navbar-collapse.show, .navbar-collapse.collapsing')) {
            document.documentElement.style.setProperty('--kc-nav-h', nav.offsetHeight + 'px');
        }
        var links = document.querySelector('.ka-subnav-links');
        if (links) links.classList.toggle('ka-scrolls', links.scrollWidth > links.clientWidth + 4);
    }
    fit();
    window.addEventListener('resize', fit);
    window.addEventListener('load', function () {
        fit();
        var a = document.querySelector('.ka-subnav-link.active');
        var box = a && a.parentNode;
        if (box && a.getBoundingClientRect().right > box.getBoundingClientRect().right) {
            box.scrollLeft += a.getBoundingClientRect().right - box.getBoundingClientRect().right + 32;
        }
    });
})();
</script>
@endif

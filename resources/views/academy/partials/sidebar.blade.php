{{-- K-Academy menu (was the kacademy/includes/sidebar.php side menu and header). On learner pages it is the
     page's top bar, with K-Connect one click away; admin pages use the K-Connect admin sidebar instead. --}}
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
<header class="ka-topbar{{ isAdminLoggedIn() ? ' ka-has-admin' : '' }}">
    <div class="ka-topbar-inner">
        <a class="ka-logo" href="{{ url('academy/index') }}" title="K-Academy home">
            <span class="ka-logo-mark"><i class="bi bi-mortarboard-fill"></i></span>
            <span class="ka-logo-text">K-Academy</span>
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
            <a class="ka-home-btn" href="{{ url('/') }}" title="Back to K-Connect">
                <i class="bi bi-house-door"></i><span>K-Connect</span>
            </a>
            <div class="dropdown">
                <button class="ka-subnav-user {{ in_array($current_page, ['profile.php']) ? 'active' : '' }}" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="ka-avatar">{{ strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) }}</span>
                    <span class="ka-subnav-user-name">{{ $user['full_name'] ?? '' }}</span>
                    <i class="bi bi-chevron-down ka-caret"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end ka-user-menu">
                    <li class="ka-user-head">
                        <strong>{{ $user['full_name'] ?? '' }}</strong>
                        <small>{{ $user['employee_code'] ?? ($user['email'] ?? '') }}</small>
                    </li>
                    <li><a class="dropdown-item" href="{{ url('academy/profile') }}"><i class="bi bi-person"></i> My K-Academy profile</a></li>
                    <li><a class="dropdown-item" href="{{ url('academy/index') }}"><i class="bi bi-mortarboard"></i> K-Academy home</a></li>
                    @if (isAdminLoggedIn())
                        <li><a class="dropdown-item" href="{{ url('academy/admin/dashboard') }}"><i class="bi bi-gear"></i> Manage K-Academy</a></li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ url('/') }}"><i class="bi bi-house-door"></i> Back to K-Connect</a></li>
                    <li><a class="dropdown-item text-danger" href="{{ url(isLoggedIn() ? 'logout' : 'admin/logout') }}"><i class="bi bi-box-arrow-right"></i> Log out</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>
<script>
// Leave room for the K-Academy bar whatever its height (it is two rows on tablets), and keep the
// current page's link in view when the links scroll sideways on narrow screens
(function () {
    function fit() {
        var bar = document.querySelector('.ka-topbar');
        if (bar) document.documentElement.style.setProperty('--ka-bar-h', bar.offsetHeight + 'px');
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

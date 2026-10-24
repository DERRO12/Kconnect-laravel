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
    <link rel="apple-touch-icon" href="{{ asset('favi.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('academy/assets/css/style.css') }}?v={{ @filemtime(public_path('academy-files/assets/css/style.css')) }}">
    <link rel="stylesheet" href="{{ url('academy/assets/css/kconnect-theme.css') }}?v={{ @filemtime(public_path('academy-files/assets/css/kconnect-theme.css')) }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // Pages that do not load Bootstrap's JS themselves still need it for the menus, dropdowns and pop-ups
    window.addEventListener('load', function () {
        if (window.bootstrap) return;
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js';
        document.head.appendChild(s);
    });
    </script>
    @foreach ($extra_css ?? [] as $css)
        <link rel="stylesheet" href="{{ $css }}">
    @endforeach
    <style>
        .ka-brand img { height: 34px; width: auto; }
        .ka-brand .ka-divider { width: 1px; height: 26px; background: #e2e8f0; margin: 0 12px; }
    </style>
</head>
<body class="ka-page {{ $is_admin_area ? 'ka-admin' : 'ka-learner' }}">
@if ($is_admin_area)
    @php
        $ka_sections = [
            'dashboard' => 'Dashboard', 'manage_courses' => 'Courses', 'add_course' => 'Add Course', 'edit_course' => 'Edit Course',
            'manage_lessons' => 'Lessons', 'manage_quizzes' => 'Quizzes', 'manage_quiz' => 'Quiz', 'add_quiz' => 'Add Quiz',
            'edit_quiz' => 'Edit Quiz', 'preview_quiz' => 'Quiz Preview', 'manage_quiz_questions' => 'Quiz Questions',
            'quiz_results' => 'Quiz Results', 'pending-certificates' => 'Certificates', 'approve-certificate' => 'Review Certificate',
            'hero-video' => 'Home Page Video', 'reports' => 'Reports', 'activity-logs' => 'Activity Logs', 'settings' => 'Settings',
            'manage_users' => 'Learners', 'import_questions' => 'Import Questions',
        ];
        $ka_page = preg_replace('/\.php$/', '', (string) ($current_page ?? ''));
        $ka_section = $ka_sections[$ka_page] ?? (($page_title ?? 'K-Academy') !== 'K-Academy' ? strip_tags(html_entity_decode((string) $page_title)) : 'Admin');
    @endphp
    {{-- Admin pages: the K-Connect admin sidebar (with its K-Academy section) --}}
    {!! \App\Support\Academy::kconnectAdminSidebar() !!}
    <div class="main-content">
        <div class="ka-admin-top">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary ka-mobile-menu" type="button" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <span class="ka-crumb"><i class="fas fa-graduation-cap"></i>K-Academy <span class="text-secondary fw-normal">/ {{ $ka_section }}</span></span>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-sm btn-outline-primary" href="{{ url('academy/dashboard') }}"><i class="fas fa-eye me-1"></i> Learner view</a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ url('admin/dashboard') }}"><i class="fas fa-th-large me-1"></i> K-Connect Admin</a>
            </div>
        </div>
@else
    {{-- Learner pages: the K-Connect top menu --}}
    <div class="kc-topnav">
        @include('partials.navbar')
    </div>
@endif

    <div class="container-fluid">
        <div class="row">

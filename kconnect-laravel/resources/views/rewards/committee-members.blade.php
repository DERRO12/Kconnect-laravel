<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="theme-color" content="#009ada">
    <title>Nomination Committee | K-Connect</title>
    <link rel="icon" type="image/png" href="../favi.png">
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --kiboko-dark: #0f172a;
            --grad: linear-gradient(135deg, var(--kiboko-blue) 0%, var(--kiboko-green) 100%);
            --grad-light: linear-gradient(135deg, rgba(0, 154, 218, 0.08) 0%, rgba(118, 188, 33, 0.08) 100%);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.06);
            --shadow-xl: 0 25px 50px rgba(0, 0, 0, 0.12);
            --radius-lg: 24px;
            --radius-xl: 32px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(145deg, #f8fafc 0%, #eef2f6 100%);
            color: var(--kiboko-dark);
            min-height: 100vh;
            overflow-x: hidden;
            padding-top: 80px;
        }

        /* Background Animation */
        .bg-animation {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            animation: float 20s infinite alternate ease-in-out;
        }

        .orb-1 { background: var(--kiboko-blue); width: 500px; height: 500px; top: -150px; left: -150px; }
        .orb-2 { background: var(--kiboko-green); width: 450px; height: 450px; bottom: -150px; right: -150px; animation-delay: 5s; }
        .orb-3 { background: #38bdf8; width: 350px; height: 350px; top: 40%; right: 20%; animation-delay: 10s; opacity: 0.2; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -20px) scale(1.05); }
            66% { transform: translate(-20px, 30px) scale(0.98); }
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 0.8rem 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand img { height: 25px; width: 160px; }

        .nav-link {
            font-weight: 600;
            color: #475569 !important;
            padding: 8px 18px !important;
            border-radius: 40px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--grad-light);
            color: var(--kiboko-blue) !important;
        }

        .dropdown-toggle::after { display: none !important; }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 60px 0 40px;
            border-bottom: 1px solid #eef2f6;
            margin-bottom: 40px;
            text-align: center;
        }

        .page-header h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 15px;
        }

        .text-gradient {
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .page-description {
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
            font-size: 1.1rem;
        }

        /* Quarter Badge */
        .quarter-badge {
            display: inline-block;
            padding: 8px 20px;
            background: var(--grad);
            color: white;
            border-radius: 40px;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* Section Styles */
        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .section-title p {
            color: #64748b;
            font-size: 1.1rem;
        }

        /* Main Committee Section */
        .main-committee-section {
            margin-bottom: 60px;
        }

        .main-committee-title h2 {
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Supporting Section */
        .supporting-section {
            margin-top: 40px;
        }

        .supporting-header {
            text-align: center;
            margin: 50px 0 40px;
        }

        .supporting-header h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--kiboko-dark);
            position: relative;
            display: inline-block;
        }

        .supporting-header h3:before,
        .supporting-header h3:after {
            content: '';
            position: absolute;
            top: 50%;
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--kiboko-blue), transparent);
        }

        .supporting-header h3:before {
            right: 100%;
            margin-right: 20px;
        }

        .supporting-header h3:after {
            left: 100%;
            margin-left: 20px;
        }

        /* Level Section */
        .level-section {
            margin-bottom: 60px;
        }

        .level-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 40px;
        }

        .level-badge {
            width: 60px;
            height: 60px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
            color: white;
        }

        .level-badge.level-1 {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
        }

        .level-badge.level-2 {
            background: linear-gradient(135deg, #009ada, #06b6d4);
        }

        .level-badge.level-3 {
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
        }

        .level-header h4 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }

        /* Committee Grid - Larger Cards */
        .committee-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 35px;
            justify-content: center;
        }

        /* Member Card - Clean & Simple */
        .member-card {
            text-align: center;
            transition: all 0.3s ease;
        }

        .member-card:hover {
            transform: translateY(-5px);
        }

        .member-image {
            width: 100%;
            aspect-ratio: 1 / 1.2;
            border-radius: 30px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: var(--shadow-xl);
            background: linear-gradient(145deg, #f1f5f9, #e2e8f0);
        }

        .member-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .member-card:hover .member-image img {
            transform: scale(1.05);
        }

        .member-image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--grad);
            color: white;
            font-size: 80px;
            font-weight: 700;
        }

        .member-info {
            padding: 0 10px;
        }

        .member-name {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--kiboko-dark);
            line-height: 1.3;
        }

        .member-position {
            font-size: 1rem;
            color: #64748b;
            font-weight: 500;
            line-height: 1.4;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid #eef2f6;
            grid-column: 1 / -1;
        }

        .empty-state i {
            font-size: 60px;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .empty-state h5 {
            font-weight: 700;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #64748b;
        }

        /* CTA Section */
        .cta-section {
            text-align: center;
            margin-top: 60px;
            padding: 40px 0;
        }

        .btn-nominate {
            background: var(--grad);
            color: white;
            padding: 16px 48px;
            border-radius: 60px;
            font-weight: 700;
            font-size: 1.1rem;
            border: none;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }

        .btn-nominate:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-xl);
            color: white;
        }

        /* Responsive */
        @media (max-width: 992px) {
            body { padding-top: 70px; }
            .committee-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .page-header h1 { font-size: 2rem; }
            .section-title h2 { font-size: 2rem; }
            .supporting-header h3 { font-size: 1.5rem; }
            .supporting-header h3:before,
            .supporting-header h3:after { display: none; }
            .level-header h4 { font-size: 1.4rem; }
            .member-name { font-size: 1.2rem; }
        }

        @media (max-width: 576px) {
            .committee-grid { grid-template-columns: 1fr; }
            .page-header { padding: 40px 0 30px; }
            .page-header h1 { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
    <!-- Background Animation -->
    <div class="bg-animation">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <img src="../K-CONNECT.png" alt="K-CONNECT">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @php getMenu(); @endphp
                </ul>
                @if (isLoggedIn())
                <div class="dropdown ms-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        @if (!empty(session('employee_photo')))
                            <img src="../uploads/employees/{{ session('employee_photo') }}" 
                                 width="36" height="36" class="rounded-circle me-2">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" 
                                 style="width: 36px; height: 36px;">
                                <i class="fas fa-user text-gradient"></i>
                            </div>
                        @endif
                        <span class="d-none d-lg-inline">{{ session('employee_name') }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">{{ session('employee_code') }}</h6></li>
                        <li><a class="dropdown-item" href="nomination.php"><i class="fas fa-award me-2"></i>Submit Nomination</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header" data-aos="fade-down">
        <div class="container">
            <span class="quarter-badge">
                <i class="fas fa-calendar-alt me-2"></i>
                {!! ($quarter_names[$current_quarter] ?? null) . ' ' . $current_year !!}
            </span>
            <h1 data-aos="fade-down">
                <span class="text-gradient">Nomination</span> Committee
            </h1>
            <p class="page-description" data-aos="fade-down" data-aos-delay="100">
                Meet the team responsible for reviewing and selecting our quarterly award winners
            </p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container mb-5">
        <!-- MAIN NOMINATION COMMITTEE (Level 3) -->
        <div class="main-committee-section" data-aos="fade-up">
            <div class="section-title main-committee-title">
                <h2>Main Nomination Committee</h2>
                <p>Final decision makers for all award categories</p>
            </div>
            
            @if (count($level3_members) > 0)
                <div class="committee-grid">
                    @foreach ($level3_members as $member)
                        @include('rewards.partials.committee-member-card', ['member' => $member])
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <h5>No Main Committee Members</h5>
                    <p>Main committee members will be announced soon.</p>
                </div>
            @endif
        </div>

        <!-- Supporting Committees -->
        <div class="supporting-section">
           

            <!-- Level 2 Committee -->
            <div class="level-section" data-aos="fade-up" data-aos-delay="100">
                <div class="level-header">
                    <div class="level-badge level-2">L2</div>
                    <h4>Secondary Review Committee</h4>
                </div>
                
                @if (count($level2_members) > 0)
                    <div class="committee-grid">
                        @foreach ($level2_members as $member)
                            @include('rewards.partials.committee-member-card', ['member' => $member])
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-users-slash"></i>
                        <h5>No Level 2 Committee Members</h5>
                        <p>Committee members will be announced soon.</p>
                    </div>
                @endif
            </div>

            <!-- Level 1 Committee -->
            <div class="level-section" data-aos="fade-up" data-aos-delay="200">
                <div class="level-header">
                    <div class="level-badge level-1">L1</div>
                    <h4>Initial Review Committee</h4>
                </div>
                
                @if (count($level1_members) > 0)
                    <div class="committee-grid">
                        @foreach ($level1_members as $member)
                            @include('rewards.partials.committee-member-card', ['member' => $member])
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-users-slash"></i>
                        <h5>No Level 1 Committee Members</h5>
                        <p>Committee members will be announced soon.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- CTA Section -->
        <div class="cta-section" data-aos="fade-up">
            <a href="nomination.php" class="btn-nominate">
                <i class="fas fa-paper-plane"></i>
                Submit a Nomination
            </a>
        </div>
    </div>
 @include('partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../js/main.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });

        // Initialize dropdowns
        document.addEventListener('DOMContentLoaded', function() {
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            dropdownElementList.map(function(dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
        });
    </script>
</body>
</html>
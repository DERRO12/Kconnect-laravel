<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="theme-color" content="#009ada">
    <title>Rewards & Recognition - K-Connect</title>
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
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-sm);
            padding: 1rem 0;
        }

      .navbar-brand img {
    height: 25px;
    width: 150px;
    margin-right: 10px;
}
        .navbar-brand img:hover {
            transform: scale(1.05);
        }

        .nav-link {
            font-weight: 500;
            color: var(--text-primary) !important;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--kiboko-blue) !important;
            transform: translateY(-1px);
        }
        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 3rem 0 2rem;
            border-bottom: 1px solid #eef2f6;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 1rem 0 0.5rem;
            text-align: center;
            letter-spacing: -0.02em;
        }

        .text-gradient {
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-description {
            text-align: center;
            max-width: 600px;
            margin: 0.5rem auto 0;
            color: #64748b;
        }

        /* Form Card */
        .form-card {
            background: white;
            border-radius: 20px;
            padding: clamp(20px, 4vw, 36px);
            box-shadow: var(--shadow-md);
            border: 1px solid #eef2f6;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: clamp(18px, 4vw, 24px);
            font-weight: 700;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i { 
            color: var(--kiboko-blue); 
            font-size: 24px; 
            background: var(--grad-light); 
            padding: 10px; 
            border-radius: 16px; 
        }

        /* Category Grid */
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .category-card {
            background: white;
            border: 2px solid rgba(0, 154, 218, 0.1);
            border-radius: 20px;
            padding: 28px 20px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            position: relative;
        }

        .category-card:hover { 
            transform: translateY(-5px); 
            box-shadow: var(--shadow-xl); 
            border-color: var(--kiboko-blue); 
        }

        .category-card.selected { 
            border-color: var(--kiboko-green); 
            background: var(--grad-light); 
        }

        .category-card.voted {
            opacity: 0.6 !important;
            pointer-events: none !important;
            border-color: #cbd5e1 !important;
            background: #f8fafc !important;
            cursor: not-allowed !important;
        }

        .category-card.voted:hover {
            transform: none !important;
            box-shadow: none !important;
        }

        .category-icon {
            width: 70px;
            height: 70px;
            background: var(--grad-light);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            color: var(--kiboko-blue);
            font-size: 32px;
            transition: all 0.3s;
        }

        .category-card.selected .category-icon { 
            background: var(--grad); 
            color: white; 
        }

        .category-card.voted .category-icon {
            background: #e2e8f0 !important;
            color: #94a3b8 !important;
        }

        .category-card h3 { font-size: 18px; font-weight: 700; margin-bottom: 8px; }

        .category-card p { color: #64748b; font-size: 13px; line-height: 1.5; }

        .selected-badge { 
            position: absolute; 
            top: 12px; 
            right: 12px; 
            color: var(--kiboko-green); 
            font-size: 24px; 
            display: none; 
        }

        .category-card.selected .selected-badge { display: block; }

        .voted-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: #10b981;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        /* Form Grid */
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 20px; }
        .form-group.full-width { grid-column: 1 / -1; }

        label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; }
        label i { color: var(--kiboko-blue); margin-right: 6px; }

        input, textarea, select {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid rgba(0, 154, 218, 0.12);
            border-radius: 16px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
            background: white;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 4px rgba(0, 154, 218, 0.1);
        }

        input[readonly] { background: #f8fafc; border-color: #e2e8f0; }

        /* Autocomplete */
        .autocomplete-wrapper { position: relative; }
        .autocomplete-results {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: white;
            border: 1px solid rgba(0, 154, 218, 0.15);
            border-radius: 16px;
            max-height: 280px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: var(--shadow-xl);
            display: none;
        }

        .autocomplete-item {
            padding: 14px 18px;
            cursor: pointer;
            transition: all 0.15s;
            border-bottom: 1px solid #f1f5f9;
        }

        .autocomplete-item:hover { background: var(--grad-light); }
        .employee-name { font-weight: 700; color: var(--kiboko-dark); }
        .employee-code { font-size: 12px; color: var(--kiboko-blue); margin-left: 8px; }
        .employee-dept { font-size: 12px; color: #64748b; margin-top: 4px; }

        /* Questions */
        .questions-container { 
            background: #f8fafc; 
            border-radius: 16px; 
            padding: 28px; 
        }

        .question-item { 
            margin-bottom: 28px; 
            padding-bottom: 28px; 
            border-bottom: 1px solid #eef2f6; 
        }

        .question-item:last-child { 
            border-bottom: none; 
            margin-bottom: 0; 
            padding-bottom: 0; 
        }

        .question-text { 
            font-weight: 700; 
            margin-bottom: 16px; 
            font-size: 16px;
            line-height: 1.6;
            white-space: pre-line;
            word-wrap: break-word;
        }

        .radio-group, .checkbox-group { display: flex; flex-direction: column; gap: 12px; }

        .radio-option, .checkbox-option { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            cursor: pointer; 
        }

        .radio-option input, .checkbox-option input { 
            width: 20px; 
            height: 20px; 
            accent-color: var(--kiboko-blue); 
        }

        .rating-stars { display: flex; gap: 8px; }
        .rating-star {
            font-size: 32px;
            cursor: pointer;
            color: #cbd5e1;
            transition: all 0.2s;
        }
        .rating-star.active, .rating-star:hover { color: #fbbf24; }

        /* Buttons */
        .action-buttons { 
            display: flex; 
            gap: 16px; 
            justify-content: flex-end; 
            margin-top: 35px; 
            padding-top: 25px; 
            border-top: 1px solid rgba(0, 154, 218, 0.1); 
        }

        .btn {
            padding: 14px 35px;
            border: none;
            border-radius: 60px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: var(--grad);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover { transform: translateY(-3px); box-shadow: var(--shadow-xl); }
        .btn-secondary { background: white; color: var(--kiboko-dark); border: 2px solid #e2e8f0; }
        .btn-secondary:hover { background: #f8fafc; border-color: var(--kiboko-blue); }

        /* Success */
        .success-section { text-align: center; padding: 60px 40px; }
        .success-icon {
            width: 100px;
            height: 100px;
            background: var(--grad);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 50px;
            animation: successPop 0.5s ease;
        }

        @keyframes successPop {
            0% { transform: scale(0); opacity: 0; }
            80% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Leaderboard */
        .leaderboard-section {
            background: white;
            border-radius: 20px;
            padding: 32px;
            box-shadow: var(--shadow-md);
            border: 1px solid #eef2f6;
            margin-bottom: 40px;
        }

        .leaderboard-header { 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            margin-bottom: 24px; 
            flex-wrap: wrap; 
            gap: 16px; 
        }

        .leaderboard-title { 
            font-size: 24px; 
            font-weight: 700; 
            display: flex; 
            align-items: center; 
            gap: 12px; 
        }

        .leaderboard-title i { color: var(--kiboko-blue); }
        .category-tabs { display: flex; gap: 8px; flex-wrap: wrap; }

        .tab-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 40px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            background: #f1f5f9;
            color: #64748b;
        }

        .tab-btn.active { background: var(--grad); color: white; box-shadow: var(--shadow-md); }

        .leaderboard-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; }

        .leader-card {
            background: white;
            border-radius: 16px;
            padding: 24px 16px;
            text-align: center;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            border: 1px solid #eef2f6;
        }

        .leader-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); }
        .leader-card::before { 
            content: ''; 
            position: absolute; 
            top: 0; 
            left: 0; 
            right: 0; 
            height: 4px; 
            background: var(--grad); 
        }

        .rank-badge {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--grad-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            font-weight: 800;
            font-size: 18px;
            color: var(--kiboko-blue);
        }

        .leader-card:nth-child(1) .rank-badge { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; }
        .leader-card:nth-child(2) .rank-badge { background: linear-gradient(135deg, #94a3b8, #64748b); color: white; }
        .leader-card:nth-child(3) .rank-badge { background: linear-gradient(135deg, #d97706, #b45309); color: white; }

        .leader-name { font-weight: 800; margin-bottom: 4px; }
        .leader-dept { color: #64748b; font-size: 13px; margin-bottom: 12px; }
        .leader-votes { color: var(--kiboko-green); font-weight: 800; font-size: 22px; }

        /* History Card */
        .history-card {
            background: white;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            border: 1px solid #eef2f6;
            transition: all 0.3s;
        }

        .history-card:hover { transform: translateX(5px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

        .quarter-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .quarter-Q1 { background: #dbeafe; color: #1e40af; }
        .quarter-Q2 { background: #dcfce7; color: #166534; }
        .quarter-Q3 { background: #fef3c7; color: #92400e; }
        .quarter-Q4 { background: #fce7f3; color: #9d174d; }

        .hidden { display: none !important; }
        .loading-spinner { 
            display: inline-block; 
            width: 20px; 
            height: 20px; 
            border: 2px solid rgba(255,255,255,0.3); 
            border-radius: 50%; 
            border-top-color: white; 
            animation: spin 0.8s linear infinite; 
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Back to Top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--grad);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: none;
            box-shadow: 0 5px 20px rgba(0,154,218,0.3);
            z-index: 99;
        }

        .back-to-top:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,154,218,0.4); }

        /* Mobile Navigation */
        .navbar-toggler {
            border: none;
            background: var(--grad-light);
            padding: 10px 14px;
            border-radius: 12px;
        }

        .navbar-toggler:focus { box-shadow: none; outline: none; }

        /* Responsive */
        @media (max-width: 1200px) {
            .leaderboard-grid { grid-template-columns: repeat(4, 1fr); }
        }

        @media (max-width: 992px) {
            body { padding-top: 70px; }
            .navbar-nav { gap: 0; margin-top: 10px; }
            .nav-link { padding: 10px 16px !important; font-size: 0.9rem; }
            .dropdown-menu { 
                position: static !important; 
                transform: none !important;
                box-shadow: none;
                background: transparent;
                backdrop-filter: none;
                padding-left: 20px;
                margin-top: 0;
                opacity: 1;
                visibility: visible;
                pointer-events: auto;
            }
            .dropdown-submenu > .dropdown-menu {
                margin-left: 16px !important;
            }
        }

        @media (max-width: 1024px) { 
            .leaderboard-grid { grid-template-columns: repeat(3, 1fr); } 
        }

        @media (max-width: 768px) {
            .page-header { padding: 2rem 0 1.5rem; }
            .page-header h1 { font-size: 2rem; }
            .form-grid { grid-template-columns: 1fr; }
            .category-grid { grid-template-columns: 1fr; }
            .leaderboard-grid { grid-template-columns: repeat(2, 1fr); }
            .action-buttons { flex-direction: column; }
            .btn { width: 100%; justify-content: center; }
        }

        @media (max-width: 480px) { 
            .leaderboard-grid { grid-template-columns: 1fr; }
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
            <a class="navbar-brand" href="../">
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
                    <a class="nav-link  d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        @if (!empty(session('employee_photo')))
                            <img src="../uploads/employees/{{ session('employee_photo') }}" 
                                 width="36" height="36" class="rounded-circle me-2" style="object-fit: cover;">
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
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="showHistoryModal()"><i class="fas fa-history me-2"></i>My Nominations</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </nav>
    <!-- Page Header -->
    <section class="page-header" data-aos="fade-down">
        <div class="container">
            <h1 data-aos="fade-down">
                <span class="text-gradient">Rewards &</span> Recognition
            </h1>
            <p class="page-description" data-aos="fade-down" data-aos-delay="100">
                Nominate colleagues who exemplify excellence in our organization
            </p>
            <!-- Current Quarter Banner -->
<div class="container mt-3" data-aos="fade-up">
    <div class="alert alert-info mb-0" style="background: linear-gradient(135deg, #e6f7ff, #f0f9ff); border: none; border-left: 4px solid var(--kiboko-blue); border-radius: 16px;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 48px; height: 48px; background: var(--grad); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-calendar-alt text-white fa-lg"></i>
                </div>
                <div>
                    <strong class="fs-5">Current Nomination Period:</strong>
                    <span class="ms-2 fs-5 fw-bold" style="color: var(--kiboko-blue);">
                        {!! $current_quarter_info['display'] !!}
                    </span>
                    <div class="small text-muted mt-1">
                        <i class="fas fa-info-circle me-1"></i>
                        Nominations are open for the current quarter. Submit your nominations before the quarter ends.
                    </div>
                </div>
            </div>
            <div>
                <span class="badge" style="background: var(--grad); padding: 8px 16px; font-size: 14px;">
                    <i class="fas fa-trophy me-1"></i> Q{!! $current_quarter_info['quarter_num'] !!} {!! $current_quarter_info['year'] !!}
                </span>
            </div>
        </div>
    </div>
</div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container mb-5">
        
        <!-- Form Container -->
        <div id="formContainer">
            <div class="form-card" data-aos="fade-up">
                
                <form id="nominationForm">
                    <!-- Section 1: Category -->
                    <div class="form-section" id="sectionCategory">
                        <div class="section-title"><i class="fas fa-award"></i> Select Award Category</div>
                        <div id="categoriesContainer" class="category-grid"></div>
                    </div>

                    <!-- Section 2: Nominee -->
                    <div class="form-section hidden" id="sectionNominee">
                        <div class="section-title"><i class="fas fa-user-star"></i> Nominate a Colleague</div>
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label><i class="fas fa-id-card"></i> Search by Employee Code or Name <span class="text-danger">*</span></label>
                                <div class="autocomplete-wrapper">
                                    <input type="text" id="nomineeSearch" placeholder="Type to search..." autocomplete="off">
                                    <div id="nomineeResults" class="autocomplete-results"></div>
                                </div>
                            </div>
                            <div class="form-group"><label><i class="fas fa-user"></i> Full Name</label><input type="text" id="nomineeName" readonly></div>
                            <div class="form-group"><label><i class="fas fa-envelope"></i> Email</label><input type="email" id="nomineeEmail" readonly></div>
                            <div class="form-group"><label><i class="fas fa-building"></i> Department</label><input type="text" id="nomineeDepartment" readonly></div>
                            <div class="form-group full-width"><label><i class="fas fa-briefcase"></i> Position</label><input type="text" id="nomineePosition" readonly></div>
                        </div>
                    </div>

                    <!-- Section 3: Questions -->
                    <div class="form-section hidden" id="sectionQuestions">
                        <div class="section-title"><i class="fas fa-clipboard-list"></i> Nomination Details</div>
                        <div id="questionsContainer" class="questions-container"></div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="button" class="btn btn-secondary" id="prevBtn"><i class="fas fa-arrow-left"></i> Previous</button>
                        <button type="button" class="btn btn-primary" id="nextBtn">Next <i class="fas fa-arrow-right"></i></button>
                        <button type="submit" class="btn btn-primary hidden" id="submitBtn"><i class="fas fa-paper-plane"></i> Submit Nomination</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Success Container -->
        <div id="successContainer" class="hidden">
            <div class="form-card" data-aos="zoom-in">
                <div class="success-section">
                    <div class="success-icon"><i class="fas fa-check"></i></div>
                    <h2>Nomination Submitted!</h2>
                    <p>Thank you for recognizing excellence in our organization.<br>Your nomination has been recorded.</p>
                    <button class="btn btn-primary" onclick="location.reload()"><i class="fas fa-plus-circle"></i> Submit Another</button>
                </div>
            </div>
        </div>

        <!-- Leaderboard -->
        <div class="leaderboard-section" data-aos="fade-up">
            <div class="leaderboard-header">
                <div class="leaderboard-title"><i class="fas fa-chart-simple"></i> Live Leaderboard</div>
                <div class="category-tabs" id="categoryTabs"></div>
            </div>
            <div id="leaderboardContent"></div>
        </div>
    </div>

    <!-- Nomination History Modal -->
    <div class="modal fade" id="nominationHistoryModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">
                        <i class="fas fa-history me-2 text-gradient"></i>My Nomination History
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Filter by Year/Quarter</label>
                        <div class="row g-2">
                            <div class="col-md-5">
                                <select id="historyYear" class="form-select">
                                    <option value="0">All Years</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <select id="historyQuarter" class="form-select">
                                    <option value="0">All Quarters</option>
                                    <option value="1">Q1 (Jan-Mar)</option>
                                    <option value="2">Q2 (Apr-Jun)</option>
                                    <option value="3">Q3 (Jul-Sep)</option>
                                    <option value="4">Q4 (Oct-Dec)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100" onclick="loadNominationHistory()" style="background: var(--grad); border: none;">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="historyContent" class="mt-3">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-3 text-muted">Loading your nomination history...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
  <!--@include('partials.footer')-->
    <!-- Back to Top Button -->
    <button class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Scripts -->
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

        // Back to top button
        window.addEventListener('scroll', function() {
            const backToTop = document.querySelector('.back-to-top');
            if (backToTop) {
                backToTop.style.display = window.pageYOffset > 300 ? 'flex' : 'none';
            }
        });

        // =============================================
        // IMPROVED DROPDOWN MENU HANDLING
        // =============================================
        (function() {
            // Wait for DOM to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initDropdowns);
            } else {
                initDropdowns();
            }

            function initDropdowns() {
                const dropdowns = document.querySelectorAll('.dropdown');
                const dropdownToggles = document.querySelectorAll('[data-bs-toggle="dropdown"]');
                
                // Initialize Bootstrap dropdowns
                dropdownToggles.forEach(toggle => {
                    if (!toggle.hasAttribute('data-bs-auto-close')) {
                        toggle.setAttribute('data-bs-auto-close', 'outside');
                    }
                });

                // Custom hover behavior with delay
                dropdowns.forEach(dropdown => {
                    const toggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
                    const menu = dropdown.querySelector('.dropdown-menu');
                    
                    if (!toggle || !menu) return;
                    
                    let hoverTimer = null;
                    let bsDropdown = bootstrap.Dropdown.getInstance(toggle);
                    
                    if (!bsDropdown) {
                        bsDropdown = new bootstrap.Dropdown(toggle);
                    }

                    // Hover to open
                    dropdown.addEventListener('mouseenter', function(e) {
                        clearTimeout(hoverTimer);
                        hoverTimer = setTimeout(() => {
                            if (!menu.classList.contains('show')) {
                                bsDropdown.show();
                            }
                        }, 150); // 150ms delay before opening
                    });

                    // Hover to close
                    dropdown.addEventListener('mouseleave', function(e) {
                        clearTimeout(hoverTimer);
                        hoverTimer = setTimeout(() => {
                            // Check if we're moving to a submenu
                            const relatedTarget = e.relatedTarget;
                            if (relatedTarget && relatedTarget.closest('.dropdown-menu')) {
                                return;
                            }
                            if (menu.classList.contains('show')) {
                                bsDropdown.hide();
                            }
                        }, 300); // 300ms delay before closing - gives time to move to submenu
                    });

                    // Keep dropdown open when hovering over menu
                    menu.addEventListener('mouseenter', function() {
                        clearTimeout(hoverTimer);
                    });

                    menu.addEventListener('mouseleave', function(e) {
                        clearTimeout(hoverTimer);
                        hoverTimer = setTimeout(() => {
                            const relatedTarget = e.relatedTarget;
                            if (relatedTarget && (relatedTarget.closest('.dropdown') === dropdown || relatedTarget.closest('.dropdown-toggle') === toggle)) {
                                return;
                            }
                            if (menu.classList.contains('show')) {
                                bsDropdown.hide();
                            }
                        }, 200);
                    });

                    // Handle submenu navigation
                    const submenuToggles = dropdown.querySelectorAll('.dropdown-submenu > .dropdown-toggle');
                    submenuToggles.forEach(subToggle => {
                        const submenu = subToggle.closest('.dropdown-submenu').querySelector('.dropdown-menu');
                        if (!submenu) return;
                        
                        let subTimer = null;
                        
                        subToggle.addEventListener('mouseenter', function() {
                            clearTimeout(subTimer);
                            subTimer = setTimeout(() => {
                                document.querySelectorAll('.dropdown-menu.show').forEach(m => {
                                    if (m !== submenu && !submenu.contains(m)) {
                                        // Keep parent menus open
                                    }
                                });
                                submenu.classList.add('show');
                            }, 100);
                        });
                        
                        subToggle.closest('.dropdown-submenu').addEventListener('mouseleave', function(e) {
                            clearTimeout(subTimer);
                            subTimer = setTimeout(() => {
                                const relatedTarget = e.relatedTarget;
                                if (relatedTarget && relatedTarget.closest('.dropdown-submenu') === this) {
                                    return;
                                }
                                submenu.classList.remove('show');
                            }, 200);
                        });
                        
                        submenu.addEventListener('mouseenter', function() {
                            clearTimeout(subTimer);
                        });
                        
                        submenu.addEventListener('mouseleave', function(e) {
                            clearTimeout(subTimer);
                            subTimer = setTimeout(() => {
                                const relatedTarget = e.relatedTarget;
                                if (relatedTarget && relatedTarget.closest('.dropdown-submenu') === subToggle.closest('.dropdown-submenu')) {
                                    return;
                                }
                                submenu.classList.remove('show');
                            }, 200);
                        });
                    });
                });

                // Click handling for touch devices and accessibility
                dropdownToggles.forEach(toggle => {
                    toggle.addEventListener('click', function(e) {
                        const parentDropdown = this.closest('.dropdown');
                        const menu = parentDropdown.querySelector('.dropdown-menu');
                        
                        if (window.innerWidth > 992) {
                            // On desktop, prevent default Bootstrap behavior and use our own
                            if (menu.classList.contains('show')) {
                                e.preventDefault();
                                e.stopPropagation();
                                bootstrap.Dropdown.getInstance(this).hide();
                            }
                        }
                    });
                });

                // Close dropdowns when clicking outside
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.dropdown')) {
                        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                            const toggle = menu.closest('.dropdown').querySelector('[data-bs-toggle="dropdown"]');
                            if (toggle) {
                                bootstrap.Dropdown.getInstance(toggle)?.hide();
                            }
                        });
                    }
                });

                // Close all dropdowns on ESC key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                            const toggle = menu.closest('.dropdown').querySelector('[data-bs-toggle="dropdown"]');
                            if (toggle) {
                                bootstrap.Dropdown.getInstance(toggle)?.hide();
                            }
                        });
                    }
                });
            }
        })();

        // State management
        let currentSection = 'category';
        let selectedCategory = null;
        let selectedCategoryData = null;
        let nomineeData = null;
        let categories = [];
        let currentQuestions = [];
        
        const currentEmployee = {
            id: {!! (int)$currentEmployee['id'] !!},
            name: {!! json_encode($currentEmployee['full_name']) !!},
            code: {!! json_encode($currentEmployee['employee_code']) !!},
            email: {!! json_encode($currentEmployee['email'] ?? '') !!},
            department: {!! json_encode($currentEmployee['department'] ?? '') !!},
            position: {!! json_encode($currentEmployee['position'] ?? '') !!}
        };

        // Load categories
        async function loadCategories() {
            try {
                const response = await fetch('api/get_categories');
                const data = await response.json();
                console.log('Categories loaded:', data);
                
                if (data.success && data.data && data.data.length > 0) {
                    categories = data.data;
                    displayCategories(data.data);
                    loadLeaderboard();
                } else {
                    document.getElementById('categoriesContainer').innerHTML = '<p class="text-center" style="grid-column:1/-1;padding:40px;">No categories available.</p>';
                }
            } catch (error) {
                console.error('Error loading categories:', error);
                document.getElementById('categoriesContainer').innerHTML = '<p class="text-center text-danger" style="grid-column:1/-1;padding:40px;">Error loading categories. Please refresh.</p>';
            }
        }

        // Display categories with self-nomination badge and voted status
        function displayCategories(categories) {
            const icons = { 'Sanyu Spotlight': 'star', 'Business Catalyst': 'chart-line', 'Priority Performer': 'medal' };
            const container = document.getElementById('categoriesContainer');
            
            container.innerHTML = categories.map(cat => {
                const isVoted = cat.user_has_voted === true || cat.user_has_voted == 1;
                const clickHandler = isVoted ? '' : `onclick="selectCategory(${cat.id})"`;
                const votedBadge = isVoted ? '<div class="voted-badge"><i class="fas fa-check-circle"></i> Already Voted</div>' : '';
                const selfBadge = cat.is_self_nomination == 1 ? '<span class="badge" style="background: linear-gradient(135deg, #8b5cf6, #6366f1); color: white; font-size: 11px; margin-left: 8px; padding: 4px 10px;"><i class="fas fa-user-check me-1"></i>Self</span>' : '';
                
                return `
                    <div class="category-card ${isVoted ? 'voted' : ''}" 
                         ${clickHandler}
                         data-category-id="${cat.id}"
                         style="${isVoted ? 'pointer-events: none;' : ''}">
                        ${votedBadge}
                        <div class="category-icon"><i class="fas fa-${icons[cat.name] || 'award'}"></i></div>
                        <h3>
                            ${escapeHtml(cat.name)}
                            ${selfBadge}
                        </h3>
                        <p>${escapeHtml(cat.description || 'Recognize outstanding achievements')}</p>
                        <div class="selected-badge"><i class="fas fa-check-circle"></i></div>
                    </div>
                `;
            }).join('');
        }

        // Select category
        function selectCategory(categoryId) {
            const category = categories.find(c => c.id == categoryId);
            
            if (category && (category.user_has_voted === true || category.user_has_voted == 1)) {
                alert('You have already submitted a nomination in this category for the current quarter. Only one nomination per category per quarter is allowed.');
                return;
            }
            
            selectedCategory = categoryId;
            selectedCategoryData = category;
            document.querySelectorAll('.category-card').forEach(card => card.classList.remove('selected'));
            
            const selectedCard = document.querySelector(`[data-category-id="${categoryId}"]`);
            if (selectedCard) {
                selectedCard.classList.add('selected');
            }
        }

        // Setup autocomplete
        function setupAutocomplete() {
            const input = document.getElementById('nomineeSearch');
            const results = document.getElementById('nomineeResults');
            let timeout;
            
            input.addEventListener('input', function() {
                clearTimeout(timeout);
                const query = this.value.trim();
                if (query.length < 2) { results.style.display = 'none'; return; }
                
                timeout = setTimeout(async () => {
                    try {
                        results.innerHTML = '<div class="autocomplete-item text-center"><div class="loading-spinner"></div> Searching...</div>';
                        results.style.display = 'block';
                        
                        const response = await fetch(`api/get_employees?search=${encodeURIComponent(query)}&exclude_current=${currentEmployee.id}`);
                        const data = await response.json();
                        
                        if (data.success && data.data && data.data.length > 0) {
                            displayAutocompleteResults(data.data, results);
                        } else {
                            results.innerHTML = '<div class="autocomplete-item text-center text-muted">No employees found</div>';
                            setTimeout(() => results.style.display = 'none', 2000);
                        }
                    } catch (error) { 
                        console.error('Autocomplete error:', error);
                        results.style.display = 'none';
                    }
                }, 300);
            });
            
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.autocomplete-wrapper')) {
                    results.style.display = 'none';
                }
            });
        }

        // Display autocomplete results
        function displayAutocompleteResults(employees, container) {
            container.innerHTML = employees.map(emp => `
                <div class="autocomplete-item" onclick="selectNominee(${emp.id})">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            ${emp.photo_url ? 
                                `<img src="${emp.photo_url}" class="rounded-circle" width="40" height="40" style="object-fit: cover;">` : 
                                `<div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-muted"></i>
                                </div>`
                            }
                        </div>
                        <div class="flex-grow-1">
                            <div class="employee-name">${escapeHtml(emp.full_name)}</div>
                            <div class="employee-code">${escapeHtml(emp.employee_code)}</div>
                            <div class="employee-dept">${escapeHtml(emp.department || '')}</div>
                        </div>
                    </div>
                </div>
            `).join('');
            container.style.display = 'block';
        }

        // Select nominee
        async function selectNominee(employeeId) {
            try {
                document.getElementById('nomineeName').value = 'Loading...';
                
                const response = await fetch(`api/get_employees?id=${employeeId}`);
                const data = await response.json();
                
                if (data.success && data.data) {
                    nomineeData = data.data;
                    document.getElementById('nomineeSearch').value = nomineeData.employee_code;
                    document.getElementById('nomineeName').value = nomineeData.full_name;
                    document.getElementById('nomineeEmail').value = nomineeData.email || '';
                    document.getElementById('nomineeDepartment').value = nomineeData.department || '';
                    document.getElementById('nomineePosition').value = nomineeData.job_title || '';
                    document.getElementById('nomineeResults').style.display = 'none';
                } else {
                    alert('Could not load employee details. Please try again.');
                }
            } catch (error) { 
                console.error('Error selecting nominee:', error);
                alert('Error loading employee details');
            }
        }

        // Navigation functions
        function nextSection() {
            if (currentSection === 'category') {
                if (!selectedCategory) { 
                    alert('Please select an award category'); 
                    return; 
                }
                
                if (selectedCategoryData && (selectedCategoryData.user_has_voted === true || selectedCategoryData.user_has_voted == 1)) {
                    alert('You have already submitted a nomination in this category for the current quarter.');
                    selectedCategory = null;
                    selectedCategoryData = null;
                    document.querySelectorAll('.category-card').forEach(card => card.classList.remove('selected'));
                    return;
                }
                
                document.getElementById('sectionCategory').classList.add('hidden');
                
                if (selectedCategoryData && selectedCategoryData.is_self_nomination == 1) {
                    nomineeData = currentEmployee;
                    
                    document.getElementById('nomineeSearch').value = currentEmployee.code;
                    document.getElementById('nomineeName').value = currentEmployee.name;
                    document.getElementById('nomineeEmail').value = currentEmployee.email || '';
                    document.getElementById('nomineeDepartment').value = currentEmployee.department || '';
                    document.getElementById('nomineePosition').value = currentEmployee.position || '';
                    
                    const nextBtn = document.getElementById('nextBtn');
                    const originalText = nextBtn.innerHTML;
                    nextBtn.innerHTML = '<span class="loading-spinner"></span> Loading...';
                    nextBtn.disabled = true;
                    
                    loadQuestions(selectedCategory).then(success => {
                        if (success) {
                            document.getElementById('sectionQuestions').classList.remove('hidden');
                            currentSection = 'questions';
                            updateUI();
                        }
                    }).finally(() => {
                        nextBtn.innerHTML = originalText;
                        nextBtn.disabled = false;
                    });
                } else {
                    document.getElementById('sectionNominee').classList.remove('hidden');
                    currentSection = 'nominee';
                    updateUI();
                }
            } else if (currentSection === 'nominee') {
                if (!nomineeData) { 
                    alert('Please select a nominee'); 
                    return; 
                }
                if (currentEmployee.id == nomineeData.id) { 
                    alert('You cannot nominate yourself for this category. Please select a different category or nominee.'); 
                    return; 
                }
                
                const nextBtn = document.getElementById('nextBtn');
                const originalText = nextBtn.innerHTML;
                nextBtn.innerHTML = '<span class="loading-spinner"></span> Loading...';
                nextBtn.disabled = true;
                
                loadQuestions(selectedCategory).then(success => {
                    if (success) {
                        document.getElementById('sectionNominee').classList.add('hidden');
                        document.getElementById('sectionQuestions').classList.remove('hidden');
                        currentSection = 'questions';
                        updateUI();
                    }
                }).finally(() => {
                    nextBtn.innerHTML = originalText;
                    nextBtn.disabled = false;
                });
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function previousSection() {
            if (currentSection === 'nominee') {
                document.getElementById('sectionNominee').classList.add('hidden');
                document.getElementById('sectionCategory').classList.remove('hidden');
                currentSection = 'category';
                nomineeData = null;
                updateUI();
            } else if (currentSection === 'questions') {
                document.getElementById('sectionQuestions').classList.add('hidden');
                
                if (selectedCategoryData && selectedCategoryData.is_self_nomination == 1) {
                    document.getElementById('sectionCategory').classList.remove('hidden');
                    currentSection = 'category';
                    nomineeData = null;
                } else {
                    document.getElementById('sectionNominee').classList.remove('hidden');
                    currentSection = 'nominee';
                }
                updateUI();
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function updateUI() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            
            prevBtn.style.display = currentSection === 'category' ? 'none' : 'flex';
            
            if (currentSection === 'questions') {
                nextBtn.classList.add('hidden');
                submitBtn.classList.remove('hidden');
            } else {
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
            }
        }

        // Load questions
        async function loadQuestions(categoryId) {
            try {
                const response = await fetch(`api/get_questions?category_id=${categoryId}`);
                const data = await response.json();
                
                if (data.success) {
                    currentQuestions = data.data;
                    
                    if (!currentQuestions || currentQuestions.length === 0) {
                        alert('This category does not have any questions configured. Please contact the administrator.');
                        previousSection();
                        return false;
                    }
                    
                    displayQuestions(data.data);
                    return true;
                } else {
                    alert('Error loading questions for this category');
                    previousSection();
                    return false;
                }
            } catch (error) { 
                console.error('Error loading questions:', error);
                alert('Error loading questions. Please try again.');
                previousSection();
                return false;
            }
        }

        // Display questions
        function displayQuestions(questions) {
            const container = document.getElementById('questionsContainer');
            if (!questions || questions.length === 0) {
                container.innerHTML = '<p class="text-center" style="color:#64748b;">No additional questions for this category.</p>';
                return;
            }
            
            container.innerHTML = questions.map((q) => {
                const questionText = escapeHtml(q.question_text || '');
                let input = '';
                const options = q.options ? q.options.split(',').map(o => o.trim()) : [];
                const isRequired = q.is_required == 1 || q.is_required === '1' || q.is_required === true;
                
                switch(q.question_type) {
                    case 'radio':
                        input = `<div class="radio-group">${options.map(opt => `
                            <label class="radio-option">
                                <input type="radio" name="q_${q.id}" value="${escapeHtml(opt)}" ${isRequired ? 'required' : ''}>
                                <span>${escapeHtml(opt)}</span>
                            </label>
                        `).join('')}</div>`;
                        break;
                    case 'checkbox':
                        input = `<div class="checkbox-group">${options.map(opt => `
                            <label class="checkbox-option">
                                <input type="checkbox" name="q_${q.id}[]" value="${escapeHtml(opt)}">
                                <span>${escapeHtml(opt)}</span>
                            </label>
                        `).join('')}</div>`;
                        break;
                    case 'long_text':
                        input = `<textarea name="q_${q.id}" rows="4" placeholder="Enter your response..." ${isRequired ? 'required' : ''}></textarea>`;
                        break;
                    case 'number':
                        input = `<input type="number" name="q_${q.id}" placeholder="Enter a number" ${isRequired ? 'required' : ''}>`;
                        break;
                    case 'date':
                        input = `<input type="date" name="q_${q.id}" ${isRequired ? 'required' : ''}>`;
                        break;
                    case 'rating':
                        input = `<div class="rating-stars" data-question="q_${q.id}">
                                    ${[1,2,3,4,5].map(r => `<i class="fas fa-star rating-star" data-value="${r}"></i>`).join('')}
                                    <input type="hidden" name="q_${q.id}" value="" ${isRequired ? 'data-required="true"' : ''}>
                                </div>`;
                        break;
                    case 'attachment':
                        input = `<div class="attachment-upload">
                                    <input type="file" name="q_${q.id}" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" ${isRequired ? 'required' : ''}>
                                    <small class="text-muted">Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max 10MB)</small>
                                </div>`;
                        break;
                    default:
                        input = `<input type="text" name="q_${q.id}" placeholder="Enter your response" ${isRequired ? 'required' : ''}>`;
                }
                
                const requiredStar = isRequired ? '<span class="text-danger">*</span>' : '';
                
                return `
                    <div class="question-item" data-question-id="${q.id}" data-required="${isRequired}">
                        <div class="question-text">${questionText} ${requiredStar}</div>
                        ${input}
                    </div>
                `;
            }).join('');
            
            document.querySelectorAll('.rating-stars').forEach(container => {
                const stars = container.querySelectorAll('.rating-star');
                const hiddenInput = container.querySelector('input[type="hidden"]');
                
                stars.forEach(star => {
                    star.addEventListener('click', function() {
                        const value = this.getAttribute('data-value');
                        stars.forEach(s => s.classList.remove('active'));
                        for(let i = 0; i < value; i++) stars[i].classList.add('active');
                        hiddenInput.value = value;
                    });
                });
            });
        }

        // Submit nomination
        document.getElementById('nominationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Submitting...';
            submitBtn.disabled = true;
            
            try {
                let hasError = false;
                let errorMessages = [];
                
                currentQuestions.forEach(q => {
                    const isRequired = q.is_required == 1 || q.is_required === '1' || q.is_required === true;
                    
                    if (isRequired) {
                        let hasValue = false;
                        
                        if (q.question_type === 'checkbox') {
                            const checkboxes = document.querySelectorAll(`input[name="q_${q.id}[]"]:checked`);
                            hasValue = checkboxes.length > 0;
                        } else if (q.question_type === 'rating') {
                            const hiddenInput = document.querySelector(`input[name="q_${q.id}"]`);
                            hasValue = hiddenInput && hiddenInput.value !== '';
                        } else if (q.question_type === 'attachment') {
                            const fileInput = document.querySelector(`input[name="q_${q.id}"]`);
                            hasValue = fileInput && fileInput.files.length > 0;
                        } else {
                            const input = document.querySelector(`[name="q_${q.id}"]`);
                            hasValue = input && input.value.trim() !== '';
                        }
                        
                        if (!hasValue) {
                            hasError = true;
                            const shortText = q.question_text.length > 50 ? q.question_text.substring(0, 50) + '...' : q.question_text;
                            errorMessages.push(`"${shortText}" is required`);
                        }
                    }
                });
                
                if (hasError) {
                    alert('Please answer all required questions:\n\n' + errorMessages.join('\n'));
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    return;
                }
                
                const formData = new FormData();
                formData.append('category_id', selectedCategory);
                formData.append('nominator_id', currentEmployee.id);
                formData.append('nominee_id', nomineeData.id);
                
                const answers = currentQuestions.map(q => {
                    let value;
                    if (q.question_type === 'checkbox') {
                        const checkboxes = document.querySelectorAll(`input[name="q_${q.id}[]"]:checked`);
                        value = Array.from(checkboxes).map(cb => cb.value).join(', ');
                    } else if (q.question_type === 'attachment') {
                        const fileInput = document.querySelector(`input[name="q_${q.id}"]`);
                        if (fileInput && fileInput.files.length > 0) {
                            formData.append(`attachment_${q.id}`, fileInput.files[0]);
                            value = fileInput.files[0].name;
                        } else {
                            value = '';
                        }
                    } else {
                        const input = document.querySelector(`[name="q_${q.id}"]`);
                        value = input ? input.value : '';
                    }
                    return { question_id: q.id, answer: value };
                });
                
                formData.append('answers', JSON.stringify(answers));
                
                const response = await fetch('api/submit_nomination', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('formContainer').classList.add('hidden');
                    document.getElementById('successContainer').classList.remove('hidden');
                    loadLeaderboard();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    alert(data.message || 'Error submitting nomination');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            } catch (error) {
                console.error('Submit error:', error);
                alert('An error occurred. Please try again.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });

        // Load leaderboard
        async function loadLeaderboard() {
            try {
                const response = await fetch('api/get_leaderboard');
                const data = await response.json();
                if (data.success) displayLeaderboard(data.data);
            } catch (error) { console.error('Leaderboard error:', error); }
        }

        // Display leaderboard
        function displayLeaderboard(data) {
            const tabsContainer = document.getElementById('categoryTabs');
            const contentContainer = document.getElementById('leaderboardContent');
            const categories = Object.keys(data);
            
            if (categories.length === 0) {
                contentContainer.innerHTML = '<p class="text-center" style="padding:40px;color:#64748b;">No nominations yet. Be the first!</p>';
                return;
            }
            
            tabsContainer.innerHTML = categories.map((cat, i) => `
                <button class="tab-btn ${i === 0 ? 'active' : ''}" onclick="showLeaderboardCategory('${cat.replace(/'/g, "\\'")}')">${escapeHtml(cat)}</button>
            `).join('');
            
            showLeaderboardCategoryData(categories[0], data[categories[0]]);
            window.leaderboardData = data;
        }

        // function showLeaderboardCategory(categoryName) {
        //     showLeaderboardCategoryData(categoryName, window.leaderboardData[categoryName]);
        //     document.querySelectorAll('.tab-btn').forEach(btn => {
        //         btn.classList.toggle('active', btn.textContent.trim() === categoryName);
        //     });
        // }

        function showLeaderboardCategoryData(categoryName, nominees) {
            const container = document.getElementById('leaderboardContent');
            if (!nominees || nominees.length === 0) {
                container.innerHTML = '<p class="text-center" style="padding:40px;color:#64748b;">No nominations yet</p>';
                return;
            }
            container.innerHTML = `<div class="leaderboard-grid">${nominees.slice(0, 5).map((n, i) => `
                <div class="leader-card">
                    <div class="rank-badge">#${i + 1}</div>
                    <div class="leader-name">${escapeHtml(n.nominee_name)}</div>
                    <div class="leader-dept">${escapeHtml(n.department || '—')}</div>
                    <div class="leader-votes">${n.votes} <small>votes</small></div>
                </div>
            `).join('')}</div>`;
        }

        // Load nomination history
        async function loadNominationHistory() {
            const year = document.getElementById('historyYear').value;
            const quarter = document.getElementById('historyQuarter').value;
            const contentDiv = document.getElementById('historyContent');
            
            contentDiv.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3 text-muted">Loading...</p></div>';
            
            try {
                let url = `api/get_user_nominations_history`;
                let params = [];
                if (year && year > 0) params.push(`year=${year}`);
                if (quarter && quarter > 0) params.push(`quarter=${quarter}`);
                if (params.length) url += '?' + params.join('&');
                
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.success && data.data.length > 0) {
                    const grouped = {};
                    data.data.forEach(nom => {
                        const key = `${nom.year}-Q${nom.quarter}`;
                        if (!grouped[key]) grouped[key] = [];
                        grouped[key].push(nom);
                    });
                    
                    let html = '';
                    for (const [period, nominations] of Object.entries(grouped)) {
                        const [year, quarterPart] = period.split('-Q');
                        const quarterName = quarterPart === '1' ? 'January - March' : 
                                           (quarterPart === '2' ? 'April - June' : 
                                           (quarterPart === '3' ? 'July - September' : 'October - December'));
                        
                        html += `
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">
                                    <span class="quarter-badge quarter-Q${quarterPart}">${period}</span>
                                    <span class="text-muted ms-2">(${quarterName})</span>
                                </h6>
                        `;
                        
                        nominations.forEach(nom => {
                            const statusClass = nom.status === 'approved' ? 'success' : (nom.status === 'rejected' ? 'danger' : 'warning');
                            const statusIcon = nom.status === 'approved' ? 'check-circle' : (nom.status === 'rejected' ? 'times-circle' : 'clock');
                            const isSelfNom = nom.nominator_id == nom.nominee_id;
                            
                            html += `
                                <div class="history-card">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <div style="width: 40px; height: 40px; background: ${nom.category_color}20; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: ${nom.category_color};">
                                                <i class="fas fa-${nom.category_icon}"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">
                                                    ${escapeHtml(nom.category_name)}
                                                    ${isSelfNom ? '<span class="badge ms-2" style="background: linear-gradient(135deg, #8b5cf6, #6366f1); color: white; font-size: 10px;"><i class="fas fa-user-check me-1"></i>Self</span>' : ''}
                                                </div>
                                                <div class="small text-muted">
                                                    ${isSelfNom ? 'Self-Nomination' : `Nominated: ${escapeHtml(nom.nominee_name)} (${escapeHtml(nom.nominee_code)})`}
                                                </div>
                                                <div class="small text-muted">${nom.department ? escapeHtml(nom.department) : ''}</div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-${statusClass}">
                                                <i class="fas fa-${statusIcon} me-1"></i>${nom.status}
                                            </span>
                                            <div class="small text-muted mt-1">${new Date(nom.created_at).toLocaleDateString()}</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        html += `</div>`;
                    }
                    contentDiv.innerHTML = html;
                } else {
                    contentDiv.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No nominations found for the selected period.</p>
                        </div>
                    `;
                }
                
                if (data.years && data.years.length > 0) {
                    const yearSelect = document.getElementById('historyYear');
                    const currentValue = yearSelect.value;
                    yearSelect.innerHTML = '<option value="0">All Years</option>';
                    data.years.forEach(y => {
                        yearSelect.innerHTML += `<option value="${y}" ${currentValue == y ? 'selected' : ''}>${y}</option>`;
                    });
                }
            } catch (error) {
                console.error('Error loading history:', error);
                contentDiv.innerHTML = '<div class="text-center py-5 text-danger">Error loading history. Please try again.</div>';
            }
        }

        // Show history modal
        function showHistoryModal() {
            loadNominationHistory();
            new bootstrap.Modal(document.getElementById('nominationHistoryModal')).show();
        }

        // Escape HTML
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        // Attach event listeners
        document.getElementById('prevBtn').addEventListener('click', previousSection);
        document.getElementById('nextBtn').addEventListener('click', nextSection);
        
        // Initial setup
        if (!currentEmployee.id) {
            window.location.href = '../login';
        } else {
            loadCategories();
            setupAutocomplete();
        }
        
    </script>
</body>
</html>
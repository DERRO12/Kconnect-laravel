<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Directory - {{ SITE_NAME }}</title>
      <link rel="icon" type="image/png" href="{{ asset('favi.png') }}">
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        /* ========================================
           EMPLOYEE DIRECTORY - CUSTOM STYLES
           ======================================== */
        
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --kiboko-dark: #0f172a;
            --kiboko-light: #f8fafc;
            --kiboko-border: #e2e8f0;
            --kiboko-gray: #64748b;
            --grad: linear-gradient(135deg, var(--kiboko-blue) 0%, var(--kiboko-green) 100%);
            --grad-light: linear-gradient(135deg, rgba(0, 154, 218, 0.08) 0%, rgba(118, 188, 33, 0.08) 100%);
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.02);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.05);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.05);
            --radius-sm: 12px;
            --radius-md: 16px;
            --radius-lg: 20px;
            --radius-xl: 24px;
        }

        /* Page Header */
        .page-header {
            background: white;
            padding: 40px 0;
            margin: 40px 0;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--grad);
            opacity: 0.5;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 15px;
        }

        .page-header .page-description {
            color: var(--kiboko-gray);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: var(--radius-lg);
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(0, 154, 218, 0.1);
        }

        .search-wrapper {
            position: relative;
        }

        .search-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--kiboko-gray);
            font-size: 1rem;
            z-index: 1;
        }

        .search-wrapper input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 2px solid var(--kiboko-border);
            border-radius: 50px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .search-wrapper input:focus {
            outline: none;
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 3px rgba(0, 154, 218, 0.1);
        }

        .filter-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--kiboko-gray);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-select {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid var(--kiboko-border);
            border-radius: 50px;
            font-size: 0.95rem;
            color: var(--kiboko-dark);
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 3px rgba(0, 154, 218, 0.1);
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .filter-btn {
            padding: 10px 20px;
            border: 2px solid var(--kiboko-border);
            border-radius: 50px;
            background: white;
            color: var(--kiboko-gray);
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .filter-btn i {
            font-size: 0.9rem;
            color: var(--kiboko-blue);
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            border-color: var(--kiboko-blue);
            color: var(--kiboko-blue);
            transform: translateY(-2px);
        }

        .filter-btn.active {
            background: var(--grad);
            border-color: transparent;
            color: white;
        }

        .filter-btn.active i {
            color: white;
        }

        .clear-filters {
            color: var(--kiboko-blue);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .clear-filters:hover {
            background: var(--grad-light);
            color: var(--kiboko-blue);
        }

        /* Company Sections */
        .company-section {
            background: white;
            border-radius: var(--radius-xl);
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(0, 154, 218, 0.1);
            transition: all 0.3s ease;
        }

        .company-section:hover {
            box-shadow: var(--shadow-xl);
        }

        .company-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--kiboko-border);
            position: relative;
        }

        .company-header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100px;
            height: 2px;
            background: var(--grad);
        }

        .company-logo {
            width: 60px;
            height: 60px;
            border-radius: var(--radius-md);
            background: var(--grad-light);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .company-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .company-logo i {
            font-size: 2rem;
            color: var(--kiboko-blue);
        }

        .company-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--kiboko-dark);
            margin: 0;
            flex: 1;
        }

        .employee-count {
            background: var(--grad-light);
            color: var(--kiboko-blue);
            font-weight: 600;
            font-size: 0.9rem;
            padding: 8px 16px;
            border-radius: 50px;
            border: 1px solid rgba(0, 154, 218, 0.2);
        }

        /* Directory Grid */
        .directory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        /* Employee Card */
        .employee-card {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(0, 154, 218, 0.1);
            transition: all 0.3s ease;
            position: relative;
        }

        .employee-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
            border-color: var(--kiboko-blue);
        }

        .employee-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--grad);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .employee-card:hover::before {
            opacity: 1;
        }

        .card-header {
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid var(--kiboko-border);
            position: relative;
        }

        .company-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--grad-light);
            color: var(--kiboko-blue);
            font-size: 0.7rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 30px;
            border: 1px solid rgba(0, 154, 218, 0.2);
        }

        .employee-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: var(--shadow-lg);
            margin: 0 auto 15px;
        }

        .avatar-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--grad-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            border: 4px solid white;
            box-shadow: var(--shadow-lg);
        }

        .avatar-placeholder i {
            font-size: 3.5rem;
            color: var(--kiboko-blue);
        }

        .employee-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--kiboko-dark);
            margin-bottom: 8px;
        }

        .employee-position {
            color: var(--kiboko-blue);
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 0;
            display: inline-block;
            padding: 4px 12px;
            background: rgba(0, 154, 218, 0.08);
            border-radius: 30px;
        }

        .card-body {
            padding: 20px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 15px;
        }

        .info-icon {
            width: 36px;
            height: 36px;
            background: var(--grad-light);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--kiboko-blue);
            font-size: 1rem;
            flex-shrink: 0;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--kiboko-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--kiboko-dark);
            word-break: break-word;
        }

        .info-value a {
            color: var(--kiboko-dark);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .info-value a:hover {
            color: var(--kiboko-blue);
            text-decoration: underline;
        }

        .department-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--grad-light);
            color: var(--kiboko-blue);
            font-size: 0.85rem;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 30px;
            margin-top: 10px;
            border: 1px solid rgba(0, 154, 218, 0.2);
        }

        .department-tag i {
            font-size: 0.8rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--kiboko-blue);
            opacity: 0.3;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--kiboko-dark);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--kiboko-gray);
            font-size: 1rem;
        }

        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--grad);
            color: white;
            border: none;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-xl);
            z-index: 1000;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.5);
            cursor: pointer;
        }

        .back-to-top:hover {
            transform: translateY(-5px) scale(1.1);
        }

        /* Responsive Design */
        @media (max-width: 991.98px) {
            .filter-buttons {
                justify-content: flex-start;
                margin-top: 15px;
            }
            
            .company-header {
                flex-wrap: wrap;
            }
            
            .company-header h2 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }
            
            .directory-grid {
                grid-template-columns: 1fr;
            }
            
            .company-section {
                padding: 20px;
            }
            
            .company-header {
                flex-direction: column;
                text-align: center;
            }
            
            .company-header::after {
                left: 50%;
                transform: translateX(-50%);
            }
            
            .filter-buttons {
                justify-content: center;
            }
            
            .filter-group {
                min-width: 100%;
            }
        }

        @media (max-width: 576px) {
            .page-header h1 {
                font-size: 1.8rem;
            }
            
            .page-header .page-description {
                font-size: 1rem;
            }
            
            .filter-section {
                padding: 20px 15px;
            }
            
            .filter-btn {
                width: 100%;
                justify-content: center;
            }
            
            .back-to-top {
                bottom: 20px;
                right: 20px;
                width: 40px;
                height: 40px;
            }
            
            .employee-avatar, .avatar-placeholder {
                width: 100px;
                height: 100px;
            }
            
            .avatar-placeholder i {
                font-size: 2.8rem;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .company-section {
            animation: fadeInUp 0.5s ease forwards;
        }

        .employee-card {
            animation: fadeInUp 0.5s ease backwards;
        }

        .employee-card:nth-child(1) { animation-delay: 0.1s; }
        .employee-card:nth-child(2) { animation-delay: 0.15s; }
        .employee-card:nth-child(3) { animation-delay: 0.2s; }
        .employee-card:nth-child(4) { animation-delay: 0.25s; }
        .employee-card:nth-child(5) { animation-delay: 0.3s; }
        .employee-card:nth-child(6) { animation-delay: 0.35s; }
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
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('K-CONNECT.png') }}" alt="K-CONNECT">
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
                            <img src="{{ asset('uploads') }}/employees/{{ session('employee_photo') }}" 
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
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="{{ url('logout') }}"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 data-aos="fade-down">
                <span class="text-gradient">Employee</span> Directory
            </h1>
            <p class="page-description" data-aos="fade-down" data-aos-delay="100">
                Find colleagues, contact information, and department details
            </p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container mb-5">
        <!-- Filter Section -->
        <div class="filter-section" data-aos="fade-up">
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="search-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search by name, department, position, or email...">
                    </div>
                </div>
            </div>
            
            <div class="filter-row">
                <div class="filter-group">
                    <div class="filter-label">Filter by Company</div>
                    <select id="companyFilter" class="filter-select">
                        <option value="all">All Companies</option>
                        @foreach ($companies as $company)
                        <option value="{!! $company['id'] !!}">
                            {{ $company['name'] }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group">
                    <div class="filter-label">Filter by Department</div>
                    <select id="departmentFilter" class="filter-select">
                        <option value="all">All Departments</option>
                        @foreach ($departments as $dept)
                        <option value="{!! strtolower(htmlspecialchars($dept['department'])) !!}">
                            {{ $dept['department'] }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group">
                    <div class="filter-label">&nbsp;</div>
                    <a href="#" id="clearFilters" class="clear-filters">
                        <i class="fas fa-times-circle"></i> Clear All Filters
                    </a>
                </div>
            </div>
        </div>

        <!-- Directory by Company -->
        <div id="directoryContainer">
            @php
            $hasEmployees = false;
@endphp
            @foreach ($companies as $company)
@php
                $companyEmployees = $companyEmployeesMap[$company['id']];
@endphp
                @if (count($companyEmployees) > 0)
@php
                    $hasEmployees = true;
@endphp
            <div class="company-section" data-company="{!! $company['id'] !!}" data-aos="fade-up">
                <div class="company-header">
                    <div class="company-logo">
                        @if ($company['logo'])
                            <img src="{{ asset('uploads') }}/{!! $company['logo'] !!}" alt="{{ $company['name'] }}">
                        @else
                            <i class="fas fa-building"></i>
                        @endif
                    </div>
                    <h2>{{ $company['name'] }}</h2>
                    <span class="employee-count">{!! count($companyEmployees) !!} employees</span>
                </div>
                
                <div class="directory-grid">
                    @foreach ($companyEmployees as $emp)
                    <div class="employee-card" 
                         data-company="{!! $emp['company_id'] !!}"
                         data-name="{!! strtolower(htmlspecialchars($emp['full_name'])) !!}"
                         data-department="{!! strtolower(htmlspecialchars($emp['department'])) !!}"
                         data-position="{!! strtolower(htmlspecialchars($emp['position'])) !!}"
                         data-email="{!! strtolower(htmlspecialchars($emp['email'])) !!}">
                        <div class="card-header">
                            <span class="company-badge">
                                <i class="fas fa-building me-1"></i>
                                {{ $company['code'] ?: $company['name'] }}
                            </span>
                            
                            @if ($emp['photo'])
                                <img src="{{ asset('uploads') }}/employees/{{ $emp['photo'] }}" 
                                     class="employee-avatar" alt="{{ $emp['full_name'] }}">
                            @else
                                <div class="avatar-placeholder">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                            @endif
                            
                            <h3 class="employee-name">{{ $emp['full_name'] }}</h3>
                            @if ($emp['position'])
                                <div class="employee-position">{{ $emp['position'] }}</div>
                            @endif
                        </div>
                        
                        <div class="card-body">
                            @if ($emp['email'])
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Email</div>
                                    <div class="info-value">
                                        <a href="mailto:{{ $emp['email'] }}">
                                            {{ $emp['email'] }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if ($emp['phone'] || $emp['extension'])
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Phone</div>
                                    <div class="info-value">
                                        @if ($emp['phone'])
                                            <a href="tel:{{ $emp['phone'] }}">
                                                {{ $emp['phone'] }}
                                            </a>
                                        @endif
                                        @if ($emp['extension'])
                                            <span class="ms-2 text-muted">Ext: {{ $emp['extension'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if ($emp['mobile'])
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Mobile</div>
                                    <div class="info-value">
                                        <a href="tel:{{ $emp['mobile'] }}">
                                            {{ $emp['mobile'] }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if ($emp['department'])
                            <div class="department-tag">
                                <i class="fas fa-sitemap"></i>
                                {{ $emp['department'] }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach
            @if (!$hasEmployees)
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>No employees found</h3>
                <p class="text-muted">Check back soon for employee directory updates</p>
            </div>
            @endif
        </div>

        <!-- No Results Message -->
        <div class="empty-state" id="noResults" style="display: none;">
            <i class="fas fa-search"></i>
            <h3>No matching employees</h3>
            <p class="text-muted">No employees found matching your search criteria</p>
        </div>
    </div>
  @include('partials.footer')
    <!-- Back to Top Button -->
    <button class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
        });

        // Back to top button
        window.addEventListener('scroll', function() {
            const backToTop = document.querySelector('.back-to-top');
            backToTop.style.display = window.pageYOffset > 300 ? 'block' : 'none';
        });

        // Search and filter functionality
        const searchInput = document.getElementById('searchInput');
        const companyFilter = document.getElementById('companyFilter');
        const departmentFilter = document.getElementById('departmentFilter');
        const clearFilters = document.getElementById('clearFilters');
        const companySections = document.querySelectorAll('.company-section');
        const employeeCards = document.querySelectorAll('.employee-card');
        const noResults = document.getElementById('noResults');
        
        let searchTimeout;

        function filterDirectory() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const selectedCompany = companyFilter.value;
            const selectedDepartment = departmentFilter.value;
            
            let visibleCount = 0;

            // Filter employee cards
            employeeCards.forEach(card => {
                const company = card.dataset.company;
                const name = card.dataset.name || '';
                const department = card.dataset.department || '';
                const position = card.dataset.position || '';
                const email = card.dataset.email || '';
                
                const matchesCompany = selectedCompany === 'all' || company === selectedCompany;
                const matchesDepartment = selectedDepartment === 'all' || department === selectedDepartment;
                const matchesSearch = searchTerm === '' || 
                    name.includes(searchTerm) || 
                    department.includes(searchTerm) || 
                    position.includes(searchTerm) ||
                    email.includes(searchTerm);
                
                if (matchesCompany && matchesDepartment && matchesSearch) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show/hide company sections
            companySections.forEach(section => {
                const visibleInSection = Array.from(section.querySelectorAll('.employee-card')).filter(
                    card => card.style.display === 'block'
                ).length;
                
                section.style.display = visibleInSection > 0 ? 'block' : 'none';
            });

            // Show/hide no results
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        // Debounced search
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(filterDirectory, 300);
        });

        // Company filter change
        companyFilter.addEventListener('change', filterDirectory);

        // Department filter change
        departmentFilter.addEventListener('change', filterDirectory);

        // Clear all filters
        clearFilters.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Reset search
            searchInput.value = '';
            
            // Reset selects
            companyFilter.value = 'all';
            departmentFilter.value = 'all';
            
            // Apply filters
            filterDirectory();
            
            // Hide clear button if visible
            if (clearBtn) {
                clearBtn.style.display = 'none';
            }
        });

        // Clear search on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.activeElement === searchInput) {
                searchInput.value = '';
                filterDirectory();
                if (clearBtn) {
                    clearBtn.style.display = 'none';
                }
            }
        });

        // Add clear button to search
        const searchWrapper = document.querySelector('.search-wrapper');
        let clearBtn;
        
        if (searchWrapper) {
            clearBtn = document.createElement('button');
            clearBtn.innerHTML = '<i class="fas fa-times"></i>';
            clearBtn.style.cssText = `
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                color: #94a3b8;
                cursor: pointer;
                display: none;
                padding: 5px;
                z-index: 2;
            `;
            clearBtn.setAttribute('aria-label', 'Clear search');
            
            searchWrapper.appendChild(clearBtn);
            
            searchInput.addEventListener('input', function() {
                clearBtn.style.display = this.value ? 'block' : 'none';
            });
            
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                filterDirectory();
                clearBtn.style.display = 'none';
                searchInput.focus();
            });
        }
    </script>
</body>
</html>
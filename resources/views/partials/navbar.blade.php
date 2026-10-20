<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('K-CONNECT.png') }}" alt="K-CONNECT">
        </a>
        
        <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                @php
                // Menu items from the navigation_links table (getMenu() in app/Support/legacy.php)
                getMenu();
                @endphp

                @if (isLoggedIn())
                    <li class="nav-item dropdown ms-3">
                        <a class="nav-link d-flex align-items-center dropdown-toggle" 
                           href="#" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                            @if (!empty(session('employee_photo')))
                                <img src="{{ asset('uploads/employees') }}/{{ session('employee_photo') }}" 
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
                            <li><h6 class="dropdown-header">ID: {{ session('employee_code') }}</h6></li>
                            <li><a class="dropdown-item" href="{{ url('my-assets') }}"><i class="fas fa-laptop me-2"></i>My Assets</a></li>
                            <li><a class="dropdown-item" href="{{ url('change-password') }}"><i class="fas fa-key me-2"></i>My Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="{{ url('logout') }}"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
<div class="container-fluid">
    <!-- Company Header -->
    <div class="text-center mb-4">
        @php
$companyClass = '';
        if ($company['name'] == 'KEL Uganda') $companyClass = 'badge-uganda';
        elseif ($company['name'] == 'KEL Tanzania') $companyClass = 'badge-tanzania';
        elseif ($company['name'] == 'KFS') $companyClass = 'badge-kfs';
        else $companyClass = 'badge-group';
@endphp
        
        <div class="company-logo-wrapper {!! strtolower(str_replace(' ', '-', $company['name'])) !!} mx-auto" style="width: 100px; height: 100px;">
            @if ($company['logo'])
                <img src="{{ asset('uploads') }}/{!! $company['logo'] !!}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <i class="fas fa-building fa-3x"></i>
            @endif
        </div>
        
        <h2 class="fw-bold mt-3">{{ $company['name'] }}</h2>
        @if ($company['code'])
            <span class="badge bg-light text-dark mb-2">{!! $company['code'] !!}</span>
        @endif
        
        <p class="text-muted">{{ $company['description'] ?: '' }}</p>
        
        <!-- Stats -->
        <div class="row mt-4">
            <div class="col-4">
                <div class="border rounded p-3">
                    <h3 class="fw-bold text-primary">{!! $stats['total_employees'] !!}</h3>
                    <small class="text-muted">Employees</small>
                </div>
            </div>
            <div class="col-4">
                <div class="border rounded p-3">
                    <h3 class="fw-bold text-warning">{!! $stats['total_departments'] !!}</h3>
                    <small class="text-muted">Departments</small>
                </div>
            </div>
            <div class="col-4">
                <div class="border rounded p-3">
                    <h3 class="fw-bold text-success">{!! $stats['sales_employees'] + $stats['support_employees'] !!}</h3>
                    <small class="text-muted">Team Members</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments -->
    <h5 class="fw-bold mb-3"><i class="fas fa-sitemap me-2" style="color: var(--kiboko-blue);"></i>Departments</h5>
    <div class="row mb-4">
        @if (count($departments) > 0)
            @foreach ($departments as $dept)
@php
$typeClass = strtolower($dept['organization_type']);
@endphp
            <div class="col-md-6 mb-3">
                <div class="card h-100" onclick="viewDepartment({!! $dept['id'] !!})" style="cursor: pointer;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="department-icon icon-{!! $typeClass !!}" style="width: 40px; height: 40px; margin-right: 10px;">
                                <i class="fas {!! $typeClass == 'sales' ? 'fa-chart-line' : ($typeClass == 'support' ? 'fa-tools' : 'fa-crown') !!}"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $dept['name'] }}</h6>
                                @if ($dept['code'])
                                    <small class="text-muted">{!! $dept['code'] !!}</small>
                                @endif
                            </div>
                            <span class="badge ms-auto {!! "badge-$typeClass" !!}">
                                {!! $dept['organization_type'] !!}
                            </span>
                        </div>
                        
                        @if ($dept['head_name'])
                            <p class="mb-1"><small><i class="fas fa-user-tie me-1 text-muted"></i> Head: {!! $dept['head_name'] !!}</small></p>
                        @endif
                        
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="badge bg-light text-dark">{!! $dept['employee_count'] !!} employees</span>
                            <small class="text-primary">View Details <i class="fas fa-arrow-right ms-1"></i></small>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="text-center py-4">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p>No departments found for this company</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Team Members -->
    <h5 class="fw-bold mb-3"><i class="fas fa-users me-2" style="color: var(--kiboko-blue);"></i>Team Members</h5>
    <div class="row">
        @if (count($employees) > 0)
            @foreach ($employees as $emp)
            <div class="col-md-4 mb-3">
                <div class="d-flex align-items-center p-3 border rounded" onclick='showEmployeeProfile({!! json_encode($emp) !!})' style="cursor: pointer;">
                    @if (!empty($emp['photo']))
                        <img src="{{ asset('uploads') }}/{!! $emp['photo'] !!}" class="rounded-circle me-3" width="50" height="50" style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-light me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-user text-muted"></i>
                        </div>
                    @endif
                    <div>
                        <h6 class="mb-1 fw-bold">{{ $emp['employee_name'] }}</h6>
                        <small class="text-muted d-block">{{ $emp['position'] }}</small>
                        @if ($emp['department_name'])
                            <small class="text-primary">{{ $emp['department_name'] }}</small>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
            
            @if ($stats['total_employees'] > 20)
            <div class="col-12 text-center mt-3">
                <a href="{{ url('employee-directory') }}" class="btn btn-outline-primary">
                    View All {!! $stats['total_employees'] !!} Employees
                </a>
            </div>
            @endif
        @else
            <div class="col-12">
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p>No employees found in this company</p>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .icon-sales { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .icon-support { background: linear-gradient(135deg, #10b981, #059669); }
    .icon-leadership { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
    .department-icon {
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .badge-sales { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
    .badge-support { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .badge-leadership { background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: white; }
    
    .company-logo-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        overflow: hidden;
    }
    .kel-uganda { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .kel-tanzania { background: linear-gradient(135deg, #10b981, #059669); }
    .kfs { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
    .group-leadership { background: linear-gradient(135deg, #009ada, #76bc21); }
</style>
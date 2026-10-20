<div class="container-fluid">
    <!-- Department Header -->
    <div class="text-center mb-4">
        @php
$typeClass = strtolower($dept['organization_type']);
        $icon = $typeClass == 'sales' ? 'fa-chart-line' : ($typeClass == 'support' ? 'fa-tools' : 'fa-crown');
@endphp
        <div class="department-icon icon-{!! $typeClass !!}" style="width: 80px; height: 80px; font-size: 2rem; margin: 0 auto 15px;">
            <i class="fas {!! $icon !!}"></i>
        </div>
        <h3 class="fw-bold">{{ $dept['name'] }}</h3>
        <p class="text-muted">{{ $dept['company_name'] }}</p>
        
        @if ($parentDept)
            <p class="mb-2">
                <small><i class="fas fa-level-up-alt me-1"></i> Parent: {{ $parentDept['name'] }}</small>
            </p>
        @endif
        
        <span class="badge badge-{!! $typeClass !!} mb-3" style="font-size: 1rem; padding: 8px 20px;">
            @php
if ($dept['organization_type'] == 'Sales') echo $dept['sales_division'];
            elseif ($dept['organization_type'] == 'Support') echo $dept['support_department'];
            else echo 'Leadership';
@endphp
        </span>
        
        @if ($dept['description'])
            <p class="mt-3">{{ $dept['description'] }}</p>
        @endif
        
        <!-- Quick Stats -->
        <div class="row mt-4">
            <div class="col-6">
                <div class="border rounded p-3">
                    <h5 class="fw-bold text-primary">{!! $stats['total_employees'] !!}</h5>
                    <small class="text-muted">Total Employees</small>
                </div>
            </div>
            <div class="col-6">
                <div class="border rounded p-3">
                    <h5 class="fw-bold text-success">{!! count($subDepts) !!}</h5>
                    <small class="text-muted">Sub-Departments</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Department Head -->
    @if ($dept['head_name'])
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-user-tie me-2" style="color: var(--kiboko-blue);"></i>Department Head</h5>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center">
                @if ($dept['head_photo'])
                    <img src="{{ asset('uploads') }}/{!! $dept['head_photo'] !!}" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-right: 15px;">
                @else
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-user-tie fa-2x text-muted"></i>
                    </div>
                @endif
                <div>
                    <h5 class="mb-1 fw-bold">{{ $dept['head_name'] }}</h5>
                    <p class="mb-0 text-muted">{{ $dept['head_position'] }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Sub-Departments -->
    @if (count($subDepts) > 0)
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-sitemap me-2" style="color: var(--kiboko-blue);"></i>Sub-Departments</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach ($subDepts as $sub)
@php
$subType = strtolower($sub['organization_type']);
@endphp
                <div class="col-md-6 mb-3">
                    <div class="p-3 border rounded" style="cursor: pointer;" onclick="viewDepartment({!! $sub['id'] !!})">
                        <div class="d-flex align-items-center">
                            <div class="department-icon icon-{!! $subType !!}" style="width: 40px; height: 40px; font-size: 1rem; margin-right: 10px;">
                                <i class="fas {!! $subType == 'sales' ? 'fa-chart-line' : ($subType == 'support' ? 'fa-tools' : 'fa-crown') !!}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold">{{ $sub['name'] }}</h6>
                                @if ($sub['head_name'])
                                    <small class="text-muted"><i class="fas fa-user-tie me-1"></i>{!! $sub['head_name'] !!}</small>
                                @endif
                            </div>
                            <span class="badge badge-{!! $subType !!} ms-2">
                                @php
if ($sub['organization_type'] == 'Sales') echo $sub['sales_division'];
                                elseif ($sub['organization_type'] == 'Support') echo $sub['support_department'];
                                else echo 'Leadership';
@endphp
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    
    <!-- Employees -->
    @if (count($employees) > 0)
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-users me-2" style="color: var(--kiboko-blue);"></i>Team Members</h5>
        </div>
        <div class="card-body">
            <div class="row">
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
                            <small class="text-primary">{{ $dept['company_name'] }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    
    @if (count($employees) == 0 && count($subDepts) == 0)
    <div class="text-center py-5">
        <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
        <h5>No sub-departments or employees found</h5>
        <p class="text-muted">This department doesn't have any members yet</p>
        <a href="{{ url('employee-directory') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Company
        </a>
    </div>
    @endif
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
    .badge-sales { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 5px 12px; border-radius: 30px; }
    .badge-support { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 5px 12px; border-radius: 30px; }
    .badge-leadership { background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: white; padding: 5px 12px; border-radius: 30px; }
</style>
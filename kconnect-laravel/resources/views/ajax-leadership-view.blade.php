<div class="container-fluid">
    <div class="text-center mb-4">
        @if ($level > 0)
            <h3 class="fw-bold">{!! $levelNames[$level] ?? '' !!}</h3>
            <p class="text-muted">{!! $levelDescriptions[$level] ?? '' !!}</p>
        @else
            <h3 class="fw-bold">Leadership Team</h3>
            <p class="text-muted">Directors, Executives, and CEOs across the group</p>
        @endif
    </div>

    @if (count($leaders) > 0)
        @if ($all || $level == 0)
            <!-- Show all levels -->
            @for ($lvl = 1; $lvl <= 3; $lvl++)
@php
$levelLeaders = $levelLeadersByLevel[$lvl];
@endphp
                @if (count($levelLeaders) > 0)
            <div class="mb-5">
                <h4 class="fw-bold mb-3" style="color: #8b5cf6;">{!! $levelNames[$lvl] !!}</h4>
                <div class="row">
                    @foreach ($levelLeaders as $leader)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100" onclick='showEmployeeProfile({!! json_encode($leader) !!})' style="cursor: pointer;">
                            <div class="card-body text-center">
                                @if (!empty($leader['photo']))
                                    <img src="uploads/{!! $leader['photo'] !!}" class="rounded-circle mb-3" width="100" height="100" style="object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-light mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                        <i class="fas fa-user-tie fa-3x text-muted"></i>
                                    </div>
                                @endif
                                
                                <h5 class="fw-bold">{{ $leader['employee_name'] }}</h5>
                                <p class="text-primary mb-2">{{ $leader['position'] }}</p>
                                
                                <span class="badge bg-light text-dark mb-2">
                                    {{ $leader['company_name'] ?: 'Group' }}
                                </span>
                                
                                <div>
                                    <span class="badge" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: white;">
                                        Level {!! $leader['level'] !!}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endfor
        @else
            <!-- Show single level -->
            <div class="row">
                @foreach ($leaders as $leader)
                <div class="col-md-4 mb-3">
                    <div class="card h-100" onclick='showEmployeeProfile({!! json_encode($leader) !!})' style="cursor: pointer;">
                        <div class="card-body text-center">
                            @if (!empty($leader['photo']))
                                <img src="uploads/{!! $leader['photo'] !!}" class="rounded-circle mb-3" width="100" height="100" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                    <i class="fas fa-user-tie fa-3x text-muted"></i>
                                </div>
                            @endif
                            
                            <h5 class="fw-bold">{{ $leader['employee_name'] }}</h5>
                            <p class="text-primary mb-2">{{ $leader['position'] }}</p>
                            
                            <span class="badge bg-light text-dark mb-2">
                                {{ $leader['company_name'] ?: 'Group' }}
                            </span>
                            
                            @if ($leader['reports_to'])
                                <p class="mb-0"><small class="text-muted">Reports to: {!! $leader['reports_to'] !!}</small></p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    @else
        <div class="text-center py-5">
            <i class="fas fa-crown fa-4x text-muted mb-3"></i>
            <h5>No leadership team members found</h5>
            <p class="text-muted">Check back later for updates</p>
        </div>
    @endif
</div>
@php $roleClass = strtolower($member['role'] ?? 'member'); @endphp
    <div class="member-card">
        <div class="member-actions">
            <button class="btn-icon edit" onclick='editMember({!! json_encode($member) !!})' title="Edit">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn-icon delete" onclick="deleteMember({!! $member['id'] !!})" title="Remove">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        
        @if (!empty($member['photo']))
            <img src="../uploads/employees/{{ $member['photo'] }}" class="member-avatar" alt="{{ $member['full_name'] }}">
        @else
            <div class="member-avatar-placeholder">
                {!! strtoupper(substr($member['full_name'], 0, 1)) !!}
            </div>
        @endif
        
        <h6 class="fw-bold mb-1">{{ $member['full_name'] }}</h6>
        <div class="text-muted small mb-2">{{ $member['employee_code'] }}</div>
        <div class="small mb-1"><i class="fas fa-briefcase me-1" style="color: var(--kiboko-blue);"></i>{{ $member['position'] ?? '—' }}</div>
        <div class="small text-muted mb-2"><i class="fas fa-building me-1"></i>{{ $member['department'] ?? '—' }}</div>
        
        <span class="member-role-badge {!! $roleClass !!}">
            <i class="fas fa-{!! $member['role'] == 'Chair' ? 'crown' : ($member['role'] == 'Secretary' ? 'pen' : 'user') !!} me-1"></i>
            {{ $member['role'] ?? 'Member' }}
        </span>
        
        @if ($member['status'] == 0)
            <span class="badge bg-secondary ms-2">Inactive</span>
        @endif
    </div>

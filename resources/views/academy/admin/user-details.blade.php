{{-- K-Academy admin: details of one learner, shown in the Manage Users pop-up (get-user-details) --}}
<div class="row g-3">
    <div class="col-md-6">
        <h6 class="text-secondary text-uppercase small mb-2">Learner</h6>
        <div class="fw-bold fs-5">{{ $u['full_name'] }}</div>
        <div class="text-secondary">{{ $u['employee_code'] }} &middot; {{ $u['email'] }}</div>
        <div class="mt-2">
            <span class="badge bg-{{ $u['status'] === 'active' ? 'success' : ($u['status'] === 'pending' ? 'warning' : 'secondary') }}">{{ ucfirst($u['status']) }}</span>
            <span class="badge bg-light text-dark">{{ ucfirst($u['role']) }}</span>
        </div>
    </div>
    <div class="col-md-6">
        <h6 class="text-secondary text-uppercase small mb-2">K-Connect employee</h6>
        @if ($employee)
            <div>{{ $employee['position'] ?: 'No position set' }}</div>
            <div class="text-secondary">{{ $employee['department'] ?: 'No department set' }}</div>
            <div class="mt-1"><span class="badge bg-{{ (int) $employee['status'] === 1 ? 'success' : 'secondary' }}">{{ (int) $employee['status'] === 1 ? 'Active employee' : 'Inactive employee' }}</span></div>
        @else
            <div class="text-warning">No K-Connect employee with this code or e-mail.</div>
        @endif
        <div class="small text-secondary mt-2">Last K-Academy visit: {{ $u['last_login'] ? date('M d, Y H:i', strtotime($u['last_login'])) : 'never' }}</div>
    </div>
</div>
<hr>
<h6 class="mb-2">Courses ({{ count($courses) }})</h6>
@forelse ($courses as $c)
    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
        <div>{{ $c['title'] }} <small class="text-secondary">&middot; enrolled {{ date('M d, Y', strtotime($c['enrolled_at'])) }}</small></div>
        <div style="min-width: 160px">
            <div class="progress" style="height: 6px"><div class="progress-bar bg-success" style="width: {{ (float) $c['progress_percentage'] }}%"></div></div>
            <small class="text-secondary">{{ round((float) $c['progress_percentage']) }}% &middot; {{ $c['status'] === 'completed' ? 'Completed' : 'In progress' }}</small>
        </div>
    </div>
@empty
    <div class="text-secondary">Not enrolled in any course.</div>
@endforelse
<h6 class="mt-4 mb-2">Certificates ({{ count($certificates) }})</h6>
@forelse ($certificates as $cert)
    <div class="d-flex justify-content-between border-bottom py-2">
        <div>{{ $cert['title'] }} <small class="text-secondary">{{ $cert['certificate_number'] }}</small></div>
        <span class="badge bg-{{ $cert['status'] === 'approved' ? 'success' : ($cert['status'] === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($cert['status']) }}</span>
    </div>
@empty
    <div class="text-secondary">No certificates.</div>
@endforelse
<h6 class="mt-4 mb-2">Quiz attempts</h6>
@forelse ($quizzes as $q)
    <div class="d-flex justify-content-between border-bottom py-2">
        <div>{{ $q['title'] }} <small class="text-secondary">attempt {{ $q['attempt_number'] }} &middot; {{ date('M d, Y', strtotime($q['created_at'])) }}</small></div>
        <span class="badge bg-{{ $q['passed'] ? 'success' : 'danger' }}">{{ round((float) $q['percentage']) }}%</span>
    </div>
@empty
    <div class="text-secondary">No quiz attempts.</div>
@endforelse

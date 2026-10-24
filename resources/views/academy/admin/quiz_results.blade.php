@include('academy.partials.header')
@include('academy.partials.sidebar')
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2 mb-0">Quiz Results</h1>
            <div class="text-secondary">{{ $quiz['title'] }} &middot; {{ $quiz['course_title'] }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url('academy/admin/edit_quiz') }}?id={{ $quiz['id'] }}" class="btn btn-outline-primary"><i class="bi bi-pencil"></i> Edit Quiz</a>
            <a href="{{ url('academy/admin/manage_quizzes') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> All Quizzes</a>
        </div>
    </div>
    <div class="row g-3 mb-4">
        @foreach ([['Attempts', $summary['attempts'] ?? 0, 'bi-pencil-square', 'primary'], ['People', $summary['people'] ?? 0, 'bi-people', 'info'], ['Passed', (int) ($summary['passed'] ?? 0), 'bi-check-circle', 'success'], ['Average score', ($summary['average'] ?? 0).'%', 'bi-graph-up', 'warning']] as [$label, $value, $icon, $color])
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <div class="text-{{ $color }} fs-4"><i class="bi {{ $icon }}"></i></div>
                    <div class="fs-3 fw-bold">{{ $value }}</div>
                    <div class="text-secondary small">{{ $label }}</div>
                </div></div>
            </div>
        @endforeach
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead><tr><th>Employee</th><th>Attempt</th><th>Score</th><th>Result</th><th>Date</th></tr></thead>
                    <tbody>
                    @forelse ($results as $r)
                        <tr>
                            <td><div class="fw-semibold">{{ $r['full_name'] }}</div><small class="text-secondary">{{ $r['employee_code'] }}</small></td>
                            <td>#{{ $r['attempt_number'] }}</td>
                            <td>{{ round((float) $r['percentage'], 1) }}% <small class="text-secondary">({{ $r['score'] }} pts)</small></td>
                            <td>@if ($r['passed'])<span class="badge bg-success">Passed</span>@else<span class="badge bg-danger">Not passed</span>@endif</td>
                            <td>{{ date('M d, Y H:i', strtotime($r['completed_at'] ?: $r['created_at'])) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-4">Nobody has taken this quiz yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

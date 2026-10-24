@include('academy.partials.header')
@include('academy.partials.sidebar')
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Add Quiz</h1>
        <a href="{{ url('academy/admin/manage_quizzes') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> All Quizzes</a>
    </div>
    {!! ka_flash_html() !!}
    <div class="card border-0 shadow-sm" style="max-width: 640px">
        <div class="card-body p-4">
            <p class="text-secondary">Each course has one quiz. Choose the course; you then set the pass mark, time limit, attempts and questions.</p>
            <form method="GET" action="{{ url('academy/admin/add_quiz') }}">
                <label for="course_id" class="form-label fw-semibold">Course</label>
                <select name="course_id" id="course_id" class="form-select mb-3" required>
                    <option value="">Select a course...</option>
                    @foreach ($courses as $c)
                        <option value="{{ $c['id'] }}">{{ $c['title'] }}{{ $c['status'] === 'draft' ? ' (draft)' : '' }}{{ $c['quiz_id'] ? ' - has a quiz, edit it' : '' }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary"><i class="bi bi-arrow-right-circle"></i> Continue</button>
            </form>
        </div>
    </div>
</main>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

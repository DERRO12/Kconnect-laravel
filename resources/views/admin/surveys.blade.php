<!DOCTYPE html>
<html lang="en">
<head>
    <title>Satisfaction Surveys - Admin</title>
    @include('admin.partials.survey-head')
</head>
<body>
@include('admin.partials.sidebar')
<div class="main-content">
    <div class="page-head">
        <div>
            <h1><i class="fas fa-face-smile text-success me-2"></i>Satisfaction Surveys</h1>
            <p>Each survey has its own link. Answers are anonymous: no names, emails, IP addresses or devices are recorded.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="survey-form-file" class="btn btn-outline-secondary"><i class="fas fa-download me-1"></i> Form file for the dashboard site</a>
            @if (! $setup_error)
                <button class="btn btn-k" data-bs-toggle="modal" data-bs-target="#newSurvey"><i class="fas fa-plus me-1"></i> New survey</button>
            @endif
        </div>
    </div>

    @include('admin.partials.survey-flash')

    @if ($setup_error)
        <div class="alert alert-danger">
            <strong>The survey tables could not be created automatically.</strong><br>
            {{ $setup_error }}<br>
            Please run <code>database/sql/satisfaction_surveys.sql</code> on the K-Connect database in phpMyAdmin, then reload this page.
        </div>
    @else
        @if (! count($surveys))
            <div class="card-k text-center py-5">
                <i class="fas fa-clipboard-list fa-3x mb-3" style="color:#cbd5e1"></i>
                <h2>No surveys yet</h2>
                <p class="muted mb-4">Start from the Kiboko Employee Satisfaction form (all its sections and questions are added for you), or build a new one.</p>
                <form method="post" class="d-inline">
                    <input type="hidden" name="action" value="template">
                    <button class="btn btn-k"><i class="fas fa-wand-magic-sparkles me-1"></i> Create the Employee Satisfaction survey</button>
                </form>
            </div>
        @else
            <div class="card-k p-0" style="overflow:auto">
                <table class="table align-middle mb-0" style="min-width:900px">
                    <thead class="table-light">
                        <tr><th class="ps-3" style="min-width:280px">Survey</th><th>Status</th><th class="text-center">Questions</th><th class="text-center">Responses</th><th style="width:300px">Link to share</th><th class="text-end pe-3">Actions</th></tr>
                    </thead>
                    <tbody>
                    @foreach ($surveys as $s)
                        <tr>
                            <td class="ps-3">
                                <a href="survey-edit?id={{ $s->id }}" class="fw-bold text-decoration-none text-dark">{{ $s->title }}</a>
                                <div class="muted" style="font-size:12px">Created {{ date('j M Y', strtotime($s->created_at)) }}@if ($s->last_response) · last answer {{ date('j M Y', strtotime($s->last_response)) }}@endif</div>
                            </td>
                            <td>
                                <form method="post" class="d-flex gap-1 align-items-center">
                                    <input type="hidden" name="action" value="status">
                                    <input type="hidden" name="id" value="{{ $s->id }}">
                                    <span class="badge-st st-{{ $s->status }}">{{ $s->status }}</span>
                                    <select name="status" class="form-select form-select-sm ms-1" style="width:auto" onchange="this.form.submit()" aria-label="Change status">
                                        <option value="" disabled selected>Change</option>
                                        <option value="open">Open</option>
                                        <option value="closed">Close</option>
                                        <option value="draft">Back to draft</option>
                                    </select>
                                </form>
                            </td>
                            <td class="text-center">{{ $s->question_count }}</td>
                            <td class="text-center fw-bold">{{ $s->response_count }}</td>
                            <td>
                                <div class="link-box">
                                    <input id="link{{ $s->id }}" class="form-control form-control-sm" value="{{ $s->link }}" readonly>
                                    <button type="button" class="btn btn-sm btn-outline-primary text-nowrap" onclick="kcCopy('link{{ $s->id }}', this)"><i class="fas fa-copy"></i> Copy</button>
                                </div>
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                <a href="survey-edit?id={{ $s->id }}" class="btn btn-sm btn-outline-secondary" title="Edit questions"><i class="fas fa-pen"></i> Edit</a>
                                <a href="survey-results?id={{ $s->id }}" class="btn btn-sm btn-outline-success" title="Results and export"><i class="fas fa-chart-column"></i> Results</a>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-label="More"><i class="fas fa-ellipsis"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <form method="post">
                                                <input type="hidden" name="action" value="duplicate">
                                                <input type="hidden" name="id" value="{{ $s->id }}">
                                                <button class="dropdown-item"><i class="fas fa-clone me-2"></i>Duplicate (new link)</button>
                                            </form>
                                        </li>
                                        <li><a class="dropdown-item" href="{{ url('satisfaction') }}?s={{ rawurlencode($s->slug) }}" target="_blank" rel="noopener"><i class="fas fa-eye me-2"></i>Preview form</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#del{{ $s->id }}"><i class="fas fa-trash me-2"></i>Delete…</button></li>
                                    </ul>
                                </div>
                                <div class="modal fade text-start" id="del{{ $s->id }}" tabindex="-1">
                                    <div class="modal-dialog"><form method="post" class="modal-content">
                                        <div class="modal-header"><h5 class="modal-title">Delete this survey?</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                        <div class="modal-body text-wrap">
                                            <p>This deletes <strong>{{ $s->title }}</strong>, its questions and all <strong>{{ $s->response_count }}</strong> responses. It cannot be undone. Export the results first if you need them.</p>
                                            <label class="form-label">Type the survey title to confirm</label>
                                            <input name="confirm_title" class="form-control" autocomplete="off" required>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="{{ $s->id }}">
                                        </div>
                                        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-danger">Delete</button></div>
                                    </form></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <form method="post">
                <input type="hidden" name="action" value="template">
                <button class="btn btn-link px-0"><i class="fas fa-wand-magic-sparkles me-1"></i> Create another survey from the Employee Satisfaction form</button>
            </form>
        @endif

        <div class="card-k mt-3">
            <h2><i class="fas fa-circle-info text-primary me-1"></i> How it works</h2>
            <ol class="muted mb-0 ps-3">
                <li>Create a survey, check its questions, then set it to <strong>Open</strong>.</li>
                <li>Copy its link and share it with staff. The form is on dashboard.kibokogroup.com and reads its questions from K-Connect.</li>
                <li>Watch the answers on <strong>Results</strong> and export them to Excel. Closing a survey stops new answers but keeps the results.</li>
            </ol>
        </div>

        <div class="modal fade" id="newSurvey" tabindex="-1">
            <div class="modal-dialog"><form method="post" class="modal-content">
                <div class="modal-header"><h5 class="modal-title">New survey</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3"><label class="form-label">Title</label><input name="title" class="form-control" maxlength="200" required placeholder="e.g. Q1 2027 Pulse Survey"></div>
                    <div class="mb-1"><label class="form-label">Introduction shown at the top of the form (optional)</label><textarea name="intro" class="form-control" rows="4"></textarea></div>
                    <p class="muted mt-3 mb-0">Tip: to start with all the Employee Satisfaction questions, close this and use “Create from the Employee Satisfaction form”.</p>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-k">Create and add questions</button></div>
            </form></div>
        </div>
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

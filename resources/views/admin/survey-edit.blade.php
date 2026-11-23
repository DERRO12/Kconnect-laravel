<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Survey - {{ $survey->title }}</title>
    @include('admin.partials.survey-head')
    <style>
        .section-block { background:#fff; border:1px solid #eef2f6; border-radius:18px; margin-bottom:18px; overflow:hidden; }
        .section-head { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:14px 18px; background:linear-gradient(135deg, rgba(0,154,218,.07), rgba(118,188,33,.07)); border-bottom:1px solid #eef2f6; }
        .section-head h3 { font-size:15px; font-weight:700; margin:0; }
        .q-row { display:flex; gap:12px; align-items:flex-start; padding:12px 18px; border-bottom:1px solid #f1f5f9; background:#fff; }
        .q-row:last-child { border-bottom:0; }
        .q-row .handle { cursor:grab; color:#94a3b8; padding-top:3px; }
        .q-row .q-text { flex:1; min-width:0; }
        .q-row .q-text .t { font-weight:600; font-size:14px; }
        .q-meta { font-size:12px; color:#64748b; margin-top:3px; }
        .q-meta .pill { background:#f1f5f9; border-radius:10px; padding:2px 8px; margin-right:4px; display:inline-block; }
        .q-row.sortable-ghost { opacity:.4; background:#e0f2fe; }
        .q-row.archived { opacity:.7; }
        .req { color:#dc2626; font-weight:700; }
    </style>
</head>
<body>
@include('admin.partials.sidebar')
<div class="main-content">
    <div class="crumb"><a href="surveys"><i class="fas fa-arrow-left"></i> Satisfaction Surveys</a></div>
    <div class="page-head">
        <div>
            <h1>{{ $survey->title }} <span class="badge-st st-{{ $survey->status }} align-middle ms-1">{{ $survey->status }}</span></h1>
            <p>{{ count(array_filter($questions, fn ($q) => (bool) $q->is_active)) }} questions on the form · {{ $response_count }} responses so far</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ $preview_link }}" target="_blank" rel="noopener" class="btn btn-outline-secondary"><i class="fas fa-eye me-1"></i> Preview</a>
            <a href="survey-results?id={{ $survey->id }}" class="btn btn-outline-success"><i class="fas fa-chart-column me-1"></i> Results</a>
            <button class="btn btn-k" onclick="openQuestion(null)"><i class="fas fa-plus me-1"></i> Add question</button>
        </div>
    </div>

    @include('admin.partials.survey-flash')

    @if ($survey->status !== 'open')
        <div class="alert alert-warning py-2"><i class="fas fa-circle-info me-1"></i> This survey is <strong>{{ $survey->status }}</strong>, so the link does not accept answers. Set the status to <strong>Open</strong> below when it is ready.</div>
    @endif
    @if ($response_count)
        <div class="alert alert-info py-2"><i class="fas fa-circle-info me-1"></i> People have already answered. You can still edit; answers already given keep the question wording they were answered with.</div>
    @endif

    <div class="row g-3">
        <div class="col-xl-8 order-2 order-xl-1">
            @php
                $active = array_values(array_filter($questions, fn ($q) => $q->is_active));
                $archived = array_values(array_filter($questions, fn ($q) => ! $q->is_active));
            @endphp
            @if (! count($active))
                <div class="card-k text-center py-5">
                    <p class="muted mb-3">No questions yet.</p>
                    <button class="btn btn-k" onclick="openQuestion(null)"><i class="fas fa-plus me-1"></i> Add the first question</button>
                </div>
            @endif
            @foreach ($sections as $si => $section)
                <div class="section-block">
                    <div class="section-head">
                        <h3>{{ $section !== '' ? $section : 'Questions without a section' }}</h3>
                        <div class="text-nowrap">
                            @if ($section !== '')
                                <button class="btn btn-sm btn-light" onclick="renameSection({{ json_encode($section) }})" title="Rename section"><i class="fas fa-i-cursor"></i></button>
                            @endif
                            <button class="btn btn-sm btn-light" onclick="openQuestion(null, {{ json_encode($section) }})" title="Add a question to this section"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="q-list">
                        @foreach ($active as $q)
                            @if ((string) $q->section === $section)
                                <div class="q-row" id="q{{ $q->id }}" data-id="{{ $q->id }}">
                                    <span class="handle" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></span>
                                    <div class="q-text">
                                        <div class="t">{{ $q->question }} @if ($q->required)<span class="req" title="Required">*</span>@endif</div>
                                        <div class="q-meta">
                                            <span class="pill">{{ $types[$q->type] ?? $q->type }}</span>
                                            @if (in_array($q->type, ['single', 'multiple', 'dropdown']))
                                                <span>{{ \Illuminate\Support\Str::limit(implode(' · ', \App\Support\Surveys::options($q->options)), 120) }}</span>
                                            @endif
                                            @if ($q->help_text)<div class="fst-italic">{{ $q->help_text }}</div>@endif
                                            @if (! empty($answer_counts[$q->id]))<div>{{ $answer_counts[$q->id] }} answers</div>@endif
                                        </div>
                                    </div>
                                    <div class="text-nowrap">
                                        <button class="btn btn-sm btn-outline-secondary" onclick="openQuestion({{ $q->id }})" title="Edit"><i class="fas fa-pen"></i></button>
                                        <form method="post" class="d-inline" onsubmit="return confirm({{ json_encode(! empty($answer_counts[$q->id]) ? 'Remove this question from the form? Its earlier answers stay in the results.' : 'Delete this question?') }})">
                                            <input type="hidden" name="action" value="remove_question">
                                            <input type="hidden" name="question_id" value="{{ $q->id }}">
                                            <button class="btn btn-sm btn-outline-danger" title="Remove"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach

            @if (count($archived))
                <div class="section-block">
                    <div class="section-head"><h3 class="text-secondary"><i class="fas fa-box-archive me-1"></i> Removed questions (kept for their earlier answers)</h3></div>
                    @foreach ($archived as $q)
                        <div class="q-row archived">
                            <div class="q-text">
                                <div class="t">{{ $q->question }}</div>
                                <div class="q-meta">{{ $q->section }} · {{ $answer_counts[$q->id] ?? 0 }} answers</div>
                            </div>
                            <form method="post">
                                <input type="hidden" name="action" value="restore_question">
                                <input type="hidden" name="question_id" value="{{ $q->id }}">
                                <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-rotate-left me-1"></i> Put back</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="col-xl-4 order-1 order-xl-2">
            <div class="card-k">
                <h2><i class="fas fa-link text-primary me-1"></i> Link to share</h2>
                <div class="link-box mb-2">
                    <input id="surveyLink" class="form-control form-control-sm" value="{{ $link }}" readonly>
                    <button type="button" class="btn btn-sm btn-outline-primary text-nowrap" onclick="kcCopy('surveyLink', this)"><i class="fas fa-copy"></i> Copy</button>
                </div>
                <div class="muted" style="font-size:12px">Anyone with this link can answer while the survey is open. Nothing identifies who answered.</div>
            </div>
            <form method="post" class="card-k">
                <h2><i class="fas fa-sliders text-primary me-1"></i> Survey settings</h2>
                <input type="hidden" name="action" value="settings">
                <div class="mb-3"><label class="form-label">Title</label><input name="title" class="form-control" maxlength="200" value="{{ $survey->title }}" required></div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach ($statuses as $k => $label)
                            <option value="{{ $k }}" @selected($survey->status === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Introduction (top of the form)</label><textarea name="intro" class="form-control" rows="6">{{ $survey->intro }}</textarea></div>
                <div class="mb-3"><label class="form-label">Thank-you message</label><textarea name="thank_you" class="form-control" rows="3">{{ $survey->thank_you }}</textarea></div>
                <button class="btn btn-k w-100">Save settings</button>
            </form>
        </div>
    </div>
</div>

{{-- Add / edit question --}}
<div class="modal fade" id="qModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><form method="post" class="modal-content" id="qForm">
        <div class="modal-header"><h5 class="modal-title" id="qTitle">Add question</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="save_question">
            <input type="hidden" name="question_id" id="qId">
            <div class="mb-3">
                <label class="form-label">Section</label>
                <input name="section" id="qSection" class="form-control" list="sectionList" maxlength="150" placeholder="e.g. Section A: Job Satisfaction">
                <datalist id="sectionList">@foreach ($sections as $s)<option value="{{ $s }}">@endforeach</datalist>
                <div class="muted mt-1" style="font-size:12px">Pick an existing section or type a new one. Questions with the same section are shown together.</div>
            </div>
            <div class="mb-3"><label class="form-label">Question</label><textarea name="question" id="qText" class="form-control" rows="2" required></textarea></div>
            <div class="row g-3">
                <div class="col-md-7">
                    <label class="form-label">Answer type</label>
                    <select name="type" id="qType" class="form-select" onchange="toggleOptions()">
                        @foreach ($types as $k => $label)<option value="{{ $k }}">{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-5 d-flex align-items-end">
                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="required" value="1" id="qReq"><label class="form-check-label" for="qReq">Answer required</label></div>
                </div>
            </div>
            <div class="mt-3" id="optWrap">
                <label class="form-label">Options (one per line)</label>
                <textarea name="options" id="qOptions" class="form-control" rows="5"></textarea>
            </div>
            <div class="mt-3" id="likertNote"><span class="muted">Shown as: {{ implode(' · ', \App\Support\Surveys::LIKERT_LABELS) }}</span></div>
            <div class="mt-3"><label class="form-label">Help text (optional, small grey text under the question)</label><input name="help_text" id="qHelp" class="form-control" maxlength="255"></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-k">Save question</button></div>
    </form></div>
</div>

<form method="post" id="renameForm" class="d-none">
    <input type="hidden" name="action" value="rename_section">
    <input type="hidden" name="from" id="renameFrom">
    <input type="hidden" name="to" id="renameTo">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
@php
    $qjs = [];
    foreach ($active as $q) {
        $qjs[$q->id] = ['section' => (string) $q->section, 'question' => $q->question, 'help' => (string) $q->help_text, 'type' => $q->type, 'options' => (string) $q->options, 'required' => (bool) $q->required];
    }
@endphp
var QUESTIONS = {!! json_encode((object) $qjs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!};
var qModal;

function toggleOptions() {
    var t = document.getElementById('qType').value;
    document.getElementById('optWrap').style.display = ['single', 'multiple', 'dropdown'].indexOf(t) >= 0 ? '' : 'none';
    document.getElementById('likertNote').style.display = t === 'likert' ? '' : 'none';
}

function openQuestion(id, section) {
    var q = id ? QUESTIONS[id] : null;
    document.getElementById('qTitle').textContent = q ? 'Edit question' : 'Add question';
    document.getElementById('qId').value = id || '';
    document.getElementById('qSection').value = q ? q.section : (section !== undefined ? section : '');
    document.getElementById('qText').value = q ? q.question : '';
    document.getElementById('qType').value = q ? q.type : 'likert';
    document.getElementById('qOptions').value = q ? q.options : '';
    document.getElementById('qHelp').value = q ? q.help : '';
    document.getElementById('qReq').checked = q ? q.required : true;
    toggleOptions();
    qModal = qModal || new bootstrap.Modal(document.getElementById('qModal'));
    qModal.show();
}

function renameSection(name) {
    var to = prompt('New name for this section:', name);
    if (to === null || to.trim() === '' || to === name) return;
    document.getElementById('renameFrom').value = name;
    document.getElementById('renameTo').value = to.trim();
    document.getElementById('renameForm').submit();
}

function saveOrder() {
    var body = new URLSearchParams();
    body.append('action', 'reorder');
    document.querySelectorAll('.q-list .q-row').forEach(function (r) { body.append('order[]', r.dataset.id); });
    var token = document.querySelector('meta[name="csrf-token"]') || document.querySelector('input[name="_token"]');
    if (token) body.append('_token', token.content || token.value);
    fetch(location.href, { method: 'POST', body: body, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(function (r) { if (!r.ok) throw 0; })
        .catch(function () { alert('The new order could not be saved. Please reload the page and try again.'); });
}

document.querySelectorAll('.q-list').forEach(function (list) {
    new Sortable(list, { handle: '.handle', animation: 150, onEnd: saveOrder });
});
</script>
</body>
</html>

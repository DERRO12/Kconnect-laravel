<!DOCTYPE html>
<html lang="en">
<head>
    <title>Survey Results - {{ $survey->title }}</title>
    @include('admin.partials.survey-head')
    <style>
        .q-card { background:#fff; border:1px solid #eef2f6; border-radius:16px; padding:16px 18px; margin-bottom:12px; }
        .q-card .t { font-weight:600; font-size:14px; margin-bottom:10px; }
        .bar-row { display:flex; align-items:center; gap:10px; font-size:12px; margin-bottom:5px; }
        .bar-row .lab { width:150px; flex-shrink:0; color:#475569; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .bar-row .track { flex:1; background:#f1f5f9; border-radius:6px; height:14px; overflow:hidden; }
        .bar-row .fill { height:100%; background:var(--grad); border-radius:6px; }
        .bar-row .val { width:70px; text-align:right; color:#0f172a; font-weight:600; }
        .score { font-size:12px; font-weight:700; padding:4px 10px; border-radius:12px; background:#f1f5f9; }
        .score.good { background:#dcfce7; color:#15803d; }
        .score.mid { background:#fef9c3; color:#a16207; }
        .score.low { background:#fee2e2; color:#b91c1c; }
        .texts { max-height:320px; overflow:auto; }
        .texts div { background:#f8fafc; border-radius:10px; padding:8px 12px; margin-bottom:6px; font-size:13px; white-space:pre-wrap; }
        .sec-title { font-weight:800; font-size:16px; margin:22px 0 10px; }
        @media (max-width: 576px) { .bar-row .lab { width:95px; } }
    </style>
</head>
<body>
@include('admin.partials.sidebar')
@php
    $scoreClass = fn ($v) => $v === null ? '' : ($v >= 4 ? 'good' : ($v >= 3 ? 'mid' : 'low'));
    $qs = http_build_query(array_filter(['id' => $survey->id, 'from' => $from, 'to' => $to]));
    $allLikert = array_filter($summary, fn ($i) => isset($i['avg']) && $i['avg'] !== null);
    $overall = count($allLikert) ? round(array_sum(array_map(fn ($i) => $i['avg'], $allLikert)) / count($allLikert), 2) : null;
@endphp
<div class="main-content">
    <div class="crumb"><a href="surveys"><i class="fas fa-arrow-left"></i> Satisfaction Surveys</a> · <a href="survey-edit?id={{ $survey->id }}">Edit questions</a></div>
    <div class="page-head">
        <div>
            <h1>Results: {{ $survey->title }}</h1>
            <p>All answers are anonymous. Each response only records the date it was sent.</p>
        </div>
        <a href="survey-results?{{ $qs }}&export=csv" class="btn btn-k"><i class="fas fa-file-excel me-1"></i> Export to Excel (CSV)</a>
    </div>

    @include('admin.partials.survey-flash')

    <form method="get" class="card-k d-flex flex-wrap gap-2 align-items-end py-3">
        <input type="hidden" name="id" value="{{ $survey->id }}">
        <div><label class="form-label mb-1">From</label><input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm"></div>
        <div><label class="form-label mb-1">To</label><input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm"></div>
        <button class="btn btn-sm btn-outline-primary">Filter</button>
        @if ($from || $to)<a href="survey-results?id={{ $survey->id }}" class="btn btn-sm btn-light">Clear</a>@endif
    </form>

    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3"><div class="stat"><div class="n">{{ $total }}</div><div class="l">Responses</div></div></div>
        <div class="col-6 col-md-3"><div class="stat"><div class="n">{{ $overall !== null ? number_format($overall, 2) : '–' }}</div><div class="l">Overall average (out of 5)</div></div></div>
        <div class="col-6 col-md-3"><div class="stat"><div class="n">{{ count($summary) }}</div><div class="l">Questions</div></div></div>
        <div class="col-6 col-md-3"><div class="stat"><div class="n"><span class="badge-st st-{{ $survey->status }}">{{ $survey->status }}</span></div><div class="l">Status</div></div></div>
    </div>

    @if (! $total)
        <div class="card-k text-center py-5 muted">No responses {{ ($from || $to) ? 'in these dates' : 'yet' }}.</div>
    @else
        @if (count($sections))
            <div class="card-k">
                <h2>Average score by section <span class="muted fw-normal">(1 = Strongly Disagree, 5 = Strongly Agree)</span></h2>
                @foreach ($sections as $name => $avg)
                    <div class="bar-row">
                        <span class="lab" title="{{ $name }}" style="width:260px">{{ $name ?: 'Other' }}</span>
                        <span class="track"><span class="fill d-block" style="width:{{ $avg * 20 }}%"></span></span>
                        <span class="val"><span class="score {{ $scoreClass($avg) }}">{{ number_format($avg, 2) }}</span></span>
                    </div>
                @endforeach
            </div>
        @endif

        @php($lastSection = null)
        @foreach ($summary as $item)
            @php($q = $item['q'])
            @if ((string) $q->section !== $lastSection)
                @php($lastSection = (string) $q->section)
                @if ($lastSection !== '')<div class="sec-title">{{ $lastSection }}</div>@endif
            @endif
            <div class="q-card">
                <div class="d-flex justify-content-between gap-2">
                    <div class="t">{{ $q->question }} @if (! $q->is_active)<span class="badge bg-secondary ms-1">removed</span>@endif</div>
                    <div class="text-nowrap muted">
                        {{ $item['count'] }} answers
                        @if (isset($item['avg']) && $item['avg'] !== null)
                            · <span class="score {{ $scoreClass($item['avg']) }}">avg {{ number_format($item['avg'], 2) }}</span>
                            · {{ $item['agree'] }}% agree
                        @endif
                    </div>
                </div>
                @if (isset($item['dist']))
                    @foreach ($item['dist'] as $v => $n)
                        <div class="bar-row">
                            <span class="lab">{{ $v }}. {{ $likert[$v - 1] }}</span>
                            <span class="track"><span class="fill d-block" style="width:{{ $item['count'] ? round($n * 100 / $item['count']) : 0 }}%"></span></span>
                            <span class="val">{{ $n }} ({{ $item['count'] ? round($n * 100 / $item['count']) : 0 }}%)</span>
                        </div>
                    @endforeach
                @elseif (isset($item['counts']))
                    @foreach ($item['counts'] as $opt => $n)
                        <div class="bar-row">
                            <span class="lab" title="{{ $opt }}">{{ $opt }}</span>
                            <span class="track"><span class="fill d-block" style="width:{{ $item['count'] ? round($n * 100 / $item['count']) : 0 }}%"></span></span>
                            <span class="val">{{ $n }}</span>
                        </div>
                    @endforeach
                @elseif (count($item['texts'] ?? []))
                    <div class="texts">
                        @foreach ($item['texts'] as $t)<div>{{ $t }}</div>@endforeach
                    </div>
                    @if ($item['count'] > count($item['texts']))<div class="muted mt-1">Showing the latest {{ count($item['texts']) }}. Export to see all.</div>@endif
                @else
                    <div class="muted">No answers.</div>
                @endif
            </div>
        @endforeach
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

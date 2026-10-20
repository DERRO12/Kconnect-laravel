@use('App\Support\ItHelpers', 'H')

    <div class="p-3">
        <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
            <div><h5 class="fw-bold mb-1">#{{ $ticket['ticket_number'] }}</h5><p class="text-muted small mb-0">{{ $ticket['subject'] }}</p><small class="text-muted">By: {{ $ticket['employee_name'] }}</small></div>
            <div class="d-flex gap-2">
                <span class="badge bg-{!! $ticket['status']=='New'?'danger':($ticket['status']=='In Progress'?'primary':($ticket['status']=='Resolved'?'success':'secondary')) !!}">{!! $ticket['status'] !!}</span>
                <span class="badge bg-{!! $ticket['priority']=='Urgent'?'danger':($ticket['priority']=='High'?'warning':'info') !!}">{!! $ticket['priority'] !!}</span>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-8">
                <div class="card mb-2"><div class="card-header py-2"><strong>Ticket Info</strong></div><div class="card-body py-2 small">
                    <div><strong>Category:</strong> {{ $ticket['category'] }} | <strong>Created:</strong> {!! H::formatTicketTime($ticket['created_at']) !!}</div>
                    <div class="mt-2 bg-light p-2 rounded">{!! nl2br(htmlspecialchars($ticket['description'])) !!}</div>
                </div></div>
                @if (!empty($tAtts))<div class="card mb-2"><div class="card-header py-2"><strong>Attachments</strong></div><div class="card-body py-2"><div class="d-flex flex-wrap gap-2">@foreach ($tAtts as $a)@php $isImg=H::isImageFile($a['file_path']); $ex=H::fileExistsCheck($a['file_path']); $url=H::getFileUrl($a['file_path']); @endphp @if ($isImg&&$ex)<img src="{{ $url }}" style="max-width:80px;max-height:60px;border-radius:6px;cursor:pointer;" onclick="openImageModal('{{ $url }}')">@elseif ($ex)<a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-secondary">{{ $a['file_name'] }}</a>@endif @endforeach</div></div></div>@endif
                <div class="card mb-2"><div class="card-header py-2"><strong>Conversation</strong></div><div class="card-body py-2">
                    @if ($comments && count($comments)>0) @foreach ($comments as $c) @php $ci=$c['id']; $ca=$byC[$ci]??[]; $ia=$c['is_admin']??0; @endphp
                        <div class="border-bottom pb-2 mb-2"><strong>{{ $c['full_name'] }}</strong>@if ($ia)<span class="badge bg-primary ms-1">Admin</span>@endif<br><small class="text-muted">{!! H::formatTicketTime($c['created_at']) !!}</small><p class="mb-0 mt-1">{!! nl2br(htmlspecialchars($c['comment'])) !!}</p></div>
                    @endforeach @else<p class="text-muted small">No comments yet.</p>@endif
                    <form method="POST" action="it-support.php" enctype="multipart/form-data">
@csrf
                        <input type="hidden" name="ticket_id" value="{!! $tid !!}"><input type="hidden" name="form_token" value="{!! $form_token !!}">
                        <textarea name="comment" class="form-control form-control-sm mb-1" rows="2" required placeholder="Reply..." id="commentTextarea_{!! $tid !!}"></textarea>
                        <input type="hidden" name="pasted_images" id="pasted_images_{!! $tid !!}" value="[]"><div class="paste-preview-container" id="paste_preview_{!! $tid !!}" style="display:none;"></div>
                        <div class="d-flex gap-1"><input type="file" name="comment_attachments[]" class="form-control form-control-sm" multiple><button type="submit" name="add_comment" class="btn btn-primary btn-sm">Send</button></div>
                    </form>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card mb-2"><div class="card-header py-2"><strong>Timeline</strong></div><div class="card-body py-2 small">
                    <div>Created: {!! H::formatTicketTime($ticket['created_at']) !!}</div>
                    @if ($ticket['resolved_at'])<div>Resolved: {!! H::formatTicketTime($ticket['resolved_at']) !!}</div>@endif
                    @if ($ticket['closed_at'])<div>Closed: {!! H::formatTicketTime($ticket['closed_at']) !!}</div>@endif
                </div></div>
                @if ($hist&&count($hist)>0)
                <div class="card mb-2"><div class="card-header py-2"><strong>History</strong></div><div class="card-body py-2 small">@foreach ($hist as $h)<div>@if ($h['from_status'])<span class="badge bg-secondary">{!! $h['from_status'] !!}</span> → @endif<span class="badge bg-primary">{!! $h['to_status'] !!}</span> <small class="text-muted">{!! H::formatTicketTime($h['created_at']) !!}</small></div>@endforeach</div></div>
                @endif
            </div>
        </div>
    </div>
    <script>initReplyPasteSupport({!! $tid !!});</script>

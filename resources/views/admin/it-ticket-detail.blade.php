<div class="ticket-detail-container">
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">#{{ $ticket['ticket_number'] }}</h3>
            <p class="text-muted mb-0">{{ $ticket['subject'] }}</p>
        </div>
        <div class="d-flex gap-2">
            <span class="status-badge status-{!! str_replace(' ', '-', $ticket['status']) !!} px-3 py-2">
                {!! $ticket['status'] !!}
            </span>
            <span class="priority-badge priority-{!! $ticket['priority'] !!} px-3 py-2">
                {!! $ticket['priority'] !!}
            </span>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-8">
            <!-- Ticket Information -->
            <div class="ticket-detail-section">
                <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2" style="color: #009ada;"></i>Ticket Information</h6>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Category</small>
                        <strong>{!! $ticket['category'] !!}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Submitted By</small>
                        <strong>{{ $ticket['employee_name'] }}</strong>
                        <small class="text-muted d-block">{!! $ticket['employee_code'] !!} | {{ $ticket['department'] ?? 'N/A' }}</small>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Created</small>
                        <strong>{!! date('F d, Y \a\t h:i A', strtotime($ticket['created_at'])) !!}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Last Updated</small>
                        <strong>{!! date('F d, Y \a\t h:i A', strtotime($ticket['updated_at'])) !!}</strong>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Description</small>
                        <div class="bg-light p-3 rounded-3 mt-1">
                            {!! nl2br(htmlspecialchars($ticket['description'])) !!}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Dynamic Answers -->
            @if (count($answers) > 0)
                <div class="ticket-detail-section">
                    <h6 class="fw-bold mb-3"><i class="fas fa-question-circle me-2" style="color: #76bc21;"></i>Additional Information</h6>
                    @foreach ($answers as $ans)
                        <div class="mb-3 pb-2 border-bottom">
                            <small class="text-muted d-block">{{ $ans['question_text'] }}</small>
                            @if ($ans['question_type'] == 'file' && !empty($ans['answer']) && \App\Support\ItHelpers::fileExistsCheck($ans['answer'], 'admin'))
                                <a href="../{{ \App\Support\ItHelpers::getFileUrl($ans['answer']) }}" class="btn btn-sm btn-outline-primary mt-1" target="_blank">
                                    <i class="fas fa-download me-1"></i>View Attachment
                                </a>
                            @else
                                <strong>{!! nl2br(htmlspecialchars($ans['answer'])) !!}</strong>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
            
            <!-- Comments Section -->
            <div class="ticket-detail-section">
                <h6 class="fw-bold mb-3"><i class="fas fa-comments me-2" style="color: #009ada;"></i>Conversation</h6>
                @if (count($comments) > 0)
                    @foreach ($comments as $comment)
                        <div class="comment-item {!! $comment['is_admin'] ? 'admin-comment' : '' !!}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    @if (!empty($comment['photo']))
                                        <img src="../uploads/employees/{{ $comment['photo'] }}" class="rounded-circle" width="30" height="30" style="object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-gradient-light d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                            <i class="fas fa-user fa-sm text-gradient"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $comment['full_name'] }}</strong>
                                        @if ($comment['is_admin'])
                                            <span class="badge bg-primary ms-1">Admin</span>
                                        @endif
                                        <small class="text-muted d-block">{!! date('M d, Y h:i A', strtotime($comment['created_at'])) !!}</small>
                                    </div>
                                </div>
                            </div>
                            <p class="mb-0">{!! nl2br(htmlspecialchars($comment['comment'])) !!}</p>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center py-3">No comments yet.</p>
                @endif
                
                <!-- Add Comment Form -->
                <form method="POST" action="it-support" class="mt-4">
@csrf
                    <input type="hidden" name="ticket_id" value="{!! $ticket_id !!}">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Add Reply</label>
                        <textarea name="comment" class="form-control" rows="3" required placeholder="Type your message here..."></textarea>
                    </div>
                    <button type="submit" name="add_comment" class="btn-gradient btn-sm">Send Reply</button>
                </form>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Status Timeline -->
            <div class="ticket-detail-section">
                <h6 class="fw-bold mb-3"><i class="fas fa-chart-line me-2" style="color: #76bc21;"></i>Ticket Timeline</h6>
                <div class="timeline-item mb-3 pb-2 border-bottom">
                    <small class="text-muted">Created</small>
                    <div class="fw-semibold">{!! date('M d, Y h:i A', strtotime($ticket['created_at'])) !!}</div>
                </div>
                @if ($ticket['resolved_at'])
                <div class="timeline-item mb-3 pb-2 border-bottom">
                    <small class="text-muted">Resolved</small>
                    <div class="fw-semibold">{!! date('M d, Y h:i A', strtotime($ticket['resolved_at'])) !!}</div>
                </div>
                @endif
                @if ($ticket['closed_at'])
                <div class="timeline-item mb-3">
                    <small class="text-muted">Closed</small>
                    <div class="fw-semibold">{!! date('M d, Y h:i A', strtotime($ticket['closed_at'])) !!}</div>
                </div>
                @endif
            </div>
            
            <!-- Admin Notes -->
            @if (!empty($ticket['admin_notes']))
            <div class="ticket-detail-section">
                <h6 class="fw-bold mb-3"><i class="fas fa-sticky-note me-2" style="color: #f59e0b;"></i>Admin Notes</h6>
                <div class="bg-light p-3 rounded-3">
                    {!! nl2br(htmlspecialchars($ticket['admin_notes'])) !!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.ticket-detail-section {
    background: #f8fafc;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
}
.comment-item {
    background: white;
    border-radius: 16px;
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid #eef2f6;
}
.admin-comment {
    border-left: 4px solid #009ada;
    background: #f0f9ff;
}
.status-badge, .priority-badge {
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
}
.status-New { background: #fee2e2; color: #dc2626; }
.status-In-Progress { background: #dbeafe; color: #2563eb; }
.status-Resolved { background: #dcfce7; color: #16a34a; }
.status-Closed { background: #f3f4f6; color: #6b7280; }
.priority-Low { background: #f3f4f6; color: #6b7280; }
.priority-Medium { background: #dbeafe; color: #2563eb; }
.priority-High { background: #fef3c7; color: #d97706; }
.priority-Urgent { background: #fee2e2; color: #dc2626; }
</style>
@use('App\Support\ItHelpers', 'H')
<div class="p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">#{{ $ticket['ticket_number'] }}</h4>
            <p class="text-muted mb-0">{{ $ticket['subject'] }}</p>
            <small class="text-muted">Submitted by: {{ $ticket['employee_name'] }}</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <span class="status-badge status-{!! str_replace(' ', '-', $ticket['status']) !!} px-3 py-2">
                {!! $ticket['status'] !!}
            </span>
            <span class="priority-badge priority-{!! $ticket['priority'] !!} px-3 py-2">
                {!! $ticket['priority'] !!}
            </span>
            @if ($ticket['assigned_to_name'])
                <span class="badge bg-primary px-3 py-2">
                    <i class="fas fa-user-check me-1"></i>{{ $ticket['assigned_to_name'] }}
                </span>
            @else
                <span class="badge bg-warning px-3 py-2">Not Assigned</span>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <!-- Ticket Info -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-info-circle me-2 text-primary"></i>Ticket Information
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted">Category</small>
                            <div><strong>{{ $ticket['category'] }}</strong></div>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted">Submitted By</small>
                            <div><strong>{{ $ticket['employee_name'] }}</strong></div>
                            <small class="text-muted">{{ $ticket['employee_code'] }} | {{ $ticket['department'] ?? 'N/A' }}</small>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted">Created</small>
                            <div><strong>{!! H::formatTicketTime($ticket['created_at']) !!}</strong></div>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted">Last Updated</small>
                            <div><strong>{!! H::formatTicketTime($ticket['updated_at']) !!}</strong></div>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Description</small>
                            <div class="bg-light p-3 rounded mt-1">
                                {!! nl2br(htmlspecialchars($ticket['description'])) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ticket Attachments - Display Images Directly -->
            @if ($ticket_attachment_count > 0)
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-paperclip me-2 text-success"></i>Attachments ({!! $ticket_attachment_count !!})
                    @if ($image_count > 0)
                        <span class="badge bg-info ms-2"><i class="fas fa-image me-1"></i>{!! $image_count !!} images</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach ($ticket_attachments as $att)
                            @php
                            $filePath = $att['file_path'];
                            $fileName = $att['file_name'];
                            $fileSize = $att['file_size'];
                            $isImage = H::isImageFile($filePath);
                            $fileExists = H::fileExistsCheck($filePath);
                            $fileUrl = H::getFileUrl($filePath);
                            @endphp
                            <div class="col-md-6 col-lg-4">
                                @if ($isImage && $fileExists)
                                    <!-- Image displayed directly -->
                                    <div class="ticket-image-card" onclick="openImageModal('{{ $fileUrl }}', '{{ $fileName }}')">
                                        <img src="{{ $fileUrl }}" 
                                             class="ticket-image-display" 
                                             alt="{{ $fileName }}"
                                             loading="lazy">
                                        <div class="ticket-image-overlay">
                                            <i class="fas fa-search-plus"></i>
                                            <span class="ticket-image-filename">{{ $fileName }}</span>
                                        </div>
                                    </div>
                                @elseif ($fileExists)
                                    <div class="file-item">
                                        <div class="file-icon">{!! H::getFileIconHtml($filePath) !!}</div>
                                        <div class="file-info">
                                            <div class="file-name text-truncate">{{ $fileName }}</div>
                                            <small class="text-muted">{!! H::formatFileSize($fileSize) !!}</small>
                                        </div>
                                        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                @else
                                    <span class="badge bg-warning">File missing{!! $missingName ? ': '.e($fileName) : '' !!}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Dynamic Answers -->
            @if ($answers && count($answers) > 0)
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-question-circle me-2 text-info"></i>Additional Information
                </div>
                <div class="card-body">
                    @foreach ($answers as $ans)
                        @php $isEmpty = empty(trim($ans['answer'])); @endphp
                        @continue ($isEmpty)
                        <div class="mb-3 pb-2 border-bottom">
                            <small class="text-muted fw-bold">{{ $ans['question_text'] }}</small>
                            
                            @if ($ans['question_type'] === 'file')
                                @php
                                $files = [];
                                $decoded = json_decode($ans['answer'], true);
                                if (is_array($decoded)) {
                                    $files = $decoded;
                                } else {
                                    $files = [$ans['answer']];
                                }
                                @endphp
                                @foreach ($files as $filePath)
                                    @php $filePath = trim($filePath); @endphp
                                    @continue (empty($filePath))
                                    @php
                                    $fileUrl = H::getFileUrl($filePath);
                                    $fileName = basename($filePath);
                                    $isImage = H::isImageFile($filePath);
                                    $fileExists = H::fileExistsCheck($filePath);
                                    @endphp
                                    @if ($fileExists && $isImage)
                                        <div class="mt-2">
                                            <img src="{{ $fileUrl }}" 
                                                 class="img-fluid rounded border" 
                                                 style="max-width: 100%; max-height: 300px; width: auto; cursor: pointer;"
                                                 alt="{{ $fileName }}"
                                                 loading="lazy"
                                                 onclick="openImageModal('{{ $fileUrl }}', '{{ $fileName }}')">
                                        </div>
                                    @elseif ($fileExists)
                                        <div class="mt-2">
                                            <a href="{{ $fileUrl }}" target="_blank" class="text-decoration-none">
                                                <div class="d-flex align-items-center gap-2 p-2 border rounded bg-light">
                                                    {!! H::getFileIconHtml($filePath) !!}
                                                    <span>{{ $fileName }}</span>
                                                    <i class="fas fa-external-link-alt ms-auto text-muted"></i>
                                                </div>
                                            </a>
                                        </div>
                                    @else
                                        <div class="alert alert-warning mt-2 p-2 small">
                                            <i class="fas fa-exclamation-triangle me-1"></i> File not found
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="mt-1"><strong>{!! nl2br(htmlspecialchars($ans['answer'])) !!}</strong></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Comments -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-comments me-2 text-primary"></i>Conversation
                    <span class="badge bg-secondary ms-2">{!! count($comments) !!}</span>
                </div>
                <div class="card-body">
                    @if (count($comments) > 0)
                        @foreach ($comments as $comment)
                            @php
                            $comment_id = $comment['id'];
                            $comment_attachments = $attachments_by_comment[$comment_id] ?? [];
                            // Safe check for has_images - use isset to avoid errors
                            $has_images = 0;
                            if (isset($comment['has_images'])) {
                                $has_images = $castFlags ? (int)$comment['has_images'] : $comment['has_images'];
                            } else {
                                // Check if any attachments for this comment are images
                                foreach ($comment_attachments as $att) {
                                    if (H::isImageFile($att['file_path']) && H::fileExistsCheck($att['file_path'])) {
                                        $has_images = 1;
                                        break;
                                    }
                                }
                            }
                            $is_admin_comment = isset($comment['is_admin']) ? ($castFlags ? (int)$comment['is_admin'] : $comment['is_admin']) : 0;
                            @endphp
                            <div class="comment-item {!! $is_admin_comment ? 'admin-comment' : '' !!}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong>{{ $comment['full_name'] }}</strong>
                                        @if ($is_admin_comment)
                                            <span class="badge bg-primary ms-1"><i class="fas fa-shield-alt me-1"></i>IT Admin</span>
                                        @endif
                                        @if ($has_images)
                                            <span class="badge bg-info ms-1"><i class="fas fa-image me-1"></i>Image</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">{!! H::formatTicketTime($comment['created_at']) !!}</small>
                                    </div>
                                </div>
                                <p class="mb-0">{!! nl2br(htmlspecialchars($comment['comment'])) !!}</p>
                                
                                <!-- Comment Attachments - Display Images Directly -->
                                @if (!empty($comment_attachments))
                                    <div class="mt-3">
                                        <div class="row g-2">
                                            @foreach ($comment_attachments as $att)
                                                @php
                                                $filePath = $att['file_path'];
                                                $fileName = $att['file_name'];
                                                $fileExists = H::fileExistsCheck($filePath);
                                                $fileUrl = H::getFileUrl($filePath);
                                                $isImage = H::isImageFile($filePath);
                                                $fileSize = $att['file_size'];
                                                @endphp
                                                @if ($fileExists)
                                                    @if ($isImage)
                                                        <div class="col-md-6 col-lg-4">
                                                            <div class="comment-image-wrapper" onclick="openImageModal('{{ $fileUrl }}', '{{ $fileName }}')">
                                                                <img src="{{ $fileUrl }}" 
                                                                     class="comment-image-display" 
                                                                     alt="{{ $fileName }}"
                                                                     loading="lazy">
                                                                <div class="comment-image-overlay">
                                                                    <i class="fas fa-expand"></i>
                                                                    <span class="comment-image-filename">{{ $fileName }}</span>
                                                                </div>
                                                            </div>
                                                            <small class="text-muted d-block mt-1">{!! H::formatFileSize($fileSize) !!}</small>
                                                        </div>
                                                    @else
                                                        <div class="col-md-6 col-lg-4">
                                                            <a href="{{ $fileUrl }}" target="_blank" class="file-attachment-link">
                                                                <div class="file-item">
                                                                    <div class="file-icon">{!! H::getFileIconHtml($filePath) !!}</div>
                                                                    <div class="file-info">
                                                                        <div class="file-name text-truncate">{{ $fileName }}</div>
                                                                        <small class="text-muted">{!! H::formatFileSize($fileSize) !!}</small>
                                                                    </div>
                                                                    <i class="fas fa-external-link-alt text-muted"></i>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No comments yet.</p>
                    @endif
                    
                    <!-- Reply Form with Paste Support -->
                    <form method="POST" action="{!! $is_admin ? 'it-admin.php' : 'it-support.php' !!}" class="mt-3" enctype="multipart/form-data">
@csrf
                        <input type="hidden" name="ticket_id" value="{!! $ticket_id !!}">
                        <label class="form-label fw-bold">Add Reply <span class="text-muted fw-normal">(Paste images with Ctrl+V)</span></label>
                        
                        <div class="position-relative">
                            <textarea name="comment" class="form-control mb-2" rows="3" required placeholder="Type your message here... You can paste images directly with Ctrl+V"></textarea>
                            <div class="paste-hint position-absolute bottom-0 end-0 m-2"><i class="fas fa-paste"></i> Ctrl+V to paste image</div>
                        </div>
                        
                        <input type="hidden" name="pasted_images" id="pasted_images_comment" value="">
                        <div class="paste-preview-container" id="paste-preview-comment" style="display: none;"></div>
                        
                        <div class="mb-2">
                            <label class="form-label small text-muted">Or attach files</label>
                            <input type="file" name="comment_attachments[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                        </div>
                        
                        <button type="submit" name="{!! $is_admin ? 'add_admin_comment' : 'add_comment' !!}" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> Send Reply
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Timeline -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-history me-2 text-warning"></i>Timeline
                </div>
                <div class="card-body">
                    <div class="timeline-item mb-3 pb-2 border-bottom">
                        <small class="text-muted">Created</small>
                        <div class="fw-semibold">{!! H::formatTicketTime($ticket['created_at']) !!}</div>
                    </div>
                    @if ($ticket['resolved_at'])
                    <div class="timeline-item mb-3 pb-2 border-bottom">
                        <small class="text-muted">Resolved</small>
                        <div class="fw-semibold">{!! H::formatTicketTime($ticket['resolved_at']) !!}</div>
                    </div>
                    @endif
                    @if ($ticket['closed_at'])
                    <div class="timeline-item mb-3">
                        <small class="text-muted">Closed</small>
                        <div class="fw-semibold">{!! H::formatTicketTime($ticket['closed_at']) !!}</div>
                    </div>
                    @endif
                    @if ($ticket['assigned_to_name'])
                    <div class="timeline-item mt-3 pt-2 border-top">
                        <small class="text-muted">Assigned To</small>
                        <div class="fw-semibold">{{ $ticket['assigned_to_name'] }}</div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Status History -->
            @if ($history && count($history) > 0)
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-exchange-alt me-2 text-info"></i>Status History
                </div>
                <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                    @foreach ($history as $h)
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <div>
                                @if ($h['from_status'])
                                    <span class="badge bg-secondary">{!! $h['from_status'] !!}</span>
                                    <i class="fas fa-arrow-right text-muted mx-1"></i>
                                @endif
                                <span class="badge bg-primary">{!! $h['to_status'] !!}</span>
                            </div>
                            <small class="text-muted">{!! H::formatTicketTime($h['created_at']) !!}</small>
                        </div>
                        @if (!empty($h['notes']))
                            <small class="text-muted d-block mb-2">{!! nl2br(htmlspecialchars($h['notes'])) !!}</small>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Admin Notes -->
            @if (!empty($ticket['admin_notes']))
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-sticky-note me-2" style="color: #f59e0b;"></i>Admin Notes
                </div>
                <div class="card-body">
                    <div class="bg-light p-3 rounded">{!! nl2br(htmlspecialchars($ticket['admin_notes'])) !!}</div>
                </div>
            </div>
            @endif

            <!-- Feedback -->
            @if ($ticket['status'] == 'Resolved' || $ticket['status'] == 'Closed')
                @if (empty($ticket['feedback']))
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body text-center">
                            <p class="text-muted mb-2">Was your issue resolved satisfactorily?</p>
                            <button class="feedback-btn btn-primary btn-sm" data-ticket-id="{!! $ticket_id !!}">
                                <i class="fas fa-star me-1"></i>Submit Feedback
                            </button>
                        </div>
                    </div>
                @else
                    <div class="card mb-3 shadow-sm">
                        <div class="card-header bg-white fw-bold">
                            <i class="fas fa-star me-2" style="color: #f59e0b;"></i>Your Feedback
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span style="font-size: 20px; color: {!! $i <= $ticket['feedback_rating'] ? '#f59e0b' : '#ddd' !!}">★</span>
                                @endfor
                            </div>
                            @if (!empty($ticket['feedback']))
                                <p class="mb-0">{!! nl2br(htmlspecialchars($ticket['feedback'])) !!}</p>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Image Lightbox -->
<div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="lightboxImageTitle">Image Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-3" style="display:flex;align-items:center;justify-content:center;min-height:70vh;">
                <img id="lightboxImage" src="" alt="Image preview" style="max-height:85vh;max-width:95%;object-fit:contain;border-radius:8px;">
                <div class="position-absolute bottom-0 start-50 translate-middle-x pb-3">
                    <a id="lightboxDownloadLink" href="#" target="_blank" class="btn btn-outline-light">
                        <i class="fas fa-download me-1"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

.comment-item {
    background: white;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid #eef2f6;
}
.comment-item.admin-comment {
    border-left: 4px solid #009ada;
    background: #f0f9ff;
}

/* Ticket Image Card */
.ticket-image-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    background: #f8fafc;
    border: 1px solid #eef2f6;
    transition: all 0.3s;
    cursor: pointer;
}
.ticket-image-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border-color: #009ada;
}
.ticket-image-display {
    width: 100%;
    height: 180px;
    object-fit: cover;
    display: block;
}
.ticket-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.3);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
}
.ticket-image-card:hover .ticket-image-overlay {
    opacity: 1;
}
.ticket-image-overlay i {
    color: white;
    font-size: 30px;
    text-shadow: 0 2px 8px rgba(0,0,0,0.5);
}
.ticket-image-filename {
    color: white;
    font-size: 12px;
    margin-top: 8px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.8);
}

/* Comment Image Display */
.comment-image-wrapper {
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    border: 1px solid #eef2f6;
    background: #f8fafc;
    transition: all 0.3s;
}
.comment-image-wrapper:hover {
    border-color: #009ada;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.comment-image-display {
    width: 100%;
    height: 160px;
    object-fit: cover;
    display: block;
}
.comment-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
}
.comment-image-wrapper:hover .comment-image-overlay {
    opacity: 1;
}
.comment-image-overlay i {
    color: white;
    font-size: 24px;
    text-shadow: 0 2px 8px rgba(0,0,0,0.5);
}
.comment-image-filename {
    color: white;
    font-size: 11px;
    margin-top: 6px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.8);
}

/* File Item */
.file-item {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    border: 1px solid #eef2f6;
    border-radius: 10px;
    padding: 10px 14px;
    transition: all 0.2s;
}
.file-item:hover {
    border-color: #009ada;
    background: #f0f9ff;
}
.file-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: linear-gradient(135deg, rgba(0,154,218,0.15), rgba(118,188,33,0.15));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #009ada;
    flex-shrink: 0;
}
.file-info {
    flex: 1;
    min-width: 0;
}
.file-name {
    font-weight: 500;
    font-size: 13px;
}

.file-attachment-link {
    text-decoration: none;
    color: inherit;
    display: block;
}
.file-attachment-link:hover .file-item {
    border-color: #009ada;
    background: #f0f9ff;
}

.feedback-btn {
    background: linear-gradient(135deg, #009ada, #76bc21);
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 30px;
    font-weight: 600;
    transition: all 0.3s;
}
.feedback-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,154,218,0.3);
    color: white;
}

.paste-preview-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 8px;
    margin-bottom: 8px;
}
.paste-preview-item {
    position: relative;
    display: inline-block;
    border: 2px solid #eef2f6;
    border-radius: 12px;
    overflow: hidden;
    background: #f8fafc;
}
.paste-preview-item img {
    max-width: 100px;
    max-height: 100px;
    object-fit: cover;
    display: block;
}
.paste-preview-item .remove-image {
    position: absolute;
    top: 4px;
    right: 4px;
    background: rgba(0,0,0,0.7);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}
.paste-preview-item .remove-image:hover {
    background: #dc2626;
}

.paste-hint {
    font-size: 12px;
    color: #94a3b8;
    background: rgba(255,255,255,0.9);
    padding: 2px 10px;
    border-radius: 12px;
    border: 1px dashed #cbd5e1;
}
.paste-hint i {
    margin-right: 4px;
}

#imageLightboxModal .modal-content {
    border-radius: 12px;
    background: rgba(0,0,0,0.92);
}
#imageLightboxModal .modal-body {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 70vh;
    padding: 20px;
}
#lightboxImage {
    max-height: 85vh;
    max-width: 95%;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 8px 40px rgba(0,0,0,0.5);
}
</style>

<script>
function openImageModal(imageUrl, imageName) {
    const modal = document.getElementById('imageLightboxModal');
    const img = document.getElementById('lightboxImage');
    const title = document.getElementById('lightboxImageTitle');
    const downloadLink = document.getElementById('lightboxDownloadLink');
    
    if (!imageUrl) return;
    
    img.src = imageUrl;
    title.textContent = imageName || 'Image Preview';
    downloadLink.href = imageUrl;
    downloadLink.download = imageName || 'image';
    
    const bsModal = new bootstrap.Modal(modal, {
        backdrop: true,
        keyboard: true
    });
    bsModal.show();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('imageLightboxModal');
        const bsModal = bootstrap.Modal.getInstance(modal);
        if (bsModal) bsModal.hide();
    }
});

// ── Reply Paste Support ──────────────────────────────────────────────────────
(function() {
    let pastedImages = [];
    let replyTextarea = null;
    let previewContainer = null;
    let hiddenInput = null;
    
    function initReplyPaste() {
        replyTextarea = document.querySelector('textarea[name="comment"]');
        if (!replyTextarea) return;
        if (replyTextarea.dataset.pasteInitialized) return;
        replyTextarea.dataset.pasteInitialized = 'true';
        
        // Find or create preview container
        previewContainer = document.getElementById('paste-preview-comment');
        if (!previewContainer) {
            previewContainer = document.createElement('div');
            previewContainer.className = 'paste-preview-container';
            previewContainer.id = 'paste-preview-comment';
            previewContainer.style.display = 'none';
            replyTextarea.parentNode.insertBefore(previewContainer, replyTextarea.nextSibling);
        }
        
        hiddenInput = document.getElementById('pasted_images_comment');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'pasted_images';
            hiddenInput.id = 'pasted_images_comment';
            replyTextarea.parentNode.insertBefore(hiddenInput, previewContainer.nextSibling);
        }
        
        replyTextarea.addEventListener('paste', function(e) {
            const items = e.clipboardData.items;
            let hasImage = false;
            
            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    hasImage = true;
                    const file = items[i].getAsFile();
                    const reader = new FileReader();
                    
                    reader.onload = function(event) {
                        pastedImages.push(event.target.result);
                        updatePreview();
                        updateHiddenInput();
                    };
                    
                    reader.readAsDataURL(file);
                }
            }
            
            if (hasImage) {
                e.preventDefault();
            }
        });
    }
    
    function updatePreview() {
        if (!previewContainer) return;
        previewContainer.innerHTML = '';
        if (pastedImages.length === 0) {
            previewContainer.style.display = 'none';
            return;
        }
        previewContainer.style.display = 'flex';
        
        pastedImages.forEach((dataUrl, index) => {
            const item = document.createElement('div');
            item.className = 'paste-preview-item';
            
            const img = document.createElement('img');
            img.src = dataUrl;
            item.appendChild(img);
            
            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-image';
            removeBtn.innerHTML = '×';
            removeBtn.title = 'Remove image';
            removeBtn.onclick = function(e) {
                e.stopPropagation();
                pastedImages.splice(index, 1);
                updatePreview();
                updateHiddenInput();
            };
            item.appendChild(removeBtn);
            
            previewContainer.appendChild(item);
        });
    }
    
    function updateHiddenInput() {
        if (hiddenInput) {
            hiddenInput.value = JSON.stringify(pastedImages);
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReplyPaste);
    } else {
        initReplyPaste();
    }
    
    // Also expose for re-initialization after modal loads
    window.initReplyPaste = initReplyPaste;
})();
</script>
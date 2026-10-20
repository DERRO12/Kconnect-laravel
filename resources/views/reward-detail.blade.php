<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reward['title'] }} - Rewards & Recognition - K-Connect</title>
     <title>Kconnect</title>
      <link rel="icon" type="image/png" href="favi.png">
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        
        .page-header h1 {
            font-size: 2rem;
            font-weight: 800;
            margin: 0;
        }

        .text-gradient {
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 10px 0 0;
        }

        .breadcrumb-item a {
            color: #64748b;
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb-item a:hover {
            color: var(--kiboko-blue);
        }

        .breadcrumb-item.active {
            color: #1e293b;
        }

        /* Main Content */
        .rewards-container {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #eef2f6;
            margin-bottom: 30px;
        }

        .video-column {
            padding-right: 20px;
        }

        .video-wrapper {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            background: #000;
        }

        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .info-column {
            padding-left: 20px;
        }

        .reward-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #1e293b;
        }

        .reward-description {
            color: #475569;
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 25px;
            white-space: pre-line;
        }

        .meta-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            padding: 15px 20px;
            background: #f8fafc;
            border-radius: 16px;
            border: 1px solid #eef2f6;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--kiboko-blue);
            font-size: 1.2rem;
        }

        .stat-info {
            flex: 1;
        }

        .stat-value {
            font-weight: 700;
            font-size: 1.2rem;
            color: #1e293b;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #64748b;
        }

        /* Attachments */
        .attachments-section {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eef2f6;
        }

        .attachments-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .attachments-title i {
            color: var(--kiboko-blue);
        }

        .attachment-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 12px;
            text-decoration: none;
            color: inherit;
            margin-bottom: 10px;
            transition: all 0.3s;
        }

        .attachment-link:hover {
            border-color: var(--kiboko-blue);
            transform: translateX(5px);
            background: white;
        }

        .attachment-icon {
            width: 40px;
            height: 40px;
            background: var(--grad);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
        }

        .attachment-info {
            flex: 1;
        }

        .attachment-name {
            font-weight: 600;
            margin-bottom: 3px;
        }

        .attachment-size {
            font-size: 0.8rem;
            color: #64748b;
        }

        .attachment-download {
            color: var(--kiboko-blue);
            font-size: 1.1rem;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s;
        }

        .attachment-link:hover .attachment-download {
            background: var(--grad);
            color: white;
        }

        /* Share Section */
        .share-section {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eef2f6;
        }

        .share-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #64748b;
        }

        .share-buttons {
            display: flex;
            gap: 10px;
        }

        .share-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }

        .share-btn:hover {
            transform: translateY(-3px);
        }

        .share-btn.copy-link {
            background: #1e293b;
        }

        .copy-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #1e293b;
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 500;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s;
            z-index: 9999;
        }

        .copy-notification.show {
            transform: translateY(0);
            opacity: 1;
        }

        /* Comments Section */
        .comments-section {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #eef2f6;
        }

        .comments-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .comments-title {
            font-size: 1.3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .comments-title i {
            color: var(--kiboko-blue);
        }

        .comment-count-badge {
            background: #f1f5f9;
            color: #1e293b;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .comment-form {
            margin-bottom: 30px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 16px;
        }

        .comment-form textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #eef2f6;
            border-radius: 12px;
            resize: none;
            font-family: inherit;
            transition: all 0.3s;
            background: white;
        }

        .comment-form textarea:focus {
            outline: none;
            border-color: var(--kiboko-blue);
            box-shadow: 0 0 0 4px rgba(0,154,218,0.1);
        }

        .comment-form button {
            margin-top: 10px;
            padding: 10px 25px;
            background: var(--grad);
            color: white;
            border: none;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            float: right;
        }

        .comment-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,154,218,0.3);
        }

        .comment-item {
            display: flex;
            gap: 15px;
            padding: 20px 0;
            border-bottom: 1px solid #eef2f6;
        }

        .comment-item:last-child {
            border-bottom: none;
        }

        .comment-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--kiboko-blue);
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .comment-content {
            flex: 1;
        }

        .comment-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
            flex-wrap: wrap;
        }

        .comment-author {
            font-weight: 700;
            color: #1e293b;
        }

        .comment-time {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .comment-text {
            color: #475569;
            line-height: 1.6;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: var(--kiboko-blue);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.5rem;
            }
            
            .video-column {
                padding-right: 0;
                margin-bottom: 30px;
            }
            
            .info-column {
                padding-left: 0;
            }
            
            .reward-title {
                font-size: 1.5rem;
            }
            
            .meta-stats {
                flex-wrap: wrap;
            }
            
            .stat-item {
                flex: 1 1 calc(50% - 10px);
            }
        }

        @media (max-width: 576px) {
            .rewards-container {
                padding: 20px;
            }
            
            .comments-section {
                padding: 20px;
            }
            
            .stat-item {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Copy Notification -->
    <div class="copy-notification" id="copyNotification">
        <i class="fas fa-check-circle me-2" style="color: #76bc21;"></i>
        Link copied to clipboard!
    </div>

    <!-- Navigation -->
       @include('partials.navbar')

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
          
        </div>
    </section>

    <!-- Main Content -->
    <div class="container mb-5">
        <!-- Two Column Layout -->
        <div class="rewards-container">
            <div class="row">
                <!-- Video Column -->
                <div class="col-md-6 video-column">
                    <div class="video-wrapper">
                        <iframe src="{{ $video_embed_url }}" 
                                allowfullscreen 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                        </iframe>
                    </div>
                </div>
                
                <!-- Info Column -->
                <div class="col-md-6 info-column">
                    <h2 class="reward-title">{{ $reward['title'] }}</h2>
                    
                    
                    
                    @if (!empty($reward['description']))
                        <div class="reward-description">
                            {!! nl2br(htmlspecialchars($reward['description'])) !!}
                        </div>
                    @endif
                    
                    <!-- Attachments -->
                    @if ($attachments && count($attachments) > 0)
                        <div class="attachments-section">
                            <div class="attachments-title">
                                <i class="fas fa-paperclip"></i>
                                Attachments ({!! count($attachments) !!})
                            </div>
                            @foreach ($attachments as $att)
@php
$icon = 'fa-file';
                                $ext = strtolower($att['file_type']);
                                if($ext == 'pdf') $icon = 'fa-file-pdf';
                                elseif(in_array($ext, ['doc', 'docx'])) $icon = 'fa-file-word';
                                elseif(in_array($ext, ['xls', 'xlsx', 'csv'])) $icon = 'fa-file-excel';
                                elseif(in_array($ext, ['ppt', 'pptx'])) $icon = 'fa-file-powerpoint';
                                elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) $icon = 'fa-file-image';
@endphp
                            <a href="download-attachment?id={!! $att['id'] !!}" class="attachment-link">
                                <div class="attachment-icon">
                                    <i class="fas {!! $icon !!}"></i>
                                </div>
                                <div class="attachment-info">
                                    <div class="attachment-name">{{ $att['file_name'] }}</div>
                                    <div class="attachment-size">{!! round($att['file_size'] / 1024, 1) !!} KB</div>
                                </div>
                                <i class="fas fa-download attachment-download"></i>
                            </a>
                            @endforeach
                        </div>
                    @endif
                    
                   
                </div>
            </div>
        </div>
        
        <!-- Comments Section -->
        <div class="comments-section">
            <div class="comments-header">
                <div class="comments-title">
                    <i class="fas fa-comments"></i>
                    Discussion
                </div>
                <span class="comment-count-badge">{!! $comment_count !!} Comments</span>
            </div>
            
            <!-- Comment Form -->
            <form method="POST" class="comment-form">
                @csrf
                <textarea name="comment" rows="3" placeholder="Share your thoughts, congratulations, or feedback..." required></textarea>
                <button type="submit" name="add_comment">
                    <i class="fas fa-paper-plane me-2"></i>Post Comment
                </button>
                <div class="clearfix"></div>
            </form>
            
            <!-- Comments List -->
            @if ($comments && count($comments) > 0)
                @foreach ($comments as $comment)
                    <div class="comment-item">
                        @if (!empty($comment['employee_photo']))
                            <img src="uploads/employees/{{ $comment['employee_photo'] }}" class="comment-avatar">
                        @else
                            <div class="comment-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                        <div class="comment-content">
                            <div class="comment-header">
                                <span class="comment-author">{{ $comment['employee_name'] }}</span>
                                <span class="comment-time">
                                    @php
 
                                    $time = strtotime($comment['created_at']);
                                    $now = time();
                                    $diff = $now - $time;
                                    
                                    if($diff < 60) {
                                        echo 'Just now';
                                    } elseif($diff < 3600) {
                                        $mins = floor($diff/60);
                                        echo $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
                                    } elseif($diff < 86400) {
                                        $hours = floor($diff/3600);
                                        echo $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
                                    } elseif($diff < 604800) {
                                        $days = floor($diff/86400);
                                        echo $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
                                    } else {
                                        echo date('M j, Y', $time);
                                    }
                                    
@endphp
                                </span>
                            </div>
                            <div class="comment-text">
                                {!! nl2br(htmlspecialchars($comment['comment'])) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="fas fa-comment-dots" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                    <h5 style="color: #64748b;">No comments yet</h5>
                    <p class="text-muted">Be the first to share your thoughts on this recognition!</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function copyPageLink(event) {
            event.preventDefault();
            
            // Create temporary input
            const tempInput = document.createElement('input');
            tempInput.value = window.location.href;
            document.body.appendChild(tempInput);
            
            // Select and copy
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            
            // Show notification
            const notification = document.getElementById('copyNotification');
            notification.classList.add('show');
            
            // Hide after 3 seconds
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }
        
        // Auto-resize textarea
        const textarea = document.querySelector('textarea');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
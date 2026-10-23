@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim


<!-- Add SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
:root {
    --kiboko-blue: #009ada;
    --kiboko-green: #76bc21;
}

.course-player-header {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.03);
    margin-bottom: 1.5rem;
}

.lesson-list .list-group-item {
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
    padding: 1rem;
}

.lesson-list .list-group-item.active {
    border-left-color: var(--kiboko-blue);
    background: linear-gradient(135deg, rgba(0,154,218,0.1) 0%, rgba(118,188,33,0.1) 100%);
}

.lesson-list .list-group-item.completed {
    border-left-color: #28a745;
    opacity: 0.8;
}

.lesson-list .list-group-item.completed .lesson-title {
    text-decoration: line-through;
    color: #6c757d;
}

.video-container {
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
    overflow: hidden;
    background: #000;
    border-radius: 12px;
}

.video-container iframe,
.video-container video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: none;
}

.no-video-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.progress-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    background: #e9ecef;
}

.progress-circle::before {
    content: '';
    position: absolute;
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 50%;
    z-index: 1;
}

.progress-circle span {
    position: relative;
    z-index: 2;
    font-weight: bold;
}

.feature-card {
    transition: all 0.3s ease;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    background: white;
}

.feature-card:hover {
    border-color: var(--kiboko-blue);
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.feature-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin-bottom: 1rem;
}

.nav-tabs .nav-link {
    color: #64748b;
    font-weight: 500;
    border: none;
    padding: 1rem 1.5rem;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    border: none;
    color: var(--kiboko-blue);
    background: rgba(0,154,218,0.05);
}

.nav-tabs .nav-link.active {
    color: var(--kiboko-blue);
    background: transparent;
    border-bottom: 3px solid var(--kiboko-blue);
}

.nav-tabs .badge {
    margin-left: 0.5rem;
}
</style>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Course Header -->
    <div class="course-player-header">
        <div class="row align-items-center">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="my-courses" class="text-decoration-none">My Courses</a></li>
                        <li class="breadcrumb-item"><a href="course-overview?id=<?php echo $course_id; ?>" class="text-decoration-none"><?php echo htmlspecialchars($course['title']); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Lesson <?php echo $current_index + 1; ?></li>
                    </ol>
                </nav>
                <h2 class="mb-1"><?php echo htmlspecialchars($current_lesson['title']); ?></h2>
                <div class="d-flex gap-3 mt-2">
                    <small class="text-muted">
                        <i class="bi bi-book"></i> Lesson <?php echo $current_index + 1; ?> of <?php echo $total_lessons; ?>
                    </small>
                    <?php if ($current_lesson['duration']): ?>
                        <small class="text-muted">
                            <i class="bi bi-clock"></i> <?php echo $current_lesson['duration']; ?> minutes
                        </small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-auto">
                <div class="course-progress">
                    <div class="d-flex align-items-center">
                        <div class="progress-circle me-3" data-progress="<?php echo $progress_percentage; ?>">
                            <span><?php echo $progress_percentage; ?>%</span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Course Progress</small>
                            <strong><?php echo $completed_lessons; ?>/<?php echo $total_lessons; ?> Lessons</strong>
                            <?php if ($course_completed && $quiz_passed && $certificate): ?>
                                <span class="badge bg-success d-block mt-1">
                                    <i class="bi bi-award-fill"></i> Certified
                                </span>
                            <?php elseif ($course_completed && !$quiz_passed && $quiz): ?>
                                <span class="badge bg-warning d-block mt-1">
                                    <i class="bi bi-exclamation-triangle"></i> Quiz Required
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Video Player Section -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div class="video-container">
                        <?php if ($video['kind'] === 'youtube'): ?>
                            <iframe src="https://www.youtube.com/embed/<?php echo $video['id']; ?>?enablejsapi=1&autoplay=0&rel=0" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    id="youtube-player"></iframe>
                        <?php elseif ($video['kind'] === 'file'): ?>
                            <video id="video-player" controls controlsList="nodownload" preload="metadata">
                                <source src="<?php echo e($video['src']); ?>">
                                Your browser does not support the video tag.
                            </video>
                        <?php elseif (in_array($video['kind'], ['vimeo', 'drive', 'link'], true)): ?>
                            <iframe src="<?php echo e($video['embed']); ?>" frameborder="0"
                                    allow="autoplay; fullscreen; picture-in-picture; encrypted-media" allowfullscreen
                                    id="external-player"></iframe>
                            <a href="<?php echo e($video['src']); ?>" target="_blank" rel="noopener" class="btn btn-sm btn-light position-absolute" style="right:12px;bottom:12px;z-index:2">
                                <i class="bi bi-box-arrow-up-right"></i> Open video
                            </a>
                        <?php else: ?>
                            <div class="no-video-placeholder d-flex align-items-center justify-content-center">
                                <div class="text-center">
                                    <i class="bi bi-camera-video-off display-1"></i>
                                    <h5 class="mt-3">No Video Available</h5>
                                    <p class="opacity-75">This lesson contains only text content.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Course Feature Tabs -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#overview">
                                <i class="bi bi-file-text"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="course-resources?course_id=<?php echo $course_id; ?>" target="_blank">
                                <i class="bi bi-paperclip"></i> Resources 
                                <?php if ($resources_count > 0): ?>
                                    <span class="badge bg-primary"><?php echo $resources_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="course-notes?course_id=<?php echo $course_id; ?>" target="_blank">
                                <i class="bi bi-journal"></i> My Notes
                                <?php if ($notes_count > 0): ?>
                                    <span class="badge bg-success"><?php echo $notes_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="course-discussions?course_id=<?php echo $course_id; ?>" target="_blank">
                                <i class="bi bi-chat"></i> Discussion
                                <?php if ($discussions_count > 0): ?>
                                    <span class="badge bg-info"><?php echo $discussions_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="overview">
                            <div class="lesson-content">
                                <?php echo $current_lesson['content'] ?: '<p class="text-muted">No additional content for this lesson.</p>'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Access Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <a href="course-resources?course_id=<?php echo $course_id; ?>" class="text-decoration-none" target="_blank">
                        <div class="feature-card">
                            <div class="feature-icon bg-primary bg-opacity-10">
                                <i class="bi bi-paperclip text-primary"></i>
                            </div>
                            <h6>Resources</h6>
                            <p class="text-muted small mb-0"><?php echo $resources_count; ?> files available</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="course-notes?course_id=<?php echo $course_id; ?>" class="text-decoration-none" target="_blank">
                        <div class="feature-card">
                            <div class="feature-icon bg-success bg-opacity-10">
                                <i class="bi bi-journal-text text-success"></i>
                            </div>
                            <h6>My Notes</h6>
                            <p class="text-muted small mb-0"><?php echo $notes_count; ?> notes taken</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="course-discussions?course_id=<?php echo $course_id; ?>" class="text-decoration-none" target="_blank">
                        <div class="feature-card">
                            <div class="feature-icon bg-info bg-opacity-10">
                                <i class="bi bi-chat-dots text-info"></i>
                            </div>
                            <h6>Discussions</h6>
                            <p class="text-muted small mb-0"><?php echo $discussions_count; ?> comments</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Quiz Info Section -->
            <?php if ($quiz): ?>
                <div class="quiz-info-card mb-4 p-3 bg-warning bg-opacity-10 rounded border-start border-warning border-4">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <i class="bi bi-patch-question fs-1 text-warning"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-2">Course Quiz</h5>
                            <p class="mb-2">Test your knowledge with the final quiz.</p>
                            <div class="d-flex gap-3 align-items-center flex-wrap">
                                <?php if ($quiz_passed): ?>
                                    <span class="badge bg-success p-2">
                                        <i class="bi bi-check-circle"></i> Passed (<?php echo round($best_score); ?>%)
                                    </span>
                                    <span class="text-muted">
                                        <i class="bi bi-arrow-repeat"></i> Attempts: <?php echo $quiz_attempts; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning p-2">Not Started</span>
                                    <span class="text-muted">
                                        <i class="bi bi-trophy"></i> Required to pass: <?php echo $quiz['passing_score']; ?>%
                                    </span>
                                <?php endif; ?>
                                <a href="quiz?course_id=<?php echo $course_id; ?>" class="btn btn-warning ms-auto">
                                    <?php echo $quiz_passed ? 'Review Quiz' : 'Take Quiz'; ?> 
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Navigation Buttons -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <?php if ($prev_lesson): ?>
                    <a href="course-player?id=<?php echo $course_id; ?>&lesson=<?php echo $prev_lesson['id']; ?>" 
                       class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Previous Lesson
                    </a>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>
                
                <div class="d-flex gap-2">
                    <?php if (!$current_lesson['completed']): ?>
                        <button class="btn btn-success" onclick="markLessonComplete()" id="completeBtn">
                            <i class="bi bi-check-circle"></i> Mark as Complete
                        </button>
                    <?php else: ?>
                        <span class="badge bg-success p-3">
                            <i class="bi bi-check-circle-fill"></i> Lesson Completed
                        </span>
                    <?php endif; ?>
                </div>
                
                <?php if ($next_lesson): ?>
                    <a href="course-player?id=<?php echo $course_id; ?>&lesson=<?php echo $next_lesson['id']; ?>" 
                       class="btn btn-primary">
                        Next Lesson <i class="bi bi-arrow-right"></i>
                    </a>
                <?php elseif ($quiz && !$quiz_passed && $course_completed): ?>
                    <a href="quiz?course_id=<?php echo $course_id; ?>" class="btn btn-warning">
                        Take Final Quiz <i class="bi bi-patch-question"></i>
                    </a>
                <?php elseif ($course_completed && $quiz_passed && !$certificate): ?>
                    <a href="generate-certificate?course_id=<?php echo $course_id; ?>" class="btn btn-success">
                        <i class="bi bi-award"></i> Get Certificate
                    </a>
                <?php elseif ($certificate): ?>
                    <a href="certificate?id=<?php echo $certificate['id']; ?>" class="btn btn-info" target="_blank">
                        <i class="bi bi-award"></i> View Certificate
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar - Lesson List -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Course Content</h6>
                    <span class="badge bg-primary"><?php echo $completed_lessons; ?>/<?php echo $total_lessons; ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush lesson-list" style="max-height: 500px; overflow-y: auto;">
                        <?php foreach ($lessons as $index => $lesson): ?>
                            <a href="course-player?id=<?php echo $course_id; ?>&lesson=<?php echo $lesson['id']; ?>" 
                               class="list-group-item list-group-item-action <?php echo $lesson['id'] == $current_lesson['id'] ? 'active' : ''; ?> 
                                      <?php echo $lesson['completed'] ? 'completed' : ''; ?>">
                                <div class="d-flex align-items-center">
                                    <div class="lesson-status me-3">
                                        <?php if ($lesson['completed']): ?>
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        <?php elseif ($lesson['id'] == $current_lesson['id']): ?>
                                            <i class="bi bi-play-circle-fill text-primary"></i>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark"><?php echo $index + 1; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="lesson-title <?php echo $lesson['completed'] ? 'text-muted' : ''; ?>">
                                            <?php echo htmlspecialchars($lesson['title']); ?>
                                        </div>
                                        <div class="d-flex gap-2 small">
                                            <?php if ($lesson['duration']): ?>
                                                <span class="text-muted">
                                                    <i class="bi bi-clock"></i> <?php echo $lesson['duration']; ?> min
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($lesson['attachments']): 
                                                $attch = json_decode($lesson['attachments'], true); ?>
                                                <span class="text-muted">
                                                    <i class="bi bi-paperclip"></i> <?php echo count($attch); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Course Info Footer -->
                <div class="card-footer bg-white">
                    <small class="text-muted d-block">
                        <i class="bi bi-info-circle"></i> 
                        <?php echo $total_lessons - $completed_lessons; ?> lessons remaining
                    </small>
                    <?php if ($quiz): ?>
                        <small class="text-muted d-block mt-1">
                            <i class="bi bi-patch-question"></i> 
                            Quiz: <?php echo $quiz_passed ? 'Passed' : 'Not started'; ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Video progress tracking
let progressInterval;
let videoCompleted = false;
const minimumWatchPercentage = 90;

<?php if ($video['kind'] === 'youtube'): ?>
// YouTube API
var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

var player;
function onYouTubeIframeAPIReady() {
    player = new YT.Player('youtube-player', {
        events: {
            'onStateChange': onPlayerStateChange,
            'onReady': onPlayerReady,
            'onError': onPlayerError
        }
    });
}

function onPlayerReady(event) {
    <?php if ($current_lesson['watched_duration'] > 0): ?>
    event.target.seekTo(<?php echo $current_lesson['watched_duration']; ?>);
    <?php endif; ?>
}

function onPlayerStateChange(event) {
    if (event.data == YT.PlayerState.PLAYING) {
        startTracking();
    } else if (event.data == YT.PlayerState.ENDED) {
        stopTracking();
        checkVideoCompletion(100);
    } else if (event.data == YT.PlayerState.PAUSED) {
        stopTracking();
    }
}

function onPlayerError(event) {
    Swal.fire({
        icon: 'error',
        title: 'Video Error',
        text: 'There was an error loading the video. Please try again.'
    });
}
<?php elseif ($video['kind'] === 'file'): ?>
const video = document.getElementById('video-player');
if (video) {
    <?php if ($current_lesson['watched_duration'] > 0): ?>
    video.currentTime = <?php echo $current_lesson['watched_duration']; ?>;
    <?php endif; ?>
    
    video.addEventListener('play', startTracking);
    video.addEventListener('pause', stopTracking);
    video.addEventListener('ended', function() {
        stopTracking();
        updateProgress(this.duration);
        checkVideoCompletion(100);
    });
    
    video.addEventListener('timeupdate', function() {
        const currentTime = this.currentTime;
        const duration = this.duration;
        const watchPercentage = (currentTime / duration) * 100;
        
        if (Math.floor(currentTime) % 30 === 0) {
            updateProgress(Math.floor(currentTime));
        }
        
        if (!videoCompleted && watchPercentage >= minimumWatchPercentage) {
            checkVideoCompletion(watchPercentage);
        }
    });
}
<?php endif; ?>

function startTracking() {
    if (progressInterval) clearInterval(progressInterval);
}

function stopTracking() {
    if (progressInterval) {
        clearInterval(progressInterval);
        progressInterval = null;
    }
}

function updateProgress(seconds) {
    fetch('api/update-progress', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            lesson_id: <?php echo $current_lesson['id']; ?>,
            watched_seconds: seconds,
            csrf_token: '<?php echo $csrf_token; ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const progressCircle = document.querySelector('.progress-circle');
            if (progressCircle) {
                const progress = data.progress;
                const angle = (progress / 100) * 360;
                progressCircle.style.background = `conic-gradient(var(--kiboko-blue) ${angle}deg, #e9ecef ${angle}deg)`;
                progressCircle.querySelector('span').textContent = Math.round(progress) + '%';
            }
        }
    });
}

function checkVideoCompletion(percentage) {
    if (percentage >= minimumWatchPercentage && !videoCompleted) {
        videoCompleted = true;
        Swal.fire({
            title: 'Mark Lesson as Complete?',
            text: 'You have watched most of the video. Would you like to mark this lesson as complete?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, mark complete',
            cancelButtonText: 'Not yet'
        }).then((result) => {
            if (result.isConfirmed) {
                markLessonComplete();
            }
        });
    }
}

function markLessonComplete() {
    const completeBtn = document.getElementById('completeBtn');
    if (completeBtn) {
        completeBtn.disabled = true;
        completeBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
    }
    
    fetch('api/complete-lesson', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            lesson_id: <?php echo $current_lesson['id']; ?>,
            completed: true,
            csrf_token: '<?php echo $csrf_token; ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Lesson Completed!',
                text: 'Great job! Moving to next lesson...',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                <?php if ($next_lesson): ?>
                    window.location.href = 'course-player?id=<?php echo $course_id; ?>&lesson=<?php echo $next_lesson['id']; ?>';
                <?php else: ?>
                    window.location.reload();
                <?php endif; ?>
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to mark lesson as complete'
            });
            if (completeBtn) {
                completeBtn.disabled = false;
                completeBtn.innerHTML = '<i class="bi bi-check-circle"></i> Mark as Complete';
            }
        }
    });
}

// Update progress circles
document.querySelectorAll('.progress-circle').forEach(circle => {
    const progress = parseInt(circle.dataset.progress || 0);
    const angle = (progress / 100) * 360;
    circle.style.background = `conic-gradient(var(--kiboko-blue) ${angle}deg, #e9ecef ${angle}deg)`;
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Left arrow for previous lesson
    if (e.key === 'ArrowLeft' && !e.ctrlKey && !e.altKey && !e.shiftKey) {
        <?php if ($prev_lesson): ?>
        window.location.href = 'course-player?id=<?php echo $course_id; ?>&lesson=<?php echo $prev_lesson['id']; ?>';
        <?php endif; ?>
    }
    
    // Right arrow for next lesson
    if (e.key === 'ArrowRight' && !e.ctrlKey && !e.altKey && !e.shiftKey) {
        <?php if ($next_lesson): ?>
        window.location.href = 'course-player?id=<?php echo $course_id; ?>&lesson=<?php echo $next_lesson['id']; ?>';
        <?php elseif ($quiz && !$quiz_passed && $course_completed): ?>
        window.location.href = 'quiz?course_id=<?php echo $course_id; ?>';
        <?php endif; ?>
    }
    
    // Space bar for play/pause
    if (e.key === ' ' && !e.ctrlKey && !e.altKey && !e.shiftKey && 
        !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
        e.preventDefault();
        <?php if ($video['kind'] === 'file'): ?>
        const video = document.getElementById('video-player');
        if (video) {
            if (video.paused) {
                video.play();
            } else {
                video.pause();
            }
        }
        <?php endif; ?>
    }
});

// Warn before leaving if video is playing
window.addEventListener('beforeunload', function(e) {
    <?php if ($video['kind'] === 'file'): ?>
    const video = document.getElementById('video-player');
    if (video && !video.paused && !video.ended) {
        e.preventDefault();
        e.returnValue = 'Video is still playing. Are you sure you want to leave?';
    }
    <?php endif; ?>
});
</script>


@endverbatim

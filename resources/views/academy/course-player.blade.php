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

.ka-watch { background:#fff; border:1px solid var(--kiboko-border,#e2e8f0); border-radius:14px; padding:.7rem 1rem; }
.ka-watch-bar { height:8px; background:#e2e8f0; border-radius:20px; overflow:hidden; }
.ka-watch-fill { height:100%; background:linear-gradient(90deg,#009ada,#76bc21); border-radius:20px; transition:width .4s ease; }
.ka-watch.done { border-color:#76bc21; background:#f3fae9; }
.ka-lesson-quiz { background:#fff; border:1px solid var(--kiboko-border,#e2e8f0); border-left:4px solid #f5a623; border-radius:14px; padding:.8rem 1rem; display:flex; flex-wrap:wrap; gap:.75rem; align-items:center; justify-content:space-between; }
.ka-lesson-quiz.passed { border-left-color:#76bc21; background:#f3fae9; }
.ka-lesson-quiz .ka-lq-icon { width:40px; height:40px; border-radius:50%; background:#fff4e0; color:#f5a623; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
.ka-lesson-quiz.passed .ka-lq-icon { background:#e6f4d4; color:#5a9a14; }
@media (max-width: 575.98px) { .ka-lesson-quiz .btn { width:100%; } }
.ka-lesson-tabs .nav-tabs { flex-wrap:nowrap; overflow-x:auto; scrollbar-width:none; }
.ka-lesson-tabs .nav-tabs::-webkit-scrollbar { display:none; }
.ka-lesson-tabs .nav-link { white-space:nowrap; background:none; border-top:0; border-left:0; border-right:0; }
.ka-notes { border-radius:12px; min-height:150px; resize:vertical; }
.ka-comment-form, .ka-comment { display:flex; gap:.75rem; align-items:flex-start; }
.ka-comment-form { margin-bottom:1.25rem; }
.ka-thread { padding:1rem 0; border-top:1px solid #eef2f7; }
.ka-avatar { width:38px; height:38px; min-width:38px; border-radius:50%; background:linear-gradient(135deg,#009ada,#76bc21); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; overflow:hidden; }
.ka-avatar img { width:100%; height:100%; object-fit:cover; }
.ka-avatar.sm { width:30px; height:30px; min-width:30px; font-size:.8rem; }
.ka-comment-head { font-size:.9rem; }
.ka-comment-body { color:#334155; margin:.15rem 0 .35rem; word-wrap:break-word; overflow-wrap:anywhere; }
.ka-comment-actions { display:flex; gap:1rem; font-size:.82rem; }
.ka-comment-actions button { background:none; border:0; padding:0; color:#64748b; }
.ka-comment-actions button:hover, .ka-like.liked { color:#009ada; }
.ka-replies { margin-top:.6rem; display:flex; flex-direction:column; gap:.6rem; }
.ka-reply { background:#f8fafc; border-radius:12px; padding:.6rem .75rem; }
.ka-reply-form { margin-top:.6rem; }
.min-w-0 { min-width:0; }
#completeBtn:disabled { opacity:.65; cursor:not-allowed; }
@media (max-width: 575.98px) {
    .ka-lesson-tabs .nav-item { flex:1 1 auto; }
    .ka-lesson-tabs .nav-link { width:100%; padding:.5rem .3rem; font-size:.8rem; text-align:center; }
    .ka-lesson-tabs .nav-link > i { display:none; }
    .ka-lesson-tabs .nav-link .badge { font-size:.6rem; padding:.2em .4em; }
    .ka-lesson-tabs .card-body { padding:1rem; }
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

            <!-- Watch progress: the lesson completes once the whole video is watched -->
            <?php if ($video['kind'] !== 'none' && $video['kind'] !== '' && !$current_lesson['completed']): ?>
            <div class="ka-watch mb-3" id="kaWatch">
                <div class="d-flex justify-content-between align-items-center mb-1 small">
                    <span><i class="bi bi-eye"></i> <span id="kaWatchText"><?php echo in_array($video['kind'], ['youtube', 'file'], true) ? 'Watch the whole video to complete this lesson' : ($ka_wait_seconds > 0 ? 'Watch the video to complete this lesson' : 'You can complete this lesson'); ?></span></span>
                    <strong id="kaWatchPct"></strong>
                </div>
                <div class="ka-watch-bar"><div class="ka-watch-fill" id="kaWatchFill" style="width:0%"></div></div>
            </div>
            <?php endif; ?>

            <!-- Lesson quiz: opens once the video is watched; passing it completes the lesson -->
            <?php if ($lesson_quiz): ?>
            <div class="ka-lesson-quiz mb-3 <?php echo $lesson_quiz_passed ? 'passed' : ''; ?>" id="kaLessonQuiz">
                <div class="d-flex align-items-center gap-3">
                    <div class="ka-lq-icon"><i class="bi <?php echo $lesson_quiz_passed ? 'bi-patch-check-fill' : 'bi-patch-question'; ?>"></i></div>
                    <div>
                        <div class="fw-semibold"><?php echo htmlspecialchars($lesson_quiz['title']); ?></div>
                        <div class="small text-muted" id="kaLessonQuizText">
                            <?php echo (int) $lesson_quiz['question_count']; ?> question<?php echo $lesson_quiz['question_count'] == 1 ? '' : 's'; ?> · pass mark <?php echo (int) $lesson_quiz['passing_score']; ?>%
                            <?php if ($lesson_quiz_passed): ?>
                                · <span class="text-success fw-semibold">Passed (<?php echo (int) $lesson_quiz_best; ?>%)</span>
                            <?php elseif ($lesson_quiz_attempts): ?>
                                · best so far <?php echo (int) $lesson_quiz_best; ?>%
                            <?php elseif (!$lesson_video_done): ?>
                                · opens when you finish the video
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php if ($lesson_quiz_passed): ?>
                    <span class="badge bg-success p-2"><i class="bi bi-check-circle-fill"></i> Quiz passed</span>
                <?php else: ?>
                    <a href="quiz?lesson_id=<?php echo (int) $current_lesson['id']; ?>" id="kaLessonQuizBtn" onclick="return kaGoQuiz(event)"
                       class="btn btn-warning <?php echo $lesson_video_done ? '' : 'disabled'; ?>" <?php echo $lesson_video_done ? '' : 'aria-disabled="true" title="Watch the whole video first"'; ?>>
                        <i class="bi bi-patch-question"></i> <?php echo $lesson_quiz_attempts ? 'Retake Lesson Quiz' : 'Take Lesson Quiz'; ?>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Lesson tabs: everything stays on this page, under the video -->
            <div class="card border-0 shadow-sm mb-4 ka-lesson-tabs">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-overview" role="tab">
                                <i class="bi bi-file-text"></i> <span>Overview</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-notes" role="tab">
                                <i class="bi bi-journal-text"></i> <span>My Notes</span>
                                <?php if (trim(strip_tags((string) $lesson_note['notes'])) !== ''): ?><span class="badge bg-success">1</span><?php endif; ?>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-discussion" role="tab">
                                <i class="bi bi-chat-dots"></i> <span>Comments</span>
                                <span class="badge bg-info" id="kaCommentCount" <?php echo $lesson_comment_count ? '' : 'hidden'; ?>><?php echo $lesson_comment_count; ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-resources" role="tab">
                                <i class="bi bi-paperclip"></i> <span>Resources</span>
                                <?php if ($lesson_attachments): ?><span class="badge bg-primary"><?php echo count($lesson_attachments); ?></span><?php endif; ?>
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Overview -->
                        <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
                            <div class="lesson-content">
                                <?php echo $current_lesson['content'] ?: '<p class="text-muted">No additional content for this lesson.</p>'; ?>
                            </div>
                        </div>

                        <!-- My Notes (private, saved automatically) -->
                        <div class="tab-pane fade" id="tab-notes" role="tabpanel">
                            <textarea id="kaNotes" class="form-control ka-notes" rows="7" placeholder="Write your notes for this lesson. They are private and saved automatically."><?php echo htmlspecialchars(trim(preg_replace('/<br\s*\/?>/i', "\n", (string) $lesson_note['notes']))); ?></textarea>
                            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-2">
                                <small class="text-muted" id="kaNotesStatus"><?php echo $lesson_note['updated_at'] ? 'Saved ' . date('M d, Y H:i', strtotime($lesson_note['updated_at'])) : 'Not saved yet'; ?></small>
                                <div class="d-flex gap-2">
                                    <a href="course-notes?course_id=<?php echo $course_id; ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-journals"></i> All course notes (<?php echo $notes_count; ?>)</a>
                                    <button type="button" class="btn btn-sm btn-primary" id="kaNotesSave"><i class="bi bi-save"></i> Save</button>
                                </div>
                            </div>
                        </div>

                        <!-- Comments / discussion for this lesson -->
                        <div class="tab-pane fade" id="tab-discussion" role="tabpanel">
                            <form id="kaCommentForm" class="ka-comment-form">
                                <div class="ka-avatar"><?php echo strtoupper(substr($user['full_name'] ?? 'U', 0, 1)); ?></div>
                                <div class="flex-grow-1">
                                    <textarea class="form-control" id="kaCommentText" rows="2" maxlength="2000" placeholder="Ask a question or share a comment about this lesson..." required></textarea>
                                    <div class="text-end mt-2"><button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-send"></i> Post</button></div>
                                </div>
                            </form>
                            <div id="kaThreads">
                                <?php if (!$lesson_threads): ?>
                                    <p class="text-muted text-center my-4" id="kaNoComments"><i class="bi bi-chat-square-text d-block fs-2 mb-1"></i>No comments yet. Start the conversation.</p>
                                <?php endif; ?>
                                <?php foreach ($lesson_threads as $t): ?>
                                    <div class="ka-thread" data-id="<?php echo $t['id']; ?>">
                                        <div class="ka-comment">
                                            <div class="ka-avatar"><?php if ($t['profile_image'] && file_exists('assets/uploads/profiles/' . $t['profile_image'])): ?><img src="assets/uploads/profiles/<?php echo htmlspecialchars($t['profile_image']); ?>" alt=""><?php else: echo strtoupper(substr($t['full_name'], 0, 1)); endif; ?></div>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="ka-comment-head"><strong><?php echo htmlspecialchars($t['full_name']); ?></strong> <small class="text-muted"><?php echo ka_time_ago($t['created_at']); ?></small></div>
                                                <div class="ka-comment-body"><?php echo nl2br(htmlspecialchars($t['comment'])); ?></div>
                                                <div class="ka-comment-actions">
                                                    <button type="button" class="ka-like <?php echo $t['liked'] ? 'liked' : ''; ?>" data-id="<?php echo $t['id']; ?>"><i class="bi bi-hand-thumbs-up<?php echo $t['liked'] ? '-fill' : ''; ?>"></i> <span><?php echo (int) $t['likes']; ?></span></button>
                                                    <button type="button" class="ka-reply-toggle"><i class="bi bi-reply"></i> Reply</button>
                                                    <?php if ($t['user_id'] == $user_id): ?><button type="button" class="ka-delete text-danger" data-id="<?php echo $t['id']; ?>"><i class="bi bi-trash"></i></button><?php endif; ?>
                                                </div>
                                                <div class="ka-replies">
                                                    <?php foreach ($lesson_replies[$t['id']] ?? [] as $r): ?>
                                                        <div class="ka-comment ka-reply">
                                                            <div class="ka-avatar sm"><?php echo strtoupper(substr($r['full_name'], 0, 1)); ?></div>
                                                            <div class="flex-grow-1 min-w-0">
                                                                <div class="ka-comment-head"><strong><?php echo htmlspecialchars($r['full_name']); ?></strong> <small class="text-muted"><?php echo ka_time_ago($r['created_at']); ?></small></div>
                                                                <div class="ka-comment-body"><?php echo nl2br(htmlspecialchars($r['comment'])); ?></div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <form class="ka-reply-form" hidden>
                                                    <textarea class="form-control form-control-sm" rows="2" maxlength="2000" placeholder="Write a reply..." required></textarea>
                                                    <div class="text-end mt-1"><button class="btn btn-sm btn-outline-primary" type="submit">Reply</button></div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="text-end mt-2"><a href="course-discussions?course_id=<?php echo $course_id; ?>" class="small">All course discussions (<?php echo $discussions_count; ?>) <i class="bi bi-arrow-right"></i></a></div>
                        </div>

                        <!-- Resources of this lesson -->
                        <div class="tab-pane fade" id="tab-resources" role="tabpanel">
                            <?php if (!$lesson_attachments): ?>
                                <p class="text-muted text-center my-4"><i class="bi bi-paperclip d-block fs-2 mb-1"></i>No files for this lesson.</p>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($lesson_attachments as $file):
                                        $file_path = 'assets/uploads/attachments/' . $file;
                                        $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION)); ?>
                                        <a class="list-group-item list-group-item-action d-flex align-items-center gap-3" href="<?php echo htmlspecialchars($file_path); ?>" target="_blank" rel="noopener">
                                            <i class="bi bi-<?php echo ka_file_icon($file_ext); ?> fs-4 text-primary"></i>
                                            <span class="flex-grow-1 text-break"><?php echo htmlspecialchars($file); ?></span>
                                            <small class="text-muted"><?php echo file_exists($file_path) ? ka_format_file_size(filesize($file_path)) : ''; ?></small>
                                            <i class="bi bi-download"></i>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <div class="text-end mt-2"><a href="course-resources?course_id=<?php echo $course_id; ?>" class="small">All course resources (<?php echo $resources_count; ?>) <i class="bi bi-arrow-right"></i></a></div>
                        </div>
                    </div>
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
                            <p class="mb-2">Test your knowledge with the final quiz. The course is completed when every lesson is watched and the quiz is passed.</p>
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
                                <?php if ($course_completed || $quiz_passed): ?>
                                <a href="quiz?course_id=<?php echo $course_id; ?>" class="btn btn-warning ms-auto">
                                    <?php echo $quiz_passed ? 'Review Quiz' : 'Take Quiz'; ?> 
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                                <?php else: ?>
                                <span class="btn btn-outline-secondary ms-auto disabled" aria-disabled="true" title="Complete every lesson first">
                                    <i class="bi bi-lock"></i> Finish all lessons first (<?php echo $completed_lessons; ?>/<?php echo $total_lessons; ?>)
                                </span>
                                <?php endif; ?>
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
                        <?php if ($lesson_quiz && !$lesson_quiz_passed): ?>
                        <span class="badge bg-warning text-dark p-3"><i class="bi bi-patch-question"></i> Pass the lesson quiz to complete</span>
                        <?php else: ?>
                        <button class="btn btn-success" onclick="markLessonComplete()" id="completeBtn" <?php echo in_array($video['kind'], ['youtube', 'file'], true) || $ka_wait_seconds > 0 ? 'disabled title="Watch the whole video first"' : ''; ?>>
                            <i class="bi bi-check-circle"></i> Mark as Complete
                        </button>
                        <?php endif; ?>
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
                <?php elseif ($course_completed && $quiz_ok && !$certificate): ?>
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
                                            <?php if (in_array((int) $lesson['id'], $lesson_quiz_ids, true)): ?>
                                                <span class="text-muted" title="This lesson has a quiz"><i class="bi bi-patch-question"></i> Quiz</span>
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
if (!window.Swal) window.Swal = { fire: () => Promise.resolve({ isConfirmed: false }) }; // pop-up library missing: keep working
// Watch tracking: the lesson completes only when the whole video has been watched.
// Skipping ahead past the furthest point watched is not allowed.
const KA_LESSON = <?php echo (int) $current_lesson['id']; ?>;
const KA_CSRF = '<?php echo $csrf_token; ?>';
const KA_KIND = '<?php echo $video['kind']; ?>';
const KA_DONE = <?php echo $current_lesson['completed'] ? 'true' : 'false'; ?>;
// The lesson has a quiz still to pass: it opens once the video is watched and completes the lesson.
const KA_QUIZ = <?php echo ($lesson_quiz && !$lesson_quiz_passed) ? "'quiz?lesson_id=" . (int) $current_lesson['id'] . "'" : 'null'; ?>;
const KA_ME = <?php echo json_encode(['name' => $user['full_name'] ?? '', 'initial' => strtoupper(substr($user['full_name'] ?? 'U', 0, 1))]); ?>;
let kaMax = <?php echo (int) ($current_lesson['watched_duration'] ?? 0); ?>;   // furthest second watched
let kaLen = 0;          // video length in seconds
let kaLastSent = 0;
let kaFinished = KA_DONE;
let kaWait = <?php echo (int) $ka_wait_seconds; ?>;

function kaFmt(s) { s = Math.max(0, Math.round(s)); const m = Math.floor(s / 60); return m + ':' + String(s % 60).padStart(2, '0'); }

function kaShowWatch() {
    const fill = document.getElementById('kaWatchFill');
    if (!fill || !kaLen) return;
    const pct = Math.min(100, (kaMax / kaLen) * 100);
    fill.style.width = pct + '%';
    document.getElementById('kaWatchPct').textContent = Math.floor(pct) + '%';
    const left = kaLen - kaMax;
    document.getElementById('kaWatchText').textContent = left <= kaSlack()
        ? (KA_QUIZ ? 'Video watched. Now pass the lesson quiz to complete this lesson.' : 'Video watched. You can complete this lesson.')
        : 'Watch the whole video to complete this lesson (' + kaFmt(left) + ' left)';
}

function kaSlack() { return Math.max(3, Math.floor(kaLen * 0.02)); }
function kaWatchedAll() { return kaLen > 0 && kaMax >= kaLen - kaSlack(); }

function kaSetButton() {
    const btn = document.getElementById('completeBtn');
    if (KA_DONE) return;
    let ready = true;
    if (KA_KIND === 'youtube' || KA_KIND === 'file') ready = kaWatchedAll();
    else if (['vimeo', 'drive', 'link'].includes(KA_KIND)) ready = kaWait <= 0;
    if (btn) { btn.disabled = !ready; btn.title = ready ? '' : 'Watch the whole video first'; }
    if (ready) {
        document.getElementById('kaWatch')?.classList.add('done');
        const qb = document.getElementById('kaLessonQuizBtn');
        if (qb) { qb.classList.remove('disabled'); qb.removeAttribute('aria-disabled'); qb.title = ''; }
    }
}

// Open the lesson quiz once the watch time has been saved (the quiz page checks it).
function kaGoQuiz(e) {
    if (e) e.preventDefault();
    const qb = document.getElementById('kaLessonQuizBtn');
    if (qb && qb.classList.contains('disabled')) return false;
    const go = () => { window.location.href = KA_QUIZ || ('quiz?lesson_id=' + KA_LESSON); };
    if (KA_DONE || kaMax <= 0) { go(); return false; }
    fetch('api/update-progress', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ lesson_id: KA_LESSON, watched_seconds: Math.floor(kaMax), video_length: Math.round(kaLen), csrf_token: KA_CSRF })
    }).catch(() => {}).finally(go);
    return false;
}

function updateProgress(force) {
    if (KA_DONE) return;
    const now = Date.now();
    if (!force && now - kaLastSent < 10000) return;
    kaLastSent = now;
    fetch('api/update-progress', {
        method: 'POST',
        keepalive: true,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ lesson_id: KA_LESSON, watched_seconds: Math.floor(kaMax), video_length: Math.round(kaLen), csrf_token: KA_CSRF })
    }).then(r => r.json()).then(data => {
        if (data && data.success) {
            const circle = document.querySelector('.progress-circle');
            if (circle) {
                const angle = (data.progress / 100) * 360;
                circle.style.background = `conic-gradient(var(--kiboko-blue) ${angle}deg, #e9ecef ${angle}deg)`;
                circle.querySelector('span').textContent = Math.round(data.progress) + '%';
            }
        }
    }).catch(() => {});
}

function kaNoSkip() {
    if (window.Swal) Swal.fire({ toast: true, position: 'top', icon: 'info', title: 'Please watch the video without skipping ahead', timer: 2200, showConfirmButton: false });
}

function kaReachedEnd() {
    kaMax = kaLen;
    kaShowWatch();
    updateProgress(true);
    kaSetButton();
    if (!kaFinished) {
        kaFinished = true;
        if (KA_QUIZ) setTimeout(kaQuizPrompt, 600);
        else setTimeout(markLessonComplete, 600);
    }
}

<?php if ($video['kind'] === 'youtube'): ?>
var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
document.head.appendChild(tag);
var player, kaPoll;
function onYouTubeIframeAPIReady() {
    player = new YT.Player('youtube-player', { events: { onReady: onPlayerReady, onStateChange: onPlayerStateChange, onError: onPlayerError } });
}
function onPlayerReady(event) {
    kaLen = event.target.getDuration() || 0;
    if (kaMax > 0 && !KA_DONE && kaMax < kaLen - 5) event.target.seekTo(kaMax, true);
    kaShowWatch(); kaSetButton();
}
function onPlayerStateChange(event) {
    if (event.data == YT.PlayerState.PLAYING) {
        kaLen = player.getDuration() || kaLen;
        clearInterval(kaPoll);
        kaPoll = setInterval(() => {
            const t = player.getCurrentTime();
            if (!KA_DONE && t > kaMax + 3) { player.seekTo(kaMax, true); kaNoSkip(); return; }
            if (t > kaMax) kaMax = t;
            kaShowWatch(); kaSetButton(); updateProgress(false);
        }, 1000);
    } else if (event.data == YT.PlayerState.ENDED) {
        clearInterval(kaPoll);
        if (KA_DONE || kaMax >= kaLen - 5) kaReachedEnd();
    } else {
        clearInterval(kaPoll);
        updateProgress(true);
    }
}
function onPlayerError() {
    Swal.fire({ icon: 'error', title: 'Video Error', text: 'There was an error loading the video. Please try again.' });
}
<?php elseif ($video['kind'] === 'file'): ?>
const video = document.getElementById('video-player');
if (video) {
    let kaSeekBack = false;
    video.addEventListener('loadedmetadata', function () {
        kaLen = this.duration || 0;
        if (kaMax > 0 && !KA_DONE && kaMax < kaLen - 5) this.currentTime = kaMax;
        kaShowWatch(); kaSetButton();
    });
    video.addEventListener('timeupdate', function () {
        if (this.seeking) return;
        const t = this.currentTime;
        if (t <= kaMax + 2 && t > kaMax) kaMax = t;   // continuous playing only
        kaShowWatch(); kaSetButton();
        if (!this.paused) updateProgress(false);
    });
    video.addEventListener('seeking', function () {
        if (!KA_DONE && this.currentTime > kaMax + 2 && !kaSeekBack) {
            kaSeekBack = true; this.currentTime = kaMax; kaNoSkip();
            setTimeout(() => { kaSeekBack = false; }, 300);
        }
    });
    video.addEventListener('pause', () => updateProgress(true));
    video.addEventListener('ended', function () {
        if (KA_DONE || kaMax >= (this.duration || 0) - 3) kaReachedEnd();
    });
}
<?php elseif (in_array($video['kind'], ['vimeo', 'drive', 'link'], true)): ?>
if (kaWait > 0 && !KA_DONE) {
    const total = kaWait;
    const tick = setInterval(() => {
        kaWait--;
        const fill = document.getElementById('kaWatchFill');
        if (fill) {
            fill.style.width = Math.min(100, ((total - kaWait) / total) * 100) + '%';
            document.getElementById('kaWatchText').textContent = kaWait > 0
                ? 'Watch the video to complete this lesson (' + kaFmt(kaWait) + ' left)'
                : 'You can complete this lesson.';
        }
        if (kaWait <= 0) clearInterval(tick);
        kaSetButton();
    }, 1000);
} else { const f = document.getElementById('kaWatchFill'); if (f) f.style.width = '100%'; }
<?php endif; ?>
kaSetButton();

window.addEventListener('pagehide', () => { if (kaMax > 0) updateProgress(true); });

function kaQuizPrompt() {
    Swal.fire({
        icon: 'success', title: 'Video watched!',
        text: 'Pass the lesson quiz to complete this lesson.',
        showCancelButton: true, confirmButtonText: 'Take the quiz now', cancelButtonText: 'Later'
    }).then(r => { if (r.isConfirmed) kaGoQuiz(); });
}

function markLessonComplete() {
    if (KA_QUIZ) { kaQuizPrompt(); return; }
    const completeBtn = document.getElementById('completeBtn');
    if (completeBtn) {
        completeBtn.disabled = true;
        completeBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
    }
    fetch('api/complete-lesson', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ lesson_id: KA_LESSON, completed: true, csrf_token: KA_CSRF })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Lesson Completed!',
                text: <?php echo $next_lesson ? "'Great job! Moving to the next lesson...'" : ($quiz && !$quiz_passed ? "'All lessons done? The quiz unlocks once every lesson is completed.'" : "'Great job!'"); ?>,
                timer: 2200,
                showConfirmButton: false
            }).then(() => {
                <?php if ($next_lesson): ?>
                    window.location.href = 'course-player?id=<?php echo $course_id; ?>&lesson=<?php echo $next_lesson['id']; ?>';
                <?php else: ?>
                    window.location.reload();
                <?php endif; ?>
            });
        } else if (data.needs_quiz) {
            if (completeBtn) { completeBtn.innerHTML = '<i class="bi bi-check-circle"></i> Mark as Complete'; kaSetButton(); }
            Swal.fire({ icon: 'info', title: 'One more step', text: data.message, showCancelButton: true, confirmButtonText: 'Take the quiz now', cancelButtonText: 'Later' })
                .then(r => { if (r.isConfirmed) window.location.href = data.quiz_url; });
        } else {
            kaFinished = false;
            Swal.fire({ icon: data.needs_video ? 'info' : 'error', title: data.needs_video ? 'Not finished yet' : 'Error', text: data.message || 'Failed to mark lesson as complete' });
            if (completeBtn) {
                completeBtn.innerHTML = '<i class="bi bi-check-circle"></i> Mark as Complete';
                kaSetButton();
            }
        }
    });
}

// ---- My Notes (saved automatically) ----
(function () {
    const box = document.getElementById('kaNotes');
    if (!box) return;
    const status = document.getElementById('kaNotesStatus');
    let timer, last = box.value;
    function save() {
        if (box.value === last) return;
        const value = box.value;
        status.textContent = 'Saving...';
        fetch('api/save-notes', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ lesson_id: KA_LESSON, notes: value }) })
            .then(r => r.json()).then(d => {
                if (d.success) { last = value; status.textContent = 'Saved ' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }); }
                else status.textContent = d.message || 'Could not save';
            }).catch(() => { status.textContent = 'Could not save. Check your connection.'; });
    }
    box.addEventListener('input', () => { status.textContent = 'Unsaved changes'; clearTimeout(timer); timer = setTimeout(save, 1200); });
    box.addEventListener('blur', save);
    document.getElementById('kaNotesSave').addEventListener('click', save);
})();

// ---- Comments / discussion ----
(function () {
    const wrap = document.getElementById('kaThreads');
    if (!wrap) return;
    const esc = s => s.replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const post = (url, body) => fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(Object.assign({ csrf_token: KA_CSRF }, body)) }).then(r => r.json());
    const count = d => { const b = document.getElementById('kaCommentCount'); const n = Math.max(0, parseInt(b.textContent || '0') + d); b.textContent = n; b.hidden = n === 0; };
    const replyHtml = text => `<div class="ka-comment ka-reply"><div class="ka-avatar sm">${esc(KA_ME.initial)}</div><div class="flex-grow-1 min-w-0"><div class="ka-comment-head"><strong>${esc(KA_ME.name)}</strong> <small class="text-muted">just now</small></div><div class="ka-comment-body">${esc(text).replace(/\n/g, '<br>')}</div></div></div>`;

    document.getElementById('kaCommentForm').addEventListener('submit', e => {
        e.preventDefault();
        const ta = document.getElementById('kaCommentText'); const text = ta.value.trim(); if (!text) return;
        const btn = e.target.querySelector('button'); btn.disabled = true;
        post('api/post-discussion', { lesson_id: KA_LESSON, comment: text }).then(d => {
            btn.disabled = false;
            if (!d.success) { Swal.fire({ icon: 'error', text: d.message || 'Could not post' }); return; }
            document.getElementById('kaNoComments')?.remove();
            const el = document.createElement('div'); el.className = 'ka-thread'; el.dataset.id = d.discussion_id;
            el.innerHTML = `<div class="ka-comment"><div class="ka-avatar">${esc(KA_ME.initial)}</div><div class="flex-grow-1 min-w-0"><div class="ka-comment-head"><strong>${esc(KA_ME.name)}</strong> <small class="text-muted">just now</small></div><div class="ka-comment-body">${esc(text).replace(/\n/g, '<br>')}</div><div class="ka-comment-actions"><button type="button" class="ka-like" data-id="${d.discussion_id}"><i class="bi bi-hand-thumbs-up"></i> <span>0</span></button><button type="button" class="ka-reply-toggle"><i class="bi bi-reply"></i> Reply</button><button type="button" class="ka-delete text-danger" data-id="${d.discussion_id}"><i class="bi bi-trash"></i></button></div><div class="ka-replies"></div><form class="ka-reply-form" hidden><textarea class="form-control form-control-sm" rows="2" maxlength="2000" placeholder="Write a reply..." required></textarea><div class="text-end mt-1"><button class="btn btn-sm btn-outline-primary" type="submit">Reply</button></div></form></div></div>`;
            wrap.prepend(el); ta.value = ''; count(1);
        }).catch(() => { btn.disabled = false; });
    });

    wrap.addEventListener('click', e => {
        const like = e.target.closest('.ka-like'), toggle = e.target.closest('.ka-reply-toggle'), del = e.target.closest('.ka-delete');
        if (toggle) { const f = toggle.closest('.ka-comment').querySelector('.ka-reply-form'); f.hidden = !f.hidden; if (!f.hidden) f.querySelector('textarea').focus(); }
        if (like) post('api/toggle-like', { discussion_id: +like.dataset.id }).then(d => {
            if (!d.success) return;
            like.classList.toggle('liked', d.liked);
            like.querySelector('i').className = 'bi bi-hand-thumbs-up' + (d.liked ? '-fill' : '');
            like.querySelector('span').textContent = d.likes;
        });
        if (del) Swal.fire({ title: 'Delete this comment?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Delete' }).then(r => {
            if (!r.isConfirmed) return;
            post('api/delete-discussion', { discussion_id: +del.dataset.id }).then(d => {
                if (!d.success) { Swal.fire({ icon: 'error', text: d.message || 'Could not delete' }); return; }
                const t = del.closest('.ka-thread'); count(-(1 + t.querySelectorAll('.ka-reply').length)); t.remove();
            });
        });
    });

    wrap.addEventListener('submit', e => {
        const f = e.target.closest('.ka-reply-form'); if (!f) return;
        e.preventDefault();
        const ta = f.querySelector('textarea'); const text = ta.value.trim(); if (!text) return;
        const id = +f.closest('.ka-thread').dataset.id;
        post('api/post-reply', { discussion_id: id, comment: text }).then(d => {
            if (!d.success) { Swal.fire({ icon: 'error', text: d.message || 'Could not reply' }); return; }
            f.closest('.ka-comment').querySelector('.ka-replies').insertAdjacentHTML('beforeend', replyHtml(text));
            ta.value = ''; f.hidden = true; count(1);
        });
    });
})();

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

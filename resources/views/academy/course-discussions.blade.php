@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
:root {
    --kiboko-blue: #009ada;
    --kiboko-green: #76bc21;
}

.discussions-header {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.03);
    margin-bottom: 1.5rem;
}

.discussion-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
}

.discussion-card:hover {
    border-left-color: var(--kiboko-blue);
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.discussion-card.own-discussion {
    border-left-color: var(--kiboko-green);
    background: linear-gradient(135deg, rgba(118,188,33,0.02) 0%, rgba(0,154,218,0.02) 100%);
}

.reply-card {
    background: #f8fafc;
    border-radius: 8px;
    padding: 1rem;
    margin: 0.5rem 0 0.5rem 3rem;
    border-left: 3px solid #e2e8f0;
}

.avatar-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
}

.lesson-badge {
    background: #e2e8f0;
    color: #475569;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.filter-btn {
    border: 2px solid #e2e8f0;
    background: white;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.filter-btn.active {
    border-color: var(--kiboko-blue);
    background: rgba(0,154,218,0.05);
    color: var(--kiboko-blue);
}

.like-btn {
    border: none;
    background: none;
    color: #64748b;
    transition: all 0.2s ease;
}

.like-btn:hover {
    color: var(--kiboko-blue);
}

.like-btn.liked {
    color: var(--kiboko-blue);
}
</style>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Header -->
    <div class="discussions-header">
        <div class="row align-items-center">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="my-courses" class="text-decoration-none">My Courses</a></li>
                        <li class="breadcrumb-item"><a href="course-player?id=<?php echo $course_id; ?>" class="text-decoration-none"><?php echo htmlspecialchars($course['title']); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Discussions</li>
                    </ol>
                </nav>
                <h2 class="mb-1">Course Discussions</h2>
                <p class="text-muted mb-0">Ask questions, share insights, and learn together</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" onclick="showNewDiscussionForm()">
                    <i class="bi bi-plus-circle"></i> Start Discussion
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10">
                    <i class="bi bi-chat-dots text-primary"></i>
                </div>
                <div>
                    <h4 class="mb-0"><?php echo count($discussions); ?></h4>
                    <small class="text-muted">Discussions</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10">
                    <i class="bi bi-chat-text text-success"></i>
                </div>
                <div>
                    <h4 class="mb-0"><?php 
                        $total_replies = array_sum(array_map(fn($d) => count($d['replies']), $discussions));
                        echo $total_replies;
                    ?></h4>
                    <small class="text-muted">Replies</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10">
                    <i class="bi bi-people text-warning"></i>
                </div>
                <div>
                    <h4 class="mb-0"><?php 
                        $participants = [];
                        foreach ($discussions as $d) {
                            $participants[$d['user_id']] = true;
                            foreach ($d['replies'] as $r) {
                                $participants[$r['user_id']] = true;
                            }
                        }
                        echo count($participants);
                    ?></h4>
                    <small class="text-muted">Participants</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info bg-opacity-10">
                    <i class="bi bi-clock-history text-info"></i>
                </div>
                <div>
                    <h4 class="mb-0"><?php 
                        $latest = !empty($discussions) ? $discussions[0]['created_at'] : 'N/A';
                        echo $latest !== 'N/A' ? date('M d', strtotime($latest)) : 'N/A';
                    ?></h4>
                    <small class="text-muted">Latest Activity</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Filter by Lesson</label>
                    <div class="d-flex gap-2 flex-wrap" id="lessonFilters">
                        <button class="filter-btn active" data-lesson="all">All Lessons</button>
                        <?php
                        $stmt = $pdo->prepare("SELECT id, title, order_number FROM lessons WHERE course_id = ? ORDER BY order_number");
                        $stmt->execute([$course_id]);
                        $lessons = $stmt->fetchAll();
                        foreach ($lessons as $lesson): ?>
                            <button class="filter-btn" data-lesson="<?php echo $lesson['id']; ?>">
                                Lesson <?php echo $lesson['order_number']; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sort by</label>
                    <select class="form-select" id="sortSelect">
                        <option value="recent">Most Recent</option>
                        <option value="popular">Most Popular</option>
                        <option value="replies">Most Replies</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- New Discussion Form -->
    <div class="card border-0 shadow-sm mb-4" id="newDiscussionForm" style="display: none;">
        <div class="card-body">
            <h5 class="mb-3">Start a New Discussion</h5>
            <form onsubmit="postDiscussion(event)">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="mb-3">
                    <label class="form-label">Lesson</label>
                    <select class="form-select" id="discussionLesson" required>
                        <option value="">Select a lesson</option>
                        <?php foreach ($lessons as $lesson): ?>
                            <option value="<?php echo $lesson['id']; ?>">
                                Lesson <?php echo $lesson['order_number']; ?>: <?php echo htmlspecialchars($lesson['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Your Question/Comment</label>
                    <textarea class="form-control" id="discussionContent" rows="4" required></textarea>
                </div>
                
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="hideNewDiscussionForm()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Post Discussion</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Discussions List -->
    <div id="discussionsList">
        <?php if (empty($discussions)): ?>
            <div class="text-center py-5">
                <i class="bi bi-chat-dots display-1 text-muted"></i>
                <h4 class="mt-3">No Discussions Yet</h4>
                <p class="text-muted">Be the first to start a discussion!</p>
                <button class="btn btn-primary" onclick="showNewDiscussionForm()">
                    <i class="bi bi-plus-circle"></i> Start Discussion
                </button>
            </div>
        <?php else: ?>
            <?php foreach ($discussions as $discussion): ?>
                <div class="discussion-card <?php echo $discussion['user_id'] == $user_id ? 'own-discussion' : ''; ?>" 
                     data-lesson="<?php echo $discussion['lesson_id']; ?>"
                     data-date="<?php echo strtotime($discussion['created_at']); ?>"
                     data-replies="<?php echo count($discussion['replies']); ?>">
                    
                    <!-- Discussion Header -->
                    <div class="d-flex gap-3">
                        <div class="flex-shrink-0">
                            <?php if ($discussion['profile_image']): ?>
                                <img src="assets/uploads/profiles/<?php echo $discussion['profile_image']; ?>" 
                                     class="rounded-circle" width="45" height="45" alt="Avatar">
                            <?php else: ?>
                                <div class="avatar-circle bg-primary" style="width: 45px; height: 45px;">
                                    <?php echo strtoupper(substr($discussion['full_name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong><?php echo htmlspecialchars($discussion['full_name']); ?></strong>
                                    <span class="badge bg-secondary ms-2"><?php echo $discussion['employee_code']; ?></span>
                                    <span class="lesson-badge ms-2">
                                        Lesson <?php echo $discussion['lesson_order']; ?>
                                    </span>
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-clock"></i> <?php echo ka_time_ago($discussion['created_at']); ?>
                                </small>
                            </div>
                            
                            <p class="mb-2"><?php echo nl2br(htmlspecialchars($discussion['comment'])); ?></p>
                            
                            <!-- Discussion Actions -->
                            <div class="d-flex gap-3 align-items-center">
                                <button class="like-btn <?php echo $discussion['user_liked'] ?? false ? 'liked' : ''; ?>" 
                                        onclick="toggleLike(<?php echo $discussion['id']; ?>)">
                                    <i class="bi bi-hand-thumbs-up<?php echo ($discussion['user_liked'] ?? false) ? '-fill' : ''; ?>"></i>
                                    <span id="likes-<?php echo $discussion['id']; ?>"><?php echo $discussion['likes'] ?? 0; ?></span>
                                </button>
                                
                                <button class="btn btn-sm btn-link text-decoration-none" 
                                        onclick="showReplyForm(<?php echo $discussion['id']; ?>)">
                                    <i class="bi bi-reply"></i> Reply
                                </button>
                                
                                <?php if ($discussion['user_id'] == $user_id): ?>
                                    <button class="btn btn-sm btn-link text-decoration-none text-danger" 
                                            onclick="deleteDiscussion(<?php echo $discussion['id']; ?>)">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Reply Form -->
                            <div class="reply-form mt-3" id="reply-form-<?php echo $discussion['id']; ?>" style="display: none;">
                                <textarea class="form-control form-control-sm mb-2" 
                                          id="reply-content-<?php echo $discussion['id']; ?>" 
                                          rows="2" 
                                          placeholder="Write your reply..."></textarea>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-primary" 
                                            onclick="postReply(<?php echo $discussion['id']; ?>)">
                                        Post Reply
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" 
                                            onclick="hideReplyForm(<?php echo $discussion['id']; ?>)">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Replies -->
                            <?php if (!empty($discussion['replies'])): ?>
                                <div class="mt-3">
                                    <?php foreach ($discussion['replies'] as $reply): ?>
                                        <div class="reply-card <?php echo $reply['user_id'] == $user_id ? 'own-discussion' : ''; ?>">
                                            <div class="d-flex gap-2">
                                                <?php if ($reply['profile_image']): ?>
                                                    <img src="assets/uploads/profiles/<?php echo $reply['profile_image']; ?>" 
                                                         class="rounded-circle" width="30" height="30" alt="Avatar">
                                                <?php else: ?>
                                                    <div class="avatar-circle bg-secondary" style="width: 30px; height: 30px; font-size: 0.9rem;">
                                                        <?php echo strtoupper(substr($reply['full_name'], 0, 1)); ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($reply['full_name']); ?></strong>
                                                            <small class="text-muted ms-2"><?php echo ka_time_ago($reply['created_at']); ?></small>
                                                        </div>
                                                        
                                                        <?php if ($reply['user_id'] == $user_id): ?>
                                                            <button class="btn btn-sm btn-link text-danger p-0" 
                                                                    onclick="deleteReply(<?php echo $reply['id']; ?>)">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                    <p class="mb-0 mt-1"><?php echo nl2br(htmlspecialchars($reply['comment'])); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<script>
    function timeAgo(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    if (seconds < 60) return 'just now';
    
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return minutes + ' minute' + (minutes > 1 ? 's' : '') + ' ago';
     
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return hours + ' hour' + (hours > 1 ? 's' : '') + ' ago';
    
    const days = Math.floor(hours / 24);
    if (days < 7) return days + ' day' + (days > 1 ? 's' : '') + ' ago';
    
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}
let currentFilter = 'all';

// Filter functionality
document.querySelectorAll('[data-lesson]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('[data-lesson]').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.dataset.lesson;
        filterDiscussions();
    });
});

// Sort functionality
document.getElementById('sortSelect').addEventListener('change', filterDiscussions);

function filterDiscussions() {
    const discussions = document.querySelectorAll('.discussion-card');
    const sortBy = document.getElementById('sortSelect').value;
    
    discussions.forEach(discussion => {
        const lessonId = discussion.dataset.lesson;
        
        if (currentFilter === 'all' || currentFilter === lessonId) {
            discussion.style.display = 'block';
        } else {
            discussion.style.display = 'none';
        }
    });
    
    // Sort visible discussions
    const visibleDiscussions = Array.from(discussions).filter(d => d.style.display !== 'none');
    
    visibleDiscussions.sort((a, b) => {
        if (sortBy === 'recent') {
            return b.dataset.date - a.dataset.date;
        } else if (sortBy === 'popular') {
            const aLikes = parseInt(a.querySelector('.like-btn span').textContent);
            const bLikes = parseInt(b.querySelector('.like-btn span').textContent);
            return bLikes - aLikes;
        } else if (sortBy === 'replies') {
            return b.dataset.replies - a.dataset.replies;
        }
    });
    
    const container = document.getElementById('discussionsList');
    visibleDiscussions.forEach(d => container.appendChild(d));
}

function showNewDiscussionForm() {
    document.getElementById('newDiscussionForm').style.display = 'block';
}

function hideNewDiscussionForm() {
    document.getElementById('newDiscussionForm').style.display = 'none';
    document.getElementById('discussionContent').value = '';
    document.getElementById('discussionLesson').value = '';
}

function postDiscussion(event) {
    event.preventDefault();
    
    const lessonId = document.getElementById('discussionLesson').value;
    const content = document.getElementById('discussionContent').value;
    
    if (!lessonId || !content) {
        Swal.fire('Error', 'Please select a lesson and write your comment', 'warning');
        return;
    }
    
    fetch('api/post-discussion', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            lesson_id: lessonId,
            comment: content,
            csrf_token: '<?php echo $csrf_token; ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Posted!',
                text: 'Your discussion has been posted.',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error', data.message || 'Failed to post discussion', 'error');
        }
    });
}

function showReplyForm(discussionId) {
    document.getElementById(`reply-form-${discussionId}`).style.display = 'block';
}

function hideReplyForm(discussionId) {
    document.getElementById(`reply-form-${discussionId}`).style.display = 'none';
    document.getElementById(`reply-content-${discussionId}`).value = '';
}

function postReply(discussionId) {
    const content = document.getElementById(`reply-content-${discussionId}`).value;
    
    if (!content) {
        Swal.fire('Error', 'Please write a reply', 'warning');
        return;
    }
    
    fetch('api/post-reply', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            discussion_id: discussionId,
            comment: content,
            csrf_token: '<?php echo $csrf_token; ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Replied!',
                text: 'Your reply has been posted.',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error', data.message || 'Failed to post reply', 'error');
        }
    });
}

function toggleLike(discussionId) {
    fetch('api/toggle-like', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            discussion_id: discussionId,
            csrf_token: '<?php echo $csrf_token; ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const likesSpan = document.getElementById(`likes-${discussionId}`);
            likesSpan.textContent = data.likes;
            
            const likeBtn = likesSpan.closest('.like-btn');
            if (data.liked) {
                likeBtn.classList.add('liked');
                likeBtn.querySelector('i').classList.remove('bi-hand-thumbs-up');
                likeBtn.querySelector('i').classList.add('bi-hand-thumbs-up-fill');
            } else {
                likeBtn.classList.remove('liked');
                likeBtn.querySelector('i').classList.remove('bi-hand-thumbs-up-fill');
                likeBtn.querySelector('i').classList.add('bi-hand-thumbs-up');
            }
        }
    });
}

function deleteDiscussion(discussionId) {
    Swal.fire({
        title: 'Delete Discussion?',
        text: 'This will delete the discussion and all replies. This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Delete'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('api/delete-discussion', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    discussion_id: discussionId,
                    csrf_token: '<?php echo $csrf_token; ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Discussion has been deleted.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }
    });
}

function deleteReply(replyId) {
    Swal.fire({
        title: 'Delete Reply?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Delete'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('api/delete-reply', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    reply_id: replyId,
                    csrf_token: '<?php echo $csrf_token; ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Reply has been deleted.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }
    });
}

function timeAgo(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    if (seconds < 60) return 'just now';
    if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
    if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
    return date.toLocaleDateString();
}
</script>


@endverbatim

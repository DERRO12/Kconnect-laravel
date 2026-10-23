@include('academy.partials.header')
@include('academy.partials.sidebar')
@verbatim


<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Notifications</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <?php if ($unread_count > 0): ?>
                <a href="?mark_all_read=1" class="btn btn-sm btn-outline-primary me-2" 
                   onclick="return confirm('Mark all notifications as read?')">
                    <i class="bi bi-check-all"></i> Mark All Read
                </a>
            <?php endif; ?>
            <a href="api/clear-notifications" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Clear all notifications?')">
                <i class="bi bi-trash"></i> Clear All
            </a>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <?php if (empty($notifications)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash display-1 text-muted"></i>
                    <h4 class="mt-3">No Notifications</h4>
                    <p class="text-muted">You're all caught up!</p>
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($notifications as $notification): ?>
                        <div class="list-group-item list-group-item-action <?php echo !$notification['is_read'] ? 'unread' : ''; ?>">
                            <div class="d-flex align-items-start">
                                <div class="notification-icon me-3">
                                    <i class="bi bi-<?php 
                                        echo $notification['type'] == 'success' ? 'check-circle-fill text-success' : 
                                            ($notification['type'] == 'warning' ? 'exclamation-triangle-fill text-warning' : 
                                            ($notification['type'] == 'danger' ? 'x-circle-fill text-danger' : 'info-circle-fill text-info')); 
                                    ?> fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                        <small class="text-muted"><?php echo ka_time_ago($notification['created_at']); ?></small>
                                    </div>
                                    <p class="mb-2"><?php echo htmlspecialchars($notification['message']); ?></p>
                                    <?php if ($notification['link']): ?>
                                        <a href="<?php echo $notification['link']; ?>" class="btn btn-sm btn-outline-primary">
                                            View Details <i class="bi bi-arrow-right"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <?php if (!$notification['is_read']): ?>
                                    <span class="badge bg-primary ms-2">New</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</main>


@endverbatim

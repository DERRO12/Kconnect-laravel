<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News & Events - Kiboko Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
        }
        
        body {
            background: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background: white;
            height: 100vh;
            position: fixed;
            width: 260px;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            overflow-y: auto;
        }
        
        .sidebar-logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: var(--grad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            padding: 20px 0;
            text-align: center;
            border-bottom: 2px solid #f1f5f9;
            margin-bottom: 20px;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 15px;
            color: #475569;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: var(--grad);
            color: white;
        }
        
        .sidebar-menu i {
            width: 25px;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        
        .page-header {
            background: white;
            border-radius: 15px;
            padding: 20px 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-tabs {
            border: none;
            margin-bottom: 20px;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #64748b;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 10px;
            margin-right: 10px;
        }
        
        .nav-tabs .nav-link.active {
            background: var(--grad);
            color: white;
        }
        
        .table-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            margin-bottom: 30px;
        }
        
        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,154,218,0.2);
            color: white;
        }
        
        .btn-outline-gradient {
            background: transparent;
            border: 1px solid var(--kiboko-blue);
            color: var(--kiboko-blue);
            padding: 5px 15px;
            border-radius: 8px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }
        
        .btn-outline-gradient:hover {
            background: var(--grad);
            border-color: transparent;
            color: white;
        }
        
        .badge-announcement { background: #009ada; color: white; }
        .badge-events { background: #76bc21; color: white; }
        .badge-people { background: #f59e0b; color: white; }
        .badge-milestones { background: #8b5cf6; color: white; }
        
        .category-badge {
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            border-bottom: 2px solid #f1f5f9;
            color: #64748b;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table tbody td {
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            padding: 15px 10px;
        }
        
        .table tbody tr:hover {
            background: #f8fafc;
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <div class="page-header">
            <div>
                <h4 class="fw-bold mb-1">News & Events</h4>
                <p class="text-muted mb-0">Manage news posts and upcoming events</p>
            </div>
            <div>
                <a href="news-add.php" class="btn-gradient me-2">
                    <i class="fas fa-plus"></i> Add News
                </a>
                <a href="event-add.php" class="btn-gradient">
                    <i class="fas fa-plus"></i> Add Event
                </a>
            </div>
        </div>
        
        @if(isset($success))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {!! $success !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        @if(isset($error))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {!! $error !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="newsEventsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="news-tab" data-bs-toggle="tab" data-bs-target="#news" type="button" role="tab">
                    <i class="fas fa-newspaper me-2"></i>News Posts
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab">
                    <i class="fas fa-calendar-alt me-2"></i>Events
                </button>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content" id="newsEventsTabContent">
            <!-- News Tab -->
            <div class="tab-pane fade show active" id="news" role="tabpanel">
                <div class="table-card">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Author</th>
                                    <th>Date</th>
                                    <th>Stats</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($newsRows) > 0)
                                    @foreach($newsRows as $news)
                                    @php
                                        $categoryClass = '';
                                        switch($news['category']) {
                                            case 'Announcements': $categoryClass = 'badge-announcement'; break;
                                            case 'Events': $categoryClass = 'badge-events'; break;
                                            case 'People': $categoryClass = 'badge-people'; break;
                                            case 'Milestones': $categoryClass = 'badge-milestones'; break;
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $news['title'] }}</div>
                                            <small class="text-muted">{!! substr(htmlspecialchars($news['content']), 0, 60) !!}...</small>
                                        </td>
                                        <td>
                                            <span class="category-badge {!! $categoryClass !!}">
                                                {!! $news['category'] !!}
                                            </span>
                                        </td>
                                        <td>
                                            <div>{{ $news['author_name'] }}</div>
                                            <small class="text-muted">{{ $news['author_role'] }}</small>
                                        </td>
                                        <td>
                                            {!! date('M d, Y', strtotime($news['created_at'])) !!}
                                        </td>
                                        <td>
                                            <span class="me-2" title="Comments">
                                                <i class="far fa-comment"></i> {!! $news['comments_count'] !!}
                                            </span>
                                            <span title="Likes">
                                                <i class="far fa-heart"></i> {!! $news['likes_count'] !!}
                                            </span>
                                        </td>
                                        <td>
                                            @if($news['status'])
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="news-edit.php?id={!! $news['id'] !!}" class="btn-outline-gradient me-2">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?toggle_news={!! $news['id'] !!}" class="btn-outline-gradient me-2">
                                                <i class="fas fa-toggle-{!! $news['status'] ? 'on' : 'off' !!}"></i>
                                            </a>
                                            <a href="?delete_news={!! $news['id'] !!}" class="btn-outline-gradient" onclick="return confirm('Are you sure you want to delete this news post?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="far fa-newspaper fa-4x text-muted mb-3"></i>
                                            <h6 class="text-muted">No news posts found</h6>
                                            <a href="news-add.php" class="btn-gradient btn-sm mt-3">
                                                <i class="fas fa-plus me-2"></i>Add Your First News
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Events Tab -->
            <div class="tab-pane fade" id="events" role="tabpanel">
                <div class="table-card">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Date & Time</th>
                                    <th>Category</th>
                                    <th>RSVP</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($eventRows) > 0)
                                    @foreach($eventRows as $event)
                                    @php
                                        $isPast = strtotime($event['event_date']) < strtotime(date('Y-m-d'));
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $event['title'] }}</div>
                                            @if(!empty($event['location']))
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>{{ $event['location'] }}
                                            </small>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{!! date('M d, Y', strtotime($event['event_date'])) !!}</div>
                                            @if(!empty($event['event_time']))
                                            <small class="text-muted">{!! date('h:i A', strtotime($event['event_time'])) !!}</small>
                                            @endif
                                            @if($isPast)
                                            <span class="badge bg-secondary ms-2">Past</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark px-3 py-2">
                                                {!! $event['category'] !!}
                                            </span>
                                        </td>
                                        <td>
                                            @if($event['rsvp_required'])
                                                <span class="badge bg-success">Required</span>
                                            @else
                                                <span class="badge bg-secondary">Optional</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($event['status'])
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="event-edit.php?id={!! $event['id'] !!}" class="btn-outline-gradient me-2">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?toggle_event={!! $event['id'] !!}" class="btn-outline-gradient me-2">
                                                <i class="fas fa-toggle-{!! $event['status'] ? 'on' : 'off' !!}"></i>
                                            </a>
                                            <a href="?delete_event={!! $event['id'] !!}" class="btn-outline-gradient" onclick="return confirm('Are you sure you want to delete this event?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="far fa-calendar-times fa-4x text-muted mb-3"></i>
                                            <h6 class="text-muted">No events found</h6>
                                            <a href="event-add.php" class="btn-gradient btn-sm mt-3">
                                                <i class="fas fa-plus me-2"></i>Add Your First Event
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
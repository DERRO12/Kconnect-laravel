<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Dashboard - K-Connect Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --kiboko-green: #76bc21;
            --kiboko-blue: #009ada;
            --kiboko-dark: #0f172a;
            --grad: linear-gradient(135deg, #009ada 0%, #76bc21 100%);
            --grad-light: linear-gradient(135deg, rgba(0,154,218,0.1) 0%, rgba(118,188,33,0.1) 100%);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            overflow-x: hidden;
        }

        .main-content {
            margin-left: 280px;
            padding: 24px 32px;
            min-height: 100vh;
        }

        .welcome-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 25px 30px;
            border-radius: 24px;
            margin-bottom: 30px;
            border: 1px solid #eef2f6;
        }

        .welcome-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .welcome-subtitle {
            color: #64748b;
            font-size: 0.9rem;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            transition: all 0.3s;
            border: 1px solid #eef2f6;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--grad);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,154,218,0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: var(--grad-light);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--kiboko-blue);
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--kiboko-dark);
            margin-bottom: 5px;
        }

        .stat-label {
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
        }

        .stat-trend {
            font-size: 11px;
            margin-top: 8px;
        }
        .trend-up { color: #16a34a; }
        .trend-down { color: #dc2626; }

        .section-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid #eef2f6;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 12px;
            border-bottom: 2px solid #eef2f6;
        }

        .section-title i {
            color: var(--kiboko-blue);
            font-size: 1.2rem;
        }

        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 36px;
            height: 36px;
            background: var(--grad-light);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--kiboko-blue);
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .activity-time {
            font-size: 11px;
            color: #94a3b8;
        }

        .badge-pending {
            background: #fef3c7;
            color: #d97706;
        }
        .badge-approved {
            background: #dcfce7;
            color: #16a34a;
        }
        .badge-unread {
            background: #fee2e2;
            color: #dc2626;
        }

        .progress-custom {
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: var(--grad);
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .top-nominee-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 14px;
            margin-bottom: 10px;
            transition: all 0.3s;
        }

        .top-nominee-card:hover {
            background: var(--grad-light);
            transform: translateX(5px);
        }

        .nominee-rank {
            width: 30px;
            height: 30px;
            background: var(--grad);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
        }

        .nominee-info {
            flex: 1;
        }

        .nominee-name {
            font-weight: 700;
            font-size: 14px;
        }

        .nominee-dept {
            font-size: 11px;
            color: #64748b;
        }

        .nominee-votes {
            font-weight: 800;
            color: var(--kiboko-green);
        }

        .btn-gradient {
            background: var(--grad);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
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

        .btn-outline {
            background: white;
            border: 2px solid var(--kiboko-blue);
            color: var(--kiboko-blue);
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-outline:hover {
            background: var(--grad);
            color: white;
            border-color: transparent;
        }

        .status-badge {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
        }
        .status-New { background: #fee2e2; color: #dc2626; }
        .status-In-Progress { background: #dbeafe; color: #2563eb; }
        .status-Resolved { background: #dcfce7; color: #16a34a; }
        .status-Closed { background: #f3f4f6; color: #6b7280; }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
            }
        }

        @media (max-width: 768px) {
            .welcome-header {
                padding: 20px;
            }
            .welcome-title {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    
    <div class="main-content">
        <!-- Welcome Header -->
        <div class="welcome-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="welcome-title">
                        Welcome back, {{ session('admin_username') }}!
                    </h1>
                    <p class="welcome-subtitle">
                        Here's what's happening across your intranet today.
                    </p>
                </div>
                <div>
                    <span class="badge bg-light text-dark p-2 me-2">
                        <i class="fas fa-calendar me-2"></i>{!! date('F j, Y') !!}
                    </span>
                    <span class="badge bg-light text-dark p-2">
                        <i class="fas fa-clock me-2"></i>{!! date('h:i A') !!}
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Stats Row 1 - Content Management -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-sitemap"></i></div>
                    <div class="stat-number">{!! $navCount !!}</div>
                    <div class="stat-label">Navigation Links</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-images"></i></div>
                    <div class="stat-number">{!! $carouselCount !!}</div>
                    <div class="stat-label">Carousel Items</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-newspaper"></i></div>
                    <div class="stat-number">{!! $newsCount !!}</div>
                    <div class="stat-label">News Posts</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="stat-number">{!! $eventsCount !!}</div>
                    <div class="stat-label">Upcoming Events</div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Row 2 - Organization -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-number">{!! $totalEmployees !!}</div>
                    <div class="stat-label">Active Employees</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                    <div class="stat-number">{!! $totalCompanies !!}</div>
                    <div class="stat-label">Companies</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-birthday-cake"></i></div>
                    <div class="stat-number">{!! $birthdayCount !!}</div>
                    <div class="stat-label">Birthday Records</div>
                    @if ($todayBirthdays > 0)
                        <div class="stat-trend trend-up">
                            <i class="fas fa-gift"></i> {!! $todayBirthdays !!} celebrating today!
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-address-book"></i></div>
                    <div class="stat-number">{!! $keyContacts !!}</div>
                    <div class="stat-label">Key Contacts</div>
                </div>
            </div>
        </div>

        <!-- IT Support & Assets -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-headset"></i></div>
                    <div class="stat-number">{!! $itOpenTickets !!}</div>
                    <div class="stat-label">Open IT Tickets</div>
                    <div class="stat-trend trend-down">
                        <i class="fas fa-ticket"></i> {!! $itTickets !!} total tickets
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-database"></i></div>
                    <div class="stat-number">{!! $todayBackups !!}</div>
                    <div class="stat-label">Today's Backups</div>
                    <div class="stat-trend">
                        <i class="fas fa-cloud-upload-alt"></i> {!! $backupLogs !!} total logs
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-laptop"></i></div>
                    <div class="stat-number">{!! $totalAssets !!}</div>
                    <div class="stat-label">Total Assets</div>
                    <div class="stat-trend">
                        <i class="fas fa-exchange-alt"></i> {!! $activeAssignments !!} assigned
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-project-diagram"></i></div>
                    <div class="stat-number">{!! $activeProjects !!}</div>
                    <div class="stat-label">Active Projects</div>
                    <div class="stat-trend">
                        <i class="fas fa-tasks"></i> {!! $openTasks !!} open tasks
                    </div>
                </div>
            </div>
        </div>

        <!-- Whistleblowing & Compliance -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="section-card">
                    <div class="section-title">
                        <i class="fas fa-shield-alt"></i>
                        Compliance & Whistleblowing
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="display-6 fw-bold text-primary">{!! $whistleblowingReports !!}</div>
                                <div class="text-muted small">Total Reports</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="display-6 fw-bold text-warning">{!! $openReports !!}</div>
                                <div class="text-muted small">Open Investigations</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="display-6 fw-bold text-success">{!! $resolvedReports !!}</div>
                                <div class="text-muted small">Resolved</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="display-6 fw-bold text-info">{!! $systemLogs !!}</div>
                                <div class="text-muted small">System Logs</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rewards & Recognition Stats -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="section-card">
                    <div class="section-title">
                        <i class="fas fa-trophy"></i>
                        Rewards & Recognition Overview
                        <a href="nomination-dashboard.php" class="btn-outline ms-auto" style="padding: 5px 12px; font-size: 12px;">
                            <i class="fas fa-chart-line"></i> Full Dashboard
                        </a>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="display-6 fw-bold text-primary">{!! $totalNominations !!}</div>
                                <div class="text-muted small">Total Nominations</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="display-6 fw-bold text-warning">{!! $pendingNominations !!}</div>
                                <div class="text-muted small">Pending Review</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="display-6 fw-bold text-success">{!! $approvedNominations !!}</div>
                                <div class="text-muted small">Approved</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <div class="display-6 fw-bold text-danger">{!! $unreadNominations !!}</div>
                                <div class="text-muted small">Unread</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="row g-4">
            <!-- Category Breakdown -->
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-title">
                        <i class="fas fa-chart-pie"></i>
                        Nominations by Category
                    </div>
                    <canvas id="categoryChart" height="200"></canvas>
                    <div class="mt-3">
                        @php $catData = []; @endphp
                        @foreach ($categoryBreakdown as $cat)
                        @php $catData[] = $cat; @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">{{ $cat['name'] }}</span>
                                <span class="badge bg-secondary">{!! $cat['count'] !!}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Top Nominees -->
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-title">
                        <i class="fas fa-star"></i>
                        Top Nominees
                        <a href="all-nominations.php" class="btn-outline ms-auto" style="padding: 5px 12px; font-size: 12px;">View All</a>
                    </div>
                    @if (count($topNominees) > 0)
                        @php $rank = 1; @endphp
                        @foreach ($topNominees as $nominee)
                            <div class="top-nominee-card">
                                <div class="nominee-rank">#{!! $rank++ !!}</div>
                                <div class="nominee-info">
                                    <div class="nominee-name">{{ $nominee['full_name'] }}</div>
                                    <div class="nominee-dept">{{ $nominee['employee_code'] }} • {{ $nominee['department'] ?? '—' }}</div>
                                </div>
                                <div class="nominee-votes">
                                    <i class="fas fa-vote-yea"></i> {!! $nominee['votes'] !!}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No nominations yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-lg-4">
                <div class="section-card">
                    <div class="section-title">
                        <i class="fas fa-clock"></i>
                        Recent Nominations
                        <a href="all-nominations.php" class="btn-outline ms-auto" style="padding: 5px 12px; font-size: 12px;">View All</a>
                    </div>
                    <div class="activity-list" style="max-height: 350px; overflow-y: auto;">
                        @if (count($recentNominations) > 0)
                            @foreach ($recentNominations as $nom)
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-award"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">
                                            {{ $nom['nominator_name'] }} 
                                            nominated 
                                            {{ $nom['nominee_name'] }}
                                        </div>
                                        <div class="activity-time">
                                            <i class="fas fa-tag me-1"></i>{{ $nom['category_name'] }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-clock me-1"></i>{!! date('M d, H:i', strtotime($nom['created_at'])) !!}
                                        </div>
                                    </div>
                                    @if (!$nom['is_read'])
                                        <span class="badge badge-unread">New</span>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>No nominations yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row -->
        <div class="row g-4 mt-2">
            <!-- Recent IT Tickets -->
            <div class="col-lg-6">
                <div class="section-card">
                    <div class="section-title">
                        <i class="fas fa-headset"></i>
                        Recent IT Tickets
                        <a href="it-admin.php" class="btn-outline ms-auto" style="padding: 5px 12px; font-size: 12px;">Full Dashboard</a>
                    </div>
                    @if (count($recentTickets) > 0)
                        @foreach ($recentTickets as $ticket)
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">#{{ $ticket['ticket_number'] }} - {{ substr($ticket['subject'], 0, 40) }}</div>
                                    <div class="activity-time">
                                        {{ $ticket['employee_name'] ?? 'Unknown' }}
                                        <span class="mx-2">•</span>
                                        {!! date('M d, H:i', strtotime($ticket['created_at'])) !!}
                                    </div>
                                </div>
                                <span class="status-badge status-{!! str_replace(' ', '-', $ticket['status']) !!}">{!! $ticket['status'] !!}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">No tickets</div>
                    @endif
                </div>
            </div>

            <!-- Recent Backups & Events -->
            <div class="col-lg-6">
                <div class="section-card">
                    <div class="section-title">
                        <i class="fas fa-database"></i>
                        Recent Backups
                        <a href="backup-log.php" class="btn-outline ms-auto" style="padding: 5px 12px; font-size: 12px;">Backup Log</a>
                    </div>
                    @if (count($recentBackups) > 0)
                        @foreach ($recentBackups as $backup)
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">{{ $backup['companies'] }}</div>
                                    <div class="activity-time">
                                        {{ $backup['employee_name'] ?? 'Unknown' }}
                                        <span class="mx-2">•</span>
                                        {!! date('M d, H:i', strtotime($backup['created_at'])) !!}
                                    </div>
                                </div>
                                <span class="status-badge status-{!! $backup['status'] !!}">{!! $backup['status'] !!}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">No backups recorded</div>
                    @endif
                </div>

                <!-- Upcoming Events -->
                <div class="section-card mt-4">
                    <div class="section-title">
                        <i class="fas fa-calendar-alt"></i>
                        Upcoming Events
                        <a href="event-items.php" class="btn-outline ms-auto" style="padding: 5px 12px; font-size: 12px;">Manage</a>
                    </div>
                    @if (count($upcomingEvents) > 0)
                        @foreach ($upcomingEvents as $event)
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">{{ $event['title'] }}</div>
                                    <div class="activity-time">
                                        <i class="fas fa-calendar me-1"></i>{!! date('M d, Y', strtotime($event['event_date'])) !!}
                                        @if ($event['location'])
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-location-dot me-1"></i>{{ $event['location'] }}
                                        @endif
                                    </div>
                                </div>
                                <span class="badge bg-info">{!! $event['category'] !!}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">No upcoming events</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- System Overview -->
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="section-card">
                    <div class="section-title">
                        <i class="fas fa-server"></i>
                        System Overview
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center">
                                <div class="display-6 fw-bold">{!! number_format($systemTotals['total_records']) !!}</div>
                                <div class="text-muted small">Total Records</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center">
                                <div class="display-6 fw-bold">{!! $systemTotals['active_modules'] !!}</div>
                                <div class="text-muted small">Active Modules</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center">
                                <div class="display-6 fw-bold">{!! $systemTotals['database_tables'] !!}</div>
                                <div class="text-muted small">Database Tables</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center">
                                <div class="display-6 fw-bold">{!! $adminUsers !!}</div>
                                <div class="text-muted small">Admin Users</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Category Chart
        const ctx = document.getElementById('categoryChart').getContext('2d');
        const categoryData = @php
 
            $cats = [];
            $counts = [];
            foreach($catData as $cat) {
                $cats[] = $cat['name'];
                $counts[] = $cat['count'];
            }
            echo json_encode(['labels' => $cats, 'data' => $counts]);
        
@endphp;
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: categoryData.labels,
                datasets: [{
                    data: categoryData.data,
                    backgroundColor: ['#009ada', '#76bc21', '#f59e0b', '#8b5cf6', '#ef4444', '#10b981', '#ec4899', '#64748b'],
                    borderWidth: 0,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 11 },
                            boxWidth: 10
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
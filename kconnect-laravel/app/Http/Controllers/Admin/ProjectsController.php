<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Admin dashboard, rewards videos manager and system logs (was admin/dashboard.php, admin/manage-rewards.php, admin/system-logs.php). */
class ProjectsController extends Controller
{
    /** admin/manage-rewards.php */
    public function manageRewards(Request $request)
    {
        // Check if admin is logged in
        if (! session()->has('admin_id')) {
            kc_redirect('login.php');
        }

        // Handle delete rewards item
        $delete = $request->query('delete');
        if ($delete !== null && is_numeric($delete)) {
            $id = (int) $delete;

            // Get all attachments to delete files
            foreach (DB::select('SELECT file_path FROM rewards_attachments WHERE rewards_item_id = ?', [$id]) as $att) {
                $filepath = kc_public_file('../'.$att['file_path'], 'admin');
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
            }

            DB::delete('DELETE FROM rewards_items WHERE id = ?', [$id]);
            session(['message' => 'Rewards item deleted successfully!', 'message_type' => 'success']);
            kc_redirect('manage-rewards.php');
        }

        // Handle toggle status
        $toggle = $request->query('toggle');
        if ($toggle !== null && is_numeric($toggle)) {
            $id = (int) $toggle;
            DB::update('UPDATE rewards_items SET status = NOT status WHERE id = ?', [$id]);
            session(['message' => 'Status updated successfully!', 'message_type' => 'success']);
            kc_redirect('manage-rewards.php');
        }

        // Handle delete attachment
        $deleteAttachment = $request->query('delete_attachment');
        if ($deleteAttachment !== null && is_numeric($deleteAttachment)) {
            $att_id = (int) $deleteAttachment;
            $item_id = (int) $request->query('item_id');

            $att = DB::selectOne('SELECT file_path FROM rewards_attachments WHERE id = ?', [$att_id]);
            if ($att) {
                $filepath = kc_public_file('../'.$att['file_path'], 'admin');
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
            }

            DB::delete('DELETE FROM rewards_attachments WHERE id = ?', [$att_id]);
            session(['message' => 'Attachment deleted successfully!', 'message_type' => 'success']);
            kc_redirect('manage-rewards.php?edit='.$item_id);
        }

        // Handle form submission
        if ($request->isMethod('post') && $request->post('save_reward') !== null) {
            $title = (string) $request->post('title');
            $description = (string) $request->post('description');
            $video_type = (string) $request->post('video_type');
            $video_url = (string) $request->post('video_url');
            $video_id = '';
            $edit_id = $request->post('edit_id') !== null ? (int) $request->post('edit_id') : 0;

            // Validate
            if (empty($title) || empty($video_url)) {
                session(['message' => 'Title and Video URL are required!', 'message_type' => 'danger']);
                kc_redirect('manage-rewards.php'.($edit_id ? '?edit='.$edit_id : ''));
            }

            // Extract video ID based on type
            if ($video_type == 'youtube') {
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video_url, $matches);
                $video_id = $matches[1] ?? '';
            } elseif ($video_type == 'vimeo') {
                preg_match('/(?:vimeo\.com\/(?:video\/)?)(\d+)/', $video_url, $matches);
                $video_id = $matches[1] ?? '';
            } else {
                $video_id = $video_url;
            }

            if ($edit_id > 0) {
                // Update existing
                DB::update('UPDATE rewards_items SET
                title = ?,
                description = ?,
                video_type = ?,
                video_url = ?,
                video_id = ?
                WHERE id = ?', [$title, $description, $video_type, $video_url, $video_id, $edit_id]);
                session(['message' => 'Rewards item updated successfully!', 'message_type' => 'success']);
            } else {
                // Insert new
                DB::insert('INSERT INTO rewards_items (title, description, video_type, video_url, video_id)
                VALUES (?, ?, ?, ?, ?)', [$title, $description, $video_type, $video_url, $video_id]);
                $new_id = DB::getPdo()->lastInsertId();
                session(['message' => 'Rewards item added successfully!', 'message_type' => 'success']);

                // Handle file attachments
                $files = $request->file('attachments');
                if (is_array($files) && ! empty($files) && ($files[0] ?? null) !== null && $files[0]->getClientOriginalName() !== '') {
                    $upload_count = $this->uploadAll($files, $new_id);
                    if ($upload_count > 0) {
                        session(['message' => session('message')." $upload_count file(s) uploaded."]);
                    }
                }

                kc_redirect('manage-rewards.php?edit='.$new_id);
            }

            kc_redirect('manage-rewards.php'.($edit_id ? '?edit='.$edit_id : ''));
        }

        // Handle attachment upload for existing item
        if ($request->isMethod('post') && $request->post('upload_attachments') !== null) {
            $item_id = (int) $request->post('item_id');

            $files = $request->file('new_attachments');
            if (is_array($files) && ! empty($files) && ($files[0] ?? null) !== null && $files[0]->getClientOriginalName() !== '') {
                $upload_count = $this->uploadAll($files, $item_id);
                session(['message' => "$upload_count file(s) uploaded successfully!", 'message_type' => 'success']);
            }

            kc_redirect('manage-rewards.php?edit='.$item_id);
        }

        // Get all rewards items
        $rewards = DB::select('SELECT * FROM rewards_items ORDER BY sort_order, created_at DESC');

        // Attachment count per item (the old page queried it inside the table loop)
        $att_counts = [];
        foreach ($rewards as $item) {
            $att_counts[$item['id']] = DB::selectOne('SELECT COUNT(*) as total FROM rewards_attachments WHERE rewards_item_id = ?', [$item['id']])['total'];
        }

        // Get stats
        $total_videos = DB::selectOne('SELECT COUNT(*) as total FROM rewards_items')['total'] ?? 0;
        $active_videos = DB::selectOne('SELECT COUNT(*) as total FROM rewards_items WHERE status = 1')['total'] ?? 0;
        $total_attachments = DB::selectOne('SELECT COUNT(*) as total FROM rewards_attachments')['total'] ?? 0;
        $views_row = DB::selectOne('SELECT SUM(views_count) as total FROM rewards_items');
        $total_views = $views_row['total'] ?? 0;

        // Get item for editing
        $edit_item = null;
        $edit_attachments = null;
        $edit = $request->query('edit');
        if ($edit !== null && is_numeric($edit)) {
            $edit_id = (int) $edit;
            $edit_item = DB::selectOne('SELECT * FROM rewards_items WHERE id = ?', [$edit_id]);
            if ($edit_item) {
                $edit_attachments = DB::select('SELECT * FROM rewards_attachments WHERE rewards_item_id = ? ORDER BY created_at DESC', [$edit_id]);
            }
        }

        // Session flash shown once (old page unset it after printing)
        $session_message = session()->pull('message');
        $session_message_type = session()->pull('message_type');

        return view('admin.manage-rewards', compact(
            'rewards', 'att_counts', 'total_videos', 'active_videos', 'total_attachments', 'total_views',
            'edit_item', 'edit_attachments', 'session_message', 'session_message_type'
        ));
    }

    /** Old uploadAttachment() loop: stores each valid file in uploads/rewards/ and records it. */
    private function uploadAll(array $files, $rewards_id): int
    {
        $upload_count = 0;
        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }
            $target_dir = kc_upload_path('rewards/');
            if (! file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $name = $file->getClientOriginalName();
            $size = $file->getSize();
            $file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $new_filename = 'reward_'.$rewards_id.'_'.time().'_'.uniqid().'.'.$file_extension;

            try {
                $file->move($target_dir, $new_filename);
            } catch (\Throwable $e) {
                continue;
            }

            DB::insert('INSERT INTO rewards_attachments (rewards_item_id, file_name, file_path, file_size, file_type)
                                               VALUES (?, ?, ?, ?, ?)',
                [$rewards_id, $name, 'uploads/rewards/'.$new_filename, $size, $file_extension]);
            $upload_count++;
        }

        return $upload_count;
    }

    /** admin/dashboard.php */
    public function dashboard(Request $request)
    {
        // Check if admin is logged in
        if (! session()->has('admin_id') && ! session()->has('admin_username')) {
            kc_redirect('login.php');
        }

        $c = fn ($sql) => DB::selectOne($sql)['total'] ?? 0;

        // Content Management Stats
        $navCount = $c('SELECT COUNT(*) as total FROM navigation_links WHERE status = 1');
        $carouselCount = $c('SELECT COUNT(*) as total FROM homepage_carousel WHERE status = 1');
        $newsCount = $c('SELECT COUNT(*) as total FROM news_posts WHERE status = 1');
        $eventsCount = $c('SELECT COUNT(*) as total FROM event_items WHERE status = 1');
        $announcementsCount = $c('SELECT COUNT(*) as total FROM announcements WHERE status = 1');
        $ceoMessagesCount = $c('SELECT COUNT(*) as total FROM ceo_messages WHERE status = 1');
        $quickLinksCount = $c('SELECT COUNT(*) as total FROM quick_links WHERE status = 1');
        $topResourcesCount = $c('SELECT COUNT(*) as total FROM top_resources WHERE status = 1');
        $socialLinksCount = $c('SELECT COUNT(*) as total FROM social_links WHERE status = 1');

        // Gallery Stats
        $galleryItems = $c('SELECT COUNT(*) as total FROM gallery_items WHERE status = 1');
        $galleryCategories = $c('SELECT COUNT(*) as total FROM gallery_categories WHERE status = 1');
        $brandAssets = $c('SELECT COUNT(*) as total FROM brand_assets WHERE status = 1');
        $brandCategories = $c('SELECT COUNT(*) as total FROM brand_categories WHERE status = 1');

        // Employee Stats
        $totalEmployees = $c('SELECT COUNT(*) as total FROM employees WHERE status = 1');
        $totalCompanies = $c('SELECT COUNT(*) as total FROM companies WHERE status = 1');
        $totalDepartments = $c('SELECT COUNT(*) as total FROM departments WHERE status = 1');
        $keyContacts = $c('SELECT COUNT(*) as total FROM key_contacts WHERE status = 1');
        $orgChartCount = $c('SELECT COUNT(*) as total FROM organization_chart WHERE status = 1');
        $orgStructureCount = $c('SELECT COUNT(*) as total FROM orgchart_structure WHERE status = 1');
        $spotlightsCount = $c('SELECT COUNT(*) as total FROM employee_spotlights WHERE status = 1');

        // Birthday Stats
        $birthdayCount = $c('SELECT COUNT(*) as total FROM birthdays WHERE status = 1');
        $todayBirthdays = $c('SELECT COUNT(*) as total FROM birthdays WHERE MONTH(birth_date)=MONTH(CURDATE()) AND DAY(birth_date)=DAY(CURDATE()) AND status=1');
        $thisMonthBirthdays = $c('SELECT COUNT(*) as total FROM birthdays WHERE MONTH(birth_date)=MONTH(CURDATE()) AND status=1');

        // IT Support Stats
        $itTickets = $c('SELECT COUNT(*) as total FROM it_tickets');
        $itOpenTickets = $c("SELECT COUNT(*) as total FROM it_tickets WHERE status IN ('New', 'In Progress')");
        $itResolvedTickets = $c("SELECT COUNT(*) as total FROM it_tickets WHERE status IN ('Resolved', 'Closed')");
        $itQuestions = $c('SELECT COUNT(*) as total FROM it_questions WHERE status = 1');
        $backupLogs = $c('SELECT COUNT(*) as total FROM backup_logs');
        $todayBackups = $c('SELECT COUNT(*) as total FROM backup_logs WHERE backup_date = CURDATE()');

        // Asset Management Stats
        $totalAssets = $c('SELECT COUNT(*) as total FROM assets WHERE status = 1');
        $activeAssignments = $c("SELECT COUNT(*) as total FROM asset_assignments WHERE status = 'Active'");
        $maintenanceCount = $c("SELECT COUNT(*) as total FROM asset_maintenance WHERE status IN ('Scheduled', 'In Progress')");

        // Project Management Stats
        $totalProjects = $c('SELECT COUNT(*) as total FROM projects');
        $activeProjects = $c("SELECT COUNT(*) as total FROM projects WHERE status IN ('Planning', 'Active')");
        $totalTasks = $c('SELECT COUNT(*) as total FROM project_tasks');
        $openTasks = $c("SELECT COUNT(*) as total FROM project_tasks WHERE status IN ('To Do', 'In Progress', 'Review', 'Blocked')");
        $completedTasks = $c("SELECT COUNT(*) as total FROM project_tasks WHERE status = 'Done'");

        // Whistleblowing Stats
        $whistleblowingReports = $c('SELECT COUNT(*) as total FROM whistleblowing_reports');
        $openReports = $c("SELECT COUNT(*) as total FROM whistleblowing_reports WHERE status IN ('new', 'under_review', 'investigating')");
        $resolvedReports = $c("SELECT COUNT(*) as total FROM whistleblowing_reports WHERE status IN ('resolved', 'closed')");

        // Rewards & Recognition Stats
        $nominationCategories = $c('SELECT COUNT(*) as total FROM nomination_categories WHERE status = 1');
        $nominationQuestions = $c('SELECT COUNT(*) as total FROM nomination_questions WHERE status = 1');
        $totalNominations = $c('SELECT COUNT(*) as total FROM nominations');
        $pendingNominations = $c("SELECT COUNT(*) as total FROM nominations WHERE status = 'pending'");
        $approvedNominations = $c("SELECT COUNT(*) as total FROM nominations WHERE status = 'approved'");
        $rejectedNominations = $c("SELECT COUNT(*) as total FROM nominations WHERE status = 'rejected'");
        $unreadNominations = $c('SELECT COUNT(*) as total FROM nominations WHERE is_read = 0');
        $uniqueNominators = $c('SELECT COUNT(DISTINCT nominator_id) as total FROM nominations');
        $uniqueNominees = $c('SELECT COUNT(DISTINCT nominee_id) as total FROM nominations');
        $rewardsVideos = $c('SELECT COUNT(*) as total FROM rewards_items WHERE status = 1');
        $quarterWinners = $c('SELECT COUNT(*) as total FROM quarter_winners');
        $committeeMembers = $c('SELECT COUNT(*) as total FROM nomination_committee WHERE status = 1');

        // System Logs Stats
        $systemLogs = $c('SELECT COUNT(*) as total FROM system_logs');
        $todayLogs = $c('SELECT COUNT(*) as total FROM system_logs WHERE DATE(created_at) = CURDATE()');
        $adminUsers = $c('SELECT COUNT(*) as total FROM admin_users WHERE status = 1');

        // Recent Activity
        $recentNominations = DB::select('
    SELECT n.*,
           cat.name as category_name,
           nom.full_name as nominator_name,
           nomi.full_name as nominee_name
    FROM nominations n
    JOIN nomination_categories cat ON n.category_id = cat.id
    JOIN employees nom ON n.nominator_id = nom.id
    JOIN employees nomi ON n.nominee_id = nomi.id
    ORDER BY n.created_at DESC
    LIMIT 5
');

        // Get category breakdown for nominations
        $categoryBreakdown = DB::select('
    SELECT cat.name, COUNT(n.id) as count
    FROM nomination_categories cat
    LEFT JOIN nominations n ON cat.id = n.category_id
    WHERE cat.status = 1
    GROUP BY cat.id
    ORDER BY count DESC
');

        // Get top nominees
        $topNominees = DB::select("
    SELECT e.full_name, e.employee_code, e.department, COUNT(n.id) as votes
    FROM nominations n
    JOIN employees e ON n.nominee_id = e.id
    WHERE n.status != 'rejected'
    GROUP BY n.nominee_id
    ORDER BY votes DESC
    LIMIT 5
");

        // Get recent news
        $recentNews = DB::select('SELECT * FROM news_posts WHERE status = 1 ORDER BY created_at DESC LIMIT 3');

        // Get upcoming events
        $upcomingEvents = DB::select('SELECT * FROM event_items WHERE status = 1 AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3');

        // Get recent tickets
        $recentTickets = DB::select('
    SELECT t.*, e.full_name as employee_name, a.full_name as assigned_to_name
    FROM it_tickets t
    LEFT JOIN employees e ON t.employee_id = e.id
    LEFT JOIN employees a ON t.assigned_to = a.id
    ORDER BY t.created_at DESC
    LIMIT 5
');

        // Get recent backups
        $recentBackups = DB::select('
    SELECT bl.*, e.full_name as employee_name
    FROM backup_logs bl
    LEFT JOIN employees e ON bl.employee_id = e.id
    ORDER BY bl.created_at DESC
    LIMIT 5
');

        // Get system totals
        $systemTotals = [
            'total_records' => $navCount + $carouselCount + $newsCount + $eventsCount + $galleryItems + $brandAssets + $totalEmployees + $birthdayCount + $keyContacts + $totalNominations + $rewardsVideos + $itTickets + $totalAssets + $totalProjects + $totalTasks + $whistleblowingReports + $backupLogs,
            'active_modules' => 15,
            'database_tables' => 50,
        ];

        unset($c, $request);

        return view('admin.dashboard', get_defined_vars());
    }

    /** admin/system-logs.php */
    public function systemLogs(Request $request)
    {
        // Check if admin is logged in
        if (! session()->has('admin_id') && ! session()->has('admin_username')) {
            kc_redirect('login.php');
        }

        // CREATE LOGS TABLE IF NOT EXISTS (the table always exists, kept as the old page ran it)
        DB::statement("
CREATE TABLE IF NOT EXISTS `system_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` enum('admin','employee') NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `action` varchar(100) NOT NULL COMMENT 'login, logout, page_view, create, update, delete, registration',
  `page_accessed` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_type` (`user_type`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`),
  KEY `idx_user_type_action` (`user_type`,`action`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='System activity and login logs';
");

        $logActivity = function ($userType, $userId, $username, $fullName, $action, $pageAccessed = null, $details = null) use ($request) {
            $ip = $request->ip() ?? 'Unknown';
            $userAgent = substr($request->userAgent() ?? 'Unknown', 0, 500);
            DB::insert('INSERT INTO system_logs (user_type, user_id, username, full_name, action, page_accessed, ip_address, user_agent, details)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', [$userType, (int) $userId, $username, $fullName, $action, $pageAccessed, $ip, $userAgent, $details]);
        };

        // Log this admin page view
        $adminId = (int) (session('admin_id') ?? 0);
        $adminUsername = session('admin_username') ?? 'Unknown';
        $logActivity('admin', $adminId, $adminUsername, $adminUsername, 'page_view', 'system-logs.php', 'Viewed system logs page');

        // HANDLE CLEAR OLD LOGS
        if ($request->post('clear_logs') !== null && $request->post('days') !== null) {
            $days = (int) $request->post('days');
            if ($days > 0) {
                $deleteDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
                $deletedCount = DB::delete('DELETE FROM system_logs WHERE created_at < ?', [$deleteDate]);
                session(['message' => "Deleted $deletedCount log entries older than $days days.", 'message_type' => 'success']);
                $logActivity('admin', $adminId, $adminUsername, $adminUsername, 'delete', 'system-logs.php', "Cleared logs older than $days days. Removed $deletedCount entries.");
            }
            kc_redirect('system-logs.php');
        }

        // GET RECENTLY LOGGED IN EMPLOYEES
        $recentLogins = DB::select("
    SELECT
        l.user_id,
        l.username,
        l.full_name,
        l.ip_address,
        l.created_at as login_time,
        e.photo,
        e.department,
        e.position,
        e.employee_code,
        e.role,
        e.status as employee_status,
        (SELECT COUNT(*) FROM system_logs WHERE user_id = l.user_id AND user_type = 'employee' AND action = 'login' AND DATE(created_at) = CURDATE()) as today_logins,
        (SELECT created_at FROM system_logs WHERE user_id = l.user_id AND user_type = 'employee' AND action = 'login' ORDER BY created_at DESC LIMIT 1 OFFSET 1) as previous_login
    FROM system_logs l
    LEFT JOIN employees e ON l.user_id = e.id
    WHERE l.user_type = 'employee'
        AND l.action = 'login'
        AND l.user_id > 0
    GROUP BY l.user_id
    ORDER BY l.created_at DESC
    LIMIT 20
");

        // GET NEWLY JOINED EMPLOYEES
        $newEmployees = DB::select("
    SELECT
        e.id,
        e.employee_code,
        e.full_name,
        e.email,
        e.phone,
        e.department,
        e.position,
        e.role,
        e.photo,
        e.status,
        e.join_date,
        e.created_at as registration_date,
        e.last_login,
        (SELECT COUNT(*) FROM system_logs WHERE user_id = e.id AND user_type = 'employee') as total_activity,
        (SELECT action FROM system_logs WHERE user_id = e.id AND user_type = 'employee' ORDER BY created_at DESC LIMIT 1) as last_action,
        (SELECT created_at FROM system_logs WHERE user_id = e.id AND user_type = 'employee' ORDER BY created_at DESC LIMIT 1) as last_action_time
    FROM employees e
    WHERE e.id > 0
    ORDER BY e.created_at DESC
    LIMIT 15
");

        // GET TODAY'S ACTIVITY SUMMARY
        $todayData = DB::selectOne("
    SELECT
        COUNT(CASE WHEN action = 'login' AND user_type = 'employee' THEN 1 END) as today_logins,
        COUNT(CASE WHEN action = 'registration' AND user_type = 'employee' THEN 1 END) as today_registrations,
        COUNT(CASE WHEN action = 'page_view' AND user_type = 'employee' THEN 1 END) as today_page_views,
        COUNT(DISTINCT CASE WHEN user_type = 'employee' THEN user_id END) as unique_active_users,
        COUNT(*) as total_today_activity
    FROM system_logs
    WHERE DATE(created_at) = CURDATE()
");

        // GET RECENT USER ACTIVITY STREAM
        $recentActivity = DB::select("
    SELECT
        l.*,
        e.photo,
        e.department,
        e.position,
        e.employee_code
    FROM system_logs l
    LEFT JOIN employees e ON l.user_id = e.id
    WHERE l.user_type = 'employee'
        AND l.user_id > 0
        AND l.action IN ('login', 'logout', 'page_view', 'registration')
    ORDER BY l.created_at DESC
    LIMIT 30
");

        // FILTERS FOR MAIN LOGS TABLE
        $whereClauses = [];
        $bindings = [];

        $dateFrom = $request->query('date_from') ?? '';
        $dateTo = $request->query('date_to') ?? '';
        $userType = $request->query('user_type') ?? '';
        $actionFilter = $request->query('action') ?? '';
        $searchTerm = $request->query('search') ?? '';
        $page = $request->query('page') !== null ? max(1, (int) $request->query('page')) : 1;
        $perPage = 50;

        if (! empty($dateFrom)) {
            $whereClauses[] = 'DATE(created_at) >= ?';
            $bindings[] = $dateFrom;
        }
        if (! empty($dateTo)) {
            $whereClauses[] = 'DATE(created_at) <= ?';
            $bindings[] = $dateTo;
        }
        if (! empty($userType)) {
            $whereClauses[] = 'user_type = ?';
            $bindings[] = $userType;
        }
        if (! empty($actionFilter)) {
            $whereClauses[] = 'action = ?';
            $bindings[] = $actionFilter;
        }
        if (! empty($searchTerm)) {
            $whereClauses[] = '(username LIKE ? OR full_name LIKE ? OR details LIKE ? OR page_accessed LIKE ?)';
            array_push($bindings, "%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%");
        }

        $whereSQL = ! empty($whereClauses) ? 'WHERE '.implode(' AND ', $whereClauses) : '';

        // Get total count for pagination
        $totalLogs = DB::selectOne("SELECT COUNT(*) as total FROM system_logs $whereSQL", $bindings)['total'] ?? 0;
        $totalPages = ceil($totalLogs / $perPage);
        $offset = ($page - 1) * $perPage;

        // Get logs
        $logsResult = DB::select("SELECT * FROM system_logs $whereSQL ORDER BY created_at DESC LIMIT $offset, $perPage", $bindings);

        // Get stats
        $todayLogins = DB::selectOne("SELECT COUNT(*) as total FROM system_logs WHERE action IN ('login', 'logout') AND DATE(created_at) = CURDATE()")['total'] ?? 0;
        $todayActivity = DB::selectOne('SELECT COUNT(*) as total FROM system_logs WHERE DATE(created_at) = CURDATE()')['total'] ?? 0;
        $uniqueUsersToday = DB::selectOne("SELECT COUNT(DISTINCT CONCAT(user_type, '-', user_id)) as total FROM system_logs WHERE DATE(created_at) = CURDATE()")['total'] ?? 0;
        $totalLogEntries = DB::selectOne('SELECT COUNT(*) as total FROM system_logs')['total'] ?? 0;

        // Get action breakdown
        $actionBreakdown = DB::select('SELECT action, COUNT(*) as count FROM system_logs GROUP BY action ORDER BY count DESC');

        // Get hourly activity for today
        $hourlyActivity = DB::select('SELECT HOUR(created_at) as hour, COUNT(*) as count
     FROM system_logs
     WHERE DATE(created_at) = CURDATE()
     GROUP BY HOUR(created_at)
     ORDER BY hour ASC');

        // Session flash shown once (old page unset it after printing)
        $session_message = session()->pull('message');
        $session_message_type = session()->pull('message_type');

        unset($logActivity, $request, $bindings);

        return view('admin.system-logs', get_defined_vars());
    }
}

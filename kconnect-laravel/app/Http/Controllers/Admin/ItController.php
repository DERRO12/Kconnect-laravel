<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ItHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** IT support tickets and backup logs, admin side (admin/it-*.php, admin/backup*.php). */
class ItController extends Controller
{
    /**
     * admin/it-support.php: the old file starts with "<?php\n<?php" and never ran
     * (PHP parse error: HTTP 500 with an empty body).
     */
    public function support(Request $request)
    {
        return response('', 500);
    }

    /**
     * admin/it-ticket-data.php: required 'config.php' relative to admin/, which does not exist
     * (fatal error before any output: HTTP 500 with an empty body).
     */
    public function ticketData(Request $request)
    {
        return response('', 500);
    }

    /** admin/it-ticket-detail.php: ticket detail fragment */
    public function ticketDetail(Request $request)
    {
        if (! isLoggedIn()) {
            kc_exit('Unauthorized');
        }

        $ticket_id = $request->query('id') !== null ? (int) $request->query('id') : 0;
        $employee_id = session('employee_id');
        $is_admin_view = $request->query('is_admin') !== null ? (int) $request->query('is_admin') : 0;
        $is_admin = $is_admin_view == 1;

        $ticket = DB::selectOne('SELECT t.*, e.full_name as employee_name, e.employee_code, e.department
          FROM it_tickets t
          JOIN employees e ON t.employee_id = e.id
          WHERE t.id = ?', [$ticket_id]);

        if (! $ticket) {
            kc_exit('<div class="text-center py-5 text-danger">Ticket not found</div>');
        }

        $answers = DB::select('SELECT q.question_text, q.question_type, a.answer
                  FROM it_answers a
                  JOIN it_questions q ON a.question_id = q.id
                  WHERE a.ticket_id = ?
                  ORDER BY q.sort_order', [$ticket_id]);

        $comments = DB::select('SELECT c.*, e.full_name, e.employee_code, e.photo
                   FROM it_ticket_comments c
                   JOIN employees e ON c.employee_id = e.id
                   WHERE c.ticket_id = ?
                   ORDER BY c.created_at ASC', [$ticket_id]);

        $statuses = ['New', 'In Progress', 'Resolved', 'Closed'];

        return view('admin.it-ticket-detail', compact('ticket', 'ticket_id', 'answers', 'comments', 'statuses', 'is_admin'));
    }

    /** getDateRangeData() of admin/it-admin.php */
    private static function getDateRangeData($period = 'all')
    {
        $date_condition = '';
        switch ($period) {
            case 'today': $date_condition = 'AND DATE(t.created_at) = CURDATE()';
                break;
            case 'yesterday': $date_condition = 'AND DATE(t.created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
                break;
            case 'week': $date_condition = 'AND t.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
                break;
            case 'month': $date_condition = 'AND t.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
                break;
            case 'quarter': $date_condition = 'AND t.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)';
                break;
            case 'year': $date_condition = 'AND t.created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)';
                break;
        }

        return $date_condition;
    }

    private static function getDateRangeLabel($period)
    {
        $labels = [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'week' => 'Last 7 Days',
            'month' => 'Last 30 Days',
            'quarter' => 'Last 90 Days',
            'year' => 'Last Year',
            'all' => 'All Time',
        ];

        return $labels[$period] ?? 'All Time';
    }

    /**
     * admin/it-admin.php: IT analytics dashboard, close/delete ticket, Excel export, print view.
     * The search text goes into the SQL text escaped (kc_escape) exactly like before, because the
     * same fragment is shared by several queries (one of them fails on it, as it did before).
     */
    public function admin(Request $request)
    {
        if (! session()->has('admin_id') && ! session()->has('admin_username')) {
            kc_redirect('../admin/login.php');
        }

        $admin_id = session('admin_id') ?? 1;
        $admin_name = session('admin_name') ?? (session('admin_username') ?? 'Admin');
        $admin_email = session('admin_email') ?? '';

        // ─── HANDLE TICKET CLOSE
        if ($request->isMethod('post') && $request->has('close_ticket')) {
            $ticket_id = (int) $request->input('ticket_id');
            $ticket_number = kc_escape($request->input('ticket_number') ?? '');
            $close_notes = $request->input('close_notes') ?? '';

            $current = DB::selectOne('SELECT status FROM it_tickets WHERE id = ?', [$ticket_id]);
            $from_status = $current['status'] ?? 'Unknown';

            DB::update("UPDATE it_tickets SET status = 'Closed', closed_at = NOW(), updated_at = NOW() WHERE id = ?", [$ticket_id]);

            DB::insert("INSERT INTO it_ticket_status_history (ticket_id, from_status, to_status, changed_by, notes) 
                         VALUES (?, ?, 'Closed', ?, ?)", [$ticket_id, $from_status, $admin_id, $close_notes]);

            if (! empty(kc_escape($close_notes))) {
                DB::insert('INSERT INTO it_ticket_comments (ticket_id, employee_id, comment, is_admin) 
                             VALUES (?, ?, ?, 1)', [$ticket_id, $admin_id, 'Ticket closed by admin: '.$close_notes]);
            }

            session(['message' => "Ticket #$ticket_number closed successfully!", 'message_type' => 'success']);
            kc_redirect('it-admin.php');
        }

        // ─── HANDLE TICKET DELETE
        if ($request->isMethod('post') && $request->has('delete_ticket')) {
            $ticket_id = (int) $request->input('ticket_id');

            foreach (DB::select('SELECT file_path FROM it_ticket_attachments WHERE ticket_id = ?', [$ticket_id]) as $att) {
                $file_path = kc_public_file('../'.ltrim($att['file_path'], './'), 'admin');
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }

            DB::delete('DELETE FROM it_ticket_attachments WHERE ticket_id = ?', [$ticket_id]);
            DB::delete('DELETE FROM it_ticket_comments WHERE ticket_id = ?', [$ticket_id]);
            DB::delete('DELETE FROM it_ticket_status_history WHERE ticket_id = ?', [$ticket_id]);
            DB::delete('DELETE FROM it_ticket_assignments WHERE ticket_id = ?', [$ticket_id]);
            DB::delete('DELETE FROM it_answers WHERE ticket_id = ?', [$ticket_id]);
            DB::delete('DELETE FROM it_tickets WHERE id = ?', [$ticket_id]);

            session(['message' => 'Ticket #'.($request->input('ticket_number') ?? '').' deleted successfully!', 'message_type' => 'warning']);
            kc_redirect('it-admin.php');
        }

        // ─── HANDLE EXPORT (Excel)
        if ($request->query('export') !== null) {
            $period = $request->query('period') !== null ? $request->query('period') : 'month';
            $selected_admin = $request->query('admin') !== null ? (int) $request->query('admin') : 0;
            $date_condition = self::getDateRangeData($period);
            $admin_filter = $selected_admin > 0 ? "AND t.assigned_to = $selected_admin" : '';
            $search = $request->query('search') !== null ? kc_escape($request->query('search')) : '';
            $search_filter = '';
            if ($search) {
                $search_filter = "AND (t.ticket_number LIKE '%$search%' OR t.subject LIKE '%$search%' OR e.full_name LIKE '%$search%' OR e.employee_code LIKE '%$search%')";
            }

            $export_result = DB::select("SELECT t.ticket_number, t.subject, t.category, t.priority, t.status,
                     e.full_name as employee_name, e.employee_code, e.department,
                     a.full_name as assigned_to_name,
                     t.created_at, t.resolved_at, t.closed_at,
                     (SELECT COUNT(*) FROM it_ticket_comments WHERE ticket_id = t.id) as comments,
                     (SELECT COUNT(*) FROM it_ticket_attachments WHERE ticket_id = t.id) as attachments
                     FROM it_tickets t 
                     JOIN employees e ON t.employee_id = e.id 
                     LEFT JOIN employees a ON t.assigned_to = a.id 
                     WHERE 1=1 $date_condition $admin_filter $search_filter
                     ORDER BY t.created_at DESC");

            $filename = 'IT_Tickets_Report_'.date('Y-m-d_His').'.xls';

            $admin_name_export = '';
            if ($selected_admin > 0) {
                $adm = DB::selectOne('SELECT full_name FROM employees WHERE id=?', [$selected_admin]);
                $admin_name_export = $adm['full_name'] ?? '';
            }
            $date_range = self::getDateRangeLabel($period);

            $out = '<html><head><meta charset="UTF-8"><style>
        body { font-family: Arial; font-size: 11px; }
        h2 { color: #009ada; }
        table { border-collapse: collapse; width: 100%; }
        th { background: #009ada; color: white; padding: 8px; text-align: left; }
        td { padding: 6px 8px; border: 1px solid #ddd; }
        .header-info { margin-bottom: 15px; padding: 10px; background: #f5f5f5; }
        .footer-info { margin-top: 15px; padding: 10px; background: #f5f5f5; border-top: 1px solid #ddd; }
        .status-New { color: #dc2626; font-weight: bold; }
        .status-In-Progress { color: #2563eb; font-weight: bold; }
        .status-Resolved { color: #16a34a; font-weight: bold; }
        .status-Closed { color: #6b7280; }
    </style></head><body>';

            $out .= '<h2>IT Support Tickets Report</h2>';
            $out .= '<div class="header-info">';
            $out .= '<p><strong>Generated:</strong> '.date('Y-m-d H:i:s').'</p>';
            $out .= '<p><strong>Period:</strong> '.$date_range.'</p>';
            if ($admin_name_export) {
                $out .= '<p><strong>Admin Filter:</strong> '.htmlspecialchars($admin_name_export).'</p>';
            }
            if ($search) {
                $out .= '<p><strong>Search:</strong> '.htmlspecialchars($search).'</p>';
            }
            $out .= '<p><strong>Total Records:</strong> '.count($export_result).'</p>';
            $out .= '</div>';

            $out .= '<table><thead><tr>
        <th>#</th><th>Ticket #</th><th>Employee</th><th>Department</th>
        <th>Subject</th><th>Category</th><th>Priority</th><th>Status</th>
        <th>Assigned To</th><th>Created</th><th>Resolved</th><th>Closed</th><th>Comments</th>
    </tr></thead><tbody>';

            $counter = 1;
            foreach ($export_result as $row) {
                $out .= '<tr>';
                $out .= '<td>'.$counter++.'</td>';
                $out .= '<td>'.$row['ticket_number'].'</td>';
                $out .= '<td>'.htmlspecialchars($row['employee_name']).' ('.$row['employee_code'].')</td>';
                $out .= '<td>'.htmlspecialchars($row['department'] ?? 'N/A').'</td>';
                $out .= '<td>'.htmlspecialchars($row['subject']).'</td>';
                $out .= '<td>'.$row['category'].'</td>';
                $out .= '<td>'.$row['priority'].'</td>';
                $out .= '<td class="status-'.str_replace(' ', '-', $row['status']).'">'.$row['status'].'</td>';
                $out .= '<td>'.htmlspecialchars($row['assigned_to_name'] ?? 'N/A').'</td>';
                $out .= '<td>'.date('Y-m-d', strtotime($row['created_at'])).'</td>';
                $out .= '<td>'.($row['resolved_at'] ? date('Y-m-d', strtotime($row['resolved_at'])) : '-').'</td>';
                $out .= '<td>'.($row['closed_at'] ? date('Y-m-d', strtotime($row['closed_at'])) : '-').'</td>';
                $out .= '<td>'.$row['comments'].'</td>';
                $out .= '</tr>';
            }

            $out .= '</tbody></table>';

            $out .= '<div class="footer-info">';
            $out .= '<p style="font-size:10px;color:#666;">This is a computer-generated report. | K-Connect Intranet Portal</p>';
            $out .= '<table style="border:none;width:100%;margin-top:10px;">';
            $out .= '<tr>';
            $out .= '<td style="border:none;width:33%;"><hr style="width:80%;"><p style="font-size:10px;margin:0;">Checked By</p></td>';
            $out .= '<td style="border:none;width:33%;"><hr style="width:80%;"><p style="font-size:10px;margin:0;">Approved By</p></td>';
            $out .= '<td style="border:none;width:33%;"><hr style="width:80%;"><p style="font-size:10px;margin:0;">Date</p></td>';
            $out .= '</tr>';
            $out .= '</table>';
            $out .= '</div>';

            $out .= '</body></html>';

            return response($out, 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        }

        // ─── HANDLE PRINT
        if ($request->query('print') !== null) {
            $period = $request->query('period') !== null ? $request->query('period') : 'month';
            $selected_admin = $request->query('admin') !== null ? (int) $request->query('admin') : 0;
            $search = $request->query('search') !== null ? kc_escape($request->query('search')) : '';
            $date_condition = self::getDateRangeData($period);
            $admin_filter = $selected_admin > 0 ? "AND assigned_to = $selected_admin" : '';

            $date_condition_clean = str_replace('t.', '', $date_condition);

            $print_result = DB::select("SELECT t.*, e.full_name as employee_name, e.employee_code, e.department,
                     a.full_name as assigned_to_name
                     FROM it_tickets t 
                     JOIN employees e ON t.employee_id = e.id 
                     LEFT JOIN employees a ON t.assigned_to = a.id 
                     WHERE 1=1 $date_condition $admin_filter
                     ORDER BY t.created_at DESC");
            $print_total = count($print_result);

            $print_stats = DB::selectOne("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status='New' THEN 1 ELSE 0 END) as new_count,
        SUM(CASE WHEN status='In Progress' THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN status='Resolved' THEN 1 ELSE 0 END) as resolved,
        SUM(CASE WHEN status='Closed' THEN 1 ELSE 0 END) as closed
        FROM it_tickets WHERE 1=1 $date_condition_clean $admin_filter");

            $admin_name_print = '';
            if ($selected_admin > 0) {
                $adm = DB::selectOne('SELECT full_name FROM employees WHERE id=?', [$selected_admin]);
                $admin_name_print = $adm['full_name'] ?? '';
            }
            $date_range = self::getDateRangeLabel($period);

            return view('admin.it-admin-print', compact('date_range', 'admin_name_print', 'search', 'print_total', 'admin_name', 'print_stats', 'print_result'));
        }

        // ─── Period & Filters
        $period = $request->query('period') !== null ? $request->query('period') : 'month';
        $selected_admin = $request->query('admin') !== null ? (int) $request->query('admin') : 0;
        $search = $request->query('search') !== null ? kc_escape($request->query('search')) : '';
        $date_condition = self::getDateRangeData($period);
        $admin_filter = $selected_admin > 0 ? "AND t.assigned_to = $selected_admin" : '';
        $search_filter = '';
        if ($search) {
            $search_filter = "AND (t.ticket_number LIKE '%$search%' OR t.subject LIKE '%$search%' OR e.full_name LIKE '%$search%' OR e.employee_code LIKE '%$search%')";
        }

        $admins = DB::select("SELECT id, full_name, employee_code, email, department, photo FROM employees 
                 WHERE role IN ('admin', 'manager', 'director', 'hr') AND status = 1 
                 ORDER BY full_name");

        $overall = DB::selectOne("SELECT 
    COUNT(*) as total_tickets,
    SUM(CASE WHEN t.status = 'New' THEN 1 ELSE 0 END) as new_count,
    SUM(CASE WHEN t.status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN t.status = 'Resolved' THEN 1 ELSE 0 END) as resolved,
    SUM(CASE WHEN t.status = 'Closed' THEN 1 ELSE 0 END) as closed,
    SUM(CASE WHEN t.status IN ('Resolved', 'Closed') THEN 1 ELSE 0 END) as total_resolved,
    SUM(CASE WHEN t.priority = 'Urgent' THEN 1 ELSE 0 END) as urgent,
    ROUND(AVG(CASE WHEN t.status IN ('Resolved', 'Closed') AND (t.resolved_at IS NOT NULL OR t.closed_at IS NOT NULL) THEN 
        TIMESTAMPDIFF(MINUTE, t.created_at, COALESCE(t.resolved_at, t.closed_at))
    END), 0) as avg_resolution_minutes
    FROM it_tickets t WHERE 1=1 $date_condition $admin_filter $search_filter") ?: [];

        $total_tickets = $overall['total_tickets'] ?? 0;
        $total_resolved = $overall['total_resolved'] ?? 0;
        $resolution_rate = $total_tickets > 0 ? round(($total_resolved / $total_tickets) * 100, 1) : 0;
        $avg_res_hours = round(($overall['avg_resolution_minutes'] ?? 0) / 60, 1);

        $backlog = DB::selectOne("SELECT COUNT(*) as backlog FROM it_tickets t 
    WHERE t.status IN ('New', 'In Progress') 
    AND t.created_at < DATE_SUB(NOW(), INTERVAL 48 HOUR) $admin_filter $search_filter") ?: [];

        // ─── ADMIN PERFORMANCE
        $admin_performance = [];
        foreach ($admins as $admin) {
            $aid = (int) $admin['id'];
            $perf = DB::selectOne("SELECT 
        COUNT(*) as total_assigned, SUM(CASE WHEN t.status='New' THEN 1 ELSE 0 END) as new_count,
        SUM(CASE WHEN t.status='In Progress' THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN t.status IN ('Resolved','Closed') THEN 1 ELSE 0 END) as total_resolved,
        SUM(CASE WHEN t.priority='Urgent' AND t.status IN ('Resolved','Closed') THEN 1 ELSE 0 END) as urgent_resolved,
        ROUND(AVG(CASE WHEN t.status IN ('Resolved','Closed') AND t.resolved_at IS NOT NULL THEN TIMESTAMPDIFF(HOUR,t.created_at,t.resolved_at) END),1) as avg_hours
        FROM it_tickets t WHERE t.assigned_to=$aid $date_condition") ?: [];
            $week = DB::selectOne("SELECT COUNT(*) as cnt FROM it_tickets WHERE assigned_to=$aid AND status IN('Resolved','Closed') AND resolved_at>=DATE_SUB(NOW(),INTERVAL 7 DAY)") ?: [];
            $comm = DB::selectOne("SELECT COUNT(*) as cnt FROM it_ticket_comments c JOIN it_tickets t ON c.ticket_id=t.id WHERE c.employee_id=$aid AND c.is_admin=1 $date_condition") ?: [];

            $ta = (int) ($perf['total_assigned'] ?? 0);
            $tr = (int) ($perf['total_resolved'] ?? 0);
            $admin_performance[] = [
                'id' => $admin['id'], 'name' => $admin['full_name'], 'code' => $admin['employee_code'] ?? '', 'department' => $admin['department'] ?? 'N/A', 'photo' => $admin['photo'] ?? '',
                'total_assigned' => $ta, 'new_count' => (int) ($perf['new_count'] ?? 0), 'in_progress' => (int) ($perf['in_progress'] ?? 0),
                'total_resolved' => $tr, 'urgent_resolved' => (int) ($perf['urgent_resolved'] ?? 0),
                'resolution_rate' => $ta > 0 ? round(($tr / $ta) * 100, 1) : 0, 'avg_resolution_hours' => (float) ($perf['avg_hours'] ?? 0),
                'total_comments' => (int) ($comm['cnt'] ?? 0), 'resolved_this_week' => (int) ($week['cnt'] ?? 0),
            ];
        }
        usort($admin_performance, fn ($a, $b) => $b['total_resolved'] - $a['total_resolved']);

        // ─── TICKETS FOR TABLE
        $tickets = DB::select("SELECT t.*, e.full_name as employee_name, e.employee_code, e.department,
                  a.full_name as assigned_to_name,
                  (SELECT COUNT(*) FROM it_ticket_comments WHERE ticket_id=t.id) as comment_count,
                  (SELECT COUNT(*) FROM it_ticket_attachments WHERE ticket_id=t.id) as attachment_count
                  FROM it_tickets t 
                  JOIN employees e ON t.employee_id=e.id 
                  LEFT JOIN employees a ON t.assigned_to=a.id 
                  WHERE 1=1 $date_condition $admin_filter $search_filter
                  ORDER BY t.created_at DESC LIMIT 100");

        // Chart data
        $categories = DB::select("SELECT t.category, COUNT(*) as count FROM it_tickets t WHERE 1=1 $date_condition $admin_filter GROUP BY t.category ORDER BY count DESC");
        $priorities = DB::select("SELECT t.priority, COUNT(*) as count, SUM(CASE WHEN t.status IN('Resolved','Closed') THEN 1 ELSE 0 END) as resolved, SUM(CASE WHEN t.status IN('New','In Progress') THEN 1 ELSE 0 END) as pending FROM it_tickets t WHERE 1=1 $date_condition $admin_filter GROUP BY t.priority ORDER BY FIELD(t.priority,'Urgent','High','Medium','Low')");
        $monthly_trend = DB::select("SELECT DATE_FORMAT(t.created_at,'%b %y') as label, COUNT(*) as total, SUM(CASE WHEN t.status IN('Resolved','Closed') THEN 1 ELSE 0 END) as resolved, SUM(CASE WHEN t.status IN('New','In Progress') THEN 1 ELSE 0 END) as pending, SUM(CASE WHEN t.priority='Urgent' THEN 1 ELSE 0 END) as urgent FROM it_tickets t WHERE t.created_at>=DATE_SUB(NOW(),INTERVAL 12 MONTH) $admin_filter GROUP BY DATE_FORMAT(t.created_at,'%Y-%m'), DATE_FORMAT(t.created_at,'%b %y') ORDER BY MIN(t.created_at)");
        $weekly_pattern = DB::select("SELECT DAYNAME(t.created_at) as day_name, DAYOFWEEK(t.created_at) as day_num, COUNT(*) as count FROM it_tickets t WHERE t.created_at>=DATE_SUB(NOW(),INTERVAL 90 DAY) $admin_filter GROUP BY DAYNAME(t.created_at), DAYOFWEEK(t.created_at) ORDER BY day_num");
        $response_dist = DB::select("SELECT CASE WHEN hours<0.5 THEN '<30 min' WHEN hours<1 THEN '30-60 min' WHEN hours<2 THEN '1-2 hrs' WHEN hours<4 THEN '2-4 hrs' WHEN hours<8 THEN '4-8 hrs' WHEN hours<24 THEN '8-24 hrs' WHEN hours<48 THEN '1-2 days' ELSE '>2 days' END as bucket, COUNT(*) as count FROM (SELECT TIMESTAMPDIFF(MINUTE,t.created_at,COALESCE(t.resolved_at,t.closed_at))/60.0 as hours FROM it_tickets t WHERE t.status IN('Resolved','Closed') AND COALESCE(t.resolved_at,t.closed_at) IS NOT NULL $date_condition $admin_filter) sub GROUP BY bucket ORDER BY MIN(hours)");
        $departments = DB::select("SELECT COALESCE(e.department,'Unknown') as department, COUNT(*) as total, SUM(CASE WHEN t.status IN('Resolved','Closed') THEN 1 ELSE 0 END) as resolved, SUM(CASE WHEN t.status IN('New','In Progress') THEN 1 ELSE 0 END) as pending FROM it_tickets t JOIN employees e ON t.employee_id=e.id WHERE 1=1 $date_condition $admin_filter GROUP BY e.department HAVING total>0 ORDER BY total DESC LIMIT 12");
        $daily_volume = DB::select("SELECT DATE(t.created_at) as date, DATE_FORMAT(t.created_at,'%a %d') as label, COUNT(*) as created, SUM(CASE WHEN t.status IN('Resolved','Closed') AND DATE(COALESCE(t.resolved_at,t.closed_at))=DATE(t.created_at) THEN 1 ELSE 0 END) as same_day FROM it_tickets t WHERE t.created_at>=DATE_SUB(NOW(),INTERVAL 30 DAY) $admin_filter GROUP BY DATE(t.created_at), DATE_FORMAT(t.created_at,'%a %d') ORDER BY date ASC");

        // SLA
        $sla_thresholds = ['Urgent' => 4, 'High' => 8, 'Medium' => 24, 'Low' => 48];
        $sla_data = [];
        foreach ($sla_thresholds as $priority => $hours) {
            $sla = DB::selectOne("SELECT COUNT(*) as total, SUM(CASE WHEN t.status IN('Resolved','Closed') THEN 1 ELSE 0 END) as resolved, SUM(CASE WHEN t.status IN('Resolved','Closed') AND TIMESTAMPDIFF(HOUR,t.created_at,COALESCE(t.resolved_at,t.closed_at))<=$hours THEN 1 ELSE 0 END) as within_sla FROM it_tickets t WHERE t.priority='$priority' $date_condition $admin_filter") ?: [];
            $rc = (int) ($sla['resolved'] ?? 0);
            $ws = (int) ($sla['within_sla'] ?? 0);
            $sla_data[] = ['priority' => $priority, 'threshold' => $hours.'h', 'total' => (int) ($sla['total'] ?? 0), 'resolved' => $rc, 'within_sla' => $ws, 'compliance' => $rc > 0 ? round(($ws / $rc) * 100, 1) : 0];
        }

        // Workload
        $workload_data = [];
        foreach ($admin_performance as $ap) {
            if ($ap['total_assigned'] > 0) {
                $workload_data[] = ['name' => $ap['name'], 'assigned' => $ap['total_assigned'], 'resolved' => $ap['total_resolved'], 'pending' => $ap['new_count'] + $ap['in_progress']];
            }
        }

        $top_resolvers = array_slice($admin_performance, 0, 8);

        $perf_trend = [];
        if ($selected_admin > 0) {
            $perf_trend = DB::select("SELECT DATE_FORMAT(t.created_at,'%b %y') as month, COUNT(*) as total, SUM(CASE WHEN t.status IN('Resolved','Closed') THEN 1 ELSE 0 END) as resolved FROM it_tickets t WHERE t.assigned_to=$selected_admin AND t.created_at>=DATE_SUB(NOW(),INTERVAL 6 MONTH) GROUP BY DATE_FORMAT(t.created_at,'%Y-%m'), DATE_FORMAT(t.created_at,'%b %y') ORDER BY MIN(t.created_at)");
        }

        return view('admin.it-admin', compact(
            'admin_id', 'admin_name', 'admin_email', 'period', 'selected_admin', 'search', 'admins', 'overall',
            'total_tickets', 'total_resolved', 'resolution_rate', 'avg_res_hours', 'backlog', 'admin_performance',
            'tickets', 'categories', 'priorities', 'monthly_trend', 'weekly_pattern', 'response_dist', 'departments',
            'daily_volume', 'sla_data', 'workload_data', 'top_resolvers', 'perf_trend'
        ));
    }

    /** admin/backup.php: backup audit dashboard, Excel export, print view */
    public function backup(Request $request)
    {
        if (! session()->has('admin_id') && ! session()->has('admin_username')) {
            kc_redirect('../admin/login.php');
        }

        $admin_id = session('admin_id') ?? 1;
        $admin_name = session('admin_name') ?? (session('admin_username') ?? 'Admin');

        $companies = [
            'KEL Uganda' => ['icon' => 'fa-building', 'color' => '#009ada'],
            'KEL Tanzania' => ['icon' => 'fa-building', 'color' => '#76bc21'],
            'KFS' => ['icon' => 'fa-building', 'color' => '#f59e0b'],
            'KIBOCELL' => ['icon' => 'fa-building', 'color' => '#8b5cf6'],
        ];

        $period = $request->query('period') !== null ? $request->query('period') : 'month';
        $company_filter = $request->query('company') ?? '';
        $status_filter = $request->query('status_filter') ?? '';
        $admin_filter = $request->query('admin_filter') ?? '';

        $date_condition = '';
        switch ($period) {
            case 'today': $date_condition = 'AND backup_date = CURDATE()';
                break;
            case 'yesterday': $date_condition = 'AND backup_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
                break;
            case 'week': $date_condition = 'AND backup_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
                break;
            case 'month': $date_condition = 'AND backup_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
                break;
            case 'quarter': $date_condition = 'AND backup_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)';
                break;
            case 'year': $date_condition = 'AND backup_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)';
                break;
            case 'all': $date_condition = '';
                break;
        }
        // filter conditions (same unqualified columns as before), values as bindings
        $filter_sql = '';
        $filter_bindings = [];
        if ($company_filter) {
            $filter_sql .= ' AND companies LIKE ?';
            $filter_bindings[] = '%'.$company_filter.'%';
        }
        if ($status_filter) {
            $filter_sql .= ' AND status = ?';
            $filter_bindings[] = $status_filter;
        }
        if ($admin_filter) {
            $filter_sql .= ' AND employee_id = ?';
            $filter_bindings[] = $admin_filter;
        }

        // ─── HANDLE EXPORT
        if ($request->query('export') !== null) {
            $exp_result = DB::select("SELECT bl.backup_date, bl.status, bl.companies, bl.notes, e.full_name, t.ticket_number, bl.created_at 
                  FROM backup_logs bl JOIN employees e ON bl.employee_id=e.id 
                  LEFT JOIN it_tickets t ON bl.ticket_id=t.id 
                  WHERE 1=1 $date_condition $filter_sql 
                  ORDER BY bl.backup_date DESC", $filter_bindings);

            $out = '<html><head><meta charset="UTF-8"><style>
        body{font-family:Arial;font-size:11px} h2{color:#009ada}
        table{border-collapse:collapse;width:100%} th{background:#009ada;color:#fff;padding:8px} td{padding:6px;border:1px solid #ddd}
        .header-info{margin-bottom:15px;padding:10px;background:#f5f5f5}
    </style></head><body>';
            $out .= '<h2>Daily Backup Audit Report</h2>';
            $out .= '<div class="header-info"><p><strong>Generated:</strong> '.date('Y-m-d H:i:s').'</p><p><strong>Period:</strong> '.ucfirst($period).'</p><p><strong>Total Records:</strong> '.count($exp_result).'</p></div>';
            $out .= '<table><tr><th>#</th><th>Date</th><th>Status</th><th>Companies</th><th>Recorded By</th><th>Ticket</th><th>Notes</th><th>Time</th></tr>';
            $i = 1;
            foreach ($exp_result as $r) {
                $out .= '<tr><td>'.$i++.'</td><td>'.$r['backup_date'].'</td><td>'.$r['status'].'</td><td>'.$r['companies'].'</td><td>'.$r['full_name'].'</td><td>'.$r['ticket_number'].'</td><td>'.$r['notes'].'</td><td>'.$r['created_at'].'</td></tr>';
            }
            $out .= '</table></body></html>';

            return response($out, 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="Backup_Audit_Report_'.date('Y-m-d').'.xls"',
            ]);
        }

        // ─── HANDLE PRINT
        if ($request->query('print') !== null) {
            $print_result = DB::select("SELECT bl.*, e.full_name, t.ticket_number FROM backup_logs bl JOIN employees e ON bl.employee_id=e.id LEFT JOIN it_tickets t ON bl.ticket_id=t.id WHERE 1=1 $date_condition $filter_sql ORDER BY bl.backup_date DESC", $filter_bindings);
            $print_total = count($print_result);

            $ps = DB::selectOne("SELECT COUNT(*) as total, SUM(CASE WHEN status='Completed' THEN 1 ELSE 0 END) as completed, SUM(CASE WHEN status='Partial' THEN 1 ELSE 0 END) as partial, SUM(CASE WHEN status='Failed' THEN 1 ELSE 0 END) as failed, COUNT(DISTINCT backup_date) as days FROM backup_logs WHERE 1=1 $date_condition");

            $today = date('Y-m-d');
            $logged = [];
            foreach (DB::select("SELECT DISTINCT backup_date FROM backup_logs WHERE DATE_FORMAT(backup_date,'%Y-%m')=?", [date('Y-m')]) as $d) {
                $logged[] = $d['backup_date'];
            }
            $missing = [];
            for ($d = 1; $d <= date('t'); $d++) {
                $dt = sprintf('%04d-%02d-%02d', date('Y'), date('m'), $d);
                if ($dt <= $today && ! in_array($dt, $logged) && date('N', strtotime($dt)) < 6) {
                    $missing[] = $dt;
                }
            }

            return view('admin.backup-print', compact('period', 'print_total', 'ps', 'missing', 'print_result', 'admin_name'));
        }

        // ─── KPI Stats
        $kpi = DB::selectOne("SELECT COUNT(*) as total, SUM(CASE WHEN status='Completed' THEN 1 ELSE 0 END) as completed, SUM(CASE WHEN status='Partial' THEN 1 ELSE 0 END) as partial, SUM(CASE WHEN status='Failed' THEN 1 ELSE 0 END) as failed, COUNT(DISTINCT backup_date) as days_covered, COUNT(DISTINCT employee_id) as admins FROM backup_logs WHERE 1=1 $date_condition");

        $today = date('Y-m-d');
        $today_backups = DB::select('SELECT bl.*, e.full_name, t.ticket_number FROM backup_logs bl JOIN employees e ON bl.employee_id=e.id LEFT JOIN it_tickets t ON bl.ticket_id=t.id WHERE bl.backup_date=? ORDER BY bl.created_at DESC', [$today]);
        $today_count = count($today_backups);

        $company_stats = [];
        foreach ($companies as $name => $info) {
            $cs = DB::selectOne("SELECT COUNT(*) as cnt, SUM(CASE WHEN status='Completed' THEN 1 ELSE 0 END) as comp FROM backup_logs WHERE companies LIKE ? $date_condition", ['%'.$name.'%']);
            $company_stats[$name] = ['count' => $cs['cnt'] ?? 0, 'completed' => $cs['comp'] ?? 0];
        }

        $monthly = DB::select("SELECT DATE_FORMAT(backup_date,'%Y-%m') as m, DATE_FORMAT(backup_date,'%b %y') as l, COUNT(*) as total, SUM(CASE WHEN status='Completed' THEN 1 ELSE 0 END) as completed, SUM(CASE WHEN status='Failed' THEN 1 ELSE 0 END) as failed FROM backup_logs WHERE backup_date>=DATE_SUB(NOW(),INTERVAL 12 MONTH) GROUP BY DATE_FORMAT(backup_date,'%Y-%m'), DATE_FORMAT(backup_date,'%b %y') ORDER BY m ASC");

        $weekly = DB::select('SELECT DAYNAME(backup_date) as day, DAYOFWEEK(backup_date) as dn, COUNT(*) as cnt FROM backup_logs WHERE backup_date>=DATE_SUB(NOW(),INTERVAL 90 DAY) GROUP BY DAYNAME(backup_date), DAYOFWEEK(backup_date) ORDER BY dn');

        $admin_perf = DB::select("SELECT e.id, e.full_name, COUNT(*) as total, SUM(CASE WHEN bl.status='Completed' THEN 1 ELSE 0 END) as completed, SUM(CASE WHEN bl.status='Failed' THEN 1 ELSE 0 END) as failed, COUNT(DISTINCT bl.backup_date) as days FROM backup_logs bl JOIN employees e ON bl.employee_id=e.id WHERE 1=1 $date_condition GROUP BY e.id, e.full_name ORDER BY total DESC");

        $year = date('Y');
        $month = date('m');
        $dim = date('t');
        $logged_days = [];
        foreach (DB::select("SELECT DISTINCT backup_date FROM backup_logs WHERE DATE_FORMAT(backup_date,'%Y-%m')=?", [date('Y-m')]) as $d) {
            $logged_days[] = $d['backup_date'];
        }
        $missing_days = [];
        for ($d = 1; $d <= $dim; $d++) {
            $dt = sprintf('%04d-%02d-%02d', $year, $month, $d);
            if ($dt <= $today && ! in_array($dt, $logged_days) && date('N', strtotime($dt)) < 6) {
                $missing_days[] = $dt;
            }
        }

        $firstDow = date('N', strtotime(date('Y-m-01'))) - 1;
        $calendar = [];
        for ($d = 1; $d <= $dim; $d++) {
            $dt = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $calendar[] = ['day' => $d, 'date' => $dt, 'isWeekend' => date('N', strtotime($dt)) >= 6, 'isToday' => $dt == $today, 'hasBackup' => in_array($dt, $logged_days)];
        }

        $logs = DB::select("SELECT bl.*, e.full_name, t.ticket_number FROM backup_logs bl JOIN employees e ON bl.employee_id=e.id LEFT JOIN it_tickets t ON bl.ticket_id=t.id WHERE 1=1 $date_condition $filter_sql ORDER BY bl.backup_date DESC, bl.created_at DESC", $filter_bindings);

        $admin_list = DB::select('SELECT DISTINCT e.id, e.full_name FROM backup_logs bl JOIN employees e ON bl.employee_id=e.id ORDER BY e.full_name');

        return view('admin.backup', compact(
            'admin_id', 'admin_name', 'companies', 'period', 'company_filter', 'status_filter', 'admin_filter',
            'kpi', 'today', 'today_backups', 'today_count', 'company_stats', 'monthly', 'weekly', 'admin_perf',
            'missing_days', 'firstDow', 'calendar', 'logs', 'admin_list'
        ));
    }

    /** admin/backup-log.php: backup logger (record a backup, list by date range, Excel export, print view) */
    public function backupLog(Request $request)
    {
        if (! session()->has('admin_id') && ! session()->has('admin_username')) {
            kc_redirect('../admin/login.php');
        }

        $admin_id = session('admin_id') ?? 1;
        $admin_name = session('admin_name') ?? (session('admin_username') ?? 'Admin');

        $success_message = '';
        $error_message = '';

        if ($request->isMethod('post') && $request->has('submit_backup')) {
            $companies = $request->input('companies') ?? [];
            $backup_date = $request->input('backup_date') ?? date('Y-m-d');
            $notes = kc_escape($request->input('notes') ?? ''); // escaped AND bound, as before
            $status = $request->input('status') ?? 'Completed';
            $ticket_id = ! empty($request->input('ticket_id')) ? intval($request->input('ticket_id')) : null;

            $screenshot = '';
            $file = $request->file('screenshot');
            if ($file && ! is_array($file) && $file->isValid()) {
                $upload_dir = kc_public_file('../uploads/backup_screenshots/', 'admin');
                if (! file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $file_extension = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($file_extension, $allowed_extensions)) {
                    $new_filename = 'backup_'.date('Ymd_His').'_'.uniqid().'.'.$file_extension;
                    if (\App\Http\Controllers\ItController::moveFile($file, $upload_dir, $new_filename)) {
                        $screenshot = 'uploads/backup_screenshots/'.$new_filename;
                    }
                }
            }

            if (empty($companies)) {
                $error_message = 'Please select at least one company.';
            } else {
                $companies_str = implode(', ', $companies);

                // Find or create employee record for admin
                $employee_id = null;
                if ($emp_row = DB::selectOne('SELECT id FROM employees WHERE full_name = ? LIMIT 1', [$admin_name])) {
                    $employee_id = $emp_row['id'];
                } else {
                    $emp_code = 'IT-'.strtoupper(substr(md5($admin_name.time()), 0, 6));
                    DB::insert("INSERT INTO employees (employee_code, full_name, role, department, status) 
                          VALUES (?, ?, 'admin', 'IT', 1)", [$emp_code, $admin_name]);
                    $employee_id = DB::getPdo()->lastInsertId();
                }

                DB::insert('INSERT INTO backup_logs (backup_date, employee_id, companies, status, notes, screenshot, ticket_id) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)', [$backup_date, (int) $employee_id, $companies_str, $status, $notes, $screenshot, $ticket_id]);
                $success_message = 'Backup recorded successfully!';
            }
        }

        // Date range from URL or default to current month
        $raw_from = $request->query('date_from') !== null ? (string) $request->query('date_from') : date('Y-m-01');
        $raw_to = $request->query('date_to') !== null ? (string) $request->query('date_to') : date('Y-m-d');
        $raw_status = (string) ($request->query('status_filter') ?? '');
        $date_from = kc_escape($raw_from);
        $date_to = kc_escape($raw_to);
        $status_filter = $request->query('status_filter') !== null ? kc_escape($raw_status) : '';

        $cond = 'AND DATE(bl.backup_date) BETWEEN ? AND ?';
        $cb = [$raw_from, $raw_to];
        if ($status_filter) {
            $cond .= ' AND bl.status = ?';
            $cb[] = $raw_status;
        }

        // Handle export
        if ($request->query('export') !== null) {
            $export_result = DB::select("SELECT bl.*, e.full_name as employee_name, e.employee_code
                     FROM backup_logs bl 
                     LEFT JOIN employees e ON bl.employee_id = e.id 
                     WHERE 1=1 $cond
                     ORDER BY bl.backup_date DESC, bl.created_at DESC", $cb);

            $filename = 'Backup_Report_'.$date_from.'_to_'.$date_to.'.xls';

            $out = '<html><head><meta charset="UTF-8"><style>
        body { font-family: Arial; font-size: 11px; }
        h2 { color: #009ada; }
        table { border-collapse: collapse; width: 100%; }
        th { background: #009ada; color: white; padding: 8px; text-align: left; }
        td { padding: 6px 8px; border: 1px solid #ddd; }
        .header-info { margin-bottom: 15px; padding: 10px; background: #f5f5f5; }
        .footer-info { margin-top: 15px; padding: 10px; background: #f5f5f5; border-top: 1px solid #ddd; }
    </style></head><body>';

            $out .= '<h2>IT Backup Report</h2>';
            $out .= '<div class="header-info">';
            $out .= '<p><strong>Generated:</strong> '.date('Y-m-d H:i:s').'</p>';
            $out .= '<p><strong>Period:</strong> '.$date_from.' to '.$date_to.'</p>';
            if ($status_filter) {
                $out .= '<p><strong>Status Filter:</strong> '.htmlspecialchars($status_filter).'</p>';
            }
            $out .= '<p><strong>Total Records:</strong> '.count($export_result).'</p>';
            $out .= '</div>';

            $out .= '<table><thead><tr>
        <th>#</th><th>Date</th><th>Staff</th><th>Employee Code</th>
        <th>Companies</th><th>Status</th><th>Evidence</th><th>Ticket</th><th>Notes</th><th>Created</th>
    </tr></thead><tbody>';

            $counter = 1;
            foreach ($export_result as $row) {
                $out .= '<tr>';
                $out .= '<td>'.$counter++.'</td>';
                $out .= '<td>'.date('Y-m-d', strtotime($row['backup_date'])).'</td>';
                $out .= '<td>'.htmlspecialchars($row['employee_name'] ?? 'Unknown').'</td>';
                $out .= '<td>'.htmlspecialchars($row['employee_code'] ?? 'N/A').'</td>';
                $out .= '<td>'.htmlspecialchars($row['companies']).'</td>';
                $out .= '<td>'.$row['status'].'</td>';
                $out .= '<td>'.($row['screenshot'] ? 'Yes' : 'No').'</td>';
                $out .= '<td>'.($row['ticket_id'] ?? '-').'</td>';
                $out .= '<td>'.htmlspecialchars(substr($row['notes'] ?? '', 0, 100)).'</td>';
                $out .= '<td>'.date('Y-m-d H:i', strtotime($row['created_at'])).'</td>';
                $out .= '</tr>';
            }

            $out .= '</tbody></table>';

            $out .= '<div class="footer-info">';
            $out .= '<p style="font-size:10px;color:#666;">This is a computer-generated report. | K-Connect Intranet Portal</p>';
            $out .= '<table style="border:none;width:100%;margin-top:10px;">';
            $out .= '<tr>';
            $out .= '<td style="border:none;width:33%;"><hr style="width:80%;"><p style="font-size:10px;margin:0;">Checked By</p></td>';
            $out .= '<td style="border:none;width:33%;"><hr style="width:80%;"><p style="font-size:10px;margin:0;">Approved By</p></td>';
            $out .= '<td style="border:none;width:33%;"><hr style="width:80%;"><p style="font-size:10px;margin:0;">Date</p></td>';
            $out .= '</tr>';
            $out .= '</table>';
            $out .= '</div>';

            $out .= '</body></html>';

            return response($out, 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        }

        // Handle print
        if ($request->query('print') !== null) {
            $print_result = DB::select("SELECT bl.*, e.full_name as employee_name, e.employee_code
                    FROM backup_logs bl 
                    LEFT JOIN employees e ON bl.employee_id = e.id 
                    WHERE 1=1 $cond
                    ORDER BY bl.backup_date DESC, bl.created_at DESC", $cb);
            $print_total = count($print_result);

            $print_stats = DB::selectOne("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status='Completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN status='Partial' THEN 1 ELSE 0 END) as partial,
        SUM(CASE WHEN status='Failed' THEN 1 ELSE 0 END) as failed,
        COUNT(DISTINCT employee_id) as unique_staff
        FROM backup_logs bl WHERE 1=1 $cond", $cb);

            return view('admin.backup-log-print', compact('date_from', 'date_to', 'status_filter', 'print_result', 'print_total', 'print_stats', 'admin_name'));
        }

        $companies_result = DB::select('SELECT * FROM companies WHERE status = 1 ORDER BY name ASC');
        $tickets_result = DB::select("SELECT id, ticket_number, subject FROM it_tickets WHERE status IN ('New', 'In Progress') ORDER BY created_at DESC LIMIT 50");

        $backup_logs = DB::select("SELECT bl.*, e.full_name as employee_name, e.employee_code
                      FROM backup_logs bl 
                      LEFT JOIN employees e ON bl.employee_id = e.id 
                      WHERE 1=1 $cond
                      ORDER BY bl.backup_date DESC, bl.created_at DESC", $cb);

        $stats = DB::selectOne("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status='Completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status='Partial' THEN 1 ELSE 0 END) as partial,
    SUM(CASE WHEN status='Failed' THEN 1 ELSE 0 END) as failed,
    COUNT(DISTINCT employee_id) as unique_staff,
    COUNT(DISTINCT backup_date) as backup_days,
    SUM(CASE WHEN screenshot IS NOT NULL AND screenshot != '' THEN 1 ELSE 0 END) as with_evidence
    FROM backup_logs bl WHERE 1=1 $cond", $cb);

        // Calculate missed days
        $total_days = (strtotime($date_to) - strtotime($date_from)) / (60 * 60 * 24) + 1;
        $backup_days_count = $stats['backup_days'] ?? 0;
        $missed_days_count = max(0, $total_days - $backup_days_count);
        $compliance_rate = $total_days > 0 ? round(($backup_days_count / $total_days) * 100, 1) : 0;

        return view('admin.backup-log', compact(
            'admin_id', 'admin_name', 'success_message', 'error_message', 'date_from', 'date_to', 'status_filter',
            'companies_result', 'tickets_result', 'backup_logs', 'stats', 'total_days', 'backup_days_count',
            'missed_days_count', 'compliance_rate'
        ));
    }
}

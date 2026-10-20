<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ItHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** IT support tickets and backup logs, admin side (admin/it-*.php, admin/backup*.php). */
class ItController extends Controller
{
    /** Admin session check shared by the admin IT pages. */
    private static function isAdminSession(): bool
    {
        return session()->has('admin_id') || session()->has('admin_username');
    }

    /**
     * The employee record an admin acts as when writing tickets/comments (it_tickets.employee_id and
     * it_ticket_comments.employee_id are NOT NULL employee ids): the employee session if there is one,
     * otherwise the employee whose e-mail matches the admin account's e-mail.
     *
     * @return array{0: int|null, 1: string} [employee id or null, display name]
     */
    private static function actingEmployee(): array
    {
        if (session()->has('employee_id')) {
            return [(int) session('employee_id'), (string) (session('employee_name') ?? '')];
        }
        $admin = session()->has('admin_id')
            ? DB::selectOne('SELECT email FROM admin_users WHERE id = ?', [session('admin_id')])
            : null;
        if ($admin && ! empty($admin['email'])) {
            if ($emp = DB::selectOne('SELECT id, full_name FROM employees WHERE email = ? AND status = 1 LIMIT 1', [$admin['email']])) {
                return [(int) $emp['id'], (string) $emp['full_name']];
            }
        }

        return [null, (string) (session('admin_username') ?? 'Admin')];
    }

    /**
     * Status / priority / search filters of the admin ticket list and its Excel export.
     *
     * @return array{0: string, 1: array} [WHERE sql (with "WHERE" or empty), bindings]
     */
    private static function supportFilters(string $status, string $priority, string $search): array
    {
        $where = [];
        $b = [];
        if ($status !== '') {
            $where[] = 't.status = ?';
            $b[] = $status;
        }
        if ($priority !== '') {
            $where[] = 't.priority = ?';
            $b[] = $priority;
        }
        if ($search !== '') {
            $like = '%'.$search.'%';
            $where[] = '(t.ticket_number LIKE ? OR t.subject LIKE ? OR e.full_name LIKE ? OR e.employee_code LIKE ?)';
            array_push($b, $like, $like, $like, $like);
        }

        return [! empty($where) ? 'WHERE '.implode(' AND ', $where) : '', $b];
    }

    /**
     * admin/it-support.php: the admin view of all IT tickets (filters, Excel export, new ticket,
     * reply, status update). The old file started with "<?php\n<?php" and never ran (parse error);
     * this is the page it was meant to be.
     */
    public function support(Request $request)
    {
        if (! self::isAdminSession()) {
            kc_redirect('login');
        }

        [$employee_id, $employee_name] = self::actingEmployee();
        $is_admin = true;

        $status_filter = ItHelpers::str($request->query('status'));
        $priority_filter = ItHelpers::str($request->query('priority'));
        $search = trim(ItHelpers::str($request->query('search')));

        // ── Excel export
        if ($request->query('export_excel') == 1) {
            [$where_sql, $b] = self::supportFilters($status_filter, $priority_filter, $search);
            $rows = DB::select("SELECT t.*, e.full_name as employee_name, e.employee_code, e.department,
                     (SELECT COUNT(*) FROM it_ticket_comments WHERE ticket_id = t.id) as comment_count
                     FROM it_tickets t
                     JOIN employees e ON t.employee_id = e.id
                     $where_sql
                     ORDER BY t.created_at DESC", $b);

            return response()->view('admin.it-support-export', compact('rows'), 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="it_tickets_export_'.date('Y-m-d_His').'.xls"',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        }

        // ── New ticket
        if ($request->isMethod('post') && $request->has('submit_ticket')) {
            if (! $employee_id) {
                session(['message' => 'Your admin account is not linked to an employee record (matching e-mail), so tickets cannot be raised from it.', 'message_type' => 'danger']);
                kc_redirect('it-support');
            }
            $category = (string) $request->input('category');
            $priority = (string) $request->input('priority');
            $subject = trim((string) $request->input('subject'));
            $description = trim((string) $request->input('description'));

            if (! in_array($category, ItHelpers::ticketCategories(), true)
                || ! in_array($priority, ['Low', 'Medium', 'High', 'Urgent'], true)
                || $subject === '' || $description === '') {
                session(['message' => 'Please fill in category, priority, subject and description.', 'message_type' => 'danger']);
                kc_redirect('it-support');
            }

            $ticket_number = 'IT-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -6));

            DB::beginTransaction();
            try {
                DB::insert("INSERT INTO it_tickets (ticket_number, employee_id, category, priority, subject, description, status, created_by_admin)
                            VALUES (?, ?, ?, ?, ?, ?, 'New', 1)", [$ticket_number, $employee_id, $category, $priority, $subject, $description]);
                $ticket_id = DB::getPdo()->lastInsertId();

                // dynamic questions
                foreach (DB::select('SELECT * FROM it_questions WHERE status = 1 ORDER BY sort_order') as $q) {
                    $key = 'q_'.$q['id'];
                    $answer = $request->input($key);
                    if ($q['question_type'] == 'file') {
                        $file = $request->file($key);
                        $answer = null;
                        if ($file && ! is_array($file) && $file->isValid()) {
                            $file_name = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                            if (\App\Http\Controllers\ItController::moveFile($file, kc_upload_path('it_tickets/'), $file_name)) {
                                $answer = 'uploads/it_tickets/'.$file_name;
                            }
                        }
                    } elseif (is_array($answer)) {
                        $answer = implode(', ', $answer);
                    }
                    if ($answer !== null && $answer !== '') {
                        DB::insert('INSERT INTO it_answers (ticket_id, question_id, answer) VALUES (?, ?, ?)', [$ticket_id, $q['id'], (string) $answer]);
                    }
                }
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                report($e);
                session(['message' => 'Error submitting ticket. Please try again.', 'message_type' => 'danger']);
                kc_redirect('it-support');
            }

            $empEmail = DB::selectOne('SELECT email FROM employees WHERE id = ?', [$employee_id])['email'] ?? '';
            notifyNewTicket(
                ['ticket_number' => $ticket_number, 'subject' => $subject, 'category' => $category, 'priority' => $priority],
                $empEmail, $employee_name, ItHelpers::getAdminEmailsForNotify()
            );

            session(['message' => "Ticket #$ticket_number submitted! Confirmation email sent.", 'message_type' => 'success']);
            kc_redirect('it-support');
        }

        // ── Reply (also posted by the ticket detail fragment)
        if ($request->isMethod('post') && $request->has('add_comment')) {
            $ticket_id = (int) $request->input('ticket_id');
            $comment = trim((string) $request->input('comment'));
            $tRow = DB::selectOne('SELECT t.*, e.full_name, e.email as emp_email FROM it_tickets t JOIN employees e ON t.employee_id = e.id WHERE t.id = ?', [$ticket_id]);

            if (! $employee_id) {
                session(['message' => 'Your admin account is not linked to an employee record (matching e-mail), so replies cannot be posted from it.', 'message_type' => 'danger']);
            } elseif (! $tRow || $comment === '') {
                session(['message' => 'Ticket not found or empty reply.', 'message_type' => 'danger']);
            } else {
                DB::insert('INSERT INTO it_ticket_comments (ticket_id, employee_id, comment, is_admin) VALUES (?, ?, ?, 1)', [$ticket_id, $employee_id, $comment]);
                DB::update('UPDATE it_tickets SET updated_at = NOW() WHERE id = ?', [$ticket_id]);
                notifyNewComment($tRow, $comment, $employee_name, true, $tRow['emp_email'], $tRow['full_name'], ItHelpers::getAdminEmailsForNotify());
                session(['message' => 'Reply sent!', 'message_type' => 'success']);
            }
            kc_redirect('it-support');
        }

        // ── Status update
        if ($request->isMethod('post') && $request->has('update_ticket_status')) {
            $ticket_id = (int) $request->input('ticket_id');
            $status = (string) $request->input('status');
            $admin_notes = trim((string) ($request->input('admin_notes') ?? ''));
            $current = DB::selectOne('SELECT status FROM it_tickets WHERE id = ?', [$ticket_id]);

            if (! $current || ! in_array($status, ['New', 'In Progress', 'Resolved', 'Closed'], true)) {
                session(['message' => 'Ticket not found or invalid status.', 'message_type' => 'danger']);
                kc_redirect('it-support');
            }

            $sql = 'UPDATE it_tickets SET status = ?, updated_at = NOW()';
            if ($status == 'Resolved') {
                $sql .= ', resolved_at = NOW()';
            } elseif ($status == 'Closed') {
                $sql .= ', closed_at = NOW()';
            }
            DB::update($sql.' WHERE id = ?', [$status, $ticket_id]);

            $changed_by = $employee_id ?? (int) (session('admin_id') ?? 0);
            DB::insert('INSERT INTO it_ticket_status_history (ticket_id, from_status, to_status, changed_by, notes) VALUES (?, ?, ?, ?, ?)',
                [$ticket_id, $current['status'], $status, $changed_by, $admin_notes]);

            if ($admin_notes !== '' && $employee_id) {
                DB::insert('INSERT INTO it_ticket_comments (ticket_id, employee_id, comment, is_admin) VALUES (?, ?, ?, 1)', [$ticket_id, $employee_id, $admin_notes]);
            }

            if ($tRow = DB::selectOne('SELECT t.*, e.email as emp_email, e.full_name FROM it_tickets t JOIN employees e ON t.employee_id = e.id WHERE t.id = ?', [$ticket_id])) {
                notifyStatusUpdate($tRow, $tRow['emp_email'] ?? '', $tRow['full_name'] ?? '', $admin_notes);
            }

            session(['message' => 'Ticket updated and employee notified.', 'message_type' => 'success']);
            kc_redirect('it-support');
        }

        // ── List + stats
        [$where_sql, $b] = self::supportFilters($status_filter, $priority_filter, $search);
        $tickets = DB::select("SELECT t.*, e.full_name as employee_name, e.employee_code, e.photo, e.department,
                      (SELECT COUNT(*) FROM it_ticket_comments WHERE ticket_id = t.id) as comment_count
                      FROM it_tickets t
                      JOIN employees e ON t.employee_id = e.id
                      $where_sql
                      ORDER BY FIELD(t.status, 'New', 'In Progress', 'Resolved', 'Closed'), t.created_at DESC", $b);

        $stats = DB::selectOne("SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'New' THEN 1 ELSE 0 END) as new_count,
                    SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) as closed,
                    SUM(CASE WHEN priority = 'Urgent' THEN 1 ELSE 0 END) as urgent
                    FROM it_tickets") ?: [];

        $questions = DB::select('SELECT * FROM it_questions WHERE status = 1 ORDER BY sort_order');
        $categories = ItHelpers::ticketCategories();
        $statuses = ['New', 'In Progress', 'Resolved', 'Closed'];
        $priorities = ['Low', 'Medium', 'High', 'Urgent'];
        $message = session()->pull('message');
        $message_type = session()->pull('message_type') ?? 'info';
        $canWrite = $employee_id !== null;

        return view('admin.it-support', compact(
            'tickets', 'stats', 'questions', 'categories', 'statuses', 'priorities',
            'status_filter', 'priority_filter', 'search', 'message', 'message_type', 'canWrite', 'is_admin'
        ));
    }

    /** admin/it-ticket-data.php: one ticket as JSON for the edit modal of admin/it-support */
    public function ticketData(Request $request)
    {
        if (! self::isAdminSession()) {
            return kc_json(['error' => 'Unauthorized'], 401);
        }

        $ticket_id = $request->query('id') !== null ? (int) $request->query('id') : 0;
        $ticket = DB::selectOne('SELECT t.*, e.full_name as employee_name, e.employee_code
          FROM it_tickets t
          JOIN employees e ON t.employee_id = e.id
          WHERE t.id = ?', [$ticket_id]);

        if (! $ticket) {
            return kc_json(['error' => 'Ticket not found'], 404);
        }

        return kc_json($ticket);
    }

    /** admin/it-ticket-detail.php: ticket detail fragment */
    public function ticketDetail(Request $request)
    {
        $isAdmin = self::isAdminSession();
        if (! $isAdmin && ! isLoggedIn()) {
            return response('Unauthorized', 401);
        }

        $ticket_id = $request->query('id') !== null ? (int) $request->query('id') : 0;
        $employee_id = session('employee_id');
        $is_admin = $isAdmin || \App\Http\Controllers\ItController::isItAdmin($employee_id);

        $ticket = DB::selectOne('SELECT t.*, e.full_name as employee_name, e.employee_code, e.department
          FROM it_tickets t
          JOIN employees e ON t.employee_id = e.id
          WHERE t.id = ?', [$ticket_id]);

        // employees without an admin role only see their own tickets
        if ($ticket && ! $is_admin && (int) $ticket['employee_id'] !== (int) $employee_id) {
            $ticket = null;
        }

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

    /** getDateRangeData() of admin/it-admin */
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

    /**
     * Search condition of admin/it-admin (ticket number, subject, employee name/code).
     * The old fragment referenced e.full_name in queries that have no "e" join (the KPI and backlog
     * queries), so every search failed with "Unknown column"; the employee part is a subquery now,
     * which works with or without the join, and the text is bound instead of pasted into the SQL.
     *
     * @return array{0: string, 1: array}
     */
    private static function itAdminSearch(string $search): array
    {
        if ($search === '') {
            return ['', []];
        }
        $like = '%'.$search.'%';

        return [
            'AND (t.ticket_number LIKE ? OR t.subject LIKE ? OR t.employee_id IN (SELECT se.id FROM employees se WHERE se.full_name LIKE ? OR se.employee_code LIKE ?))',
            [$like, $like, $like, $like],
        ];
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
     * The search is bound (see itAdminSearch()) and applied to the KPIs, the backlog, the ticket
     * table, the Excel export and the print view.
     */
    public function admin(Request $request)
    {
        if (! session()->has('admin_id') && ! session()->has('admin_username')) {
            kc_redirect('../admin/login');
        }

        $admin_id = session('admin_id') ?? 1;
        $admin_name = session('admin_name') ?? (session('admin_username') ?? 'Admin');
        $admin_email = session('admin_email') ?? '';

        // ─── HANDLE TICKET CLOSE
        if ($request->isMethod('post') && $request->has('close_ticket')) {
            $ticket_id = (int) $request->input('ticket_id');
            $ticket_number = (string) ($request->input('ticket_number') ?? '');
            $close_notes = trim((string) ($request->input('close_notes') ?? ''));
            [$acting_employee] = self::actingEmployee();

            $current = DB::selectOne('SELECT status FROM it_tickets WHERE id = ?', [$ticket_id]);
            $from_status = $current['status'] ?? 'Unknown';

            DB::update("UPDATE it_tickets SET status = 'Closed', closed_at = NOW(), updated_at = NOW() WHERE id = ?", [$ticket_id]);

            DB::insert("INSERT INTO it_ticket_status_history (ticket_id, from_status, to_status, changed_by, notes) 
                         VALUES (?, ?, 'Closed', ?, ?)", [$ticket_id, $from_status, $acting_employee ?? $admin_id, $close_notes]);

            // comments belong to an employee (shown with the employee's name): the admin's linked employee record
            if ($close_notes !== '' && $acting_employee) {
                DB::insert('INSERT INTO it_ticket_comments (ticket_id, employee_id, comment, is_admin) 
                             VALUES (?, ?, ?, 1)', [$ticket_id, $acting_employee, 'Ticket closed by admin: '.$close_notes]);
            }

            session(['message' => "Ticket #$ticket_number closed successfully!", 'message_type' => 'success']);
            kc_redirect('it-admin');
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
            kc_redirect('it-admin');
        }

        // ─── HANDLE EXPORT (Excel)
        if ($request->query('export') !== null) {
            $period = $request->query('period') !== null ? $request->query('period') : 'month';
            $selected_admin = $request->query('admin') !== null ? (int) $request->query('admin') : 0;
            $date_condition = self::getDateRangeData($period);
            $admin_filter = $selected_admin > 0 ? "AND t.assigned_to = $selected_admin" : '';
            $search = trim(ItHelpers::str($request->query('search')));
            [$search_filter, $sb] = self::itAdminSearch($search);

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
                     ORDER BY t.created_at DESC", $sb);

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
            $search = trim(ItHelpers::str($request->query('search')));
            $date_condition = self::getDateRangeData($period);
            $admin_filter = $selected_admin > 0 ? "AND t.assigned_to = $selected_admin" : '';
            [$search_filter, $sb] = self::itAdminSearch($search);

            $print_result = DB::select("SELECT t.*, e.full_name as employee_name, e.employee_code, e.department,
                     a.full_name as assigned_to_name
                     FROM it_tickets t 
                     JOIN employees e ON t.employee_id = e.id 
                     LEFT JOIN employees a ON t.assigned_to = a.id 
                     WHERE 1=1 $date_condition $admin_filter $search_filter
                     ORDER BY t.created_at DESC", $sb);
            $print_total = count($print_result);

            $print_stats = DB::selectOne("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN t.status='New' THEN 1 ELSE 0 END) as new_count,
        SUM(CASE WHEN t.status='In Progress' THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN t.status='Resolved' THEN 1 ELSE 0 END) as resolved,
        SUM(CASE WHEN t.status='Closed' THEN 1 ELSE 0 END) as closed
        FROM it_tickets t WHERE 1=1 $date_condition $admin_filter $search_filter", $sb);

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
        $search = trim(ItHelpers::str($request->query('search')));
        $date_condition = self::getDateRangeData($period);
        $admin_filter = $selected_admin > 0 ? "AND t.assigned_to = $selected_admin" : '';
        [$search_filter, $sb] = self::itAdminSearch($search);

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
    FROM it_tickets t WHERE 1=1 $date_condition $admin_filter $search_filter", $sb) ?: [];

        $total_tickets = $overall['total_tickets'] ?? 0;
        $total_resolved = $overall['total_resolved'] ?? 0;
        $resolution_rate = $total_tickets > 0 ? round(($total_resolved / $total_tickets) * 100, 1) : 0;
        $avg_res_hours = round(($overall['avg_resolution_minutes'] ?? 0) / 60, 1);

        $backlog = DB::selectOne("SELECT COUNT(*) as backlog FROM it_tickets t 
    WHERE t.status IN ('New', 'In Progress') 
    AND t.created_at < DATE_SUB(NOW(), INTERVAL 48 HOUR) $admin_filter $search_filter", $sb) ?: [];

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
                  ORDER BY t.created_at DESC LIMIT 100", $sb);

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
            kc_redirect('../admin/login');
        }

        $admin_id = session('admin_id') ?? 1;
        $admin_name = session('admin_name') ?? (session('admin_username') ?? 'Admin');

        // companies come from the companies table (the backup logger lists the same ones)
        $palette = ['#009ada', '#76bc21', '#f59e0b', '#8b5cf6', '#ef4444', '#14b8a6', '#ec4899', '#64748b'];
        $companies = [];
        foreach (DB::select('SELECT name FROM companies WHERE status = 1 ORDER BY sort_order, name') as $i => $c) {
            $companies[$c['name']] = ['icon' => 'fa-building', 'color' => $palette[$i % count($palette)]];
        }

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
            kc_redirect('../admin/login');
        }

        $admin_id = session('admin_id') ?? 1;
        $admin_name = session('admin_name') ?? (session('admin_username') ?? 'Admin');

        $success_message = '';
        $error_message = '';

        if ($request->isMethod('post') && $request->has('submit_backup')) {
            $companies = $request->input('companies') ?? [];
            $backup_date = $request->input('backup_date') ?? date('Y-m-d');
            $notes = (string) ($request->input('notes') ?? '');
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

                // The admin's employee record (session / matching e-mail), else find or create one by name
                [$employee_id] = self::actingEmployee();
                if ($employee_id) {
                    // linked employee found
                } elseif ($emp_row = DB::selectOne('SELECT id FROM employees WHERE full_name = ? LIMIT 1', [$admin_name])) {
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
        $raw_from = ItHelpers::validDate($request->query('date_from'), date('Y-m-01'));
        $raw_to = ItHelpers::validDate($request->query('date_to'), date('Y-m-d'));
        $raw_status = ItHelpers::oneOf($request->query('status_filter'), ['Completed', 'Partial', 'Failed']);
        $date_from = $raw_from;
        $date_to = $raw_to;
        $status_filter = $raw_status;

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

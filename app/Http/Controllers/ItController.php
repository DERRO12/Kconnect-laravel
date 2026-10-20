<?php

namespace App\Http\Controllers;

use App\Support\ItHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** IT support tickets and backup logs, employee side (it-support, it-admin, it-ticket-*.php, backup*.php). */
class ItController extends Controller
{
    /** it-support */
    public function support(Request $request)
    {
        if (! isLoggedIn()) {
            kc_redirect('login');
        }

        $employee_id = session('employee_id');
        $employee_name = session('employee_name');
        $employee_code = session('employee_code');
        $employee_email = session('employee_email') ?? '';

        $is_admin = false;
        if (session()->has('admin_id') || session()->has('admin_username')) {
            $is_admin = true;
        } else {
            $role_check = DB::select("SELECT role FROM employees WHERE id=? AND role IN ('admin','manager','hr','director')", [$employee_id]);
            if (count($role_check) > 0) {
                $is_admin = true;
            }
        }

        if (! session()->has('form_token')) {
            session(['form_token' => bin2hex(random_bytes(32))]);
        }
        $form_token = session('form_token');

        // ── AJAX: Search Employees
        if ($request->query('ajax') !== null && $request->query('ajax') == 'search_employees') {
            $search = $request->query('q') !== null ? $request->query('q') : '';

            return kc_json(ItHelpers::getAllEmployees($search));
        }

        // ── HANDLE: Submit Ticket
        if ($request->isMethod('post') && $request->has('submit_ticket')) {
            if (! $request->has('form_token') || $request->input('form_token') !== session('form_token')) {
                session(['message' => 'Duplicate submission prevented.', 'message_type' => 'warning']);
                kc_redirect('it-support');
            }
            session(['form_token' => bin2hex(random_bytes(32))]);

            $category = $request->input('category');
            $priority = $request->input('priority');
            $subject = $request->input('subject');
            $description = $request->input('description');
            $assigned_to = $request->has('assigned_to') ? (int) $request->input('assigned_to') : 0;

            // Determine ticket owner
            $ticket_owner_id = $employee_id;
            if ($is_admin && $request->has('ticket_owner') && $request->input('ticket_owner') > 0) {
                $ticket_owner_id = (int) $request->input('ticket_owner');
            }

            if (! $assigned_to) {
                session(['message' => 'Please assign this ticket to an admin.', 'message_type' => 'danger']);
                kc_redirect('it-support');
            }

            $owner_data = DB::selectOne('SELECT email, full_name FROM employees WHERE id=?', [$ticket_owner_id]);
            $owner_email = $owner_data['email'] ?? '';
            $owner_name = $owner_data['full_name'] ?? '';

            $ticket_number = 'IT-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -6));

            DB::beginTransaction();
            try {
                DB::insert("INSERT INTO it_tickets (ticket_number, employee_id, category, priority, subject, description, assigned_to, status, created_by_admin)
                  VALUES (?, ?, ?, ?, ?, ?, ?, 'New', ".($is_admin ? 1 : 0).')',
                    [$ticket_number, $ticket_owner_id, $category, $priority, $subject, $description, $assigned_to]);
                $ticket_id = DB::getPdo()->lastInsertId();

                DB::insert('INSERT INTO it_ticket_assignments (ticket_id, assigned_to, assigned_by) VALUES (?, ?, ?)', [$ticket_id, $assigned_to, $employee_id]);

                if ($is_admin && $ticket_owner_id != $employee_id) {
                    $created_by_note = "Ticket created by admin ($employee_name) on behalf of $owner_name";
                    DB::insert('INSERT INTO it_ticket_comments (ticket_id, employee_id, comment, is_admin)
                                VALUES (?, ?, ?, 1)', [$ticket_id, $employee_id, $created_by_note]);
                }

                $upload_dir = 'uploads/it_tickets/';
                $disk_dir = kc_upload_path('it_tickets/');
                if (! file_exists($disk_dir)) {
                    mkdir($disk_dir, 0777, true);
                }

                // Pasted images
                if (! empty($request->input('pasted_images'))) {
                    $pasted = json_decode($request->input('pasted_images'), true);
                    if (is_array($pasted)) {
                        foreach ($pasted as $img) {
                            if (preg_match('/^data:image\/(\w+);base64,/', $img, $m)) {
                                $bin = base64_decode(substr($img, strpos($img, ',') + 1));
                                if ($bin) {
                                    $fn = time().'_'.uniqid().'.'.$m[1];
                                    file_put_contents($disk_dir.$fn, $bin);
                                    $fs = strlen($bin);
                                    DB::insert('INSERT INTO it_ticket_attachments (ticket_id, file_name, file_path, file_size, file_type, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)',
                                        [$ticket_id, $fn, $upload_dir.$fn, $fs, 'image/'.$m[1], $ticket_owner_id]);
                                }
                            }
                        }
                    }
                }

                // File uploads
                foreach (self::uploadedFiles($request, 'attachments') as $file) {
                    if ($file->isValid()) {
                        $fn = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                        $fs = $file->getSize();
                        $ft = $file->getClientMimeType();
                        if (self::moveFile($file, $disk_dir, $fn)) {
                            DB::insert('INSERT INTO it_ticket_attachments (ticket_id, file_name, file_path, file_size, file_type, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)',
                                [$ticket_id, $fn, $upload_dir.$fn, $fs, $ft, $ticket_owner_id]);
                        }
                    }
                }

                DB::commit();

                // e-mails got the escaped values in the old code
                $mailTicket = ['ticket_number' => $ticket_number, 'subject' => $subject, 'category' => $category, 'priority' => $priority, 'assigned_to' => $assigned_to];

                if (! empty($owner_email)) {
                    notifyNewTicket($mailTicket, $owner_email, $owner_name, []);
                }

                $adminEmail = '';
                if ($row = DB::selectOne('SELECT email FROM employees WHERE id=?', [$assigned_to])) {
                    $adminEmail = $row['email'];
                }

                $allAdmins = ItHelpers::getAdminEmailsForNotify();
                $allAdmins = array_diff($allAdmins, [$adminEmail]);

                if (! empty($adminEmail)) {
                    notifyNewTicket($mailTicket, $adminEmail, 'Admin', []);
                }
                if (! empty($allAdmins)) {
                    notifyNewTicket($mailTicket, '', '', $allAdmins);
                }

                $created_by_msg = ($is_admin && $ticket_owner_id != $employee_id) ? " on behalf of $owner_name" : '';
                session(['message' => "Ticket #$ticket_number submitted successfully$created_by_msg!", 'message_type' => 'success']);
            } catch (\Exception $e) {
                DB::rollBack();
                session(['message' => 'Error: '.$e->getMessage(), 'message_type' => 'danger']);
            }
            kc_redirect('it-support');
        }

        // ── HANDLE: Reply
        if ($request->isMethod('post') && ($request->has('add_comment') || $request->has('add_admin_comment'))) {
            if (! $request->has('form_token') || $request->input('form_token') !== session('form_token')) {
                session(['message' => 'Duplicate reply prevented.', 'message_type' => 'warning']);
                kc_redirect('it-support');
            }
            session(['form_token' => bin2hex(random_bytes(32))]);

            $ticket_id = (int) $request->input('ticket_id');
            $comment = $request->input('comment');
            $ia = $is_admin ? 1 : 0;

            $verify = 'SELECT t.*, e.full_name, e.email as emp_email FROM it_tickets t JOIN employees e ON t.employee_id=e.id WHERE t.id=?';
            $vb = [$ticket_id];
            if (! $is_admin) {
                $verify .= ' AND t.employee_id=?';
                $vb[] = $employee_id;
            }
            $vr = DB::select($verify, $vb);
            if (count($vr) == 0) {
                session(['message' => 'Access denied.', 'message_type' => 'danger']);
                kc_redirect('it-support');
            }
            $td = $vr[0];

            DB::beginTransaction();
            try {
                DB::insert('INSERT INTO it_ticket_comments (ticket_id, employee_id, comment, is_admin, has_images) VALUES (?, ?, ?, ?, 0)', [$ticket_id, $employee_id, $comment, $ia]);
                $cid = DB::getPdo()->lastInsertId();

                $upload_dir = 'uploads/it_tickets/';
                $disk_dir = kc_upload_path('it_tickets/');
                if (! file_exists($disk_dir)) {
                    mkdir($disk_dir, 0777, true);
                }
                $ic = 0;

                if (! empty($request->input('pasted_images'))) {
                    $pasted = json_decode($request->input('pasted_images'), true) ?: [];
                    foreach ($pasted as $img) {
                        if (preg_match('/^data:image\/(\w+);base64,/', $img, $m)) {
                            $bin = base64_decode(substr($img, strpos($img, ',') + 1));
                            if ($bin) {
                                $fn = time().'_'.uniqid().'.'.$m[1];
                                file_put_contents($disk_dir.$fn, $bin);
                                DB::insert('INSERT INTO it_ticket_attachments (ticket_id, comment_id, file_name, file_path, file_size, file_type, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?)',
                                    [$ticket_id, $cid, $fn, $upload_dir.$fn, strlen($bin), 'image/'.$m[1], $employee_id]);
                                $ic++;
                            }
                        }
                    }
                }

                foreach (self::uploadedFiles($request, 'comment_attachments') as $file) {
                    if ($file->isValid()) {
                        $fn = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                        $fs = $file->getSize();
                        $ft = $file->getClientMimeType();
                        if (self::moveFile($file, $disk_dir, $fn)) {
                            DB::insert('INSERT INTO it_ticket_attachments (ticket_id, comment_id, file_name, file_path, file_size, file_type, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?)',
                                [$ticket_id, $cid, $fn, $upload_dir.$fn, $fs, $ft, $employee_id]);
                        }
                    }
                }

                if ($ic > 0) {
                    DB::update('UPDATE it_ticket_comments SET has_images=1 WHERE id=?', [$cid]);
                }
                DB::update('UPDATE it_tickets SET updated_at=NOW() WHERE id=?', [$ticket_id]);
                DB::commit();

                $aEmail = '';
                if ($td['assigned_to']) {
                    if ($row = DB::selectOne('SELECT email FROM employees WHERE id='.(int) $td['assigned_to'])) {
                        $aEmail = $row['email'];
                    }
                }
                $allAdmins = array_unique(array_merge([$aEmail], ItHelpers::getAdminEmailsForNotify()));
                notifyNewComment($td, $comment, $employee_name, $is_admin, $td['emp_email'], $td['full_name'], $allAdmins);
                session(['message' => 'Reply sent!', 'message_type' => 'success']);
            } catch (\Exception $e) {
                DB::rollBack();
                session(['message' => 'Error: '.$e->getMessage(), 'message_type' => 'danger']);
            }
            kc_redirect('it-support');
        }

        // ── HANDLE: Status Update
        if ($request->isMethod('post') && $request->has('update_ticket_status')) {
            if (! $request->has('form_token') || $request->input('form_token') !== session('form_token')) {
                session(['message' => 'Duplicate prevented.', 'message_type' => 'warning']);
                kc_redirect('it-support');
            }
            session(['form_token' => bin2hex(random_bytes(32))]);

            $ticket_id = (int) $request->input('ticket_id');
            $status = $request->input('status');
            $notes = $request->input('admin_notes') ?? '';
            $assigned_to = $request->has('assigned_to') && $request->input('assigned_to') > 0 ? (int) $request->input('assigned_to') : 0;

            $ticket_data = DB::selectOne('SELECT t.*, e.role as employee_role FROM it_tickets t JOIN employees e ON t.employee_id=e.id WHERE t.id=?', [$ticket_id]);

            if (! $ticket_data) {
                session(['message' => 'Ticket not found.', 'message_type' => 'danger']);
                kc_redirect('it-support');
            }

            $can_update = false;
            if ($is_admin) {
                $admin_allowed_statuses = ['New', 'In Progress', 'Resolved'];
                if (in_array($status, $admin_allowed_statuses, true)) {
                    $can_update = true;
                } else {
                    session(['message' => 'Admins cannot close tickets. Only the ticket creator can close tickets.', 'message_type' => 'danger']);
                    kc_redirect('it-support');
                }
            } else {
                if ($ticket_data['employee_id'] == $employee_id) {
                    if ($status == 'Closed') {
                        $can_update = true;
                    } else {
                        session(['message' => 'You can only close tickets you raised.', 'message_type' => 'danger']);
                        kc_redirect('it-support');
                    }
                } else {
                    session(['message' => "You don't have permission to update this ticket.", 'message_type' => 'danger']);
                    kc_redirect('it-support');
                }
            }

            if (! $can_update) {
                session(['message' => 'Permission denied.', 'message_type' => 'danger']);
                kc_redirect('it-support');
            }

            $cur = DB::selectOne('SELECT status FROM it_tickets WHERE id=?', [$ticket_id]);
            $from_status = $cur['status'] ?? 'New';

            $upd = 'UPDATE it_tickets SET status=?, updated_at=NOW()';
            $ub = [$status];
            if ($assigned_to) {
                $upd .= ', assigned_to=?';
                $ub[] = $assigned_to;
            }
            if ($status == 'Resolved') {
                $upd .= ', resolved_at=NOW()';
            } elseif ($status == 'Closed') {
                $upd .= ', closed_at=NOW()';
            }
            $upd .= ' WHERE id=?';
            $ub[] = $ticket_id;
            DB::update($upd, $ub);

            DB::insert('INSERT INTO it_ticket_status_history (ticket_id, from_status, to_status, changed_by, notes) VALUES (?, ?, ?, ?, ?)', [$ticket_id, $from_status, $status, $employee_id, $notes]);

            if (! empty($notes)) {
                DB::insert('INSERT INTO it_ticket_comments (ticket_id, employee_id, comment, is_admin) VALUES (?, ?, ?, '.($is_admin ? 1 : 0).')', [$ticket_id, $employee_id, $notes]);
            }

            $tRow = DB::selectOne('SELECT t.*, e.email as emp_email, e.full_name FROM it_tickets t JOIN employees e ON t.employee_id=e.id WHERE t.id=?', [$ticket_id]);
            if ($tRow) {
                notifyStatusUpdate($tRow, $tRow['emp_email'] ?? '', $tRow['full_name'] ?? '', $notes);
            }

            session(['message' => 'Ticket updated to '.$status.'!', 'message_type' => 'success']);
            kc_redirect('it-support');
        }

        // ── HANDLE: Feedback
        if ($request->isMethod('post') && $request->has('submit_feedback')) {
            if (! $request->has('form_token') || $request->input('form_token') !== session('form_token')) {
                kc_redirect('it-support');
            }
            session(['form_token' => bin2hex(random_bytes(32))]);
            // it_tickets.feedback / feedback_rating are not part of every installation's schema
            if (! ItHelpers::hasTicketFeedback()) {
                session(['message' => 'Ticket feedback is not available on this system.', 'message_type' => 'warning']);
                kc_redirect('it-support');
            }
            $ticket_id = (int) $request->input('ticket_id');
            $feedback = (string) ($request->input('feedback') ?? '');
            $rating = max(0, min(5, (int) ($request->input('rating') ?? 0)));
            $updated = DB::update("UPDATE it_tickets SET feedback=?, feedback_rating=? WHERE id=? AND employee_id=? AND status IN ('Resolved','Closed')", [$feedback, $rating, $ticket_id, $employee_id]);
            if ($updated > 0) {
                session(['message' => 'Feedback submitted!', 'message_type' => 'success']);
            } else {
                session(['message' => 'Feedback can only be given on your own resolved or closed tickets.', 'message_type' => 'warning']);
            }
            kc_redirect('it-support');
        }

        // ── AJAX: Ticket Detail
        if ($request->query('ajax') !== null && $request->query('ajax') == 'ticket_detail') {
            $tid = $request->query('id') !== null ? (int) $request->query('id') : 0;
            $acl = $is_admin ? '' : 'AND t.employee_id='.(int) $employee_id;
            $ticket = DB::selectOne("SELECT t.*, e.full_name as employee_name, e.employee_code, e.department, e.photo as emp_photo, a.full_name as assigned_to_name FROM it_tickets t JOIN employees e ON t.employee_id=e.id LEFT JOIN employees a ON t.assigned_to=a.id WHERE t.id=? $acl", [$tid]);
            if (! $ticket) {
                kc_exit('<div class="text-center py-5 text-danger"><h4>Access Denied</h4></div>', 200, ['Content-Type' => 'text/html; charset=utf-8']);
            }

            $comments = DB::select('SELECT c.*, e.full_name FROM it_ticket_comments c JOIN employees e ON c.employee_id=e.id WHERE c.ticket_id=? ORDER BY c.created_at ASC', [$tid]);
            $atts = DB::select('SELECT * FROM it_ticket_attachments WHERE ticket_id=? ORDER BY created_at DESC', [$tid]);
            $byC = [];
            $tAtts = [];
            foreach ($atts as $a) {
                $ci = (int) ($a['comment_id'] ?? 0);
                $byC[$ci][] = $a;
                if ($ci == 0) {
                    $tAtts[] = $a;
                }
            }
            $hist = DB::select('SELECT * FROM it_ticket_status_history WHERE ticket_id=? ORDER BY created_at DESC LIMIT 10', [$tid]);
            $hasFeedback = ItHelpers::hasTicketFeedback();
            $canGiveFeedback = $hasFeedback && (int) $ticket['employee_id'] === (int) $employee_id;

            return response()->view('it-support-ticket-detail', compact('ticket', 'comments', 'byC', 'tAtts', 'hist', 'tid', 'form_token', 'hasFeedback', 'canGiveFeedback'))
                ->header('Content-Type', 'text/html; charset=utf-8');
        }

        // ── AJAX: Refresh
        if ($request->query('ajax') !== null && $request->query('ajax') == 'refresh_tickets') {
            $last_id = (int) ($request->query('last_id') ?? 0);
            $new = $is_admin
                ? DB::select('SELECT t.*, e.full_name as employee_name, a.full_name as assigned_to_name FROM it_tickets t JOIN employees e ON t.employee_id=e.id LEFT JOIN employees a ON t.assigned_to=a.id WHERE t.id>? ORDER BY t.created_at DESC', [$last_id])
                : DB::select('SELECT t.*, a.full_name as assigned_to_name FROM it_tickets t LEFT JOIN employees a ON t.assigned_to=a.id WHERE t.employee_id=? AND t.id>? ORDER BY t.created_at DESC', [$employee_id, $last_id]);
            $stats = $is_admin
                ? DB::selectOne("SELECT COUNT(*) as total, SUM(CASE WHEN status='New' THEN 1 ELSE 0 END) as new_count, SUM(CASE WHEN status='In Progress' THEN 1 ELSE 0 END) as in_progress, SUM(CASE WHEN status='Resolved' THEN 1 ELSE 0 END) as resolved, SUM(CASE WHEN status='Closed' THEN 1 ELSE 0 END) as closed FROM it_tickets")
                : DB::selectOne("SELECT COUNT(*) as total, SUM(CASE WHEN status='New' THEN 1 ELSE 0 END) as new_count, SUM(CASE WHEN status='In Progress' THEN 1 ELSE 0 END) as in_progress, SUM(CASE WHEN status='Resolved' THEN 1 ELSE 0 END) as resolved, SUM(CASE WHEN status='Closed' THEN 1 ELSE 0 END) as closed FROM it_tickets WHERE employee_id=?", [$employee_id]);

            return kc_json(['tickets' => $new, 'stats' => $stats]);
        }

        // ── Export Filtered Tickets
        if ($request->query('export') !== null && $request->query('export') == 'csv') {
            [$where, $bindings] = self::ticketFilters($request, true);
            if (! $is_admin) {
                $where[] = 't.employee_id=?';
                $bindings[] = $employee_id;
            }
            $ws = ! empty($where) ? 'WHERE '.implode(' AND ', $where) : '';
            $rows = DB::select("SELECT t.ticket_number, e.full_name as employee_name, t.category, t.priority, t.subject, t.status, a.full_name as assigned_to_name, t.created_at, t.resolved_at, t.closed_at, t.created_by_admin FROM it_tickets t JOIN employees e ON t.employee_id=e.id LEFT JOIN employees a ON t.assigned_to=a.id $ws ORDER BY t.created_at DESC", $bindings);

            $output = fopen('php://temp', 'w+');
            fputcsv($output, ['Ticket #', 'Employee', 'Category', 'Priority', 'Subject', 'Status', 'Assigned To', 'Created At', 'Resolved At', 'Closed At', 'Created By Admin']);
            foreach ($rows as $row) {
                fputcsv($output, [
                    $row['ticket_number'],
                    $row['employee_name'],
                    $row['category'],
                    $row['priority'],
                    $row['subject'],
                    $row['status'],
                    $row['assigned_to_name'] ?? 'N/A',
                    $row['created_at'],
                    $row['resolved_at'] ?? '',
                    $row['closed_at'] ?? '',
                    $row['created_by_admin'] ? 'Yes' : 'No',
                ]);
            }
            rewind($output);
            $csv = stream_get_contents($output);
            fclose($output);

            return response($csv, 200, [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename=it_tickets_export_'.date('Y-m-d').'.csv',
            ]);
        }

        // ── Admin List & Filters
        $admin_employees = ItHelpers::getAdminEmployees();
        $all_employees = ItHelpers::getAllEmployees();
        $status_filter = ItHelpers::oneOf($request->query('status'), ['New', 'In Progress', 'Resolved', 'Closed', 'Rejected']);
        $priority_filter = ItHelpers::oneOf($request->query('priority'), ['Low', 'Medium', 'High', 'Urgent']);
        $search = ItHelpers::str($request->query('search'));
        $date_from = ItHelpers::validDate($request->query('date_from'));
        $date_to = ItHelpers::validDate($request->query('date_to'));
        $date_range = ItHelpers::oneOf($request->query('date_range'), ['today', 'week', 'month', 'unsolved']);

        if ($is_admin) {
            [$where, $bindings] = self::ticketFilters($request, true);
            $ws = ! empty($where) ? 'WHERE '.implode(' AND ', $where) : '';
            $my_tickets = DB::select("SELECT t.*, e.full_name as employee_name, e.photo, a.full_name as assigned_to_name, (SELECT COUNT(*) FROM it_ticket_comments WHERE ticket_id=t.id) as cc, (SELECT COUNT(*) FROM it_ticket_attachments WHERE ticket_id=t.id) as ac, t.created_by_admin FROM it_tickets t JOIN employees e ON t.employee_id=e.id LEFT JOIN employees a ON t.assigned_to=a.id $ws ORDER BY FIELD(t.status,'New','In Progress','Resolved','Closed'), t.created_at DESC", $bindings);

            $stats = DB::selectOne("SELECT
        COUNT(*) as total,
        SUM(CASE WHEN status='New' THEN 1 ELSE 0 END) as new_count,
        SUM(CASE WHEN status='In Progress' THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN status='Resolved' THEN 1 ELSE 0 END) as resolved,
        SUM(CASE WHEN status='Closed' THEN 1 ELSE 0 END) as closed,
        SUM(CASE WHEN priority='Urgent' THEN 1 ELSE 0 END) as urgent,
        SUM(CASE WHEN DATE(created_at)=CURDATE() THEN 1 ELSE 0 END) as today_count,
        SUM(CASE WHEN status IN ('New', 'In Progress') THEN 1 ELSE 0 END) as unsolved_count
        FROM it_tickets");
        } else {
            [$where, $bindings] = self::ticketFilters($request, false);
            array_unshift($where, 't.employee_id=?');
            array_unshift($bindings, $employee_id);
            $ws = ! empty($where) ? 'WHERE '.implode(' AND ', $where) : '';
            $my_tickets = DB::select("SELECT t.*, a.full_name as assigned_to_name, (SELECT COUNT(*) FROM it_ticket_comments WHERE ticket_id=t.id) as cc, (SELECT COUNT(*) FROM it_ticket_attachments WHERE ticket_id=t.id) as ac FROM it_tickets t LEFT JOIN employees a ON t.assigned_to=a.id $ws ORDER BY FIELD(t.status,'New','In Progress','Resolved','Closed'), t.created_at DESC", $bindings);

            $stats = DB::selectOne("SELECT
        COUNT(*) as total,
        SUM(CASE WHEN status='New' THEN 1 ELSE 0 END) as new_count,
        SUM(CASE WHEN status='In Progress' THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN status='Resolved' THEN 1 ELSE 0 END) as resolved,
        SUM(CASE WHEN status='Closed' THEN 1 ELSE 0 END) as closed,
        SUM(CASE WHEN DATE(created_at)=CURDATE() THEN 1 ELSE 0 END) as today_count,
        SUM(CASE WHEN status IN ('New', 'In Progress') THEN 1 ELSE 0 END) as unsolved_count
        FROM it_tickets WHERE employee_id=?", [$employee_id]);
        }

        $max_id = DB::selectOne('SELECT MAX(id) as max_id FROM it_tickets')['max_id'] ?? 0;
        $categories = ItHelpers::ticketCategories();

        return view('it-support', compact(
            'employee_id', 'employee_name', 'is_admin', 'form_token', 'admin_employees', 'all_employees',
            'status_filter', 'priority_filter', 'search', 'date_from', 'date_to', 'date_range',
            'my_tickets', 'stats', 'max_id', 'categories'
        ));
    }

    /** it-admin.php: ticket detail fragment (loaded into a modal) */
    public function admin(Request $request)
    {
        return $this->ticketView($request, false, true);
    }

    /** it-ticket-detail.php: same fragment as it-admin (tiny differences: "File missing: name", no int casts) */
    public function ticketDetail(Request $request)
    {
        return $this->ticketView($request, true, false);
    }

    private function ticketView(Request $request, bool $missingName, bool $castFlags)
    {
        if (! isLoggedIn()) {
            kc_exit('Unauthorized');
        }

        $ticket_id = $request->query('id') !== null ? (int) $request->query('id') : 0;
        $employee_id = session('employee_id');
        $is_admin_view = $request->query('is_admin') !== null ? (int) $request->query('is_admin') : 0;
        // ?is_admin=1 only widens access for real IT admins (it used to trust the query string)
        $is_admin = $is_admin_view == 1 && self::isItAdmin($employee_id);

        $access_clause = $is_admin ? '' : 'AND t.employee_id = '.(int) $employee_id;
        $ticket = DB::selectOne("SELECT t.*, 
          e.full_name as employee_name, e.employee_code, e.department, e.email as emp_email, e.photo as emp_photo,
          a.full_name as assigned_to_name, a.employee_code as assigned_to_code
          FROM it_tickets t 
          JOIN employees e ON t.employee_id = e.id 
          LEFT JOIN employees a ON t.assigned_to = a.id 
          WHERE t.id = ? $access_clause", [$ticket_id]);

        if (! $ticket) {
            kc_exit('<div class="text-center py-5 text-danger">Ticket not found or access denied.</div>');
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

        $attachments = DB::select('SELECT * FROM it_ticket_attachments 
    WHERE ticket_id = ? 
    ORDER BY created_at DESC', [$ticket_id]);

        $attachments_by_comment = [];
        $ticket_attachments = [];
        $image_count = 0;
        foreach ($attachments as $att) {
            $comment_id = isset($att['comment_id']) ? (int) $att['comment_id'] : 0;
            if (! isset($attachments_by_comment[$comment_id])) {
                $attachments_by_comment[$comment_id] = [];
            }
            $attachments_by_comment[$comment_id][] = $att;
            if ($comment_id == 0) {
                $ticket_attachments[] = $att;
            }
            if (ItHelpers::isImageFile($att['file_path'])) {
                $image_count++;
            }
        }

        $history = null;
        if (count(DB::select("SHOW TABLES LIKE 'it_ticket_status_history'")) > 0) {
            $history = DB::select('SELECT * FROM it_ticket_status_history 
        WHERE ticket_id = ? 
        ORDER BY created_at DESC', [$ticket_id]);
        }

        $ticket_attachment_count = count($ticket_attachments);

        // feedback is optional (columns missing from some schemas); only the ticket owner can give it
        $hasFeedback = ItHelpers::hasTicketFeedback();
        $canGiveFeedback = $hasFeedback && (int) $ticket['employee_id'] === (int) $employee_id;
        if (! session()->has('form_token')) {
            session(['form_token' => bin2hex(random_bytes(32))]);
        }
        $form_token = session('form_token');

        return view('it-admin', compact(
            'ticket', 'ticket_id', 'is_admin', 'answers', 'comments', 'attachments_by_comment',
            'ticket_attachments', 'image_count', 'history', 'ticket_attachment_count', 'missingName', 'castFlags',
            'hasFeedback', 'canGiveFeedback', 'form_token'
        ));
    }

    /** it-ticket-data.php: JSON of one ticket (edit modal) */
    public function ticketData(Request $request)
    {
        if (! isLoggedIn()) {
            return kc_json(['error' => 'Unauthorized'], 401);
        }

        $ticket_id = $request->query('id') !== null ? (int) $request->query('id') : 0;

        $ticket = DB::selectOne('SELECT t.*, 
          e.full_name as employee_name, e.employee_code,
          a.full_name as assigned_to_name, a.employee_code as assigned_to_code,
          (SELECT COUNT(*) FROM it_ticket_attachments WHERE ticket_id = t.id) as attachment_count,
          (SELECT COUNT(*) FROM it_ticket_comments WHERE ticket_id = t.id) as comment_count
          FROM it_tickets t 
          JOIN employees e ON t.employee_id = e.id 
          LEFT JOIN employees a ON t.assigned_to = a.id 
          WHERE t.id = ?', [$ticket_id]);

        if (! $ticket) {
            return kc_json(['error' => 'Ticket not found'], 404);
        }

        // only IT admins, or the employee who raised the ticket, may read it
        $employee_id = (int) session('employee_id');
        if ((int) $ticket['employee_id'] !== $employee_id && ! self::isItAdmin($employee_id)) {
            return kc_json(['error' => 'Unauthorized'], 403);
        }

        $ticket['attachments'] = DB::select('SELECT * FROM it_ticket_attachments WHERE ticket_id = ? ORDER BY created_at DESC', [$ticket_id]);

        return kc_json($ticket);
    }

    /** download-attachment.php: download a rewards attachment */
    public function downloadAttachment(Request $request)
    {
        if (! isLoggedIn()) {
            kc_redirect('login');
        }

        $attachment_id = $request->query('id') !== null ? (int) $request->query('id') : 0;

        if ($attachment_id) {
            $attachment = DB::selectOne('SELECT * FROM rewards_attachments WHERE id = ?', [$attachment_id]);
            if ($attachment) {
                $file_path = $attachment['file_path'];

                DB::update('UPDATE rewards_attachments SET download_count = download_count + 1 WHERE id = ?', [$attachment_id]);

                $disk_path = kc_public_file((string) $file_path, '');
                if (file_exists($disk_path)) {
                    return response(file_get_contents($disk_path), 200, [
                        'Content-Description' => 'File Transfer',
                        'Content-Type' => 'application/octet-stream',
                        'Content-Disposition' => 'attachment; filename="'.$attachment['file_name'].'"',
                        'Expires' => '0',
                        'Cache-Control' => 'must-revalidate',
                        'Pragma' => 'public',
                        'Content-Length' => filesize($disk_path),
                    ]);
                }
            }
        }

        kc_redirect('rewards');
    }

    /** backup.php: an old copy of the public home page (no login check) */
    public function backup(Request $request)
    {
        $carouselItems = DB::select('SELECT * FROM homepage_carousel WHERE status = 1 ORDER BY sort_order ASC');

        $birthdays = DB::select('SELECT * FROM birthdays 
                         WHERE MONTH(birth_date) = MONTH(CURDATE()) 
                         AND DAY(birth_date) = DAY(CURDATE())
                         AND status = 1
                         ORDER BY full_name ASC');

        $upcoming = DB::select("SELECT *, 
                         DATE_FORMAT(birth_date, '%m-%d') as bday_md,
                         DATEDIFF(
                             DATE(CONCAT(YEAR(CURDATE()), '-', MONTH(birth_date), '-', DAY(birth_date))),
                             CURDATE()
                         ) as days_until
                         FROM birthdays 
                         WHERE status = 1
                         AND DATE(CONCAT(YEAR(CURDATE()), '-', MONTH(birth_date), '-', DAY(birth_date))) 
                             BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                         AND NOT (MONTH(birth_date) = MONTH(CURDATE()) AND DAY(birth_date) = DAY(CURDATE()))
                         ORDER BY bday_md ASC
                         LIMIT 6");

        return view('backup', compact('carouselItems', 'birthdays', 'upcoming'));
    }

    /** backup-log.php: daily backup log (record a backup, list, CSV export) */
    public function backupLog(Request $request)
    {
        return self::backupLogPage($request, '', 'backup-log');
    }

    private static function backupLogPage(Request $request, string $scriptDir, string $view)
    {
        if (! session()->has('employee_id') && ! session()->has('admin_id') && ! session()->has('user_id')) {
            kc_redirect('./');
        }

        $success_message = '';
        $error_message = '';

        $employee_id = session('employee_id') ?? session('admin_id') ?? session('user_id') ?? null;
        $is_admin = false;
        if (session()->has('admin_id') || session()->has('admin_username')) {
            $is_admin = true;
        } else {
            $role_check = DB::select("SELECT role FROM employees WHERE id=? AND role IN ('admin','manager','hr','director')", [$employee_id]);
            if (count($role_check) > 0) {
                $is_admin = true;
            }
        }

        // Handle form submission
        if ($request->isMethod('post') && $request->has('submit_backup')) {
            $companies = $request->input('companies') ?? [];
            $backup_date = $request->input('backup_date') ?? date('Y-m-d');
            $notes = (string) ($request->input('notes') ?? '');
            $status = $request->input('status') ?? 'Completed';
            $ticket_id = ! empty($request->input('ticket_id')) ? intval($request->input('ticket_id')) : null;

            $screenshot = '';
            $file = $request->file('screenshot');
            if ($file && ! is_array($file) && $file->isValid()) {
                $upload_dir = kc_public_file('uploads/backup_screenshots/', $scriptDir);
                if (! file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $file_extension = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($file_extension, $allowed_extensions)) {
                    $new_filename = 'backup_'.date('Ymd_His').'_'.uniqid().'.'.$file_extension;
                    if (self::moveFile($file, $upload_dir, $new_filename)) {
                        $screenshot = $new_filename;
                    }
                }
            }

            if (empty($companies)) {
                $error_message = 'Please select at least one company.';
            } else {
                $companies_str = implode(', ', $companies);

                DB::insert('INSERT INTO backup_logs (backup_date, employee_id, companies, status, notes, screenshot, ticket_id) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)', [$backup_date, $employee_id === null ? null : (int) $employee_id, $companies_str, $status, $notes, $screenshot, $ticket_id]);
                $success_message = 'Backup recorded successfully!';
            }
        }

        // Handle export
        if ($request->query('export') !== null && $request->query('export') == 'csv') {
            $date_from = ItHelpers::validDate($request->query('date_from'));
            $date_to = ItHelpers::validDate($request->query('date_to'));
            $status_filter = ItHelpers::oneOf($request->query('status_filter'), ['Completed', 'Partial', 'Failed']);

            $where = [];
            $b = [];
            if ($date_from) {
                $where[] = 'DATE(bl.backup_date) >= ?';
                $b[] = $date_from;
            }
            if ($date_to) {
                $where[] = 'DATE(bl.backup_date) <= ?';
                $b[] = $date_to;
            }
            if ($status_filter) {
                $where[] = 'bl.status = ?';
                $b[] = $status_filter;
            }
            if (! $is_admin) {
                $where[] = 'bl.employee_id = ?';
                $b[] = $employee_id;
            }

            $ws = ! empty($where) ? 'WHERE '.implode(' AND ', $where) : '';
            $rows = DB::select("SELECT bl.*, e.full_name as employee_name 
                     FROM backup_logs bl 
                     LEFT JOIN employees e ON bl.employee_id = e.id 
                     $ws 
                     ORDER BY bl.backup_date DESC, bl.created_at DESC", $b);

            $output = fopen('php://temp', 'w+');
            fputcsv($output, ['Date', 'Staff', 'Companies', 'Status', 'Notes', 'Evidence', 'Ticket ID', 'Created At']);
            foreach ($rows as $row) {
                fputcsv($output, [
                    $row['backup_date'],
                    $row['employee_name'] ?? 'Unknown',
                    $row['companies'],
                    $row['status'],
                    $row['notes'] ?? '',
                    $row['screenshot'] ? 'Yes' : 'No',
                    $row['ticket_id'] ?? '',
                    $row['created_at'],
                ]);
            }
            rewind($output);
            $csv = stream_get_contents($output);
            fclose($output);

            // anything printed before (none in practice) would precede the CSV; the page body is not sent
            return response($csv, 200, [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename=backup_logs_export_'.date('Y-m-d').'.csv',
            ]);
        }

        // Filters (the escaped values are what the page prints back)
        $status_filter = $raw_status = ItHelpers::oneOf($request->query('status_filter'), ['Completed', 'Partial', 'Failed']);
        $date_from = $raw_from = ItHelpers::validDate($request->query('date_from'));
        $date_to = $raw_to = ItHelpers::validDate($request->query('date_to'));

        $companies_result = DB::select('SELECT * FROM companies WHERE status = 1 ORDER BY name ASC');
        $tickets_result = DB::select("SELECT id, ticket_number, subject FROM it_tickets WHERE status IN ('New', 'In Progress') ORDER BY created_at DESC");

        $where = [];
        $b = [];
        if ($status_filter) {
            $where[] = 'bl.status = ?';
            $b[] = $raw_status;
        }
        if ($date_from) {
            $where[] = 'DATE(bl.backup_date) >= ?';
            $b[] = $raw_from;
        }
        if ($date_to) {
            $where[] = 'DATE(bl.backup_date) <= ?';
            $b[] = $raw_to;
        }
        if (! $is_admin) {
            $where[] = 'bl.employee_id = ?';
            $b[] = $employee_id;
        }
        $ws = ! empty($where) ? 'WHERE '.implode(' AND ', $where) : '';

        $backup_logs = DB::select("SELECT bl.*, e.full_name as employee_name 
                      FROM backup_logs bl 
                      LEFT JOIN employees e ON bl.employee_id = e.id 
                      $ws 
                      ORDER BY bl.backup_date DESC, bl.created_at DESC 
                      LIMIT 100", $b);

        $stats = DB::selectOne("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status='Completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status='Partial' THEN 1 ELSE 0 END) as partial,
    SUM(CASE WHEN status='Failed' THEN 1 ELSE 0 END) as failed,
    SUM(CASE WHEN DATE(backup_date)=CURDATE() THEN 1 ELSE 0 END) as today_count,
    SUM(CASE WHEN DATE(backup_date)=DATE_SUB(CURDATE(), INTERVAL 1 DAY) THEN 1 ELSE 0 END) as yesterday_count,
    SUM(CASE WHEN screenshot IS NOT NULL AND screenshot != '' THEN 1 ELSE 0 END) as with_evidence,
    COUNT(DISTINCT employee_id) as unique_staff
    FROM backup_logs bl".(! $is_admin ? ' WHERE bl.employee_id = ?' : ''), ! $is_admin ? [$employee_id] : []);

        return view($view, compact(
            'success_message', 'error_message', 'is_admin', 'status_filter', 'date_from', 'date_to',
            'companies_result', 'tickets_result', 'backup_logs', 'stats'
        ));
    }

    /**
     * The WHERE conditions of the it-support ticket list / CSV export
     * (status, priority, dates, date range preset, search).
     * The employee list searches subject and number only; the admin list and the export also the employee name.
     */
    private static function ticketFilters(Request $request, bool $searchName): array
    {
        $status_filter = ItHelpers::str($request->query('status'));
        $priority_filter = ItHelpers::str($request->query('priority'));
        $search = ItHelpers::str($request->query('search'));
        $date_from = ItHelpers::validDate($request->query('date_from'));
        $date_to = ItHelpers::validDate($request->query('date_to'));
        $date_range = ItHelpers::str($request->query('date_range'));

        $where = [];
        $b = [];
        if ($status_filter !== '') {
            $where[] = 't.status=?';
            $b[] = $status_filter;
        }
        if ($priority_filter !== '') {
            $where[] = 't.priority=?';
            $b[] = $priority_filter;
        }
        if ($date_from) {
            $where[] = 'DATE(t.created_at)>=?';
            $b[] = $date_from;
        }
        if ($date_to) {
            $where[] = 'DATE(t.created_at)<=?';
            $b[] = $date_to;
        }
        if ($date_range == 'today') {
            $where[] = 'DATE(t.created_at)=CURDATE()';
        } elseif ($date_range == 'unsolved') {
            $where[] = "t.status IN ('New', 'In Progress')";
        } elseif ($date_range == 'week') {
            $where[] = 'YEARWEEK(t.created_at)=YEARWEEK(CURDATE())';
        } elseif ($date_range == 'month') {
            $where[] = 'MONTH(t.created_at)=MONTH(CURDATE()) AND YEAR(t.created_at)=YEAR(CURDATE())';
        }
        if ($search !== '') {
            $like = '%'.$search.'%';
            if ($searchName) {
                $where[] = '(t.ticket_number LIKE ? OR t.subject LIKE ? OR e.full_name LIKE ?)';
                array_push($b, $like, $like, $like);
            } else {
                $where[] = '(t.ticket_number LIKE ? OR t.subject LIKE ?)';
                array_push($b, $like, $like);
            }
        }

        return [$where, $b];
    }

    /** Admin session, or an employee with an admin-type role (the it-support pages' $is_admin). */
    public static function isItAdmin($employee_id): bool
    {
        if (session()->has('admin_id') || session()->has('admin_username')) {
            return true;
        }

        return $employee_id
            && count(DB::select("SELECT role FROM employees WHERE id=? AND role IN ('admin','manager','hr','director')", [$employee_id])) > 0;
    }

    /** $_FILES['field'] with name="field[]": the list of UploadedFile (empty when none was sent). */
    public static function uploadedFiles(Request $request, string $field): array
    {
        $files = $request->file($field);
        if ($files === null) {
            return [];
        }

        return is_array($files) ? array_values(array_filter($files)) : [$files];
    }

    /** move_uploaded_file(): true on success, false on failure. */
    public static function moveFile($file, string $dir, string $name): bool
    {
        try {
            $file->move($dir, $name);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}

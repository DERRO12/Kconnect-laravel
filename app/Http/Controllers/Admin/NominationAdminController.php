<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\NominationsLegacy;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Admin side of the quarterly awards nominations (admin/ nomination pages). */
class NominationAdminController extends Controller
{
    /** nominations.status enum */
    private const STATUSES = ['pending', 'reviewed', 'approved', 'rejected'];

    /** Old: if (!isset($_SESSION['admin_id']) && !isset($_SESSION['admin_username'])) */
    private function adminMissing(): bool
    {
        return !session()->has('admin_id') && !session()->has('admin_username');
    }

    /** Old: isset($_GET[$k]) ? (int)$_GET[$k] : $default */
    private function qint(Request $request, string $k, $default = 0)
    {
        return $request->query($k) !== null ? (int) $request->query($k) : $default;
    }

    /** Current quarter/year from nomination_settings, or today's quarter */
    private function currentQuarter(): array
    {
        $settings = DB::selectOne('SELECT current_quarter, current_year FROM nomination_settings WHERE status = 1 ORDER BY id DESC LIMIT 1');
        if ($settings) {
            return [$settings['current_quarter'], $settings['current_year']];
        }

        return [ceil(date('n') / 3), date('Y')];
    }

    /** admin/all-nominations */
    public function allNominations(Request $request)
    {
        // Check if admin is logged in
        if ($this->adminMissing()) {
            kc_redirect('../admin/login');
        }

        // Handle Excel Export - ADVANCED EXPORT WITH FILTERS
        if ($request->query('export_excel') !== null && $request->query('export_excel') == 1) {
            return $this->allNominationsExport($request);
        }

        // Handle bulk actions
        if ($request->isMethod('post')) {
            if ($request->post('bulk_action') !== null && $request->post('selected_ids') !== null) {
                $selected_ids = $request->post('selected_ids');
                $action = $request->post('bulk_action');
                $ids = array_values(array_filter(array_map('intval', (array) $selected_ids)));
                if (empty($ids)) {
                    NominationsLegacy::fail('all-nominations', 'Please select at least one nomination.', null, true);
                }
                $ids_string = implode(',', $ids);

                try {
                    if ($action === 'delete') {
                        DB::transaction(function () use ($ids_string) {
                            DB::delete("DELETE FROM nomination_answers WHERE nomination_id IN ($ids_string)");
                            DB::delete("DELETE FROM nominations WHERE id IN ($ids_string)");
                        });
                        session(['message' => 'Nominations deleted successfully!', 'message_type' => 'success']);
                    } elseif ($action === 'mark_read') {
                        DB::update("UPDATE nominations SET is_read = 1, read_at = NOW(), read_by = ? WHERE id IN ($ids_string)", [session('admin_id')]);
                        session(['message' => 'Nominations marked as read!', 'message_type' => 'success']);
                    } elseif ($action === 'mark_unread') {
                        DB::update("UPDATE nominations SET is_read = 0, read_at = NULL, read_by = NULL WHERE id IN ($ids_string)");
                        session(['message' => 'Nominations marked as unread!', 'message_type' => 'success']);
                    } elseif ($action === 'approve') {
                        DB::update("UPDATE nominations SET status = 'approved' WHERE id IN ($ids_string)");
                        session(['message' => 'Nominations approved!', 'message_type' => 'success']);
                    } elseif ($action === 'reject') {
                        DB::update("UPDATE nominations SET status = 'rejected' WHERE id IN ($ids_string)");
                        session(['message' => 'Nominations rejected!', 'message_type' => 'success']);
                    }
                } catch (QueryException $e) {
                    NominationsLegacy::fail('all-nominations', 'The bulk action could not be applied (database error). Please try again.', $e, true);
                }
                kc_redirect('all-nominations');
            }

            // Handle single nomination update
            if ($request->post('update_nomination') !== null) {
                $nom_id = (int) $request->post('nomination_id');
                $status = (string) $request->post('status');
                $admin_notes = (string) $request->post('admin_notes');
                if (!in_array($status, self::STATUSES, true)) {
                    NominationsLegacy::fail('all-nominations', 'Invalid nomination status.', null, true);
                }

                try {
                    DB::update('UPDATE nominations SET status = ?, admin_notes = ? WHERE id = ?', [$status, $admin_notes, $nom_id]);
                } catch (QueryException $e) {
                    NominationsLegacy::fail('all-nominations', 'The nomination could not be updated (database error).', $e, true);
                }
                session(['message' => 'Nomination updated successfully!', 'message_type' => 'success']);
                kc_redirect('all-nominations');
            }
        }

        // Get filter parameters (the old page showed the mysqli-escaped values)
        $category_filter = $this->qint($request, 'category');
        $status_raw = $request->query('status') !== null ? (string) $request->query('status') : '';
        $read_raw = $request->query('read_status') !== null ? (string) $request->query('read_status') : '';
        $search_raw = $request->query('search') !== null ? (string) $request->query('search') : '';
        $status_filter = kc_escape($status_raw);
        $read_filter = kc_escape($read_raw);
        $search = kc_escape($search_raw);
        $sort_by = $request->query('sort_by') !== null ? kc_escape($request->query('sort_by')) : 'created_at';
        $sort_order = $request->query('sort_order') !== null ? kc_escape($request->query('sort_order')) : 'DESC';

        // Build WHERE clause
        $where = [];
        $bindings = [];
        if ($category_filter > 0) {
            $where[] = "n.category_id = $category_filter";
        }
        if ($status_filter) {
            $where[] = 'n.status = ?';
            $bindings[] = $status_raw;
        }
        if ($read_filter === 'unread') {
            $where[] = 'n.is_read = 0';
        }
        if ($read_filter === 'read') {
            $where[] = 'n.is_read = 1';
        }
        if ($search) {
            $where[] = '(nom.full_name LIKE ? OR nom.employee_code LIKE ? OR nomi.full_name LIKE ? OR nomi.employee_code LIKE ?)';
            array_push($bindings, "%$search_raw%", "%$search_raw%", "%$search_raw%", "%$search_raw%");
        }

        $where_sql = !empty($where) ? 'WHERE '.implode(' AND ', $where) : '';

        // Validate sort
        switch ($sort_by) {
            case 'nominee_name':
                $sort_column = 'nomi.full_name';
                break;
            case 'category_name':
                $sort_column = 'cat.name';
                break;
            case 'status':
                $sort_column = 'n.status';
                break;
            case 'is_read':
                $sort_column = 'n.is_read';
                break;
            case 'created_at':
            default:
                $sort_column = 'n.created_at';
                break;
        }
        $sort_order = ($sort_order === 'ASC') ? 'ASC' : 'DESC';

        // Get all nominations with details
        $nominations = DB::select("
    SELECT n.*,
           cat.name as category_name, cat.color as category_color, cat.icon as category_icon,
           nom.id as nominator_id, nom.employee_code as nominator_code, nom.full_name as nominator_name,
           nom.department as nominator_dept, nom.position as nominator_position, nom.photo as nominator_photo,
           nomi.id as nominee_id, nomi.employee_code as nominee_code, nomi.full_name as nominee_name,
           nomi.department as nominee_dept, nomi.position as nominee_position, nomi.photo as nominee_photo,
           (SELECT COUNT(*) FROM nomination_answers WHERE nomination_id = n.id) as answers_count
    FROM nominations n
    JOIN nomination_categories cat ON n.category_id = cat.id
    JOIN employees nom ON n.nominator_id = nom.id
    JOIN employees nomi ON n.nominee_id = nomi.id
    $where_sql
    ORDER BY $sort_column $sort_order
", $bindings);

        // Get categories for filter
        $categories = DB::select('SELECT * FROM nomination_categories WHERE status = 1 ORDER BY name');

        // Get statistics
        $stats = DB::selectOne("
    SELECT
        COUNT(*) as total,
        SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'reviewed' THEN 1 ELSE 0 END) as reviewed,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
        COUNT(DISTINCT nominator_id) as unique_nominators,
        COUNT(DISTINCT nominee_id) as unique_nominees
    FROM nominations
");

        // Get category breakdown
        $category_stats = DB::select('
    SELECT cat.name, COUNT(n.id) as count
    FROM nomination_categories cat
    LEFT JOIN nominations n ON cat.id = n.category_id
    WHERE cat.status = 1
    GROUP BY cat.id
    ORDER BY count DESC
');

        // Get current quarter for info
        [$current_quarter, $current_year] = $this->currentQuarter();
        $quarter_names = [
            1 => 'Q1 (Jan-Mar)',
            2 => 'Q2 (Apr-Jun)',
            3 => 'Q3 (Jul-Sep)',
            4 => 'Q4 (Oct-Dec)'
        ];

        // "Current View" category name (was queried inside the page)
        $filter_category_row = $category_filter > 0
            ? DB::selectOne('SELECT name FROM nomination_categories WHERE id = ?', [$category_filter])
            : null;

        return view('admin.all-nominations', compact(
            'nominations', 'categories', 'stats', 'category_stats', 'current_quarter', 'current_year',
            'quarter_names', 'category_filter', 'status_filter', 'read_filter', 'search', 'filter_category_row'
        ));
    }

    /**
     * admin/api/mark_as_read: the "Mark as Read" button on all-nominations (POST JSON {"id": n}).
     * The old page called api/mark_as_read.php, which never existed; this sets the same
     * is_read / read_at / read_by columns as the bulk action and view-nomination.
     */
    public function markAsRead(Request $request)
    {
        if ($this->adminMissing()) {
            return kc_json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        if (!$request->isMethod('post')) {
            return kc_json(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        $id = (int) ($request->json('id') ?? $request->input('id') ?? 0);
        if ($id <= 0) {
            return kc_json(['success' => false, 'message' => 'Missing nomination id'], 422);
        }

        try {
            $exists = DB::selectOne('SELECT id FROM nominations WHERE id = ?', [$id]);
            if (!$exists) {
                return kc_json(['success' => false, 'message' => 'Nomination not found'], 404);
            }
            DB::update('UPDATE nominations SET is_read = 1, read_at = NOW(), read_by = ? WHERE id = ? AND is_read = 0', [session('admin_id'), $id]);
        } catch (QueryException $e) {
            NominationsLegacy::failJson('Could not mark the nomination as read.', $e);
        }

        return kc_json(['success' => true]);
    }

    /** admin/all-nominations?export_excel=1 */
    private function allNominationsExport(Request $request)
    {
        // Get all filter parameters for export
        $export_category_filter = $this->qint($request, 'category');
        $status_raw = $request->query('status') !== null ? (string) $request->query('status') : '';
        $read_raw = $request->query('read_status') !== null ? (string) $request->query('read_status') : '';
        $search_raw = $request->query('search') !== null ? (string) $request->query('search') : '';
        $export_status_filter = kc_escape($status_raw);
        $export_read_filter = kc_escape($read_raw);
        $export_search = kc_escape($search_raw);

        // Build WHERE clause for export
        $export_where = [];
        $bindings = [];
        if ($export_category_filter > 0) {
            $export_where[] = "n.category_id = $export_category_filter";
        }
        if ($export_status_filter) {
            $export_where[] = 'n.status = ?';
            $bindings[] = $status_raw;
        }
        if ($export_read_filter === 'unread') {
            $export_where[] = 'n.is_read = 0';
        }
        if ($export_read_filter === 'read') {
            $export_where[] = 'n.is_read = 1';
        }
        if ($export_search) {
            $export_where[] = '(nom.full_name LIKE ? OR nom.employee_code LIKE ? OR nomi.full_name LIKE ? OR nomi.employee_code LIKE ?)';
            array_push($bindings, "%$search_raw%", "%$search_raw%", "%$search_raw%", "%$search_raw%");
        }

        $export_where_sql = !empty($export_where) ? 'WHERE '.implode(' AND ', $export_where) : '';

        // Get current quarter/year from settings
        [$current_quarter, $current_year] = $this->currentQuarter();

        $quarter_names = [
            1 => 'Q1 (Jan-Mar)',
            2 => 'Q2 (Apr-Jun)',
            3 => 'Q3 (Jul-Sep)',
            4 => 'Q4 (Oct-Dec)'
        ];

        // Get all nominations with complete details based on filters
        $export_result = DB::select("
        SELECT
            n.id AS nomination_id,
            n.created_at AS nomination_date,
            n.quarter,
            n.year,
            n.status,
            n.is_winner,
            n.admin_notes,
            n.level1_decision,
            n.level1_notes,
            n.level1_reviewed_at,
            n.level2_decision,
            n.level2_notes,
            n.level2_reviewed_at,
            n.level3_decision,
            n.level3_notes,
            n.level3_reviewed_at,
            cat.id AS category_id,
            cat.name AS category_name,
            cat.description AS category_description,
            nom.id AS nominator_id,
            nom.employee_code AS nominator_code,
            nom.full_name AS nominator_name,
            nom.department AS nominator_department,
            nom.position AS nominator_position,
            nom.email AS nominator_email,
            nom.phone AS nominator_phone,
            nom.mobile AS nominator_mobile,
            nom.join_date AS nominator_join_date,
            nomi.id AS nominee_id,
            nomi.employee_code AS nominee_code,
            nomi.full_name AS nominee_name,
            nomi.department AS nominee_department,
            nomi.position AS nominee_position,
            nomi.email AS nominee_email,
            nomi.phone AS nominee_phone,
            nomi.mobile AS nominee_mobile,
            nomi.join_date AS nominee_join_date
        FROM nominations n
        JOIN nomination_categories cat ON n.category_id = cat.id
        JOIN employees nom ON n.nominator_id = nom.id
        JOIN employees nomi ON n.nominee_id = nomi.id
        $export_where_sql
        ORDER BY n.created_at DESC
    ", $bindings);

        // Get ALL questions for the filtered categories
        $questions_conditions = [];
        if ($export_category_filter > 0) {
            $questions_conditions[] = "q.category_id = $export_category_filter";
        }
        // Always include active questions
        $questions_conditions[] = 'q.status = 1';
        $questions_where_sql = 'WHERE '.implode(' AND ', $questions_conditions);

        $all_questions = [];
        foreach (DB::select("
        SELECT DISTINCT q.id, q.question_text, q.question_type, q.category_id, cat.name as category_name
        FROM nomination_questions q
        JOIN nomination_categories cat ON q.category_id = cat.id
        $questions_where_sql
        ORDER BY q.category_id, q.sort_order
    ") as $q) {
            $all_questions[$q['id']] = $q;
        }

        // Get all nomination IDs from the filtered results
        $nomination_ids = [];
        $export_data_rows = [];
        foreach ($export_result as $row) {
            $nomination_ids[] = $row['nomination_id'];
            $export_data_rows[$row['nomination_id']] = $row;
        }

        // Get answers for all nominations in one query
        $answers_by_nomination = [];
        if (!empty($nomination_ids)) {
            $ids_string = implode(',', array_map('intval', $nomination_ids));
            foreach (DB::select("
            SELECT na.nomination_id, na.question_id, na.answer
            FROM nomination_answers na
            WHERE na.nomination_id IN ($ids_string)
        ") as $ans) {
                $answers_by_nomination[$ans['nomination_id']][$ans['question_id']] = $ans['answer'];
            }
        }

        // Prepare data for export with all columns
        $export_data = [];
        $filter_type_name = '';
        if ($export_category_filter > 0) {
            $cat = DB::selectOne('SELECT name FROM nomination_categories WHERE id = ?', [$export_category_filter]);
            if ($cat) {
                $filter_type_name = ' - '.str_replace(' ', '_', $cat['name']);
            }
        }
        if ($export_status_filter) {
            $filter_type_name .= ' - '.ucfirst($export_status_filter);
        }
        if ($export_search) {
            $filter_type_name .= ' - Search_'.substr(preg_replace('/[^a-zA-Z0-9]/', '_', $export_search), 0, 30);
        }

        foreach ($export_data_rows as $nom_id => $row) {
            $row_data = [
                // Basic Nomination Info
                'Nomination ID' => $row['nomination_id'],
                'Nomination Date' => date('Y-m-d H:i:s', strtotime($row['nomination_date'])),
                'Quarter' => $quarter_names[$row['quarter']] ?? null,
                'Year' => $row['year'],
                'Status' => ucfirst($row['status']),
                'Is Winner' => $row['is_winner'] ? 'Yes' : 'No',

                // Category Info
                'Category ID' => $row['category_id'],
                'Category Name' => $row['category_name'],
                'Category Description' => $row['category_description'],

                // Nominator Complete Info
                'Nominator ID' => $row['nominator_id'],
                'Nominator Code' => $row['nominator_code'],
                'Nominator Name' => $row['nominator_name'],
                'Nominator Department' => $row['nominator_department'] ?? '—',
                'Nominator Position' => $row['nominator_position'] ?? '—',
                'Nominator Email' => $row['nominator_email'] ?? '—',
                'Nominator Phone' => $row['nominator_phone'] ?? '—',
                'Nominator Mobile' => $row['nominator_mobile'] ?? '—',
                'Nominator Join Date' => $row['nominator_join_date'] ?? '—',

                // Nominee Complete Info
                'Nominee ID' => $row['nominee_id'],
                'Nominee Code' => $row['nominee_code'],
                'Nominee Name' => $row['nominee_name'],
                'Nominee Department' => $row['nominee_department'] ?? '—',
                'Nominee Position' => $row['nominee_position'] ?? '—',
                'Nominee Email' => $row['nominee_email'] ?? '—',
                'Nominee Phone' => $row['nominee_phone'] ?? '—',
                'Nominee Mobile' => $row['nominee_mobile'] ?? '—',
                'Nominee Join Date' => $row['nominee_join_date'] ?? '—',

                // Committee Decision Details
                'Level 1 Decision' => ucfirst($row['level1_decision'] ?? 'Pending'),
                'Level 1 Notes' => $row['level1_notes'] ?? '—',
                'Level 1 Reviewed At' => $row['level1_reviewed_at'] ? date('Y-m-d H:i:s', strtotime($row['level1_reviewed_at'])) : '—',
                'Level 2 Decision' => ucfirst($row['level2_decision'] ?? 'Pending'),
                'Level 2 Notes' => $row['level2_notes'] ?? '—',
                'Level 2 Reviewed At' => $row['level2_reviewed_at'] ? date('Y-m-d H:i:s', strtotime($row['level2_reviewed_at'])) : '—',
                'Level 3 Decision' => ucfirst($row['level3_decision'] ?? 'Pending'),
                'Level 3 Notes' => $row['level3_notes'] ?? '—',
                'Level 3 Reviewed At' => $row['level3_reviewed_at'] ? date('Y-m-d H:i:s', strtotime($row['level3_reviewed_at'])) : '—',
                'Admin Notes' => $row['admin_notes'] ?? '—',
            ];

            // Add ALL question answers as individual columns
            foreach ($all_questions as $qid => $question) {
                $col_name = 'Q_'.$qid.': '.$question['question_text'];
                // Truncate column name if too long (Excel max is 255 chars)
                if (strlen($col_name) > 250) {
                    $col_name = substr($col_name, 0, 247).'...';
                }

                $answer = $answers_by_nomination[$nom_id][$qid] ?? '';

                // Clean up answer based on question type
                if (!empty($answer)) {
                    if ($question['question_type'] == 'attachment' && strpos($answer, 'uploads/') !== false) {
                        $answer = '[File Uploaded: '.basename($answer).']';
                    } elseif ($question['question_type'] == 'checkbox') {
                        // Format checkbox answers nicely
                        $answer = str_replace(',', ', ', $answer);
                    } elseif ($question['question_type'] == 'rating') {
                        $rating_val = (int) $answer;
                        $answer = str_repeat('★', $rating_val).str_repeat('☆', 5 - $rating_val).' ('.$rating_val.'/5)';
                    }
                } else {
                    $answer = '—';
                }

                $row_data[$col_name] = $answer;
            }

            $export_data[] = $row_data;
        }

        // Create filename with filter information
        $filename = 'nominations_export_'.date('Y-m-d_His').$filter_type_name.'.xls';

        // Output data as HTML table (Excel can read this format)
        $o = '<html>';
        $o .= '<head>';
        $o .= '<meta charset="UTF-8">';
        $o .= '<title>Nominations Export - '.date('Y-m-d H:i:s').'</title>';
        $o .= '<style>';
        $o .= 'th { background-color: #009ada; color: white; padding: 10px; border: 1px solid #ddd; }';
        $o .= 'td { padding: 8px; border: 1px solid #ddd; }';
        $o .= 'tr:nth-child(even) { background-color: #f9f9f9; }';
        $o .= '.header-info { margin-bottom: 20px; font-size: 12px; }';
        $o .= '</style>';
        $o .= '</head>';
        $o .= '<body>';

        // Add report header information
        $o .= '<div class="header-info">';
        $o .= '<h2>Nominations Report</h2>';
        $o .= '<p><strong>Generated:</strong> '.date('Y-m-d H:i:s').'</p>';
        $o .= '<p><strong>Quarter:</strong> '.($quarter_names[$current_quarter] ?? null).' '.$current_year.'</p>';
        if ($export_category_filter > 0) {
            $category_name_for_header = '';
            if (!empty($export_data)) {
                $category_name_for_header = $export_data[0]['Category Name'] ?? 'Selected Category';
            }
            $o .= '<p><strong>Category Filter:</strong> '.htmlspecialchars($category_name_for_header).'</p>';
        }
        if ($export_status_filter) {
            $o .= '<p><strong>Status Filter:</strong> '.ucfirst($export_status_filter).'</p>';
        }
        if ($export_search) {
            $o .= '<p><strong>Search Filter:</strong> '.htmlspecialchars($export_search).'</p>';
        }
        $o .= '<p><strong>Total Records:</strong> '.count($export_data).'</p>';
        $o .= '</div>';

        $o .= '<table border="1" cellpadding="5" cellspacing="0">';

        // Headers
        if (!empty($export_data)) {
            $o .= '<thead><tr>';
            foreach (array_keys($export_data[0]) as $header) {
                $o .= '<th style="background-color: #009ada; color: white; padding: 10px;">'.htmlspecialchars($header).'</th>';
            }
            $o .= '</tr></thead>';

            // Data rows
            $o .= '<tbody>';
            foreach ($export_data as $row) {
                $o .= '<tr>';
                foreach ($row as $value) {
                    // Clean value for Excel
                    $clean_value = str_replace(["\r\n", "\n", "\r"], ' ', (string) $value);
                    $clean_value = strip_tags($clean_value);
                    $o .= '<td style="padding: 8px;">'.nl2br(htmlspecialchars($clean_value)).'</td>';
                }
                $o .= '</tr>';
            }
            $o .= '</tbody>';
        } else {
            $o .= '<tr><td colspan="100" style="text-align: center; padding: 40px;">No nominations found matching the selected filters.</td></tr>';
        }

        $o .= '</body></html>';

        return response($o, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /** admin/nomination-dashboard */
    public function nominationDashboard(Request $request)
    {
        // Check admin login
        if ($this->adminMissing()) {
            kc_redirect('../admin/login');
        }

        // Handle mark as read
        if ($request->post('mark_read') !== null && $request->post('nomination_id') !== null) {
            $nom_id = (int) $request->post('nomination_id');
            $admin_id = session('admin_id') ?? 0;
            DB::update('UPDATE nominations SET is_read = 1, read_at = NOW(), read_by = ? WHERE id = ?', [$admin_id, $nom_id]);
        }

        // Get available quarters for filter
        $available_quarters = DB::select('
    SELECT DISTINCT quarter, year
    FROM nominations
    ORDER BY year DESC, quarter DESC
');

        // If no quarters exist, add current
        if (empty($available_quarters)) {
            $available_quarters[] = ['quarter' => ceil(date('n') / 3), 'year' => date('Y')];
        }

        // Get selected quarter from GET or default to current
        $selected_quarter = $this->qint($request, 'quarter');
        $selected_year = $this->qint($request, 'year');

        // If no valid selection, use current
        if ($selected_quarter == 0 || $selected_year == 0) {
            [$selected_quarter, $selected_year] = $this->currentQuarter();
        }

        $quarter_names = [
            1 => 'Q1 (Jan-Mar)',
            2 => 'Q2 (Apr-Jun)',
            3 => 'Q3 (Jul-Sep)',
            4 => 'Q4 (Oct-Dec)'
        ];

        // Handle Excel Export - COMPLETE QUARTER DUMP
        if ($request->query('export_excel') !== null && $request->query('export_excel') == 1) {
            $export_quarter = $this->qint($request, 'export_quarter', $selected_quarter);
            $export_year = $this->qint($request, 'export_year', $selected_year);

            return $this->dashboardExport($export_quarter, $export_year, $quarter_names);
        }

        // Get filter parameters
        $category_filter = $this->qint($request, 'category');
        $status_raw = $request->query('status') !== null ? (string) $request->query('status') : '';
        $read_raw = $request->query('read_status') !== null ? (string) $request->query('read_status') : '';
        $status_filter = kc_escape($status_raw);
        $read_filter = kc_escape($read_raw);

        // Build query with quarter filter
        $where = ['n.quarter = ? AND n.year = ?'];
        $bindings = [$selected_quarter, $selected_year];
        if ($category_filter > 0) {
            $where[] = "n.category_id = $category_filter";
        }
        if ($status_filter) {
            $where[] = 'n.status = ?';
            $bindings[] = $status_raw;
        }
        if ($read_filter === 'unread') {
            $where[] = 'n.is_read = 0';
        }
        if ($read_filter === 'read') {
            $where[] = 'n.is_read = 1';
        }
        $where_sql = 'WHERE '.implode(' AND ', $where);

        // Get nominations with details
        $nominations = DB::select("
    SELECT n.*,
           cat.name as category_name,
           nom.employee_code as nominator_code, nom.full_name as nominator_name, nom.department as nominator_dept,
           nomi.employee_code as nominee_code, nomi.full_name as nominee_name, nomi.department as nominee_dept,
           nomi.photo as nominee_photo
    FROM nominations n
    JOIN nomination_categories cat ON n.category_id = cat.id
    JOIN employees nom ON n.nominator_id = nom.id
    JOIN employees nomi ON n.nominee_id = nomi.id
    $where_sql
    ORDER BY n.is_read ASC, n.created_at DESC
", $bindings);

        // Get categories for filter
        $categories = DB::select('SELECT * FROM nomination_categories WHERE status = 1 ORDER BY sort_order');

        // Get statistics for selected quarter
        $stats = DB::selectOne("
    SELECT
        COUNT(*) as total,
        SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'reviewed' THEN 1 ELSE 0 END) as reviewed,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
    FROM nominations
    WHERE quarter = ? AND year = ?
", [$selected_quarter, $selected_year]);

        // Get detailed leaderboard with top 4 nominees per category for selected quarter
        $detailed_results = DB::select("
    SELECT
        cat.id as category_id,
        cat.name as category_name,
        cat.icon,
        cat.color,
        e.id as nominee_id,
        e.full_name as nominee_name,
        e.employee_code as nominee_code,
        e.department as nominee_department,
        e.position as nominee_position,
        e.photo as nominee_photo,
        COUNT(n.id) as vote_count,
        GROUP_CONCAT(DISTINCT nom.full_name SEPARATOR '||') as nominators
    FROM nomination_categories cat
    LEFT JOIN nominations n ON cat.id = n.category_id AND n.status != 'rejected' AND n.quarter = ? AND n.year = ?
    LEFT JOIN employees e ON n.nominee_id = e.id
    LEFT JOIN employees nom ON n.nominator_id = nom.id
    WHERE cat.status = 1
    GROUP BY cat.id, n.nominee_id
    ORDER BY cat.sort_order, vote_count DESC
", [$selected_quarter, $selected_year]);

        // Organize leaderboard by category
        $leaderboard_by_category = [];
        foreach ($detailed_results as $row) {
            $cat_id = $row['category_id'];
            if (!isset($leaderboard_by_category[$cat_id])) {
                $leaderboard_by_category[$cat_id] = [
                    'name' => $row['category_name'] ?? '',
                    'icon' => $row['icon'] ?? 'star',
                    'color' => $row['color'] ?? '#009ada',
                    'nominees' => []
                ];
            }
            if ($row['nominee_id'] && count($leaderboard_by_category[$cat_id]['nominees']) < 4) {
                $leaderboard_by_category[$cat_id]['nominees'][] = $row;
            }
        }

        // Get participation metrics for selected quarter
        $participation = DB::selectOne('
    SELECT
        COUNT(DISTINCT nominator_id) as unique_nominators,
        COUNT(DISTINCT nominee_id) as unique_nominees,
        (SELECT COUNT(*) FROM employees WHERE status = 1) as total_employees
    FROM nominations
    WHERE quarter = ? AND year = ?
', [$selected_quarter, $selected_year]);

        // Get recent activity for timeline (filtered by selected quarter)
        $recent_activity = DB::select('
    SELECT
        n.created_at,
        nom.full_name as nominator_name,
        nomi.full_name as nominee_name,
        cat.name as category_name
    FROM nominations n
    JOIN employees nom ON n.nominator_id = nom.id
    JOIN employees nomi ON n.nominee_id = nomi.id
    JOIN nomination_categories cat ON n.category_id = cat.id
    WHERE n.quarter = ? AND n.year = ?
    ORDER BY n.created_at DESC
    LIMIT 10
', [$selected_quarter, $selected_year]);

        return view('admin.nomination-dashboard', compact(
            'available_quarters', 'selected_quarter', 'selected_year', 'quarter_names', 'category_filter',
            'status_filter', 'read_filter', 'nominations', 'categories', 'stats', 'leaderboard_by_category',
            'participation', 'recent_activity'
        ));
    }

    /** admin/nomination-dashboard?export_excel=1 */
    private function dashboardExport($export_quarter, $export_year, array $quarter_names)
    {
        // Get all nominations for selected quarter with complete details
        $export_result = DB::select('
        SELECT
            n.id AS nomination_id,
            n.created_at AS nomination_date,
            n.quarter,
            n.year,
            n.status,
            n.is_winner,
            n.admin_notes,
            n.level1_decision,
            n.level1_notes,
            n.level2_decision,
            n.level2_notes,
            n.level3_decision,
            n.level3_notes,
            cat.id AS category_id,
            cat.name AS category_name,
            cat.description AS category_description,
            nom.id AS nominator_id,
            nom.employee_code AS nominator_code,
            nom.full_name AS nominator_name,
            nom.department AS nominator_department,
            nom.position AS nominator_position,
            nom.email AS nominator_email,
            nom.phone AS nominator_phone,
            nomi.id AS nominee_id,
            nomi.employee_code AS nominee_code,
            nomi.full_name AS nominee_name,
            nomi.department AS nominee_department,
            nomi.position AS nominee_position,
            nomi.email AS nominee_email,
            nomi.phone AS nominee_phone
        FROM nominations n
        JOIN nomination_categories cat ON n.category_id = cat.id
        JOIN employees nom ON n.nominator_id = nom.id
        JOIN employees nomi ON n.nominee_id = nomi.id
        WHERE n.quarter = ? AND n.year = ?
        ORDER BY n.created_at DESC
    ', [$export_quarter, $export_year]);

        // Get all questions for dynamic columns
        $all_questions = [];
        foreach (DB::select('
        SELECT DISTINCT q.id, q.question_text, q.question_type, q.category_id
        FROM nomination_questions q
        WHERE q.status = 1
        ORDER BY q.category_id, q.sort_order
    ') as $q) {
            $all_questions[$q['id']] = $q;
        }

        // Get answers for all nominations
        $answers_by_nomination = [];
        foreach (DB::select('
        SELECT na.nomination_id, na.question_id, na.answer
        FROM nomination_answers na
        WHERE na.nomination_id IN (SELECT id FROM nominations WHERE quarter = ? AND year = ?)
    ', [$export_quarter, $export_year]) as $ans) {
            $answers_by_nomination[$ans['nomination_id']][$ans['question_id']] = $ans['answer'];
        }

        // Prepare data for export
        $export_data = [];
        foreach ($export_result as $row) {
            $row_data = [
                'Nomination ID' => $row['nomination_id'] ?? '',
                'Nomination Date' => !empty($row['nomination_date']) ? date('Y-m-d H:i:s', strtotime($row['nomination_date'])) : '',
                'Quarter' => $quarter_names[$row['quarter']] ?? '',
                'Year' => $row['year'] ?? '',
                'Status' => ucfirst($row['status'] ?? ''),
                'Is Winner' => ($row['is_winner'] ?? 0) ? 'Yes' : 'No',
                'Category ID' => $row['category_id'] ?? '',
                'Category Name' => $row['category_name'] ?? '',
                'Category Description' => $row['category_description'] ?? '',
                'Nominator ID' => $row['nominator_id'] ?? '',
                'Nominator Code' => $row['nominator_code'] ?? '',
                'Nominator Name' => $row['nominator_name'] ?? '',
                'Nominator Department' => $row['nominator_department'] ?? '',
                'Nominator Position' => $row['nominator_position'] ?? '',
                'Nominator Email' => $row['nominator_email'] ?? '',
                'Nominator Phone' => $row['nominator_phone'] ?? '',
                'Nominee ID' => $row['nominee_id'] ?? '',
                'Nominee Code' => $row['nominee_code'] ?? '',
                'Nominee Name' => $row['nominee_name'] ?? '',
                'Nominee Department' => $row['nominee_department'] ?? '',
                'Nominee Position' => $row['nominee_position'] ?? '',
                'Nominee Email' => $row['nominee_email'] ?? '',
                'Nominee Phone' => $row['nominee_phone'] ?? '',
                'Level 1 Decision' => ucfirst($row['level1_decision'] ?? 'Pending'),
                'Level 1 Notes' => $row['level1_notes'] ?? '',
                'Level 2 Decision' => ucfirst($row['level2_decision'] ?? 'Pending'),
                'Level 2 Notes' => $row['level2_notes'] ?? '',
                'Level 3 Decision' => ucfirst($row['level3_decision'] ?? 'Pending'),
                'Level 3 Notes' => $row['level3_notes'] ?? '',
                'Admin Notes' => $row['admin_notes'] ?? '',
            ];

            // Add question answers as columns
            foreach ($all_questions as $qid => $question) {
                $col_name = 'Q_'.$qid.': '.substr($question['question_text'], 0, 60);
                $answer = $answers_by_nomination[$row['nomination_id']][$qid] ?? '';
                // Clean up answer - remove file paths if it's an attachment
                if ($question['question_type'] == 'attachment' && strpos($answer, 'uploads/') !== false) {
                    $answer = '[File Uploaded: '.basename($answer).']';
                }
                $row_data[$col_name] = $answer;
            }

            $export_data[] = $row_data;
        }

        // Output data as HTML table (Excel can read this)
        $o = '<table border="1">';
        if (!empty($export_data)) {
            $o .= '<tr>';
            foreach (array_keys($export_data[0]) as $header) {
                $o .= '<th style="background-color: #009ada; color: white; padding: 8px;">'.htmlspecialchars($header ?? '').'</th>';
            }
            $o .= '</tr>';

            // Data rows
            foreach ($export_data as $row) {
                $o .= '<tr>';
                foreach ($row as $value) {
                    $o .= '<td style="padding: 6px;">'.htmlspecialchars($value ?? '').'</td>';
                }
                $o .= '</tr>';
            }
        } else {
            $o .= '<tr><td>No nominations found for '.($quarter_names[$export_quarter] ?? null).' '.$export_year.'</td></tr>';
        }
        $o .= '</table>';

        return response($o, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="nomination_quarter_report_'.$export_quarter.'_'.$export_year.'.xls"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /** admin/nominee-votes */
    public function nomineeVotes(Request $request)
    {
        // Check if admin is logged in
        if ($this->adminMissing()) {
            kc_redirect('../admin/login');
        }

        // Get current quarter/year from settings
        [$current_quarter, $current_year] = $this->currentQuarter();

        $quarter_names = [
            1 => 'Q1 (January - March)',
            2 => 'Q2 (April - June)',
            3 => 'Q3 (July - September)',
            4 => 'Q4 (October - December)'
        ];

        // Get category filter
        $category_filter = $this->qint($request, 'category');

        // Get all categories for filter
        $categories = DB::select('SELECT * FROM nomination_categories WHERE status = 1 ORDER BY name');

        // Build query to get votes per nominee
        $category_where = $category_filter > 0 ? "AND n.category_id = $category_filter" : '';

        $votes_result = DB::select("
    SELECT
        e.id,
        e.employee_code,
        e.full_name,
        e.department,
        e.position,
        e.photo,
        COUNT(DISTINCT n.id) as total_votes,
        COUNT(DISTINCT n.nominator_id) as unique_nominators,
        GROUP_CONCAT(DISTINCT cat.name ORDER BY cat.name SEPARATOR ', ') as categories_nominated,
        MAX(n.created_at) as last_nominated
    FROM employees e
    INNER JOIN nominations n ON e.id = n.nominee_id
    INNER JOIN nomination_categories cat ON n.category_id = cat.id
    WHERE n.quarter = ?
        AND n.year = ?
        AND n.status != 'rejected'
        $category_where
    GROUP BY e.id, e.employee_code, e.full_name, e.department, e.position, e.photo
    ORDER BY total_votes DESC, e.full_name ASC
", [$current_quarter, $current_year]);

        // Get summary statistics
        $stats = DB::selectOne("
    SELECT
        COUNT(DISTINCT n.nominee_id) as total_nominees,
        COUNT(DISTINCT n.nominator_id) as total_nominators,
        COUNT(*) as total_nominations,
        COUNT(DISTINCT n.category_id) as categories_with_nominations
    FROM nominations n
    WHERE n.quarter = ?
        AND n.year = ?
        AND n.status != 'rejected'
", [$current_quarter, $current_year]);

        // Get category breakdown for this quarter
        $cat_breakdown = DB::select("
    SELECT
        cat.name,
        cat.color,
        COUNT(n.id) as nomination_count,
        COUNT(DISTINCT n.nominee_id) as nominee_count
    FROM nomination_categories cat
    LEFT JOIN nominations n ON cat.id = n.category_id
        AND n.quarter = ?
        AND n.year = ?
        AND n.status != 'rejected'
    WHERE cat.status = 1
    GROUP BY cat.id
    ORDER BY nomination_count DESC
", [$current_quarter, $current_year]);

        return view('admin.nominee-votes', compact(
            'current_quarter', 'current_year', 'quarter_names', 'category_filter', 'categories',
            'votes_result', 'stats', 'cat_breakdown'
        ));
    }

    /** admin/view-nomination */
    public function viewNomination(Request $request)
    {
        if ($this->adminMissing()) {
            kc_redirect('../admin/login');
        }

        $id = $this->qint($request, 'id');
        if (!$id) {
            kc_redirect('nomination-dashboard');
        }

        // Get nomination details
        $nom = DB::selectOne('
    SELECT n.*,
           cat.name as category_name, cat.description as category_desc, cat.color, cat.icon,
           nom.employee_code as nominator_code, nom.full_name as nominator_name, nom.department as nominator_dept, nom.position as nominator_position, nom.photo as nominator_photo,
           nomi.employee_code as nominee_code, nomi.full_name as nominee_name, nomi.department as nominee_dept, nomi.position as nominee_position, nomi.photo as nominee_photo
    FROM nominations n
    JOIN nomination_categories cat ON n.category_id = cat.id
    JOIN employees nom ON n.nominator_id = nom.id
    JOIN employees nomi ON n.nominee_id = nomi.id
    WHERE n.id = ?
', [$id]);
        if (!$nom) {
            kc_redirect('nomination-dashboard');
        }

        // Get answers
        $answers = DB::select('
    SELECT q.question_text, q.question_type, q.options, a.answer
    FROM nomination_answers a
    JOIN nomination_questions q ON a.question_id = q.id
    WHERE a.nomination_id = ?
    ORDER BY q.sort_order
', [$id]);

        // Mark as read if not already
        if (!$nom['is_read']) {
            $admin_id = session('admin_id') ?? 0;
            DB::update('UPDATE nominations SET is_read = 1, read_at = NOW(), read_by = ? WHERE id = ?', [$admin_id, $id]);
        }

        // Handle status update
        if ($request->isMethod('post') && $request->post('update_status') !== null) {
            $status = sanitize($request->post('status'));
            $admin_notes = sanitize($request->post('admin_notes') ?? '');
            if (!in_array($status, self::STATUSES, true)) {
                NominationsLegacy::fail("view-nomination?id=$id", 'Invalid nomination status.');
            }
            try {
                DB::update('UPDATE nominations SET status = ?, admin_notes = ? WHERE id = ?', [$status, $admin_notes, $id]);
            } catch (QueryException $e) {
                NominationsLegacy::fail("view-nomination?id=$id", 'The status could not be updated (database error).', $e);
            }
            kc_redirect("view-nomination?id=$id&msg=updated");
        }

        return view('admin.view-nomination', compact('nom', 'answers'));
    }

    /** admin/manage-categories */
    public function manageCategories(Request $request)
    {
        if ($this->adminMissing()) {
            kc_redirect('../admin/login');
        }

        // Handle Add/Edit/Delete
        if ($request->isMethod('post')) {
            if ($request->post('action') !== null) {
                $action = $request->post('action');

                try {
                    if ($action === 'add') {
                        DB::insert('INSERT INTO nomination_categories (name, description, icon, color, sort_order, is_self_nomination) VALUES (?, ?, ?, ?, ?, ?)', [
                            sanitize($request->post('name')),
                            sanitize($request->post('description')),
                            sanitize($request->post('icon')),
                            sanitize($request->post('color')),
                            (int) $request->post('sort_order'),
                            $request->post('is_self_nomination') !== null ? 1 : 0,
                        ]);
                    } elseif ($action === 'edit') {
                        DB::update('UPDATE nomination_categories SET name=?, description=?, icon=?, color=?, sort_order=?, is_self_nomination=? WHERE id=?', [
                            sanitize($request->post('name')),
                            sanitize($request->post('description')),
                            sanitize($request->post('icon')),
                            sanitize($request->post('color')),
                            (int) $request->post('sort_order'),
                            $request->post('is_self_nomination') !== null ? 1 : 0,
                            (int) $request->post('id'),
                        ]);
                    } elseif ($action === 'delete') {
                        DB::delete('DELETE FROM nomination_categories WHERE id = ?', [(int) $request->post('id')]);
                    } elseif ($action === 'toggle_status') {
                        DB::update('UPDATE nomination_categories SET status = ? WHERE id = ?', [(int) $request->post('status'), (int) $request->post('id')]);
                    }
                } catch (QueryException $e) {
                    NominationsLegacy::fail('manage-categories', $action === 'delete'
                        ? 'This category could not be deleted: it still has nominations or questions linked to it. Deactivate it instead.'
                        : 'The category could not be saved (database error).', $e);
                }
                kc_redirect('manage-categories?msg=success');
            }
        }

        $categories = DB::select('SELECT * FROM nomination_categories ORDER BY sort_order');

        return view('admin.manage-categories', compact('categories'));
    }

    /** admin/manage-questions */
    public function manageQuestions(Request $request)
    {
        if ($this->adminMissing()) {
            kc_redirect('../admin/login');
        }

        // Get all categories for dropdown
        $categories = DB::select('SELECT * FROM nomination_categories WHERE status = 1 ORDER BY name');

        // Handle Add/Edit/Delete/Reorder
        if ($request->isMethod('post')) {
            if ($request->post('action') !== null) {
                $action = $request->post('action');

                try {
                    if ($action === 'add' || $action === 'edit') {
                        $category_id = (int) $request->post('category_id');
                        $question_text = $request->post('question_text');
                        $question_type = sanitize($request->post('question_type'));
                        $options = $request->post('options') !== null ? sanitize($request->post('options')) : null;
                        $is_required = $request->post('is_required') !== null ? 1 : 0;
                        $sort_order = (int) $request->post('sort_order');

                        if ($action === 'add') {
                            DB::insert('INSERT INTO nomination_questions (category_id, question_text, question_type, options, is_required, sort_order) VALUES (?, ?, ?, ?, ?, ?)', [$category_id, $question_text, $question_type, $options, $is_required, $sort_order]);
                        } else {
                            DB::update('UPDATE nomination_questions SET category_id=?, question_text=?, question_type=?, options=?, is_required=?, sort_order=? WHERE id=?', [$category_id, $question_text, $question_type, $options, $is_required, $sort_order, (int) $request->post('id')]);
                        }
                    } elseif ($action === 'delete') {
                        DB::delete('DELETE FROM nomination_questions WHERE id = ?', [(int) $request->post('id')]);
                    } elseif ($action === 'reorder') {
                        $order = json_decode((string) $request->post('order'), true);
                        // foreach over a non-array only printed a warning
                        foreach (is_array($order) ? $order : [] as $index => $id) {
                            $id = (int) $id;
                            $sort_order = $index + 1;
                            DB::update('UPDATE nomination_questions SET sort_order = ? WHERE id = ?', [$sort_order, $id]);
                        }

                        return kc_exit(json_encode(['success' => true]));
                    }
                } catch (QueryException $e) {
                    if (($action ?? '') === 'reorder') {
                        NominationsLegacy::failJson('The new order could not be saved.', $e);
                    }
                    NominationsLegacy::fail('manage-questions', $action === 'delete'
                        ? 'This question could not be deleted: it already has answers. Deactivate it instead.'
                        : 'The question could not be saved (database error).', $e);
                }
                kc_redirect('manage-questions?msg=success');
            }
        }

        // Get filter
        $category_filter = $this->qint($request, 'category');
        $where = $category_filter > 0 ? "WHERE q.category_id = $category_filter" : '';
        $questions = DB::select("
    SELECT q.*, c.name as category_name, c.color
    FROM nomination_questions q
    JOIN nomination_categories c ON q.category_id = c.id
    $where
    ORDER BY q.category_id, q.sort_order
");

        return view('admin.manage-questions', compact('categories', 'category_filter', 'questions'));
    }

    /** admin/manage-quarters */
    public function manageQuarters(Request $request)
    {
        if ($this->adminMissing()) {
            kc_redirect('login');
        }

        // Handle quarter update
        if ($request->isMethod('post')) {
            try {
                if ($request->post('update_quarter') !== null) {
                    $quarter = (int) $request->post('quarter');
                    $year = (int) $request->post('year');
                    $cooldown_days = (int) $request->post('cooldown_days');
                    $max_sql = !empty($request->post('max_nominations')) ? (int) $request->post('max_nominations') : 'NULL';
                    $unique_votes = $request->post('unique_votes') !== null ? 1 : 0;

                    DB::update("UPDATE nomination_settings SET
            current_quarter = $quarter,
            current_year = $year,
            cooldown_days = $cooldown_days,
            max_nominations_per_quarter = $max_sql,
            unique_votes_per_quarter = $unique_votes
            WHERE status = 1");

                    session(['message' => 'Quarter settings updated successfully!', 'message_type' => 'success']);
                    kc_redirect('manage-quarters');
                }

                // Handle announcing winners (supports up to 2 winners per category)
                if ($request->post('announce_winners') !== null) {
                    $category_id = (int) $request->post('category_id');
                    $quarter = (int) $request->post('quarter');
                    $year = (int) $request->post('year');
                    $winner_id = (int) $request->post('winner_id');
                    $runner_up_id = !empty($request->post('runner_up_id')) ? (int) $request->post('runner_up_id') : 0;

                    // Clear existing winners for this category/quarter
                    DB::delete('DELETE FROM quarter_winners WHERE category_id = ? AND quarter = ? AND year = ?', [$category_id, $quarter, $year]);

                    // Clear is_winner flag from nominations
                    DB::update('UPDATE nominations SET is_winner = 0 WHERE category_id = ? AND quarter = ? AND year = ?', [$category_id, $quarter, $year]);

                    $winners_announced = 0;

                    // Process Winner (Rank 1)
                    if ($winner_id > 0) {
                        $winner_notes = (string) ($request->post('winner_notes') ?? '');
                        $nom_data = DB::selectOne('SELECT nominee_id FROM nominations WHERE id = ?', [$winner_id]);
                        if ($nom_data) {
                            DB::insert('INSERT INTO quarter_winners (category_id, nomination_id, nominee_id, quarter, year, winner_rank, announced_at)
                                 VALUES (?, ?, ?, ?, ?, 1, NOW())', [$category_id, $winner_id, $nom_data['nominee_id'], $quarter, $year]);
                            DB::update("UPDATE nominations SET is_winner = 1, winner_announced_at = NOW(), admin_notes = CONCAT(IFNULL(admin_notes, ''), ?) WHERE id = ?", ["\n[WINNER ANNOUNCEMENT] $winner_notes", $winner_id]);
                            $winners_announced++;
                        }
                    }

                    // Process Runner Up (Rank 2)
                    if ($runner_up_id > 0 && $runner_up_id != $winner_id) {
                        $runner_up_notes = (string) ($request->post('runner_up_notes') ?? '');
                        $nom_data = DB::selectOne('SELECT nominee_id FROM nominations WHERE id = ?', [$runner_up_id]);
                        if ($nom_data) {
                            DB::insert('INSERT INTO quarter_winners (category_id, nomination_id, nominee_id, quarter, year, winner_rank, announced_at)
                                 VALUES (?, ?, ?, ?, ?, 2, NOW())', [$category_id, $runner_up_id, $nom_data['nominee_id'], $quarter, $year]);
                            DB::update("UPDATE nominations SET admin_notes = CONCAT(IFNULL(admin_notes, ''), ?) WHERE id = ?", ["\n[RUNNER UP ANNOUNCEMENT] $runner_up_notes", $runner_up_id]);
                            $winners_announced++;
                        }
                    }

                    $cat = DB::selectOne('SELECT name FROM nomination_categories WHERE id = ?', [$category_id]);
                    session([
                        'message' => "$winners_announced winner(s) announced successfully for ".($cat['name'] ?? 'Category')." - Q$quarter $year!",
                        'message_type' => 'success',
                    ]);
                    kc_redirect('manage-quarters');
                }

                if ($request->post('reset_quarter_tracking') !== null) {
                    $quarter = (int) $request->post('reset_quarter');
                    $year = (int) $request->post('reset_year');

                    // Clear tracking for this quarter to allow new nominations
                    DB::delete('DELETE FROM nomination_user_tracking WHERE quarter = ? AND year = ?', [$quarter, $year]);

                    session(['message' => 'Quarter tracking has been reset! Users can now submit new nominations.', 'message_type' => 'success']);
                    kc_redirect('manage-quarters');
                }

                if ($request->post('remove_winner') !== null) {
                    $winner_id = (int) $request->post('winner_id');
                    $winner_info = DB::selectOne('SELECT * FROM quarter_winners WHERE id = ?', [$winner_id]);

                    DB::delete('DELETE FROM quarter_winners WHERE id = ?', [$winner_id]);

                    // Update nomination - remove winner flag if it was a winner (rank 1)
                    if ($winner_info && $winner_info['winner_rank'] == 1) {
                        DB::update('UPDATE nominations SET is_winner = 0 WHERE id = ?', [$winner_info['nomination_id']]);
                    }

                    session(['message' => 'Winner removed successfully!', 'message_type' => 'success']);
                    kc_redirect('manage-quarters');
                }
            } catch (QueryException $e) {
                NominationsLegacy::fail('manage-quarters', 'The change could not be saved (database error).', $e, true);
            }
        }

        // Get current settings
        $settings = DB::selectOne('SELECT * FROM nomination_settings WHERE status = 1 ORDER BY id DESC LIMIT 1');

        // Get quarter winners (with rank)
        $winners = DB::select('
    SELECT qw.id as winner_record_id, qw.category_id, qw.nomination_id, qw.nominee_id,
           qw.quarter as winner_quarter, qw.year as winner_year, qw.winner_rank, qw.announced_at,
           c.name as category_name, c.color as category_color,
           e.id as nominee_id, e.full_name as nominee_name, e.employee_code, e.photo,
           n.status as nomination_status
    FROM quarter_winners qw
    JOIN nomination_categories c ON qw.category_id = c.id
    JOIN employees e ON qw.nominee_id = e.id
    LEFT JOIN nominations n ON qw.nomination_id = n.id
    ORDER BY qw.year DESC, qw.quarter DESC, qw.category_id, qw.winner_rank ASC
');

        // Group winners by category and quarter for display
        $grouped_winners = [];
        foreach ($winners as $winner) {
            $key = $winner['category_id'].'_'.$winner['winner_quarter'].'_'.$winner['winner_year'];
            if (!isset($grouped_winners[$key])) {
                $grouped_winners[$key] = [
                    'category_id' => $winner['category_id'],
                    'category_name' => $winner['category_name'],
                    'category_color' => $winner['category_color'],
                    'quarter' => $winner['winner_quarter'],
                    'year' => $winner['winner_year'],
                    'winners' => []
                ];
            }
            $grouped_winners[$key]['winners'][] = $winner;
        }

        // Get nomination stats by quarter
        $stats = DB::select('
    SELECT quarter, year, COUNT(*) as total,
           COUNT(DISTINCT nominator_id) as nominators,
           COUNT(DISTINCT nominee_id) as nominees
    FROM nominations
    GROUP BY quarter, year
    ORDER BY year DESC, quarter DESC
');

        // Get categories with approved nominations for winner selection
        $categories_with_nominations = DB::select("
    SELECT DISTINCT c.id, c.name, c.color, c.icon
    FROM nomination_categories c
    JOIN nominations n ON c.id = n.category_id
    WHERE c.status = 1 AND n.status = 'approved'
    ORDER BY c.name
");

        // Get selected values for dropdown preview
        $selected_category = $this->qint($request, 'category');
        $selected_quarter = $this->qint($request, 'quarter');
        $selected_year = $this->qint($request, 'year', date('Y'));

        // Get available nominations based on selection
        $available_nominations = [];
        if ($selected_category > 0 && $selected_quarter > 0 && $selected_year > 0) {
            $available_nominations = DB::select("
        SELECT DISTINCT n.id,
               e.id as nominee_id, e.full_name as nominee_name, e.employee_code, e.photo, e.department
        FROM nominations n
        JOIN employees e ON n.nominee_id = e.id
        WHERE n.category_id = ?
        AND n.quarter = ?
        AND n.year = ?
        AND n.status = 'approved'
        ORDER BY e.full_name ASC
    ", [$selected_category, $selected_quarter, $selected_year]);
        }

        return view('admin.manage-quarters', compact(
            'settings', 'grouped_winners', 'stats', 'categories_with_nominations', 'selected_category',
            'selected_quarter', 'selected_year', 'available_nominations'
        ));
    }

    /** admin/manage-committee */
    public function manageCommittee(Request $request)
    {
        if ($this->adminMissing()) {
            kc_redirect('../admin/login');
        }

        // Handle actions
        if ($request->isMethod('post')) {
            if ($request->post('action') !== null) {
                $action = $request->post('action');

                try {
                    if ($action === 'add') {
                        DB::insert('INSERT INTO nomination_committee (employee_id, committee_level, role, sort_order) VALUES (?, ?, ?, ?)', [
                            (int) $request->post('employee_id'),
                            (int) $request->post('committee_level'),
                            sanitize($request->post('role')),
                            (int) $request->post('sort_order'),
                        ]);
                    } elseif ($action === 'edit') {
                        DB::update('UPDATE nomination_committee SET employee_id=?, committee_level=?, role=?, sort_order=? WHERE id=?', [
                            (int) $request->post('employee_id'),
                            (int) $request->post('committee_level'),
                            sanitize($request->post('role')),
                            (int) $request->post('sort_order'),
                            (int) $request->post('id'),
                        ]);
                    } elseif ($action === 'delete') {
                        DB::delete('DELETE FROM nomination_committee WHERE id = ?', [(int) $request->post('id')]);
                    } elseif ($action === 'toggle_status') {
                        DB::update('UPDATE nomination_committee SET status = ? WHERE id = ?', [(int) $request->post('status'), (int) $request->post('id')]);
                    }
                } catch (QueryException $e) {
                    NominationsLegacy::fail('manage-committee', 'The committee member could not be saved (database error).', $e);
                }
                kc_redirect('manage-committee?msg=success');
            }
        }

        // Get committee members with employee details
        $levelQuery = '
    SELECT c.*, e.employee_code, e.full_name, e.department, e.position, e.photo, e.email
    FROM nomination_committee c
    JOIN employees e ON c.employee_id = e.id
    WHERE c.committee_level = ?
    ORDER BY c.sort_order, e.full_name
';
        $level1 = DB::select($levelQuery, [1]);
        $level2 = DB::select($levelQuery, [2]);
        $level3 = DB::select($levelQuery, [3]);

        // Get all employees for dropdown
        $employees = DB::select('SELECT id, employee_code, full_name, department, position FROM employees WHERE status = 1 ORDER BY full_name');

        return view('admin.manage-committee', compact('level1', 'level2', 'level3', 'employees'));
    }

    /** admin/get-category-nominations */
    public function getCategoryNominations(Request $request)
    {
        // Check if admin is logged in
        if ($this->adminMissing()) {
            return kc_json(['error' => 'Unauthorized'], 401);
        }

        $category_id = $this->qint($request, 'category_id');
        $quarter = $this->qint($request, 'quarter');
        $year = $this->qint($request, 'year');

        // Validate inputs
        if ($category_id == 0 || $quarter == 0 || $year == 0) {
            return kc_json(['error' => 'Missing required parameters']);
        }

        // Get approved nominations for the selected category, quarter, and year
        // (nominations has no "subject" column; the old query selected one and always failed)
        try {
            $result = DB::select("
    SELECT DISTINCT
        n.id,
        e.id as nominee_id,
        e.full_name as nominee_name,
        e.employee_code,
        e.photo,
        e.department
    FROM nominations n
    INNER JOIN employees e ON n.nominee_id = e.id
    WHERE n.category_id = ?
        AND n.quarter = ?
        AND n.year = ?
        AND n.status = 'approved'
    ORDER BY e.full_name ASC
", [$category_id, $quarter, $year]);
        } catch (QueryException $e) {
            NominationsLegacy::failJson('Could not load the nominations.', $e);
        }

        $nominations = [];
        foreach ($result as $row) {
            $nominations[] = [
                'id' => $row['id'],
                'nominee_id' => $row['nominee_id'],
                'nominee_name' => $row['nominee_name'],
                'employee_code' => $row['employee_code'],
                'department' => $row['department'] ?? 'N/A',
                'subject' => '',
                'display_text' => $row['nominee_name'].' ('.$row['employee_code'].')'
            ];
        }

        return kc_json($nominations);
    }

    /** admin/sync_nomination_tracking */
    public function syncNominationTracking(Request $request)
    {
        if ($this->adminMissing()) {
            kc_exit('Access denied', 403);
        }

        // Get current quarter settings
        $settings = DB::selectOne('SELECT current_quarter, current_year, unique_votes_per_quarter FROM nomination_settings WHERE status = 1 ORDER BY id DESC LIMIT 1');

        if (($settings['unique_votes_per_quarter'] ?? null) != 1) {
            kc_exit('Unique votes is not enabled. Please enable it first in Quarter Management.');
        }

        $quarter = $settings['current_quarter'];
        $year = $settings['current_year'];

        // Sync existing nominations to tracking table
        try {
            $affected = DB::affectingStatement("
    INSERT IGNORE INTO nomination_user_tracking (user_id, category_id, quarter, year, nomination_id)
    SELECT DISTINCT nominator_id, category_id, quarter, year, id
    FROM nominations
    WHERE quarter = ? AND year = ? AND status != 'rejected'
", [$quarter, $year]);
        } catch (QueryException $e) {
            report($e);

            return response('Sync failed: database error. The details were written to the application log.', 500);
        }

        return response("Successfully synced $affected existing nominations to tracking table.");
    }
}

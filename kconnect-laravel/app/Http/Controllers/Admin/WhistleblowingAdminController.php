<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Whistleblowing reports admin (admin/admin-whistleblowing.php, admin/whistleblowing-api.php). */
class WhistleblowingAdminController extends Controller
{
    /**
     * Filter values from the query string. 'raw' are bound into the SQL; 'esc' are the
     * mysqli-escaped strings the old page echoed back (export header, filter form).
     */
    private function filters(Request $request): array
    {
        $raw = [];
        $esc = [];
        foreach (['status', 'type', 'entity', 'search', 'date_from', 'date_to', 'urgent'] as $k) {
            $v = $request->query($k);
            $raw[$k] = $v !== null ? (string) $v : '';
            $esc[$k] = $v !== null ? kc_escape($v) : '';
        }

        $where = [];
        $bindings = [];
        if ($esc['status']) {
            $where[] = 'status = ?';
            $bindings[] = $raw['status'];
        }
        if ($esc['type']) {
            $where[] = 'submission_type = ?';
            $bindings[] = $raw['type'];
        }
        if ($esc['entity']) {
            $where[] = 'entity = ?';
            $bindings[] = $raw['entity'];
        }
        if ($esc['date_from']) {
            $where[] = 'DATE(created_at) >= ?';
            $bindings[] = $raw['date_from'];
        }
        if ($esc['date_to']) {
            $where[] = 'DATE(created_at) <= ?';
            $bindings[] = $raw['date_to'];
        }
        if ($esc['urgent']) {
            $where[] = 'urgent = ?';
            $bindings[] = $raw['urgent'];
        }
        if ($esc['search']) {
            $where[] = '(reference_number LIKE ? OR full_name LIKE ? OR what_happened LIKE ? OR department LIKE ?)';
            $like = '%'.$raw['search'].'%';
            array_push($bindings, $like, $like, $like, $like);
        }
        $where_sql = ! empty($where) ? 'WHERE '.implode(' AND ', $where) : '';

        return [$esc, $where_sql, $bindings];
    }

    /** admin/admin-whistleblowing.php */
    public function index(Request $request)
    {
        if (! session()->has('admin_id') && ! session()->has('admin_username')) {
            kc_redirect('login.php');
        }

        // Handle status update
        if ($request->post('update_status') !== null && $request->post('report_id') !== null && $request->post('status') !== null) {
            $report_id = intval($request->post('report_id'));
            $status = (string) $request->post('status');
            $admin_notes = $request->post('admin_notes') !== null ? (string) $request->post('admin_notes') : '';
            $resolution_notes = $request->post('resolution_notes') !== null ? (string) $request->post('resolution_notes') : '';

            $now = date('Y-m-d H:i:s');

            try {
                DB::update("UPDATE whistleblowing_reports SET 
                  status = ?, 
                  admin_notes = CONCAT(IFNULL(admin_notes, ''), ?, ?),
                  resolution_notes = CONCAT(IFNULL(resolution_notes, ''), ?, ?),
                  updated_at = NOW() 
                  WHERE id = $report_id", [$status, "\n$now - ", $admin_notes, "\n$now - ", $resolution_notes]);
                $success = 'Report status updated successfully!';
            } catch (\Illuminate\Database\QueryException $e) {
                $error = 'Error updating report: '.$e->getMessage();
            }
        }

        // Handle Excel Export with filters
        if ($request->query('export_excel') !== null && $request->query('export_excel') == 1) {
            [$f, $where_sql, $bindings] = $this->filters($request);
            $rows = DB::select("SELECT * FROM whistleblowing_reports $where_sql ORDER BY created_at DESC", $bindings);

            $filename = 'Whistleblowing_Reports_'.date('Y-m-d').'.xls';

            $out = '<html><head><meta charset="UTF-8"><title>Whistleblowing Reports</title>';
            $out .= '<style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h2 { color: #009ada; }
        table { border-collapse: collapse; width: 100%; }
        th { background: #009ada; color: white; padding: 6px 8px; text-align: left; }
        td { padding: 5px 8px; border: 1px solid #ddd; }
        .header-info { margin-bottom: 15px; padding: 10px; background: #f5f5f5; }
        .status-new { color: #dc2626; font-weight: bold; }
        .status-under_review { color: #d97706; font-weight: bold; }
        .status-investigating { color: #2563eb; font-weight: bold; }
        .status-resolved { color: #16a34a; font-weight: bold; }
        .status-closed { color: #6b7280; }
    </style>';
            $out .= '</head><body>';

            $out .= '<h2>📋 Whistleblowing Reports</h2>';
            $out .= '<div class="header-info">';
            $out .= '<p><strong>Generated:</strong> '.date('Y-m-d H:i:s').'</p>';
            $out .= '<p><strong>Total Records:</strong> '.count($rows).'</p>';
            if ($f['date_from']) {
                $out .= '<p><strong>From:</strong> '.$f['date_from'].'</p>';
            }
            if ($f['date_to']) {
                $out .= '<p><strong>To:</strong> '.$f['date_to'].'</p>';
            }
            if ($f['status']) {
                $out .= '<p><strong>Status:</strong> '.$f['status'].'</p>';
            }
            if ($f['type']) {
                $out .= '<p><strong>Type:</strong> '.$f['type'].'</p>';
            }
            if ($f['entity']) {
                $out .= '<p><strong>Entity:</strong> '.$f['entity'].'</p>';
            }
            $out .= '</div>';

            $out .= '<table>';
            $out .= '<thead><tr>
            <th>#</th>
            <th>Reference</th>
            <th>Type</th>
            <th>Status</th>
            <th>Full Name</th>
            <th>Entity</th>
            <th>Department</th>
            <th>Contact</th>
            <th>Relationship</th>
            <th>Concerns</th>
            <th>What Happened</th>
            <th>Who Involved</th>
            <th>Incident Type</th>
            <th>Incident Date</th>
            <th>Location</th>
            <th>Urgent</th>
            <th>Prior Reported</th>
            <th>Created</th>
          </tr></thead><tbody>';

            $counter = 1;
            foreach ($rows as $row) {
                $out .= '<tr>';
                $out .= '<td>'.$counter++.'</td>';
                $out .= '<td>'.$row['reference_number'].'</td>';
                $out .= '<td>'.$row['submission_type'].'</td>';
                $out .= '<td class="status-'.$row['status'].'">'.str_replace('_', ' ', (string) $row['status']).'</td>';
                $out .= '<td>'.htmlspecialchars($row['full_name'] ?? 'Anonymous').'</td>';
                $out .= '<td>'.htmlspecialchars($row['entity'] ?? 'N/A').'</td>';
                $out .= '<td>'.htmlspecialchars($row['department'] ?? 'N/A').'</td>';
                $out .= '<td>'.htmlspecialchars($row['contact_method'] ?? 'N/A').'</td>';
                $out .= '<td>'.$row['relationship'].'</td>';
                $out .= '<td>'.htmlspecialchars($row['nature_concerns'] ?? 'N/A').'</td>';
                $out .= '<td>'.htmlspecialchars(substr((string) $row['what_happened'], 0, 100)).(strlen((string) $row['what_happened']) > 100 ? '...' : '').'</td>';
                $out .= '<td>'.htmlspecialchars($row['who_involved'] ?? 'N/A').'</td>';
                $out .= '<td>'.$row['incident_type'].'</td>';
                $out .= '<td>'.($row['incident_date'] ?? $row['incident_from_date'] ?? 'N/A').'</td>';
                $out .= '<td>'.htmlspecialchars($row['incident_location'] ?? 'N/A').'</td>';
                $out .= '<td>'.($row['urgent'] ? 'Yes' : 'No').'</td>';
                $out .= '<td>'.($row['prior_reported'] ? 'Yes' : 'No').'</td>';
                $out .= '<td>'.date('Y-m-d H:i', strtotime((string) $row['created_at'])).'</td>';
                $out .= '</tr>';
            }

            $out .= '</tbody></table></body></html>';

            return response($out, 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        }

        [$f, $where_sql, $bindings] = $this->filters($request);
        $status_filter = $f['status'];
        $type_filter = $f['type'];
        $entity_filter = $f['entity'];
        $search = $f['search'];
        $date_from = $f['date_from'];
        $date_to = $f['date_to'];
        $urgent_filter = $f['urgent'];

        $reports = DB::select("SELECT * FROM whistleblowing_reports $where_sql ORDER BY created_at DESC", $bindings);

        $stats = [
            'total' => DB::selectOne('SELECT COUNT(*) as count FROM whistleblowing_reports')['count'],
            'new' => DB::selectOne("SELECT COUNT(*) as count FROM whistleblowing_reports WHERE status = 'new'")['count'],
            'under_review' => DB::selectOne("SELECT COUNT(*) as count FROM whistleblowing_reports WHERE status = 'under_review'")['count'],
            'investigating' => DB::selectOne("SELECT COUNT(*) as count FROM whistleblowing_reports WHERE status = 'investigating'")['count'],
            'resolved' => DB::selectOne("SELECT COUNT(*) as count FROM whistleblowing_reports WHERE status IN ('resolved', 'closed')")['count'],
            'urgent' => DB::selectOne("SELECT COUNT(*) as count FROM whistleblowing_reports WHERE urgent = 1 AND status IN ('new', 'under_review', 'investigating')")['count'],
        ];

        $entities = DB::select("SELECT DISTINCT entity FROM whistleblowing_reports WHERE entity IS NOT NULL AND entity != '' ORDER BY entity");

        $vars = compact('status_filter', 'type_filter', 'entity_filter', 'search', 'date_from', 'date_to', 'urgent_filter', 'reports', 'stats', 'entities');
        if (isset($success)) {
            $vars['success'] = $success;
        }
        if (isset($error)) {
            $vars['error'] = $error;
        }

        return view('admin.admin-whistleblowing', $vars);
    }

    /** admin/whistleblowing-api.php (no login check, as before) */
    public function api(Request $request)
    {
        if ($request->query('id') === null || ! is_numeric($request->query('id'))) {
            return kc_json(['error' => 'Invalid report ID']);
        }

        $id = intval($request->query('id'));

        $row = DB::selectOne("SELECT * FROM whistleblowing_reports WHERE id = $id");

        if (! $row) {
            return kc_json(['error' => 'Report not found']);
        }

        $relationshipMap = [
            'current_employee' => 'Current Employee',
            'former_employee' => 'Former Employee',
            'contractor' => 'Contractor / Consultant',
            'supplier' => 'Supplier / Service Provider',
            'customer' => 'Customer',
            'other' => 'Other',
        ];

        $safe = fn ($str) => $str !== null ? $str : '';

        $response = [
            'report' => [
                'id' => $row['id'],
                'reference_number' => $safe($row['reference_number']),
                'submission_type' => $safe($row['submission_type']),
                'full_name' => $safe($row['full_name']),
                'entity' => $safe($row['entity']),
                'department' => $safe($row['department']),
                'contact_method' => $safe($row['contact_method']),
                'relationship_display' => $relationshipMap[$row['relationship'] ?? ''] ?? $safe($row['relationship']),
                'relationship_other' => $safe($row['relationship_other']),
                'nature_concerns' => $safe($row['nature_concerns']),
                'what_happened' => $safe($row['what_happened']),
                'who_involved' => $safe($row['who_involved']),
                'incident_type' => $safe($row['incident_type']),
                'incident_date' => $safe($row['incident_date']),
                'incident_from_date' => $safe($row['incident_from_date']),
                'incident_to_date' => $safe($row['incident_to_date']),
                'incident_location' => $safe($row['incident_location']),
                'awareness_method' => $safe($row['awareness_method']),
                'awareness_other' => $safe($row['awareness_other']),
                'estimated_impact' => $safe($row['estimated_impact']),
                'evidence_description' => $safe($row['evidence_description']),
                'witnesses' => $safe($row['witnesses']),
                'prior_reported' => (int) $row['prior_reported'],
                'prior_report_details' => $safe($row['prior_report_details']),
                'urgent' => (int) $row['urgent'],
                'involves_handler' => (int) $row['involves_handler'],
                'status' => $safe($row['status']),
                'admin_notes' => $safe($row['admin_notes']),
                'resolution_notes' => $safe($row['resolution_notes']),
                'created_at_formatted' => date('d M Y, H:i', strtotime((string) $row['created_at'])),
            ],
        ];

        return kc_json($response);
    }
}

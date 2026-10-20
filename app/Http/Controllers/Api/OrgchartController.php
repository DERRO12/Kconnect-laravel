<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\OrgChart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Org chart JSON API (was api/get_orgchart, get_employees, save_orgchart, delete_node).
 * Only get_orgchart is part of the current module; the other three are kept for old links
 * (nothing in the app calls them any more).
 */
class OrgchartController extends Controller
{
    private const NO_CACHE = ['Cache-Control' => 'no-cache, must-revalidate'];

    /**
     * api/get_orgchart: the organization chart for logged-in staff as JSON
     * { success, data: [ {employee_id, parent_id, level, ...}, ... ], levels: {1: {...}, ...} }
     */
    public function getOrgchart(Request $request)
    {
        $headers = ['Content-Type' => 'application/json; charset=utf-8', 'Cache-Control' => 'no-store'];

        if (! isLoggedIn() && ! isAdminLoggedIn()) {
            return kc_json(['success' => false, 'message' => 'Please log in to view the organization chart.'], 401, $headers);
        }

        try {
            return kc_json([
                'success' => true,
                'data' => OrgChart::nodes(),
                'levels' => OrgChart::LEVELS,
            ], 200, $headers, JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            report($e);

            return kc_json(['success' => false, 'message' => 'Could not load the organization chart.'], 500, $headers);
        }
    }

    /** api/get_employees */
    public function getEmployees(Request $request)
    {
        if (! isLoggedIn() && ! isAdminLoggedIn()) {
            return kc_json(['success' => false, 'message' => 'Unauthorized'], 401, self::NO_CACHE);
        }

        try {
            $companyId = $request->query->has('company_id') ? (int) $request->query('company_id') : 0;
            $search = $request->query->has('search') ? trim($request->query('search')) : '';

            $query = '
        SELECT 
            e.id,
            e.employee_code,
            e.full_name,
            e.email,
            e.photo,
            e.role,
            e.company_id,
            e.department,
            e.position,
            e.status,
            c.name as company_name
        FROM employees e
        LEFT JOIN companies c ON e.company_id = c.id
        WHERE e.status = 1
    ';

            $params = [];

            if ($companyId > 0) {
                $query .= ' AND e.company_id = ?';
                $params[] = $companyId;
            }

            if (! empty($search)) {
                $query .= ' AND (e.full_name LIKE ? OR e.employee_code LIKE ? OR e.position LIKE ? OR e.department LIKE ?)';
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            $query .= ' ORDER BY e.full_name ASC LIMIT 200';

            $employees = [];

            foreach (DB::select($query, $params) as $row) {
                $photoUrl = null;
                if (! empty($row['photo'])) {
                    $photoUrl = 'uploads/employees/'.$row['photo'];
                }

                $employees[] = [
                    'id' => (int) $row['id'],
                    'employee_code' => $row['employee_code'],
                    'full_name' => $row['full_name'],
                    'email' => $row['email'],
                    'photo' => $photoUrl,
                    'role' => $row['role'],
                    'company_id' => (int) $row['company_id'],
                    'company_name' => $row['company_name'],
                    'department' => $row['department'],
                    'position' => $row['position'],
                    'status' => (int) $row['status'],
                ];
            }

            return kc_json([
                'success' => true,
                'data' => $employees,
                'count' => count($employees),
            ], 200, self::NO_CACHE);
        } catch (\Exception $e) {
            return kc_json([
                'success' => false,
                'message' => 'Server error: '.$e->getMessage(),
            ], 500, self::NO_CACHE);
        }
    }

    /** api/save_orgchart */
    public function saveOrgchart(Request $request)
    {
        if (! isAdminLoggedIn() && ! (isLoggedIn() && isAdmin())) {
            return kc_json(['success' => false, 'message' => 'Forbidden - Admin access required'], 403, self::NO_CACHE);
        }

        if (! $request->isMethod('post')) {
            return kc_json(['success' => false, 'message' => 'Method not allowed'], 405, self::NO_CACHE);
        }

        try {
            $input = json_decode($request->getContent(), true);

            if (! $input || ! isset($input['employee_id'])) {
                throw new \Exception('Missing required fields');
            }

            $employeeId = (int) $input['employee_id'];
            $parentId = isset($input['parent_id']) && $input['parent_id'] !== '' && $input['parent_id'] !== null ? (int) $input['parent_id'] : null;
            $level = (int) ($input['level'] ?? 1);
            // The old code escaped the label with mysqli_real_escape_string() and then bound it,
            // so the escaped text is what is stored.
            $levelLabel = isset($input['level_label']) ? kc_escape($input['level_label']) : 'Staff';
            $sortOrder = (int) ($input['sort_order'] ?? 0);

            // Prevent self-referencing
            if ($parentId === $employeeId) {
                throw new \Exception('An employee cannot report to themselves');
            }

            // Validate employee exists
            $checkResult = DB::select('SELECT id, full_name FROM employees WHERE id = ? AND status = 1', [$employeeId]);

            if (count($checkResult) === 0) {
                throw new \Exception('Employee not found or inactive');
            }

            // Check if employee already in orgchart
            $existing = DB::selectOne('SELECT id FROM orgchart_structure WHERE employee_id = ?', [$employeeId]);

            if ($existing) {
                // Update existing
                DB::update(
                    'UPDATE orgchart_structure SET parent_id = ?, level = ?, level_label = ?, sort_order = ?, updated_at = NOW() WHERE employee_id = ?',
                    [$parentId, $level, $levelLabel, $sortOrder, $employeeId]
                );

                $message = 'Org chart entry updated successfully';
            } else {
                // Insert new
                DB::insert(
                    'INSERT INTO orgchart_structure (employee_id, parent_id, level, level_label, sort_order) VALUES (?, ?, ?, ?, ?)',
                    [$employeeId, $parentId, $level, $levelLabel, $sortOrder]
                );

                $message = 'Employee added to org chart successfully';
            }

            return kc_json([
                'success' => true,
                'message' => $message,
            ], 200, self::NO_CACHE);
        } catch (\Exception $e) {
            return kc_json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500, self::NO_CACHE);
        }
    }

    /** api/delete_node */
    public function deleteNode(Request $request)
    {
        if (! isAdminLoggedIn() && ! (isLoggedIn() && isAdmin())) {
            return kc_json(['success' => false, 'message' => 'Forbidden'], 403, self::NO_CACHE);
        }

        if (! $request->isMethod('post')) {
            return kc_json(['success' => false, 'message' => 'Method not allowed'], 405, self::NO_CACHE);
        }

        try {
            $input = json_decode($request->getContent(), true);
            $employeeId = (int) ($input['employee_id'] ?? 0);

            if ($employeeId === 0) {
                throw new \Exception('Invalid employee ID');
            }

            // Start transaction
            DB::beginTransaction();

            // Get this node's parent
            $parentRow = DB::selectOne('SELECT parent_id FROM orgchart_structure WHERE employee_id = ?', [$employeeId]);
            $grandparentId = $parentRow['parent_id'] ?? null;

            // Update children to point to grandparent
            $reassignedCount = DB::update('UPDATE orgchart_structure SET parent_id = ? WHERE parent_id = ?', [$grandparentId, $employeeId]);

            // Delete the node
            DB::delete('DELETE FROM orgchart_structure WHERE employee_id = ?', [$employeeId]);

            DB::commit();

            $message = 'Node removed successfully.';
            if ($reassignedCount > 0) {
                $message .= " {$reassignedCount} subordinate(s) reassigned to parent.";
            }

            return kc_json([
                'success' => true,
                'message' => $message,
            ], 200, self::NO_CACHE);
        } catch (\Exception $e) {
            DB::rollBack();

            return kc_json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500, self::NO_CACHE);
        }
    }
}

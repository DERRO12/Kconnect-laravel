<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Org chart JSON API (was api/get_orgchart.php, get_employees.php, save_orgchart.php, delete_node.php). */
class OrgchartController extends Controller
{
    private const NO_CACHE = ['Cache-Control' => 'no-cache, must-revalidate'];

    /** api/get_orgchart.php */
    public function getOrgchart(Request $request)
    {
        $headers = self::NO_CACHE + ['X-Content-Type-Options' => 'nosniff'];

        // Check authentication
        if (! isLoggedIn()) {
            return kc_json(['success' => false, 'message' => 'Unauthorized'], 401, $headers);
        }

        try {
            // Get orgchart data with employee details
            $rows = DB::select('
        SELECT 
            os.id as org_id,
            os.employee_id,
            os.parent_id,
            os.level,
            os.level_label,
            os.sort_order,
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
        FROM orgchart_structure os
        INNER JOIN employees e ON os.employee_id = e.id AND e.status = 1
        LEFT JOIN companies c ON e.company_id = c.id
        WHERE os.status = 1
        ORDER BY os.level ASC, os.sort_order ASC, e.full_name ASC
    ');

            $orgchartData = [];

            foreach ($rows as $row) {
                $photoUrl = null;
                if (! empty($row['photo'])) {
                    $photoUrl = 'uploads/employees/'.$row['photo'];
                }

                $orgchartData[] = [
                    'id' => (int) $row['org_id'],
                    'employee_id' => (int) $row['employee_id'],
                    'parent_id' => $row['parent_id'] ? (int) $row['parent_id'] : null,
                    'level' => (int) $row['level'],
                    'level_label' => $row['level_label'],
                    'sort_order' => (int) $row['sort_order'],
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
                'data' => $orgchartData,
                'count' => count($orgchartData),
            ], 200, $headers);
        } catch (\Exception $e) {
            return kc_json([
                'success' => false,
                'message' => 'Server error: '.$e->getMessage(),
            ], 500, $headers);
        }
    }

    /** api/get_employees.php */
    public function getEmployees(Request $request)
    {
        if (! isLoggedIn()) {
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

    /** api/save_orgchart.php */
    public function saveOrgchart(Request $request)
    {
        if (! isLoggedIn() || ! isAdmin()) {
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

    /** api/delete_node.php */
    public function deleteNode(Request $request)
    {
        if (! isLoggedIn() || ! isAdmin()) {
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

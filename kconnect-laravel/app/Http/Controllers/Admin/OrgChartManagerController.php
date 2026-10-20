<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Organisation chart manager (admin/orgchart_manager.php). */
class OrgChartManagerController extends Controller
{
    private function flash($message, $type): void
    {
        session(['message' => $message, 'message_type' => $type]);
    }

    /** admin/orgchart_manager.php */
    public function index(Request $request)
    {
        if (! isAdminLoggedIn()) {
            kc_redirect('login.php');
        }

        $employeeName = session('employee_name') ?? '';
        $employeeCode = session('employee_code') ?? '';
        $employeePhoto = session('employee_photo') ?? '';

        if ($request->isMethod('post') && $request->post('action') !== null) {
            $action = $request->post('action');

            if ($action === 'save') {
                $employeeId = (int) $request->post('employee_id');
                $parentId = ! empty($request->post('parent_id')) ? (int) $request->post('parent_id') : null;
                $level = (int) $request->post('level');
                $levelLabel = (string) ($request->post('level_label') ?? '');
                $sortOrder = (int) $request->post('sort_order');

                if ($employeeId <= 0) {
                    $this->flash('Please select an employee', 'danger');
                    kc_redirect('orgchart_manager.php');
                }

                if ($parentId === $employeeId) {
                    $this->flash('An employee cannot report to themselves', 'danger');
                    kc_redirect('orgchart_manager.php');
                }

                $checkEmp = DB::select("SELECT id FROM employees WHERE id = $employeeId AND status = 1");
                if (count($checkEmp) === 0) {
                    $this->flash('Employee not found or inactive', 'danger');
                    kc_redirect('orgchart_manager.php');
                }

                $existing = DB::select("SELECT id FROM orgchart_structure WHERE employee_id = $employeeId");

                if (count($existing) > 0) {
                    try {
                        DB::update('UPDATE orgchart_structure SET 
                parent_id = '.($parentId ? $parentId : 'NULL').",
                level = $level,
                level_label = ?,
                sort_order = $sortOrder,
                updated_at = NOW()
                WHERE employee_id = $employeeId", [$levelLabel ? $levelLabel : null]);
                        $this->flash('Organization chart entry updated successfully!', 'success');
                    } catch (\Illuminate\Database\QueryException $e) {
                        $this->flash('Error updating: '.$e->getMessage(), 'danger');
                    }
                } else {
                    try {
                        DB::insert("INSERT INTO orgchart_structure (employee_id, parent_id, level, level_label, sort_order) 
                VALUES ($employeeId, ".($parentId ? $parentId : 'NULL').", $level, ?, $sortOrder)", [$levelLabel ? $levelLabel : null]);
                        $this->flash('Employee added to organization chart successfully!', 'success');
                    } catch (\Illuminate\Database\QueryException $e) {
                        $this->flash('Error adding: '.$e->getMessage(), 'danger');
                    }
                }

                kc_redirect('orgchart_manager.php');
            }

            if ($action === 'delete') {
                $employeeId = (int) $request->post('employee_id');

                $parentRow = DB::selectOne("SELECT parent_id FROM orgchart_structure WHERE employee_id = $employeeId");
                $parentId = $parentRow ? $parentRow['parent_id'] : null;

                if ($parentId) {
                    DB::update("UPDATE orgchart_structure SET parent_id = $parentId WHERE parent_id = $employeeId");
                } else {
                    DB::update("UPDATE orgchart_structure SET parent_id = NULL WHERE parent_id = $employeeId");
                }

                DB::delete("DELETE FROM orgchart_structure WHERE employee_id = $employeeId");

                $this->flash('Employee removed from organization chart successfully!', 'success');
                kc_redirect('orgchart_manager.php');
            }
        }

        $orgData = [];
        $orgDataJSON = [];
        foreach (DB::select('
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
        e.department,
        e.position,
        e.status,
        c.name as company_name
    FROM orgchart_structure os
    INNER JOIN employees e ON os.employee_id = e.id
    LEFT JOIN companies c ON e.company_id = c.id
    WHERE os.status = 1 AND e.status = 1
    ORDER BY os.level ASC, os.sort_order ASC, e.full_name ASC
') as $row) {
            $orgData[] = $row;
            $orgDataJSON[] = [
                'org_id' => (int) $row['org_id'],
                'employee_id' => (int) $row['employee_id'],
                'parent_id' => $row['parent_id'] ? (int) $row['parent_id'] : null,
                'level' => (int) $row['level'],
                'level_label' => $row['level_label'] ?? '',
                'sort_order' => (int) $row['sort_order'],
                'employee_code' => $row['employee_code'] ?? '',
                'full_name' => $row['full_name'] ?? '',
                'position' => $row['position'] ?? '',
                'department' => $row['department'] ?? '',
                'photo' => $row['photo'] ?? '',
                'email' => $row['email'] ?? '',
                'company_name' => $row['company_name'] ?? '',
            ];
        }

        $allEmployees = [];
        $allEmployeesJSON = [];
        foreach (DB::select('SELECT id, employee_code, full_name, position, department, photo FROM employees WHERE status = 1 ORDER BY full_name') as $row) {
            $allEmployees[] = $row;
            $allEmployeesJSON[] = [
                'id' => (int) $row['id'],
                'employee_code' => $row['employee_code'] ?? '',
                'full_name' => $row['full_name'] ?? '',
                'position' => $row['position'] ?? '',
                'department' => $row['department'] ?? '',
                'photo' => $row['photo'] ?? '',
            ];
        }

        $allChartEmployees = [];
        $allChartEmployeesJSON = [];
        foreach (DB::select('SELECT e.id, e.employee_code, e.full_name, e.position, os.level_label, os.level 
    FROM orgchart_structure os 
    INNER JOIN employees e ON os.employee_id = e.id 
    WHERE os.status = 1 AND e.status = 1 
    ORDER BY os.level ASC, e.full_name ASC') as $row) {
            $allChartEmployees[] = $row;
            $allChartEmployeesJSON[] = [
                'id' => (int) $row['id'],
                'full_name' => $row['full_name'] ?? '',
                'position' => $row['position'] ?? '',
                'level_label' => $row['level_label'] ?? '',
                'level' => (int) $row['level'],
            ];
        }

        $totalInChart = count($orgData);
        $topLevelCount = count(array_filter($orgData, fn ($e) => empty($e['parent_id'])));
        $levelsUsed = count(array_unique(array_column($orgData, 'level')));
        $orgEmployeeIds = array_column($orgData, 'employee_id');
        $availableCount = count(array_filter($allEmployees, fn ($e) => ! in_array($e['id'], $orgEmployeeIds)));

        $levelLabels = [
            1 => 'CEO / C-Suite',
            2 => 'Director',
            3 => 'Senior Head',
            4 => 'Head',
            5 => 'Senior Manager',
            6 => 'Manager',
            7 => 'Supervisor',
            8 => 'Staff',
        ];

        $message = session('message');
        $message_type = session('message_type');
        session()->forget(['message', 'message_type']);

        return view('admin.orgchart_manager', compact(
            'employeeName', 'employeeCode', 'employeePhoto', 'orgData', 'orgDataJSON', 'allEmployees', 'allEmployeesJSON',
            'allChartEmployees', 'allChartEmployeesJSON', 'totalInChart', 'topLevelCount', 'levelsUsed', 'orgEmployeeIds',
            'availableCount', 'levelLabels', 'message', 'message_type'
        ));
    }
}

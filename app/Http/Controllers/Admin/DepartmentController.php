<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Companies and departments admin (admin/manage-departments and its AJAX helpers). */
class DepartmentController extends Controller
{
    /** admin/manage-departments */
    public function manage(Request $request)
    {
        if (! isAdminLoggedIn()) {
            kc_redirect('login');
        }

        if ($request->post('action') !== null) {
            $action = $request->post('action');

            // Add/Edit Company
            if ($action == 'save_company') {
                $id = $request->post('id') !== null ? (int) $request->post('id') : 0;
                $name = sanitize($request->post('name'));
                $code = sanitize($request->post('code'));
                $description = sanitize($request->post('description'));
                $address = sanitize($request->post('address'));
                $phone = sanitize($request->post('phone'));
                $email = sanitize($request->post('email'));
                $website = sanitize($request->post('website'));
                $sort_order = (int) $request->post('sort_order');
                $status = $request->post('status') !== null ? 1 : 0;

                // Handle logo upload
                $logo = '';
                if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                    $file = $request->file('logo');
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
                    $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));

                    if (in_array($ext, $allowed)) {
                        $newFilename = 'company_'.time().'_'.preg_replace('/[^a-zA-Z0-9.]/', '_', $file->getClientOriginalName());

                        $moved = true;
                        try {
                            $file->move(kc_upload_path(), $newFilename);
                        } catch (\Throwable $e) {
                            $moved = false;
                        }
                        if ($moved) {
                            $logo = $newFilename;

                            if ($id > 0) {
                                $old = DB::selectOne("SELECT logo FROM companies WHERE id = $id");
                                if ($old && $old['logo'] && is_file(kc_upload_path().basename($old['logo']))) {
                                    unlink(kc_upload_path().basename($old['logo']));
                                }
                            }
                        }
                    }
                } elseif ($request->post('existing_logo') !== null) {
                    // only a bare file name in uploads/ (never a path)
                    $logo = basename((string) $request->post('existing_logo'));
                }

                if ($id > 0) {
                    $query = "UPDATE companies SET 
                      name = ?,
                      code = ?,
                      description = ?,
                      address = ?,
                      phone = ?,
                      email = ?,
                      website = ?,
                      sort_order = $sort_order,
                      status = $status";
                    $bindings = [$name, $code, $description, $address, $phone, $email, $website];

                    if ($logo) {
                        $query .= ', logo = ?';
                        $bindings[] = $logo;
                    }

                    $query .= " WHERE id = $id";
                } else {
                    $query = "INSERT INTO companies (name, code, description, address, phone, email, website, logo, sort_order, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, $sort_order, $status)";
                    $bindings = [$name, $code, $description, $address, $phone, $email, $website, $logo ? $logo : null];
                }

                try {
                    DB::statement($query, $bindings);
                    session(['success' => 'Company saved successfully']);
                } catch (\Illuminate\Database\QueryException $e) {
                    session(['error' => 'Error: '.$e->getMessage()]);
                }

                kc_redirect('manage-departments');
            }

            // Delete Company
            if ($action == 'delete_company') {
                $id = (int) $request->post('id');

                $count = DB::selectOne("SELECT COUNT(*) as count FROM departments WHERE company_id = $id")['count'];

                if ($count > 0) {
                    session(['error' => 'Cannot delete company with existing departments. Delete departments first.']);
                } else {
                    $company = DB::selectOne("SELECT logo FROM companies WHERE id = $id");
                    if ($company && $company['logo'] && is_file(kc_upload_path().basename($company['logo']))) {
                        unlink(kc_upload_path().basename($company['logo']));
                    }

                    DB::delete("DELETE FROM companies WHERE id = $id");
                    session(['success' => 'Company deleted successfully']);
                }

                kc_redirect('manage-departments');
            }

            // Save Department
            if ($action == 'save_department') {
                $id = $request->post('id') !== null ? (int) $request->post('id') : 0;
                $name = sanitize($request->post('name'));
                $code = sanitize($request->post('code'));
                $company_id = (int) $request->post('company_id');
                $organization_type = sanitize($request->post('organization_type'));
                $sales_division = ($organization_type == 'Sales') ? sanitize($request->post('sales_division')) : 'None';
                $support_department = ($organization_type == 'Support') ? sanitize($request->post('support_department')) : null;
                $parent_id = ! empty($request->post('parent_id')) ? (int) $request->post('parent_id') : 'NULL';
                $level = (int) $request->post('level');
                $head_employee_id = ! empty($request->post('head_employee_id')) ? (int) $request->post('head_employee_id') : 'NULL';
                $description = sanitize($request->post('description'));
                $icon = sanitize($request->post('icon'));
                $color = sanitize($request->post('color'));
                $sort_order = (int) $request->post('sort_order');
                $status = $request->post('status') !== null ? 1 : 0;

                if ($id > 0) {
                    $query = "UPDATE departments SET 
                      name = ?,
                      code = ?,
                      company_id = $company_id,
                      organization_type = ?,
                      sales_division = ?,
                      support_department = ?,
                      parent_id = $parent_id,
                      level = $level,
                      head_employee_id = $head_employee_id,
                      description = ?,
                      icon = ?,
                      color = ?,
                      sort_order = $sort_order,
                      status = $status
                      WHERE id = $id";
                    $bindings = [$name, $code, $organization_type, $sales_division, $support_department ? $support_department : null,
                        $description, $icon ? $icon : null, $color ? $color : null];
                } else {
                    $query = "INSERT INTO departments 
                      (name, code, company_id, organization_type, sales_division, support_department, 
                       parent_id, level, head_employee_id, description, icon, color, sort_order, status) 
                      VALUES (?, ?, $company_id, ?, ?, ?, 
                      $parent_id, $level, $head_employee_id, ?, ?, ?, $sort_order, $status)";
                    $bindings = [$name, $code, $organization_type, $sales_division, $support_department ? $support_department : null,
                        $description, $icon ? $icon : null, $color ? $color : null];
                }

                try {
                    DB::statement($query, $bindings);
                    session(['success' => 'Department saved successfully']);
                } catch (\Illuminate\Database\QueryException $e) {
                    session(['error' => 'Error: '.$e->getMessage()]);
                }

                kc_redirect('manage-departments');
            }

            // Delete Department
            if ($action == 'delete_department') {
                $id = (int) $request->post('id');

                $count = DB::selectOne("SELECT COUNT(*) as count FROM departments WHERE parent_id = $id")['count'];

                if ($count > 0) {
                    session(['error' => 'Cannot delete department with sub-departments. Delete sub-departments first.']);
                } else {
                    $empCount = DB::selectOne("SELECT COUNT(*) as count FROM organization_chart WHERE department_id = $id OR sub_department_id = $id")['count'];

                    if ($empCount > 0) {
                        session(['error' => 'Cannot delete department with assigned employees. Reassign employees first.']);
                    } else {
                        DB::delete("DELETE FROM departments WHERE id = $id");
                        session(['success' => 'Department deleted successfully']);
                    }
                }

                kc_redirect('manage-departments');
            }
        }

        $companies = DB::select('SELECT * FROM companies ORDER BY sort_order, name');

        $departments = DB::select('SELECT d.*, c.name as company_name, 
                                    (SELECT name FROM departments WHERE id = d.parent_id) as parent_name,
                                    (SELECT employee_name FROM organization_chart WHERE id = d.head_employee_id) as head_name
                                    FROM departments d
                                    LEFT JOIN companies c ON d.company_id = c.id
                                    ORDER BY c.sort_order, d.level, d.sort_order, d.name');

        $employees = DB::select('SELECT id, employee_name, position, company FROM organization_chart WHERE status = 1 ORDER BY employee_name');

        $editCompany = null;
        $editDepartment = null;
        if ($request->query('edit_company') !== null) {
            $id = (int) $request->query('edit_company');
            $editCompany = DB::selectOne("SELECT * FROM companies WHERE id = $id");
        }
        if ($request->query('edit_department') !== null) {
            $id = (int) $request->query('edit_department');
            $editDepartment = DB::selectOne("SELECT * FROM departments WHERE id = $id");
        }

        $totalCompanies = count($companies);
        $totalDepartments = DB::selectOne('SELECT COUNT(*) as total FROM departments')['total'];
        $salesDepartments = DB::selectOne("SELECT COUNT(*) as total FROM departments WHERE organization_type = 'Sales'")['total'];
        $supportDepartments = DB::selectOne("SELECT COUNT(*) as total FROM departments WHERE organization_type = 'Support'")['total'];

        // Department tree (the old template queried it while rendering, recursively)
        $topDeptsByCompany = [];
        $subDeptsByParent = [];
        foreach ($companies as $company) {
            $topDeptsByCompany[$company['id']] = DB::select("SELECT d.*, 
                                                             (SELECT employee_name FROM organization_chart WHERE id = d.head_employee_id) as head_name
                                                             FROM departments d 
                                                             WHERE d.company_id = {$company['id']} AND d.parent_id IS NULL 
                                                             ORDER BY d.level, d.sort_order, d.name");
            foreach ($topDeptsByCompany[$company['id']] as $dept) {
                $this->loadSubDepartments($dept['id'], $subDeptsByParent);
            }
        }

        $allDepts = DB::select('SELECT d.*, c.name as company_name FROM departments d 
                                                                          LEFT JOIN companies c ON d.company_id = c.id 
                                                                          ORDER BY c.sort_order, d.level, d.name');

        // flash messages (echoed then unset by the old template)
        $flash_success = session('success');
        $flash_error = session('error');
        session()->forget(['success', 'error']);

        return view('admin.manage-departments', compact(
            'companies', 'departments', 'employees', 'editCompany', 'editDepartment', 'totalCompanies',
            'totalDepartments', 'salesDepartments', 'supportDepartments', 'topDeptsByCompany', 'subDeptsByParent',
            'allDepts', 'flash_success', 'flash_error'
        ));
    }

    /** Old displaySubDepartmentTree($parent_id, $conn): loads the children of $parent_id recursively. */
    private function loadSubDepartments($parent_id, array &$map): void
    {
        $map[$parent_id] = DB::select("SELECT d.*, 
                                                                      (SELECT employee_name FROM organization_chart WHERE id = d.head_employee_id) as head_name
                                                                      FROM departments d 
                                                                      WHERE d.parent_id = $parent_id 
                                                                      ORDER BY d.level, d.sort_order, d.name");
        foreach ($map[$parent_id] as $subDept) {
            $this->loadSubDepartments($subDept['id'], $map);
        }
    }

    /** admin/ajax-department-view */
    public function ajaxView(Request $request)
    {
        if (! isAdminLoggedIn()) {
            kc_exit('<div class="alert alert-warning m-3">Your session has expired. Please <a href="login">log in</a> again.</div>', 401);
        }

        if ($request->post('department_id') === null) {
            kc_exit('Department ID required');
        }

        $dept_id = (int) $request->post('department_id');

        $dept = DB::selectOne("SELECT d.*, c.name as company_name,
                                                (SELECT employee_name FROM organization_chart WHERE id = d.head_employee_id) as head_name,
                                                (SELECT position FROM organization_chart WHERE id = d.head_employee_id) as head_position,
                                                (SELECT photo FROM organization_chart WHERE id = d.head_employee_id) as head_photo
                                                FROM departments d
                                                LEFT JOIN companies c ON d.company_id = c.id
                                                WHERE d.id = $dept_id");

        if (! $dept) {
            kc_exit('Department not found');
        }

        $subDepts = DB::select("SELECT d.*, 
                                 (SELECT employee_name FROM organization_chart WHERE id = d.head_employee_id) as head_name
                                 FROM departments d
                                 WHERE d.parent_id = $dept_id AND d.status = 1
                                 ORDER BY d.level, d.sort_order, d.name");

        $employees = DB::select("SELECT oc.*, 
                                  (SELECT employee_name FROM organization_chart WHERE id = oc.parent_id) as reports_to
                                  FROM organization_chart oc
                                  WHERE (oc.department_id = $dept_id OR oc.sub_department_id = $dept_id) AND oc.status = 1
                                  ORDER BY oc.level, oc.sort_order");

        return view('admin.ajax-department-view', compact('dept_id', 'dept', 'subDepts', 'employees'));
    }

    /** admin/get-departments */
    public function getDepartments(Request $request)
    {
        if (! isAdminLoggedIn()) {
            return kc_json(['error' => 'Unauthorized'], 401);
        }

        $company_id = $request->query('company_id') !== null ? (int) $request->query('company_id') : 0;
        $parent_id = $request->query('parent_id') !== null ? (int) $request->query('parent_id') : 0;

        $departments = [];

        if ($company_id > 0) {
            $departments = DB::select("SELECT id, name, code, organization_type FROM departments 
              WHERE company_id = $company_id AND status = 1 AND parent_id IS NULL 
              ORDER BY level, sort_order, name");
        } elseif ($parent_id > 0) {
            $departments = DB::select("SELECT id, name, code, organization_type FROM departments 
              WHERE parent_id = $parent_id AND status = 1 
              ORDER BY level, sort_order, name");
        }

        return kc_json($departments);
    }
}

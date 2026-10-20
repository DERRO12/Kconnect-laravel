<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminPeopleKAcademy;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Admin people pages: employees, users, K-Academy sync. */
class PeopleController extends Controller
{
    /** Old uploadPhoto($file) from admin/manage-employees */
    private function uploadPhoto($file): array
    {
        $target_dir = kc_upload_path('employees/');
        if (! file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (! in_array($file_extension, $allowed)) {
            return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed.'];
        }

        $new_filename = 'emp_'.time().'_'.uniqid().'.'.$file_extension;

        try {
            $file->move($target_dir, $new_filename);

            return [
                'success' => true,
                'filename' => $new_filename,
                'filepath' => 'uploads/employees/'.$new_filename,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => 'Failed to upload file'];
        }
    }

    /** Old safeEscape($conn, $value): now only the null/'' handling (values are bound). */
    private function safeEscape($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return (string) $value;
    }

    private function flash($message, $type): void
    {
        session(['message' => $message, 'message_type' => $type]);
    }

    /** admin/manage-employees */
    public function manageEmployees(Request $request)
    {
        if (! session()->has('admin_id')) {
            kc_redirect('login');
        }

        $kacademy = getKADB();

        // Handle delete (POST from the row's delete button)
        if ($request->isMethod('post') && $request->query('delete') !== null && is_numeric($request->query('delete'))) {
            $id = (int) $request->query('delete');

            if ($id == session('admin_id')) {
                $this->flash('You cannot delete your own account!', 'danger');
                kc_redirect('manage-employees');
            }

            $employee = DB::selectOne("SELECT photo FROM employees WHERE id = $id");

            try {
                DB::delete("DELETE FROM employees WHERE id = $id");
            } catch (\Illuminate\Database\QueryException $e) {
                report($e);
                $this->flash('This employee could not be deleted because other records (tickets, nominations, assets...) still refer to them. Deactivate the employee instead.', 'danger');
                kc_redirect('manage-employees');
            }

            // remove the photo only once the row is gone
            if ($employee && $employee['photo']) {
                $photo_path = kc_upload_path('employees/').$employee['photo'];
                if (file_exists($photo_path)) {
                    unlink($photo_path);
                }
            }
            $this->flash('Employee deleted successfully!', 'success');
            kc_redirect('manage-employees');
        }

        // Handle toggle status (POST)
        if ($request->isMethod('post') && $request->query('toggle') !== null && is_numeric($request->query('toggle'))) {
            $id = (int) $request->query('toggle');

            if ($id == session('admin_id')) {
                $this->flash('You cannot change your own status!', 'danger');
                kc_redirect('manage-employees');
            }

            DB::update("UPDATE employees SET status = NOT status WHERE id = $id");
            $this->flash('Employee status updated successfully!', 'success');
            kc_redirect('manage-employees');
        }

        // Handle password reset
        if ($request->post('reset_password') !== null) {
            $id = (int) $request->post('employee_id');
            $new_password = $request->post('new_password') ?? '';
            $confirm_password = $request->post('confirm_password') ?? '';

            if (empty($new_password) || empty($confirm_password)) {
                $this->flash('Please enter and confirm the new password', 'danger');
            } elseif ($new_password !== $confirm_password) {
                $this->flash('Passwords do not match!', 'danger');
            } elseif (strlen($new_password) < 6) {
                $this->flash('Password must be at least 6 characters long', 'danger');
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                try {
                    DB::update("UPDATE employees SET password = ? WHERE id = $id", [$hashed_password]);
                    $this->flash('Password reset successfully!', 'success');
                } catch (\Throwable $e) {
                    $this->flash('Error resetting password: '.$e->getMessage(), 'danger');
                }
            }
            kc_redirect('manage-employees');
        }

        // Handle sync single employee
        if ($request->post('sync_single') !== null && $request->post('employee_id') !== null) {
            $employee_id = intval($request->post('employee_id'));
            $result = AdminPeopleKAcademy::syncSingleEmployee($kacademy, $employee_id);
            $this->flash($result['message'], $result['success'] ? 'success' : 'danger');
            kc_redirect('manage-employees');
        }

        // Handle sync all employees
        if ($request->post('sync_all') !== null) {
            if ($kacademy) {
                $synced = 0;
                $updated = 0;
                $failed = 0;
                $skipped = 0;

                $rows = DB::select("SELECT * FROM employees WHERE email IS NOT NULL AND email != '' AND employee_code IS NOT NULL AND employee_code != '' ORDER BY id");
                $total_to_sync = count($rows);

                foreach ($rows as $emp) {
                    $sync_result = AdminPeopleKAcademy::syncSingleEmployee($kacademy, $emp['id']);
                    if ($sync_result['success']) {
                        if ($sync_result['action'] == 'inserted') {
                            $synced++;
                        } else {
                            $updated++;
                        }
                    } else {
                        if (strpos($sync_result['message'], 'Missing email') !== false) {
                            $skipped++;
                        } else {
                            $failed++;
                        }
                    }
                }

                $this->flash("Sync completed! Total: $total_to_sync, New: $synced, Updated: $updated, Failed: $failed, Skipped: $skipped", $failed > 0 ? 'warning' : 'success');
            } else {
                $this->flash('K-Academy connection not available', 'danger');
            }
            kc_redirect('manage-employees');
        }

        // Handle form submission
        if ($request->isMethod('post') && $request->post('save_employee') !== null) {
            $employee_code = $this->safeEscape($request->post('employee_code') ?? '');
            $full_name = $this->safeEscape($request->post('full_name') ?? '');
            $email = $this->safeEscape($request->post('email') ?? '');
            $phone = $this->safeEscape($request->post('phone') ?? '');
            $extension = $this->safeEscape($request->post('extension') ?? '');
            $mobile = $this->safeEscape($request->post('mobile') ?? '');
            $department = $this->safeEscape($request->post('department') ?? '');
            $position = $this->safeEscape($request->post('position') ?? '');
            $company_id = ! empty($request->post('company_id')) ? (int) $request->post('company_id') : null;
            $role = $this->safeEscape($request->post('role') ?? 'employee');
            $birth_date = ! empty($request->post('birth_date')) ? $this->safeEscape($request->post('birth_date')) : null;
            $join_date = ! empty($request->post('join_date')) ? $this->safeEscape($request->post('join_date')) : null;
            $emergency_contact = $this->safeEscape($request->post('emergency_contact') ?? '');
            $emergency_phone = $this->safeEscape($request->post('emergency_phone') ?? '');
            $address = $this->safeEscape($request->post('address') ?? '');
            $notes = $this->safeEscape($request->post('notes') ?? '');
            $edit_id = $request->post('edit_id') !== null ? (int) $request->post('edit_id') : 0;

            if (empty($employee_code) || empty($full_name) || empty($email)) {
                $this->flash('Employee Code, Name, and Email are required!', 'danger');
                kc_redirect('manage-employees'.($edit_id ? '?edit='.$edit_id : ''));
            }

            $check_code = DB::select('SELECT id FROM employees WHERE employee_code = ?'.($edit_id ? " AND id != $edit_id" : ''), [$employee_code]);
            if (count($check_code) > 0) {
                $this->flash('Employee Code already exists!', 'danger');
                kc_redirect('manage-employees'.($edit_id ? '?edit='.$edit_id : ''));
            }

            $check_email = DB::select('SELECT id FROM employees WHERE email = ?'.($edit_id ? " AND id != $edit_id" : ''), [$email]);
            if (count($check_email) > 0) {
                $this->flash('Email already exists!', 'danger');
                kc_redirect('manage-employees'.($edit_id ? '?edit='.$edit_id : ''));
            }

            if ($edit_id > 0) {
                $photo_update = '';
                $photo_binding = [];
                if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                    $upload = $this->uploadPhoto($request->file('photo'));
                    if ($upload['success']) {
                        $old_emp = DB::selectOne("SELECT photo FROM employees WHERE id = $edit_id");
                        if ($old_emp) {
                            if ($old_emp['photo']) {
                                $old_path = kc_upload_path('employees/').$old_emp['photo'];
                                if (file_exists($old_path)) {
                                    unlink($old_path);
                                }
                            }
                        }
                        $photo_update = ', photo = ?';
                        $photo_binding = [$upload['filename']];
                    }
                }

                $sql = "UPDATE employees SET 
                employee_code = ?,
                full_name = ?,
                email = ?,
                phone = ?,
                extension = ?,
                mobile = ?,
                department = ?,
                position = ?,
                company_id = ?,
                role = ?,
                birth_date = ?,
                join_date = ?,
                emergency_contact = ?,
                emergency_phone = ?,
                address = ?,
                notes = ?
                $photo_update
                WHERE id = $edit_id";

                try {
                    DB::update($sql, array_merge([
                        $employee_code, $full_name, $email, $phone, $extension, $mobile, $department, $position,
                        $company_id, $role, $birth_date, $join_date, $emergency_contact, $emergency_phone, $address, $notes,
                    ], $photo_binding));
                    $this->flash('Employee updated successfully!', 'success');
                } catch (\Throwable $e) {
                    $this->flash('Error: '.$e->getMessage(), 'danger');
                }
            } else {
                if (empty($request->post('password'))) {
                    $this->flash('Password is required for new employees!', 'danger');
                    kc_redirect('manage-employees');
                }

                $password = password_hash($request->post('password'), PASSWORD_BCRYPT);

                $photo = null;
                if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                    $upload = $this->uploadPhoto($request->file('photo'));
                    if ($upload['success']) {
                        $photo = $upload['filename'];
                    }
                }

                $sql = 'INSERT INTO employees (
                employee_code, full_name, email, password, phone, extension, mobile,
                department, position, company_id, role, birth_date, join_date,
                emergency_contact, emergency_phone, address, notes, photo, status
                ) VALUES (
                ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, 1
                )';

                try {
                    DB::insert($sql, [
                        $employee_code, $full_name, $email, $password, $phone, $extension, $mobile,
                        $department, $position, $company_id, $role, $birth_date, $join_date,
                        $emergency_contact, $emergency_phone, $address, $notes, $photo,
                    ]);
                    $new_id = DB::getPdo()->lastInsertId();

                    if ($kacademy) {
                        $sync_result = AdminPeopleKAcademy::syncSingleEmployee($kacademy, $new_id);
                        if ($sync_result['success']) {
                            $this->flash('Employee added and synced to K-Academy successfully!', 'success');
                        } else {
                            $this->flash('Employee added but K-Academy sync failed: '.$sync_result['message'], 'warning');
                        }
                    } else {
                        $this->flash('Employee added successfully! (K-Academy not available)', 'success');
                    }
                } catch (\Throwable $e) {
                    $this->flash('Error: '.$e->getMessage(), 'danger');
                }
            }

            kc_redirect('manage-employees');
        }

        // GET DATA
        $employees = DB::select('
    SELECT e.*, c.name as company_name, c.code as company_code 
    FROM employees e 
    LEFT JOIN companies c ON e.company_id = c.id 
    ORDER BY e.full_name
');

        $companies = DB::select('SELECT * FROM companies WHERE status = 1 ORDER BY name');

        $total_employees = DB::selectOne('SELECT COUNT(*) as total FROM employees')['total'] ?? 0;
        $active_employees = DB::selectOne('SELECT COUNT(*) as total FROM employees WHERE status = 1')['total'] ?? 0;
        $total_companies_count = DB::selectOne('SELECT COUNT(DISTINCT company_id) as total FROM employees WHERE company_id IS NOT NULL')['total'] ?? 0;

        $edit_employee = null;
        if ($request->query('edit') !== null && is_numeric($request->query('edit'))) {
            $edit_id = (int) $request->query('edit');
            $edit_employee = DB::selectOne("SELECT * FROM employees WHERE id = $edit_id");
        }

        $synced_count = 0;
        $not_synced_count = 0;
        $total_with_data = 0;
        if ($kacademy) {
            $total_with_data = DB::selectOne("SELECT COUNT(*) as total FROM employees WHERE email IS NOT NULL AND email != '' AND employee_code IS NOT NULL AND employee_code != ''")['total'];

            $synced_employees = 0;
            $emp_check = DB::select("SELECT employee_code, email FROM employees WHERE email IS NOT NULL AND email != '' AND employee_code IS NOT NULL AND employee_code != ''");
            foreach ($emp_check as $emp) {
                if (AdminPeopleKAcademy::isEmployeeSynced($kacademy, $emp['employee_code'], $emp['email'])) {
                    $synced_employees++;
                }
            }

            $synced_count = $synced_employees;
            $not_synced_count = $total_with_data - $synced_count;
        }

        // per-row sync flag (the old template queried K-Academy for each row)
        $emp_synced = [];
        foreach ($employees as $emp) {
            $emp_synced[$emp['id']] = AdminPeopleKAcademy::isEmployeeSynced($kacademy, $emp['employee_code'], $emp['email']);
        }

        // flash message (read then unset in the old template)
        $message = session('message');
        $message_type = session('message_type');
        session()->forget(['message', 'message_type']);

        return view('admin.manage-employees', compact(
            'employees', 'companies', 'total_employees', 'active_employees', 'total_companies_count',
            'edit_employee', 'synced_count', 'not_synced_count', 'total_with_data', 'emp_synced',
            'message', 'message_type'
        ));
    }

    /** admin/users */
    public function users(Request $request)
    {
        if (! isAdminLoggedIn()) {
            kc_redirect('login');
        }

        $current_admin_id = session('admin_id') ?? session('employee_id') ?? 0;

        // HANDLE EXPORT TO EXCEL
        if ($request->query('export') !== null) {
            $export_type = $request->query('export');

            $export_where = '';
            $export_title = '';
            $bindings = [];

            if ($export_type === 'active') {
                $export_where = 'WHERE status = 1';
                $export_title = 'Active Users';
            } elseif ($export_type === 'inactive') {
                $export_where = 'WHERE status = 0';
                $export_title = 'Inactive Users';
            } else {
                $export_where = '';
                $export_title = 'All Users';
            }

            $search = $request->query('search') !== null ? sanitize($request->query('search')) : '';
            if ($search) {
                $search_condition = '(employee_code LIKE ? OR full_name LIKE ? OR email LIKE ?)';
                $bindings = ["%$search%", "%$search%", "%$search%"];
                if ($export_where) {
                    $export_where .= " AND $search_condition";
                } else {
                    $export_where = "WHERE $search_condition";
                }
            }

            $export_rows = DB::select("SELECT 
        id,
        employee_code,
        full_name,
        email,
        phone,
        extension,
        mobile,
        department,
        position,
        company_id,
        role,
        status,
        join_date,
        birth_date,
        last_login,
        created_at
        FROM employees 
        $export_where 
        ORDER BY full_name ASC", $bindings);

            $filename = 'Users_'.str_replace(' ', '_', $export_title).'_'.date('Y-m-d_His').'.xls';

            $out = '<html><head><meta charset="UTF-8"><style>
        body { font-family: Arial; font-size: 11px; }
        h2 { color: #009ada; margin: 0 0 5px; }
        .header-info { margin-bottom: 15px; padding: 10px; background: #f5f5f5; border-left: 4px solid #009ada; }
        .header-info p { margin: 3px 0; }
        table { border-collapse: collapse; width: 100%; }
        th { background: #009ada; color: white; padding: 8px; text-align: left; font-size: 11px; border: 1px solid #0077b6; }
        td { padding: 6px 8px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #f9f9f9; }
        .status-active { color: #16a34a; font-weight: bold; }
        .status-inactive { color: #dc2626; font-weight: bold; }
        .role-admin { color: #991b1b; font-weight: bold; }
        .role-hr { color: #1e40af; font-weight: bold; }
        .role-manager { color: #92400e; font-weight: bold; }
        .role-employee { color: #334155; }
        .footer { margin-top: 15px; padding: 10px; background: #f5f5f5; font-size: 10px; color: #666; text-align: center; }
    </style></head><body>';

            $out .= '<h2>K-Connect User Report - '.htmlspecialchars($export_title).'</h2>';
            $out .= '<div class="header-info">';
            $out .= '<p><strong>Generated:</strong> '.date('F d, Y H:i:s').'</p>';
            $out .= '<p><strong>Report Type:</strong> '.htmlspecialchars($export_title).'</p>';
            if ($search) {
                $out .= '<p><strong>Search Filter:</strong> '.htmlspecialchars($search).'</p>';
            }
            $out .= '<p><strong>Total Records:</strong> '.count($export_rows).'</p>';
            $out .= '<p><strong>Exported By:</strong> '.htmlspecialchars(session('admin_name') ?? session('employee_name') ?? 'Admin').'</p>';
            $out .= '</div>';

            $out .= '<table><thead><tr>
        <th>#</th>
        <th>Employee Code</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Extension</th>
        <th>Mobile</th>
        <th>Department</th>
        <th>Position</th>
        <th>Role</th>
        <th>Status</th>
        <th>Join Date</th>
        <th>Last Login</th>
        <th>Created</th>
    </tr></thead><tbody>';

            $counter = 1;
            foreach ($export_rows as $user) {
                $status_class = ($user['status'] ?? 0) ? 'status-active' : 'status-inactive';
                $status_text = ($user['status'] ?? 0) ? 'Active' : 'Inactive';
                $role_class = 'role-'.($user['role'] ?? 'employee');

                $out .= '<tr>';
                $out .= '<td>'.$counter++.'</td>';
                $out .= '<td>'.htmlspecialchars($user['employee_code'] ?? '').'</td>';
                $out .= '<td>'.htmlspecialchars($user['full_name'] ?? '').'</td>';
                $out .= '<td>'.htmlspecialchars($user['email'] ?? '').'</td>';
                $out .= '<td>'.htmlspecialchars($user['phone'] ?? '').'</td>';
                $out .= '<td>'.htmlspecialchars($user['extension'] ?? '').'</td>';
                $out .= '<td>'.htmlspecialchars($user['mobile'] ?? '').'</td>';
                $out .= '<td>'.htmlspecialchars($user['department'] ?? '').'</td>';
                $out .= '<td>'.htmlspecialchars($user['position'] ?? '').'</td>';
                $out .= '<td class="'.$role_class.'">'.ucfirst($user['role'] ?? 'employee').'</td>';
                $out .= '<td class="'.$status_class.'">'.$status_text.'</td>';
                $out .= '<td>'.(! empty($user['join_date']) ? date('Y-m-d', strtotime($user['join_date'])) : '').'</td>';
                $out .= '<td>'.(! empty($user['last_login']) ? date('Y-m-d H:i', strtotime($user['last_login'])) : 'Never').'</td>';
                $out .= '<td>'.(! empty($user['created_at']) ? date('Y-m-d', strtotime($user['created_at'])) : '').'</td>';
                $out .= '</tr>';
            }

            $out .= '</tbody></table>';

            $out .= '<div class="footer">';
            $out .= '<p>This is a computer-generated report from K-Connect Intranet Portal</p>';
            $out .= '<table style="border:none; width:100%; margin-top:20px;">';
            $out .= '<tr>';
            $out .= '<td style="border:none; width:33%; text-align:center;"><hr style="width:80%;"><p style="font-size:10px;">Checked By</p></td>';
            $out .= '<td style="border:none; width:33%; text-align:center;"><hr style="width:80%;"><p style="font-size:10px;">Approved By</p></td>';
            $out .= '<td style="border:none; width:33%; text-align:center;"><hr style="width:80%;"><p style="font-size:10px;">Date</p></td>';
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

        // HANDLE USER ACTIONS (POST from the row buttons, so links/prefetching cannot change users)
        if ($request->isMethod('post') && $request->query('action') !== null && $request->query('id') !== null) {
            $id = (int) $request->query('id');

            if ($request->query('action') === 'delete' && $id != $current_admin_id) {
                try {
                    DB::delete("DELETE FROM employees WHERE id = $id");
                } catch (\Illuminate\Database\QueryException $e) {
                    report($e);
                    kc_redirect('users?msg=delete_failed');
                }
                kc_redirect('users?msg=deleted');
            }

            if ($request->query('action') === 'activate') {
                DB::update("UPDATE employees SET status = 1 WHERE id = $id");
                kc_redirect('users?msg=activated');
            }

            if ($request->query('action') === 'deactivate') {
                DB::update("UPDATE employees SET status = 0 WHERE id = $id");
                kc_redirect('users?msg=deactivated');
            }
        }

        // SEARCH & FILTER
        $search = $request->query('search') !== null ? sanitize($request->query('search')) : '';
        $status_filter = $request->query('status') !== null ? sanitize($request->query('status')) : '';

        $where_parts = [];
        $bindings = [];
        if ($search) {
            $where_parts[] = '(employee_code LIKE ? OR full_name LIKE ? OR email LIKE ?)';
            $bindings = ["%$search%", "%$search%", "%$search%"];
        }
        if ($status_filter === 'active') {
            $where_parts[] = 'status = 1';
        } elseif ($status_filter === 'inactive') {
            $where_parts[] = 'status = 0';
        }

        $where = ! empty($where_parts) ? 'WHERE '.implode(' AND ', $where_parts) : '';

        $users = DB::select("SELECT * FROM employees $where ORDER BY created_at DESC", $bindings);

        $total_users = DB::selectOne('SELECT COUNT(*) as total FROM employees')['total'] ?? 0;
        $active_users = DB::selectOne('SELECT COUNT(*) as total FROM employees WHERE status = 1')['total'] ?? 0;
        $inactive_users = $total_users - $active_users;

        return view('admin.users', compact('current_admin_id', 'search', 'status_filter', 'users', 'total_users', 'active_users', 'inactive_users'));
    }

    /** admin/user-form */
    public function userForm(Request $request)
    {
        if (! isAdminLoggedIn()) {
            kc_redirect('login');
        }

        $id = $request->query('id') !== null ? (int) $request->query('id') : 0;
        $user = null;
        $error = '';
        $success = '';

        if ($id) {
            $user = DB::selectOne("SELECT * FROM employees WHERE id = $id");
            if (! $user) {
                kc_redirect('users');
            }
        }

        if ($request->isMethod('post')) {
            $employee_code = sanitize($request->post('employee_code'));
            $full_name = sanitize($request->post('full_name'));
            $email = sanitize($request->post('email'));
            $department = sanitize($request->post('department'));
            $position = sanitize($request->post('position'));
            $role = sanitize($request->post('role'));
            $status = $request->post('status') !== null ? 1 : 0;
            $password = $request->post('password');

            if (empty($employee_code) || empty($full_name)) {
                $error = 'Employee code and full name are required';
            } else {
                $check = DB::select('SELECT id FROM employees WHERE employee_code = ?'.($id ? " AND id != $id" : ''), [$employee_code]);

                if (count($check) > 0) {
                    $error = 'Employee code already exists';
                } else {
                    $photo = $user['photo'] ?? '';
                    if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                        $uploadDir = kc_upload_path('employees/');
                        if (! file_exists($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        $file = $request->file('photo');
                        $ext = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                        $filename = 'emp_'.time().'_'.rand(1000, 9999).'.'.$ext;

                        try {
                            $file->move($uploadDir, $filename);
                            if ($photo && file_exists($uploadDir.$photo)) {
                                unlink($uploadDir.$photo);
                            }
                            $photo = $filename;
                        } catch (\Throwable $e) {
                        }
                    }

                    if ($id) {
                        $query = 'UPDATE employees SET 
                         employee_code = ?,
                         full_name = ?,
                         email = ?,
                         department = ?,
                         position = ?,
                         role = ?,
                         status = ?,
                         photo = ?';
                        $bindings = [$employee_code, $full_name, $email ?: null, $department ?: null, $position ?: null, $role, $status, $photo ?: null];

                        if (! empty($password)) {
                            $hashed = hashPassword($password);
                            $query .= ', password = ?';
                            $bindings[] = $hashed;
                        }

                        $query .= " WHERE id = $id";

                        try {
                            DB::update($query, $bindings);
                            kc_redirect('users?msg=updated');
                        } catch (\Illuminate\Database\QueryException $e) {
                            $error = 'Error updating user: '.$e->getMessage();
                        }
                    } else {
                        $hashed = hashPassword($password ?: 'Kiboko@2024');

                        try {
                            DB::insert('INSERT INTO employees (employee_code, full_name, email, department, position, role, status, photo, password) 
                         VALUES (?, ?, ?, 
                         ?, ?, 
                         ?, ?, ?, ?)', [$employee_code, $full_name, $email ?: null, $department ?: null, $position ?: null, $role, $status, $photo ?: null, $hashed]);
                            kc_redirect('users?msg=added');
                        } catch (\Illuminate\Database\QueryException $e) {
                            $error = 'Error adding user: '.$e->getMessage();
                        }
                    }
                }
            }
        }

        // Department choices: the active departments plus any value already used by an employee
        $departments = collect(DB::select('SELECT name FROM departments WHERE status = 1 ORDER BY name'))->pluck('name')
            ->merge(collect(DB::select("SELECT DISTINCT department FROM employees WHERE department IS NOT NULL AND department != ''"))->pluck('department'))
            ->map(fn ($d) => (string) $d)
            ->unique()
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        return view('admin.user-form', compact('id', 'user', 'error', 'success', 'departments'));
    }

    /**
     * admin/admin-sync-users
     *
     * GET shows the K-Connect / K-Academy comparison; the sync itself only runs on a
     * POST from the "Run Sync" button on this page.
     */
    public function adminSyncUsers(Request $request)
    {
        if (! isAdminLoggedIn()) {
            kc_redirect('login');
        }

        $kacademy_db = getKADB();
        if (! $kacademy_db) {
            return response()->view('admin.admin-sync-users', ['ka_error' => 'K-Academy database connection is not available. Check the K-Academy connection settings and try again.'], 503);
        }

        // Step 1: employees from K-Connect
        $kconnect_employees = DB::select("SELECT id, employee_code, full_name, email, phone, department, position, role, status, birth_date, join_date, photo, created_at 
          FROM employees 
          WHERE email IS NOT NULL AND email != '' 
          AND employee_code IS NOT NULL AND employee_code != ''
          ORDER BY id");
        $total = $total_kconnect = count($kconnect_employees);

        // Step 2: users from K-Academy
        [$total_kacademy, $missing_count, $existing_count] = $this->compareWithKAcademy($kacademy_db, $kconnect_employees);

        // Step 3: the sync (POST only)
        $ran_sync = false;
        $synced_count = 0;
        $updated_count = 0;
        $failed_count = 0;
        $error_log = [];
        $sync_log = [];
        if ($request->isMethod('post') && $request->has('run_sync')) {
            set_time_limit(600);
            $ran_sync = true;

            foreach ($kconnect_employees as $emp) {
                $code = $emp['employee_code'];
                $name = $emp['full_name'];
                $role = $emp['role'] ?? 'user';

                $exists = count($kacademy_db->select('SELECT id FROM users WHERE employee_code = ? OR email = ?', [$code, $emp['email']])) > 0;

                if ($exists) {
                    try {
                        $kacademy_db->update("UPDATE users SET 
                full_name = ?,
                email = ?,
                status = 'active',
                email_verified = 1,
                updated_at = NOW()
                WHERE employee_code = ? OR email = ?", [$name, $emp['email'], $code, $emp['email']]);
                        $updated_count++;
                        $sync_log[] = "✅ Updated: $code - $name";
                    } catch (\Throwable $e) {
                        report($e);
                        $failed_count++;
                        $error_log[] = "Update failed for $code: ".$e->getMessage();
                        $sync_log[] = "❌ Update failed: $code";
                    }
                } else {
                    try {
                        $kacademy_db->insert("INSERT INTO users (
                employee_code, full_name, email, password, activation_token, email_verified, role, status, created_at
            ) VALUES (?, ?, ?, ?, ?, 1, ?, 'active', NOW())", [$code, $name, $emp['email'], password_hash('default123', PASSWORD_DEFAULT), bin2hex(random_bytes(32)), $role]);
                        $synced_count++;
                        $sync_log[] = "➕ Inserted: $code - $name";
                    } catch (\Throwable $e) {
                        report($e);
                        $failed_count++;
                        $error_log[] = "Insert failed for $code: ".$e->getMessage();
                        $sync_log[] = "❌ Insert failed: $code";
                    }
                }
            }

            // counts after the sync
            [$total_kacademy, $missing_count, $existing_count] = $this->compareWithKAcademy($kacademy_db, $kconnect_employees);
        }

        // Step 4: verification
        $verify_data = null;
        $verify_error = '';
        $recent_users = [];
        try {
            $verify_data = $kacademy_db->selectOne("SELECT COUNT(*) as total, 
                 SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count,
                 SUM(CASE WHEN email_verified = 1 THEN 1 ELSE 0 END) as verified_count
                 FROM users");
            $recent_users = $kacademy_db->select('SELECT id, employee_code, full_name, email, status, email_verified, created_at FROM users ORDER BY id DESC LIMIT 20');
        } catch (\Throwable $e) {
            report($e);
            $verify_error = $e->getMessage();
        }

        return view('admin.admin-sync-users', compact(
            'total', 'total_kconnect', 'total_kacademy', 'missing_count', 'existing_count', 'ran_sync',
            'synced_count', 'updated_count', 'failed_count', 'error_log', 'sync_log',
            'verify_data', 'verify_error', 'recent_users'
        ));
    }

    /** K-Academy user count, and how many K-Connect employees are missing / already there (by email or code). */
    private function compareWithKAcademy(ConnectionInterface $kacademy_db, array $kconnect_employees): array
    {
        $ka_emails = [];
        $ka_codes = [];
        $rows = $kacademy_db->select('SELECT employee_code, email FROM users');
        foreach ($rows as $row) {
            $ka_emails[(string) $row['email']] = true;
            $ka_codes[(string) $row['employee_code']] = true;
        }

        $missing = 0;
        foreach ($kconnect_employees as $emp) {
            if (! isset($ka_emails[$emp['email']]) && ! isset($ka_codes[$emp['employee_code']])) {
                $missing++;
            }
        }

        return [count($rows), $missing, count($kconnect_employees) - $missing];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ItHelpers;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin: IT asset register, key contacts, top resources, bulk e-mails, course resources
 * (was admin/manage-assets, asset-*.php, key-contacts*.php, top-resources*.php,
 * send-emails, course-resources).
 *
 * A failing query (the old pages died with a blank "Fatal error" page) is logged and answered
 * with a readable error page (HTTP 500), or a JSON error for the AJAX lookups.
 */
class AdminAssetsController extends Controller
{
    private function legacy(callable $fn)
    {
        try {
            return $fn();
        } catch (QueryException $e) {
            report($e);
            $request = request();
            if ($request->expectsJson() || $request->query('ajax_search') !== null || $request->query('ajax_search_employee') !== null) {
                return kc_json(['error' => 'A database error occurred. Please try again.'], 500);
            }

            return response()->view('admin.notice', [
                'title' => 'Database Error',
                'heading' => 'Something went wrong',
                'text' => 'The page could not be loaded because of a database error. The error has been logged; please try again or contact IT support if it keeps happening.',
                'tone' => 'danger',
                'icon' => 'fa-database',
                'backUrl' => 'manage-assets',
                'backLabel' => 'Back to Asset Register',
            ], 500);
        }
    }

    /** Values of an ENUM column (read from the schema, cached) with the old fixed list as fallback. */
    private function assetCategories(): array
    {
        return ItHelpers::enumValues('assets', 'category', ['Laptop', 'Desktop', 'Monitor', 'Printer', 'Phone', 'Tablet', 'Networking', 'Server', 'Other IT', 'Furniture', 'Vehicle', 'Other']);
    }

    private function assetConditions(): array
    {
        return ItHelpers::enumValues('assets', 'condition_status', ['New', 'Excellent', 'Good', 'Fair', 'Poor', 'Damaged', 'Retired']);
    }

    /** Conditions an assignment can record (asset_assignments.condition_on_assignment). */
    private function assignmentConditions(): array
    {
        return ItHelpers::enumValues('asset_assignments', 'condition_on_assignment', ['New', 'Excellent', 'Good', 'Fair', 'Poor', 'Damaged']);
    }

    /** key_contacts.photo exists on some installations only. */
    private function contactsHavePhoto(): bool
    {
        return ItHelpers::hasColumn('key_contacts', 'photo');
    }

    /** Contact categories: the standard list plus any category already used. */
    private function contactCategories(): array
    {
        $categories = ['Emergency', 'Executive', 'Management', 'Finance', 'Human Resources', 'IT', 'Sales', 'Operations', 'Marketing', 'Legal', 'Other'];
        foreach (DB::select("SELECT DISTINCT category FROM key_contacts WHERE category IS NOT NULL AND category != '' ORDER BY category") as $row) {
            if (! in_array($row['category'], $categories, true)) {
                $categories[] = $row['category'];
            }
        }

        return $categories;
    }

    /** Old `$_POST['x']` read where the old code passed it through mysqli_real_escape_string (null -> ''). */
    private function p(Request $request, string $key): string
    {
        return (string) $request->post($key);
    }

    private function adminOrEmployee(): bool
    {
        return session()->has('admin_id') || session()->has('admin_username') || session()->has('employee_id');
    }

    /** admin/manage-assets */
    public function manageAssets(Request $request)
    {
        return $this->legacy(function () use ($request) {
            if (! $this->adminOrEmployee()) {
                kc_redirect('login');
            }

            $page_title = 'Asset Register - Admin';
            $success = '';
            $error = '';
            $get = $request->query();

            if (isset($get['msg'])) {
                switch ($get['msg']) {
                    case 'added': $success = 'Asset added successfully!'; break;
                    case 'updated': $success = 'Asset updated successfully!'; break;
                    case 'assigned': $success = 'Asset assigned successfully!'; break;
                    case 'deleted': $success = 'Asset deleted successfully!'; break;
                }
            }

            if (isset($get['delete']) && isset($get['id'])) {
                $id = (int) $get['id'];
                $check = DB::select("SELECT id FROM asset_assignments WHERE asset_id = ? AND status = 'Active'", [$id]);
                if (count($check) > 0) {
                    $error = 'Cannot delete asset that is currently assigned. Please return it first.';
                } else {
                    DB::delete('DELETE FROM assets WHERE id = ?', [$id]);
                    $success = 'Asset deleted successfully!';
                }
            }

            $where = 'WHERE a.status = 1';
            $bindings = [];
            $cat_filter = '';
            $cond_filter = '';

            if (isset($get['search']) && $get['search'] !== '') {
                $like = '%'.$get['search'].'%';
                $where .= ' AND (a.asset_name LIKE ? OR a.asset_code LIKE ? OR a.serial_number LIKE ? OR e.full_name LIKE ?)';
                array_push($bindings, $like, $like, $like, $like);
            }
            if (isset($get['category']) && $get['category'] !== '') {
                $cat_filter = $get['category'];
                $where .= ' AND a.category = ?';
                $bindings[] = $cat_filter;
            }
            if (isset($get['condition']) && $get['condition'] !== '') {
                $cond_filter = $get['condition'];
                $where .= ' AND a.condition_status = ?';
                $bindings[] = $cond_filter;
            }

            $result = DB::select("SELECT a.*,
          aa.employee_id as assigned_to_id,
          e.full_name as assigned_to_name,
          e.employee_code as assigned_to_code,
          aa.assigned_date
          FROM assets a
          LEFT JOIN asset_assignments aa ON a.id = aa.asset_id AND aa.status = 'Active'
          LEFT JOIN employees e ON aa.employee_id = e.id
          $where
          ORDER BY a.id DESC", $bindings);

            $totalAssets = DB::selectOne('SELECT COUNT(*) as cnt FROM assets WHERE status = 1')['cnt'] ?? 0;
            $activeAssignments = DB::selectOne("SELECT COUNT(*) as cnt FROM asset_assignments WHERE status = 'Active'")['cnt'] ?? 0;
            $availableAssets = $totalAssets - $activeAssignments;

            $categories = $this->assetCategories();
            $conditions = $this->assetConditions();

            return view('admin.manage-assets', compact('page_title', 'success', 'error', 'get', 'cat_filter', 'cond_filter',
                'result', 'totalAssets', 'activeAssignments', 'availableAssets', 'categories', 'conditions'));
        });
    }

    /** admin/asset-form */
    public function assetForm(Request $request)
    {
        return $this->legacy(function () use ($request) {
            if (! $this->adminOrEmployee()) {
                kc_redirect('login');
            }

            $id = $request->query('id') !== null ? (int) $request->query('id') : 0;
            $asset = null;
            $error = '';

            if ($id > 0) {
                $asset = DB::selectOne('SELECT * FROM assets WHERE id = ?', [$id]);
                if (! $asset) {
                    kc_redirect('manage-assets');
                }
            }

            if ($request->query('ajax_search') !== null) {
                $like = '%'.$request->query('ajax_search').'%';
                $type = $request->query('type');
                if ($type === 'employee') {
                    $query = "SELECT id, employee_code, full_name, department
                  FROM employees
                  WHERE status = 1
                  AND (full_name LIKE ? OR employee_code LIKE ? OR email LIKE ?)
                  ORDER BY full_name ASC
                  LIMIT 15";
                    $bindings = [$like, $like, $like];
                } elseif ($type === 'asset') {
                    $query = "SELECT id, asset_code, asset_name, category, serial_number, brand, model
                  FROM assets
                  WHERE status = 1
                  AND (asset_name LIKE ? OR asset_code LIKE ? OR serial_number LIKE ? OR brand LIKE ? OR model LIKE ?)
                  ORDER BY asset_name ASC
                  LIMIT 15";
                    $bindings = [$like, $like, $like, $like, $like];
                } else {
                    return kc_json([]);
                }

                return kc_json(DB::select($query, $bindings));
            }

            if ($request->isMethod('post')) {
                $asset_code = $this->p($request, 'asset_code');
                $asset_name = $this->p($request, 'asset_name');
                $category = $this->p($request, 'category');
                $brand = $this->p($request, 'brand');
                $model = $this->p($request, 'model');
                $serial_number = $this->p($request, 'serial_number');
                $ram = $this->p($request, 'ram');
                $storage = $this->p($request, 'storage');
                $processor = $this->p($request, 'processor');
                $specifications = $this->p($request, 'specifications');
                $purchase_date = ! empty($request->post('purchase_date')) ? (string) $request->post('purchase_date') : null;
                $purchase_cost = ! empty($request->post('purchase_cost')) ? (float) $request->post('purchase_cost') : null;
                $warranty_expiry = ! empty($request->post('warranty_expiry')) ? (string) $request->post('warranty_expiry') : null;
                $condition_status = $this->p($request, 'condition_status');
                $notes = ! empty($request->post('notes')) ? (string) $request->post('notes') : null;
                $status = $request->request->has('status') ? 1 : 0;

                if (empty($asset_code) || empty($asset_name) || empty($category)) {
                    $error = 'Asset Code, Name, and Category are required';
                } elseif (! in_array($category, $this->assetCategories(), true)) {
                    $error = 'Please choose a valid category';
                } elseif (! in_array($condition_status, $this->assetConditions(), true)) {
                    $error = 'Please choose a valid condition';
                } elseif (($purchase_date && strtotime($purchase_date) === false) || ($warranty_expiry && strtotime($warranty_expiry) === false)) {
                    $error = 'Please enter valid dates';
                } else {
                    $checkQuery = 'SELECT id FROM assets WHERE asset_code = ?';
                    $checkBindings = [$asset_code];
                    if ($id > 0) {
                        $checkQuery .= ' AND id != ?';
                        $checkBindings[] = $id;
                    }
                    $check = DB::select($checkQuery, $checkBindings);

                    if (count($check) > 0) {
                        $error = 'Asset code already exists! Please use a unique code.';
                    } else {
                        $image = $asset['image'] ?? '';
                        $imageValue = $image ? $image : null;

                        $file = $request->file('image');
                        if ($file && $file->isValid()) {
                            $uploadDir = kc_public_file('../uploads/assets/', 'admin');
                            if (! file_exists($uploadDir)) {
                                mkdir($uploadDir, 0777, true);
                            }
                            $ext = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                            $filename = 'asset_'.time().'_'.rand(1000, 9999).'.'.$ext;
                            try {
                                $file->move($uploadDir, $filename);
                                $moved = true;
                            } catch (\Throwable $e) {
                                $moved = false;
                            }
                            if ($moved) {
                                if ($image && file_exists($uploadDir.$image)) {
                                    unlink($uploadDir.$image);
                                }
                                $imageValue = $filename;
                            }
                        }

                        $nn = fn ($v) => ! empty($v) ? $v : null;
                        $values = [
                            $asset_code, $asset_name, $category,
                            $nn($brand), $nn($model), $nn($serial_number), $nn($ram), $nn($storage), $nn($processor), $nn($specifications),
                            $purchase_date, $purchase_cost, $warranty_expiry, $condition_status, $notes, $imageValue, $status,
                        ];

                        if ($id > 0) {
                            DB::update('UPDATE assets SET
                    asset_code=?, asset_name=?, category=?,
                    brand=?,
                    model=?,
                    serial_number=?,
                    ram=?,
                    storage=?,
                    processor=?,
                    specifications=?,
                    purchase_date=?, purchase_cost=?,
                    warranty_expiry=?, condition_status=?,
                    notes=?, image=?, status=?
                    WHERE id=?', array_merge($values, [$id]));
                        } else {
                            DB::insert('INSERT INTO assets (asset_code,asset_name,category,brand,model,serial_number,ram,storage,processor,specifications,purchase_date,purchase_cost,warranty_expiry,condition_status,notes,image,status)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', $values);
                        }

                        $msg = $id > 0 ? 'updated' : 'added';
                        kc_redirect('manage-assets?msg='.$msg);
                    }
                }
            }

            $categories = $this->assetCategories();
            $conditions = $this->assetConditions();
            $page_title = ($id > 0 ? 'Edit' : 'Add New').' Asset - Admin';

            return view('admin.asset-form', compact('id', 'asset', 'error', 'categories', 'conditions', 'page_title'));
        });
    }

    /** admin/asset-detail */
    public function assetDetail(Request $request)
    {
        return $this->legacy(function () use ($request) {
            if ($request->query('ajax_search_employee') !== null) {
                if (! $this->adminOrEmployee()) {
                    return kc_json(['error' => 'Unauthorized'], 401);
                }
                $like = '%'.$request->query('ajax_search_employee').'%';
                $exclude_id = $request->query('exclude_id') !== null ? (int) $request->query('exclude_id') : 0;
                $exclude = $exclude_id > 0 ? 'AND id != ?' : '';
                $bindings = $exclude_id > 0 ? [$exclude_id] : [];
                array_push($bindings, $like, $like, $like, $like, $like);

                $employees = DB::select("SELECT id, employee_code, full_name, department, position, email
              FROM employees
              WHERE status = 1 $exclude
              AND (full_name LIKE ?
                   OR employee_code LIKE ?
                   OR email LIKE ?
                   OR department LIKE ?
                   OR position LIKE ?)
              ORDER BY full_name ASC
              LIMIT 20", $bindings);

                return kc_json($employees);
            }

            if (! $this->adminOrEmployee()) {
                kc_redirect('login');
            }

            $id = $request->query('id') !== null ? (int) $request->query('id') : 0;

            $asset = DB::selectOne("SELECT a.*,
          aa.id as assignment_id,
          aa.assigned_date,
          aa.expected_return_date,
          aa.condition_on_assignment,
          aa.assignment_notes,
          aa.status as assignment_status,
          e.id as employee_id,
          e.full_name as assigned_to_name,
          e.employee_code as assigned_to_code,
          e.department as assigned_department,
          e.position as assigned_position,
          e.email as assigned_email,
          e.phone as assigned_phone
          FROM assets a
          LEFT JOIN asset_assignments aa ON a.id = aa.asset_id AND aa.status = 'Active'
          LEFT JOIN employees e ON aa.employee_id = e.id
          WHERE a.id = ?", [$id]);
            if (! $asset) {
                kc_redirect('manage-assets');
            }

            $assignmentHistory = DB::select('SELECT aa.*,
                e.full_name as employee_name,
                e.employee_code,
                e.department,
                e.position,
                ab.full_name as assigned_by_name,
                rb.full_name as received_by_name
                FROM asset_assignments aa
                JOIN employees e ON aa.employee_id = e.id
                LEFT JOIN employees ab ON aa.assigned_by = ab.id
                LEFT JOIN employees rb ON aa.received_by = rb.id
                WHERE aa.asset_id = ?
                ORDER BY aa.assigned_date DESC', [$id]);
            $totalAssignments = count($assignmentHistory);

            $maintenanceRecords = DB::select('SELECT * FROM asset_maintenance
                    WHERE asset_id = ?
                    ORDER BY maintenance_date DESC', [$id]);
            $totalMaintenance = count($maintenanceRecords);

            $post = $request->request;

            if ($post->has('return_asset') && $asset['assignment_id']) {
                $return_date = date('Y-m-d H:i:s');
                $return_condition = $this->p($request, 'return_condition');
                $return_notes = ! empty($request->post('return_notes')) ? (string) $request->post('return_notes') : null;
                $received_by = session('admin_id') ?? session('employee_id') ?? 1;

                DB::update("UPDATE asset_assignments SET
                   status = 'Returned',
                   actual_return_date = ?,
                   condition_on_return = ?,
                   return_notes = ?,
                   received_by = ?
                   WHERE id = ?", [$return_date, $return_condition, $return_notes, $received_by, $asset['assignment_id']]);
                DB::update('UPDATE assets SET condition_status = ? WHERE id = ?', [$return_condition, $id]);
                kc_redirect('asset-detail?id='.$id.'&msg=returned');
            }

            if ($post->has('transfer_asset') && $asset['assignment_id']
                && (! (int) $request->post('new_employee_id')
                    || (int) $request->post('new_employee_id') === (int) $asset['employee_id']
                    || ! DB::selectOne('SELECT id FROM employees WHERE id = ? AND status = 1', [(int) $request->post('new_employee_id')]))) {
                $error = 'Please search and select the active employee to transfer this asset to.';
            } elseif ($post->has('transfer_asset') && $asset['assignment_id']) {
                $new_employee_id = (int) $request->post('new_employee_id');
                $transfer_date = date('Y-m-d H:i:s');
                $transfer_condition = $this->p($request, 'transfer_condition');
                $transfer_notes = ! empty($request->post('transfer_notes')) ? (string) $request->post('transfer_notes') : null;
                $assigned_by = session('admin_id') ?? session('employee_id') ?? 1;

                DB::update("UPDATE asset_assignments SET
        status = 'Transferred',
        actual_return_date = ?,
        condition_on_return = ?,
        return_notes = 'Transferred to another employee',
        received_by = ?
        WHERE id = ?", [$transfer_date, $transfer_condition, $assigned_by, $asset['assignment_id']]);

                DB::insert("INSERT INTO asset_assignments
                (asset_id, employee_id, assigned_date, condition_on_assignment,
                 assignment_notes, assigned_by, status)
                VALUES (?, ?, ?, ?,
                ?, ?, 'Active')", [$id, $new_employee_id, $transfer_date, $transfer_condition, $transfer_notes, $assigned_by]);
                DB::update('UPDATE assets SET condition_status = ? WHERE id = ?', [$transfer_condition, $id]);
                kc_redirect('asset-detail?id='.$id.'&msg=transferred');
            }

            if ($post->has('add_maintenance')) {
                $maintenance_date = $this->p($request, 'maintenance_date');
                $maintenance_type = $this->p($request, 'maintenance_type');
                $description = $this->p($request, 'maintenance_description');
                $cost = ! empty($request->post('maintenance_cost')) ? (float) $request->post('maintenance_cost') : null;
                $performed_by = ! empty($request->post('performed_by')) ? (string) $request->post('performed_by') : null;
                $next_maintenance = ! empty($request->post('next_maintenance_date')) ? (string) $request->post('next_maintenance_date') : null;
                $maintenance_status = $this->p($request, 'maintenance_status');
                $maintenance_notes = ! empty($request->post('maintenance_notes')) ? (string) $request->post('maintenance_notes') : null;

                if (empty($maintenance_date) || empty($description)) {
                    $error = 'Date and description are required for maintenance records';
                } else {
                    DB::insert('INSERT INTO asset_maintenance
                       (asset_id, maintenance_date, maintenance_type, description, cost,
                        performed_by, next_maintenance_date, status, notes)
                       VALUES (?, ?, ?, ?,
                       ?, ?, ?, ?, ?)', [$id, $maintenance_date, $maintenance_type, $description,
                        $cost, $performed_by, $next_maintenance, $maintenance_status, $maintenance_notes]);
                    kc_redirect('asset-detail?id='.$id.'&msg=maintenance');
                }
            }

            if ($request->query('delete_maintenance') !== null && $request->query('mid') !== null) {
                $mid = (int) $request->query('mid');
                DB::delete('DELETE FROM asset_maintenance WHERE id = ? AND asset_id = ?', [$mid, $id]);
                kc_redirect('asset-detail?id='.$id.'&msg=maintenance_deleted');
            }

            $success = '';
            $error = isset($error) ? $error : '';
            if ($request->query('msg') !== null) {
                switch ($request->query('msg')) {
                    case 'returned': $success = 'Asset returned successfully!'; break;
                    case 'transferred': $success = 'Asset transferred successfully!'; break;
                    case 'maintenance': $success = 'Maintenance record added!'; break;
                    case 'maintenance_deleted': $success = 'Maintenance record deleted!'; break;
                    case 'updated': $success = 'Asset updated successfully!'; break;
                }
            }

            $conditions = $this->assignmentConditions();
            $maintenanceTypes = ItHelpers::enumValues('asset_maintenance', 'maintenance_type', ['Repair', 'Upgrade', 'Cleaning', 'Inspection', 'Software Update', 'Other']);
            $maintenanceStatuses = ItHelpers::enumValues('asset_maintenance', 'status', ['Scheduled', 'In Progress', 'Completed', 'Cancelled']);

            $totalDaysAssigned = 0;
            if ($asset['assigned_date']) {
                $assignedDate = new \DateTime($asset['assigned_date']);
                $endDate = ($asset['assignment_status'] ?? '') === 'Active' ? new \DateTime : new \DateTime($asset['actual_return_date'] ?? 'now');
                $totalDaysAssigned = $assignedDate->diff($endDate)->days;
            }

            $page_title = 'Asset Detail - '.htmlspecialchars($asset['asset_name']);

            return view('admin.asset-detail', compact('asset', 'assignmentHistory', 'totalAssignments', 'maintenanceRecords',
                'totalMaintenance', 'success', 'error', 'conditions', 'maintenanceTypes', 'maintenanceStatuses',
                'totalDaysAssigned', 'page_title'));
        });
    }

    /** admin/asset-assign */
    public function assetAssign(Request $request)
    {
        return $this->legacy(function () use ($request) {
            if ($request->query('ajax_search_employee') !== null) {
                if (! $this->adminOrEmployee()) {
                    return kc_json(['error' => 'Unauthorized'], 401);
                }
                $like = '%'.$request->query('ajax_search_employee').'%';
                $employees = DB::select('SELECT id, employee_code, full_name, department, position, email
              FROM employees
              WHERE status = 1
              AND (full_name LIKE ?
                   OR employee_code LIKE ?
                   OR email LIKE ?
                   OR department LIKE ?
                   OR position LIKE ?)
              ORDER BY full_name ASC
              LIMIT 20', [$like, $like, $like, $like, $like]);

                return kc_json($employees);
            }

            if (! $this->adminOrEmployee()) {
                kc_redirect('login');
            }

            $asset_id = $request->query('asset_id') !== null ? (int) $request->query('asset_id') : 0;
            $error = '';
            $success = '';

            $asset = DB::selectOne('SELECT * FROM assets WHERE id = ?', [$asset_id]);
            if (! $asset) {
                kc_redirect('manage-assets');
            }

            $check = DB::select("SELECT id FROM asset_assignments WHERE asset_id = ? AND status = 'Active'", [$asset_id]);
            $alreadyAssigned = count($check) > 0;
            if ($alreadyAssigned) {
                $error = 'This asset is already assigned to an employee. Please return it first.';
            }

            // (an asset that is already assigned could be assigned a second time before)
            if ($request->isMethod('post') && ! $alreadyAssigned) {
                $employee_id = (int) $request->post('employee_id');
                $assigned_date = $this->p($request, 'assigned_date');
                $expected_return = ! empty($request->post('expected_return_date')) ? (string) $request->post('expected_return_date') : null;
                $condition = $this->p($request, 'condition_on_assignment');
                $notes = ! empty($request->post('assignment_notes')) ? (string) $request->post('assignment_notes') : null;
                $assigned_by = session('admin_id') ?? session('employee_id') ?? 1;

                if (empty($employee_id)) {
                    $error = 'Please search and select an employee';
                } elseif (empty($assigned_date) || strtotime($assigned_date) === false) {
                    $error = 'Please enter a valid assignment date';
                } elseif (! in_array($condition, $this->assignmentConditions(), true)) {
                    $error = 'Please select the asset condition';
                } else {
                    $empCheck = DB::select('SELECT id FROM employees WHERE id = ? AND status = 1', [$employee_id]);
                    if (count($empCheck) === 0) {
                        $error = 'Selected employee not found or inactive';
                    } else {
                        DB::insert("INSERT INTO asset_assignments (asset_id, employee_id, assigned_date, expected_return_date,
                condition_on_assignment, assignment_notes, assigned_by, status)
                VALUES (?, ?, ?, ?,
                ?, ?, ?, 'Active')", [$asset_id, $employee_id, $assigned_date, $expected_return, $condition, $notes, $assigned_by]);
                        DB::update('UPDATE assets SET condition_status = ? WHERE id = ?', [$condition, $asset_id]);
                        kc_redirect('manage-assets?msg=assigned');
                    }
                }
            }

            $conditions = $this->assignmentConditions();

            return view('admin.asset-assign', compact('asset_id', 'error', 'success', 'asset', 'conditions'));
        });
    }

    /** admin/asset-reports */
    public function assetReports(Request $request)
    {
        return $this->legacy(function () use ($request) {
            if (! $this->adminOrEmployee()) {
                kc_redirect('login');
            }
            $page_title = 'Asset Reports - Admin';
            $get = $request->query();

            if (isset($get['export']) && $get['export'] === 'csv') {
                return $this->assetReportsCsv($get);
            }

            $categorySummary = DB::select("
    SELECT a.category,
           COUNT(a.id) as total,
           SUM(CASE WHEN aa.status = 'Active' THEN 1 ELSE 0 END) as assigned,
           SUM(CASE WHEN aa.status IS NULL OR aa.status != 'Active' THEN 1 ELSE 0 END) as available
    FROM assets a
    LEFT JOIN (
        SELECT aa1.* FROM asset_assignments aa1
        INNER JOIN (
            SELECT asset_id, MAX(id) as max_id FROM asset_assignments GROUP BY asset_id
        ) aa2 ON aa1.id = aa2.max_id
    ) aa ON a.id = aa.asset_id AND aa.status = 'Active'
    WHERE a.status = 1
    GROUP BY a.category
    ORDER BY total DESC
");

            $conditionOverview = DB::select("
    SELECT condition_status, COUNT(*) as count
    FROM assets
    WHERE status = 1
    GROUP BY condition_status
    ORDER BY FIELD(condition_status, 'New','Excellent','Good','Fair','Poor','Damaged','Retired')
");

            $employeeSummary = DB::select("
    SELECT e.full_name, e.employee_code, e.department, e.position,
           COUNT(DISTINCT aa.id) as total_assignments,
           SUM(CASE WHEN aa.status = 'Active' THEN 1 ELSE 0 END) as active_count,
           SUM(CASE WHEN aa.status = 'Returned' THEN 1 ELSE 0 END) as returned_count,
           SUM(CASE WHEN aa.status IN ('Lost','Damaged') THEN 1 ELSE 0 END) as lost_damaged
    FROM employees e
    LEFT JOIN asset_assignments aa ON e.id = aa.employee_id
    WHERE e.status = 1
    GROUP BY e.id
    HAVING total_assignments > 0
    ORDER BY active_count DESC, total_assignments DESC
    LIMIT 20
");

            $recentAssignments = DB::select('
    SELECT a.asset_code, a.asset_name, a.category, a.serial_number,
           e.full_name, e.employee_code, e.department,
           aa.assigned_date, aa.status as assignment_status,
           aa.condition_on_assignment
    FROM asset_assignments aa
    JOIN assets a ON aa.asset_id = a.id
    JOIN employees e ON aa.employee_id = e.id
    ORDER BY aa.assigned_date DESC
    LIMIT 15
');

            $trail_where = [];
            $trail_bindings = [];
            if (isset($get['trail_asset']) && $get['trail_asset'] !== '') {
                $trail_asset = '%'.sanitize($get['trail_asset']).'%';
                $trail_where[] = '(a.asset_name LIKE ? OR a.asset_code LIKE ?)';
                array_push($trail_bindings, $trail_asset, $trail_asset);
            }
            if (isset($get['trail_employee']) && $get['trail_employee'] !== '') {
                $trail_emp = '%'.sanitize($get['trail_employee']).'%';
                $trail_where[] = '(e.full_name LIKE ? OR e.employee_code LIKE ?)';
                array_push($trail_bindings, $trail_emp, $trail_emp);
            }
            if (isset($get['trail_status']) && $get['trail_status'] !== '') {
                $trail_where[] = 'aa.status = ?';
                $trail_bindings[] = sanitize($get['trail_status']);
            }

            $trail_query = 'SELECT a.asset_code, a.asset_name, a.category, a.serial_number,
               e.full_name, e.employee_code, e.department,
               aa.assigned_date, aa.actual_return_date, aa.expected_return_date,
               aa.condition_on_assignment, aa.condition_on_return, aa.status as assignment_status,
               aa.assignment_notes, aa.return_notes, aa.id as assignment_id
               FROM asset_assignments aa
               JOIN assets a ON aa.asset_id = a.id
               JOIN employees e ON aa.employee_id = e.id';
            if (! empty($trail_where)) {
                $trail_query .= ' WHERE '.implode(' AND ', $trail_where);
            }
            $trail_query .= ' ORDER BY aa.assigned_date DESC LIMIT 100';
            $assetTrail = DB::select($trail_query, $trail_bindings);

            $totalAssets = DB::selectOne('SELECT COUNT(*) as total FROM assets WHERE status = 1')['total'];
            $activeAssignments = DB::selectOne("SELECT COUNT(*) as total FROM asset_assignments WHERE status = 'Active'")['total'];
            $availableAssets = $totalAssets - $activeAssignments;
            $totalAssignmentsHistory = DB::selectOne('SELECT COUNT(*) as total FROM asset_assignments')['total'];

            $categories = $this->assetCategories();
            $conditions = $this->assetConditions();
            $assignmentStatuses = ItHelpers::enumValues('asset_assignments', 'status', ['Active', 'Returned', 'Lost', 'Damaged', 'Transferred']);

            // data for the two charts (was computed inline in the page's <script>)
            $catLabels = $catCounts = $catAssigned = $catAvailable = [];
            foreach ($categorySummary as $c) {
                $catLabels[] = $c['category'];
                $catCounts[] = $c['total'];
                $catAssigned[] = $c['assigned'];
                $catAvailable[] = $c['available'];
            }
            $categoryJson = json_encode([
                'labels' => $catLabels,
                'total' => $catCounts,
                'assigned' => $catAssigned,
                'available' => $catAvailable,
            ]);
            $condLabels = $condCounts = [];
            foreach ($conditionOverview as $co) {
                $condLabels[] = $co['condition_status'];
                $condCounts[] = $co['count'];
            }
            $conditionJson = json_encode(['labels' => $condLabels, 'data' => $condCounts]);

            return view('admin.asset-reports', compact('page_title', 'get', 'categorySummary', 'conditionOverview',
                'employeeSummary', 'recentAssignments', 'assetTrail', 'totalAssets', 'activeAssignments', 'availableAssets',
                'totalAssignmentsHistory', 'categories', 'conditions', 'assignmentStatuses', 'categoryJson', 'conditionJson'));
        });
    }

    /** CSV export branch of admin/asset-reports (?export=csv&type=...) */
    private function assetReportsCsv(array $get)
    {
        $export_type = in_array($get['type'] ?? '', ['all_assets', 'active_assignments', 'assignment_trail', 'employee_assets', 'category_summary'], true)
            ? $get['type'] : 'all_assets';
        $filename = '';
        $headers = [];
        $query = '';

        switch ($export_type) {
            case 'all_assets':
                $filename = 'asset_register_'.date('Y-m-d').'.csv';
                $headers = ['Asset Code', 'Asset Name', 'Category', 'Brand', 'Model', 'Serial Number',
                    'RAM', 'Storage', 'Processor', 'Specifications', 'Purchase Date', 'Purchase Cost',
                    'Warranty Expiry', 'Condition', 'Status', 'Assigned To', 'Employee Code', 'Assigned Date'];
                $query = "SELECT a.*, e.full_name as assigned_to, e.employee_code, aa.assigned_date
                     FROM assets a
                     LEFT JOIN (
                         SELECT aa1.* FROM asset_assignments aa1
                         INNER JOIN (
                             SELECT asset_id, MAX(id) as max_id FROM asset_assignments GROUP BY asset_id
                         ) aa2 ON aa1.id = aa2.max_id
                     ) aa ON a.id = aa.asset_id AND aa.status = 'Active'
                     LEFT JOIN employees e ON aa.employee_id = e.id
                     ORDER BY a.category, a.asset_name";
                break;

            case 'active_assignments':
                $filename = 'active_assignments_'.date('Y-m-d').'.csv';
                $headers = ['Asset Code', 'Asset Name', 'Category', 'Serial Number', 'Employee Name',
                    'Employee Code', 'Department', 'Position', 'Assigned Date', 'Expected Return',
                    'Condition', 'Assignment Notes'];
                $query = "SELECT a.asset_code, a.asset_name, a.category, a.serial_number,
                     e.full_name, e.employee_code, e.department, e.position,
                     aa.assigned_date, aa.expected_return_date, aa.condition_on_assignment,
                     aa.assignment_notes
                     FROM asset_assignments aa
                     JOIN assets a ON aa.asset_id = a.id
                     JOIN employees e ON aa.employee_id = e.id
                     WHERE aa.status = 'Active'
                     ORDER BY e.full_name";
                break;

            case 'assignment_trail':
                $filename = 'assignment_trail_'.date('Y-m-d').'.csv';
                $headers = ['Asset Code', 'Asset Name', 'Category', 'Serial Number', 'Employee Name',
                    'Employee Code', 'Department', 'Assigned Date', 'Return Date', 'Expected Return',
                    'Condition Assigned', 'Condition Returned', 'Status', 'Assignment Notes', 'Return Notes'];
                $query = 'SELECT a.asset_code, a.asset_name, a.category, a.serial_number,
                     e.full_name, e.employee_code, e.department,
                     aa.assigned_date, aa.actual_return_date, aa.expected_return_date,
                     aa.condition_on_assignment, aa.condition_on_return, aa.status,
                     aa.assignment_notes, aa.return_notes
                     FROM asset_assignments aa
                     JOIN assets a ON aa.asset_id = a.id
                     JOIN employees e ON aa.employee_id = e.id
                     ORDER BY aa.assigned_date DESC';
                break;

            case 'employee_assets':
                $filename = 'employee_asset_report_'.date('Y-m-d').'.csv';
                $headers = ['Employee Name', 'Employee Code', 'Department', 'Total Assets Assigned',
                    'Currently Active', 'Returned', 'Lost/Damaged'];
                $query = "SELECT e.full_name, e.employee_code, e.department,
                     COUNT(aa.id) as total_assets,
                     SUM(CASE WHEN aa.status = 'Active' THEN 1 ELSE 0 END) as active_assets,
                     SUM(CASE WHEN aa.status = 'Returned' THEN 1 ELSE 0 END) as returned_assets,
                     SUM(CASE WHEN aa.status IN ('Lost','Damaged') THEN 1 ELSE 0 END) as lost_damaged
                     FROM employees e
                     LEFT JOIN asset_assignments aa ON e.id = aa.employee_id
                     WHERE e.status = 1
                     GROUP BY e.id
                     HAVING total_assets > 0
                     ORDER BY active_assets DESC";
                break;

            case 'category_summary':
                $filename = 'asset_category_summary_'.date('Y-m-d').'.csv';
                $headers = ['Category', 'Total Assets', 'Assigned', 'Available', 'New/Excellent', 'Good', 'Fair', 'Poor/Damaged'];
                $query = "SELECT a.category,
                     COUNT(a.id) as total,
                     SUM(CASE WHEN aa.status = 'Active' THEN 1 ELSE 0 END) as assigned,
                     SUM(CASE WHEN aa.status IS NULL OR aa.status != 'Active' THEN 1 ELSE 0 END) as available,
                     SUM(CASE WHEN a.condition_status IN ('New','Excellent') THEN 1 ELSE 0 END) as new_excellent,
                     SUM(CASE WHEN a.condition_status = 'Good' THEN 1 ELSE 0 END) as good,
                     SUM(CASE WHEN a.condition_status = 'Fair' THEN 1 ELSE 0 END) as fair,
                     SUM(CASE WHEN a.condition_status IN ('Poor','Damaged') THEN 1 ELSE 0 END) as poor_damaged
                     FROM assets a
                     LEFT JOIN (
                         SELECT aa1.* FROM asset_assignments aa1
                         INNER JOIN (
                             SELECT asset_id, MAX(id) as max_id FROM asset_assignments GROUP BY asset_id
                         ) aa2 ON aa1.id = aa2.max_id
                     ) aa ON a.id = aa.asset_id AND aa.status = 'Active'
                     WHERE a.status = 1
                     GROUP BY a.category
                     ORDER BY total DESC";
                break;
        }

        $where_clauses = [];
        $bindings = [];
        if (isset($get['category']) && $get['category'] !== '' && $export_type === 'all_assets') {
            $where_clauses[] = 'a.category = ?';
            $bindings[] = sanitize($get['category']);
        }
        if (isset($get['condition']) && $get['condition'] !== '' && $export_type === 'all_assets') {
            $where_clauses[] = 'a.condition_status = ?';
            $bindings[] = sanitize($get['condition']);
        }
        if (! empty($where_clauses) && strpos($query, 'WHERE') === false) {
            $query = str_replace('ORDER BY', 'WHERE '.implode(' AND ', $where_clauses).' ORDER BY', $query);
        } else {
            $bindings = [];
        }

        $rows = DB::select($query, $bindings);

        $output = fopen('php://temp', 'w+');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, $headers);
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /** admin/asset-trail */
    public function assetTrail(Request $request)
    {
        return $this->legacy(function () use ($request) {
            if (! session()->has('admin_id') && ! session()->has('admin_username')) {
                kc_redirect('login');
            }

            $asset_id = $request->query('id') !== null ? (int) $request->query('id') : 0;
            $employee_id = $request->query('employee_id') !== null ? (int) $request->query('employee_id') : 0;

            $asset = DB::selectOne('SELECT * FROM assets WHERE id = ?', [$asset_id]);

            $history = DB::select('SELECT aa.*,
          e.full_name as employee_name,
          e.employee_code,
          e.department,
          ab.full_name as assigned_by_name,
          rb.full_name as received_by_name
          FROM asset_assignments aa
          JOIN employees e ON aa.employee_id = e.id
          LEFT JOIN employees ab ON aa.assigned_by = ab.id
          LEFT JOIN employees rb ON aa.received_by = rb.id
          WHERE aa.asset_id = ?
          ORDER BY aa.assigned_date DESC', [$asset_id]);

            $emp_history = [];
            $employee = null;
            if ($employee_id) {
                $emp_history = DB::select('SELECT aa.*, a.asset_name, a.asset_code, a.category, a.serial_number,
                  ab.full_name as assigned_by_name, rb.full_name as received_by_name
                  FROM asset_assignments aa
                  JOIN assets a ON aa.asset_id = a.id
                  LEFT JOIN employees ab ON aa.assigned_by = ab.id
                  LEFT JOIN employees rb ON aa.received_by = rb.id
                  WHERE aa.employee_id = ?
                  ORDER BY aa.assigned_date DESC', [$employee_id]);
                $employee = DB::selectOne('SELECT * FROM employees WHERE id = ?', [$employee_id]);
            }

            return view('admin.asset-trail', compact('asset', 'history', 'employee', 'emp_history'));
        });
    }

    /** admin/key-contacts */
    public function keyContacts(Request $request)
    {
        return $this->legacy(function () use ($request) {
            if (! $this->adminOrEmployee()) {
                kc_redirect('login');
            }

            if ($request->query('delete') !== null) {
                $id = (int) $request->query('delete');
                // key_contacts.photo is optional (not in every schema)
                $contact = $this->contactsHavePhoto() ? DB::selectOne('SELECT photo FROM key_contacts WHERE id = ?', [$id]) : null;
                if ($contact && $contact['photo']) {
                    $photoPath = kc_public_file('../uploads/contacts/'.$contact['photo'], 'admin');
                    if (file_exists($photoPath)) {
                        unlink($photoPath);
                    }
                }
                DB::delete('DELETE FROM key_contacts WHERE id = ?', [$id]);
                kc_redirect('key-contacts');
            }

            if ($request->query('toggle') !== null) {
                $id = (int) $request->query('toggle');
                $current = DB::selectOne('SELECT status FROM key_contacts WHERE id = ?', [$id]);
                $newStatus = ($current['status'] ?? null) ? 0 : 1;
                DB::update('UPDATE key_contacts SET status = ? WHERE id = ?', [$newStatus, $id]);
                kc_redirect('key-contacts');
            }

            $contacts = DB::select('SELECT * FROM key_contacts ORDER BY category, sort_order ASC');
            $get = $request->query();
            $hasPhoto = $this->contactsHavePhoto();

            return view('admin.key-contacts', compact('contacts', 'get', 'hasPhoto'));
        });
    }

    /** admin/key-contacts-add */
    public function keyContactsAdd(Request $request)
    {
        return $this->legacy(function () use ($request) {
            if (! $this->adminOrEmployee()) {
                kc_redirect('login');
            }

            if ($request->isMethod('post')) {
                $values = [];
                foreach (['name', 'position', 'department', 'email', 'phone', 'extension', 'mobile', 'bio', 'office_location', 'office_hours', 'category'] as $k) {
                    $values[] = $this->p($request, $k);
                }
                $values[] = intval($request->post('sort_order'));
                $values[] = $request->request->has('status') ? 1 : 0;

                DB::insert('INSERT INTO key_contacts (name, position, department, email, phone, extension, mobile, bio, office_location, office_hours, category, sort_order, status)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', $values);
                kc_redirect('key-contacts?success=added');
            }

            $categories = $this->contactCategories();

            return view('admin.key-contacts-add', compact('categories'));
        });
    }

    /** admin/key-contacts-edit */
    public function keyContactsEdit(Request $request)
    {
        return $this->legacy(function () use ($request) {
            if (! $this->adminOrEmployee()) {
                kc_redirect('login');
            }
            if ($request->query('id') === null) {
                kc_redirect('key-contacts');
            }

            $id = (int) $request->query('id');
            $contact = DB::selectOne('SELECT * FROM key_contacts WHERE id = ?', [$id]);
            if (! $contact) {
                kc_redirect('key-contacts');
            }

            if ($request->isMethod('post')) {
                $f = [];
                foreach (['name', 'position', 'department', 'email', 'phone', 'extension', 'mobile', 'bio', 'office_location', 'office_hours', 'category'] as $k) {
                    $f[$k] = $this->p($request, $k);
                }
                $sort_order = intval($request->post('sort_order'));
                $status = $request->request->has('status') ? 1 : 0;

                $hasPhoto = $this->contactsHavePhoto();
                $photo = $contact['photo'] ?? null;
                $file = $hasPhoto ? $request->file('photo') : null;
                if ($file && $file->isValid()) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $filename = $file->getClientOriginalName();
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    if (in_array($ext, $allowed)) {
                        $newFilename = 'contact_'.time().'_'.preg_replace('/[^a-zA-Z0-9.]/', '_', $filename);
                        $uploadDir = kc_public_file('../uploads/contacts/', 'admin');
                        if (! file_exists($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        try {
                            $file->move($uploadDir, $newFilename);
                            $moved = true;
                        } catch (\Throwable $e) {
                            $moved = false;
                        }
                        if ($moved) {
                            if (($contact['photo'] ?? null) && file_exists($uploadDir.$contact['photo'])) {
                                unlink($uploadDir.$contact['photo']);
                            }
                            $photo = $newFilename;
                        }
                    }
                }

                $values = [
                    'name' => $f['name'], 'position' => $f['position'], 'department' => $f['department'],
                    'email' => $f['email'], 'phone' => $f['phone'], 'extension' => $f['extension'], 'mobile' => $f['mobile'],
                    'bio' => $f['bio'], 'office_location' => $f['office_location'], 'office_hours' => $f['office_hours'],
                    'category' => $f['category'], 'sort_order' => $sort_order, 'status' => $status,
                ];
                if ($hasPhoto) {
                    $values['photo'] = $photo ? $photo : null;
                }
                DB::table('key_contacts')->where('id', $id)->update($values);
                kc_redirect('key-contacts?success=updated');
            }

            $categories = $this->contactCategories();
            $hasPhoto = $this->contactsHavePhoto();

            return view('admin.key-contacts-edit', compact('contact', 'categories', 'hasPhoto'));
        });
    }

    /** admin/top-resources */
    public function topResources(Request $request)
    {
        return $this->legacy(function () use ($request) {
            if (! $this->adminOrEmployee()) {
                kc_redirect('login');
            }
            $vars = [];

            if ($request->query('delete') !== null) {
                DB::delete('DELETE FROM top_resources WHERE id = ?', [(string) $request->query('delete')]);
                $vars['success'] = 'Resource deleted successfully!';
            }

            if ($request->query('toggle') !== null) {
                DB::update('UPDATE top_resources SET status = NOT status WHERE id = ?', [(string) $request->query('toggle')]);
                $vars['success'] = 'Resource status updated!';
            }

            $vars['result'] = DB::select('SELECT * FROM top_resources ORDER BY category, sort_order ASC');

            return view('admin.top-resources', $vars);
        });
    }

    private function commonIcons(): array
    {
        return [
            'fa-file-invoice-dollar' => 'Budget',
            'fa-paint-brush' => 'Branding',
            'fa-book' => 'Handbook',
            'fa-laptop' => 'IT',
            'fa-file-contract' => 'Legal',
            'fa-user-plus' => 'Onboarding',
            'fa-baby' => 'Parental',
            'fa-star' => 'Talent',
            'fa-link' => 'Link',
            'fa-file-pdf' => 'PDF',
            'fa-file-word' => 'Word',
            'fa-file-excel' => 'Excel',
            'fa-download' => 'Download',
            'fa-cloud-download-alt' => 'Cloud',
            'fa-envelope' => 'Email',
            'fa-phone' => 'Phone',
            'fa-calendar' => 'Calendar',
            'fa-clock' => 'Time',
            'fa-users' => 'Team',
            'fa-user-tie' => 'Executive',
            'fa-building' => 'Building',
            'fa-chart-line' => 'Analytics',
            'fa-chart-bar' => 'Chart',
            'fa-cog' => 'Settings',
            'fa-tools' => 'Tools',
            'fa-shield-alt' => 'Security',
            'fa-lock' => 'Lock',
            'fa-key' => 'Key',
            'fa-search' => 'Search',
            'fa-print' => 'Print',
            'fa-camera' => 'Camera',
            'fa-video' => 'Video',
            'fa-microphone' => 'Audio',
            'fa-headset' => 'Support',
            'fa-question-circle' => 'Help',
            'fa-info-circle' => 'Info',
            'fa-exclamation-triangle' => 'Warning',
            'fa-check-circle' => 'Success',
            'fa-times-circle' => 'Error',
        ];
    }

    private function topResourceValues(Request $request): array
    {
        $values = [];
        foreach (['title', 'icon', 'url', 'color', 'bg_color', 'category', 'description', 'target'] as $k) {
            $values[] = $this->p($request, $k);
        }
        $values[] = intval($request->post('sort_order'));
        $values[] = $request->request->has('status') ? 1 : 0;

        return $values;
    }

    /** admin/top-resources-add */
    public function topResourcesAdd(Request $request)
    {
        return $this->legacy(function () use ($request) {
            if (! $this->adminOrEmployee()) {
                kc_redirect('login');
            }

            if ($request->isMethod('post')) {
                DB::insert('INSERT INTO top_resources (title, icon, url, color, bg_color, category, description, target, sort_order, status)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', $this->topResourceValues($request));
                kc_redirect('top-resources?success=added');
            }

            $commonIcons = $this->commonIcons();

            return view('admin.top-resources-add', compact('commonIcons'));
        });
    }

    /** admin/top-resources-edit */
    public function topResourcesEdit(Request $request)
    {
        return $this->legacy(function () use ($request) {
            if (! $this->adminOrEmployee()) {
                kc_redirect('login');
            }
            if ($request->query('id') === null) {
                kc_redirect('top-resources');
            }

            $id = (string) $request->query('id');
            $resource = DB::selectOne('SELECT * FROM top_resources WHERE id = ?', [$id]);
            if (! $resource) {
                kc_redirect('top-resources');
            }

            if ($request->isMethod('post')) {
                DB::update('UPDATE top_resources SET
                    title = ?,
                    icon = ?,
                    url = ?,
                    color = ?,
                    bg_color = ?,
                    category = ?,
                    description = ?,
                    target = ?,
                    sort_order = ?,
                    status = ?
                    WHERE id = ?', array_merge($this->topResourceValues($request), [$id]));
                kc_redirect('top-resources?success=updated');
            }

            $commonIcons = $this->commonIcons();

            return view('admin.top-resources-edit', compact('resource', 'commonIcons'));
        });
    }

    /** The old send-emails has CRLF line endings, so its multi-line HTML literals contained "\r\n". */
    private function crlf(string $html): string
    {
        return preg_replace("/(?<!\r)\n/", "\r\n", $html);
    }

    /** admin/send-emails */
    public function sendEmails(Request $request)
    {
        return $this->legacy(function () use ($request) {
            if (! session()->has('admin_id') && ! session()->has('admin_username')) {
                session(['error' => 'Please login to access this page.']);
                kc_redirect('login');
            }

            // reports go to the logged-in admin's e-mail (admin_users.email); the login does not put it in the session
            $adminEmail = session('admin_email');
            if (empty($adminEmail) && session()->has('admin_id')) {
                $adminEmail = DB::selectOne('SELECT email FROM admin_users WHERE id = ?', [session('admin_id')])['email'] ?? null;
            }
            $adminEmail = $adminEmail ?: config('mail.from.address');

            $settings = DB::selectOne('SELECT current_quarter, current_year FROM nomination_settings WHERE status = 1 ORDER BY id DESC LIMIT 1');
            $currentQuarter = $settings['current_quarter'] ?? ceil(date('n') / 3);
            $currentYear = $settings['current_year'] ?? date('Y');

            if ($request->query('action') !== null) {
                switch ($request->query('action')) {
                    case 'get_stats':
                        $nomStats = DB::selectOne("SELECT COUNT(DISTINCT nominator_id) as total_submitted
                         FROM nominations
                         WHERE quarter = ?
                         AND year = ?
                         AND status IN ('pending', 'reviewed', 'approved')", [$currentQuarter, $currentYear]);
                        $submitted = $nomStats['total_submitted'] ?? 0;

                        $empStats = DB::selectOne('SELECT COUNT(*) as total FROM employees WHERE status = 1');
                        $totalEmployees = $empStats['total'] ?? 0;

                        $birthdayStats = DB::selectOne('SELECT COUNT(*) as total FROM birthdays WHERE MONTH(birth_date) = ? AND status = 1', [date('m')]);
                        $birthdayCount = $birthdayStats['total'] ?? 0;

                        return kc_json([
                            'success' => true,
                            'submitted' => $submitted,
                            'total_employees' => $totalEmployees,
                            'pending' => $totalEmployees - $submitted,
                            'birthday_count' => $birthdayCount,
                            'current_quarter' => $currentQuarter,
                            'current_year' => $currentYear,
                        ]);

                    case 'preview_nomination':
                        $quarter = $request->query('quarter') !== null ? (int) $request->query('quarter') : $currentQuarter;
                        $year = $request->query('year') !== null ? (int) $request->query('year') : $currentYear;

                        $sample = DB::selectOne("SELECT id, full_name, email FROM employees WHERE status = 1 AND email IS NOT NULL AND email != '' LIMIT 1");

                        if ($sample) {
                            $quarterNames = [1 => 'Q1 (January - March)', 2 => 'Q2 (April - June)', 3 => 'Q3 (July - September)', 4 => 'Q4 (October - December)'];
                            $quarterName = $quarterNames[$quarter] ?? "Q{$quarter}";
                            $nominationUrl = kcSiteUrl('rewards/nomination');

                            $content = '
                    <p>Dear <strong>'.htmlspecialchars($sample['full_name']).'</strong>,</p>
                    <p>We noticed you haven\'t submitted a nomination for the <strong>'.$quarterName.' '.$year.'</strong> Rewards & Recognition program.</p>
                    <div class="info-box">
                        <p><strong>📢 Nominations are still open!</strong></p>
                        <p>Recognize your colleagues who demonstrate excellence, teamwork, and dedication.</p>
                        <p><strong>Categories available:</strong></p>
                        <ul>
                            '.nominationCategoryListItems('<li><strong>Sanyu Spotlight</strong> – Celebrate a colleague who brings positivity and team spirit</li>
                            <li><strong>Business Catalyst</strong> – For driving business growth and innovation</li>
                            <li><strong>Priority Performer</strong> – For measurable contributions to Growth, Cost Savings, or Cash Generation</li>').'
                        </ul>
                    </div>
                    <p style="text-align: center;">
                        <a href="'.$nominationUrl.'" class="button" style="background: linear-gradient(135deg, #009ada, #76bc21);">🏆 Submit Your Nomination Now</a>
                    </p>
                ';

                            return kc_json([
                                'success' => true,
                                'preview' => itMailWrapper('Rewards & Recognition - Nomination Reminder', $this->crlf($content)),
                                'employee' => $sample['full_name'],
                            ]);
                        }

                        return kc_json(['success' => false, 'error' => 'No active employees found for preview']);

                    case 'preview_birthday':
                        $month = $request->query('month') !== null ? (int) $request->query('month') : date('m');
                        $monthName = date('F', mktime(0, 0, 0, $month, 1));

                        $birthdayPeople = DB::select('SELECT full_name, department, birth_date FROM birthdays WHERE MONTH(birth_date) = ? AND status = 1 ORDER BY DAY(birth_date) ASC LIMIT 5', [$month]);

                        $sample = DB::selectOne("SELECT id, full_name, email FROM employees WHERE status = 1 AND email IS NOT NULL AND email != '' LIMIT 1");

                        if ($sample) {
                            $birthdayHtml = '';
                            foreach ($birthdayPeople as $person) {
                                $birthdayHtml .= '
                        <div class="birthday-item">
                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #fbbf24, #f59e0b); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; flex-shrink: 0;">
                                <i class="fas fa-birthday-cake" style="color: white;"></i>
                            </div>
                            <div>
                                <strong>'.htmlspecialchars($person['full_name']).'</strong>
                                <div style="font-size: 13px; color: #64748b;">'.htmlspecialchars($person['department'] ?? '').'</div>
                            </div>
                            <div style="margin-left: auto; font-size: 13px; color: #64748b;">'.date('M d', strtotime($person['birth_date'])).'</div>
                        </div>
                    ';
                            }

                            $content = '
                    <p>Dear <strong>'.htmlspecialchars($sample['full_name']).'</strong>,</p>
                    <p>Here are our colleagues celebrating their birthdays in <strong>'.$monthName.'</strong>! 🎉</p>
                    <div class="info-box">
                        <p style="font-size: 14px; margin-bottom: 10px;"><strong>🎂 Birthday Celebrants:</strong></p>
                        '.($birthdayHtml ?: '<p style="color: #64748b;">No birthdays found for this month.</p>').'
                    </div>
                    <p style="text-align: center;">
                        <a href="'.kcSiteUrl().'" class="button">🎊 View All Birthdays</a>
                    </p>
                ';

                            return kc_json([
                                'success' => true,
                                'preview' => itMailWrapper("Birthday Celebrations - {$monthName}", $this->crlf($content)),
                                'birthday_count' => count($birthdayPeople),
                            ]);
                        }

                        return kc_json(['success' => false, 'error' => 'No active employees found for preview']);

                    default:
                        return kc_json(['success' => false, 'error' => 'Invalid action']);
                }
            }

            if ($request->isMethod('post')) {
                $redirect_url = 'send-emails';
                $post = $request->request;

                if ($post->has('send_nomination_reminder')) {
                    $quarter = $post->has('quarter') ? (int) $request->post('quarter') : $currentQuarter;
                    $year = $post->has('year') ? (int) $request->post('year') : $currentYear;

                    $result = sendBulkQuarterlyReminders(null, $quarter, $year, $adminEmail);

                    if ($result['success']) {
                        $success_message = '✅ '.$result['sent'].' nomination reminder(s) sent successfully!';
                        if ($result['failed'] > 0) {
                            $success_message .= ' '.$result['failed'].' failed.';
                        }
                        session(['success' => $success_message, 'email_result' => $result]);
                    } else {
                        session(['error' => $result['error'] ?? 'Failed to send nomination reminders.']);
                    }

                    kc_redirect($redirect_url.'#nominationForm');
                }

                if ($post->has('send_birthday_emails')) {
                    $month = $post->has('month') ? (int) $request->post('month') : date('m');

                    $result = sendBulkBirthdayNotifications(null, $month, $adminEmail);

                    if ($result['success']) {
                        $success_message = '🎂 '.$result['sent'].' birthday notification(s) sent successfully!';
                        if ($result['failed'] > 0) {
                            $success_message .= ' '.$result['failed'].' failed.';
                        }
                        session(['success' => $success_message, 'email_result' => $result]);
                    } else {
                        session(['error' => $result['error'] ?? 'Failed to send birthday notifications.']);
                    }

                    kc_redirect($redirect_url.'#birthdayForm');
                }
            }

            $stats = DB::selectOne("SELECT
                (SELECT COUNT(DISTINCT nominator_id) FROM nominations WHERE quarter = ? AND year = ? AND status IN ('pending', 'reviewed', 'approved')) as submitted,
                (SELECT COUNT(*) FROM employees WHERE status = 1) as total_employees,
                (SELECT COUNT(*) FROM birthdays WHERE MONTH(birth_date) = ? AND status = 1) as birthday_count", [$currentQuarter, $currentYear, date('m')]);
            $submitted = $stats['submitted'] ?? 0;
            $totalEmployees = $stats['total_employees'] ?? 0;
            $pending = max(0, $totalEmployees - $submitted);
            $birthdayCount = $stats['birthday_count'] ?? 0;

            $quarterNames = [
                1 => 'Q1 (January - March)',
                2 => 'Q2 (April - June)',
                3 => 'Q3 (July - September)',
                4 => 'Q4 (October - December)',
            ];
            $currentQuarterName = $quarterNames[$currentQuarter] ?? "Q{$currentQuarter}";
            $currentMonthName = date('F');

            $emailResult = session('email_result');
            session()->forget('email_result');

            return view('admin.send-emails', compact('currentQuarter', 'currentYear', 'submitted', 'totalEmployees',
                'pending', 'birthdayCount', 'currentQuarterName', 'currentMonthName', 'emailResult'));
        });
    }

    /** admin/course-resources (the courses / course_resources tables do not exist: the old page always died with a fatal error) */
    public function courseResources(Request $request)
    {
        return $this->legacy(function () use ($request) {
            if (! $this->adminOrEmployee()) {
                kc_redirect('login');
            }

            // the courses / course_resources tables are not part of the K-Connect schema
            if (! ItHelpers::hasTable('courses') || ! ItHelpers::hasTable('course_resources')) {
                return response()->view('admin.notice', [
                    'title' => 'Course Resources',
                    'heading' => 'Course resources are not available',
                    'text' => 'Course management is not set up on this system (the courses and course resources tables are not installed), so there are no course resources to manage here.',
                    'icon' => 'fa-graduation-cap',
                ]);
            }

            $course_id = $request->query('id') !== null ? (int) $request->query('id') : 0;

            $course = DB::selectOne('SELECT * FROM courses WHERE id = ?', [$course_id]);
            if (! $course) {
                kc_redirect('dashboard');
            }

            if ($request->isMethod('post') && $request->request->has('add_resource')) {
                $title = $this->p($request, 'title');
                $resource_type = $this->p($request, 'resource_type');
                $resource_url = $this->p($request, 'resource_url');
                $description = $this->p($request, 'description');
                $sort_order = (int) $request->post('sort_order');

                $file = $request->file('pdf_file');
                if ($resource_type == 'pdf' && $file && $file->isValid()) {
                    $upload_dir = kc_public_file('../uploads/resources/', 'admin');
                    if (! file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $file_path = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($file->getClientOriginalName()));
                    $file->move($upload_dir, $file_path);
                    $resource_url = 'uploads/resources/'.$file_path;
                }

                // (a failing INSERT threw in the old code too: mysqli exception mode)
                DB::insert('INSERT INTO course_resources (course_id, title, resource_type, resource_url, description, sort_order)
            VALUES (?, ?, ?, ?, ?, ?)', [$course_id, $title, $resource_type, $resource_url, $description, $sort_order]);
                session(['message' => 'Resource added successfully!', 'message_type' => 'success']);
                kc_redirect("course-resources?id=$course_id");
            }

            if ($request->query('delete') !== null) {
                $resource_id = (int) $request->query('delete');
                DB::delete('DELETE FROM course_resources WHERE id = ?', [$resource_id]);
                session(['message' => 'Resource deleted successfully!', 'message_type' => 'success']);
                kc_redirect("course-resources?id=$course_id");
            }

            $resources = DB::select('SELECT * FROM course_resources WHERE course_id = ? ORDER BY sort_order ASC', [$course_id]);

            return view('admin.course-resources', compact('course_id', 'course', 'resources'));
        });
    }
}

<?php

namespace App\Http\Controllers;

use App\Support\OrgChart;
use App\Support\PeopleMailer;
use App\Support\PeopleRateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/** Employee account pages, directory and org views ("people" area). */
class PeopleController extends Controller
{
    /** register.php: employee self-registration */
    public function register(Request $request)
    {
        // Redirect if already logged in
        if (isLoggedIn()) {
            kc_redirect('./');
        }

        $error = '';
        $success = '';
        $metaRefresh = '';

        // Get companies for dropdown
        $companies = DB::select('SELECT id, name FROM companies WHERE status = 1 ORDER BY name');

        // Handle registration
        if ($request->isMethod('post') && $request->has('register')) {
            $employee_code = sanitize($request->input('employee_code'));
            $full_name = sanitize($request->input('full_name'));
            $email = sanitize($request->input('email'));
            $phone = sanitize($request->input('phone') ?? '');
            $extension = sanitize($request->input('extension') ?? '');
            $mobile = sanitize($request->input('mobile') ?? '');
            $department = sanitize($request->input('department') ?? '');
            $company_id = ! empty($request->input('company_id')) ? intval($request->input('company_id')) : null;
            $position = sanitize($request->input('position') ?? '');
            $birth_date = sanitize($request->input('birth_date') ?? '');
            $join_date = sanitize($request->input('join_date') ?? '');
            $emergency_contact = sanitize($request->input('emergency_contact') ?? '');
            $emergency_phone = sanitize($request->input('emergency_phone') ?? '');
            $address = sanitize($request->input('address') ?? '');
            $notes = sanitize($request->input('notes') ?? '');
            $role = sanitize($request->input('role') ?? 'employee');

            // File upload handling
            $photo = '';
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $file = $request->file('photo');
                $target_dir = kc_upload_path('employees/');
                if (! file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $file_extension = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $max_size = 2 * 1024 * 1024; // 2MB

                if (! in_array($file_extension, $allowed)) {
                    $error = 'Invalid photo format. Only JPG, PNG, GIF, WEBP allowed.';
                } elseif ($file->getSize() > $max_size) {
                    $error = 'Photo size must be less than 2MB.';
                } else {
                    $new_filename = 'emp_'.time().'_'.uniqid().'.'.$file_extension;
                    try {
                        $file->move($target_dir, $new_filename);
                        $photo = $new_filename;
                    } catch (\Throwable $e) {
                        $error = 'Failed to upload photo.';
                    }
                }
            }

            // Validate inputs
            if (empty($error)) {
                // Required fields validation
                $required_fields = [
                    'employee_code' => 'Employee Code',
                    'full_name' => 'Full Name',
                    'email' => 'Email Address',
                    'phone' => 'Phone Number',
                    'department' => 'Department',
                    'position' => 'Position',
                    'birth_date' => 'Birth Date',
                    'join_date' => 'Join Date',
                ];

                foreach ($required_fields as $field => $label) {
                    if (empty($request->post($field))) {
                        $error = "Please enter your $label";
                        break;
                    }
                }

                // (the old page fell through to the insert even when a required field was missing)
                if (! empty($error)) {
                    // show the missing-field message
                } elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Please enter a valid email address';
                } elseif (! $this->registerValidDate($birth_date) || ! $this->registerValidDate($join_date)) {
                    $error = 'Please enter valid birth and join dates';
                } else {
                    // Check if employee code already exists
                    $check_code = DB::select('SELECT id FROM employees WHERE employee_code = ?', [$employee_code]);
                    if (count($check_code) > 0) {
                        $error = 'Employee code already exists. Please use a different code.';
                    } else {
                        // Check if email already exists
                        $check_email = DB::select('SELECT id FROM employees WHERE email = ?', [$email]);
                        if (count($check_email) > 0) {
                            $error = 'Email already registered. Please use a different email.';
                        } else {
                            // Generate random password
                            $password = $this->registerRandomPassword(10);
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                            // Insert new employee with ALL fields
                            $insert = 'INSERT INTO employees (
                                employee_code,
                                full_name,
                                email,
                                phone,
                                extension,
                                mobile,
                                password,
                                department,
                                company_id,
                                position,
                                birth_date,
                                join_date,
                                emergency_contact,
                                emergency_phone,
                                address,
                                notes,
                                photo,
                                role,
                                is_verified,
                                status,
                                registered_at
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1, NOW())';

                            try {
                                $inserted = DB::insert($insert, [
                                    $employee_code,
                                    $full_name,
                                    $email,
                                    $phone,
                                    $extension,
                                    $mobile,
                                    $hashed_password,
                                    $department,
                                    $company_id,
                                    $position,
                                    $birth_date,
                                    $join_date,
                                    $emergency_contact,
                                    $emergency_phone,
                                    $address,
                                    $notes,
                                    $photo,
                                    $role,
                                ]);
                            } catch (\Illuminate\Database\QueryException $e) {
                                report($e);
                                $inserted = false;
                            }

                            if ($inserted) {
                                $new_id = DB::getPdo()->lastInsertId();

                                // SYNC TO K-ACADEMY
                                $syncResult = syncUserToKAcademy($employee_code, $full_name, $email, $password, $role);

                                if ($syncResult['success']) {
                                    error_log("[Registration] User synced to K-Academy: $employee_code - Action: {$syncResult['action']}");
                                } else {
                                    error_log("[Registration] Failed to sync user to K-Academy: $employee_code - ".$syncResult['message']);
                                    // Continue registration even if K-Academy sync fails
                                }

                                // Send welcome email with credentials
                                if (PeopleMailer::sendWelcomeEmail($email, $full_name, $employee_code, $password)) {
                                    $success = 'Registration successful! Your login credentials have been sent to your email.';

                                    // Log the registration
                                    $this->logRegistration($request, $employee_code, $full_name, $email);

                                    // Auto-login after registration
                                    $request->session()->regenerate();
                                    session([
                                        'employee_id' => $new_id,
                                        'employee_code' => $employee_code,
                                        'employee_name' => $full_name,
                                        'employee_role' => $role,
                                        'employee_photo' => $photo,
                                    ]);

                                    // Redirect to dashboard after 3 seconds
                                    $metaRefresh = '<meta http-equiv="refresh" content="3;url=./">';
                                } else {
                                    // Rollback - delete the inserted record if email fails
                                    DB::delete('DELETE FROM employees WHERE id = ?', [$new_id]);

                                    $error = 'Failed to send welcome email. Please try again or contact IT support.';
                                }
                            } else {
                                $error = 'Registration failed. Please try again.';
                            }
                        }
                    }
                }
            }
        }

        // values put back into the form (array values from a tampered form are dropped)
        $post = array_filter($request->post(), 'is_string');

        return view('register', compact('error', 'success', 'companies', 'post', 'metaRefresh'));
    }

    /** A Y-m-d date the employees table accepts. */
    private function registerValidDate($value): bool
    {
        $d = \DateTime::createFromFormat('!Y-m-d', (string) $value);

        return $d !== false && $d->format('Y-m-d') === $value;
    }

    /** register generateRandomPassword() */
    private function registerRandomPassword($length = 10)
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $password;
    }

    /** register logRegistration() */
    private function logRegistration(Request $request, $employee_code, $full_name, $email)
    {
        $ip = $request->ip() ?? 'unknown';
        $user_agent = $request->userAgent() ?? '';

        DB::insert("INSERT INTO system_logs (user_type, user_id, username, full_name, action, page_accessed, ip_address, user_agent, details)
              VALUES ('employee', 0, ?, ?, 'registration', 'register', ?, ?, 'Employee self-registration completed')",
            [$employee_code, $full_name, $ip, $user_agent]);
    }

    /** forgot-password.php: e-mail a new random password */
    public function forgotPassword(Request $request)
    {
        $error = '';
        $success = '';

        if (isLoggedIn()) {
            kc_redirect('./');
        }

        if ($request->isMethod('post')) {
            $employee_code = sanitize($request->input('employee_code'));

            if (empty($employee_code)) {
                $error = 'Please enter your employee code';
            } elseif (! PeopleRateLimiter::checkPasswordResetLimit($employee_code)) {
                // max 3 reset requests per hour per employee code / IP (password_reset_attempts)
                $error = 'Too many password reset requests. Please try again in an hour or contact IT support.';
            } else {
                // Get employee details
                $row = DB::selectOne('SELECT id, employee_code, full_name, email, password FROM employees WHERE employee_code = ? AND status = 1', [$employee_code]);

                if ($row) {
                    if (empty($row['email'])) {
                        $error = 'No email address registered. Please contact HR.';
                    } else {
                        // Generate new password
                        $new_password = $this->forgotRandomPassword();
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);


                        // (passwords are never written to the log)
                        DB::update('UPDATE employees SET password = ? WHERE id = ?', [$hashed_password, $row['id']]);
                        $updated = true;

                        if ($updated) {
                            // Verify the update worked
                            $verify_row = DB::selectOne('SELECT password FROM employees WHERE id = ?', [$row['id']]);


                            if (password_verify($new_password, $verify_row['password'])) {

                                // Send email
                                $email_sent = PeopleMailer::sendPasswordResetEmail($row, $new_password);

                                if ($email_sent) {
                                    $success = 'A new password has been sent to your email address.';

                                    // Log the reset
                                    $this->logPasswordReset($request, $row['id'], $row['employee_code'], $row['full_name']);
                                } else {
                                    $error = 'Email could not be sent. Please contact IT support.';
                                    // Rollback password change
                                    DB::update('UPDATE employees SET password = ? WHERE id = ?', [$row['password'], $row['id']]);
                                }
                            } else {
                                $error = 'Technical error. Please contact IT support.';
                            }
                        } else {
                            $error = 'Database error. Please try again.';
                        }
                    }
                } else {
                    $error = 'Employee code not found or account inactive.';
                }
            }
        }

        return view('forgot-password', compact('error', 'success'));
    }

    /** forgot-password generateRandomPassword() (alphanumeric only) */
    private function forgotRandomPassword($length = 10)
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $password;
    }

    /** forgot-password logPasswordReset() */
    private function logPasswordReset(Request $request, $employee_id, $employee_code, $full_name)
    {
        $ip = $request->ip() ?? 'unknown';
        $user_agent = $request->userAgent() ?? '';

        DB::insert("INSERT INTO system_logs (user_type, user_id, username, full_name, action, page_accessed, ip_address, user_agent, details)
              VALUES ('employee', ?, ?, ?, 'password_reset', 'forgot-password', ?, ?, 'Password reset completed')",
            [$employee_id, $employee_code, $full_name, $ip, $user_agent]);
    }

    /** change-password.php: my profile, photo and password */
    public function changePassword(Request $request)
    {
        // Redirect to login if not logged in
        if (! isLoggedIn()) {
            kc_redirect('login');
        }

        $error = '';
        $success = '';
        $profile_error = '';
        $profile_success = '';

        // Get employee data
        $employee_id = session('employee_id');
        $employee = DB::selectOne('SELECT e.*, c.name as company_name, c.code as company_code
          FROM employees e
          LEFT JOIN companies c ON e.company_id = c.id
          WHERE e.id = ? AND e.status = 1', [$employee_id]);

        if (! $employee) {
            kc_redirect('logout');
        }

        // Handle profile update
        if ($request->isMethod('post') && $request->has('update_profile')) {
            // Stored as typed (the old page saved mysqli_real_escape_string() text through a prepared
            // statement, so every save added backslashes in front of quotes)
            $field = fn ($name) => is_string($request->input($name)) ? trim($request->input($name)) : '';
            $full_name = $field('full_name');
            $email = $field('email');
            $phone = $field('phone');
            $extension = $field('extension');
            $mobile = $field('mobile');
            $emergency_contact = $field('emergency_contact');
            $emergency_phone = $field('emergency_phone');
            $address = $field('address');
            $notes = $field('notes');

            // Validate email uniqueness (excluding current user)
            $check_email = DB::select('SELECT id FROM employees WHERE email = ? AND id != ?', [$email, $employee_id]);
            if ($full_name === '') {
                $profile_error = 'Please enter your full name.';
            } elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $profile_error = 'Please enter a valid email address.';
            } elseif ($check_email && count($check_email) > 0) {
                $profile_error = 'Email already exists in the system!';
            } else {
                $ok = true;
                try {
                    DB::update('UPDATE employees SET
                    full_name = ?,
                    email = ?,
                    phone = ?,
                    extension = ?,
                    mobile = ?,
                    emergency_contact = ?,
                    emergency_phone = ?,
                    address = ?,
                    notes = ?
                    WHERE id = ?', [$full_name, $email, $phone, $extension, $mobile, $emergency_contact, $emergency_phone, $address, $notes, $employee_id]);
                } catch (\Throwable $e) {
                    $ok = false;
                }

                if ($ok) {
                    // Update session
                    session(['employee_name' => $full_name]);
                    $profile_success = 'Profile updated successfully!';
                    // Refresh employee data
                    $employee['full_name'] = $full_name;
                    $employee['email'] = $email;
                    $employee['phone'] = $phone;
                    $employee['extension'] = $extension;
                    $employee['mobile'] = $mobile;
                    $employee['emergency_contact'] = $emergency_contact;
                    $employee['emergency_phone'] = $emergency_phone;
                    $employee['address'] = $address;
                    $employee['notes'] = $notes;
                } else {
                    $profile_error = 'Error updating profile. Please try again.';
                }
            }
        }

        // Handle photo upload
        if ($request->isMethod('post') && $request->has('upload_photo')) {
            if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
                $file = $request->file('profile_photo');
                $target_dir = kc_upload_path('employees/');
                if (! file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $file_extension = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (! in_array($file_extension, $allowed)) {
                    $profile_error = 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed.';
                } else {
                    $new_filename = 'emp_'.time().'_'.uniqid().'.'.$file_extension;

                    // Delete old photo if exists
                    if ($employee['photo']) {
                        $old_path = $target_dir.$employee['photo'];
                        if (file_exists($old_path)) {
                            unlink($old_path);
                        }
                    }

                    $moved = true;
                    try {
                        $file->move($target_dir, $new_filename);
                    } catch (\Throwable $e) {
                        $moved = false;
                    }

                    if ($moved) {
                        $ok = true;
                        try {
                            DB::update('UPDATE employees SET photo = ? WHERE id = ?', [$new_filename, $employee_id]);
                        } catch (\Throwable $e) {
                            $ok = false;
                        }

                        if ($ok) {
                            session(['employee_photo' => $new_filename]);
                            $employee['photo'] = $new_filename;
                            $profile_success = 'Profile photo updated successfully!';
                        } else {
                            $profile_error = 'Error updating photo. Please try again.';
                        }
                    } else {
                        $profile_error = 'Failed to upload photo.';
                    }
                }
            } else {
                $profile_error = 'Please select a photo to upload.';
            }
        }

        // Handle remove photo
        if ($request->isMethod('post') && $request->has('remove_photo')) {
            if ($employee['photo']) {
                $old_path = kc_upload_path('employees/').$employee['photo'];
                if (file_exists($old_path)) {
                    unlink($old_path);
                }

                $ok = true;
                try {
                    DB::update('UPDATE employees SET photo = NULL WHERE id = ?', [$employee_id]);
                } catch (\Throwable $e) {
                    $ok = false;
                }

                if ($ok) {
                    session(['employee_photo' => null]);
                    $employee['photo'] = null;
                    $profile_success = 'Profile photo removed successfully!';
                } else {
                    $profile_error = 'Error removing photo. Please try again.';
                }
            }
        }

        // Handle password change
        if ($request->isMethod('post') && $request->has('change_password')) {
            $current_password = is_string($request->input('current_password')) ? $request->input('current_password') : '';
            $new_password = is_string($request->input('new_password')) ? $request->input('new_password') : '';
            $confirm_password = is_string($request->input('confirm_password')) ? $request->input('confirm_password') : '';

            // Validate inputs
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $error = 'All password fields are required';
            } elseif ($new_password !== $confirm_password) {
                $error = 'New passwords do not match';
            } elseif (strlen($new_password) < 6) {
                $error = 'New password must be at least 6 characters long';
            } else {
                // Verify current password
                $row = DB::selectOne('SELECT password FROM employees WHERE id = ? AND status = 1', [$employee_id]);

                if ($row) {
                    if (verifyPassword($current_password, $row['password'])) {
                        // Update to new password
                        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                        $ok = true;
                        try {
                            DB::update('UPDATE employees SET password = ? WHERE id = ?', [$hashed_password, $employee_id]);
                        } catch (\Throwable $e) {
                            $ok = false;
                        }

                        if ($ok) {
                            $success = 'Password changed successfully!';
                        } else {
                            $error = 'Error updating password. Please try again.';
                        }
                    } else {
                        $error = 'Current password is incorrect';
                    }
                } else {
                    $error = 'Employee not found';
                }
            }
        }

        return view('change-password', compact('error', 'success', 'profile_error', 'profile_success', 'employee'));
    }

    /** employee-directory */
    public function employeeDirectory(Request $request)
    {
        // Redirect to login if not logged in
        if (! isLoggedIn()) {
            kc_redirect('login');
        }

        // Get all active employees with company info
        $employees = DB::select('
    SELECT e.*, c.name as company_name, c.code as company_code, c.logo as company_logo
    FROM employees e
    LEFT JOIN companies c ON e.company_id = c.id
    WHERE e.status = 1
    ORDER BY c.name, e.full_name
');

        // Get companies for filter
        $companies = DB::select('SELECT * FROM companies WHERE status = 1 ORDER BY name');

        // Get departments for filter - using DISTINCT from employees table
        $departments = DB::select("SELECT DISTINCT department FROM employees WHERE department IS NOT NULL AND department != '' AND status = 1 ORDER BY department");

        // Employees of each company (the old page queried these inside the template loop)
        $companyEmployeesMap = [];
        foreach ($companies as $company) {
            $companyEmployeesMap[$company['id']] = DB::select('
                    SELECT e.*, c.name as company_name
                    FROM employees e
                    LEFT JOIN companies c ON e.company_id = c.id
                    WHERE e.status = 1 AND e.company_id = ?
                    ORDER BY e.full_name
                ', [$company['id']]);
        }

        return view('employee-directory', compact('employees', 'companies', 'departments', 'companyEmployeesMap'));
    }

    /** directors.php: leadership page (data cached for 10 minutes, as the old cache/directors_page.cache) */
    public function directors(Request $request)
    {
        // Redirect to login if not logged in
        if (! isLoggedIn()) {
            kc_redirect('login');
        }

        $cached = Cache::get('directors_page');
        if (is_array($cached) && isset($cached['timestamp'])) {
            $boardDirectors = $cached['boardDirectors'];
            $executiveDirector = $cached['executiveDirector'];
            $groupCEO = $cached['groupCEO'];
            $leadership = $cached['leadership'];
        } else {
            $allQuery = "
        SELECT e.id, e.full_name, e.position, e.photo, e.company_id,
               c.name as company_name, c.code as company_code,
               CASE
                   WHEN e.company_id = 6 AND e.position NOT LIKE '%Executive Director%' THEN 'board'
                   WHEN e.company_id = 6 AND e.position LIKE '%Executive Director%' THEN 'executive'
                   WHEN e.company_id = 1 AND (e.position LIKE '%Group Ceo%' OR e.position LIKE '%CEO%') THEN 'ceo'
                   WHEN e.company_id = 1 THEN 'leadership'
                   ELSE 'other'
               END as member_type,
               CASE
                   WHEN e.position LIKE '%Chairman%' THEN 1
                   WHEN e.position LIKE '%Managing Director%' THEN 2
                   WHEN e.position LIKE '%CFO%' THEN 3
                   WHEN e.position LIKE '%Head%' THEN 4
                   WHEN e.position LIKE '%Manager%' THEN 5
                   ELSE 6
               END as sort_priority
        FROM employees e
        LEFT JOIN companies c ON e.company_id = c.id
        WHERE e.status = 1
            AND e.company_id IN (1, 6)
        ORDER BY
            FIELD(member_type, 'board', 'executive', 'ceo', 'leadership'),
            sort_priority,
            e.full_name
        LIMIT 100
    ";

            $boardDirectors = [];
            $executiveDirector = [];
            $groupCEO = [];
            $leadership = [];

            foreach (DB::select($allQuery) as $row) {
                // Pre-process photo path
                $row['photo_url'] = ! empty($row['photo']) ? asset('uploads/employees/'.$row['photo']) : null;

                // Sanitize for JSON
                $row['full_name_safe'] = htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8');
                $row['position_safe'] = htmlspecialchars($row['position'] ?? '', ENT_QUOTES, 'UTF-8');
                $row['company_name_safe'] = htmlspecialchars($row['company_name'] ?? '', ENT_QUOTES, 'UTF-8');

                switch ($row['member_type']) {
                    case 'board':
                        $boardDirectors[] = $row;
                        break;
                    case 'executive':
                        $executiveDirector[] = $row;
                        break;
                    case 'ceo':
                        $groupCEO[] = $row;
                        break;
                    case 'leadership':
                        $leadership[] = $row;
                        break;
                }
            }

            Cache::put('directors_page', [
                'boardDirectors' => $boardDirectors,
                'executiveDirector' => $executiveDirector,
                'groupCEO' => $groupCEO,
                'leadership' => $leadership,
                'timestamp' => time(),
            ], 600); // 10 minutes cache
        }

        // Pre-compute counts for display logic
        $hasBoardDirectors = ! empty($boardDirectors);
        $hasExecutiveDirector = ! empty($executiveDirector);
        $hasGroupCEO = ! empty($groupCEO);
        $hasLeadership = ! empty($leadership);

        // User session data (pre-fetched for template)
        $isLoggedIn = isLoggedIn();
        $employeePhoto = session('employee_photo') ?? '';
        $employeeName = session('employee_name') ?? '';
        $employeeCode = session('employee_code') ?? '';

        return view('directors', compact(
            'boardDirectors', 'executiveDirector', 'groupCEO', 'leadership',
            'hasBoardDirectors', 'hasExecutiveDirector', 'hasGroupCEO', 'hasLeadership',
            'isLoggedIn', 'employeePhoto', 'employeeName', 'employeeCode'
        ));
    }

    /**
     * orgchart_view: staff organization chart (chart view + list view, search, filters,
     * details panel, deep link ?emp=<employee id>). The data is embedded in the page.
     */
    public function orgchartView(Request $request)
    {
        if (! isLoggedIn() && ! isAdminLoggedIn()) {
            kc_redirect(url('login'));
        }

        $employeeName = session('employee_name') ?? '';
        $employeeCode = session('employee_code') ?? '';
        $employeePhoto = session('employee_photo') ?? '';
        $isAdminUser = isAdmin();
        $orgNodes = OrgChart::nodes();
        $levels = OrgChart::LEVELS;

        return view('orgchart_view', compact('employeeName', 'employeeCode', 'employeePhoto', 'isAdminUser', 'orgNodes', 'levels'));
    }

    /** ajax-company-view.php: company details fragment */
    public function ajaxCompanyView(Request $request)
    {
        if (! $request->request->has('company_id')) {
            kc_exit('Company ID required');
        }

        $company_id = (int) $request->post('company_id');

        // Get company details
        $company = DB::selectOne('SELECT * FROM companies WHERE id = ?', [$company_id]);

        if (! $company) {
            kc_exit('Company not found');
        }

        // Get departments for this company
        $departments = DB::select('SELECT d.*,
                                    (SELECT employee_name FROM organization_chart WHERE id = d.head_employee_id) as head_name,
                                    COUNT(oc.id) as employee_count
                                    FROM departments d
                                    LEFT JOIN organization_chart oc ON (oc.department_id = d.id OR oc.sub_department_id = d.id) AND oc.status = 1
                                    WHERE d.company_id = ? AND d.status = 1 AND d.parent_id IS NULL
                                    GROUP BY d.id
                                    ORDER BY d.level, d.sort_order, d.name', [$company_id]);

        // Get employees for this company
        $employees = DB::select('SELECT oc.*, d.name as department_name
                                  FROM organization_chart oc
                                  LEFT JOIN departments d ON oc.department_id = d.id
                                  WHERE oc.company_id = ? AND oc.status = 1
                                  ORDER BY oc.level, oc.sort_order
                                  LIMIT 20', [$company_id]);

        // Get statistics
        $stats = DB::selectOne("SELECT
    COUNT(DISTINCT oc.id) as total_employees,
    COUNT(DISTINCT d.id) as total_departments,
    COUNT(DISTINCT CASE WHEN oc.organization_type = 'Sales' THEN oc.id END) as sales_employees,
    COUNT(DISTINCT CASE WHEN oc.organization_type = 'Support' THEN oc.id END) as support_employees
    FROM companies c
    LEFT JOIN departments d ON d.company_id = c.id AND d.status = 1
    LEFT JOIN organization_chart oc ON oc.company_id = c.id AND oc.status = 1
    WHERE c.id = ?", [$company_id]);

        return view('ajax-company-view', compact('company_id', 'company', 'departments', 'employees', 'stats'));
    }

    /** ajax-department-view.php: department details fragment */
    public function ajaxDepartmentView(Request $request)
    {
        if (! $request->request->has('department_id')) {
            kc_exit('Department ID required');
        }

        $dept_id = (int) $request->post('department_id');

        // Get department details
        $dept = DB::selectOne('SELECT d.*, c.name as company_name, c.id as company_id,
                                                (SELECT employee_name FROM organization_chart WHERE id = d.head_employee_id) as head_name,
                                                (SELECT position FROM organization_chart WHERE id = d.head_employee_id) as head_position,
                                                (SELECT photo FROM organization_chart WHERE id = d.head_employee_id) as head_photo
                                                FROM departments d
                                                LEFT JOIN companies c ON d.company_id = c.id
                                                WHERE d.id = ?', [$dept_id]);

        if (! $dept) {
            kc_exit('Department not found');
        }

        // Get sub-departments
        $subDepts = DB::select('SELECT d.*,
                                 (SELECT employee_name FROM organization_chart WHERE id = d.head_employee_id) as head_name
                                 FROM departments d
                                 WHERE d.parent_id = ? AND d.status = 1
                                 ORDER BY d.level, d.sort_order, d.name', [$dept_id]);

        // Get employees in this department
        $employees = DB::select('SELECT oc.*,
                                  (SELECT employee_name FROM organization_chart WHERE id = oc.parent_id) as reports_to
                                  FROM organization_chart oc
                                  WHERE (oc.department_id = ? OR oc.sub_department_id = ?) AND oc.status = 1
                                  ORDER BY oc.level, oc.sort_order', [$dept_id, $dept_id]);

        // Get parent department info
        $parentDept = null;
        if ($dept['parent_id']) {
            $parentDept = DB::selectOne('SELECT name FROM departments WHERE id = ?', [$dept['parent_id']]);
        }

        // Get statistics
        $stats = DB::selectOne("SELECT
    COUNT(*) as total_employees,
    COUNT(DISTINCT CASE WHEN organization_type = 'Sales' THEN id END) as sales_count,
    COUNT(DISTINCT CASE WHEN organization_type = 'Support' THEN id END) as support_count
    FROM organization_chart
    WHERE (department_id = ? OR sub_department_id = ?) AND status = 1", [$dept_id, $dept_id]);

        return view('ajax-department-view', compact('dept_id', 'dept', 'subDepts', 'employees', 'parentDept', 'stats'));
    }

    /** ajax-leadership-view.php: leadership fragment */
    public function ajaxLeadershipView(Request $request)
    {
        $level = $request->request->has('level') ? (int) $request->post('level') : 0;
        $all = $request->request->has('all') ? true : false;

        $where = 'WHERE oc.level <= 3 AND oc.status = 1';
        if ($level > 0) {
            $where .= " AND oc.level = $level";
        }

        // Get leadership by level
        $leaders = DB::select("SELECT oc.*, c.name as company_name,
          (SELECT employee_name FROM organization_chart WHERE id = oc.parent_id) as reports_to
          FROM organization_chart oc
          LEFT JOIN companies c ON oc.company_id = c.id
          $where
          ORDER BY oc.level, oc.sort_order, oc.employee_name");

        // Get level info
        $levelNames = [
            1 => 'Board of Directors',
            2 => 'Executive Directors',
            3 => 'Chief Executive Officers',
        ];

        $levelDescriptions = [
            1 => 'Strategic guidance and governance',
            2 => 'Executive management and oversight',
            3 => 'Operational leadership and execution',
        ];

        // Leaders of each level (the old page queried these inside the template loop)
        $levelLeadersByLevel = [];
        if (count($leaders) > 0 && ($all || $level == 0)) {
            for ($lvl = 1; $lvl <= 3; $lvl++) {
                $levelLeadersByLevel[$lvl] = DB::select('SELECT oc.*, c.name as company_name
                                                     FROM organization_chart oc
                                                     LEFT JOIN companies c ON oc.company_id = c.id
                                                     WHERE oc.level = ? AND oc.status = 1
                                                     ORDER BY oc.sort_order, oc.employee_name', [$lvl]);
            }
        }

        return view('ajax-leadership-view', compact('level', 'all', 'leaders', 'levelNames', 'levelDescriptions', 'levelLeadersByLevel'));
    }

    /** get_department_structure.php: JSON tree under a department head */
    public function getDepartmentStructure(Request $request)
    {
        if ($request->isMethod('post') && $request->request->has('dept_head_id')) {
            $deptHeadId = (int) $request->post('dept_head_id');

            // Get the department head info
            $headRow = DB::selectOne('SELECT * FROM organization_chart WHERE id = ? AND status = 1', [$deptHeadId]);

            if ($headRow) {
                // Get reports to name
                if ($headRow['parent_id']) {
                    $parent = DB::selectOne('SELECT employee_name FROM organization_chart WHERE id = ?', [$headRow['parent_id']]);
                    $headRow['reports_to_name'] = $parent['employee_name'] ?? null;
                }

                // Get children
                $headRow['children'] = $this->buildDepartmentTree($headRow['id']);

                return kc_json([
                    'success' => true,
                    'data' => $headRow,
                ]);
            }

            return kc_json([
                'success' => false,
                'message' => 'Department head not found',
            ]);
        }

        return kc_json([
            'success' => false,
            'message' => 'Invalid request',
        ]);
    }

    /** get_department_structure buildDepartmentTree() */
    private function buildDepartmentTree($parent_id)
    {
        $tree = [];
        $rows = DB::select('SELECT * FROM organization_chart WHERE status = 1 AND parent_id = ? ORDER BY level, sort_order, employee_name', [$parent_id]);
        foreach ($rows as $row) {
            // Get children recursively
            $row['children'] = $this->buildDepartmentTree($row['id']);
            $tree[] = $row;
        }

        return $tree;
    }

    /** my-assets.php: the employee's assigned IT assets */
    public function myAssets(Request $request)
    {
        if (! isLoggedIn()) {
            kc_redirect('login');
        }

        // The logged-in employee (employees.id, referenced by asset_assignments.employee_id)
        $employee_id = (int) session('employee_id');
        $page_title = 'My Assets';

        // Get employee's active assets
        $active_assets = DB::select("SELECT a.*, aa.assigned_date, aa.condition_on_assignment, aa.assignment_notes,
          aa.expected_return_date, aa.status as assignment_status
          FROM asset_assignments aa
          JOIN assets a ON aa.asset_id = a.id
          WHERE aa.employee_id = ? AND aa.status = 'Active'
          ORDER BY aa.assigned_date DESC", [$employee_id]);

        // Get asset history (previously assigned)
        $asset_history = DB::select("SELECT a.*, aa.assigned_date, aa.actual_return_date,
                  aa.condition_on_assignment, aa.condition_on_return,
                  aa.status as assignment_status, aa.assignment_notes, aa.return_notes
                  FROM asset_assignments aa
                  JOIN assets a ON aa.asset_id = a.id
                  WHERE aa.employee_id = ? AND aa.status != 'Active'
                  ORDER BY aa.assigned_date DESC", [$employee_id]);

        // Get employee info
        $employee = DB::selectOne('SELECT * FROM employees WHERE id = ?', [$employee_id]);

        return view('my-assets', compact('active_assets', 'asset_history', 'employee', 'page_title'));
    }

    /** hornet-whitelist.php: mail allow-list info as JSON */
    public function hornetWhitelist(Request $request)
    {
        $config = [
            'service' => 'Kiboko IT Support System',
            'domain' => 'kibokogroup.com',
            'sender_email' => 'derromu44@gmail.com',
            'sender_name' => 'Kiboko IT Support',
            'ip_addresses' => [
                $request->server('SERVER_ADDR'),
            ],
            'email_headers' => [
                'X-Hornet-Trusted-Sender' => 'Yes',
                'X-Hornet-Scan' => 'Allowed',
                'X-Hornet-Category' => 'Internal IT Support',
            ],
            'instructions' => [
                'Add to Hornet Allowlist:',
                '1. Login to Hornet Security dashboard',
                '2. Go to Anti-Spam → Allowlist',
                '3. Add: derromu44@gmail.com',
                '4. Add: *@kibokogroup.com',
                '5. Create content rule: Header contains "X-Hornet-Trusted-Sender: Yes" → Bypass spam scanning',
            ],
        ];

        return kc_json($config, 200, [], JSON_PRETTY_PRINT);
    }
}

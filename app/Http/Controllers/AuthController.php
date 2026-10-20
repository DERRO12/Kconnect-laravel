<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Employee sign in / sign out (was login and logout). */
class AuthController extends Controller
{
    /** login */
    public function login(Request $request)
    {
        // Redirect if already logged in
        if (isLoggedIn()) {
            kc_redirect('./');
        }

        $error = '';

        if ($request->isMethod('post')) {
            // (a field sent as an array counts as empty instead of raising a type error)
            $employee_code = is_string($request->input('employee_code')) ? sanitize($request->input('employee_code')) : '';
            $password = is_string($request->input('password')) ? $request->input('password') : '';

            if (empty($employee_code) || empty($password)) {
                $error = 'Please enter both employee code and password';
            } else {
                $row = Employee::where('employee_code', $employee_code)->where('status', 1)->first();

                if ($row) {
                    if (verifyPassword($password, $row['password'])) {
                        $request->session()->regenerate();
                        session([
                            'employee_id' => $row['id'],
                            'employee_code' => $row['employee_code'],
                            'employee_name' => $row['full_name'],
                            'employee_role' => $row['role'],
                            'employee_department' => $row['department'],
                            'employee_photo' => $row['photo'],
                        ]);

                        // Update last login
                        DB::update('UPDATE employees SET last_login = NOW() WHERE id = ?', [$row['id']]);

                        kc_redirect('./');
                    } else {
                        $error = 'Invalid password';
                    }
                } else {
                    $error = 'Employee code not found or account inactive';
                }
            }
        }

        return view('auth.login', compact('error'));
    }

    /** logout.php: session_destroy() and back to the login page */
    public function logout(Request $request)
    {
        $request->session()->invalidate();

        return kc_redirect_response('login');
    }
}

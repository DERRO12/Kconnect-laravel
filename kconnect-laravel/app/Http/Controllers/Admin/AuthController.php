<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;

/** Admin sign in / sign out (was admin/login.php and admin/logout.php). */
class AuthController extends Controller
{
    /** admin/login.php */
    public function login(Request $request)
    {
        $error = '';

        if ($request->isMethod('post')) {
            $username = sanitize($request->input('username'));
            $password = $request->input('password');

            if ($username && $password) {
                $matches = AdminUser::where('username', $username)->where('status', 1)->get();

                if ($matches->count() == 1) {
                    $admin = $matches->first();
                    if (password_verify($password, $admin['password'])) {
                        $request->session()->regenerate();
                        session([
                            'admin_id' => $admin['id'],
                            'admin_username' => $admin['username'],
                            'admin_role' => $admin['role'],
                        ]);
                        kc_redirect('dashboard.php');
                    } else {
                        $error = 'Invalid password';
                    }
                } else {
                    $error = 'Username not found';
                }
            } else {
                $error = 'Please fill all fields';
            }
        }

        return view('admin.auth.login', compact('error'));
    }

    /** admin/logout.php */
    public function logout(Request $request)
    {
        $request->session()->invalidate();

        return kc_redirect_response('login.php');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;

/** Admin sign in / sign out (was admin/login and admin/logout). */
class AuthController extends Controller
{
    /** admin/login */
    public function login(Request $request)
    {
        $error = '';

        if ($request->isMethod('post')) {
            // (a field sent as an array counts as empty instead of raising a type error)
            $username = is_string($request->input('username')) ? sanitize($request->input('username')) : '';
            $password = is_string($request->input('password')) ? $request->input('password') : '';

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
                        kc_redirect('dashboard');
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

    /** admin/logout */
    public function logout(Request $request)
    {
        $request->session()->invalidate();

        return kc_redirect_response('login');
    }
}

<?php

use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\NavigationManagerController;
use App\Http\Controllers\Admin\OrgChartManagerController;
use App\Http\Controllers\Admin\PeopleController;
use App\Http\Controllers\Admin\WhistleblowingAdminController;
use Illuminate\Support\Facades\Route;

Route::any('/admin/manage-employees.php', [PeopleController::class, 'manageEmployees']);
Route::any('/admin/users.php', [PeopleController::class, 'users']);
Route::any('/admin/user-form.php', [PeopleController::class, 'userForm']);
Route::any('/admin/admin-sync-users.php', [PeopleController::class, 'adminSyncUsers']);
Route::any('/admin/manage-departments.php', [DepartmentController::class, 'manage']);
Route::any('/admin/ajax-department-view.php', [DepartmentController::class, 'ajaxView']);
Route::any('/admin/get-departments.php', [DepartmentController::class, 'getDepartments']);
Route::any('/admin/orgchart_manager.php', [OrgChartManagerController::class, 'index']);
Route::any('/admin/manage-navigation.php', [NavigationManagerController::class, 'index']);
Route::any('/admin/admin-whistleblowing.php', [WhistleblowingAdminController::class, 'index']);
Route::any('/admin/whistleblowing-api.php', [WhistleblowingAdminController::class, 'api']);

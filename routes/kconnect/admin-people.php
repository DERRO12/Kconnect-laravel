<?php

use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\NavigationManagerController;
use App\Http\Controllers\Admin\OrgChartManagerController;
use App\Http\Controllers\Admin\PeopleController;
use App\Http\Controllers\Admin\WhistleblowingAdminController;
use Illuminate\Support\Facades\Route;

Route::any('/admin/manage-employees', [PeopleController::class, 'manageEmployees']);
Route::any('/admin/users', [PeopleController::class, 'users']);
Route::any('/admin/user-form', [PeopleController::class, 'userForm']);
Route::any('/admin/admin-sync-users', [PeopleController::class, 'adminSyncUsers']);
Route::any('/admin/manage-departments', [DepartmentController::class, 'manage']);
Route::any('/admin/ajax-department-view', [DepartmentController::class, 'ajaxView']);
Route::any('/admin/get-departments', [DepartmentController::class, 'getDepartments']);
Route::any('/admin/orgchart_manager', [OrgChartManagerController::class, 'index']);
Route::any('/admin/manage-navigation', [NavigationManagerController::class, 'index']);
Route::any('/admin/admin-whistleblowing', [WhistleblowingAdminController::class, 'index']);
Route::any('/admin/whistleblowing-api', [WhistleblowingAdminController::class, 'api']);

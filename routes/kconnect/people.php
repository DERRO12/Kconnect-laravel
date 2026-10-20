<?php

use App\Http\Controllers\Api\OrgchartController;
use App\Http\Controllers\PeopleController;
use Illuminate\Support\Facades\Route;

Route::any('/register', [PeopleController::class, 'register']);
Route::any('/forgot-password', [PeopleController::class, 'forgotPassword']);
Route::any('/change-password', [PeopleController::class, 'changePassword']);
Route::any('/employee-directory', [PeopleController::class, 'employeeDirectory']);
Route::any('/directors', [PeopleController::class, 'directors']);
Route::any('/orgchart_view', [PeopleController::class, 'orgchartView']);
Route::any('/ajax-company-view', [PeopleController::class, 'ajaxCompanyView']);
Route::any('/ajax-department-view', [PeopleController::class, 'ajaxDepartmentView']);
Route::any('/ajax-leadership-view', [PeopleController::class, 'ajaxLeadershipView']);
Route::any('/get_department_structure', [PeopleController::class, 'getDepartmentStructure']);
Route::any('/my-assets', [PeopleController::class, 'myAssets']);
Route::any('/hornet-whitelist', [PeopleController::class, 'hornetWhitelist']);

Route::any('/api/delete_node', [OrgchartController::class, 'deleteNode']);
Route::any('/api/get_employees', [OrgchartController::class, 'getEmployees']);
Route::any('/api/get_orgchart', [OrgchartController::class, 'getOrgchart']);
Route::any('/api/save_orgchart', [OrgchartController::class, 'saveOrgchart']);

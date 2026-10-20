<?php

use App\Http\Controllers\Api\OrgchartController;
use App\Http\Controllers\PeopleController;
use Illuminate\Support\Facades\Route;

Route::any('/register.php', [PeopleController::class, 'register']);
Route::any('/forgot-password.php', [PeopleController::class, 'forgotPassword']);
Route::any('/change-password.php', [PeopleController::class, 'changePassword']);
Route::any('/employee-directory.php', [PeopleController::class, 'employeeDirectory']);
Route::any('/directors.php', [PeopleController::class, 'directors']);
Route::any('/orgchart_view.php', [PeopleController::class, 'orgchartView']);
Route::any('/ajax-company-view.php', [PeopleController::class, 'ajaxCompanyView']);
Route::any('/ajax-department-view.php', [PeopleController::class, 'ajaxDepartmentView']);
Route::any('/ajax-leadership-view.php', [PeopleController::class, 'ajaxLeadershipView']);
Route::any('/get_department_structure.php', [PeopleController::class, 'getDepartmentStructure']);
Route::any('/my-assets.php', [PeopleController::class, 'myAssets']);
Route::any('/hornet-whitelist.php', [PeopleController::class, 'hornetWhitelist']);

Route::any('/api/delete_node.php', [OrgchartController::class, 'deleteNode']);
Route::any('/api/get_employees.php', [OrgchartController::class, 'getEmployees']);
Route::any('/api/get_orgchart.php', [OrgchartController::class, 'getOrgchart']);
Route::any('/api/save_orgchart.php', [OrgchartController::class, 'saveOrgchart']);

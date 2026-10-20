<?php

use App\Http\Controllers\Admin\ProjectsController as AdminProjectsController;
use App\Http\Controllers\ProjectsController;
use Illuminate\Support\Facades\Route;

Route::any('/project-detail.php', [ProjectsController::class, 'detail']);
Route::any('/projects-admin.php', [ProjectsController::class, 'admin']);
Route::any('/projects-dashboard.php', [ProjectsController::class, 'dashboard']);
Route::any('/rewards.php', [ProjectsController::class, 'rewards']);
Route::any('/reward-detail.php', [ProjectsController::class, 'rewardDetail']);

Route::any('/admin/manage-rewards.php', [AdminProjectsController::class, 'manageRewards']);
Route::any('/admin/dashboard.php', [AdminProjectsController::class, 'dashboard']);
Route::any('/admin/system-logs.php', [AdminProjectsController::class, 'systemLogs']);

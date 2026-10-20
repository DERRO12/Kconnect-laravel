<?php

use App\Http\Controllers\Admin\ProjectsController as AdminProjectsController;
use App\Http\Controllers\ProjectsController;
use Illuminate\Support\Facades\Route;

Route::any('/project-detail', [ProjectsController::class, 'detail']);
Route::any('/projects-admin', [ProjectsController::class, 'admin']);
Route::any('/projects-dashboard', [ProjectsController::class, 'dashboard']);
Route::any('/rewards', [ProjectsController::class, 'rewards']);
Route::any('/reward-detail', [ProjectsController::class, 'rewardDetail']);

Route::any('/admin/manage-rewards', [AdminProjectsController::class, 'manageRewards']);
Route::any('/admin/dashboard', [AdminProjectsController::class, 'dashboard']);
Route::any('/admin/system-logs', [AdminProjectsController::class, 'systemLogs']);

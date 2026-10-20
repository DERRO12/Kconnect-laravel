<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::any('/login.php', [AuthController::class, 'login']);
Route::any('/logout.php', [AuthController::class, 'logout']);

Route::any('/admin/login.php', [AdminAuthController::class, 'login']);
Route::any('/admin/logout.php', [AdminAuthController::class, 'logout']);

<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::any('/login', [AuthController::class, 'login']);
Route::any('/logout', [AuthController::class, 'logout']);

Route::any('/admin/login', [AdminAuthController::class, 'login']);
Route::any('/admin/logout', [AdminAuthController::class, 'logout']);

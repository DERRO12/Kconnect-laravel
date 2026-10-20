<?php

use App\Http\Controllers\Admin\ItController as AdminItController;
use App\Http\Controllers\ItController;
use Illuminate\Support\Facades\Route;

Route::any('/it-support', [ItController::class, 'support']);
Route::any('/it-admin', [ItController::class, 'admin']);
Route::any('/it-ticket-detail', [ItController::class, 'ticketDetail']);
Route::any('/it-ticket-data', [ItController::class, 'ticketData']);
Route::any('/download-attachment', [ItController::class, 'downloadAttachment']);

Route::any('/admin/it-support', [AdminItController::class, 'support']);
Route::any('/admin/it-ticket-data', [AdminItController::class, 'ticketData']);
Route::any('/admin/it-ticket-detail', [AdminItController::class, 'ticketDetail']);
Route::any('/admin/it-admin', [AdminItController::class, 'admin']);
Route::any('/backup', [ItController::class, 'backup']);
Route::any('/admin/backup', [AdminItController::class, 'backup']);
Route::any('/backup-log', [ItController::class, 'backupLog']);
Route::any('/admin/backup-log', [AdminItController::class, 'backupLog']);

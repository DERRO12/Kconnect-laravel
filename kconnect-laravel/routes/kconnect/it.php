<?php

use App\Http\Controllers\Admin\ItController as AdminItController;
use App\Http\Controllers\ItController;
use Illuminate\Support\Facades\Route;

Route::any('/it-support.php', [ItController::class, 'support']);
Route::any('/it-admin.php', [ItController::class, 'admin']);
Route::any('/it-ticket-detail.php', [ItController::class, 'ticketDetail']);
Route::any('/it-ticket-data.php', [ItController::class, 'ticketData']);
Route::any('/download-attachment.php', [ItController::class, 'downloadAttachment']);

Route::any('/admin/it-support.php', [AdminItController::class, 'support']);
Route::any('/admin/it-ticket-data.php', [AdminItController::class, 'ticketData']);
Route::any('/admin/it-ticket-detail.php', [AdminItController::class, 'ticketDetail']);
Route::any('/admin/it-admin.php', [AdminItController::class, 'admin']);
Route::any('/backup.php', [ItController::class, 'backup']);
Route::any('/admin/backup.php', [AdminItController::class, 'backup']);
Route::any('/backup-log.php', [ItController::class, 'backupLog']);
Route::any('/admin/backup-log.php', [AdminItController::class, 'backupLog']);

<?php

use App\Http\Controllers\Admin\AdminAssetsController;
use Illuminate\Support\Facades\Route;

Route::any('/admin/manage-assets.php', [AdminAssetsController::class, 'manageAssets']);
Route::any('/admin/asset-form.php', [AdminAssetsController::class, 'assetForm']);
Route::any('/admin/asset-detail.php', [AdminAssetsController::class, 'assetDetail']);
Route::any('/admin/asset-assign.php', [AdminAssetsController::class, 'assetAssign']);
Route::any('/admin/asset-reports.php', [AdminAssetsController::class, 'assetReports']);
Route::any('/admin/asset-trail.php', [AdminAssetsController::class, 'assetTrail']);
Route::any('/admin/key-contacts.php', [AdminAssetsController::class, 'keyContacts']);
Route::any('/admin/key-contacts-add.php', [AdminAssetsController::class, 'keyContactsAdd']);
Route::any('/admin/key-contacts-edit.php', [AdminAssetsController::class, 'keyContactsEdit']);
Route::any('/admin/top-resources.php', [AdminAssetsController::class, 'topResources']);
Route::any('/admin/top-resources-add.php', [AdminAssetsController::class, 'topResourcesAdd']);
Route::any('/admin/top-resources-edit.php', [AdminAssetsController::class, 'topResourcesEdit']);
Route::any('/admin/send-emails.php', [AdminAssetsController::class, 'sendEmails']);
Route::any('/admin/course-resources.php', [AdminAssetsController::class, 'courseResources']);

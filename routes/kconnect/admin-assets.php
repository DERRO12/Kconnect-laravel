<?php

use App\Http\Controllers\Admin\AdminAssetsController;
use Illuminate\Support\Facades\Route;

Route::any('/admin/manage-assets', [AdminAssetsController::class, 'manageAssets']);
Route::any('/admin/asset-form', [AdminAssetsController::class, 'assetForm']);
Route::any('/admin/asset-detail', [AdminAssetsController::class, 'assetDetail']);
Route::any('/admin/asset-assign', [AdminAssetsController::class, 'assetAssign']);
Route::any('/admin/asset-reports', [AdminAssetsController::class, 'assetReports']);
Route::any('/admin/asset-trail', [AdminAssetsController::class, 'assetTrail']);
Route::any('/admin/key-contacts', [AdminAssetsController::class, 'keyContacts']);
Route::any('/admin/key-contacts-add', [AdminAssetsController::class, 'keyContactsAdd']);
Route::any('/admin/key-contacts-edit', [AdminAssetsController::class, 'keyContactsEdit']);
Route::any('/admin/top-resources', [AdminAssetsController::class, 'topResources']);
Route::any('/admin/top-resources-add', [AdminAssetsController::class, 'topResourcesAdd']);
Route::any('/admin/top-resources-edit', [AdminAssetsController::class, 'topResourcesEdit']);
Route::any('/admin/send-emails', [AdminAssetsController::class, 'sendEmails']);
Route::any('/admin/course-resources', [AdminAssetsController::class, 'courseResources']);

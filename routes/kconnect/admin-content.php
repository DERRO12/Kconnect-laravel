<?php

use App\Http\Controllers\Admin\ContentController;
use Illuminate\Support\Facades\Route;

Route::any('/admin/news-add', [ContentController::class, 'newsAdd']);
Route::any('/admin/news-edit', [ContentController::class, 'newsEdit']);
Route::any('/admin/news-events', [ContentController::class, 'newsEvents']);
Route::any('/admin/event-add', [ContentController::class, 'eventAdd']);
Route::any('/admin/event-edit', [ContentController::class, 'eventEdit']);
Route::any('/admin/manage-birthdays', [ContentController::class, 'manageBirthdays']);
Route::any('/admin/normalize-birthdays', [ContentController::class, 'normalizeBirthdays']);
Route::any('/admin/manage-carousel', [ContentController::class, 'manageCarousel']);
Route::any('/admin/manage-spotlights', [ContentController::class, 'manageSpotlights']);
Route::any('/admin/manage-gallery', [ContentController::class, 'manageGallery']);
Route::any('/admin/manage-brand-assets', [ContentController::class, 'manageBrandAssets']);

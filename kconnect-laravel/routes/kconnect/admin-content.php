<?php

use App\Http\Controllers\Admin\ContentController;
use Illuminate\Support\Facades\Route;

Route::any('/admin/news-add.php', [ContentController::class, 'newsAdd']);
Route::any('/admin/news-edit.php', [ContentController::class, 'newsEdit']);
Route::any('/admin/news-events.php', [ContentController::class, 'newsEvents']);
Route::any('/admin/event-add.php', [ContentController::class, 'eventAdd']);
Route::any('/admin/event-edit.php', [ContentController::class, 'eventEdit']);
Route::any('/admin/manage-birthdays.php', [ContentController::class, 'manageBirthdays']);
Route::any('/admin/normalize-birthdays.php', [ContentController::class, 'normalizeBirthdays']);
Route::any('/admin/manage-carousel.php', [ContentController::class, 'manageCarousel']);
Route::any('/admin/manage-spotlights.php', [ContentController::class, 'manageSpotlights']);
Route::any('/admin/manage-gallery.php', [ContentController::class, 'manageGallery']);
Route::any('/admin/manage-brand-assets.php', [ContentController::class, 'manageBrandAssets']);

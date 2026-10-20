<?php

use App\Http\Controllers\Ajax\HomeAjaxController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Employee home page (also serves /index.php) and news/content pages
Route::any('/', [HomeController::class, 'index']);
Route::any('/all-news.php', [HomeController::class, 'allNews']);
Route::any('/news-details.php', [HomeController::class, 'newsDetails']);
Route::any('/gallery.php', [HomeController::class, 'gallery']);
Route::any('/brand-assets.php', [HomeController::class, 'brandAssets']);
Route::any('/get-asset.php', [HomeController::class, 'getAsset']);

// AJAX endpoints used by the home page
Route::any('/ajax/birthday-handler.php', [HomeAjaxController::class, 'birthdayHandler']);
Route::any('/ajax/contact-handler.php', [HomeAjaxController::class, 'contactHandler']);
Route::any('/ajax/get-all-spotlights.php', [HomeAjaxController::class, 'getAllSpotlights']);
Route::any('/ajax/get_event.php', [HomeAjaxController::class, 'getEvent']);
Route::any('/ajax/news-handler.php', [HomeAjaxController::class, 'newsHandler']);

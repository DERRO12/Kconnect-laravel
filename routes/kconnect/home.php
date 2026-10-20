<?php

use App\Http\Controllers\Ajax\HomeAjaxController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Employee home page (also serves /index.php) and news/content pages
Route::any('/all-news', [HomeController::class, 'allNews']);
Route::any('/news-details', [HomeController::class, 'newsDetails']);
Route::any('/gallery', [HomeController::class, 'gallery']);
Route::any('/brand-assets', [HomeController::class, 'brandAssets']);
Route::any('/get-asset', [HomeController::class, 'getAsset']);

// AJAX endpoints used by the home page
Route::any('/ajax/birthday-handler', [HomeAjaxController::class, 'birthdayHandler']);
Route::any('/ajax/contact-handler', [HomeAjaxController::class, 'contactHandler']);
Route::any('/ajax/get-all-spotlights', [HomeAjaxController::class, 'getAllSpotlights']);
Route::any('/ajax/get_event', [HomeAjaxController::class, 'getEvent']);
Route::any('/ajax/news-handler', [HomeAjaxController::class, 'newsHandler']);

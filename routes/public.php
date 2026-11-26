<?php

use App\Http\Controllers\SurveyPublicController;
use Illuminate\Support\Facades\Route;

/*
| Public routes that run WITHOUT the web middleware: no session, no cookies, no CSRF token.
| Used by the anonymous satisfaction form on dashboard.kibokogroup.com.
*/

Route::get('/api/survey', [SurveyPublicController::class, 'show']);
Route::post('/api/survey', [SurveyPublicController::class, 'submit']);
Route::options('/api/survey', [SurveyPublicController::class, 'preflight']);
Route::get('/satisfaction', [SurveyPublicController::class, 'form']);

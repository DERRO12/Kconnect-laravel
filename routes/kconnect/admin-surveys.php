<?php

use App\Http\Controllers\Admin\SurveyAdminController;
use Illuminate\Support\Facades\Route;

// Satisfaction surveys (the public API and form are in routes/public.php)
Route::any('/admin/surveys', [SurveyAdminController::class, 'index']);
Route::any('/admin/survey-edit', [SurveyAdminController::class, 'edit']);
Route::get('/admin/survey-results', [SurveyAdminController::class, 'results']);
Route::get('/admin/survey-form-file', [SurveyAdminController::class, 'formFile']);

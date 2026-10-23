<?php

use App\Http\Controllers\Academy\AdminController;
use App\Http\Controllers\Academy\ApiController;
use App\Http\Controllers\Academy\LearnerController;
use Illuminate\Support\Facades\Route;

// K-Academy module. Learner pages: /academy/..., admin: /academy/admin/..., AJAX: /academy/api/...
// Page names are the old kacademy file names without .php, so old links keep working.

Route::get('/academy', fn () => redirect(url(App\Support\Academy::learner() ? 'academy/dashboard' : 'academy/index')));
Route::get('/academy/admin', fn () => redirect(url('academy/admin/dashboard')));
// One login: K-Academy uses the K-Connect employee and admin logins.
Route::get('/academy/login', fn () => redirect(url('login')));
Route::get('/academy/register', fn () => redirect(url('register')));
Route::get('/academy/logout', fn () => redirect(url('logout')));
Route::get('/academy/admin/logout', fn () => redirect(url('admin/logout')));
Route::get('/academy/admin/login', fn () => redirect(url('admin/login')));
Route::get('/academy/change-password', fn () => redirect(url('change-password')));
Route::get('/academy/settings', fn () => redirect(url('academy/profile')));
Route::get('/academy/view-certificate', fn (Illuminate\Http\Request $r) => redirect(url('academy/certificates').($r->getQueryString() ? '?'.$r->getQueryString() : '')));
Route::get('/academy/my-certificates', fn () => redirect(url('academy/certificates')));
Route::get('/academy/certificate', fn (Illuminate\Http\Request $r) => redirect(url('academy/download-certificate').($r->getQueryString() ? '?'.$r->getQueryString() : '')));
Route::get('/academy/course', fn (Illuminate\Http\Request $r) => redirect(url('academy/course-overview').($r->getQueryString() ? '?'.$r->getQueryString() : '')));
// Files of public/academy-files/assets when the web server does not apply public/.htaccess (php artisan serve).
Route::get('/academy/assets/{path}', [App\Http\Controllers\Academy\FileController::class, 'asset'])->where('path', '.*');

Route::any('/academy/index', [LearnerController::class, 'index']);
Route::any('/academy/dashboard', [LearnerController::class, 'dashboard']);
Route::any('/academy/browse-courses', [LearnerController::class, 'browseCourses']);
Route::any('/academy/browse-course', [LearnerController::class, 'browseCourse']);
Route::any('/academy/course-overview', [LearnerController::class, 'courseOverview']);
Route::any('/academy/course-player', [LearnerController::class, 'coursePlayer']);
Route::any('/academy/course-notes', [LearnerController::class, 'courseNotes']);
Route::any('/academy/course-discussions', [LearnerController::class, 'courseDiscussions']);
Route::any('/academy/course-resources', [LearnerController::class, 'courseResources']);
Route::any('/academy/my-courses', [LearnerController::class, 'myCourses']);
Route::any('/academy/progress', [LearnerController::class, 'progress']);
Route::any('/academy/notifications', [LearnerController::class, 'notifications']);
Route::any('/academy/quiz', [LearnerController::class, 'quiz']);
Route::any('/academy/quiz-results', [LearnerController::class, 'quizResults']);
Route::any('/academy/certificates', [LearnerController::class, 'certificates']);
Route::any('/academy/certificate-pending', [LearnerController::class, 'certificatePending']);
Route::any('/academy/download-certificate', [LearnerController::class, 'downloadCertificate']);
Route::any('/academy/generate-certificate', [LearnerController::class, 'generateCertificate']);
Route::any('/academy/verify-certificate', [LearnerController::class, 'verifyCertificate']);
Route::any('/academy/profile', [LearnerController::class, 'profile']);

Route::any('/academy/admin/dashboard', [AdminController::class, 'dashboard']);
Route::any('/academy/admin/manage_courses', [AdminController::class, 'manageCourses']);
Route::any('/academy/admin/add_course', [AdminController::class, 'addCourse']);
Route::any('/academy/admin/edit_course', [AdminController::class, 'editCourse']);
Route::any('/academy/admin/manage_lessons', [AdminController::class, 'manageLessons']);
Route::any('/academy/admin/add_lesson', [AdminController::class, 'addLesson']);
Route::any('/academy/admin/edit_lesson', [AdminController::class, 'editLesson']);
Route::post('/academy/admin/bulk_add_lessons', [AdminController::class, 'bulkAddLessons']);
Route::post('/academy/admin/upload-video-chunk', [AdminController::class, 'uploadVideoChunk']);
Route::any('/academy/admin/get_lesson', [AdminController::class, 'getLesson']);
Route::any('/academy/admin/reorder_lessons', [AdminController::class, 'reorderLessons']);
Route::any('/academy/admin/manage_quizzes', [AdminController::class, 'manageQuizzes']);
Route::any('/academy/admin/manage_quiz', [AdminController::class, 'manageQuiz']);
Route::any('/academy/admin/edit_quiz', [AdminController::class, 'editQuiz']);
Route::any('/academy/admin/delete_quiz', [AdminController::class, 'deleteQuiz']);
Route::any('/academy/admin/duplicate_quiz', [AdminController::class, 'duplicateQuiz']);
Route::any('/academy/admin/preview_quiz', [AdminController::class, 'previewQuiz']);
Route::any('/academy/admin/reset_quiz_stats', [AdminController::class, 'resetQuizStats']);
Route::any('/academy/admin/manage_quiz_questions', [AdminController::class, 'manageQuizQuestions']);
Route::any('/academy/admin/get_question', [AdminController::class, 'getQuestion']);
Route::any('/academy/admin/duplicate_question', [AdminController::class, 'duplicateQuestion']);
Route::any('/academy/admin/import_questions', [AdminController::class, 'importQuestions']);
Route::any('/academy/admin/manage_users', [AdminController::class, 'manageUsers']);
Route::any('/academy/admin/get-user-details', [AdminController::class, 'getUserDetails']);
Route::any('/academy/admin/pending-certificates', [AdminController::class, 'pendingCertificates']);
Route::any('/academy/admin/approve-certificate', [AdminController::class, 'approveCertificate']);
Route::any('/academy/admin/reports', [AdminController::class, 'reports']);
Route::any('/academy/admin/activity-logs', [AdminController::class, 'activityLogs']);
Route::any('/academy/admin/settings', [AdminController::class, 'settings']);
Route::any('/academy/admin/hero-video', [AdminController::class, 'heroVideo']);
Route::any('/academy/admin/post-comment', [AdminController::class, 'postComment']);

Route::any('/academy/api/check-certificate-status', [ApiController::class, 'checkCertificateStatus']);
Route::any('/academy/api/clear-notifications', [ApiController::class, 'clearNotifications']);
Route::any('/academy/api/complete-lesson', [ApiController::class, 'completeLesson']);
Route::any('/academy/api/delete-comment', [ApiController::class, 'deleteComment']);
Route::any('/academy/api/delete-discussion', [ApiController::class, 'deleteDiscussion']);
Route::any('/academy/api/delete-reply', [ApiController::class, 'deleteReply']);
Route::any('/academy/api/enroll-course', [ApiController::class, 'enrollCourse']);
Route::any('/academy/api/get-attempts', [ApiController::class, 'getAttempts']);
Route::any('/academy/api/like-comment', [ApiController::class, 'likeComment']);
Route::any('/academy/api/post-discussion', [ApiController::class, 'postDiscussion']);
Route::any('/academy/api/post-reply', [ApiController::class, 'postReply']);
Route::any('/academy/api/toggle-like', [ApiController::class, 'toggleLike']);
Route::any('/academy/api/update-progress', [ApiController::class, 'updateProgress']);

// Endpoints the old pages called that the old site did not have
Route::post('/academy/api/mark-notifications-read', [ApiController::class, 'markNotificationsRead']);
Route::get('/academy/api/get-notes', [ApiController::class, 'getNotes']);
Route::post('/academy/api/save-notes', [ApiController::class, 'saveNotes']);
Route::get('/academy/api/export-all-notes', [ApiController::class, 'exportAllNotes']);
Route::get('/academy/api/backup-notes', [ApiController::class, 'exportAllNotes']);
Route::get('/academy/api/unenroll-course', [ApiController::class, 'unenrollCourse']);
Route::get('/academy/api/get-course-lessons', [ApiController::class, 'getCourseLessons']);
Route::post('/academy/api/upload-editor-image', [ApiController::class, 'uploadEditorImage']);
Route::post('/academy/admin/reorder_questions', [AdminController::class, 'reorderQuestions']);
Route::any('/academy/admin/add_quiz', [AdminController::class, 'addQuiz']);
Route::get('/academy/admin/quiz_results', [AdminController::class, 'quizResults']);

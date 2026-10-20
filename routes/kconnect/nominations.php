<?php

use App\Http\Controllers\Admin\NominationAdminController;
use App\Http\Controllers\Rewards\Api\NominationApiController;
use App\Http\Controllers\Rewards\NominationController;
use Illuminate\Support\Facades\Route;

// Employee side (rewards/)
Route::any('/rewards/nomination', [NominationController::class, 'nomination']);
Route::any('/rewards/committee-members', [NominationController::class, 'committeeMembers']);
Route::any('/rewards/committee-review', [NominationController::class, 'committeeReview']);

// rewards/api/
Route::any('/rewards/api/check_nomination', [NominationApiController::class, 'checkNomination']);
Route::any('/rewards/api/get_categories', [NominationApiController::class, 'getCategories']);
Route::any('/rewards/api/get_committee_nominations', [NominationApiController::class, 'getCommitteeNominations']);
Route::any('/rewards/api/get_committee_stats', [NominationApiController::class, 'getCommitteeStats']);
Route::any('/rewards/api/get_employees', [NominationApiController::class, 'getEmployees']);
Route::any('/rewards/api/get_leaderboard', [NominationApiController::class, 'getLeaderboard']);
Route::any('/rewards/api/get_questions', [NominationApiController::class, 'getQuestions']);
Route::any('/rewards/api/get_user_nominations_history', [NominationApiController::class, 'getUserNominationsHistory']);
Route::any('/rewards/api/submit_committee_decision', [NominationApiController::class, 'submitCommitteeDecision']);
Route::any('/rewards/api/submit_nomination', [NominationApiController::class, 'submitNomination']);

// Admin side (admin/)
Route::any('/admin/all-nominations', [NominationAdminController::class, 'allNominations']);
Route::post('/admin/api/mark_as_read', [NominationAdminController::class, 'markAsRead']);
Route::any('/admin/nomination-dashboard', [NominationAdminController::class, 'nominationDashboard']);
Route::any('/admin/nominee-votes', [NominationAdminController::class, 'nomineeVotes']);
Route::any('/admin/view-nomination', [NominationAdminController::class, 'viewNomination']);
Route::any('/admin/manage-categories', [NominationAdminController::class, 'manageCategories']);
Route::any('/admin/manage-questions', [NominationAdminController::class, 'manageQuestions']);
Route::any('/admin/manage-quarters', [NominationAdminController::class, 'manageQuarters']);
Route::any('/admin/manage-committee', [NominationAdminController::class, 'manageCommittee']);
Route::any('/admin/get-category-nominations', [NominationAdminController::class, 'getCategoryNominations']);
Route::any('/admin/sync_nomination_tracking', [NominationAdminController::class, 'syncNominationTracking']);

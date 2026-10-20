<?php

use App\Http\Controllers\Admin\NominationAdminController;
use App\Http\Controllers\Rewards\Api\NominationApiController;
use App\Http\Controllers\Rewards\NominationController;
use Illuminate\Support\Facades\Route;

// Employee side (rewards/)
Route::any('/rewards/nomination.php', [NominationController::class, 'nomination']);
Route::any('/rewards/committee-members.php', [NominationController::class, 'committeeMembers']);
Route::any('/rewards/committee-review.php', [NominationController::class, 'committeeReview']);

// rewards/api/
Route::any('/rewards/api/check_nomination.php', [NominationApiController::class, 'checkNomination']);
Route::any('/rewards/api/get_categories.php', [NominationApiController::class, 'getCategories']);
Route::any('/rewards/api/get_committee_nominations.php', [NominationApiController::class, 'getCommitteeNominations']);
Route::any('/rewards/api/get_committee_stats.php', [NominationApiController::class, 'getCommitteeStats']);
Route::any('/rewards/api/get_employees.php', [NominationApiController::class, 'getEmployees']);
Route::any('/rewards/api/get_leaderboard.php', [NominationApiController::class, 'getLeaderboard']);
Route::any('/rewards/api/get_questions.php', [NominationApiController::class, 'getQuestions']);
Route::any('/rewards/api/get_user_nominations_history.php', [NominationApiController::class, 'getUserNominationsHistory']);
Route::any('/rewards/api/submit_committee_decision.php', [NominationApiController::class, 'submitCommitteeDecision']);
Route::any('/rewards/api/submit_nomination.php', [NominationApiController::class, 'submitNomination']);

// Admin side (admin/)
Route::any('/admin/all-nominations.php', [NominationAdminController::class, 'allNominations']);
Route::any('/admin/nomination-dashboard.php', [NominationAdminController::class, 'nominationDashboard']);
Route::any('/admin/nominee-votes.php', [NominationAdminController::class, 'nomineeVotes']);
Route::any('/admin/view-nomination.php', [NominationAdminController::class, 'viewNomination']);
Route::any('/admin/manage-categories.php', [NominationAdminController::class, 'manageCategories']);
Route::any('/admin/manage-questions.php', [NominationAdminController::class, 'manageQuestions']);
Route::any('/admin/manage-quarters.php', [NominationAdminController::class, 'manageQuarters']);
Route::any('/admin/manage-committee.php', [NominationAdminController::class, 'manageCommittee']);
Route::any('/admin/get-category-nominations.php', [NominationAdminController::class, 'getCategoryNominations']);
Route::any('/admin/sync_nomination_tracking.php', [NominationAdminController::class, 'syncNominationTracking']);
